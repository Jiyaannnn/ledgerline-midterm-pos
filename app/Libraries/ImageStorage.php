<?php

namespace App\Libraries;

use CodeIgniter\HTTP\Files\UploadedFile;

class ImageStorage
{
    public function store(UploadedFile $upload, string $kind, int $width, int $height): string
    {
        if (! in_array($kind, ['avatars', 'products'], true)) {
            throw new \InvalidArgumentException('Unsupported image category.');
        }

        if (ENVIRONMENT !== 'production') {
            $directory = FCPATH . 'uploads/' . $kind . '/';
            if (! is_dir($directory) && ! mkdir($directory, 0755, true) && ! is_dir($directory)) {
                throw new \RuntimeException('Image directory could not be created.');
            }
            $filename = bin2hex(random_bytes(16)) . '.jpg';
            $this->prepare($upload, $directory . $filename, $width, $height);
            return 'uploads/' . $kind . '/' . $filename;
        }

        // Temporary processing is safe on Render; only the final bytes need durable storage.
        $directory = WRITEPATH . 'uploads/';
        if (! is_dir($directory) && ! mkdir($directory, 0755, true) && ! is_dir($directory)) {
            throw new \RuntimeException('Temporary image directory could not be created.');
        }
        $temporary = $directory . bin2hex(random_bytes(16)) . '.jpg';
        try {
            $this->prepare($upload, $temporary, $width, $height);
            $content = file_get_contents($temporary);
            if ($content === false || strlen($content) > 2 * 1024 * 1024) {
                throw new \RuntimeException('Prepared image exceeds the 2 MB storage limit.');
            }
            $database = db_connect();
            if (! $database->table('uploaded_images')->insert(['content' => $content, 'created_at' => date('Y-m-d H:i:s')])) {
                throw new \RuntimeException('Image database insert failed.');
            }
            return 'media/' . $database->insertID();
        } finally {
            if (is_file($temporary)) {
                unlink($temporary);
            }
        }
    }

    public function delete(string $storedPath): void
    {
        if (preg_match('~^media/([1-9][0-9]*)$~', $storedPath, $matches)) {
            db_connect()->table('uploaded_images')->where('id', (int) $matches[1])->delete();
        } elseif (preg_match('~^uploads/(avatars|products)/[a-f0-9]{32}\.jpg$~', $storedPath)) {
            $path = FCPATH . $storedPath;
            if (is_file($path)) {
                unlink($path);
            }
        }
    }

    private function prepare(UploadedFile $upload, string $target, int $width, int $height): void
    {
        // Convert validated JPG/PNG input to a bounded JPEG without source metadata.
        service('image')->withFile($upload->getTempName())->fit($width, $height, 'center')
            ->convert(IMAGETYPE_JPEG)->save($target, 82);
    }
}

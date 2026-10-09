<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\Exceptions\PageNotFoundException;

class Media extends Controller
{
    public function show(int $id)
    {
        $row = db_connect()->table('uploaded_images')->select('content')->where('id', $id)->get()->getRowArray();
        if ($row === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        return service('response')->setContentType('image/jpeg')->setHeader('X-Content-Type-Options', 'nosniff')
            ->setHeader('Cache-Control', 'public, max-age=31536000, immutable')->setBody($row['content']);
    }
}

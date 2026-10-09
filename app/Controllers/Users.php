<?php

namespace App\Controllers;

use App\Models\UserModel;

class Users extends BaseController
{
    public function index(): string
    {
        // The UserModel replaces the static user array used in TFA1.
        $userModel = new UserModel();

        // Query Builder retrieves the users in a stable order before rendering.
        return view('users/index', [
            'title'      => 'User Accounts',
            'activePage' => 'users',
            'users'      => $userModel->where('is_archived', 0)->orderBy('id', 'ASC')->findAll(),
        ]);
    }

    public function new(): string
    {
        return view('users/form', ['title' => 'New User', 'activePage' => 'users', 'user' => null, 'errors' => [], 'values' => []]);
    }

    public function create()
    {
        return $this->saveUser(null);
    }

    public function edit(int $id): string
    {
        $user = (new UserModel())->find($id);
        if ($user === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        return view('users/form', ['title' => 'Edit User', 'activePage' => 'users', 'user' => $user, 'errors' => [], 'values' => $user]);
    }

    public function update(int $id)
    {
        return $this->saveUser($id);
    }

    public function remove(int $id)
    {
        if ($id === (int) session()->get('staff_id')) return redirect()->to(site_url('users'))->with('error', 'You cannot delete your own account.');
        $model = new UserModel();
        $user = $model->find($id);
        if ($user === null) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        // Sellers with past transactions remain in the database but lose access.
        if (db_connect()->table('sales')->where('sold_by', $id)->countAllResults() > 0) {
            $model->update($id, ['is_archived' => 1]);
            $message = 'Staff removed from the active directory; past sales remain intact.';
        } else {
            $model->delete($id);
            if (! empty($user['avatar']) && str_starts_with($user['avatar'], 'uploads/avatars/')) {
                $avatarPath = FCPATH . $user['avatar'];
                if (is_file($avatarPath)) unlink($avatarPath);
            }
            $message = 'Staff account deleted.';
        }
        return redirect()->to(site_url('users'))->with('success', $message);
    }

    private function saveUser(?int $id)
    {
        $model = new UserModel();
        $user = $id === null ? null : $model->find($id);
        if ($id !== null && $user === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        $values = [
            'username' => trim((string) $this->request->getPost('username')),
            'full_name' => trim((string) $this->request->getPost('full_name')),
        ];
        // is_unique ignores the current row during editing but rejects every other duplicate.
        $unique = $id === null ? 'is_unique[users.username]' : 'is_unique[users.username,id,' . $id . ']';
        $rules = ['username' => 'required|max_length[50]|alpha_dash|' . $unique, 'full_name' => 'required|max_length[100]'];
        // New staff need a password; editing can leave it blank to keep the current hash.
        $password = (string) $this->request->getPost('password');
        if ($id === null || $password !== '') {
            $rules['password'] = 'required|min_length[12]|max_length[128]';
            $values['password'] = $password;
        }
        $errors = [];
        if (! $this->validateData($values, $rules)) {
            $errors = $this->validator->getErrors();
        }
        $upload = $this->request->getFile('avatar');
        $hasUpload = $upload !== null && $upload->getError() !== UPLOAD_ERR_NO_FILE;
        if ($hasUpload) {
            // Framework rules inspect the actual image, MIME type, and 2 MB size limit.
            $fileRules = ['avatar' => 'uploaded[avatar]|is_image[avatar]|mime_in[avatar,image/jpeg,image/png]|max_size[avatar,2048]'];
            if (! $this->validateData([], $fileRules)) {
                $errors = array_merge($errors, $this->validator->getErrors());
            }
        }
        if ($errors !== []) {
            unset($values['password']);
            return view('users/form', ['title' => $id ? 'Edit User' : 'New User', 'activePage' => 'users', 'user' => $user, 'errors' => $errors, 'values' => $values]);
        }
        if (isset($values['password'])) {
            $values['password'] = password_hash($values['password'], PASSWORD_DEFAULT);
        }
        if ($hasUpload) {
            $directory = FCPATH . 'uploads/avatars/';
            if (! is_dir($directory) && ! mkdir($directory, 0755, true) && ! is_dir($directory)) {
                throw new \RuntimeException('Avatar upload directory could not be created.');
            }
            $filename = bin2hex(random_bytes(16)) . '.jpg';
            $path = $directory . $filename;
            try {
                // A bounded JPEG thumbnail is consistent for display and strips source metadata.
                service('image')->withFile($upload->getTempName())->fit(320, 320, 'center')->convert(IMAGETYPE_JPEG)->save($path, 82);
            } catch (\Throwable $exception) {
                log_message('error', 'Avatar preparation failed: {message}', ['message' => $exception->getMessage()]);
                $errors['avatar'] = 'The image could not be prepared. Please choose another JPG or PNG.';
                return view('users/form', ['title' => $id ? 'Edit User' : 'New User', 'activePage' => 'users', 'user' => $user, 'errors' => $errors, 'values' => $values]);
            }
            $values['avatar'] = 'uploads/avatars/' . $filename;
        }
        if ($id === null) {
            $values['created_at'] = date('Y-m-d H:i:s');
            $model->insert($values);
        } else {
            $model->update($id, $values);
        }
        return redirect()->to(site_url('users'))->with('success', $id ? 'User updated.' : 'User created.');
    }
}

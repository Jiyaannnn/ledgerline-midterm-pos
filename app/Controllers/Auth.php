<?php
namespace App\Controllers;
use App\Models\UserModel;
class Auth extends BaseController {
    public function login() {
        if (session()->get('staff_id')) return redirect()->to(site_url('tasks'));
        return view('auth/login', ['title' => 'Staff sign in', 'activePage' => 'login', 'error' => null, 'username' => '', 'notice' => session()->get('auth_notice')]);
    }
    public function attempt() {
        $username = strtolower(trim((string) $this->request->getPost('username')));
        $password = (string) $this->request->getPost('password');
        $user = $username === '' ? null : (new UserModel())->where('username', $username)->where('is_archived', 0)->first();
        // Verify the stored hash; never compare or store a plain text password.
        if ($user === null || ! password_verify($password, (string) ($user['password'] ?? ''))) {
            return view('auth/login', ['title' => 'Staff sign in', 'activePage' => 'login', 'error' => 'The username or password is incorrect.', 'username' => $username, 'notice' => session()->get('auth_notice')]);
        }
        // Rotate the session ID after login to prevent reuse of a pre-login session.
        session()->regenerate(true);
        session()->set(['staff_id' => (int) $user['id'], 'staff_name' => $user['full_name']]);
        $requested = (string) session()->get('auth_redirect');
        session()->remove(['auth_redirect', 'auth_notice']);
        // Only known local GET routes are allowed after sign in.
        $destination = preg_match('~^/(?:tasks(?:/[0-9]+/edit|/new)?|customers(?:/[0-9]+/edit|/new)?|users(?:/[0-9]+/edit|/new)?|products(?:/[0-9]+/edit|/new)?|sales(?:/new)?)$~', $requested)
            ? site_url(ltrim($requested, '/')) : site_url('tasks');
        return redirect()->to($destination)->with('success', 'Welcome back, ' . $user['full_name'] . '.');
    }
    public function logout() {
        session()->destroy();
        return redirect()->to(site_url('login'));
    }
}

<?php
namespace App\Filters;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UserModel;
class AuthFilter implements FilterInterface {
    public function before(RequestInterface $request, $arguments = null) {
        $staffId = (int) session()->get('staff_id');
        if ($staffId > 0 && (new UserModel())->where('id', $staffId)->where('is_archived', 0)->first() !== null) return;
        // A staff account archived in another session loses management access immediately.
        session()->remove(['staff_id', 'staff_name']);
        if ($request->getMethod() === 'GET') {
            $path = '/' . ltrim($request->getUri()->getPath(), '/');
            session()->set('auth_redirect', $path);
            session()->set('auth_notice', str_starts_with($path, '/tasks')
                ? 'Sign in to manage shop tasks.'
                : (str_starts_with($path, '/customers') ? 'Sign in to view customer accounts.' : (str_starts_with($path, '/products') ? 'Sign in to manage products.' : (str_starts_with($path, '/sales') ? 'Sign in to record and view sales.' : 'Sign in to view staff accounts.'))));
        }
        return redirect()->to(site_url('login'));
    }
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}

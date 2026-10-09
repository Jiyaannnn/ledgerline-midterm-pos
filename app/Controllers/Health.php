<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Health extends Controller
{
    public function index()
    {
        try {
            // Readiness includes database access, without exposing connection details.
            db_connect()->query('SELECT 1');
            return service('response')->setContentType('text/plain')->setBody('ok');
        } catch (\Throwable) {
            return service('response')->setStatusCode(503)->setContentType('text/plain')->setBody('unavailable');
        }
    }
}

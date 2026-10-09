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
        } catch (\Throwable $exception) {
            // Keep the public response generic, but expose a safe failure category
            // in service logs so deployment issues can be diagnosed without
            // recording credentials, hostnames, or database error messages.
            $cause = $exception;
            while ($cause->getPrevious() !== null) {
                $cause = $cause->getPrevious();
            }
            error_log(sprintf(
                'Database health check failed: %s (code %d).',
                $cause::class,
                (int) $cause->getCode()
            ));
            return service('response')->setStatusCode(503)->setContentType('text/plain')->setBody('unavailable');
        }
    }
}

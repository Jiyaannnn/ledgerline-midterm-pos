<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
// A route connects a browser URL to the controller method that should handle it.
$routes->get('/', 'Pages::index');
$routes->get('/health', 'Health::index');
$routes->get('/about', 'Pages::about');
$routes->get('/login', 'Auth::login');
$routes->post('/login', 'Auth::attempt', ['filter' => 'csrf']);
$routes->post('/logout', 'Auth::logout', ['filter' => 'csrf']);
// Task changes use POST with CodeIgniter's CSRF filter, including deletion.
$routes->get('/tasks', 'Tasks::index');
$routes->get('/tasks/new', 'Tasks::new', ['filter' => 'auth']);
$routes->post('/tasks', 'Tasks::create', ['filter' => ['auth', 'csrf']]);
$routes->get('/tasks/(:num)/edit', 'Tasks::edit/$1', ['filter' => 'auth']);
$routes->post('/tasks/(:num)', 'Tasks::update/$1', ['filter' => ['auth', 'csrf']]);
$routes->post('/tasks/(:num)/delete', 'Tasks::delete/$1', ['filter' => ['auth', 'csrf']]);
$routes->get('/profile', 'Profile::index');
// Protect both the pages and their write actions, including new and edit forms.
$routes->group('customers', ['filter' => 'auth'], static function ($routes): void {
    $routes->get('/', 'Customers::index');
    $routes->get('new', 'Customers::new');
    $routes->post('/', 'Customers::create', ['filter' => 'csrf']);
    $routes->get('(:num)/edit', 'Customers::edit/$1');
    $routes->post('(:num)', 'Customers::update/$1', ['filter' => 'csrf']);
    $routes->post('(:num)/delete', 'Customers::remove/$1', ['filter' => 'csrf']);
});
$routes->group('users', ['filter' => 'auth'], static function ($routes): void {
    $routes->get('/', 'Users::index');
    $routes->get('new', 'Users::new');
    $routes->post('/', 'Users::create', ['filter' => 'csrf']);
    $routes->get('(:num)/edit', 'Users::edit/$1');
    $routes->post('(:num)', 'Users::update/$1', ['filter' => 'csrf']);
    $routes->post('(:num)/delete', 'Users::remove/$1', ['filter' => 'csrf']);
});
$routes->group('products', ['filter' => 'auth'], static function ($routes): void {
    $routes->get('/', 'Products::index');
    $routes->get('new', 'Products::new');
    $routes->post('/', 'Products::create', ['filter' => 'csrf']);
    $routes->get('(:num)/edit', 'Products::edit/$1');
    $routes->post('(:num)', 'Products::update/$1', ['filter' => 'csrf']);
    $routes->post('(:num)/archive', 'Products::archive/$1', ['filter' => 'csrf']);
});
$routes->group('sales', ['filter' => 'auth'], static function ($routes): void {
    $routes->get('/', 'Sales::index');
    $routes->get('new', 'Sales::new');
    $routes->post('/', 'Sales::create', ['filter' => 'csrf']);
});

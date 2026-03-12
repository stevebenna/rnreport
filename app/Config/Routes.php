<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'AuthController::login');
$routes->post('/login', 'AuthController::login');
$routes->get('/logout', 'AuthController::logout');
$routes->get('/dashboard', 'Dashboard::index', ['filter' => 'auth']);

$routes->group('', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/canzoni', 'Songs::index');
    $routes->get('/canzoni/create', 'Songs::create');
    $routes->post('/canzoni/store', 'Songs::store');
    $routes->get('/canzoni/show/(:any)', 'Songs::show/$1');
    $routes->get('/canzoni/edit/(:any)', 'Songs::edit/$1');
    $routes->post('/canzoni/update/(:any)', 'Songs::update/$1');
    $routes->post('/canzoni/delete/(:any)', 'Songs::delete/$1');
});
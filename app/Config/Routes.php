<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Auth Routes
$routes->group('auth', function($routes) {
    $routes->get('login', 'Auth\AuthController::login');
    $routes->post('process-login', 'Auth\AuthController::processLogin');
    $routes->get('register', 'Auth\AuthController::register');
    $routes->post('process-register', 'Auth\AuthController::processRegister');
    $routes->post('guest-checkout', 'Auth\AuthController::guestCheckout');
    $routes->get('logout', 'Auth\AuthController::logout');
});
<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// ========================================
// HOME
// ========================================
$routes->get('/', 'Home::index');

// ========================================
// AUTH ROUTES
// ========================================
$routes->group('auth', function($routes) {
    $routes->get('login', 'Auth\AuthController::login');
    $routes->post('process-login', 'Auth\AuthController::processLogin');
    $routes->get('register', 'Auth\AuthController::register');
    $routes->post('process-register', 'Auth\AuthController::processRegister');
    $routes->post('guest-checkout', 'Auth\AuthController::guestCheckout');
    $routes->get('logout', 'Auth\AuthController::logout');
    $routes->get('send-otp', 'Auth\AuthController::sendOtpPage');
    $routes->post('send-otp', 'Auth\AuthController::sendOtp');
    $routes->get('verify-otp', 'Auth\AuthController::verifyOtpPage');
    $routes->post('verify-otp', 'Auth\AuthController::verifyOtp');
    $routes->get('forgot-password', 'Auth\AuthController::forgotPasswordPage');
    $routes->post('request-reset-password', 'Auth\AuthController::requestResetPassword');
    $routes->get('reset-password', 'Auth\AuthController::resetPasswordPage');
    $routes->post('reset-password', 'Auth\AuthController::resetPassword');
});

// ========================================
// PUBLIC CATALOG ROUTES
// ========================================
$routes->get('services', 'Catalog\ServiceController::index');
$routes->get('services/(:num)', 'Catalog\ServiceController::detail/$1');
$routes->get('gallery', 'Catalog\GalleryController::index');
$routes->get('gallery/(:num)', 'Catalog\GalleryController::detail/$1');
$routes->get('branches', 'Catalog\BranchController::index');
$routes->get('branches/active', 'Catalog\BranchController::active');
$routes->get('branches/(:num)', 'Catalog\BranchController::detail/$1');

// ========================================
// ORDER ROUTES
// ========================================
$routes->group('order', function($routes) {
    $routes->get('create', 'Order\CheckoutController::createPage');
    $routes->post('checkout', 'Order\CheckoutController::processCheckout', ['filter' => 'auth']);
    $routes->post('guest-checkout', 'Order\CheckoutController::processGuestCheckout');
    $routes->get('track/(:segment)', 'Order\OrderController::track/$1');
    $routes->get('detail/(:segment)', 'Order\OrderController::detail/$1', ['filter' => 'auth']);
    $routes->put('(:segment)/update', 'Order\OrderController::update/$1', ['filter' => 'auth']);
    $routes->delete('(:segment)/cancel', 'Order\OrderController::cancel/$1', ['filter' => 'auth']);
});

// ========================================
// GUEST ORDER ROUTES
// ========================================
$routes->get('guest/order/(:segment)', 'Order\GuestOrderController::detail/$1');

// ========================================
// USER ROUTES (Protected)
// ========================================
$routes->group('user', ['filter' => 'auth'], function($routes) {
    $routes->get('profile', 'User\ProfileController::index');
    $routes->post('profile/update', 'User\ProfileController::update');
    $routes->get('orders', 'User\OrderController::myOrders');
    $routes->get('orders/(:segment)', 'User\OrderController::detail/$1');
});

// ========================================
// ADMIN ROUTES (Protected)
// ========================================
$routes->group('admin', ['filter' => 'admin'], function($routes) {
    $routes->get('dashboard', 'Admin\DashboardController::index');
    
    // User Management
    $routes->get('users', 'Admin\UserController::index');
    $routes->get('users/(:num)', 'Admin\UserController::show/$1');
    $routes->post('users', 'Admin\UserController::create');
    $routes->put('users/(:num)', 'Admin\UserController::update/$1');
    $routes->delete('users/(:num)', 'Admin\UserController::delete/$1');
    
    // Order Management
    $routes->get('orders', 'Admin\OrderController::index');
    $routes->get('orders/(:segment)', 'Admin\OrderController::detail/$1');
    $routes->patch('orders/(:segment)/status', 'Admin\OrderController::updateStatus/$1');
    $routes->delete('orders/(:segment)', 'Admin\OrderController::cancel/$1');
    
    // Branch Management
    $routes->get('branches', 'Admin\BranchController::index');
    $routes->post('branches', 'Admin\BranchController::create');
    $routes->put('branches/(:num)', 'Admin\BranchController::update/$1');
    $routes->delete('branches/(:num)', 'Admin\BranchController::delete/$1');
    $routes->patch('branches/(:num)/activate', 'Admin\BranchController::activate/$1');
    $routes->patch('branches/(:num)/deactivate', 'Admin\BranchController::deactivate/$1');
    
    // Service Management
    $routes->get('services', 'Admin\ServiceController::index');
    $routes->post('services', 'Admin\ServiceController::create');
    $routes->put('services/(:num)', 'Admin\ServiceController::update/$1');
    $routes->delete('services/(:num)', 'Admin\ServiceController::delete/$1');
    $routes->patch('services/(:num)/activate', 'Admin\ServiceController::activate/$1');
    $routes->patch('services/(:num)/deactivate', 'Admin\ServiceController::deactivate/$1');
    
    // Gallery Management
    $routes->get('gallery', 'Admin\GalleryController::index');
    $routes->post('gallery', 'Admin\GalleryController::create');
    $routes->put('gallery/(:num)', 'Admin\GalleryController::update/$1');
    $routes->delete('gallery/(:num)', 'Admin\GalleryController::delete/$1');
    $routes->patch('gallery/(:num)/activate', 'Admin\GalleryController::activate/$1');
    $routes->patch('gallery/(:num)/deactivate', 'Admin\GalleryController::deactivate/$1');
});

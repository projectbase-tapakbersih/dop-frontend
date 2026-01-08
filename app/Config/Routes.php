<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// ==========================================================
// PUBLIC ROUTES
// ==========================================================
$routes->get('/', 'Home::index');

// ==========================================================
// CATALOG ROUTES (Public)
// ==========================================================
$routes->get('services', 'Catalog\ServiceController::index');
$routes->get('services/(:segment)', 'Catalog\ServiceController::detail/$1');
$routes->get('services/(:num)/gallery', 'Catalog\ServiceController::gallery/$1');
$routes->get('layanan', 'Catalog\ServiceController::index');

$routes->get('branches', 'Catalog\BranchController::index');
$routes->get('branches/(:segment)', 'Catalog\BranchController::detail/$1');
$routes->get('cabang', 'Catalog\BranchController::index');

$routes->get('gallery', 'Catalog\GalleryController::index');
$routes->get('gallery/(:num)', 'Catalog\GalleryController::detail/$1');

// ==========================================================
// AUTHENTICATION ROUTES
// ==========================================================
$routes->group('auth', function($routes) {
    // Login
    $routes->get('login', 'Auth\AuthController::loginPage');
    $routes->post('login', 'Auth\AuthController::login');
    $routes->post('process-login', 'Auth\AuthController::login');
    
    // Register
    $routes->get('register', 'Auth\AuthController::registerPage');
    $routes->post('register', 'Auth\AuthController::register');
    $routes->post('process-register', 'Auth\AuthController::register');
    
    // Logout
    $routes->get('logout', 'Auth\AuthController::logout');
    $routes->post('logout', 'Auth\AuthController::logout');
    
    // OTP
    $routes->post('send-otp', 'Auth\AuthController::sendOtp');
    $routes->post('verify-otp', 'Auth\AuthController::verifyOtp');
    
    // Password Reset
    $routes->get('forgot-password', 'Auth\AuthController::forgotPasswordPage');
    $routes->post('forgot-password', 'Auth\AuthController::requestResetPassword');
    $routes->post('request-reset-password', 'Auth\AuthController::requestResetPassword');
    $routes->get('reset-password', 'Auth\AuthController::resetPasswordPage');
    $routes->post('reset-password', 'Auth\AuthController::resetPassword');
    
    // Debug (development only)
    $routes->get('debug-session', 'Auth\AuthController::debugSession');
});

// ==========================================================
// ORDER/CHECKOUT ROUTES
// ==========================================================
$routes->group('order', function($routes) {
    $routes->get('create', 'Order\CheckoutController::createPage');
    $routes->get('checkout', 'Order\CheckoutController::checkoutPage');
    $routes->post('checkout', 'Order\CheckoutController::processCheckout');
    $routes->post('guest-checkout', 'Order\CheckoutController::processGuestCheckout');
});

// ==========================================================
// GUEST ORDER ROUTES
// ==========================================================
$routes->group('guest', function($routes) {
    $routes->get('track', 'Order\GuestOrderController::trackPage');
    $routes->post('track', 'Order\GuestOrderController::track');
    $routes->get('order/(:segment)', 'Order\GuestOrderController::detail/$1');
    $routes->post('order/(:segment)/cancel', 'Order\GuestOrderController::cancel/$1');
});

// ==========================================================
// PAYMENT ROUTES
// ==========================================================
$routes->group('payment', function($routes) {
    $routes->get('(:segment)', 'Payment\PaymentController::show/$1');
    $routes->post('(:segment)/pay', 'Payment\PaymentController::pay/$1');
    $routes->get('(:segment)/status', 'Payment\PaymentController::status/$1');
    $routes->post('(:segment)/cancel', 'Payment\PaymentController::cancel/$1');
    $routes->get('(:segment)/success', 'Payment\PaymentController::success/$1');
});

// ==========================================================
// USER ROUTES
// ==========================================================
$routes->group('user', function($routes) {
    $routes->get('dashboard', 'User\DashboardController::index');
    
    $routes->get('profile', 'User\ProfileController::index');
    $routes->post('profile/update', 'User\ProfileController::update');
    
    $routes->get('orders', 'User\OrderController::myOrders');
    $routes->get('orders/(:segment)', 'User\OrderController::detail/$1');
    $routes->post('orders/(:segment)/cancel', 'User\OrderController::cancelOrder/$1');
});

// ==========================================================
// ADMIN ROUTES
// ==========================================================
$routes->group('admin', function($routes) {
    
    // Dashboard
    $routes->get('/', 'Admin\DashboardController::index');
    $routes->get('dashboard', 'Admin\DashboardController::index');
    $routes->get('debug', 'Admin\DashboardController::debug');
    
    // Orders
    $routes->group('orders', function($routes) {
        $routes->get('/', 'Admin\OrderController::index');
        $routes->get('(:segment)', 'Admin\OrderController::show/$1');
        $routes->post('(:segment)/status', 'Admin\OrderController::updateStatus/$1');
        $routes->post('(:segment)/cancel', 'Admin\OrderController::cancel/$1');
    });
    
    // Users
    $routes->group('users', function($routes) {
        $routes->get('/', 'Admin\UserController::index');
        $routes->get('debug', 'Admin\UserController::debug');
        $routes->post('store', 'Admin\UserController::store');
        $routes->get('(:num)', 'Admin\UserController::show/$1');
        $routes->post('(:num)/update', 'Admin\UserController::update/$1');
        $routes->post('(:num)/delete', 'Admin\UserController::delete/$1');
    });
    
    // Services
    $routes->group('services', function($routes) {
        $routes->get('/', 'Admin\ServiceController::index');
        $routes->post('store', 'Admin\ServiceController::store');
        $routes->post('(:num)/update', 'Admin\ServiceController::update/$1');
        $routes->post('(:num)/delete', 'Admin\ServiceController::delete/$1');
        $routes->post('(:num)/activate', 'Admin\ServiceController::activate/$1');
        $routes->post('(:num)/deactivate', 'Admin\ServiceController::deactivate/$1');
    });
    
    // Branches
    $routes->group('branches', function($routes) {
        $routes->get('/', 'Admin\BranchController::index');
        $routes->post('store', 'Admin\BranchController::store');
        $routes->post('(:num)/update', 'Admin\BranchController::update/$1');
        $routes->post('(:num)/delete', 'Admin\BranchController::delete/$1');
        $routes->post('(:num)/activate', 'Admin\BranchController::activate/$1');
        $routes->post('(:num)/deactivate', 'Admin\BranchController::deactivate/$1');
    });
    
    // Gallery - NEW!
    $routes->group('gallery', function($routes) {
        $routes->get('/', 'Admin\GalleryController::index');
        $routes->post('store', 'Admin\GalleryController::store');
        $routes->get('(:num)', 'Admin\GalleryController::show/$1');
        $routes->post('(:num)/update', 'Admin\GalleryController::update/$1');
        $routes->post('(:num)/delete', 'Admin\GalleryController::delete/$1');
        $routes->post('(:num)/activate', 'Admin\GalleryController::activate/$1');
        $routes->post('(:num)/deactivate', 'Admin\GalleryController::deactivate/$1');
    });
    
    // Promo Codes
    $routes->group('promo-codes', function($routes) {
        $routes->get('/', 'Admin\PromoCodeController::index');
        $routes->post('store', 'Admin\PromoCodeController::store');
        $routes->get('(:num)', 'Admin\PromoCodeController::show/$1');
        $routes->post('(:num)/update', 'Admin\PromoCodeController::update/$1');
        $routes->post('(:num)/delete', 'Admin\PromoCodeController::delete/$1');
        $routes->post('(:num)/toggle', 'Admin\PromoCodeController::toggle/$1');
    });
    
    // Reports
    $routes->group('reports', function($routes) {
        $routes->get('/', 'Admin\ReportController::index');
        $routes->get('revenue', 'Admin\ReportController::revenue');
        $routes->get('orders', 'Admin\ReportController::orders');
    });
});
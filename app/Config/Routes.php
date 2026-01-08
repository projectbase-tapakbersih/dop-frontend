<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

/*
 * ==========================================================
 * TIRTA BERSIH LAUNDRY - ROUTE CONFIGURATION
 * ==========================================================
 * 
 * Updated based on Laravel API Documentation (Postman Collection)
 * 
 * API Base URL: http://localhost:8000/api
 * Frontend: http://localhost:8080
 */

// ==========================================================
// PUBLIC ROUTES (No Authentication Required)
// ==========================================================

// Home
$routes->get('/', 'Home::index');

// ==========================================================
// CATALOG ROUTES (Public)
// API: GET /api/services, GET /api/branches, GET /api/gallery
// ==========================================================

// Services
$routes->get('services', 'Catalog\ServiceController::index');
$routes->get('services/(:segment)', 'Catalog\ServiceController::detail/$1');
$routes->get('services/(:num)/gallery', 'Catalog\ServiceController::gallery/$1');
$routes->get('layanan', 'Catalog\ServiceController::index');

// Branches
$routes->get('branches', 'Catalog\BranchController::index');
$routes->get('branches/(:segment)', 'Catalog\BranchController::detail/$1');
$routes->get('cabang', 'Catalog\BranchController::index');

// Gallery
$routes->get('gallery', 'Catalog\GalleryController::index');
$routes->get('gallery/(:num)', 'Catalog\GalleryController::detail/$1');

// ==========================================================
// AUTHENTICATION ROUTES
// API: POST /api/auth/login, /api/auth/register, etc.
// ==========================================================
$routes->group('auth', function($routes) {
    $routes->get('login', 'Auth\AuthController::loginPage');
    $routes->post('login', 'Auth\AuthController::login');
    
    $routes->get('register', 'Auth\AuthController::registerPage');
    $routes->post('register', 'Auth\AuthController::register');
    
    $routes->get('logout', 'Auth\AuthController::logout');
    $routes->post('logout', 'Auth\AuthController::logout');
    
    // OTP
    $routes->post('send-otp', 'Auth\AuthController::sendOtp');
    $routes->post('verify-otp', 'Auth\AuthController::verifyOtp');
    
    // Password Reset
    $routes->get('forgot-password', 'Auth\AuthController::forgotPasswordPage');
    $routes->post('request-reset-password', 'Auth\AuthController::requestResetPassword');
    $routes->get('reset-password', 'Auth\AuthController::resetPasswordPage');
    $routes->post('reset-password', 'Auth\AuthController::resetPassword');
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
// API: POST /api/guest/orders, GET /api/guest/orders/{order_number}
// ==========================================================
$routes->group('guest', function($routes) {
    $routes->get('track', 'Order\GuestOrderController::trackPage');
    $routes->post('track', 'Order\GuestOrderController::track');
    $routes->get('order/(:segment)', 'Order\GuestOrderController::detail/$1');
    $routes->post('order/(:segment)/cancel', 'Order\GuestOrderController::cancel/$1');
});

// ==========================================================
// PAYMENT ROUTES
// API: POST /api/payments/{order_number}, etc.
// ==========================================================
$routes->group('payment', function($routes) {
    $routes->get('(:segment)', 'Payment\PaymentController::show/$1');
    $routes->post('(:segment)/pay', 'Payment\PaymentController::pay/$1');
    $routes->get('(:segment)/status', 'Payment\PaymentController::status/$1');
    $routes->post('(:segment)/cancel', 'Payment\PaymentController::cancel/$1');
    $routes->get('(:segment)/success', 'Payment\PaymentController::success/$1');
});

// ==========================================================
// USER ROUTES (Authenticated Users)
// API: GET /api/user/orders, POST /api/user/checkout, etc.
// ==========================================================
$routes->group('user', function($routes) {
    $routes->get('dashboard', 'User\DashboardController::index');
    
    // Profile
    $routes->get('profile', 'User\ProfileController::index');
    $routes->post('profile/update', 'User\ProfileController::update');
    
    // Orders - API: GET /api/user/orders
    $routes->get('orders', 'User\OrderController::myOrders');
    
    // Order Detail - API: GET /api/user/order/{orderNumber} (singular!)
    $routes->get('orders/(:segment)', 'User\OrderController::detail/$1');
    
    // Cancel Order - API: POST /api/user/order/{orderNumber}/cancel
    $routes->post('orders/(:segment)/cancel', 'User\OrderController::cancelOrder/$1');
});

// ==========================================================
// ADMIN ROUTES
// ==========================================================
$routes->group('admin', function($routes) {
    
    // Dashboard
    $routes->get('/', 'Admin\DashboardController::index');
    $routes->get('dashboard', 'Admin\DashboardController::index');
    
    // ------------------------------------------------------
    // ORDERS MANAGEMENT
    // API: GET/DELETE /api/admin/orders, PATCH /api/admin/orders/{order}/status
    // ------------------------------------------------------
    $routes->group('orders', function($routes) {
        $routes->get('/', 'Admin\OrderController::index');
        $routes->get('(:segment)', 'Admin\OrderController::show/$1');
        $routes->post('(:segment)/status', 'Admin\OrderController::updateStatus/$1');
        $routes->post('(:segment)/cancel', 'Admin\OrderController::cancel/$1');
    });
    
    // ------------------------------------------------------
    // USERS MANAGEMENT
    // API: /api/admin/users
    // ------------------------------------------------------
    $routes->group('users', function($routes) {
        $routes->get('/', 'Admin\UserController::index');
        $routes->get('debug', 'Admin\UserController::debug');
        $routes->post('store', 'Admin\UserController::store');
        $routes->get('(:num)', 'Admin\UserController::show/$1');
        $routes->post('(:num)/update', 'Admin\UserController::update/$1');
        $routes->post('(:num)/delete', 'Admin\UserController::delete/$1');
    });
    
    // ------------------------------------------------------
    // SERVICES MANAGEMENT
    // API: POST /api/admin/services, PUT/DELETE /api/admin/services/{id}
    // API: PATCH /api/admin/services/{id}/activate, /deactivate
    // ------------------------------------------------------
    $routes->group('services', function($routes) {
        $routes->get('/', 'Admin\ServiceController::index');
        $routes->post('store', 'Admin\ServiceController::store');
        $routes->post('(:num)/update', 'Admin\ServiceController::update/$1');
        $routes->post('(:num)/delete', 'Admin\ServiceController::delete/$1');
        $routes->post('(:num)/activate', 'Admin\ServiceController::activate/$1');
        $routes->post('(:num)/deactivate', 'Admin\ServiceController::deactivate/$1');
    });
    
    // ------------------------------------------------------
    // BRANCHES MANAGEMENT
    // API: POST /api/admin/branches, PUT/DELETE /api/admin/branches/{id}
    // API: PATCH /api/admin/branches/{id}/activate, /deactivate
    // ------------------------------------------------------
    $routes->group('branches', function($routes) {
        $routes->get('/', 'Admin\BranchController::index');
        $routes->post('store', 'Admin\BranchController::store');
        $routes->post('(:num)/update', 'Admin\BranchController::update/$1');
        $routes->post('(:num)/delete', 'Admin\BranchController::delete/$1');
        $routes->post('(:num)/activate', 'Admin\BranchController::activate/$1');
        $routes->post('(:num)/deactivate', 'Admin\BranchController::deactivate/$1');
    });
    
    // ------------------------------------------------------
    // GALLERY MANAGEMENT
    // API: POST /api/admin/services/{id}/gallery
    // API: PUT/DELETE /api/admin/gallery/{id}
    // API: PATCH /api/admin/gallery/{id}/activate, /deactivate
    // ------------------------------------------------------
    $routes->group('gallery', function($routes) {
        $routes->get('/', 'Admin\GalleryController::index');
        $routes->post('service/(:num)/store', 'Admin\GalleryController::store/$1');
        $routes->post('(:num)/update', 'Admin\GalleryController::update/$1');
        $routes->post('(:num)/delete', 'Admin\GalleryController::delete/$1');
        $routes->post('(:num)/activate', 'Admin\GalleryController::activate/$1');
        $routes->post('(:num)/deactivate', 'Admin\GalleryController::deactivate/$1');
    });
    
    // ------------------------------------------------------
    // PROMO CODES MANAGEMENT
    // API: /api/admin/promo-codes
    // ------------------------------------------------------
    $routes->group('promo-codes', function($routes) {
        $routes->get('/', 'Admin\PromoCodeController::index');
        $routes->post('store', 'Admin\PromoCodeController::store');
        $routes->get('(:num)', 'Admin\PromoCodeController::show/$1');
        $routes->post('(:num)/update', 'Admin\PromoCodeController::update/$1');
        $routes->post('(:num)/delete', 'Admin\PromoCodeController::delete/$1');
        $routes->post('(:num)/toggle', 'Admin\PromoCodeController::toggle/$1');
    });
    
    // ------------------------------------------------------
    // REPORTS
    // ------------------------------------------------------
    $routes->group('reports', function($routes) {
        $routes->get('/', 'Admin\ReportController::index');
        $routes->get('revenue', 'Admin\ReportController::revenue');
        $routes->get('orders', 'Admin\ReportController::orders');
    });
});
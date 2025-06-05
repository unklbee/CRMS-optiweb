<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Default route
$routes->get('/', 'Frontend\HomeController::index');

// Admin routes
$routes->group('admin', ['namespace' => 'App\Controllers\Admin'], function($routes) {
    // Auth routes (tidak pakai filter auth)
    $routes->get('login', 'AuthController::login', ['filter' => 'guest']);
    $routes->post('login', 'AuthController::authenticate');
    $routes->get('logout', 'AuthController::logout');

    // Dashboard (requires auth)
    $routes->group('', ['filter' => 'auth'], function($routes) {
        $routes->get('/', 'DashboardController::index'); // redirect ke dashboard
        $routes->get('dashboard', 'DashboardController::index');

        // Order management - UPDATED WITH NEW ROUTES
        $routes->get('orders', 'OrderController::index');
        $routes->get('orders/new', 'OrderController::new');
        $routes->post('orders', 'OrderController::create');
        $routes->get('orders/(:num)', 'OrderController::show/$1');
        $routes->get('orders/(:num)/edit', 'OrderController::edit/$1');
        $routes->put('orders/(:num)', 'OrderController::update/$1');
        $routes->delete('orders/(:num)', 'OrderController::delete/$1');

        // Status management
        $routes->get('orders/(:num)/status', 'OrderController::updateStatus/$1');
        $routes->post('orders/(:num)/status', 'OrderController::saveStatus/$1');

        // NEW: Parts management in orders
        $routes->get('orders/(:num)/manage-parts', 'OrderController::manageParts/$1');
        $routes->post('orders/(:num)/parts', 'OrderController::addPart/$1');
        $routes->delete('orders/(:num)/parts/(:num)/remove', 'OrderController::removePart/$1/$2');

        // Customer management
        $routes->get('customers', 'CustomerController::index');
        $routes->get('customers/new', 'CustomerController::new');
        $routes->post('customers', 'CustomerController::store');
        $routes->get('customers/(:num)', 'CustomerController::show/$1');
        $routes->get('customers/(:num)/edit', 'CustomerController::edit/$1');
        $routes->put('customers/(:num)', 'CustomerController::update/$1');
        $routes->delete('customers/(:num)', 'CustomerController::delete/$1');

        // Service management
        $routes->get('services', 'ServiceController::index');
        $routes->get('services/new', 'ServiceController::new');
        $routes->post('services', 'ServiceController::store');
        $routes->get('services/(:num)', 'ServiceController::show/$1');
        $routes->get('services/(:num)/edit', 'ServiceController::edit/$1');
        $routes->put('services/(:num)', 'ServiceController::update/$1');
        $routes->delete('services/(:num)', 'ServiceController::delete/$1');

        $routes->get('service-categories', 'ServiceCategoryController::index');
        $routes->get('service-categories/new', 'ServiceCategoryController::new');
        $routes->post('service-categories', 'ServiceCategoryController::store');
        $routes->get('service-categories/(:num)', 'ServiceCategoryController::show/$1');
        $routes->get('service-categories/(:num)/edit', 'ServiceCategoryController::edit/$1');
        $routes->put('service-categories/(:num)', 'ServiceCategoryController::update/$1');
        $routes->delete('service-categories/(:num)', 'ServiceCategoryController::delete/$1');

        // Parts management - UPDATED WITH NEW ROUTES
        $routes->get('parts', 'PartController::index');
        $routes->get('parts/new', 'PartController::new');
        $routes->post('parts', 'PartController::store');
        $routes->get('parts/(:num)', 'PartController::show/$1');
        $routes->get('parts/(:num)/edit', 'PartController::edit/$1');
        $routes->put('parts/(:num)', 'PartController::update/$1');
        $routes->delete('parts/(:num)', 'PartController::delete/$1');

        // NEW: Stock management routes
        $routes->get('parts/(:num)/adjust-stock', 'PartController::adjustStock/$1');
        $routes->post('parts/(:num)/adjust-stock', 'PartController::updateStock/$1');

        // User management
        $routes->get('users', 'UserController::index');
        $routes->get('users/new', 'UserController::create');
        $routes->post('users', 'UserController::store');
        $routes->get('users/(:num)', 'UserController::show/$1');
        $routes->get('users/(:num)/edit', 'UserController::edit/$1');
        $routes->put('users/(:num)', 'UserController::update/$1');
        $routes->delete('users/(:num)', 'UserController::delete/$1');

        // CMS management
        $routes->get('pages', 'PageController::index');
        $routes->get('pages/new', 'PageController::new');
        $routes->post('pages', 'PageController::store');
        $routes->get('pages/(:num)', 'PageController::show/$1');
        $routes->get('pages/(:num)/edit', 'PageController::edit/$1');
        $routes->put('pages/(:num)', 'PageController::update/$1');
        $routes->delete('pages/(:num)', 'PageController::delete/$1');
        $routes->post('pages/(:num)/duplicate', 'PageController::duplicate/$1');

        // Settings
        $routes->get('settings', 'SettingController::index');
        $routes->post('settings', 'SettingController::update');
        $routes->get('settings/new', 'SettingController::create');
        $routes->post('settings/store', 'SettingController::store');
        $routes->get('settings/(:num)/edit', 'SettingController::edit/$1');
        $routes->put('settings/(:num)', 'SettingController::updateSingle/$1');
        $routes->delete('settings/(:num)', 'SettingController::delete/$1');

        // NEW: Settings backup & restore
        $routes->get('settings/backup', 'SettingController::backup');
        $routes->get('settings/restore', 'SettingController::restore');
        $routes->post('settings/restore', 'SettingController::processRestore');
        $routes->get('settings/cache', 'SettingController::cache');
        $routes->post('settings/clear-cache', 'SettingController::clearCache');

        // NEW: API routes for AJAX calls (within admin area)
        $routes->group('api', function($routes) {
            // Stock movement API
            $routes->get('parts/(:num)/movements', 'PartController::getMovements/$1');
            $routes->get('parts/(:num)/usage-stats', 'PartController::getUsageStats/$1');

            // Order API
            $routes->get('orders/(:num)/parts', 'OrderController::getOrderParts/$1');
            $routes->get('orders/(:num)/movements', 'OrderController::getOrderMovements/$1');

            // Dashboard API
            $routes->get('dashboard/stats', 'DashboardController::getStats');
            $routes->get('dashboard/recent-activities', 'DashboardController::getRecentActivities');

            // Search API
            $routes->get('search/parts', 'DashboardController::searchParts');
            $routes->get('search/customers', 'DashboardController::searchCustomers');
        });
    });
});

// API routes for frontend
$routes->group('api', ['namespace' => 'App\Controllers\Api'], function($routes) {
    $routes->post('orders', 'OrderController::create');
    $routes->get('orders/(:segment)', 'OrderController::show/$1');
    $routes->get('services', 'ServiceController::index');
    $routes->get('service-categories', 'ServiceCategoryController::index');
});

// Frontend routes
$routes->group('', ['namespace' => 'App\Controllers\Frontend'], function($routes) {
    $routes->get('services', 'ServiceController::index');
    $routes->get('services/(:segment)', 'ServiceController::show/$1');
    $routes->get('track-order', 'OrderController::track');
    $routes->post('track-order', 'OrderController::checkStatus');
    $routes->get('book-service', 'OrderController::create');
    $routes->post('book-service', 'OrderController::store');
    $routes->get('contact', 'ContactController::index');
    $routes->post('contact', 'ContactController::send');
    $routes->get('(:segment)', 'PageController::show/$1'); // CMS pages
});
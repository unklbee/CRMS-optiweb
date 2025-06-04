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

        // Order management - specific routes first, then resource
        $routes->get('orders/(:num)/status', 'OrderController::updateStatus/$1');
        $routes->post('orders/(:num)/status', 'OrderController::saveStatus/$1');
        $routes->resource('orders', ['controller' => 'OrderController']);

        // Customer management
        $routes->resource('customers', ['controller' => 'CustomerController']);

        // Service management
        $routes->resource('services', ['controller' => 'ServiceController']);
        $routes->resource('service-categories', ['controller' => 'ServiceCategoryController']);

        // Parts management - specific routes first, then resource
        $routes->get('parts/(:num)/adjust-stock', 'PartController::adjustStock/$1');
        $routes->post('parts/(:num)/adjust-stock', 'PartController::updateStock/$1');
        $routes->resource('parts', ['controller' => 'PartController']);

        // User management
        $routes->resource('users', ['controller' => 'UserController']);

        // CMS management - specific routes first, then resource
        $routes->get('pages/(:num)/duplicate', 'PageController::duplicate/$1');
        $routes->resource('pages', ['controller' => 'PageController']);

        // Settings management
        $routes->get('settings', 'SettingController::index');
        $routes->post('settings', 'SettingController::update');
        $routes->get('settings/new', 'SettingController::create');
        $routes->post('settings/create', 'SettingController::store');
        $routes->get('settings/(:num)/edit', 'SettingController::edit/$1');
        $routes->post('settings/(:num)', 'SettingController::updateSingle/$1');
        $routes->delete('settings/(:num)', 'SettingController::delete/$1');
        $routes->get('settings/backup', 'SettingController::backup');
        $routes->get('settings/restore', 'SettingController::restore');
        $routes->post('settings/restore', 'SettingController::processRestore');
        $routes->get('settings/cache', 'SettingController::cache');
        $routes->post('settings/cache/clear', 'SettingController::clearCache');
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
    $routes->get('(:segment)', 'PageController::show/$1'); // CMS pages (harus di akhir)
});
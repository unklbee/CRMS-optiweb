<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Default route
$routes->get('/', 'Frontend\HomeController::index');

// PUBLIC QUOTATION ROUTES (Before admin group)
$routes->group('quotation', ['namespace' => 'App\Controllers'], function($routes) {
    $routes->get('(:num)', 'QuotationController::view/$1');
    $routes->get('(:num)/approve', 'QuotationController::approve/$1');
    $routes->post('(:num)/approve', 'QuotationController::approve/$1');
    $routes->get('(:num)/reject', 'QuotationController::reject/$1');
    $routes->post('(:num)/reject', 'QuotationController::reject/$1');
    $routes->get('(:num)/pdf', 'QuotationController::downloadPdf/$1');
    $routes->get('(:num)/status', 'QuotationController::checkStatus/$1'); // AJAX endpoint
});

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

        // ===============================================
        // ORDER MANAGEMENT - CLEANED UP (Diagnosis methods removed)
        // ===============================================
        $routes->get('orders', 'OrderController::index');
        $routes->get('orders/new', 'OrderController::create');
        $routes->post('orders', 'OrderController::store');
        $routes->get('orders/(:num)', 'OrderController::show/$1');
        $routes->get('orders/(:num)/edit', 'OrderController::edit/$1');
        $routes->put('orders/(:num)', 'OrderController::update/$1');
        $routes->delete('orders/(:num)', 'OrderController::delete/$1');

        // Status management
        $routes->get('orders/(:num)/status', 'OrderController::updateStatus/$1');
        $routes->post('orders/(:num)/status', 'OrderController::saveStatus/$1');

        // Parts management in orders
        $routes->get('orders/(:num)/manage-parts', 'OrderController::manageParts/$1');
        $routes->post('orders/(:num)/parts', 'OrderController::addPart/$1');
        $routes->delete('orders/(:num)/parts/(:num)/remove', 'OrderController::removePart/$1/$2');
        $routes->get('orders/(:num)/receipt', 'OrderController::receipt/$1');
        $routes->get('orders/(:num)/email-receipt', 'OrderController::emailReceipt/$1');
        $routes->get('orders/(:num)/delivery-receipt', 'OrderController::deliveryReceipt/$1');

        // BACKWARD COMPATIBILITY: Redirect old diagnosis URLs to new DiagnosisController
        $routes->get('orders/(:num)/diagnosis', 'OrderController::redirectToDiagnosis/$1');

        // ===============================================
        // NEW: SEPARATE DIAGNOSIS MANAGEMENT
        // ===============================================
        $routes->group('diagnosis', ['namespace' => 'App\Controllers\Admin'], function($routes) {
            // Main diagnosis queue and management
            $routes->get('/', 'DiagnosisController::index');
            $routes->get('queue', 'DiagnosisController::index'); // Alias for backward compatibility

            // Individual order diagnosis workflow
            $routes->get('(:num)/start', 'DiagnosisController::start/$1');
            $routes->get('(:num)/create', 'DiagnosisController::create/$1');
            $routes->post('(:num)', 'DiagnosisController::store/$1');
            $routes->get('(:num)', 'DiagnosisController::show/$1');
            $routes->post('(:num)/store', 'DiagnosisController::store/$1');
            $routes->get('(:num)/edit', 'DiagnosisController::edit/$1');
            $routes->put('(:num)', 'DiagnosisController::update/$1');

            // Templates and utilities
            $routes->get('templates', 'DiagnosisController::templates');
            $routes->get('templates/new', 'DiagnosisController::createTemplate');
            $routes->post('templates', 'DiagnosisController::storeTemplate');
            $routes->get('templates/(:num)/edit', 'DiagnosisController::editTemplate/$1');
            $routes->put('templates/(:num)', 'DiagnosisController::updateTemplate/$1');
            $routes->delete('templates/(:num)', 'DiagnosisController::deleteTemplate/$1');

            // AJAX endpoints
            $routes->get('device-type/(:num)/common-issues', 'DiagnosisController::getCommonIssues/$1');
            $routes->get('device-type/(:num)/templates', 'DiagnosisController::getTemplatesByDeviceType/$1');
            $routes->get('widget/queue', 'DiagnosisController::getQueueWidget');
        });

        // ===============================================
        // QUOTATION MANAGEMENT - UPDATED TO WORK WITH NEW DIAGNOSIS FLOW
        // ===============================================

        // Create quotation from diagnosed orders
        $routes->get('orders/(:num)/create-quotation', 'QuotationController::create/$1');
        $routes->post('orders/(:num)/quotation', 'QuotationController::store/$1');
        $routes->get('orders/(:num)/quotation', 'QuotationController::showOrderQuotation/$1');
        $routes->get('orders/(:num)/quotation/edit', 'QuotationController::editOrderQuotation/$1');
        $routes->post('orders/(:num)/quotation/revise', 'QuotationController::reviseQuotation/$1');

        // Individual quotation management
        $routes->get('quotations', 'QuotationController::index');
        $routes->get('quotations/pending', 'QuotationController::pending');
        $routes->get('quotations/expired', 'QuotationController::expired');
        $routes->get('quotations/analytics', 'QuotationController::analytics');
        $routes->get('quotations/export', 'QuotationController::export');

        $routes->get('quotations/(:num)', 'QuotationController::show/$1');
        $routes->get('quotations/create/(:num)', 'QuotationController::create/$1');
        $routes->get('quotations/(:num)/edit', 'QuotationController::edit/$1');
        $routes->put('quotations/(:num)', 'QuotationController::update/$1');
        $routes->delete('quotations/(:num)', 'QuotationController::delete/$1');

        $routes->get('quotations/(:num)/pdf', 'QuotationController::downloadPdf/$1');
        $routes->get('quotations/(:num)/duplicate', 'QuotationController::duplicate/$1');
        $routes->get('quotations/(:num)/send', 'QuotationController::send/$1');
        $routes->post('quotations/(:num)/send', 'QuotationController::processSend/$1');
        $routes->get('quotations/(:num)/send-reminder', 'QuotationController::sendReminder/$1');
        $routes->get('quotations/(:num)/mark-expired', 'QuotationController::markExpired/$1');
        $routes->get('quotations/(:num)/convert-to-invoice', 'QuotationController::convertToInvoice/$1');
        $routes->post('quotations/bulk-action', 'QuotationController::bulkAction');

        // Legacy quotation routes (for backward compatibility)
        $routes->get('orders/(:num)/quotation/(:num)/send', 'QuotationController::send/$2');

        // ===============================================
        // CUSTOMER MANAGEMENT
        // ===============================================
        $routes->get('customers', 'CustomerController::index');
        $routes->get('customers/new', 'CustomerController::new');
        $routes->post('customers', 'CustomerController::store');
        $routes->get('customers/(:num)', 'CustomerController::show/$1');
        $routes->get('customers/(:num)/edit', 'CustomerController::edit/$1');
        $routes->put('customers/(:num)', 'CustomerController::update/$1');
        $routes->delete('customers/(:num)', 'CustomerController::delete/$1');

        // ===============================================
        // SERVICE MANAGEMENT
        // ===============================================
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

        // ===============================================
        // PARTS MANAGEMENT
        // ===============================================
        $routes->get('parts', 'PartController::index');
        $routes->get('parts/new', 'PartController::new');
        $routes->post('parts', 'PartController::store');
        $routes->get('parts/(:num)', 'PartController::show/$1');
        $routes->get('parts/(:num)/edit', 'PartController::edit/$1');
        $routes->put('parts/(:num)', 'PartController::update/$1');
        $routes->delete('parts/(:num)', 'PartController::delete/$1');

        // Stock management routes
        $routes->get('parts/(:num)/adjust-stock', 'PartController::adjustStock/$1');
        $routes->post('parts/(:num)/adjust-stock', 'PartController::updateStock/$1');

        // ===============================================
        // USER MANAGEMENT
        // ===============================================
        $routes->get('users', 'UserController::index');
        $routes->get('users/new', 'UserController::create');
        $routes->post('users', 'UserController::store');
        $routes->get('users/(:num)', 'UserController::show/$1');
        $routes->get('users/(:num)/edit', 'UserController::edit/$1');
        $routes->put('users/(:num)', 'UserController::update/$1');
        $routes->delete('users/(:num)', 'UserController::delete/$1');

        // ===============================================
        // CMS MANAGEMENT
        // ===============================================
        $routes->get('pages', 'PageController::index');
        $routes->get('pages/new', 'PageController::new');
        $routes->post('pages', 'PageController::store');
        $routes->get('pages/(:num)', 'PageController::show/$1');
        $routes->get('pages/(:num)/edit', 'PageController::edit/$1');
        $routes->put('pages/(:num)', 'PageController::update/$1');
        $routes->delete('pages/(:num)', 'PageController::delete/$1');
        $routes->post('pages/(:num)/duplicate', 'PageController::duplicate/$1');

        // ===============================================
        // SETTINGS
        // ===============================================
        $routes->get('settings', 'SettingController::index');
        $routes->post('settings', 'SettingController::update');
        $routes->get('settings/new', 'SettingController::create');
        $routes->post('settings/store', 'SettingController::store');
        $routes->get('settings/(:num)/edit', 'SettingController::edit/$1');
        $routes->put('settings/(:num)', 'SettingController::updateSingle/$1');
        $routes->delete('settings/(:num)', 'SettingController::delete/$1');

        // Settings backup & restore
        $routes->get('settings/backup', 'SettingController::backup');
        $routes->get('settings/restore', 'SettingController::restore');
        $routes->post('settings/restore', 'SettingController::processRestore');
        $routes->get('settings/cache', 'SettingController::cache');
        $routes->post('settings/clear-cache', 'SettingController::clearCache');

        // ===============================================
        // REPORTS (NEW SECTION)
        // ===============================================
        $routes->group('reports', function($routes) {
            $routes->get('/', 'ReportController::index');
            $routes->get('orders', 'ReportController::orders');
            $routes->get('diagnosis', 'ReportController::diagnosis'); // NEW: Diagnosis reports
            $routes->get('quotations', 'ReportController::quotations');
            $routes->get('technician-performance', 'ReportController::technicianPerformance');
            $routes->get('diagnosis-efficiency', 'ReportController::diagnosisEfficiency'); // NEW
            $routes->get('cost-analysis', 'ReportController::costAnalysis');
            $routes->get('export/(:segment)', 'ReportController::export/$1');
        });

        // ===============================================
        // API ROUTES FOR AJAX CALLS (within admin area)
        // ===============================================
        $routes->group('api', function($routes) {
            // Stock movement API
            $routes->get('parts/(:num)/movements', 'PartController::getMovements/$1');
            $routes->get('parts/(:num)/usage-stats', 'PartController::getUsageStats/$1');

            // Order API
            $routes->get('orders/(:num)/parts', 'OrderController::getOrderParts/$1');
            $routes->get('orders/(:num)/movements', 'OrderController::getOrderMovements/$1');
            $routes->get('orders/ready-for-quotation', 'OrderController::getOrdersReadyForQuotation');
            $routes->get('orders/stats', 'OrderController::getOrderStats');

            // NEW: Diagnosis API
            $routes->get('diagnosis/queue-stats', 'DiagnosisController::getQueueWidget');
            $routes->get('diagnosis/common-issues/(:num)', 'DiagnosisController::getCommonIssues/$1');
            $routes->get('diagnosis/templates/(:num)', 'DiagnosisController::getTemplatesByDeviceType/$1');
            $routes->get('diagnosis/technician-workload', 'DiagnosisController::getTechnicianWorkload');

            // Quotation API
            $routes->get('quotations/stats', 'QuotationController::getQuotationStats');
            $routes->get('quotations/pending-count', 'QuotationController::getPendingCount');

            // Dashboard API
            $routes->get('dashboard/stats', 'DashboardController::getStats');
            $routes->get('dashboard/recent-activities', 'DashboardController::getRecentActivities');
            $routes->get('dashboard/diagnosis-summary', 'DiagnosisController::getQueueWidget'); // NEW

            // Search API
            $routes->get('search/parts', 'DashboardController::searchParts');
            $routes->get('search/customers', 'DashboardController::searchCustomers');
            $routes->get('search/orders', 'DashboardController::searchOrders');
        });
    });
});

// ===============================================
// API ROUTES FOR FRONTEND
// ===============================================
$routes->group('api', ['namespace' => 'App\Controllers\Api'], function($routes) {
    $routes->post('orders', 'OrderController::create');
    $routes->get('orders/(:segment)', 'OrderController::show/$1');
    $routes->get('services', 'ServiceController::index');
    $routes->get('service-categories', 'ServiceCategoryController::index');
});

// ===============================================
// FRONTEND ROUTES
// ===============================================
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
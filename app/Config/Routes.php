<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
//Login
$routes->get('/', 'Auth::login');
$routes->get('/login', 'Auth::login');
$routes->post('/auth/doLogin', 'Auth::doLogin');
$routes->get('/logout', 'Auth::logout');
//Change Password
$routes->get('/change-password', 'Auth::changePassword');
$routes->post('/auth/updatePassword', 'Auth::updatePassword');
//Dashbaord
$routes->get('/dashboard/admin', 'AdminDashboard::index', ['filter' => 'auth']);
$routes->get('/dashboard/user', 'UserDashboard::user_assigned', ['filter' => 'auth']);
$routes->get('/dashboard', 'Dashboard::index');


//Admin panel for user manage
$routes->get('auth/users', 'AdminController::index', ['filters' => 'admin']);
$routes->post('auth/users/add', 'AdminController::store');
$routes->get('auth/users/edit/(:num)', 'AdminController::edit/$1');
$routes->post('auth/users/update/(:num)', 'AdminController::update/$1');
$routes->post('auth/users/reset-password/(:num)', 'AdminController::resetPassword/$1');
$routes->get('auth/users/delete/(:num)', 'AdminController::delete/$1', ['filters' => 'admin']);

//Main Categories
$routes->get('assets/categories', 'CategoryController::index');
$routes->post('assets/categories/add', 'CategoryController::store');
$routes->get('assets/categories/edit/(:num)', 'CategoryController::index/$1');
$routes->post('assets/categories/update/(:num)', 'CategoryController::update/$1');
$routes->get('assets/categories/delete/(:num)', 'CategoryController::delete/$1', ['filters' => 'admin']);

//Sub Categories
$routes->get('assets/sub_categories', 'SubCategoryController::index');
$routes->post('assets/sub_categories/add', 'SubCategoryController::store');
$routes->get('assets/sub_categories/edit(:num)', 'SubCategoryController::index/$1');
$routes->post('assets/sub_categories/update/(:num)', 'SubCategoryController::update/$1');
$routes->get('assets/sub_categories/delete/(:num)', 'SubCategoryController::delete/$1', ['filters' => 'admin']);

// Models
$routes->get('assets/models', 'ModelsController::index');
$routes->post('assets/models/add', 'ModelsController::store');
$routes->get('assets/models/edit/(:num)', 'ModelsController::index/$1');
$routes->post('assets/models/update/(:num)', 'ModelsController::update/$1');
$routes->get('assets/models/delete/(:num)', 'ModelsController::delete/$1', ['filters' => 'admin']);

// Suppliers
$routes->get('assets/suppliers', 'SupplierController::index');
$routes->post('assets/suppliers/add', 'SupplierController::store');
$routes->get('assets/suppliers/edit/(:num)', 'SupplierController::edit/$1');
$routes->post('assets/suppliers/update/(:num)', 'SupplierController::update/$1');
$routes->get('assets/suppliers/delete/(:num)', 'SupplierController::delete/$1', ['filters' => 'admin']);

//Assets
$routes->get('assets/manage', 'AssetController::index');
$routes->get('assets/edit/(:num)', 'AssetController::index/$1'); // inline edit
$routes->post('assets/store', 'AssetController::store');
$routes->post('assets/update/(:num)', 'AssetController::update/$1');
$routes->get('assets/delete/(:num)', 'AssetController::delete/$1', ['filters' => 'admin']);
$routes->get('/assets/qr', 'AssetController::qrList');
$routes->get('/assets/generateQr/(:num)', 'AssetController::generateQRCode/$1');
$routes->get('/assets/view/(:num)', 'AssetController::view/$1');   // Returns JSON for QR scanner
$routes->get('/assets/qr-scan', 'AssetController::qrScan');       // Mobile scanner page
$routes->get('/assets/generate-qr/(:num)', 'AssetController::generateQRCode/$1'); // QR code generation

//Asset Assignment and Returned
$routes->get('/assets/assignments', 'AssetAssignmentController::index');
$routes->post('/assets/assignments/store', 'AssetAssignmentController::store');
$routes->post('/assets/assignments/return', 'AssetAssignmentController::return');

//Asset Transfers & Disposals
$routes->get('/assets/movements', 'AssetMovementController::index');
$routes->post('/assets/movements/transfer', 'AssetMovementController::storeTransfer');
$routes->post('/assets/movements/dispose', 'AssetMovementController::storeDisposal');

//Asset Tranfers
$routes->get('/assets/assets_transfers', 'AssetTransferController::create');
$routes->post('/asset-transfer/store', 'AssetTransferController::store');
$routes->get('/asset-transfer/pending', 'AssetTransferController::pending');
$routes->post('asset-transfer/approve/(:num)', 'AssetTransferController::approve/$1');
$routes->get('/assets/asset-transfer/received', 'AssetTransferController::received');
$routes->post('asset-transfer/receiveAsset/(:num)', 'AssetTransferController::receiveAsset/$1');
$routes->get('/asset-transfer/downloadTransferNote/(:num)', 'AssetTransferController::downloadTransferNote/$1');
$routes->get('assets/transfer/approve/(:num)/(:any)', '\App\Controllers\AssetTransferController::emailApprove/$1/$2');
$routes->get('assets/transfer/reject/(:num)/(:any)', '\App\Controllers\AssetTransferController::emailReject/$1/$2');



//Asset Maintenance
$routes->get('/assets/maintenance', 'AssetMaintenanceController::index');
$routes->get('/assets/maintenance/create', 'AssetMaintenanceController::create');
$routes->post('/assets/maintenance/store', 'AssetMaintenanceController::store');

//Asset Sticker generate
$routes->get('/assets/sticker/(:num)', 'AssetController::generateSticker/$1');
$routes->get('/assets/downloadSticker/(:num)', 'AssetController::downloadSticker/$1');




//user dashboard department
$routes->get('user-dashboard/user-assigned', 'UserDashboard::user_assigned');
$routes->get('user-dashboard/department-assigned', 'UserDashboard::department_assigned');


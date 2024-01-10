<?php

use App\Http\Controllers\Admin\CategoryProductController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\MaterialController;
use App\Http\Controllers\Admin\NotaDinasController;
use App\Http\Controllers\Admin\OrderTypeController;
use App\Http\Controllers\Admin\OutgoingProductController;
use App\Http\Controllers\Admin\ProcessPlanController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\ProductTypeController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\QualifierController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\ProductTransactionController;
use App\Http\Controllers\Admin\ProductLocationController;
use App\Http\Controllers\Admin\ProductPlanningController;
use App\Http\Controllers\ChartManageController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth']], function () {
    Route::resource('/profile', ProfileController::class);
    Route::resource('/location', LocationController::class);
    Route::resource('/category', CategoryProductController::class);
    Route::resource('/material', MaterialController::class);
    Route::group(['middleware' => ['permission:manage nota dinas']], function () {
        Route::resource('/product', ProductController::class);
    });
    Route::resource('/rpp', ProcessPlanController::class);
    Route::resource('/supplier', SupplierController::class);
    Route::resource('/customer', CustomerController::class);
    Route::resource('/orderType', OrderTypeController::class);
    Route::resource('/notaDinas', NotaDinasController::class);
    Route::resource('/transaction', TransactionController::class);
    Route::resource('/productTransaction', ProductTransactionController::class);
    Route::resource('/productLocation', ProductLocationController::class);
    Route::resource('/productPlanning', ProductPlanningController::class);
});

Route::get('/get-table/unread-notifications', [NotificationController::class, 'getTableUnreadNotifications'])->name('get-table.unread-notifications');
Route::get('/get-table/read-notifications', [NotificationController::class, 'getTableReadNotifications'])->name('get-table.read-notifications');
Route::get('/get-table/read-notifications', [NotificationController::class, 'getTableReadNotifications'])->name('get-table.read-notifications');
Route::resource('/dashboard', AdminDashboardController::class);
Route::get('/get-profiles', [ProfileController::class, 'getProfiles'])->name('get-profiles');
Route::get('/get-suppliers', [SupplierController::class, 'getSuppliers'])->name('get-suppliers');
Route::get('/get-locations', [LocationController::class, 'getLocations'])->name('get-locations');
Route::get('/get-customers', [CustomerController::class, 'getCustomers'])->name('get-customers');
Route::get('/get-orderTypes', [OrderTypeController::class, 'table'])->name('get-table-order-types');
Route::get('/get-notaDinas', [NotaDinasController::class, 'table'])->name('get-table-nota-dinas');
Route::get('/get-rpps', [ProcessPlanController::class, 'getRpps'])->name('get-rpps');
Route::get('/get-transactions', [TransactionController::class, 'allTransactions'])->name('get-transactions');
Route::get('/get-transaction/{transaction}', [TransactionController::class, 'getTransaction'])->name('get-transaction');
Route::get('/get-all-products', [ProductController::class, 'getAllProducts'])->name('get-all-products');
Route::get('/get-this-year-products', [ProductController::class, 'getThisYear'])->name('get-this-year-products');
Route::get('/get-warning-products', [ProductController::class, 'getWarningProducts'])->name('get-warning-products');
Route::get('/get-danger-products', [ProductController::class, 'getDangerProducts'])->name('get-danger-products');
Route::get('/get-products/{category}', [ProductController::class, 'getProductsByCategory'])->name('get-products-by-category');
Route::get('/get-categories', [CategoryProductController::class, 'getCategories'])->name('get-categories');
Route::get('/get-unused-products', [AdminDashboardController::class, 'getUnusedProducts'])->name('get-unused-products');
Route::get('/get-report-process-plan', [AdminDashboardController::class, 'getReportProcessPlan'])->name('get-report-process-plan');
Route::get('/get-qualifiers', [AdminDashboardController::class, 'getQualifiers'])->name('get-qualifiers');
Route::get('/material/search', [AdminDashboardController::class, 'search'])->name('material.search');
Route::get('/export/profiles', [ProfileController::class, 'exportProfiles'])->name('export.profiles');
Route::post('/import/profiles', [ProfileController::class, 'importProfiles'])->name('import.profiles');
Route::get('/export/products', [ProductController::class, 'exportProducts'])->name('export.products');
Route::post('/import/products', [ProductController::class, 'importProducts'])->name('import.products');
Route::get('/export/process-plans', [ProcessPlanController::class, 'exportProcessPlans'])->name('export.processplans');
Route::post('/import/process-plans', [ProcessPlanController::class, 'importProcessPlans'])->name('import.processplans');
Route::get('/export/transaction', [TransactionController::class, 'exportTransactions'])->name('export.transactions');
Route::post('/import/transaction', [TransactionController::class, 'importTransactions'])->name('import.transactions');
Route::get('/export/productLocations', [ProductLocationController::class, 'exportProductLocations'])->name('export.productLocations');
Route::post('/import/productLocations', [ProductLocationController::class, 'importProductLocations'])->name('import.productLocations');

Route::prefix('/table')->group(function () {
    Route::get('/productLocations', [ProductLocationController::class, 'table'])->name('table-productLocations');
});

Route::prefix('/json')->group(function () {
    Route::get('/get-customers', [CustomerController::class, 'getJsonCustomers'])->name('get-json-customers');
    Route::get('/get-suppliers', [SupplierController::class, 'getJsonSuppliers'])->name('get-json-suppliers');
    Route::get('/get-locations', [LocationController::class, 'selectLocations'])->name('get-json-locations');
    Route::get('/select-material', [MaterialController::class, 'select'])->name('select-material');
    Route::get('/select-product-type', [ProductTypeController::class, 'select'])->name('select-product-type');
    Route::get('/select-transactions', [LocationController::class, 'selectTransactions'])->name('select-transactions');
    Route::get('/select-order-type', [OrderTypeController::class, 'select'])->name('select-order-type');
    Route::get('/select-product-locations', [ProductLocationController::class, 'select'])->name('select-product-locations');
    Route::get('/select-product-locations-param', [ProductLocationController::class, 'selectWithParam'])->name('select-product-locations-param');
    Route::get('/get-transactions', [TransactionController::class, 'getTransactions'])->name('get-json-transactions');
    Route::get('/get-rpps', [ProcessPlanController::class, 'getJsonRpps'])->name('get-json-rpps');
    Route::get('/get-products', [ProductController::class, 'getJsonProducts'])->name('get-json-products');
    Route::get('/get-products/{category}', [ProductController::class, 'getJsonProductsByCategory'])->name('get-json-products-by-category');
    Route::get('/get-product/{product_id}', [ProductController::class, 'getJsonProduct'])->name('get-json-product');
    Route::get('/get-rpp/{customer}', [ProcessPlanController::class, 'getRppsByCustomerName'])->name('get-json-rpp-by-customer-name');
    Route::get('/get-transaction/{supplier}', [TransactionController::class, 'getJsonTransactionBySupplierName'])->name('get-json-transaction-by-supplier-name');
    Route::get('/get-outgoingProducts', [OutgoingProductController::class, 'getJsonOutgoingProducts'])->name('get-json-outgoing-products');
    Route::get('/get-outgoingProduct/{outgoingProduct}', [OutgoingProductController::class, 'getJsonOutgoingProduct'])->name('get-json-outgoing-product');
    Route::get('/get-outgoingProducts/{rpp}', [OutgoingProductController::class, 'getJsonOutgoingProductsByRpp'])->name('get-json-outgoing-products-by-rpp');
    Route::get('/get-materials', [MaterialController::class, 'getJsonMaterials'])->name('get-json-materials');
    Route::get('/get-categories', [CategoryProductController::class, 'getJsonCategories'])->name('get-json-categories');
    Route::get('/get-category/{category}', [CategoryProductController::class, 'getJsonCategory'])->name('get-json-category');
    Route::get('/get-qualifiers', [QualifierController::class, 'getJsonQualifiers'])->name('get-json-qualifiers');
    Route::get('/get-qualifier/{qualifier}', [QualifierController::class, 'getJsonQualifier'])->name('get-json-qualifier');
    Route::get('/get-qualifiers-by-product/{product}', [QualifierController::class, 'getJsonQualifierByProduct'])->name('get-json-qualifiers-by-product');
    Route::get('/get-roles', [RoleController::class, 'getJsonRoles'])->name('get-json-roles');
    Route::prefix('/chart')->group(function () {
        Route::get('/plan/paper-1', [ChartManageController::class, 'planPaperFirst'])->name('plan.paper.1.chart');
        Route::get('/plan/ink-1', [ChartManageController::class, 'planInkFirst'])->name('plan.ink.1.chart');
        Route::get('/plan/paper-2', [ChartManageController::class, 'planPaperSecond'])->name('plan.paper.2.chart');
        Route::get('/plan/ink-2', [ChartManageController::class, 'planInkSecond'])->name('plan.ink.2.chart');
        Route::get('/transaction', [ChartManageController::class, 'transactionMonthly'])->name('monthly.transaction.chart');
        Route::get('/tinta', [ChartManageController::class, 'tintaMonthly'])->name('monthly.tinta.chart');
        Route::get('/rpp', [ChartManageController::class, 'rppYearly'])->name('yearly.rpp.chart');
        Route::get('/category', [ChartManageController::class, 'categoryOverall'])->name('category.overall.chart');
    });
});

Route::prefix('/notifications')->group(function () {
    Route::get('/', [NotificationController::class, 'index'])->name('notification.index');
    Route::get('/show/{id}', [NotificationController::class, 'show'])->name('notification.show');
    Route::post('/markAsRead', [NotificationController::class, 'markAsReads'])->name('notification.markAsReads');
    Route::post('/markAsRead/{id}', [NotificationController::class, 'markAsRead'])->name('notification.markAsRead');
});

Route::fallback(function () {
    return redirect()->route('dashboard.index');
});

require __DIR__ . '/auth.php';

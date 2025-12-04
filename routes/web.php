<?php

use Illuminate\Support\Facades\Route;


Route::middleware(['auth'])->group(function () {

    Route::get('dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    Route::get('dashboard/job-stats', [\App\Http\Controllers\DashboardController::class, 'getJobStats'])->name('dashboard.job-stats');
    Route::get('dashboard/filtered-stats', [\App\Http\Controllers\DashboardController::class, 'getFilteredStats'])->name('dashboard.filtered-stats');

    Route::resource('users', \App\Http\Controllers\UserController::class);
    Route::resource('roles', \App\Http\Controllers\RoleController::class);
    Route::resource('categories', \App\Http\Controllers\CategoryController::class);
    Route::resource('products', \App\Http\Controllers\ProductController::class);
    Route::resource('departments', \App\Http\Controllers\DepartmentController::class);
    Route::resource('expertises', \App\Http\Controllers\ExpertiseController::class);
    Route::resource('engineers', \App\Http\Controllers\EngineerController::class);
    Route::resource('co-ordinators', \App\Http\Controllers\CoordinatorController::class);
    Route::resource('technicians', \App\Http\Controllers\TechnicianController::class);
    Route::resource('customers', \App\Http\Controllers\CustomerController::class);
    
    Route::get('customers/{customer}/locations', [\App\Http\Controllers\LocationController::class, 'getCustomerLocations'])->name('customers.locations.index');
    Route::post('customers/{customer}/locations', [\App\Http\Controllers\LocationController::class, 'store'])->name('customers.locations.store');
    Route::get('locations/{location}', [\App\Http\Controllers\LocationController::class, 'show'])->name('locations.show');
    Route::put('locations/{location}', [\App\Http\Controllers\LocationController::class, 'update'])->name('locations.update');
    Route::delete('locations/{location}', [\App\Http\Controllers\LocationController::class, 'destroy'])->name('locations.destroy');
    
    Route::resource('jobs', \App\Http\Controllers\JobController::class);
    Route::resource('requisitions', \App\Http\Controllers\RequisitionController::class);
    Route::resource('notification-templates', \App\Http\Controllers\NotificationTemplateController::class);
    Route::post('notification-templates/{notificationTemplate}/change-status', [\App\Http\Controllers\NotificationTemplateController::class, 'changeStatus'])->name('notification-templates.change-status');
    
    Route::post('category-list', [\App\Helpers\Helper::class, 'getCategories'])->name('category-list');
    Route::post('product-list', [\App\Helpers\Helper::class, 'getProducts'])->name('product-list');
    Route::post('country-list', [\App\Helpers\Helper::class, 'getCountries'])->name('country-list');
    Route::post('state-list', [\App\Helpers\Helper::class, 'getStatesByCountry'])->name('state-list');
    Route::post('city-list', [\App\Helpers\Helper::class, 'getCitiesByState'])->name('city-list');
    Route::post('user-list', [\App\Helpers\Helper::class, 'getUsers'])->name('user-list');
    Route::post('product-category-list', [\App\Helpers\Helper::class, 'getProductCategories'])->name('product-category-list');
    Route::post('department-list', [\App\Helpers\Helper::class, 'getDepartments'])->name('department-list');
    Route::post('expertise-list', [\App\Helpers\Helper::class, 'getExpertise'])->name('expertise-list');
    Route::post('job-list', [\App\Helpers\Helper::class, 'getJobs'])->name('job-list');
    Route::post('notification-template-list', [\App\Helpers\Helper::class, 'notificationTemplates'])->name('notification-template-list');

    Route::post('products/{product}/images/upload', [\App\Http\Controllers\ProductImageController::class, 'upload'])->name('products.images.upload');
    Route::get('products/{product}/images', [\App\Http\Controllers\ProductImageController::class, 'list'])->name('products.images.list');
    Route::delete('products/{product}/images/{media}', [\App\Http\Controllers\ProductImageController::class, 'delete'])->name('products.images.delete');
    Route::post('products/{product}/images/sort', [\App\Http\Controllers\ProductImageController::class, 'sort'])->name('products.images.sort');    
    Route::get('products/{product}/images/list', [\App\Http\Controllers\ProductController::class, 'images'])->name('products-media');

    Route::get('settings', [App\Http\Controllers\SettingController::class, 'index'])->name('settings.index');
    Route::post('settings', [App\Http\Controllers\SettingController::class, 'update'])->name('settings.update');
    
    Route::get('job-settings', [App\Http\Controllers\SettingController::class, 'jobIndex'])->name('job.settings');
    Route::post('job-settings-update', [App\Http\Controllers\SettingController::class, 'jobUpdate'])->name('job.settings-update');
    Route::post('requisition-approve-reject/{id}', [App\Http\Controllers\RequisitionController::class, 'approveReject'])->name('requisitions.approve-reject');

    Route::post('jobs/{job}/reschedule', [\App\Http\Controllers\JobController::class, 'reschedule'])->name('jobs.reschedule');
    Route::post('/jobs/{job}/change-status', [\App\Http\Controllers\JobController::class, 'changeStatus'])->name('jobs.change-status');
    Route::post('jobs/approve/{job}', [\App\Http\Controllers\JobController::class, 'approve'])->name('jobs.approve');
    Route::get('jobs/can-approve/{job}', [\App\Http\Controllers\JobController::class, 'getCurrentInspectionStatus'])->name('jobs.can-approve');
    Route::post( 'jobs/ajax_common', [ \App\Http\Controllers\JobController::class, 'ajax_common' ] )->name( 'jobs.ajax.common' );

    // Invoice routes
    Route::get('jobs/{job}/generate-invoice', [\App\Http\Controllers\InvoiceController::class, 'generateInvoice'])->name('jobs.generate-invoice');
    Route::get('jobs/{job}/download-invoice', [\App\Http\Controllers\InvoiceController::class, 'downloadInvoice'])->name('jobs.download-invoice');

});

Route::get('get-po-invoice/{id}', [App\Http\Controllers\RequisitionController::class, 'po'])->name('get-po-invoice');
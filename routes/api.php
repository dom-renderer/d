<?php

use Illuminate\Support\Facades\Route;

Route::post('dms_before_login', [\App\Http\Controllers\API\JobController::class, 'before_login']);

Route::post('device-token', [\App\Http\Controllers\API\JobController::class, 'deviceToken']);
Route::post('remove-device-token', [\App\Http\Controllers\API\JobController::class, 'removeDeviceToken']);

Route::middleware('auth:api')->group(function () {
    Route::any( 'dms_after_login', [ \App\Http\Controllers\API\JobController::class, 'after_login' ] );
    /* Route::get('jobs', [\App\Http\Controllers\API\JobController::class, 'jobs']);
    Route::post('punch-in', [\App\Http\Controllers\API\JobController::class, 'punchIn']);
    Route::post('punch-out', [\App\Http\Controllers\API\JobController::class, 'punchOut']); */
});
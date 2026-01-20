<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiController;

Route::post('/login', [ApiController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [ApiController::class, 'logout'])
        ->name('auth.logout');
    
    Route::post('logout-device', [ApiController::class, 'logoutFromDevice'])
        ->name('auth.logout-device');
    
    Route::post('refresh', [ApiController::class, 'refreshToken'])
        ->name('auth.refresh');
});
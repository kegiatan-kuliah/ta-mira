<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SiswaQrController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/admin/siswas/print-qr/{id}', [SiswaQrController::class, 'cetak'])->name('print_qr');
});

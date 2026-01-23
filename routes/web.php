<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SiswaQrController;
use App\Http\Controllers\AbsenController;

Route::get('/', function () {
    return redirect('/admin');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/admin/siswas/print-qr/{id}', [SiswaQrController::class, 'cetak'])->name('print_qr');
    Route::get('/admin/absen/cetak', [AbsenController::class, 'print'])->name('laporan.absen.print');
});

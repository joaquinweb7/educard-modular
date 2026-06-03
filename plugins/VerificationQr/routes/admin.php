<?php

use Illuminate\Support\Facades\Route;

Route::get('/verification-qr', function () {
    return view('VerificationQr::index');
})->name('verificationqr.index');

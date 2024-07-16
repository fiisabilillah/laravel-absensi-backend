<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('pages.auth.auth-login');
});

Route::middleware(['auth'])->group(function () {
    Route::get('home', function () {
        return view('pages.dashboard', ['type_menu' => 'home']);
    })->name('home');

    // Route::resource('users', UserController::class);
    // Route::resource('companies', CompanyController::class);
    // Route::resource('attendances', AttendanceController::class);
    // Route::resource('permissions', PermissionController::class);
});



<?php

use App\Http\Controllers\Admin\SecurityController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'permission:admin.access'])
    ->group(function (): void {
        Route::view('/', 'admin.dashboard')->name('dashboard');
        Route::view('/profile', 'admin.profile')->name('profile');
        Route::get('/security', SecurityController::class)
            ->middleware('password.confirm')
            ->name('security');
    });

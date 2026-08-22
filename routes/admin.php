<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\SecurityController;
use App\Http\Controllers\Admin\UserController;
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

        Route::prefix('categories')
            ->name('categories.')
            ->controller(CategoryController::class)
            ->group(function (): void {
                Route::get('/', 'index')->middleware('permission:categories.view')->name('index');
                Route::get('/create', 'create')->middleware('permission:categories.create')->name('create');
                Route::post('/', 'store')->middleware('permission:categories.create')->name('store');
                Route::get('/{category}/edit', 'edit')->middleware('permission:categories.update')->name('edit');
                Route::put('/{category}', 'update')->middleware('permission:categories.update')->name('update');
                Route::delete('/{category}', 'destroy')->middleware('permission:categories.delete')->name('destroy');
            });

        Route::prefix('users')
            ->name('users.')
            ->controller(UserController::class)
            ->group(function (): void {
                Route::get('/', 'index')->middleware('permission:users.view')->name('index');
                Route::get('/create', 'create')->middleware('permission:users.create')->name('create');
                Route::post('/', 'store')->middleware('permission:users.create')->name('store');
                Route::get('/{user}/edit', 'edit')->middleware('permission:users.update')->name('edit');
                Route::put('/{user}', 'update')->middleware('permission:users.update')->name('update');
                Route::put('/{user}/password', 'updatePassword')
                    ->middleware('permission:users.reset-password')
                    ->name('password.update');
                Route::patch('/{user}/suspend', 'suspend')
                    ->middleware('permission:users.suspend')
                    ->name('suspend');
                Route::patch('/{user}/restore', 'restore')
                    ->middleware('permission:users.restore')
                    ->name('restore');
                Route::delete('/{user}', 'destroy')
                    ->middleware('permission:users.delete')
                    ->name('destroy');
            });
    });

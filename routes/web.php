<?php

use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\HeroSlideController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\Admin\PageSectionController;
use App\Http\Controllers\Admin\TeacherProfileController;
use App\Http\Controllers\LandingController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LandingController::class, 'index'])->name('landing');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    });

    Route::middleware('auth')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

        Route::resource('news', NewsController::class);
        Route::resource('announcements', AnnouncementController::class);
        Route::resource('sections', PageSectionController::class);
        Route::resource('media', MediaController::class);
        Route::resource('slides', HeroSlideController::class)->parameters(['slides' => 'slide']);
        Route::resource('teachers', TeacherProfileController::class)->parameters(['teachers' => 'teacher']);
    });
});

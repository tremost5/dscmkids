<?php

use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DailyQuizBankController;
use App\Http\Controllers\Admin\HeroSlideController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\Admin\PageSectionController;
use App\Http\Controllers\Admin\TeacherProfileController;
use App\Http\Controllers\Admin\LiveStreamController;
use App\Http\Controllers\Admin\SpiritualContentController;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\StudentAuthController;
use App\Http\Controllers\StudentGameController;
use App\Http\Controllers\TestimonialSubmissionController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::get('/berita', [LandingController::class, 'newsIndex'])->name('news.index');
Route::get('/berita/{slug}', [LandingController::class, 'newsShow'])->name('news.show');
Route::get('/galeri/event/{eventSlug}', [LandingController::class, 'galleryEventShow'])->name('gallery.event');
Route::post('/testimoni', [TestimonialSubmissionController::class, 'store'])->name('testimonials.submit');
Route::post('/murid/quiz/submit', [StudentGameController::class, 'submitDailyQuiz'])
    ->middleware('auth')
    ->name('student.quiz.submit');
Route::post('/murid/reward/claim', [StudentGameController::class, 'claimWeeklyReward'])
    ->middleware('auth')
    ->name('student.reward.claim');
Route::post('/murid/reset/seen', [StudentGameController::class, 'markDailyResetSeen'])
    ->middleware('auth')
    ->name('student.reset.seen');
Route::post('/murid/arcade/score', [StudentGameController::class, 'submitArcadeScore'])
    ->middleware('auth')
    ->name('student.arcade.score');

Route::prefix('murid')->name('student.')->group(function () {
    Route::get('/arcade', [StudentGameController::class, 'arcade'])->name('arcade');

    Route::middleware('guest')->group(function () {
        Route::get('/login', [StudentAuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [StudentAuthController::class, 'login'])->name('login.submit');
        Route::get('/daftar', [StudentAuthController::class, 'showRegister'])->name('register');
        Route::post('/daftar', [StudentAuthController::class, 'register'])->name('register.submit');
    });

    Route::middleware('auth')->group(function () {
        Route::post('/logout', [StudentAuthController::class, 'logout'])->name('logout');
    });
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    });

    Route::middleware(['auth', 'admin'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

        Route::resource('news', NewsController::class);
        Route::resource('announcements', AnnouncementController::class);
        Route::resource('quiz-banks', DailyQuizBankController::class)->except('show');
        Route::resource('sections', PageSectionController::class);
        Route::resource('media', MediaController::class);
        Route::resource('slides', HeroSlideController::class)->parameters(['slides' => 'slide']);
        Route::resource('teachers', TeacherProfileController::class)->parameters(['teachers' => 'teacher']);
        Route::resource('testimonials', TestimonialController::class)->except('show');
        Route::get('livestream', [LiveStreamController::class, 'edit'])->name('livestream.edit');
        Route::put('livestream', [LiveStreamController::class, 'update'])->name('livestream.update');
        Route::get('spiritual', [SpiritualContentController::class, 'edit'])->name('spiritual.edit');
        Route::put('spiritual', [SpiritualContentController::class, 'update'])->name('spiritual.update');
    });
});

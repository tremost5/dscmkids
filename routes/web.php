<?php

use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DailyQuizBankController;
use App\Http\Controllers\Admin\HeroSlideController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\Admin\PageSectionController;
use App\Http\Controllers\Admin\ParentPortalController as AdminParentPortalController;
use App\Http\Controllers\Admin\SystemMonitorController;
use App\Http\Controllers\Admin\TeacherProfileController;
use App\Http\Controllers\Admin\LiveStreamController;
use App\Http\Controllers\Admin\SpiritualContentController;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\Admin\LearningMaterialController as AdminLearningMaterialController;
use App\Http\Controllers\Admin\NotificationBroadcastController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\LearningMaterialController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\StudentAuthController;
use App\Http\Controllers\StudentGameController;
use App\Http\Controllers\StudentWalletController;
use App\Http\Controllers\TestimonialSubmissionController;
use App\Http\Controllers\ParentPortalController;
use App\Http\Controllers\TeacherPhotoController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::get('/berita', [LandingController::class, 'newsIndex'])->name('news.index');
Route::get('/berita/{slug}', [LandingController::class, 'newsShow'])->name('news.show');
Route::get('/galeri/event/{eventSlug}', [LandingController::class, 'galleryEventShow'])->name('gallery.event');
Route::get('/materi', [LearningMaterialController::class, 'index'])->name('materials.index');
Route::get('/orangtua', [ParentPortalController::class, 'index'])->name('parent.portal');
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/teacher-photo/{teacher}', TeacherPhotoController::class)->name('teacher.photo');
Route::post('/testimoni', [TestimonialSubmissionController::class, 'store'])->middleware('throttle:5,1')->name('testimonials.submit');
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
    Route::get('/progress', [StudentGameController::class, 'progress'])->middleware('auth')->name('progress');
    Route::get('/wallet', [StudentWalletController::class, 'index'])->middleware('auth')->name('wallet');

    Route::get('/login', [StudentAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [StudentAuthController::class, 'login'])->middleware('throttle:8,1')->name('login.submit');
    Route::get('/daftar', [StudentAuthController::class, 'showRegister'])->name('register');
    Route::post('/daftar', [StudentAuthController::class, 'register'])->middleware('throttle:6,1')->name('register.submit');

    Route::middleware('auth')->group(function () {
        Route::post('/logout', [StudentAuthController::class, 'logout'])->name('logout');
    });
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1')->name('login.submit');
    });

    Route::middleware(['auth', 'admin', 'admin.activity'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('permission:dashboard.view')->name('dashboard');
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

        Route::get('news-export', [NewsController::class, 'export'])->middleware('permission:content.manage')->name('news.export');
        Route::post('news-bulk', [NewsController::class, 'bulkUpdate'])->middleware('permission:content.manage')->name('news.bulk');
        Route::resource('news', NewsController::class)->middleware('permission:content.manage');
        Route::resource('announcements', AnnouncementController::class)->middleware('permission:content.manage');
        Route::resource('quiz-banks', DailyQuizBankController::class)->except('show')->middleware('permission:content.manage');
        Route::resource('sections', PageSectionController::class)->middleware('permission:content.manage');
        Route::resource('media', MediaController::class)->middleware('permission:content.manage');
        Route::resource('slides', HeroSlideController::class)->parameters(['slides' => 'slide'])->middleware('permission:content.manage');
        Route::resource('teachers', TeacherProfileController::class)->parameters(['teachers' => 'teacher'])->middleware('permission:content.manage');
        Route::resource('materials', AdminLearningMaterialController::class)->except('show')->middleware('permission:content.manage');
        Route::resource('testimonials', TestimonialController::class)->except('show')->middleware('permission:content.manage');
        Route::get('notifications', [NotificationBroadcastController::class, 'index'])->middleware('permission:notifications.manage')->name('notifications.index');
        Route::get('notifications/create', [NotificationBroadcastController::class, 'create'])->middleware('permission:notifications.manage')->name('notifications.create');
        Route::post('notifications', [NotificationBroadcastController::class, 'store'])->middleware('permission:notifications.manage')->name('notifications.store');
        Route::get('livestream', [LiveStreamController::class, 'edit'])->middleware('permission:content.manage')->name('livestream.edit');
        Route::put('livestream', [LiveStreamController::class, 'update'])->middleware('permission:content.manage')->name('livestream.update');
        Route::get('spiritual', [SpiritualContentController::class, 'edit'])->middleware('permission:content.manage')->name('spiritual.edit');
        Route::put('spiritual', [SpiritualContentController::class, 'update'])->middleware('permission:content.manage')->name('spiritual.update');
        Route::get('parent-portal', [AdminParentPortalController::class, 'edit'])->middleware('permission:content.manage')->name('parent-portal.edit');
        Route::put('parent-portal', [AdminParentPortalController::class, 'update'])->middleware('permission:content.manage')->name('parent-portal.update');
        Route::get('users', [UserManagementController::class, 'index'])->middleware('permission:users.manage')->name('users.index');
        Route::get('users/create', [UserManagementController::class, 'create'])->middleware('permission:users.manage')->name('users.create');
        Route::post('users', [UserManagementController::class, 'store'])->middleware('permission:users.manage')->name('users.store');
        Route::get('users/export', [UserManagementController::class, 'export'])->middleware('permission:users.manage')->name('users.export');
        Route::post('users/bulk', [UserManagementController::class, 'bulkUpdate'])->middleware('permission:users.manage')->name('users.bulk');
        Route::get('users/{user}/edit', [UserManagementController::class, 'edit'])->middleware('permission:users.manage')->name('users.edit');
        Route::put('users/{user}', [UserManagementController::class, 'update'])->middleware('permission:users.manage')->name('users.update');
        Route::get('system-monitor', [SystemMonitorController::class, 'index'])->middleware('permission:monitoring.view')->name('system.index');
    });
});

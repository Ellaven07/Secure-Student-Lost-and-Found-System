<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminReviewController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Student\ClaimController;
use App\Http\Controllers\Student\ItemBrowseController;
use App\Http\Controllers\Student\ItemReportController;
use App\Http\Controllers\Student\StudentDashboardController;
use App\Http\Controllers\Student\StudentProfileController;
use Illuminate\Support\Facades\Route;

// Homepage
Route::get('/', function () {
    return redirect()->route('login');
});

// Guest Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:6,1')
        ->name('login.store');

    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])
        ->name('password.request');

    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])
        ->middleware('throttle:6,1')
        ->name('password.email');

    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])
        ->name('password.reset');

    Route::post('/reset-password', [AuthController::class, 'resetPassword'])
        ->middleware('throttle:6,1')
        ->name('password.update');
});

// Logout
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware(['auth', 'role:admin,student', 'frontend-preview'])->group(function () {
    Route::patch('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])
        ->name('notifications.read-all');

    Route::patch('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])
        ->name('notifications.read');

    Route::delete('/notifications', [NotificationController::class, 'clear'])
        ->name('notifications.clear');
});

// Admin Routes
Route::middleware(['auth', 'role:admin', 'frontend-preview'])->group(function () {

    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])
        ->name('admin.dashboard');

    Route::get('/admin/dashboard/export', [AdminDashboardController::class, 'export'])
        ->name('admin.dashboard.export');

    Route::get('/admin/reports', [AdminReviewController::class, 'reports'])
        ->name('admin.reports.index');

    Route::patch('/admin/reports/{item}/verify', [AdminReviewController::class, 'verifyReport'])
        ->whereNumber('item')
        ->name('admin.reports.verify');

    Route::patch('/admin/reports/{item}/reject', [AdminReviewController::class, 'rejectReport'])
        ->whereNumber('item')
        ->name('admin.reports.reject');

    Route::get('/admin/claims', [AdminReviewController::class, 'claims'])
        ->name('admin.claims.index');

    Route::patch('/admin/claims/{claim}/approve', [AdminReviewController::class, 'approveClaim'])
        ->whereNumber('claim')
        ->name('admin.claims.approve');

    Route::patch('/admin/claims/{claim}/reject', [AdminReviewController::class, 'rejectClaim'])
        ->whereNumber('claim')
        ->name('admin.claims.reject');

    Route::get('/admin/notifications', [NotificationController::class, 'index'])
        ->name('admin.notifications');

    Route::view('/admin/profile', 'admin.profile')
        ->name('admin.profile');

    // Student Management
    Route::get('/admin/students', [StudentController::class, 'index'])
        ->name('admin.students.index');

    Route::get('/admin/students/create', [StudentController::class, 'create'])
        ->name('admin.students.create');

    Route::post('/admin/students', [StudentController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('admin.students.store');

    Route::get('/admin/students/{student}', [StudentController::class, 'show'])
        ->whereNumber('student')
        ->name('admin.students.show');

    Route::get('/admin/students/{student}/edit', [StudentController::class, 'edit'])
        ->whereNumber('student')
        ->name('admin.students.edit');

    Route::put('/admin/students/{student}', [StudentController::class, 'update'])
        ->whereNumber('student')
        ->name('admin.students.update');

    Route::delete('/admin/students/{student}', [StudentController::class, 'destroy'])
        ->whereNumber('student')
        ->name('admin.students.destroy');

    Route::patch('/admin/students/{student}/status', [StudentController::class, 'toggleStatus'])
        ->whereNumber('student')
        ->name('admin.students.status');

    Route::put('/admin/students/{student}/password', [StudentController::class, 'resetPassword'])
        ->whereNumber('student')
        ->name('admin.students.password');

    // Manage Categories
    Route::get('/admin/categories', [CategoryController::class, 'index'])
        ->name('admin.categories');

    Route::post('/admin/categories', [CategoryController::class, 'store'])
        ->name('admin.categories.store');

    Route::put('/admin/categories/{category}', [CategoryController::class, 'update'])
        ->whereNumber('category')
        ->name('admin.categories.update');

    Route::delete('/admin/categories/{category}', [CategoryController::class, 'destroy'])
        ->whereNumber('category')
        ->name('admin.categories.destroy');
});

// Student Routes
Route::middleware(['auth', 'role:student', 'frontend-preview'])->group(function () {

    Route::get('/student/dashboard', [StudentDashboardController::class, 'index'])
        ->name('student.dashboard');

    Route::get('/student/profile', [StudentProfileController::class, 'show'])
        ->name('student.profile');

    Route::put('/student/profile', [StudentProfileController::class, 'update'])
        ->name('student.profile.update');

    Route::post('/student/profile/photo', [StudentProfileController::class, 'updatePhoto'])
        ->name('student.profile.photo');

    Route::put('/student/profile/password', [StudentProfileController::class, 'updatePassword'])
        ->name('student.profile.password');

    Route::get('/student/notifications', [NotificationController::class, 'index'])
        ->name('student.notifications');

    Route::get('/student/reports/lost', [ItemReportController::class, 'createLost'])
        ->name('student.reports.lost.create');

    Route::post('/student/reports/lost', [ItemReportController::class, 'storeLost'])
        ->middleware('throttle:10,1')
        ->name('student.reports.lost.store');

    Route::get('/student/reports/found', [ItemReportController::class, 'createFound'])
        ->name('student.reports.found.create');

    Route::post('/student/reports/found', [ItemReportController::class, 'storeFound'])
        ->middleware('throttle:10,1')
        ->name('student.reports.found.store');

    Route::get('/student/reports/history', [ItemReportController::class, 'history'])
        ->name('student.reports.history');

    Route::get('/student/items', [ItemBrowseController::class, 'index'])
        ->name('student.items.index');

    Route::get('/student/items/{item}', [ItemBrowseController::class, 'show'])
        ->whereNumber('item')
        ->name('student.items.show');

    Route::get('/student/items/{item}/claim', [ClaimController::class, 'create'])
        ->whereNumber('item')
        ->name('student.claims.create');

    Route::post('/student/items/{item}/claim', [ClaimController::class, 'store'])
        ->whereNumber('item')
        ->middleware('throttle:6,1')
        ->name('student.claims.store');

    Route::get('/student/claims', [ClaimController::class, 'index'])
        ->name('student.claims.index');
});

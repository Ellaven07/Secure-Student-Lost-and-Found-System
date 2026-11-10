<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Student\StudentProfileController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\NotificationController;
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

    Route::view('/student/dashboard', 'student.dashboard')
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
});

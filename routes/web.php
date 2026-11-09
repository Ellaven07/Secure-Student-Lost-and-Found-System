<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\StudentController;

Route::redirect('/', '/admin/students');

Route::middleware('frontend-preview')->group(function () {

    // Student Accounts
    Route::get('/admin/students', [StudentController::class, 'index'])
        ->name('admin.students.index');

    Route::get('/admin/students/create', [StudentController::class, 'create'])
        ->name('admin.students.create');

    Route::post('/admin/students', [StudentController::class, 'store'])
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

    // Other Pages
    Route::view('/student/profile', 'student.profile')
        ->name('student.profile');

    Route::view('/admin/categories', 'admin.categories')
        ->name('admin.categories');

    Route::view('/student/notifications', 'student.notifications')
        ->name('student.notifications');

});
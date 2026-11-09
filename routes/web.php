<?php

use Illuminate\Support\Facades\Route;

// Homepage
Route::get('/', function () {
    return view('welcome');
});


Route::middleware('frontend-preview')->group(function () {

    Route::view('/admin/students', 'admin.students.index')
        ->name('admin.students.index');

    Route::view('/student/profile', 'student.profile')
        ->name('student.profile');

    Route::view('/admin/categories', 'admin.categories')
        ->name('admin.categories');

    Route::view('/student/notifications', 'student.notifications')
        ->name('student.notifications');

});

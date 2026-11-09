
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\StudentController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('frontend-preview')->group(function () {

    Route::get('/admin/students', [StudentController::class, 'index'])
        ->name('admin.students.index');

    Route::view('/student/profile', 'student.profile')
        ->name('student.profile');

    Route::view('/admin/categories', 'admin.categories')
        ->name('admin.categories');

    Route::view('/student/notifications', 'student.notifications')
        ->name('student.notifications');

});

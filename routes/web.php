<?php

use App\Http\Controllers\CourseController;
use App\Http\Controllers\CourseQuestionController;
use App\Http\Controllers\CourseStudentController;
use App\Http\Controllers\LearningController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentAnswerController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::resource('courses', CourseController::class)->middleware('role:teacher');

        Route::get('courses/questions/create/{course}', [CourseQuestionController::class, 'create'])->middleware('role:teacher')->name('courses.create.question');

        Route::post('courses/questions/save/{course}', [CourseQuestionController::class, 'store'])->middleware('role:teacher')->name('courses.create.question.store');

        Route::resource('courses_questions', CourseQuestionController::class)->middleware('role:teacher')->except('create');

        Route::get('/couse/students/show/{course}', [CourseStudentController::class, 'index'])->middleware('role:teacher')->name('course.course_students.show');

        Route::get('/couse/students/create/{course}', [CourseStudentController::class, 'create'])->middleware('role:teacher')->name('course.course_students.create');

        Route::post('/couse/students/save/{course}', [CourseStudentController::class, 'store'])->middleware('role:teacher')->name('course.course_students.store');

        Route::get('/learning/finished/{courses}', [LearningController::class, 'learning_finished'])->middleware('role:student')->name('learning.finished.course');

        Route::get('/learning/rapport/{courses}', [LearningController::class, 'learning_rapport'])->middleware('role:student')->name('learning.rapport.course');

        Route::get('/learning', [LearningController::class, 'index'])->middleware('role:student')->name('learning.index');

        Route::get('/learning/{course}', [LearningController::class, 'show'])->middleware('role:student')->name('learning.course.show');

        Route::post('/learning/{courses}/{questions}', [StudentAnswerController::class, 'store'])->middleware('role:student')->name('learning.course.answer.store');

    });
});

require __DIR__ . '/auth.php';

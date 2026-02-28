<?php

use App\Livewire\ExploreCourses;
use App\Livewire\MyCertificates;
use App\Livewire\MyLearning;
use App\Livewire\ShowCourse;
use App\Livewire\ShowLesson;
use Illuminate\Support\Facades\Route;

Route::get('/', ExploreCourses::class)->name('home');

Route::get('/courses/{course}', ShowCourse::class)->name('courses.show');
Route::get('/courses/{course}/lessons/{lesson}', ShowLesson::class)->name('courses.lessons.show');

Route::get('/my-learning', MyLearning::class)
    ->middleware(['auth', 'verified'])
    ->name('my-learning');

Route::get('/my-certificates', MyCertificates::class)
    ->middleware(['auth', 'verified'])
    ->name('my-certificates');

Route::redirect('/dashboard', '/my-learning')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';

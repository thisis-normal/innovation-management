<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TestController;
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
});

//make a get route test to TestController
Route::get('/test-grading', [TestController::class, 'testGrading']);
Route::get('/test-delete-grading', [TestController::class, 'testDeleteGrading']);
Route::get('/test-sth', [TestController::class, 'testSomething']);

require __DIR__.'/auth.php';

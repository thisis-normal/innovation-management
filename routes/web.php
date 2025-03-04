<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SearchController;
use App\Mail\UserNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect('/user');
    }
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
// Route::get('/test-grading', [TestController::class, 'testGrading']);
// Route::get('/test-delete-grading', [TestController::class, 'testDeleteGrading']);
// Route::get('/test-sth', [TestController::class, 'testSomething']);
Route::get('/search', [SearchController::class, 'index'])->name('search');

Route::get('/mail-test', function () {
    try {
        Mail::to('thuonghuunguyen2002@gmail.com')
            ->send(new UserNotification([
                'subject' => 'Test Email',
                'title' => 'This is a test email',
                'message' => 'If you can see this, your email configuration is working!'
            ]));
        return 'Email sent successfully!';
    } catch (\Exception $e) {
        return 'Error sending email: ' . $e->getMessage();
    }
});

Route::get('/logout', function () {
    Auth::logout();
    return redirect('/');
})->name('logout');

require __DIR__.'/auth.php';

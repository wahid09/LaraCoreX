<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Feedback\FeedbackController;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');


    // Feedback routes
    Route::get('/feedback', [FeedbackController::class, 'index'])->name('feedback.index');
    Route::post('/feedback', [FeedbackController::class, 'store'])->name('feedback.store');
    Route::put('/feedback/{feedback}', [FeedbackController::class, 'update'])->name('feedback.update');
    Route::post('/feedback/{feedback}/comments', [FeedbackController::class, 'addComment'])->name('feedback.comments.store');
    Route::get('/feedback/{feedback}', [FeedbackController::class, 'show'])->name('feedback.show');

    // Admin only routes
    //Route::middleware(['admin'])->group(function () {
    //Route::get('/admin/feedback', [AdminFeedbackController::class, 'index'])->name('admin.feedback.index');
});

<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/tracking/register', [RegisteredUserController::class, 'create'])
                ->name('register');

    Route::post('/tracking/register', [RegisteredUserController::class, 'store']);

    Route::get('/tracking/login', [AuthenticatedSessionController::class, 'create'])
                ->name('login');

    Route::post('/tracking/login', [AuthenticatedSessionController::class, 'store']);

    Route::get('/tracking/forgot-password', [PasswordResetLinkController::class, 'create'])
                ->name('password.request');

    Route::post('/tracking/forgot-password', [PasswordResetLinkController::class, 'store'])
                ->name('password.email');

    Route::get('/tracking/reset-password/{token}', [NewPasswordController::class, 'create'])
                ->name('password.reset');

    Route::post('/tracking/reset-password', [NewPasswordController::class, 'store'])
                ->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::get('/tracking/verify-email', [EmailVerificationPromptController::class, '__invoke'])
                ->name('verification.notice');

    Route::get('/tracking/verify-email/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
                ->middleware(['signed', 'throttle:6,1'])
                ->name('verification.verify');

    Route::post('/tracking/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
                ->middleware('throttle:6,1')
                ->name('verification.send');

    Route::get('/tracking/confirm-password', [ConfirmablePasswordController::class, 'show'])
                ->name('password.confirm');

    Route::post('/tracking/confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::post('/tracking/logout', [AuthenticatedSessionController::class, 'destroy'])
                ->name('logout');
});

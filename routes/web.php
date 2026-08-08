<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Landing Page
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Guest Routes (unauthenticated users only)
Route::middleware('guest')->group(function () {
    // Login
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');

    // Register
    Route::get('/register', [AuthenticatedSessionController::class, 'registerCreate'])->name('register');
    Route::post('/register', [AuthenticatedSessionController::class, 'registerStore'])->name('register.store');

    // Forgot Password
    Route::get('/forgot-password', [PasswordController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordController::class, 'store'])->name('password.email');

    // Reset Password
    Route::get('/reset-password/{token}', [PasswordController::class, 'edit'])->name('password.reset');
    Route::post('/reset-password', [PasswordController::class, 'update'])->name('password.update');
})->middleware('throttle:5,1');

// Authenticated Routes
Route::middleware(['auth'])->group(function () {
    // Dashboard (verified middleware disabled for development)
    // TODO: Add 'verified' middleware for production
    Route::get('/dashboard', [DashboardController::class, 'show'])->name('dashboard');

    // Settings
    Route::get('/settings', [\App\Http\Controllers\Settings\SettingsController::class, 'index'])->name('settings.index');
    Route::prefix('settings')->name('settings.')->group(function () {
        // Profile
        Route::get('/profile', [\App\Http\Controllers\Settings\ProfileController::class, 'edit'])->name('profile');
        Route::put('/profile', [\App\Http\Controllers\Settings\ProfileController::class, 'update'])->name('profile.update');
        Route::post('/avatar', [\App\Http\Controllers\Settings\ProfileController::class, 'uploadAvatar'])->name('avatar.upload');
        Route::delete('/avatar', [\App\Http\Controllers\Settings\ProfileController::class, 'removeAvatar'])->name('avatar.remove');

        // Security
        Route::get('/security', [\App\Http\Controllers\Settings\SecurityController::class, 'edit'])->name('security');
        Route::put('/security', [\App\Http\Controllers\Settings\SecurityController::class, 'update'])->name('security.update');

        // Preferences
        Route::get('/preferences', [\App\Http\Controllers\Settings\PreferencesController::class, 'edit'])->name('preferences');
        Route::put('/preferences', [\App\Http\Controllers\Settings\PreferencesController::class, 'update'])->name('preferences.update');

        // Sessions
        Route::get('/sessions', [\App\Http\Controllers\Settings\SessionController::class, 'index'])->name('sessions');
        Route::delete('/sessions/{sessionId}', [\App\Http\Controllers\Settings\SessionController::class, 'destroy'])->name('sessions.revoke');
        Route::post('/sessions/revoke-all', [\App\Http\Controllers\Settings\SessionController::class, 'revokeAll'])->name('sessions.revoke-all');
    });
});

// Email Verification Routes
Route::middleware('auth')->group(function () {
    // Verification Notice (prompt user to verify)
    Route::get('/email/verify', [VerificationController::class, 'notice'])->name('verification.notice');

    // Verify Email (click link from email)
    Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
        ->name('verification.verify')
        ->middleware(['signed']);

    // Resend Verification Email
    Route::post('/email/verification-notification', [VerificationController::class, 'resend'])
        ->name('verification.send')
        ->middleware('throttle:3,1');
});

// Logout (must be logged in to logout)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Organization\OrganizationController;
use App\Http\Controllers\Organization\OrganizationMemberController;
use App\Http\Controllers\Organization\OrganizationSettingsController;
use App\Http\Controllers\Organization\SwitchOrganizationController;
use App\Http\Controllers\Agent\AgentTokenController;
use App\Http\Controllers\Project\ExceptionGroupController;
use App\Http\Controllers\Project\ProjectController;
use App\Http\Controllers\Project\QueryEventController;
use App\Http\Controllers\Project\RequestEventController;
use App\Http\Middleware\LoadCurrentOrganization;
use App\Http\Middleware\LoadCurrentProject;
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

    // Organization Routes - MUST define literal routes BEFORE {organization} wildcard
    Route::get('/organizations/create', [OrganizationController::class, 'create'])->name('organizations.create');
    Route::post('/organizations', [OrganizationController::class, 'store'])->name('organizations.store');
    Route::get('/organizations', [OrganizationController::class, 'index'])->name('organizations.index');
    Route::post('/organizations/switch/{organization}', [SwitchOrganizationController::class, 'switch'])->name('organizations.switch');

    // Organization Routes with {organization} parameter (must come after literal routes)
    Route::middleware([LoadCurrentOrganization::class])->prefix('organizations/{organization}')->name('organizations.')->group(function () {
        // Overview
        Route::get('/', [OrganizationController::class, 'show'])->name('show');

        // Settings
        Route::get('/settings', [OrganizationSettingsController::class, 'edit'])->name('settings');
        Route::put('/settings', [OrganizationSettingsController::class, 'update'])->name('settings.update');

        // Project Routes
        Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
        Route::get('/projects/create', [ProjectController::class, 'create'])->name('projects.create');
        Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');

        // Project Routes with {project} parameter
        Route::middleware([LoadCurrentProject::class])->group(function () {
            Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');
            Route::get('/projects/{project}/edit', [ProjectController::class, 'edit'])->name('projects.edit');
            Route::put('/projects/{project}', [ProjectController::class, 'update'])->name('projects.update');
            Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');

            // Agent Token Routes
            Route::get('/projects/{project}/agent', [AgentTokenController::class, 'show'])->name('projects.agent.show');
            Route::post('/projects/{project}/agent', [AgentTokenController::class, 'store'])->name('projects.agent.store');
            Route::put('/projects/{project}/agent', [AgentTokenController::class, 'update'])->name('projects.agent.update');
            Route::delete('/projects/{project}/agent', [AgentTokenController::class, 'destroy'])->name('projects.agent.destroy');

            // Request Events Routes
            Route::get('/projects/{project}/requests', [RequestEventController::class, 'index'])->name('projects.requests.index');
            Route::get('/projects/{project}/requests/{uuid}', [RequestEventController::class, 'show'])->name('projects.requests.show');

            // Exception Groups Routes
            Route::get('/projects/{project}/exceptions', [ExceptionGroupController::class, 'index'])->name('projects.exceptions.index');
            Route::get('/projects/{project}/exceptions/{uuid}', [ExceptionGroupController::class, 'show'])->name('projects.exceptions.show');
            Route::put('/projects/{project}/exceptions/{uuid}/status', [ExceptionGroupController::class, 'updateStatus'])->name('projects.exceptions.update-status');

            // Query Events Routes
            Route::get('/projects/{project}/queries', [QueryEventController::class, 'index'])->name('projects.queries.index');
            Route::get('/projects/{project}/queries/{uuid}', [QueryEventController::class, 'show'])->name('projects.queries.show');
        });
    });

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

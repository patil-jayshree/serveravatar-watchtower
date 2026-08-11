<?php

use App\Http\Controllers\Api\Agent\ConnectionController;
use App\Http\Controllers\Api\Agent\ExceptionController;
use App\Http\Controllers\Api\Agent\JobController;
use App\Http\Controllers\Api\Agent\LogController;
use App\Http\Controllers\Api\Agent\QueryController;
use App\Http\Controllers\Api\Agent\TelemetryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Agent API Routes
|--------------------------------------------------------------------------
|
| These routes are used by the ServerAvatar Watchtower Agent to communicate
| with Watchtower. Authentication is handled via Agent Tokens, NOT user
| sessions or Sanctum tokens.
|
*/

Route::prefix('agent')->name('api.agent.')->group(function () {
    // Connection verification
    Route::post('/connection', [ConnectionController::class, 'verify'])->name('connection');

    // Request telemetry
    Route::post('/requests', [TelemetryController::class, 'storeRequest'])
        ->middleware('agent.token.telemetry')
        ->name('requests.store');

    // Exception telemetry
    Route::post('/exceptions', [ExceptionController::class, 'store'])
        ->middleware('agent.token.telemetry')
        ->name('exceptions.store');

    // Query telemetry
    Route::post('/queries', [QueryController::class, 'store'])
        ->middleware('agent.token.telemetry')
        ->name('queries.store');

    // Job telemetry
    Route::post('/jobs', [JobController::class, 'store'])
        ->middleware('agent.token.telemetry')
        ->name('jobs.store');

    // Log telemetry
    Route::post('/logs', [LogController::class, 'store'])
        ->middleware('agent.token.telemetry')
        ->name('logs.store');
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

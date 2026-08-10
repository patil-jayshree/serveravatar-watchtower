<?php

use App\Http\Controllers\Api\Agent\ConnectionController;
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
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

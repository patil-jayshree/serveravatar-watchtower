<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application.
| These routes are loaded by the RouteServiceProvider and all of them
| will be assigned to the "web" middleware group.
|
*/

// Landing Page
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Auth Placeholder Routes (Phase 2)
Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return view('welcome'); // Placeholder
    })->name('login');

    Route::get('/register', function () {
        return view('welcome'); // Placeholder
    })->name('register');
});

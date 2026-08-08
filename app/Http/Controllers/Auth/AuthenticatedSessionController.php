<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\LoginUser;
use App\Actions\Auth\LogoutUser;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Actions\Auth\RegisterUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request, LoginUser $loginUser): RedirectResponse
    {
        $loginUser->execute($request->validated(), $request);

        // For development: skip email verification, go directly to dashboard
        // TODO: Add email verification check for production
        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Display the registration view.
     */
    public function registerCreate(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     */
    public function registerStore(RegisterRequest $request, RegisterUser $registerUser): RedirectResponse
    {
        $registerUser->execute($request->validated());

        return redirect()->route('login')
            ->with('status', 'Registration successful! Please check your email to verify your account.');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request, LogoutUser $logoutUser): RedirectResponse
    {
        $logoutUser->execute($request);

        return redirect()->route('home');
    }
}

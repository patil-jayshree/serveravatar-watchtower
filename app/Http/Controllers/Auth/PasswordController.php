<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Password\ResetPassword;
use App\Actions\Password\SendPasswordResetLink;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordController extends Controller
{
    /**
     * Display the forgot password view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming forgot password request.
     */
    public function store(ForgotPasswordRequest $request, SendPasswordResetLink $sendResetLink): RedirectResponse
    {
        $sendResetLink->execute($request->validated()['email']);

        return redirect()->back()
            ->with('status', 'We have emailed your password reset link!');
    }

    /**
     * Display the reset password view.
     */
    public function edit(Request $request): View
    {
        return view('auth.reset-password', [
            'email' => $request->query('email'),
            'token' => $request->query('token'),
        ]);
    }

    /**
     * Handle an incoming password reset request.
     */
    public function update(ResetPasswordRequest $request, ResetPassword $resetPassword): RedirectResponse
    {
        return $resetPassword->execute($request->validated());
    }
}

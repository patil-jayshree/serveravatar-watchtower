@extends('layouts.auth')

@section('title', 'Verify email')

@section('content')
<div class="text-center mb-8">
    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center">
        <svg class="w-8 h-8 text-primary-600 dark:text-primary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
        </svg>
    </div>
    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Verify your email</h1>
    <p class="text-gray-600 dark:text-gray-400">
        We've sent a verification link to your email address.
    </p>
</div>

{{-- Success Alert --}}
@if (session('status'))
    <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
        <div class="flex items-center gap-3">
            <svg class="w-5 h-5 text-green-600 dark:text-green-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-sm text-green-800 dark:text-green-200">{{ session('status') }}</p>
        </div>
    </div>
@endif

{{-- Info Alert --}}
<div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
    <div class="flex items-start gap-3">
        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <div class="text-sm text-blue-800 dark:text-blue-200">
            <p class="font-medium">Please check your email</p>
            <p class="mt-1">Click the verification link in the email we sent to activate your account. The link will expire in 60 minutes.</p>
        </div>
    </div>
</div>

{{-- Current Email --}}
<div class="mb-6 p-4 bg-gray-100 dark:bg-gray-800 rounded-lg">
    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Sent to:</p>
    <p class="font-medium text-gray-900 dark:text-white">{{ $user->email }}</p>
</div>

{{-- Resend Verification --}}
<form method="POST" action="{{ route('verification.send') }}" class="space-y-4">
    @csrf

    <button
        type="submit"
        class="w-full py-3 px-4 rounded-lg font-semibold text-white bg-primary-600 hover:bg-primary-700 dark:bg-primary-500 dark:hover:bg-primary-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:focus:ring-offset-gray-900 transition-colors duration-200"
    >
        Resend verification email
    </button>
</form>

{{-- Logout --}}
<div class="mt-6 text-center">
    <form method="POST" action="{{ route('logout') }}" class="inline">
        @csrf
        <button
            type="submit"
            class="text-sm text-gray-600 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400"
        >
            Sign out and use a different account
        </button>
    </form>
</div>
@endsection

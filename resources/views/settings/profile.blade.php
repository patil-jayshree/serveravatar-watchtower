@extends('layouts.settings')

@section('settings_content')
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
    <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Profile Information</h3>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Update your account's profile information</p>
    </div>

    {{-- Success Message --}}
    @if (session('status'))
        <div class="mx-6 mt-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-green-600 dark:text-green-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-sm text-green-800 dark:text-green-200">{{ session('status') }}</p>
            </div>
        </div>
    @endif

    <div class="p-6">
        {{-- Avatar Section --}}
        <div class="mb-8 pb-8 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-6">
                {{-- Current Avatar --}}
                <div class="flex-shrink-0">
                    @if($user->avatar_path)
                        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-24 h-24 rounded-full object-cover border-4 border-gray-200 dark:border-gray-700">
                    @else
                        <img src="{{ $user->default_avatar_url }}" alt="{{ $user->name }}" class="w-24 h-24 rounded-full object-cover border-4 border-gray-200 dark:border-gray-700">
                    @endif
                </div>

                {{-- Avatar Actions --}}
                <div>
                    <h4 class="text-sm font-medium text-gray-900 dark:text-white">Profile Photo</h4>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">JPG, PNG, GIF or WebP. Max 2MB.</p>
                    
                    <div class="flex gap-3">
                        <form method="POST" action="{{ route('settings.avatar.upload') }}" enctype="multipart/form-data" class="inline">
                            @csrf
                            <label for="avatar-upload" class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg cursor-pointer transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Upload New
                            </label>
                            <input type="file" name="avatar" id="avatar-upload" accept="image/*" class="hidden" onchange="this.form.submit()">
                        </form>

                        @if($user->avatar_path)
                            <form method="POST" action="{{ route('settings.avatar.remove') }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg transition-colors duration-200">
                                    Remove
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Profile Form --}}
        <form method="POST" action="{{ route('settings.profile.update') }}" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Name --}}
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Full name <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 dark:focus:ring-primary-400 focus:border-transparent transition-colors duration-200">
                @error('name', 'profile')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Email --}}
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Email address <span class="text-red-500">*</span>
                </label>
                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 dark:focus:ring-primary-400 focus:border-transparent transition-colors duration-200">
                @error('email', 'profile')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
                
                @if(!$user->hasVerifiedEmail())
                    <p class="mt-2 text-sm text-amber-600 dark:text-amber-400">
                        <svg class="inline w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        Your email is not verified. A verification link will be sent if you change it.
                    </p>
                @endif
            </div>

            {{-- Submit --}}
            <div class="flex justify-end">
                <button type="submit" class="px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors duration-200">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

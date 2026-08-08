@extends('layouts.settings')

@section('settings_content')
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
    <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Preferences</h3>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage your account preferences</p>
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
        <form method="POST" action="{{ route('settings.preferences.update') }}" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Timezone --}}
            <div>
                <label for="timezone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Timezone
                </label>
                <select name="timezone" id="timezone"
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 dark:focus:ring-primary-400 focus:border-transparent transition-colors duration-200">
                    @foreach($timezones as $tz)
                        <option value="{{ $tz }}" {{ $user->timezone === $tz ? 'selected' : '' }}>
                            {{ $tz }}
                        </option>
                    @endforeach
                </select>
                @error('timezone')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Locale --}}
            <div>
                <label for="locale" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Language / Locale
                </label>
                <select name="locale" id="locale"
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 dark:focus:ring-primary-400 focus:border-transparent transition-colors duration-200">
                    <option value="en" {{ $user->locale === 'en' ? 'selected' : '' }}>English</option>
                    <option value="es" {{ $user->locale === 'es' ? 'selected' : '' }}>Español</option>
                    <option value="fr" {{ $user->locale === 'fr' ? 'selected' : '' }}>Français</option>
                    <option value="de" {{ $user->locale === 'de' ? 'selected' : '' }}>Deutsch</option>
                    <option value="pt" {{ $user->locale === 'pt' ? 'selected' : '' }}>Português</option>
                    <option value="zh" {{ $user->locale === 'zh' ? 'selected' : '' }}>中文</option>
                    <option value="ja" {{ $user->locale === 'ja' ? 'selected' : '' }}>日本語</option>
                </select>
                @error('locale')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Theme Preference --}}
            <div>
                <label for="theme_preference" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Appearance
                </label>
                <div class="grid grid-cols-3 gap-3">
                    <label class="relative cursor-pointer">
                        <input type="radio" name="theme_preference" value="light" class="sr-only peer" {{ $user->theme_preference === 'light' ? 'checked' : '' }}>
                        <div class="flex flex-col items-center p-4 rounded-lg border-2 border-gray-200 dark:border-gray-600 peer-checked:border-primary-500 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                            <svg class="w-8 h-8 mb-2 text-gray-700 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Light</span>
                        </div>
                    </label>
                    
                    <label class="relative cursor-pointer">
                        <input type="radio" name="theme_preference" value="dark" class="sr-only peer" {{ $user->theme_preference === 'dark' ? 'checked' : '' }}>
                        <div class="flex flex-col items-center p-4 rounded-lg border-2 border-gray-200 dark:border-gray-600 peer-checked:border-primary-500 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                            <svg class="w-8 h-8 mb-2 text-gray-700 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                            </svg>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Dark</span>
                        </div>
                    </label>
                    
                    <label class="relative cursor-pointer">
                        <input type="radio" name="theme_preference" value="system" class="sr-only peer" {{ $user->theme_preference === 'system' ? 'checked' : '' }}>
                        <div class="flex flex-col items-center p-4 rounded-lg border-2 border-gray-200 dark:border-gray-600 peer-checked:border-primary-500 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                            <svg class="w-8 h-8 mb-2 text-gray-700 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">System</span>
                        </div>
                    </label>
                </div>
                @error('theme_preference')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Submit --}}
            <div class="flex justify-end">
                <button type="submit" class="px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors duration-200">
                    Save Preferences
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

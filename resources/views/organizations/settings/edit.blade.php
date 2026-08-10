@extends('layouts.app')

@section('title', 'Settings - ' . $organization->name)

@section('content')
<div class="py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="mb-8">
            <a href="{{ route('organizations.show', $organization) }}" class="inline-flex items-center text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 mb-2">
                <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                {{ $organization->name }}
            </a>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Organization Settings</h1>
        </div>

        {{-- Success Message --}}
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

        {{-- Organization Info Form --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
            <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Organization Information</h3>
            </div>
            <form method="POST" action="{{ route('organizations.settings.update', $organization) }}" class="p-6 space-y-6">
                @csrf
                @method('PUT')

                {{-- Name --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Organization Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name', $organization->name) }}" required
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 dark:focus:ring-primary-400 focus:border-transparent transition-colors duration-200">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Submit --}}
                <div class="flex justify-end">
                    <button type="submit" class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors duration-200">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>

        {{-- Logo Form --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
            <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Organization Logo</h3>
            </div>
            <div class="p-6">
                {{-- Current Logo --}}
                <div class="flex items-center gap-6 mb-6">
                    @if($organization->logo_path)
                        <img src="{{ $organization->logo_url }}" alt="{{ $organization->name }}" class="w-20 h-20 rounded-xl object-cover border-4 border-gray-200 dark:border-gray-700">
                    @else
                        <img src="{{ $organization->default_logo_url }}" alt="{{ $organization->name }}" class="w-20 h-20 rounded-xl object-cover border-4 border-gray-200 dark:border-gray-700">
                    @endif
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">JPG, PNG, GIF or WebP. Max 2MB.</p>
                    </div>
                </div>

                {{-- Upload New Logo --}}
                <form method="POST" action="{{ route('organizations.settings.update', $organization) }}" enctype="multipart/form-data" class="flex gap-3">
                    @csrf
                    @method('PUT')
                    <label for="logo-upload" class="inline-flex items-center px-4 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg cursor-pointer transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Upload New
                    </label>
                    <input type="file" name="logo" id="logo-upload" accept="image/*" class="hidden" onchange="this.form.submit()">
                </form>

                {{-- Remove Logo --}}
                @if($organization->logo_path)
                    <form method="POST" action="{{ route('organizations.settings.update', $organization) }}" class="mt-3">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="remove_logo" value="1">
                        <button type="submit" class="text-sm text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300">
                            Remove logo
                        </button>
                    </form>
                @endif
            </div>
        </div>

        {{-- Danger Zone --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-red-200 dark:border-red-800">
            <div class="px-6 py-5 border-b border-red-200 dark:border-red-800">
                <h3 class="text-lg font-semibold text-red-600 dark:text-red-400">Danger Zone</h3>
            </div>
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Delete Organization</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Once you delete an organization, there is no going back.</p>
                    </div>
                    <form method="POST" action="{{ route('organizations.settings.update', $organization) }}" onsubmit="return confirm('Are you sure you want to delete this organization? This action cannot be undone.');">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="delete" value="1">
                        <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                            Delete Organization
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

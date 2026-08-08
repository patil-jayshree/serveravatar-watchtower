@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Dashboard</h1>
                <p class="mt-2 text-gray-600 dark:text-gray-400">Welcome back, {{ $user->name }}!</p>
            </div>
            <div class="flex items-center gap-4">
                {{-- Organization Switcher --}}
                @php
                    $organizations = $user->memberOf()->with('organization')->get()->pluck('organization');
                    $selectedOrg = session('selected_organization_id') ? \App\Models\Organization::find(session('selected_organization_id')) : null;
                @endphp
                @if($organizations->count() > 0)
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" @click.away="open = false" class="flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors duration-200">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">
                                {{ $selectedOrg ? $selectedOrg->name : 'Select Organization' }}
                            </span>
                            <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="open" x-cloak class="absolute right-0 mt-2 w-64 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg z-50">
                            <div class="p-2">
                                @foreach($organizations as $org)
                                    @php
                                        $membership = $user->memberOf()->where('organization_id', $org->id)->first();
                                    @endphp
                                    <form method="POST" action="{{ route('organizations.switch', $org) }}">
                                        @csrf
                                        <button type="submit" class="w-full flex items-center justify-between px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200 {{ session('selected_organization_id') == $org->id ? 'bg-primary-50 dark:bg-primary-900/20' : '' }}">
                                            <div class="flex items-center gap-3">
                                                @if($org->logo_path)
                                                    <img src="{{ $org->logo_url }}" alt="{{ $org->name }}" class="w-8 h-8 rounded-lg object-cover">
                                                @else
                                                    <img src="{{ $org->default_logo_url }}" alt="{{ $org->name }}" class="w-8 h-8 rounded-lg object-cover">
                                                @endif
                                                <div class="text-left">
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $org->name }}</p>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $membership->role->label() }}</p>
                                                </div>
                                            </div>
                                            @if(session('selected_organization_id') == $org->id)
                                                <svg class="w-4 h-4 text-primary-600" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                </svg>
                                            @endif
                                        </button>
                                    </form>
                                @endforeach
                            </div>
                            <div class="border-t border-gray-200 dark:border-gray-700 p-2">
                                <a href="{{ route('organizations.index') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors duration-200">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    Create Organization
                                </a>
                            </div>
                        </div>
                    </div>
                @else
                    <a href="{{ route('organizations.create') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                        Create Organization
                    </a>
                @endif

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors duration-200">
                        Logout
                    </button>
                </form>
            </div>
        </div>

        {{-- Selected Organization Overview --}}
        @if($selectedOrg)
            <div class="mt-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center gap-4 mb-4">
                    @if($selectedOrg->logo_path)
                        <img src="{{ $selectedOrg->logo_url }}" alt="{{ $selectedOrg->name }}" class="w-12 h-12 rounded-xl object-cover">
                    @else
                        <img src="{{ $selectedOrg->default_logo_url }}" alt="{{ $selectedOrg->name }}" class="w-12 h-12 rounded-xl object-cover">
                    @endif
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">{{ $selectedOrg->name }}</h2>
                        <a href="{{ route('organizations.show', $selectedOrg) }}" class="text-sm text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300">
                            View Organization →
                        </a>
                    </div>
                </div>
            </div>
        @else
            <div class="mt-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No organization selected</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Create or select an organization to get started.</p>
                <div class="mt-6">
                    <a href="{{ route('organizations.create') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                        Create Organization
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', $organization->name)

@section('content')
<div class="py-8">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center gap-4">
                @if($organization->logo_path)
                    <img src="{{ $organization->logo_url }}" alt="{{ $organization->name }}" class="w-16 h-16 rounded-xl object-cover">
                @else
                    <img src="{{ $organization->default_logo_url }}" alt="{{ $organization->name }}" class="w-16 h-16 rounded-xl object-cover">
                @endif
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $organization->name }}</h1>
                    <div class="flex items-center gap-3 mt-1">
                        @php
                            $membership = Auth::user()->memberOf()->where('organization_id', $organization->id)->first();
                        @endphp
                        @if($membership)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($membership->role->value === 'owner')
                                    bg-primary-100 dark:bg-primary-900 text-primary-800 dark:text-primary-200
                                @elseif($membership->role->value === 'admin')
                                    bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200
                                @else
                                    bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200
                                @endif">
                                {{ $membership->role->label() }}
                            </span>
                        @endif
                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $organization->member_count }} member{{ $organization->member_count !== 1 ? 's' : '' }}
                        </span>
                    </div>
                </div>
            </div>

            @if($membership && $membership->role->canManageSettings())
                <a href="{{ route('organizations.settings', $organization) }}" class="inline-flex items-center px-4 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Settings
                </a>
            @endif
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

        {{-- Quick Actions --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            {{-- Members Card --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Members</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $organization->member_count }}</p>
                    </div>
                    <div class="p-3 bg-gray-100 dark:bg-gray-700 rounded-lg">
                        <svg class="w-6 h-6 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                </div>
                @if($membership && $membership->role->canManageMembers())
                    <a href="{{ route('organizations.members.index', $organization) }}" class="mt-4 block text-sm font-medium text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300">
                        Manage Members →
                    </a>
                @endif
            </div>

            {{-- Projects Card (Coming Soon) --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 opacity-75">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Projects</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">—</p>
                    </div>
                    <div class="p-3 bg-gray-100 dark:bg-gray-700 rounded-lg">
                        <svg class="w-6 h-6 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                        </svg>
                    </div>
                </div>
                <p class="mt-4 text-xs text-gray-400 dark:text-gray-500">Coming in next phase</p>
            </div>

            {{-- Created Date --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Created</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white mt-1">{{ $organization->created_at->format('M d, Y') }}</p>
                    </div>
                    <div class="p-3 bg-gray-100 dark:bg-gray-700 rounded-lg">
                        <svg class="w-6 h-6 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- Organization Info --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">About this Organization</h2>
            <dl class="grid grid-cols-1 gap-4">
                <div class="flex justify-between">
                    <dt class="text-sm text-gray-500 dark:text-gray-400">Organization Name</dt>
                    <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $organization->name }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm text-gray-500 dark:text-gray-400">Status</dt>
                    <dd class="text-sm font-medium">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($organization->status->value === 'active')
                                bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200
                            @elseif($organization->status->value === 'suspended')
                                bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200
                            @else
                                bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200
                            @endif">
                            {{ $organization->status->label() }}
                        </span>
                    </dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm text-gray-500 dark:text-gray-400">Your Role</dt>
                    <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $membership?->role->label() ?? 'Unknown' }}</dd>
                </div>
            </dl>
        </div>
    </div>
</div>
@endsection

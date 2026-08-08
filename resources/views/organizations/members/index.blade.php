@extends('layouts.app')

@section('title', 'Members - ' . $organization->name)

@section('content')
<div class="py-8">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-8">
            <div>
                <a href="{{ route('organizations.show', $organization) }}" class="inline-flex items-center text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 mb-2">
                    <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    {{ $organization->name }}
                </a>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Members</h1>
                <p class="mt-2 text-gray-600 dark:text-gray-400">{{ $members->count() }} member{{ $members->count() !== 1 ? 's' : '' }} in this organization</p>
            </div>
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

        {{-- Add Member Form (Admin/Owner only) --}}
        @php
            $currentMembership = Auth::user()->memberOf()->where('organization_id', $organization->id)->first();
        @endphp
        @if($currentMembership && $currentMembership->role->canManageMembers())
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Add Member</h2>
                <form method="POST" action="{{ route('organizations.members.store', $organization) }}" class="flex gap-3">
                    @csrf
                    <input type="email" name="email" placeholder="Enter user's email address" required
                        class="flex-1 px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 dark:focus:ring-primary-400 focus:border-transparent">
                    <button type="submit" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors duration-200">
                        Add Member
                    </button>
                </form>
                @error('email')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
        @endif

        {{-- Members List --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($members as $membership)
                    <li class="p-6 flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            @if($membership->user->avatar_path)
                                <img src="{{ $membership->user->avatar_url }}" alt="{{ $membership->user->name }}" class="w-12 h-12 rounded-full object-cover">
                            @else
                                <img src="{{ $membership->user->default_avatar_url }}" alt="{{ $membership->user->name }}" class="w-12 h-12 rounded-full object-cover">
                            @endif
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $membership->user->name }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $membership->user->email }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            {{-- Role Selector --}}
                            @if($currentMembership && $currentMembership->role->canManageMembers() && !$membership->isOwner())
                                <form method="POST" action="{{ route('organizations.members.update', [$organization, $membership->user]) }}" class="flex items-center gap-2">
                                    @csrf
                                    @method('PUT')
                                    <select name="role" onchange="this.form.submit()"
                                        class="px-3 py-1.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                                        <option value="admin" {{ $membership->role->value === 'admin' ? 'selected' : '' }}>Admin</option>
                                        <option value="member" {{ $membership->role->value === 'member' ? 'selected' : '' }}>Member</option>
                                    </select>
                                </form>
                            @else
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

                            {{-- Remove Button (Admin/Owner only, cannot remove owner) --}}
                            @if($currentMembership && $currentMembership->role->canManageMembers() && !$membership->isOwner())
                                <form method="POST" action="{{ route('organizations.members.destroy', [$organization, $membership->user]) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors duration-200">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endsection

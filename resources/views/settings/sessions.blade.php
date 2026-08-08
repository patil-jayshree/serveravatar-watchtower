@extends('layouts.settings')

@section('settings_content')
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
    <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
        <div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Active Sessions</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage your active login sessions</p>
        </div>
        
        <form method="POST" action="{{ route('settings.sessions.revoke-all') }}">
            @csrf
            <button type="submit" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg transition-colors duration-200">
                Log out all other sessions
            </button>
        </form>
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
        @if(count($sessions) > 0)
            <div class="space-y-4">
                @foreach($sessions as $session)
                    <div class="flex items-center justify-between p-4 rounded-lg border border-gray-200 dark:border-gray-700 {{ $session['id'] === $currentSessionId ? 'bg-primary-50 dark:bg-primary-900/20 border-primary-500' : 'bg-gray-50 dark:bg-gray-800' }}">
                        <div class="flex items-center gap-4">
                            {{-- Device Icon --}}
                            <div class="flex-shrink-0">
                                @if(str_contains(strtolower($session['user_agent'] ?? ''), 'mobile'))
                                    <svg class="w-10 h-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                @elseif(str_contains(strtolower($session['user_agent'] ?? ''), 'tablet'))
                                    <svg class="w-10 h-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                @else
                                    <svg class="w-10 h-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                @endif
                            </div>

                            {{-- Session Info --}}
                            <div>
                                <div class="flex items-center gap-2">
                                    <h4 class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $session['id'] === $currentSessionId ? 'Current Session' : 'Active Session' }}
                                    </h4>
                                    @if($session['id'] === $currentSessionId)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-primary-100 dark:bg-primary-900 text-primary-800 dark:text-primary-200">
                                            This device
                                        </span>
                                    @endif
                                </div>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                    <span class="font-mono">{{ $session['ip_address'] ?? 'Unknown IP' }}</span>
                                </p>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                                    Last active: {{ isset($session['last_activity']) ? \Carbon\Carbon::createFromTimestamp($session['last_activity'])->diffForHumans() : 'Unknown' }}
                                </p>
                            </div>
                        </div>

                        {{-- Revoke Button --}}
                        @if($session['id'] !== $currentSessionId)
                            <form method="POST" action="{{ route('settings.sessions.revoke', $session['id']) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1.5 text-sm font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors duration-200">
                                    Revoke
                                </button>
                            </form>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No other sessions</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">You're only logged in on this device.</p>
            </div>
        @endif
    </div>
</div>
@endsection

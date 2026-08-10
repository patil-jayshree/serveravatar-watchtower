@extends('layouts.app')

@section('title', 'Agent - ' . $project->name)

@section('content')
<div class="py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="mb-8">
            <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mb-1">
                <a href="{{ route('organizations.show', $organization) }}" class="hover:text-gray-700 dark:hover:text-gray-300">{{ $organization->name }}</a>
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <a href="{{ route('organizations.projects.show', [$organization, $project]) }}" class="hover:text-gray-700 dark:hover:text-gray-300">{{ $project->name }}</a>
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <span>Agent</span>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Laravel Agent</h1>
        </div>

        {{-- Success/Error Messages --}}
        @if (session('status'))
            <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg" id="message-area">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-green-600 dark:text-green-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-sm text-green-800 dark:text-green-200" id="message-text">{{ session('status') }}</p>
                </div>
            </div>
        @endif

        {{-- Connection Status Banner --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    @if($project->is_connected)
                        <div class="flex-shrink-0 w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">Connected</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                @if($project->last_connected_at)
                                    Last connected: {{ $project->last_connected_at->diffForHumans() }}
                                @else
                                    Laravel Agent is connected
                                @endif
                            </p>
                        </div>
                    @else
                        <div class="flex-shrink-0 w-12 h-12 bg-yellow-100 dark:bg-yellow-900/30 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">Not Connected</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Follow the steps below to connect your Laravel application</p>
                        </div>
                    @endif
                </div>

                {{-- Verify Connection Button --}}
                <button onclick="verifyConnection()" class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Verify Connection
                </button>
            </div>
        </div>

        {{-- Installation Steps --}}
        <div class="space-y-6">
            {{-- Step 1: Install --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-8 h-8 bg-primary-100 dark:bg-primary-900/30 rounded-full flex items-center justify-center">
                        <span class="text-sm font-semibold text-primary-600 dark:text-primary-400">1</span>
                    </div>
                    <div class="flex-1">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Install the Agent</h2>

                        {{-- Development Installation --}}
                        <div class="mb-6">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300">Development</span>
                            </div>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">For local development, configure a path repository in your Laravel application's composer.json:</p>
                            <div class="bg-gray-900 dark:bg-gray-700 rounded-lg p-4 font-mono text-sm mb-3">
                                <p class="text-gray-400 mb-2">// Add to your composer.json repositories section:</p>
                                <p class="text-green-400">{</p>
                                <p class="text-green-400">    "type": "path",</p>
                                <p class="text-green-400">    "url": "../sa-watchtower-agent",</p>
                                <p class="text-green-400">    "options": { "symlink": true }</p>
                                <p class="text-green-400">}</p>
                            </div>
                            <div class="bg-gray-900 dark:bg-gray-700 rounded-lg p-4 font-mono text-sm">
                                <code class="text-green-400">composer require serveravatar/watchtower-agent:*@dev</code>
                            </div>
                        </div>

                        {{-- Production Installation --}}
                        <div>
                            <div class="flex items-center gap-2 mb-2">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300">Production</span>
                                <span class="text-xs text-gray-500 dark:text-gray-400">(Coming soon — package pending Packagist publication)</span>
                            </div>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">Once published to Packagist, install via:</p>
                            <div class="bg-gray-900 dark:bg-gray-700 rounded-lg p-4 font-mono text-sm">
                                <code class="text-green-400">composer require serveravatar/watchtower-agent</code>
                            </div>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">Package repository: git@github.com:patil-jayshree/serveravatar-watchtower-agent.git</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Step 2: Configure --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-8 h-8 bg-primary-100 dark:bg-primary-900/30 rounded-full flex items-center justify-center">
                        <span class="text-sm font-semibold text-primary-600 dark:text-primary-400">2</span>
                    </div>
                    <div class="flex-1">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Configure Environment</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Add these variables to your Laravel application's <code class="px-1 py-0.5 bg-gray-100 dark:bg-gray-700 rounded text-xs">.env</code> file:</p>

                        <div class="bg-gray-900 dark:bg-gray-700 rounded-lg p-4 font-mono text-sm space-y-2">
                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="text-gray-400">WATCHTOWER_URL=</span>
                                    <span class="text-green-400">{{ url('/') }}</span>
                                </div>
                                <button onclick="copyToClipboard('{{ url('/') }}', 'URL')" class="text-xs text-gray-400 hover:text-white">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                                    </svg>
                                </button>
                            </div>
                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="text-gray-400">WATCHTOWER_TOKEN=</span>
                                    <span class="text-green-400" id="token-display">{{ $agentToken ? $agentToken->masked_token : 'No token generated' }}</span>
                                </div>
                                @if($agentToken && $justGenerated && $rawToken)
                                    <button onclick="copyToClipboard('{{ $rawToken }}', 'Token')" class="text-xs text-gray-400 hover:text-white">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                                        </svg>
                                    </button>
                                @endif
                            </div>
                        </div>

                        @if($justGenerated && $rawToken)
                            <div class="mt-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg p-4">
                                <div class="flex items-start gap-3">
                                    <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    <div>
                                        <p class="text-sm font-medium text-amber-800 dark:text-amber-200">Store this token securely.</p>
                                        <p class="text-sm text-amber-700 dark:text-amber-300 mt-1">It will not be shown again. If you lose it, regenerate a new token.</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Step 3: Verify --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-8 h-8 bg-primary-100 dark:bg-primary-900/30 rounded-full flex items-center justify-center">
                        <span class="text-sm font-semibold text-primary-600 dark:text-primary-400">3</span>
                    </div>
                    <div class="flex-1">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Verify Connection</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Run this command in your Laravel application to test the connection:</p>
                        <div class="bg-gray-900 dark:bg-gray-700 rounded-lg p-4 font-mono text-sm">
                            <code class="text-green-400">php artisan watchtower:status</code>
                        </div>

                        <div class="mt-6 border-t border-gray-200 dark:border-gray-700 pt-6">
                            <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Expected Output:</h3>
                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 text-sm font-mono">
                                @if($project->is_connected)
                                    <p class="text-green-600 dark:text-green-400">Watchtower Agent<br>----------------<br><br>Status: Connected<br>Project: {{ $project->name }}<br>Environment: {{ $project->environment }}</p>
                                @else
                                    <p class="text-yellow-600 dark:text-yellow-400">Watchtower Agent<br>----------------<br><br>Status: Not Connected<br><br>Connection has not been verified.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Generate Token Button (when no token exists) --}}
        @if(!$agentToken || !$agentToken->isActive())
            <div class="mt-6 flex flex-wrap gap-3 justify-end">
                <button onclick="generateToken()" class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    Generate Token
                </button>
            </div>
        @endif

        {{-- Token Actions --}}
        @if($agentToken && $agentToken->isActive())
            <div class="mt-6 flex flex-wrap gap-3 justify-end">
                <button onclick="regenerateToken()" class="inline-flex items-center px-4 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Regenerate Token
                </button>
                <button onclick="revokeToken()" class="inline-flex items-center px-4 py-2 bg-red-100 dark:bg-red-900/30 hover:bg-red-200 dark:hover:bg-red-900/50 text-red-700 dark:text-red-300 text-sm font-medium rounded-lg transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Revoke Token
                </button>
            </div>
        @endif
    </div>
</div>

{{-- Confirmation Modals --}}
{{-- Regenerate Confirmation --}}
<div id="regenerate-modal" class="hidden fixed inset-0 bg-black/50 dark:bg-black/70 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-md w-full p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Regenerate Agent Token?</h3>
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
            Regenerating this token will disconnect any Laravel application currently using the old token. You will need to update your WATCHTOWER_TOKEN environment variable.
        </p>
        <div class="flex justify-end gap-3">
            <button onclick="closeModals()" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors duration-200">
                Cancel
            </button>
            <button onclick="confirmRegenerate()" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                Regenerate
            </button>
        </div>
    </div>
</div>

{{-- Revoke Confirmation --}}
<div id="revoke-modal" class="hidden fixed inset-0 bg-black/50 dark:bg-black/70 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-md w-full p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Revoke Agent Token?</h3>
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
            Revoking this token will immediately disconnect any Laravel application using it. You can generate a new token later.
        </p>
        <div class="flex justify-end gap-3">
            <button onclick="closeModals()" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors duration-200">
                Cancel
            </button>
            <button onclick="confirmRevoke()" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                Revoke
            </button>
        </div>
    </div>
</div>

{{-- Generate Token Modal --}}
<div id="generate-modal" class="hidden fixed inset-0 bg-black/50 dark:bg-black/70 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-md w-full p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Generate Agent Token?</h3>
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
            This will create a new agent token for this project. You will be shown the token once — store it securely.
        </p>
        <div class="flex justify-end gap-3">
            <button onclick="closeModals()" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors duration-200">
                Cancel
            </button>
            <button onclick="confirmGenerate()" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                Generate
            </button>
        </div>
    </div>
</div>

{{-- Verify Connection Modal --}}
<div id="verify-modal" class="hidden fixed inset-0 bg-black/50 dark:bg-black/70 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-md w-full p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Verify Connection</h3>
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
            To verify the connection, run <code class="px-1 py-0.5 bg-gray-100 dark:bg-gray-700 rounded text-xs">php artisan watchtower:status</code> in your Laravel application terminal. This checks if the agent can successfully connect to Watchtower.
        </p>
        <div class="flex justify-end gap-3">
            <button onclick="closeModals()" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors duration-200">
                Close
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let justGeneratedToken = '{{ $justGenerated && $rawToken ? $rawToken : '' }}';

function copyToClipboard(text, label) {
    navigator.clipboard.writeText(text).then(() => {
        showToast(label + ' copied to clipboard');
    });
}

function showToast(message) {
    const toast = document.createElement('div');
    toast.className = 'fixed bottom-4 right-4 bg-gray-900 dark:bg-gray-700 text-white px-4 py-2 rounded-lg shadow-lg text-sm z-50';
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}

function verifyConnection() {
    document.getElementById('verify-modal').classList.remove('hidden');
}

function generateToken() {
    document.getElementById('generate-modal').classList.remove('hidden');
}

function regenerateToken() {
    document.getElementById('regenerate-modal').classList.remove('hidden');
}

function revokeToken() {
    document.getElementById('revoke-modal').classList.remove('hidden');
}

function closeModals() {
    document.getElementById('regenerate-modal').classList.add('hidden');
    document.getElementById('revoke-modal').classList.add('hidden');
    document.getElementById('verify-modal').classList.add('hidden');
    document.getElementById('generate-modal').classList.add('hidden');
}

function confirmGenerate() {
    closeModals();
    fetch('{{ route('organizations.projects.agent.store', [$organization, $project]) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.token) {
            justGeneratedToken = data.token;
            window.location.href = '{{ route('organizations.projects.agent.show', [$organization, $project]) }}?token=' + encodeURIComponent(data.token);
        } else {
            window.location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('An error occurred. Please try again.');
    });
}

function confirmRegenerate() {
    closeModals();
    fetch('{{ route('organizations.projects.agent.update', [$organization, $project]) }}', {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.token) {
            justGeneratedToken = data.token;
            window.location.href = '{{ route('organizations.projects.agent.show', [$organization, $project]) }}?token=' + encodeURIComponent(data.token);
        } else {
            window.location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('An error occurred. Please try again.');
    });
}

function confirmRevoke() {
    closeModals();
    fetch('{{ route('organizations.projects.agent.destroy', [$organization, $project]) }}', {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        window.location.reload();
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('An error occurred. Please try again.');
    });
}
</script>
@endpush

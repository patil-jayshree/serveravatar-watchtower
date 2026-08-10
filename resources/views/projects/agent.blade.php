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
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Agent</h1>
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

        {{-- Connection Status Card --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Agent Connection</h2>
            
            <div class="flex items-center gap-4 mb-6">
                @if($agentToken && $agentToken->isActive())
                    <div class="flex-shrink-0 w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-lg font-medium text-gray-900 dark:text-white">Token Active</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Your Laravel application is connected</p>
                    </div>
                @elseif($agentToken && $agentToken->isRevoked())
                    <div class="flex-shrink-0 w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-lg font-medium text-gray-900 dark:text-white">Token Revoked</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">The agent token has been revoked</p>
                    </div>
                @else
                    <div class="flex-shrink-0 w-12 h-12 bg-yellow-100 dark:bg-yellow-900/30 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-lg font-medium text-gray-900 dark:text-white">Not Connected</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Generate an Agent Token to connect your Laravel application</p>
                    </div>
                @endif
            </div>

            {{-- Token Display --}}
            @if($agentToken)
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 mb-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Agent Token</p>
                            <p class="font-mono text-sm text-gray-900 dark:text-white" id="token-display">
                                @if($justGenerated && $rawToken)
                                    {{ $rawToken }}
                                @else
                                    {{ $agentToken->masked_token }}
                                @endif
                            </p>
                        </div>
                        @if($justGenerated && $rawToken)
                            <button onclick="copyToken()" class="inline-flex items-center px-3 py-1.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                                <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                                </svg>
                                Copy Token
                            </button>
                        @endif
                    </div>
                </div>

                {{-- Warning for newly generated tokens --}}
                @if($justGenerated && $rawToken)
                    <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg p-4 mb-6">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <div>
                                <p class="text-sm font-medium text-amber-800 dark:text-amber-200">Store this token securely.</p>
                                <p class="text-sm text-amber-700 dark:text-amber-300 mt-1">It will be used by your Laravel application to authenticate with Watchtower. This token will not be shown again after you leave this page.</p>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Token Info --}}
                <div class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                    @if($agentToken->isActive())
                        <p>Created: {{ $agentToken->created_at->format('M d, Y H:i') }}</p>
                    @elseif($agentToken->isRevoked())
                        <p>Revoked: {{ $agentToken->revoked_at->format('M d, Y H:i') }}</p>
                    @endif
                </div>
            @endif

            {{-- Actions --}}
            <div class="flex flex-wrap gap-3">
                @if(!$agentToken || ($agentToken && $agentToken->isRevoked()))
                    <button onclick="generateToken()" class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Generate Token
                    </button>
                @elseif($agentToken && $agentToken->isActive())
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
                @endif
            </div>
        </div>

        {{-- Installation Section --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Laravel Agent</h2>
            <p class="text-gray-600 dark:text-gray-400 mb-4">
                Installation instructions will be available soon. The Watchtower Agent will allow your Laravel application to connect and send telemetry data to Watchtower.
            </p>
            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    <span class="font-medium">Coming Soon:</span> Composer installation, configuration, and setup guide.
                </p>
            </div>
        </div>
    </div>
</div>

{{-- Confirmation Modals --}}
{{-- Regenerate Confirmation --}}
<div id="regenerate-modal" class="hidden fixed inset-0 bg-black/50 dark:bg-black/70 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-md w-full p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Regenerate Agent Token?</h3>
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
            Regenerating this token will disconnect any Laravel application currently using the old token until it is updated.
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

@endsection

@push('scripts')
<script>
function copyToken() {
    const tokenDisplay = document.getElementById('token-display');
    if (tokenDisplay) {
        navigator.clipboard.writeText(tokenDisplay.textContent.trim()).then(() => {
            showCopyFeedback();
        });
    }
}

function showCopyFeedback() {
    const messageArea = document.getElementById('message-area') || createMessageArea();
    const messageText = document.getElementById('message-text') || messageArea.querySelector('p');
    
    messageArea.className = 'mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg';
    messageText.className = 'text-sm text-green-800 dark:text-green-200';
    messageText.textContent = 'Copied!';
    
    messageArea.classList.remove('hidden');
}

function createMessageArea() {
    const div = document.createElement('div');
    div.id = 'message-area';
    div.className = 'mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg';
    div.innerHTML = '<p id="message-text" class="text-sm text-green-800 dark:text-green-200"></p>';
    document.querySelector('.max-w-3xl').prepend(div);
    return div;
}

function generateToken() {
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
            // Show the new token
            window.location.href = '{{ route('organizations.projects.agent.show', [$organization, $project]) }}?token=' + encodeURIComponent(data.token);
        } else {
            // Token already exists, show it
            window.location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
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
            window.location.href = '{{ route('organizations.projects.agent.show', [$organization, $project]) }}?token=' + encodeURIComponent(data.token);
        } else {
            window.location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
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
        alert('An error occurred. Please try again.');
    });
}
</script>
@endpush

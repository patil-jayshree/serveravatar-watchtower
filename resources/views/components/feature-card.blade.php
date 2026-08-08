{{--
    Feature Card Component — For the features preview section

    Usage:
    <x-feature-card
        title="Request Monitoring"
        description="Track every HTTP request with timing and status."
        icon="<svg>...</svg>"
    />
--}}

@props([
    'title' => '',
    'description' => '',
    'icon' => '',
])

<div class="group relative p-6 bg-white dark:bg-gray-800/50 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1 overflow-hidden">
    {{-- Decorative gradient background on hover --}}
    <div class="absolute inset-0 bg-gradient-to-br from-primary-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

    {{-- Content --}}
    <div class="relative">
        {{-- Icon --}}
        @if($icon)
            <div class="mb-5 flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-br from-primary-100 to-primary-50 dark:from-primary-900/50 dark:to-primary-800/30 text-primary-600 dark:text-primary-400 shadow-sm group-hover:scale-110 group-hover:shadow-md transition-all duration-300">
                {!! $icon !!}
            </div>
        @endif

        {{-- Title --}}
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
            {{ $title }}
        </h3>

        {{-- Description --}}
        <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
            {{ $description }}
        </p>
    </div>
</div>

{{--
    Card Component — Feature/info card

    Usage:
    <x-card title="Title" icon="...">Content</x-card>
--}}

@props([
    'title' => '',
    'icon' => '',
    'class' => '',
])

<div class="relative group p-6 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-1 {{ $class }}">
    {{-- Icon --}}
    @if($icon)
        <div class="mb-4 flex items-center justify-center w-12 h-12 rounded-xl bg-primary-100 dark:bg-primary-900/50 text-primary-600 dark:text-primary-400">
            {!! $icon !!}
        </div>
    @endif

    {{-- Title --}}
    @if($title)
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">
            {{ $title }}
        </h3>
    @endif

    {{-- Content --}}
    <div class="text-gray-600 dark:text-gray-400">
        {{ $slot }}
    </div>
</div>

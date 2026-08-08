{{--
    Secondary Button Component

    Usage:
    <x-btn.secondary href="/register">Register</x-btn.secondary>
    <x-btn.secondary type="button">Click Me</x-btn.secondary>
--}}

@props([
    'href' => null,
    'type' => 'button',
    'class' => '',
])

@php
    $classes = 'inline-flex items-center justify-center gap-2 px-6 py-3 '
        . 'text-sm font-semibold '
        . 'text-primary-600 dark:text-primary-400 '
        . 'bg-primary-50 dark:bg-primary-950/50 '
        . 'hover:bg-primary-100 dark:hover:bg-primary-900/50 '
        . 'border border-primary-200 dark:border-primary-800 '
        . 'rounded-lg transition-all duration-200 '
        . 'focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 '
        . 'dark:focus:ring-offset-gray-900 '
        . $class;
@endphp

@if($href)
    <a href="{{ $href }}" class="{{ $classes }}">
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" class="{{ $classes }}">
        {{ $slot }}
    </button>
@endif

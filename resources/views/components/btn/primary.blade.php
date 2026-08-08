{{--
    Primary Button Component

    Usage:
    <x-btn.primary href="/login">Login</x-btn.primary>
    <x-btn.primary type="submit">Submit</x-btn.primary>
--}}

@props([
    'href' => null,
    'type' => 'button',
    'class' => '',
])

@php
    $classes = 'inline-flex items-center justify-center gap-2 px-6 py-3 '
        . 'text-sm font-semibold text-white '
        . 'bg-primary-600 hover:bg-primary-700 '
        . 'dark:bg-primary-500 dark:hover:bg-primary-600 '
        . 'rounded-lg transition-all duration-200 '
        . 'focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 '
        . 'dark:focus:ring-offset-gray-900 '
        . 'shadow-sm hover:shadow-md '
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

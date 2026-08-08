{{--
    Badge Component — Small label/tag

    Usage:
    <x-badge>New</x-badge>
    <x-badge variant="success">Active</x-badge>
--}}

@props([
    'variant' => 'default', // default, success, warning, danger, info
    'class' => '',
])

@php
    $variantClasses = match($variant) {
        'success' => 'bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-400',
        'warning' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/50 dark:text-yellow-400',
        'danger' => 'bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-400',
        'info' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-400',
        default => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300',
    };
@endphp

<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $variantClasses }} {{ $class }}">
    {{ $slot }}
</span>

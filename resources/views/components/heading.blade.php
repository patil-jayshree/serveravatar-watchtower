{{--
    Heading Component — Consistent typography

    Usage:
    <x-heading size="xl">Title</x-heading>
--}}

@props([
    'size' => 'xl', // xs, sm, md, lg, xl, 2xl, 3xl, 4xl
    'class' => '',
    'as' => 'h2',
])

@php
    $sizeClasses = match($size) {
        'xs' => 'text-xs',
        'sm' => 'text-sm',
        'md' => 'text-base',
        'lg' => 'text-lg',
        'xl' => 'text-xl',
        '2xl' => 'text-2xl',
        '3xl' => 'text-3xl',
        '4xl' => 'text-4xl',
        '5xl' => 'text-5xl',
        default => 'text-xl',
    };
@endphp

<{{ $as }} class="font-bold text-gray-900 dark:text-white {{ $sizeClasses }} {{ $class }}">
    {{ $slot }}
</{{ $as }}>

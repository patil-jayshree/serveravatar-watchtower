{{--
    Container Component — Centered max-width container

    Usage:
    <x-container>Content here</x-container>
--}}

@props([
    'class' => '',
])

<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 {{ $class }}">
    {{ $slot }}
</div>

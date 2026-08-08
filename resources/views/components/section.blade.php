{{--
    Section Component — Full-width page section

    Usage:
    <x-section>Content here</x-section>
    <x-section class="bg-gray-50 dark:bg-gray-900">Custom section</x-section>
--}}

@props([
    'class' => '',
])

<section class="w-full py-20 lg:py-28 {{ $class }}">
    {{ $slot }}
</section>

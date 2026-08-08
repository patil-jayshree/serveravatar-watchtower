{{--
    Logo Component — Theme-aware

    Usage:
    <x-logo class="h-8" />
    <x-logo class="h-10" with-text />

    Note: The theme-aware switching is handled via JavaScript data attributes
    in the layouts. This component renders both logos with proper hide/show classes.
--}}

@props([
    'class' => 'h-8',
    'withText' => false,
])

<a href="/" class="inline-flex items-center gap-3 {{ $class }}">
    {{-- Dark Logo (shown in light mode) --}}
    <img
        src="{{ config('watchtower.logos.dark') }}"
        alt="{{ config('watchtower.application_name') }}"
        class="{{ $class }} hidden dark:block"
        loading="lazy"
    />

    {{-- Light Logo (shown in dark mode) --}}
    <img
        src="{{ config('watchtower.logos.light') }}"
        alt="{{ config('watchtower.application_name') }}"
        class="{{ $class }} dark:hidden"
        loading="lazy"
    />

    {{-- Optional Product Text --}}
    @if($withText)
        <span class="text-xl font-bold text-gray-900 dark:text-white">
            {{ config('watchtower.short_name') }}
        </span>
    @endif
</a>

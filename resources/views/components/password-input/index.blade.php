{{--
Reusable Password Input Component with Show/Hide Toggle

Usage:
<x-password-input
    name="password"
    id="password"
    label="Password"
    placeholder="••••••••"
    :showRequirements="true"
    autocomplete="new-password"
/>
--}}

@props([
    'name' => 'password',
    'id' => null,
    'label' => null,
    'placeholder' => '••••••••',
    'showRequirements' => false,
    'autocomplete' => null,
    'required' => false,
])

@php
$id = $id ?? $name;
@endphp

<div x-data="{ showPassword: false }">
    @if($label)
        <label for="{{ $id }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            {{ $label }}
        </label>
    @endif
    
    <div class="relative">
        <input
            :type="showPassword ? 'text' : 'password'"
            name="{{ $name }}"
            id="{{ $id }}"
            @if($autocomplete) autocomplete="{{ $autocomplete }}" @endif
            @if($required) required @endif
            {{ $attributes->merge([
                'class' => 'w-full px-4 py-3 pr-12 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 dark:focus:ring-primary-400 focus:border-transparent transition-colors duration-200'
            ]) }}
            placeholder="{{ $placeholder }}"
        >
        
        {{-- Show/Hide Toggle --}}
        <button
            type="button"
            @click="showPassword = !showPassword"
            class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 focus:outline-none"
            :aria-label="showPassword ? 'Hide password' : 'Show password'"
        >
            {{-- Eye Open (visible) --}}
            <svg x-show="!showPassword" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
            
            {{-- Eye Closed (hidden) --}}
            <svg x-show="showPassword" x-cloak class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
            </svg>
        </button>
    </div>
    
    {{-- Password Requirements (optional) --}}
    @if($showRequirements)
        <div x-data="{
            password: '',
            get hasMinLength() { return this.password && this.password.length >= 8; },
            get hasUppercase() { return this.password && /[A-Z]/.test(this.password); },
            get hasLowercase() { return this.password && /[a-z]/.test(this.password); },
            get hasNumber() { return this.password && /[0-9]/.test(this.password); },
            get hasSpecial() { return this.password && /[!@#$%^&*(),.?\:{}|<>]/.test(this.password); },
            get allValid() { return this.hasMinLength && this.hasUppercase && this.hasLowercase && this.hasNumber && this.hasSpecial; }
        }" x-init="
            $watch('password', value => $dispatch('password-update', { value }))
        " class="mt-2 space-y-1"
        >
            <template x-if="!allValid">
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Password requirements:</p>
            </template>
            
            <div class="flex flex-wrap gap-x-4 gap-y-1">
                <div class="flex items-center gap-1.5 text-xs" :class="hasMinLength ? 'text-green-600 dark:text-green-400' : 'text-gray-500 dark:text-gray-400'">
                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path x-show="hasMinLength" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        <path x-show="!hasMinLength" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <span>8+ characters</span>
                </div>
                
                <div class="flex items-center gap-1.5 text-xs" :class="hasUppercase ? 'text-green-600 dark:text-green-400' : 'text-gray-500 dark:text-gray-400'">
                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path x-show="hasUppercase" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        <path x-show="!hasUppercase" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <span>Uppercase</span>
                </div>
                
                <div class="flex items-center gap-1.5 text-xs" :class="hasLowercase ? 'text-green-600 dark:text-green-400' : 'text-gray-500 dark:text-gray-400'">
                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path x-show="hasLowercase" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        <path x-show="!hasLowercase" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <span>Lowercase</span>
                </div>
                
                <div class="flex items-center gap-1.5 text-xs" :class="hasNumber ? 'text-green-600 dark:text-green-400' : 'text-gray-500 dark:text-gray-400'">
                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path x-show="hasNumber" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        <path x-show="!hasNumber" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <span>Number</span>
                </div>
                
                <div class="flex items-center gap-1.5 text-xs" :class="hasSpecial ? 'text-green-600 dark:text-green-400' : 'text-gray-500 dark:text-gray-400'">
                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path x-show="hasSpecial" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        <path x-show="!hasSpecial" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <span>Special</span>
                </div>
            </div>
        </div>
    @endif
</div>

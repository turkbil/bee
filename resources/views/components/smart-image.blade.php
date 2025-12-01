{{--
    Smart Image Component with Skeleton Loading

    Usage:
    <x-smart-image
        :src="$imageUrl"
        :alt="$altText"
        class="w-full h-full object-cover"
        skeleton-class="bg-gray-200"
        :eager="false"
    />

    Props:
    - src: Image URL (required)
    - alt: Alt text (required)
    - class: CSS classes for the image
    - skeleton-class: CSS classes for skeleton
    - eager: true for priority images (no lazy loading)
    - icon: Fallback icon class (default: fa-light fa-image)
--}}

@props([
    'src' => null,
    'alt' => '',
    'class' => 'w-full h-full object-cover',
    'skeletonClass' => 'bg-gradient-to-br from-gray-100 to-gray-200',
    'eager' => false,
    'icon' => 'fa-light fa-image'
])

<div
    x-data="{ loaded: false, error: false }"
    class="relative overflow-hidden {{ $skeletonClass }}"
    {{ $attributes->except(['src', 'alt', 'class', 'skeleton-class', 'eager', 'icon']) }}
>
    {{-- Skeleton Loader --}}
    <div
        x-show="!loaded && !error"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="absolute inset-0 z-10"
    >
        {{-- Shimmer animation --}}
        <div class="w-full h-full relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/40 to-transparent -translate-x-full animate-[shimmer_1.5s_infinite]"></div>
        </div>
        {{-- Loading icon --}}
        <div class="absolute inset-0 flex items-center justify-center">
            <i class="fa-solid fa-image text-3xl text-gray-300 animate-pulse"></i>
        </div>
    </div>

    {{-- Actual Image --}}
    @if($src)
        <img
            src="{{ $src }}"
            alt="{{ $alt }}"
            class="{{ $class }} transition-opacity duration-300"
            :class="{ 'opacity-0': !loaded, 'opacity-100': loaded }"
            @load="loaded = true"
            @error="error = true"
            @if(!$eager) loading="lazy" @endif
            decoding="async"
        >
    @endif

    {{-- Error/No Image Fallback --}}
    <div
        x-show="error || !{{ $src ? 'true' : 'false' }}"
        x-cloak
        class="absolute inset-0 flex items-center justify-center bg-gradient-to-br from-gray-50 to-gray-100"
    >
        <i class="{{ $icon }} text-5xl text-gray-300"></i>
    </div>
</div>

{{-- Shimmer keyframe (add once to page) --}}
@once
@push('styles')
<style>
@keyframes shimmer {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}
</style>
@endpush
@endonce

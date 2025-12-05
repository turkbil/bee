@props([
    'src' => null,
    'alt' => ''',
    'class' => 'w-full h-full object-cover',
    'wrapperClass' => 'relative overflow-hidden',
    'skeletonClass' => 'absolute inset-0 bg-gradient-to-br from-white/5 to-white/10 animate-pulse',
])

{{-- Lazy Loading Image with Skeleton Effect --}}
<div {{ $attributes->merge(['class' => $wrapperClass]) }} x-data="{ loaded: false }">
    @if($src)
        {{-- Skeleton Loader --}}
        <div x-show="!loaded" {{ $attributes->merge(['class' => $skeletonClass]) }}>
            <div class="absolute inset-0 flex items-center justify-center">
                <i class="fas fa-music text-white/20 text-2xl"></i>
            </div>
        </div>

        {{-- Actual Image --}}
        <img
            src="{{ $src }}"
            alt="{{ $alt }}"
            class="{{ $class }}"
            loading="lazy"
            @load="loaded = true"
            x-show="loaded"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
        >
    @else
        {{-- Fallback: No image --}}
        <div class="absolute inset-0 bg-gradient-to-br from-purple-500/20 to-pink-500/20 flex items-center justify-center">
            <i class="fas fa-music text-white/40 text-3xl"></i>
        </div>
    @endif
</div>

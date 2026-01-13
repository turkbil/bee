@props([
    'src' => null,
    'srcWebp' => null,
    'srcJpg' => null,
    'alt' => '',
    'class' => 'w-full h-full object-cover',
    'wrapperClass' => 'relative overflow-hidden',
    'skeletonClass' => 'absolute inset-0 bg-gradient-to-br from-white/5 to-white/10 animate-pulse',
    'width' => null,
    'height' => null,
])

{{--
    Lazy Loading Image with Picture Tag Fallback
    Eski cihazlar (Safari <14, IE11, Android <5) için JPG fallback
--}}
@php
    // Eğer srcWebp/srcJpg ayrı verilmişse onları kullan
    // Verilmemişse src'den türet
    $webpSrc = $srcWebp;
    $jpgSrc = $srcJpg;

    // src varsa ve ayrı formatlar verilmemişse, thumb URL'i parse et
    if ($src && (!$webpSrc || !$jpgSrc)) {
        // Eğer thumbmaker URL'i ise
        if (str_contains($src, '/thumbmaker?') || str_contains($src, '&f=')) {
            // Format parametresini değiştirerek her iki versiyon oluştur
            $webpSrc = $webpSrc ?: preg_replace('/([&?])f=[^&]+/', '$1f=webp', $src);
            $jpgSrc = $jpgSrc ?: preg_replace('/([&?])f=[^&]+/', '$1f=jpg', $src);

            // f parametresi yoksa ekle
            if (!str_contains($webpSrc, '&f=') && !str_contains($webpSrc, '?f=')) {
                $webpSrc .= (str_contains($webpSrc, '?') ? '&' : '?') . 'f=webp';
            }
            if (!str_contains($jpgSrc, '&f=') && !str_contains($jpgSrc, '?f=')) {
                $jpgSrc .= (str_contains($jpgSrc, '?') ? '&' : '?') . 'f=jpg';
            }
        } else {
            // Normal URL - her iki format için aynı kullan
            $webpSrc = $webpSrc ?: $src;
            $jpgSrc = $jpgSrc ?: $src;
        }
    }
@endphp

<div {{ $attributes->merge(['class' => $wrapperClass]) }} x-data="{ loaded: false }">
    @if($src || $webpSrc || $jpgSrc)
        {{-- Skeleton Loader --}}
        <div x-show="!loaded" class="{{ $skeletonClass }}">
            <div class="absolute inset-0 flex items-center justify-center">
                <i class="fas fa-music text-white/20 text-2xl"></i>
            </div>
        </div>

        {{-- Picture Tag with WebP + JPG Fallback --}}
        <picture x-show="loaded" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
            {{-- WebP source (modern browsers) --}}
            @if($webpSrc)
                <source srcset="{{ $webpSrc }}" type="image/webp">
            @endif

            {{-- JPG fallback (eski cihazlar: Safari <14, IE11, Android <5) --}}
            <img
                src="{{ $jpgSrc ?: $src }}"
                alt="{{ $alt }}"
                class="{{ $class }}"
                loading="lazy"
                @load="loaded = true"
                @if($width) width="{{ $width }}" @endif
                @if($height) height="{{ $height }}" @endif
                onerror="this.onerror=null; this.parentElement.querySelector('source')?.remove();"
            >
        </picture>
    @else
        {{-- Fallback: No image --}}
        <div class="absolute inset-0 bg-gradient-to-br from-purple-500/20 to-pink-500/20 flex items-center justify-center">
            <i class="fas fa-music text-white/40 text-3xl"></i>
        </div>
    @endif
</div>

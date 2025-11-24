{{--
    Hero Subheader Component - Büyük arka planlı tasarım

    Usage:
    @include('components.subheaders.hero', [
        'title' => 'Page Title',
        'subtitle' => 'Alt başlık açıklaması',
        'backgroundImage' => '/images/hero-bg.jpg',
        'breadcrumbs' => [...]
    ])
--}}

@php
    $breadcrumbs = $breadcrumbs ?? [];
    $subtitle = $subtitle ?? null;
    $backgroundImage = $backgroundImage ?? null;
@endphp

<section class="relative bg-gradient-to-r from-blue-600 to-purple-700 dark:from-gray-800 dark:to-gray-900 overflow-hidden">
    @if($backgroundImage)
        <div class="absolute inset-0 opacity-20">
            <img src="{{ $backgroundImage }}" alt="" class="w-full h-full object-cover">
        </div>
    @endif

    <div class="relative container mx-auto px-4 py-16 md:py-24">
        @if(!empty($breadcrumbs))
            <nav class="text-sm text-white/70 mb-4">
                @foreach($breadcrumbs as $index => $crumb)
                    @if(isset($crumb['url']))
                        <a href="{{ $crumb['url'] }}" class="hover:text-white transition">
                            {{ $crumb['label'] }}
                        </a>
                        @if($index < count($breadcrumbs) - 1)
                            <span class="mx-2">/</span>
                        @endif
                    @else
                        <span class="text-white font-medium">{{ $crumb['label'] }}</span>
                    @endif
                @endforeach
            </nav>
        @endif

        <h1 class="text-3xl md:text-5xl lg:text-6xl font-extrabold text-white mb-4">{{ $title }}</h1>

        @if($subtitle)
            <p class="text-lg md:text-xl text-white/80 max-w-2xl">{{ $subtitle }}</p>
        @endif
    </div>
</section>

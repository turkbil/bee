{{--
    Colored Subheader Component - Renkli gradient arka plan

    Usage:
    @include('components.subheaders.colored', [
        'title' => 'Page Title',
        'icon' => 'fa-solid fa-blog',
        'gradient' => 'from-green-500 to-teal-600',
        'breadcrumbs' => [...]
    ])
--}}

@php
    $breadcrumbs = $breadcrumbs ?? [];
    $icon = $icon ?? null;
    $gradient = $gradient ?? 'from-primary-500 to-indigo-600';
@endphp

<section class="bg-gradient-to-r {{ $gradient }}">
    <div class="container mx-auto px-4 py-10">
        <div class="flex items-center gap-6">
            @if($icon)
                <div class="w-16 h-16 bg-white/20 backdrop-blur rounded-xl flex items-center justify-center">
                    <i class="{{ $icon }} text-3xl text-white"></i>
                </div>
            @endif

            <div>
                @if(!empty($breadcrumbs))
                    <nav class="text-sm text-white/70 mb-2">
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

                <h1 class="text-2xl md:text-4xl font-bold text-white">{{ $title }}</h1>
            </div>
        </div>
    </div>
</section>

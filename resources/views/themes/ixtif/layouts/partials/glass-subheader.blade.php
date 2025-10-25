{{--
    Glass Subheader Component - Reusable across all modules

    Usage:
    @include('themes.ixtif.layouts.partials.glass-subheader', [
        'title' => 'Page Title',
        'icon' => 'fa-solid fa-store',           // Optional
        'iconGradient' => 'from-blue-500 to-purple-600',  // Optional, default: blue-purple
        'breadcrumbs' => [                       // Optional
            ['label' => 'Ana Sayfa', 'url' => '/', 'icon' => 'fa-home'],
            ['label' => 'Ürünler', 'url' => '/shop'],
            ['label' => 'Current Page'] // Last item without URL
        ],
        'rightSlot' => null // Optional: Blade content for right side (e.g., Sort + View toggle)
    ])
--}}

@php
    $iconGradient = $iconGradient ?? 'from-blue-500 to-purple-600';
    $icon = $icon ?? null;
    $breadcrumbs = $breadcrumbs ?? [];
    $rightSlot = $rightSlot ?? null;
@endphp

<section class="bg-white/70 dark:bg-white/5 backdrop-blur-md border-y border-white/20 dark:border-white/10">
    <div class="container mx-auto py-6">
        <div class="grid {{ $rightSlot ? 'lg:grid-cols-[1fr_400px]' : 'lg:grid-cols-1' }} gap-8 items-stretch">
            <!-- Left: Title & Breadcrumb -->
            <div class="flex flex-col justify-between">
                <div class="flex items-center gap-6">
                    @if($icon)
                        <div class="w-24 h-24 bg-gradient-to-br {{ $iconGradient }} rounded-2xl flex items-center justify-center shadow-xl">
                            <i class="{{ $icon }} text-5xl text-white"></i>
                        </div>
                    @endif
                    <div>
                        <h1 class="text-4xl md:text-5xl font-bold text-gray-900 dark:text-white mb-3">{{ $title }}</h1>

                        @if(!empty($breadcrumbs))
                            <!-- Breadcrumb -->
                            <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                                @foreach($breadcrumbs as $index => $crumb)
                                    @if(isset($crumb['url']))
                                        <a href="{{ $crumb['url'] }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition flex items-center gap-1.5">
                                            @if(isset($crumb['icon']))
                                                <i class="{{ $crumb['icon'] }} text-xs"></i>
                                            @endif
                                            <span>{{ $crumb['label'] }}</span>
                                        </a>
                                        @if($index < count($breadcrumbs) - 1)
                                            <i class="fa-solid fa-chevron-right text-xs opacity-60"></i>
                                        @endif
                                    @else
                                        <span class="font-semibold text-gray-900 dark:text-white">{{ $crumb['label'] }}</span>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right: Optional Slot (e.g., Sort + View Toggle for Shop) -->
            @if($rightSlot)
                <div class="flex flex-col justify-end">
                    {!! $rightSlot !!}
                </div>
            @endif
        </div>
    </div>
</section>

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
    <div class="ixtif-subheader-container">
        <div class="grid {{ $rightSlot ? 'lg:grid-cols-[1fr_400px]' : 'lg:grid-cols-1' }} gap-8 items-stretch">
            <!-- Left: Title & Breadcrumb -->
            <div class="flex flex-col justify-between gap-4">
                <div class="flex items-center gap-6">
                    @if($icon)
                        <div class="w-24 h-24 bg-gradient-to-br {{ $iconGradient }} rounded-2xl flex items-center justify-center shadow-xl flex-shrink-0">
                            <i class="{{ $icon }} text-5xl text-white"></i>
                        </div>
                    @endif
                    <div class="min-w-0 flex-1">
                        <h1 class="text-2xl md:text-4xl lg:text-5xl font-extrabold text-gray-900 dark:text-white">{{ $title }}</h1>

                        @if(!empty($breadcrumbs))
                            <!-- Breadcrumb - Desktop (Title altında) -->
                            <div class="hidden lg:flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 mt-3">
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

                @if(!empty($breadcrumbs))
                    <!-- Breadcrumb - Mobile (İkon altında, kaydırılabilir) -->
                    <div class="flex lg:hidden items-center gap-2 text-sm text-gray-600 dark:text-gray-400 overflow-x-auto pb-2 scrollbar-hide max-w-sm sm:max-w-lg md:max-w-2xl" style="-webkit-overflow-scrolling: touch;">
                        @foreach($breadcrumbs as $index => $crumb)
                            @if(isset($crumb['url']))
                                <a href="{{ $crumb['url'] }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition flex items-center gap-1.5 whitespace-nowrap flex-shrink-0">
                                    @if(isset($crumb['icon']))
                                        <i class="{{ $crumb['icon'] }} text-xs"></i>
                                    @endif
                                    <span>{{ $crumb['label'] }}</span>
                                </a>
                                @if($index < count($breadcrumbs) - 1)
                                    <i class="fa-solid fa-chevron-right text-xs opacity-60 flex-shrink-0"></i>
                                @endif
                            @else
                                <span class="font-semibold text-gray-900 dark:text-white whitespace-nowrap flex-shrink-0">{{ $crumb['label'] }}</span>
                            @endif
                        @endforeach
                    </div>
                @endif
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

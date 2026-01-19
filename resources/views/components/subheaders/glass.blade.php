{{--
    Glass Subheader Component - Modern cam efektli tasarım

    Usage:
    @include('components.subheaders.glass', [
        'title' => 'Page Title',
        'icon' => 'fa-solid fa-store',
        'iconGradient' => 'from-primary-500 to-purple-600',
        'breadcrumbs' => [
            ['label' => 'Ana Sayfa', 'url' => '/', 'icon' => 'fa-home'],
            ['label' => 'Ürünler', 'url' => '/shop'],
            ['label' => 'Current Page']
        ],
        'rightSlot' => null
    ])
--}}

@php
    $iconGradient = $iconGradient ?? 'from-primary-500 to-primary-600';
    $icon = $icon ?? null;
    $breadcrumbs = $breadcrumbs ?? [];
    $rightSlot = $rightSlot ?? null;
@endphp

<section class="bg-white/70 dark:bg-white/5 backdrop-blur-md border-y border-white/20 dark:border-white/10">
    <div class="container mx-auto px-4 py-8">
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
                            <!-- Breadcrumb - Desktop -->
                            <div class="hidden lg:flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 mt-3">
                                @foreach($breadcrumbs as $index => $crumb)
                                    @if(isset($crumb['url']))
                                        <a href="{{ $crumb['url'] }}" class="hover:text-primary-600 dark:hover:text-primary-400 transition flex items-center gap-1.5">
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
                    <!-- Breadcrumb - Mobile -->
                    <div class="flex lg:hidden items-center gap-2 text-sm text-gray-600 dark:text-gray-400 overflow-x-auto pb-2 scrollbar-hide">
                        @foreach($breadcrumbs as $index => $crumb)
                            @if(isset($crumb['url']))
                                <a href="{{ $crumb['url'] }}" class="hover:text-primary-600 dark:hover:text-primary-400 transition flex items-center gap-1.5 whitespace-nowrap flex-shrink-0">
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

            <!-- Right: Optional Slot -->
            @if($rightSlot)
                <div class="flex flex-col justify-end">
                    {!! $rightSlot !!}
                </div>
            @endif
        </div>
    </div>
</section>

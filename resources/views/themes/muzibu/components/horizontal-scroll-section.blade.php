@props([
    'title' => '',
    'viewAllUrl' => null,
    'viewAllText' => null,
    'icon' => null,
    'class' => ''
])

<section class="mb-8 {{ $class }}">
    @if($title)
        <div class="flex items-center justify-between mb-4 px-4 lg:px-0">
            <h2 class="text-xl font-bold text-white flex items-center gap-2">
                @if($icon)
                    <i class="{{ $icon }} text-muzibu-coral"></i>
                @endif
                {{ $title }}
            </h2>
            @if($viewAllUrl)
                <a href="{{ $viewAllUrl }}" class="text-sm text-gray-400 hover:text-white transition flex items-center gap-1" data-spa>
                    {{ $viewAllText ?? __('muzibu::front.general.view_all') }}
                    <i class="fas fa-chevron-right text-xs"></i>
                </a>
            @endif
        </div>
    @endif

    <div class="relative">
        {{-- Gradient Fade Left --}}
        <div class="absolute left-0 top-0 bottom-0 w-8 bg-gradient-to-r from-muzibu-dark to-transparent z-10 pointer-events-none lg:hidden"></div>

        {{-- Scroll Container --}}
        <div class="flex gap-4 overflow-x-auto scrollbar-hide px-4 lg:px-0 pb-2 snap-x snap-mandatory">
            {{ $slot }}
        </div>

        {{-- Gradient Fade Right --}}
        <div class="absolute right-0 top-0 bottom-0 w-8 bg-gradient-to-l from-muzibu-dark to-transparent z-10 pointer-events-none lg:hidden"></div>
    </div>
</section>

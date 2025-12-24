@props([
    'sector',
    'size' => 'normal'
])

@php
    $cover = $sector->coverMedia ?? null;
    $coverUrl = $cover ? thumb($cover, 300, 300) : '/images/default-sector.png';
    $sectorUrl = '/sectors/' . ($sector->slug ?? $sector->sector_id ?? $sector->id);
    $playlistsCount = $sector->playlists_count ?? 0;
    $radiosCount = $sector->radios_count ?? 0;

    // Sektör ikonları
    $icons = [
        'restaurant' => 'fas fa-utensils',
        'cafe' => 'fas fa-coffee',
        'hotel' => 'fas fa-hotel',
        'gym' => 'fas fa-dumbbell',
        'spa' => 'fas fa-spa',
        'retail' => 'fas fa-shopping-bag',
        'office' => 'fas fa-building',
        'default' => 'fas fa-music'
    ];
    $icon = $icons[$sector->icon ?? 'default'] ?? $icons['default'];

    $sizeClasses = [
        'small' => 'w-36 sm:w-40',
        'normal' => 'w-44 sm:w-48',
        'large' => 'w-52 sm:w-60'
    ];
    $cardSize = $sizeClasses[$size] ?? $sizeClasses['normal'];
@endphp

<div class="group flex-shrink-0 {{ $cardSize }} snap-start">
    <a href="{{ $sectorUrl }}" class="block" data-spa>
        {{-- Cover --}}
        <div class="relative aspect-[4/3] rounded-xl overflow-hidden mb-3 bg-white/5">
            @if($cover)
                <img src="{{ $coverUrl }}" alt="{{ $sector->title }}"
                     class="w-full h-full object-cover transition group-hover:scale-105"
                     loading="lazy">
            @else
                <div class="w-full h-full bg-gradient-to-br from-slate-700 to-slate-800 flex items-center justify-center">
                    <i class="{{ $icon }} text-white/20 text-5xl"></i>
                </div>
            @endif

            {{-- Overlay --}}
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>

            {{-- Content --}}
            <div class="absolute bottom-0 left-0 right-0 p-4">
                <div class="flex items-center gap-2 mb-2">
                    <i class="{{ $icon }} text-green-400"></i>
                    <h3 class="text-white font-bold">{{ $sector->title }}</h3>
                </div>
                <div class="flex items-center gap-3 text-xs text-gray-300">
                    @if($playlistsCount > 0)
                        <span><i class="fas fa-list mr-1"></i>{{ $playlistsCount }} playlist</span>
                    @endif
                    @if($radiosCount > 0)
                        <span><i class="fas fa-broadcast-tower mr-1"></i>{{ $radiosCount }} radyo</span>
                    @endif
                </div>
            </div>

            {{-- Hover Arrow --}}
            <div class="absolute top-3 right-3 w-8 h-8 bg-white/10 backdrop-blur rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition">
                <i class="fas fa-arrow-right text-white text-sm"></i>
            </div>
        </div>
    </a>
</div>

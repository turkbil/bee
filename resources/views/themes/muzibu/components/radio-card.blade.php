@props([
    'radio',
    'size' => 'normal'
])

@php
    $cover = $radio->coverMedia ?? null;
    $coverUrl = $cover ? thumb($cover, 300, 300) : '/images/default-radio.png';
    $radioId = $radio->radio_id ?? $radio->id;
    $songsCount = $radio->songs_count ?? 0;
    $isLive = $radio->is_live ?? false;
    $isFavorite = auth()->check() && method_exists($radio, 'isFavoritedBy') && $radio->isFavoritedBy(auth()->id());

    $sizeClasses = [
        'small' => 'w-32 sm:w-36',
        'normal' => 'w-40 sm:w-44',
        'large' => 'w-48 sm:w-56'
    ];
    $cardSize = $sizeClasses[$size] ?? $sizeClasses['normal'];
@endphp

<div class="group flex-shrink-0 {{ $cardSize }} snap-start"
     x-on:contextmenu.prevent.stop="$store.contextMenu.openContextMenu($event, 'radio', {
         id: {{ $radioId }},
         title: '{{ addslashes($radio->title) }}',
         is_favorite: {{ $isFavorite ? 'true' : 'false' }}
     })"
     x-data="{ touchTimer: null, touchStartPos: { x: 0, y: 0 } }"
     x-on:touchstart="touchStartPos = { x: $event.touches[0].clientX, y: $event.touches[0].clientY }; touchTimer = setTimeout(() => { if (navigator.vibrate) navigator.vibrate(50); $store.contextMenu.openContextMenu({ clientX: $event.touches[0].clientX, clientY: $event.touches[0].clientY }, 'radio', { id: {{ $radioId }}, title: '{{ addslashes($radio->title) }}', is_favorite: {{ $isFavorite ? 'true' : 'false' }} }); }, 500);"
     x-on:touchend="clearTimeout(touchTimer)"
     x-on:touchmove="if (Math.abs($event.touches[0].clientX - touchStartPos.x) > 10 || Math.abs($event.touches[0].clientY - touchStartPos.y) > 10) clearTimeout(touchTimer)">
    <div class="cursor-pointer" @click="playRadio({{ $radioId }})">
        {{-- Cover --}}
        <div class="relative aspect-square rounded-xl overflow-hidden mb-3 bg-white/5">
            <img src="{{ $coverUrl }}" alt="{{ $radio->title }}"
                 class="w-full h-full object-cover transition group-hover:scale-105"
                 loading="lazy">

            {{-- Live Badge --}}
            @if($isLive)
                <div class="absolute top-2 left-2 flex items-center gap-1 px-2 py-1 bg-red-500 rounded text-xs text-white font-semibold">
                    <span class="w-2 h-2 bg-white rounded-full animate-pulse"></span>
                    CANLI
                </div>
            @endif

            {{-- Play Button Overlay --}}
            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                <div class="w-14 h-14 bg-green-500 hover:bg-green-400 rounded-full flex items-center justify-center shadow-lg transform hover:scale-110 transition">
                    <i class="fas fa-broadcast-tower text-black text-xl"></i>
                </div>
            </div>

            {{-- Equalizer Animation --}}
            <div class="absolute bottom-2 right-2 flex items-end gap-0.5 h-4">
                <span class="w-1 bg-green-400 rounded-full animate-pulse" style="height: 40%"></span>
                <span class="w-1 bg-green-400 rounded-full animate-pulse" style="height: 80%; animation-delay: 0.1s"></span>
                <span class="w-1 bg-green-400 rounded-full animate-pulse" style="height: 60%; animation-delay: 0.2s"></span>
                <span class="w-1 bg-green-400 rounded-full animate-pulse" style="height: 100%; animation-delay: 0.3s"></span>
            </div>
        </div>

        {{-- Info --}}
        <div class="px-1">
            <h3 class="text-white font-medium text-sm truncate group-hover:text-green-400 transition">
                {{ $radio->title }}
            </h3>
            <p class="text-gray-400 text-xs mt-1 flex items-center gap-2">
                <i class="fas fa-broadcast-tower"></i>
                {{ __('muzibu::front.general.radio') }}
                @if($songsCount > 0)
                    <span class="text-gray-600">•</span>
                    {{ $songsCount }} {{ __('muzibu::front.general.songs') }}
                @endif
            </p>
        </div>
    </div>
</div>

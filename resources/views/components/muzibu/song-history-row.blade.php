@props([
    'play',
    'index' => 0
])

@php
    $song = $play->song;
    if (!$song) return;

    // Tarih formatla
    $playedAt = \Carbon\Carbon::parse($play->created_at);
    $dateTimeStr = $playedAt->format('d.m.Y H:i');

    // IP adresini maskele
    $ipAddress = $play->ip_address ?? null;
    if ($ipAddress) {
        $parts = explode('.', $ipAddress);
        if (count($parts) === 4) {
            $ipAddress = $parts[0] . '.*.*.' . $parts[3];
        }
    }

    // Cihaz tipi
    $deviceType = $play->device_type ?? null;
    $deviceIcon = match($deviceType) {
        'mobile' => 'fa-mobile-alt',
        'tablet' => 'fa-tablet-alt',
        'desktop' => 'fa-desktop',
        default => null
    };

    // Tarayıcı ve OS (user_agent'tan)
    $browser = null;
    $browserIcon = null;
    $os = null;
    $osIcon = null;
    $ua = $play->user_agent ?? '';

    // Tarayıcı
    if (str_contains($ua, 'Edg/')) { $browser = 'Edge'; $browserIcon = 'fab fa-edge'; }
    elseif (str_contains($ua, 'OPR/') || str_contains($ua, 'Opera')) { $browser = 'Opera'; $browserIcon = 'fab fa-opera'; }
    elseif (str_contains($ua, 'Chrome/')) { $browser = 'Chrome'; $browserIcon = 'fab fa-chrome'; }
    elseif (str_contains($ua, 'Safari/') && !str_contains($ua, 'Chrome')) { $browser = 'Safari'; $browserIcon = 'fab fa-safari'; }
    elseif (str_contains($ua, 'Firefox/')) { $browser = 'Firefox'; $browserIcon = 'fab fa-firefox'; }

    // İşletim Sistemi
    if (str_contains($ua, 'Windows')) { $os = 'Win'; $osIcon = 'fab fa-windows'; }
    elseif (str_contains($ua, 'Macintosh') || str_contains($ua, 'Mac OS')) { $os = 'Mac'; $osIcon = 'fab fa-apple'; }
    elseif (str_contains($ua, 'Android')) { $os = 'Android'; $osIcon = 'fab fa-android'; }
    elseif (str_contains($ua, 'iPhone') || str_contains($ua, 'iPad')) { $os = 'iOS'; $osIcon = 'fab fa-apple'; }
    elseif (str_contains($ua, 'Linux')) { $os = 'Linux'; $osIcon = 'fab fa-linux'; }
@endphp

{{-- LISTENING HISTORY ROW --}}
<div class="group flex items-center gap-3 px-4 py-2 hover:bg-white/5 cursor-pointer transition-all"
     @click="$dispatch('play-song', { songId: {{ $song->song_id }} })"
     x-on:contextmenu.prevent.stop="$store.contextMenu.openContextMenu($event, 'song', {
         id: {{ $song->song_id }},
         title: '{{ addslashes($song->getTranslation('title', app()->getLocale())) }}',
         artist: '{{ $song->artist ? addslashes($song->artist->getTranslation('title', app()->getLocale())) : '' }}',
         album_id: {{ $song->album_id ?? 'null' }},
         is_favorite: {{ is_favorited('song', $song->song_id) ? 'true' : 'false' }}
     })">

    {{-- Thumbnail --}}
    @php $coverUrl = $song->getCoverUrl(120, 120); @endphp
    <div class="relative w-12 h-12 flex-shrink-0 rounded-lg overflow-hidden bg-gradient-to-br from-muzibu-coral to-orange-600">
        @if($coverUrl)
            <img src="{{ $coverUrl }}" alt="{{ $song->getTranslation('title', app()->getLocale()) }}" class="w-full h-full object-cover" loading="lazy">
        @else
            <div class="w-full h-full flex items-center justify-center">
                <i class="fas fa-music text-white/50 text-xs"></i>
            </div>
        @endif
        <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity">
            <i class="fas fa-play text-white text-sm"></i>
        </div>
    </div>

    {{-- Song Info --}}
    <div class="flex-1 min-w-0">
        <h4 class="text-white text-sm font-medium truncate group-hover:text-muzibu-coral transition-colors">
            {{ $song->getTranslation('title', app()->getLocale()) }}
        </h4>
        <p class="text-gray-400 text-xs truncate">
            {{ $song->artist ? $song->artist->getTranslation('title', app()->getLocale()) : '' }}
        </p>
    </div>

    {{-- Date/IP Info --}}
    <div class="text-right flex-shrink-0">
        <div class="text-gray-300 text-[11px]">{{ $dateTimeStr }}</div>
        @if($ipAddress || $deviceIcon || $os || $browser)
            <div class="text-gray-500 text-[10px] flex items-center justify-end gap-1">
                @if($deviceIcon)<i class="fas {{ $deviceIcon }}"></i>@endif
                @if($os)<span>{{ $os }}</span>@endif
                @if($browser)<span class="text-gray-600">·</span><span>{{ $browser }}</span>@endif
                @if($ipAddress)<span class="text-gray-600">·</span><span class="font-mono">{{ $ipAddress }}</span>@endif
            </div>
        @endif
    </div>

    {{-- Actions - Show on Hover --}}
    <div class="flex items-center gap-1 flex-shrink-0 ml-3">
        {{-- Heart --}}
        <button @click.stop.prevent="$store.favorites.toggle('song', {{ $song->song_id }})"
                class="w-7 h-7 flex items-center justify-center rounded-full hover:bg-white/10 transition-all"
                x-bind:class="$store.favorites.isFavorite('song', {{ $song->song_id }}) ? 'text-muzibu-coral opacity-100' : 'text-gray-400 opacity-0 group-hover:opacity-100'">
            <i class="text-xs" x-bind:class="$store.favorites.isFavorite('song', {{ $song->song_id }}) ? 'fas fa-heart' : 'far fa-heart'"></i>
        </button>

        {{-- 3-dot --}}
        <button @click.stop.prevent="$store.contextMenu.openContextMenu($event, 'song', {
                    id: {{ $song->song_id }},
                    title: '{{ addslashes($song->getTranslation('title', app()->getLocale())) }}',
                    artist: '{{ $song->artist ? addslashes($song->artist->getTranslation('title', app()->getLocale())) : '' }}',
                    album_id: {{ $song->album_id ?? 'null' }},
                    is_favorite: {{ is_favorited('song', $song->song_id) ? 'true' : 'false' }}
                })"
                class="w-7 h-7 flex items-center justify-center rounded-full text-gray-400 hover:text-white hover:bg-white/10 transition-colors opacity-0 group-hover:opacity-100">
            <i class="fas fa-ellipsis-v text-xs"></i>
        </button>
    </div>
</div>

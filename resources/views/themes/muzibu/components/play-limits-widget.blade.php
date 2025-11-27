{{--
    Muzibu Play Limits - Sidebar Widget (Minimal)
    Küçük, modern, Spotify-style widget

    Normal Üye: 3 şarkı/gün limit göstergesi
    Premium/Trial: Widget gösterilmez
--}}

<div x-data="playLimits">

    {{-- Sadece Normal Member için göster (Premium/Trial için gizle) --}}
    <template x-if="remainingPlays >= 0 && remainingPlays < 999">
        <div class="play-limits-widget-mini">
            {{-- İçerik tek satır: ikon + bar + sayı --}}
            <div class="play-limits-mini-content">
                <i class="fas fa-music text-spotify-green text-xs"></i>

                {{-- Progress Bar --}}
                <div class="play-limits-mini-bar">
                    <div
                        class="play-limits-mini-fill"
                        :class="{
                            'bg-yellow-500': remainingPlays === 1,
                            'bg-red-500': remainingPlays === 0,
                            'bg-spotify-green': remainingPlays > 1
                        }"
                        :style="`width: ${((3 - remainingPlays) / 3) * 100}%`"
                    ></div>
                </div>

                {{-- Sayı --}}
                <span class="play-limits-mini-count"
                      :class="{
                          'text-yellow-400': remainingPlays === 1,
                          'text-red-400': remainingPlays === 0,
                          'text-gray-400': remainingPlays > 1
                      }">
                    <span x-text="(3 - remainingPlays)"></span>/3
                </span>
            </div>
        </div>
    </template>

</div>

{{-- Alpine.js cloaking --}}
<style>
    [x-cloak] { display: none !important; }
</style>

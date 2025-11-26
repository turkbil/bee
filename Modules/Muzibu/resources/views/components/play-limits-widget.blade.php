{{--
    Muzibu Play Limits - Sidebar Widget
    Tema-bağımsız kalan hak göstergesi

    Üye: 3/5 şarkı + progress bar
    Premium: ♾️ Sınırsız rozeti
--}}

<div x-data="playLimits">

    {{-- Normal Member Widget --}}
    <template x-if="remainingPlays >= 0 && remainingPlays < 999">
        <div class="play-limits-widget">
            {{-- Header --}}
            <div class="play-limits-info">
                <span class="play-limits-label">
                    <i class="fas fa-headphones"></i>
                    Günlük Dinleme
                </span>
                <span class="play-limits-count" :class="{'updating': remainingPlays !== $el.textContent}">
                    <span x-text="(5 - remainingPlays)"></span>/5
                </span>
            </div>

            {{-- Progress Bar --}}
            <div class="play-limits-bar">
                <div
                    class="play-limits-fill"
                    :class="{
                        'low': remainingPlays <= 2 && remainingPlays > 0,
                        'critical': remainingPlays === 0
                    }"
                    :style="`width: ${((5 - remainingPlays) / 5) * 100}%`"
                ></div>
            </div>

            {{-- Footer Text --}}
            <p class="play-limits-label" style="font-size: 0.8rem; margin-top: 8px; text-align: center;">
                <template x-if="remainingPlays > 0">
                    <span>
                        <span x-text="remainingPlays"></span> şarkı hakkın kaldı
                    </span>
                </template>
                <template x-if="remainingPlays === 0">
                    <span style="color: #ef4444;">⛔ Günlük limit doldu</span>
                </template>
            </p>
        </div>
    </template>

    {{-- Premium/Trial Widget --}}
    <template x-if="remainingPlays === -1 || remainingPlays >= 999">
        <div class="play-limits-premium-badge">
            <span>
                <i class="fas fa-crown"></i>
                ♾️ Sınırsız Dinleme
            </span>
        </div>
    </template>

</div>

{{-- Alpine.js cloaking --}}
<style>
    [x-cloak] { display: none !important; }
</style>

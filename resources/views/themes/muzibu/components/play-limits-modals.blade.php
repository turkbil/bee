{{--
    Muzibu Play Limits - Modals
    Tema-bağımsız modal componentleri

    Guest Modal: 30 saniye preview bittiğinde
    Limit Modal: Günlük 3 şarkı limiti dolduğunda
--}}

<div x-data="playLimits">

    {{-- Guest Preview Ended Modal --}}
    <template x-teleport="body">
        <div x-show="showGuestModal"
             x-cloak
             @click.self="showGuestModal = false"
             class="play-limits-overlay"
             style="display: none;">

            <div class="play-limits-modal">
                {{-- Icon --}}
                <div class="play-limits-icon guest">
                    <i class="fas fa-clock"></i>
                </div>

                {{-- Content --}}
                <h3>Müziğin Tadını Çıkarın</h3>
                <p>Sınırsız dinleme ve keşif için kayıt olun.</p>
                <p class="small">✨ Ücretsiz deneme üyeliğiyle başlayın.</p>

                {{-- Buttons --}}
                <div class="play-limits-buttons">
                    <button @click="handleGuestRegister()" class="play-limits-btn primary">
                        <i class="fas fa-rocket"></i>
                        <span>Ücretsiz Kayıt Ol</span>
                    </button>
                    <button @click="handleGuestLogin()" class="play-limits-btn secondary">
                        <span>Zaten Hesabım Var</span>
                    </button>
                </div>
            </div>
        </div>
    </template>

    {{-- Member Limit Exceeded Modal --}}
    <template x-teleport="body">
        <div x-show="showLimitModal"
             x-cloak
             @click.self="showLimitModal = false"
             class="play-limits-overlay"
             style="display: none;">

            <div class="play-limits-modal">
                {{-- Icon --}}
                <div class="play-limits-icon limit">
                    <i class="fas fa-ban"></i>
                </div>

                {{-- Content --}}
                <h3>Günlük Limit Doldu</h3>
                <p>Bugün <strong>3 şarkı</strong> hakkınızı kullandınız.</p>
                <p class="small">⏰ Yarın saat 00:00'da yeni 3 hakkınız olacak.</p>

                {{-- Buttons --}}
                <div class="play-limits-buttons">
                    <button @click="showLimitModal = false; window.location.href='/pricing'" class="play-limits-btn primary">
                        <i class="fas fa-crown"></i>
                        <span>Premium'a Geç (Sınırsız)</span>
                    </button>
                    <button @click="showLimitModal = false" class="play-limits-btn secondary">
                        <span>Tamam</span>
                    </button>
                </div>
            </div>
        </div>
    </template>

</div>

{{-- Alpine.js cloaking --}}
<style>
    [x-cloak] { display: none !important; }
</style>

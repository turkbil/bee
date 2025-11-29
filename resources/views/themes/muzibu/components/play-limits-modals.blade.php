{{--
    Muzibu Play Limits - Modals (SPOTIFY STYLE)
    Tema-bağımsız modal componentleri

    Guest Modal: 30 saniye preview bittiğinde
    Limit Modal: Günlük 3 şarkı limiti dolduğunda
--}}

<div x-data="playLimits()">

    {{-- Guest Preview Ended Modal --}}
    <template x-teleport="body">
        <div
            x-show="showGuestModal"
            x-cloak
            @keydown.escape.window="showGuestModal = false"
            class="fixed inset-0 z-[9999] flex items-center justify-center p-4"
        >
            {{-- Backdrop --}}
            <div
                x-show="showGuestModal"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="absolute inset-0 bg-black/90 backdrop-blur-md"
            ></div>

            {{-- Modal --}}
            <div
                x-show="showGuestModal"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95 -translate-y-4"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 -translate-y-4"
                @click.stop
                class="relative w-full max-w-md bg-gradient-to-br from-zinc-900 to-black rounded-2xl shadow-2xl border border-white/10 p-8 text-center"
            >
                {{-- Close Button --}}
                <button
                    @click="showGuestModal = false"
                    class="absolute top-4 right-4 w-10 h-10 flex items-center justify-center text-white/60 hover:text-white hover:bg-white/10 rounded-full transition-all duration-300"
                >
                    <i class="fas fa-times text-xl"></i>
                </button>

                {{-- Icon --}}
                <div class="w-20 h-20 mx-auto mb-6 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center shadow-lg">
                    <i class="fas fa-clock text-3xl text-white"></i>
                </div>

                {{-- Content --}}
                <h3 class="text-2xl font-bold text-white mb-3">Müziğin Tadını Çıkarın</h3>
                <p class="text-zinc-300 mb-2">Sınırsız dinleme ve keşif için kayıt olun.</p>
                <p class="text-sm text-muzibu-coral">✨ Ücretsiz deneme üyeliğiyle başlayın.</p>

                {{-- Buttons --}}
                <div class="flex flex-col gap-3 mt-6">
                    <button
                        @click="handleGuestRegister()"
                        class="w-full py-3 px-6 bg-gradient-to-r from-muzibu-coral to-muzibu-coral-light hover:from-muzibu-coral-light hover:to-muzibu-coral rounded-full text-white font-bold text-sm transition-all duration-300 shadow-lg hover:shadow-muzibu-coral/50 flex items-center justify-center gap-2"
                    >
                        <i class="fas fa-rocket"></i>
                        <span>Ücretsiz Kayıt Ol</span>
                    </button>
                    <button
                        @click="handleGuestLogin()"
                        class="w-full py-3 px-6 bg-white/5 hover:bg-white/10 rounded-full text-white font-semibold text-sm transition-all duration-300"
                    >
                        Zaten Hesabım Var
                    </button>
                </div>
            </div>
        </div>
    </template>

    {{-- ⚠️ REMOVED: Member Limit Exceeded Modal (3/3 rule removed) --}}
    {{-- This modal is no longer needed as daily 3 song limit is disabled --}}
    {{-- Only 30-second preview system is active now --}}

</div>

{{-- Alpine.js cloaking --}}
<style>
    [x-cloak] { display: none !important; }
</style>

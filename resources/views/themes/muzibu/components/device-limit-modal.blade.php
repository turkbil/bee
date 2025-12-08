{{-- 🔐 DEVICE LIMIT EXCEEDED MODAL --}}
{{-- Modal backdrop - x-teleport ile body seviyesinde render --}}
<template x-teleport="body">
    <div
        x-show="showDeviceLimitModal"
        x-cloak
        class="fixed inset-0 z-[9999] flex items-center justify-center p-4 animate-fade-in"
    >
        {{-- Backdrop with blur --}}
        <div
            x-show="showDeviceLimitModal"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="absolute inset-0 bg-black/90 backdrop-blur-md"
        ></div>

        {{-- Modal Container --}}
        <div
            x-show="showDeviceLimitModal"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95 -translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 -translate-y-4"
            @click.stop
            class="relative w-full max-w-md bg-gradient-to-br from-zinc-900 to-black rounded-2xl shadow-2xl border border-red-500/30 overflow-hidden"
        >
            {{-- Icon & Message --}}
            <div class="text-center p-8">
                {{-- Warning Icon --}}
                <div class="mb-6">
                    <div class="w-20 h-20 mx-auto bg-red-500/10 rounded-full flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-4xl text-red-500"></i>
                    </div>
                </div>

                {{-- Title --}}
                <h2 class="text-2xl font-bold text-white mb-3">
                    Cihaz Limiti Aşıldı
                </h2>

                {{-- Message --}}
                <p class="text-zinc-400 text-base leading-relaxed mb-6">
                    Başka bir cihazdan giriş yapıldığı için bu cihazdan çıkış yapılıyor.
                </p>

                {{-- Auto-logout countdown --}}
                <div class="bg-zinc-800/50 rounded-lg p-4 border border-white/5">
                    <p class="text-sm text-zinc-500">
                        <i class="fas fa-clock mr-2"></i>
                        3 saniye içinde çıkış yapılacak...
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

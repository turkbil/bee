{{-- 🔄 SPA Loading Overlay - Ekran Ortasında --}}
<div
    x-show="isLoading"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0 scale-90"
    x-transition:enter-end="opacity-100 scale-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100 scale-100"
    x-transition:leave-end="opacity-0 scale-90"
    class="fixed z-[60] left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 bg-black/90 backdrop-blur-md border-2 border-muzibu-coral/40 rounded-xl shadow-2xl shadow-muzibu-coral/20"
    x-cloak
    id="loading-overlay"
>
    {{-- Loading Content --}}
    <div class="flex items-center gap-3 px-5 py-3.5">
        {{-- Spinner --}}
        <div class="relative w-6 h-6 flex-shrink-0">
            <div class="absolute inset-0 border-2 border-transparent border-t-muzibu-coral rounded-full animate-spin"></div>
            <div class="absolute inset-1 border-2 border-transparent border-t-muzibu-coral-light rounded-full animate-spin" style="animation-duration: 0.7s; animation-direction: reverse;"></div>
        </div>

        {{-- Loading Text --}}
        <span class="text-white text-sm font-semibold">Yükleniyor</span>
    </div>
</div>

{{-- 🔄 SPA Loading Overlay - Full Screen --}}
<div
    x-show="isLoading"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/20 backdrop-blur-sm"
    x-cloak
>
    {{-- Loading Content --}}
    <div class="text-center px-6 py-8 bg-slate-900/90 rounded-2xl shadow-2xl">
        {{-- Spinner --}}
        <div class="relative w-20 h-20 mx-auto mb-4">
            <div class="absolute inset-0 border-4 border-muzibu-coral/20 rounded-full"></div>
            <div class="absolute inset-0 border-4 border-transparent border-t-muzibu-coral rounded-full animate-spin"></div>
            <div class="absolute inset-2 border-4 border-transparent border-t-muzibu-coral-light rounded-full animate-spin" style="animation-duration: 0.7s; animation-direction: reverse;"></div>
        </div>

        {{-- Loading Text --}}
        <p class="text-white text-lg font-semibold mb-1">Yükleniyor</p>
        <p class="text-slate-400 text-sm">Lütfen bekleyin...</p>
    </div>
</div>

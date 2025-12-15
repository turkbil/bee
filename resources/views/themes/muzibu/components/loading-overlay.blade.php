{{-- 🔄 SPA Loading Overlay - Main Content & Right Sidebar --}}
<div
    x-show="$store.player.isLoading"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-40 pointer-events-none"
    x-cloak
>
    {{-- Main Content Loading --}}
    <div class="absolute lg:left-[232px] left-3 right-3 2xl:right-[372px] xl:right-[352px] lg:right-3 top-[68px] bottom-[77px] bg-transparent backdrop-blur-sm rounded-lg flex items-center justify-center">
        <div class="text-center">
            {{-- Spinner --}}
            <div class="relative w-20 h-20 mx-auto mb-4">
                <div class="absolute inset-0 border-4 border-muzibu-coral/20 rounded-full"></div>
                <div class="absolute inset-0 border-4 border-transparent border-t-muzibu-coral rounded-full animate-spin"></div>
                <div class="absolute inset-2 border-4 border-transparent border-t-muzibu-coral-light rounded-full animate-spin" style="animation-duration: 0.7s; animation-direction: reverse;"></div>
            </div>
            
            {{-- Loading Text --}}
            <p class="text-white text-lg font-semibold mb-1">Yükleniyor</p>
            <p class="text-muzibu-text-gray text-sm">Lütfen bekleyin...</p>
        </div>
    </div>
</div>

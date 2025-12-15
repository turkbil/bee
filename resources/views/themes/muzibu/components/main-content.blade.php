{{-- MAIN CONTENT --}}
<main class="muzibu-main relative overflow-hidden">
    <div class="overflow-y-auto h-full relative">
        {{-- V3: Turuncu → Kırmızı → Bordo - Yatay Animasyonlu + Dark Altta --}}
        <div class="absolute top-0 left-0 right-0 h-[250px] rounded-t-2xl pointer-events-none overflow-hidden">
            {{-- Animated layer (Soldan sağa renk kayması) --}}
            <div class="absolute top-0 left-0 w-[200%] h-full animate-gradient-horizontal"></div>
            {{-- Dark overlay (Altta sabit) --}}
            <div class="absolute top-0 left-0 right-0 bottom-0 bg-gradient-to-b from-transparent via-black/50 to-[#121212]"></div>
        </div>

        {{-- Content (Gradient ile birlikte scroll yapar) --}}
        <div class="relative z-10">
            {{-- 🚀 SPA Loading Skeleton --}}
            <div x-show="$store.player.isLoading" x-cloak class="spa-loading-skeleton">
                @include('themes.muzibu.partials.loading-skeleton')
            </div>

            {{-- 🚀 SPA Content Wrapper --}}
            <div
                class="spa-content-wrapper"
                id="spaContent"
                x-show="!$store.player.isLoading"
            >
                {{ $slot ?? '' }}
                @yield('content')
            </div>
        </div>
    </div>
</main>

<style>
@keyframes gradient-horizontal {
    0% { transform: translateX(0%); }
    100% { transform: translateX(-50%); }
}

.animate-gradient-horizontal {
    background: linear-gradient(to right,
        #ff6b6b,
        #ff5252,
        #e91e63,
        #ff5252,
        #ff6b6b,
        #ff5252,
        #e91e63,
        #ff5252,
        #ff6b6b);
    animation: gradient-horizontal 8s linear infinite;
}
</style>

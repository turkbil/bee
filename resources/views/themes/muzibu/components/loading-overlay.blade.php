{{-- 🔄 SPA Loading Overlay - Logo Yanında Otomatik Hizalanmış --}}
<div
    x-show="isLoading"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0 scale-90"
    x-transition:enter-end="opacity-100 scale-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100 scale-100"
    x-transition:leave-end="opacity-0 scale-90"
    class="fixed z-[60] bg-black/90 backdrop-blur-md border-2 border-muzibu-coral/40 rounded-xl shadow-2xl shadow-muzibu-coral/20"
    x-cloak
    style="width: 200px;"
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

<script>
// 🎯 Loading Overlay - Logo ile otomatik hizalama
(function() {
    function alignLoadingWithLogo() {
        var overlay = document.getElementById('loading-overlay');
        if (!overlay) return;

        var logo = document.querySelector('header a[href="/"]') || document.querySelector('header a');
        if (logo) {
            var rect = logo.getBoundingClientRect();
            overlay.style.left = (rect.right + 12) + 'px';
            overlay.style.top = (rect.top + (rect.height / 2) - 20) + 'px';
        } else {
            overlay.style.left = '80px';
            overlay.style.top = '16px';
        }
    }

    // İlk yüklemede hizala
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', alignLoadingWithLogo);
    } else {
        alignLoadingWithLogo();
    }

    // Scroll/resize olduğunda güncelle
    window.addEventListener('scroll', alignLoadingWithLogo, { passive: true });
    window.addEventListener('resize', alignLoadingWithLogo, { passive: true });

    // Alpine store değiştiğinde güncelle
    document.addEventListener('alpine:initialized', function() {
        if (window.Alpine && Alpine.store('player')) {
            Alpine.effect(function() {
                if (Alpine.store('player').isLoading) {
                    setTimeout(alignLoadingWithLogo, 10);
                }
            });
        }
    });
})();
</script>

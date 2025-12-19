{{-- 🔄 SPA Loading Overlay - İçerik Alanı Ortasında --}}
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
// 🎯 Loading Overlay - İçerik alanı ortasında konumlandır
(function() {
    function centerLoadingInContent() {
        var overlay = document.getElementById('loading-overlay');
        if (!overlay) return;

        // Main content alanını bul
        var mainContent = document.querySelector('main') || document.querySelector('.muzibu-main-content');

        if (mainContent) {
            var rect = mainContent.getBoundingClientRect();
            // Overlay'i main content alanının tam ortasında konumlandır
            overlay.style.left = (rect.left + rect.width / 2 - overlay.offsetWidth / 2) + 'px';
            overlay.style.top = (rect.top + rect.height / 2 - overlay.offsetHeight / 2) + 'px';
        } else {
            // Fallback: Ekranın ortasında
            overlay.style.left = '50%';
            overlay.style.top = '50%';
            overlay.style.transform = 'translate(-50%, -50%)';
        }
    }

    // İlk yüklemede konumlandır
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', centerLoadingInContent);
    } else {
        centerLoadingInContent();
    }

    // Resize olduğunda güncelle
    window.addEventListener('resize', centerLoadingInContent, { passive: true });

    // Alpine store değiştiğinde güncelle
    document.addEventListener('alpine:initialized', function() {
        if (window.Alpine && Alpine.store('player')) {
            Alpine.effect(function() {
                if (Alpine.store('player').isLoading) {
                    setTimeout(centerLoadingInContent, 10);
                }
            });
        }
    });
})();
</script>

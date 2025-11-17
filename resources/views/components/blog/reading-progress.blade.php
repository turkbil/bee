@props(['target' => '.prose'])

<div x-data="readingProgress('{{ $target }}')" class="fixed top-0 left-0 w-full z-[9999]">
    <!-- Progress Bar -->
    <div class="h-1 bg-gray-200 dark:bg-gray-800">
        <div
            class="h-full bg-gradient-to-r from-blue-500 via-indigo-500 to-purple-600 transition-all duration-300 ease-out"
            :style="{ width: progress + '%' }"
        ></div>
    </div>

    <!-- Progress Info (Mobile) -->
    <div x-show="showInfo"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 transform -translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform -translate-y-2"
         class="absolute top-1 right-4 bg-white dark:bg-slate-900 px-3 py-1 rounded-b-lg shadow-lg border border-gray-200 dark:border-slate-700">
        <div class="flex items-center space-x-2 text-xs">
            <i class="fas fa-book-open text-blue-500"></i>
            <span class="text-gray-700 dark:text-gray-300" x-text="Math.round(progress) + '%'"></span>
            <span class="text-gray-500 dark:text-gray-400" x-text="remainingTime"></span>
        </div>
    </div>
</div>

<script>
function readingProgress(targetSelector = '.prose') {
    return {
        progress: 0,
        showInfo: false,
        remainingTime: '',
        totalReadingTime: 0,

        init() {
            this.calculateTotalReadingTime();
            this.setupScrollListener();

            // Mobile'da progress info göster
            if (window.innerWidth < 768) {
                this.showInfo = true;
            }
        },

        calculateTotalReadingTime() {
            const content = document.querySelector(targetSelector);
            if (content) {
                const text = content.textContent || content.innerText || '';
                const wordCount = text.trim().split(/\s+/).length;
                this.totalReadingTime = Math.ceil(wordCount / 200); // 200 kelime/dakika
            }
        },

        setupScrollListener() {
            const content = document.querySelector(targetSelector);
            if (!content) return;

            const updateProgress = () => {
                const contentTop = content.getBoundingClientRect().top + window.pageYOffset;
                const contentHeight = content.offsetHeight;
                const viewportHeight = window.innerHeight;
                const scrollTop = window.pageYOffset;

                // İçerik başlamadan önce
                if (scrollTop < contentTop) {
                    this.progress = 0;
                    this.updateRemainingTime();
                    return;
                }

                // İçeriğin ortasından sonra %100 olsun (jetpack yerine yavaş geçiş)
                const contentMiddle = contentTop + (contentHeight * 0.6); // İçeriğin %60'ında bitir

                if (scrollTop + viewportHeight >= contentMiddle) {
                    this.progress = 100;
                    this.remainingTime = 'Tamamlandı!';
                    return;
                }

                // Normal okuma durumu - daha yavaş artış
                const readableDistance = contentMiddle - contentTop;
                const scrollProgress = (scrollTop - contentTop) / readableDistance;
                this.progress = Math.max(0, Math.min(100, scrollProgress * 100));
                this.updateRemainingTime();
            };

            window.addEventListener('scroll', updateProgress, { passive: true });
            window.addEventListener('resize', updateProgress, { passive: true });

            // İlk değeri hesapla
            updateProgress();
        },

        updateRemainingTime() {
            if (this.totalReadingTime <= 0) {
                this.remainingTime = '';
                return;
            }

            const remainingPercent = (100 - this.progress) / 100;
            const remainingMinutes = Math.ceil(this.totalReadingTime * remainingPercent);

            if (remainingMinutes <= 0) {
                this.remainingTime = 'Tamamlandı!';
            } else if (remainingMinutes === 1) {
                this.remainingTime = '~1 dk kaldı';
            } else {
                this.remainingTime = `~${remainingMinutes} dk kaldı`;
            }
        }
    }
}
</script>

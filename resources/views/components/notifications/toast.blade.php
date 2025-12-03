{{-- Toast Notification Component - Alpine.js --}}
<div x-data="toastNotification()"
     @notify.window="show($event.detail.type, $event.detail.message)"
     x-show="visible"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 transform translate-y-2"
     x-transition:enter-end="opacity-100 transform translate-y-0"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed top-20 right-4 z-[9999] max-w-sm w-full pointer-events-auto"
     style="display: none;">

    <div class="rounded-lg shadow-2xl overflow-hidden border-2"
         :class="{
             'bg-green-50 dark:bg-green-900/30 border-green-500': type === 'success',
             'bg-red-50 dark:bg-red-900/30 border-red-500': type === 'error',
             'bg-yellow-50 dark:bg-yellow-900/30 border-yellow-500': type === 'warning',
             'bg-blue-50 dark:bg-blue-900/30 border-blue-500': type === 'info'
         }">

        <div class="p-4 flex items-start gap-3">
            {{-- Icon --}}
            <div class="flex-shrink-0">
                <i class="text-2xl"
                   :class="{
                       'fas fa-check-circle text-green-600 dark:text-green-400': type === 'success',
                       'fas fa-times-circle text-red-600 dark:text-red-400': type === 'error',
                       'fas fa-exclamation-triangle text-yellow-600 dark:text-yellow-400': type === 'warning',
                       'fas fa-info-circle text-blue-600 dark:text-blue-400': type === 'info'
                   }"></i>
            </div>

            {{-- Message --}}
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium"
                   :class="{
                       'text-green-900 dark:text-green-100': type === 'success',
                       'text-red-900 dark:text-red-100': type === 'error',
                       'text-yellow-900 dark:text-yellow-100': type === 'warning',
                       'text-blue-900 dark:text-blue-100': type === 'info'
                   }"
                   x-text="message"></p>
            </div>

            {{-- Close Button --}}
            <button @click="hide()"
                    class="flex-shrink-0 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>

        {{-- Progress Bar --}}
        <div class="h-1 bg-gray-200 dark:bg-gray-700">
            <div class="h-full transition-all duration-[3000ms] ease-linear"
                 :class="{
                     'bg-green-600': type === 'success',
                     'bg-red-600': type === 'error',
                     'bg-yellow-600': type === 'warning',
                     'bg-blue-600': type === 'info'
                 }"
                 :style="{ width: progress + '%' }"></div>
        </div>
    </div>
</div>

@once
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('toastNotification', () => ({
        visible: false,
        type: 'info',
        message: '',
        progress: 100,
        timer: null,
        progressInterval: null,

        show(type, message) {
            // Önceki notification varsa temizle
            this.hide();

            this.type = type || 'info';
            this.message = message || 'Bildirim';
            this.visible = true;
            this.progress = 100;

            // Progress bar animasyonu
            this.progressInterval = setInterval(() => {
                this.progress -= 3.33; // 3000ms / 30 = 100 adımda %100'den %0'a
                if (this.progress <= 0) {
                    clearInterval(this.progressInterval);
                }
            }, 100);

            // 3 saniye sonra otomatik kapat
            this.timer = setTimeout(() => {
                this.hide();
            }, 3000);

            console.log(`🔔 Toast: [${type}] ${message}`);
        },

        hide() {
            this.visible = false;
            if (this.timer) clearTimeout(this.timer);
            if (this.progressInterval) clearInterval(this.progressInterval);
            this.progress = 100;
        }
    }));
});

// Global notify helper function
window.notify = function(type, message) {
    window.dispatchEvent(new CustomEvent('notify', {
        detail: { type, message }
    }));
};
</script>
@endonce

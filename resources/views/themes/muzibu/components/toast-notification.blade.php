{{-- Toast Notification - Alpine Store Based --}}
<div x-data="{
        // Toast store'dan değerleri alıyoruz
        get toastVisible() { return $store.toast?.visible || false; },
        get toastMessage() { return $store.toast?.message || ''; },
        get toastType() { return $store.toast?.type || 'info'; }
     }"
     x-show="toastVisible"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 transform translate-y-2 translate-x-0"
     x-transition:enter-end="opacity-100 transform translate-y-0 translate-x-0"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 transform translate-y-0"
     x-transition:leave-end="opacity-0 transform translate-y-2"
     class="fixed right-4 z-[9999] w-[calc(100%-2rem)] sm:w-96 pointer-events-auto"
     style="display: none; bottom: 7rem;"
     @click="$store.toast.hide()">

    <div class="rounded-xl shadow-2xl overflow-hidden backdrop-blur-lg border"
         :class="{
             'bg-green-500/95 border-green-400': toastType === 'success',
             'bg-red-500/95 border-red-400': toastType === 'error',
             'bg-yellow-500/95 border-yellow-400': toastType === 'warning',
             'bg-blue-500/95 border-blue-400': toastType === 'info'
         }">

        <div class="p-4 flex items-center gap-3">
            {{-- Icon --}}
            <div class="flex-shrink-0">
                <i class="text-2xl text-white"
                   :class="{
                       'fas fa-check-circle': toastType === 'success',
                       'fas fa-times-circle': toastType === 'error',
                       'fas fa-exclamation-triangle': toastType === 'warning',
                       'fas fa-info-circle': toastType === 'info'
                   }"></i>
            </div>

            {{-- Message --}}
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-white" x-text="toastMessage"></p>
            </div>

            {{-- Close Button --}}
            <button @click.stop="$store.toast.hide()"
                    class="flex-shrink-0 text-white/80 hover:text-white transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
</div>

{{-- Alpine.store('toast') is defined in /public/themes/muzibu/js/ui/muzibu-toast.js --}}

{{-- 🔴 Confirmation Modal - Alpine Store Based --}}
<div x-data
     x-show="$store.confirmModal.visible"
     x-cloak
     class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     @click.self="$store.confirmModal.hide()">

    <div class="bg-slate-900 border border-white/10 rounded-2xl p-6 w-full max-w-md shadow-2xl"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         @click.stop>

        {{-- Icon & Title --}}
        <div class="text-center mb-6">
            <div class="w-14 h-14 mx-auto mb-4 rounded-xl flex items-center justify-center"
                 :class="$store.confirmModal.type === 'danger' ? 'bg-red-500/20' : 'bg-blue-500/20'">
                <i class="text-2xl"
                   :class="$store.confirmModal.type === 'danger' ? 'fas fa-exclamation-triangle text-red-400' : 'fas fa-question-circle text-blue-400'"></i>
            </div>
            <h3 class="text-xl font-bold text-white mb-2" x-text="$store.confirmModal.title"></h3>
            <p class="text-gray-400 text-sm" x-text="$store.confirmModal.message"></p>
        </div>

        {{-- Buttons --}}
        <div class="flex gap-3">
            <button @click="$store.confirmModal.hide()"
                    class="flex-1 py-3 bg-white/5 hover:bg-white/10 text-white rounded-xl transition-colors">
                <span x-text="$store.confirmModal.cancelText || 'Vazgeç'"></span>
            </button>
            <button @click="$store.confirmModal.confirm()"
                    class="flex-1 py-3 rounded-xl transition-colors text-white"
                    :class="$store.confirmModal.type === 'danger' ? 'bg-red-500 hover:bg-red-600' : 'bg-blue-500 hover:bg-blue-600'">
                <span x-text="$store.confirmModal.confirmText || 'Onayla'"></span>
            </button>
        </div>
    </div>
</div>

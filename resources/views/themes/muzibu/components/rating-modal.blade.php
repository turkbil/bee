{{-- Rating Modal Component --}}
<div x-show="$store.contextMenu.ratingModal.open"
     x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center p-4">
    <div x-on:click="$store.contextMenu.ratingModal.open = false" class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>

    <div class="relative bg-gray-800 rounded-lg shadow-2xl border border-white/20 max-w-md w-full p-6">
        {{-- Content Info Header --}}
        <div class="mb-6 pb-6 border-b border-white/20">
            <div class="flex items-center gap-4">
                {{-- Icon/Cover --}}
                <div class="flex-shrink-0">
                    <div :class="{
                        'bg-gradient-to-br from-pink-500 to-purple-600': $store.contextMenu.type === 'song',
                        'bg-gradient-to-br from-blue-500 to-cyan-600': $store.contextMenu.type === 'album',
                        'bg-gradient-to-br from-purple-500 to-indigo-600': $store.contextMenu.type === 'playlist'
                    }" class="w-16 h-16 rounded-lg flex items-center justify-center">
                        <i :class="{
                            'fa-music': $store.contextMenu.type === 'song',
                            'fa-compact-disc': $store.contextMenu.type === 'album',
                            'fa-list': $store.contextMenu.type === 'playlist'
                        }" class="fas text-white text-2xl opacity-80"></i>
                    </div>
                </div>

                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    <p class="text-xs uppercase font-semibold mb-1" :class="{
                        'text-pink-400': $store.contextMenu.type === 'song',
                        'text-blue-400': $store.contextMenu.type === 'album',
                        'text-purple-400': $store.contextMenu.type === 'playlist'
                    }">
                        <i :class="{
                            'fa-music': $store.contextMenu.type === 'song',
                            'fa-compact-disc': $store.contextMenu.type === 'album',
                            'fa-list': $store.contextMenu.type === 'playlist'
                        }" class="fas mr-1"></i>
                        <span x-text="{
                            'song': 'Şarkıya Puan Ver',
                            'album': 'Albüme Puan Ver',
                            'playlist': 'Playliste Puan Ver'
                        }[$store.contextMenu.type]"></span>
                    </p>
                    <h3 class="text-lg font-bold text-white truncate" x-text="$store.contextMenu.data?.title"></h3>
                    <p class="text-sm text-gray-400 truncate" x-text="$store.contextMenu.data?.artist || ($store.contextMenu.data?.songCount + ' şarkı')"></p>
                </div>
            </div>
        </div>

        <h3 class="text-xl font-bold text-white mb-6 text-center">Puanınızı Verin</h3>

        {{-- Stars --}}
        <div class="flex items-center justify-center gap-2 mb-6">
            <template x-for="star in [1,2,3,4,5]" :key="star">
                <button x-on:click="$store.contextMenu.ratingModal.rating = star"
                        @mouseover="$store.contextMenu.ratingModal.hoverRating = star"
                        @mouseleave="$store.contextMenu.ratingModal.hoverRating = 0"
                        type="button"
                        class="text-4xl transition-all duration-200 hover:scale-110">
                    <i :class="star <= ($store.contextMenu.ratingModal.hoverRating || $store.contextMenu.ratingModal.rating) ? 'fas fa-star text-yellow-400' : 'far fa-star text-gray-500'"></i>
                </button>
            </template>
        </div>

        <p class="text-center text-gray-300 mb-6" x-show="$store.contextMenu.ratingModal.rating > 0">
            <span x-text="$store.contextMenu.ratingModal.rating"></span> yıldız seçtiniz
        </p>

        {{-- Comment --}}
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-300 mb-2">Yorumunuz (opsiyonel)</label>
            <textarea x-model="$store.contextMenu.ratingModal.comment"
                      rows="4"
                      class="w-full bg-gray-900 border border-white/20 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-orange-500 transition-colors"
                      placeholder="Bu içerik hakkında düşünceleriniz..."></textarea>
        </div>

        {{-- Actions --}}
        <div class="flex gap-3">
            <button x-on:click="$store.contextMenu.ratingModal.open = false"
                    type="button"
                    class="flex-1 px-4 py-3 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors">
                İptal
            </button>
            <button x-on:click="$store.contextMenu.submitRating()"
                    :disabled="$store.contextMenu.ratingModal.rating === 0"
                    :class="$store.contextMenu.ratingModal.rating === 0 ? 'opacity-50 cursor-not-allowed' : ''"
                    type="button"
                    class="flex-1 px-4 py-3 bg-orange-500 hover:bg-orange-600 text-white rounded-lg transition-colors font-semibold">
                Puanı Gönder
            </button>
        </div>
    </div>
</div>

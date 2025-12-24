{{-- Create Playlist Modal (SPA Compatible) --}}
<div x-data="createPlaylistModal()"
     x-show="open"
     x-cloak
     @open-create-playlist.window="openModal()"
     @open-create-playlist-modal.window="openModal()"
     @keydown.escape.window="closeModal()"
     class="fixed inset-0 z-50 flex items-center justify-center p-4">

    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" @click="closeModal()"></div>

    {{-- Modal --}}
    <div class="relative w-full max-w-md bg-slate-900 border border-white/10 rounded-2xl shadow-2xl"
         x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         @click.stop>

        {{-- Header --}}
        <div class="flex items-center justify-between p-6 border-b border-white/10">
            <h3 class="text-xl font-bold text-white">
                <i class="fas fa-plus mr-2 text-green-400"></i>
                {{ __('muzibu::front.sidebar.create_playlist') }}
            </h3>
            <button @click="closeModal()" class="text-gray-400 hover:text-white transition">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

        {{-- Form --}}
        <form @submit.prevent="createPlaylist()" class="p-6 space-y-4">
            {{-- Title --}}
            <div>
                <label class="block text-sm text-gray-400 mb-2">{{ __('muzibu::front.general.playlist') }} Adı</label>
                <input type="text" x-model="title" required
                       placeholder="Playlistinize bir isim verin"
                       class="w-full px-4 py-3 bg-white/5 border border-white/20 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-green-500 transition">
            </div>

            {{-- Description --}}
            <div>
                <label class="block text-sm text-gray-400 mb-2">Açıklama (Opsiyonel)</label>
                <textarea x-model="description" rows="3"
                          placeholder="Playlist hakkında kısa bir açıklama"
                          class="w-full px-4 py-3 bg-white/5 border border-white/20 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-green-500 transition resize-none"></textarea>
            </div>

            {{-- Privacy --}}
            <div>
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" x-model="isPublic"
                           class="w-5 h-5 rounded border-white/20 bg-white/5 text-green-500 focus:ring-green-500 focus:ring-offset-0">
                    <span class="text-gray-300">Herkese Açık</span>
                </label>
                <p class="text-xs text-gray-500 mt-1 ml-8">
                    Herkese açık playlistler diğer kullanıcılar tarafından görülebilir.
                </p>
            </div>

            {{-- Buttons --}}
            <div class="flex gap-3 pt-4">
                <button type="button" @click="closeModal()"
                        class="flex-1 py-3 bg-white/10 hover:bg-white/20 text-white font-semibold rounded-xl transition">
                    {{ __('muzibu::front.corporate.cancel') }}
                </button>
                <button type="submit" :disabled="loading || !title.trim()"
                        class="flex-1 py-3 bg-green-500 hover:bg-green-600 disabled:bg-gray-600 disabled:cursor-not-allowed text-white font-semibold rounded-xl transition">
                    <span x-show="!loading">Oluştur</span>
                    <span x-show="loading"><i class="fas fa-spinner fa-spin"></i></span>
                </button>
            </div>
        </form>
    </div>
</div>

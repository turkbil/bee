{{-- Create Playlist Modal --}}
<template x-teleport="body">
    <div
        x-data="{ open: false, title: '', description: '', loading: false }"
        @open-create-playlist-modal.window="open = true; title = ''; description = ''"
        x-show="open"
        x-cloak
        @keydown.escape.window="open = false"
        class="fixed inset-0 z-[9999] flex items-center justify-center p-4"
    >
        {{-- Backdrop --}}
        <div
            x-show="open"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="open = false"
            class="absolute inset-0 bg-black/90 backdrop-blur-sm"
        ></div>

        {{-- Modal --}}
        <div
            x-show="open"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            @click.stop
            class="relative w-full max-w-md bg-gradient-to-br from-zinc-900 to-black rounded-2xl shadow-2xl border border-white/10 p-6"
        >
            {{-- Close Button --}}
            <button
                @click="open = false"
                class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center text-white/60 hover:text-white hover:bg-white/10 rounded-full transition-all"
            >
                <i class="fas fa-times"></i>
            </button>

            {{-- Header --}}
            <div class="mb-6">
                <h3 class="text-2xl font-bold text-white">Yeni Playlist Oluştur</h3>
                <p class="text-sm text-gray-400 mt-1">Playlist'ine bir isim ver</p>
            </div>

            {{-- Form --}}
            <form @submit.prevent="
                if (!title.trim()) {
                    $store.toast.show('Playlist adı gerekli', 'error');
                    return;
                }

                loading = true;

                fetch('/api/muzibu/playlists', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || ''
                    },
                    body: JSON.stringify({
                        title: { tr: title },
                        description: description ? { tr: description } : null,
                        is_public: false
                    })
                }).then(r => r.json())
                  .then(data => {
                      loading = false;
                      if (data.success) {
                          $store.toast.show('Playlist oluşturuldu', 'success');
                          open = false;
                          setTimeout(() => window.location.reload(), 1000);
                      } else {
                          $store.toast.show(data.message || 'Hata oluştu', 'error');
                      }
                  })
                  .catch(err => {
                      loading = false;
                      console.error(err);
                      $store.toast.show('Bağlantı hatası', 'error');
                  });
            ">
                {{-- Playlist Title --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-300 mb-2">
                        Playlist Adı *
                    </label>
                    <input
                        type="text"
                        x-model="title"
                        placeholder="Örn: Sabah Müziklerim"
                        required
                        class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-muzibu-coral focus:border-transparent"
                    >
                </div>

                {{-- Playlist Description --}}
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-300 mb-2">
                        Açıklama (İsteğe Bağlı)
                    </label>
                    <textarea
                        x-model="description"
                        placeholder="Bu playlist hakkında bir şeyler yaz..."
                        rows="3"
                        class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-muzibu-coral focus:border-transparent resize-none"
                    ></textarea>
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-3">
                    <button
                        type="button"
                        @click="open = false"
                        class="flex-1 px-6 py-3 bg-white/10 hover:bg-white/20 text-white font-semibold rounded-full transition-all"
                    >
                        İptal
                    </button>
                    <button
                        type="submit"
                        :disabled="loading || !title.trim()"
                        :class="loading || !title.trim() ? 'opacity-50 cursor-not-allowed' : 'hover:bg-opacity-90'"
                        class="flex-1 px-6 py-3 bg-muzibu-coral text-white font-semibold rounded-full transition-all flex items-center justify-center gap-2"
                    >
                        <i class="fas fa-plus" x-show="!loading"></i>
                        <i class="fas fa-spinner fa-spin" x-show="loading" x-cloak></i>
                        <span x-text="loading ? 'Oluşturuluyor...' : 'Oluştur'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

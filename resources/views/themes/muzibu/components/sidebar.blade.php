<aside class="fixed left-0 z-40 h-full w-64 bg-black" :class="isLoggedIn ? 'top-0' : 'top-14'">
    <div class="flex flex-col h-full">
        <div class="p-6">
            {!! app(\App\Services\LogoService::class)->renderHeaderLogo(['class' => 'h-10 w-auto']) !!}
        </div>

        <nav class="flex-1 overflow-y-auto py-4 px-3">
            <ul class="space-y-1">
                <li><a href="/" :class="currentPath === '/' ? 'text-white font-semibold bg-spotify-gray' : 'text-gray-400 hover:text-white'" class="flex items-center px-4 py-2 transition-all rounded-md"><i class="fas fa-home w-5 mr-3"></i>Ana Sayfa</a></li>
                <li><a href="/search" :class="currentPath === '/search' ? 'text-white font-semibold bg-spotify-gray' : 'text-gray-400 hover:text-white'" class="flex items-center px-4 py-2 transition-all rounded-md"><i class="fas fa-search w-5 mr-3"></i>Ara</a></li>
                <li><a href="/playlists" :class="currentPath === '/playlists' ? 'text-white font-semibold bg-spotify-gray' : 'text-gray-400 hover:text-white'" class="flex items-center px-4 py-2 transition-all rounded-md"><i class="fas fa-list w-5 mr-3"></i>Playlistler</a></li>
                <li><a href="/albums" :class="currentPath === '/albums' ? 'text-white font-semibold bg-spotify-gray' : 'text-gray-400 hover:text-white'" class="flex items-center px-4 py-2 transition-all rounded-md"><i class="fas fa-record-vinyl w-5 mr-3"></i>Albümler</a></li>
                <li><a href="/genres" :class="currentPath === '/genres' ? 'text-white font-semibold bg-spotify-gray' : 'text-gray-400 hover:text-white'" class="flex items-center px-4 py-2 transition-all rounded-md"><i class="fas fa-guitar w-5 mr-3"></i>Türler</a></li>
                <li><a href="/sectors" :class="currentPath === '/sectors' ? 'text-white font-semibold bg-spotify-gray' : 'text-gray-400 hover:text-white'" class="flex items-center px-4 py-2 transition-all rounded-md"><i class="fas fa-building w-5 mr-3"></i>Sektörler</a></li>
            </ul>

            <div class="mt-6 pt-6 border-t border-white/10">
                <button class="flex items-center px-4 py-2 text-gray-400 hover:text-white transition-all w-full">
                    <i class="fas fa-plus-square w-5 mr-3"></i>Playlist Oluştur
                </button>
                <button class="flex items-center px-4 py-2 text-gray-400 hover:text-white transition-all w-full">
                    <i class="fas fa-heart w-5 mr-3"></i>Beğenilen Şarkılar
                </button>
            </div>

            <div x-show="!isLoggedIn" x-transition class="mt-6 px-3 space-y-3">
                <button @click="showAuthModal = 'register'" class="w-full px-4 py-3 rounded-full bg-gradient-to-r from-spotify-green to-green-600 hover:from-spotify-green-light hover:to-green-500 text-white font-bold transition-all shadow-lg">
                    <i class="fas fa-rocket mr-2"></i>Ücretsiz Başla
                </button>
                <button @click="showAuthModal = 'login'" class="w-full px-4 py-3 rounded-full bg-transparent border border-gray-600 text-white font-semibold hover:border-white hover:scale-105 transition-all">
                    Giriş Yap
                </button>
            </div>

            <div x-show="isLoggedIn" x-transition class="mt-6 px-3">
                <div class="p-4 bg-spotify-gray rounded-lg">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-spotify-green to-green-600 rounded-full flex items-center justify-center shadow-lg">
                            <i class="fas fa-user text-white text-sm"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="font-semibold text-white text-sm truncate">Demo Kullanıcı</div>
                            <div class="text-xs text-spotify-green-light flex items-center gap-1">
                                <i class="fas fa-crown"></i> Premium
                            </div>
                        </div>
                    </div>
                    <button @click="logout()" class="w-full px-3 py-2 bg-white/10 text-white rounded-full text-xs hover:bg-white/20 transition-all font-semibold">
                        Çıkış Yap
                    </button>
                </div>
            </div>
        </nav>
    </div>
</aside>

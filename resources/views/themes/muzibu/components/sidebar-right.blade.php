        {{-- RIGHT SIDEBAR with better styling --}}
        <aside class="hidden xl:block bg-black p-4 overflow-y-auto border-l border-white/5 animate-slide-up">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold bg-gradient-to-r from-white to-spotify-text-gray bg-clip-text text-transparent">Öne Çıkan Listeler</h3>
            </div>

            @for($i = 0; $i < 8; $i++)
            <div class="flex items-center gap-3 p-2 rounded-lg hover:bg-spotify-gray cursor-pointer mb-2 group  transition-all">
                <div class="w-14 h-14 rounded bg-gradient-to-br from-indigo-500 to-purple-600 flex-shrink-0 flex items-center justify-center text-2xl shadow-lg group-hover:shadow-spotify-green/50  transition-all">
                    {{ ['🎵', '🎧', '👑', '🎤', '🇹🇷', '💚', '🎸', '📚'][$i] }}
                </div>
                <div class="flex-1 min-w-0">
                    <h4 class="text-sm font-semibold text-white truncate group-hover:text-spotify-green transition-colors">{{ ['Daily Mix 1', 'Türkçe Pop', 'Top Hits', 'Acoustic', 'Turkish 2000s', 'Chill', 'Rock', 'Podcast'][$i] }}</h4>
                    <p class="text-xs text-spotify-text-gray truncate">Playlist • Spotify</p>
                </div>
            </div>
            @endfor
        </aside>


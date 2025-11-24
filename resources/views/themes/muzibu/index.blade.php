<x-themes.muzibu.layouts.app>
    <!-- HERO CAROUSEL - SPOTIFY STYLE (Guest only) -->
    <section x-show="!isLoggedIn" x-transition
             x-data="{
                 currentSlide: 0,
                 slides: [
                     {title: 'Yasal & Telifsiz Müzik', desc: '25.000+ telifsiz şarkı. Telif cezalarından kurtulun.', bg: 'from-green-500 via-emerald-600 to-teal-700', img: 'https://images.unsplash.com/photo-1514320291840-2e0a9bf2a9ae?w=800'},
                     {title: 'İşletmeniz İçin Özel', desc: 'Restoranlar, kafeler, oteller için profesyonel müzik çözümü.', bg: 'from-teal-500 via-cyan-600 to-blue-700', img: 'https://images.unsplash.com/photo-1470225620780-dba8ba36b745?w=800'},
                     {title: '%100 Yasal & Güvenli', desc: '5.000+ işletme güvenle kullanıyor. Siz de katılın.', bg: 'from-emerald-500 via-green-600 to-lime-700', img: 'https://images.unsplash.com/photo-1511379938547-c1f69419868d?w=800'}
                 ]
             }"
             x-init="setInterval(() => { currentSlide = (currentSlide + 1) % slides.length }, 5000)"
             class="relative px-8 py-8 overflow-hidden">

        <div class="relative h-[500px] rounded-3xl overflow-hidden shadow-2xl">
            <template x-for="(slide, index) in slides" :key="index">
                <div x-show="currentSlide === index"
                     x-transition:enter="transition ease-out duration-700"
                     x-transition:enter-start="opacity-0 translate-x-10"
                     x-transition:enter-end="opacity-100 translate-x-0"
                     x-transition:leave="transition ease-in duration-300"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="absolute inset-0">

                    <div :class="'absolute inset-0 bg-gradient-to-r ' + slide.bg"></div>
                    <img :src="slide.img" class="absolute inset-0 w-full h-full object-cover mix-blend-overlay opacity-40">

                    <div class="relative h-full flex items-center px-16">
                        <div class="max-w-2xl space-y-6">
                            <div class="flex gap-3">
                                <span class="px-4 py-1.5 bg-white/20 backdrop-blur-md rounded-full text-xs font-bold text-white">100% Yasal</span>
                                <span class="px-4 py-1.5 bg-white/20 backdrop-blur-md rounded-full text-xs font-bold text-white">Telifsiz</span>
                                <span class="px-4 py-1.5 bg-white/20 backdrop-blur-md rounded-full text-xs font-bold text-white">İşletmeniz İçin</span>
                            </div>
                            <h1 class="text-7xl font-black text-white leading-tight" x-text="slide.title"></h1>
                            <p class="text-2xl text-white/95 font-medium" x-text="slide.desc"></p>
                            <div class="flex gap-4 pt-4">
                                <button @click="showAuthModal = 'register'" class="px-10 py-4 bg-white text-black rounded-full font-bold text-lg hover:scale-105 transition-all shadow-2xl">
                                    <i class="fas fa-rocket mr-2"></i>7 Gün Ücretsiz Deneyin
                                </button>
                                <button @click="showAuthModal = 'login'" class="px-10 py-4 bg-black/30 backdrop-blur-md text-white rounded-full font-bold text-lg hover:bg-black/50 transition-all border-2 border-white">
                                    Giriş Yap
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Carousel Indicators -->
            <div class="absolute bottom-8 left-16 flex gap-2 z-10">
                <template x-for="(slide, index) in slides" :key="'dot-'+index">
                    <button @click="currentSlide = index"
                            :class="currentSlide === index ? 'bg-white w-10' : 'bg-white/40 w-2.5'"
                            class="h-2.5 rounded-full transition-all"></button>
                </template>
            </div>

            <!-- Arrow Navigation -->
            <button @click="currentSlide = (currentSlide - 1 + slides.length) % slides.length" class="absolute left-6 top-1/2 -translate-y-1/2 w-12 h-12 bg-white/20 hover:bg-white/30 rounded-full flex items-center justify-center transition-all backdrop-blur-sm">
                <i class="fas fa-chevron-left text-white text-xl"></i>
            </button>
            <button @click="currentSlide = (currentSlide + 1) % slides.length" class="absolute right-6 top-1/2 -translate-y-1/2 w-12 h-12 bg-white/20 hover:bg-white/30 rounded-full flex items-center justify-center transition-all backdrop-blur-sm">
                <i class="fas fa-chevron-right text-white text-xl"></i>
            </button>
        </div>
    </section>

    <!-- GUEST: EN POPÜLER PLAYLISTLER -->
    <section x-show="!isLoggedIn" x-transition class="px-8 py-8">
        <div class="mb-6">
            <h2 class="text-3xl font-bold text-white mb-2">🔥 En Popüler Playlistler</h2>
            <p class="text-gray-400">İşletmenizde dinlenebilecek en popüler telifsiz playlistler</p>
        </div>

        <div class="grid grid-cols-6 gap-4">
            @for($i = 1; $i <= 6; $i++)
            <div class="group bg-spotify-gray/50 hover:bg-spotify-gray p-4 rounded-lg transition-all relative">
                <div class="relative mb-3 cursor-pointer" @click="showAuthModal = 'register'">
                    <img src="https://picsum.photos/seed/guestpl{{ $i }}/200" class="w-full aspect-square rounded-md shadow-lg mb-3">

                    <!-- Overlay: Login Required -->
                    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm rounded-md opacity-0 group-hover:opacity-100 transition-all flex items-center justify-center">
                        <div class="text-center">
                            <i class="fas fa-lock text-white text-3xl mb-2"></i>
                            <div class="text-xs text-white font-semibold">Dinlemek için üye olun</div>
                        </div>
                    </div>

                    <!-- Bottom Right: Play Button (Locked) -->
                    <button @click.stop="showAuthModal = 'register'" class="absolute bottom-2 right-2 w-12 h-12 bg-spotify-green rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 translate-y-2 group-hover:translate-y-0 transition-all shadow-xl">
                        <i class="fas fa-play text-black ml-0.5"></i>
                    </button>
                </div>
                <h4 class="font-bold text-white truncate mb-1">Popüler Playlist {{ $i }}</h4>
                <p class="text-sm text-gray-400 truncate">25 şarkı</p>
            </div>
            @endfor
        </div>
    </section>

    <!-- GUEST: YENİ ALBÜMLER -->
    <section x-show="!isLoggedIn" x-transition class="px-8 py-8">
        <div class="mb-6">
            <h2 class="text-3xl font-bold text-white mb-2">🎵 Yeni Çıkan Albümler</h2>
            <p class="text-gray-400">Bu hafta eklenen telifsiz müzik albümleri</p>
        </div>

        <div class="grid grid-cols-6 gap-4">
            @for($i = 1; $i <= 6; $i++)
            <div class="group bg-spotify-gray/50 hover:bg-spotify-gray p-4 rounded-lg transition-all relative">
                <div class="relative mb-3 cursor-pointer" @click="showAuthModal = 'register'">
                    <img src="https://picsum.photos/seed/guestalbum{{ $i }}/200" class="w-full aspect-square rounded-md shadow-lg mb-3">

                    <!-- Overlay: Login Required -->
                    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm rounded-md opacity-0 group-hover:opacity-100 transition-all flex items-center justify-center">
                        <div class="text-center">
                            <i class="fas fa-lock text-white text-3xl mb-2"></i>
                            <div class="text-xs text-white font-semibold">Dinlemek için üye olun</div>
                        </div>
                    </div>

                    <!-- Bottom Right: Play Button (Locked) -->
                    <button @click.stop="showAuthModal = 'register'" class="absolute bottom-2 right-2 w-12 h-12 bg-spotify-green rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 translate-y-2 group-hover:translate-y-0 transition-all shadow-xl">
                        <i class="fas fa-play text-black ml-0.5"></i>
                    </button>
                </div>
                <h4 class="font-bold text-white truncate mb-1">Yeni Album {{ $i }}</h4>
                <p class="text-sm text-gray-400 truncate">Çeşitli Sanatçılar</p>
            </div>
            @endfor
        </div>
    </section>

    <!-- GUEST: TÜRLER - COLORFUL GENRE CARDS -->
    <section x-show="!isLoggedIn" x-transition class="px-8 py-8">
        <div class="mb-6">
            <h2 class="text-3xl font-bold text-white mb-2">🎸 Müzik Türleri</h2>
            <p class="text-gray-400">İşletmenizin atmosferine uygun türleri keşfedin</p>
        </div>

        <div class="grid grid-cols-5 gap-4">
            @php
                $genres = [
                    ['name' => 'Caz', 'emoji' => '🎷', 'count' => '142 şarkı', 'color' => 'from-green-500 to-emerald-700'],
                    ['name' => 'Rock', 'emoji' => '🎸', 'count' => '238 şarkı', 'color' => 'from-teal-500 to-cyan-700'],
                    ['name' => 'Pop', 'emoji' => '🎤', 'count' => '456 şarkı', 'color' => 'from-emerald-500 to-green-700'],
                    ['name' => 'Bossa Nova', 'emoji' => '🌴', 'count' => '89 şarkı', 'color' => 'from-lime-500 to-green-700'],
                    ['name' => 'Elektronik', 'emoji' => '🎧', 'count' => '321 şarkı', 'color' => 'from-cyan-500 to-teal-700']
                ];
            @endphp
            @foreach($genres as $genre)
            <button @click="showAuthModal = 'register'" class="group relative h-48 rounded-lg overflow-hidden bg-gradient-to-br {{ $genre['color'] }} hover:scale-105 transition-all shadow-lg cursor-pointer">
                <div class="absolute inset-0 p-5 flex flex-col justify-between">
                    <h4 class="text-3xl font-black text-white">{{ $genre['name'] }}</h4>
                    <div class="text-sm text-white/90 font-semibold">{{ $genre['count'] }}</div>
                </div>
                <div class="absolute -bottom-6 -right-6 text-9xl opacity-20">{{ $genre['emoji'] }}</div>
                <div class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition-all flex items-center justify-center">
                    <i class="fas fa-lock text-white text-3xl"></i>
                </div>
            </button>
            @endforeach
        </div>
    </section>

    <!-- MEMBER: FEATURED PLAYLISTS -->
    <section x-show="isLoggedIn" x-transition class="px-8 py-6">
        <div class="mb-6">
            <h2 class="text-3xl font-bold text-white mb-2">İyi günler</h2>
            <p class="text-gray-400">En çok dinlediğin playlistler</p>
        </div>

        <!-- Large Grid Cards -->
        <div class="grid grid-cols-3 gap-4 mb-8">
            @for($i = 1; $i <= 6; $i++)
            <div class="group bg-white/5 hover:bg-white/10 rounded-lg p-4 flex items-center gap-4 transition-all relative" x-data="{ menuOpen: false }">
                <a href="/playlist/{{ $i }}" class="flex items-center gap-4 flex-1 min-w-0">
                    <div class="relative">
                        <img src="https://picsum.photos/seed/featured{{ $i }}/80" class="w-20 h-20 rounded shadow-lg">

                        <!-- Top Right: Favorite (Mini) -->
                        <button @click.prevent="toggleFavorite('playlist', {{ $i }})" class="absolute -top-1 -right-1 w-6 h-6 bg-black/80 backdrop-blur-sm rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all">
                            <i :class="isFavorite('playlist', {{ $i }}) ? 'fas fa-heart text-spotify-green' : 'far fa-heart text-white'" class="text-xs"></i>
                        </button>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="font-bold text-white truncate mb-1">Playlist {{ $i }}</h4>
                        <p class="text-sm text-gray-400 truncate">25 şarkı</p>
                    </div>
                </a>

                <!-- Play Button -->
                <button @click="playPlaylist({{ $i }})" class="w-12 h-12 bg-spotify-green rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 translate-y-2 group-hover:translate-y-0 transition-all shadow-xl pulse-play">
                    <i class="fas fa-play text-black ml-0.5"></i>
                </button>

                <!-- Context Menu Button -->
                <button @click="menuOpen = !menuOpen" class="w-8 h-8 bg-black/60 backdrop-blur-sm rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all ml-2">
                    <i class="fas fa-ellipsis-h text-white text-xs"></i>
                </button>

                <!-- Context Menu Dropdown -->
                <div x-show="menuOpen" @click.away="menuOpen = false" x-transition class="absolute top-full right-4 mt-2 w-48 bg-spotify-gray border border-white/10 rounded-md shadow-2xl z-50">
                    <div class="p-1">
                        <button @click="shareContent('playlist', {{ $i }}); menuOpen = false" class="w-full flex items-center gap-3 px-3 py-2 rounded-sm hover:bg-white/10 transition-all text-left text-sm">
                            <i class="fas fa-share text-gray-400"></i><span class="text-white">Paylaş</span>
                        </button>
                        <button @click="addToQueue('playlist', {{ $i }}); menuOpen = false" class="w-full flex items-center gap-3 px-3 py-2 rounded-sm hover:bg-white/10 transition-all text-left text-sm">
                            <i class="fas fa-list text-gray-400"></i><span class="text-white">Kuyruğa Ekle</span>
                        </button>
                    </div>
                </div>
            </div>
            @endfor
        </div>

        <!-- JUMP BACK IN - HORIZONTAL SCROLL (GERÇEK SPOTIFY!) -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-2xl font-bold text-white">Kaldığın Yerden Devam Et</h3>
                <a href="/recent" class="text-sm text-gray-400 hover:text-white font-semibold">Tümünü Gör</a>
            </div>
            <div class="flex gap-4 overflow-x-auto pb-4" style="scrollbar-width: none; -ms-overflow-style: none;">
                @for($i = 1; $i <= 8; $i++)
                <div class="group flex-shrink-0 w-52" x-data="{ menuOpen: false }">
                    <div class="bg-spotify-gray hover:bg-white/10 p-4 rounded-lg transition-all relative">
                        <div class="relative mb-3">
                            <img src="https://picsum.photos/seed/jumpback{{ $i }}/200" class="w-full aspect-square rounded-md shadow-lg mb-3">
                            <button @click.prevent="playAlbum({{ $i }})" class="absolute bottom-2 right-2 w-12 h-12 bg-spotify-green rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 translate-y-2 group-hover:translate-y-0 transition-all shadow-xl">
                                <i class="fas fa-play text-black ml-0.5"></i>
                            </button>
                        </div>
                        <h4 class="font-bold text-white truncate mb-1">Continue {{ $i }}</h4>
                        <p class="text-sm text-gray-400 truncate">Last played 2 hours ago</p>
                    </div>
                </div>
                @endfor
            </div>
        </div>

        <!-- YENİ ÇIKAN ALBUMLER - HORIZONTAL SCROLL -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-2xl font-bold text-white">Yeni Albümler</h3>
                <a href="/albums" class="text-sm text-gray-400 hover:text-white font-semibold">Tümünü Gör</a>
            </div>
            <div class="flex gap-4 overflow-x-auto pb-4" style="scrollbar-width: none; -ms-overflow-style: none;">
                @for($i = 1; $i <= 8; $i++)
                <div class="group flex-shrink-0 w-52 bg-spotify-gray hover:bg-white/10 p-4 rounded-lg transition-all relative" x-data="{ menuOpen: false }">
                    <a href="/album/{{ $i }}" class="block">
                        <div class="relative mb-3">
                            <img src="https://picsum.photos/seed/album{{ $i }}/200" class="w-full aspect-square rounded-md shadow-lg mb-3">

                            <!-- Top Right: Favorite Button -->
                            <button @click.prevent="toggleFavorite('album', {{ $i }})" class="absolute top-2 right-2 w-8 h-8 bg-black/60 backdrop-blur-sm rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all">
                                <i :class="isFavorite('album', {{ $i }}) ? 'fas fa-heart text-spotify-green' : 'far fa-heart text-white'" class="text-sm"></i>
                            </button>

                            <!-- Top Left: Context Menu Button -->
                            <button @click.prevent="menuOpen = !menuOpen" class="absolute top-2 left-2 w-8 h-8 bg-black/60 backdrop-blur-sm rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all">
                                <i class="fas fa-ellipsis-h text-white text-xs"></i>
                            </button>

                            <!-- Bottom Right: Play Button -->
                            <button @click.prevent="playAlbum({{ $i }})" class="absolute bottom-2 right-2 w-12 h-12 bg-spotify-green rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 translate-y-2 group-hover:translate-y-0 transition-all shadow-xl">
                                <i class="fas fa-play text-black ml-0.5"></i>
                            </button>
                        </div>
                        <h4 class="font-bold text-white truncate mb-1">Album {{ $i }}</h4>
                        <p class="text-sm text-gray-400 truncate">Çeşitli Sanatçılar</p>
                    </a>

                    <!-- Context Menu Dropdown -->
                    <div x-show="menuOpen" @click.away="menuOpen = false" x-transition class="absolute top-14 left-6 w-48 bg-spotify-gray border border-white/10 rounded-md shadow-2xl z-50">
                        <div class="p-1">
                            <button @click="shareContent('album', {{ $i }}); menuOpen = false" class="w-full flex items-center gap-3 px-3 py-2 rounded-sm hover:bg-white/10 transition-all text-left text-sm">
                                <i class="fas fa-share text-gray-400"></i><span class="text-white">Paylaş</span>
                            </button>
                            <button @click="addToQueue('album', {{ $i }}); menuOpen = false" class="w-full flex items-center gap-3 px-3 py-2 rounded-sm hover:bg-white/10 transition-all text-left text-sm">
                                <i class="fas fa-list text-gray-400"></i><span class="text-white">Kuyruğa Ekle</span>
                            </button>
                            <button @click="goToArtist({{ $i }}); menuOpen = false" class="w-full flex items-center gap-3 px-3 py-2 rounded-sm hover:bg-white/10 transition-all text-left text-sm">
                                <i class="fas fa-user text-gray-400"></i><span class="text-white">Sanatçıya Git</span>
                            </button>
                        </div>
                    </div>
                </div>
                @endfor
            </div>
        </div>

        <!-- POPÜLER PLAYLISTLER - HORIZONTAL SCROLL -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-2xl font-bold text-white">Popüler Playlistler</h3>
                <a href="/playlists" class="text-sm text-gray-400 hover:text-white font-semibold">Tümünü Gör</a>
            </div>
            <div class="flex gap-4 overflow-x-auto pb-4" style="scrollbar-width: none; -ms-overflow-style: none;">
                @for($i = 1; $i <= 8; $i++)
                <div class="group flex-shrink-0 w-52 bg-spotify-gray hover:bg-white/10 p-4 rounded-lg transition-all relative" x-data="{ menuOpen: false }">
                    <a href="/playlist/{{ 100+$i }}" class="block">
                        <div class="relative mb-3">
                            <img src="https://picsum.photos/seed/playlist{{ $i }}/200" class="w-full aspect-square rounded-md shadow-lg mb-3">

                            <!-- Top Right: Favorite Button -->
                            <button @click.prevent="toggleFavorite('playlist', {{ 100+$i }})" class="absolute top-2 right-2 w-8 h-8 bg-black/60 backdrop-blur-sm rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all">
                                <i :class="isFavorite('playlist', {{ 100+$i }}) ? 'fas fa-heart text-spotify-green' : 'far fa-heart text-white'" class="text-sm"></i>
                            </button>

                            <!-- Top Left: Context Menu Button -->
                            <button @click.prevent="menuOpen = !menuOpen" class="absolute top-2 left-2 w-8 h-8 bg-black/60 backdrop-blur-sm rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all">
                                <i class="fas fa-ellipsis-h text-white text-xs"></i>
                            </button>

                            <!-- Bottom Right: Play Button -->
                            <button @click.prevent="playPlaylist({{ 100+$i }})" class="absolute bottom-2 right-2 w-12 h-12 bg-spotify-green rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 translate-y-2 group-hover:translate-y-0 transition-all shadow-xl">
                                <i class="fas fa-play text-black ml-0.5"></i>
                            </button>
                        </div>
                        <h4 class="font-bold text-white truncate mb-1">Playlist {{ $i }}</h4>
                        <p class="text-sm text-gray-400 truncate">25 şarkı</p>
                    </a>

                    <!-- Context Menu Dropdown -->
                    <div x-show="menuOpen" @click.away="menuOpen = false" x-transition class="absolute top-14 left-6 w-48 bg-spotify-gray border border-white/10 rounded-md shadow-2xl z-50">
                        <div class="p-1">
                            <button @click="shareContent('playlist', {{ 100+$i }}); menuOpen = false" class="w-full flex items-center gap-3 px-3 py-2 rounded-sm hover:bg-white/10 transition-all text-left text-sm">
                                <i class="fas fa-share text-gray-400"></i><span class="text-white">Paylaş</span>
                            </button>
                            <button @click="addToQueue('playlist', {{ 100+$i }}); menuOpen = false" class="w-full flex items-center gap-3 px-3 py-2 rounded-sm hover:bg-white/10 transition-all text-left text-sm">
                                <i class="fas fa-list text-gray-400"></i><span class="text-white">Kuyruğa Ekle</span>
                            </button>
                        </div>
                    </div>
                </div>
                @endfor
            </div>
        </div>

        <!-- TÜRLER - COLORFUL GENRE CARDS -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-2xl font-bold text-white">Türler</h3>
                <a href="/genres" class="text-sm text-gray-400 hover:text-white font-semibold">Tümünü Gör</a>
            </div>
            <div class="grid grid-cols-5 gap-4">
                @php
                    $memberGenres = [
                        ['name' => 'Caz', 'emoji' => '🎷', 'count' => '142 şarkı', 'color' => 'from-green-500 to-emerald-700'],
                        ['name' => 'Rock', 'emoji' => '🎸', 'count' => '238 şarkı', 'color' => 'from-teal-500 to-cyan-700'],
                        ['name' => 'Pop', 'emoji' => '🎤', 'count' => '456 şarkı', 'color' => 'from-emerald-500 to-green-700'],
                        ['name' => 'Bossa Nova', 'emoji' => '🌴', 'count' => '89 şarkı', 'color' => 'from-lime-500 to-green-700'],
                        ['name' => 'Elektronik', 'emoji' => '🎧', 'count' => '321 şarkı', 'color' => 'from-cyan-500 to-teal-700']
                    ];
                @endphp
                @foreach($memberGenres as $genre)
                <a href="/genre/{{ Str::slug($genre['name']) }}" class="group relative h-40 rounded-lg overflow-hidden bg-gradient-to-br {{ $genre['color'] }} hover:scale-105 transition-all shadow-lg">
                    <div class="absolute inset-0 p-4 flex flex-col justify-between">
                        <h4 class="text-2xl font-black text-white">{{ $genre['name'] }}</h4>
                        <div class="text-sm text-white/80">{{ $genre['count'] }}</div>
                    </div>
                    <div class="absolute -bottom-4 -right-4 text-8xl opacity-20">{{ $genre['emoji'] }}</div>
                </a>
                @endforeach
            </div>
        </div>
    </section>
</x-themes.muzibu.layouts.app>

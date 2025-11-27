<!DOCTYPE html>
<html lang="tr" x-data="muzibuApp()" x-init="init()" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Muzibu - Modern Music Platform</title>

    {{-- Tailwind CSS CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        spotify: {
                            green: '#1DB954',
                            'green-light': '#1ed760',
                            black: '#000000',
                            dark: '#121212',
                            gray: '#181818',
                            'gray-light': '#282828',
                            'text-gray': '#b3b3b3',
                        }
                    },
                    animation: {
                        'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                        'float': 'float 6s ease-in-out infinite',
                        'slide-up': 'slideUp 0.3s ease-out',
                        'fade-in': 'fadeIn 0.4s ease-out',
                        'scale-in': 'scaleIn 0.2s ease-out',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0px)' },
                            '50%': { transform: 'translateY(-10px)' },
                        },
                        slideUp: {
                            '0%': { transform: 'translateY(10px)', opacity: '0' },
                            '100%': { transform: 'translateY(0)', opacity: '1' },
                        },
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        },
                        scaleIn: {
                            '0%': { transform: 'scale(0.95)', opacity: '0' },
                            '100%': { transform: 'scale(1)', opacity: '1' },
                        }
                    }
                }
            }
        }
    </script>

    {{-- FontAwesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Audio Libraries --}}
    <script src="https://cdn.jsdelivr.net/npm/hls.js@1.4.12/dist/hls.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/howler@2.2.4/dist/howler.min.js"></script>

    @livewireStyles

    <style>
        [x-cloak] { display: none !important; }

        /* Custom scrollbar with glow */
        ::-webkit-scrollbar { width: 12px; height: 12px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, rgba(29, 185, 84, 0.6), rgba(29, 185, 84, 0.3));
            border-radius: 6px;
            border: 2px solid transparent;
            background-clip: padding-box;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(180deg, rgba(29, 185, 84, 0.8), rgba(29, 185, 84, 0.5));
        }

        /* Smooth transitions - NO TRANSFORM/SCALE */
        * {
            transition-property: background-color, border-color, color, fill, stroke, opacity, box-shadow;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
            transition-duration: 200ms;
        }

        /* Glow effect on hover */
        .glow-on-hover {
            position: relative;
        }
        .glow-on-hover::before {
            content: '';
            position: absolute;
            inset: -2px;
            background: linear-gradient(45deg, #1DB954, #1ed760, #1DB954);
            border-radius: inherit;
            opacity: 0;
            filter: blur(8px);
            transition: opacity 0.3s ease;
            z-index: -1;
        }
        .glow-on-hover:hover::before {
            opacity: 0.7;
        }

        /* Card shine effect */
        .card-shine {
            position: relative;
            overflow: hidden;
        }
        .card-shine::after {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            bottom: -50%;
            left: -50%;
            background: linear-gradient(to bottom, rgba(255,255,255,0) 0%, rgba(255,255,255,0.1) 50%, rgba(255,255,255,0) 100%);
            transform: rotate(45deg) translateX(-100%);
            transition: transform 0.6s;
        }
        .card-shine:hover::after {
            transform: rotate(45deg) translateX(100%);
        }

        /* Gradient animation */
        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .animate-gradient {
            background-size: 200% 200%;
            animation: gradient 4s ease infinite;
        }

        /* Skeleton loading animation */
        @keyframes skeleton-loading {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
        .skeleton {
            background: linear-gradient(
                90deg,
                rgba(255, 255, 255, 0.05) 0%,
                rgba(255, 255, 255, 0.15) 50%,
                rgba(255, 255, 255, 0.05) 100%
            );
            background-size: 200% 100%;
            animation: skeleton-loading 1.5s ease-in-out infinite;
        }

        /* Ripple effect */
        .ripple-container {
            position: relative;
            overflow: hidden;
        }
        .ripple {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.4);
            transform: scale(0);
            animation: ripple-animation 0.6s ease-out;
            pointer-events: none;
        }
        @keyframes ripple-animation {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }
    </style>
</head>
<body class="bg-black text-white overflow-hidden" x-data="{ mobileMenuOpen: false, searchOpen: false }">
    {{-- Hidden Audio Elements --}}
    <audio id="hlsAudio" x-ref="hlsAudio" class="hidden"></audio>
    <audio id="hlsAudioNext" class="hidden"></audio>

    {{-- MAIN APP GRID --}}
    <div class="grid grid-rows-[64px_1fr_90px] xl:grid-cols-[280px_1fr_420px] lg:grid-cols-[280px_1fr] grid-cols-1 h-screen">

        {{-- HEADER --}}
        <header class="xl:col-span-3 lg:col-span-2 col-span-1 bg-black/80 backdrop-blur-md border-b border-white/5 px-6 flex items-center justify-between sticky top-0 z-50">
            <div class="flex items-center gap-6 flex-1">
                {{-- Mobile Hamburger --}}
                <button
                    @click="mobileMenuOpen = !mobileMenuOpen"
                    class="lg:hidden text-spotify-text-gray hover:text-white transition-colors"
                >
                    <i class="fas fa-bars text-xl"></i>
                </button>

                {{-- Logo with animation --}}
                <a href="{{ route('muzibu.home') }}" class="text-2xl font-bold group">
                    <span class="bg-gradient-to-r from-spotify-green via-spotify-green-light to-spotify-green bg-clip-text text-transparent animate-gradient">
                        muzibu
                    </span>
                </a>

                {{-- Search Box - Centered & Modern (Meilisearch) --}}
                <div class="relative flex-1 max-w-3xl mx-auto hidden md:block group">
                    <i class="fas fa-search absolute left-5 top-1/2 -translate-y-1/2 text-zinc-400 group-focus-within:text-white transition-colors text-lg"></i>
                    <input
                        type="text"
                        placeholder="Şarkı, sanatçı, albüm ara..."
                        x-model="searchQuery"
                        @focus="searchOpen = true"
                        class="w-full pl-14 pr-6 py-3.5 bg-white/10 hover:bg-white/15 focus:bg-white/20 border-0 rounded-full text-white placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-white/20 transition-all text-base"
                    >
                </div>
            </div>

            <div class="flex items-center gap-3">
                {{-- Premium Button (non-premium only) --}}
                @auth
                    @if(!isset(auth()->user()->is_premium) || !auth()->user()->is_premium)
                    <a href="#premium" class="hidden lg:flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-yellow-500 to-orange-500 hover:from-yellow-600 hover:to-orange-600 rounded-full text-black font-bold text-sm shadow-lg shadow-yellow-500/30 hover:scale-105 transition-transform">
                        <i class="fas fa-crown"></i>
                        <span>Premium</span>
                    </a>
                    @endif
                @endauth

                {{-- Notification with badge --}}
                @auth
                <button class="relative text-white/70 hover:text-white text-lg transition-colors">
                    <i class="far fa-bell"></i>
                    <span class="absolute -top-1 -right-1 w-2 h-2 bg-red-500 rounded-full animate-pulse"></span>
                </button>
                @endauth

                {{-- User Dropdown --}}
                @auth
                <div class="relative" x-data="{ userMenuOpen: false }">
                    <button @click="userMenuOpen = !userMenuOpen" class="w-10 h-10 bg-gradient-to-br from-green-500 to-emerald-600 hover:from-green-400 hover:to-emerald-500 rounded-full text-black font-bold text-sm transition-all hover:scale-105 shadow-lg">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </button>
                    <div x-show="userMenuOpen"
                         @click.away="userMenuOpen = false"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                         x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                         class="absolute right-0 mt-3 w-64 bg-zinc-900 rounded-xl shadow-2xl border border-white/10 py-2 overflow-hidden z-50"
                         style="display: none;">
                        <div class="px-4 py-3 border-b border-white/10">
                            <p class="text-white font-semibold text-sm">{{ auth()->user()->name }}</p>
                            <p class="text-zinc-400 text-xs">{{ auth()->user()->email }}</p>
                            @if(isset(auth()->user()->is_premium) && auth()->user()->is_premium)
                            <div class="mt-2 inline-flex items-center gap-1 px-2 py-1 bg-gradient-to-r from-yellow-500/20 to-orange-500/20 border border-yellow-500/30 rounded-full">
                                <i class="fas fa-crown text-yellow-400 text-xs"></i>
                                <span class="text-yellow-400 text-xs font-semibold">Premium Üye</span>
                            </div>
                            @endif
                        </div>
                        <a href="#profile" class="flex items-center gap-3 px-4 py-2.5 hover:bg-white/5 text-white text-sm transition-colors">
                            <i class="fas fa-user w-5"></i>
                            <span>Profil</span>
                        </a>
                        <a href="#settings" class="flex items-center gap-3 px-4 py-2.5 hover:bg-white/5 text-white text-sm transition-colors">
                            <i class="fas fa-cog w-5"></i>
                            <span>Ayarlar</span>
                        </a>
                        @if(!isset(auth()->user()->is_premium) || !auth()->user()->is_premium)
                        <a href="#premium" class="flex items-center gap-3 px-4 py-2.5 hover:bg-white/5 text-yellow-400 text-sm transition-colors">
                            <i class="fas fa-crown w-5"></i>
                            <span>Premium'a Geç</span>
                        </a>
                        @endif
                        <div class="h-px bg-white/10 my-1"></div>
                        <a href="#" @click.prevent="logout()" class="flex items-center gap-3 px-4 py-2.5 hover:bg-white/5 text-red-400 text-sm transition-colors">
                            <i class="fas fa-sign-out-alt w-5"></i>
                            <span>Çıkış Yap</span>
                        </a>
                    </div>
                </div>
                @else
                <button @click="showAuthModal = 'login'" class="w-10 h-10 bg-zinc-800 hover:bg-zinc-700 rounded-full flex items-center justify-center text-white/80 transition-colors">
                    <i class="fas fa-user"></i>
                </button>
                @endauth
            </div>
        </header>

        {{-- LEFT SIDEBAR --}}
        <aside
            class="bg-black p-2 overflow-y-auto hidden lg:block animate-slide-up"
            :class="mobileMenuOpen ? 'block fixed inset-0 z-50 lg:relative' : 'hidden lg:block'"
            @click.away="mobileMenuOpen = false"
        >
            <nav class="space-y-1">
                <a href="{{ route('muzibu.home') }}" class="flex items-center gap-4 px-4 py-3 rounded-lg text-white bg-gradient-to-r from-spotify-gray to-spotify-gray-light hover:from-spotify-gray-light hover:to-spotify-gray group transition-all shadow-lg">
                    <i class="fas fa-home w-6 text-lg"></i>
                    <span class="font-semibold">Ana Sayfa</span>
                </a>
                <a href="#" class="flex items-center gap-4 px-4 py-3 rounded-lg text-spotify-text-gray hover:text-white hover:bg-spotify-gray group transition-all">
                    <i class="fas fa-search w-6 text-lg"></i>
                    <span class="font-semibold">Ara</span>
                </a>
                <a href="#" class="flex items-center gap-4 px-4 py-3 rounded-lg text-spotify-text-gray hover:text-white hover:bg-spotify-gray group transition-all">
                    <i class="fas fa-book w-6 text-lg"></i>
                    <span class="font-semibold">Playlistler</span>
                </a>
                <a href="#" class="flex items-center gap-4 px-4 py-3 rounded-lg text-spotify-text-gray hover:text-white hover:bg-spotify-gray group transition-all">
                    <i class="fas fa-compact-disc w-6 text-lg"></i>
                    <span class="font-semibold">Albümler</span>
                </a>
                <a href="#" class="flex items-center gap-4 px-4 py-3 rounded-lg text-spotify-text-gray hover:text-white hover:bg-spotify-gray group transition-all">
                    <i class="fas fa-microphone w-6 text-lg"></i>
                    <span class="font-semibold">Türler</span>
                </a>
            </nav>

            <div class="h-px bg-gradient-to-r from-transparent via-white/10 to-transparent my-4"></div>

            <nav class="space-y-1">
                <a href="#" class="flex items-center gap-4 px-4 py-3 rounded-lg text-spotify-text-gray hover:text-white hover:bg-spotify-gray group transition-all">
                    <i class="fas fa-plus-circle w-6 text-lg group-hover:text-spotify-green"></i>
                    <span class="font-semibold">Playlist Oluştur</span>
                </a>
                <a href="#" class="flex items-center gap-4 px-4 py-3 rounded-lg text-spotify-text-gray hover:text-white hover:bg-spotify-gray group transition-all">
                    <i class="fas fa-heart w-6 text-lg group-hover:text-red-500"></i>
                    <span class="font-semibold">Favoriler</span>
                </a>
            </nav>

            {{-- Premium Card with animation --}}
            @auth
            <div class="mt-4 bg-gradient-to-br from-spotify-green via-spotify-green-light to-spotify-green p-5 rounded-xl shadow-2xl hover:shadow-spotify-green/50 transition-shadow animate-gradient card-shine">
                <h3 class="text-black font-bold mb-1">🌟 {{ auth()->user()->name }}</h3>
                <p class="text-black/80 text-sm mb-3">Premium üyelik</p>
                <button @click="logout()" class="bg-black text-white px-6 py-2 rounded-full text-sm font-bold transition-all shadow-lg hover:shadow-xl">
                    Çıkış Yap
                </button>
            </div>
            @endauth

            {{-- Cache Button with pulse --}}
            <button @click="clearCache()" class="w-full mt-4 bg-spotify-gray hover:bg-red-600/20 rounded-lg px-4 py-3 flex items-center justify-center gap-2 text-spotify-text-gray hover:text-red-400 transition-all group">
                <i class="fas fa-trash group-hover:animate-pulse"></i>
                <span>Cache Temizle</span>
            </button>
        </aside>

        {{-- MAIN CONTENT --}}
        <main class="bg-gradient-to-b from-indigo-900/20 via-spotify-dark to-spotify-dark overflow-y-auto animate-fade-in">
            {{-- SKELETON LOADING STATE --}}
            <div x-show="isLoading" x-cloak class="px-6 py-8 space-y-8">
                {{-- Skeleton Pills --}}
                <div class="flex gap-3">
                    <div class="skeleton w-20 h-10 rounded-full"></div>
                    <div class="skeleton w-24 h-10 rounded-full"></div>
                    <div class="skeleton w-28 h-10 rounded-full"></div>
                </div>

                {{-- Skeleton Cards Grid --}}
                <div class="space-y-6">
                    <div class="skeleton w-48 h-8 rounded"></div>
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
                        <div class="space-y-3" x-data="{ delay: 0 }" :style="`animation-delay: ${delay}ms`">
                            <div class="skeleton w-full aspect-square rounded-lg"></div>
                            <div class="skeleton w-3/4 h-4 rounded"></div>
                            <div class="skeleton w-1/2 h-3 rounded"></div>
                        </div>
                        <div class="space-y-3" x-data="{ delay: 50 }" :style="`animation-delay: ${delay}ms`">
                            <div class="skeleton w-full aspect-square rounded-lg"></div>
                            <div class="skeleton w-3/4 h-4 rounded"></div>
                            <div class="skeleton w-1/2 h-3 rounded"></div>
                        </div>
                        <div class="space-y-3" x-data="{ delay: 100 }" :style="`animation-delay: ${delay}ms`">
                            <div class="skeleton w-full aspect-square rounded-lg"></div>
                            <div class="skeleton w-3/4 h-4 rounded"></div>
                            <div class="skeleton w-1/2 h-3 rounded"></div>
                        </div>
                        <div class="space-y-3" x-data="{ delay: 150 }" :style="`animation-delay: ${delay}ms`">
                            <div class="skeleton w-full aspect-square rounded-lg"></div>
                            <div class="skeleton w-3/4 h-4 rounded"></div>
                            <div class="skeleton w-1/2 h-3 rounded"></div>
                        </div>
                        <div class="space-y-3" x-data="{ delay: 200 }" :style="`animation-delay: ${delay}ms`">
                            <div class="skeleton w-full aspect-square rounded-lg"></div>
                            <div class="skeleton w-3/4 h-4 rounded"></div>
                            <div class="skeleton w-1/2 h-3 rounded"></div>
                        </div>
                    </div>
                </div>

                {{-- Skeleton Song List --}}
                <div class="space-y-4">
                    <div class="skeleton w-40 h-8 rounded"></div>
                    <div class="space-y-2">
                        <div class="flex gap-4 items-center">
                            <div class="skeleton w-10 h-10 rounded"></div>
                            <div class="flex-1 skeleton h-4 rounded"></div>
                            <div class="skeleton w-20 h-4 rounded"></div>
                        </div>
                        <div class="flex gap-4 items-center">
                            <div class="skeleton w-10 h-10 rounded"></div>
                            <div class="flex-1 skeleton h-4 rounded"></div>
                            <div class="skeleton w-20 h-4 rounded"></div>
                        </div>
                        <div class="flex gap-4 items-center">
                            <div class="skeleton w-10 h-10 rounded"></div>
                            <div class="flex-1 skeleton h-4 rounded"></div>
                            <div class="skeleton w-20 h-4 rounded"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ACTUAL CONTENT --}}
            <div x-show="contentLoaded" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
            {{-- Top Pills with enhanced effects --}}
            <div class="px-6 py-4 flex gap-3 overflow-x-auto scrollbar-hide sticky top-0 bg-gradient-to-b from-spotify-dark to-transparent backdrop-blur-sm z-10">
                <button class="px-4 py-2 bg-white text-black rounded-full text-sm font-semibold whitespace-nowrap transition-all shadow-lg hover:shadow-white/30">
                    Tümü
                </button>
                <button class="px-4 py-2 bg-spotify-gray-light text-white rounded-full text-sm font-semibold whitespace-nowrap hover:bg-white hover:text-black transition-all">
                    Müzik
                </button>
                <button class="px-4 py-2 bg-spotify-gray-light text-white rounded-full text-sm font-semibold whitespace-nowrap hover:bg-white hover:text-black transition-all">
                    Podcast'ler
                </button>
            </div>

            {{-- Horizontal Scroll Cards with parallax --}}
            @if(isset($featuredPlaylists) && $featuredPlaylists->count() > 0)
            <div class="px-6 py-4 animate-slide-up">
                <div class="flex gap-4 overflow-x-auto pb-2 scrollbar-thin">
                    @foreach($featuredPlaylists as $index => $playlist)
                    @php
                        $titleJson = @json_decode($playlist->title);
                        $playlistTitle = $titleJson && isset($titleJson->tr) ? $titleJson->tr : $playlist->title;
                        $gradients = [
                            'from-purple-500 via-pink-500 to-red-500',
                            'from-blue-500 via-cyan-500 to-teal-500',
                            'from-orange-500 via-red-500 to-pink-500',
                            'from-green-500 via-emerald-500 to-teal-500',
                            'from-indigo-500 via-purple-500 to-pink-500',
                            'from-yellow-500 via-orange-500 to-red-500',
                        ];
                    @endphp
                    <div
                        class="min-w-[180px] bg-spotify-gray p-4 rounded-lg hover:bg-spotify-gray-light cursor-pointer group transition-all shadow-xl hover:shadow-2xl card-shine animate-scale-in"
                        @click="playPlaylist({{ $playlist->playlist_id }})"
                        style="animation-delay: {{ $index * 50 }}ms"
                    >
                        <div class="relative mb-4">
                            <div class="w-full aspect-square rounded-lg bg-gradient-to-br {{ $gradients[$index % count($gradients)] }} flex items-center justify-center text-5xl animate-gradient shadow-lg group-hover:shadow-2xl transition-shadow">
                                🎵
                            </div>
                            <button class="absolute bottom-2 right-2 w-12 h-12 bg-spotify-green rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 shadow-2xl transition-all glow-on-hover">
                                <i class="fas fa-play text-black ml-0.5"></i>
                            </button>
                        </div>
                        <h3 class="font-bold text-white mb-2 truncate group-hover:text-spotify-green transition-colors">{{ $playlistTitle }}</h3>
                        <p class="text-sm text-spotify-text-gray truncate">Playlist</p>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Albums Section with stagger animation --}}
            @if(isset($newReleases) && $newReleases->count() > 0)
            <div class="px-6 py-8 animate-slide-up" style="animation-delay: 100ms">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-2xl font-bold bg-gradient-to-r from-white to-spotify-text-gray bg-clip-text text-transparent">Yeni Albümler</h2>
                    <a href="/albums" class="text-sm font-bold text-spotify-text-gray hover:text-white hover:underline transition-all">Tümünü göster →</a>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
                    @foreach($newReleases->take(10) as $index => $album)
                    @php
                        $albumTitleJson = @json_decode($album->title);
                        $albumTitle = $albumTitleJson && isset($albumTitleJson->tr) ? $albumTitleJson->tr : $album->title;
                        $artistTitleJson = @json_decode($album->artist_title);
                        $artistTitle = $artistTitleJson && isset($artistTitleJson->tr) ? $artistTitleJson->tr : $album->artist_title;
                    @endphp
                    <div
                        class="bg-spotify-gray p-4 rounded-lg hover:bg-spotify-gray-light cursor-pointer group transition-all shadow-xl hover:shadow-2xl card-shine animate-scale-in"
                        @click="playAlbum({{ $album->album_id }})"
                        style="animation-delay: {{ $index * 50 }}ms"
                    >
                        <div class="relative mb-4">
                            <div class="w-full aspect-square rounded-lg bg-gradient-to-br from-blue-500 via-purple-600 to-pink-600 flex items-center justify-center text-5xl animate-gradient shadow-lg group-hover:shadow-2xl transition-shadow">
                                🎸
                            </div>
                            <button class="absolute bottom-2 right-2 w-12 h-12 bg-spotify-green rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 shadow-2xl transition-all glow-on-hover">
                                <i class="fas fa-play text-black ml-0.5"></i>
                            </button>
                        </div>
                        <h3 class="font-bold text-white mb-2 truncate group-hover:text-spotify-green transition-colors">{{ $albumTitle }}</h3>
                        <p class="text-sm text-spotify-text-gray truncate">{{ $artistTitle }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Popular Songs with row hover --}}
            @if(isset($popularSongs) && $popularSongs->count() > 0)
            <div class="px-6 py-8 animate-slide-up" style="animation-delay: 200ms">
                <h2 class="text-2xl font-bold mb-4 bg-gradient-to-r from-white to-spotify-text-gray bg-clip-text text-transparent">Popüler Şarkılar</h2>
                <div class="bg-spotify-gray/30 rounded-lg overflow-hidden backdrop-blur-sm">
                    @foreach($popularSongs->take(10) as $index => $song)
                    @php
                        $songTitleJson = @json_decode($song->song_title);
                        $songTitle = $songTitleJson && isset($songTitleJson->tr) ? $songTitleJson->tr : $song->song_title;
                        $artistTitleJson = @json_decode($song->artist_title);
                        $songArtist = $artistTitleJson && isset($artistTitleJson->tr) ? $artistTitleJson->tr : $song->artist_title;
                        $albumTitleJson = @json_decode($song->album_title);
                        $songAlbum = $albumTitleJson && isset($albumTitleJson->tr) ? $albumTitleJson->tr : $song->album_title;
                    @endphp
                    <div
                        class="grid grid-cols-[40px_1fr_60px_60px] md:grid-cols-[40px_50px_1fr_200px_60px_60px] gap-4 items-center px-4 py-3 hover:bg-spotify-green/10 cursor-pointer group border-l-2 border-transparent hover:border-spotify-green transition-all"
                    >
                        <div class="text-center text-sm text-spotify-text-gray group-hover:text-spotify-green transition-colors" @click="playSong({ id: {{ $song->song_id }}, title: '{{ addslashes($songTitle) }}' })">
                            <span class="group-hover:hidden">{{ $index + 1 }}</span>
                            <i class="fas fa-play text-spotify-green hidden group-hover:inline animate-pulse"></i>
                        </div>
                        <div class="hidden md:block w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-500 rounded flex items-center justify-center text-xl shadow-lg group-hover:shadow-spotify-green/50 transition-shadow" @click="playSong({ id: {{ $song->song_id }}, title: '{{ addslashes($songTitle) }}' })">🎵</div>
                        <div class="min-w-0" @click="playSong({ id: {{ $song->song_id }}, title: '{{ addslashes($songTitle) }}' })">
                            <h4 class="text-white font-medium truncate group-hover:text-spotify-green transition-colors">{{ $songTitle }}</h4>
                            <p class="text-sm text-spotify-text-gray truncate">{{ $songArtist }}</p>
                        </div>
                        <div class="text-sm text-spotify-text-gray truncate hidden lg:block" @click="playSong({ id: {{ $song->song_id }}, title: '{{ addslashes($songTitle) }}' })">{{ $songAlbum }}</div>
                        <button
                            @click.stop="toggleLike({{ $song->song_id }})"
                            class="text-spotify-text-gray hover:text-spotify-green transition-colors"
                            :class="{ 'text-spotify-green': isLiked({{ $song->song_id }}) }"
                        >
                            <i class="fas fa-heart" :class="{ 'animate-pulse': isLiked({{ $song->song_id }}) }"></i>
                        </button>
                        <div class="text-sm text-spotify-text-gray text-right group-hover:text-white transition-colors">{{ gmdate('i:s', $song->duration ?? 0) }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Genres with 3D effect --}}
            @if(isset($genres) && $genres->count() > 0)
            <div class="px-6 py-8 pb-20 animate-slide-up" style="animation-delay: 300ms">
                <h2 class="text-2xl font-bold mb-4 bg-gradient-to-r from-white to-spotify-text-gray bg-clip-text text-transparent">Türlere Göre Keşfet</h2>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4">
                    @php
                        $genreColors = [
                            'from-pink-500 to-pink-700',
                            'from-purple-500 to-purple-700',
                            'from-blue-500 to-blue-700',
                            'from-cyan-500 to-cyan-700',
                            'from-teal-500 to-teal-700',
                            'from-green-500 to-green-700',
                            'from-orange-500 to-orange-700',
                            'from-red-500 to-red-700',
                        ];
                    @endphp
                    @foreach($genres as $index => $genre)
                    @php
                        $slugJson = @json_decode($genre->slug);
                        $slug = $slugJson && isset($slugJson->tr) ? $slugJson->tr : $genre->slug;
                        $titleJson = @json_decode($genre->title);
                        $title = $titleJson && isset($titleJson->tr) ? $titleJson->tr : $genre->title;
                    @endphp
                    <a
                        href="/genres/{{ $slug }}"
                        class="h-32 bg-gradient-to-br {{ $genreColors[$index % count($genreColors)] }} rounded-lg p-4 flex items-end cursor-pointer shadow-2xl hover:shadow-spotify-green/30 transition-all relative overflow-hidden group card-shine animate-scale-in"
                        style="animation-delay: {{ $index * 30 }}ms"
                    >
                        <div class="absolute inset-0 bg-black/20 group-hover:bg-black/0 transition-colors"></div>
                        <h3 class="text-xl font-bold text-white relative z-10">{{ $title }}</h3>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif
            </div>
            {{-- END ACTUAL CONTENT --}}
        </main>

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

        {{-- PLAYER BAR - Keep existing for now --}}
        <div class="xl:col-span-3 lg:col-span-2 col-span-1 bg-spotify-gray/95 backdrop-blur-md grid grid-cols-[1fr_2fr_1fr] items-center px-4 py-3 gap-4 border-t border-white/10 shadow-2xl">
            {{-- Song Info --}}
            <div class="flex items-center gap-3">
                <div class="w-14 h-14 bg-gradient-to-br from-pink-500 to-purple-600 rounded flex items-center justify-center text-2xl flex-shrink-0 shadow-lg">
                    <template x-if="currentSong && currentSong.album_cover">
                        <img :src="currentSong.album_cover" :alt="currentSong.title" class="w-full h-full rounded object-cover">
                    </template>
                    <template x-if="!currentSong || !currentSong.album_cover">
                        <span>🎵</span>
                    </template>
                </div>
                <div class="min-w-0 hidden sm:block">
                    <h4 class="text-sm font-semibold text-white truncate" x-text="currentSong ? currentSong.title : 'Şarkı seç'"></h4>
                    <p class="text-xs text-spotify-text-gray truncate" x-text="currentSong ? currentSong.artist_name : 'Sanatçı'"></p>
                </div>
                <button class="text-spotify-text-gray hover:text-spotify-green ml-auto  transition-all" @click="toggleFavorite('song', currentSong?.song_id)">
                    <i :class="isLiked ? 'fas fa-heart text-spotify-green animate-pulse' : 'far fa-heart'"></i>
                </button>
            </div>

            {{-- Player Controls --}}
            <div class="flex flex-col gap-2">
                <div class="flex items-center justify-center gap-4">
                    <button class="text-spotify-text-gray hover:text-white  transition-all" :class="shuffle ? 'text-spotify-green' : ''" @click="toggleShuffle()">
                        <i class="fas fa-random"></i>
                    </button>
                    <button class="text-spotify-text-gray hover:text-white  transition-all" @click="previousTrack()">
                        <i class="fas fa-step-backward"></i>
                    </button>
                    <button class="w-8 h-8 bg-white rounded-full flex items-center justify-center text-black transition-all shadow-lg hover:shadow-white/50" @click="togglePlayPause()">
                        <i :class="isPlaying ? 'fas fa-pause' : 'fas fa-play ml-0.5'"></i>
                    </button>
                    <button class="text-spotify-text-gray hover:text-white  transition-all" @click="nextTrack()">
                        <i class="fas fa-step-forward"></i>
                    </button>
                    <button class="text-spotify-text-gray hover:text-white  transition-all" :class="repeatMode !== 'off' ? 'text-spotify-green' : ''" @click="cycleRepeat()">
                        <i class="fas fa-redo"></i>
                    </button>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs text-spotify-text-gray w-10 text-right" x-text="formatTime(currentTime)">0:00</span>
                    <div class="flex-1 h-1 bg-spotify-text-gray/30 rounded-full cursor-pointer group" @click="seekTo($event)">
                        <div class="h-full bg-white rounded-full relative group-hover:bg-spotify-green transition-colors" :style="`width: ${progressPercent}%`">
                            <div class="absolute right-0 top-1/2 -translate-y-1/2 w-3 h-3 bg-white rounded-full opacity-0 group-hover:opacity-100 shadow-lg"></div>
                        </div>
                    </div>
                    <span class="text-xs text-spotify-text-gray w-10" x-text="formatTime(duration)">0:00</span>
                </div>
            </div>

            {{-- Volume Controls --}}
            <div class="flex items-center justify-end gap-2">
                <button class="text-spotify-text-gray hover:text-white hidden lg:block  transition-all">
                    <i class="fas fa-hdd"></i>
                </button>
                <button class="text-spotify-text-gray hover:text-white  transition-all" @click="showQueue = !showQueue">
                    <i class="fas fa-list"></i>
                </button>
                <button class="text-spotify-text-gray hover:text-white  transition-all" @click="toggleMute()">
                    <i :class="isMuted ? 'fas fa-volume-mute' : (volume > 50 ? 'fas fa-volume-up' : 'fas fa-volume-down')"></i>
                </button>
                <div class="w-20 h-1 bg-spotify-text-gray/30 rounded-full cursor-pointer group hidden md:block" @click="setVolume($event)">
                    <div class="h-full bg-white rounded-full group-hover:bg-spotify-green transition-colors" :style="`width: ${volume}%`"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Toast Notification --}}
    <div
        x-show="toast.show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-4"
        x-cloak
        class="fixed bottom-24 right-6 z-[100] max-w-sm"
    >
        <div
            class="flex items-center gap-3 px-6 py-4 rounded-xl shadow-2xl backdrop-blur-md"
            :class="{
                'bg-spotify-green/90 text-white': toast.type === 'success',
                'bg-blue-500/90 text-white': toast.type === 'info',
                'bg-red-500/90 text-white': toast.type === 'error',
                'bg-yellow-500/90 text-black': toast.type === 'warning'
            }"
        >
            <i class="text-xl" :class="{
                'fas fa-check-circle': toast.type === 'success',
                'fas fa-info-circle': toast.type === 'info',
                'fas fa-exclamation-circle': toast.type === 'error',
                'fas fa-exclamation-triangle': toast.type === 'warning'
            }"></i>
            <p class="font-medium" x-text="toast.message"></p>
            <button @click="toast.show = false" class="ml-auto transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>

    {{-- Player JavaScript --}}
    <script src="{{ asset('themes/muzibu/js/player/spotify-player.js') }}?v={{ time() }}"></script>

    <script>
        window.muzibuPlayerConfig = {
            lang: @json(tenant_lang('player')),
            frontLang: @json(tenant_lang('front')),
            isLoggedIn: {{ auth()->check() ? 'true' : 'false' }},
            currentUser: @json(auth()->user()),
            tenantId: {{ tenant('id') }}
        };

        // Alpine.js App Data
        function muzibuApp() {
            return {
                // Loading states
                isLoading: true,
                contentLoaded: false,

                // Search
                searchQuery: '',
                searchResults: [],
                searchOpen: false,

                // Player states
                currentSong: null,
                isPlaying: false,
                isMuted: false,
                volume: 80,
                currentTime: 0,
                duration: 0,
                showQueue: false,
                queue: [],

                // User interactions
                likedSongs: [],
                recentlyPlayed: [],

                // UI states
                activeTab: 'all',
                showNotifications: false,
                notifications: [],
                toast: { show: false, message: '', type: '' },

                // Initialize
                init() {
                    // Simulate content loading
                    setTimeout(() => {
                        this.isLoading = false;
                        this.contentLoaded = true;
                    }, 500);

                    // Load liked songs from localStorage
                    this.loadLikedSongs();

                    // Listen for keyboard shortcuts
                    this.setupKeyboardShortcuts();
                },

                // Format time helper
                formatTime(seconds) {
                    if (!seconds || isNaN(seconds)) return '0:00';
                    const mins = Math.floor(seconds / 60);
                    const secs = Math.floor(seconds % 60);
                    return `${mins}:${secs.toString().padStart(2, '0')}`;
                },

                // Like/Unlike song
                toggleLike(songId) {
                    const index = this.likedSongs.indexOf(songId);
                    if (index > -1) {
                        this.likedSongs.splice(index, 1);
                        this.showToast('Beğenilerden kaldırıldı', 'info');
                    } else {
                        this.likedSongs.push(songId);
                        this.showToast('Beğenilenlere eklendi!', 'success');
                    }
                    this.saveLikedSongs();
                },

                // Check if song is liked
                isLiked(songId) {
                    return this.likedSongs.includes(songId);
                },

                // Save liked songs to localStorage
                saveLikedSongs() {
                    localStorage.setItem('muzibu_liked_songs', JSON.stringify(this.likedSongs));
                },

                // Load liked songs from localStorage
                loadLikedSongs() {
                    const saved = localStorage.getItem('muzibu_liked_songs');
                    if (saved) {
                        this.likedSongs = JSON.parse(saved);
                    }
                },

                // Play song
                playSong(song) {
                    this.currentSong = song;
                    this.isPlaying = true;
                    this.showToast(`Oynatılıyor: ${song.title}`, 'success');
                },

                // Play playlist
                playPlaylist(playlistId) {
                    this.showToast('Playlist oynatılıyor...', 'success');
                    console.log('Playing playlist:', playlistId);
                },

                // Play album
                playAlbum(albumId) {
                    this.showToast('Albüm oynatılıyor...', 'success');
                    console.log('Playing album:', albumId);
                },

                // Toggle play/pause
                togglePlay() {
                    this.isPlaying = !this.isPlaying;
                },

                // Toggle mute
                toggleMute() {
                    this.isMuted = !this.isMuted;
                },

                // Set volume
                setVolume(event) {
                    const rect = event.target.getBoundingClientRect();
                    const x = event.clientX - rect.left;
                    this.volume = Math.round((x / rect.width) * 100);
                    this.isMuted = false;
                },

                // Set progress
                setProgress(event) {
                    const rect = event.target.getBoundingClientRect();
                    const x = event.clientX - rect.left;
                    const percent = x / rect.width;
                    this.currentTime = Math.floor(this.duration * percent);
                },

                // Add to queue
                addToQueue(song) {
                    this.queue.push(song);
                    this.showToast('Kuyruğa eklendi', 'success');
                },

                // Show toast notification
                showToast(message, type = 'info') {
                    this.toast = { show: true, message, type };
                    setTimeout(() => {
                        this.toast.show = false;
                    }, 3000);
                },

                // Search functionality
                performSearch() {
                    if (this.searchQuery.length < 2) {
                        this.searchResults = [];
                        return;
                    }
                    // Simulate search (bu kısmı backend'e bağlayacaksın)
                    console.log('Searching for:', this.searchQuery);
                },

                // Keyboard shortcuts
                setupKeyboardShortcuts() {
                    document.addEventListener('keydown', (e) => {
                        // Space: play/pause
                        if (e.code === 'Space' && e.target.tagName !== 'INPUT') {
                            e.preventDefault();
                            this.togglePlay();
                        }
                        // M: mute
                        if (e.code === 'KeyM' && e.ctrlKey) {
                            e.preventDefault();
                            this.toggleMute();
                        }
                    });
                },

                // Logout
                logout() {
                    if (confirm('Çıkış yapmak istediğinize emin misiniz?')) {
                        window.location.href = '/logout';
                    }
                },

                // Clear cache
                clearCache() {
                    this.showToast('Cache temizleniyor...', 'info');
                    fetch('/api/clear-cache', { method: 'POST' })
                        .then(() => {
                            this.showToast('Cache başarıyla temizlendi!', 'success');
                            setTimeout(() => window.location.reload(), 1000);
                        })
                        .catch(() => {
                            this.showToast('Cache temizlenirken hata oluştu', 'error');
                        });
                },

                // Ripple effect
                createRipple(event) {
                    const button = event.currentTarget;
                    const ripple = document.createElement('span');
                    const rect = button.getBoundingClientRect();
                    const size = Math.max(rect.width, rect.height);
                    const x = event.clientX - rect.left - size / 2;
                    const y = event.clientY - rect.top - size / 2;

                    ripple.style.width = ripple.style.height = size + 'px';
                    ripple.style.left = x + 'px';
                    ripple.style.top = y + 'px';
                    ripple.classList.add('ripple');

                    const existingRipple = button.querySelector('.ripple');
                    if (existingRipple) {
                        existingRipple.remove();
                    }

                    button.appendChild(ripple);
                    setTimeout(() => ripple.remove(), 600);
                }
            }
        }
    </script>

    @livewireScripts
</body>
</html>

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

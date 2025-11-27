<header class="muzibu-header">
    <div class="muzibu-header-left">
        {{-- Hamburger Menu (Mobile) --}}
        <button class="muzibu-hamburger" onclick="toggleMobileMenu()">
            <i class="fas fa-bars"></i>
        </button>

        {{-- Logo --}}
        <a href="{{ route('muzibu.home') }}" class="muzibu-logo">
            muzibu
        </a>

        {{-- Modern Search Box (Centered) --}}
        <div class="muzibu-search muzibu-search-modern">
            <i class="fas fa-search"></i>
            <input type="text"
                   placeholder="Şarkı, sanatçı, albüm ara..."
                   x-model="searchQuery"
                   @focus="searchOpen = true"
                   @click.away="searchOpen = false">
        </div>
    </div>

    <div class="muzibu-header-right">
        {{-- Premium Button (non-premium only) --}}
        @auth
            @if(!auth()->user()->is_premium)
            <a href="#premium" class="muzibu-premium-button">
                <i class="fas fa-crown"></i>
                <span>Premium</span>
            </a>
            @endif
        @endauth

        {{-- Notification Button --}}
        @auth
        <button class="muzibu-notification-btn">
            <i class="far fa-bell"></i>
        </button>
        @endauth

        {{-- User Dropdown --}}
        @auth
            <div class="muzibu-user-dropdown" x-data="{ userMenuOpen: false }">
                <button @click="userMenuOpen = !userMenuOpen" class="muzibu-profile-btn">
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
                     class="muzibu-dropdown-menu"
                     style="display: none;">
                    {{-- User Info --}}
                    <div class="muzibu-dropdown-header">
                        <p class="text-white font-semibold text-sm">{{ auth()->user()->name }}</p>
                        <p class="text-zinc-400 text-xs">{{ auth()->user()->email }}</p>
                        @if(auth()->user()->is_premium)
                        <div class="mt-2 inline-flex items-center gap-1 px-2 py-1 bg-gradient-to-r from-yellow-500/20 to-orange-500/20 border border-yellow-500/30 rounded-full">
                            <i class="fas fa-crown text-yellow-400 text-xs"></i>
                            <span class="text-yellow-400 text-xs font-semibold">Premium Üye</span>
                        </div>
                        @endif
                    </div>
                    {{-- Menu Items --}}
                    <a href="#profile" class="muzibu-dropdown-item">
                        <i class="fas fa-user"></i>
                        <span>Profil</span>
                    </a>
                    <a href="#settings" class="muzibu-dropdown-item">
                        <i class="fas fa-cog"></i>
                        <span>Ayarlar</span>
                    </a>
                    @if(!auth()->user()->is_premium)
                    <a href="#premium" class="muzibu-dropdown-item" style="color: #fbbf24;">
                        <i class="fas fa-crown"></i>
                        <span>Premium'a Geç</span>
                    </a>
                    @endif
                    <div class="muzibu-dropdown-divider"></div>
                    <a href="#" @click.prevent="logout()" class="muzibu-dropdown-item" style="color: #f87171;">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Çıkış Yap</span>
                    </a>
                </div>
            </div>
        @else
            <button @click="showAuthModal = 'login'" class="muzibu-profile-btn">
                <i class="fas fa-user"></i>
            </button>
        @endauth
    </div>
</header>

<aside class="muzibu-left-sidebar" id="leftSidebar">
    {{-- Navigation Items --}}
    <a href="{{ route('muzibu.home') }}" class="muzibu-nav-item active">
        <i class="fas fa-home"></i>
        <span>Ana Sayfa</span>
    </a>
    <a href="#" class="muzibu-nav-item">
        <i class="fas fa-search"></i>
        <span>Ara</span>
    </a>
    <a href="#" class="muzibu-nav-item">
        <i class="fas fa-book"></i>
        <span>Playlistler</span>
    </a>
    <a href="#" class="muzibu-nav-item">
        <i class="fas fa-compact-disc"></i>
        <span>Albümler</span>
    </a>
    <a href="#" class="muzibu-nav-item">
        <i class="fas fa-microphone"></i>
        <span>Türler</span>
    </a>
    <a href="#" class="muzibu-nav-item">
        <i class="fas fa-building"></i>
        <span>Sektörler</span>
    </a>

    <div class="muzibu-divider"></div>

    <a href="#" class="muzibu-nav-item">
        <i class="fas fa-plus-circle"></i>
        <span>Playlist Oluştur</span>
    </a>
    <a href="#" class="muzibu-nav-item">
        <i class="fas fa-heart"></i>
        <span>Favoriler</span>
    </a>

    {{-- Premium Card / User Info --}}
    @auth
    <div class="muzibu-premium-card">
        <h3>🌟 {{ auth()->user()->name }}</h3>
        <p>{{ auth()->user()->subscription_tier ?? 'Free' }} üyelik</p>
        <button class="muzibu-premium-btn" @click="logout()">
            Çıkış Yap
        </button>
    </div>
    @else
    <div class="muzibu-premium-card">
        <h3>🎵 Müziğin Keyfini Çıkar</h3>
        <p>Ücretsiz hesap oluştur</p>
        <button class="muzibu-premium-btn" @click="showAuthModal = 'register'">
            Kayıt Ol
        </button>
    </div>
    @endauth

    {{-- Cache Button --}}
    <button class="muzibu-cache-btn" @click="clearCache()">
        <i class="fas fa-trash"></i>
        <span>Cache</span>
    </button>
</aside>

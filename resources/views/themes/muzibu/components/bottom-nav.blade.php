<nav class="muzibu-bottom-nav">
    <div class="muzibu-bottom-nav-items">
        <a href="{{ route('muzibu.home') }}"
           @click="if (window.Alpine?.store('sidebar')) window.Alpine.store('sidebar').reset()"
           class="muzibu-bottom-nav-item {{ request()->routeIs('muzibu.home') ? 'active' : '' }}">
            <i class="fas fa-home"></i>
            <span>Ana Sayfa</span>
        </a>
        <a href="/search" class="muzibu-bottom-nav-item {{ request()->is('search*') ? 'active' : '' }}">
            <i class="fas fa-search"></i>
            <span>Ara</span>
        </a>
        <button onclick="toggleMobileMenu()" class="muzibu-bottom-nav-item">
            <i class="fas fa-bars"></i>
            <span>Menü</span>
        </button>
    </div>
</nav>

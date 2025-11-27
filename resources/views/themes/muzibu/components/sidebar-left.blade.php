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

<!DOCTYPE html>
<html lang="tr" x-data="{ darkMode: localStorage.getItem('darkMode') || 'light' }" x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))" :class="{ 'dark': darkMode === 'dark' }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>v2: Modern Grid + Carousel - İxtif Akademi</title>

    <script>if(localStorage.getItem('darkMode')==='dark')document.documentElement.classList.add('dark')</script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.5.1/css/all.css">

    <style>
        body { font-family: 'Inter', system-ui, sans-serif; }
        @keyframes float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-10px); } }
        .float-animation { animation: float 6s ease-in-out infinite; }
        @keyframes fade-in { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
        .fade-enter { animation: fade-in 0.6s ease-out; }

        /* Masonry Grid */
        .masonry-grid { column-count: 3; column-gap: 1.5rem; }
        .masonry-item { break-inside: avoid; margin-bottom: 1.5rem; }
        @media (max-width: 1024px) { .masonry-grid { column-count: 2; } }
        @media (max-width: 640px) { .masonry-grid { column-count: 1; } }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-50 to-blue-50 dark:from-gray-900 dark:to-slate-900 min-h-screen"
      x-data="blogLayoutV2()" x-init="init()">

    {{-- HEADER PLACEHOLDER --}}
    <header class="bg-white/90 dark:bg-gray-900/90 backdrop-blur-xl border-b border-gray-200 dark:border-gray-700 sticky top-0 z-50 shadow-lg">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <h1 class="text-2xl font-black bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">İxtif Akademi</h1>
            <button @click="darkMode = darkMode === 'dark' ? 'light' : 'dark'"
                    class="p-3 rounded-xl bg-gradient-to-r from-blue-600 to-purple-600 text-white hover:shadow-lg transition">
                <i class="fas fa-moon dark:hidden"></i>
                <i class="fas fa-sun hidden dark:block"></i>
            </button>
        </div>
    </header>

    {{-- MAIN BLOG SECTION - MODERN GRID + CAROUSEL --}}
    <section class="container mx-auto px-4 py-12">

        {{-- TOP: CAROUSEL - Otomatik Dönen --}}
        <div class="mb-12">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-4xl font-black text-gray-900 dark:text-white flex items-center gap-3">
                    <i class="fas fa-bolt text-yellow-500"></i>
                    Şu An Trend
                </h2>
                <a href="/blog" class="px-6 py-3 bg-gradient-to-r from-yellow-500 to-orange-600 text-white rounded-2xl font-bold hover:shadow-2xl transition-all flex items-center gap-2">
                    Tümünü Gör <i class="fas fa-arrow-right"></i>
                </a>
            </div>

            {{-- Carousel Container --}}
            <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-yellow-500 via-orange-500 to-red-600 p-1 shadow-2xl">
                <div class="bg-white dark:bg-gray-900 rounded-3xl overflow-hidden">
                    <div class="flex transition-transform duration-700 ease-in-out" :style="'transform: translateX(-' + (carouselIndex * 100) + '%)'">
                        <template x-for="blog in carousel" :key="blog.id">
                            <div class="w-full flex-shrink-0">
                                <div class="relative h-96 flex items-end" :style="'background: linear-gradient(to bottom, transparent, rgba(0,0,0,0.8)), url(' + blog.image + '); background-size: cover; background-position: center;'">
                                    <div class="w-full p-8 text-white">
                                        <div class="flex gap-3 mb-4">
                                            <span class="px-4 py-2 bg-gradient-to-r from-yellow-500 to-orange-600 rounded-full text-sm font-black uppercase tracking-wide">
                                                <i class="fas fa-crown"></i> TOP HABER
                                            </span>
                                            <span class="px-4 py-2 bg-white/20 backdrop-blur-md rounded-full text-sm font-bold" x-text="blog.timeAgo"></span>
                                        </div>
                                        <h3 class="text-4xl font-black mb-3 leading-tight" x-text="blog.title"></h3>
                                        <p class="text-xl text-gray-200 mb-5" x-text="blog.excerpt"></p>
                                        <div class="flex items-center gap-6">
                                            <span class="text-lg"><i class="fas fa-fire text-orange-500"></i> <span x-text="blog.views"></span> görüntülenme</span>
                                            <span class="text-lg"><i class="fas fa-comments text-blue-400"></i> <span x-text="blog.comments"></span> yorum</span>
                                            <a :href="blog.url" class="ml-auto px-8 py-4 bg-white text-gray-900 rounded-2xl font-black hover:bg-gray-100 transition text-lg">
                                                Oku <i class="fas fa-arrow-right ml-2"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    {{-- Carousel Dots --}}
                    <div class="absolute bottom-6 left-1/2 -translate-x-1/2 flex gap-3 z-10">
                        <template x-for="(blog, index) in carousel" :key="index">
                            <button @click="carouselIndex = index; pauseCarousel()"
                                    class="transition-all rounded-full"
                                    :class="carouselIndex === index ? 'w-8 h-3 bg-white' : 'w-3 h-3 bg-white/50'"></button>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        {{-- MIDDLE: MASONRY GRID - Pinterest Style --}}
        <div class="mb-12">
            <h3 class="text-3xl font-black text-gray-900 dark:text-white mb-8 flex items-center gap-3">
                <i class="fas fa-grip text-blue-600"></i>
                Son Eklenenler
            </h3>

            <div class="masonry-grid">
                <template x-for="blog in masonryBlogs" :key="blog.id">
                    <div class="masonry-item fade-enter">
                        <a :href="blog.url" class="group block bg-white dark:bg-gray-800 rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all border-2 border-transparent hover:border-blue-500">
                            {{-- Image --}}
                            <div class="relative overflow-hidden" :class="blog.height">
                                <img :src="blog.image" :alt="blog.title" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                                <div class="absolute top-3 left-3">
                                    <span class="px-3 py-1.5 bg-white/90 backdrop-blur-md rounded-lg text-xs font-bold text-gray-900" x-text="blog.timeAgo"></span>
                                </div>
                            </div>

                            {{-- Content --}}
                            <div class="p-5">
                                <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-2 line-clamp-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition" x-text="blog.title"></h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2 mb-4" x-text="blog.excerpt"></p>
                                <div class="flex items-center justify-between text-sm">
                                    <div class="flex items-center gap-3 text-gray-500">
                                        <span><i class="fas fa-eye"></i> <span x-text="blog.views"></span></span>
                                        <span><i class="fas fa-heart"></i> <span x-text="blog.likes"></span></span>
                                    </div>
                                    <span class="text-blue-600 dark:text-blue-400 font-bold">Oku →</span>
                                </div>
                            </div>
                        </a>
                    </div>
                </template>
            </div>
        </div>

        {{-- BOTTOM: COMPACT LIST - Çok Küçük Başlıklar --}}
        <div class="bg-gradient-to-br from-gray-900 to-blue-900 dark:from-gray-800 dark:to-blue-800 rounded-3xl p-8 shadow-2xl">
            <h3 class="text-3xl font-black text-white mb-6 flex items-center gap-3">
                <i class="fas fa-list text-yellow-400"></i>
                Diğer Haberler (24 İçerik)
            </h3>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-3">
                <template x-for="blog in compactList" :key="blog.id">
                    <a :href="blog.url" class="group flex items-center gap-3 bg-white/10 backdrop-blur-md hover:bg-white/20 rounded-xl p-3 transition-all">
                        <div class="flex-shrink-0 w-12 h-12 rounded-lg bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
                            <i class="fas fa-newspaper text-white text-lg"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-white line-clamp-1 group-hover:text-yellow-300 transition" x-text="blog.title"></p>
                            <p class="text-xs text-gray-400" x-text="blog.timeAgo"></p>
                        </div>
                    </a>
                </template>
            </div>
        </div>

    </section>

    {{-- FOOTER PLACEHOLDER --}}
    <footer class="bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 py-8 mt-20">
        <div class="container mx-auto px-4 text-center text-gray-600 dark:text-gray-400">
            <p>© 2025 İxtif Akademi - v2: Modern Grid + Carousel</p>
        </div>
    </footer>

    <script>
        function blogLayoutV2() {
            return {
                carouselIndex: 0,
                carouselInterval: null,

                // 3 carousel item (otomatik döner)
                carousel: [
                    { id: 1, title: 'Forklift Sektöründe Yeni Dönem Başlıyor', excerpt: 'Elektrikli forkliftler pazarın %70\'ini ele geçirdi. İşte detaylar...', image: 'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?w=1200', url: '#', timeAgo: '30 dk önce', views: '3.2K', comments: '48' },
                    { id: 2, title: 'Reach Truck Fiyatlarında Büyük Düşüş', excerpt: 'İkinci el reach truck fiyatları son 3 ayda %15 düştü. Uzmanlar yorumluyor...', image: 'https://images.unsplash.com/photo-1601366092959-4f06770cc456?w=1200', url: '#', timeAgo: '1 saat önce', views: '2.8K', comments: '35' },
                    { id: 3, title: 'Depo Otomasyon Sistemleri Türkiye\'de', excerpt: 'Otomasyon sistemleri depo verimliliğini 3 kat artırıyor. Başarı hikayeleri...', image: 'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?w=1200', url: '#', timeAgo: '2 saat önce', views: '4.1K', comments: '62' }
                ],

                // 12 masonry grid item (farklı yükseklikler)
                masonryBlogs: [
                    { id: 4, title: 'Forklift Bakım Rehberi', excerpt: 'Periyodik bakım ile maliyetlerinizi %40 azaltın.', image: 'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?w=400', url: '#', timeAgo: '3 saat önce', views: '1.2K', likes: '89', height: 'h-48' },
                    { id: 5, title: 'Transpalet Seçim Kriterleri', excerpt: 'Manuel mi elektrikli mi? Doğru seçim nasıl yapılır?', image: 'https://images.unsplash.com/photo-1601366092959-4f06770cc456?w=400', url: '#', timeAgo: '4 saat önce', views: '856', likes: '62', height: 'h-64' },
                    { id: 6, title: 'Yeni Nesil Akülü Forklift', excerpt: 'Lityum-iyon batarya teknolojisi ile 8 saat kesintisiz çalışma.', image: 'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?w=400', url: '#', timeAgo: '5 saat önce', views: '643', likes: '41', height: 'h-56' },
                    { id: 7, title: 'İş Güvenliği ve Forklift', excerpt: 'Operatör eğitimi neden bu kadar önemli?', image: 'https://images.unsplash.com/photo-1601366092959-4f06770cc456?w=400', url: '#', timeAgo: '6 saat önce', views: '912', likes: '73', height: 'h-48' },
                    { id: 8, title: 'Forklift Kiralama Avantajları', excerpt: 'Kısa süreli projeler için en ekonomik çözüm.', image: 'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?w=400', url: '#', timeAgo: '7 saat önce', views: '1.3K', likes: '95', height: 'h-60' },
                    { id: 9, title: 'Soğuk Hava Deposu Ekipmanları', excerpt: '-25°C\'de çalışan özel forklift modelleri.', image: 'https://images.unsplash.com/photo-1601366092959-4f06770cc456?w=400', url: '#', timeAgo: '8 saat önce', views: '721', likes: '58', height: 'h-52' },
                    { id: 10, title: 'Forklift Lastik Teknolojisi', excerpt: 'Dolgu lastik mi pnömatik mi?', image: 'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?w=400', url: '#', timeAgo: '9 saat önce', views: '534', likes: '37', height: 'h-48' },
                    { id: 11, title: 'Depo Optimizasyon İpuçları', excerpt: 'Raf sistemleri ile alan kullanımını 2 katına çıkarın.', image: 'https://images.unsplash.com/photo-1601366092959-4f06770cc456?w=400', url: '#', timeAgo: '10 saat önce', views: '892', likes: '71', height: 'h-64' },
                    { id: 12, title: 'Forklift Satış Sonrası Hizmetler', excerpt: 'Yetkili servis garantisi neden önemli?', image: 'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?w=400', url: '#', timeAgo: '11 saat önce', views: '678', likes: '49', height: 'h-56' },
                    { id: 13, title: 'LPG Forklift Ekonomik Analiz', excerpt: 'Yakıt maliyetleri karşılaştırması.', image: 'https://images.unsplash.com/photo-1601366092959-4f06770cc456?w=400', url: '#', timeAgo: '12 saat önce', views: '1.1K', likes: '82', height: 'h-48' },
                    { id: 14, title: 'Yüksek Kaldırma Sistemleri', excerpt: '12 metreye kadar istif kapasitesi.', image: 'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?w=400', url: '#', timeAgo: '13 saat önce', views: '512', likes: '34', height: 'h-60' },
                    { id: 15, title: 'Forklift Sigorta Rehberi', excerpt: 'Kasko ve trafik sigortası zorunlu mu?', image: 'https://images.unsplash.com/photo-1601366092959-4f06770cc456?w=400', url: '#', timeAgo: '14 saat önce', views: '734', likes: '56', height: 'h-52' }
                ],

                // 12 compact list item
                compactList: [
                    { id: 16, title: 'Forklift Operatör Maaşları 2025', url: '#', timeAgo: '15 saat önce' },
                    { id: 17, title: 'Sıfır vs İkinci El Forklift', url: '#', timeAgo: '16 saat önce' },
                    { id: 18, title: 'Reach Truck Dar Koridor Kullanımı', url: '#', timeAgo: '17 saat önce' },
                    { id: 19, title: 'Forklift Periyodik Muayene', url: '#', timeAgo: '18 saat önce' },
                    { id: 20, title: 'Elektrikli Transpalet Şarj Süreleri', url: '#', timeAgo: '19 saat önce' },
                    { id: 21, title: 'Forklift Yedek Parça Stok Yönetimi', url: '#', timeAgo: '20 saat önce' },
                    { id: 22, title: 'Depo Güvenlik Kamera Sistemleri', url: '#', timeAgo: '21 saat önce' },
                    { id: 23, title: 'Forklift Filo Yönetim Yazılımı', url: '#', timeAgo: '22 saat önce' },
                    { id: 24, title: 'İstif Makinesi Hidrolik Sistem', url: '#', timeAgo: '23 saat önce' },
                    { id: 25, title: 'Forklift Kullanım Ömrü', url: '#', timeAgo: '1 gün önce' },
                    { id: 26, title: 'Otomasyon ve Forklift Entegrasyonu', url: '#', timeAgo: '1 gün önce' },
                    { id: 27, title: 'Forklift Finansman Seçenekleri', url: '#', timeAgo: '1 gün önce' }
                ],

                init() {
                    this.startCarousel();
                },

                startCarousel() {
                    this.carouselInterval = setInterval(() => {
                        this.carouselIndex = (this.carouselIndex + 1) % this.carousel.length;
                    }, 5000);
                },

                pauseCarousel() {
                    clearInterval(this.carouselInterval);
                    setTimeout(() => this.startCarousel(), 10000);
                }
            }
        }
    </script>

</body>
</html>

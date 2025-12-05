<!DOCTYPE html>
<html lang="tr" x-data="{ darkMode: localStorage.getItem('darkMode') || 'light' }" x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))" :class="{ 'dark': darkMode === 'dark' }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>v1: News Magazine Style - İxtif Akademi</title>

    <script>if(localStorage.getItem('darkMode')==='dark')document.documentElement.classList.add('dark')</script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.5.1/css/all.css">

    <style>
        body { font-family: 'Inter', system-ui, sans-serif; }
        @keyframes slide-in { from { opacity: 0; transform: translateX(20px); } to { opacity: 1; transform: translateX(0); } }
        .slide-enter { animation: slide-in 0.5s ease-out; }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800 min-h-screen"
      x-data="blogLayoutV1()" x-init="init()">

    {{-- HEADER PLACEHOLDER --}}
    <header class="bg-white/80 dark:bg-gray-900/80 backdrop-blur-lg border-b border-gray-200 dark:border-gray-700 sticky top-0 z-50">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <h1 class="text-2xl font-black text-gray-900 dark:text-white">İxtif Akademi</h1>
            <button @click="darkMode = darkMode === 'dark' ? 'light' : 'dark'"
                    class="p-2 rounded-lg bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                <i class="fas fa-moon dark:hidden"></i>
                <i class="fas fa-sun hidden dark:block"></i>
            </button>
        </div>
    </header>

    {{-- MAIN BLOG SECTION - NEWS MAGAZINE STYLE --}}
    <section class="container mx-auto px-4 py-12">

        {{-- Section Header --}}
        <div class="flex justify-between items-center mb-8">
            <div>
                <h2 class="text-4xl font-black text-gray-900 dark:text-white mb-2">
                    Son Haberler
                </h2>
                <p class="text-gray-600 dark:text-gray-400">Günde 24 yeni içerik ekleniyor</p>
            </div>
            <a href="/blog" class="px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl font-semibold hover:shadow-lg transition-all flex items-center gap-2">
                Tümünü Gör
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>

        {{-- MAIN GRID: Slider (Sol) + Yan Liste (Sağ) --}}
        <div class="grid lg:grid-cols-3 gap-8 mb-12">

            {{-- SOL: BÜYÜK SLIDER (2/3 width) --}}
            <div class="lg:col-span-2">
                <div class="relative bg-white dark:bg-gray-800 rounded-3xl overflow-hidden shadow-2xl">
                    {{-- Slider Container --}}
                    <div class="relative h-[500px] overflow-hidden">
                        <template x-for="(blog, index) in mainSlider" :key="blog.id">
                            <div x-show="currentSlide === index"
                                 x-transition:enter="transition ease-out duration-500"
                                 x-transition:enter-start="opacity-0 transform translate-x-full"
                                 x-transition:enter-end="opacity-100 transform translate-x-0"
                                 x-transition:leave="transition ease-in duration-300"
                                 x-transition:leave-start="opacity-100 transform translate-x-0"
                                 x-transition:leave-end="opacity-0 transform -translate-x-full"
                                 class="absolute inset-0">

                                {{-- Background Image --}}
                                <div class="absolute inset-0" :style="'background-image: url(' + blog.image + '); background-size: cover; background-position: center;'">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/50 to-transparent"></div>
                                </div>

                                {{-- Content --}}
                                <div class="absolute bottom-0 left-0 right-0 p-8 text-white">
                                    <div class="flex gap-3 mb-4">
                                        <span class="px-4 py-1.5 bg-red-600 rounded-full text-sm font-bold">
                                            <i class="fas fa-fire"></i> GÜNDEM
                                        </span>
                                        <span class="px-4 py-1.5 bg-white/20 backdrop-blur-md rounded-full text-sm font-semibold" x-text="blog.timeAgo"></span>
                                    </div>
                                    <h3 class="text-3xl font-black mb-3 leading-tight" x-text="blog.title"></h3>
                                    <p class="text-lg text-gray-200 mb-4 line-clamp-2" x-text="blog.excerpt"></p>
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-4 text-sm">
                                            <span><i class="fas fa-eye"></i> <span x-text="blog.views"></span></span>
                                            <span><i class="fas fa-comment"></i> <span x-text="blog.comments"></span></span>
                                        </div>
                                        <a :href="blog.url" class="px-6 py-3 bg-white text-gray-900 rounded-xl font-bold hover:bg-gray-100 transition">
                                            Devamını Oku <i class="fas fa-arrow-right ml-2"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    {{-- Slider Controls --}}
                    <div class="absolute bottom-4 left-4 flex gap-2 z-10">
                        <template x-for="(blog, index) in mainSlider" :key="index">
                            <button @click="currentSlide = index; pauseAutoplay()"
                                    class="w-12 h-1 rounded-full transition-all"
                                    :class="currentSlide === index ? 'bg-white' : 'bg-white/30'"></button>
                        </template>
                    </div>

                    {{-- Navigation Buttons --}}
                    <button @click="prevSlide()" class="absolute left-4 top-1/2 -translate-y-1/2 w-12 h-12 bg-white/20 backdrop-blur-md hover:bg-white/40 rounded-full text-white transition">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button @click="nextSlide()" class="absolute right-4 top-1/2 -translate-y-1/2 w-12 h-12 bg-white/20 backdrop-blur-md hover:bg-white/40 rounded-full text-white transition">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>

            {{-- SAĞ: YAN LİSTE (1/3 width) - Sabit Duran --}}
            <div class="space-y-4">
                <div class="bg-gradient-to-r from-orange-500 to-red-600 text-white rounded-2xl p-4 mb-4">
                    <h3 class="text-lg font-black flex items-center gap-2">
                        <i class="fas fa-bolt"></i>
                        SON DAKİKA
                    </h3>
                </div>

                <template x-for="blog in sideList" :key="blog.id">
                    <a :href="blog.url" class="group block bg-white dark:bg-gray-800 rounded-2xl p-4 hover:shadow-xl transition-all border-2 border-transparent hover:border-blue-500">
                        <div class="flex gap-3">
                            <div class="flex-shrink-0 w-24 h-24 rounded-xl overflow-hidden">
                                <img :src="blog.image" :alt="blog.title" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                            </div>
                            <div class="flex-1 min-w-0">
                                <span class="text-xs text-blue-600 dark:text-blue-400 font-bold" x-text="blog.timeAgo"></span>
                                <h4 class="text-sm font-bold text-gray-900 dark:text-white line-clamp-2 mb-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition" x-text="blog.title"></h4>
                                <div class="flex items-center gap-3 text-xs text-gray-500">
                                    <span><i class="fas fa-eye"></i> <span x-text="blog.views"></span></span>
                                </div>
                            </div>
                        </div>
                    </a>
                </template>
            </div>
        </div>

        {{-- ALT: KÜÇÜK BAŞLIKLAR - Yatay Scroll --}}
        <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-xl">
            <h3 class="text-2xl font-black text-gray-900 dark:text-white mb-6 flex items-center gap-3">
                <i class="fas fa-newspaper text-blue-600"></i>
                Tüm Haberler
            </h3>

            <div class="flex overflow-x-auto gap-4 pb-4 scrollbar-hide" x-ref="scrollContainer">
                <template x-for="blog in allBlogs" :key="blog.id">
                    <a :href="blog.url" class="group flex-shrink-0 w-80 bg-gray-50 dark:bg-gray-700 rounded-xl p-4 hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                        <div class="flex gap-3">
                            <div class="flex-shrink-0 w-16 h-16 rounded-lg overflow-hidden bg-gradient-to-br from-blue-100 to-purple-100 dark:from-blue-900 dark:to-purple-900 flex items-center justify-center">
                                <i class="fas fa-file-alt text-2xl text-blue-600 dark:text-blue-400"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <span class="text-xs text-gray-500 dark:text-gray-400 font-semibold" x-text="blog.timeAgo"></span>
                                <h4 class="text-sm font-bold text-gray-900 dark:text-white line-clamp-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition" x-text="blog.title"></h4>
                            </div>
                        </div>
                    </a>
                </template>
            </div>
        </div>

    </section>

    {{-- FOOTER PLACEHOLDER --}}
    <footer class="bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 py-8 mt-20">
        <div class="container mx-auto px-4 text-center text-gray-600 dark:text-gray-400">
            <p>© 2025 İxtif Akademi - v1: News Magazine Style</p>
        </div>
    </footer>

    <script>
        function blogLayoutV1() {
            return {
                currentSlide: 0,
                autoplayInterval: null,

                // 4 büyük slider item
                mainSlider: [
                    { id: 1, title: 'En İyi Reach Truck Modelleri 2025', excerpt: 'Reach truck seçerken dikkat edilmesi gereken özellikler ve en çok tercih edilen modeller.', image: 'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?w=800', url: '#', timeAgo: '2 saat önce', views: '1.2K', comments: '24' },
                    { id: 2, title: 'Forklift Yedek Parça Bursa: Hızlı Teslimat', excerpt: 'Bursa bölgesinde forklift yedek parça tedariki ve 24 saat içinde teslimat garantisi.', image: 'https://images.unsplash.com/photo-1601366092959-4f06770cc456?w=800', url: '#', timeAgo: '4 saat önce', views: '856', comments: '12' },
                    { id: 3, title: 'İstif Makinesi Bakım İpuçları', excerpt: 'İstif makinenizin ömrünü uzatmak için periyodik bakım ve kontrol noktaları.', image: 'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?w=800', url: '#', timeAgo: '6 saat önce', views: '643', comments: '8' },
                    { id: 4, title: 'Elektrikli Transpalet Avantajları', excerpt: 'Manuel transpalet mi yoksa elektrikli mi? Hangi durumda hangisi daha avantajlı?', image: 'https://images.unsplash.com/photo-1601366092959-4f06770cc456?w=800', url: '#', timeAgo: '8 saat önce', views: '2.1K', comments: '31' }
                ],

                // 5 yan liste item (sabit)
                sideList: [
                    { id: 5, title: 'Tuzla OSB Forklift Satış Noktaları', image: 'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?w=200', url: '#', timeAgo: '1 saat önce', views: '432' },
                    { id: 6, title: 'Forklift Kaldırma Kapasitesi Hesaplama', image: 'https://images.unsplash.com/photo-1601366092959-4f06770cc456?w=200', url: '#', timeAgo: '3 saat önce', views: '789' },
                    { id: 7, title: 'Depo Ekipmanları Seçim Rehberi', image: 'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?w=200', url: '#', timeAgo: '5 saat önce', views: '521' },
                    { id: 8, title: 'İş Güvenliği ve Forklift Kullanımı', image: 'https://images.unsplash.com/photo-1601366092959-4f06770cc456?w=200', url: '#', timeAgo: '7 saat önce', views: '912' },
                    { id: 9, title: 'Forklift Kiralama vs Satın Alma', image: 'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?w=200', url: '#', timeAgo: '9 saat önce', views: '1.3K' }
                ],

                // 15 küçük başlık (yatay scroll)
                allBlogs: [
                    { id: 10, title: 'Forklift Operatör Eğitimi ve Sertifikası', url: '#', timeAgo: '10 dk önce' },
                    { id: 11, title: 'İstif Yüksekliği Optimizasyonu', url: '#', timeAgo: '30 dk önce' },
                    { id: 12, title: 'Akülü Forklift Şarj İstasyonları', url: '#', timeAgo: '1 saat önce' },
                    { id: 13, title: 'Dar Koridor Reach Truck Çözümleri', url: '#', timeAgo: '2 saat önce' },
                    { id: 14, title: 'Forklift Lastik Bakımı ve Değişimi', url: '#', timeAgo: '3 saat önce' },
                    { id: 15, title: 'Depo Raf Sistemleri ve Forklift Uyumu', url: '#', timeAgo: '4 saat önce' },
                    { id: 16, title: 'Sıfır vs İkinci El Forklift Karşılaştırması', url: '#', timeAgo: '5 saat önce' },
                    { id: 17, title: 'Forklift Sigorta ve Garanti Şartları', url: '#', timeAgo: '6 saat önce' },
                    { id: 18, title: 'LPG vs Elektrikli Forklift Maliyet Analizi', url: '#', timeAgo: '7 saat önce' },
                    { id: 19, title: 'Yüksek Kaldırma Kapasiteli Forkliftler', url: '#', timeAgo: '8 saat önce' },
                    { id: 20, title: 'Forklift Periyodik Bakım Takvimi', url: '#', timeAgo: '9 saat önce' },
                    { id: 21, title: 'Özel Sektör Forklift Kiralama Fiyatları', url: '#', timeAgo: '10 saat önce' },
                    { id: 22, title: 'Forklift Güvenlik Sensörleri ve Sistemleri', url: '#', timeAgo: '11 saat önce' },
                    { id: 23, title: 'Soğuk Hava Deposu Forklift Seçimi', url: '#', timeAgo: '12 saat önce' },
                    { id: 24, title: 'Forklift Operatör Hataları ve Çözümleri', url: '#', timeAgo: '1 gün önce' }
                ],

                init() {
                    this.startAutoplay();
                },

                startAutoplay() {
                    this.autoplayInterval = setInterval(() => {
                        this.nextSlide();
                    }, 5000);
                },

                pauseAutoplay() {
                    clearInterval(this.autoplayInterval);
                    setTimeout(() => this.startAutoplay(), 10000);
                },

                nextSlide() {
                    this.currentSlide = (this.currentSlide + 1) % this.mainSlider.length;
                },

                prevSlide() {
                    this.currentSlide = this.currentSlide === 0 ? this.mainSlider.length - 1 : this.currentSlide - 1;
                    this.pauseAutoplay();
                }
            }
        }
    </script>

</body>
</html>

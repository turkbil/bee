<!DOCTYPE html>
<html lang="tr" x-data="{ darkMode: localStorage.getItem('darkMode') || 'dark' }" x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))" :class="{ 'dark': darkMode === 'dark' }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>v3: Full-Width Epic Hero + Timeline - İxtif Akademi</title>

    <script>if(localStorage.getItem('darkMode')==='dark')document.documentElement.classList.add('dark')</script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.5.1/css/all.css">

    <style>
        body { font-family: 'SF Pro Display', -apple-system, system-ui, sans-serif; }
        @keyframes epic-enter { from { opacity: 0; transform: translateY(40px) scale(0.95); } to { opacity: 1; transform: translateY(0) scale(1); } }
        .epic-animation { animation: epic-enter 1s cubic-bezier(0.16, 1, 0.3, 1); }

        @keyframes pulse-glow { 0%, 100% { box-shadow: 0 0 20px rgba(59, 130, 246, 0.5); } 50% { box-shadow: 0 0 40px rgba(59, 130, 246, 0.8); } }
        .pulse-effect { animation: pulse-glow 2s ease-in-out infinite; }

        /* Timeline vertical line */
        .timeline-line { position: relative; }
        .timeline-line::before { content: ''; position: absolute; left: 19px; top: 0; bottom: 0; width: 2px; background: linear-gradient(to bottom, rgba(59, 130, 246, 0.8), rgba(139, 92, 246, 0.8)); }
    </style>
</head>
<body class="bg-black text-white min-h-screen overflow-x-hidden"
      x-data="blogLayoutV3()" x-init="init()">

    {{-- FIXED HEADER --}}
    <header class="fixed top-0 left-0 right-0 z-50 bg-black/80 backdrop-blur-2xl border-b border-white/10">
        <div class="container mx-auto px-6 py-4 flex justify-between items-center">
            <h1 class="text-2xl font-black bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500 bg-clip-text text-transparent">
                İXTİF AKADEMİ
            </h1>
            <div class="flex items-center gap-4">
                <a href="/blog" class="px-6 py-2 rounded-full bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-500 hover:to-purple-500 font-semibold text-sm transition-all">
                    Tüm Haberler
                </a>
                <button @click="darkMode = darkMode === 'dark' ? 'light' : 'dark'"
                        class="w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 transition flex items-center justify-center">
                    <i class="fas fa-moon dark:hidden"></i>
                    <i class="fas fa-sun hidden dark:block"></i>
                </button>
            </div>
        </div>
    </header>

    {{-- EPIC HERO SLIDER - Full Width --}}
    <section class="relative h-screen mt-16">
        <div class="absolute inset-0 overflow-hidden">
            <template x-for="(blog, index) in heroSlides" :key="blog.id">
                <div x-show="heroIndex === index"
                     x-transition:enter="transition ease-out duration-1000"
                     x-transition:enter-start="opacity-0 scale-110"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-700"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="absolute inset-0">

                    {{-- Background Image with Parallax --}}
                    <div class="absolute inset-0" :style="'background-image: url(' + blog.image + '); background-size: cover; background-position: center;'">
                        <div class="absolute inset-0 bg-gradient-to-b from-black/70 via-black/50 to-black"></div>
                    </div>

                    {{-- Content --}}
                    <div class="relative h-full flex items-center justify-center text-center px-6">
                        <div class="max-w-5xl epic-animation">
                            <div class="flex justify-center gap-4 mb-8">
                                <span class="px-6 py-3 bg-gradient-to-r from-red-600 to-orange-600 rounded-2xl text-lg font-black uppercase tracking-wider pulse-effect">
                                    <i class="fas fa-bolt"></i> BREAKING NEWS
                                </span>
                                <span class="px-6 py-3 bg-white/10 backdrop-blur-xl rounded-2xl text-lg font-bold" x-text="blog.timeAgo"></span>
                            </div>

                            <h1 class="text-6xl md:text-8xl font-black mb-8 leading-none bg-gradient-to-r from-white via-blue-100 to-purple-100 bg-clip-text text-transparent" x-text="blog.title"></h1>

                            <p class="text-2xl md:text-3xl text-gray-300 mb-12 leading-relaxed" x-text="blog.excerpt"></p>

                            <div class="flex justify-center items-center gap-8 mb-8 text-xl">
                                <span class="flex items-center gap-2">
                                    <i class="fas fa-fire text-orange-500 text-2xl"></i>
                                    <span class="font-bold" x-text="blog.views"></span> views
                                </span>
                                <span class="w-1 h-8 bg-white/30"></span>
                                <span class="flex items-center gap-2">
                                    <i class="fas fa-comments text-blue-500 text-2xl"></i>
                                    <span class="font-bold" x-text="blog.comments"></span> comments
                                </span>
                            </div>

                            <a :href="blog.url" class="inline-flex items-center gap-4 px-12 py-5 bg-white text-black rounded-2xl text-xl font-black hover:bg-gray-100 transition-all hover:scale-105">
                                READ FULL STORY
                                <i class="fas fa-arrow-right text-2xl"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        {{-- Hero Navigation --}}
        <div class="absolute bottom-12 left-0 right-0 flex justify-center gap-4 z-20">
            <button @click="prevHero()" class="w-16 h-16 rounded-full bg-white/20 backdrop-blur-xl hover:bg-white/30 transition flex items-center justify-center text-2xl">
                <i class="fas fa-chevron-left"></i>
            </button>
            <template x-for="(blog, index) in heroSlides" :key="index">
                <button @click="heroIndex = index; pauseHero()"
                        class="h-16 rounded-full transition-all flex items-center justify-center px-6 font-bold text-sm"
                        :class="heroIndex === index ? 'w-32 bg-white text-black' : 'w-16 bg-white/20 backdrop-blur-xl text-white'">
                    <span x-show="heroIndex === index" x-text="'0' + (index + 1)"></span>
                    <span x-show="heroIndex !== index">•</span>
                </button>
            </template>
            <button @click="nextHero()" class="w-16 h-16 rounded-full bg-white/20 backdrop-blur-xl hover:bg-white/30 transition flex items-center justify-center text-2xl">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>

        {{-- Scroll Indicator --}}
        <div class="absolute bottom-8 left-1/2 -translate-x-1/2 animate-bounce">
            <i class="fas fa-chevron-down text-white/50 text-3xl"></i>
        </div>
    </section>

    {{-- TIMELINE SECTION - Zaman Çizelgesi --}}
    <section class="container mx-auto px-6 py-24">
        <div class="text-center mb-16">
            <h2 class="text-5xl md:text-6xl font-black mb-4 bg-gradient-to-r from-blue-500 to-purple-500 bg-clip-text text-transparent">
                SON 24 SAAT
            </h2>
            <p class="text-xl text-gray-400">Günde 24 yeni haber ekleniyor</p>
        </div>

        {{-- Timeline --}}
        <div class="max-w-4xl mx-auto timeline-line">
            <template x-for="blog in timeline" :key="blog.id">
                <div class="relative pl-16 pb-12 group">
                    {{-- Timeline Dot --}}
                    <div class="absolute left-0 top-0 w-10 h-10 rounded-full bg-gradient-to-r from-blue-600 to-purple-600 flex items-center justify-center z-10 group-hover:scale-125 transition-transform">
                        <i class="fas fa-circle text-white text-xs"></i>
                    </div>

                    {{-- Content Card --}}
                    <a :href="blog.url" class="block bg-gradient-to-br from-gray-900 to-gray-800 rounded-3xl p-8 hover:from-gray-800 hover:to-gray-700 transition-all border border-white/10 group-hover:border-blue-500 group-hover:shadow-2xl group-hover:shadow-blue-500/20">
                        <div class="flex items-start gap-6">
                            {{-- Image --}}
                            <div class="flex-shrink-0 w-32 h-32 rounded-2xl overflow-hidden bg-gradient-to-br from-blue-500/20 to-purple-500/20 hidden md:block">
                                <template x-if="blog.image">
                                    <img :src="blog.image" :alt="blog.title" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                                </template>
                                <template x-if="!blog.image">
                                    <div class="w-full h-full flex items-center justify-center">
                                        <i class="fas fa-newspaper text-4xl text-blue-500"></i>
                                    </div>
                                </template>
                            </div>

                            {{-- Text --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-3 mb-3">
                                    <span class="px-4 py-1.5 bg-blue-600/20 rounded-full text-sm font-bold text-blue-400" x-text="blog.timeAgo"></span>
                                    <span class="text-sm text-gray-500 flex items-center gap-2">
                                        <i class="fas fa-eye"></i> <span x-text="blog.views"></span>
                                    </span>
                                </div>
                                <h3 class="text-2xl font-black mb-3 text-white group-hover:text-blue-400 transition" x-text="blog.title"></h3>
                                <p class="text-gray-400 leading-relaxed line-clamp-2" x-text="blog.excerpt"></p>
                            </div>
                        </div>
                    </a>
                </div>
            </template>
        </div>
    </section>

    {{-- COMPACT GRID - Tüm Haberler --}}
    <section class="bg-gradient-to-br from-gray-900 to-black py-24">
        <div class="container mx-auto px-6">
            <h3 class="text-4xl font-black mb-12 text-center bg-gradient-to-r from-yellow-500 to-orange-500 bg-clip-text text-transparent">
                <i class="fas fa-grid-2"></i> DİĞER HABERLER
            </h3>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                <template x-for="blog in compactGrid" :key="blog.id">
                    <a :href="blog.url" class="group bg-white/5 backdrop-blur-xl rounded-2xl p-6 hover:bg-white/10 transition-all border border-white/10 hover:border-white/20">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 w-16 h-16 rounded-xl bg-gradient-to-br from-blue-600 to-purple-600 flex items-center justify-center">
                                <i class="fas fa-file-alt text-2xl text-white"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs text-blue-400 font-bold mb-2" x-text="blog.timeAgo"></p>
                                <h4 class="text-base font-bold text-white line-clamp-2 group-hover:text-blue-400 transition" x-text="blog.title"></h4>
                            </div>
                        </div>
                    </a>
                </template>
            </div>
        </div>
    </section>

    {{-- FOOTER --}}
    <footer class="bg-black border-t border-white/10 py-12">
        <div class="container mx-auto px-6 text-center text-gray-500">
            <p class="text-lg">© 2025 İXTİF AKADEMİ - v3: Full-Width Epic Hero + Timeline</p>
        </div>
    </footer>

    <script>
        function blogLayoutV3() {
            return {
                heroIndex: 0,
                heroInterval: null,

                // 4 epic hero slides
                heroSlides: [
                    { id: 1, title: 'FORKLIFT DEVRIMI 2025', excerpt: 'Elektrikli forkliftler sektöre damgasını vurdu. Yeni nesil batarya teknolojisi ile 12 saat kesintisiz çalışma.', image: 'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?w=1920', url: '#', timeAgo: '15 dakika önce', views: '5.2K', comments: '87' },
                    { id: 2, title: 'REACH TRUCK YENİLENDİ', excerpt: 'Dar koridor çözümleri ile depo verimliliğini %300 artırın. İşte 2025 modelleri...', image: 'https://images.unsplash.com/photo-1601366092959-4f06770cc456?w=1920', url: '#', timeAgo: '45 dakika önce', views: '4.8K', comments: '72' },
                    { id: 3, title: 'OTOMASYON ÇAĞI BAŞLADI', excerpt: 'Yapay zeka destekli depo yönetim sistemleri Türkiye\'de. Maliyetleri yarıya indirin.', image: 'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?w=1920', url: '#', timeAgo: '2 saat önce', views: '6.1K', comments: '103' },
                    { id: 4, title: 'YEŞİL ENERJİ TRANSPALETİ', excerpt: 'Güneş enerjisi ile şarj olan transpalet modelleri piyasada. Sıfır karbon emisyonu.', image: 'https://images.unsplash.com/photo-1601366092959-4f06770cc456?w=1920', url: '#', timeAgo: '4 saat önce', views: '3.9K', comments: '61' }
                ],

                // 12 timeline items
                timeline: [
                    { id: 5, title: 'Forklift Bakım Rehberi: Periyodik Kontroller', excerpt: 'Maliyetlerinizi %40 azaltan bakım stratejileri ve uzman tavsiyeleri.', image: 'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?w=400', url: '#', timeAgo: '30 dakika önce', views: '1.2K' },
                    { id: 6, title: 'Transpalet Seçim Kriterleri 2025', excerpt: 'Manuel mi elektrikli mi? Uzmanlar açıklıyor: Hangi durumda hangisi?', image: null, url: '#', timeAgo: '1 saat önce', views: '856' },
                    { id: 7, title: 'Lityum-iyon Akü Teknolojisi Geldi', excerpt: '8 saat kesintisiz çalışma ve 2 kat daha uzun ömür garantisi.', image: 'https://images.unsplash.com/photo-1601366092959-4f06770cc456?w=400', url: '#', timeAgo: '2 saat önce', views: '643' },
                    { id: 8, title: 'İş Güvenliği: Operatör Eğitimi Zorunlu Mu?', excerpt: 'Yeni yasal düzenlemeler ve sertifikasyon süreçleri hakkında bilmeniz gerekenler.', image: null, url: '#', timeAgo: '3 saat önce', views: '912' },
                    { id: 9, title: 'Forklift Kiralama Ekonomik Analiz', excerpt: 'Kısa süreli projeler için en mantıklı çözüm. Maliyet karşılaştırması.', image: 'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?w=400', url: '#', timeAgo: '4 saat önce', views: '1.3K' },
                    { id: 10, title: 'Soğuk Hava Deposu Ekipman Seçimi', excerpt: '-25°C\'de bile çalışan özel forklift modelleri ve teknik özellikler.', image: null, url: '#', timeAgo: '5 saat önce', views: '721' },
                    { id: 11, title: 'Forklift Lastik Teknolojisi: Dolgu vs Pnömatik', excerpt: 'Hangi zemin için hangi lastik? Uzmanlar karşılaştırıyor.', image: 'https://images.unsplash.com/photo-1601366092959-4f06770cc456?w=400', url: '#', timeAgo: '6 saat önce', views: '534' },
                    { id: 12, title: 'Depo Optimizasyon: Alan Kullanımı İpuçları', excerpt: 'Raf sistemleri ile depo kapasitesini 2 katına çıkarmanın yolları.', image: null, url: '#', timeAgo: '8 saat önce', views: '892' },
                    { id: 13, title: 'Satış Sonrası Hizmetler: Yetkili Servis Neden Önemli?', excerpt: 'Garantinizi kaybetmemek için bilmeniz gereken kritik detaylar.', image: 'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?w=400', url: '#', timeAgo: '10 saat önce', views: '678' },
                    { id: 14, title: 'LPG Forklift Ekonomik Analiz', excerpt: 'Yakıt maliyetleri, avantajlar ve dezavantajlar tam karşılaştırma.', image: null, url: '#', timeAgo: '12 saat önce', views: '1.1K' },
                    { id: 15, title: 'Yüksek Kaldırma: 12 Metreye Kadar İstif', excerpt: 'Yeni nesil yüksek kaldırmalı forkliftler ve güvenlik standartları.', image: 'https://images.unsplash.com/photo-1601366092959-4f06770cc456?w=400', url: '#', timeAgo: '16 saat önce', views: '512' },
                    { id: 16, title: 'Forklift Sigorta Rehberi 2025', excerpt: 'Kasko, trafik ve zorunlu sigortalar hakkında bilmeniz gerekenler.', image: null, url: '#', timeAgo: '20 saat önce', views: '734' }
                ],

                // 12 compact grid items
                compactGrid: [
                    { id: 17, title: 'Forklift Operatör Maaşları 2025 Güncel', url: '#', timeAgo: '22 saat önce' },
                    { id: 18, title: 'Sıfır vs İkinci El: Hangisi Daha Avantajlı?', url: '#', timeAgo: '23 saat önce' },
                    { id: 19, title: 'Reach Truck Dar Koridor Performansı', url: '#', timeAgo: '1 gün önce' },
                    { id: 20, title: 'Forklift Periyodik Muayene Süreci', url: '#', timeAgo: '1 gün önce' },
                    { id: 21, title: 'Elektrikli Transpalet Şarj Optimizasyonu', url: '#', timeAgo: '1 gün önce' },
                    { id: 22, title: 'Yedek Parça Stok Yönetim Stratejileri', url: '#', timeAgo: '1 gün önce' },
                    { id: 23, title: 'Depo Güvenlik Sistemleri 2025', url: '#', timeAgo: '1 gün önce' },
                    { id: 24, title: 'Filo Yönetim Yazılımları Karşılaştırması', url: '#', timeAgo: '1 gün önce' },
                    { id: 25, title: 'Hidrolik Sistem Bakım ve Onarım', url: '#', timeAgo: '1 gün önce' },
                    { id: 26, title: 'Forklift Kullanım Ömrü Nasıl Uzatılır?', url: '#', timeAgo: '1 gün önce' },
                    { id: 27, title: 'Otomasyon Entegrasyonu Rehberi', url: '#', timeAgo: '1 gün önce' },
                    { id: 28, title: 'Forklift Finansman Seçenekleri ve Krediler', url: '#', timeAgo: '1 gün önce' }
                ],

                init() {
                    this.startHero();
                },

                startHero() {
                    this.heroInterval = setInterval(() => {
                        this.nextHero();
                    }, 6000);
                },

                pauseHero() {
                    clearInterval(this.heroInterval);
                    setTimeout(() => this.startHero(), 12000);
                },

                nextHero() {
                    this.heroIndex = (this.heroIndex + 1) % this.heroSlides.length;
                },

                prevHero() {
                    this.heroIndex = this.heroIndex === 0 ? this.heroSlides.length - 1 : this.heroIndex - 1;
                    this.pauseHero();
                }
            }
        }
    </script>

</body>
</html>

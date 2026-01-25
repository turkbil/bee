@extends('themes.t-7.layouts.app')

@section('content')
    {{-- ========== HERO SECTION - Full-Screen Slider ========== --}}
    <section id="anasayfa" class="relative min-h-screen group">
        <div class="animated-mesh"></div>

        <div class="swiper hero-swiper h-screen">
            <div class="swiper-wrapper">
                {{-- Slide 1 - Modern Üretim Tesisleri --}}
                <div class="swiper-slide relative">
                    <div class="absolute inset-0">
                        <img src="https://picsum.photos/1920/1080?random=200" alt="Modern Üretim Tesisi" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-r from-slate-950/95 via-slate-900/80 to-transparent"></div>
                    </div>
                    <div class="relative h-full flex items-center">
                        <div class="container mx-auto">
                            <div class="max-w-3xl">
                                <div class="inline-flex items-center gap-3 bg-sky-500/20 backdrop-blur-sm text-sky-300 px-5 py-2 rounded-full text-sm font-medium mb-8">
                                    <div class="w-2 h-2 bg-sky-400 rounded-full animate-pulse"></div>
                                    <span>1999'dan Bu Yana</span>
                                </div>
                                <h1 class="hero-title font-heading font-bold text-5xl sm:text-6xl lg:text-7xl text-white mb-6">
                                    Endüstriyel Ambalajda
                                    <span class="block gradient-text-hero">Lider Üretici</span>
                                </h1>
                                <p class="text-xl text-slate-300 leading-relaxed mb-10 max-w-xl">
                                    Türkiye'nin önde gelen endüstriyel ambalaj üreticisi. Üç entegre tesisimizde uluslararası standartlarda üretim gerçekleştiriyoruz.
                                </p>
                                <div class="flex flex-wrap gap-4">
                                    <a href="#sirketlerimiz" class="inline-flex items-center gap-3 gradient-shift text-white px-8 py-4 rounded-xl font-semibold shadow-lg shadow-sky-500/30 btn-hover">
                                        <span>Tesislerimizi İnceleyin</span>
                                        <i class="fa-light fa-arrow-right"></i>
                                    </a>
                                    <a href="#iletisim" class="inline-flex items-center gap-3 bg-white/10 backdrop-blur-sm hover:bg-white/20 text-white px-8 py-4 rounded-xl font-semibold transition-all border border-white/20 hover:border-sky-400/50">
                                        <i class="fa-light fa-phone"></i>
                                        <span>İletişime Geçin</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Slide 2 - Üretim Kapasitesi --}}
                <div class="swiper-slide relative">
                    <div class="absolute inset-0">
                        <img src="https://picsum.photos/1920/1080?random=201" alt="Üretim Kapasitesi" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-r from-slate-950/95 via-slate-900/80 to-transparent"></div>
                    </div>
                    <div class="relative h-full flex items-center">
                        <div class="container mx-auto">
                            <div class="max-w-3xl">
                                <div class="inline-flex items-center gap-3 bg-sky-500/20 backdrop-blur-sm text-sky-300 px-5 py-2 rounded-full text-sm font-medium mb-8">
                                    <div class="w-2 h-2 bg-sky-400 rounded-full animate-pulse"></div>
                                    <span>Yüksek Kapasite</span>
                                </div>
                                <h1 class="hero-title font-heading font-bold text-5xl sm:text-6xl lg:text-7xl text-white mb-6">
                                    Yıllık 5.000 Ton
                                    <span class="block gradient-text-hero">Üretim Kapasitesi</span>
                                </h1>
                                <p class="text-xl text-slate-300 leading-relaxed mb-10 max-w-xl">
                                    Tam otomatik üretim hatlarımızda ISO 9001 ve UN/ADR standartlarında üretim. Sektörün en güvenilir tedarikçisi olarak hizmetinizdeyiz.
                                </p>
                                <div class="flex flex-wrap gap-4">
                                    <a href="#sirketlerimiz" class="inline-flex items-center gap-3 gradient-shift text-white px-8 py-4 rounded-xl font-semibold shadow-lg shadow-sky-500/30 btn-hover">
                                        <span>Üretim Sürecimiz</span>
                                        <i class="fa-light fa-arrow-right"></i>
                                    </a>
                                    <a href="#iletisim" class="inline-flex items-center gap-3 bg-white/10 backdrop-blur-sm hover:bg-white/20 text-white px-8 py-4 rounded-xl font-semibold transition-all border border-white/20 hover:border-sky-400/50">
                                        <i class="fa-light fa-file-invoice"></i>
                                        <span>Teklif Alın</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Slide 3 - Döngüsel Üretim --}}
                <div class="swiper-slide relative">
                    <div class="absolute inset-0">
                        <img src="https://picsum.photos/1920/1080?random=202" alt="Döngüsel Üretim" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-r from-slate-950/95 via-slate-900/80 to-transparent"></div>
                    </div>
                    <div class="relative h-full flex items-center">
                        <div class="container mx-auto">
                            <div class="max-w-3xl">
                                <div class="inline-flex items-center gap-3 bg-sky-500/20 backdrop-blur-sm text-sky-300 px-5 py-2 rounded-full text-sm font-medium mb-8">
                                    <div class="w-2 h-2 bg-sky-400 rounded-full animate-pulse"></div>
                                    <span>Sürdürülebilirlik</span>
                                </div>
                                <h1 class="hero-title font-heading font-bold text-5xl sm:text-6xl lg:text-7xl text-white mb-6">
                                    Çevreye Duyarlı
                                    <span class="block gradient-text-hero">Döngüsel Ekonomi</span>
                                </h1>
                                <p class="text-xl text-slate-300 leading-relaxed mb-10 max-w-xl">
                                    %97 geri kazanım oranıyla sektörün en çevreci üreticisiyiz. Atıkları hammaddeye dönüştürerek döngüsel ekonomiye katkı sağlıyoruz.
                                </p>
                                <div class="flex flex-wrap gap-4">
                                    <a href="#hakkimizda" class="inline-flex items-center gap-3 gradient-shift text-white px-8 py-4 rounded-xl font-semibold shadow-lg shadow-sky-500/30 btn-hover">
                                        <span>Sürdürülebilirlik Politikamız</span>
                                        <i class="fa-light fa-leaf"></i>
                                    </a>
                                    <a href="#iletisim" class="inline-flex items-center gap-3 bg-white/10 backdrop-blur-sm hover:bg-white/20 text-white px-8 py-4 rounded-xl font-semibold transition-all border border-white/20 hover:border-sky-400/50">
                                        <i class="fa-light fa-phone"></i>
                                        <span>İletişime Geçin</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="swiper-pagination !bottom-10"></div>

            {{-- Navigation Arrows - Hover'da görünür --}}
            <div class="hidden lg:flex absolute left-4 top-1/2 -translate-y-1/2 z-30 opacity-0 hover:opacity-100 transition-opacity duration-300 group-hover:opacity-100">
                <button class="hero-prev w-12 h-12 bg-black/30 hover:bg-black/50 rounded-full flex items-center justify-center text-white transition-all">
                    <i class="fa-light fa-chevron-left"></i>
                </button>
            </div>
            <div class="hidden lg:flex absolute right-4 top-1/2 -translate-y-1/2 z-30 opacity-0 hover:opacity-100 transition-opacity duration-300 group-hover:opacity-100">
                <button class="hero-next w-12 h-12 bg-black/30 hover:bg-black/50 rounded-full flex items-center justify-center text-white transition-all">
                    <i class="fa-light fa-chevron-right"></i>
                </button>
            </div>
        </div>

        {{-- Floating Stats --}}
        <div class="absolute bottom-0 left-0 right-0 z-20 hidden lg:block">
            <div class="container mx-auto">
                <div class="bg-white/10 backdrop-blur-sm rounded-t-3xl border border-white/10 border-b-0">
                    <div class="grid grid-cols-4 divide-x divide-white/10">
                        <div class="p-8 text-center">
                            <div class="font-heading font-bold text-4xl text-white mb-2">25+</div>
                            <div class="text-white/60 text-sm">Yıllık Deneyim</div>
                        </div>
                        <div class="p-8 text-center">
                            <div class="font-heading font-bold text-4xl text-white mb-2">5.000</div>
                            <div class="text-white/60 text-sm">Ton/Yıl Kapasite</div>
                        </div>
                        <div class="p-8 text-center">
                            <div class="font-heading font-bold text-4xl text-white mb-2">%97</div>
                            <div class="text-white/60 text-sm">Geri Kazanım</div>
                        </div>
                        <div class="p-8 text-center">
                            <div class="font-heading font-bold text-4xl text-white mb-2">3</div>
                            <div class="text-white/60 text-sm">Üretim Tesisi</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ========== COMPANIES SECTION ========== --}}
    <section id="sirketlerimiz" class="py-20 md:py-28 lg:py-36 bg-white dark:bg-slate-950">
        <div class="container mx-auto">
            {{-- Section Header --}}
            <div class="text-center mb-16" data-aos="fade-up">
                <span class="inline-flex items-center gap-2 bg-sky-100 dark:bg-sky-900/30 text-sky-600 dark:text-sky-400 px-4 py-2 rounded-full text-sm font-semibold mb-6">
                    <i class="fa-light fa-building"></i>
                    <span>Grup Şirketleri</span>
                </span>
                <h2 class="font-heading font-bold text-4xl md:text-5xl text-slate-900 dark:text-white mb-6">
                    Entegre <span class="gradient-text">Grup Şirketleri</span>
                </h2>
                <p class="text-lg text-slate-600 dark:text-slate-400 max-w-2xl mx-auto">
                    Üç uzman kuruluşumuzla üretimden geri dönüşüme kesintisiz hizmet zinciri oluşturuyoruz
                </p>
            </div>

            {{-- Companies Grid --}}
            <div class="grid lg:grid-cols-3 gap-8">
                {{-- Varilsan Polimer --}}
                <div class="group relative bg-gradient-to-br from-sky-50 to-white dark:from-slate-800 dark:to-slate-900 rounded-3xl overflow-hidden card-shadow border border-sky-100 dark:border-slate-700" data-aos="fade-up" data-aos-delay="100">
                    <div class="absolute top-0 right-0 w-40 h-40 bg-sky-500/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                    <div class="p-8 relative">
                        <div class="w-16 h-16 bg-sky-500 rounded-2xl flex items-center justify-center mb-6 shadow-lg shadow-sky-500/30 icon-hover">
                            <i class="fa-light fa-industry text-white text-2xl"></i>
                        </div>
                        <h3 class="font-heading font-bold text-2xl text-slate-900 dark:text-white mb-2">Varilsan Polimer</h3>
                        <p class="text-sky-600 font-medium mb-4">Gebze, Kocaeli</p>
                        <p class="text-slate-600 dark:text-slate-400 mb-6 leading-relaxed">
                            IBC Tank, plastik bidon ve HDPE granül üretimi. ISO ve UN sertifikalı modern tesislerimizde kaliteli üretim.
                        </p>
                        <div class="flex flex-wrap gap-2 mb-6">
                            <span class="px-3 py-1.5 bg-sky-100 dark:bg-sky-900/30 text-sky-700 dark:text-sky-300 rounded-lg text-sm font-medium">IBC Tank</span>
                            <span class="px-3 py-1.5 bg-sky-100 dark:bg-sky-900/30 text-sky-700 dark:text-sky-300 rounded-lg text-sm font-medium">Plastik Bidon</span>
                            <span class="px-3 py-1.5 bg-sky-100 dark:bg-sky-900/30 text-sky-700 dark:text-sky-300 rounded-lg text-sm font-medium">HDPE Granül</span>
                        </div>
                        <a href="#" class="inline-flex items-center gap-2 text-sky-600 font-semibold group-hover:gap-3 transition-all link-hover">
                            <span>Şirketi İncele</span>
                            <i class="fa-light fa-arrow-right"></i>
                        </a>
                    </div>
                    <div class="aspect-video overflow-hidden">
                        <img src="https://picsum.photos/800/450?random=400" alt="Varilsan Polimer" class="w-full h-full object-cover transition-transform duration-700">
                    </div>
                </div>

                {{-- Varilsan Ambalaj --}}
                <div class="group relative bg-gradient-to-br from-sky-50 to-white dark:from-slate-800 dark:to-slate-900 rounded-3xl overflow-hidden card-shadow border border-sky-100 dark:border-slate-700" data-aos="fade-up" data-aos-delay="200">
                    <div class="absolute top-0 right-0 w-40 h-40 bg-sky-500/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                    <div class="p-8 relative">
                        <div class="w-16 h-16 bg-sky-600 rounded-2xl flex items-center justify-center mb-6 shadow-lg shadow-sky-500/30 icon-hover">
                            <i class="fa-light fa-recycle text-white text-2xl"></i>
                        </div>
                        <h3 class="font-heading font-bold text-2xl text-slate-900 dark:text-white mb-2">Varilsan Ambalaj</h3>
                        <p class="text-sky-600 font-medium mb-4">Kocaeli</p>
                        <p class="text-slate-600 dark:text-slate-400 mb-6 leading-relaxed">
                            IBC yenileme (Re-bottle), metal varil işleme ve geri dönüşüm hizmetleri. Döngüsel ekonomiye katkı sağlıyoruz.
                        </p>
                        <div class="flex flex-wrap gap-2 mb-6">
                            <span class="px-3 py-1.5 bg-sky-100 dark:bg-sky-900/30 text-sky-700 dark:text-sky-300 rounded-lg text-sm font-medium">Re-bottle</span>
                            <span class="px-3 py-1.5 bg-sky-100 dark:bg-sky-900/30 text-sky-700 dark:text-sky-300 rounded-lg text-sm font-medium">Metal Varil</span>
                            <span class="px-3 py-1.5 bg-sky-100 dark:bg-sky-900/30 text-sky-700 dark:text-sky-300 rounded-lg text-sm font-medium">UN Sertifika</span>
                        </div>
                        <a href="#" class="inline-flex items-center gap-2 text-sky-600 font-semibold group-hover:gap-3 transition-all link-hover">
                            <span>Şirketi İncele</span>
                            <i class="fa-light fa-arrow-right"></i>
                        </a>
                    </div>
                    <div class="aspect-video overflow-hidden">
                        <img src="https://picsum.photos/800/450?random=401" alt="Varilsan Ambalaj" class="w-full h-full object-cover transition-transform duration-700">
                    </div>
                </div>

                {{-- Varilsan Plastik --}}
                <div class="group relative bg-gradient-to-br from-sky-50 to-white dark:from-slate-800 dark:to-slate-900 rounded-3xl overflow-hidden card-shadow border border-sky-100 dark:border-slate-700" data-aos="fade-up" data-aos-delay="300">
                    <div class="absolute top-0 right-0 w-40 h-40 bg-sky-500/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                    <div class="p-8 relative">
                        <div class="w-16 h-16 bg-sky-700 rounded-2xl flex items-center justify-center mb-6 shadow-lg shadow-sky-500/30 icon-hover">
                            <i class="fa-light fa-leaf text-white text-2xl"></i>
                        </div>
                        <h3 class="font-heading font-bold text-2xl text-slate-900 dark:text-white mb-2">Varilsan Plastik</h3>
                        <p class="text-sky-600 font-medium mb-4">Kemalpaşa, İzmir</p>
                        <p class="text-slate-600 dark:text-slate-400 mb-6 leading-relaxed">
                            %95-97 geri kazanım oranıyla çevre dostu geri dönüşüm merkezi. UN/ADR sertifikalı tesislerde sürdürülebilir üretim.
                        </p>
                        <div class="flex flex-wrap gap-2 mb-6">
                            <span class="px-3 py-1.5 bg-sky-100 dark:bg-sky-900/30 text-sky-700 dark:text-sky-300 rounded-lg text-sm font-medium">%97 Geri Kazanım</span>
                            <span class="px-3 py-1.5 bg-sky-100 dark:bg-sky-900/30 text-sky-700 dark:text-sky-300 rounded-lg text-sm font-medium">UN/ADR</span>
                        </div>
                        <a href="#" class="inline-flex items-center gap-2 text-sky-600 font-semibold group-hover:gap-3 transition-all link-hover">
                            <span>Şirketi İncele</span>
                            <i class="fa-light fa-arrow-right"></i>
                        </a>
                    </div>
                    <div class="aspect-video overflow-hidden">
                        <img src="https://picsum.photos/800/450?random=402" alt="Varilsan Plastik" class="w-full h-full object-cover transition-transform duration-700">
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ========== ABOUT / SUSTAINABILITY SECTION - KINETIC MAXIMALIST ========== --}}
    <section id="hakkimizda" class="py-24 lg:py-32 relative overflow-hidden"
        x-data="{
            activeIndex: 0,
            isPaused: false,
            autoInterval: null,
            items: [
                {
                    id: 'leaf',
                    icon: 'leaf',
                    hex: '#3b82f6',
                    hexLight: '#60a5fa',
                    pastelBg: 'rgba(59, 130, 246, 0.15)',
                    pastelGlow: 'rgba(59, 130, 246, 0.3)',
                    stat: '%97',
                    label: 'Geri Kazanım',
                    centerIcon: 'recycle',
                    title: 'Sürdürülebilir Üretim',
                    desc: 'Çevre dostu süreçlerle geleceğe yatırım'
                },
                {
                    id: 'droplet',
                    icon: 'droplet',
                    hex: '#22c55e',
                    hexLight: '#4ade80',
                    pastelBg: 'rgba(34, 197, 94, 0.15)',
                    pastelGlow: 'rgba(34, 197, 94, 0.3)',
                    stat: '5.000+',
                    label: 'Ton/Yıl',
                    centerIcon: 'industry',
                    title: 'Yüksek Kapasite',
                    desc: 'Endüstriyel ölçekte güçlü üretim'
                },
                {
                    id: 'recycle',
                    icon: 'recycle',
                    hex: '#f97316',
                    hexLight: '#fb923c',
                    pastelBg: 'rgba(249, 115, 22, 0.15)',
                    pastelGlow: 'rgba(249, 115, 22, 0.3)',
                    stat: '25+',
                    label: 'Yıl Deneyim',
                    centerIcon: 'award',
                    title: 'Köklü Tecrübe',
                    desc: '1999dan bu yana sektör lideri'
                },
                {
                    id: 'cube',
                    icon: 'cube',
                    hex: '#a855f7',
                    hexLight: '#c084fc',
                    pastelBg: 'rgba(168, 85, 247, 0.15)',
                    pastelGlow: 'rgba(168, 85, 247, 0.3)',
                    stat: '3',
                    label: 'Entegre Tesis',
                    centerIcon: 'building',
                    title: 'Entegre Çözümler',
                    desc: 'Üretimden geri dönüşüme tam entegrasyon'
                }
            ],
            get current() { return this.items[this.activeIndex]; },
            next() { this.activeIndex = (this.activeIndex + 1) % this.items.length; },
            setActive(index) {
                this.activeIndex = index;
                this.resetTimer();
            },
            resetTimer() {
                clearInterval(this.autoInterval);
                this.autoInterval = setInterval(() => {
                    if (!this.isPaused) this.next();
                }, 3500);
            },
            startAuto() {
                this.autoInterval = setInterval(() => {
                    if (!this.isPaused) this.next();
                }, 3500);
            }
        }"
        x-init="startAuto()"
    >
        {{-- Background Gradient --}}
        <div class="absolute inset-0 bg-gradient-to-br from-sky-950 via-slate-950 to-cyan-950"></div>

        {{-- Kinetic Background Particles --}}
        <div class="absolute inset-0 overflow-hidden">
            <div class="particle particle-1"></div>
            <div class="particle particle-2"></div>
            <div class="particle particle-3"></div>
            <div class="particle particle-4"></div>
            <div class="particle particle-5"></div>
        </div>

        {{-- Animated Floating Circles - Organik --}}
        <div class="absolute top-10 left-10 w-64 h-64 rounded-full border border-sky-500/10 organic-float"></div>
        <div class="absolute bottom-10 right-10 w-80 h-80 rounded-full border border-cyan-500/10 organic-float-reverse"></div>
        <div class="absolute top-1/3 right-1/4 w-40 h-40 rounded-full border border-emerald-500/10 organic-float" style="animation-delay: -3s;"></div>

        <div class="container mx-auto relative z-10">
            <div class="grid lg:grid-cols-2 gap-12 lg:gap-20 items-center">

                {{-- Left Content - Dynamic --}}
                <div data-aos="fade-right">
                    {{-- Dynamic Badge --}}
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-medium mb-6 transition-all duration-700"
                          :style="'background: ' + current.pastelBg + '; color: ' + current.hexLight">
                        <i class="fa-light" :class="'fa-' + current.icon"></i>
                        <span x-text="current.title" class="transition-all duration-300"></span>
                    </span>

                    <h2 class="font-heading font-bold text-3xl sm:text-4xl lg:text-5xl text-white mb-6">
                        Endüstriyel Ambalajda
                        <span class="block text-transparent bg-clip-text bg-gradient-to-r from-sky-400 to-cyan-400 kinetic-text">
                            Güvenilir Çözüm Ortağı
                        </span>
                    </h2>

                    {{-- Dynamic Description --}}
                    <p class="text-lg text-white/70 mb-8 min-h-[80px]">
                        <span x-show="activeIndex === 0" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                            Gelecek nesillere yaşanabilir bir dünya bırakmak için %97 geri kazanım oranıyla çevre dostu üretim süreçleri uyguluyoruz.
                        </span>
                        <span x-show="activeIndex === 1" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                            Yıllık 5.000 ton üretim kapasitemizle Türkiye'nin en büyük endüstriyel ambalaj üreticilerinden biriyiz.
                        </span>
                        <span x-show="activeIndex === 2" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                            1999'dan bu yana 25 yılı aşkın tecrübemizle sektörün güvenilir ve öncü kuruluşu olmaya devam ediyoruz.
                        </span>
                        <span x-show="activeIndex === 3" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                            Gebze ve İzmir'deki 3 entegre tesisimizle üretimden geri dönüşüme kesintisiz hizmet sunuyoruz.
                        </span>
                    </p>

                    {{-- Interactive Feature Cards --}}
                    <div class="space-y-3">
                        <template x-for="(item, index) in items" :key="item.id">
                            <div class="flex items-start gap-4 p-4 rounded-xl cursor-pointer transition-all duration-700 border-2 relative overflow-hidden"
                                 :class="activeIndex !== index ? 'bg-white/5 hover:bg-white/10' : ''"
                                 :style="activeIndex === index
                                     ? 'background: ' + item.pastelBg + '; border-color: ' + item.hex + '50; transform: scale(1.02)'
                                     : 'border-color: transparent'"
                                 @mouseenter="setActive(index)"
                                 @click="setActive(index)">
                                {{-- Pastel glow effect --}}
                                <div class="absolute inset-0 transition-opacity duration-700"
                                     :style="'opacity: ' + (activeIndex === index ? '1' : '0') + '; background: linear-gradient(135deg, ' + item.pastelBg + ', transparent)'"></div>

                                <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0 transition-all duration-500 relative z-10"
                                     :style="activeIndex === index
                                         ? 'background: ' + item.hex + '; box-shadow: 0 10px 25px ' + item.hex + '50'
                                         : 'background: rgba(255,255,255,0.1)'">
                                    <i class="fa-light text-xl transition-colors duration-300"
                                       :class="'fa-' + item.icon"
                                       :style="'color: ' + (activeIndex === index ? '#fff' : 'rgba(255,255,255,0.6)')"></i>
                                </div>
                                <div class="relative z-10">
                                    <h4 class="font-semibold text-white mb-1 flex items-center gap-2">
                                        <span x-text="item.stat"></span>
                                        <span x-text="item.label" class="text-white/60 font-normal text-sm"></span>
                                    </h4>
                                    <p class="text-sm text-white/60" x-text="item.desc"></p>
                                </div>
                                {{-- Active Indicator --}}
                                <div class="ml-auto self-center relative z-10">
                                    <div class="w-2 h-2 rounded-full transition-all duration-500"
                                         :style="activeIndex === index
                                             ? 'background: ' + item.hex + '; transform: scale(1.5)'
                                             : 'background: rgba(255,255,255,0.2)'"></div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Right - KINETIC Orbital System (pointer-events-none = mouse etkisiz) --}}
                <div class="relative flex items-center justify-center min-h-[400px] lg:min-h-[500px] pointer-events-none" data-aos="fade-left">

                    {{-- Pastel Background Glow - Smooth Transition --}}
                    <div class="absolute inset-0 rounded-full blur-3xl transition-all duration-1000 ease-in-out opacity-70"
                         :style="'background: radial-gradient(circle, ' + current.pastelGlow + ' 0%, transparent 60%)'">
                    </div>

                    {{-- Outermost Decorative Ring --}}
                    <div class="absolute w-[340px] h-[340px] sm:w-[400px] sm:h-[400px] rounded-full border border-dashed border-white/5 animate-spin" style="animation-duration: 60s;"></div>

                    {{-- Outer Orbit Ring - Color changes smoothly --}}
                    <div class="absolute w-[280px] h-[280px] sm:w-[320px] sm:h-[320px] rounded-full border-2 border-dashed orbit-ring transition-all duration-1000"
                         :style="'border-color: ' + current.hex + '40'">
                    </div>

                    {{-- Middle Glow Ring --}}
                    <div class="absolute w-[220px] h-[220px] sm:w-[260px] sm:h-[260px] rounded-full transition-all duration-1000"
                         :style="'box-shadow: 0 0 80px ' + current.pastelGlow">
                    </div>

                    {{-- Inner Static Ring --}}
                    <div class="absolute w-[180px] h-[180px] sm:w-[200px] sm:h-[200px] rounded-full border border-white/10"></div>

                    {{-- Center Dynamic Content --}}
                    <div class="absolute w-[140px] h-[140px] sm:w-[160px] sm:h-[160px] rounded-full bg-slate-900/80 backdrop-blur-xl flex items-center justify-center border border-white/10 transition-all duration-700"
                         :style="'box-shadow: 0 0 60px ' + current.pastelBg">
                        <div class="text-center">
                            {{-- Dynamic Center Icon --}}
                            <div class="relative mb-2">
                                <i class="fa-duotone text-4xl sm:text-5xl transition-all duration-500"
                                   :class="'fa-' + current.centerIcon"
                                   :style="'color: ' + current.hexLight"></i>
                            </div>
                            {{-- Dynamic Stat --}}
                            <p class="text-2xl sm:text-3xl font-bold text-white transition-all duration-300"
                               x-text="current.stat"></p>
                            <p class="text-xs sm:text-sm text-white/60 transition-all duration-300"
                               x-text="current.label"></p>
                        </div>
                    </div>

                    {{-- Orbiting Icons Container --}}
                    <div class="absolute w-[280px] h-[280px] sm:w-[320px] sm:h-[320px] orbit-container"
                         :style="'transform: rotate(' + (activeIndex * -90) + 'deg)'">

                        {{-- Icon 1 - Top (Leaf) - Mavi --}}
                        <div class="absolute top-0 left-1/2 -translate-x-1/2 -translate-y-1/2 orbit-icon pointer-events-none"
                             :style="'transform: translateX(-50%) translateY(-50%) rotate(' + (activeIndex * 90) + 'deg)'">
                            <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-full flex items-center justify-center transition-all duration-500"
                                 :style="activeIndex === 0
                                     ? 'background: #3b82f6; box-shadow: 0 10px 40px rgba(59,130,246,0.5); transform: scale(1.25)'
                                     : 'background: #1e293b'">
                                <i class="fa-solid fa-leaf text-white text-lg"></i>
                            </div>
                            <div class="absolute inset-0 rounded-full border-2 scale-150 transition-all duration-500"
                                 :style="'border-color: #60a5fa; opacity: ' + (activeIndex === 0 ? '1' : '0')"
                                 :class="activeIndex === 0 ? 'animate-ping' : ''"></div>
                        </div>

                        {{-- Icon 2 - Right (Droplet) - Yeşil --}}
                        <div class="absolute top-1/2 right-0 translate-x-1/2 -translate-y-1/2 orbit-icon pointer-events-none"
                             :style="'transform: translateX(50%) translateY(-50%) rotate(' + (activeIndex * 90) + 'deg)'">
                            <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-full flex items-center justify-center transition-all duration-500"
                                 :style="activeIndex === 1
                                     ? 'background: #22c55e; box-shadow: 0 10px 40px rgba(34,197,94,0.5); transform: scale(1.25)'
                                     : 'background: #1e293b'">
                                <i class="fa-solid fa-droplet text-white text-lg"></i>
                            </div>
                            <div class="absolute inset-0 rounded-full border-2 scale-150 transition-all duration-500"
                                 :style="'border-color: #4ade80; opacity: ' + (activeIndex === 1 ? '1' : '0')"
                                 :class="activeIndex === 1 ? 'animate-ping' : ''"></div>
                        </div>

                        {{-- Icon 3 - Bottom (Recycle) - Turuncu --}}
                        <div class="absolute bottom-0 left-1/2 -translate-x-1/2 translate-y-1/2 orbit-icon pointer-events-none"
                             :style="'transform: translateX(-50%) translateY(50%) rotate(' + (activeIndex * 90) + 'deg)'">
                            <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-full flex items-center justify-center transition-all duration-500"
                                 :style="activeIndex === 2
                                     ? 'background: #f97316; box-shadow: 0 10px 40px rgba(249,115,22,0.5); transform: scale(1.25)'
                                     : 'background: #1e293b'">
                                <i class="fa-solid fa-recycle text-white text-lg"></i>
                            </div>
                            <div class="absolute inset-0 rounded-full border-2 scale-150 transition-all duration-500"
                                 :style="'border-color: #fb923c; opacity: ' + (activeIndex === 2 ? '1' : '0')"
                                 :class="activeIndex === 2 ? 'animate-ping' : ''"></div>
                        </div>

                        {{-- Icon 4 - Left (Cube) - Mor --}}
                        <div class="absolute top-1/2 left-0 -translate-x-1/2 -translate-y-1/2 orbit-icon pointer-events-none"
                             :style="'transform: translateX(-50%) translateY(-50%) rotate(' + (activeIndex * 90) + 'deg)'">
                            <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-full flex items-center justify-center transition-all duration-500"
                                 :style="activeIndex === 3
                                     ? 'background: #a855f7; box-shadow: 0 10px 40px rgba(168,85,247,0.5); transform: scale(1.25)'
                                     : 'background: #1e293b'">
                                <i class="fa-solid fa-cube text-white text-lg"></i>
                            </div>
                            <div class="absolute inset-0 rounded-full border-2 scale-150 transition-all duration-500"
                                 :style="'border-color: #c084fc; opacity: ' + (activeIndex === 3 ? '1' : '0')"
                                 :class="activeIndex === 3 ? 'animate-ping' : ''"></div>
                        </div>
                    </div>

                    {{-- Floating Stats Cards --}}
                    <div class="absolute -bottom-4 -left-4 sm:left-0 p-4 rounded-xl glass transition-all duration-700 pointer-events-none"
                         :style="activeIndex === 1 ? 'box-shadow: 0 0 0 2px rgba(34,197,94,0.5); transform: scale(1.1)' : ''">
                        <p class="text-xl sm:text-2xl font-bold text-white">5.000+</p>
                        <p class="text-xs text-white/60">Ton/Yıl Kapasite</p>
                    </div>

                    <div class="absolute -top-4 -right-4 sm:right-0 p-4 rounded-xl glass transition-all duration-700 pointer-events-none"
                         :style="activeIndex === 2 ? 'box-shadow: 0 0 0 2px rgba(249,115,22,0.5); transform: scale(1.1)' : ''">
                        <p class="text-xl sm:text-2xl font-bold text-white">25+</p>
                        <p class="text-xs text-white/60">Yıl Deneyim</p>
                    </div>

                    {{-- Progress Indicator --}}
                    <div class="absolute -bottom-12 left-1/2 -translate-x-1/2 flex gap-2 pointer-events-none">
                        <template x-for="(item, index) in items" :key="'dot-' + item.id">
                            <div class="h-2 rounded-full transition-all duration-500"
                                 :style="activeIndex === index
                                     ? 'background: ' + item.hex + '; width: 1.5rem'
                                     : 'background: rgba(255,255,255,0.3); width: 0.5rem'">
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ========== BLOG SECTION ========== --}}
    <section id="blog" class="py-20 md:py-28 lg:py-36 bg-white dark:bg-slate-950">
        <div class="container mx-auto">
            {{-- Section Header --}}
            <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6 mb-16" data-aos="fade-up">
                <div>
                    <span class="inline-flex items-center gap-2 bg-sky-100 dark:bg-sky-900/30 text-sky-600 dark:text-sky-400 px-4 py-2 rounded-full text-sm font-semibold mb-6">
                        <i class="fa-light fa-newspaper"></i>
                        <span>Blog & Haberler</span>
                    </span>
                    <h2 class="font-heading font-bold text-4xl md:text-5xl text-slate-900 dark:text-white mb-4">
                        Sektörel <span class="gradient-text">İçgörüler</span>
                    </h2>
                    <p class="text-lg text-slate-600 dark:text-slate-400 max-w-xl">
                        Endüstriyel ambalaj sektöründen güncel haberler, teknik bilgiler ve profesyonel öneriler
                    </p>
                </div>
                <a href="#" class="inline-flex items-center gap-2 text-sky-600 font-semibold hover:gap-3 transition-all">
                    <span>Tüm Yazılar</span>
                    <i class="fa-light fa-arrow-right"></i>
                </a>
            </div>

            {{-- Blog Grid --}}
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                {{-- Blog Card 1 --}}
                <article class="blog-card group bg-slate-50 dark:bg-slate-900 rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-800" data-aos="fade-up" data-aos-delay="100">
                    <div class="blog-image aspect-video overflow-hidden">
                        <img src="https://picsum.photos/800/450?random=600" alt="IBC Tank Bakım" class="w-full h-full object-cover">
                    </div>
                    <div class="p-6">
                        <div class="flex items-center gap-3 mb-4">
                            <span class="px-3 py-1 bg-sky-100 dark:bg-sky-900/30 text-sky-600 dark:text-sky-400 rounded-full text-xs font-medium">Rehber</span>
                            <span class="text-slate-400 text-xs">15 Ocak 2026</span>
                        </div>
                        <h3 class="font-heading font-bold text-xl text-slate-900 dark:text-white mb-3 line-clamp-2 group-hover:text-sky-600 transition-colors">
                            IBC Tank Bakımı: Uzun Ömürlü Kullanım İçin 10 İpucu
                        </h3>
                        <p class="text-slate-600 dark:text-slate-400 text-sm mb-4 line-clamp-3">
                            IBC konteynerlerinizin ömrünü uzatmak ve güvenli kullanım sağlamak için dikkat etmeniz gereken önemli bakım adımları.
                        </p>
                    </div>
                </article>

                {{-- Blog Card 2 --}}
                <article class="blog-card group bg-slate-50 dark:bg-slate-900 rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-800" data-aos="fade-up" data-aos-delay="200">
                    <div class="blog-image aspect-video overflow-hidden">
                        <img src="https://picsum.photos/800/450?random=601" alt="Geri Dönüşüm" class="w-full h-full object-cover">
                    </div>
                    <div class="p-6">
                        <div class="flex items-center gap-3 mb-4">
                            <span class="px-3 py-1 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-full text-xs font-medium">Sürdürülebilirlik</span>
                            <span class="text-slate-400 text-xs">10 Ocak 2026</span>
                        </div>
                        <h3 class="font-heading font-bold text-xl text-slate-900 dark:text-white mb-3 line-clamp-2 group-hover:text-sky-600 transition-colors">
                            Döngüsel Ekonomi ve Endüstriyel Ambalajın Rolü
                        </h3>
                        <p class="text-slate-600 dark:text-slate-400 text-sm mb-4 line-clamp-3">
                            Sürdürülebilir üretim modellerinde endüstriyel ambalajın önemi ve geri dönüşümün çevreye katkıları hakkında detaylı analiz.
                        </p>
                    </div>
                </article>

                {{-- Blog Card 3 --}}
                <article class="blog-card group bg-slate-50 dark:bg-slate-900 rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-800" data-aos="fade-up" data-aos-delay="300">
                    <div class="blog-image aspect-video overflow-hidden">
                        <img src="https://picsum.photos/800/450?random=602" alt="UN Sertifikası" class="w-full h-full object-cover">
                    </div>
                    <div class="p-6">
                        <div class="flex items-center gap-3 mb-4">
                            <span class="px-3 py-1 bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 rounded-full text-xs font-medium">Sertifikasyon</span>
                            <span class="text-slate-400 text-xs">5 Ocak 2026</span>
                        </div>
                        <h3 class="font-heading font-bold text-xl text-slate-900 dark:text-white mb-3 line-clamp-2 group-hover:text-sky-600 transition-colors">
                            UN/ADR Sertifikası Nedir? Neden Önemlidir?
                        </h3>
                        <p class="text-slate-600 dark:text-slate-400 text-sm mb-4 line-clamp-3">
                            Tehlikeli madde taşımacılığında UN/ADR standartlarının önemi ve sertifikalı ürün kullanmanın avantajları.
                        </p>
                    </div>
                </article>
            </div>
        </div>
    </section>

    {{-- ========== CTA SECTION ========== --}}
    <section class="py-20 md:py-28 bg-slate-50 dark:bg-slate-900">
        <div class="container mx-auto">
            <div class="relative bg-slate-900 dark:bg-slate-800 rounded-3xl p-12 md:p-16 lg:p-20 overflow-hidden" data-aos="fade-up">
                {{-- Animated Background --}}
                <div class="cta-animated-bg"></div>
                <div class="absolute top-0 right-0 w-96 h-96 bg-sky-500/30 rounded-full blur-3xl animate-pulse"></div>
                <div class="absolute bottom-0 left-0 w-96 h-96 bg-cyan-500/20 rounded-full blur-3xl animate-pulse" style="animation-delay: 1s;"></div>
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-sky-600/10 rounded-full blur-3xl animate-ping" style="animation-duration: 4s;"></div>

                <div class="relative z-10 flex flex-col lg:flex-row items-center justify-between gap-10">
                    <div class="text-center lg:text-left">
                        <h2 class="font-heading font-bold text-3xl md:text-4xl lg:text-5xl text-white mb-6">
                            Projeniz İçin
                            <span class="cta-gradient-text">Teklif Alın</span>
                        </h2>
                        <p class="text-lg text-slate-400 max-w-xl">
                            Endüstriyel ambalaj ihtiyaçlarınız için size özel çözümler sunalım. Uzman ekibimiz 24 saat içinde size dönüş yapacaktır.
                        </p>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="#iletisim" class="inline-flex items-center justify-center gap-3 gradient-shift text-white px-10 py-5 rounded-xl font-semibold shadow-lg shadow-sky-500/30 hover:shadow-sky-500/50 transition-all text-lg">
                            <span>Teklif Al</span>
                            <i class="fa-light fa-arrow-right"></i>
                        </a>
                        <a href="tel:{{ setting('site_phone') }}" class="inline-flex items-center justify-center gap-3 bg-white/10 backdrop-blur-sm hover:bg-white/20 text-white px-10 py-5 rounded-xl font-semibold transition-all border border-white/20 text-lg">
                            <i class="fa-light fa-phone"></i>
                            <span>Hemen Arayın</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
<script>
    // Hero Swiper
    const heroSwiper = new Swiper('.hero-swiper', {
        loop: true,
        speed: 800,
        autoplay: {
            delay: 6000,
            disableOnInteraction: false,
        },
        effect: 'fade',
        fadeEffect: {
            crossFade: true
        },
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },
        navigation: {
            nextEl: '.hero-next',
            prevEl: '.hero-prev',
        },
    });
</script>
@endpush

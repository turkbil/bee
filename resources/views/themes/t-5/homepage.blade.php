@extends('themes.t-5.layouts.app')

@php
    // Settings
    $siteName = setting('site_title', 'Ecrin Turizm');
    $siteSlogan = setting('site_slogan', 'Olçun Travel');
    $phone = setting('contact_phone_1', '0546 810 17 17');
    $phoneClean = preg_replace('/[^0-9]/', '', $phone);
    $whatsapp = setting('contact_whatsapp_1', '905468101717');
    $email = setting('contact_email_1', 'info@ecrinturizm.org');
    $address = setting('contact_address_line_1', 'Güngören / İstanbul');
    $workingHours = setting('contact_working_hours', '7/24 Hizmet');

    // Services from database
    $services = \Modules\Service\App\Models\Service::where('is_active', true)
        ->whereNull('deleted_at')
        ->orderBy('created_at')
        ->get();

    // Icon map for services
    $iconMap = [
        'turizm-tasimaciligi' => 'fa-bus',
        'personel-tasimaciligi' => 'fa-people-group',
        'ogrenci-tasimaciligi' => 'fa-school-flag',
        'otel-rezervasyonlari' => 'fa-hotel',
        'yat-kiralama' => 'fa-sailboat',
    ];
@endphp

@section('content')
    <!-- HERO SECTION -->
    <section id="anasayfa" class="relative min-h-screen flex items-center bg-gradient-to-br from-sky-50 via-blue-50 to-white dark:from-slate-900 dark:via-slate-800 dark:to-slate-900 overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-30 dark:opacity-10">
            <div class="absolute top-20 left-10 w-72 h-72 bg-sky-300 rounded-full mix-blend-multiply filter blur-3xl"></div>
            <div class="absolute bottom-20 right-10 w-96 h-96 bg-blue-300 rounded-full mix-blend-multiply filter blur-3xl"></div>
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-cyan-200 rounded-full mix-blend-multiply filter blur-3xl"></div>
        </div>

        <div class="container mx-auto  relative z-10">
            <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center py-32">
                <!-- Left Content -->
                <div class="text-center lg:text-left" data-aos="fade-right" data-aos-duration="1000">
                    <!-- Badge -->
                    <div class="inline-flex items-center space-x-2 px-4 py-2 bg-sky-100 dark:bg-sky-900/30 text-sky-700 dark:text-sky-300 rounded-full text-sm font-medium mb-6">
                        <i class="fat fa-certificate"></i>
                        <span>Profesyonel Turizm Çözümleri</span>
                    </div>

                    <!-- Main Title -->
                    <h1 class="font-heading text-4xl sm:text-5xl lg:text-6xl xl:text-7xl font-bold text-slate-900 dark:text-white leading-tight mb-6">
                        Güvenle Yolculuk,
                        <span class="animated-gradient-text">Huzurla Varış</span>
                    </h1>

                    <!-- Subtitle -->
                    <p class="text-lg sm:text-xl text-slate-600 dark:text-slate-400 leading-relaxed mb-8 max-w-xl mx-auto lg:mx-0">
                        2008'den beri A Grubu Seyahat Acentası olarak profesyonel taşımacılık hizmetleri sunuyoruz. Güvenliğiniz bizim önceliğimiz.
                    </p>

                    <!-- CTA Buttons -->
                    <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4">
                        <a href="{{ module_locale_url('service', 'index') }}" class="w-full sm:w-auto inline-flex items-center justify-center space-x-2 px-8 py-4 bg-gradient-to-r from-sky-500 to-blue-600 text-white font-semibold rounded-lg hover:from-sky-600 hover:to-blue-700 transition-all shadow-lg shadow-sky-500/30 hover:shadow-xl hover:shadow-sky-500/40">
                            <span>Hizmetlerimiz</span>
                            <i class="fat fa-arrow-right"></i>
                        </a>
                        <a href="{{ module_locale_url('page', 'show', ['iletisim']) }}" class="w-full sm:w-auto inline-flex items-center justify-center space-x-2 px-8 py-4 border-2 border-sky-500 text-sky-600 dark:text-sky-400 font-semibold rounded-lg hover:bg-sky-50 dark:hover:bg-sky-900/20 transition-all">
                            <i class="fat fa-phone-volume"></i>
                            <span>İletişim</span>
                        </a>
                    </div>

                    <!-- Trust Badges -->
                    <div class="flex items-center justify-center lg:justify-start gap-6 mt-10 pt-10 border-t border-slate-200 dark:border-slate-700">
                        <div class="flex items-center space-x-2 text-slate-600 dark:text-slate-400">
                            <i class="fat fa-shield-check text-sky-500 text-xl"></i>
                            <span class="text-sm font-medium">A Grubu Lisans</span>
                        </div>
                        <div class="flex items-center space-x-2 text-slate-600 dark:text-slate-400">
                            <i class="fat fa-clock text-sky-500 text-xl"></i>
                            <span class="text-sm font-medium">{{ $workingHours }}</span>
                        </div>
                        <div class="flex items-center space-x-2 text-slate-600 dark:text-slate-400">
                            <i class="fat fa-location-dot text-sky-500 text-xl"></i>
                            <span class="text-sm font-medium">GPS Takip</span>
                        </div>
                    </div>
                </div>

                <!-- Right Image -->
                <div class="relative" data-aos="fade-left" data-aos-duration="1000" data-aos-delay="200">
                    <div class="relative">
                        <!-- Main Image -->
                        <div class="gradient-border">
                            <div class="gradient-border-inner p-2">
                                <img src="https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?w=800&h=600&fit=crop" alt="Turizm Taşımacılığı" class="w-full h-auto rounded-xl object-cover">
                            </div>
                        </div>

                        <!-- Floating Card 1 -->
                        <div class="absolute -bottom-6 -left-6 bg-white dark:bg-slate-800 rounded-xl shadow-2xl p-4 flex items-center space-x-3" data-aos="fade-up" data-aos-delay="400">
                            <div class="w-12 h-12 bg-gradient-to-br from-sky-500 to-blue-600 rounded-lg flex items-center justify-center">
                                <i class="fat fa-calendar-check text-white text-xl"></i>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-slate-900 dark:text-white">2008</p>
                                <p class="text-sm text-slate-500 dark:text-slate-400">Kuruluş</p>
                            </div>
                        </div>

                        <!-- Floating Card 2 -->
                        <div class="absolute -top-4 -right-4 bg-white dark:bg-slate-800 rounded-xl shadow-2xl p-4 flex items-center space-x-3" data-aos="fade-down" data-aos-delay="500">
                            <div class="w-12 h-12 bg-gradient-to-br from-amber-400 to-orange-500 rounded-lg flex items-center justify-center">
                                <i class="fat fa-star text-white text-xl"></i>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-slate-900 dark:text-white">9817</p>
                                <p class="text-sm text-slate-500 dark:text-slate-400">Belge No</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Scroll Indicator -->
        <div class="absolute bottom-8 left-1/2 -translate-x-1/2 scroll-indicator">
            <a href="#stats" class="flex flex-col items-center text-slate-500 dark:text-slate-400 hover:text-sky-600 transition-colors">
                <span class="text-sm mb-2">Keşfet</span>
                <i class="fat fa-chevron-down text-xl"></i>
            </a>
        </div>
    </section>

    <!-- STATS SECTION -->
    <section id="stats" class="py-16 md:py-24 bg-white dark:bg-slate-900">
        <div class="container mx-auto ">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 lg:gap-8">
                <!-- Stat 1 -->
                <div class="text-center p-6 lg:p-8 bg-gradient-to-br from-sky-50 to-blue-50 dark:from-slate-800 dark:to-slate-800/50 rounded-2xl" data-aos="fade-up" data-aos-delay="100">
                    <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-sky-500 to-blue-600 rounded-xl flex items-center justify-center">
                        <i class="fat fa-certificate text-white text-2xl"></i>
                    </div>
                    <p class="stat-value text-3xl lg:text-4xl font-bold mb-2">A Grubu</p>
                    <p class="text-slate-600 dark:text-slate-400 font-medium">Seyahat Acentası</p>
                </div>

                <!-- Stat 2 -->
                <div class="text-center p-6 lg:p-8 bg-gradient-to-br from-sky-50 to-blue-50 dark:from-slate-800 dark:to-slate-800/50 rounded-2xl" data-aos="fade-up" data-aos-delay="200">
                    <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-sky-500 to-blue-600 rounded-xl flex items-center justify-center">
                        <i class="fat fa-bus text-white text-2xl"></i>
                    </div>
                    <p class="stat-value text-3xl lg:text-4xl font-bold mb-2">Geniş</p>
                    <p class="text-slate-600 dark:text-slate-400 font-medium">Araç Filosu</p>
                </div>

                <!-- Stat 3 -->
                <div class="text-center p-6 lg:p-8 bg-gradient-to-br from-sky-50 to-blue-50 dark:from-slate-800 dark:to-slate-800/50 rounded-2xl" data-aos="fade-up" data-aos-delay="300">
                    <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-sky-500 to-blue-600 rounded-xl flex items-center justify-center">
                        <i class="fat fa-users text-white text-2xl"></i>
                    </div>
                    <p class="stat-value text-3xl lg:text-4xl font-bold mb-2">Binlerce</p>
                    <p class="text-slate-600 dark:text-slate-400 font-medium">Mutlu Müşteri</p>
                </div>

                <!-- Stat 4 -->
                <div class="text-center p-6 lg:p-8 bg-gradient-to-br from-sky-50 to-blue-50 dark:from-slate-800 dark:to-slate-800/50 rounded-2xl" data-aos="fade-up" data-aos-delay="400">
                    <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-sky-500 to-blue-600 rounded-xl flex items-center justify-center">
                        <i class="fat fa-headset text-white text-2xl"></i>
                    </div>
                    <p class="stat-value text-3xl lg:text-4xl font-bold mb-2">{{ $workingHours }}</p>
                    <p class="text-slate-600 dark:text-slate-400 font-medium">Destek</p>
                </div>
            </div>
        </div>
    </section>

    <!-- SERVICES SECTION (Dynamic) -->
    <section id="hizmetler" class="py-16 md:py-24 bg-slate-50 dark:bg-slate-800">
        <div class="container mx-auto ">
            <!-- Section Header -->
            <div class="text-center max-w-3xl mx-auto mb-16" data-aos="fade-up">
                <span class="inline-block px-4 py-2 bg-sky-100 dark:bg-sky-900/30 text-sky-700 dark:text-sky-300 rounded-full text-sm font-medium mb-4">
                    Hizmetlerimiz
                </span>
                <h2 class="font-heading text-3xl sm:text-4xl lg:text-5xl font-bold text-slate-900 dark:text-white mb-6">
                    Profesyonel <span class="gradient-text">Çözümler</span>
                </h2>
                <p class="text-lg text-slate-600 dark:text-slate-400">
                    Profesyonel ve güvenilir taşımacılık çözümleriyle hizmetinizdeyiz
                </p>
            </div>

            <!-- Services Grid (Visual-heavy: mobile 2 col, tablet+ 3 col) -->
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 md:gap-6 lg:gap-8">
                @foreach($services as $index => $service)
                    @php
                        $slug = $service->getTranslation('slug', 'tr') ?? '';
                        $icon = $iconMap[$slug] ?? 'fa-concierge-bell';
                        $bodyContent = $service->getTranslation('body', 'tr') ?? '';
                        // HTML'den ilk paragrafı al
                        preg_match('/<p>(.*?)<\/p>/s', $bodyContent, $matches);
                        $shortDesc = isset($matches[1]) ? strip_tags($matches[1]) : Str::limit(strip_tags($bodyContent), 150);
                    @endphp
                    <div class="card-hover bg-white dark:bg-slate-900 rounded-2xl p-8 shadow-lg" data-aos="fade-up" data-aos-delay="{{ ($index + 1) * 100 }}">
                        <div class="w-16 h-16 bg-gradient-to-br from-sky-100 to-blue-100 dark:from-sky-900/50 dark:to-blue-900/50 rounded-xl flex items-center justify-center mb-6">
                            <i class="fat {{ $icon }} icon-hover text-sky-600 dark:text-sky-400 text-3xl"></i>
                        </div>
                        <h3 class="title-hover font-heading text-xl font-bold text-slate-900 dark:text-white mb-4 transition-colors">
                            {{ $service->getTranslation('title', app()->getLocale()) }}
                        </h3>
                        <p class="text-slate-600 dark:text-slate-400 leading-relaxed mb-6">
                            {{ $shortDesc }}
                        </p>
                        <a href="{{ $service->getUrl() }}" class="inline-flex items-center text-sky-600 dark:text-sky-400 font-medium hover:text-sky-700 transition-colors">
                            <span>Detaylı Bilgi</span>
                            <i class="fat fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                @endforeach

                <!-- CTA Card -->
                <div class="bg-gradient-to-br from-sky-500 to-blue-600 rounded-2xl p-8 flex flex-col justify-center text-center text-white" data-aos="fade-up" data-aos-delay="{{ (count($services) + 1) * 100 }}">
                    <i class="fat fa-phone-volume text-5xl mb-6 opacity-90"></i>
                    <h3 class="font-heading text-2xl font-bold mb-4">Hemen Arayın</h3>
                    <p class="text-sky-100 mb-6">Size en uygun hizmeti birlikte belirleyelim</p>
                    <a href="tel:+90{{ $phoneClean }}" class="inline-flex items-center justify-center space-x-2 py-4 px-8 bg-white text-sky-600 font-semibold rounded-lg hover:bg-sky-50 transition-colors">
                        <i class="fat fa-phone"></i>
                        <span>{{ $phone }}</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- WHY US SECTION -->
    <section class="py-16 md:py-24 bg-white dark:bg-slate-900">
        <div class="container mx-auto ">
            <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">
                <!-- Left Image -->
                <div class="relative" data-aos="fade-right">
                    <div class="gradient-border">
                        <div class="gradient-border-inner p-2">
                            <img src="https://images.unsplash.com/photo-1449965408869-eaa3f722e40d?w=700&h=500&fit=crop" alt="Neden {{ $siteName }}" class="w-full h-auto rounded-xl object-cover" loading="lazy">
                        </div>
                    </div>

                    <!-- Badge -->
                    <div class="absolute -bottom-6 -right-6 bg-white dark:bg-slate-800 rounded-2xl shadow-2xl p-6 max-w-xs" data-aos="fade-up" data-aos-delay="200">
                        <div class="flex items-center space-x-4">
                            <div class="w-14 h-14 bg-gradient-to-br from-amber-400 to-orange-500 rounded-xl flex items-center justify-center flex-shrink-0">
                                <i class="fat fa-award text-white text-2xl"></i>
                            </div>
                            <div>
                                <p class="font-heading font-bold text-slate-900 dark:text-white">A Grubu Lisanslı</p>
                                <p class="text-sm text-slate-500 dark:text-slate-400">T.C. Kültür ve Turizm Bakanlığı</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Content -->
                <div data-aos="fade-left">
                    <span class="inline-block px-4 py-2 bg-sky-100 dark:bg-sky-900/30 text-sky-700 dark:text-sky-300 rounded-full text-sm font-medium mb-4">
                        Neden Biz?
                    </span>
                    <h2 class="font-heading text-3xl sm:text-4xl lg:text-5xl font-bold text-slate-900 dark:text-white mb-6">
                        Neden <span class="gradient-text">{{ $siteName }}?</span>
                    </h2>
                    <p class="text-lg text-slate-600 dark:text-slate-400 mb-10">
                        Yılların deneyimi ve profesyonel kadromuzla güvenli, konforlu ve kaliteli hizmet sunuyoruz.
                    </p>

                    <!-- Features Grid -->
                    <div class="grid sm:grid-cols-2 gap-6">
                        <div class="flex items-start space-x-4">
                            <div class="w-12 h-12 bg-sky-100 dark:bg-sky-900/50 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fat fa-shield-check text-sky-600 dark:text-sky-400 text-xl"></i>
                            </div>
                            <div>
                                <h4 class="font-heading font-semibold text-slate-900 dark:text-white mb-1">A Grubu Lisans</h4>
                                <p class="text-sm text-slate-600 dark:text-slate-400">Resmi güvence ile hizmet</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-4">
                            <div class="w-12 h-12 bg-sky-100 dark:bg-sky-900/50 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fat fa-bus text-sky-600 dark:text-sky-400 text-xl"></i>
                            </div>
                            <div>
                                <h4 class="font-heading font-semibold text-slate-900 dark:text-white mb-1">Modern Filo</h4>
                                <p class="text-sm text-slate-600 dark:text-slate-400">Geniş araç seçenekleri</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-4">
                            <div class="w-12 h-12 bg-sky-100 dark:bg-sky-900/50 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fat fa-users text-sky-600 dark:text-sky-400 text-xl"></i>
                            </div>
                            <div>
                                <h4 class="font-heading font-semibold text-slate-900 dark:text-white mb-1">Profesyonel Kadro</h4>
                                <p class="text-sm text-slate-600 dark:text-slate-400">Deneyimli ekip</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-4">
                            <div class="w-12 h-12 bg-sky-100 dark:bg-sky-900/50 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fat fa-headset text-sky-600 dark:text-sky-400 text-xl"></i>
                            </div>
                            <div>
                                <h4 class="font-heading font-semibold text-slate-900 dark:text-white mb-1">{{ $workingHours }}</h4>
                                <p class="text-sm text-slate-600 dark:text-slate-400">Her an yanınızdayız</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-4">
                            <div class="w-12 h-12 bg-sky-100 dark:bg-sky-900/50 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fat fa-shield-halved text-sky-600 dark:text-sky-400 text-xl"></i>
                            </div>
                            <div>
                                <h4 class="font-heading font-semibold text-slate-900 dark:text-white mb-1">Tam Güvenlik</h4>
                                <p class="text-sm text-slate-600 dark:text-slate-400">Sigorta güvencesi</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-4">
                            <div class="w-12 h-12 bg-sky-100 dark:bg-sky-900/50 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fat fa-location-crosshairs text-sky-600 dark:text-sky-400 text-xl"></i>
                            </div>
                            <div>
                                <h4 class="font-heading font-semibold text-slate-900 dark:text-white mb-1">GPS Takip</h4>
                                <p class="text-sm text-slate-600 dark:text-slate-400">Canlı konum izleme</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ABOUT SECTION -->
    <section id="hakkimizda" class="py-16 md:py-24 bg-white dark:bg-slate-900">
        <div class="container mx-auto ">
            <!-- Section Header -->
            <div class="text-center max-w-3xl mx-auto mb-16" data-aos="fade-up">
                <span class="inline-block px-4 py-2 bg-sky-100 dark:bg-sky-900/30 text-sky-700 dark:text-sky-300 rounded-full text-sm font-medium mb-4">
                    Hakkımızda
                </span>
                <h2 class="font-heading text-3xl sm:text-4xl lg:text-5xl font-bold text-slate-900 dark:text-white mb-6">
                    <span class="gradient-text">Hikayemiz</span>
                </h2>
            </div>

            <!-- Story Content -->
            <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center mb-16">
                <div data-aos="fade-right">
                    <div class="prose prose-lg dark:prose-invert max-w-none">
                        <p class="text-slate-600 dark:text-slate-400 leading-relaxed mb-6">
                            <strong class="text-slate-900 dark:text-white">{{ $siteName }} Sanayi ve Ticaret Limited Şirketi</strong>, 2008 yılında İstanbul'da kurulmuştur. "{{ $siteSlogan }}" markasıyla hizmet veren firmamız, A Grubu Seyahat Acentası İşletme Belgesi (No: 9817) ile faaliyetlerini sürdürmektedir.
                        </p>
                        <p class="text-slate-600 dark:text-slate-400 leading-relaxed mb-6">
                            Kuruluşumuzdan bu yana, turizm ve taşımacılık sektöründe güvenilir, kaliteli ve müşteri odaklı hizmet anlayışımızla yolcularımızın konforunu ve güvenliğini ön planda tutarak hizmet veriyoruz.
                        </p>
                        <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                            {{ $address }} merkezli olarak faaliyet göstermekteyiz. Turizm taşımacılığı, personel servisleri, öğrenci taşımacılığı, otel rezervasyonları ve yat kiralama alanlarında geniş bir hizmet yelpazesi sunmaktayız.
                        </p>
                    </div>
                    <div class="mt-8">
                        <a href="{{ module_locale_url('page', 'show', ['hakkimizda']) }}" class="inline-flex items-center text-sky-600 dark:text-sky-400 font-medium hover:text-sky-700 transition-colors">
                            <span>Daha Fazla Bilgi</span>
                            <i class="fat fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                </div>

                <div data-aos="fade-left">
                    <div class="gradient-border">
                        <div class="gradient-border-inner p-2">
                            <img src="https://images.unsplash.com/photo-1557223562-6c77ef16210f?w=700&h=500&fit=crop" alt="Hakkımızda" class="w-full h-auto rounded-xl object-cover" loading="lazy">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mission & Vision -->
            <div class="grid md:grid-cols-2 gap-8 mb-16">
                <!-- Mission -->
                <div class="bg-gradient-to-br from-sky-50 to-blue-50 dark:from-slate-800 dark:to-slate-800/50 rounded-2xl p-8" data-aos="fade-up" data-aos-delay="100">
                    <div class="w-14 h-14 bg-gradient-to-br from-sky-500 to-blue-600 rounded-xl flex items-center justify-center mb-6">
                        <i class="fat fa-bullseye text-white text-2xl"></i>
                    </div>
                    <h3 class="font-heading text-2xl font-bold text-slate-900 dark:text-white mb-4">Misyonumuz</h3>
                    <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                        Turizm ve taşımacılık sektöründe güvenilir, kaliteli ve müşteri odaklı hizmet anlayışıyla, yolcularımızın konforunu ve güvenliğini ön planda tutarak profesyonel çözümler sunmak.
                    </p>
                </div>

                <!-- Vision -->
                <div class="bg-gradient-to-br from-sky-50 to-blue-50 dark:from-slate-800 dark:to-slate-800/50 rounded-2xl p-8" data-aos="fade-up" data-aos-delay="200">
                    <div class="w-14 h-14 bg-gradient-to-br from-sky-500 to-blue-600 rounded-xl flex items-center justify-center mb-6">
                        <i class="fat fa-eye text-white text-2xl"></i>
                    </div>
                    <h3 class="font-heading text-2xl font-bold text-slate-900 dark:text-white mb-4">Vizyonumuz</h3>
                    <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                        Türkiye'nin en çok tercih edilen, güvenilir ve yenilikçi turizm ve taşımacılık markası olmak. Sektöre yön veren, müşteri memnuniyetinde örnek gösterilen bir kurum olmaktır.
                    </p>
                </div>
            </div>

            <!-- Values -->
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="card-hover bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-6" data-aos="fade-up" data-aos-delay="100">
                    <i class="fat fa-shield-heart icon-hover text-sky-500 text-3xl mb-4"></i>
                    <h4 class="font-heading font-semibold text-slate-900 dark:text-white mb-2">Güven</h4>
                    <p class="text-sm text-slate-600 dark:text-slate-400">Müşterilerimizin bize emanet ettiği en değerli varlıkları güvenle taşımak.</p>
                </div>

                <div class="card-hover bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-6" data-aos="fade-up" data-aos-delay="200">
                    <i class="fat fa-gem icon-hover text-sky-500 text-3xl mb-4"></i>
                    <h4 class="font-heading font-semibold text-slate-900 dark:text-white mb-2">Kalite</h4>
                    <p class="text-sm text-slate-600 dark:text-slate-400">İş süreçlerimizde en yüksek kalite standartlarını benimseyerek çalışırız.</p>
                </div>

                <div class="card-hover bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-6" data-aos="fade-up" data-aos-delay="300">
                    <i class="fat fa-users-gear icon-hover text-sky-500 text-3xl mb-4"></i>
                    <h4 class="font-heading font-semibold text-slate-900 dark:text-white mb-2">Müşteri Odaklılık</h4>
                    <p class="text-sm text-slate-600 dark:text-slate-400">Müşterilerimizin ihtiyaçlarını dinler, beklentilerini aşmak için çaba gösteririz.</p>
                </div>

                <div class="card-hover bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-6" data-aos="fade-up" data-aos-delay="400">
                    <i class="fat fa-briefcase icon-hover text-sky-500 text-3xl mb-4"></i>
                    <h4 class="font-heading font-semibold text-slate-900 dark:text-white mb-2">Profesyonellik</h4>
                    <p class="text-sm text-slate-600 dark:text-slate-400">İşimizi tutkuyla yapar, profesyonel bir ekip anlayışıyla hareket ederiz.</p>
                </div>

                <div class="card-hover bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-6" data-aos="fade-up" data-aos-delay="500">
                    <i class="fat fa-shield-check icon-hover text-sky-500 text-3xl mb-4"></i>
                    <h4 class="font-heading font-semibold text-slate-900 dark:text-white mb-2">Güvenlik</h4>
                    <p class="text-sm text-slate-600 dark:text-slate-400">Yolcularımızın güvenliği her zaman en önemli önceliğimizdir.</p>
                </div>

                <div class="card-hover bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-6" data-aos="fade-up" data-aos-delay="600">
                    <i class="fat fa-leaf icon-hover text-sky-500 text-3xl mb-4"></i>
                    <h4 class="font-heading font-semibold text-slate-900 dark:text-white mb-2">Sürdürülebilirlik</h4>
                    <p class="text-sm text-slate-600 dark:text-slate-400">Çevreye duyarlı, sosyal sorumluluğa önem veren bir kurum olarak faaliyet gösteririz.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- LICENSE SECTION -->
    <section class="py-16 md:py-24 bg-slate-50 dark:bg-slate-800">
        <div class="container mx-auto ">
            <div class="max-w-4xl mx-auto">
                <div class="bg-gradient-to-br from-sky-500 to-blue-600 rounded-3xl p-8 md:p-12 text-center text-white" data-aos="fade-up">
                    <div class="w-20 h-20 mx-auto mb-6 bg-white/20 rounded-2xl flex items-center justify-center">
                        <i class="fat fa-certificate text-4xl"></i>
                    </div>
                    <h3 class="font-heading text-2xl md:text-3xl font-bold mb-2">A Grubu Seyahat Acentası</h3>
                    <p class="text-sky-100 mb-6">İşletme Belgesi</p>

                    <div class="grid sm:grid-cols-3 gap-6 text-center">
                        <div>
                            <p class="text-3xl font-bold mb-1">9817</p>
                            <p class="text-sky-100 text-sm">Belge No</p>
                        </div>
                        <div>
                            <p class="text-3xl font-bold mb-1">17.09.2008</p>
                            <p class="text-sky-100 text-sm">Kuruluş Tarihi</p>
                        </div>
                        <div>
                            <p class="text-3xl font-bold mb-1">İstanbul</p>
                            <p class="text-sky-100 text-sm">Güngören</p>
                        </div>
                    </div>

                    <div class="mt-8 pt-8 border-t border-white/20">
                        <p class="text-sky-100 text-sm">
                            <i class="fat fa-landmark mr-2"></i>
                            T.C. Kültür ve Turizm Bakanlığı onaylı
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CONTACT SECTION -->
    <section id="iletisim" class="py-16 md:py-24 bg-white dark:bg-slate-900">
        <div class="container mx-auto ">
            <!-- Section Header -->
            <div class="text-center max-w-3xl mx-auto mb-16" data-aos="fade-up">
                <span class="inline-block px-4 py-2 bg-sky-100 dark:bg-sky-900/30 text-sky-700 dark:text-sky-300 rounded-full text-sm font-medium mb-4">
                    İletişim
                </span>
                <h2 class="font-heading text-3xl sm:text-4xl lg:text-5xl font-bold text-slate-900 dark:text-white mb-6">
                    Bizimle <span class="gradient-text">İletişime Geçin</span>
                </h2>
                <p class="text-lg text-slate-600 dark:text-slate-400">
                    Her an ulaşabileceğiniz profesyonel destek ekibimiz
                </p>
            </div>

            <div class="grid lg:grid-cols-2 gap-12 lg:gap-16">
                <!-- Contact Info -->
                <div data-aos="fade-right">
                    <div class="space-y-6">
                        <!-- Phone -->
                        <a href="tel:+90{{ $phoneClean }}" class="card-hover flex items-center space-x-6 p-6 bg-slate-50 dark:bg-slate-800 rounded-2xl group">
                            <div class="w-16 h-16 bg-gradient-to-br from-sky-500 to-blue-600 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg shadow-sky-500/30">
                                <i class="fat fa-phone icon-hover text-white text-2xl"></i>
                            </div>
                            <div>
                                <p class="text-sm text-slate-500 dark:text-slate-400 mb-1">Telefon</p>
                                <p class="title-hover text-xl font-bold text-slate-900 dark:text-white transition-colors">{{ $phone }}</p>
                            </div>
                        </a>

                        <!-- WhatsApp -->
                        <a href="https://wa.me/{{ $whatsapp }}" target="_blank" class="card-hover flex items-center space-x-6 p-6 bg-slate-50 dark:bg-slate-800 rounded-2xl group">
                            <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg shadow-green-500/30">
                                <i class="fab fa-whatsapp icon-hover text-white text-2xl"></i>
                            </div>
                            <div>
                                <p class="text-sm text-slate-500 dark:text-slate-400 mb-1">WhatsApp</p>
                                <p class="title-hover text-xl font-bold text-slate-900 dark:text-white transition-colors">{{ $phone }}</p>
                            </div>
                        </a>

                        <!-- Email -->
                        <a href="mailto:{{ $email }}" class="card-hover flex items-center space-x-6 p-6 bg-slate-50 dark:bg-slate-800 rounded-2xl group">
                            <div class="w-16 h-16 bg-gradient-to-br from-sky-500 to-blue-600 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg shadow-sky-500/30">
                                <i class="fat fa-envelope icon-hover text-white text-2xl"></i>
                            </div>
                            <div>
                                <p class="text-sm text-slate-500 dark:text-slate-400 mb-1">E-posta</p>
                                <p class="title-hover text-xl font-bold text-slate-900 dark:text-white transition-colors">{{ $email }}</p>
                            </div>
                        </a>

                        <!-- Address -->
                        <div class="card-hover flex items-center space-x-6 p-6 bg-slate-50 dark:bg-slate-800 rounded-2xl">
                            <div class="w-16 h-16 bg-gradient-to-br from-sky-500 to-blue-600 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg shadow-sky-500/30">
                                <i class="fat fa-location-dot icon-hover text-white text-2xl"></i>
                            </div>
                            <div>
                                <p class="text-sm text-slate-500 dark:text-slate-400 mb-1">Adres</p>
                                <p class="title-hover text-xl font-bold text-slate-900 dark:text-white transition-colors">{{ $address }}</p>
                            </div>
                        </div>

                        <!-- Working Hours -->
                        <div class="card-hover flex items-center space-x-6 p-6 bg-slate-50 dark:bg-slate-800 rounded-2xl">
                            <div class="w-16 h-16 bg-gradient-to-br from-amber-400 to-orange-500 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg shadow-amber-500/30">
                                <i class="fat fa-clock icon-hover text-white text-2xl"></i>
                            </div>
                            <div>
                                <p class="text-sm text-slate-500 dark:text-slate-400 mb-1">Çalışma Saatleri</p>
                                <p class="title-hover text-xl font-bold text-slate-900 dark:text-white transition-colors">{{ $workingHours }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact CTA -->
                <div data-aos="fade-left">
                    <div class="bg-gradient-to-br from-sky-50 to-blue-50 dark:from-slate-800 dark:to-slate-800/50 rounded-2xl p-8 h-full flex flex-col justify-center">
                        <div class="text-center">
                            <div class="w-20 h-20 mx-auto mb-6 bg-gradient-to-br from-sky-500 to-blue-600 rounded-2xl flex items-center justify-center">
                                <i class="fat fa-phone-volume text-white text-3xl"></i>
                            </div>
                            <h3 class="font-heading text-2xl font-bold text-slate-900 dark:text-white mb-4">Hemen Bize Ulaşın</h3>
                            <p class="text-slate-600 dark:text-slate-400 mb-8">
                                Sorularınız için bizi arayın veya WhatsApp'tan yazın. Ekibimiz size yardımcı olmak için hazır.
                            </p>
                            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                                <a href="tel:+90{{ $phoneClean }}" class="inline-flex items-center justify-center space-x-2 px-8 py-4 bg-gradient-to-r from-sky-500 to-blue-600 text-white font-semibold rounded-lg hover:from-sky-600 hover:to-blue-700 transition-all shadow-lg shadow-sky-500/30">
                                    <i class="fat fa-phone"></i>
                                    <span>{{ $phone }}</span>
                                </a>
                                <a href="https://wa.me/{{ $whatsapp }}" target="_blank" class="inline-flex items-center justify-center space-x-2 px-8 py-4 bg-green-500 text-white font-semibold rounded-lg hover:bg-green-600 transition-all shadow-lg shadow-green-500/30">
                                    <i class="fab fa-whatsapp"></i>
                                    <span>WhatsApp</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA SECTION -->
    <section class="py-16 md:py-24 bg-gradient-to-r from-sky-500 to-blue-600">
        <div class="container mx-auto ">
            <div class="text-center text-white" data-aos="fade-up">
                <h2 class="font-heading text-3xl sm:text-4xl lg:text-5xl font-bold mb-6">
                    Hemen Arayın veya WhatsApp'tan Yazın
                </h2>
                <p class="text-xl text-sky-100 mb-10 max-w-2xl mx-auto">
                    Profesyonel ekibimiz size yardımcı olmak için hazır
                </p>
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    <a href="tel:+90{{ $phoneClean }}" class="w-full sm:w-auto inline-flex items-center justify-center space-x-3 px-8 py-4 bg-white text-sky-600 font-semibold rounded-lg hover:bg-sky-50 transition-all shadow-lg">
                        <i class="fat fa-phone text-xl"></i>
                        <span>{{ $phone }}</span>
                    </a>
                    <a href="https://wa.me/{{ $whatsapp }}" target="_blank" class="w-full sm:w-auto inline-flex items-center justify-center space-x-3 px-8 py-4 bg-green-500 text-white font-semibold rounded-lg hover:bg-green-600 transition-all shadow-lg">
                        <i class="fab fa-whatsapp text-xl"></i>
                        <span>WhatsApp</span>
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection

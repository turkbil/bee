@extends('themes.t-5.layouts.app')

@php
    $siteName = setting('site_title', 'Ecrin Turizm');
    $phone = setting('contact_phone_1', '0546 810 17 17');
    $phoneClean = preg_replace('/[^0-9]/', '', $phone);
    $whatsapp = setting('contact_whatsapp_1', '905468101717');
    $workingHours = setting('contact_working_hours', '7/24 Hizmet');
@endphp

@section('content')
    <!-- HERO SECTION -->
    <section id="anasayfa" class="relative py-16 lg:py-24 bg-gradient-to-br from-sky-50 via-blue-50 to-white dark:from-slate-900 dark:via-slate-800 dark:to-slate-900 overflow-hidden overflow-x-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-30 dark:opacity-10">
            <div class="absolute top-20 left-10 w-72 h-72 bg-sky-300 rounded-full mix-blend-multiply filter blur-3xl"></div>
            <div class="absolute bottom-20 right-10 w-96 h-96 bg-blue-300 rounded-full mix-blend-multiply filter blur-3xl"></div>
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-cyan-200 rounded-full mix-blend-multiply filter blur-3xl"></div>
        </div>

        <div class="container mx-auto relative z-10">
            <div class="grid lg:grid-cols-2 gap-8 lg:gap-12 items-center">
                <!-- Left Content -->
                <div class="text-center lg:text-left" data-aos="fade-right" data-aos-duration="1000">
                    <!-- Badge -->
                    <div class="inline-flex items-center space-x-2 px-4 py-2 bg-sky-100 dark:bg-sky-900/30 text-sky-700 dark:text-sky-300 rounded-full text-sm font-medium mb-6">
                        <i class="fat fa-certificate"></i>
                        <span>A Grubu Seyahat Acentası</span>
                    </div>

                    <!-- Main Title -->
                    <h1 class="font-heading text-4xl sm:text-5xl lg:text-6xl xl:text-7xl font-bold text-slate-900 dark:text-white leading-tight mb-6">
                        Güvenle Yolculuk,
                        <span class="animated-gradient-text">Huzurla Varış</span>
                    </h1>

                    <!-- Subtitle -->
                    <p class="text-lg sm:text-xl text-slate-600 dark:text-slate-400 leading-relaxed mb-8 max-w-xl mx-auto lg:mx-0">
                        2008'den beri profesyonel taşımacılık hizmetleri. Turizm, personel ve öğrenci taşımacılığı, otel rezervasyonları.
                    </p>

                    <!-- CTA Buttons -->
                    <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4">
                        <a href="{{ module_locale_url('service', 'index') }}" class="w-full sm:w-auto inline-flex items-center justify-center space-x-2 px-8 py-4 bg-gradient-to-r from-sky-500 to-blue-600 text-white font-semibold rounded-lg hover:from-sky-600 hover:to-blue-700 transition-all shadow-lg shadow-sky-500/30 hover:shadow-xl hover:shadow-sky-500/40">
                            <span>Hizmetlerimiz</span>
                            <i class="fat fa-arrow-right"></i>
                        </a>
                        <a href="tel:+90{{ $phoneClean }}" class="w-full sm:w-auto inline-flex items-center justify-center space-x-2 px-8 py-4 border-2 border-sky-500 text-sky-600 dark:text-sky-400 font-semibold rounded-lg hover:bg-sky-50 dark:hover:bg-sky-900/20 transition-all">
                            <i class="fat fa-phone-volume"></i>
                            <span>{{ $phone }}</span>
                        </a>
                    </div>

                    <!-- Trust Badges -->
                    <div class="flex items-center justify-center lg:justify-start gap-6 mt-10 pt-10 border-t border-slate-200 dark:border-slate-700">
                        <div class="flex items-center space-x-2 text-slate-600 dark:text-slate-400">
                            <i class="fat fa-shield-check text-sky-500 text-xl"></i>
                            <span class="text-sm font-medium">Lisanslı</span>
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
                        <div class="gradient-border">
                            <div class="gradient-border-inner p-2">
                                <img src="{{ asset('storage/tenant5/6/ecrin-hero-banner.jpg') }}" alt="VIP Turizm Taşımacılığı" class="w-full h-auto rounded-xl object-cover">
                            </div>
                        </div>

                        <!-- Floating Card 1 -->
                        <div class="hidden sm:flex absolute -bottom-6 left-0 sm:-left-6 bg-white dark:bg-slate-800 rounded-xl shadow-2xl p-4 items-center space-x-3" data-aos="fade-up" data-aos-delay="400">
                            <div class="w-12 h-12 bg-gradient-to-br from-sky-500 to-blue-600 rounded-lg flex items-center justify-center">
                                <i class="fat fa-calendar-check text-white text-xl"></i>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-slate-900 dark:text-white">2008</p>
                                <p class="text-sm text-slate-500 dark:text-slate-400">Kuruluş</p>
                            </div>
                        </div>

                        <!-- Floating Card 2 -->
                        <div class="hidden sm:flex absolute -top-4 right-0 sm:-right-4 bg-white dark:bg-slate-800 rounded-xl shadow-2xl p-4 items-center space-x-3" data-aos="fade-down" data-aos-delay="500">
                            <div class="w-12 h-12 bg-gradient-to-br from-amber-400 to-orange-500 rounded-lg flex items-center justify-center">
                                <i class="fat fa-certificate text-white text-xl"></i>
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

    </section>

    <!-- SERVICES SECTION -->
    <section id="hizmetler" class="py-16 md:py-24 bg-white dark:bg-slate-900">
        <div class="container mx-auto">
            <!-- Section Header -->
            <div class="text-center max-w-3xl mx-auto mb-16" data-aos="fade-up">
                <span class="inline-block px-4 py-2 bg-sky-100 dark:bg-sky-900/30 text-sky-700 dark:text-sky-300 rounded-full text-sm font-medium mb-4">
                    Hizmetlerimiz
                </span>
                <h2 class="font-heading text-3xl sm:text-4xl lg:text-5xl font-bold text-slate-900 dark:text-white mb-6">
                    Profesyonel <span class="gradient-text">Çözümler</span>
                </h2>
                <p class="text-lg text-slate-600 dark:text-slate-400">
                    Güvenilir taşımacılık ve turizm hizmetleriyle yanınızdayız
                </p>
            </div>

            <!-- Services Grid -->
            @php
                $serviceData = [
                    [
                        'icon' => 'fa-bus',
                        'title' => 'Turizm Taşımacılığı',
                        'desc' => 'Yurt içi ve yurt dışı turlarda konforlu, güvenli ulaşım hizmeti.',
                        'slug' => 'turizm-tasimaciligi'
                    ],
                    [
                        'icon' => 'fa-people-group',
                        'title' => 'Personel Taşımacılığı',
                        'desc' => 'Şirketlere özel düzenli servis güzergahları ve araç tahsisi.',
                        'slug' => 'personel-tasimaciligi'
                    ],
                    [
                        'icon' => 'fa-school-flag',
                        'title' => 'Öğrenci Taşımacılığı',
                        'desc' => 'Okul servisleri için güvenlik öncelikli taşımacılık çözümleri.',
                        'slug' => 'ogrenci-tasimaciligi'
                    ],
                    [
                        'icon' => 'fa-hotel',
                        'title' => 'Otel Rezervasyonları',
                        'desc' => 'Anlaşmalı otellerle uygun fiyatlı konaklama seçenekleri.',
                        'slug' => 'otel-rezervasyonlari'
                    ],
                    [
                        'icon' => 'fa-sailboat',
                        'title' => 'Yat Kiralama',
                        'desc' => 'Özel günler ve tatiller için lüks yat kiralama hizmeti.',
                        'slug' => 'yat-kiralama'
                    ],
                    [
                        'icon' => 'fa-headset',
                        'title' => 'Özel Talepler',
                        'desc' => 'Farklı ihtiyaçlarınız için size özel çözümler üretiyoruz.',
                        'slug' => 'iletisim',
                        'isContact' => true
                    ],
                ];
            @endphp
            <div class="services-grid grid gap-4 md:gap-6 lg:gap-8">
                @foreach($serviceData as $index => $item)
                    <div class="card-hover bg-slate-50 dark:bg-slate-800 rounded-2xl p-6 lg:p-8" data-aos="fade-up" data-aos-delay="{{ ($index + 1) * 50 }}">
                        <div class="w-14 h-14 bg-gradient-to-br {{ isset($item['isContact']) ? 'from-amber-400 to-orange-500' : 'from-sky-500 to-blue-600' }} rounded-xl flex items-center justify-center mb-5">
                            <i class="fat {{ $item['icon'] }} text-white text-2xl"></i>
                        </div>
                        <h3 class="title-hover font-heading text-lg lg:text-xl font-bold text-slate-900 dark:text-white mb-3 transition-colors">
                            {{ $item['title'] }}
                        </h3>
                        <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed mb-4">
                            {{ $item['desc'] }}
                        </p>
                        <a href="{{ isset($item['isContact']) ? module_locale_url('page', 'show', ['iletisim']) : module_locale_url('service', 'show', [$item['slug']]) }}" class="inline-flex items-center text-sky-600 dark:text-sky-400 font-medium text-sm hover:text-sky-700 transition-colors">
                            <span>{{ isset($item['isContact']) ? 'İletişim' : 'Detay' }}</span>
                            <i class="fat fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- WHY US SECTION -->
    <section class="py-16 md:py-24 bg-slate-50 dark:bg-slate-800">
        <div class="container mx-auto">
            <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">
                <!-- Left Image -->
                <div class="relative" data-aos="fade-right">
                    <div class="gradient-border">
                        <div class="gradient-border-inner p-2">
                            <img src="https://images.unsplash.com/photo-1449965408869-eaa3f722e40d?w=700&h=500&fit=crop" alt="Neden {{ $siteName }}" class="w-full h-auto rounded-xl object-cover" loading="lazy">
                        </div>
                    </div>

                    <!-- Badge -->
                    <div class="hidden sm:block absolute -bottom-6 right-0 sm:-right-6 bg-white dark:bg-slate-900 rounded-2xl shadow-2xl p-5" data-aos="fade-up" data-aos-delay="200">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-amber-400 to-orange-500 rounded-xl flex items-center justify-center">
                                <i class="fat fa-award text-white text-xl"></i>
                            </div>
                            <div>
                                <p class="font-heading font-bold text-slate-900 dark:text-white">A Grubu Lisans</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400">T.C. Kültür ve Turizm Bakanlığı</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Content -->
                <div data-aos="fade-left">
                    <span class="inline-block px-4 py-2 bg-sky-100 dark:bg-sky-900/30 text-sky-700 dark:text-sky-300 rounded-full text-sm font-medium mb-4">
                        Neden Biz?
                    </span>
                    <h2 class="font-heading text-3xl sm:text-4xl font-bold text-slate-900 dark:text-white mb-6">
                        Neden <span class="gradient-text">{{ $siteName }}?</span>
                    </h2>
                    <p class="text-lg text-slate-600 dark:text-slate-400 mb-8">
                        Yılların deneyimi ve profesyonel kadromuzla güvenli, konforlu hizmet.
                    </p>

                    <!-- Features Grid -->
                    <div class="grid sm:grid-cols-2 gap-5">
                        <div class="flex items-start space-x-3">
                            <div class="w-10 h-10 bg-sky-100 dark:bg-sky-900/50 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fat fa-shield-check text-sky-600 dark:text-sky-400"></i>
                            </div>
                            <div>
                                <h4 class="font-heading font-semibold text-slate-900 dark:text-white text-sm">A Grubu Lisans</h4>
                                <p class="text-xs text-slate-600 dark:text-slate-400">Resmi güvence</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-3">
                            <div class="w-10 h-10 bg-sky-100 dark:bg-sky-900/50 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fat fa-bus text-sky-600 dark:text-sky-400"></i>
                            </div>
                            <div>
                                <h4 class="font-heading font-semibold text-slate-900 dark:text-white text-sm">Modern Filo</h4>
                                <p class="text-xs text-slate-600 dark:text-slate-400">Geniş araç seçenekleri</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-3">
                            <div class="w-10 h-10 bg-sky-100 dark:bg-sky-900/50 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fat fa-headset text-sky-600 dark:text-sky-400"></i>
                            </div>
                            <div>
                                <h4 class="font-heading font-semibold text-slate-900 dark:text-white text-sm">{{ $workingHours }}</h4>
                                <p class="text-xs text-slate-600 dark:text-slate-400">Her an yanınızdayız</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-3">
                            <div class="w-10 h-10 bg-sky-100 dark:bg-sky-900/50 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fat fa-location-crosshairs text-sky-600 dark:text-sky-400"></i>
                            </div>
                            <div>
                                <h4 class="font-heading font-semibold text-slate-900 dark:text-white text-sm">GPS Takip</h4>
                                <p class="text-xs text-slate-600 dark:text-slate-400">Canlı konum izleme</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8">
                        <a href="{{ module_locale_url('page', 'show', ['hakkimizda']) }}" class="inline-flex items-center text-sky-600 dark:text-sky-400 font-medium hover:text-sky-700 transition-colors">
                            <span>Hakkımızda</span>
                            <i class="fat fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA SECTION -->
    <section class="py-16 md:py-20 bg-gradient-to-r from-sky-500 to-blue-600">
        <div class="container mx-auto">
            <div class="text-center text-white" data-aos="fade-up">
                <h2 class="font-heading text-3xl sm:text-4xl font-bold mb-4">
                    Hemen İletişime Geçin
                </h2>
                <p class="text-lg text-sky-100 mb-8 max-w-xl mx-auto">
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

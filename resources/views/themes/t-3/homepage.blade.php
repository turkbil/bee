{{-- t-3 Panjur Theme - Homepage --}}
@extends('themes.t-3.layouts.app')

@push('styles')
<style>
    .animated-gradient-bg {
        background: linear-gradient(135deg, #1f2937 0%, #111827 25%, #0f172a 50%, #111827 75%, #1f2937 100%);
        background-size: 400% 400%;
        animation: gradientBg 15s ease infinite;
    }
    @keyframes gradientBg {
        0%, 100% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
    }

    .gradient-text-animated {
        background: linear-gradient(
            90deg,
            #f97316 0%,
            #ef4444 20%,
            #ec4899 40%,
            #8b5cf6 60%,
            #3b82f6 80%,
            #f97316 100%
        );
        background-size: 200% auto;
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
        animation: shimmer 3s linear infinite;
    }
    @keyframes shimmer {
        0% { background-position: 0% center; }
        100% { background-position: 200% center; }
    }

    .glass {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .gradient-border {
        position: relative;
        background: linear-gradient(135deg, #f97316, #3b82f6);
        padding: 2px;
        border-radius: 1rem;
    }

    .text-slide { transition: transform 0.3s ease; }
    .card-hover:hover .text-slide { transform: translateX(8px); }

    .pulse-ring { position: relative; }
    .pulse-ring::after {
        content: '';
        position: absolute;
        inset: -4px;
        border-radius: 50%;
        border: 2px solid #f97316;
        animation: pulseRing 2s ease-out infinite;
    }
    @keyframes pulseRing {
        0% { transform: scale(1); opacity: 1; }
        100% { transform: scale(1.3); opacity: 0; }
    }

    .pattern-overlay {
        background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    }

    .step-card {
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .step-card:hover {
        transform: translateY(-8px);
    }
    .step-card:hover .step-number {
        transform: rotate(0deg) scale(1.1);
    }
    .step-card:hover .step-icon {
        transform: scale(1.2);
    }
    .step-number {
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .step-icon {
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }
</style>
@endpush

@section('content')
@php
    $siteName = setting('site_name') ?? 'Yildirim Panjur';
    $siteSlogan = setting('site_slogan') ?? '40 Yıllık Tecrübe';
    $siteDescription = setting('site_description') ?? 'Profesyonel panjur tamiri ve montaj hizmetleri.';
    $sitePhone = setting('contact_phone_1') ?? '0212 596 72 30';
    $siteMobile = setting('contact_phone_2') ?? '0533 687 73 11';
    $siteEmail = setting('contact_email_1') ?? 'info@example.com';
    $whatsappUrl = whatsapp_link();
@endphp

    {{-- Hero Section with Slider --}}
    <section class="relative py-12 md:py-16 lg:py-20 overflow-hidden bg-gradient-to-br from-gray-50 via-white to-gray-100 dark:from-gray-900 dark:via-slate-900 dark:to-gray-900" x-data="{
            currentSlide: 0,
            totalSlides: 4,
            autoplay: null,
            init() {
                if (this.totalSlides > 1) {
                    this.autoplay = setInterval(() => { this.nextSlide() }, 4000);
                }
            },
            nextSlide() {
                this.currentSlide = (this.currentSlide + 1) % this.totalSlides;
            },
            prevSlide() {
                this.currentSlide = (this.currentSlide - 1 + this.totalSlides) % this.totalSlides;
            },
            goToSlide(index) {
                this.currentSlide = index;
            }
        }">

        {{-- Floating Elements --}}
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute top-20 left-10 w-64 h-64 bg-primary-500/10 dark:bg-primary-500/5 rounded-full blur-3xl"></div>
            <div class="absolute bottom-20 right-1/3 w-96 h-96 bg-blue-500/10 dark:bg-blue-500/5 rounded-full blur-3xl"></div>
        </div>

        <div class="container mx-auto px-4 sm:px-4 md:px-2 relative z-10">
            <div class="grid lg:grid-cols-2 gap-8 lg:gap-12 items-center">
                {{-- Left: Text Content --}}
                <div class="order-2 lg:order-1">
                    {{-- Badge --}}
                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-primary-100 dark:bg-primary-900/30 rounded-full mb-6" data-aos="fade-right">
                        <span class="w-2 h-2 bg-primary-500 rounded-full animate-pulse"></span>
                        <span class="text-primary-600 dark:text-primary-400 font-medium text-sm">İstanbul Panjur Tamiri | {{ $siteName }}</span>
                    </div>

                    {{-- Main Heading --}}
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold font-heading mb-6 leading-tight" data-aos="fade-right" data-aos-delay="100">
                        <span class="gradient-text-animated">Panjur Tamiri</span><br>
                        <span class="text-gray-900 dark:text-white">ve Montaj Hizmetleri</span>
                    </h1>

                    {{-- Subtitle --}}
                    <p class="text-lg text-gray-600 dark:text-gray-300 mb-8 leading-relaxed max-w-xl" data-aos="fade-right" data-aos-delay="200">
                        İstanbul'un tüm bölgelerinde profesyonel panjur tamiri, motorlu panjur sistemleri, sineklik ve garaj kapısı hizmetleri.
                    </p>

                    {{-- CTA Buttons --}}
                    <div class="flex flex-col sm:flex-row gap-4" data-aos="fade-right" data-aos-delay="300">
                        <a href="tel:{{ preg_replace('/[^0-9+]/', '', $sitePhone) }}" class="group flex items-center justify-center gap-3 px-8 py-4 bg-gradient-to-r from-primary-600 to-primary-500 text-white font-semibold text-lg rounded-xl hover:from-primary-700 hover:to-primary-600 transition-all duration-300 shadow-xl shadow-primary-500/30 hover:shadow-primary-500/50 hover:scale-105">
                                <i class="fat fa-phone text-xl group-hover:rotate-12 transition-transform"></i>
                                <span>Acil Servis</span>
                            </a>
                        <a href="{{ url('/service') }}" class="flex items-center justify-center gap-3 px-8 py-4 bg-white dark:bg-slate-800 text-gray-900 dark:text-white font-semibold text-lg rounded-xl hover:bg-gray-50 dark:hover:bg-slate-700 transition-all duration-300 border border-gray-200 dark:border-slate-700 shadow-lg">
                            <i class="fat fa-grid-2 text-xl"></i>
                            <span>Hizmetlerimiz</span>
                        </a>
                    </div>
                </div>

                {{-- Right: Image Slider --}}
                <div class="order-1 lg:order-2 lg:col-span-1" data-aos="fade-left" data-aos-delay="200">
                    <div class="relative">
                        {{-- Glow Effect --}}
                        <div class="absolute -inset-4 bg-gradient-to-r from-primary-500 to-blue-500 rounded-3xl opacity-20 blur-2xl"></div>

                        {{-- Slider Container --}}
                        <div class="relative bg-gradient-to-br from-primary-500 to-primary-600 p-1 rounded-2xl shadow-2xl">
                            <div class="relative h-[280px] sm:h-[320px] lg:h-[360px] xl:h-[400px] rounded-xl overflow-hidden bg-gray-900">
                                {{-- Slides --}}
                                <div x-show="currentSlide === 0" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 scale-105" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="absolute inset-0">
                                    <img src="{{ asset('storage/tenant3/19/motorlu-panjur.jpg') }}" alt="Motorlu Panjur" class="w-full h-full object-cover">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/30 via-transparent to-transparent"></div>
                                </div>
                                <div x-show="currentSlide === 1" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 scale-105" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="absolute inset-0">
                                    <img src="{{ asset('storage/tenant3/20/garaj-kapisi.jpg') }}" alt="Garaj Kapısı" class="w-full h-full object-cover">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/30 via-transparent to-transparent"></div>
                                </div>
                                <div x-show="currentSlide === 2" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 scale-105" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="absolute inset-0">
                                    <img src="{{ asset('storage/tenant3/21/is-yeri-panjur.jpg') }}" alt="İş Yeri Panjur" class="w-full h-full object-cover">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/30 via-transparent to-transparent"></div>
                                </div>
                                <div x-show="currentSlide === 3" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 scale-105" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="absolute inset-0">
                                    <img src="{{ asset('storage/tenant3/22/sineklik-sistemi.jpg') }}" alt="Sineklik Sistemi" class="w-full h-full object-cover">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/30 via-transparent to-transparent"></div>
                                </div>

                                {{-- Navigation Arrows --}}
                                <button @click="prevSlide()" class="absolute left-4 top-1/2 -translate-y-1/2 w-10 h-10 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center text-white hover:bg-white/30 transition-all">
                                    <i class="fat fa-chevron-left"></i>
                                </button>
                                <button @click="nextSlide()" class="absolute right-4 top-1/2 -translate-y-1/2 w-10 h-10 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center text-white hover:bg-white/30 transition-all">
                                    <i class="fat fa-chevron-right"></i>
                                </button>
                            </div>

                            {{-- Dot Indicators --}}
                            <div class="absolute -bottom-6 left-1/2 -translate-x-1/2 flex gap-2">
                                <button @click="goToSlide(0)" :class="currentSlide === 0 ? 'bg-primary-500 w-8' : 'bg-gray-300 dark:bg-gray-600 w-2'" class="h-2 rounded-full transition-all duration-300 hover:bg-primary-400"></button>
                                <button @click="goToSlide(1)" :class="currentSlide === 1 ? 'bg-primary-500 w-8' : 'bg-gray-300 dark:bg-gray-600 w-2'" class="h-2 rounded-full transition-all duration-300 hover:bg-primary-400"></button>
                                <button @click="goToSlide(2)" :class="currentSlide === 2 ? 'bg-primary-500 w-8' : 'bg-gray-300 dark:bg-gray-600 w-2'" class="h-2 rounded-full transition-all duration-300 hover:bg-primary-400"></button>
                                <button @click="goToSlide(3)" :class="currentSlide === 3 ? 'bg-primary-500 w-8' : 'bg-gray-300 dark:bg-gray-600 w-2'" class="h-2 rounded-full transition-all duration-300 hover:bg-primary-400"></button>
                            </div>
                        </div>

                        {{-- Floating Badge --}}
                        <div class="absolute -bottom-4 -left-4 bg-white dark:bg-slate-800 rounded-xl shadow-xl p-4 border border-gray-100 dark:border-slate-700" data-aos="fade-up" data-aos-delay="500">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 bg-gradient-to-br from-primary-500 to-primary-600 rounded-lg flex items-center justify-center">
                                    <i class="fat fa-medal text-white text-xl"></i>
                                </div>
                                <div>
                                    <div class="text-2xl font-bold text-gray-900 dark:text-white">40+</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">Yıl Tecrübe</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>

    {{-- Stats Section --}}
    <section class="py-8 bg-gradient-to-r from-primary-600 via-primary-500 to-primary-600 relative overflow-hidden">
        <div class="absolute inset-0 bg-black/10"></div>
        <div class="container mx-auto px-4 sm:px-4 md:px-2 relative z-10">
            <div class="grid grid-cols-3 gap-6 md:gap-8">
                <div class="text-center" data-aos="zoom-in" data-aos-delay="0">
                    <div class="text-3xl md:text-4xl font-bold text-white mb-1">40</div>
                    <div class="text-white/80 text-sm md:text-base">Yıl Tecrübe</div>
                </div>
                <div class="text-center" data-aos="zoom-in" data-aos-delay="100">
                    <div class="text-3xl md:text-4xl font-bold text-white mb-1">5</div>
                    <div class="text-white/80 text-sm md:text-base">Yıl Garanti</div>
                </div>
                <div class="text-center" data-aos="zoom-in" data-aos-delay="200">
                    <div class="text-3xl md:text-4xl font-bold text-white mb-1">7/24</div>
                    <div class="text-white/80 text-sm md:text-base">Kesintisiz Destek</div>
                </div>
            </div>
        </div>
    </section>

    {{-- Services Section --}}
    @php
        $services = \Modules\Service\App\Models\Service::where('is_active', true)->orderBy('service_id')->take(6)->get();
        $gradients = [
            'from-primary-500 to-primary-600',
            'from-blue-500 to-blue-600',
            'from-green-500 to-green-600',
            'from-purple-500 to-purple-600',
            'from-red-500 to-red-600',
            'from-amber-500 to-amber-600',
        ];
        $colors = ['primary', 'blue', 'green', 'purple', 'red', 'amber'];
    @endphp
    <section id="hizmetler" class="py-16 md:py-24 bg-gray-50 dark:bg-slate-900">
        <div class="container mx-auto px-4 sm:px-4 md:px-2">
            {{-- Section Header --}}
            <div class="text-center max-w-3xl mx-auto mb-12 md:mb-16">
                <span class="inline-block px-4 py-1.5 bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 text-sm font-medium rounded-full mb-4" data-aos="fade-up">Hizmetlerimiz</span>
                <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold font-heading text-gray-900 dark:text-white mb-4" data-aos="fade-up" data-aos-delay="100">
                    Profesyonel <span class="gradient-text-animated">Panjur Çözümleri</span>
                </h2>
                <p class="text-lg text-gray-600 dark:text-gray-400 leading-relaxed" data-aos="fade-up" data-aos-delay="200">
                    İstanbul genelinde panjur tamiri, motorlu sistemler ve daha fazlası
                </p>
            </div>

            {{-- Services Grid - Dynamic --}}
            <div class="grid grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-8">
                @foreach($services as $index => $service)
                @php
                    $gradient = $gradients[$index % count($gradients)];
                    $color = $colors[$index % count($colors)];
                    $heroImage = $service->getFirstMediaUrl('hero');
                    $delay = ($index % 3) * 100;
                @endphp
                <a href="{{ url('/service/' . $service->slug) }}" class="card-hover group block" data-aos="fade-up" data-aos-delay="{{ $delay }}">
                    <div class="p-[2px] bg-gradient-to-br {{ $gradient }} rounded-2xl h-full">
                        <div class="bg-white dark:bg-slate-800 rounded-[14px] overflow-hidden h-full flex flex-col">
                            <div class="relative aspect-video overflow-hidden">
                                @if($heroImage)
                                <img src="{{ $heroImage }}" alt="{{ $service->title }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                @else
                                <div class="w-full h-full bg-gradient-to-br {{ $gradient }} flex items-center justify-center">
                                    <i class="fat fa-image text-white/50 text-4xl"></i>
                                </div>
                                @endif
                                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"></div>
                                <div class="absolute bottom-0 left-0 right-0 p-4">
                                    <h3 class="text-lg font-bold font-heading text-white leading-tight">{{ $service->title }}</h3>
                                </div>
                            </div>
                            <div class="p-6 pt-4 flex-grow flex flex-col">
                                <ul class="space-y-2 mb-6">
                                    <li class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                                        <i class="fat fa-check text-{{ $color }}-500"></i>
                                        <span>Ücretsiz keşif</span>
                                    </li>
                                    <li class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                                        <i class="fat fa-check text-{{ $color }}-500"></i>
                                        <span>5 yıl garanti</span>
                                    </li>
                                    <li class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                                        <i class="fat fa-check text-{{ $color }}-500"></i>
                                        <span>Profesyonel ekip</span>
                                    </li>
                                </ul>
                                <div class="mt-auto">
                                    <span class="group/link inline-flex items-center gap-2 px-4 py-2 bg-{{ $color }}-50 dark:bg-{{ $color }}-900/20 text-{{ $color }}-600 dark:text-{{ $color }}-400 font-medium rounded-lg group-hover:bg-{{ $color }}-100 dark:group-hover:bg-{{ $color }}-900/40 group-hover:gap-3 transition-all duration-300">
                                        <span>Detaylı Bilgi</span>
                                        <i class="fat fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>

            {{-- View All Button --}}
            <div class="text-center mt-12" data-aos="fade-up">
                <a href="{{ url('/service') }}" class="inline-flex items-center gap-3 px-8 py-4 bg-gradient-to-r from-primary-600 to-primary-500 text-white font-semibold rounded-xl hover:from-primary-700 hover:to-primary-600 transition-all duration-300 shadow-lg shadow-primary-500/25 hover:shadow-primary-500/40 hover:scale-105">
                    <span>Tüm Hizmetlerimiz</span>
                    <i class="fat fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </section>

    {{-- Why Us Section --}}
    <section id="hakkimizda" class="py-16 md:py-24 bg-white dark:bg-slate-800">
        <div class="container mx-auto px-4 sm:px-4 md:px-2">
            {{-- Section Header --}}
            <div class="text-center max-w-3xl mx-auto mb-12 md:mb-16">
                <span class="inline-block px-4 py-1.5 bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 text-sm font-medium rounded-full mb-4" data-aos="fade-up">Neden Biz?</span>
                <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold font-heading text-gray-900 dark:text-white mb-4" data-aos="fade-up" data-aos-delay="100">
                    Neden <span class="gradient-text-animated">{{ $siteName }}?</span>
                </h2>
                <p class="text-lg text-gray-600 dark:text-gray-400 leading-relaxed" data-aos="fade-up" data-aos-delay="200">
                    40 yıllık tecrübemizle İstanbul'un güvenilir panjur ustası
                </p>
            </div>

            {{-- Features Grid --}}
            <div class="grid grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-8">
                <div class="card-hover group text-center p-6 md:p-8 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-slate-700 dark:to-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 hover:border-primary-300 dark:hover:border-primary-700 transition-all duration-300" data-aos="fade-up" data-aos-delay="0">
                    <div class="w-16 h-16 mx-auto bg-gradient-to-br from-primary-500 to-primary-600 rounded-2xl flex items-center justify-center mb-5 shadow-lg shadow-primary-500/30 icon-hover">
                        <i class="fat fa-medal text-white text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-bold font-heading text-gray-900 dark:text-white mb-2 text-slide">40 Yıl Tecrübe</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">Sektörde uzun süreli deneyim ve güvenilirlik</p>
                </div>

                <div class="card-hover group text-center p-6 md:p-8 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-slate-700 dark:to-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 hover:border-blue-300 dark:hover:border-blue-700 transition-all duration-300" data-aos="fade-up" data-aos-delay="100">
                    <div class="w-16 h-16 mx-auto bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center mb-5 shadow-lg shadow-blue-500/30 icon-hover">
                        <i class="fat fa-shield-halved text-white text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-bold font-heading text-gray-900 dark:text-white mb-2 text-slide">5 Yıl Garanti</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">Tüm ürün ve hizmetlerde güvence</p>
                </div>

                <div class="card-hover group text-center p-6 md:p-8 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-slate-700 dark:to-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 hover:border-green-300 dark:hover:border-green-700 transition-all duration-300" data-aos="fade-up" data-aos-delay="200">
                    <div class="w-16 h-16 mx-auto bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center mb-5 shadow-lg shadow-green-500/30 icon-hover">
                        <i class="fat fa-hand-holding-dollar text-white text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-bold font-heading text-gray-900 dark:text-white mb-2 text-slide">Ücretsiz Keşif</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">Ölçüm ve fiyat teklifi tamamen bedava</p>
                </div>

                <div class="card-hover group text-center p-6 md:p-8 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-slate-700 dark:to-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 hover:border-purple-300 dark:hover:border-purple-700 transition-all duration-300" data-aos="fade-up" data-aos-delay="0">
                    <div class="w-16 h-16 mx-auto bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center mb-5 shadow-lg shadow-purple-500/30 icon-hover">
                        <i class="fat fa-bolt text-white text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-bold font-heading text-gray-900 dark:text-white mb-2 text-slide">Hızlı Servis</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">Aynı gün tamir garantisi</p>
                </div>

                <div class="card-hover group text-center p-6 md:p-8 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-slate-700 dark:to-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 hover:border-red-300 dark:hover:border-red-700 transition-all duration-300" data-aos="fade-up" data-aos-delay="100">
                    <div class="w-16 h-16 mx-auto bg-gradient-to-br from-red-500 to-red-600 rounded-2xl flex items-center justify-center mb-5 shadow-lg shadow-red-500/30 icon-hover">
                        <i class="fat fa-users text-white text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-bold font-heading text-gray-900 dark:text-white mb-2 text-slide">Uzman Ekip</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">Deneyimli ve profesyonel teknisyenler</p>
                </div>

                <div class="card-hover group text-center p-6 md:p-8 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-slate-700 dark:to-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 hover:border-amber-300 dark:hover:border-amber-700 transition-all duration-300" data-aos="fade-up" data-aos-delay="200">
                    <div class="w-16 h-16 mx-auto bg-gradient-to-br from-amber-500 to-amber-600 rounded-2xl flex items-center justify-center mb-5 shadow-lg shadow-amber-500/30 icon-hover">
                        <i class="fat fa-map-location-dot text-white text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-bold font-heading text-gray-900 dark:text-white mb-2 text-slide">İstanbul Geneli</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">Tüm ilçelere hızlı ulaşım</p>
                </div>
            </div>
        </div>
    </section>

    {{-- How It Works Section --}}
    <section class="py-16 md:py-24 bg-gray-50 dark:bg-slate-900">
        <div class="container mx-auto px-4 sm:px-4 md:px-2">
            {{-- Section Header --}}
            <div class="text-center max-w-3xl mx-auto mb-12 md:mb-16">
                <span class="inline-block px-4 py-1.5 bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 text-sm font-medium rounded-full mb-4" data-aos="fade-up">Süreç</span>
                <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold font-heading text-gray-900 dark:text-white mb-4" data-aos="fade-up" data-aos-delay="100">
                    <span class="gradient-text-animated">4 Kolay Adımda</span> Çözüm
                </h2>
                <p class="text-lg text-gray-600 dark:text-gray-400 leading-relaxed" data-aos="fade-up" data-aos-delay="200">
                    Panjur sorununuzu hızlı ve güvenilir şekilde çözüyoruz
                </p>
            </div>

            {{-- Steps --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-8">
                <div class="step-card text-center p-6 bg-white dark:bg-slate-800 rounded-2xl shadow-lg hover:shadow-2xl border border-gray-100 dark:border-slate-700" data-aos="fade-up" data-aos-delay="0">
                    <div class="relative inline-block mb-6">
                        <div class="step-number w-20 h-20 bg-gradient-to-br from-primary-500 to-primary-600 rounded-2xl flex items-center justify-center text-white text-2xl font-bold shadow-xl shadow-primary-500/30 rotate-3">
                            1
                        </div>
                        <div class="step-icon absolute -bottom-2 -right-2 w-10 h-10 bg-white dark:bg-slate-800 rounded-full flex items-center justify-center shadow-lg border-2 border-primary-500">
                            <i class="fat fa-phone text-primary-500"></i>
                        </div>
                        <div class="hidden lg:flex absolute -right-12 top-1/2 -translate-y-1/2 text-primary-300 dark:text-primary-600">
                            <i class="fat fa-arrow-right text-2xl"></i>
                        </div>
                    </div>
                    <h3 class="text-lg font-bold font-heading text-gray-900 dark:text-white mb-2">Bizi Arayın</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">7/24 iletişim hattımızdan ulaşın</p>
                </div>

                <div class="step-card text-center p-6 bg-white dark:bg-slate-800 rounded-2xl shadow-lg hover:shadow-2xl border border-gray-100 dark:border-slate-700" data-aos="fade-up" data-aos-delay="100">
                    <div class="relative inline-block mb-6">
                        <div class="step-number w-20 h-20 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center text-white text-2xl font-bold shadow-xl shadow-blue-500/30 -rotate-3">
                            2
                        </div>
                        <div class="step-icon absolute -bottom-2 -right-2 w-10 h-10 bg-white dark:bg-slate-800 rounded-full flex items-center justify-center shadow-lg border-2 border-blue-500">
                            <i class="fat fa-magnifying-glass text-blue-500"></i>
                        </div>
                        <div class="hidden lg:flex absolute -right-12 top-1/2 -translate-y-1/2 text-blue-300 dark:text-blue-600">
                            <i class="fat fa-arrow-right text-2xl"></i>
                        </div>
                    </div>
                    <h3 class="text-lg font-bold font-heading text-gray-900 dark:text-white mb-2">Ücretsiz Keşif</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">Ekibimiz adresinize gelir</p>
                </div>

                <div class="step-card text-center p-6 bg-white dark:bg-slate-800 rounded-2xl shadow-lg hover:shadow-2xl border border-gray-100 dark:border-slate-700" data-aos="fade-up" data-aos-delay="200">
                    <div class="relative inline-block mb-6">
                        <div class="step-number w-20 h-20 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center text-white text-2xl font-bold shadow-xl shadow-green-500/30 rotate-3">
                            3
                        </div>
                        <div class="step-icon absolute -bottom-2 -right-2 w-10 h-10 bg-white dark:bg-slate-800 rounded-full flex items-center justify-center shadow-lg border-2 border-green-500">
                            <i class="fat fa-file-invoice-dollar text-green-500"></i>
                        </div>
                        <div class="hidden lg:flex absolute -right-12 top-1/2 -translate-y-1/2 text-green-300 dark:text-green-600">
                            <i class="fat fa-arrow-right text-2xl"></i>
                        </div>
                    </div>
                    <h3 class="text-lg font-bold font-heading text-gray-900 dark:text-white mb-2">Fiyat Teklifi</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">Net ve uygun fiyat alın</p>
                </div>

                <div class="step-card text-center p-6 bg-white dark:bg-slate-800 rounded-2xl shadow-lg hover:shadow-2xl border border-gray-100 dark:border-slate-700" data-aos="fade-up" data-aos-delay="300">
                    <div class="relative inline-block mb-6">
                        <div class="step-number w-20 h-20 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center text-white text-2xl font-bold shadow-xl shadow-purple-500/30 -rotate-3">
                            4
                        </div>
                        <div class="step-icon absolute -bottom-2 -right-2 w-10 h-10 bg-white dark:bg-slate-800 rounded-full flex items-center justify-center shadow-lg border-2 border-purple-500">
                            <i class="fat fa-check-circle text-purple-500"></i>
                        </div>
                    </div>
                    <h3 class="text-lg font-bold font-heading text-gray-900 dark:text-white mb-2">Hızlı Çözüm</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">Aynı gün tamir garantisi</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Contact CTA Section --}}
    <section id="iletisim" class="py-16 md:py-24 bg-gradient-to-r from-primary-600 via-primary-500 to-primary-600 relative overflow-hidden">
        <div class="absolute inset-0 pattern-overlay opacity-30"></div>
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute top-10 left-10 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
            <div class="absolute bottom-10 right-10 w-48 h-48 bg-white/10 rounded-full blur-2xl"></div>
        </div>

        <div class="container mx-auto px-4 sm:px-4 md:px-2 relative z-10">
            <div class="max-w-4xl mx-auto text-center">
                <div class="w-20 h-20 mx-auto bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center mb-8" data-aos="zoom-in">
                    <i class="fat fa-phone-volume text-white text-4xl animate-pulse"></i>
                </div>

                <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold font-heading text-white mb-4" data-aos="fade-up" data-aos-delay="100">
                    Hemen Arayın, Hızlı Çözüm Alın!
                </h2>
                <p class="text-lg md:text-xl text-white/90 mb-10 max-w-2xl mx-auto" data-aos="fade-up" data-aos-delay="200">
                    7/24 acil servis hattımızdan bize ulaşın. Ücretsiz keşif ve uygun fiyat garantisi.
                </p>

                <div class="flex flex-col sm:flex-row items-center justify-center gap-4" data-aos="fade-up" data-aos-delay="300">
                    <a href="tel:{{ preg_replace('/[^0-9+]/', '', $sitePhone) }}" class="group flex items-center gap-4 px-8 py-5 bg-white text-primary-600 font-bold text-lg rounded-xl hover:bg-gray-100 transition-all duration-300 shadow-xl hover:shadow-2xl hover:scale-105">
                        <i class="fat fa-phone-rotary text-2xl group-hover:animate-pulse"></i>
                        <div class="text-left">
                            <span class="text-xs text-gray-500 block">Sabit Hat</span>
                            <span>{{ $sitePhone }}</span>
                        </div>
                    </a>

                    <a href="tel:{{ preg_replace('/[^0-9+]/', '', $siteMobile) }}" class="group flex items-center gap-4 px-8 py-5 bg-white text-primary-600 font-bold text-lg rounded-xl hover:bg-gray-100 transition-all duration-300 shadow-xl hover:shadow-2xl hover:scale-105">
                        <i class="fat fa-mobile text-2xl group-hover:animate-pulse"></i>
                        <div class="text-left">
                            <span class="text-xs text-gray-500 block">Mobil Hat</span>
                            <span>{{ $siteMobile }}</span>
                        </div>
                    </a>

                    @if($whatsappUrl)
                    <a href="{{ $whatsappUrl }}" target="_blank" class="group flex items-center gap-4 px-8 py-5 bg-green-600 text-white font-bold text-lg rounded-xl hover:bg-green-700 transition-all duration-300 shadow-xl hover:shadow-2xl hover:scale-105">
                        <i class="fab fa-whatsapp text-2xl group-hover:animate-pulse"></i>
                        <div class="text-left">
                            <span class="text-xs text-white/80 block">WhatsApp</span>
                            <span>Mesaj Gönderin</span>
                        </div>
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- SEO Content Section --}}
    <section class="py-16 md:py-20 bg-white dark:bg-slate-800">
        <div class="container mx-auto px-4 sm:px-4 md:px-2">
            <div class="max-w-4xl mx-auto">
                <h2 class="text-2xl md:text-3xl font-bold font-heading text-gray-900 dark:text-white mb-6 text-center" data-aos="fade-up">
                    İstanbul <span class="text-primary-500">Panjur Tamiri</span> Hizmeti
                </h2>

                <div class="prose prose-lg dark:prose-invert max-w-none text-gray-600 dark:text-gray-300" data-aos="fade-up" data-aos-delay="100">
                    <p class="leading-relaxed mb-6">
                        <strong>Yıldırım Panjur</strong>, 40 yılı aşkın tecrübesiyle İstanbul'un tüm ilçelerinde <strong>panjur tamiri</strong>, <strong>panjur montajı</strong> ve <strong>panjur bakım</strong> hizmetleri sunmaktadır. Kadıköy, Beşiktaş, Şişli, Bakırköy, Ataşehir, Üsküdar, Fatih, Beylikdüzü, Pendik, Maltepe ve İstanbul'un diğer tüm semtlerinde uzman ekibimizle yanınızdayız.
                    </p>

                    <p class="leading-relaxed mb-6">
                        Arızalı panjurunuz mu var? <strong>Panjur motoru bozuldu</strong>, <strong>panjur kayışı koptu</strong>, <strong>panjur açılmıyor</strong> veya <strong>panjur kapanmıyor</strong> mu? Endişelenmeyin! Deneyimli ustalarımız aynı gün içinde adresinize gelerek sorununuzu çözer. <strong>Motorlu panjur tamiri</strong>, <strong>manuel panjur tamiri</strong>, <strong>alüminyum panjur tamiri</strong> ve <strong>plastik panjur tamiri</strong> dahil her türlü panjur arızasına müdahale ediyoruz.
                    </p>

                    <p class="leading-relaxed mb-6">
                        Hizmetlerimiz arasında <strong>panjur motor değişimi</strong>, <strong>panjur kumandalı sistem</strong>, <strong>akıllı panjur sistemleri</strong>, <strong>garaj kapısı tamiri</strong>, <strong>otomatik kepenk tamiri</strong> ve <strong>sineklik montajı</strong> da bulunmaktadır. Ev, işyeri, mağaza, ofis ve apartman girişleri için profesyonel çözümler üretiyoruz.
                    </p>

                    <p class="leading-relaxed">
                        <strong>Ücretsiz keşif</strong>, <strong>5 yıl garanti</strong> ve <strong>uygun fiyat</strong> avantajlarıyla İstanbul'un en güvenilir panjur firmasıyız. 7/24 acil servis hattımızdan bize ulaşabilir, aynı gün randevu alabilirsiniz. <strong>Panjur fiyatları</strong> ve detaylı bilgi için hemen arayın!
                    </p>
                </div>

                {{-- Service Areas --}}
                <div class="mt-10 pt-8 border-t border-gray-200 dark:border-slate-700" data-aos="fade-up" data-aos-delay="200">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 text-center">Hizmet Verdiğimiz Bölgeler</h3>
                    <div class="flex flex-wrap justify-center gap-2 text-sm">
                        @php
                            $districts = ['Kadıköy', 'Beşiktaş', 'Şişli', 'Bakırköy', 'Ataşehir', 'Üsküdar', 'Fatih', 'Beylikdüzü', 'Pendik', 'Maltepe', 'Kartal', 'Sarıyer', 'Beyoğlu', 'Zeytinburnu', 'Bahçelievler', 'Bağcılar', 'Küçükçekmece', 'Esenyurt', 'Avcılar', 'Başakşehir'];
                        @endphp
                        @foreach($districts as $district)
                            <a href="{{ url('/blog') }}" class="px-3 py-1.5 bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-gray-300 rounded-full hover:bg-primary-100 dark:hover:bg-primary-900/30 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">
                                {{ $district }} Panjur Tamiri
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@extends('themes.t-6.layouts.app')

@php
    $siteName = setting('site_title');

    // Service icons mapping
    $serviceIcons = [
        'ticaret-hukuku' => 'fa-handshake',
        'sirketler-hukuku' => 'fa-building',
        'ceza-hukuku' => 'fa-gavel',
        'saglik-hukuku' => 'fa-stethoscope',
        'sigorta-hukuku' => 'fa-shield-check',
        'idare-ve-imar-hukuku' => 'fa-landmark',
        'borclar-hukuku' => 'fa-file-contract',
        'is-hukuku' => 'fa-briefcase',
    ];

    // Service images mapping - Leonardo AI generated
    $serviceImages = [
        'ticaret-hukuku' => '/storage/themes/t-6/service-ticaret-hukuku.jpg',
        'sirketler-hukuku' => '/storage/themes/t-6/service-sirketler-hukuku.jpg',
        'ceza-hukuku' => '/storage/themes/t-6/service-ceza-hukuku.jpg',
        'saglik-hukuku' => '/storage/themes/t-6/service-saglik-hukuku.jpg',
        'sigorta-hukuku' => '/storage/themes/t-6/service-sigorta-hukuku.jpg',
        'idare-ve-imar-hukuku' => '/storage/themes/t-6/service-idare-imar-hukuku.jpg',
        'borclar-hukuku' => '/storage/themes/t-6/service-borclar-hukuku.jpg',
        'is-hukuku' => '/storage/themes/t-6/service-is-hukuku.jpg',
    ];
@endphp

@section('content')

{{-- Page Header with Background Image --}}
<section class="relative pt-24 pb-10 overflow-hidden">
    {{-- Background Image --}}
    <div class="absolute inset-0">
        <img src="/storage/themes/t-6/hero-bg.jpg" alt="" class="absolute inset-0 w-full h-full object-cover" aria-hidden="true" loading="eager" fetchpriority="high">
        {{-- Light mode: beyaz overlay / Dark mode: koyu overlay --}}
        <div class="absolute inset-0 bg-gradient-to-r from-white/90 via-white/85 to-white/75 dark:from-slate-950/95 dark:via-slate-950/90 dark:to-slate-950/85"></div>
    </div>

    {{-- Decorative Line --}}
    <div class="absolute top-0 left-0 w-full h-px bg-gradient-to-r from-transparent via-amber-500/30 to-transparent"></div>

    <div class="container mx-auto px-3 sm:px-6 md:px-8 lg:px-12 xl:px-16 2xl:px-20 relative z-10">
        {{-- Breadcrumb --}}
        <nav class="mb-6" data-aos="fade-up">
            <ol class="flex items-center space-x-2 text-sm">
                <li><a href="{{ url('/') }}" class="text-amber-700 dark:text-amber-400 hover:text-amber-600 dark:hover:text-amber-300 transition-colors">Ana Sayfa</a></li>
                <li class="text-slate-400 dark:text-slate-500">/</li>
                <li class="text-slate-600 dark:text-slate-300">Hizmetlerimiz</li>
            </ol>
        </nav>

        <div class="flex items-center gap-6" data-aos="fade-up" data-aos-delay="100">
            {{-- Icon --}}
            <div class="w-16 h-16 rounded-xl bg-amber-500/20 dark:bg-amber-500/20 backdrop-blur-sm flex items-center justify-center flex-shrink-0">
                <i class="fat fa-scale-balanced text-3xl text-amber-700 dark:text-amber-400"></i>
            </div>

            <div>
                <h1 class="font-heading text-2xl md:text-3xl lg:text-4xl font-bold text-slate-900 dark:text-white mb-2">
                    Hizmetlerimiz
                </h1>
                <p class="text-slate-700 dark:text-slate-300 text-base max-w-2xl">
                    Geniş bir yelpazede profesyonel danışmanlık ve dava takibi hizmeti sunuyoruz.
                </p>
            </div>
        </div>
    </div>
</section>

{{-- Services Grid --}}
<section class="py-16 md:py-24 bg-white dark:bg-slate-950">
    <div class="container mx-auto px-3 sm:px-6 md:px-8 lg:px-12 xl:px-16 2xl:px-20">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($items ?? [] as $index => $service)
            @php
                $icon = $serviceIcons[$service->slug] ?? 'fa-scale-balanced';
                $image = $serviceImages[$service->slug] ?? null;
            @endphp
            <a href="{{ url('/service/' . $service->slug) }}" class="service-card group bg-slate-50 dark:bg-slate-900/50 backdrop-blur-sm rounded-xl overflow-hidden shadow-lg dark:shadow-none" data-aos="fade-up" data-aos-delay="{{ 100 + ($index * 50) }}">
                @if($image)
                <div class="h-48 overflow-hidden">
                    <img src="{{ $image }}" alt="{{ $service->title }}" class="w-full h-full object-cover transition-opacity duration-500" loading="lazy">
                </div>
                @endif
                <div class="p-8">
                    <div class="w-14 h-14 rounded-xl bg-amber-500/10 flex items-center justify-center mb-6 group-hover:bg-amber-500/20 transition-colors {{ $image ? '-mt-12 relative z-10 bg-white dark:bg-slate-900 shadow-lg' : '' }}">
                        <i class="fat {{ $icon }} icon-hover text-2xl text-amber-700 dark:text-amber-400 group-hover:font-black transition-all"></i>
                    </div>
                    <h3 class="font-heading text-xl font-semibold text-slate-900 dark:text-white mb-4 tracking-wide">{{ $service->title }}</h3>
                    <p class="text-slate-700 dark:text-slate-300 text-base leading-relaxed font-medium mb-4">{{ Str::limit(strip_tags($service->summary ?? $service->body), 120) }}</p>
                    <span class="inline-flex items-center text-amber-700 dark:text-amber-400 font-heading text-sm tracking-wider uppercase group-hover:text-amber-600">
                        Detaylı Bilgi
                        <i class="fat fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                    </span>
                </div>
            </a>
            @empty
            {{-- Default services if none --}}
            @foreach([
                ['slug' => 'ticaret-hukuku', 'icon' => 'fa-handshake', 'title' => 'Ticaret Hukuku', 'desc' => 'Ticari sözleşmeler, anlaşmazlıklar ve uluslararası ticaret hukuku konularında danışmanlık ve dava takibi.'],
                ['slug' => 'sirketler-hukuku', 'icon' => 'fa-building', 'title' => 'Şirketler Hukuku', 'desc' => 'Şirket kuruluşu, birleşme-devir, borç yapılandırma, konkordato ve iflas süreçlerinde hukuki destek.'],
                ['slug' => 'ceza-hukuku', 'icon' => 'fa-gavel', 'title' => 'Ceza Hukuku', 'desc' => 'Soruşturma ve kovuşturma aşamalarında müdafilik, suçlu iadesi ve yeniden yargılama başvuruları.'],
                ['slug' => 'saglik-hukuku', 'icon' => 'fa-stethoscope', 'title' => 'Sağlık Hukuku', 'desc' => 'Tıbbi malpraktis, hekim ve sağlık kuruluşu sorumlulukları, hasta hakları konularında hukuki danışmanlık.'],
                ['slug' => 'sigorta-hukuku', 'icon' => 'fa-shield-check', 'title' => 'Sigorta Hukuku', 'desc' => 'Sigorta sözleşmelerinden kaynaklanan uyuşmazlıklar, tazminat talepleri ve poliçe anlaşmazlıkları.'],
                ['slug' => 'idare-ve-imar-hukuku', 'icon' => 'fa-landmark', 'title' => 'İdare ve İmar Hukuku', 'desc' => 'İdari işlem ve eylemler, imar uygulamaları, kamulaştırma ve iptal davaları.'],
                ['slug' => 'borclar-hukuku', 'icon' => 'fa-file-contract', 'title' => 'Borçlar Hukuku', 'desc' => 'Sözleşme hukuku, haksız fiil, sebepsiz zenginleşme ve tazminat davaları.'],
                ['slug' => 'is-hukuku', 'icon' => 'fa-briefcase', 'title' => 'İş Hukuku', 'desc' => 'İş sözleşmeleri, işçi-işveren uyuşmazlıkları, kıdem-ihbar tazminatı ve iş kazası davaları.'],
            ] as $index => $svc)
            <div class="service-card group bg-slate-50 dark:bg-slate-900/50 backdrop-blur-sm rounded-xl p-8 shadow-lg dark:shadow-none" data-aos="fade-up" data-aos-delay="{{ 100 + ($index * 50) }}">
                <div class="w-16 h-16 rounded-xl bg-amber-500/10 flex items-center justify-center mb-6 group-hover:bg-amber-500/20 transition-colors">
                    <i class="fat {{ $svc['icon'] }} icon-hover text-3xl text-amber-700 dark:text-amber-400 group-hover:font-black transition-all"></i>
                </div>
                <h3 class="font-heading text-xl font-semibold text-slate-900 dark:text-white mb-4 tracking-wide">{{ $svc['title'] }}</h3>
                <p class="text-slate-700 dark:text-slate-300 text-base leading-relaxed font-medium">{{ $svc['desc'] }}</p>
            </div>
            @endforeach
            @endforelse
        </div>
    </div>
</section>

{{-- CTA Section --}}
<section class="py-16 md:py-20 bg-slate-100 dark:bg-slate-900">
    <div class="container mx-auto px-3 sm:px-6 md:px-8 lg:px-12 xl:px-16 2xl:px-20">
        <div class="max-w-3xl mx-auto text-center">
            <div class="flex items-center justify-center mb-6" data-aos="fade-up">
                <div class="w-12 h-px bg-gradient-to-r from-transparent to-amber-500"></div>
                <div class="mx-3 art-deco-diamond scale-75"></div>
                <div class="w-12 h-px bg-gradient-to-l from-transparent to-amber-500"></div>
            </div>
            <h2 class="font-heading text-2xl md:text-3xl font-bold text-slate-900 dark:text-white mb-6" data-aos="fade-up" data-aos-delay="100">
                Hukuki Destek İçin Bize Ulaşın
            </h2>
            <p class="text-slate-700 dark:text-slate-300 mb-8 font-medium" data-aos="fade-up" data-aos-delay="200">
                Uzman kadromuz ile hukuki sorunlarınıza profesyonel çözümler sunuyoruz.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center" data-aos="fade-up" data-aos-delay="300">
                @if(setting('contact_phone_1'))
                <a href="tel:{{ preg_replace('/[^0-9+]/', '', setting('contact_phone_1')) }}" class="btn-shine bg-gradient-to-r from-amber-600 to-amber-500 text-white font-heading text-sm tracking-widest uppercase px-8 py-4 rounded-lg hover:from-amber-500 hover:to-amber-400 transition-all flex items-center justify-center">
                    <i class="fas fa-phone mr-3"></i>
                    Hemen Ara
                </a>
                @endif
                <a href="{{ url('/page/iletisim') }}" class="border-2 border-amber-600 dark:border-amber-500/50 text-amber-700 dark:text-amber-400 font-heading text-sm tracking-widest uppercase px-8 py-4 rounded-lg hover:bg-amber-500/10 hover:border-amber-500 transition-all flex items-center justify-center">
                    <i class="fas fa-envelope mr-3"></i>
                    İletişim
                </a>
            </div>
        </div>
    </div>
</section>

@endsection

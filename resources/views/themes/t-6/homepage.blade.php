@extends('themes.t-6.layouts.app')

@php
    $siteName = setting('site_title');
    $siteSlogan = setting('site_slogan');
    $siteDescription = setting('site_description');
    $sitePhone = setting('contact_phone_1');
    $siteEmail = setting('contact_email_1');
    $siteWhatsapp = setting('contact_whatsapp_1');
    $whatsappUrl = whatsapp_link();
    $siteAddress = setting('contact_address');

    // Services from database
    $services = \Modules\Service\App\Models\Service::where('is_active', true)
        ->orderBy('service_id')
        ->take(8)
        ->get();

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
@endphp

@section('content')

{{-- ========== HERO SECTION ========== --}}
<section class="relative min-h-screen flex items-center justify-center overflow-hidden art-deco-pattern bg-gradient-to-b from-slate-100 to-white dark:from-slate-950 dark:to-slate-900">
    {{-- Background Image - Same for Light/Dark --}}
    <div class="absolute inset-0 z-0">
        <img src="/storage/themes/t-6/hero-bg.jpg" alt="" class="absolute inset-0 w-full h-full object-cover opacity-40 dark:opacity-50 dark:brightness-75" aria-hidden="true" loading="eager" fetchpriority="high">
    </div>

    {{-- Background Gradient Overlay --}}
    <div class="absolute inset-0 z-[1] bg-gradient-to-b from-white/60 via-transparent to-white/70 dark:from-slate-950/80 dark:via-slate-900/60 dark:to-slate-950/90"></div>

    {{-- Art Deco Decorative Lines --}}
    <div class="absolute top-0 left-0 w-full h-px bg-gradient-to-r from-transparent via-amber-500/30 to-transparent"></div>
    <div class="absolute bottom-0 left-0 w-full h-px bg-gradient-to-r from-transparent via-amber-500/30 to-transparent"></div>

    {{-- Floating Geometric Shapes --}}
    <div class="absolute top-1/4 left-10 w-20 h-20 border border-amber-500/10 rotate-45 floating hidden lg:block" style="animation-delay: 0s;"></div>
    <div class="absolute top-1/3 right-20 w-16 h-16 border border-amber-500/10 rotate-45 floating hidden lg:block" style="animation-delay: 2s;"></div>
    <div class="absolute bottom-1/4 left-1/4 w-12 h-12 border border-amber-500/10 rotate-45 floating hidden lg:block" style="animation-delay: 4s;"></div>

    <div class="container mx-auto px-3 sm:px-6 md:px-8 lg:px-12 xl:px-16 2xl:px-20 relative z-10 pt-24">
        <div class="max-w-5xl mx-auto text-center">

            {{-- Art Deco Ornament --}}
            <div class="flex items-center justify-center mb-8" data-aos="fade-down" data-aos-duration="800">
                <div class="w-16 h-px bg-gradient-to-r from-transparent to-amber-500"></div>
                <div class="mx-4 art-deco-diamond"></div>
                <div class="w-16 h-px bg-gradient-to-l from-transparent to-amber-500"></div>
            </div>

            {{-- Tagline --}}
            <p class="font-heading text-sm md:text-base tracking-[0.3em] uppercase text-amber-700 dark:text-amber-400/80 mb-6" data-aos="fade-up" data-aos-delay="100">
                2009'dan Beri Güvenilir Hukuk Hizmeti
            </p>

            {{-- Main Headline --}}
            <h1 class="font-heading text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold leading-tight mb-8" data-aos="fade-up" data-aos-delay="200">
                <span class="text-slate-900 dark:text-white">Hukuki Haklarınız İçin</span> <span class="gradient-text-animate">Güvenilir Çözüm Ortağınız</span>
            </h1>

            {{-- Sub Headline --}}
            <p class="text-base text-slate-800 dark:text-slate-300 font-medium max-w-3xl mx-auto mb-12 leading-relaxed" data-aos="fade-up" data-aos-delay="300">
                Bireysel ve kurumsal müvekkillerimize <span class="text-amber-700 dark:text-amber-400">geniş bir yelpazede</span> profesyonel hukuki destek sunuyoruz. Adaletin ve hakkaniyet ilkelerinin savunucusu olarak, haklarınızı titizlikle koruyoruz.
            </p>

            {{-- CTA Buttons --}}
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 sm:gap-6 mb-16" data-aos="fade-up" data-aos-delay="400">
                @if($sitePhone)
                <a href="tel:{{ preg_replace('/[^0-9+]/', '', $sitePhone) }}" class="btn-shine group bg-gradient-to-r from-amber-600 to-amber-500 text-white font-heading text-sm tracking-widest uppercase px-8 py-4 rounded-lg hover:from-amber-500 hover:to-amber-400 transition-all flex items-center">
                    <span>Randevu Alın</span>
                    <i class="fat fa-arrow-right ml-3 group-hover:translate-x-1 transition-transform"></i>
                </a>
                @endif
                <a href="#hizmetler" class="group border-2 border-amber-600 dark:border-amber-500/50 text-amber-700 dark:text-amber-400 font-heading text-sm tracking-widest uppercase px-8 py-4 rounded-lg hover:bg-amber-500/10 hover:border-amber-500 transition-all flex items-center">
                    <span>Hizmetlerimiz</span>
                    <i class="fat fa-chevron-down ml-3 group-hover:translate-y-1 transition-transform"></i>
                </a>
            </div>

        </div>
    </div>

    {{-- Scroll Indicator --}}
    <div class="absolute bottom-8 left-1/2 -translate-x-1/2 flex flex-col items-center text-amber-700 dark:text-amber-500/70">
        <span class="font-heading text-[10px] tracking-widest uppercase mb-2">Keşfet</span>
        <div class="w-6 h-10 border-2 border-amber-500/30 rounded-full flex justify-center">
            <div class="w-1 h-3 bg-amber-500/50 rounded-full mt-2 animate-bounce"></div>
        </div>
    </div>
</section>

{{-- ========== SERVICES SECTION ========== --}}
<section id="hizmetler" class="py-20 md:py-28 lg:py-32 bg-slate-100 dark:bg-slate-900 relative overflow-hidden">
    {{-- Background Pattern --}}
    <div class="absolute inset-0 art-deco-pattern opacity-50"></div>

    {{-- Top Line --}}
    <div class="absolute top-0 left-0 w-full h-px bg-gradient-to-r from-transparent via-amber-500/20 to-transparent"></div>

    <div class="container mx-auto px-3 sm:px-6 md:px-8 lg:px-12 xl:px-16 2xl:px-20 relative z-10">

        {{-- Section Header --}}
        <div class="text-center max-w-3xl mx-auto mb-16 md:mb-20">
            <div class="flex items-center justify-center mb-6" data-aos="fade-up">
                <div class="w-12 h-px bg-gradient-to-r from-transparent to-amber-500"></div>
                <div class="mx-3 art-deco-diamond scale-75"></div>
                <div class="w-12 h-px bg-gradient-to-l from-transparent to-amber-500"></div>
            </div>
            <p class="font-heading text-sm tracking-[0.3em] uppercase text-amber-700 dark:text-amber-400/80 mb-4" data-aos="fade-up" data-aos-delay="100">Uzmanlık Alanlarımız</p>
            <h2 class="font-heading text-3xl md:text-4xl lg:text-5xl font-bold mb-6" data-aos="fade-up" data-aos-delay="200">
                <span class="gradient-text">Kapsamlı</span> <span class="text-slate-900 dark:text-white">Hukuki Destek</span>
            </h2>
            <p class="text-base text-slate-800 dark:text-slate-300 leading-relaxed font-medium" data-aos="fade-up" data-aos-delay="300">
                Her dosyayı titizlikle inceler, müvekkillerimizin hukuki çıkarlarını koruyacak stratejiler geliştiririz.
            </p>
        </div>

        {{-- Services Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @forelse($services as $index => $service)
            @php
                $icon = $serviceIcons[$service->slug] ?? 'fa-scale-balanced';
            @endphp
            <a href="{{ url('/service/' . $service->slug) }}" class="service-card group bg-white dark:bg-slate-950/50 backdrop-blur-sm rounded-xl p-6 lg:p-8 shadow-lg dark:shadow-none" data-aos="fade-up" data-aos-delay="{{ 100 + ($index * 50) }}">
                <div class="w-14 h-14 rounded-lg bg-amber-500/10 flex items-center justify-center mb-6 group-hover:bg-amber-500/20 transition-colors">
                    <i class="fat {{ $icon }} icon-hover text-2xl text-amber-700 dark:text-amber-400 group-hover:font-black transition-all"></i>
                </div>
                <h3 class="font-heading text-lg font-semibold text-slate-900 dark:text-white mb-3 tracking-wide">{{ $service->title }}</h3>
                <p class="text-slate-800 dark:text-slate-300 text-base leading-relaxed font-medium">{{ Str::limit(strip_tags($service->summary ?? $service->body), 100) }}</p>
            </a>
            @empty
            {{-- Default services if none in database --}}
            @foreach([
                ['icon' => 'fa-handshake', 'title' => 'Ticaret Hukuku', 'desc' => 'Ticari sözleşmeler, anlaşmazlıklar ve uluslararası ticaret hukuku konularında danışmanlık.'],
                ['icon' => 'fa-building', 'title' => 'Şirketler Hukuku', 'desc' => 'Şirket kuruluşu, birleşme-devir, konkordato ve iflas süreçlerinde hukuki destek.'],
                ['icon' => 'fa-gavel', 'title' => 'Ceza Hukuku', 'desc' => 'Soruşturma ve kovuşturma aşamalarında müdafilik, yeniden yargılama başvuruları.'],
                ['icon' => 'fa-stethoscope', 'title' => 'Sağlık Hukuku', 'desc' => 'Tıbbi malpraktis, hekim sorumlulukları, hasta hakları konularında danışmanlık.'],
                ['icon' => 'fa-shield-check', 'title' => 'Sigorta Hukuku', 'desc' => 'Sigorta sözleşmelerinden kaynaklanan uyuşmazlıklar ve tazminat talepleri.'],
                ['icon' => 'fa-landmark', 'title' => 'İdare ve İmar Hukuku', 'desc' => 'İdari işlem ve eylemler, imar uygulamaları, kamulaştırma ve iptal davaları.'],
                ['icon' => 'fa-file-contract', 'title' => 'Borçlar Hukuku', 'desc' => 'Sözleşme hukuku, haksız fiil, sebepsiz zenginleşme ve tazminat davaları.'],
                ['icon' => 'fa-briefcase', 'title' => 'İş Hukuku', 'desc' => 'İş sözleşmeleri, işçi-işveren uyuşmazlıkları, kıdem-ihbar tazminatı davaları.'],
            ] as $index => $svc)
            <div class="service-card group bg-white dark:bg-slate-950/50 backdrop-blur-sm rounded-xl p-6 lg:p-8 shadow-lg dark:shadow-none" data-aos="fade-up" data-aos-delay="{{ 100 + ($index * 50) }}">
                <div class="w-14 h-14 rounded-lg bg-amber-500/10 flex items-center justify-center mb-6 group-hover:bg-amber-500/20 transition-colors">
                    <i class="fat {{ $svc['icon'] }} icon-hover text-2xl text-amber-700 dark:text-amber-400 group-hover:font-black transition-all"></i>
                </div>
                <h3 class="font-heading text-lg font-semibold text-slate-900 dark:text-white mb-3 tracking-wide">{{ $svc['title'] }}</h3>
                <p class="text-slate-800 dark:text-slate-300 text-base leading-relaxed font-medium">{{ $svc['desc'] }}</p>
            </div>
            @endforeach
            @endforelse
        </div>

    </div>
</section>

{{-- ========== ABOUT SECTION ========== --}}
<section id="hakkimizda" class="py-20 md:py-28 lg:py-32 bg-white dark:bg-slate-950 relative overflow-hidden">
    {{-- Art Deco Corner Decorations --}}
    <div class="absolute top-8 left-8 art-deco-corner art-deco-corner-tl hidden lg:block"></div>
    <div class="absolute top-8 right-8 art-deco-corner art-deco-corner-tr hidden lg:block"></div>
    <div class="absolute bottom-8 left-8 art-deco-corner art-deco-corner-bl hidden lg:block"></div>
    <div class="absolute bottom-8 right-8 art-deco-corner art-deco-corner-br hidden lg:block"></div>

    <div class="container mx-auto px-3 sm:px-6 md:px-8 lg:px-12 xl:px-16 2xl:px-20 relative z-10">
        <div class="grid lg:grid-cols-2 gap-12 lg:gap-20 items-center">

            {{-- Left: Content --}}
            <div>
                <div class="flex items-center mb-6" data-aos="fade-right">
                    <div class="w-12 h-px bg-gradient-to-r from-amber-500 to-transparent"></div>
                    <div class="ml-3 art-deco-diamond scale-75"></div>
                </div>
                <p class="font-heading text-sm tracking-[0.3em] uppercase text-amber-700 dark:text-amber-400/80 mb-4" data-aos="fade-right" data-aos-delay="100">Kurumsal</p>
                <h2 class="font-heading text-3xl md:text-4xl lg:text-5xl font-bold mb-8" data-aos="fade-right" data-aos-delay="200">
                    <span class="gradient-text">Güvenilir</span> <span class="text-slate-900 dark:text-white">Hukuk Danışmanlığı</span>
                </h2>

                <div class="space-y-6 text-base text-slate-800 dark:text-slate-300 leading-relaxed font-medium" data-aos="fade-right" data-aos-delay="300">
                    <p>
                        <span class="text-amber-800 dark:text-amber-400 font-bold">{{ $siteName }} {{ $siteSlogan }}</span>, İstanbul merkezli, ulusal ve uluslararası hukuki hizmetler sunan profesyonel bir avukatlık bürosudur. 2009 yılından bu yana bireysel ve kurumsal müvekkillerimize adalet ve hakkaniyet ilkeleri doğrultusunda hizmet vermekteyiz.
                    </p>
                    <p>
                        Her müvekkil için özel stratejiler geliştiriyor, karmaşık hukuki sorunlara pratik ve etkili çözümler üretiyoruz. Hukuki süreçlerde <span class="text-slate-900 dark:text-white">şeffaflık, dürüstlük ve müvekkil memnuniyeti</span> bizim için öncelikli değerlerdir.
                    </p>
                </div>

                {{-- Key Points --}}
                <div class="grid grid-cols-2 gap-4 mt-8" data-aos="fade-right" data-aos-delay="400">
                    @foreach([
                        'Deneyimli Kadro',
                        'Çözüm Odaklı',
                        'Şeffaf İletişim',
                        'Gizlilik Garantisi'
                    ] as $point)
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded bg-amber-500/20 dark:bg-amber-500/10 flex items-center justify-center">
                            <i class="fat fa-check text-amber-700 dark:text-amber-400"></i>
                        </div>
                        <span class="text-slate-800 dark:text-slate-300 text-base font-medium">{{ $point }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Right: Image & Quote --}}
            <div class="relative" data-aos="fade-left" data-aos-delay="200">
                {{-- Main Image --}}
                <div class="animated-border">
                    <div class="animated-border-inner p-2">
                        <img
                            src="/storage/themes/t-6/hero-lawyer.jpg"
                            alt="{{ $siteName }} Ofisi"
                            class="w-full h-auto rounded-lg object-cover"
                            loading="lazy"
                            decoding="async"
                        >
                    </div>
                </div>

                {{-- Floating Quote Card --}}
                <div class="absolute -bottom-8 -left-8 lg:-left-16 max-w-xs bg-gradient-to-br from-amber-600 to-amber-500 rounded-xl p-6 shadow-2xl" data-aos="fade-up" data-aos-delay="400">
                    <span class="text-5xl text-slate-950/30 font-serif leading-none block -mb-4">"</span>
                    <p class="text-slate-950 font-medium text-base leading-relaxed italic">
                        Adaletin savunucusu olarak, müvekkillerimizin haklarını titizlikle korumak en önemli görevimizdir.
                    </p>
                    <div class="mt-4 flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-full bg-slate-950/20 flex items-center justify-center">
                            <i class="fas fa-user text-slate-950"></i>
                        </div>
                        <div>
                            <p class="font-heading text-sm font-semibold text-slate-950">Av. Semih Mahmutoğlu</p>
                            <p class="text-xs text-slate-950/70">Kurucu Ortak</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- ========== CONTACT CTA SECTION ========== --}}
<section id="iletisim" class="py-20 md:py-28 lg:py-32 bg-white dark:bg-slate-900 relative overflow-hidden">
    {{-- Art Deco Background --}}
    <div class="absolute inset-0 art-deco-pattern opacity-30"></div>
    <div class="absolute inset-0 bg-gradient-to-b from-white via-transparent to-white dark:from-slate-900 dark:via-transparent dark:to-slate-900"></div>

    <div class="container mx-auto px-3 sm:px-6 md:px-8 lg:px-12 xl:px-16 2xl:px-20 relative z-10">

        <div class="max-w-5xl mx-auto">

            {{-- Section Header --}}
            <div class="text-center mb-16">
                <div class="flex items-center justify-center mb-6" data-aos="fade-up">
                    <div class="w-12 h-px bg-gradient-to-r from-transparent to-amber-500"></div>
                    <div class="mx-3 art-deco-diamond scale-75"></div>
                    <div class="w-12 h-px bg-gradient-to-l from-transparent to-amber-500"></div>
                </div>
                <p class="font-heading text-sm tracking-[0.3em] uppercase text-amber-700 dark:text-amber-400/80 mb-4" data-aos="fade-up" data-aos-delay="100">İletişim</p>
                <h2 class="font-heading text-3xl md:text-4xl lg:text-5xl font-bold mb-6" data-aos="fade-up" data-aos-delay="200">
                    <span class="text-slate-900 dark:text-white">Hukuki Destek İçin</span> <span class="gradient-text">Bize Ulaşın</span>
                </h2>
                <p class="text-lg text-slate-800 dark:text-slate-300 max-w-2xl mx-auto" data-aos="fade-up" data-aos-delay="300">
                    Hukuki sorunlarınız ve sorularınız için bizimle iletişime geçebilirsiniz. Size en kısa sürede dönüş yapacağız.
                </p>
            </div>

            {{-- Contact Cards --}}
            <div class="grid md:grid-cols-3 gap-6">

                {{-- Phone --}}
                @if($sitePhone)
                <a href="tel:{{ preg_replace('/[^0-9+]/', '', $sitePhone) }}" class="gradient-border group" data-aos="fade-up" data-aos-delay="100">
                    <div class="gradient-border-inner p-8 text-center">
                        <div class="w-16 h-16 mx-auto rounded-xl bg-amber-500/10 flex items-center justify-center mb-6 group-hover:bg-amber-500/20 transition-colors">
                            <i class="fat fa-phone icon-hover text-2xl text-amber-700 dark:text-amber-400 group-hover:font-black transition-all"></i>
                        </div>
                        <h3 class="font-heading text-lg font-semibold text-slate-900 dark:text-white mb-2">Telefon</h3>
                        <span class="text-amber-700 dark:text-amber-400 text-lg font-semibold">{{ $sitePhone }}</span>
                    </div>
                </a>
                @endif

                {{-- Email --}}
                @if($siteEmail)
                <a href="mailto:{{ $siteEmail }}" class="gradient-border group" data-aos="fade-up" data-aos-delay="200">
                    <div class="gradient-border-inner p-8 text-center">
                        <div class="w-16 h-16 mx-auto rounded-xl bg-amber-500/10 flex items-center justify-center mb-6 group-hover:bg-amber-500/20 transition-colors">
                            <i class="fat fa-envelope icon-hover text-2xl text-amber-700 dark:text-amber-400 group-hover:font-black transition-all"></i>
                        </div>
                        <h3 class="font-heading text-lg font-semibold text-slate-900 dark:text-white mb-2">E-posta</h3>
                        <span class="text-amber-700 dark:text-amber-400 font-semibold">{{ $siteEmail }}</span>
                    </div>
                </a>
                @endif

                {{-- Address --}}
                @if($siteAddress)
                <div class="gradient-border group" data-aos="fade-up" data-aos-delay="300">
                    <div class="gradient-border-inner p-8 text-center">
                        <div class="w-16 h-16 mx-auto rounded-xl bg-amber-500/10 flex items-center justify-center mb-6 group-hover:bg-amber-500/20 transition-colors">
                            <i class="fat fa-location-dot icon-hover text-2xl text-amber-700 dark:text-amber-400 group-hover:font-black transition-all"></i>
                        </div>
                        <h3 class="font-heading text-lg font-semibold text-slate-900 dark:text-white mb-2">Adres</h3>
                        <p class="text-slate-800 dark:text-slate-300 text-base font-medium">{{ $siteAddress }}</p>
                    </div>
                </div>
                @endif

            </div>

        </div>

    </div>
</section>

@endsection

@extends('themes.t-6.layouts.app')

@php
    // Controller'dan gelen $item'ı $page olarak kullan
    $page = $item;

    $siteName = setting('site_title') ?: setting('site_company_name') ?: 'Mahmutoğlu';
    $sitePhone = setting('contact_phone_1');
    $siteEmail = setting('contact_email_1') ?: setting('site_email');
    $siteAddress = setting('contact_address');

    // Page images mapping
    $pageImages = [
        'hakkimizda' => '/storage/themes/t-6/hakkimizda-hero.jpg',
        'iletisim' => '/storage/themes/t-6/hero-lawyer.jpg',
    ];
    $pageImage = $pageImages[$page->slug] ?? '/storage/themes/t-6/hero-bg.jpg';

    // Page icons mapping
    $pageIcons = [
        'hakkimizda' => 'fa-building-columns',
        'iletisim' => 'fa-envelope',
    ];
    $pageIcon = $pageIcons[$page->slug] ?? 'fa-file-lines';

    // Değerler - Hakkımızda sayfasında kullanılacak
    $values = [
        ['icon' => 'fa-scale-balanced', 'title' => 'Adalet ve Dürüstlük', 'desc' => 'Her davada adalet ve dürüstlük ilkelerini ön planda tutarız.'],
        ['icon' => 'fa-award', 'title' => 'Profesyonellik', 'desc' => 'Mesleki etik kurallarına sıkı sıkıya bağlı kalırız.'],
        ['icon' => 'fa-lock', 'title' => 'Gizlilik', 'desc' => 'Müvekkil-avukat güven ilişkisini koruruz.'],
        ['icon' => 'fa-graduation-cap', 'title' => 'Sürekli Gelişim', 'desc' => 'Hukuk alanındaki yenilikleri takip ederiz.'],
        ['icon' => 'fa-heart', 'title' => 'Müvekkil Memnuniyeti', 'desc' => 'Şeffaf süreç yönetimi sağlarız.'],
        ['icon' => 'fa-users', 'title' => 'Ekip Çalışması', 'desc' => 'İşbirliği içinde profesyonel hizmet sunarız.'],
    ];
@endphp

@section('content')

{{-- Page Header with Background Image --}}
<section class="relative pt-24 pb-10 overflow-hidden">
    {{-- Background Image --}}
    <div class="absolute inset-0">
        <img src="{{ $pageImage }}" alt="" class="absolute inset-0 w-full h-full object-cover" aria-hidden="true">
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
                <li class="text-slate-600 dark:text-slate-300">{{ $page->title }}</li>
            </ol>
        </nav>

        <div class="flex items-center gap-6" data-aos="fade-up" data-aos-delay="100">
            {{-- Icon --}}
            <div class="w-16 h-16 rounded-xl bg-amber-500/20 dark:bg-amber-500/20 backdrop-blur-sm flex items-center justify-center flex-shrink-0">
                <i class="fat {{ $pageIcon }} text-3xl text-amber-700 dark:text-amber-400"></i>
            </div>

            <div>
                <h1 class="font-heading text-2xl md:text-3xl lg:text-4xl font-bold text-slate-900 dark:text-white mb-2">
                    {{ $page->title }}
                </h1>
                @if($page->summary)
                <p class="text-slate-700 dark:text-slate-300 text-base max-w-2xl">
                    {{ Str::limit($page->summary, 150) }}
                </p>
                @endif
            </div>
        </div>
    </div>
</section>

{{-- ========== HAKKIMIZDA PAGE ========== --}}
@if($page->slug === 'hakkimizda')
<section class="py-16 md:py-24 bg-white dark:bg-slate-950">
    <div class="container mx-auto px-3 sm:px-6 md:px-8 lg:px-12 xl:px-16 2xl:px-20">

        {{-- Two Column Layout - Balanced --}}
        <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">

            {{-- Left: Content --}}
            <div data-aos="fade-right">
                <span class="inline-block px-4 py-1 bg-amber-500/10 text-amber-700 dark:text-amber-400 text-sm font-semibold rounded-full mb-4">
                    HAKKIMIZDA
                </span>
                <h2 class="font-heading text-3xl md:text-4xl font-bold mb-6" style="line-height: 1.2;">
                    <span class="text-slate-900 dark:text-white">Tecrübeyi Güvenle</span>
                    <span class="gradient-text block">Birleştiriyoruz</span>
                </h2>

                {{-- Content from DB --}}
                <div class="prose dark:prose-invert max-w-none prose-headings:font-heading prose-headings:text-slate-900 dark:prose-headings:text-white prose-a:text-amber-600 dark:prose-a:text-amber-400 mb-8">
                    {!! $page->body !!}
                </div>

                {{-- Quick Stats --}}
                <div class="grid grid-cols-3 gap-4">
                    <div class="text-center p-4 bg-slate-50 dark:bg-slate-900/50 rounded-xl">
                        <span class="block font-heading text-3xl font-bold text-amber-600 dark:text-amber-400">15+</span>
                        <span class="text-sm text-slate-600 dark:text-slate-400">Yıllık Deneyim</span>
                    </div>
                    <div class="text-center p-4 bg-slate-50 dark:bg-slate-900/50 rounded-xl">
                        <span class="block font-heading text-3xl font-bold text-amber-600 dark:text-amber-400">500+</span>
                        <span class="text-sm text-slate-600 dark:text-slate-400">Başarılı Dava</span>
                    </div>
                    <div class="text-center p-4 bg-slate-50 dark:bg-slate-900/50 rounded-xl">
                        <span class="block font-heading text-3xl font-bold text-amber-600 dark:text-amber-400">8</span>
                        <span class="text-sm text-slate-600 dark:text-slate-400">Uzmanlık Alanı</span>
                    </div>
                </div>
            </div>

            {{-- Right: Image --}}
            <div class="relative" data-aos="fade-left">
                <div class="aspect-[4/3] rounded-2xl overflow-hidden shadow-2xl bg-gradient-to-br from-slate-800 to-slate-900 flex items-center justify-center">
                    {{-- Leonardo AI görsel yüklenene kadar placeholder --}}
                    @php
                        $aboutImage = '/storage/themes/t-6/hakkimizda-hero.jpg';
                        $imageExists = file_exists(public_path('storage/themes/t-6/hakkimizda-hero.jpg'));
                    @endphp
                    @if($imageExists)
                        <img src="{{ $aboutImage }}" alt="Hukuk Bürosu" class="w-full h-full object-cover">
                    @else
                        <div class="text-center p-8">
                            <i class="fat fa-scale-balanced text-6xl text-amber-500/30 mb-4"></i>
                            <p class="text-slate-500 text-sm">Görsel yükleniyor...</p>
                        </div>
                    @endif
                </div>

                {{-- Floating Card --}}
                <div class="absolute -bottom-6 -left-6 bg-white dark:bg-slate-900 rounded-xl p-6 shadow-xl hidden lg:block" data-aos="fade-up" data-aos-delay="200">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl flex items-center justify-center">
                            <i class="fat fa-scale-balanced text-2xl text-white"></i>
                        </div>
                        <div>
                            <span class="block font-heading text-lg font-bold text-slate-900 dark:text-white">2009'dan Beri</span>
                            <span class="text-sm text-slate-600 dark:text-slate-400">Güvenilir Hukuk Hizmeti</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

{{-- Values Section - Full Width --}}
<section class="py-16 md:py-20 bg-slate-100 dark:bg-slate-900">
    <div class="container mx-auto px-3 sm:px-6 md:px-8 lg:px-12 xl:px-16 2xl:px-20">

        <div class="text-center mb-12">
            <div class="flex items-center justify-center mb-6" data-aos="fade-up">
                <div class="w-12 h-px bg-gradient-to-r from-transparent to-amber-500"></div>
                <div class="mx-3 art-deco-diamond scale-75"></div>
                <div class="w-12 h-px bg-gradient-to-l from-transparent to-amber-500"></div>
            </div>
            <p class="font-heading text-sm tracking-[0.3em] uppercase text-amber-700 dark:text-amber-400/80 mb-4" data-aos="fade-up">Değerlerimiz</p>
            <h2 class="font-heading text-2xl md:text-3xl font-bold" data-aos="fade-up">
                <span class="text-slate-900 dark:text-white">Hizmet Anlayışımızın</span> <span class="gradient-text">Temelleri</span>
            </h2>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($values as $index => $value)
            <div class="group bg-white dark:bg-slate-950/50 rounded-xl p-6 hover:shadow-lg transition-all" data-aos="fade-up" data-aos-delay="{{ 100 + ($index * 50) }}">
                <div class="w-14 h-14 rounded-xl bg-amber-500/10 flex items-center justify-center mb-4 group-hover:bg-amber-500/20 transition-colors">
                    <i class="fat {{ $value['icon'] }} text-2xl text-amber-700 dark:text-amber-400"></i>
                </div>
                <h3 class="font-heading text-lg font-semibold text-slate-900 dark:text-white mb-2">{{ $value['title'] }}</h3>
                <p class="text-slate-700 dark:text-slate-300 text-sm">{{ $value['desc'] }}</p>
            </div>
            @endforeach
        </div>

    </div>
</section>

{{-- ========== ILETISIM PAGE ========== --}}
@elseif($page->slug === 'iletisim')
<section class="py-16 md:py-24 bg-white dark:bg-slate-950">
    <div class="container mx-auto px-3 sm:px-6 md:px-8 lg:px-12 xl:px-16 2xl:px-20">

        {{-- Two Column Layout --}}
        <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-stretch">

            {{-- Left: Contact Info --}}
            <div data-aos="fade-right">
                <span class="inline-block px-4 py-1 bg-amber-500/10 text-amber-700 dark:text-amber-400 text-sm font-semibold rounded-full mb-4">
                    İLETİŞİM
                </span>
                <h2 class="font-heading text-3xl md:text-4xl font-bold mb-4" style="line-height: 1.2;">
                    <span class="text-slate-900 dark:text-white">Bize</span> <span class="gradient-text">Ulaşın</span>
                </h2>

                {{-- Content from DB --}}
                @if($page->body)
                <div class="prose dark:prose-invert max-w-none mb-8">
                    {!! $page->body !!}
                </div>
                @else
                <p class="text-slate-700 dark:text-slate-300 mb-8">
                    Hukuki sorunlarınız ve sorularınız için bizimle iletişime geçebilirsiniz.
                </p>
                @endif

                {{-- Contact Cards --}}
                <div class="space-y-4">
                    @if($siteAddress)
                    <div class="flex items-start gap-4 p-5 bg-slate-50 dark:bg-slate-900/50 rounded-xl">
                        <div class="w-12 h-12 bg-amber-500/10 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fat fa-location-dot text-xl text-amber-700 dark:text-amber-400"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-slate-900 dark:text-white mb-1">Adres</h4>
                            <p class="text-slate-700 dark:text-slate-300">{{ $siteAddress }}</p>
                        </div>
                    </div>
                    @endif

                    @if($sitePhone)
                    <div class="flex items-start gap-4 p-5 bg-slate-50 dark:bg-slate-900/50 rounded-xl">
                        <div class="w-12 h-12 bg-amber-500/10 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fat fa-phone text-xl text-amber-700 dark:text-amber-400"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-slate-900 dark:text-white mb-1">Telefon</h4>
                            <a href="tel:{{ preg_replace('/[^0-9+]/', '', $sitePhone) }}" class="text-amber-700 dark:text-amber-400 hover:text-amber-600 font-semibold text-lg">
                                {{ $sitePhone }}
                            </a>
                        </div>
                    </div>
                    @endif

                    @if($siteEmail)
                    <div class="flex items-start gap-4 p-5 bg-slate-50 dark:bg-slate-900/50 rounded-xl">
                        <div class="w-12 h-12 bg-amber-500/10 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fat fa-envelope text-xl text-amber-700 dark:text-amber-400"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-slate-900 dark:text-white mb-1">E-posta</h4>
                            <a href="mailto:{{ $siteEmail }}" class="text-amber-700 dark:text-amber-400 hover:text-amber-600 font-semibold">
                                {{ $siteEmail }}
                            </a>
                        </div>
                    </div>
                    @endif
                </div>

                {{-- CTA Buttons --}}
                <div class="flex flex-wrap gap-4 mt-8">
                    @if($sitePhone)
                    <a href="tel:{{ preg_replace('/[^0-9+]/', '', $sitePhone) }}" class="btn-shine bg-gradient-to-r from-amber-600 to-amber-500 text-white font-heading text-sm tracking-widest uppercase px-6 py-3 rounded-lg hover:from-amber-500 hover:to-amber-400 transition-all flex items-center">
                        <i class="fas fa-phone mr-2"></i>
                        Hemen Ara
                    </a>
                    @endif
                    @if($siteEmail)
                    <a href="mailto:{{ $siteEmail }}" class="border-2 border-amber-600 dark:border-amber-500/50 text-amber-700 dark:text-amber-400 font-heading text-sm tracking-widest uppercase px-6 py-3 rounded-lg hover:bg-amber-500/10 transition-all flex items-center">
                        <i class="fas fa-envelope mr-2"></i>
                        E-posta Gönder
                    </a>
                    @endif
                </div>
            </div>

            {{-- Right: Contact Form --}}
            <div data-aos="fade-left" class="flex">
                <div class="bg-slate-50 dark:bg-slate-900/50 rounded-2xl p-8 lg:p-10 w-full">
                    <h3 class="font-heading text-2xl text-slate-900 dark:text-white mb-6">Mesaj Gönderin</h3>

                    <form class="space-y-6" action="#" method="POST">
                        @csrf
                        <div class="grid sm:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Ad Soyad *</label>
                                <input type="text" name="name" required
                                       class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent text-slate-900 dark:text-white placeholder-slate-400 transition-all"
                                       placeholder="Adınız Soyadınız">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Telefon *</label>
                                <input type="tel" name="phone" required
                                       class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent text-slate-900 dark:text-white placeholder-slate-400 transition-all"
                                       placeholder="+90 (___) ___ __ __">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">E-posta *</label>
                            <input type="email" name="email" required
                                   class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent text-slate-900 dark:text-white placeholder-slate-400 transition-all"
                                   placeholder="email@example.com">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Konu</label>
                            <select name="subject"
                                    class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent text-slate-900 dark:text-white transition-all">
                                <option value="">Konu Seçiniz</option>
                                <option value="danismanlik">Hukuki Danışmanlık</option>
                                <option value="dava">Dava Takibi</option>
                                <option value="sozlesme">Sözleşme İncelemesi</option>
                                <option value="diger">Diğer</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Mesajınız *</label>
                            <textarea rows="4" name="message" required
                                      class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent text-slate-900 dark:text-white placeholder-slate-400 resize-none transition-all"
                                      placeholder="Mesajınızı yazınız..."></textarea>
                        </div>

                        <button type="submit"
                                class="btn-shine w-full px-8 py-4 bg-gradient-to-r from-amber-600 to-amber-500 text-white font-heading text-sm tracking-widest uppercase rounded-lg hover:from-amber-500 hover:to-amber-400 transition-all flex items-center justify-center gap-2">
                            <i class="fat fa-paper-plane"></i>
                            Mesaj Gönder
                        </button>

                        <p class="text-xs text-slate-500 dark:text-slate-400 text-center">
                            * Zorunlu alanlar. Bilgileriniz gizli tutulacaktır.
                        </p>
                    </form>
                </div>
            </div>
        </div>

    </div>
</section>

{{-- ========== GENERIC PAGE ========== --}}
@else
<section class="py-16 md:py-24 bg-white dark:bg-slate-950">
    <div class="container mx-auto px-3 sm:px-6 md:px-8 lg:px-12 xl:px-16 2xl:px-20">
        <div class="max-w-4xl mx-auto" data-aos="fade-up">

            {{-- Page Cover Image --}}
            @if($page->getFirstMediaUrl('cover'))
            <div class="animated-border mb-12">
                <div class="animated-border-inner p-2">
                    <img src="{{ $page->getFirstMediaUrl('cover') }}" alt="{{ $page->title }}" class="w-full h-auto rounded-lg object-cover">
                </div>
            </div>
            @endif

            {{-- Content --}}
            <div class="prose prose-lg dark:prose-invert max-w-none prose-headings:font-heading prose-headings:text-slate-900 dark:prose-headings:text-white prose-a:text-amber-600 dark:prose-a:text-amber-400 prose-strong:text-slate-900 dark:prose-strong:text-white">
                {!! $page->body !!}
            </div>

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
                Sorularınız ve hukuki danışmanlık için bizimle iletişime geçebilirsiniz.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center" data-aos="fade-up" data-aos-delay="300">
                @if($sitePhone)
                <a href="tel:{{ preg_replace('/[^0-9+]/', '', $sitePhone) }}" class="btn-shine bg-gradient-to-r from-amber-600 to-amber-500 text-white font-heading text-sm tracking-widest uppercase px-8 py-4 rounded-lg hover:from-amber-500 hover:to-amber-400 transition-all flex items-center justify-center">
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
@endif

@endsection

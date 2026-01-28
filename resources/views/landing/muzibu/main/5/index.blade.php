@extends('layouts.landing.muzibu')

@push('meta')
    <title>Kurumsal Müzik Çözümleri | Muzibu</title>
    <meta name="description" content="İşletmeniz için %100 yasal müzik lisansı. 150.000 TL telif cezası riskinden korunun. Profesyonel müzik altyapısı.">
    <meta property="og:title" content="Kurumsal Müzik Çözümleri | Muzibu">
    <meta property="og:url" content="{{ url('/landing5') }}">
    <link rel="canonical" href="{{ url('/landing5') }}">
@endpush

@push('styles')
<style>
    /* ========================================
       SPACING SYSTEM (8px grid)
       ======================================== */
    :root {
        --coral: #ff6b6b;
        --coral-dark: #e85555;
        --space-xs: 0.5rem;   /* 8px */
        --space-sm: 1rem;     /* 16px */
        --space-md: 1.5rem;   /* 24px */
        --space-lg: 2rem;     /* 32px */
        --space-xl: 3rem;     /* 48px */
        --space-2xl: 4rem;    /* 64px */
        --space-3xl: 6rem;    /* 96px */
    }

    /* ========================================
       HERO - Corporate
       ======================================== */
    .hero-corporate {
        background: #0f0f10;
        padding: var(--space-3xl) 0;
    }

    @media (min-width: 1024px) {
        .hero-corporate {
            padding: var(--space-3xl) 0 calc(var(--space-3xl) + 2rem) 0;
        }
    }

    /* ========================================
       SECTIONS - Consistent Spacing
       ======================================== */
    .section-padding {
        padding: var(--space-2xl) 0;
    }

    @media (min-width: 1024px) {
        .section-padding {
            padding: var(--space-3xl) 0;
        }
    }

    /* ========================================
       CARDS - Corporate Style
       ======================================== */
    .card-corporate {
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 1rem;
        padding: var(--space-lg);
        transition: border-color 0.3s ease, background 0.3s ease;
    }

    .card-corporate:hover {
        border-color: rgba(255, 107, 107, 0.3);
        background: rgba(255, 255, 255, 0.04);
    }

    /* ========================================
       STATS - Minimal Grid
       ======================================== */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: var(--space-md);
    }

    @media (min-width: 1024px) {
        .stats-grid {
            grid-template-columns: repeat(4, 1fr);
        }
    }

    .stat-item {
        text-align: center;
        padding: var(--space-lg);
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 1rem;
    }

    /* ========================================
       FAQ - Corporate
       ======================================== */
    .faq-item {
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 0.75rem;
        overflow: hidden;
        transition: border-color 0.3s ease;
    }

    .faq-item:hover {
        border-color: rgba(255, 107, 107, 0.3);
    }

    .faq-item button {
        padding: var(--space-md);
    }

    .faq-item .faq-answer {
        padding: 0 var(--space-md) var(--space-md) var(--space-md);
    }

    /* ========================================
       TABLE - Comparison
       ======================================== */
    .comparison-table {
        width: 100%;
        border-collapse: collapse;
    }

    .comparison-table th,
    .comparison-table td {
        padding: var(--space-md);
        text-align: center;
        border-bottom: 1px solid rgba(255, 255, 255, 0.06);
    }

    .comparison-table thead th {
        background: rgba(255, 107, 107, 0.1);
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .comparison-table th:first-child,
    .comparison-table td:first-child {
        text-align: left;
    }

    /* ========================================
       STICKY CTA
       ======================================== */
    .sticky-cta {
        position: fixed;
        bottom: var(--space-md);
        left: var(--space-md);
        z-index: 40;
    }

    @media (min-width: 1024px) {
        .sticky-cta {
            left: 260px;
        }
    }

    /* ========================================
       VIDEO CARD
       ======================================== */
    .video-card {
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 1rem;
        overflow: hidden;
        transition: border-color 0.3s ease;
    }

    .video-card:hover {
        border-color: rgba(255, 107, 107, 0.3);
    }

    /* ========================================
       FOOTER SPACE
       ======================================== */
    .footer-space {
        padding-bottom: var(--space-2xl);
    }
</style>
@endpush

@section('content')
<!-- Sticky CTA -->
<a href="/login?tab=register" class="sticky-cta hidden md:flex items-center gap-2 bg-[#ff6b6b] text-white px-5 py-3 rounded-lg font-semibold shadow-lg hover:bg-[#e85555] transition-colors">
    <i class="fas fa-building"></i>
    <span>Kurumsal Başvuru</span>
    <i class="fas fa-arrow-right"></i>
</a>

<!-- Contact Buttons -->
<div class="fixed bottom-6 right-6 z-50 flex flex-col gap-3">
    <a href="https://wa.me/905326564447?text=Merhaba,%20Muzibu%20kurumsal%20çözümler%20hakkında%20bilgi%20almak%20istiyorum" target="_blank" class="bg-green-600 hover:bg-green-700 text-white p-3.5 rounded-lg shadow-lg transition-colors">
        <i class="fab fa-whatsapp text-xl"></i>
    </a>
    <a href="tel:+905326564447" class="bg-zinc-800 hover:bg-zinc-700 text-white p-3.5 rounded-lg shadow-lg border border-white/10 transition-colors">
        <i class="fas fa-phone text-xl"></i>
    </a>
</div>

<!-- ========================================
     1. HERO
     ======================================== -->
<section class="hero-corporate">
    <div class="container mx-auto px-6">
        <div class="max-w-4xl">
            <!-- Label -->
            <p class="text-[#ff6b6b] font-semibold text-sm uppercase tracking-wider mb-4" data-aos="fade-up">
                Kurumsal Müzik Çözümleri
            </p>

            <!-- Heading -->
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white leading-tight mb-6" data-aos="fade-up" data-aos-delay="50">
                Türkiye'nin Lider<br>
                <span class="text-[#ff6b6b]">Telifsiz Müzik</span> Platformu
            </h1>

            <!-- Subheading -->
            <p class="text-xl text-gray-400 mb-8 max-w-2xl leading-relaxed" data-aos="fade-up" data-aos-delay="100">
                İşletmenizde yasal müzik çalın. Metrekare başı <strong class="text-white">150.000 TL'ye varan</strong> telif cezası riskinden korunun.
            </p>

            <!-- CTA Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 mb-12" data-aos="fade-up" data-aos-delay="150">
                <a href="/login?tab=register" class="inline-flex items-center justify-center gap-2 bg-[#ff6b6b] hover:bg-[#e85555] text-white px-8 py-4 rounded-lg font-semibold transition-colors">
                    <span>Ücretsiz Deneyin</span>
                    <i class="fas fa-arrow-right"></i>
                </a>
                <a href="#avantajlar" class="inline-flex items-center justify-center gap-2 bg-white/5 hover:bg-white/10 text-white px-8 py-4 rounded-lg font-semibold border border-white/10 transition-colors">
                    <span>Detaylı Bilgi</span>
                </a>
            </div>

            <!-- Trust Indicators -->
            <div class="flex flex-wrap items-center gap-6 pt-8 border-t border-white/10" data-aos="fade-up" data-aos-delay="200">
                <div class="flex items-center gap-2 text-sm text-gray-500">
                    <i class="fas fa-shield-check text-[#ff6b6b]"></i>
                    <span>%100 Yasal</span>
                </div>
                <div class="flex items-center gap-2 text-sm text-gray-500">
                    <i class="fas fa-music text-[#ff6b6b]"></i>
                    <span>5M+ Şarkı</span>
                </div>
                <div class="flex items-center gap-2 text-sm text-gray-500">
                    <i class="fas fa-headset text-[#ff6b6b]"></i>
                    <span>7/24 Destek</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ========================================
     2. STATS
     ======================================== -->
<section class="section-padding bg-zinc-950">
    <div class="container mx-auto px-6">
        <div class="stats-grid">
            <div class="stat-item" data-aos="fade-up" data-aos-delay="0">
                <div class="text-3xl md:text-4xl font-bold text-white mb-2">5+</div>
                <div class="text-gray-500 text-sm">Yıl Tecrübe</div>
            </div>
            <div class="stat-item" data-aos="fade-up" data-aos-delay="50">
                <div class="text-3xl md:text-4xl font-bold text-white mb-2">8+</div>
                <div class="text-gray-500 text-sm">Aktif Ülke</div>
            </div>
            <div class="stat-item" data-aos="fade-up" data-aos-delay="100">
                <div class="text-3xl md:text-4xl font-bold text-white mb-2">20+</div>
                <div class="text-gray-500 text-sm">Desteklenen Sektör</div>
            </div>
            <div class="stat-item" data-aos="fade-up" data-aos-delay="150">
                <div class="text-3xl md:text-4xl font-bold text-[#ff6b6b] mb-2">5M+</div>
                <div class="text-gray-500 text-sm">Müzik Kütüphanesi</div>
            </div>
        </div>
    </div>
</section>

<!-- ========================================
     3. REFERANS LOGOLARI
     ======================================== -->
<section class="py-12 bg-zinc-950 border-y border-white/5">
    <div class="container mx-auto px-6">
        <p class="text-center text-gray-600 text-xs uppercase tracking-wider mb-8" data-aos="fade-up">
            Güvenilir Markalar Tarafından Tercih Ediliyor
        </p>
        <div class="grid grid-cols-3 md:grid-cols-6 gap-6">
            @for($i = 1; $i <= 6; $i++)
            <div class="text-center opacity-40 hover:opacity-60 transition-opacity" data-aos="fade-up" data-aos-delay="{{ $i * 30 }}">
                <div class="text-sm font-semibold text-white/50">LOGO</div>
            </div>
            @endfor
        </div>
    </div>
</section>

<!-- ========================================
     4. AVANTAJLAR
     ======================================== -->
<section id="avantajlar" class="section-padding bg-zinc-900">
    <div class="container mx-auto px-6">
        <!-- Section Header -->
        <div class="mb-12" data-aos="fade-up">
            <p class="text-[#ff6b6b] font-semibold text-sm uppercase tracking-wider mb-3">Neden Muzibu?</p>
            <h2 class="text-3xl md:text-4xl font-bold text-white">
                Kurumsal Müzik Yönetiminde Lider Çözüm
            </h2>
        </div>

        <!-- Cards Grid -->
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="card-corporate" data-aos="fade-up" data-aos-delay="0">
                <div class="w-12 h-12 bg-[#ff6b6b]/10 rounded-lg flex items-center justify-center mb-5">
                    <i class="fas fa-shield-check text-[#ff6b6b] text-lg"></i>
                </div>
                <h3 class="text-base font-semibold text-white mb-3">%100 Yasal Güvence</h3>
                <p class="text-gray-500 text-sm leading-relaxed">
                    MESAM/Mü-Yap ödeme zorunluluğu olmadan, tek abonelik ile tam yasal koruma.
                </p>
            </div>

            <div class="card-corporate" data-aos="fade-up" data-aos-delay="50">
                <div class="w-12 h-12 bg-[#ff6b6b]/10 rounded-lg flex items-center justify-center mb-5">
                    <i class="fas fa-music text-[#ff6b6b] text-lg"></i>
                </div>
                <h3 class="text-base font-semibold text-white mb-3">Geniş Kütüphane</h3>
                <p class="text-gray-500 text-sm leading-relaxed">
                    Her sektöre ve atmosfere uygun, profesyonelce hazırlanmış binlerce çalma listesi.
                </p>
            </div>

            <div class="card-corporate" data-aos="fade-up" data-aos-delay="100">
                <div class="w-12 h-12 bg-[#ff6b6b]/10 rounded-lg flex items-center justify-center mb-5">
                    <i class="fas fa-laptop text-[#ff6b6b] text-lg"></i>
                </div>
                <h3 class="text-base font-semibold text-white mb-3">Kolay Entegrasyon</h3>
                <p class="text-gray-500 text-sm leading-relaxed">
                    Mobil, masaüstü veya web üzerinden anında yayın. Özel donanım gerektirmez.
                </p>
            </div>

            <div class="card-corporate" data-aos="fade-up" data-aos-delay="150">
                <div class="w-12 h-12 bg-[#ff6b6b]/10 rounded-lg flex items-center justify-center mb-5">
                    <i class="fas fa-signal text-[#ff6b6b] text-lg"></i>
                </div>
                <h3 class="text-base font-semibold text-white mb-3">Kesintisiz Yayın</h3>
                <p class="text-gray-500 text-sm leading-relaxed">
                    Optimize edilmiş altyapı ile 7/24 kesintisiz, reklamsız müzik akışı.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- ========================================
     5. REFERANS VIDEOLARI
     ======================================== -->
<section class="section-padding bg-black">
    <div class="container mx-auto px-6">
        <!-- Section Header -->
        <div class="mb-12" data-aos="fade-up">
            <p class="text-[#ff6b6b] font-semibold text-sm uppercase tracking-wider mb-3">Müşteri Referansları</p>
            <h2 class="text-3xl md:text-4xl font-bold text-white">
                İşletme Sahiplerinin Deneyimleri
            </h2>
        </div>

        <!-- Video Grid -->
        <div class="grid md:grid-cols-3 gap-6">
            @for($i = 1; $i <= 3; $i++)
            <div class="video-card" data-aos="fade-up" data-aos-delay="{{ ($i - 1) * 50 }}">
                <div class="aspect-video bg-zinc-900 flex items-center justify-center cursor-pointer group">
                    <div class="w-14 h-14 bg-[#ff6b6b] rounded-full flex items-center justify-center group-hover:bg-[#e85555] transition-colors">
                        <i class="fas fa-play text-white ml-1"></i>
                    </div>
                </div>
                <div class="p-5">
                    <h3 class="font-semibold text-white text-sm mb-1">Kurumsal Referans {{ $i }}</h3>
                    <p class="text-xs text-gray-500">İşletme Yöneticisi</p>
                </div>
            </div>
            @endfor
        </div>
    </div>
</section>

<!-- ========================================
     6. KARŞILAŞTIRMA
     ======================================== -->
<section class="section-padding bg-zinc-900">
    <div class="container mx-auto px-6">
        <!-- Section Header -->
        <div class="text-center mb-12" data-aos="fade-up">
            <p class="text-[#ff6b6b] font-semibold text-sm uppercase tracking-wider mb-3">Karşılaştırma</p>
            <h2 class="text-3xl md:text-4xl font-bold text-white">
                Muzibu ile Diğer Platformlar
            </h2>
        </div>

        <!-- Table -->
        <div class="max-w-3xl mx-auto">
            <div class="bg-zinc-800/50 rounded-xl overflow-hidden border border-white/5" data-aos="fade-up" data-aos-delay="50">
                <table class="comparison-table">
                    <thead>
                        <tr class="text-gray-400">
                            <th>Kriter</th>
                            <th class="text-[#ff6b6b]">Muzibu</th>
                            <th>Diğer Platformlar</th>
                        </tr>
                    </thead>
                    <tbody class="text-white">
                        <tr>
                            <td class="font-medium">Ticari Kullanım Lisansı</td>
                            <td>
                                <i class="fas fa-check text-green-500"></i>
                                <span class="text-xs text-gray-500 block mt-1">Dahil</span>
                            </td>
                            <td>
                                <i class="fas fa-times text-red-500"></i>
                                <span class="text-xs text-gray-500 block mt-1">Yok</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="font-medium">Telif Cezası Riski</td>
                            <td><span class="text-green-500 font-semibold">Sıfır</span></td>
                            <td><span class="text-red-500 font-semibold">150.000 TL'ye kadar</span></td>
                        </tr>
                        <tr>
                            <td class="font-medium">Reklam</td>
                            <td>
                                <i class="fas fa-check text-green-500"></i>
                                <span class="text-xs text-gray-500 block mt-1">Reklamsız</span>
                            </td>
                            <td>
                                <i class="fas fa-times text-red-500"></i>
                                <span class="text-xs text-gray-500 block mt-1">Reklamlı</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="font-medium">Sektöre Özel Listeler</td>
                            <td>
                                <i class="fas fa-check text-green-500"></i>
                                <span class="text-xs text-gray-500 block mt-1">Mevcut</span>
                            </td>
                            <td>
                                <i class="fas fa-times text-red-500"></i>
                                <span class="text-xs text-gray-500 block mt-1">Kişisel kullanım</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<!-- ========================================
     7. SSS
     ======================================== -->
<section class="section-padding bg-black">
    <div class="container mx-auto px-6">
        <div class="grid lg:grid-cols-2 gap-12">
            <!-- Left: Header -->
            <div data-aos="fade-up">
                <p class="text-[#ff6b6b] font-semibold text-sm uppercase tracking-wider mb-3">Sıkça Sorulan Sorular</p>
                <h2 class="text-3xl md:text-4xl font-bold text-white mb-6">
                    Merak Edilenler
                </h2>
                <p class="text-gray-400 mb-8 leading-relaxed">
                    Kurumsal müzik çözümlerimiz hakkında en çok sorulan soruların yanıtları.
                </p>
                <a href="/login?tab=register" class="inline-flex items-center gap-2 text-[#ff6b6b] font-semibold hover:text-[#ff8a8a] transition-colors">
                    <span>Daha fazla soru için iletişime geçin</span>
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>

            <!-- Right: FAQ -->
            <div class="space-y-4" data-aos="fade-up" data-aos-delay="100">
                @php
                $faqs = [
                    ['q' => 'Muzibu\'daki müzikler gerçekten telifsiz mi?', 'a' => 'Evet. Tüm müzikler ticari kullanım için özel olarak lisanslanmıştır. MESAM, Mü-Yap gibi kurumlara ek ücret ödemenize gerek yoktur.'],
                    ['q' => 'Hangi sektörlere hizmet veriyorsunuz?', 'a' => 'Restoran, cafe, mağaza, spor salonu, otel, kuaför, ofis ve müzik yayını yapan tüm ticari işletmeler için çözümler sunuyoruz.'],
                    ['q' => 'Teknik destek sağlıyor musunuz?', 'a' => 'Evet. 7/24 teknik destek ekibimiz kurulum ve kullanım süreçlerinde yanınızda.'],
                    ['q' => 'Çoklu şube yönetimi mümkün mü?', 'a' => 'Her lokasyon için ayrı üyelik gerekmektedir. Kurumsal paketlerimiz hakkında bilgi alabilirsiniz.'],
                    ['q' => 'Ücretsiz deneme var mı?', 'a' => 'Evet. Hemen üye olarak platformumuzu ücretsiz deneyebilirsiniz.']
                ];
                @endphp

                @foreach($faqs as $index => $faq)
                <div class="faq-item" data-aos="fade-up" data-aos-delay="{{ ($index + 1) * 30 }}">
                    <button class="w-full text-left font-medium flex justify-between items-center text-white hover:text-[#ff6b6b] transition-colors" onclick="toggleAccordion(this)">
                        <span>{{ $faq['q'] }}</span>
                        <i class="fas fa-chevron-down text-gray-500 text-xs transition-transform"></i>
                    </button>
                    <div class="hidden faq-answer text-gray-400 text-sm leading-relaxed">
                        {{ $faq['a'] }}
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

<!-- ========================================
     8. FINAL CTA
     ======================================== -->
<section class="section-padding bg-[#ff6b6b] footer-space">
    <div class="container mx-auto px-6">
        <div class="max-w-2xl mx-auto text-center">
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-4" data-aos="fade-up">
                Kurumsal Müzik Çözümünüze Bugün Başlayın
            </h2>
            <p class="text-lg text-white/80 mb-8" data-aos="fade-up" data-aos-delay="50">
                İşletmenizi telif riskinden koruyun, profesyonel müzik deneyimi sunun.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center" data-aos="fade-up" data-aos-delay="100">
                <a href="/login?tab=register" class="inline-flex items-center justify-center gap-2 bg-white text-[#ff6b6b] px-8 py-4 rounded-lg font-semibold hover:bg-gray-100 transition-colors">
                    <span>Ücretsiz Deneyin</span>
                    <i class="fas fa-arrow-right"></i>
                </a>
                <a href="tel:+905326564447" class="inline-flex items-center justify-center gap-2 bg-white/10 text-white px-8 py-4 rounded-lg font-semibold border border-white/20 hover:bg-white/20 transition-colors">
                    <i class="fas fa-phone"></i>
                    <span>Bizi Arayın</span>
                </a>
            </div>
        </div>
    </div>
</section>
@endsection

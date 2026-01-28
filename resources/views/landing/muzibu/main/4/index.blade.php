@extends('layouts.landing.muzibu')

@push('meta')
    <title>Müziğin Keyfini Çıkarın, Telif Derdini Unutun! | Muzibu</title>
    <meta name="description" content="🎵 %100 Yasal Güvence 🎵 150.000 TL telif cezasından kurtulun 🎵 5 Milyon+ şarkı | Muzibu.com">
    <meta property="og:title" content="Müziğin Keyfini Çıkarın! | Muzibu">
    <meta property="og:url" content="{{ url('/landing4') }}">
    <link rel="canonical" href="{{ url('/landing4') }}">
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
       HERO - Clean Playful
       ======================================== */
    .hero-playful {
        background: var(--coral);
        padding: var(--space-3xl) 0;
    }

    @media (min-width: 1024px) {
        .hero-playful {
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
       CARDS - Unified Style
       ======================================== */
    .card-playful {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 1.5rem;
        padding: var(--space-lg);
        transition: border-color 0.3s ease, background 0.3s ease;
    }

    .card-playful:hover {
        border-color: rgba(255, 107, 107, 0.4);
        background: rgba(255, 255, 255, 0.05);
    }

    /* ========================================
       STATS - Bubble Style
       ======================================== */
    .stat-bubble {
        background: white;
        border-radius: 1.5rem;
        padding: var(--space-lg);
        text-align: center;
    }

    /* ========================================
       FAQ - Clean Accordion
       ======================================== */
    .faq-item {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 1rem;
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

    .comparison-table th {
        background: var(--coral);
        font-weight: 600;
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
       FOOTER SPACE
       ======================================== */
    .footer-space {
        padding-bottom: var(--space-2xl);
    }
</style>
@endpush

@section('content')
<!-- Sticky CTA -->
<a href="/login?tab=register" class="sticky-cta hidden md:flex items-center gap-2 bg-white text-[#ff6b6b] px-5 py-3 rounded-full font-bold shadow-xl hover:shadow-2xl transition-shadow">
    <i class="fas fa-rocket"></i>
    <span>Hadi Başlayalım!</span>
</a>

<!-- Contact Buttons -->
<div class="fixed bottom-6 right-6 z-50 flex flex-col gap-3">
    <a href="https://wa.me/905326564447?text=Merhaba,%20Muzibu%20hakkında%20bilgi%20almak%20istiyorum" target="_blank" class="bg-green-500 hover:bg-green-600 text-white p-4 rounded-full shadow-lg transition-colors">
        <i class="fab fa-whatsapp text-2xl"></i>
    </a>
    <a href="tel:+905326564447" class="bg-[#ff6b6b] hover:bg-[#e85555] text-white p-4 rounded-full shadow-lg transition-colors">
        <i class="fas fa-phone text-2xl"></i>
    </a>
</div>

<!-- ========================================
     1. HERO
     ======================================== -->
<section class="hero-playful">
    <div class="container mx-auto px-6">
        <div class="max-w-3xl mx-auto text-center">
            <!-- Badge -->
            <div class="inline-flex items-center gap-2 bg-white/20 px-4 py-2 rounded-full text-sm font-semibold mb-6" data-aos="fade-down">
                <span>🎵</span>
                <span>Türkiye'nin #1 Müzik Platformu</span>
            </div>

            <!-- Heading -->
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-black text-white leading-tight mb-6" data-aos="fade-up">
                Müziğin Keyfini Çıkarın,<br>
                Telif Derdini Unutun!
            </h1>

            <!-- Subheading -->
            <p class="text-xl md:text-2xl text-white/90 mb-8 max-w-2xl mx-auto" data-aos="fade-up" data-aos-delay="100">
                İşletmenizde <strong>150.000 TL'ye varan</strong> telif cezası riski var.<br>
                Muzibu ile bu dertten kurtulun!
            </p>

            <!-- CTA Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center" data-aos="fade-up" data-aos-delay="150">
                <a href="/login?tab=register" class="inline-flex items-center justify-center gap-2 bg-white text-[#ff6b6b] px-8 py-4 rounded-full font-bold text-lg shadow-lg hover:shadow-xl transition-shadow">
                    <i class="fas fa-rocket"></i>
                    <span>Ücretsiz Dene!</span>
                </a>
                <a href="#neden-muzibu" class="inline-flex items-center justify-center gap-2 bg-white/10 text-white px-8 py-4 rounded-full font-semibold text-lg border-2 border-white/30 hover:bg-white/20 transition-colors">
                    <i class="fas fa-arrow-down"></i>
                    <span>Keşfet</span>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- ========================================
     2. STATS
     ======================================== -->
<section class="section-padding bg-zinc-900">
    <div class="container mx-auto px-6">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
            <div class="stat-bubble" data-aos="fade-up" data-aos-delay="0">
                <div class="text-3xl md:text-4xl font-black text-[#ff6b6b]">5+</div>
                <div class="text-gray-600 font-medium mt-2 text-sm">Yıl Tecrübe</div>
            </div>
            <div class="stat-bubble" data-aos="fade-up" data-aos-delay="50">
                <div class="text-3xl md:text-4xl font-black text-[#ff6b6b]">8+</div>
                <div class="text-gray-600 font-medium mt-2 text-sm">Ülke</div>
            </div>
            <div class="stat-bubble" data-aos="fade-up" data-aos-delay="100">
                <div class="text-3xl md:text-4xl font-black text-[#ff6b6b]">20+</div>
                <div class="text-gray-600 font-medium mt-2 text-sm">Sektör</div>
            </div>
            <div class="stat-bubble" data-aos="fade-up" data-aos-delay="150">
                <div class="text-3xl md:text-4xl font-black text-[#ff6b6b]">5M+</div>
                <div class="text-gray-600 font-medium mt-2 text-sm">Şarkı</div>
            </div>
        </div>
    </div>
</section>

<!-- ========================================
     3. REFERANS LOGOLARI
     ======================================== -->
<section class="py-12 bg-zinc-900 border-y border-white/5">
    <div class="container mx-auto px-6">
        <p class="text-center text-gray-500 text-sm font-medium mb-8" data-aos="fade-up">
            <i class="fas fa-heart text-[#ff6b6b] mr-2"></i>Bize Güvenen Markalar
        </p>
        <div class="grid grid-cols-3 md:grid-cols-6 gap-4 md:gap-6">
            @for($i = 1; $i <= 6; $i++)
            <div class="bg-white/5 rounded-xl p-4 text-center" data-aos="fade-up" data-aos-delay="{{ $i * 30 }}">
                <div class="text-lg font-bold text-white/20">LOGO</div>
            </div>
            @endfor
        </div>
    </div>
</section>

<!-- ========================================
     4. NEDEN MUZIBU
     ======================================== -->
<section id="neden-muzibu" class="section-padding bg-zinc-900">
    <div class="container mx-auto px-6">
        <!-- Section Header -->
        <div class="text-center mb-12" data-aos="fade-up">
            <span class="inline-flex items-center gap-2 bg-[#ff6b6b]/10 text-[#ff6b6b] px-4 py-2 rounded-full font-semibold text-sm mb-4">
                <i class="fas fa-sparkles"></i>
                <span>Avantajlar</span>
            </span>
            <h2 class="text-3xl md:text-4xl font-black text-white">Neden Muzibu?</h2>
        </div>

        <!-- Cards Grid -->
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="card-playful" data-aos="fade-up" data-aos-delay="0">
                <div class="w-14 h-14 bg-[#ff6b6b] rounded-xl flex items-center justify-center mb-5">
                    <i class="fas fa-shield-check text-white text-xl"></i>
                </div>
                <h3 class="text-lg font-bold text-white mb-3">%100 Yasal Güvence</h3>
                <p class="text-gray-400 text-sm leading-relaxed">
                    MESAM/Mü-Yap derdi yok! Tek abonelik, tam koruma.
                </p>
            </div>

            <div class="card-playful" data-aos="fade-up" data-aos-delay="50">
                <div class="w-14 h-14 bg-[#ff6b6b] rounded-xl flex items-center justify-center mb-5">
                    <i class="fas fa-music text-white text-xl"></i>
                </div>
                <h3 class="text-lg font-bold text-white mb-3">5 Milyon+ Şarkı</h3>
                <p class="text-gray-400 text-sm leading-relaxed">
                    Her tarza, her sektöre uygun devasa kütüphane.
                </p>
            </div>

            <div class="card-playful" data-aos="fade-up" data-aos-delay="100">
                <div class="w-14 h-14 bg-[#ff6b6b] rounded-xl flex items-center justify-center mb-5">
                    <i class="fas fa-mobile-screen text-white text-xl"></i>
                </div>
                <h3 class="text-lg font-bold text-white mb-3">Süper Kolay</h3>
                <p class="text-gray-400 text-sm leading-relaxed">
                    Telefon, tablet, bilgisayar... Hepsiyle çalışır!
                </p>
            </div>

            <div class="card-playful" data-aos="fade-up" data-aos-delay="150">
                <div class="w-14 h-14 bg-[#ff6b6b] rounded-xl flex items-center justify-center mb-5">
                    <i class="fas fa-bolt text-white text-xl"></i>
                </div>
                <h3 class="text-lg font-bold text-white mb-3">Kesintisiz Yayın</h3>
                <p class="text-gray-400 text-sm leading-relaxed">
                    7/24 müzik, sıfır kesinti, sıfır reklam.
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
        <div class="text-center mb-12" data-aos="fade-up">
            <span class="inline-flex items-center gap-2 bg-[#ff6b6b]/10 text-[#ff6b6b] px-4 py-2 rounded-full font-semibold text-sm mb-4">
                <i class="fas fa-video"></i>
                <span>Müşteri Hikayeleri</span>
            </span>
            <h2 class="text-3xl md:text-4xl font-black text-white">İşletme Sahipleri Anlatıyor</h2>
        </div>

        <!-- Video Grid -->
        <div class="grid md:grid-cols-3 gap-6">
            @for($i = 1; $i <= 3; $i++)
            <div class="card-playful p-0 overflow-hidden" data-aos="fade-up" data-aos-delay="{{ ($i - 1) * 50 }}">
                <div class="aspect-video bg-zinc-800 flex items-center justify-center cursor-pointer group">
                    <div class="w-16 h-16 bg-[#ff6b6b] rounded-full flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i class="fas fa-play text-white text-lg ml-1"></i>
                    </div>
                </div>
                <div class="p-5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-[#ff6b6b]/20 rounded-full flex items-center justify-center">
                            <i class="fas fa-user text-[#ff6b6b] text-sm"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-white text-sm">Mutlu Müşteri {{ $i }}</h3>
                            <p class="text-xs text-gray-500">İşletme Sahibi</p>
                        </div>
                    </div>
                </div>
            </div>
            @endfor
        </div>
    </div>
</section>

<!-- ========================================
     6. SSS
     ======================================== -->
<section class="section-padding bg-zinc-900">
    <div class="container mx-auto px-6 max-w-3xl">
        <!-- Section Header -->
        <div class="text-center mb-12" data-aos="fade-up">
            <span class="inline-flex items-center gap-2 bg-[#ff6b6b]/10 text-[#ff6b6b] px-4 py-2 rounded-full font-semibold text-sm mb-4">
                <i class="fas fa-circle-question"></i>
                <span>Merak Edilenler</span>
            </span>
            <h2 class="text-3xl md:text-4xl font-black text-white">Sıkça Sorulan Sorular</h2>
        </div>

        <!-- FAQ List -->
        <div class="space-y-4">
            @php
            $faqs = [
                ['q' => 'Muzibu\'daki müzikler gerçekten telifsiz mi?', 'a' => 'Evet! Tüm müzikler ticari kullanım için özel lisanslı. MESAM, Mü-Yap gibi kurumlara ek ödeme yok.'],
                ['q' => 'Hangi işletmeler kullanabilir?', 'a' => 'Restoran, cafe, mağaza, spor salonu, otel, kuaför, ofis... Müzik çalan her işletme!'],
                ['q' => 'Reklam var mı?', 'a' => 'Kesinlikle hayır! %100 kesintisiz, reklamsız yayın.'],
                ['q' => 'Ücretsiz deneyebilir miyim?', 'a' => 'Evet! Hemen üye ol, ücretsiz dene!'],
                ['q' => 'Birden fazla şubem varsa?', 'a' => 'Her lokasyon için ayrı üyelik gerekiyor.']
            ];
            @endphp

            @foreach($faqs as $index => $faq)
            <div class="faq-item" data-aos="fade-up" data-aos-delay="{{ $index * 30 }}">
                <button class="w-full text-left font-semibold flex justify-between items-center text-white" onclick="toggleAccordion(this)">
                    <span>{{ $faq['q'] }}</span>
                    <i class="fas fa-plus text-[#ff6b6b] text-sm transition-transform"></i>
                </button>
                <div class="hidden faq-answer text-gray-400 text-sm leading-relaxed">
                    {{ $faq['a'] }}
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- ========================================
     7. KARŞILAŞTIRMA
     ======================================== -->
<section class="section-padding bg-black">
    <div class="container mx-auto px-6">
        <!-- Section Header -->
        <div class="text-center mb-12" data-aos="fade-up">
            <span class="inline-flex items-center gap-2 bg-[#ff6b6b]/10 text-[#ff6b6b] px-4 py-2 rounded-full font-semibold text-sm mb-4">
                <i class="fas fa-scale-balanced"></i>
                <span>Karşılaştırma</span>
            </span>
            <h2 class="text-3xl md:text-4xl font-black text-white">Muzibu vs Diğerleri</h2>
        </div>

        <!-- Table -->
        <div class="max-w-3xl mx-auto">
            <div class="bg-zinc-900 rounded-2xl overflow-hidden border border-white/10" data-aos="fade-up" data-aos-delay="50">
                <table class="comparison-table">
                    <thead>
                        <tr class="text-white">
                            <th>Özellik</th>
                            <th>Muzibu</th>
                            <th>Spotify & Co.</th>
                        </tr>
                    </thead>
                    <tbody class="text-white">
                        <tr>
                            <td class="font-medium">Ticari Kullanım</td>
                            <td><span class="text-green-400 text-xl">✓</span></td>
                            <td><span class="text-red-400 text-xl">✗</span></td>
                        </tr>
                        <tr>
                            <td class="font-medium">Telif Riski</td>
                            <td><span class="text-green-400 font-semibold">Sıfır</span></td>
                            <td><span class="text-red-400 font-semibold">150K TL!</span></td>
                        </tr>
                        <tr>
                            <td class="font-medium">Reklam</td>
                            <td><span class="text-green-400">Yok</span></td>
                            <td><span class="text-red-400">Var</span></td>
                        </tr>
                        <tr>
                            <td class="font-medium">İşletmeye Özel</td>
                            <td><span class="text-green-400 text-xl">✓</span></td>
                            <td><span class="text-red-400 text-xl">✗</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<!-- ========================================
     8. FINAL CTA
     ======================================== -->
<section class="section-padding bg-[#ff6b6b] footer-space">
    <div class="container mx-auto px-6 text-center">
        <h2 class="text-3xl md:text-4xl font-black text-white mb-4" data-aos="fade-up">
            Hazır mısın?
        </h2>
        <p class="text-lg text-white/90 mb-8 max-w-xl mx-auto" data-aos="fade-up" data-aos-delay="50">
            Telif derdi olmadan, kesintisiz müzik yayını yapmanın keyfini çıkar!
        </p>
        <a href="/login?tab=register" class="inline-flex items-center gap-2 bg-white text-[#ff6b6b] px-8 py-4 rounded-full font-bold text-lg shadow-lg hover:shadow-xl transition-shadow" data-aos="fade-up" data-aos-delay="100">
            <i class="fas fa-rocket"></i>
            <span>Ücretsiz Başla!</span>
        </a>
    </div>
</section>
@endsection

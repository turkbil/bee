@extends('layouts.landing.muzibu-minimal')

@push('meta')
    <!-- PRIMARY SEO -->
    <title>Türkiye'nin İlk Kurumsal Telifsiz Müzik Platformu | Muzibu</title>
    <meta name="description" content="✓ %100 Yasal Güvence ✓ 150.000 TL'ye varan telif cezalarından korunun ✓ Profesyonel müzik kütüphanesi ✓ Kesintisiz yayın ✓ Tüm sektörler için | Muzibu.com">
    <meta name="keywords" content="muzibu, telifsiz müzik, işletme müzik lisansı, telif hakları, yasal müzik platformu, ticari müzik, restoran müziği, cafe müziği, mağaza müziği, telif cezası, MESAM, Mü-Yap, kurumsal müzik">

    <!-- Open Graph -->
    <meta property="og:title" content="Türkiye'nin İlk Kurumsal Telifsiz Müzik Platformu | Muzibu">
    <meta property="og:description" content="Yasal Güvence ile telif riskini sıfırlayın! İşletmenizde güvenle müzik çalın, telif cezalarından korunun.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/landing3') }}">

    <!-- Canonical -->
    <link rel="canonical" href="{{ url('/landing3') }}">
@endpush

@push('styles')
<style>
    /* Modern Gradient Background */
    .hero-modern {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
        position: relative;
        overflow: hidden;
    }

    .hero-modern::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        animation: pulse 15s ease-in-out infinite;
    }

    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.1); }
    }

    /* Glassmorphism Cards */
    .glass-card {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    /* Sticky CTA - Modern */
    .sticky-cta-modern {
        position: fixed;
        bottom: 30px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 50;
        box-shadow: 0 10px 40px rgba(102, 126, 234, 0.4);
        transition: all 0.3s ease;
    }

    .sticky-cta-modern:hover {
        transform: translateX(-50%) scale(1.05);
    }

    @media (max-width: 768px) {
        .sticky-cta-modern {
            bottom: 20px;
            left: 20px;
            right: 20px;
            transform: none;
        }

        .sticky-cta-modern:hover {
            transform: scale(1.05);
        }
    }

    /* Gradient text - kontrast renkler */
    .highlight-text {
        background: linear-gradient(90deg, #FFD700, #FFA500, #FF6B00);
        background-size: 200% auto;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        font-weight: 900;
        animation: gradient-shift 3s ease infinite;
    }

    @keyframes gradient-shift {
        0%, 100% { background-position: 0% center; }
        50% { background-position: 100% center; }
    }

    /* Modern comparison table */
    .comparison-modern th,
    .comparison-modern td {
        padding: 1.5rem;
        text-align: center;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }

    .comparison-modern th {
        background: rgba(102, 126, 234, 0.1);
        font-weight: 700;
    }
</style>
@endpush

@section('content')
<!-- Sticky Modern CTA -->
<a href="/login?tab=register"
   class="sticky-cta-modern hidden md:flex items-center gap-4 bg-white text-purple-600 px-5 py-5 rounded-full font-bold text-lg hover:shadow-3xl shadow-2xl whitespace-nowrap justify-center">
    <i class="fas fa-shield-check text-xl"></i>
    <span class="px-2">Yasal Güvence Altına Alın</span>
</a>

<!-- WhatsApp ve Telefon Butonları -->
<div class="fixed bottom-6 right-6 z-50 flex flex-col gap-3">
    <a href="https://wa.me/905326564447?text=Merhaba,%20Muzibu%20hakkında%20bilgi%20almak%20istiyorum"
       target="_blank"
       class="bg-green-500 hover:bg-green-600 text-white p-4 rounded-full shadow-lg transition-all duration-300 hover:scale-110"
       title="WhatsApp ile iletişime geç">
        <i class="fab fa-whatsapp text-2xl"></i>
    </a>
    <a href="tel:+905326564447"
       class="bg-blue-600 hover:bg-blue-700 text-white p-4 rounded-full shadow-lg transition-all duration-300 hover:scale-110"
       title="Telefon ile ara">
        <i class="fas fa-phone text-2xl"></i>
    </a>
</div>

<!-- 1. HERO SECTION - Modern -->
<section class="hero-modern min-h-screen flex items-center relative">
    <div class="container mx-auto px-6 py-20 relative z-10">
        <div class="max-w-4xl mx-auto text-center">
            <div class="inline-block px-4 py-2 bg-white/20 rounded-full text-sm font-semibold mb-6" data-aos="fade-down">
                🎵 Türkiye'nin #1 Kurumsal Müzik Platformu
            </div>

            <h1 class="text-5xl lg:text-7xl font-black mb-6 leading-tight" data-aos="fade-up">
                Telif <span class="highlight-text">Cezalarından</span> Kurtulun!
            </h1>

            <p class="text-2xl lg:text-3xl font-semibold mb-4 text-white/90" data-aos="fade-up" data-aos-delay="100">
                İşletmenizde Yasal Müzik Çalın
            </p>

            <p class="text-xl text-white/80 mb-12 max-w-2xl mx-auto" data-aos="fade-up" data-aos-delay="200">
                Metrekare başı 150.000 TL'ye kadar ulaşan telif cezası riskinden Muzibu ile korunun. %100 yasal güvence.
            </p>

            <div class="flex flex-col sm:flex-row gap-6 justify-center" data-aos="fade-up" data-aos-delay="300">
                <a href="/login?tab=register"
                   class="bg-white text-purple-600 px-12 py-6 rounded-full font-bold text-xl transition-all duration-300 hover:scale-105 hover:shadow-3xl shadow-2xl inline-flex items-center justify-center gap-3 w-full sm:w-[380px]">
                    <i class="fas fa-rocket text-2xl"></i>
                    <span>Ücretsiz Başlayın</span>
                </a>
                <a href="#ozellikler"
                   class="border-2 border-white text-white px-12 py-6 rounded-full font-semibold text-xl transition-all duration-300 hover:scale-105 hover:shadow-2xl inline-flex items-center justify-center gap-3 w-full sm:w-[380px]">
                    <i class="fas fa-compass text-2xl"></i>
                    <span>Özellikleri Keşfedin</span>
                </a>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mt-20" data-aos="fade-up" data-aos-delay="400">
                <div class="glass-card p-6 rounded-2xl">
                    <div class="text-4xl font-bold mb-2">5+</div>
                    <div class="text-sm text-white/70">Yıl Tecrübe</div>
                </div>
                <div class="glass-card p-6 rounded-2xl">
                    <div class="text-4xl font-bold mb-2">5M+</div>
                    <div class="text-sm text-white/70">Şarkı</div>
                </div>
                <div class="glass-card p-6 rounded-2xl">
                    <div class="text-4xl font-bold mb-2">20+</div>
                    <div class="text-sm text-white/70">Sektör</div>
                </div>
                <div class="glass-card p-6 rounded-2xl">
                    <div class="text-4xl font-bold mb-2">%100</div>
                    <div class="text-sm text-white/70">Yasal</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- MAIN CONTENT WRAPPER - Tek Gradient Arkaplan -->
<div class="bg-gradient-to-b from-gray-900 via-black to-gray-900">

<!-- 2. REFERANS LOGOLARI -->
<section class="py-12">
    <div class="container mx-auto px-6">
        <h2 class="text-center text-gray-400 font-semibold mb-8" data-aos="fade-up">
            Bize Güvenen Markalar
        </h2>
        <div class="grid grid-cols-2 md:grid-cols-6 gap-8 items-center opacity-50 grayscale hover:grayscale-0 transition-all duration-300">
            @for($i = 1; $i <= 6; $i++)
            <div class="text-center glass-card p-6 rounded-xl" data-aos="fade-up" data-aos-delay="{{ $i * 100 }}">
                <div class="text-3xl font-bold text-white/30">LOGO {{ $i }}</div>
            </div>
            @endfor
        </div>
    </div>
</section>

<!-- 3. ÖZELLİKLER -->
<section id="ozellikler" class="pt-16">
    <div class="container mx-auto px-6">
        <div class="text-center mb-12" data-aos="fade-up">
            <h2 class="text-5xl font-bold mb-6">Neden Muzibu?</h2>
            <p class="text-xl text-gray-400 max-w-2xl mx-auto">
                İşletmeniz için en güvenli ve profesyonel müzik çözümü
            </p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="glass-card p-8 rounded-3xl hover:transform hover:scale-105 transition-all duration-300" data-aos="fade-up" data-aos-delay="100">
                <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-pink-500 rounded-2xl flex items-center justify-center mb-6">
                    <i class="fas fa-shield-check text-3xl"></i>
                </div>
                <h3 class="text-2xl font-bold mb-4">%100 Yasal Güvence</h3>
                <p class="text-gray-400">
                    MESAM/Mü-Yap ödeme zorunluluğunuz sona erer. Tek abonelik, tam yasal koruma.
                </p>
            </div>

            <div class="glass-card p-8 rounded-3xl hover:transform hover:scale-105 transition-all duration-300" data-aos="fade-up" data-aos-delay="200">
                <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-2xl flex items-center justify-center mb-6">
                    <i class="fas fa-music text-3xl"></i>
                </div>
                <h3 class="text-2xl font-bold mb-4">Sınırsız Müzik</h3>
                <p class="text-gray-400">
                    Her tarza ve sektöre uygun, profesyonel binlerce çalma listesi.
                </p>
            </div>

            <div class="glass-card p-8 rounded-3xl hover:transform hover:scale-105 transition-all duration-300" data-aos="fade-up" data-aos-delay="300">
                <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-emerald-500 rounded-2xl flex items-center justify-center mb-6">
                    <i class="fas fa-laptop text-3xl"></i>
                </div>
                <h3 class="text-2xl font-bold mb-4">Kolay Kullanım</h3>
                <p class="text-gray-400">
                    Karmaşık donanım gerektirmez. Mobil, masaüstü veya tarayıcıdan anında başlayın.
                </p>
            </div>

            <div class="glass-card p-8 rounded-3xl hover:transform hover:scale-105 transition-all duration-300" data-aos="fade-up" data-aos-delay="400">
                <div class="w-16 h-16 bg-gradient-to-br from-orange-500 to-red-500 rounded-2xl flex items-center justify-center mb-6">
                    <i class="fas fa-signal text-3xl"></i>
                </div>
                <h3 class="text-2xl font-bold mb-4">Kesintisiz Akış</h3>
                <p class="text-gray-400">
                    En yoğun saatlerde bile müzik asla durmaز. Optimize edilmiş altyapı.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- 4. KARŞILAŞTIRMA -->
<section class="pt-16">
    <div class="container mx-auto px-6">
        <div class="text-center mb-12" data-aos="fade-up">
            <h2 class="text-5xl font-bold mb-6">Muzibu vs Diğerleri</h2>
            <p class="text-xl text-gray-400">Neden popüler müzik platformları yerine Muzibu?</p>
        </div>

        <div class="max-w-5xl mx-auto glass-card rounded-3xl overflow-hidden" data-aos="fade-up" data-aos-delay="200">
            <table class="comparison-modern w-full">
                <thead>
                    <tr>
                        <th class="text-left">Özellik</th>
                        <th class="bg-gradient-to-r from-purple-600 to-pink-600">Muzibu</th>
                        <th>Spotify/YouTube</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-left font-semibold">Ticari Kullanım</td>
                        <td><span class="text-3xl">✓</span><br><span class="text-xs text-gray-400">Tamamen yasal</span></td>
                        <td><span class="text-3xl text-red-500">✗</span><br><span class="text-xs text-gray-400">Yasak</span></td>
                    </tr>
                    <tr>
                        <td class="text-left font-semibold">Telif Cezası Riski</td>
                        <td><span class="text-3xl">✓</span><br><span class="text-xs text-gray-400">Sıfır risk</span></td>
                        <td><span class="text-3xl text-red-500">✗</span><br><span class="text-xs text-gray-400">150K TL'ye kadar</span></td>
                    </tr>
                    <tr>
                        <td class="text-left font-semibold">Reklamsız</td>
                        <td><span class="text-3xl">✓</span><br><span class="text-xs text-gray-400">%100 kesintisiz</span></td>
                        <td><span class="text-3xl text-red-500">✗</span><br><span class="text-xs text-gray-400">Reklamlı</span></td>
                    </tr>
                    <tr>
                        <td class="text-left font-semibold">İşletmeye Özel</td>
                        <td><span class="text-3xl">✓</span><br><span class="text-xs text-gray-400">Sektöre özel listeler</span></td>
                        <td><span class="text-3xl text-red-500">✗</span><br><span class="text-xs text-gray-400">Kişisel kullanım</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</section>

<!-- 5. MÜŞTERİ DENEYİMLERİ -->
<section class="pt-16">
    <div class="container mx-auto px-6">
        <div class="text-center mb-12" data-aos="fade-up">
            <h2 class="text-5xl font-bold mb-6">Müşterilerimiz Ne Diyor?</h2>
            <p class="text-xl text-gray-400">Muzibu kullanan işletme sahiplerinin deneyimleri</p>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            @for($i = 1; $i <= 3; $i++)
            <div class="glass-card rounded-3xl overflow-hidden hover:transform hover:scale-105 transition-all duration-300" data-aos="fade-up" data-aos-delay="{{ $i * 100 }}">
                <div class="aspect-video bg-gradient-to-br from-purple-600 to-pink-600 flex items-center justify-center">
                    <i class="fas fa-play-circle text-7xl text-white/90 hover:text-white cursor-pointer transition-colors"></i>
                </div>
                <div class="p-8">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-user text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg">Müşteri {{ $i }}</h3>
                            <p class="text-sm text-gray-400">İşletme Sahibi</p>
                        </div>
                    </div>
                    <p class="text-gray-300 italic">"Muzibu ile telif sorunlarından tamamen kurtulduk. Hem yasal güvence hem de kaliteli müzik!"</p>
                </div>
            </div>
            @endfor
        </div>
    </div>
</section>

<!-- 6. SSS -->
<section class="pt-16 pb-24">
    <div class="container mx-auto px-6 max-w-4xl pt-12">
        <div class="text-center mb-12" data-aos="fade-up">
            <h2 class="text-5xl font-bold mb-6">Sıkça Sorulan Sorular</h2>
            <p class="text-xl text-gray-400">Merak ettiğiniz her şey</p>
        </div>

        <div class="space-y-4">
            @php
            $faqs = [
                ['q' => 'Muzibu\'daki müzikler gerçekten "telifsiz" mi?', 'a' => 'Evet. Muzibu\'daki müzikler, ticari kullanım için özel olarak lisanslanmıştır. MESAM, Mü-Yap gibi kurumlara ek ücret ödemenize gerek kalmaz.'],
                ['q' => 'Hangi sektörler için uygun?', 'a' => 'Restoran, cafe, mağaza, spor salonu, otel, kuaför, ofis ve daha birçok sektör için özel müzik listeleri sunuyoruz.'],
                ['q' => 'Reklam var mı?', 'a' => 'Hayır, yayınlarımız %100 kesintisiz ve reklamsızdır.'],
                ['q' => 'Müzik kalitesi nasıl?', 'a' => 'Stüdyo kalitesi ve kayıpsız (Lossless) ses çözünürlüğü. Düşük kaliteli yayın yapmayız.'],
                ['q' => 'Ücretsiz deneyebilir miyim?', 'a' => 'Evet! Hemen üye olarak ücretsiz deneme sürümünü başlatabilirsiniz.']
            ];
            @endphp

            @foreach($faqs as $index => $faq)
            <div class="glass-card rounded-2xl overflow-hidden" data-aos="fade-up" data-aos-delay="{{ ($index + 1) * 100 }}">
                <button class="w-full px-8 py-6 text-left font-semibold flex justify-between items-center hover:bg-white/5 transition-colors" onclick="toggleAccordion(this)">
                    <span class="text-lg">{{ $faq['q'] }}</span>
                    <i class="fas fa-plus transition-transform text-purple-400 text-xl"></i>
                </button>
                <div class="hidden px-8 pb-6 text-gray-400 leading-relaxed">
                    {{ $faq['a'] }}
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

</div>
<!-- END MAIN CONTENT WRAPPER -->

<!-- 7. CTA FINAL -->
<section class="pt-16 bg-gradient-to-r from-purple-600 via-pink-600 to-purple-600 relative overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-0 left-0 w-96 h-96 bg-white rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 right-0 w-96 h-96 bg-white rounded-full blur-3xl"></div>
    </div>

    <div class="container mx-auto px-6 text-center relative z-10">
        <h2 class="text-5xl lg:text-6xl font-black mb-6" data-aos="zoom-in">
            Telif Derdinden Kurtulun!
        </h2>
        <p class="text-2xl mb-10 text-white/90 max-w-2xl mx-auto" data-aos="fade-up" data-aos-delay="100">
            İşletmenizde yasal müzik çalın, 150.000 TL'ye varan cezalardan korunun
        </p>
        <a href="/login?tab=register"
           class="inline-flex items-center justify-center gap-3 bg-white text-purple-600 px-12 py-6 rounded-full font-bold text-2xl transition-all duration-300 hover:scale-105 hover:shadow-3xl shadow-2xl whitespace-nowrap min-w-[320px]"
           data-aos="fade-up" data-aos-delay="200">
            <i class="fas fa-rocket text-3xl"></i>
            <span>Hemen Başlayın</span>
        </a>
    </div>
</section>
@endsection

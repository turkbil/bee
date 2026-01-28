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
    <meta property="og:url" content="{{ url('/landing') }}">

    <!-- Canonical -->
    <link rel="canonical" href="{{ url('/landing') }}">
@endpush

@push('styles')
<style>
    /* Hero Gradient - Muzibu Colors */
    .hero-gradient {
        background: linear-gradient(135deg, #ff6b6b 0%, #ff5252 50%, #e91e63 100%);
    }

    /* Sticky CTA - Sol Alt (Sidebar yok) */
    .sticky-cta {
        position: fixed;
        bottom: 20px;
        left: 20px;
        z-index: 40;
    }

    @media (max-width: 768px) {
        .sticky-cta {
            bottom: 10px;
            left: 10px;
            font-size: 0.875rem;
            padding: 0.5rem 1rem;
        }
    }

    /* Comparison Table */
    .comparison-table th,
    .comparison-table td {
        padding: 1rem;
        text-align: center;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .comparison-table th {
        background: rgba(255, 255, 255, 0.05);
        font-weight: 600;
    }

    .comparison-yes {
        color: #10b981;
        font-size: 1.5rem;
    }

    .comparison-no {
        color: #ef4444;
        font-size: 1.5rem;
    }

</style>
@endpush

@section('content')
<!-- Sticky Kayıt Butonu - Sol Alt -->
<a href="/login?tab=register" class="sticky-cta hidden md:block bg-gradient-to-r from-[#ff6b6b] to-[#ff9966] hover:opacity-90 text-white px-6 py-3 rounded-full font-semibold transition-all duration-300 hover:scale-105 shadow-lg">
    <i class="fas fa-rocket mr-2"></i> Telif Derdinden Kurtulun!
</a>

<!-- WhatsApp ve Telefon Butonları - Sağ Alt -->
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

<!-- 1. HERO SECTION - Açılış Cümlesi -->
<section class="hero-gradient py-20 lg:py-32">
    <div class="container mx-auto px-6">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <!-- Sol: Metin -->
            <div class="space-y-6" data-aos="fade-right">
                <h1 class="text-4xl lg:text-5xl font-bold leading-tight">
                    Türkiye'nin İlk Kurumsal Telifsiz Müzik Platformu
                </h1>
                <p class="text-2xl text-white/90 font-semibold">
                    Yasal Güvence ile telif riskini sıfırlayın!
                </p>
                <p class="text-lg text-white/80">
                    İşletmenizde metrekare başı 150.000 TL'ye kadar ulaşan telif cezası yeme riskiniz var. Muzibu ile bu riskten kurtulun.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 pt-4">
                    <a href="/login?tab=register" class="bg-white text-[#ff6b6b] px-8 py-4 rounded-lg font-bold text-lg hover:bg-white/90 transition-all duration-300 hover:scale-105 text-center">
                        <i class="fas fa-rocket mr-2"></i> Telif Derdinden Kurtulun!
                    </a>
                    <a href="#neden-muzibu" class="border-2 border-white text-white px-8 py-4 rounded-lg font-semibold text-lg hover:bg-white hover:text-[#ff6b6b] transition-all duration-300 text-center">
                        Daha Fazla Bilgi
                    </a>
                </div>
            </div>

            <!-- Sağ: Görsel -->
            <div data-aos="fade-left" data-aos-delay="200">
                <img src="https://images.unsplash.com/photo-1511379938547-c1f69419868d?w=800"
                     alt="Müzik Dinleyen İşletme"
                     class="rounded-lg shadow-2xl">
            </div>
        </div>
    </div>
</section>

<!-- 2. REFERANS LOGOLARI -->
<section class="bg-zinc-900 py-12 border-b border-white/10">
    <div class="container mx-auto px-6">
        <h2 class="text-center text-gray-400 font-semibold mb-8" data-aos="fade-up">
            Bize Güvenen Markalar
        </h2>
        <div class="grid grid-cols-2 md:grid-cols-6 gap-8 items-center opacity-60 grayscale hover:grayscale-0 transition-all duration-300">
            @for($i = 1; $i <= 6; $i++)
            <div class="text-center" data-aos="fade-up" data-aos-delay="{{ $i * 100 }}">
                <div class="text-4xl font-bold text-white/20">LOGO {{ $i }}</div>
            </div>
            @endfor
        </div>
    </div>
</section>

<!-- 3. NEDEN MUZIBU - Avantajlar -->
<section id="neden-muzibu" class="py-20 bg-zinc-900/50">
    <div class="container mx-auto px-6">
        <div class="text-center mb-16" data-aos="fade-up">
            <span class="text-[#ff6b6b] font-semibold text-sm uppercase tracking-wider">Neden Kullanılmalı?</span>
            <h2 class="text-4xl font-bold mt-2 mb-4">Neden Muzibu?</h2>
            <p class="text-gray-400 text-lg max-w-2xl mx-auto">
                Ziyaretçilerin Muzibu'yu neden tercih etmesi gerektiğini keşfedin
            </p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- Avantaj 1 -->
            <div class="bg-white/5 hover:bg-white/10 p-8 rounded-xl border border-white/10 hover:border-[#ff6b6b]/50 transition-all duration-300" data-aos="fade-up" data-aos-delay="100">
                <div class="bg-purple-500/20 w-16 h-16 rounded-full flex items-center justify-center mb-6">
                    <i class="fas fa-shield-check text-purple-400 text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-3">%100 Yasal Güvence</h3>
                <p class="text-gray-400">
                    Geleneksel telif ücreti (MESAM/Mü-Yap) ödeme zorunluluğunuz sona erer. Tek abonelik, tam yasal koruma.
                </p>
            </div>

            <!-- Avantaj 2 -->
            <div class="bg-white/5 hover:bg-white/10 p-8 rounded-xl border border-white/10 hover:border-[#ff6b6b]/50 transition-all duration-300" data-aos="fade-up" data-aos-delay="200">
                <div class="bg-green-500/20 w-16 h-16 rounded-full flex items-center justify-center mb-6">
                    <i class="fas fa-music text-green-400 text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-3">Kapsamlı Müzik Çeşitliliği</h3>
                <p class="text-gray-400">
                    Kütüphanemizde her tarza ve her sektöre uygun, profesyonel binlerce çalma listesi mevcuttur.
                </p>
            </div>

            <!-- Avantaj 3 -->
            <div class="bg-white/5 hover:bg-white/10 p-8 rounded-xl border border-white/10 hover:border-[#ff6b6b]/50 transition-all duration-300" data-aos="fade-up" data-aos-delay="300">
                <div class="bg-blue-500/20 w-16 h-16 rounded-full flex items-center justify-center mb-6">
                    <i class="fas fa-laptop text-blue-400 text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-3">Kolay Kurulum ve Kullanım</h3>
                <p class="text-gray-400">
                    Karmaşık donanım gerektirmez. Mobil uygulama, masaüstü veya tarayıcı üzerinden anında yayın yapmaya başlayın.
                </p>
            </div>

            <!-- Avantaj 4 -->
            <div class="bg-white/5 hover:bg-white/10 p-8 rounded-xl border border-white/10 hover:border-[#ff6b6b]/50 transition-all duration-300" data-aos="fade-up" data-aos-delay="400">
                <div class="bg-orange-500/20 w-16 h-16 rounded-full flex items-center justify-center mb-6">
                    <i class="fas fa-signal text-orange-400 text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-3">Güvenilir ve Kesintisiz Akış</h3>
                <p class="text-gray-400">
                    Yayın altyapımız, en yoğun saatlerde ve uzun çalışma periyotlarında dahi müziğin durmamasını sağlar. Müşteri deneyiminiz asla aksamaz.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- 4. SOSYAL KANITLAR - Sayılar -->
<section class="py-16 bg-gradient-to-r from-[#ff6b6b] to-[#e91e63]">
    <div class="container mx-auto px-6">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
            <div data-aos="fade-up" data-aos-delay="100">
                <div class="text-5xl font-bold mb-2">5<span class="text-white/70">YIL</span></div>
                <div class="text-white/70 uppercase tracking-wide text-sm">Tecrübe</div>
            </div>
            <div data-aos="fade-up" data-aos-delay="200">
                <div class="text-5xl font-bold mb-2">8<span class="text-white/70">+</span></div>
                <div class="text-white/70 uppercase tracking-wide text-sm">Farklı Ülke</div>
            </div>
            <div data-aos="fade-up" data-aos-delay="300">
                <div class="text-5xl font-bold mb-2">20<span class="text-white/70">+</span></div>
                <div class="text-white/70 uppercase tracking-wide text-sm">Farklı Sektör</div>
            </div>
            <div data-aos="fade-up" data-aos-delay="400">
                <div class="text-5xl font-bold mb-2">5<span class="text-white/70">M+</span></div>
                <div class="text-white/70 uppercase tracking-wide text-sm">Şarkı</div>
            </div>
        </div>
    </div>
</section>

<!-- 5. REFERANS VIDEOLARI -->
<section class="py-20 bg-black">
    <div class="container mx-auto px-6">
        <div class="text-center mb-16" data-aos="fade-up">
            <h2 class="text-4xl font-bold mb-4">Muzibu Kullanıcısı İşletme Sahipleri Anlatıyor</h2>
            <p class="text-gray-400 text-lg">Müşterilerimizin deneyimlerini dinleyin</p>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            @for($i = 1; $i <= 3; $i++)
            <div class="bg-white/5 hover:bg-white/10 rounded-xl border border-white/10 overflow-hidden transition-all duration-300" data-aos="fade-up" data-aos-delay="{{ $i * 100 }}">
                <div class="aspect-video bg-zinc-800 flex items-center justify-center">
                    <i class="fas fa-play-circle text-6xl text-[#ff6b6b]"></i>
                </div>
                <div class="p-6">
                    <h3 class="font-bold text-lg mb-2">Müşteri Referansı {{ $i }}</h3>
                    <p class="text-sm text-gray-400 mb-3">İşletme Sahibi</p>
                    <p class="text-white">"Muzibu ile telif sorunlarından kurtulduk!"</p>
                </div>
            </div>
            @endfor
        </div>
    </div>
</section>

<!-- 6. SIKÇA SORULAN SORULAR -->
<section class="py-20 bg-zinc-900/50">
    <div class="container mx-auto px-6 max-w-4xl">
        <div class="text-center mb-16" data-aos="fade-up">
            <h2 class="text-4xl font-bold mb-4">Sıkça Sorulan Sorular</h2>
            <p class="text-gray-400">En çok merak edilen soruların cevapları</p>
        </div>

        <div class="space-y-4">
            @php
            $faqs = [
                ['q' => 'Sürekli aynı şarkıları duyar mıyız?', 'a' => 'Hayır, kataloğumuz çok zengin ve sürekli güncelleniyor. Her müzik tarzında ve her atmosfere uygun profesyonel çalma listesi sunuyoruz.'],
                ['q' => 'Kullanım için özel bir cihaz gerekli mi?', 'a' => 'Hayır gerekmiyor. Akıllı telefon, tablet ve bilgisayarınız yeterlidir. Tek tıkla kolayca sitemize ulaşabilirsiniz.'],
                ['q' => 'Reklam içeriyor musunuz?', 'a' => 'Hayır, yayınlarımız %100 kesintisizdir.'],
                ['q' => 'Müzik kaliteniz nedir?', 'a' => 'Stüdyo kalitesi ve kayıpsız (Lossless) ses çözünürlüğü. Düşük çözünürlüklü yayınlar yapmayız.'],
                ['q' => 'Abonelik ücretleriniz aylık mı yoksa yıllık mı?', 'a' => 'Farklı ihtiyaçlara yönelik hem aylık hem de yıllık avantajlı abonelik paketleri sunuyoruz. Detayları fiyatlandırma sayfamızdan bulabilirsiniz.'],
                ['q' => 'Birden fazla şubem var ise ne yapmalıyım?', 'a' => 'Her lokasyon için ayrı üyelik almanız gerekiyor.'],
                ['q' => 'Muzibu\'yu ücretsiz deneyebilir miyim?', 'a' => 'Evet, hemen üye olarak deneme sürümünü başlatabilirsiniz!'],
                ['q' => 'Teknik destek alabilir miyim?', 'a' => 'Evet, kullanıcılarımız için kurulum ve yayın sırasında yaşanabilecek her türlü sorun için hızlı ve etkili teknik destek sağlıyoruz.'],
                ['q' => 'Listelerinizi günün saatlerine göre istediğim gibi değiştirebiliyor muyum?', 'a' => 'Elbette, sabah, öğle ve akşam servislerine veya yoğun saatlere göre hatta mekanın ruh haline göre bile değişim yapabilirsiniz.'],
                ['q' => 'Muzibu\'daki müzikler gerçekten "telifsiz" mi?', 'a' => 'Evet. Muzibu\'daki müzikler, ticari kullanım için özel olarak lisanslanmıştır. Bu, geleneksel telif hakları kurumlarına (Türkiye\'de MESAM, Mü-Yap, vb.) ek bir ücret ödemenize gerek kalmadan müziği işletmenizde kullanabileceğiniz anlamına gelir.'],
                ['q' => 'Hangi tür işletmeler Muzibu\'yu kullanabilir?', 'a' => 'Halka açık bir alanda müzik yayınlayan veya müzik içeren içerikler oluşturan tüm ticari işletmeler Muzibu\'yu kullanabilir. Mağazalar ve Perakende Zincirleri, Restoranlar ve Kafeler, Oteller ve Konaklama Tesisleri, Spor Salonları ve Stüdyolar, Ofisler ve Çalışma Alanları, Kuaförler ve Güzellik Salonları gibi.'],
                ['q' => 'Kuaför salonum için doğru müzik listesini nasıl seçmeliyim?', 'a' => 'Muzibu\'da kuaförler için özel olarak tasarlanmış listelerimiz mevcuttur: Gün Ortası Enerjisi: Orta tempolu pop veya hafif dans müziği ile yoğun saatlerde motivasyonu artırır. Lüks ve Rahatlama: Caz, Chill-out veya Soft Lounge tarzlarıyla saç yıkama ve bakım seanslarında müşteriyi dinlendirir. Erken Saat Sakinliği: Daha düşük tempolu, enstrümantal veya akustik müzikler ile güne huzurlu bir başlangıç sağlar. Platformumuzdaki ruh hali (mood) ve tempo filtrelerini kullanarak size en uygun listeyi kolayca bulabilirsiniz.'],
                ['q' => 'Muzibu Nedir?', 'a' => 'İşletmelerin ticari alanlarında yasal, telifsiz müzik yayınlamaları için hazırlanmış, özel bir müzik akış platformudur.'],
                ['q' => 'Diğer platformlardan farkı nedir?', 'a' => 'Onlar kişisel kullanıma açıktır ve ticari alanda yasal değildir. Muzibu ise %100 yasal ticari yayın için tasarlanmış, işletmeye özel özellikler sunan tek çözümdür.'],
                ['q' => 'Muzibu\'yu Neden Kullanmalıyım?', 'a' => 'İşletmende çaldığın müziklerden dolayı yüksek rakamlı telif cezalarından korunmak için. İşletmende metrekare başı 150.000 TL\'ye kadar ulaşan telif cezası yeme riskin var.']
            ];
            @endphp

            @foreach($faqs as $index => $faq)
            <div class="border border-white/10 rounded-lg bg-white/5 hover:bg-white/10 transition-all duration-300" data-aos="fade-up" data-aos-delay="{{ ($index + 1) * 100 }}">
                <button class="w-full px-6 py-4 text-left font-semibold flex justify-between items-center" onclick="toggleAccordion(this)">
                    <span>{{ $faq['q'] }}</span>
                    <i class="fas fa-plus transition-transform text-[#ff6b6b]"></i>
                </button>
                <div class="hidden px-6 pb-4 text-gray-400">
                    {{ $faq['a'] }}
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- 7. BİZ VE ONLAR - Karşılaştırma -->
<section class="py-20 bg-black">
    <div class="container mx-auto px-6">
        <div class="text-center mb-16" data-aos="fade-up">
            <h2 class="text-4xl font-bold mb-4">Muzibu vs Popüler Müzik Hizmetleri</h2>
            <p class="text-gray-400 text-lg">Spotify, YouTube Music, Apple Music gibi platformlarla karşılaştırma</p>
        </div>

        <div class="max-w-5xl mx-auto bg-white/5 rounded-xl border border-white/10 overflow-hidden" data-aos="fade-up" data-aos-delay="200">
            <table class="comparison-table w-full">
                <thead>
                    <tr>
                        <th class="text-left">Konu</th>
                        <th class="bg-[#ff6b6b]/20 text-white border-l border-r border-white/20">Muzibu</th>
                        <th>Diğer Platformlar</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-left font-semibold">Ticari Kullanım Sertifikası</td>
                        <td class="border-l border-r border-white/10">
                            <div class="flex flex-col items-center gap-2">
                                <span class="comparison-yes">✓</span>
                                <span class="text-xs text-gray-400">Tamamen yasal ve sertifikalı. Tek abonelikle telif cezasından kurtulun.</span>
                            </div>
                        </td>
                        <td>
                            <div class="flex flex-col items-center gap-2">
                                <span class="comparison-no">✗</span>
                                <span class="text-xs text-gray-400">Ticari kullanıma uygun değil. Telif kurumlarına ödeme gerektirir.</span>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-left font-semibold">Yasal Risk</td>
                        <td class="border-l border-r border-white/10">
                            <div class="flex flex-col items-center gap-2">
                                <span class="comparison-yes">✓</span>
                                <span class="text-xs text-gray-400">Sıfır risk, üyeliğiniz sayesinde yasal güvence altındasınız.</span>
                            </div>
                        </td>
                        <td>
                            <div class="flex flex-col items-center gap-2">
                                <span class="comparison-no">✗</span>
                                <span class="text-xs text-gray-400">Yüksek risk, telif cezalarıyla karşılaşma riski yüksek.</span>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-left font-semibold">Reklam Durumu</td>
                        <td class="border-l border-r border-white/10">
                            <div class="flex flex-col items-center gap-2">
                                <span class="comparison-yes">✓</span>
                                <span class="text-xs text-gray-400">Kesintisiz ve reklamsız yayın garantisi.</span>
                            </div>
                        </td>
                        <td>
                            <div class="flex flex-col items-center gap-2">
                                <span class="comparison-no">✗</span>
                                <span class="text-xs text-gray-400">Reklamlı (ücretsiz planda). İşinize ait olmayan kişisel reklamlarla karşılaşırsınız.</span>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-left font-semibold">Müzik Seçimi</td>
                        <td class="border-l border-r border-white/10">
                            <div class="flex flex-col items-center gap-2">
                                <span class="comparison-yes">✓</span>
                                <span class="text-xs text-gray-400">Tam kontrol. Sektöre, atmosfere ve müzik tarzına göre çalma listeleri.</span>
                            </div>
                        </td>
                        <td>
                            <div class="flex flex-col items-center gap-2">
                                <span class="comparison-no">✗</span>
                                <span class="text-xs text-gray-400">Müzik seçimi markanızla uyumlu olmayabilir.</span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</section>

<!-- Final CTA -->
<section class="py-16 bg-black text-white text-center border-t border-white/10">
    <div class="container mx-auto px-6">
        <h2 class="text-3xl font-bold mb-4">Telif Derdinden Hemen Kurtulun!</h2>
        <p class="text-xl text-gray-400 mb-8">İşletmenizde yasal müzik çalın, 150.000 TL'ye varan telif cezası riskinden kurtulun.</p>
        <a href="/login?tab=register" class="inline-block bg-gradient-to-r from-[#ff6b6b] to-[#ff9966] hover:opacity-90 text-white px-8 py-4 rounded-lg font-bold text-lg transition-all duration-300 hover:scale-105">
            <i class="fas fa-rocket mr-2"></i> Ücretsiz Deneyin
        </a>
    </div>
</section>
@endsection

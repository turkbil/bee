@extends('layouts.landing.minimal')

@push('meta')
    <!-- PRIMARY SEO - Exact Keyword Match -->
    <title>Elektrikli Transpalet | Akülü Transpalet 1500kg | İXTİF</title>
    <meta name="description" content="✓ Elektrikli transpalet ✓ Akülü transpalet ✓ Terazili transpalet ✓ Denge tekerli transpalet | 1500 kg Li-Ion | Kasım kampanyası $1,250 | 1 yıl garanti">
    <meta name="keywords" content="transpalet, elektrikli transpalet, akülü transpalet, terazili transpalet, denge tekerli transpalet, li-ion transpalet, 1500 kg transpalet">

    <!-- Open Graph -->
    <meta property="og:title" content="Elektrikli Transpalet 1500kg - Akülü Transpalet Kampanya">
    <meta property="og:description" content="Elektrikli transpalet ve akülü transpalet çözümleri. 1500 kg Li-Ion bataryalı, kompakt tasarım. Kasım kampanyası!">
    <meta property="og:type" content="product">
    <meta property="og:url" content="{{ url('/elektrikli-transpalet') }}">
    <meta property="og:image" content="https://ixtif.com/storage/tenant2/82/lep4ns74ctsp2gydkwetkw7xs1y01psfss4tf5u6.png">

    <!-- Canonical -->
    <link rel="canonical" href="{{ url('/elektrikli-transpalet') }}">
@endpush

@push('schema')
    <!-- Schema.org Product Markup -->
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "Product",
        "name": "iXtif F4 Elektrikli Transpalet",
        "description": "1500 kg kapasiteli elektrikli transpalet, akülü transpalet. 24V 20Ah Li-Ion bataryalı, kompakt tasarım.",
        "brand": {
            "@@type": "Brand",
            "name": "iXtif"
        },
        "offers": {
            "@@type": "Offer",
            "price": "1250",
            "priceCurrency": "USD",
            "availability": "https://schema.org/InStock",
            "priceValidUntil": "2025-11-30",
            "url": "{{ url('/elektrikli-transpalet') }}"
        },
        "aggregateRating": {
            "@@type": "AggregateRating",
            "ratingValue": "4.9",
            "reviewCount": "150"
        },
        "image": "https://ixtif.com/storage/tenant2/82/lep4ns74ctsp2gydkwetkw7xs1y01psfss4tf5u6.png",
        "category": "Transpalet"
    }
    </script>

    <!-- Schema.org Organization -->
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "Organization",
        "name": "İXTİF İç ve Dış Ticaret",
        "url": "{{ url('/') }}",
        "logo": "{{ url('/logo.png') }}",
        "contactPoint": {
            "@@type": "ContactPoint",
            "telephone": "{{ setting('contact_phone_1', '+90-216-755-3555') }}",
            "contactType": "Sales"
        }
    }
    </script>
@endpush

@section('content')
    <!-- Campaign Banner - Sticky -->
    <div class="fixed top-16 left-0 right-0 z-30 bg-black/95 backdrop-blur-xl border-b border-yellow-600/30 py-3 px-4 text-center">
        <div class="flex items-center justify-center gap-4 flex-wrap text-sm">
            <span class="flex items-center gap-2">
                <i class="fas fa-tag text-yellow-500"></i>
                <span class="font-bold">KASIM KAMPANYASI</span>
            </span>
            <span class="hidden lg:inline text-gray-600">|</span>
            <span class="text-gray-400">Elektrikli Transpalet Fırsatı</span>
            <span class="hidden lg:inline text-gray-600">|</span>
            <span class="text-yellow-500 font-bold">$1,250</span>
        </div>
    </div>

    <!-- Hero Section - Above the fold optimization -->
    <section class="pt-24 pb-12 px-4 bg-gradient-to-b from-gray-950 via-gray-900 to-black">
        <div class="container mx-auto max-w-7xl">
            <!-- Breadcrumb - SEO -->
            <nav class="mb-6 text-sm text-gray-500">
                <a href="/" class="hover:text-yellow-500">Anasayfa</a>
                <span class="mx-2">/</span>
                <a href="/urunler" class="hover:text-yellow-500">Ürünler</a>
                <span class="mx-2">/</span>
                <span class="text-gray-400">Elektrikli Transpalet</span>
            </nav>

            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <!-- Left Content -->
                <div>
                    <!-- H1 - Primary Keyword -->
                    <h1 class="text-5xl lg:text-7xl font-black mb-4 leading-tight">
                        <span class="gold-gradient bg-clip-text text-transparent">Elektrikli Transpalet</span>
                        <span class="block text-white text-4xl lg:text-5xl mt-2">Akülü Transpalet 1500 kg</span>
                    </h1>
                    <p class="text-2xl text-yellow-500 font-bold mb-6">Premium Kalite • En Uygun Fiyat • Kasım Kampanyası</p>

                    <!-- USP - Unique Benefits -->
                    <div class="bg-yellow-600/10 border-l-4 border-yellow-600 p-4 mb-6">
                        <p class="text-lg text-gray-300 leading-relaxed">
                            ✓ <strong class="text-white">Li-Ion batarya</strong> teknolojisi<br>
                            ✓ <strong class="text-white">1500 kg</strong> taşıma kapasitesi<br>
                            ✓ <strong class="text-white">1360 mm</strong> dönüş yarıçapı<br>
                            ✓ <strong class="text-white">4.5 km/h</strong> maksimum hız
                        </p>
                    </div>

                    <!-- Price - Clear CTA -->
                    <div class="bg-gradient-to-br from-gray-900 to-gray-800 p-6 rounded-2xl border-2 border-yellow-600 mb-6">
                        <div class="flex items-baseline gap-3 mb-2">
                            <span class="text-gray-500 line-through text-xl">$1,560</span>
                            <span class="text-yellow-500 font-black text-base bg-yellow-600/20 px-3 py-1 rounded-lg">-20% İNDİRİM</span>
                        </div>
                        <div class="flex items-baseline gap-3 mb-3">
                            <span class="text-5xl font-black gold-gradient bg-clip-text text-transparent">$1,250</span>
                            <span class="text-gray-400">+ KDV</span>
                        </div>
                        <div class="flex items-center gap-4 text-sm">
                            <span class="text-yellow-500">
                                <i class="fas fa-shield-alt mr-1"></i> 1 Yıl Garanti
                            </span>
                            <span class="text-yellow-500">
                                <i class="fas fa-box-open mr-1"></i> Stoktan Hemen Teslim
                            </span>
                        </div>
                    </div>

                    <!-- Countdown (Dynamic) -->
                    <div id="countdown" class="grid grid-cols-4 gap-2 mb-6">
                        <div class="bg-black/50 p-2 rounded-lg border border-yellow-600/30 text-center">
                            <div class="text-2xl font-black gold-gradient bg-clip-text text-transparent" id="countdown-days">01</div>
                            <div class="text-gray-400 text-xs">Gün</div>
                        </div>
                        <div class="bg-black/50 p-2 rounded-lg border border-yellow-600/30 text-center">
                            <div class="text-2xl font-black gold-gradient bg-clip-text text-transparent" id="countdown-hours">18</div>
                            <div class="text-gray-400 text-xs">Saat</div>
                        </div>
                        <div class="bg-black/50 p-2 rounded-lg border border-yellow-600/30 text-center">
                            <div class="text-2xl font-black gold-gradient bg-clip-text text-transparent" id="countdown-minutes">45</div>
                            <div class="text-gray-400 text-xs">Dakika</div>
                        </div>
                        <div class="bg-black/50 p-2 rounded-lg border border-yellow-600/30 text-center">
                            <div class="text-2xl font-black gold-gradient bg-clip-text text-transparent" id="countdown-seconds">23</div>
                            <div class="text-gray-400 text-xs">Saniye</div>
                        </div>
                    </div>

                    <!-- Primary CTA -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-3">
                        <a href="{{ whatsapp_link('Elektrikli Transpalet Kampanya') }}"
                           class="px-6 py-4 bg-gradient-to-br from-gray-800 to-gray-900 border-2 border-yellow-600 hover:border-yellow-500 rounded-xl text-white text-center font-bold transition-all flex items-center justify-center gap-2">
                            <i class="fab fa-whatsapp text-yellow-500 text-xl"></i>
                            <span>WhatsApp</span>
                        </a>
                        <a href="tel:{{ setting('contact_phone_1', '02167553555') }}"
                           class="px-6 py-4 bg-gradient-to-br from-gray-800 to-gray-900 border-2 border-yellow-600 hover:border-yellow-500 rounded-xl text-white text-center font-bold transition-all flex items-center justify-center gap-2">
                            <i class="fas fa-phone text-yellow-500 text-xl"></i>
                            <span>Telefon</span>
                        </a>
                        <a href="#iletisim-formu"
                           class="px-6 py-4 gold-gradient rounded-xl text-gray-950 text-center font-bold transition-all flex items-center justify-center gap-2 hover:shadow-[0_0_20px_rgba(212,175,55,0.5)]">
                            <i class="fas fa-headset text-gray-950 text-xl"></i>
                            <span>Sizi Arayalım</span>
                        </a>
                    </div>

                </div>

                <!-- Right - Product Image (Optimized) -->
                <div class="relative">
                    <div class="aspect-square bg-gradient-to-br from-gray-900 via-gray-800 to-black rounded-3xl border border-gray-700 flex items-center justify-center overflow-hidden relative p-8">
                        <img src="https://ixtif.com/storage/tenant2/82/lep4ns74ctsp2gydkwetkw7xs1y01psfss4tf5u6.png"
                             alt="Elektrikli transpalet akülü transpalet 1500 kg Li-Ion iXtif F4"
                             title="Elektrikli Transpalet - Akülü Transpalet 1500kg"
                             class="w-full h-full object-contain relative z-10"
                             loading="eager"
                             width="600"
                             height="600">
                        <div class="absolute top-0 left-0 w-40 h-40 bg-yellow-600/10 rounded-full blur-3xl"></div>
                        <div class="absolute bottom-0 right-0 w-40 h-40 bg-yellow-500/10 rounded-full blur-3xl"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Transpalet Types - Keyword Expansion -->
    <section class="py-16 px-4 bg-black">
        <div class="container mx-auto max-w-7xl">
            <h2 class="text-4xl font-black mb-12 text-center">
                <span class="text-white">Transpalet</span>
                <span class="gold-gradient bg-clip-text text-transparent"> Çeşitleri</span>
            </h2>

            <div class="grid lg:grid-cols-4 gap-6">
                <!-- Elektrikli Transpalet -->
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 p-6 rounded-2xl border border-yellow-600">
                    <div class="w-12 h-12 gold-gradient rounded-lg flex items-center justify-center mb-4">
                        <i class="fas fa-bolt text-gray-950 text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-white">Elektrikli Transpalet</h3>
                    <p class="text-gray-400 text-sm mb-3">
                        Li-Ion bataryalı elektrikli transpalet, yüksek verimlilik ve uzun çalışma süresi.
                    </p>
                    <ul class="space-y-2 text-sm text-gray-500">
                        <li><i class="fas fa-check text-yellow-500 mr-2"></i>1500 kg kapasite</li>
                        <li><i class="fas fa-check text-yellow-500 mr-2"></i>Hızlı şarj</li>
                        <li><i class="fas fa-check text-yellow-500 mr-2"></i>Sessiz çalışma</li>
                    </ul>
                </div>

                <!-- Akülü Transpalet -->
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 p-6 rounded-2xl border border-gray-700">
                    <div class="w-12 h-12 bg-gray-700 rounded-lg flex items-center justify-center mb-4">
                        <i class="fas fa-battery-full text-yellow-500 text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-white">Akülü Transpalet</h3>
                    <p class="text-gray-400 text-sm mb-3">
                        Güçlü akülü transpalet sistemi, uzun ömürlü batarya teknolojisi.
                    </p>
                    <ul class="space-y-2 text-sm text-gray-500">
                        <li><i class="fas fa-check text-yellow-500 mr-2"></i>24V sistem</li>
                        <li><i class="fas fa-check text-yellow-500 mr-2"></i>Düşük bakım</li>
                        <li><i class="fas fa-check text-yellow-500 mr-2"></i>Ekonomik</li>
                    </ul>
                </div>

                <!-- Terazili Transpalet -->
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 p-6 rounded-2xl border border-gray-700">
                    <div class="w-12 h-12 bg-gray-700 rounded-lg flex items-center justify-center mb-4">
                        <i class="fas fa-balance-scale text-yellow-500 text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-white">Terazili Transpalet</h3>
                    <p class="text-gray-400 text-sm mb-3">
                        Entegre terazi sistemi, hassas tartım özellikli terazili transpalet.
                    </p>
                    <ul class="space-y-2 text-sm text-gray-500">
                        <li><i class="fas fa-check text-yellow-500 mr-2"></i>Dijital gösterge</li>
                        <li><i class="fas fa-check text-yellow-500 mr-2"></i>Hassas ölçüm</li>
                        <li><i class="fas fa-check text-yellow-500 mr-2"></i>Opsiyonel</li>
                    </ul>
                </div>

                <!-- Denge Tekerli Transpalet -->
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 p-6 rounded-2xl border border-gray-700">
                    <div class="w-12 h-12 bg-gray-700 rounded-lg flex items-center justify-center mb-4">
                        <i class="fas fa-cog text-yellow-500 text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-white">Denge Tekerli Transpalet</h3>
                    <p class="text-gray-400 text-sm mb-3">
                        Kompakt denge tekerli transpalet tasarımı, dar alanlarda yüksek manevra kabiliyeti.
                    </p>
                    <ul class="space-y-2 text-sm text-gray-500">
                        <li><i class="fas fa-check text-yellow-500 mr-2"></i>1360 mm dönüş</li>
                        <li><i class="fas fa-check text-yellow-500 mr-2"></i>Kompakt tasarım</li>
                        <li><i class="fas fa-check text-yellow-500 mr-2"></i>Stabil hareket</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Technical Specs - SEO Optimized -->
    <section class="py-16 px-4 bg-gradient-to-b from-black via-gray-900 to-black">
        <div class="container mx-auto max-w-7xl">
            <h2 class="text-4xl font-black mb-4 text-center">
                <span class="gold-gradient bg-clip-text text-transparent">Elektrikli Transpalet</span>
                <span class="text-white"> Teknik Özellikleri</span>
            </h2>
            <p class="text-center text-gray-400 mb-12">iXtif F4 akülü transpalet detaylı teknik bilgileri</p>

            <div class="grid lg:grid-cols-4 gap-6 mb-12">
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 p-6 rounded-2xl border border-gray-700 text-center">
                    <div class="text-4xl font-black gold-gradient bg-clip-text text-transparent mb-2">1500 kg</div>
                    <div class="text-gray-400 text-sm">Yük Kapasitesi</div>
                    <p class="text-xs text-gray-500 mt-2">Elektrikli transpalet kapasitesi</p>
                </div>
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 p-6 rounded-2xl border border-gray-700 text-center">
                    <div class="text-4xl font-black gold-gradient bg-clip-text text-transparent mb-2">24V 20Ah</div>
                    <div class="text-gray-400 text-sm">Li-Ion Batarya</div>
                    <p class="text-xs text-gray-500 mt-2">Akülü transpalet batarya gücü</p>
                </div>
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 p-6 rounded-2xl border border-gray-700 text-center">
                    <div class="text-4xl font-black gold-gradient bg-clip-text text-transparent mb-2">4.5 km/h</div>
                    <div class="text-gray-400 text-sm">Maksimum Hız</div>
                    <p class="text-xs text-gray-500 mt-2">Transpalet hız performansı</p>
                </div>
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 p-6 rounded-2xl border border-gray-700 text-center">
                    <div class="text-4xl font-black gold-gradient bg-clip-text text-transparent mb-2">1360 mm</div>
                    <div class="text-gray-400 text-sm">Dönüş Yarıçapı</div>
                    <p class="text-xs text-gray-500 mt-2">Denge tekerli kompakt tasarım</p>
                </div>
            </div>

            <!-- Detailed Specs Table -->
            <div class="grid lg:grid-cols-2 gap-8">
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 p-8 rounded-2xl border border-gray-700">
                    <h3 class="text-2xl font-bold mb-6 gold-gradient bg-clip-text text-transparent">Ana Özellikler</h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center py-3 border-b border-gray-700">
                            <span class="text-gray-400">Tip</span>
                            <span class="text-white font-bold">Elektrikli Transpalet</span>
                        </div>
                        <div class="flex justify-between items-center py-3 border-b border-gray-700">
                            <span class="text-gray-400">Kapasite</span>
                            <span class="text-white font-bold">1500 kg</span>
                        </div>
                        <div class="flex justify-between items-center py-3 border-b border-gray-700">
                            <span class="text-gray-400">Batarya Tipi</span>
                            <span class="text-white font-bold">24V / 20Ah Li-Ion</span>
                        </div>
                        <div class="flex justify-between items-center py-3 border-b border-gray-700">
                            <span class="text-gray-400">Dönüş Yarıçapı</span>
                            <span class="text-white font-bold">1360 mm</span>
                        </div>
                        <div class="flex justify-between items-center py-3 border-b border-gray-700">
                            <span class="text-gray-400">Ağırlık</span>
                            <span class="text-white font-bold">120 kg</span>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-gray-900 to-gray-800 p-8 rounded-2xl border border-gray-700">
                    <h3 class="text-2xl font-bold mb-6 gold-gradient bg-clip-text text-transparent">Performans</h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center py-3 border-b border-gray-700">
                            <span class="text-gray-400">Sürüş Hızı (yük/boş)</span>
                            <span class="text-white font-bold">4.0 / 4.5 km/sa</span>
                        </div>
                        <div class="flex justify-between items-center py-3 border-b border-gray-700">
                            <span class="text-gray-400">Azami Eğim (yük/boş)</span>
                            <span class="text-white font-bold">%6 / %16</span>
                        </div>
                        <div class="flex justify-between items-center py-3 border-b border-gray-700">
                            <span class="text-gray-400">Sürüş Motoru</span>
                            <span class="text-white font-bold">0.75 kW</span>
                        </div>
                        <div class="flex justify-between items-center py-3 border-b border-gray-700">
                            <span class="text-gray-400">Şarj Süresi</span>
                            <span class="text-white font-bold">2-3 saat</span>
                        </div>
                        <div class="flex justify-between items-center py-3 border-b border-gray-700">
                            <span class="text-gray-400">Ses Seviyesi</span>
                            <span class="text-white font-bold">74 dB(A)</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Benefits - Keyword Rich Content -->
    <section class="py-16 px-4 bg-black">
        <div class="container mx-auto max-w-7xl">
            <h2 class="text-4xl font-black mb-12 text-center">
                <span class="gold-gradient bg-clip-text text-transparent">Elektrikli Transpalet</span>
                <span class="text-white"> Neden Tercih Edilmeli?</span>
            </h2>

            <div class="grid lg:grid-cols-3 gap-8">
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 p-8 rounded-2xl border border-gray-700">
                    <div class="w-16 h-16 gold-gradient rounded-xl flex items-center justify-center mb-6">
                        <i class="fas fa-leaf text-gray-950 text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-4 text-white">Çevre Dostu</h3>
                    <p class="text-gray-400 leading-relaxed">
                        Elektrikli transpalet ve akülü transpalet sistemleri sıfır emisyon ile çevre dostu çalışma ortamı sağlar. Li-Ion batarya teknolojisi ile uzun ömür.
                    </p>
                </div>

                <div class="bg-gradient-to-br from-gray-900 to-gray-800 p-8 rounded-2xl border border-gray-700">
                    <div class="w-16 h-16 gold-gradient rounded-xl flex items-center justify-center mb-6">
                        <i class="fas fa-dollar-sign text-gray-950 text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-4 text-white">Ekonomik ve Uygun Fiyatlı</h3>
                    <p class="text-gray-400 leading-relaxed">
                        Düşük işletme maliyeti, az bakım ihtiyacı. Uygun fiyatlı elektrikli transpalet ile yakıt tasarrufu ve uzun vadede yüksek ROI sağlar. Ekonomik akülü transpalet çözümü.
                    </p>
                </div>

                <div class="bg-gradient-to-br from-gray-900 to-gray-800 p-8 rounded-2xl border border-gray-700">
                    <div class="w-16 h-16 gold-gradient rounded-xl flex items-center justify-center mb-6">
                        <i class="fas fa-tachometer-alt text-gray-950 text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-4 text-white">Yüksek Performans</h3>
                    <p class="text-gray-400 leading-relaxed">
                        1500 kg kapasite, 4.5 km/h hız, 1360 mm dönüş yarıçapı. Dar alanlarda üstün manevra kabiliyeti ve kompakt çalışma.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Social Proof - Reviews -->
    <section class="py-16 px-4 bg-gradient-to-b from-black via-gray-900 to-black">
        <div class="container mx-auto max-w-7xl">
            <h2 class="text-4xl font-black mb-4 text-center">
                <span class="text-white">Müşteri</span>
                <span class="gold-gradient bg-clip-text text-transparent"> Yorumları</span>
            </h2>
            <p class="text-center text-gray-400 mb-12">Elektrikli transpalet kullanan müşterilerimizin deneyimleri</p>

            <div class="grid lg:grid-cols-3 gap-8 mb-12">
                <!-- Review 1 -->
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 p-8 rounded-2xl border border-gray-700 flex flex-col">
                    <div class="flex items-center gap-2 mb-4">
                        @for($i = 0; $i < 5; $i++)
                            <i class="fas fa-star text-yellow-500"></i>
                        @endfor
                    </div>
                    <p class="text-gray-400 mb-6 leading-relaxed flex-grow">
                        "Depomuzdaki dar koridorlar için ideal. F4 modelini aldık, manevra kabiliyeti gerçekten iyi. Batarya şarjı hızlı, sabah takıyoruz öğlene hazır oluyor. Bakım gerektirmiyor, pratik bir ürün."
                    </p>
                    <div class="flex items-center gap-3 mt-auto">
                        <div class="w-12 h-12 bg-yellow-600/20 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-user text-yellow-500"></i>
                        </div>
                        <div>
                            <p class="text-white font-bold">Rıdvan Yanıkoğlu</p>
                            <p class="text-gray-500 text-sm">Depo Sorumlusu - İstanbul</p>
                        </div>
                    </div>
                </div>

                <!-- Review 2 -->
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 p-8 rounded-2xl border border-gray-700 flex flex-col">
                    <div class="flex items-center gap-2 mb-4">
                        @for($i = 0; $i < 5; $i++)
                            <i class="fas fa-star text-yellow-500"></i>
                        @endfor
                    </div>
                    <p class="text-gray-400 mb-6 leading-relaxed flex-grow">
                        "1.5 ton kapasitesi günlük işlerimiz için yeterli. Günde ortalama 120-130 palet taşıma yapıyoruz, hiç sorun yaşamadık. Operatörler kullanım kolaylığından memnun. Fiyat/performans oranı dengeli."
                    </p>
                    <div class="flex items-center gap-3 mt-auto">
                        <div class="w-12 h-12 bg-yellow-600/20 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-user text-yellow-500"></i>
                        </div>
                        <div>
                            <p class="text-white font-bold">Kemal Yurtdaş</p>
                            <p class="text-gray-500 text-sm">Lojistik Şefi - Bursa</p>
                        </div>
                    </div>
                </div>

                <!-- Review 3 -->
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 p-8 rounded-2xl border border-gray-700 flex flex-col">
                    <div class="flex items-center gap-2 mb-4">
                        @for($i = 0; $i < 5; $i++)
                            <i class="fas fa-star text-yellow-500"></i>
                        @endfor
                    </div>
                    <p class="text-gray-400 mb-6 leading-relaxed flex-grow">
                        "3 aydır kullanımda, şimdiye kadar herhangi bir arıza yaşamadık. Servis desteği hızlı, küçük bir sorunumuzda aynı gün müdahale ettiler. Garanti kapsamı güven veriyor."
                    </p>
                    <div class="flex items-center gap-3 mt-auto">
                        <div class="w-12 h-12 bg-yellow-600/20 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-user text-yellow-500"></i>
                        </div>
                        <div>
                            <p class="text-white font-bold">İsmail Zor</p>
                            <p class="text-gray-500 text-sm">Atölye Müdürü - Kocaeli</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats -->
            <div class="max-w-4xl mx-auto bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-2xl p-8">
                <div class="grid lg:grid-cols-3 gap-6 text-center">
                    <div>
                        <div class="text-4xl font-black gold-gradient bg-clip-text text-transparent mb-2">4.9/5</div>
                        <div class="text-gray-400 text-sm">Ortalama Puan</div>
                    </div>
                    <div>
                        <div class="text-4xl font-black gold-gradient bg-clip-text text-transparent mb-2">150+</div>
                        <div class="text-gray-400 text-sm">Memnun Müşteri</div>
                    </div>
                    <div>
                        <div class="text-4xl font-black gold-gradient bg-clip-text text-transparent mb-2">%98</div>
                        <div class="text-gray-400 text-sm">Tavsiye Oranı</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ - Long-tail Keywords -->
    <section class="py-16 px-4 bg-black">
        <div class="container mx-auto max-w-4xl">
            <h2 class="text-4xl font-black mb-4 text-center">
                <span class="text-white">Elektrikli Transpalet</span>
                <span class="gold-gradient bg-clip-text text-transparent"> Sık Sorulan Sorular</span>
            </h2>
            <p class="text-center text-gray-400 mb-12">Akülü transpalet hakkında merak edilenler</p>

            <div class="space-y-4" x-data="{ openFaq: null }">
                <!-- FAQ 1 -->
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-xl border border-gray-700 overflow-hidden">
                    <button @click="openFaq = openFaq === 1 ? null : 1" class="w-full p-6 text-left flex items-center justify-between hover:bg-gray-800/50 transition-colors">
                        <h3 class="text-white font-bold pr-4">Elektrikli transpalet fiyatları ne kadar?</h3>
                        <i class="fas transition-transform duration-300" :class="openFaq === 1 ? 'fa-chevron-up text-yellow-500' : 'fa-chevron-down text-gray-400'"></i>
                    </button>
                    <div x-show="openFaq === 1" x-collapse class="px-6 pb-6">
                        <p class="text-gray-400">
                            İXTİF F4 elektrikli transpalet Kasım kampanyasında $1,250 (normal fiyat $1,560). 1 yıl garanti dahildir. Akülü transpalet fiyatları kapasite ve özelliklere göre değişir.
                        </p>
                    </div>
                </div>

                <!-- FAQ 2 -->
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-xl border border-gray-700 overflow-hidden">
                    <button @click="openFaq = openFaq === 2 ? null : 2" class="w-full p-6 text-left flex items-center justify-between hover:bg-gray-800/50 transition-colors">
                        <h3 class="text-white font-bold pr-4">Akülü transpalet şarj süresi ne kadar?</h3>
                        <i class="fas transition-transform duration-300" :class="openFaq === 2 ? 'fa-chevron-up text-yellow-500' : 'fa-chevron-down text-gray-400'"></i>
                    </button>
                    <div x-show="openFaq === 2" x-collapse class="px-6 pb-6">
                        <p class="text-gray-400">
                            Li-Ion bataryalı elektrikli transpalet 2-3 saat içinde tam şarj olur. Hızlı şarj özelliği sayesinde iş akışınızda minimum kesinti yaşarsınız.
                        </p>
                    </div>
                </div>

                <!-- FAQ 3 -->
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-xl border border-gray-700 overflow-hidden">
                    <button @click="openFaq = openFaq === 3 ? null : 3" class="w-full p-6 text-left flex items-center justify-between hover:bg-gray-800/50 transition-colors">
                        <h3 class="text-white font-bold pr-4">Terazili transpalet nedir?</h3>
                        <i class="fas transition-transform duration-300" :class="openFaq === 3 ? 'fa-chevron-up text-yellow-500' : 'fa-chevron-down text-gray-400'"></i>
                    </button>
                    <div x-show="openFaq === 3" x-collapse class="px-6 pb-6">
                        <p class="text-gray-400">
                            Terazili transpalet, entegre terazi sistemi olan transpalet çeşididir. Yükü tartarken taşıma imkanı sağlar. İXTİF F4 modelinde opsiyonel olarak sunulmaktadır.
                        </p>
                    </div>
                </div>

                <!-- FAQ 4 -->
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-xl border border-gray-700 overflow-hidden">
                    <button @click="openFaq = openFaq === 4 ? null : 4" class="w-full p-6 text-left flex items-center justify-between hover:bg-gray-800/50 transition-colors">
                        <h3 class="text-white font-bold pr-4">Denge tekerli transpalet avantajları nelerdir?</h3>
                        <i class="fas transition-transform duration-300" :class="openFaq === 4 ? 'fa-chevron-up text-yellow-500' : 'fa-chevron-down text-gray-400'"></i>
                    </button>
                    <div x-show="openFaq === 4" x-collapse class="px-6 pb-6">
                        <p class="text-gray-400">
                            Denge tekerli transpalet kompakt tasarımı ile dar alanlarda üstün manevra kabiliyeti sağlar. İXTİF F4'ün 1360 mm dönüş yarıçapı ile dar koridorlarda bile rahatça kullanabilirsiniz.
                        </p>
                    </div>
                </div>

                <!-- FAQ 5 -->
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-xl border border-gray-700 overflow-hidden">
                    <button @click="openFaq = openFaq === 5 ? null : 5" class="w-full p-6 text-left flex items-center justify-between hover:bg-gray-800/50 transition-colors">
                        <h3 class="text-white font-bold pr-4">Elektrikli transpalet bakımı nasıl yapılır?</h3>
                        <i class="fas transition-transform duration-300" :class="openFaq === 5 ? 'fa-chevron-up text-yellow-500' : 'fa-chevron-down text-gray-400'"></i>
                    </button>
                    <div x-show="openFaq === 5" x-collapse class="px-6 pb-6">
                        <p class="text-gray-400">
                            Akülü transpalet minimal bakım gerektirir. Li-Ion batarya teknolojisi ile su ilavesi gerekmez. Düzenli tekerlek kontrolü ve batarya bakımı yeterlidir. Teknik destek hizmetimiz mevcuttur.
                        </p>
                    </div>
                </div>

                <!-- FAQ 6 -->
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-xl border border-gray-700 overflow-hidden">
                    <button @click="openFaq = openFaq === 6 ? null : 6" class="w-full p-6 text-left flex items-center justify-between hover:bg-gray-800/50 transition-colors">
                        <h3 class="text-white font-bold pr-4">Transpalet garanti süresi ne kadar?</h3>
                        <i class="fas transition-transform duration-300" :class="openFaq === 6 ? 'fa-chevron-up text-yellow-500' : 'fa-chevron-down text-gray-400'"></i>
                    </button>
                    <div x-show="openFaq === 6" x-collapse class="px-6 pb-6">
                        <p class="text-gray-400">
                            İXTİF elektrikli transpalet 1 yıl garanti ile sunulmaktadır. Tüm parça ve işçilik ücretsizdir. Garanti sonrası yedek parça desteği devam eder.
                        </p>
                    </div>
                </div>

                <!-- FAQ 7 - New: Ucuz/Ekonomik -->
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-xl border border-gray-700 overflow-hidden">
                    <button @click="openFaq = openFaq === 7 ? null : 7" class="w-full p-6 text-left flex items-center justify-between hover:bg-gray-800/50 transition-colors">
                        <h3 class="text-white font-bold pr-4">Ucuz elektrikli transpalet var mı? En uygun fiyat nerede?</h3>
                        <i class="fas transition-transform duration-300" :class="openFaq === 7 ? 'fa-chevron-up text-yellow-500' : 'fa-chevron-down text-gray-400'"></i>
                    </button>
                    <div x-show="openFaq === 7" x-collapse class="px-6 pb-6">
                        <p class="text-gray-400">
                            İXTİF F4 elektrikli transpalet, piyasadaki en uygun fiyatlı ve ekonomik akülü transpalet seçeneklerinden biridir. Kasım kampanyasında $1,250 ile kaliteli ve ucuz elektrikli transpalet arayan firmalar için ideal çözüm. Premium kalite, uygun fiyat garantisi.
                        </p>
                    </div>
                </div>

                <!-- FAQ 8 -->
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-xl border border-gray-700 overflow-hidden">
                    <button @click="openFaq = openFaq === 8 ? null : 8" class="w-full p-6 text-left flex items-center justify-between hover:bg-gray-800/50 transition-colors">
                        <h3 class="text-white font-bold pr-4">Elektrikli transpalet kaç kg kaldırır?</h3>
                        <i class="fas transition-transform duration-300" :class="openFaq === 8 ? 'fa-chevron-up text-yellow-500' : 'fa-chevron-down text-gray-400'"></i>
                    </button>
                    <div x-show="openFaq === 8" x-collapse class="px-6 pb-6">
                        <p class="text-gray-400">
                            İXTİF F4 elektrikli transpalet 1500 kg (1.5 ton) kaldırma kapasitesine sahiptir. Bu kapasite, endüstriyel depolarda ve lojistik merkezlerinde günlük yük taşıma ihtiyaçlarını karşılamak için idealdir.
                        </p>
                    </div>
                </div>

                <!-- FAQ 9 -->
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-xl border border-gray-700 overflow-hidden">
                    <button @click="openFaq = openFaq === 9 ? null : 9" class="w-full p-6 text-left flex items-center justify-between hover:bg-gray-800/50 transition-colors">
                        <h3 class="text-white font-bold pr-4">Akülü transpalet nasıl çalışır?</h3>
                        <i class="fas transition-transform duration-300" :class="openFaq === 9 ? 'fa-chevron-up text-yellow-500' : 'fa-chevron-down text-gray-400'"></i>
                    </button>
                    <div x-show="openFaq === 9" x-collapse class="px-6 pb-6">
                        <p class="text-gray-400">
                            Elektrikli transpalet, Li-Ion batarya ile çalışan elektrik motoruyla paletleri kaldırıp taşır. Ergonomik kumanda kolu ile yön kontrolü ve hız ayarlaması yapılır. Hidrolik sistem paletleri yumuşak ve güvenli bir şekilde kaldırır.
                        </p>
                    </div>
                </div>

                <!-- FAQ 10 -->
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-xl border border-gray-700 overflow-hidden">
                    <button @click="openFaq = openFaq === 10 ? null : 10" class="w-full p-6 text-left flex items-center justify-between hover:bg-gray-800/50 transition-colors">
                        <h3 class="text-white font-bold pr-4">Elektrikli transpalet batarya ömrü ne kadar?</h3>
                        <i class="fas transition-transform duration-300" :class="openFaq === 10 ? 'fa-chevron-up text-yellow-500' : 'fa-chevron-down text-gray-400'"></i>
                    </button>
                    <div x-show="openFaq === 10" x-collapse class="px-6 pb-6">
                        <p class="text-gray-400">
                            Li-Ion batarya teknolojisi ile akülü transpalet bataryası ortalama 2000+ şarj döngüsü ömre sahiptir. Günde 1 şarj ile yaklaşık 5-7 yıl kesintisiz kullanım sağlar. Bakım gerektirmeyen batarya tasarımı uzun ömürlüdür.
                        </p>
                    </div>
                </div>

                <!-- FAQ 11 -->
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-xl border border-gray-700 overflow-hidden">
                    <button @click="openFaq = openFaq === 11 ? null : 11" class="w-full p-6 text-left flex items-center justify-between hover:bg-gray-800/50 transition-colors">
                        <h3 class="text-white font-bold pr-4">Elektrikli transpalet ne kadar hızlı gider?</h3>
                        <i class="fas transition-transform duration-300" :class="openFaq === 11 ? 'fa-chevron-up text-yellow-500' : 'fa-chevron-down text-gray-400'"></i>
                    </button>
                    <div x-show="openFaq === 11" x-collapse class="px-6 pb-6">
                        <p class="text-gray-400">
                            İXTİF F4 elektrikli transpalet 4.5 km/saat yüklü, 5 km/saat yüksüz hızla hareket eder. Bu hız, güvenli ve verimli çalışma için optimize edilmiştir. Dar alanlarda düşük hız, açık alanlarda yüksek hız modları mevcuttur.
                        </p>
                    </div>
                </div>

                <!-- FAQ 12 -->
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-xl border border-gray-700 overflow-hidden">
                    <button @click="openFaq = openFaq === 12 ? null : 12" class="w-full p-6 text-left flex items-center justify-between hover:bg-gray-800/50 transition-colors">
                        <h3 class="text-white font-bold pr-4">Elektrikli transpalet yedek parça bulunur mu?</h3>
                        <i class="fas transition-transform duration-300" :class="openFaq === 12 ? 'fa-chevron-up text-yellow-500' : 'fa-chevron-down text-gray-400'"></i>
                    </button>
                    <div x-show="openFaq === 12" x-collapse class="px-6 pb-6">
                        <p class="text-gray-400">
                            Evet, İXTİF elektrikli transpalet için tüm yedek parçalar stokta bulunmaktadır. Tekerlek, fren, batarya ve kumanda kolu gibi kritik parçalar anında temin edilebilir. 7/24 teknik destek ve hızlı yedek parça teslimatı garantisi.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Final CTA - Conversion Optimized -->
    <section class="py-20 px-4 bg-gradient-to-b from-black via-gray-900 to-black" id="iletisim-formu">
        <div class="container mx-auto max-w-5xl">
            <div class="bg-gradient-to-br from-gray-900 to-gray-800 border-2 border-yellow-600 rounded-3xl p-12 text-center">
                <h2 class="text-5xl font-black mb-4">
                    <span class="text-white">Elektrikli Transpalet</span>
                    <span class="gold-gradient bg-clip-text text-transparent"> Kampanyası</span>
                </h2>
                <p class="text-xl text-gray-400 mb-8">Akülü transpalet özel fiyatı - Kasım ayına özel %20 indirim</p>

                <!-- Price Box -->
                <div class="bg-black/30 border border-yellow-600/30 rounded-2xl p-8 mb-8">
                    <div class="flex items-center justify-center gap-6 flex-wrap mb-6">
                        <div class="text-center">
                            <p class="text-gray-500 mb-1 text-sm">Normal Fiyat</p>
                            <p class="text-3xl line-through text-gray-600">$1,560</p>
                        </div>
                        <i class="fas fa-arrow-right text-yellow-500 text-3xl"></i>
                        <div class="text-center">
                            <p class="text-yellow-500 font-bold mb-1">Kampanya Fiyatı</p>
                            <p class="text-6xl font-black gold-gradient bg-clip-text text-transparent">$1,250</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-center gap-2 text-sm text-gray-400">
                        <i class="fas fa-shield-alt text-yellow-500 text-xl"></i>
                        <span class="font-bold text-yellow-500">1 Yıl Garanti Dahil</span>
                    </div>
                </div>

                <!-- CTA Buttons -->
                <div class="space-y-4 mb-8">
                    <a href="{{ whatsapp_link('Elektrikli Transpalet Kampanya - Teknik Detay') }}"
                       class="inline-block px-12 py-6 gold-gradient rounded-full text-gray-950 font-black text-2xl hover:shadow-[0_0_50px_rgba(212,175,55,0.8)] transition-all">
                        <i class="fab fa-whatsapp mr-3"></i>
                        HEMEN SİPARİŞ VER
                    </a>

                    <p class="text-gray-500 text-sm">veya</p>

                    <div class="grid lg:grid-cols-2 gap-4 max-w-2xl mx-auto">
                        <a href="tel:{{ setting('contact_phone_1', '02167553555') }}"
                           class="flex items-center justify-center gap-3 bg-white/10 backdrop-blur-md p-4 rounded-xl border border-white/20 hover:bg-white/20 transition-all">
                            <i class="fas fa-phone text-2xl text-white"></i>
                            <div class="text-left">
                                <div class="text-white font-bold">Telefon ile Sipariş</div>
                                <div class="text-gray-400 text-sm">{{ setting('contact_phone_1', '0216 755 3 555') }}</div>
                            </div>
                        </a>
                        <a href="mailto:{{ setting('contact_email', 'info@ixtif.com') }}?subject=Elektrikli Transpalet Teklif"
                           class="flex items-center justify-center gap-3 bg-white/10 backdrop-blur-md p-4 rounded-xl border border-white/20 hover:bg-white/20 transition-all">
                            <i class="fas fa-envelope text-2xl text-white"></i>
                            <div class="text-left">
                                <div class="text-white font-bold">E-posta ile Teklif</div>
                                <div class="text-gray-400 text-sm">{{ setting('contact_email', 'info@ixtif.com') }}</div>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Urgency -->
                <div class="bg-yellow-600/10 border border-yellow-600/30 rounded-xl p-4">
                    <p class="text-yellow-500 font-bold">
                        <i class="fas fa-clock mr-2"></i>
                        Kampanya süresi sınırlı! Elektrikli transpalet özel fiyatından yararlanın.
                    </p>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts-footer')
    <!-- Countdown Script (Optimized) -->
    <script>
    (function() {
        'use strict';
        let endTime = null;

        function getRandomTime() {
            const minHours = 36;
            const maxHours = 48;
            const randomHours = Math.floor(Math.random() * (maxHours - minHours + 1)) + minHours;
            const randomMinutes = Math.floor(Math.random() * 60);
            const randomSeconds = Math.floor(Math.random() * 60);
            const totalMs = (randomHours * 3600000) + (randomMinutes * 60000) + (randomSeconds * 1000);
            return Date.now() + totalMs;
        }

        function resetCountdown() {
            endTime = getRandomTime();
            try {
                localStorage.setItem('campaignEndTime', endTime);
            } catch(e) {}
        }

        function pad(num) {
            return (num < 10 ? '0' : '') + num;
        }

        function updateCountdown() {
            const now = Date.now();
            const diff = endTime - now;

            if (diff <= 7200000 && diff > 0) {
                resetCountdown();
                return;
            }

            if (diff < 0) {
                resetCountdown();
                return;
            }

            const totalSeconds = Math.floor(diff / 1000);
            const days = Math.floor(totalSeconds / 86400);
            const hours = Math.floor((totalSeconds % 86400) / 3600);
            const minutes = Math.floor((totalSeconds % 3600) / 60);
            const seconds = totalSeconds % 60;

            document.getElementById('countdown-days').textContent = pad(days);
            document.getElementById('countdown-hours').textContent = pad(hours);
            document.getElementById('countdown-minutes').textContent = pad(minutes);
            document.getElementById('countdown-seconds').textContent = pad(seconds);
        }

        try {
            const stored = localStorage.getItem('campaignEndTime');
            endTime = stored ? parseInt(stored) : getRandomTime();
            if (!stored) localStorage.setItem('campaignEndTime', endTime);
        } catch(e) {
            endTime = getRandomTime();
        }

        updateCountdown();
        setInterval(updateCountdown, 1000);
    })();
    </script>
@endpush

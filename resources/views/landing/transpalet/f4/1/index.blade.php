@extends('layouts.landing.minimal')

@push('meta')
    <!-- PRIMARY SEO - Exact Keyword Match -->
    <title>Elektrikli Transpalet Satın Al | Satılık Transpalet | Uygun Fiyat | İXTİF</title>
    <meta name="description" content="✓ Elektrikli transpalet satın al ✓ Satılık transpalet ✓ Şarjlı transpalet ✓ Terazili transpalet ✓ Transpalet fiyatları ✓ Depo için transpalet | 1500 kg Li-Ion | Kasım kampanyası $1,250 | 1 yıl garanti">
    <meta name="keywords" content="elektrikli transpalet satın al, satılık transpalet, elektrikli transpalet fiyatları, transpalet fiyatları, şarjlı transpalet, terazili transpalet, depo için transpalet, akülü transpalet, elektrikli transpalet, transpalet, istanbul transpalet, kocaeli transpalet, transpalet jungheinrich, still transpalet, en uygun fiyatlı transpaletler, stoktan teslim transpaletler, garantili terazili transpalet, yüksek performanslı transpalet, dar alanlar için transpaletler, depo içi elektrikli transpalet, soğuk hava deposuna transpalet, elektrikli şantiye transpaleti">

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
    <!-- Schema.org Product Markup - Comprehensive -->
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "Product",
        "name": "iXtif F4 Elektrikli Transpalet",
        "description": "1500 kg kapasiteli elektrikli transpalet, akülü transpalet. 24V 20Ah Li-Ion bataryalı, kompakt tasarım, terazili ve denge tekerlekli.",
        "sku": "IXTIF-F4-EP-1500",
        "mpn": "F4-EP-1500KG",
        "brand": {
            "@@type": "Brand",
            "name": "iXtif"
        },
        "manufacturer": {
            "@@type": "Organization",
            "name": "İXTİF İç ve Dış Ticaret A.Ş."
        },
        "offers": {
            "@@type": "Offer",
            "price": "1250",
            "priceCurrency": "USD",
            "availability": "https://schema.org/InStock",
            "priceValidUntil": "2025-12-31",
            "url": "{{ url('/elektrikli-transpalet') }}",
            "seller": {
                "@@type": "Organization",
                "name": "İXTİF"
            },
            "itemCondition": "https://schema.org/NewCondition",
            "warranty": "1 yıl üretici garantisi dahil"
        },
        "aggregateRating": {
            "@@type": "AggregateRating",
            "ratingValue": "4.9",
            "bestRating": "5",
            "worstRating": "1",
            "reviewCount": "150"
        },
        "image": [
            "https://ixtif.com/storage/tenant2/82/lep4ns74ctsp2gydkwetkw7xs1y01psfss4tf5u6.png"
        ],
        "category": "Transpalet",
        "additionalProperty": [
            {
                "@@type": "PropertyValue",
                "name": "Kapasite",
                "value": "1500 kg"
            },
            {
                "@@type": "PropertyValue",
                "name": "Batarya",
                "value": "24V 20Ah Li-Ion"
            },
            {
                "@@type": "PropertyValue",
                "name": "Şarj Süresi",
                "value": "2-3 saat"
            },
            {
                "@@type": "PropertyValue",
                "name": "Hız",
                "value": "4.5-5 km/saat"
            }
        ]
    }
    </script>

    <!-- Schema.org Organization -->
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "Organization",
        "name": "İXTİF İç ve Dış Ticaret A.Ş.",
        "url": "{{ url('/') }}",
        "logo": "{{ setting('site_logo_url') ?? url('/logo.png') }}",
        "sameAs": [
            "https://www.facebook.com/ixtif",
            "https://www.instagram.com/ixtif"
        ],
        "contactPoint": {
            "@@type": "ContactPoint",
            "telephone": "{{ setting('contact_phone_1', '+90-216-755-3555') }}",
            "contactType": "Sales",
            "areaServed": "TR",
            "availableLanguage": ["Turkish", "English"]
        },
        "address": {
            "@@type": "PostalAddress",
            "addressCountry": "TR",
            "addressLocality": "İstanbul"
        }
    }
    </script>

    <!-- Schema.org BreadcrumbList -->
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "BreadcrumbList",
        "itemListElement": [
            {
                "@@type": "ListItem",
                "position": 1,
                "name": "Anasayfa",
                "item": "{{ url('/') }}"
            },
            {
                "@@type": "ListItem",
                "position": 2,
                "name": "Shop",
                "item": "{{ url('/shop') }}"
            },
            {
                "@@type": "ListItem",
                "position": 3,
                "name": "Elektrikli Transpalet",
                "item": "{{ url('/elektrikli-transpalet') }}"
            }
        ]
    }
    </script>

    <!-- Schema.org FAQPage -->
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "FAQPage",
        "mainEntity": [
            {
                "@@type": "Question",
                "name": "Elektrikli transpalet fiyatları ne kadar?",
                "acceptedAnswer": {
                    "@@type": "Answer",
                    "text": "İXTİF F4 elektrikli transpalet Kasım kampanyasında $1,250 (normal fiyat $1,560). 1 yıl garanti dahildir. Akülü transpalet fiyatları kapasite ve özelliklere göre değişir."
                }
            },
            {
                "@@type": "Question",
                "name": "Akülü transpalet şarj süresi ne kadar?",
                "acceptedAnswer": {
                    "@@type": "Answer",
                    "text": "Li-Ion bataryalı elektrikli transpalet 2-3 saat içinde tam şarj olur. Hızlı şarj özelliği sayesinde iş akışınızda minimum kesinti yaşarsınız."
                }
            },
            {
                "@@type": "Question",
                "name": "Elektrikli transpalet kapasitesi nedir?",
                "acceptedAnswer": {
                    "@@type": "Answer",
                    "text": "İXTİF F4 elektrikli transpalet 1500 kg (1.5 ton) yük taşıma kapasitesine sahiptir. Endüstriyel kullanım için ideal güvenli kapasite."
                }
            },
            {
                "@@type": "Question",
                "name": "Transpalet garanti süresi ne kadar?",
                "acceptedAnswer": {
                    "@@type": "Answer",
                    "text": "İXTİF elektrikli transpalet 1 yıl üretici garantisi ile satılır. Garanti kapsamında arızalı parçalar ücretsiz değiştirilir."
                }
            },
            {
                "@@type": "Question",
                "name": "Elektrikli transpalet ne kadar hızlı gider?",
                "acceptedAnswer": {
                    "@@type": "Answer",
                    "text": "İXTİF F4 elektrikli transpalet 4.5 km/saat yüklü, 5 km/saat yüksüz hızla hareket eder. Bu hız, güvenli ve verimli çalışma için optimize edilmiştir."
                }
            }
        ]
    }
    </script>

    <!-- Schema.org WebPage -->
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "WebPage",
        "name": "Elektrikli Transpalet - Akülü Transpalet Kampanya",
        "description": "Elektrikli transpalet ve akülü transpalet çözümleri. 1500 kg Li-Ion bataryalı, kompakt tasarım. Kasım kampanyası!",
        "url": "{{ url('/elektrikli-transpalet') }}",
        "inLanguage": "tr-TR",
        "isPartOf": {
            "@@type": "WebSite",
            "name": "İXTİF",
            "url": "{{ url('/') }}"
        },
        "breadcrumb": {
            "@@type": "BreadcrumbList",
            "itemListElement": [
                {
                    "@@type": "ListItem",
                    "position": 1,
                    "name": "Anasayfa",
                    "item": "{{ url('/') }}"
                },
                {
                    "@@type": "ListItem",
                    "position": 2,
                    "name": "Elektrikli Transpalet"
                }
            ]
        }
    }
    </script>
@endpush

@section('content')
@php
    // Contact Settings (Header ile aynı) - Fallback values
    $contactPhone = setting('contact_phone_1', '0216 755 35 55');
    $contactWhatsapp = setting('contact_whatsapp_1', '905309555885');
@endphp

    <!-- Hero Section - Above the fold optimization -->
    <section class="py-10 sm:py-12 lg:py-14 px-4 bg-gradient-to-b from-gray-950 via-gray-900 to-black">
        <div class="container mx-auto max-w-7xl">
            <div class="grid lg:grid-cols-2 gap-6 sm:gap-8 lg:gap-12 items-center">
                <!-- Left Content -->
                <div>
                    <!-- H1 - Primary Keyword -->
                    <h1 class="text-5xl sm:text-6xl lg:text-7xl font-black mb-3 sm:mb-4 lg:mb-6 leading-tight">
                        <span class="gold-gradient-strong bg-clip-text text-transparent">Elektrikli Transpalet</span>
                        <span class="block text-white text-4xl sm:text-5xl lg:text-6xl mt-2 sm:mt-3">1500 kg Kapasite</span>
                    </h1>
                    <p class="text-base sm:text-lg lg:text-xl text-gray-300 mb-3 sm:mb-4">
                        8 Saat Kesintisiz • Hızlı Şarj • Düşük Maliyet
                    </p>

                    <!-- Trust Badge - Şirket Garantisi -->
                    <div class="flex items-center gap-2 text-xs sm:text-sm text-gray-400 mb-4 sm:mb-6">
                        <i class="fas fa-shield-check text-yellow-500"></i>
                        <span>İXTİF A.Ş. Garantisiyle</span>
                    </div>

                    <!-- Price - Clear CTA with Animation -->
                    <div class="bg-gradient-to-br from-gray-900 to-gray-800 p-3 sm:p-4 lg:p-6 rounded-xl sm:rounded-2xl border-2 border-yellow-600 mb-3 sm:mb-4"
                         x-data="{
                             showTL: false,
                             priceTimer: null,
                             init() {
                                 this.startPriceCycle();
                             },
                             startPriceCycle() {
                                 this.priceTimer = setInterval(() => {
                                     // TL'ye geç (1.5 saniye)
                                     this.showTL = true;
                                     setTimeout(() => {
                                         this.showTL = false;
                                     }, 1500);
                                 }, 4500); // Her 4.5 saniyede bir döngü (3s USD + 1.5s TL)
                             },
                             destroy() {
                                 if (this.priceTimer) clearInterval(this.priceTimer);
                             }
                         }">
                        <div class="flex items-baseline gap-2 mb-1 sm:mb-2">
                            <span class="text-gray-500 line-through text-sm sm:text-base lg:text-lg">$1,560</span>
                            <span class="text-yellow-500 font-black text-[10px] sm:text-xs lg:text-sm bg-yellow-600/20 px-1.5 sm:px-2 py-0.5 sm:py-1 rounded">-20%</span>
                        </div>
                        <div class="flex items-baseline gap-2 mb-2 sm:mb-3">
                            <div class="relative inline-block min-w-[140px] sm:min-w-[180px] lg:min-w-[220px]">
                                <span x-show="!showTL" class="absolute left-0 top-0 text-2xl sm:text-3xl lg:text-4xl font-black gold-gradient bg-clip-text text-transparent">$1,250</span>
                                <span x-show="showTL" class="absolute left-0 top-0 text-2xl sm:text-3xl lg:text-4xl font-black gold-gradient bg-clip-text text-transparent">52.500 ₺</span>
                                <span class="invisible text-2xl sm:text-3xl lg:text-4xl font-black">52.500 ₺</span>
                            </div>
                        </div>
                        <div class="flex flex-wrap items-center gap-2 sm:gap-3 text-[10px] sm:text-xs">
                            <span class="text-yellow-500">
                                <i class="fas fa-shield-alt mr-1"></i> 1 Yıl Garanti
                            </span>
                            <span class="text-yellow-500">
                                <i class="fas fa-box-open mr-1"></i> Stoktan Teslim
                            </span>
                        </div>
                    </div>

                    <!-- Countdown (Dynamic) -->
                    <div id="countdown" class="grid grid-cols-4 gap-1 sm:gap-1.5 mb-3 sm:mb-4">
                        <div class="bg-black/50 p-1.5 sm:p-2 rounded-lg border border-yellow-600/30 text-center">
                            <div class="text-xl sm:text-2xl font-black gold-gradient bg-clip-text text-transparent" id="countdown-days">01</div>
                            <div class="text-gray-400 text-[10px] sm:text-xs">Gün</div>
                        </div>
                        <div class="bg-black/50 p-1.5 sm:p-2 rounded-lg border border-yellow-600/30 text-center">
                            <div class="text-xl sm:text-2xl font-black gold-gradient bg-clip-text text-transparent" id="countdown-hours">18</div>
                            <div class="text-gray-400 text-[10px] sm:text-xs">Saat</div>
                        </div>
                        <div class="bg-black/50 p-1.5 sm:p-2 rounded-lg border border-yellow-600/30 text-center">
                            <div class="text-xl sm:text-2xl font-black gold-gradient bg-clip-text text-transparent" id="countdown-minutes">45</div>
                            <div class="text-gray-400 text-[10px] sm:text-xs">Dakika</div>
                        </div>
                        <div class="bg-black/50 p-1.5 sm:p-2 rounded-lg border border-yellow-600/30 text-center">
                            <div class="text-xl sm:text-2xl font-black gold-gradient bg-clip-text text-transparent" id="countdown-seconds">23</div>
                            <div class="text-gray-400 text-[10px] sm:text-xs">Saniye</div>
                        </div>
                    </div>

                    <!-- Primary CTA -->
                    <div class="grid grid-cols-3 gap-2 sm:gap-3">
                        <a href="{{ whatsapp_link(null, 'Elektrikli Transpalet Kampanya') }}"
                           target="_blank"
                           class="px-2 py-3 sm:px-6 sm:py-4 bg-gradient-to-br from-gray-800 to-gray-900 border-2 border-yellow-600 hover:border-yellow-500 rounded-xl text-white text-center text-xs sm:text-base font-bold transition-all flex flex-col sm:flex-row items-center justify-center gap-1 sm:gap-2">
                            <i class="fab fa-whatsapp text-yellow-500 text-lg sm:text-xl"></i>
                            <span class="text-[10px] sm:text-base">WhatsApp</span>
                        </a>

                        <a href="tel:{{ str_replace(' ', '', $contactPhone) }}"
                           class="px-2 py-3 sm:px-6 sm:py-4 bg-gradient-to-br from-gray-800 to-gray-900 border-2 border-yellow-600 hover:border-yellow-500 rounded-xl text-white text-center text-xs sm:text-base font-bold transition-all flex flex-col sm:flex-row items-center justify-center gap-1 sm:gap-2">
                            <i class="fas fa-phone text-yellow-500 text-lg sm:text-xl"></i>
                            <span class="text-[10px] sm:text-base">Telefon</span>
                        </a>

                        <a href="https://ixtif.com/sizi-arayalim"
                           target="_blank"
                           class="px-2 py-3 sm:px-6 sm:py-4 gold-gradient rounded-xl text-gray-950 text-center text-xs sm:text-base font-bold transition-all flex flex-col sm:flex-row items-center justify-center gap-1 sm:gap-2 hover:shadow-[0_0_20px_rgba(212,175,55,0.5)]">
                            <i class="fas fa-headset text-gray-950 text-lg sm:text-xl"></i>
                            <span class="text-[10px] sm:text-base">Sizi Arayalım</span>
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

    <!-- Product Gallery - Lightbox -->
    <section class="py-10 sm:py-12 lg:py-14 px-4 bg-gradient-to-b from-black via-gray-900 to-black">
        <div class="container mx-auto max-w-7xl">
            <h2 class="text-2xl sm:text-3xl lg:text-4xl font-black mb-2 sm:mb-3 text-center">
                <span class="gold-gradient bg-clip-text text-transparent">Ürün Görselleri</span>
            </h2>
            <p class="text-center text-gray-400 text-xs sm:text-sm mb-6 sm:mb-8">Elektrikli Transpalet Detay Fotoğrafları</p>

            <!-- Gallery Grid -->
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6" id="product-gallery">
                <!-- Gallery Images (Generated by JavaScript) -->
            </div>
        </div>

        <!-- Lightbox Modal -->
        <div id="lightbox-modal" class="fixed inset-0 z-50 bg-black/95 items-center justify-center p-4 hidden">
            <!-- Overlay - Boşluğa tıklayınca kapat -->
            <div class="absolute inset-0" onclick="closeLightbox()"></div>

            <!-- Close Button -->
            <button onclick="closeLightbox()"
                    class="absolute top-4 right-4 z-10 w-10 h-10 sm:w-12 sm:h-12 bg-gray-900/90 hover:bg-yellow-600 text-white rounded-full flex items-center justify-center transition-all">
                <i class="fas fa-times text-lg sm:text-xl"></i>
            </button>

            <!-- Image Counter -->
            <div class="absolute top-4 left-4 z-10 bg-gray-900/90 px-3 py-2 rounded-lg text-white text-xs sm:text-sm">
                <span id="lightbox-counter">1 / 8</span>
            </div>

            <!-- Image Container -->
            <div class="relative max-w-6xl w-full flex items-center justify-center z-10">
                <!-- Previous Button -->
                <button onclick="changeImage(-1)"
                        class="absolute left-2 sm:left-4 z-10 w-10 h-10 sm:w-12 sm:h-12 bg-gray-900/90 hover:bg-yellow-600 text-white rounded-full flex items-center justify-center transition-all">
                    <i class="fas fa-chevron-left text-base sm:text-lg"></i>
                </button>

                <!-- Image -->
                <div class="max-h-[80vh] flex items-center justify-center">
                    <img id="lightbox-image"
                         src=""
                         alt="Elektrikli Transpalet"
                         class="max-w-full max-h-[80vh] object-contain rounded-lg">
                </div>

                <!-- Next Button -->
                <button onclick="changeImage(1)"
                        class="absolute right-2 sm:right-4 z-10 w-10 h-10 sm:w-12 sm:h-12 bg-gray-900/90 hover:bg-yellow-600 text-white rounded-full flex items-center justify-center transition-all">
                    <i class="fas fa-chevron-right text-base sm:text-lg"></i>
                </button>
            </div>
        </div>
    </section>

    <script>
        // Galeri sistemi - Vanilla JS (Alpine'dan bağımsız)
        const galleryImages = [
            'https://ixtif.com/storage/tenant2/137/1.jpg',
            'https://ixtif.com/storage/tenant2/138/2.jpg',
            'https://ixtif.com/storage/tenant2/139/3.jpg',
            'https://ixtif.com/storage/tenant2/142/6.jpg',
            'https://ixtif.com/storage/tenant2/141/5.jpg',
            'https://ixtif.com/storage/tenant2/143/7.jpg',
            'https://ixtif.com/storage/tenant2/140/4.jpg',
            'https://ixtif.com/storage/tenant2/144/8.jpg'
        ];

        let currentImageIndex = 0;

        // Galeriyi oluştur
        function initGallery() {
            const gallery = document.getElementById('product-gallery');
            if (!gallery) return;

            galleryImages.forEach((image, index) => {
                const div = document.createElement('div');
                div.className = 'relative aspect-square bg-gradient-to-br from-gray-900 to-gray-800 rounded-xl border border-gray-700 overflow-hidden cursor-pointer group hover:border-yellow-600 transition-all';
                div.onclick = () => openLightbox(index);

                div.innerHTML = `
                    <img src="${image}"
                         alt="Elektrikli Transpalet Görsel ${index + 1}"
                         class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110"
                         loading="lazy">
                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/60 transition-all flex items-center justify-center">
                        <i class="fas fa-search-plus text-white text-2xl opacity-0 group-hover:opacity-100 transition-opacity"></i>
                    </div>
                `;

                gallery.appendChild(div);
            });
        }

        // Lightbox'ı aç
        function openLightbox(index) {
            currentImageIndex = index;
            const modal = document.getElementById('lightbox-modal');
            const image = document.getElementById('lightbox-image');
            const counter = document.getElementById('lightbox-counter');

            image.src = galleryImages[currentImageIndex];
            counter.textContent = `${currentImageIndex + 1} / ${galleryImages.length}`;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        // Lightbox'ı kapat
        function closeLightbox() {
            const modal = document.getElementById('lightbox-modal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = 'auto';
        }

        // Görsel değiştir
        function changeImage(direction) {
            currentImageIndex += direction;

            if (currentImageIndex >= galleryImages.length) {
                currentImageIndex = 0;
            } else if (currentImageIndex < 0) {
                currentImageIndex = galleryImages.length - 1;
            }

            const image = document.getElementById('lightbox-image');
            const counter = document.getElementById('lightbox-counter');

            image.src = galleryImages[currentImageIndex];
            counter.textContent = `${currentImageIndex + 1} / ${galleryImages.length}`;
        }

        // Klavye kontrolleri
        document.addEventListener('keydown', function(event) {
            const modal = document.getElementById('lightbox-modal');
            if (!modal.classList.contains('hidden')) {
                if (event.key === 'Escape') {
                    closeLightbox();
                } else if (event.key === 'ArrowLeft') {
                    changeImage(-1);
                } else if (event.key === 'ArrowRight') {
                    changeImage(1);
                }
            }
        });

        // Sayfa yüklendiğinde galeriyi başlat
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initGallery);
        } else {
            initGallery();
        }
    </script>

    <!-- Technical Specs - SEO Optimized -->
    <section class="py-10 sm:py-12 lg:py-14 px-4 bg-black">
        <div class="container mx-auto max-w-7xl">
            <h2 class="text-2xl sm:text-3xl lg:text-4xl font-black mb-2 sm:mb-3 text-center">
                <span class="gold-gradient bg-clip-text text-transparent">Teknik Özellikler</span>
            </h2>
            <p class="text-center text-gray-400 text-xs sm:text-sm mb-6 sm:mb-8">F4 Model Detayları</p>

            <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-6 sm:mb-8">
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 p-3 sm:p-4 rounded-xl border border-gray-700 text-center">
                    <div class="text-2xl sm:text-3xl lg:text-4xl font-black gold-gradient bg-clip-text text-transparent mb-2">1500 kg</div>
                    <div class="text-gray-400 text-xs sm:text-sm">Yük Kapasitesi</div>
                    <p class="text-[10px] sm:text-xs text-gray-500 mt-1 sm:mt-2">Elektrikli transpalet kapasitesi</p>
                </div>
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 p-3 sm:p-4 rounded-xl border border-gray-700 text-center">
                    <div class="text-2xl sm:text-3xl lg:text-4xl font-black gold-gradient bg-clip-text text-transparent mb-2">24V 20Ah</div>
                    <div class="text-gray-400 text-xs sm:text-sm">Li-Ion Batarya</div>
                    <p class="text-[10px] sm:text-xs text-gray-500 mt-1 sm:mt-2">Akülü transpalet batarya gücü</p>
                </div>
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 p-3 sm:p-4 rounded-xl border border-gray-700 text-center">
                    <div class="text-2xl sm:text-3xl lg:text-4xl font-black gold-gradient bg-clip-text text-transparent mb-2">4.5 km/h</div>
                    <div class="text-gray-400 text-xs sm:text-sm">Maksimum Hız</div>
                    <p class="text-[10px] sm:text-xs text-gray-500 mt-1 sm:mt-2">Transpalet hız performansı</p>
                </div>
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 p-3 sm:p-4 rounded-xl border border-gray-700 text-center">
                    <div class="text-2xl sm:text-3xl lg:text-4xl font-black gold-gradient bg-clip-text text-transparent mb-2">1360 mm</div>
                    <div class="text-gray-400 text-xs sm:text-sm">Dönüş Yarıçapı</div>
                    <p class="text-[10px] sm:text-xs text-gray-500 mt-1 sm:mt-2">Denge tekerli kompakt tasarım</p>
                </div>
            </div>

            <!-- Detailed Specs Table -->
            <div class="grid md:grid-cols-2 gap-4 sm:gap-6">
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 p-3 sm:p-4 rounded-xl border border-gray-700">
                    <h3 class="text-sm sm:text-base lg:text-lg font-bold mb-2 sm:mb-3 gold-gradient bg-clip-text text-transparent">Ana Özellikler</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between items-center py-2 border-b border-gray-700">
                            <span class="text-gray-400 text-xs sm:text-sm">Tip</span>
                            <span class="text-white font-bold text-xs sm:text-sm">Elektrikli Transpalet</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-700">
                            <span class="text-gray-400 text-xs sm:text-sm">Kapasite</span>
                            <span class="text-white font-bold text-xs sm:text-sm">1500 kg</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-700">
                            <span class="text-gray-400 text-xs sm:text-sm">Batarya</span>
                            <span class="text-white font-bold text-xs sm:text-sm">24V 20Ah Li-Ion</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-700">
                            <span class="text-gray-400 text-xs sm:text-sm">Dönüş Yarıçapı</span>
                            <span class="text-white font-bold text-xs sm:text-sm">1360 mm</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-700">
                            <span class="text-gray-400 text-xs sm:text-sm">Ağırlık</span>
                            <span class="text-white font-bold text-xs sm:text-sm">120 kg</span>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-gray-900 to-gray-800 p-3 sm:p-4 rounded-xl border border-gray-700">
                    <h3 class="text-sm sm:text-base lg:text-lg font-bold mb-2 sm:mb-3 gold-gradient bg-clip-text text-transparent">Performans</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between items-center py-2 border-b border-gray-700">
                            <span class="text-gray-400 text-xs sm:text-sm">Hız (yük/boş)</span>
                            <span class="text-white font-bold text-xs sm:text-sm">4.0 / 4.5 km/s</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-700">
                            <span class="text-gray-400 text-xs sm:text-sm">Eğim (yük/boş)</span>
                            <span class="text-white font-bold text-xs sm:text-sm">%6 / %16</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-700">
                            <span class="text-gray-400 text-xs sm:text-sm">Motor</span>
                            <span class="text-white font-bold text-xs sm:text-sm">0.75 kW</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-700">
                            <span class="text-gray-400 text-xs sm:text-sm">Şarj</span>
                            <span class="text-white font-bold text-xs sm:text-sm">2-3 saat</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-700">
                            <span class="text-gray-400 text-xs sm:text-sm">Ses</span>
                            <span class="text-white font-bold text-xs sm:text-sm">74 dB(A)</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Benefits - Keyword Rich Content -->
    <section class="py-10 sm:py-12 lg:py-14 px-4 bg-gradient-to-b from-black via-gray-900 to-black">
        <div class="container mx-auto max-w-7xl">
            <h2 class="text-2xl sm:text-3xl lg:text-4xl font-black mb-2 sm:mb-3 text-center">
                <span class="gold-gradient bg-clip-text text-transparent">Neden Tercih Edilmeli?</span>
            </h2>
            <p class="text-center text-gray-400 text-xs sm:text-sm mb-6 sm:mb-8">Ana Avantajlar</p>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 p-3 sm:p-4 rounded-xl border border-gray-700 flex gap-3 sm:gap-4 min-h-[120px]">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-yellow-600/20 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-medal text-yellow-500 text-base sm:text-lg"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-sm sm:text-base lg:text-lg font-bold mb-1 sm:mb-2 text-white">Kalite ve Güvenilirlik</h3>
                        <p class="text-gray-400 text-xs sm:text-sm leading-relaxed">
                            Sağlam yapı, dayanıklı malzeme ve uzun ömürlü Li-Ion batarya teknolojisi. 1 yıl garanti kapsamı.
                        </p>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-gray-900 to-gray-800 p-3 sm:p-4 rounded-xl border border-gray-700 flex gap-3 sm:gap-4 min-h-[120px]">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-yellow-600/20 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-dollar-sign text-yellow-500 text-base sm:text-lg"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-sm sm:text-base lg:text-lg font-bold mb-1 sm:mb-2 text-white">Ekonomik Fiyat</h3>
                        <p class="text-gray-400 text-xs sm:text-sm leading-relaxed">
                            Uygun fiyatlarla yüksek kalite. Düşük işletme maliyeti, az bakım ihtiyacı.
                        </p>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-gray-900 to-gray-800 p-3 sm:p-4 rounded-xl border border-gray-700 flex gap-3 sm:gap-4 min-h-[120px]">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-yellow-600/20 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-tachometer-alt text-yellow-500 text-base sm:text-lg"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-sm sm:text-base lg:text-lg font-bold mb-1 sm:mb-2 text-white">Yüksek Performans</h3>
                        <p class="text-gray-400 text-xs sm:text-sm leading-relaxed">
                            1500 kg kapasite, 4.5 km/h hız. Dar alanlarda üstün manevra kabiliyeti.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Hizmet Avantajları ve Güvenceler -->
    <section class="py-10 sm:py-12 lg:py-14 px-4 bg-gradient-to-b from-black via-gray-900 to-black">
        <div class="container mx-auto max-w-7xl">
            <h2 class="text-2xl sm:text-3xl lg:text-4xl font-black mb-2 sm:mb-3 text-center">
                <span class="gold-gradient bg-clip-text text-transparent">Hizmet Avantajları</span>
            </h2>
            <p class="text-center text-gray-400 text-xs sm:text-sm mb-6 sm:mb-8">%100 Müşteri Memnuniyeti</p>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                <!-- Avantaj 1 -->
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 p-3 sm:p-4 rounded-xl border border-gray-700 flex gap-3 sm:gap-4 min-h-[120px]">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-yellow-600/20 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-shipping-fast text-yellow-500 text-lg sm:text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-sm sm:text-base lg:text-lg font-bold text-white mb-1 sm:mb-2">Stoktan Teslim</h3>
                        <p class="text-gray-400 text-xs sm:text-sm leading-relaxed">
                            Stoktan anında teslimat. İstanbul ve Kocaeli'ne hızlı kargo.
                        </p>
                    </div>
                </div>

                <!-- Avantaj 2 -->
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 p-3 sm:p-4 rounded-xl border border-gray-700 flex gap-3 sm:gap-4 min-h-[120px]">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-yellow-600/20 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-building text-yellow-500 text-base sm:text-lg"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-sm sm:text-base lg:text-lg font-bold text-white mb-1 sm:mb-2">Özel Uygulamalar</h3>
                        <p class="text-gray-400 text-xs sm:text-sm leading-relaxed">
                            Depo, şantiye, soğuk hava deposu ve dar alanlar için özel çözümler.
                        </p>
                    </div>
                </div>

                <!-- Avantaj 3 -->
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 p-3 sm:p-4 rounded-xl border border-gray-700 flex gap-3 sm:gap-4 min-h-[120px]">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-yellow-600/20 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-dollar-sign text-yellow-500 text-base sm:text-lg"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-sm sm:text-base lg:text-lg font-bold text-white mb-1 sm:mb-2">Uygun Fiyat</h3>
                        <p class="text-gray-400 text-xs sm:text-sm leading-relaxed">
                            En uygun seviyede fiyatlandırma. %40 daha ekonomik.
                        </p>
                    </div>
                </div>

                <!-- Avantaj 4 -->
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 p-3 sm:p-4 rounded-xl border border-gray-700 flex gap-3 sm:gap-4 min-h-[120px]">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-yellow-600/20 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-trophy text-yellow-500 text-base sm:text-lg"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-sm sm:text-base lg:text-lg font-bold text-white mb-1 sm:mb-2">Yüksek Performans</h3>
                        <p class="text-gray-400 text-xs sm:text-sm leading-relaxed">
                            1500 kg kapasite, Li-Ion teknolojisi ile kesintisiz çalışma.
                        </p>
                    </div>
                </div>

                <!-- Avantaj 5 -->
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 p-3 sm:p-4 rounded-xl border border-gray-700 flex gap-3 sm:gap-4 min-h-[120px]">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-yellow-600/20 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-award text-yellow-500 text-base sm:text-lg"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-sm sm:text-base lg:text-lg font-bold text-white mb-1 sm:mb-2">Garanti & Destek</h3>
                        <p class="text-gray-400 text-xs sm:text-sm leading-relaxed">
                            1 yıl garanti dahil, 7/24 teknik destek.
                        </p>
                    </div>
                </div>

                <!-- Avantaj 6 -->
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 p-3 sm:p-4 rounded-xl border border-gray-700 flex gap-3 sm:gap-4 min-h-[120px]">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-yellow-600/20 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-handshake text-yellow-500 text-base sm:text-lg"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-sm sm:text-base lg:text-lg font-bold text-white mb-1 sm:mb-2">Kolay Satın Alma</h3>
                        <p class="text-gray-400 text-xs sm:text-sm leading-relaxed">
                            Şeffaf fiyatlandırma, net ödeme seçenekleri.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Social Proof - Reviews -->
    <section class="py-10 sm:py-12 lg:py-14 px-4 bg-black">
        <div class="container mx-auto max-w-7xl">
            <h2 class="text-2xl sm:text-3xl lg:text-4xl font-black mb-2 sm:mb-3 text-center">
                <span class="text-white">Müşteri</span>
                <span class="gold-gradient bg-clip-text text-transparent"> Yorumları</span>
            </h2>
            <p class="text-center text-gray-400 text-xs sm:text-sm mb-6 sm:mb-8">Elektrikli transpalet kullanan müşterilerimizin deneyimleri</p>

            <!-- Swiper Carousel with Gradient Fade -->
            <div class="mb-6 sm:mb-8">
                <div class="relative before:absolute before:bottom-0 before:left-0 before:top-0 before:z-10 before:w-16 md:before:w-36 before:bg-gradient-to-r before:from-black before:via-gray-900/80 before:to-transparent after:absolute after:bottom-0 after:right-0 after:top-0 after:z-10 after:w-16 md:after:w-36 after:bg-gradient-to-l after:from-black after:via-gray-900/80 after:to-transparent">
                    <div class="swiper reviewsSwiper">
                        <div class="swiper-wrapper">
                            <!-- Review 1 -->
                            <div class="swiper-slide">
                                <div class="bg-gradient-to-br from-gray-900 to-gray-800 p-3 sm:p-4 rounded-xl border border-gray-700 flex flex-col h-full">
                                <div class="flex items-center gap-2 mb-4">
                                    @for($i = 0; $i < 5; $i++)
                                        <i class="fas fa-star text-yellow-500 text-sm"></i>
                                    @endfor
                                </div>
                                <p class="text-gray-400 text-sm md:text-base mb-6 leading-relaxed flex-grow">
                                    "Depomuzdaki dar koridorlar için ideal. F4 modelini aldık, manevra kabiliyeti gerçekten iyi. Batarya şarjı hızlı, sabah takıyoruz öğlene hazır oluyor. Bakım gerektirmiyor, pratik bir ürün."
                                </p>
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 bg-yellow-600/20 rounded-full flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-user text-yellow-500"></i>
                                    </div>
                                    <div>
                                        <p class="text-white font-bold text-sm md:text-base">Rıdvan Yanıkoğlu</p>
                                        <p class="text-gray-500 text-xs md:text-sm">Pazarlama Müdürü - İstanbul</p>
                                    </div>
                                </div>
                                </div>
                            </div>

                            <!-- Review 2 -->
                            <div class="swiper-slide">
                                <div class="bg-gradient-to-br from-gray-900 to-gray-800 p-3 sm:p-4 rounded-xl border border-gray-700 flex flex-col h-full">
                                <div class="flex items-center gap-2 mb-4">
                                    @for($i = 0; $i < 5; $i++)
                                        <i class="fas fa-star text-yellow-500 text-sm"></i>
                                    @endfor
                                </div>
                                <p class="text-gray-400 text-sm md:text-base mb-6 leading-relaxed flex-grow">
                                    "1.5 ton kapasitesi günlük işlerimiz için yeterli. Günde ortalama 120-130 palet taşıma yapıyoruz, hiç sorun yaşamadık. Operatörler kullanım kolaylığından memnun. Fiyat/performans oranı dengeli."
                                </p>
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 bg-yellow-600/20 rounded-full flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-user text-yellow-500"></i>
                                    </div>
                                    <div>
                                        <p class="text-white font-bold text-sm md:text-base">Kemal Yurtdaş</p>
                                        <p class="text-gray-500 text-xs md:text-sm">Fabrika Sahibi - Bursa</p>
                                    </div>
                                </div>
                                </div>
                            </div>

                            <!-- Review 3 -->
                            <div class="swiper-slide">
                                <div class="bg-gradient-to-br from-gray-900 to-gray-800 p-3 sm:p-4 rounded-xl border border-gray-700 flex flex-col h-full">
                                <div class="flex items-center gap-2 mb-4">
                                    @for($i = 0; $i < 5; $i++)
                                        <i class="fas fa-star text-yellow-500 text-sm"></i>
                                    @endfor
                                </div>
                                <p class="text-gray-400 text-sm md:text-base mb-6 leading-relaxed flex-grow">
                                    "3 aydır kullanımda, şimdiye kadar herhangi bir arıza yaşamadık. Servis desteği hızlı, küçük bir sorunumuzda aynı gün müdahale ettiler. Garanti kapsamı güven veriyor."
                                </p>
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 bg-yellow-600/20 rounded-full flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-user text-yellow-500"></i>
                                    </div>
                                    <div>
                                        <p class="text-white font-bold text-sm md:text-base">İsmail Zor</p>
                                        <p class="text-gray-500 text-xs md:text-sm">Atölye Müdürü - Kocaeli</p>
                                    </div>
                                </div>
                                </div>
                            </div>

                            <!-- Review 4 -->
                            <div class="swiper-slide">
                                <div class="bg-gradient-to-br from-gray-900 to-gray-800 p-3 sm:p-4 rounded-xl border border-gray-700 flex flex-col h-full">
                                <div class="flex items-center gap-2 mb-4">
                                    @for($i = 0; $i < 5; $i++)
                                        <i class="fas fa-star text-yellow-500 text-sm"></i>
                                    @endfor
                                </div>
                                <p class="text-gray-400 text-sm md:text-base mb-6 leading-relaxed flex-grow">
                                    "Batarya ömrü beklediğimizden uzun. 6 ay oldu, hala ilk gündeki performansta çalışıyor. Şarj istasyonu kompakt, fazla yer kaplamıyor. Terazili özelliği ile tartım işlemlerini kolaylaştırdı."
                                </p>
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 bg-yellow-600/20 rounded-full flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-user text-yellow-500"></i>
                                    </div>
                                    <div>
                                        <p class="text-white font-bold text-sm md:text-base">Mehmet Arslan</p>
                                        <p class="text-gray-500 text-xs md:text-sm">Depo Müdürü - Ankara</p>
                                    </div>
                                </div>
                                </div>
                            </div>

                            <!-- Review 5 -->
                            <div class="swiper-slide">
                                <div class="bg-gradient-to-br from-gray-900 to-gray-800 p-3 sm:p-4 rounded-xl border border-gray-700 flex flex-col h-full">
                                <div class="flex items-center gap-2 mb-4">
                                    @for($i = 0; $i < 5; $i++)
                                        <i class="fas fa-star text-yellow-500 text-sm"></i>
                                    @endfor
                                </div>
                                <p class="text-gray-400 text-sm md:text-base mb-6 leading-relaxed flex-grow">
                                    "Fiyat araştırması yaptım, İXTİF'in kampanya fiyatı piyasanın en uygunuydu. Ürün kalitesi fiyatına göre çok iyi. Denge tekerlekli tasarım sayesinde rampalarda güvenli çalışıyor."
                                </p>
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 bg-yellow-600/20 rounded-full flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-user text-yellow-500"></i>
                                    </div>
                                    <div>
                                        <p class="text-white font-bold text-sm md:text-base">Murat Demir</p>
                                        <p class="text-gray-500 text-xs md:text-sm">Satın Alma Uzmanı - İzmir</p>
                                    </div>
                                </div>
                                </div>
                            </div>

                            <!-- Review 6 -->
                            <div class="swiper-slide">
                                <div class="bg-gradient-to-br from-gray-900 to-gray-800 p-3 sm:p-4 rounded-xl border border-gray-700 flex flex-col h-full">
                                <div class="flex items-center gap-2 mb-4">
                                    @for($i = 0; $i < 5; $i++)
                                        <i class="fas fa-star text-yellow-500 text-sm"></i>
                                    @endfor
                                </div>
                                <p class="text-gray-400 text-sm md:text-base mb-6 leading-relaxed flex-grow">
                                    "Operatör eğitimi 20 dakika sürdü, kullanımı çok kolay. Ergonomik kumanda kolu yorulmadan uzun süre çalışma imkanı veriyor. Gürültü seviyesi düşük, kapalı alanda rahatsız etmiyor."
                                </p>
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 bg-yellow-600/20 rounded-full flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-user text-yellow-500"></i>
                                    </div>
                                    <div>
                                        <p class="text-white font-bold text-sm md:text-base">Selim Öztürk</p>
                                        <p class="text-gray-500 text-xs md:text-sm">Operasyon Şefi - Bursa</p>
                                    </div>
                                </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats -->
            <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-xl p-3 sm:p-4">
                <div class="grid md:grid-cols-3 gap-4 sm:gap-6">
                    <div class="flex items-center gap-3 sm:gap-4">
                        <div class="text-3xl sm:text-4xl font-black gold-gradient bg-clip-text text-transparent flex-shrink-0">4.9/5</div>
                        <div class="text-gray-400 text-xs sm:text-sm">Ortalama Puan</div>
                    </div>
                    <div class="flex items-center gap-3 sm:gap-4">
                        <div class="text-3xl sm:text-4xl font-black gold-gradient bg-clip-text text-transparent flex-shrink-0">150+</div>
                        <div class="text-gray-400 text-xs sm:text-sm">Memnun Müşteri</div>
                    </div>
                    <div class="flex items-center gap-3 sm:gap-4">
                        <div class="text-3xl sm:text-4xl font-black gold-gradient bg-clip-text text-transparent flex-shrink-0">%98</div>
                        <div class="text-gray-400 text-xs sm:text-sm">Tavsiye Oranı</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Ürün Kategorileri - Tüm Modellere Ulaşım -->
    <section class="py-10 sm:py-12 lg:py-14 px-4 bg-gradient-to-b from-black via-gray-900 to-black">
        <div class="container mx-auto max-w-7xl">
            <h2 class="text-2xl sm:text-3xl lg:text-4xl font-black mb-2 sm:mb-3 text-center">
                <span class="gold-gradient bg-clip-text text-transparent">Ürün Kategorileri</span>
            </h2>
            <p class="text-center text-gray-400 mb-6 sm:mb-8 text-xs sm:text-sm">İhtiyacınıza uygun depolama ekipmanı modellerini keşfedin</p>

            @php
                use Modules\Shop\App\Models\ShopCategory;

                // Homepage'de gösterilecek kategorileri çek
                $categories = ShopCategory::where('show_in_homepage', true)
                    ->where('is_active', true)
                    ->orderBy('sort_order')
                    ->get();

                $locale = app()->getLocale();
            @endphp

            <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                @foreach($categories as $category)
                    @php
                        $slug = $category->getTranslated('slug', $locale) ?? ($category->slug[$locale] ?? '');
                        $title = $category->getTranslated('title', $locale) ?? ($category->title[$locale] ?? '');
                        $description = $category->getTranslated('description', $locale) ?? ($category->description[$locale] ?? '');
                        $iconClass = $category->icon_class ?? 'fas fa-box';
                    @endphp

                    <a href="/shop/kategori/{{ $slug }}"
                       class="group bg-gradient-to-br from-gray-900 to-gray-800 p-3 sm:p-4 rounded-xl border border-gray-700 hover:border-yellow-600 transition-all duration-300 transform hover:scale-105">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gray-700 group-hover:bg-yellow-600/20 rounded-lg flex items-center justify-center mb-2 sm:mb-3 transition-colors">
                            <i class="{{ $iconClass }} text-yellow-500 text-xl sm:text-2xl"></i>
                        </div>
                        <h3 class="text-sm sm:text-base lg:text-lg font-bold mb-2 text-white group-hover:text-yellow-500 transition-colors">{{ $title }}</h3>
                        <p class="text-gray-400 text-xs sm:text-sm mb-3">
                            {{ strip_tags(Str::limit($description, 60)) }}
                        </p>
                        <div class="flex items-center text-yellow-500 text-xs sm:text-sm font-semibold">
                            <span>Modelleri İncele</span>
                            <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                        </div>
                    </a>
                @endforeach
            </div>

            <!-- All Products CTA -->
            <div class="mt-6 sm:mt-8 text-center">
                <a href="/shop" class="inline-flex items-center gap-3 px-6 sm:px-8 py-3 sm:py-4 gold-gradient rounded-xl text-gray-950 text-sm sm:text-base font-bold transition-all hover:shadow-[0_0_30px_rgba(212,175,55,0.6)] transform hover:scale-105">
                    <i class="fas fa-th-large text-lg sm:text-xl"></i>
                    <span>Tüm Ürünleri Görüntüle</span>
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Mid-Page CTA - Conversional Focused -->
    <section class="py-10 sm:py-12 lg:py-14 px-4 bg-gradient-to-br from-yellow-600/10 via-black to-yellow-600/5">
        <div class="container mx-auto max-w-7xl">
            <div class="bg-gradient-to-br from-gray-900 to-gray-800 border-2 border-yellow-600 rounded-xl p-4 sm:p-6">
                <div class="text-center mb-6 sm:mb-8">
                    <h2 class="text-2xl sm:text-3xl lg:text-4xl font-black mb-2 sm:mb-3">
                        <span class="gold-gradient bg-clip-text text-transparent">Hemen Teklif Alın</span>
                    </h2>
                    <p class="text-gray-400 text-xs sm:text-sm">
                        Elektrikli transpalet ihtiyacınız için <strong class="text-white">ücretsiz fiyat teklifi</strong> ve <strong class="text-white">teknik danışmanlık</strong> hizmeti sunuyoruz. Uzman ekibimiz size en uygun çözümü önerir.
                    </p>
                </div>

                <div class="grid sm:grid-cols-3 gap-3 sm:gap-4 mb-6 sm:mb-8">
                    <a href="{{ whatsapp_link(null, 'Elektrikli Transpalet - Mid CTA') }}"
                       target="_blank"
                       class="flex flex-col items-center justify-center gap-2 sm:gap-3 gold-gradient p-3 sm:p-4 rounded-xl hover:shadow-[0_0_30px_rgba(212,175,55,0.6)] transition-all transform hover:scale-105">
                        <i class="fab fa-whatsapp text-gray-950 text-2xl sm:text-3xl"></i>
                        <div class="text-center">
                            <div class="text-gray-950 font-black text-sm sm:text-base">WhatsApp</div>
                            <div class="text-gray-800 text-xs sm:text-sm">Anında Yanıt</div>
                        </div>
                    </a>

                    <a href="tel:{{ str_replace(' ', '', $contactPhone) }}"
                       class="flex flex-col items-center justify-center gap-2 sm:gap-3 bg-gray-800 border-2 border-yellow-600 p-3 sm:p-4 rounded-xl hover:bg-gray-700 transition-all transform hover:scale-105">
                        <i class="fas fa-phone text-yellow-500 text-2xl sm:text-3xl"></i>
                        <div class="text-center">
                            <div class="text-white font-black text-sm sm:text-base">Hemen Arayın</div>
                            <div class="text-yellow-500 text-xs sm:text-sm font-bold">{{ $contactPhone }}</div>
                        </div>
                    </a>

                    <a href="https://ixtif.com/sizi-arayalim"
                       target="_blank"
                       class="flex flex-col items-center justify-center gap-2 sm:gap-3 bg-gray-800 border-2 border-gray-600 p-3 sm:p-4 rounded-xl hover:border-yellow-600 transition-all transform hover:scale-105">
                        <i class="fas fa-headset text-yellow-500 text-2xl sm:text-3xl"></i>
                        <div class="text-center">
                            <div class="text-white font-black text-sm sm:text-base">Sizi Arayalım</div>
                            <div class="text-gray-400 text-xs sm:text-sm">Ücretsiz Danışma</div>
                        </div>
                    </a>
                </div>

                <div class="flex flex-wrap items-center justify-center gap-4 sm:gap-6 text-xs sm:text-sm text-gray-400">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-check-circle text-yellow-500"></i>
                        <span>Ücretsiz Teknik Danışmanlık</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-check-circle text-yellow-500"></i>
                        <span>Hızlı Teklif Süreci</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-check-circle text-yellow-500"></i>
                        <span>%100 Güvenli Alışveriş</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Opsiyonlar - Configuration Options -->
    <section class="py-10 sm:py-12 lg:py-14 px-4 bg-black">
        <div class="container mx-auto max-w-7xl">
            <h2 class="text-2xl sm:text-3xl lg:text-4xl font-black mb-2 sm:mb-3 text-center">
                <span class="gold-gradient bg-clip-text text-transparent">Opsiyonlar</span>
            </h2>
            <p class="text-center text-gray-400 text-xs sm:text-sm mb-6 sm:mb-8">Özelleştirme Seçenekleri</p>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                <!-- Çatal Boyutları -->
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 p-3 sm:p-4 rounded-xl border border-gray-700 flex gap-3 sm:gap-4 min-h-[140px]">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-yellow-600/20 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-arrows-alt-h text-yellow-500 text-base sm:text-lg"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-sm sm:text-base lg:text-lg font-bold mb-1 sm:mb-2 text-white">Çatal Boyutları</h3>
                        <p class="text-gray-400 text-xs sm:text-sm mb-2">
                            İşletmenizin paletlerine uygun çatal uzunluğu ve genişliği
                        </p>
                        <ul class="space-y-1 text-xs sm:text-sm text-gray-500">
                            <li><i class="fas fa-check text-yellow-500 mr-2"></i>900-1500 mm uzunluk</li>
                            <li><i class="fas fa-check text-yellow-500 mr-2"></i>560 / 685 mm genişlik</li>
                            <li><i class="fas fa-check text-yellow-500 mr-2"></i>Özel sipariş mevcut</li>
                        </ul>
                    </div>
                </div>

                <!-- Batarya Kapasitesi -->
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 p-3 sm:p-4 rounded-xl border border-gray-700 flex gap-3 sm:gap-4 min-h-[140px]">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-yellow-600/20 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-battery-full text-yellow-500 text-base sm:text-lg"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-sm sm:text-base lg:text-lg font-bold mb-1 sm:mb-2 text-white">Batarya Kapasitesi</h3>
                        <p class="text-gray-400 text-xs sm:text-sm mb-2">
                            Çalışma sürenize göre artırılabilir batarya seçenekleri
                        </p>
                        <ul class="space-y-1 text-xs sm:text-sm text-gray-500">
                            <li><i class="fas fa-check text-yellow-500 mr-2"></i>1x 20Ah (Standart)</li>
                            <li><i class="fas fa-check text-yellow-500 mr-2"></i>2x 20Ah (Yoğun kullanım)</li>
                            <li><i class="fas fa-check text-yellow-500 mr-2"></i>3x-4x 20Ah (7/24 vardiya)</li>
                        </ul>
                    </div>
                </div>

                <!-- Denge Tekerlekleri -->
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 p-3 sm:p-4 rounded-xl border border-gray-700 flex gap-3 sm:gap-4 min-h-[140px]">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gray-700 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-circle-notch text-yellow-500 text-base sm:text-lg"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-sm sm:text-base lg:text-lg font-bold mb-1 sm:mb-2 text-white">Denge Tekerlekleri</h3>
                        <p class="text-gray-400 text-xs sm:text-sm mb-2">
                            Ağır yükler ve engebeli zeminler için stabilite desteği
                        </p>
                        <ul class="space-y-1 text-xs sm:text-sm text-gray-500">
                            <li><i class="fas fa-check text-yellow-500 mr-2"></i>Büyük yükler için güvenlik</li>
                            <li><i class="fas fa-check text-yellow-500 mr-2"></i>Engebeli zemin desteği</li>
                            <li><i class="fas fa-check text-yellow-500 mr-2"></i>Sonradan eklenebilir</li>
                        </ul>
                    </div>
                </div>

                <!-- Çatal İndirme Yüksekliği -->
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 p-3 sm:p-4 rounded-xl border border-gray-700 flex gap-3 sm:gap-4 min-h-[140px]">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gray-700 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-arrows-alt-v text-yellow-500 text-base sm:text-lg"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-sm sm:text-base lg:text-lg font-bold mb-1 sm:mb-2 text-white">Çatal Yüksekliği</h3>
                        <p class="text-gray-400 text-xs sm:text-sm mb-2">
                            Farklı palet tipleri için ayarlanabilir çatal yüksekliği
                        </p>
                        <ul class="space-y-1 text-xs sm:text-sm text-gray-500">
                            <li><i class="fas fa-check text-yellow-500 mr-2"></i>80 mm (Standart)</li>
                            <li><i class="fas fa-check text-yellow-500 mr-2"></i>55 mm (Alçak profil)</li>
                            <li><i class="fas fa-check text-yellow-500 mr-2"></i>Özel paletler için ideal</li>
                        </ul>
                    </div>
                </div>

                <!-- Şarj Cihazı -->
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 p-3 sm:p-4 rounded-xl border border-gray-700 flex gap-3 sm:gap-4 min-h-[140px]">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gray-700 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-charging-station text-yellow-500 text-base sm:text-lg"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-sm sm:text-base lg:text-lg font-bold mb-1 sm:mb-2 text-white">Şarj Cihazı</h3>
                        <p class="text-gray-400 text-xs sm:text-sm mb-2">
                            Kullanım yoğunluğunuza göre şarj hızı seçenekleri
                        </p>
                        <ul class="space-y-1 text-xs sm:text-sm text-gray-500">
                            <li><i class="fas fa-check text-yellow-500 mr-2"></i>5A (Standart şarj)</li>
                            <li><i class="fas fa-check text-yellow-500 mr-2"></i>10A (Hızlı şarj)</li>
                            <li><i class="fas fa-check text-yellow-500 mr-2"></i>DC-DC akıllı şarj</li>
                        </ul>
                    </div>
                </div>

                <!-- Kumanda Kolu -->
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 p-3 sm:p-4 rounded-xl border border-gray-700 flex gap-3 sm:gap-4 min-h-[140px]">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gray-700 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-hand-paper text-yellow-500 text-base sm:text-lg"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-sm sm:text-base lg:text-lg font-bold mb-1 sm:mb-2 text-white">Kumanda Kolu</h3>
                        <p class="text-gray-400 text-xs sm:text-sm mb-2">
                            Operatör konforuna göre kol tipi seçeneği
                        </p>
                        <ul class="space-y-1 text-xs sm:text-sm text-gray-500">
                            <li><i class="fas fa-check text-yellow-500 mr-2"></i>Küçük kol (Standart)</li>
                            <li><i class="fas fa-check text-yellow-500 mr-2"></i>Büyük kol (Konfor)</li>
                            <li><i class="fas fa-check text-yellow-500 mr-2"></i>Ergonomik tasarım</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Info Box -->
            <div class="mt-6 sm:mt-8 bg-yellow-600/10 border-l-4 border-yellow-600 p-3 sm:p-4 rounded-r-xl">
                <div class="flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-4">
                    <div class="flex-shrink-0 w-10 h-10 sm:w-12 sm:h-12 gold-gradient rounded-full flex items-center justify-center">
                        <i class="fas fa-cog text-gray-950 text-lg sm:text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="text-sm sm:text-base font-bold text-white mb-1 sm:mb-2">Özelleştirilmiş Çözümler</h4>
                        <p class="text-xs sm:text-sm text-gray-300 leading-relaxed">
                            Tüm opsiyonlar fabrika çıkışlı veya sonradan retrofitlenebilir. İşletmenizin özel ihtiyaçları için <strong>ücretsiz danışmanlık</strong> hizmeti sunuyoruz. WhatsApp veya telefon ile hemen iletişime geçin.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ - Long-tail Keywords -->
    <section class="py-10 sm:py-12 lg:py-14 px-4 bg-black">
        <div class="container mx-auto max-w-7xl">
            <h2 class="text-2xl sm:text-3xl lg:text-4xl font-black mb-2 sm:mb-3 text-center">
                <span class="gold-gradient bg-clip-text text-transparent">Sık Sorulan Sorular</span>
            </h2>
            <p class="text-center text-gray-400 text-xs sm:text-sm mb-6 sm:mb-8">Merak Edilenler</p>

            <div class="space-y-4" x-data="{ openFaq: null }">
                <!-- FAQ 1 -->
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-xl border border-gray-700 overflow-hidden">
                    <button @click="openFaq = openFaq === 1 ? null : 1" class="w-full p-3 sm:p-4 text-left flex items-center justify-between hover:bg-gray-800/50 transition-colors">
                        <h3 class="text-white font-bold pr-4">Elektrikli transpalet fiyatları ne kadar?</h3>
                        <i class="fas transition-transform duration-300" :class="openFaq === 1 ? 'fa-chevron-up text-yellow-500' : 'fa-chevron-down text-gray-400'"></i>
                    </button>
                    <div x-show="openFaq === 1" x-collapse class="px-3 sm:px-4 pb-3 sm:pb-4">
                        <p class="text-gray-400">
                            İXTİF F4 elektrikli transpalet Kasım kampanyasında $1,250 (normal fiyat $1,560). Satılık transpalet stoklarımız mevcuttur. Transpalet fiyatları kapasite ve özelliklere göre değişir. Elektrikli transpalet satın al fırsatından yararlanın, 1 yıl garanti dahildir.
                        </p>
                    </div>
                </div>

                <!-- FAQ 2 -->
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-xl border border-gray-700 overflow-hidden">
                    <button @click="openFaq = openFaq === 2 ? null : 2" class="w-full p-3 sm:p-4 text-left flex items-center justify-between hover:bg-gray-800/50 transition-colors">
                        <h3 class="text-white font-bold pr-4">Akülü transpalet şarj süresi ne kadar?</h3>
                        <i class="fas transition-transform duration-300" :class="openFaq === 2 ? 'fa-chevron-up text-yellow-500' : 'fa-chevron-down text-gray-400'"></i>
                    </button>
                    <div x-show="openFaq === 2" x-collapse class="px-3 sm:px-4 pb-3 sm:pb-4">
                        <p class="text-gray-400">
                            Şarjlı transpalet ve Li-Ion bataryalı elektrikli transpalet 2-3 saat içinde tam şarj olur. Akülü transpalet sistemimiz hızlı şarj özelliği sayesinde iş akışınızda minimum kesinti yaşarsınız. Depo için transpalet ihtiyaçlarınızda kesintisiz çalışma sağlar.
                        </p>
                    </div>
                </div>

                <!-- FAQ 3 -->
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-xl border border-gray-700 overflow-hidden">
                    <button @click="openFaq = openFaq === 3 ? null : 3" class="w-full p-3 sm:p-4 text-left flex items-center justify-between hover:bg-gray-800/50 transition-colors">
                        <h3 class="text-white font-bold pr-4">Terazili transpalet nedir?</h3>
                        <i class="fas transition-transform duration-300" :class="openFaq === 3 ? 'fa-chevron-up text-yellow-500' : 'fa-chevron-down text-gray-400'"></i>
                    </button>
                    <div x-show="openFaq === 3" x-collapse class="px-3 sm:px-4 pb-3 sm:pb-4">
                        <p class="text-gray-400">
                            Terazili transpalet, entegre terazi sistemi olan elektrikli transpalet çeşididir. Yükü tartarken taşıma imkanı sağlar. Terazili transpalet satın al opsiyonu ile İXTİF F4 modelinde sunulmaktadır. Depo için transpalet ihtiyaçlarınızda hassas ölçüm gerektiren uygulamalar için idealdir.
                        </p>
                    </div>
                </div>

                <!-- FAQ 4 -->
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-xl border border-gray-700 overflow-hidden">
                    <button @click="openFaq = openFaq === 4 ? null : 4" class="w-full p-3 sm:p-4 text-left flex items-center justify-between hover:bg-gray-800/50 transition-colors">
                        <h3 class="text-white font-bold pr-4">Denge tekerli transpalet avantajları nelerdir?</h3>
                        <i class="fas transition-transform duration-300" :class="openFaq === 4 ? 'fa-chevron-up text-yellow-500' : 'fa-chevron-down text-gray-400'"></i>
                    </button>
                    <div x-show="openFaq === 4" x-collapse class="px-3 sm:px-4 pb-3 sm:pb-4">
                        <p class="text-gray-400">
                            Denge tekerli transpalet kompakt tasarımı ile dar alanlarda üstün manevra kabiliyeti sağlar. İXTİF F4'ün 1360 mm dönüş yarıçapı ile dar koridorlarda bile rahatça kullanabilirsiniz.
                        </p>
                    </div>
                </div>

                <!-- FAQ 5 -->
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-xl border border-gray-700 overflow-hidden">
                    <button @click="openFaq = openFaq === 5 ? null : 5" class="w-full p-3 sm:p-4 text-left flex items-center justify-between hover:bg-gray-800/50 transition-colors">
                        <h3 class="text-white font-bold pr-4">Elektrikli transpalet bakımı nasıl yapılır?</h3>
                        <i class="fas transition-transform duration-300" :class="openFaq === 5 ? 'fa-chevron-up text-yellow-500' : 'fa-chevron-down text-gray-400'"></i>
                    </button>
                    <div x-show="openFaq === 5" x-collapse class="px-3 sm:px-4 pb-3 sm:pb-4">
                        <p class="text-gray-400">
                            Akülü transpalet minimal bakım gerektirir. Li-Ion batarya teknolojisi ile su ilavesi gerekmez. Düzenli tekerlek kontrolü ve batarya bakımı yeterlidir. Teknik destek hizmetimiz mevcuttur.
                        </p>
                    </div>
                </div>

                <!-- FAQ 6 -->
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-xl border border-gray-700 overflow-hidden">
                    <button @click="openFaq = openFaq === 6 ? null : 6" class="w-full p-3 sm:p-4 text-left flex items-center justify-between hover:bg-gray-800/50 transition-colors">
                        <h3 class="text-white font-bold pr-4">Transpalet garanti süresi ne kadar?</h3>
                        <i class="fas transition-transform duration-300" :class="openFaq === 6 ? 'fa-chevron-up text-yellow-500' : 'fa-chevron-down text-gray-400'"></i>
                    </button>
                    <div x-show="openFaq === 6" x-collapse class="px-3 sm:px-4 pb-3 sm:pb-4">
                        <p class="text-gray-400">
                            İXTİF elektrikli transpalet 1 yıl garanti ile sunulmaktadır. Tüm parça ve işçilik ücretsizdir. Garanti sonrası yedek parça desteği devam eder.
                        </p>
                    </div>
                </div>

                <!-- FAQ 7 - New: Ucuz/Ekonomik -->
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-xl border border-gray-700 overflow-hidden">
                    <button @click="openFaq = openFaq === 7 ? null : 7" class="w-full p-3 sm:p-4 text-left flex items-center justify-between hover:bg-gray-800/50 transition-colors">
                        <h3 class="text-white font-bold pr-4">Ucuz elektrikli transpalet var mı? En uygun fiyat nerede?</h3>
                        <i class="fas transition-transform duration-300" :class="openFaq === 7 ? 'fa-chevron-up text-yellow-500' : 'fa-chevron-down text-gray-400'"></i>
                    </button>
                    <div x-show="openFaq === 7" x-collapse class="px-3 sm:px-4 pb-3 sm:pb-4">
                        <p class="text-gray-400">
                            İXTİF F4 elektrikli transpalet, piyasadaki en uygun fiyatlı ve ekonomik akülü transpalet seçeneklerinden biridir. Kasım kampanyasında $1,250 ile kaliteli ve ucuz elektrikli transpalet arayan firmalar için ideal çözüm. Premium kalite, uygun fiyat garantisi.
                        </p>
                    </div>
                </div>

                <!-- FAQ 8 -->
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-xl border border-gray-700 overflow-hidden">
                    <button @click="openFaq = openFaq === 8 ? null : 8" class="w-full p-3 sm:p-4 text-left flex items-center justify-between hover:bg-gray-800/50 transition-colors">
                        <h3 class="text-white font-bold pr-4">Elektrikli transpalet kaç kg kaldırır?</h3>
                        <i class="fas transition-transform duration-300" :class="openFaq === 8 ? 'fa-chevron-up text-yellow-500' : 'fa-chevron-down text-gray-400'"></i>
                    </button>
                    <div x-show="openFaq === 8" x-collapse class="px-3 sm:px-4 pb-3 sm:pb-4">
                        <p class="text-gray-400">
                            İXTİF F4 elektrikli transpalet 1500 kg (1.5 ton) kaldırma kapasitesine sahiptir. Bu kapasite, endüstriyel depolarda ve lojistik merkezlerinde günlük yük taşıma ihtiyaçlarını karşılamak için idealdir.
                        </p>
                    </div>
                </div>

                <!-- FAQ 9 -->
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-xl border border-gray-700 overflow-hidden">
                    <button @click="openFaq = openFaq === 9 ? null : 9" class="w-full p-3 sm:p-4 text-left flex items-center justify-between hover:bg-gray-800/50 transition-colors">
                        <h3 class="text-white font-bold pr-4">Akülü transpalet nasıl çalışır?</h3>
                        <i class="fas transition-transform duration-300" :class="openFaq === 9 ? 'fa-chevron-up text-yellow-500' : 'fa-chevron-down text-gray-400'"></i>
                    </button>
                    <div x-show="openFaq === 9" x-collapse class="px-3 sm:px-4 pb-3 sm:pb-4">
                        <p class="text-gray-400">
                            Elektrikli transpalet, Li-Ion batarya ile çalışan elektrik motoruyla paletleri kaldırıp taşır. Ergonomik kumanda kolu ile yön kontrolü ve hız ayarlaması yapılır. Hidrolik sistem paletleri yumuşak ve güvenli bir şekilde kaldırır.
                        </p>
                    </div>
                </div>

                <!-- FAQ 10 -->
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-xl border border-gray-700 overflow-hidden">
                    <button @click="openFaq = openFaq === 10 ? null : 10" class="w-full p-3 sm:p-4 text-left flex items-center justify-between hover:bg-gray-800/50 transition-colors">
                        <h3 class="text-white font-bold pr-4">Elektrikli transpalet batarya ömrü ne kadar?</h3>
                        <i class="fas transition-transform duration-300" :class="openFaq === 10 ? 'fa-chevron-up text-yellow-500' : 'fa-chevron-down text-gray-400'"></i>
                    </button>
                    <div x-show="openFaq === 10" x-collapse class="px-3 sm:px-4 pb-3 sm:pb-4">
                        <p class="text-gray-400">
                            Li-Ion batarya teknolojisi ile akülü transpalet bataryası ortalama 2000+ şarj döngüsü ömre sahiptir. Günde 1 şarj ile yaklaşık 5-7 yıl kesintisiz kullanım sağlar. Bakım gerektirmeyen batarya tasarımı uzun ömürlüdür.
                        </p>
                    </div>
                </div>

                <!-- FAQ 11 -->
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-xl border border-gray-700 overflow-hidden">
                    <button @click="openFaq = openFaq === 11 ? null : 11" class="w-full p-3 sm:p-4 text-left flex items-center justify-between hover:bg-gray-800/50 transition-colors">
                        <h3 class="text-white font-bold pr-4">Elektrikli transpalet ne kadar hızlı gider?</h3>
                        <i class="fas transition-transform duration-300" :class="openFaq === 11 ? 'fa-chevron-up text-yellow-500' : 'fa-chevron-down text-gray-400'"></i>
                    </button>
                    <div x-show="openFaq === 11" x-collapse class="px-3 sm:px-4 pb-3 sm:pb-4">
                        <p class="text-gray-400">
                            İXTİF F4 elektrikli transpalet 4.5 km/saat yüklü, 5 km/saat yüksüz hızla hareket eder. Bu hız, güvenli ve verimli çalışma için optimize edilmiştir. Dar alanlarda düşük hız, açık alanlarda yüksek hız modları mevcuttur.
                        </p>
                    </div>
                </div>

                <!-- FAQ 12 -->
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-xl border border-gray-700 overflow-hidden">
                    <button @click="openFaq = openFaq === 12 ? null : 12" class="w-full p-3 sm:p-4 text-left flex items-center justify-between hover:bg-gray-800/50 transition-colors">
                        <h3 class="text-white font-bold pr-4">Elektrikli transpalet yedek parça bulunur mu?</h3>
                        <i class="fas transition-transform duration-300" :class="openFaq === 12 ? 'fa-chevron-up text-yellow-500' : 'fa-chevron-down text-gray-400'"></i>
                    </button>
                    <div x-show="openFaq === 12" x-collapse class="px-3 sm:px-4 pb-3 sm:pb-4">
                        <p class="text-gray-400">
                            Evet, İXTİF elektrikli transpalet için tüm yedek parçalar stokta bulunmaktadır. Tekerlek, fren, batarya ve kumanda kolu gibi kritik parçalar anında temin edilebilir. 7/24 teknik destek ve hızlı yedek parça teslimatı garantisi.
                        </p>
                    </div>
                </div>

                <!-- FAQ 13 - Lokasyon (önceki 14) -->
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-xl border border-gray-700 overflow-hidden">
                    <button @click="openFaq = openFaq === 13 ? null : 13" class="w-full p-3 sm:p-4 text-left flex items-center justify-between hover:bg-gray-800/50 transition-colors">
                        <h3 class="text-white font-bold pr-4">Tüm Türkiye'ye elektrikli transpalet satış ve servisi var mı?</h3>
                        <i class="fas transition-transform duration-300" :class="openFaq === 13 ? 'fa-chevron-up text-yellow-500' : 'fa-chevron-down text-gray-400'"></i>
                    </button>
                    <div x-show="openFaq === 13" x-collapse class="px-3 sm:px-4 pb-3 sm:pb-4">
                        <p class="text-gray-400">
                            Evet! Tüm Türkiye'ye stoktan teslim transpaletler sunuyoruz. Bugün ara, yarın teslim hızlı teslimat garantisi. Depo içi elektrikli transpalet, elektrikli şantiye transpaleti ve soğuk hava deposuna transpalet çözümlerimiz mevcuttur. Dar alanlar için transpaletler özel olarak stoklanmaktadır.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Final CTA - Conversion Optimized -->
    <section class="py-10 sm:py-12 lg:py-14 px-4 bg-gradient-to-b from-black via-gray-900 to-black" id="iletisim-formu">
        <div class="container mx-auto max-w-7xl">
            <div class="bg-gradient-to-br from-gray-900 to-gray-800 border-2 border-yellow-600 rounded-xl p-4 sm:p-6 text-center">
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-black mb-2 sm:mb-3">
                    <span class="text-white">Elektrikli Transpalet</span>
                    <span class="gold-gradient bg-clip-text text-transparent"> Kampanyası</span>
                </h2>
                <p class="text-xs sm:text-sm text-gray-400 mb-6 sm:mb-8">Şarjlı transpalet ve satılık transpalet stoklarımızdan elektrikli transpalet satın al - Kasım ayına özel %20 indirim</p>

                <!-- Price Box with Animation -->
                <div class="bg-black/30 border border-yellow-600/30 rounded-xl p-3 sm:p-4 mb-6 sm:mb-8"
                     x-data="{
                         showTL: false,
                         priceTimer: null,
                         init() {
                             this.startPriceCycle();
                         },
                         startPriceCycle() {
                             this.priceTimer = setInterval(() => {
                                 // TL'ye geç (1.5 saniye)
                                 this.showTL = true;
                                 setTimeout(() => {
                                     this.showTL = false;
                                 }, 1500);
                             }, 4500); // Her 4.5 saniyede bir döngü (3s USD + 1.5s TL)
                         },
                         destroy() {
                             if (this.priceTimer) clearInterval(this.priceTimer);
                         }
                     }">
                    <div class="text-center mb-4 sm:mb-6">
                        <p class="text-yellow-500 font-bold mb-2 text-xs sm:text-sm">Kampanya Fiyatı</p>
                        <p x-show="!showTL" class="text-2xl sm:text-3xl lg:text-4xl font-black gold-gradient bg-clip-text text-transparent">$1,250</p>
                        <p x-show="showTL" class="text-2xl sm:text-3xl lg:text-4xl font-black gold-gradient bg-clip-text text-transparent">52.500 ₺</p>
                    </div>

                    <div class="flex items-center justify-center gap-2 text-xs sm:text-sm text-gray-400">
                        <i class="fas fa-shield-alt text-yellow-500 text-base sm:text-xl"></i>
                        <span class="font-bold text-yellow-500">1 Yıl Garanti Dahil</span>
                    </div>
                </div>

                <!-- CTA Buttons -->
                <div class="mb-6 sm:mb-8">
                    <div class="grid sm:grid-cols-3 gap-3 sm:gap-4">
                        <a href="{{ whatsapp_link(null, 'Elektrikli Transpalet Kampanya - Final CTA') }}"
                           target="_blank"
                           class="flex items-center justify-center gap-2 sm:gap-3 gold-gradient p-3 sm:p-4 rounded-xl border-2 border-yellow-600 hover:shadow-[0_0_30px_rgba(212,175,55,0.6)] transition-all">
                            <i class="fab fa-whatsapp text-gray-950 text-lg sm:text-2xl"></i>
                            <div class="text-left">
                                <div class="text-gray-950 font-black text-sm sm:text-base">WhatsApp</div>
                                <div class="text-gray-800 text-xs sm:text-sm">Hızlı İletişim</div>
                            </div>
                        </a>
                        <a href="tel:{{ str_replace(' ', '', $contactPhone) }}"
                           class="flex items-center justify-center gap-2 sm:gap-3 bg-white/10 backdrop-blur-md p-3 sm:p-4 rounded-lg sm:rounded-xl border border-white/20 hover:bg-white/20 transition-all">
                            <i class="fas fa-phone text-lg sm:text-2xl text-white"></i>
                            <div class="text-left">
                                <div class="text-white font-bold text-sm sm:text-base">Telefon</div>
                                <div class="text-gray-400 text-xs sm:text-sm">{{ $contactPhone }}</div>
                            </div>
                        </a>
                        <a href="mailto:{{ setting('contact_email', 'info@ixtif.com') }}?subject=Elektrikli Transpalet Teklif"
                           class="flex items-center justify-center gap-2 sm:gap-3 bg-white/10 backdrop-blur-md p-3 sm:p-4 rounded-lg sm:rounded-xl border border-white/20 hover:bg-white/20 transition-all">
                            <i class="fas fa-envelope text-lg sm:text-2xl text-white"></i>
                            <div class="text-left">
                                <div class="text-white font-bold text-sm sm:text-base">E-posta</div>
                                <div class="text-gray-400 text-xs sm:text-sm">Teklif Al</div>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Urgency -->
                <div class="bg-yellow-600/10 border border-yellow-600/30 rounded-lg sm:rounded-xl p-3 sm:p-4">
                    <p class="text-yellow-500 font-bold text-xs sm:text-sm lg:text-base">
                        <i class="fas fa-clock mr-2"></i>
                        Kampanya süresi sınırlı! Elektrikli transpalet özel fiyatından yararlanın.
                    </p>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts-footer')
    <!-- Swiper Init Script -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const reviewsSwiper = new Swiper('.reviewsSwiper', {
            slidesPerView: 'auto',
            spaceBetween: 16,
            loop: true,
            autoplay: {
                delay: 0,
                disableOnInteraction: false,
                pauseOnMouseEnter: true,
            },
            speed: 5000,
            freeMode: true,
            freeModeMomentum: false,
            allowTouchMove: true,      // ✅ Manuel kaydırma aktif
            grabCursor: true,           // ✅ Mouse cursor grab olur
            breakpoints: {
                768: {
                    spaceBetween: 24,
                }
            }
        });
    });
    </script>

    <!-- Swiper Custom CSS -->
    <style>
    .reviewsSwiper .swiper-slide {
        width: 280px;
        height: auto;
    }
    @media (min-width: 768px) {
        .reviewsSwiper .swiper-slide {
            width: 350px;
        }
    }
    /* Equal height cards */
    .reviewsSwiper .swiper-wrapper {
        align-items: stretch;
    }
    .reviewsSwiper .swiper-slide > div {
        height: 100%;
    }
    </style>

    <!-- Countdown Script (Optimized) -->
    <script>
    (function() {
        'use strict';
        let endTime = null;

        function getFixedTime() {
            // 3 gün sabit süre = 72 saat
            const threeDays = 3 * 24 * 3600 * 1000; // 259,200,000 ms
            return Date.now() + threeDays;
        }

        function resetCountdown() {
            endTime = getFixedTime();
            try {
                localStorage.setItem('campaignEndTime_v3', endTime);
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
            const stored = localStorage.getItem('campaignEndTime_v3');
            endTime = stored ? parseInt(stored) : getFixedTime();
            if (!stored) localStorage.setItem('campaignEndTime_v3', endTime);
        } catch(e) {
            endTime = getFixedTime();
        }

        updateCountdown();
        setInterval(updateCountdown, 1000);
    })();
    </script>
@endpush

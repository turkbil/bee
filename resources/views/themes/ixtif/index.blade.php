<!DOCTYPE html>
<html lang="tr"
      x-data="{ darkMode: localStorage.getItem('darkMode') || 'light' }"
      x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))"
      :class="{ 'dark': darkMode === 'dark' }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>İXtif - Türkiye'nin İstif Pazarı</title>

    {{-- Theme Flash Fix: Minimal inline script - Prevents flash before Alpine.js loads --}}
    <script>if(localStorage.getItem('darkMode')==='dark')document.documentElement.classList.add('dark')</script>

    {{-- Tailwind CSS CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Alpine.js CDN --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.5.1/css/all.css">

    {{-- Google Fonts - Roboto --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;500;700;900&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Roboto', sans-serif;
        }
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Roboto', sans-serif;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

        .animate-float {
            animation: float 3s ease-in-out infinite;
        }

        /* Letter Switch - Sabit genişlik için */
        .letter-switch {
            width: 0.75em;
            height: 1em;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            vertical-align: baseline;
        }

        /* Blob Animation for Product Cards */
        @keyframes blob {
            0%, 100% { transform: translate(0, 0) scale(1); }
            25% { transform: translate(20px, -20px) scale(1.1); }
            50% { transform: translate(-20px, 20px) scale(0.9); }
            75% { transform: translate(20px, 20px) scale(1.05); }
        }

        .animate-blob {
            animation: blob 20s ease-in-out infinite;
        }

        .animation-delay-2000 {
            animation-delay: 2s;
        }
    </style>

    {{-- GLightbox CSS --}}
    <link rel="preload" href="https://cdn.jsdelivr.net/npm/glightbox@3.2.0/dist/css/glightbox.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox@3.2.0/dist/css/glightbox.min.css"></noscript>
</head>

<body class="antialiased overflow-x-hidden"
    x-data="homepage()" x-init="init()">

    <!-- Hero Section - Full Screen -->
    <section class="min-h-screen flex items-center relative overflow-hidden">
        <!-- Animated Background Blobs -->
        <div class="absolute inset-0 opacity-20">
            <div class="absolute top-20 -left-20 w-96 h-96 bg-purple-300 dark:bg-white rounded-full blur-3xl animate-pulse"></div>
            <div class="absolute bottom-20 -right-20 w-96 h-96 bg-blue-300 dark:bg-yellow-300 rounded-full blur-3xl animate-pulse" style="animation-delay: 1s;"></div>
        </div>

        <div class="container mx-auto px-6 relative z-10">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <!-- Left Content -->
                <div class="text-gray-900 dark:text-white">
                    <!-- Badge -->
                    <span class="inline-block bg-purple-100 dark:bg-white/20 backdrop-blur-lg text-purple-700 dark:text-white px-6 py-3 rounded-full text-sm font-bold mb-6 animate-bounce">
                        <i class="fa-solid fa-fire mr-2"></i>SÜPER KAMPANYA
                    </span>

                    <!-- Main Heading with Animation -->
                    <h1 class="text-7xl md:text-8xl font-black mb-6 leading-tight">
                        İ<span class="relative inline-block letter-switch">
                            <span x-show="showX"
                                  x-transition:enter="transition-opacity duration-500"
                                  x-transition:enter-start="opacity-0"
                                  x-transition:enter-end="opacity-100"
                                  x-transition:leave="transition-opacity duration-500"
                                  x-transition:leave-start="opacity-100"
                                  x-transition:leave-end="opacity-0"
                                  class="absolute inset-0 flex items-center justify-center">X</span>
                            <span x-show="!showX"
                                  x-transition:enter="transition-opacity duration-500"
                                  x-transition:enter-start="opacity-0"
                                  x-transition:enter-end="opacity-100"
                                  x-transition:leave="transition-opacity duration-500"
                                  x-transition:leave-start="opacity-100"
                                  x-transition:leave-end="opacity-0"
                                  class="absolute inset-0 flex items-center justify-center">S</span>
                            <span class="opacity-0">X</span>
                        </span>tif Ürünlerde
                        <span class="block text-orange-600 dark:text-yellow-300 mt-2">%70'e Varan İndirim</span>
                    </h1>

                    <!-- Slogan -->
                    <p class="text-2xl text-gray-700 dark:text-purple-100 mb-8 leading-relaxed font-semibold">
                        TÜRKİYE'NİN İSTİF PAZARI
                    </p>

                    <!-- Description -->
                    <p class="text-xl text-gray-600 dark:text-purple-100 mb-8 leading-relaxed">
                        20.000+ orijinal ürün, güvenli alışveriş, hızlı teslimat. Forkliftten mobilyaya her kategoride!
                    </p>

                    <!-- CTA Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 mb-10">
                        <a href="#shop" class="group bg-white text-purple-600 px-10 py-5 rounded-2xl font-bold text-lg hover:shadow-2xl transition-all inline-block text-center">
                            <i class="fa-solid fa-shopping-cart mr-2 inline-block group-hover:scale-125 group-hover:rotate-12 transition-all duration-300"></i>
                            Alışverişe Başla
                        </a>
                        <a href="#nasil-calisir" class="group bg-gray-100 dark:bg-white/10 backdrop-blur-lg text-gray-900 dark:text-white border-2 border-gray-300 dark:border-white/30 px-10 py-5 rounded-2xl font-bold text-lg hover:bg-gray-200 dark:hover:bg-white/20 transition-all inline-block text-center">
                            <i class="fa-solid fa-play mr-2 inline-block group-hover:scale-125 group-hover:rotate-12 transition-all duration-300"></i>
                            Nasıl Çalışır?
                        </a>
                    </div>

                    <!-- Stats -->
                    <div class="flex items-center gap-8 flex-wrap">
                        <div>
                            <div class="text-4xl font-black">128</div>
                            <div class="text-gray-600 dark:text-purple-200 text-sm">Forklift</div>
                        </div>
                        <div>
                            <div class="text-4xl font-black">106</div>
                            <div class="text-gray-600 dark:text-purple-200 text-sm">İstif Makinesi</div>
                        </div>
                        <div>
                            <div class="text-4xl font-black">69</div>
                            <div class="text-gray-600 dark:text-purple-200 text-sm">Transpalet</div>
                        </div>
                    </div>
                </div>

                <!-- Right Content - Floating Cards -->
                <div class="relative animate-float">
                    <div class="grid grid-cols-2 gap-8">
                        <div class="bg-white dark:bg-white/20 backdrop-blur-lg rounded-3xl p-8 border border-gray-200 dark:border-white/30 hover:shadow-xl dark:hover:bg-white/30 transition-all duration-300">
                            <i class="fa-solid fa-warehouse text-6xl text-purple-600 dark:text-white mb-4"></i>
                            <h3 class="font-bold text-gray-900 dark:text-white text-xl">Forklift</h3>
                        </div>
                        <div class="bg-white dark:bg-white/20 backdrop-blur-lg rounded-3xl p-8 border border-gray-200 dark:border-white/30 mt-8 hover:shadow-xl dark:hover:bg-white/30 transition-all duration-300">
                            <i class="fa-solid fa-boxes-stacked text-6xl text-purple-600 dark:text-white mb-4"></i>
                            <h3 class="font-bold text-gray-900 dark:text-white text-xl">İstif Makinesi</h3>
                        </div>
                        <div class="bg-white dark:bg-white/20 backdrop-blur-lg rounded-3xl p-8 border border-gray-200 dark:border-white/30 hover:shadow-xl dark:hover:bg-white/30 transition-all duration-300">
                            <i class="fa-solid fa-dolly text-6xl text-purple-600 dark:text-white mb-4"></i>
                            <h3 class="font-bold text-gray-900 dark:text-white text-xl">Transpalet</h3>
                        </div>
                        <div class="bg-white dark:bg-white/20 backdrop-blur-lg rounded-3xl p-8 border border-gray-200 dark:border-white/30 mt-8 hover:shadow-xl dark:hover:bg-white/30 transition-all duration-300">
                            <i class="fa-solid fa-truck-ramp-box text-6xl text-purple-600 dark:text-white mb-4"></i>
                            <h3 class="font-bold text-gray-900 dark:text-white text-xl">Yükleme</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Scroll Down Indicator -->
        <div class="absolute bottom-8 left-1/2 -translate-x-1/2 animate-bounce">
            <div class="flex flex-col items-center gap-2 text-gray-500 dark:text-white/80">
                <span class="text-sm font-semibold">Aşağı Kaydır</span>
                <i class="fa-solid fa-chevron-down text-2xl"></i>
            </div>
        </div>
    </section>

    <!-- Featured Products Section -->
    <section id="shop" class="py-20 bg-gradient-to-br from-gray-50 via-white to-gray-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 relative overflow-hidden">
        <!-- Background Blobs -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-20 left-10 w-72 h-72 bg-blue-300 dark:bg-blue-500 rounded-full blur-3xl animate-blob"></div>
            <div class="absolute bottom-20 right-10 w-72 h-72 bg-purple-300 dark:bg-purple-500 rounded-full blur-3xl animate-blob animation-delay-2000"></div>
        </div>

        <div class="container mx-auto px-6 relative z-10">
            <!-- Section Header -->
            <div class="text-center mb-16">
                <h2 class="text-5xl md:text-6xl font-black text-gray-900 dark:text-white mb-4">
                    Öne Çıkan <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600">Ürünler</span>
                </h2>
                <p class="text-xl text-gray-600 dark:text-gray-300">En çok tercih edilen istif ekipmanları</p>
            </div>

            <!-- Product Grid: 4 Kolonlu Modern Tasarım -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8 md:gap-10">
                <template x-for="product in products" :key="product.id">
                    <article class="group relative bg-white rounded-3xl overflow-hidden border border-gray-100 hover:border-gray-200 transition-all duration-500 hover:shadow-2xl hover:shadow-blue-500/10">
                        <a :href="product.url" class="block">
                            <!-- Image Section -->
                            <div class="relative aspect-square overflow-hidden bg-gradient-to-br from-gray-50 to-gray-100"
                                 x-data="{ imageLoaded: false }">
                                <template x-if="product.image">
                                    <div class="relative w-full h-full">
                                        {{-- Blur Placeholder - Mini görsel ANINDA --}}
                                        <img :src="product.image"
                                             :alt="product.title"
                                             x-show="!imageLoaded"
                                             class="absolute inset-0 w-full h-full object-cover blur-2xl scale-110">

                                        {{-- Actual Image - Net görsel --}}
                                        <img :src="product.image"
                                             :alt="product.title"
                                             x-show="imageLoaded"
                                             x-transition:enter="transition ease-out duration-300"
                                             x-transition:enter-start="opacity-0"
                                             x-transition:enter-end="opacity-100"
                                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700 relative z-10"
                                             @load="imageLoaded = true"
                                             loading="lazy">
                                    </div>
                                </template>
                                <template x-if="!product.image">
                                    <!-- Category Icon Fallback -->
                                    <div class="w-full h-full flex items-center justify-center">
                                        <i :class="product.category_icon || 'fa-light fa-box'"
                                           class="text-8xl text-blue-400 group-hover:scale-110 transition-transform"></i>
                                    </div>
                                </template>

                                <!-- Hover Overlay -->
                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>

                                <!-- Badges -->
                                <div class="absolute top-4 left-4 flex flex-col gap-2">
                                    <template x-if="product.featured">
                                        <span class="px-3 py-1.5 bg-yellow-500 text-white text-xs font-semibold rounded-lg shadow-lg">
                                            <i class="fa-solid fa-star mr-1"></i>Öne Çıkan
                                        </span>
                                    </template>
                                    <template x-if="product.bestseller">
                                        <span class="px-3 py-1.5 bg-red-500 text-white text-xs font-semibold rounded-lg shadow-lg">
                                            <i class="fa-solid fa-fire mr-1"></i>Çok Satan
                                        </span>
                                    </template>
                                </div>

                                <!-- Quick Actions -->
                                <div class="absolute top-4 right-4 flex flex-col gap-2 opacity-0 group-hover:opacity-100 transition-all duration-300 translate-x-4 group-hover:translate-x-0">
                                    <button @click.prevent="addToCart(product.id)"
                                            class="w-10 h-10 bg-blue-600 text-white rounded-lg shadow-lg hover:scale-110 hover:bg-blue-700 transition-all"
                                            title="Sepete Ekle">
                                        <i class="fa-solid fa-shopping-cart"></i>
                                    </button>
                                    <button @click.prevent="toggleFavorite(product.id)"
                                            class="w-10 h-10 bg-white text-gray-900 rounded-lg shadow-lg hover:scale-110 transition-transform">
                                        <i class="fa-solid fa-heart" :class="{'text-red-500': product.is_favorite}"></i>
                                    </button>
                                    <button @click.prevent="openProductModal(product)"
                                            class="w-10 h-10 bg-white text-gray-900 rounded-lg shadow-lg hover:scale-110 transition-transform">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Content Section -->
                            <div class="p-6 space-y-4">
                                <!-- Category -->
                                <div class="flex items-center gap-2">
                                    <span class="text-xs text-blue-600 font-medium uppercase tracking-wider" x-text="product.category"></span>
                                </div>

                                <!-- Title -->
                                <h3 class="text-xl font-bold text-gray-900 leading-tight line-clamp-2 group-hover:text-blue-600 transition-colors"
                                    x-text="product.title"></h3>

                                <!-- Description -->
                                <p class="text-sm text-gray-600 line-clamp-2 leading-relaxed" x-text="product.description"></p>

                                <!-- Price & CTA -->
                                <div class="pt-4 border-t border-gray-100 flex items-center justify-between">
                                    <template x-if="product.price && product.price > 0">
                                        <div class="text-2xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600"
                                             x-text="product.formatted_price"></div>
                                    </template>
                                    <div class="flex items-center gap-2 text-sm font-semibold text-blue-600 group-hover:gap-3 transition-all"
                                         :class="product.price && product.price > 0 ? '' : 'ml-auto'">
                                        <span>Özet</span>
                                        <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </article>
                </template>
            </div>

            <!-- View All Button -->
            <div class="text-center mt-12">
                <a href="/shop"
                   class="inline-flex items-center gap-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white px-10 py-4 rounded-2xl font-bold text-lg hover:from-blue-700 hover:to-purple-700 transition-all shadow-lg hover:shadow-xl">
                    <span>Tüm Ürünleri Gör</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </section>

    {{-- Alpine.js Component --}}
    <script>
        function homepage() {
            return {
                loaded: false,
                showX: false,
                products: @json($homepageProducts ?? []),

                init() {
                    this.$nextTick(() => {
                        this.loaded = true;

                        // GLightbox Initialize
                        if (typeof GLightbox !== 'undefined') {
                            const lightbox = GLightbox({
                                touchNavigation: true,
                                loop: true,
                                autoplayVideos: true
                            });
                        }
                    });

                    // İXTİF - İSTİF animasyonu (S ↔ X değişimi)
                    setInterval(() => {
                        this.showX = !this.showX;
                    }, 2000);
                },

                toggleFavorite(productId) {
                    const product = this.products.find(p => p.id === productId);
                    if (product) {
                        product.is_favorite = !product.is_favorite;
                        // TODO: API call to save favorite status
                        console.log(`Product ${productId} favorite: ${product.is_favorite}`);
                    }
                },

                openProductModal(product) {
                    // Modal için GLightbox kullan
                    if (typeof GLightbox !== 'undefined') {
                        const modalContent = `
                            <div class="p-8 bg-white rounded-2xl max-w-4xl">
                                <div class="grid md:grid-cols-2 gap-8">
                                    <!-- Left: Image -->
                                    <div class="aspect-square bg-gradient-to-br from-gray-50 to-gray-100 rounded-2xl overflow-hidden">
                                        ${product.image
                                            ? `<img src="${product.image}" alt="${product.title}" class="w-full h-full object-cover">`
                                            : `<div class="w-full h-full flex items-center justify-center">
                                                <i class="${product.category_icon || 'fa-light fa-box'} text-9xl text-blue-400"></i>
                                               </div>`
                                        }
                                    </div>

                                    <!-- Right: Info -->
                                    <div class="space-y-4">
                                        <div class="text-xs text-blue-600 font-medium uppercase tracking-wider">${product.category}</div>
                                        <h2 class="text-3xl font-bold text-gray-900">${product.title}</h2>
                                        <p class="text-gray-600">${product.description}</p>

                                        <div class="flex items-center gap-4 text-sm text-gray-500 pt-4 border-t border-gray-100">
                                            <span><i class="fa-solid fa-barcode mr-1"></i> ${product.sku}</span>
                                            <span><i class="fa-solid fa-eye mr-1"></i> ${product.views}</span>
                                        </div>

                                        <div class="pt-6">
                                            ${product.price && product.price > 0 ? `
                                                <div class="text-4xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600 mb-6">
                                                    ${product.price} ₺
                                                </div>
                                            ` : ''}
                                            <a href="${product.url}"
                                               class="inline-flex items-center gap-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white px-8 py-4 rounded-xl font-semibold hover:shadow-xl transition-all">
                                                <span>Detaylı İncele</span>
                                                <i class="fa-solid fa-arrow-right"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;

                        GLightbox({
                            elements: [{
                                content: modalContent
                            }],
                            touchNavigation: true,
                            closeButton: true,
                            closeOnOutsideClick: true
                        }).open();
                    } else {
                        // Fallback: Direct link
                        window.location.href = product.url;
                    }
                },

                addToCart(productId) {
                    // Livewire event dispatch
                    window.Livewire.emit('cartUpdated');

                    // AJAX call to add item to cart
                    fetch('/api/cart/add', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            product_id: productId,
                            quantity: 1
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Trigger cart update event
                            window.Livewire.emit('cartUpdated');

                            // Show success notification
                            console.log('✅ Ürün sepete eklendi!');

                            // Optional: Toast notification
                            if (typeof window.dispatchEvent !== 'undefined') {
                                window.dispatchEvent(new CustomEvent('product-added-to-cart', {
                                    detail: { message: 'Ürün sepete eklendi!' }
                                }));
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Sepete eklenirken hata:', error);
                        alert('Ürün sepete eklenirken bir hata oluştu.');
                    });
                }
            }
        }
    </script>

    {{-- GLightbox JS --}}
    <script defer src="https://cdn.jsdelivr.net/npm/glightbox@3.2.0/dist/js/glightbox.min.js"></script>

</body>
</html>

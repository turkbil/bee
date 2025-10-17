<!DOCTYPE html>
<html lang="tr" x-data="{ darkMode: false }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>İXtif - Türkiye'nin İstif Pazarı</title>

    {{-- Tailwind CSS CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Alpine.js CDN --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.5.1/css/all.css">

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
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
    </style>
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
                    <div class="grid grid-cols-2 gap-4">
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

    {{-- Alpine.js Component --}}
    <script>
        function homepage() {
            return {
                loaded: false,
                showX: false,

                init() {
                    this.$nextTick(() => {
                        this.loaded = true;
                    });

                    // İXTİF - İSTİF animasyonu (S ↔ X değişimi)
                    setInterval(() => {
                        this.showX = !this.showX;
                    }, 2000);
                }
            }
        }
    </script>

</body>
</html>

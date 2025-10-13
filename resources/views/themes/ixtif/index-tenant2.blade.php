@extends('themes.ixtif.layouts.app')

@section('content')
<div class="relative" x-data="homepage()" x-init="init()">
    <!-- Purple-Pink Gradient Background for Digital Agency -->
    <!-- Background removed - causing overlay issue -->
    
    <!-- Hero Section - Digital Agency Theme -->
    <div class="py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Main Title -->
            <div class="text-center mb-16">
                <h1 class="text-6xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent mb-6">
                    Dijital Ajans
                </h1>
                <p class="text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto mb-8">
                    Markanızı dijital dünyada güçlendiren yaratıcı çözümler üretiyoruz. Modern tasarım, güçlü kodlama ve etkileyici deneyimler.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <button class="px-8 py-4 bg-gradient-to-r from-purple-600 to-pink-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300">
                        Projelerinizi Görün
                    </button>
                    <button class="px-8 py-4 border-2 border-purple-600 text-purple-600 dark:text-purple-400 font-semibold rounded-xl hover:bg-purple-50 dark:hover:bg-purple-900/20 transition-all duration-300">
                        Hizmetlerimiz
                    </button>
                </div>
            </div>
            
            <!-- Services Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-16">
                <!-- Web Tasarım -->
                <div class="group relative bg-white/90 dark:bg-gray-800/90 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-500 overflow-hidden border border-purple-100 dark:border-purple-900/30" 
                     x-data="{ localHover: false }"
                     @mouseenter="localHover = true"
                     @mouseleave="localHover = false">
                    
                    <!-- Purple Gradient Overlay -->
                    <div class="absolute inset-0 bg-gradient-to-br from-purple-600/10 to-pink-600/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    
                    <div class="relative p-8">
                        <div class="flex items-center mb-6">
                            <div class="w-14 h-14 bg-gradient-to-r from-purple-500 to-pink-500 rounded-xl flex items-center justify-center mr-4 group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white group-hover:text-transparent group-hover:bg-gradient-to-r group-hover:from-purple-600 group-hover:to-pink-600 group-hover:bg-clip-text transition-all duration-300">
                                Web Tasarım
                            </h3>
                        </div>
                        <p class="text-gray-600 dark:text-gray-300 leading-relaxed mb-6">
                            Modern, responsive ve kullanıcı dostu web siteleri tasarlıyoruz. Her cihazda mükemmel görünüm.
                        </p>
                        <div class="inline-flex items-center text-sm font-semibold text-transparent bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text group-hover:from-purple-700 group-hover:to-pink-700 transition-all duration-300">
                            Keşfet
                            <svg class="h-4 w-4 ml-2 text-purple-600 group-hover:text-pink-600 transition-all duration-300"
                                 :class="localHover ? 'translate-x-2' : 'translate-x-0'"
                                 fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-purple-600 to-pink-600 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-500"></div>
                    </div>
                </div>

                <!-- Digital Marketing -->
                <div class="group relative bg-white/90 dark:bg-gray-800/90 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-500 overflow-hidden border border-purple-100 dark:border-purple-900/30" 
                     x-data="{ localHover: false }"
                     @mouseenter="localHover = true"
                     @mouseleave="localHover = false">
                    
                    <div class="absolute inset-0 bg-gradient-to-br from-purple-600/10 to-pink-600/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    
                    <div class="relative p-8">
                        <div class="flex items-center mb-6">
                            <div class="w-14 h-14 bg-gradient-to-r from-purple-500 to-pink-500 rounded-xl flex items-center justify-center mr-4 group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white group-hover:text-transparent group-hover:bg-gradient-to-r group-hover:from-purple-600 group-hover:to-pink-600 group-hover:bg-clip-text transition-all duration-300">
                                Dijital Pazarlama
                            </h3>
                        </div>
                        <p class="text-gray-600 dark:text-gray-300 leading-relaxed mb-6">
                            SEO, sosyal medya yönetimi ve dijital reklamlarla markanızı büyütüyoruz.
                        </p>
                        <div class="inline-flex items-center text-sm font-semibold text-transparent bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text group-hover:from-purple-700 group-hover:to-pink-700 transition-all duration-300">
                            Keşfet
                            <svg class="h-4 w-4 ml-2 text-purple-600 group-hover:text-pink-600 transition-all duration-300"
                                 :class="localHover ? 'translate-x-2' : 'translate-x-0'"
                                 fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-purple-600 to-pink-600 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-500"></div>
                    </div>
                </div>

                <!-- Brand Design -->
                <div class="group relative bg-white/90 dark:bg-gray-800/90 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-500 overflow-hidden border border-purple-100 dark:border-purple-900/30" 
                     x-data="{ localHover: false }"
                     @mouseenter="localHover = true"
                     @mouseleave="localHover = false">
                    
                    <div class="absolute inset-0 bg-gradient-to-br from-purple-600/10 to-pink-600/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    
                    <div class="relative p-8">
                        <div class="flex items-center mb-6">
                            <div class="w-14 h-14 bg-gradient-to-r from-purple-500 to-pink-500 rounded-xl flex items-center justify-center mr-4 group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v12a4 4 0 004 4h4a2 2 0 002-2V5z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white group-hover:text-transparent group-hover:bg-gradient-to-r group-hover:from-purple-600 group-hover:to-pink-600 group-hover:bg-clip-text transition-all duration-300">
                                Marka Tasarımı
                            </h3>
                        </div>
                        <p class="text-gray-600 dark:text-gray-300 leading-relaxed mb-6">
                            Logo tasarımından kurumsal kimliğe kadar markanızın görsel dilini yaratıyoruz.
                        </p>
                        <div class="inline-flex items-center text-sm font-semibold text-transparent bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text group-hover:from-purple-700 group-hover:to-pink-700 transition-all duration-300">
                            Keşfet
                            <svg class="h-4 w-4 ml-2 text-purple-600 group-hover:text-pink-600 transition-all duration-300"
                                 :class="localHover ? 'translate-x-2' : 'translate-x-0'"
                                 fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-purple-600 to-pink-600 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-500"></div>
                    </div>
                </div>
            </div>

            <!-- CTA Section -->
            <div class="text-center bg-white/70 dark:bg-gray-800/70 rounded-3xl p-12 border border-purple-200 dark:border-purple-900/50">
                <h2 class="text-4xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent mb-4">
                    Hayalinizdeki Projeyi Gerçekleştirelim
                </h2>
                <p class="text-lg text-gray-600 dark:text-gray-300 mb-8 max-w-2xl mx-auto">
                    Profesyonel ekibimizle birlikte dijital dünyadaki hedeflerinize ulaşın. Ücretsiz danışmanlık için hemen iletişime geçin.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <button class="px-8 py-4 bg-gradient-to-r from-purple-600 to-pink-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300">
                        Ücretsiz Teklif Alın
                    </button>
                    <button class="px-8 py-4 border-2 border-purple-600 text-purple-600 dark:text-purple-400 font-semibold rounded-xl hover:bg-purple-50 dark:hover:bg-purple-900/20 transition-all duration-300">
                        Portfolyoyu İnceleyin
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function homepage() {
    return {
        loaded: false,
        
        init() {
            this.$nextTick(() => {
                this.loaded = true;
            });
        }
    }
}
</script>
@endsection
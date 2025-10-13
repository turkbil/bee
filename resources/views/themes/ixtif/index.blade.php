@extends('themes.blank.layouts.app')

@section('content')
<div class="relative" x-data="homepage()" x-init="init()">
    <!-- Gradient Background -->
    <!-- Main background overlay removed - causing opacity issues -->
    
    <!-- Minimal Hero Section -->
    <div class="py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent mb-3">
                    Hoşgeldiniz
                </h1>
                <p class="text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                    Modern web çözümleri ile dijital dünyanızı keşfedin
                </p>
            </div>
            
            <!-- Quick Links -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Pages Link -->
                <div class="group relative bg-white dark:bg-gray-800 rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden border border-gray-100 dark:border-gray-700" 
                     x-data="{ localHover: false }"
                     @mouseenter="localHover = true"
                     @mouseleave="localHover = false">
                    
                    <!-- Gradient Overlay -->
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-600/5 to-purple-600/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    
                    <div class="relative p-5">
                        <div class="flex items-center mb-3">
                            <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-500 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white group-hover:text-transparent group-hover:bg-gradient-to-r group-hover:from-blue-600 group-hover:to-purple-600 group-hover:bg-clip-text transition-all duration-300">
                                Sayfalar
                            </h3>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed mb-4">
                            Kurumsal sayfalarımızı inceleyin
                        </p>
                        <div class="inline-flex items-center text-sm font-semibold text-transparent bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text group-hover:from-blue-700 group-hover:to-purple-700 transition-all duration-300">
                            İncele
                            <svg class="h-4 w-4 ml-2 text-blue-600 group-hover:text-purple-600 transition-all duration-300"
                                 :class="localHover ? 'translate-x-1' : 'translate-x-0'"
                                 fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-blue-600 to-purple-600 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300"></div>
                    </div>
                </div>

                <!-- Announcements Link -->
                <div class="group relative bg-white dark:bg-gray-800 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 dark:border-gray-700" 
                     x-data="{ localHover: false }"
                     @mouseenter="localHover = true"
                     @mouseleave="localHover = false">
                    
                    <!-- Gradient Overlay -->
                    <div class="absolute inset-0 bg-gradient-to-br from-amber-600/5 to-orange-600/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    
                    <div class="relative p-6">
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 bg-gradient-to-r from-amber-500 to-orange-500 rounded-xl flex items-center justify-center mr-4">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white group-hover:text-transparent group-hover:bg-gradient-to-r group-hover:from-amber-600 group-hover:to-orange-600 group-hover:bg-clip-text transition-all duration-300">
                                Duyurular
                            </h3>
                        </div>
                        <p class="text-gray-600 dark:text-gray-300 leading-relaxed mb-6">
                            Güncel duyuru ve haberlerimiz
                        </p>
                        <div class="inline-flex items-center text-sm font-semibold text-transparent bg-gradient-to-r from-amber-600 to-orange-600 bg-clip-text group-hover:from-amber-700 group-hover:to-orange-700 transition-all duration-300">
                            İncele
                            <svg class="h-4 w-4 ml-2 text-amber-600 group-hover:text-orange-600 transition-all duration-300"
                                 :class="localHover ? 'translate-x-1' : 'translate-x-0'"
                                 fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-amber-600 to-orange-600 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300"></div>
                    </div>
                </div>

                <!-- Portfolio Link -->
                <div class="group relative bg-white dark:bg-gray-800 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 dark:border-gray-700" 
                     x-data="{ localHover: false }"
                     @mouseenter="localHover = true"
                     @mouseleave="localHover = false">
                    
                    <!-- Gradient Overlay -->
                    <div class="absolute inset-0 bg-gradient-to-br from-green-600/5 to-teal-600/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    
                    <div class="relative p-6">
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-teal-500 rounded-xl flex items-center justify-center mr-4">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white group-hover:text-transparent group-hover:bg-gradient-to-r group-hover:from-green-600 group-hover:to-teal-600 group-hover:bg-clip-text transition-all duration-300">
                                Portfolyo
                            </h3>
                        </div>
                        <p class="text-gray-600 dark:text-gray-300 leading-relaxed mb-6">
                            Yapmış olduğumuz projelerimizi inceleyin
                        </p>
                        <div class="inline-flex items-center text-sm font-semibold text-transparent bg-gradient-to-r from-green-600 to-teal-600 bg-clip-text group-hover:from-green-700 group-hover:to-teal-700 transition-all duration-300">
                            İncele  
                            <svg class="h-4 w-4 ml-2 text-green-600 group-hover:text-teal-600 transition-all duration-300"
                                 :class="localHover ? 'translate-x-1' : 'translate-x-0'"
                                 fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-green-600 to-teal-600 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300"></div>
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
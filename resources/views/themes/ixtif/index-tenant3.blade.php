@extends('themes.blank.layouts.app')

@section('content')
<div class="relative" x-data="homepage()" x-init="init()">
    <!-- Green-Blue Gradient Background for Tech Solutions -->
    <!-- Background removed - causing overlay issue -->
    
    <!-- Hero Section - Tech Solutions Theme -->
    <div class="py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Main Title -->
            <div class="text-center mb-16">
                <h1 class="text-6xl font-bold bg-gradient-to-r from-green-600 to-blue-600 bg-clip-text text-transparent mb-6">
                    Tech Solutions
                </h1>
                <p class="text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto mb-8">
                    Innovation Hub - Cutting-edge technology solutions that drive your business forward. AI-powered systems, cloud infrastructure, and smart automation.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <button class="px-8 py-4 bg-gradient-to-r from-green-600 to-blue-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300">
                        Explore Solutions
                    </button>
                    <button class="px-8 py-4 border-2 border-green-600 text-green-600 dark:text-green-400 font-semibold rounded-xl hover:bg-green-50 dark:hover:bg-green-900/20 transition-all duration-300">
                        Our Services
                    </button>
                </div>
            </div>
            
            <!-- Solutions Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-16">
                <!-- AI Solutions -->
                <div class="group relative bg-white/90 dark:bg-gray-800/90 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-500 overflow-hidden border border-green-100 dark:border-green-900/30" 
                     x-data="{ localHover: false }"
                     @mouseenter="localHover = true"
                     @mouseleave="localHover = false">
                    
                    <!-- Green Gradient Overlay -->
                    <div class="absolute inset-0 bg-gradient-to-br from-green-600/10 to-blue-600/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    
                    <div class="relative p-8">
                        <div class="flex items-center mb-6">
                            <div class="w-14 h-14 bg-gradient-to-r from-green-500 to-blue-500 rounded-xl flex items-center justify-center mr-4 group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white group-hover:text-transparent group-hover:bg-gradient-to-r group-hover:from-green-600 group-hover:to-blue-600 group-hover:bg-clip-text transition-all duration-300">
                                AI Solutions
                            </h3>
                        </div>
                        <p class="text-gray-600 dark:text-gray-300 leading-relaxed mb-6">
                            Machine learning algorithms and AI-powered automation to optimize your business processes and decision making.
                        </p>
                        <div class="inline-flex items-center text-sm font-semibold text-transparent bg-gradient-to-r from-green-600 to-blue-600 bg-clip-text group-hover:from-green-700 group-hover:to-blue-700 transition-all duration-300">
                            Learn More
                            <svg class="h-4 w-4 ml-2 text-green-600 group-hover:text-blue-600 transition-all duration-300"
                                 :class="localHover ? 'translate-x-2' : 'translate-x-0'"
                                 fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-green-600 to-blue-600 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-500"></div>
                    </div>
                </div>

                <!-- Cloud Infrastructure -->
                <div class="group relative bg-white/90 dark:bg-gray-800/90 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-500 overflow-hidden border border-green-100 dark:border-green-900/30" 
                     x-data="{ localHover: false }"
                     @mouseenter="localHover = true"
                     @mouseleave="localHover = false">
                    
                    <div class="absolute inset-0 bg-gradient-to-br from-green-600/10 to-blue-600/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    
                    <div class="relative p-8">
                        <div class="flex items-center mb-6">
                            <div class="w-14 h-14 bg-gradient-to-r from-green-500 to-blue-500 rounded-xl flex items-center justify-center mr-4 group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white group-hover:text-transparent group-hover:bg-gradient-to-r group-hover:from-green-600 group-hover:to-blue-600 group-hover:bg-clip-text transition-all duration-300">
                                Cloud Infrastructure
                            </h3>
                        </div>
                        <p class="text-gray-600 dark:text-gray-300 leading-relaxed mb-6">
                            Scalable, secure cloud solutions with 99.9% uptime guarantee. AWS, Azure, and Google Cloud expertise.
                        </p>
                        <div class="inline-flex items-center text-sm font-semibold text-transparent bg-gradient-to-r from-green-600 to-blue-600 bg-clip-text group-hover:from-green-700 group-hover:to-blue-700 transition-all duration-300">
                            Learn More
                            <svg class="h-4 w-4 ml-2 text-green-600 group-hover:text-blue-600 transition-all duration-300"
                                 :class="localHover ? 'translate-x-2' : 'translate-x-0'"
                                 fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-green-600 to-blue-600 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-500"></div>
                    </div>
                </div>

                <!-- IoT & Smart Systems -->
                <div class="group relative bg-white/90 dark:bg-gray-800/90 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-500 overflow-hidden border border-green-100 dark:border-green-900/30" 
                     x-data="{ localHover: false }"
                     @mouseenter="localHover = true"
                     @mouseleave="localHover = false">
                    
                    <div class="absolute inset-0 bg-gradient-to-br from-green-600/10 to-blue-600/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    
                    <div class="relative p-8">
                        <div class="flex items-center mb-6">
                            <div class="w-14 h-14 bg-gradient-to-r from-green-500 to-blue-500 rounded-xl flex items-center justify-center mr-4 group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white group-hover:text-transparent group-hover:bg-gradient-to-r group-hover:from-green-600 group-hover:to-blue-600 group-hover:bg-clip-text transition-all duration-300">
                                IoT & Smart Systems
                            </h3>
                        </div>
                        <p class="text-gray-600 dark:text-gray-300 leading-relaxed mb-6">
                            Connected devices and intelligent automation systems for industrial and commercial applications.
                        </p>
                        <div class="inline-flex items-center text-sm font-semibold text-transparent bg-gradient-to-r from-green-600 to-blue-600 bg-clip-text group-hover:from-green-700 group-hover:to-blue-700 transition-all duration-300">
                            Learn More
                            <svg class="h-4 w-4 ml-2 text-green-600 group-hover:text-blue-600 transition-all duration-300"
                                 :class="localHover ? 'translate-x-2' : 'translate-x-0'"
                                 fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-green-600 to-blue-600 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-500"></div>
                    </div>
                </div>
            </div>

            <!-- CTA Section -->
            <div class="text-center bg-white/90 dark:bg-gray-800/90 rounded-3xl p-12 border border-green-100 dark:border-green-900/30">
                <h2 class="text-4xl font-bold bg-gradient-to-r from-green-600 to-blue-600 bg-clip-text text-transparent mb-4">
                    Transform Your Business with Technology
                </h2>
                <p class="text-lg text-gray-600 dark:text-gray-300 mb-8 max-w-2xl mx-auto">
                    Partner with us to implement cutting-edge technology solutions that will revolutionize your operations and accelerate growth.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <button class="px-8 py-4 bg-gradient-to-r from-green-600 to-blue-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300">
                        Get Free Consultation
                    </button>
                    <button class="px-8 py-4 border-2 border-green-600 text-green-600 dark:text-green-400 font-semibold rounded-xl hover:bg-green-50 dark:hover:bg-green-900/20 transition-all duration-300">
                        View Case Studies
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
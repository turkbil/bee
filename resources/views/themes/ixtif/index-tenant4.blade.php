@extends('themes.blank.layouts.app')

@section('content')
<div class="relative" x-data="homepage()" x-init="init()">
    <!-- Indigo-Cyan Gradient Background for SaaS Platform -->
    <!-- Background removed - causing overlay issue -->
    
    <!-- Hero Section - SaaS Platform Theme -->
    <div class="py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Main Title -->
            <div class="text-center mb-16">
                <h1 class="text-6xl font-bold bg-gradient-to-r from-indigo-600 to-cyan-600 bg-clip-text text-transparent mb-6">
                    SaaS Platform
                </h1>
                <p class="text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto mb-8">
                    Streamline Your Business - All-in-one cloud platform to automate workflows, manage teams, and scale operations efficiently. Built for modern enterprises.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <button class="px-8 py-4 bg-gradient-to-r from-indigo-600 to-cyan-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300">
                        Start Free Trial
                    </button>
                    <button class="px-8 py-4 border-2 border-indigo-600 text-indigo-600 dark:text-indigo-400 font-semibold rounded-xl hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-all duration-300">
                        View Pricing
                    </button>
                </div>
            </div>
            
            <!-- Features Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-16">
                <!-- Workflow Automation -->
                <div class="group relative bg-white/90 dark:bg-gray-800/90 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-500 overflow-hidden border border-indigo-100 dark:border-indigo-900/30" 
                     x-data="{ localHover: false }"
                     @mouseenter="localHover = true"
                     @mouseleave="localHover = false">
                    
                    <!-- Indigo Gradient Overlay -->
                    <div class="absolute inset-0 bg-gradient-to-br from-indigo-600/10 to-cyan-600/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    
                    <div class="relative p-8">
                        <div class="flex items-center mb-6">
                            <div class="w-14 h-14 bg-gradient-to-r from-indigo-500 to-cyan-500 rounded-xl flex items-center justify-center mr-4 group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white group-hover:text-transparent group-hover:bg-gradient-to-r group-hover:from-indigo-600 group-hover:to-cyan-600 group-hover:bg-clip-text transition-all duration-300">
                                Workflow Automation
                            </h3>
                        </div>
                        <p class="text-gray-600 dark:text-gray-300 leading-relaxed mb-6">
                            Automate repetitive tasks with intelligent workflows. Save time and reduce errors with our drag-and-drop automation builder.
                        </p>
                        <div class="inline-flex items-center text-sm font-semibold text-transparent bg-gradient-to-r from-indigo-600 to-cyan-600 bg-clip-text group-hover:from-indigo-700 group-hover:to-cyan-700 transition-all duration-300">
                            Explore
                            <svg class="h-4 w-4 ml-2 text-indigo-600 group-hover:text-cyan-600 transition-all duration-300"
                                 :class="localHover ? 'translate-x-2' : 'translate-x-0'"
                                 fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-indigo-600 to-cyan-600 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-500"></div>
                    </div>
                </div>

                <!-- Team Collaboration -->
                <div class="group relative bg-white/90 dark:bg-gray-800/90 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-500 overflow-hidden border border-indigo-100 dark:border-indigo-900/30" 
                     x-data="{ localHover: false }"
                     @mouseenter="localHover = true"
                     @mouseleave="localHover = false">
                    
                    <div class="absolute inset-0 bg-gradient-to-br from-indigo-600/10 to-cyan-600/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    
                    <div class="relative p-8">
                        <div class="flex items-center mb-6">
                            <div class="w-14 h-14 bg-gradient-to-r from-indigo-500 to-cyan-500 rounded-xl flex items-center justify-center mr-4 group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white group-hover:text-transparent group-hover:bg-gradient-to-r group-hover:from-indigo-600 group-hover:to-cyan-600 group-hover:bg-clip-text transition-all duration-300">
                                Team Collaboration
                            </h3>
                        </div>
                        <p class="text-gray-600 dark:text-gray-300 leading-relaxed mb-6">
                            Real-time collaboration tools with chat, video calls, file sharing, and project management in one unified platform.
                        </p>
                        <div class="inline-flex items-center text-sm font-semibold text-transparent bg-gradient-to-r from-indigo-600 to-cyan-600 bg-clip-text group-hover:from-indigo-700 group-hover:to-cyan-700 transition-all duration-300">
                            Explore
                            <svg class="h-4 w-4 ml-2 text-indigo-600 group-hover:text-cyan-600 transition-all duration-300"
                                 :class="localHover ? 'translate-x-2' : 'translate-x-0'"
                                 fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-indigo-600 to-cyan-600 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-500"></div>
                    </div>
                </div>

                <!-- Analytics & Reports -->
                <div class="group relative bg-white/90 dark:bg-gray-800/90 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-500 overflow-hidden border border-indigo-100 dark:border-indigo-900/30" 
                     x-data="{ localHover: false }"
                     @mouseenter="localHover = true"
                     @mouseleave="localHover = false">
                    
                    <div class="absolute inset-0 bg-gradient-to-br from-indigo-600/10 to-cyan-600/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    
                    <div class="relative p-8">
                        <div class="flex items-center mb-6">
                            <div class="w-14 h-14 bg-gradient-to-r from-indigo-500 to-cyan-500 rounded-xl flex items-center justify-center mr-4 group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white group-hover:text-transparent group-hover:bg-gradient-to-r group-hover:from-indigo-600 group-hover:to-cyan-600 group-hover:bg-clip-text transition-all duration-300">
                                Analytics & Reports
                            </h3>
                        </div>
                        <p class="text-gray-600 dark:text-gray-300 leading-relaxed mb-6">
                            Advanced analytics dashboard with real-time metrics, custom reports, and AI-powered business insights.
                        </p>
                        <div class="inline-flex items-center text-sm font-semibold text-transparent bg-gradient-to-r from-indigo-600 to-cyan-600 bg-clip-text group-hover:from-indigo-700 group-hover:to-cyan-700 transition-all duration-300">
                            Explore
                            <svg class="h-4 w-4 ml-2 text-indigo-600 group-hover:text-cyan-600 transition-all duration-300"
                                 :class="localHover ? 'translate-x-2' : 'translate-x-0'"
                                 fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-indigo-600 to-cyan-600 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-500"></div>
                    </div>
                </div>
            </div>

            <!-- CTA Section -->
            <div class="text-center bg-white/90 dark:bg-gray-800/90 rounded-3xl p-12 border border-indigo-100 dark:border-indigo-900/30">
                <h2 class="text-4xl font-bold bg-gradient-to-r from-indigo-600 to-cyan-600 bg-clip-text text-transparent mb-4">
                    Ready to Scale Your Business?
                </h2>
                <p class="text-lg text-gray-600 dark:text-gray-300 mb-8 max-w-2xl mx-auto">
                    Join thousands of companies already using our platform to streamline operations and accelerate growth. Start your free trial today.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <button class="px-8 py-4 bg-gradient-to-r from-indigo-600 to-cyan-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300">
                        Start Free Trial
                    </button>
                    <button class="px-8 py-4 border-2 border-indigo-600 text-indigo-600 dark:text-indigo-400 font-semibold rounded-xl hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-all duration-300">
                        Schedule Demo
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
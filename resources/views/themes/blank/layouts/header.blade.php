<!DOCTYPE html>
<html lang="tr" x-data="{ darkMode: localStorage.getItem('darkMode') || 'light' }"
    x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))" :class="{ 'dark': darkMode === 'dark' }">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ $title ?? 'Sayfa Başlığı' }} - {{ config('app.name') }}</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                        }
                    }
                }
            }
        }
    </script>
    {{-- Livewire Styles --}}
    @livewireStyles

    {{-- Genel Organization Schema.org - Tüm tenant'larda otomatik --}}
    <script type="application/ld+json">
        {
        "@context": "https://schema.org",
        "@type": "Organization",
        "name": "{{ config('app.name') }}",
        "url": "{{ url('/') }}",
        "sameAs": []
    }
    </script>

    {{-- SEO ve Schema.org için alan --}}
    @stack('head')
    @stack('styles')
</head>

<body
    class="font-sans antialiased min-h-screen bg-gray-50 text-gray-800 dark:bg-gray-900 dark:text-gray-200 transition-colors duration-300">


    <header class="bg-white shadow dark:bg-gray-800 transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <span class="text-2xl font-bold text-primary-600 dark:text-primary-400">{{ config('app.name')
                            }}</span>
                    </div>
                    <nav class="ml-6 flex space-x-4">
                        <a href="{{ url('/') }}"
                            class="px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-100 dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-700 transition-colors duration-300">Ana
                            Sayfa</a>
                        <a href="{{ href('Page', 'index') }}"
                            class="px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-100 dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-700 transition-colors duration-300">Sayfalar</a>
                        <a href="{{ href('Announcement', 'index') }}"
                            class="px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-100 dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-700 transition-colors duration-300">Duyurular</a>
                        <a href="{{ href('Portfolio', 'index') }}"
                            class="px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-100 dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-700 transition-colors duration-300">Portfolyo</a>
                        <a href="#"
                            class="px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-100 dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-700 transition-colors duration-300">Hakkımızda</a>
                        <a href="#"
                            class="px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-100 dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-700 transition-colors duration-300">İletişim</a>
                        @auth
                        @if(Auth::user()->roles->count() > 0)
                        <a href="/admin/dashboard"
                            class="px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-100 dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-700 transition-colors duration-300">Admin
                            Paneli</a>
                        @endif
                        @endauth
                    </nav>
                </div>
                <div class="flex items-center space-x-3">
                    {{-- Site Dil Değiştirici - OPTİMİZE EDİLMİŞ --}}
                    <div class="language-switcher-header relative" x-data="{ open: false }">
                        @php
                            $currentLang = app()->getLocale();
                            $siteLanguages = collect();
                            $currentLangObj = null;
                            $debugInfo = [];
                            
                            try {
                                $debugInfo['tenant_exists'] = tenant() ? 'yes' : 'no';
                                $debugInfo['tenant_id'] = tenant() ? tenant()->id : 'null';
                                
                                if (tenant()) {
                                    $siteLanguages = tenant()->siteLanguages()
                                        ->where('is_active', 1)
                                        ->orderBy('sort_order')
                                        ->get();
                                    $debugInfo['source'] = 'tenant';
                                } else {
                                    // Tenant yoksa direkt SiteLanguage modelinden çek
                                    $siteLanguages = \Modules\LanguageManagement\app\Models\SiteLanguage::where('is_active', 1)
                                        ->orderBy('sort_order')
                                        ->get();
                                    $debugInfo['source'] = 'direct';
                                }
                                
                                $debugInfo['languages_count'] = $siteLanguages->count();
                                $debugInfo['current_lang'] = $currentLang;
                                
                                $currentLangObj = $siteLanguages->where('code', $currentLang)->first();
                                $debugInfo['current_lang_found'] = $currentLangObj ? 'yes' : 'no';
                            } catch (\Exception $e) {
                                $debugInfo['error'] = $e->getMessage();
                                \Log::error('Header dil yükleme hatası: ' . $e->getMessage());
                                // Exception durumunda fallback
                                $siteLanguages = collect();
                            }
                        @endphp
                        
                        {{-- DEBUG BİLGİSİ --}}
                        <!-- DEBUG: {{ json_encode($debugInfo) }} -->
                        
                        <button @click="open = !open" 
                                class="flex items-center justify-center w-10 h-10 text-gray-700 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white transition-colors duration-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800">
                            {{ $currentLangObj->flag_icon ?? '🌐' }}
                        </button>
                        
                        <div x-show="open" 
                             @click.away="open = false"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute right-0 top-full mt-2 w-44 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-1 z-50">
                            
                            @foreach($siteLanguages as $lang)
                                <a href="/language/{{ $lang->code }}" 
                                   class="w-full flex items-center px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 {{ $lang->code === $currentLang ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : '' }}">
                                    <span class="mr-2 text-base">{{ $lang->flag_icon ?? '🌐' }}</span>
                                    <span class="flex-1 text-left">{{ $lang->native_name ?? $lang->name }}</span>
                                    @if($lang->code === $currentLang)
                                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    </div>
                    
                    <button @click="darkMode = darkMode === 'dark' ? 'light' : 'dark'"
                        class="p-2 rounded-md text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white transition-colors duration-300">
                        <template x-if="darkMode === 'dark'">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z">
                                </path>
                            </svg>
                        </template>
                        <template x-if="darkMode === 'light'">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z">
                                </path>
                            </svg>
                        </template>
                    </button>
                    {{-- AUTH CONTROL VIA LIVEWIRE - CACHE-SAFE --}}
                    @livewire('auth.header-menu')
                </div>
            </div>
        </div>
    </header>
@extends('themes.blank.layouts.app')

@section('module_content')
<div class="relative" x-data="portfolioShow()" x-init="init()">
    <!-- Gradient Background -->
    <div class="absolute inset-0 bg-gradient-to-br from-green-50 via-white to-teal-50 dark:from-gray-900 dark:via-gray-900 dark:to-gray-800 -z-10"></div>
    
    @php
        $currentLocale = app()->getLocale();
        
        // Direct JSON field access with proper decoding
        $titleData = $item->title;
        
        // If string, decode JSON
        if (is_string($titleData)) {
            $titleData = json_decode($titleData, true) ?: [];
        }
        
        // Extract current language content
        $title = is_array($titleData) ? ($titleData[$currentLocale] ?? $titleData['tr'] ?? reset($titleData)) : $titleData;
        $title = $title ?: 'Başlıksız';
        
        // Category title ve slug handling
        $categoryTitle = null;
        $categorySlug = null;
        $categoryDynamicUrl = null;
        
        if (isset($item->category)) {
            $categoryTitleData = $item->category->title;
            if (is_string($categoryTitleData)) {
                $categoryTitleData = json_decode($categoryTitleData, true) ?: [];
            }
            $categoryTitle = is_array($categoryTitleData) ? ($categoryTitleData[$currentLocale] ?? $categoryTitleData['tr'] ?? reset($categoryTitleData)) : $categoryTitleData;
            
            $categorySlugData = $item->category->slug;
            if (is_string($categorySlugData)) {
                $categorySlugData = json_decode($categorySlugData, true) ?: [];
            }
            $categorySlug = is_array($categorySlugData) ? ($categorySlugData[$currentLocale] ?? $categorySlugData['tr'] ?? reset($categorySlugData)) : $categorySlugData;
            
            // DİNAMİK URL'ler - LOCALE AWARE
            $moduleSlugService = app(\App\Services\ModuleSlugService::class);
            
            // Mevcut dil için index slug'ını al
            $indexSlug = $moduleSlugService->getMultiLangSlug('Portfolio', 'index', $currentLocale);
            
            // Mevcut dil için category slug'ını al
            $categoryActionSlug = $moduleSlugService->getMultiLangSlug('Portfolio', 'category', $currentLocale);
            
            // Locale prefix'i kontrol et
            $defaultLocale = get_tenant_default_locale();
            $localePrefix = ($currentLocale !== $defaultLocale) ? '/' . $currentLocale : '';
            
            $portfolioIndexUrl = $localePrefix . '/' . $indexSlug;
            $categoryDynamicUrl = $localePrefix . '/' . $indexSlug . '/' . $categoryActionSlug . '/' . $categorySlug;
        }
        
        // Body için JSON decode
        $bodyData = $item->body;
        if (is_string($bodyData)) {
            $bodyData = json_decode($bodyData, true) ?: [];
        }
        $body = is_array($bodyData) ? ($bodyData[$currentLocale] ?? $bodyData['tr'] ?? reset($bodyData)) : $bodyData;
    @endphp
    
    <!-- Simple Title Section -->
    <div class="py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-12">
                <h1 class="text-5xl font-bold bg-gradient-to-r from-gray-900 to-gray-700 dark:from-white dark:to-gray-300 bg-clip-text text-transparent mb-4">
                    {{ $title }}
                </h1>
                
                <div class="flex flex-wrap items-center gap-4 text-sm">
                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        {{ $item->created_at->format('d F Y') }}
                    </span>
                    
                    @if(isset($item->category))
                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-teal-100 dark:bg-teal-900/30 text-teal-700 dark:text-teal-300">
                        <svg class="h-4 w-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M17.707 9.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7A.997.997 0 012 10V5a3 3 0 013-3h5c.256 0 .512.098.707.293l7 7zM5 6a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                        </svg>
                        {{ $categoryTitle }}
                    </span>
                    @endif
                </div>
            </div>
            
            <!-- Project Image -->
            @if($item->getMedia('images')->isNotEmpty() || true)
            <div class="mb-8">
                @if($item->getMedia('images')->isNotEmpty())
                <img src="{{ $item->getFirstMedia('images')->getUrl() }}" alt="{{ $title }}" class="w-full h-80 md:h-96 object-cover rounded-2xl shadow-xl">
                @else
                <div class="w-full h-80 md:h-96 bg-gradient-to-br from-green-100 to-teal-100 dark:from-green-900/20 dark:to-teal-900/20 flex items-center justify-center rounded-2xl shadow-xl">
                    <div class="text-center">
                        <svg class="w-16 h-16 text-green-400 dark:text-green-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        <p class="text-lg font-medium text-green-600 dark:text-green-400">{{ $title }}</p>
                    </div>
                </div>
                @endif
            </div>
            @endif
            
            <!-- Content without card wrapper -->
            <article class="prose prose-lg max-w-none dark:prose-invert 
                          prose-headings:font-bold prose-headings:text-gray-900 dark:prose-headings:text-white 
                          prose-p:text-gray-600 dark:prose-p:text-gray-300 prose-p:leading-relaxed
                          prose-a:text-transparent prose-a:bg-gradient-to-r prose-a:from-green-600 prose-a:to-teal-600 prose-a:bg-clip-text hover:prose-a:from-green-700 hover:prose-a:to-teal-700
                          prose-strong:text-gray-900 dark:prose-strong:text-white
                          prose-blockquote:border-l-4 prose-blockquote:border-green-500 prose-blockquote:bg-green-50 dark:prose-blockquote:bg-green-900/20 prose-blockquote:italic
                          prose-code:text-teal-600 dark:prose-code:text-teal-400 prose-code:bg-teal-50 dark:prose-code:bg-teal-900/20 prose-code:px-1 prose-code:py-0.5 prose-code:rounded
                          prose-pre:bg-gray-900 prose-pre:shadow-xl
                          prose-img:rounded-xl prose-img:shadow-lg">
                @parsewidgets($body ?? '')
            </article>

            <!-- Project Details -->
            @if(isset($item->client) || isset($item->date) || isset($item->url))
            <div class="mt-8 pt-8 border-t border-gray-200 dark:border-gray-700">
                <h3 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white">{{ __('portfolio::front.general.project_details') }}</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @if(isset($item->client))
                    <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-xl border border-green-200 dark:border-green-800">
                        <div class="flex items-center mb-2">
                            <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <span class="font-semibold text-green-900 dark:text-green-100">{{ __('portfolio::front.general.client') }}</span>
                        </div>
                        <span class="text-green-800 dark:text-green-200">{{ $item->client }}</span>
                    </div>
                    @endif
                    
                    @if(isset($item->date))
                    <div class="bg-teal-50 dark:bg-teal-900/20 p-4 rounded-xl border border-teal-200 dark:border-teal-800">
                        <div class="flex items-center mb-2">
                            <svg class="w-5 h-5 text-teal-600 dark:text-teal-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span class="font-semibold text-teal-900 dark:text-teal-100">{{ __('portfolio::front.general.project_date') }}</span>
                        </div>
                        <span class="text-teal-800 dark:text-teal-200">{{ $item->date }}</span>
                    </div>
                    @endif
                    
                    @if(isset($item->url))
                    <div class="md:col-span-2 bg-blue-50 dark:bg-blue-900/20 p-4 rounded-xl border border-blue-200 dark:border-blue-800">
                        <div class="flex items-center mb-2">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                            </svg>
                            <span class="font-semibold text-blue-900 dark:text-blue-100">{{ __('portfolio::front.general.project_url') }}</span>
                        </div>
                        <a href="{{ $item->url }}" target="_blank" class="inline-flex items-center text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium transition-colors">
                            {{ $item->url }}
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                            </svg>
                        </a>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Navigation -->
            <div class="mt-12 pt-8 border-t border-gray-200 dark:border-gray-700">
                <div class="flex flex-wrap justify-between items-center gap-4">
                    <a href="{{ $portfolioIndexUrl }}" 
                       class="group inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-600 to-teal-600 text-white font-semibold rounded-xl hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200"
                       x-data="{ hover: false }"
                       @mouseenter="hover = true"
                       @mouseleave="hover = false">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 transform transition-transform duration-200"
                             :class="hover ? '-translate-x-1' : ''"
                             fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        {{ __('portfolio::front.general.all_portfolios') }}
                    </a>
                    
                    @if(isset($item->category))
                    <a href="{{ $categoryDynamicUrl }}" 
                       class="group inline-flex items-center px-6 py-3 bg-gradient-to-r from-teal-600 to-green-600 text-white font-semibold rounded-xl hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200"
                       x-data="{ hover: false }"
                       @mouseenter="hover = true"
                       @mouseleave="hover = false">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                        {{ $categoryTitle }} {{ __('portfolio::front.general.portfolios') }}
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@extends('themes.blank.layouts.app')

@section('module_content')
<div class="min-h-screen">
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
            
            $moduleSlugService = app(\App\Services\ModuleSlugService::class);
            $indexSlug = $moduleSlugService->getMultiLangSlug('Portfolio', 'index', $currentLocale);
            $categoryActionSlug = $moduleSlugService->getMultiLangSlug('Portfolio', 'category', $currentLocale);
            
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
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">        
        @if($item->getMedia('images')->isNotEmpty())
        <div class="mb-12">
            <img src="{{ $item->getFirstMedia('images')->getUrl() }}" alt="{{ $title }}" class="w-full rounded-lg shadow-lg">
        </div>
        @endif
        
        <article class="prose prose-xl max-w-none dark:prose-invert 
                      prose-headings:text-gray-900 dark:prose-headings:text-white 
                      prose-p:text-gray-700 dark:prose-p:text-gray-300 
                      prose-a:text-blue-600 dark:prose-a:text-blue-400 hover:prose-a:text-blue-700 dark:hover:prose-a:text-blue-300
                      prose-strong:text-gray-900 dark:prose-strong:text-white
                      prose-blockquote:border-l-blue-500 prose-blockquote:bg-blue-50/50 dark:prose-blockquote:bg-blue-900/10
                      prose-code:text-blue-600 dark:prose-code:text-blue-400 prose-code:bg-blue-50 dark:prose-code:bg-blue-900/20
                      prose-pre:bg-gray-900 dark:prose-pre:bg-gray-800
                      prose-img:rounded-lg">
            @parsewidgets($body ?? '')
        </article>

        @if(isset($item->client) || isset($item->date) || isset($item->url))
        <div class="mt-12 pt-8 border-t border-gray-200 dark:border-gray-700">
            <h3 class="text-xl font-semibold mb-6 text-gray-900 dark:text-white">{{ __('portfolio::front.general.project_details') }}</h3>
            <div class="grid gap-4">
                @if(isset($item->client))
                <div class="flex justify-between py-2">
                    <span class="font-medium text-gray-600 dark:text-gray-400">{{ __('portfolio::front.general.client') }}</span>
                    <span class="text-gray-900 dark:text-white">{{ $item->client }}</span>
                </div>
                @endif
                
                @if(isset($item->date))
                <div class="flex justify-between py-2">
                    <span class="font-medium text-gray-600 dark:text-gray-400">{{ __('portfolio::front.general.project_date') }}</span>
                    <span class="text-gray-900 dark:text-white">{{ $item->date }}</span>
                </div>
                @endif
                
                @if(isset($item->url))
                <div class="flex justify-between py-2">
                    <span class="font-medium text-gray-600 dark:text-gray-400">{{ __('portfolio::front.general.project_url') }}</span>
                    <a href="{{ $item->url }}" target="_blank" class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300">
                        {{ $item->url }}
                    </a>
                </div>
                @endif
            </div>
        </div>
        @endif

        <footer class="mt-12 pt-8 border-t border-gray-200 dark:border-gray-700">
            <div class="flex flex-wrap gap-4">
                <a href="{{ $portfolioIndexUrl }}" 
                   class="inline-flex items-center text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    {{ __('portfolio::front.general.all_portfolios') }}
                </a>
                
                @if(isset($item->category))
                <a href="{{ $categoryDynamicUrl }}" 
                   class="inline-flex items-center text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                    {{ $categoryTitle }} {{ __('portfolio::front.general.portfolios') }}
                </a>
                @endif
            </div>
        </footer>
    </div>
</div>
@endsection
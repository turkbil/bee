@extends('themes.blank.layouts.app')

@section('module_content')
<div class="container animate-fade-in">
    <h1 class="text-3xl font-bold mb-6 text-center text-gray-800 dark:text-white">{{ $title ?? __('portfolio::front.general.portfolios') }}</h1>

    @if($items->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($items as $item)
        @php
            $currentLocale = app()->getLocale();
            
            // Direct JSON field access with proper decoding
            $titleData = $item->title;
            $slugData = $item->slug;
            $metadescData = $item->metadesc;
            $bodyData = $item->body;
            
            // If string, decode JSON
            if (is_string($titleData)) {
                $titleData = json_decode($titleData, true) ?: [];
            }
            if (is_string($slugData)) {
                $slugData = json_decode($slugData, true) ?: [];
            }
            if (is_string($metadescData)) {
                $metadescData = json_decode($metadescData, true) ?: [];
            }
            if (is_string($bodyData)) {
                $bodyData = json_decode($bodyData, true) ?: [];
            }
            
            // Extract current language content
            $title = is_array($titleData) ? ($titleData[$currentLocale] ?? $titleData['tr'] ?? reset($titleData)) : $titleData;
            $title = $title ?: 'Başlıksız';
            
            $slug = is_array($slugData) ? ($slugData[$currentLocale] ?? $slugData['tr'] ?? reset($slugData)) : $slugData;
            $slug = $slug ?: $item->portfolio_id; // Fallback to ID if no slug
            
            $metadesc = is_array($metadescData) ? ($metadescData[$currentLocale] ?? $metadescData['tr'] ?? reset($metadescData)) : $metadescData;
            $body = is_array($bodyData) ? ($bodyData[$currentLocale] ?? $bodyData['tr'] ?? reset($bodyData)) : $bodyData;
            $content = $metadesc ?? $body ?? null;
            
            // DİNAMİK URL - ModuleSlugService'den show ve index slug'larını al
            $showSlug = \App\Services\ModuleSlugService::getSlug('Portfolio', 'show');
            $indexSlug = \App\Services\ModuleSlugService::getSlug('Portfolio', 'index');
            $dynamicShowUrl = '/' . $showSlug . '/' . $slug;
            $categoryDynamicUrl = isset($categorySlug) ? '/' . $indexSlug . '/kategori/' . $categorySlug : null;
        @endphp
        <div class="portfolio-item overflow-hidden hover:shadow-sm transition-shadow duration-300">
            <div class="relative overflow-hidden aspect-w-16 aspect-h-9">
                <a href="{{ $dynamicShowUrl }}">
                    @if($item->getMedia('images')->isNotEmpty())
                    <img src="{{ $item->getFirstMedia('images')->getUrl() }}" alt="{{ $title }}" 
                        class="w-full h-48 object-cover transition-transform duration-300 hover:scale-105">
                    @else
                    <img src="https://placehold.co/600x400?text={{ urlencode($title) }}" alt="{{ $title }}" 
                        class="w-full h-48 object-cover transition-transform duration-300 hover:scale-105">
                    @endif
                </a>
            </div>
            
            <div class="p-6">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">
                    <a href="{{ $dynamicShowUrl }}" class="hover:text-primary dark:hover:text-primary-400 transition-colors duration-300">{{ $title }}</a>
                </h3>
                
                <div class="flex flex-wrap items-center gap-3 text-sm text-gray-600 dark:text-gray-400 mb-4">
                    <span class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        {{ $item->created_at->format('d.m.Y') }}
                    </span>
                    
                    
                    @if(isset($item->category) && $item->category)
                    @php
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
                    @endphp
                    <span class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                        <a href="{{ $categoryDynamicUrl }}" class="hover:text-primary dark:hover:text-primary-400">
                            {{ $categoryTitle }}
                        </a>
                    </span>
                    @endif
                </div>
                
                @if($content)
                <div class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                    {{ Str::limit($content, 120) }}
                </div>
                @endif
                
                <div class="mt-4">
                    <a href="{{ $dynamicShowUrl }}" class="inline-flex items-center text-sm text-primary dark:hover:text-primary-400 hover:underline font-medium">
                        {{ __('portfolio::front.general.view_details') }}
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-8 pagination">
        {{ $items->links() }}
    </div>
    @else
    <div class="border-t-4 border-primary dark:border-primary-400 p-8 text-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 dark:text-gray-600 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
        <p class="text-lg text-gray-600 dark:text-gray-400">{{ __('portfolio::front.general.no_portfolio_found') }}</p>
    </div>
    @endif
</div>
@endsection
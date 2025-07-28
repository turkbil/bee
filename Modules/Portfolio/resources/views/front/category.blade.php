@extends('themes.blank.layouts.app')

@section('module_content')
<div class="bg-white dark:bg-gray-900" x-data="portfolioCategoryList()" x-init="init()">
    
    <!-- Header -->
    <div class="border-b border-gray-100 dark:border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="max-w-full">
                <h1 class="text-4xl font-semibold text-gray-900 dark:text-white mb-4">
                    {{ $category->getTranslated('title') }}
                </h1>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        @php
            $currentLocale = app()->getLocale();
            $indexSlug = \App\Services\ModuleSlugService::getSlug('Portfolio', 'index');
            $showSlug = \App\Services\ModuleSlugService::getSlug('Portfolio', 'show');
            $portfolioIndexUrl = '/' . $indexSlug;
        @endphp

    @if($items->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12" x-show="loaded" x-transition.duration.300ms>
            @foreach($items as $item)
            @php
                // JSON slug handling
                $slugData = $item->getRawOriginal('slug');
                if (is_string($slugData)) {
                    $slugData = json_decode($slugData, true) ?: [];
                }
                $itemSlug = is_array($slugData) ? ($slugData[$currentLocale] ?? $slugData['tr'] ?? reset($slugData)) : $slugData;
                $itemSlug = $itemSlug ?: $item->portfolio_id;
                
                $itemShowUrl = '/' . $showSlug . '/' . $itemSlug;
            @endphp
            <article class="group cursor-pointer transform hover:scale-[1.02] transition-transform duration-200" 
                     @mouseenter="prefetch('{{ $itemShowUrl }}')"
                     @click="navigate('{{ $itemShowUrl }}')">
                <div class="p-6 border border-gray-200 dark:border-gray-700 rounded-lg group-hover:border-purple-300 transition-colors">
                    @if($item->getMedia('images')->isNotEmpty())
                    <div class="relative overflow-hidden rounded-lg aspect-video mb-4">
                        <img src="{{ $item->getFirstMedia('images')->getUrl() }}" alt="{{ $item->getTranslated('title') }}" 
                             class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-500"
                             loading="lazy">
                        
                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all duration-300 flex items-center justify-center">
                            <svg class="h-12 w-12 text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>
                    @endif
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3 group-hover:text-purple-600 transition-colors">
                        {{ $item->getTranslated('title') }}
                    </h3>
                    
                    <div class="flex items-center gap-4 text-sm text-gray-600 dark:text-gray-400 mb-4">
                        <time class="flex items-center">
                            <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $item->created_at->format('d.m.Y') }}
                        </time>
                        
                        <span class="flex items-center text-purple-500">
                            <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M17.707 9.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7A.997.997 0 012 10V5a3 3 0 013-3h5c.256 0 .512.098.707.293l7 7zM5 6a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $category->getTranslated('title') }}
                        </span>
                    </div>
                    
                    @php
                        // Portfolio body alanından description al
                        $bodyData = $item->getRawOriginal('body');
                        if (is_string($bodyData)) {
                            $bodyData = json_decode($bodyData, true) ?: [];
                        }
                        $currentLocale = app()->getLocale();
                        $bodyContent = is_array($bodyData) ? ($bodyData[$currentLocale] ?? $bodyData['tr'] ?? reset($bodyData)) : $bodyData;
                        
                        // Metadesc varsa onu kullan, yoksa body'den al
                        $description = $item->getTranslated('metadesc') ?? $bodyContent ?? '';
                        // HTML decode et sonra strip_tags uygula
                        $description = html_entity_decode($description, ENT_QUOTES, 'UTF-8');
                        $description = strip_tags($description);
                    @endphp
                    @if($description)
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4 line-clamp-3">
                        {{ Str::limit($description, 120) }}
                    </p>
                    @endif
                    
                    <span class="inline-flex items-center text-sm text-purple-600 dark:text-purple-400 font-medium group-hover:underline">
                        {{ __('portfolio::front.general.view_details') }}
                        <svg class="h-4 w-4 ml-1 transform group-hover:translate-x-1 transition-transform" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </span>
                </div>
            </article>
            @endforeach
        </div>

    <div class="mt-8" x-show="loaded">
        {{ $items->links() }}
    </div>
        @else
    <div class="border-t-4 border-purple-500 p-8 text-center rounded-lg">
        <svg class="h-16 w-16 mx-auto text-gray-400 mb-4" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>
        </svg>
        <p class="text-lg text-gray-600 dark:text-gray-400">{{ __('portfolio::front.general.no_portfolio_in_category') }}</p>
    </div>
        @endif
</div>

<script>
function portfolioCategoryList() {
    return {
        loaded: false,
        prefetchedUrls: new Set(),
        
        init() {
            this.loaded = true;
        },
        
        prefetch(url) {
            if (this.prefetchedUrls.has(url)) return;
            
            const link = document.createElement('link');
            link.rel = 'prefetch';
            link.href = url;
            document.head.appendChild(link);
            this.prefetchedUrls.add(url);
        },
        
        navigate(url) {
            window.location.href = url;
        }
    }
}
</script>
@endsection
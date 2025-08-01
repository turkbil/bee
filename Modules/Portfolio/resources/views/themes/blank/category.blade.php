@extends('themes.blank.layouts.app')

@section('module_content')
<div class="relative" x-data="portfolioCategoryList()" x-init="init()">
    
    <!-- Gradient Background -->
    <div class="absolute inset-0 bg-gradient-to-br from-green-50 via-white to-teal-50 dark:from-gray-900 dark:via-gray-900 dark:to-gray-800 -z-10"></div>
    
    @php
        $currentLocale = app()->getLocale();
        
        // Category title handling
        $categoryTitleData = $category->title;
        if (is_string($categoryTitleData)) {
            $categoryTitleData = json_decode($categoryTitleData, true) ?: [];
        }
        $categoryTitle = is_array($categoryTitleData) ? ($categoryTitleData[$currentLocale] ?? $categoryTitleData['tr'] ?? reset($categoryTitleData)) : $categoryTitleData;
        
        // Category body handling
        $categoryBodyData = $category->body;
        if (is_string($categoryBodyData)) {
            $categoryBodyData = json_decode($categoryBodyData, true) ?: [];
        }
        $categoryBody = is_array($categoryBodyData) ? ($categoryBodyData[$currentLocale] ?? $categoryBodyData['tr'] ?? reset($categoryBodyData)) : $categoryBodyData;
        
        // DİNAMİK URL'ler
        $indexSlug = \App\Services\ModuleSlugService::getSlug('Portfolio', 'index');
        $showSlug = \App\Services\ModuleSlugService::getSlug('Portfolio', 'show');
        $portfolioIndexUrl = '/' . $indexSlug;
    @endphp
    
    <!-- Simple Title Section -->
    <div class="py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-12">
                <h1 class="text-5xl font-bold bg-gradient-to-r from-gray-900 to-gray-700 dark:from-white dark:to-gray-300 bg-clip-text text-transparent mb-4">
                    {{ $categoryTitle }}
                </h1>
                <p class="text-lg text-gray-600 dark:text-gray-400">
                    {{ $categoryTitle }} kategorisindeki projelerimiz
                </p>
                
                @if(isset($categoryBody) && trim(strip_tags($categoryBody ?? '')) !== '')
                <div class="prose prose-lg max-w-none dark:prose-invert mt-6 
                          prose-headings:font-bold prose-headings:text-gray-900 dark:prose-headings:text-white 
                          prose-p:text-gray-600 dark:prose-p:text-gray-300 prose-p:leading-relaxed">
                    {!! $categoryBody !!}
                </div>
                @endif
                
                <div class="mt-6">
                    <a href="{{ $portfolioIndexUrl }}" 
                       class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-600 to-teal-600 text-white font-semibold rounded-xl hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200"
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
                </div>
            </div>

            @if($items->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($items as $item)
                @php
                    $itemSlugData = $item->getRawOriginal('slug');
                    
                    if (is_string($itemSlugData)) {
                        $itemSlugData = json_decode($itemSlugData, true) ?: [];
                    }
                    $itemSlug = is_array($itemSlugData) ? ($itemSlugData[$currentLocale] ?? $itemSlugData['tr'] ?? reset($itemSlugData)) : $itemSlugData;
                    $itemSlug = $itemSlug ?: $item->portfolio_id;
                    
                    $itemTitle = $item->getTranslated('title') ?? $item->getRawOriginal('title') ?? $item->title ?? 'Başlıksız';
                    
                    $itemBodyData = $item->getRawOriginal('body');
                    if (is_string($itemBodyData)) {
                        $itemBodyData = json_decode($itemBodyData, true) ?: [];
                    }
                    $itemBodyContent = is_array($itemBodyData) ? ($itemBodyData[$currentLocale] ?? $itemBodyData['tr'] ?? reset($itemBodyData)) : $itemBodyData;
                    
                    $itemMetadesc = $item->getTranslated('metadesc') ?? $item->getRawOriginal('metadesc') ?? $item->metadesc ?? null;
                    $itemDescription = $itemMetadesc ?? strip_tags($itemBodyContent) ?? null;
                    
                    // İtem için dinamik URL - locale aware
                    $defaultLocale = get_tenant_default_locale();
                    if ($currentLocale === $defaultLocale) {
                        $itemShowUrl = '/' . $showSlug . '/' . $itemSlug;
                    } else {
                        $itemShowUrl = '/' . $currentLocale . '/' . $showSlug . '/' . $itemSlug;
                    }
                @endphp
                
                <article class="group relative bg-white dark:bg-gray-800 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 dark:border-gray-700" 
                         @click="navigate('{{ $itemShowUrl }}')" 
                         @mouseenter="prefetch('{{ $itemShowUrl }}'); hover = {{ $loop->index }}" 
                         @mouseleave="hover = null"
                         x-data="{ localHover: false }"
                         @mouseenter.self="localHover = true"
                         @mouseleave.self="localHover = false">
                    
                    <!-- Gradient Overlay -->
                    <div class="absolute inset-0 bg-gradient-to-br from-green-600/5 to-teal-600/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    
                    <div class="relative p-6 cursor-pointer">
                        
                        <!-- Category Badge if exists -->
                        @if($item->category ?? false)
                        <div class="absolute top-4 right-4 z-10">
                            <span class="inline-flex items-center px-3 py-1 text-xs font-bold text-white bg-gradient-to-r from-green-500 to-teal-500 rounded-full shadow-lg">
                                <svg class="h-3 w-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M17.707 9.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7A.997.997 0 012 10V5a3 3 0 013-3h5c.256 0 .512.098.707.293l7 7zM5 6a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                                </svg>
                                {{ $item->category->getTranslated('title') }}
                            </span>
                        </div>
                        @endif

                        <!-- Date with Icon -->
                        <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 mb-4">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            {{ $item->created_at->format('d M Y') }}
                        </div>

                        <!-- Title with hover effect -->
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-3 group-hover:text-transparent group-hover:bg-gradient-to-r group-hover:from-green-600 group-hover:to-teal-600 group-hover:bg-clip-text transition-all duration-300">
                            {{ $itemTitle }}
                        </h2>

                        <!-- Description -->
                        @if($itemDescription)
                        <p class="text-gray-600 dark:text-gray-300 leading-relaxed mb-6 line-clamp-3">
                            {{ Str::limit($itemDescription, 120) }}
                        </p>
                        @endif

                        <!-- Read More with Alpine animation -->
                        <div class="inline-flex items-center text-sm font-semibold text-transparent bg-gradient-to-r from-green-600 to-teal-600 bg-clip-text group-hover:from-green-700 group-hover:to-teal-700 transition-all duration-300"
                             x-show="true"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-75 transform translate-x-0"
                             x-transition:enter-end="opacity-100 transform translate-x-0">
                            {{ __('portfolio::front.general.view_details') }}
                            <svg class="h-4 w-4 ml-2 text-green-600 group-hover:text-teal-600 transition-all duration-300"
                                 :class="localHover ? 'translate-x-1' : 'translate-x-0'"
                                 fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        
                        <!-- Hover Border Effect -->
                        <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-green-600 to-teal-600 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300"></div>
                    </div>
                </article>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($items->hasPages())
            <div class="mt-20">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-4">
                    {{ $items->links() }}
                </div>
            </div>
            @endif
            @else
            <!-- Empty State -->
            <div class="text-center py-20" x-data="{ show: false }" x-init="setTimeout(() => show = true, 100)">
                <div x-show="show"
                     x-transition:enter="transition ease-out duration-500"
                     x-transition:enter-start="opacity-0 transform scale-90"
                     x-transition:enter-end="opacity-100 transform scale-100"
                     class="inline-block">
                    <div class="w-20 h-20 bg-gradient-to-br from-green-100 to-teal-200 dark:from-green-700 dark:to-teal-800 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg">
                        <svg class="h-10 w-10 text-green-500 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Bu kategoride proje yok</h3>
                    <p class="text-gray-500 dark:text-gray-400 max-w-sm mx-auto">{{ __('portfolio::front.general.no_portfolio_in_category') }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
function portfolioCategoryList() {
    return {
        loaded: false,
        hover: null,
        prefetchedUrls: new Set(),
        
        init() {
            // Smooth fade in
            this.$nextTick(() => {
                this.loaded = true;
            });
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
            // Add a subtle loading state
            document.body.style.cursor = 'wait';
            window.location.href = url;
        }
    }
}
</script>
@endsection
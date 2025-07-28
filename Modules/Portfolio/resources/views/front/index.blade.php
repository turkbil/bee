@extends('themes.blank.layouts.app')

@section('module_content')
<div class="bg-white dark:bg-gray-900" x-data="portfolioList()" x-init="init()">
    
    <!-- Header -->
    <div class="border-b border-gray-100 dark:border-gray-800">
        <div class="py-16">
            <h1 class="text-4xl font-semibold text-gray-900 dark:text-white mb-4">
                {{ $title ?? __('portfolio::front.general.portfolios') }}
            </h1>
        </div>
    </div>

    <div class="py-16">
        @if($items->count() > 0)
        <!-- Articles -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
            @foreach($items as $item)
            @php
                $currentLocale = app()->getLocale();
                $slugData = $item->getRawOriginal('slug');
                
                if (is_string($slugData)) {
                    $slugData = json_decode($slugData, true) ?: [];
                }
                $slug = is_array($slugData) ? ($slugData[$currentLocale] ?? $slugData['tr'] ?? reset($slugData)) : $slugData;
                $slug = $slug ?: $item->portfolio_id;
                
                $title = $item->getTranslated('title') ?? $item->getRawOriginal('title') ?? $item->title ?? 'Başlıksız';
                
                $showSlug = \App\Services\ModuleSlugService::getSlug('Portfolio', 'show');
                $dynamicUrl = '/' . $showSlug . '/' . $slug;

                $bodyData = $item->getRawOriginal('body');
                if (is_string($bodyData)) {
                    $bodyData = json_decode($bodyData, true) ?: [];
                }
                $bodyContent = is_array($bodyData) ? ($bodyData[$currentLocale] ?? $bodyData['tr'] ?? reset($bodyData)) : $bodyData;
                
                $metadesc = $item->getTranslated('metadesc') ?? $item->getRawOriginal('metadesc') ?? $item->metadesc ?? null;
                $description = $metadesc ?? strip_tags($bodyContent) ?? null;
            @endphp
            
            <article class="group" 
                     @click="navigate('{{ $dynamicUrl }}')" 
                     @mouseenter="prefetch('{{ $dynamicUrl }}')">
                
                <div class="cursor-pointer">
                    
                    <!-- Category Badge if exists -->
                    @if($item->category ?? false)
                    <div class="inline-flex items-center text-xs font-medium text-blue-600 dark:text-blue-400 mb-3">
                        <svg class="h-3 w-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M17.707 9.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7A.997.997 0 012 10V5a3 3 0 013-3h5c.256 0 .512.098.707.293l7 7zM5 6a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                        </svg>
                        {{ $item->category->getTranslated('title') }}
                    </div>
                    @endif

                    <!-- Date -->
                    <div class="text-sm text-gray-500 dark:text-gray-400 mb-3">
                        {{ $item->created_at->format('d M Y') }}
                    </div>

                    <!-- Title -->
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                        {{ $title }}
                    </h2>

                    <!-- Description -->
                    @if($description)
                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed mb-6">
                        {{ Str::limit($description, 150) }}
                    </p>
                    @endif

                    <!-- Read More -->
                    <div class="inline-flex items-center text-sm font-medium text-blue-600 dark:text-blue-400 group-hover:text-blue-700 dark:group-hover:text-blue-300 transition-colors">
                        {{ __('portfolio::front.general.view_details') }}
                        <svg class="h-4 w-4 ml-1 group-hover:translate-x-1 transition-transform" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                </div>
            </article>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($items->hasPages())
        <div class="mt-16 border-t border-gray-100 dark:border-gray-800 pt-12">
            {{ $items->links() }}
        </div>
        @endif

        @else
        <!-- Empty State -->
        <div class="text-center py-20">
            <div class="w-16 h-16 bg-gray-100 dark:bg-gray-800 rounded-lg flex items-center justify-center mx-auto mb-4">
                <svg class="h-8 w-8 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Henüz proje yok</h3>
            <p class="text-gray-500 dark:text-gray-400">{{ __('portfolio::front.general.no_portfolio_found') }}</p>
        </div>
        @endif

    </div>
    </div>
</div>

<script>
function portfolioList() {
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
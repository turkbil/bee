@extends('portfolio::front.themes.blank.layouts.app')

@section('module_content')
<div class="py-6" x-data="portfolioList()" x-init="init()">
    <h1 class="text-3xl font-bold mb-6 text-center text-gray-800 dark:text-white">{{ $title ?? __('portfolio::general.portfolios') }}</h1>
    
    @if($items->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" x-show="loaded" x-transition.duration.300ms>
        @foreach($items as $item)
        <article class="group cursor-pointer" 
                 @mouseenter="prefetch('{{ route('portfolios.show', $item->slug) }}')"
                 @click="navigate('{{ route('portfolios.show', $item->slug) }}')">
            
            @if($item->getMedia('images')->isNotEmpty())
            <div class="relative overflow-hidden rounded-lg aspect-video mb-4">
                <img src="{{ $item->getFirstMedia('images')->getUrl() }}" 
                     alt="{{ $item->title }}" 
                     class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-500"
                     loading="lazy">
                
                @if($item->category ?? false)
                <div class="absolute top-3 right-3">
                    <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-purple-600 bg-opacity-90 text-white rounded-full">
                        {{ $item->category->title }}
                    </span>
                </div>
                @endif
                
                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all duration-300 flex items-center justify-center">
                    <svg class="h-12 w-12 text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            </div>
            @endif
            
            <div class="p-6">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3 group-hover:text-purple-600 transition-colors">
                    {{ $item->title }}
                </h3>
                
                <div class="flex items-center gap-4 text-sm text-gray-600 dark:text-gray-400 mb-4">
                    <time class="flex items-center">
                        <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                        </svg>
                        {{ $item->created_at->format('d.m.Y') }}
                    </time>
                    
                    @if($item->category ?? false)
                    <span class="flex items-center text-purple-500">
                        <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M17.707 9.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7A.997.997 0 012 10V5a3 3 0 013-3h5c.256 0 .512.098.707.293l7 7zM5 6a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                        </svg>
                        {{ $item->category->title }}
                    </span>
                    @endif
                </div>
                
                @if($item->metadesc ?? $item->body ?? false)
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4 line-clamp-3">
                    {{ Str::limit($item->metadesc ?? strip_tags($item->body), 120) }}
                </p>
                @endif
                
                <span class="inline-flex items-center text-sm text-purple-600 dark:text-purple-400 font-medium group-hover:underline">
                    {{ __('portfolio::general.view_details') }}
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
    <div class="p-8 text-center border-t-4 border-purple-500 rounded-lg">
        <svg class="h-16 w-16 mx-auto text-gray-400 mb-4" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>
        </svg>
        <p class="text-lg text-gray-600 dark:text-gray-400">{{ __('portfolio::general.no_portfolio_found') }}</p>
    </div>
    @endif
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
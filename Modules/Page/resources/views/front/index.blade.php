@extends('page::front.themes.blank.layouts.app')

@section('module_content')
<div class="container py-6" x-data="pageList()" x-init="init()">
    <h1 class="text-3xl font-bold mb-6 text-center text-gray-800 dark:text-white">{{ $title ?? t('page::general.pages') }}</h1>

    @if($items->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" x-show="loaded" x-transition.duration.300ms>
            @foreach($items as $item)
                <article class="group cursor-pointer" 
                         @mouseenter="prefetch('{{ route('pages.show', $item->slug) }}')"
                         @click="navigate('{{ route('pages.show', $item->slug) }}')">
                    <div class="p-6 transform group-hover:scale-[1.02] transition-transform duration-200">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3 group-hover:text-blue-600 transition-colors">
                            {{ $item->title }}
                        </h3>
                        
                        <time class="flex items-center text-sm text-gray-600 dark:text-gray-400 mb-4">
                            <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $item->created_at->format('d.m.Y') }}
                        </time>
                        
                        @if($item->metadesc ?? $item->body ?? false)
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4 line-clamp-3">
                            {{ Str::limit($item->metadesc ?? strip_tags($item->body), 120) }}
                        </p>
                        @endif
                        
                        <span class="inline-flex items-center text-sm text-blue-600 dark:text-blue-400 font-medium group-hover:underline">
                            {{ t('page::general.read_more') }}
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
        <div class="p-8 text-center border-t-4 border-blue-500">
            <svg class="h-16 w-16 mx-auto text-gray-400 mb-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
            </svg>
            <p class="text-lg text-gray-600 dark:text-gray-400">{{ t('page::messages.no_pages_found') }}</p>
        </div>
    @endif
</div>

<script>
function pageList() {
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
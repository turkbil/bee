@extends('announcement::front.themes.blank.layouts.app')

@section('module_content')
<div class="container py-6" x-data="announcementList()" x-init="init()">
    <h1 class="text-3xl font-bold mb-6 text-center text-gray-900 dark:text-white">{{ $title ?? 'Duyurular' }}</h1>
    
    @if($items->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" x-show="loaded" x-transition.duration.300ms>
        @foreach($items as $item)
        <article class="group cursor-pointer transform hover:scale-[1.02] transition-transform duration-200" 
                 @mouseenter="prefetch('{{ route('announcements.show', $item->slug) }}')"
                 @click="navigate('{{ route('announcements.show', $item->slug) }}')">
            <div class="p-6 border border-gray-200 dark:border-gray-700 rounded-lg group-hover:border-orange-300 transition-colors">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3 group-hover:text-orange-600 transition-colors">
                    {{ $item->title }}
                </h3>
                
                <div class="flex items-center gap-4 text-sm text-gray-600 dark:text-gray-400 mb-4">
                    <time class="flex items-center">
                        <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                        </svg>
                        {{ $item->created_at->format('d.m.Y') }}
                    </time>
                    
                    @if($item->attachment ?? false)
                    <span class="flex items-center text-orange-500">
                        <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8 4a3 3 0 00-3 3v4a5 5 0 0010 0V7a1 1 0 112 0v4a7 7 0 11-14 0V7a5 5 0 0110 0v4a3 3 0 11-6 0V7a1 1 0 012 0v4a1 1 0 102 0V7a3 3 0 00-3-3z" clip-rule="evenodd"></path>
                        </svg>
                        Ek
                    </span>
                    @endif
                </div>
                
                @if($item->metadesc ?? $item->body ?? false)
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4 line-clamp-3">
                    {{ Str::limit($item->metadesc ?? strip_tags($item->body), 120) }}
                </p>
                @endif
                
                <span class="inline-flex items-center text-sm text-orange-600 dark:text-orange-400 font-medium group-hover:underline">
                    Devamını Oku
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
    <div class="border-t-4 border-orange-500 p-8 text-center rounded-lg">
        <svg class="h-16 w-16 mx-auto text-gray-400 mb-4" fill="currentColor" viewBox="0 0 20 20">
            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
        </svg>
        <p class="text-lg text-gray-600 dark:text-gray-400">Henüz duyuru bulunmamaktadır.</p>
    </div>
    @endif
</div>

<script>
function announcementList() {
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
@extends('themes.blank.layouts.app')

@section('module_content')
<div class="relative" x-data="announcementShow()" x-init="init()">
    <!-- Gradient Background -->
    <div class="absolute inset-0 bg-gradient-to-br from-amber-50 via-white to-orange-50 dark:from-gray-900 dark:via-gray-900 dark:to-gray-800 -z-10"></div>
    
    @php
        $currentLocale = app()->getLocale();
        $title = $item->getTranslated('title', $currentLocale);
        $body = $item->getTranslated('body', $currentLocale);
        
        // Get index URL
        $indexSlug = \App\Services\ModuleSlugService::getSlug('Announcement', 'index');
        $announcementIndexUrl = '/' . $indexSlug;
    @endphp
    
    <!-- Simple Title Section -->
    <div class="py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-12">
                <h1 class="text-5xl font-bold bg-gradient-to-r from-gray-900 to-gray-700 dark:from-white dark:to-gray-300 bg-clip-text text-transparent mb-4">
                    {{ $title }}
                </h1>
                
                <div class="flex flex-wrap items-center gap-4 text-sm">
                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        {{ $item->created_at->format('d F Y') }}
                    </span>
                    
                    @if($item->updated_at != $item->created_at)
                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        {{ __('announcement::front.general.updated') }}
                    </span>
                    @endif
                    
                    @if($item->attachment ?? false)
                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                        </svg>
                        {{ __('announcement::front.general.attachment') }}
                    </span>
                    @endif
                </div>
            </div>
            
            <!-- Attachment Section -->
            @if($item->attachment ?? false)
            <div class="mb-8 p-6 bg-gradient-to-r from-amber-50 to-orange-50 dark:from-amber-900/20 dark:to-orange-900/20 border border-amber-200 dark:border-amber-800 rounded-xl">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-gradient-to-r from-amber-500 to-orange-500 rounded-xl flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8 4a3 3 0 00-3 3v4a5 5 0 0010 0V7a1 1 0 112 0v4a7 7 0 11-14 0V7a5 5 0 0110 0v4a3 3 0 11-6 0V7a1 1 0 012 0v4a1 1 0 102 0V7a3 3 0 00-3-3z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-amber-900 dark:text-amber-200">Ek Dosya</h3>
                            <p class="text-sm text-amber-700 dark:text-amber-400">Duyuru ile ilgili dosyayı indirin</p>
                        </div>
                    </div>
                    <a href="{{ $item->attachment }}" 
                       target="_blank"
                       class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-amber-600 to-orange-600 hover:from-amber-700 hover:to-orange-700 text-white font-semibold rounded-xl transform hover:-translate-y-0.5 transition-all duration-200 shadow-lg hover:shadow-xl">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                        İndir
                    </a>
                </div>
            </div>
            @endif
            
            <!-- Content without card wrapper -->
            <article class="prose prose-lg max-w-none dark:prose-invert 
                          prose-headings:font-bold prose-headings:text-gray-900 dark:prose-headings:text-white 
                          prose-p:text-gray-600 dark:prose-p:text-gray-300 prose-p:leading-relaxed
                          prose-a:text-transparent prose-a:bg-gradient-to-r prose-a:from-amber-600 prose-a:to-orange-600 prose-a:bg-clip-text hover:prose-a:from-amber-700 hover:prose-a:to-orange-700
                          prose-strong:text-gray-900 dark:prose-strong:text-white
                          prose-blockquote:border-l-4 prose-blockquote:border-amber-500 prose-blockquote:bg-amber-50 dark:prose-blockquote:bg-amber-900/20 prose-blockquote:italic
                          prose-code:text-orange-600 dark:prose-code:text-orange-400 prose-code:bg-orange-50 dark:prose-code:bg-orange-900/20 prose-code:px-1 prose-code:py-0.5 prose-code:rounded
                          prose-pre:bg-gray-900 prose-pre:shadow-xl
                          prose-img:rounded-xl prose-img:shadow-lg">
                @parsewidgets($body ?? '')
            </article>

            <!-- Navigation -->
            <div class="mt-12 pt-8 border-t border-gray-200 dark:border-gray-700">
                <div class="flex flex-wrap justify-between items-center gap-4">
                    <a href="{{ $announcementIndexUrl }}" 
                       class="group inline-flex items-center px-6 py-3 bg-gradient-to-r from-amber-600 to-orange-600 text-white font-semibold rounded-xl hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200"
                       x-data="{ hover: false }"
                       @mouseenter="hover = true"
                       @mouseleave="hover = false">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 transform transition-transform duration-200"
                             :class="hover ? '-translate-x-1' : ''"
                             fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        {{ __('announcement::front.general.all_announcements') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function announcementShow() {
    return {
        loaded: false,
        
        init() {
            // Instant load
            this.loaded = true;
            
            // Preload announcements list
            this.preloadIndex();
        },
        
        preloadIndex() {
            const link = document.createElement('link');
            link.rel = 'prefetch';
            @php
                $indexSlug = \App\Services\ModuleSlugService::getSlug('Announcement', 'index');
                $announcementIndexUrl = '/' . $indexSlug;
            @endphp
            link.href = '{{ $announcementIndexUrl }}';
            document.head.appendChild(link);
        },
        
        goBack() {
            if (history.length > 1) {
                history.back();
            } else {
                @php
                    $indexSlug = \App\Services\ModuleSlugService::getSlug('Announcement', 'index');
                    $announcementIndexUrl = '/' . $indexSlug;
                @endphp
                window.location.href = '{{ $announcementIndexUrl }}';
            }
        }
    }
}
</script>
@endsection
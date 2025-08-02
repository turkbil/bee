@extends('themes.blank.layouts.app')

@section('module_content')
<div class="bg-white dark:bg-gray-900" x-data="announcementShow()" x-init="init()">
    <article>
        @php
            $currentLocale = app()->getLocale();
            $title = $item->getTranslated('title', $currentLocale);
            $body = $item->getTranslated('body', $currentLocale);
            
            // Get index URL - LOCALE AWARE
            $moduleSlugService = app(\App\Services\ModuleSlugService::class);
            
            // Mevcut dil için index slug'ını al
            $indexSlug = $moduleSlugService->getMultiLangSlug('Announcement', 'index', $currentLocale);
            
            // Locale prefix'i kontrol et
            $defaultLocale = get_tenant_default_locale();
            $localePrefix = ($currentLocale !== $defaultLocale) ? '/' . $currentLocale : '';
            
            $announcementIndexUrl = $localePrefix . '/' . $indexSlug;
        @endphp
        
        <!-- Header with title -->
        <div class="border-b border-gray-100 dark:border-gray-800">
            <div class="py-16">
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mb-4">
                    {{ $title }}
                </h1>
                
                <div class="flex flex-wrap items-center gap-3 text-sm text-gray-600 dark:text-gray-400">
                    <span class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        {{ $item->created_at->format('d.m.Y') }}
                    </span>
                    
                    @if($item->updated_at != $item->created_at)
                    <span class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        {{ __('announcement::front.general.updated') }}: {{ $item->updated_at->format('d.m.Y') }}
                    </span>
                    @endif
                    
                    @if($item->attachment ?? false)
                    <span class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                        </svg>
                        <span class="text-xs">{{ __('announcement::front.general.attachment') }}</span>
                    </span>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Content -->
        <div class="py-16">
            
            <!-- Attachment Section -->
            @if($item->attachment ?? false)
            <div class="mb-8 p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8 4a3 3 0 00-3 3v4a5 5 0 0010 0V7a1 1 0 112 0v4a7 7 0 11-14 0V7a5 5 0 0110 0v4a3 3 0 11-6 0V7a1 1 0 012 0v4a1 1 0 102 0V7a3 3 0 00-3-3z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-amber-800 dark:text-amber-200 font-medium">Ek Dosya</span>
                    </div>
                    <a href="{{ $item->attachment }}" 
                       target="_blank"
                       class="inline-flex items-center px-3 py-1.5 bg-amber-600 hover:bg-amber-700 text-white text-sm font-medium rounded-md transition-colors">
                        <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                        İndir
                    </a>
                </div>
            </div>
            @endif
            
            <div class="prose prose-sm sm:prose lg:prose-lg max-w-none dark:prose-invert 
                       prose-headings:text-gray-900 dark:prose-headings:text-white 
                       prose-p:text-gray-600 dark:prose-p:text-gray-300 
                       prose-a:text-blue-600 dark:prose-a:text-blue-400 
                       prose-strong:text-gray-900 dark:prose-strong:text-white
                       prose-img:rounded-lg">
                @parsewidgets($body ?? '')
            </div>
            
            <!-- Navigation -->
            <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                <div class="flex flex-wrap justify-between items-center gap-4">
                    <a href="{{ $announcementIndexUrl }}" 
                       class="inline-flex items-center text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        {{ __('announcement::front.general.all_announcements') }}
                    </a>
                </div>
            </div>
        </div>
    </article>
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
                $currentLocale = app()->getLocale();
                $moduleSlugService = app(\App\Services\ModuleSlugService::class);
                
                // Mevcut dil için index slug'ını al
                $indexSlug = $moduleSlugService->getMultiLangSlug('Announcement', 'index', $currentLocale);
                
                // Locale prefix'i kontrol et
                $defaultLocale = get_tenant_default_locale();
                $localePrefix = ($currentLocale !== $defaultLocale) ? '/' . $currentLocale : '';
                
                $announcementIndexUrl = $localePrefix . '/' . $indexSlug;
            @endphp
            link.href = '{{ $announcementIndexUrl }}';
            document.head.appendChild(link);
        },
        
        goBack() {
            if (history.length > 1) {
                history.back();
            } else {
                @php
                    $currentLocale = app()->getLocale();
                    $moduleSlugService = app(\App\Services\ModuleSlugService::class);
                    
                    // Mevcut dil için index slug'ını al
                    $indexSlug = $moduleSlugService->getMultiLangSlug('Announcement', 'index', $currentLocale);
                    
                    // Locale prefix'i kontrol et
                    $defaultLocale = get_tenant_default_locale();
                    $localePrefix = ($currentLocale !== $defaultLocale) ? '/' . $currentLocale : '';
                    
                    $announcementIndexUrl = $localePrefix . '/' . $indexSlug;
                @endphp
                window.location.href = '{{ $announcementIndexUrl }}';
            }
        }
    }
}
</script>
@endsection
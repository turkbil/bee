@extends('themes.blank.layouts.app')

@section('module_content')
<div class="min-h-screen">
    @php
        $currentLocale = app()->getLocale();
        $title = $item->getTranslated('title', $currentLocale);
        $body = $item->getTranslated('body', $currentLocale);
        
        $moduleSlugService = app(\App\Services\ModuleSlugService::class);
        $indexSlug = $moduleSlugService->getMultiLangSlug('Announcement', 'index', $currentLocale);
        $defaultLocale = get_tenant_default_locale();
        $localePrefix = ($currentLocale !== $defaultLocale) ? '/' . $currentLocale : '';
        $announcementIndexUrl = $localePrefix . '/' . $indexSlug;
    @endphp
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        @if($item->attachment ?? false)
        <div class="mb-8 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                    </svg>
                    <span class="font-medium text-blue-900 dark:text-blue-100">Ek Dosya</span>
                </div>
                <a href="{{ $item->attachment }}" target="_blank" class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium">
                    İndir
                </a>
            </div>
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

        <footer class="mt-12 pt-8 border-t border-gray-200 dark:border-gray-700">
            <a href="{{ $announcementIndexUrl }}" 
               class="inline-flex items-center text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                {{ __('announcement::front.general.all_announcements') }}
            </a>
        </footer>
    </div>
</div>
@endsection
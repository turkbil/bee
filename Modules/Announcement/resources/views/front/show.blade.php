@extends('themes.blank.layouts.app')

@section('module_content')
<div class="py-6" x-data="announcementShow()" x-init="init()">
    <article class="max-w-4xl mx-auto" x-show="loaded" x-transition.duration.300ms>
        <div class="p-8">
            <h1 class="text-3xl font-bold mb-4 text-gray-900 dark:text-white">{{ $item->getTranslated('title', app()->getLocale()) }}</h1>
            
            <div class="flex items-center gap-4 text-sm text-gray-600 dark:text-gray-400 mb-6 pb-4 border-b border-gray-200 dark:border-gray-700">
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
                    {{ __('announcement::front.general.attachment') }}
                </span>
                @endif
            </div>

            <div class="prose max-w-none dark:prose-invert prose-headings:text-gray-900 dark:prose-headings:text-white prose-p:text-gray-800 dark:prose-p:text-gray-200 prose-a:text-orange-600 dark:prose-a:text-orange-400">
                @parsewidgets($item->getTranslated('body', app()->getLocale()) ?? '')
            </div>

            @if($item->attachment ?? false)
            <div class="mt-6 pt-4 pb-4 border-t border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white flex items-center">
                    <svg class="h-5 w-5 mr-2 text-orange-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8 4a3 3 0 00-3 3v4a5 5 0 0010 0V7a1 1 0 112 0v4a7 7 0 11-14 0V7a5 5 0 0110 0v4a3 3 0 11-6 0V7a1 1 0 012 0v4a1 1 0 102 0V7a3 3 0 00-3-3z" clip-rule="evenodd"></path>
                    </svg>
                    {{ __('announcement::front.general.attachment') }}
                </h3>
                <a href="{{ $item->attachment }}" 
                   class="inline-flex items-center px-4 py-2 bg-orange-100 hover:bg-orange-200 text-orange-800 rounded-lg font-medium transition-colors" 
                   target="_blank">
                    <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                    İndir
                </a>
            </div>
            @endif

            <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                <button @click="goBack()" class="inline-flex items-center text-orange-600 dark:text-orange-400 hover:underline font-medium">
                    <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"></path>
                    </svg>
                    {{ __('announcement::front.general.all_announcements') }}
                </button>
            </div>
        </div>
    </article>
</div>

<script>
function announcementShow() {
    return {
        loaded: false,
        
        init() {
            this.loaded = true;
            this.preloadIndex();
        },
        
        preloadIndex() {
            const link = document.createElement('link');
            link.rel = 'prefetch';
            link.href = '{{ route("announcements.index") }}';
            document.head.appendChild(link);
        },
        
        goBack() {
            if (history.length > 1) {
                history.back();
            } else {
                window.location.href = '{{ route("announcements.index") }}';
            }
        }
    }
}
</script>
@endsection
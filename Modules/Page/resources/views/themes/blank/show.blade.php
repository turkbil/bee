@extends('themes.blank.layouts.app')

@push('head')
{{-- Schema.org için sayfa bilgileri --}}
{!! \App\Services\SEOService::getPageSchema($item) !!}
@endpush

@section('module_content')
@if(isset($is_homepage) && $is_homepage)
<div class="relative" x-data="homepage()" x-init="init()">
    <!-- Gradient Background -->
    <div class="absolute inset-0 bg-gradient-to-br from-blue-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-900 dark:to-gray-800 -z-10"></div>
    
    @php
        $currentLocale = app()->getLocale();
        $title = $item->getTranslated('title', $currentLocale);
        $body = $item->getTranslated('body', $currentLocale);
    @endphp
    
    <!-- Homepage Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="prose prose-lg max-w-none dark:prose-invert mb-12
                  prose-headings:font-bold prose-headings:text-gray-900 dark:prose-headings:text-white 
                  prose-p:text-gray-600 dark:prose-p:text-gray-300 prose-p:leading-relaxed
                  prose-a:text-transparent prose-a:bg-gradient-to-r prose-a:from-blue-600 prose-a:to-purple-600 prose-a:bg-clip-text hover:prose-a:from-blue-700 hover:prose-a:to-purple-700
                  prose-strong:text-gray-900 dark:prose-strong:text-white
                  prose-blockquote:border-l-4 prose-blockquote:border-blue-500 prose-blockquote:bg-blue-50 dark:prose-blockquote:bg-blue-900/20 prose-blockquote:italic
                  prose-code:text-purple-600 dark:prose-code:text-purple-400 prose-code:bg-purple-50 dark:prose-code:bg-purple-900/20 prose-code:px-1 prose-code:py-0.5 prose-code:rounded
                  prose-pre:bg-gray-900 prose-pre:shadow-xl
                  prose-img:rounded-xl prose-img:shadow-lg">
            @parsewidgets($body ?? '')
        </div>
            
        </div>
    </div>
    
    @if(isset($item->js))
    <script>
        {!! $item->js !!}
    </script>
    @endif
    
    @if(isset($item->css))
    <style>
        {!! $item->css !!}
    </style>
    @endif
</div>

<script>
function homepage() {
    return {
        loaded: false,
        
        init() {
            this.$nextTick(() => {
                this.loaded = true;
            });
        }
    }
}
</script>
@else
<div class="relative" x-data="pageShow()" x-init="init()">
    <!-- Gradient Background -->
    <div class="absolute inset-0 bg-gradient-to-br from-blue-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-900 dark:to-gray-800 -z-10"></div>
    
    @php
        $currentLocale = app()->getLocale();
        $title = $item->getTranslated('title', $currentLocale);
        $body = $item->getTranslated('body', $currentLocale);
        
        // Get index URL
        $indexSlug = \App\Services\ModuleSlugService::getSlug('Page', 'index');
        $pageIndexUrl = '/' . $indexSlug;
    @endphp
    
    <!-- Simple Title Section -->
    <div class="py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-12">
                <h1 class="text-5xl font-bold bg-gradient-to-r from-gray-900 to-gray-700 dark:from-white dark:to-gray-300 bg-clip-text text-transparent mb-4">
                    {{ $title }}
                </h1>
                
                <div class="flex flex-wrap items-center gap-4 text-sm">
                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        {{ $item->created_at->format('d F Y') }}
                    </span>
                    
                    @if($item->updated_at != $item->created_at)
                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        {{ __('page::front.general.updated') }}
                    </span>
                    @endif
                </div>
            </div>
            
            <!-- Content without card wrapper -->
            <article class="prose prose-lg max-w-none dark:prose-invert 
                          prose-headings:font-bold prose-headings:text-gray-900 dark:prose-headings:text-white 
                          prose-p:text-gray-600 dark:prose-p:text-gray-300 prose-p:leading-relaxed
                          prose-a:text-transparent prose-a:bg-gradient-to-r prose-a:from-blue-600 prose-a:to-purple-600 prose-a:bg-clip-text hover:prose-a:from-blue-700 hover:prose-a:to-purple-700
                          prose-strong:text-gray-900 dark:prose-strong:text-white
                          prose-blockquote:border-l-4 prose-blockquote:border-blue-500 prose-blockquote:bg-blue-50 dark:prose-blockquote:bg-blue-900/20 prose-blockquote:italic
                          prose-code:text-purple-600 dark:prose-code:text-purple-400 prose-code:bg-purple-50 dark:prose-code:bg-purple-900/20 prose-code:px-1 prose-code:py-0.5 prose-code:rounded
                          prose-pre:bg-gray-900 prose-pre:shadow-xl
                          prose-img:rounded-xl prose-img:shadow-lg">
                @parsewidgets($body ?? '')
            </article>
            
            @if(isset($item->js))
            <script>
                {!! $item->js !!}
            </script>
            @endif
            
            @if(isset($item->css))
            <style>
                {!! $item->css !!}
            </style>
            @endif
            
            <!-- Navigation -->
            <div class="mt-12 pt-8 border-t border-gray-200 dark:border-gray-700">
                <div class="flex flex-wrap justify-between items-center gap-4">
                    <a href="{{ $pageIndexUrl }}" 
                       class="group inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold rounded-xl hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200"
                       x-data="{ hover: false }"
                       @mouseenter="hover = true"
                       @mouseleave="hover = false">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 transform transition-transform duration-200"
                             :class="hover ? '-translate-x-1' : ''"
                             fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        {{ __('page::front.general.all_pages') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function pageShow() {
    return {
        loaded: false,
        
        init() {
            // Instant load
            this.loaded = true;
            
            // Preload pages list
            this.preloadIndex();
        },
        
        preloadIndex() {
            const link = document.createElement('link');
            link.rel = 'prefetch';
            @php
                $indexSlug = \App\Services\ModuleSlugService::getSlug('Page', 'index');
                $pageIndexUrl = '/' . $indexSlug;
            @endphp
            link.href = '{{ $pageIndexUrl }}';
            document.head.appendChild(link);
        }
    }
}
</script>
@endif
@endsection
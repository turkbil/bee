@extends('page::front.themes.blank.layouts.app')

@push('head')
{{-- Schema.org için sayfa bilgileri --}}
{!! \App\Services\SEOService::getPageSchema($item) !!}
@endpush

@section('content')
@if(isset($is_homepage) && $is_homepage)
@parsewidgets($item->body)
    
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
@else
<div class="py-6" x-data="pageShow()" x-init="init()">
    <article class="max-w-4xl mx-auto" x-show="loaded" x-transition.duration.300ms>
        <div class="p-6">
            <h1 class="text-3xl font-bold mb-4 text-gray-900 dark:text-white">{{ $item->title }}</h1>
            
            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200 dark:border-gray-700">
                <time class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                    <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                    </svg>
                    {{ $item->created_at->format('d.m.Y') }}
                </time>
            </div>

            <div class="prose max-w-none dark:prose-invert prose-headings:text-gray-900 dark:prose-headings:text-white prose-p:text-gray-800 dark:prose-p:text-gray-200 prose-a:text-blue-600 dark:prose-a:text-blue-400">
                @parsewidgets($item->body)
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
            
            <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                <button @click="goBack()" class="inline-flex items-center text-blue-600 dark:text-blue-400 hover:underline font-medium">
                    <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"></path>
                    </svg>
                    {{ __('page::general.all_pages') }}
                </button>
            </div>
        </div>
    </article>
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
            link.href = '{{ route("pages.index") }}';
            document.head.appendChild(link);
        },
        
        goBack() {
            if (history.length > 1) {
                history.back();
            } else {
                window.location.href = '{{ route("pages.index") }}';
            }
        }
    }
}
</script>
@endif
@endsection
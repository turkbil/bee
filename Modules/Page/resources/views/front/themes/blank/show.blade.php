@extends('themes.blank.layouts.app')

@section('content')
@if(isset($is_homepage) && $is_homepage)
    <div class="homepage-widget-wrapper">@parsewidgets($item->body)</div>

    @if(!empty(trim($item->custom_js ?? '')))
    <script>
        {{ $item->custom_js }}
    </script>
    @endif

    @if(!empty(trim($item->custom_css ?? '')))
    <style>
        {{ $item->custom_css }}
    </style>
    @endif
@else
<div class="container animate-fade-in">
    <article>
            <h1 class="text-3xl font-bold mb-4 text-gray-900 dark:text-white">{{ $item->title }}</h1>
            
            <div class="flex flex-wrap items-center gap-3 mb-6 pb-4 border-b">
                <span class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    {{ $item->created_at->format('d.m.Y') }}
                </span>
                
            </div>

            <div class="content prose max-w-none dark:prose-invert text-gray-800 dark:text-gray-200">
                <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6 transition-colors duration-300">
        @parsewidgets($item->body)
    </div>
            </div>
            
            @if(!empty(trim($item->js ?? '')))
            <script>
                {!! $item->js !!}
            </script>
            @endif
            
            @if(!empty(trim($item->css ?? '')))
            <style>
                {!! $item->css !!}
            </style>
            @endif
            
            <div class="mt-8 pt-4 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ href('Page', 'index') }}" class="inline-flex items-center text-primary dark:text-primary-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Tüm Sayfalar
                </a>
            </div>
    </article>
</div>
@endif
@endsection
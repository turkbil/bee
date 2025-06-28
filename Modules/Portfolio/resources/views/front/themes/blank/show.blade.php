@extends('themes.blank.layouts.app')

@section('content')
<div class="container animate-fade-in">
    <article>
        <div class="relative">
            @if($item->getMedia('images')->isNotEmpty())
            <img src="{{ $item->getFirstMedia('images')->getUrl() }}" alt="{{ $item->title }}" class="w-full h-80 object-cover">
            @else
            <img src="https://www.placehold.co/1200x400/f0f0f0/909090?text={{ urlencode($item->title) }}" alt="{{ $item->title }}" class="w-full h-80 object-cover">
            @endif
            
            @if(isset($item->category))
            <div class="absolute top-4 right-4">
                <a href="{{ href('Portfolio', 'category', $item->category->slug) }}" class="text-primary dark:text-primary-400 hover:underline text-sm font-medium transition-colors">
                    {{ $item->category->title }}
                </a>
            </div>
            @endif
        </div>
        
        <div class="py-6">
            <h1 class="text-2xl md:text-3xl font-bold mb-4 text-gray-900 dark:text-white">{{ $item->title }}</h1>
            
            <div class="flex flex-wrap items-center gap-3 mb-6 pb-4 border-b text-sm text-gray-600 dark:text-gray-400">
                <span class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    {{ $item->created_at->format('d.m.Y') }}
                </span>
                
                @if(isset($item->category))
                <span class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                    <a href="{{ href('Portfolio', 'category', $item->category->slug) }}" class="hover:text-primary dark:hover:text-primary-400 transition-colors">
                        {{ $item->category->title }}
                    </a>
                </span>
                @endif
                
            </div>

            <div class="prose prose-sm sm:prose lg:prose-lg max-w-none dark:prose-invert prose-headings:text-gray-900 dark:prose-headings:text-white prose-a:text-primary dark:prose-a:text-primary-400 prose-img:rounded-md">
                {!! $item->body !!}
            </div>
            
            @if(isset($item->client) || isset($item->date) || isset($item->url))
            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                <h3 class="text-xl font-semibold mb-4 text-gray-900 dark:text-white">{{ __('portfolio::general.project_details') }}</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-gray-700 dark:text-gray-400">
                    @if(isset($item->client))
                    <div>
                        <span class="font-medium block text-gray-900 dark:text-white">{{ __('portfolio::general.client') }}:</span>
                        <span>{{ $item->client }}</span>
                    </div>
                    @endif
                    
                    @if(isset($item->date))
                    <div>
                        <span class="font-medium block text-gray-900 dark:text-white">{{ __('portfolio::general.project_date') }}:</span>
                        <span>{{ $item->date }}</span>
                    </div>
                    @endif
                    
                    @if(isset($item->url))
                    <div class="md:col-span-2">
                        <span class="font-medium block text-gray-900 dark:text-white">{{ __('portfolio::general.project_url') }}:</span>
                        <a href="{{ $item->url }}" target="_blank" class="text-primary dark:text-primary-400 hover:underline">{{ $item->url }}</a>
                    </div>
                    @endif
                </div>
            </div>
            @endif
            
            <div class="mt-8 flex flex-wrap justify-between items-center gap-4">
                <a href="{{ href('Portfolio', 'index') }}" class="inline-flex items-center text-sm text-primary dark:text-primary-400 hover:underline font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    {{ __('portfolio::general.all_portfolios') }}
                </a>
                
                @if(isset($item->category))
                <a href="{{ href('Portfolio', 'category', $item->category->slug) }}" class="inline-flex items-center text-sm text-primary dark:text-primary-400 hover:underline font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                    {{ $item->category->title }} {{ __('portfolio::general.portfolios') }}
                </a>
                @endif
            </div>
        </div>
    </article>
</div>
@endsection
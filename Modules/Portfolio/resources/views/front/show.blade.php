@extends('portfolio::front.themes.blank.layouts.app')

@section('module_content')
<div class="animate-fade-in py-6">
    <article class="max-w-4xl mx-auto overflow-hidden">
        @if($item->getMedia('images')->isNotEmpty())
        <div class="relative">
            <img src="{{ $item->getFirstMedia('images')->getUrl() }}" alt="{{ $item->title }}" class="w-full h-80 object-cover">
            
            @if(isset($item->category))
            <div class="absolute top-4 right-4">
                <a href="{{ route('portfolios.category', $item->category->slug) }}" class="inline-flex items-center px-3 py-1.5 bg-primary bg-opacity-90 text-white text-sm font-medium rounded-full hover:bg-opacity-100 transition-colors">
                    {{ $item->category->name ?? $item->category->title }}
                </a>
            </div>
            @endif
        </div>
        @endif
        
        <div class="p-8">
            <h1 class="text-3xl font-bold mb-4 text-gray-900 dark:text-white">{{ $item->title }}</h1>
            
            <div class="flex flex-wrap items-center gap-3 mb-6 pb-4 border-b border-gray-200 dark:border-gray-700">
                <span class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    {{ $item->created_at->format('d.m.Y') }}
                </span>
                
                @if(isset($item->category))
                <a href="{{ route('portfolios.category', $item->category->slug) }}" class="flex items-center text-sm text-primary dark:text-primary-400 hover:underline">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                    {{ $item->category->name ?? $item->category->title }}
                </a>
                @endif
                
            </div>

            <div class="content prose max-w-none dark:prose-invert prose-headings:text-gray-900 dark:prose-headings:text-white prose-p:text-gray-800 dark:prose-p:text-gray-200 prose-a:text-primary dark:prose-a:text-primary-400">
                @if(isset($item->body))
                    {!! $item->body !!}
                @elseif(isset($item->content))
                    {!! $item->content !!}
                @endif
            </div>
            
            @if(isset($item->client) || isset($item->date) || isset($item->url))
            <div class="mt-8 p-6 bg-slate-50 dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700">
                <h3 class="text-xl font-semibold mb-4 text-gray-900 dark:text-white">Proje Detayları</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @if(isset($item->client))
                    <div class="flex flex-col">
                        <span class="font-medium text-gray-700 dark:text-gray-300 mb-1">Müşteri:</span>
                        <span class="text-gray-800 dark:text-gray-200">{{ $item->client }}</span>
                    </div>
                    @endif
                    
                    @if(isset($item->date))
                    <div class="flex flex-col">
                        <span class="font-medium text-gray-700 dark:text-gray-300 mb-1">Proje Tarihi:</span>
                        <span class="text-gray-800 dark:text-gray-200">{{ $item->date }}</span>
                    </div>
                    @endif
                    
                    @if(isset($item->url))
                    <div class="md:col-span-2 flex flex-col">
                        <span class="font-medium text-gray-700 dark:text-gray-300 mb-1">Proje URL:</span>
                        <a href="{{ $item->url }}" target="_blank" class="text-primary dark:text-primary-400 hover:underline break-all">{{ $item->url }}</a>
                    </div>
                    @endif
                </div>
            </div>
            @endif
            
            <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700 flex flex-wrap gap-4 justify-between items-center">
                <a href="{{ route('portfolios.index') }}" class="inline-flex items-center text-primary dark:text-primary-400 hover:underline font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Tüm Projeler
                </a>
                
                @if(isset($item->category))
                <a href="{{ route('portfolios.category', $item->category->slug) }}" class="inline-flex items-center text-primary dark:text-primary-400 hover:underline font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                    {{ $item->category->name ?? $item->category->title }} Projeleri
                </a>
                @endif
            </div>
        </div>
    </article>
</div>
@endsection
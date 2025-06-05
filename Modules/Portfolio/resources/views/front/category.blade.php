@extends('portfolio::front.themes.blank.layouts.app')

@section('module_content')
<div class="animate-fade-in py-6">
    <div class="max-w-5xl mx-auto">
        <h1 class="text-3xl font-bold mb-4 text-gray-900 dark:text-white">{{ $category->title }} Kategorisi</h1>
            
            @if(isset($category->body) && trim(strip_tags($category->body)) !== '')
            <div class="prose max-w-none dark:prose-invert prose-headings:text-gray-900 dark:prose-headings:text-white prose-p:text-gray-800 dark:prose-p:text-gray-200 prose-a:text-primary dark:prose-a:text-primary-400 mb-6">
                {!! $category->body !!}
            </div>
            @endif
            
            <div class="mb-6">
                <a href="{{ route('portfolios.index') }}" class="inline-flex items-center text-primary dark:text-primary-400 hover:underline font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Tüm Portfolyolar
                </a>
            </div>

        @if($items->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($items as $item)
            <div class="portfolio-item overflow-hidden hover:shadow-sm transition-shadow duration-300">
                @if($item->getMedia('images')->isNotEmpty())
                <div class="relative overflow-hidden aspect-w-16 aspect-h-9">
                    <a href="{{ route('portfolios.show', $item->slug) }}">
                        <img src="{{ $item->getFirstMedia('images')->getUrl() }}" alt="{{ $item->title }}" 
                            class="w-full h-48 object-cover transition-transform duration-300 hover:scale-105">
                    </a>
                </div>
                @elseif(isset($item->getFirstMedia) && $item->getFirstMedia('image'))
                <div class="relative overflow-hidden aspect-w-16 aspect-h-9">
                    <a href="{{ route('portfolios.show', $item->slug) }}">
                        <img src="{{ $item->getFirstMedia('image')->getUrl() }}" alt="{{ $item->title }}" 
                            class="w-full h-48 object-cover transition-transform duration-300 hover:scale-105">
                    </a>
                </div>
                @endif
                
                <div class="p-6">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">
                        <a href="{{ route('portfolios.show', $item->slug) }}" 
                            class="hover:text-primary dark:hover:text-primary-400 transition-colors duration-300">{{ $item->title }}</a>
                    </h3>
                    
                    <div class="flex flex-wrap items-center gap-3 text-sm text-gray-600 dark:text-gray-400 mb-4">
                        <span class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            {{ $item->created_at->format('d.m.Y') }}
                        </span>
                        
                        @if(function_exists('views'))
                        <span class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            {{ views($item)->count() }}
                        </span>
                        @endif
                    </div>
                    
                    @if(isset($item->metadesc) || isset($item->body) || isset($item->content))
                    <div class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        @if(isset($item->metadesc))
                            {{ Str::limit($item->metadesc, 120) }}
                        @elseif(isset($item->body))
                            {{ Str::limit(strip_tags($item->body), 120) }}
                        @elseif(isset($item->content))
                            {{ Str::limit(strip_tags($item->content), 120) }}
                        @endif
                    </div>
                    @endif
                    
                    <div class="mt-4">
                        <a href="{{ route('portfolios.show', $item->slug) }}" class="inline-flex items-center text-sm text-primary dark:text-primary-400 hover:underline font-medium">
                            Detayları Gör
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $items->links() }}
        </div>
        @else
        <div class="p-8 text-center border-t-4 border-primary dark:border-primary-400">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 dark:text-gray-600 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <p class="text-lg text-gray-600 dark:text-gray-400">Bu kategoride henüz portfolyo bulunmamaktadır.</p>
        </div>
        @endif
    </div>
</div>
@endsection
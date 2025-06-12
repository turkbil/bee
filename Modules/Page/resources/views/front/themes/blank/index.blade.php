@extends('themes.blank.layouts.app')

@section('content')
<div class="container animate-fade-in">
    <h1 class="text-3xl font-bold mb-6 text-center text-gray-800 dark:text-white">{{ $title ?? 'Sayfalar' }}</h1>

    @if($items->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($items as $item)
                <div class="page-item overflow-hidden hover:shadow-sm transition-shadow duration-300">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">
                            <a href="{{ href('Page', 'show', $item->slug) }}" class="hover:text-primary dark:hover:text-primary-400 transition-colors duration-300">{{ $item->title }}</a>
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
                            <a href="{{ href('Page', 'show', $item->slug) }}" class="inline-flex items-center text-sm text-primary dark:text-primary-400 hover:underline font-medium">
                                Devamını Oku
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
        <div class="border-t-4 border-primary dark:border-primary-400 p-8 text-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 dark:text-gray-600 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <p class="text-lg text-gray-600 dark:text-gray-400">Henüz sayfa bulunmamaktadır.</p>
        </div>
    @endif
</div>
@endsection
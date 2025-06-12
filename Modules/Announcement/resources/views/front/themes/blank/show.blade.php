@extends('themes.blank.layouts.app')

@section('content')
<div class="container animate-fade-in">
    <article>
        <div class="py-6">
            <h1 class="text-3xl font-bold mb-4 text-gray-900 dark:text-white">{{ $item->title }}</h1>
            
            <div class="flex flex-wrap items-center gap-3 mb-6 pb-4 border-b">
                <span class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    {{ $item->created_at->format('d.m.Y') }}
                </span>
                
                @if(function_exists('views'))
                <span class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    {{ views($item)->count() }} görüntülenme
                </span>
                @endif
                
                @if(isset($item->attachment) && $item->attachment)
                <span class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                    </svg>
                    Ek Dosya
                </span>
                @endif
            </div>

            <div class="content prose max-w-none dark:prose-invert text-gray-800 dark:text-gray-200">
                @if(isset($item->body))
                    {!! $item->body !!}
                @elseif(isset($item->content))
                    {!! $item->content !!}
                @endif
            </div>

            @if(isset($item->attachment) && $item->attachment)
            <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-primary dark:text-primary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                    </svg>
                    Ekler
                </h3>
                <div class="flex items-center">
                    <a href="{{ $item->attachment }}" class="inline-flex items-center text-primary dark:text-primary-400" target="_blank">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Eki İndir
                    </a>
                </div>
            </div>
            @endif

            <div class="mt-8 pt-4 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ href('Announcement', 'index') }}" class="inline-flex items-center text-primary dark:text-primary-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Tüm Duyurular
                </a>
            </div>
        </div>
    </article>
</div>
@endsection
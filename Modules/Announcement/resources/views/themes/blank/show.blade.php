@extends('themes.blank.layouts.app')

@section('module_content')
<div class="container animate-fade-in">
    <article>
        <div class="py-6">
            @php
                $currentLocale = app()->getLocale();
                
                // Direct JSON field access with proper decoding
                $titleData = $item->title;
                $bodyData = $item->body;
                
                // If string, decode JSON
                if (is_string($titleData)) {
                    $titleData = json_decode($titleData, true) ?: [];
                }
                if (is_string($bodyData)) {
                    $bodyData = json_decode($bodyData, true) ?: [];
                }
                
                // Extract current language content
                $title = is_array($titleData) ? ($titleData[$currentLocale] ?? $titleData['tr'] ?? reset($titleData)) : $titleData;
                $title = $title ?: 'Başlıksız';
                
                $body = is_array($bodyData) ? ($bodyData[$currentLocale] ?? $bodyData['tr'] ?? reset($bodyData)) : $bodyData;
            @endphp
            <h1 class="text-3xl font-bold mb-4 text-gray-900 dark:text-white">{{ $title }}</h1>
            
            <div class="flex flex-wrap items-center gap-3 mb-6 pb-4 border-b">
                <span class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    {{ $item->created_at->format('d.m.Y') }}
                </span>
                
                
                @if(isset($item->attachment) && $item->attachment)
                <span class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                    </svg>
                    {{ __('announcement::front.general.attachment') }}
                </span>
                @endif
            </div>

            <div class="content prose max-w-none dark:prose-invert text-gray-800 dark:text-gray-200">
                @parsewidgets($body ?? '')
            </div>

            @if(isset($item->attachment) && $item->attachment)
            <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-primary dark:text-primary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                    </svg>
                    {{ __('announcement::front.general.attachment') }}
                </h3>
                <div class="flex items-center">
                    <a href="{{ $item->attachment }}" class="inline-flex items-center text-primary dark:text-primary-400" target="_blank">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        {{ basename($item->attachment) }}
                    </a>
                </div>
            </div>
            @endif

            <div class="mt-8 pt-4 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('announcements.index') }}" class="inline-flex items-center text-primary dark:text-primary-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    {{ __('announcement::front.general.all_announcements') }}
                </a>
            </div>
        </div>
    </article>
</div>
@endsection
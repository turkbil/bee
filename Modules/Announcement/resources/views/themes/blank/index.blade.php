@extends('themes.blank.layouts.app')

@section('module_content')
<div class="container animate-fade-in">
    <h1 class="text-3xl font-bold mb-6 text-center text-gray-800 dark:text-white">{{ $title ?? __('announcement::front.general.announcements') }}</h1>
    
    @if($items->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach($items as $item)
        @php
            $currentLocale = app()->getLocale();
            
            // Direct JSON field access with proper decoding
            $titleData = $item->title;
            $slugData = $item->slug;
            $metadescData = $item->metadesc;
            $bodyData = $item->body;
            
            // If string, decode JSON
            if (is_string($titleData)) {
                $titleData = json_decode($titleData, true) ?: [];
            }
            if (is_string($slugData)) {
                $slugData = json_decode($slugData, true) ?: [];
            }
            if (is_string($metadescData)) {
                $metadescData = json_decode($metadescData, true) ?: [];
            }
            if (is_string($bodyData)) {
                $bodyData = json_decode($bodyData, true) ?: [];
            }
            
            // Extract current language content
            $title = is_array($titleData) ? ($titleData[$currentLocale] ?? $titleData['tr'] ?? reset($titleData)) : $titleData;
            $title = $title ?: 'Başlıksız';
            
            $slug = is_array($slugData) ? ($slugData[$currentLocale] ?? $slugData['tr'] ?? reset($slugData)) : $slugData;
            $slug = $slug ?: $item->announcement_id; // Fallback to ID if no slug
            
            $metadesc = is_array($metadescData) ? ($metadescData[$currentLocale] ?? $metadescData['tr'] ?? reset($metadescData)) : $metadescData;
            $body = is_array($bodyData) ? ($bodyData[$currentLocale] ?? $bodyData['tr'] ?? reset($bodyData)) : $bodyData;
            $description = $metadesc ?? strip_tags($body ?? '') ?? null;
            
            // DİNAMİK URL - ModuleSlugService'den show slug'ını al
            $showSlug = \App\Services\ModuleSlugService::getSlug('Announcement', 'show');
            $dynamicUrl = '/' . $showSlug . '/' . $slug;
        @endphp
        <div class="announcement-item group overflow-hidden hover:shadow-sm transition-shadow duration-300">
            <div class="p-6">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">
                    <a href="{{ $dynamicUrl }}" class="hover:text-primary dark:hover:text-primary-400 transition-colors duration-300">{{ $title }}</a>
                </h3>
                
                <div class="flex flex-wrap items-center gap-3 text-sm text-gray-600 dark:text-gray-400 mb-4">
                    <span class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        {{ $item->created_at->format(__('announcement::front.general.date_format')) }}
                    </span>
                    
                    
                    @if(isset($item->attachment) && $item->attachment)
                    <span class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                        </svg>
                        <span class="text-xs">{{ __('announcement::front.general.attachment') }}</span>
                    </span>
                    @endif
                </div>
                
                @if($description)
                <div class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                    {{ Str::limit($description, 120) }}
                </div>
                @endif
                
                <div class="mt-4">
                    <a href="{{ $dynamicUrl }}" class="inline-flex items-center text-sm text-primary dark:text-primary-400 hover:underline font-medium">
                        {{ __('announcement::front.general.continue_reading') }}
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-8 pagination">
        {{ $items->links() }}
    </div>
    @else
    <div class="border-t-4 border-primary dark:border-primary-400 p-8 text-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
        </svg>
        <p class="mt-4 text-xl">{{ __('announcement::front.general.no_announcements_yet') }}</p>
    </div>
    @endif
</div>
@endsection
@extends('themes.blank.layouts.app')

@section('module_content')
<div class="bg-white dark:bg-gray-900" x-data="portfolioShow()" x-init="init()">
    
    <!-- Header -->
    <div class="border-b border-gray-100 dark:border-gray-800">
        <div class="py-16">
                @php
                    $currentLocale = app()->getLocale();
                    
                    // Direct JSON field access with proper decoding
                    $titleData = $item->title;
                    
                    // If string, decode JSON
                    if (is_string($titleData)) {
                        $titleData = json_decode($titleData, true) ?: [];
                    }
                    
                    // Extract current language content
                    $title = is_array($titleData) ? ($titleData[$currentLocale] ?? $titleData['tr'] ?? reset($titleData)) : $titleData;
                    $title = $title ?: 'Başlıksız';
                @endphp
                
                <!-- Title -->
                <h1 class="text-4xl font-semibold text-gray-900 dark:text-white mb-4">
                    {{ $title }}
                </h1>
        </div>
    </div>

    <!-- Content -->
    <div class="py-16">
        <div class="prose prose-lg max-w-none dark:prose-invert 
                   prose-headings:text-gray-900 dark:prose-headings:text-white 
                   prose-p:text-gray-600 dark:prose-p:text-gray-300 
                   prose-a:text-blue-600 dark:prose-a:text-blue-400 
                   prose-strong:text-gray-900 dark:prose-strong:text-white
                   prose-img:rounded-lg">
            
            <!-- Image if exists -->
            @if($item->getMedia('images')->isNotEmpty())
            <div class="mb-8">
                <img src="{{ $item->getFirstMedia('images')->getUrl() }}" 
                     alt="{{ $title }}" 
                     class="w-full h-64 object-cover rounded-lg">
            </div>
            @endif

            @php
                $bodyData = $item->body;
                
                // If string, decode JSON
                if (is_string($bodyData)) {
                    $bodyData = json_decode($bodyData, true) ?: [];
                }
                
                $body = is_array($bodyData) ? ($bodyData[$currentLocale] ?? $bodyData['tr'] ?? reset($bodyData)) : $bodyData;
                // HTML decode et sonra strip_tags uygula
                $body = html_entity_decode($body, ENT_QUOTES, 'UTF-8');
                $body = strip_tags($body); // HTML kodlarını temizle
            @endphp
            
            @if($body)
                <p class="text-gray-600 dark:text-gray-300 leading-relaxed">{{ $body }}</p>
            @endif
            
            @if(isset($item->client) || isset($item->date) || isset($item->url))
            <div class="mt-8 p-6 bg-gray-50 dark:bg-gray-800/50 rounded-lg border border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">{{ __('portfolio::front.general.project_details') }}</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @if(isset($item->client))
                    <div class="flex flex-col">
                        <span class="font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('portfolio::front.general.client_name') }}:</span>
                        <span class="text-gray-900 dark:text-gray-100">{{ $item->client }}</span>
                    </div>
                    @endif
                    
                    @if(isset($item->date))
                    <div class="flex flex-col">
                        <span class="font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('portfolio::front.general.project_date') }}:</span>
                        <span class="text-gray-900 dark:text-gray-100">{{ $item->date }}</span>
                    </div>
                    @endif
                    
                    @if(isset($item->url))
                    <div class="md:col-span-2 flex flex-col">
                        <span class="font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('portfolio::front.general.project_url') }}:</span>
                        <a href="{{ $item->url }}" target="_blank" class="text-blue-600 dark:text-blue-400 hover:underline break-all">{{ $item->url }}</a>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
function portfolioShow() {
    return {
        loaded: false,
        
        init() {
            this.loaded = true;
            this.preloadIndex();
        },
        
        preloadIndex() {
            const link = document.createElement('link');
            link.rel = 'prefetch';
            @php
                $indexSlug = \App\Services\ModuleSlugService::getSlug('Portfolio', 'index');
                $portfolioIndexUrl = '/' . $indexSlug;
            @endphp
            link.href = '{{ $portfolioIndexUrl }}';
            document.head.appendChild(link);
        },
        
        goBack() {
            if (history.length > 1) {
                history.back();
            } else {
                @php
                    $indexSlug = \App\Services\ModuleSlugService::getSlug('Portfolio', 'index');
                    $portfolioIndexUrl = '/' . $indexSlug;
                @endphp
                window.location.href = '{{ $portfolioIndexUrl }}';
            }
        }
    }
}
</script>
@endsection
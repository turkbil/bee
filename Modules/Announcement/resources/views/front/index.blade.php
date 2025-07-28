@extends('themes.blank.layouts.app')

@section('module_content')
<div class="bg-white dark:bg-gray-900" x-data="announcementList()" x-init="init()">
    
    <!-- Header -->
    <div class="border-b border-gray-100 dark:border-gray-800">
        <div class="py-16">
            <h1 class="text-4xl font-semibold text-gray-900 dark:text-white mb-4">
                {{ $title ?? __('announcement::front.general.announcements') }}
            </h1>
        </div>
    </div>

    <div class="py-16">
        @if($items->count() > 0)
        <!-- Articles -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
            @foreach($items as $item)
            @php
                $currentLocale = app()->getLocale();
                $slugData = $item->getRawOriginal('slug');
                
                if (is_string($slugData)) {
                    $slugData = json_decode($slugData, true) ?: [];
                }
                $slug = is_array($slugData) ? ($slugData[$currentLocale] ?? $slugData['tr'] ?? reset($slugData)) : $slugData;
                $slug = $slug ?: $item->announcement_id;
                
                $title = $item->getTranslated('title') ?? $item->getRawOriginal('title') ?? $item->title ?? 'Başlıksız';
                
                $showSlug = \App\Services\ModuleSlugService::getSlug('Announcement', 'show');
                $dynamicUrl = '/' . $showSlug . '/' . $slug;

                $metadesc = $item->getTranslated('metadesc') ?? $item->getRawOriginal('metadesc') ?? $item->metadesc ?? null;
                $body = $item->getTranslated('body') ?? $item->getRawOriginal('body') ?? $item->body ?? null;
                $description = $metadesc ?? strip_tags($body) ?? null;
            @endphp
            
            <article class="group" 
                     @click="navigate('{{ $dynamicUrl }}')" 
                     @mouseenter="prefetch('{{ $dynamicUrl }}')">
                
                <div class="cursor-pointer">
                    
                    <!-- Date -->
                    <div class="text-sm text-gray-500 dark:text-gray-400 mb-3">
                        {{ $item->created_at->format('d M Y') }}
                    </div>

                    <!-- Title -->
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                        {{ $title }}
                    </h2>

                    <!-- Description -->
                    @if($description)
                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed mb-6">
                        {{ Str::limit($description, 150) }}
                    </p>
                    @endif

                    <!-- Read More -->
                    <div class="inline-flex items-center text-sm font-medium text-blue-600 dark:text-blue-400 group-hover:text-blue-700 dark:group-hover:text-blue-300 transition-colors">
                        {{ __('announcement::front.general.read_more') }}
                        <svg class="h-4 w-4 ml-1 group-hover:translate-x-1 transition-transform" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                </div>
            </article>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($items->hasPages())
        <div class="mt-16 border-t border-gray-100 dark:border-gray-800 pt-12">
            {{ $items->links() }}
        </div>
        @endif

        @else
        <!-- Empty State -->
        <div class="text-center py-20">
            <div class="w-16 h-16 bg-gray-100 dark:bg-gray-800 rounded-lg flex items-center justify-center mx-auto mb-4">
                <svg class="h-8 w-8 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Henüz duyuru yok</h3>
            <p class="text-gray-500 dark:text-gray-400">{{ __('announcement::front.general.no_announcements_yet') }}</p>
        </div>
        @endif
    </div>
</div>

<script>
function announcementList() {
    return {
        loaded: false,
        prefetchedUrls: new Set(),
        
        init() {
            this.loaded = true;
        },
        
        prefetch(url) {
            if (this.prefetchedUrls.has(url)) return;
            
            const link = document.createElement('link');
            link.rel = 'prefetch';
            link.href = url;
            document.head.appendChild(link);
            this.prefetchedUrls.add(url);
        },
        
        navigate(url) {
            window.location.href = url;
        }
    }
}
</script>
@endsection
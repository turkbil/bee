@extends('themes.blank.layouts.app')

@section('module_content')
<div class="relative" x-data="announcementList()" x-init="init()">
    
    <!-- Gradient Background -->
    <div class="absolute inset-0 bg-gradient-to-br from-amber-50 via-white to-orange-50 dark:from-gray-900 dark:via-gray-900 dark:to-gray-800 -z-10"></div>
    
    <!-- Header -->
    <div class="relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-r from-amber-600/10 to-orange-600/10 dark:from-amber-600/20 dark:to-orange-600/20"></div>
        <div class="relative py-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="max-w-3xl">
                <h1 class="text-5xl font-bold bg-gradient-to-r from-gray-900 to-gray-700 dark:from-white dark:to-gray-300 bg-clip-text text-transparent mb-4">
                    {{ $moduleTitle ?? __('announcement::front.general.announcements') }}
                </h1>
                <p class="text-lg text-gray-600 dark:text-gray-400">
                    Güncel duyuru ve haberlerimiz
                </p>
                </div>
            </div>
        </div>
    </div>

    <div class="py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if($items->count() > 0)
            <!-- Articles Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
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
                // Locale-aware URL
                $defaultLocale = get_tenant_default_locale();
                if ($currentLocale === $defaultLocale) {
                    $dynamicUrl = '/' . $showSlug . '/' . $slug;
                } else {
                    $dynamicUrl = '/' . $currentLocale . '/' . $showSlug . '/' . $slug;
                }

                $metadesc = $item->getTranslated('metadesc') ?? $item->getRawOriginal('metadesc') ?? $item->metadesc ?? null;
                $body = $item->getTranslated('body') ?? $item->getRawOriginal('body') ?? $item->body ?? null;
                $description = $metadesc ?? strip_tags($body) ?? null;
            @endphp
            
            <article class="group relative bg-white dark:bg-gray-800 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 dark:border-gray-700" 
                     @click="navigate('{{ $dynamicUrl }}')" 
                     @mouseenter="prefetch('{{ $dynamicUrl }}'); hover = {{ $loop->index }}" 
                     @mouseleave="hover = null"
                     x-data="{ localHover: false }"
                     @mouseenter.self="localHover = true"
                     @mouseleave.self="localHover = false">
                
                <!-- Gradient Overlay -->
                <div class="absolute inset-0 bg-gradient-to-br from-amber-600/5 to-orange-600/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                
                <!-- Important Badge for Recent -->
                @if($item->created_at->diffInDays() < 3)
                <div class="absolute top-4 right-4 z-10">
                    <span class="inline-flex items-center px-2 py-1 text-xs font-bold text-white bg-gradient-to-r from-amber-500 to-orange-500 rounded-full shadow-lg animate-pulse">
                        YENİ
                    </span>
                </div>
                @endif
                
                <div class="relative p-6 cursor-pointer">
                    
                    <!-- Date with Icon -->
                    <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 mb-4">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                        {{ $item->created_at->format('d M Y') }}
                    </div>

                    <!-- Title with hover effect -->
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-3 group-hover:text-transparent group-hover:bg-gradient-to-r group-hover:from-amber-600 group-hover:to-orange-600 group-hover:bg-clip-text transition-all duration-300">
                        {{ $title }}
                    </h2>

                    <!-- Description -->
                    @if($description)
                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed mb-6 line-clamp-3">
                        {{ Str::limit($description, 120) }}
                    </p>
                    @endif

                    <!-- Read More with Alpine animation -->
                    <div class="inline-flex items-center text-sm font-semibold text-transparent bg-gradient-to-r from-amber-600 to-orange-600 bg-clip-text group-hover:from-amber-700 group-hover:to-orange-700 transition-all duration-300"
                         x-show="true"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-75 transform translate-x-0"
                         x-transition:enter-end="opacity-100 transform translate-x-0">
                        {{ __('announcement::front.general.read_more') }}
                        <svg class="h-4 w-4 ml-2 text-amber-600 group-hover:text-orange-600 transition-all duration-300"
                             :class="localHover ? 'translate-x-1' : 'translate-x-0'"
                             fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    
                    <!-- Hover Border Effect -->
                    <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-amber-600 to-orange-600 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300"></div>
                </div>
            </article>
            @endforeach
            @php
                // Foreach döngüsü sonrası $item değişkenini temizle
                // Böylece header.blade.php'de yanlış kullanılmaz
                unset($item);
            @endphp
        </div>

            <!-- Pagination -->
            @if($items->hasPages())
            <div class="mt-20">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-4">
                    {{ $items->links() }}
                </div>
            </div>
            @endif

            @else
            <!-- Empty State -->
            <div class="text-center py-20" x-data="{ show: false }" x-init="setTimeout(() => show = true, 100)">
                <div x-show="show"
                     x-transition:enter="transition ease-out duration-500"
                     x-transition:enter-start="opacity-0 transform scale-90"
                     x-transition:enter-end="opacity-100 transform scale-100"
                     class="inline-block">
                    <div class="w-20 h-20 bg-gradient-to-br from-amber-100 to-orange-200 dark:from-amber-700 dark:to-orange-800 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg">
                        <svg class="h-10 w-10 text-amber-500 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Henüz duyuru yok</h3>
                    <p class="text-gray-500 dark:text-gray-400 max-w-sm mx-auto">{{ __('announcement::front.general.no_announcements_yet') }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
function announcementList() {
    return {
        loaded: false,
        hover: null,
        prefetchedUrls: new Set(),
        
        init() {
            // Smooth fade in
            this.$nextTick(() => {
                this.loaded = true;
            });
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
            // Add a subtle loading state
            document.body.style.cursor = 'wait';
            window.location.href = url;
        }
    }
}
</script>
@endsection
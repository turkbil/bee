@php
    $themeService = app(\App\Services\ThemeService::class);
    $activeTheme = $themeService->getActiveTheme();
    $themeName = $activeTheme ? $activeTheme->name : 'simple';
@endphp
@extends('themes.' . $themeName . '.layouts.app')

@section('module_content')
    <div class="relative" x-data="announcementsList()" x-init="init()">

        <!-- Gradient Background -->
        <div
            class="absolute inset-0 bg-gradient-to-br from-blue-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-900 dark:to-gray-800 -z-10">
        </div>

        <!-- Header -->
        <div class="relative overflow-hidden">
            <div
                class="absolute inset-0 bg-gradient-to-r from-blue-600/10 to-purple-600/10 dark:from-blue-600/20 dark:to-purple-600/20">
            </div>
            <div class="relative py-20">
                <div class="container mx-auto px-4 sm:px-4 md:px-0">
                    <div class="max-w-3xl">
                        <h1
                            class="text-5xl font-bold bg-gradient-to-r from-gray-900 to-gray-700 dark:from-white dark:to-gray-300 bg-clip-text text-transparent mb-4">
                            {{ $moduleTitle ?? __('announcement::front.general.announcements') }}
                        </h1>
                        <p class="text-lg text-gray-600 dark:text-gray-400">
                            Bilgi sayfalarımızı keşfedin
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="py-20">
            <div class="container mx-auto px-4 sm:px-4 md:px-0">
                @if ($items->count() > 0)
                    <!-- Articles Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        @foreach ($items as $item)
                            @php
                                $currentLocale = app()->getLocale();
                                $slugData = $item->getRawOriginal('slug');

                                if (is_string($slugData)) {
                                    $slugData = json_decode($slugData, true) ?: [];
                                }
                                // Slug için öncelikle mevcut dili kontrol et, yoksa fallback ama URL'de mevcut dili koru
if (
    is_array($slugData) &&
    isset($slugData[$currentLocale]) &&
    !empty($slugData[$currentLocale])
) {
    $slug = $slugData[$currentLocale];
} else {
    // Mevcut dilde slug yoksa fallback kullan ama URL'de mevcut dili koru
                                    $slug = is_array($slugData) ? $slugData['tr'] ?? reset($slugData) : $slugData;
                                }
                                $slug = $slug ?: $item->announcement_id;

                                $title =
                                    $item->getTranslated('title') ??
                                    ($item->getRawOriginal('title') ?? ($item->title ?? 'Başlıksız'));

                                $showSlug = \App\Services\ModuleSlugService::getSlug('Announcement', 'show');
                                // Locale-aware URL
                                $defaultLocale = get_tenant_default_locale();
                                if ($currentLocale === $defaultLocale) {
                                    $dynamicUrl = '/' . $showSlug . '/' . $slug;
                                } else {
                                    $dynamicUrl = '/' . $currentLocale . '/' . $showSlug . '/' . $slug;
                                }

                                $metadesc =
                                    $item->getTranslated('metadesc') ??
                                    ($item->getRawOriginal('metadesc') ?? ($item->metadesc ?? null));
                                $body =
                                    $item->getTranslated('body') ??
                                    ($item->getRawOriginal('body') ?? ($item->body ?? null));
                                $description = $metadesc ?? (strip_tags($body) ?? null);
                            @endphp

                            <article
                                class="group relative bg-white dark:bg-gray-800 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 dark:border-gray-700"
                                @click="navigate('{{ $dynamicUrl }}')"
                                @mouseenter="prefetch('{{ $dynamicUrl }}'); hover = {{ $loop->index }}"
                                @mouseleave="hover = null" x-data="{ localHover: false }" @mouseenter.self="localHover = true"
                                @mouseleave.self="localHover = false">

                                <!-- Gradient Overlay -->
                                <div
                                    class="absolute inset-0 bg-gradient-to-br from-blue-600/5 to-purple-600/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                </div>

                                <div class="relative p-6 cursor-pointer">

                                    <!-- Date with Icon -->
                                    <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 mb-4">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                        {{ $item->created_at->format('d M Y') }}
                                    </div>

                                    <!-- Title with hover effect -->
                                    <h2
                                        class="text-xl font-bold text-gray-900 dark:text-white mb-3 group-hover:text-transparent group-hover:bg-gradient-to-r group-hover:from-blue-600 group-hover:to-purple-600 group-hover:bg-clip-text transition-all duration-300">
                                        {{ $title }}
                                    </h2>

                                    <!-- Description -->
                                    @if ($description)
                                        <p class="text-gray-600 dark:text-gray-300 leading-relaxed mb-6 line-clamp-3">
                                            {{ Str::limit($description, 120) }}
                                        </p>
                                    @endif

                                    <!-- Read More with Alpine animation -->
                                    <div class="inline-flex items-center text-sm font-semibold text-transparent bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text group-hover:from-blue-700 group-hover:to-purple-700 transition-all duration-300"
                                        x-show="true" x-transition:enter="transition ease-out duration-200"
                                        x-transition:enter-start="opacity-75 transform translate-x-0"
                                        x-transition:enter-end="opacity-100 transform translate-x-0">
                                        {{ __('announcement::front.general.read_more') }}
                                        <svg class="h-4 w-4 ml-2 text-blue-600 group-hover:text-purple-600 transition-all duration-300"
                                            :class="localHover ? 'translate-x-1' : 'translate-x-0'" fill="currentColor"
                                            viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                    </div>

                                    <!-- Hover Border Effect -->
                                    <div
                                        class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-blue-600 to-purple-600 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300">
                                    </div>
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
                    @if ($items->hasPages())
                        <div class="mt-20">
                            <div
                                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-4">
                                {{ $items->links() }}
                            </div>
                        </div>
                    @endif
                @else
                    <!-- Empty State -->
                    <div class="text-center py-20" x-data="{ show: false }" x-init="setTimeout(() => show = true, 100)">
                        <div x-show="show" x-transition:enter="transition ease-out duration-500"
                            x-transition:enter-start="opacity-0 transform scale-90"
                            x-transition:enter-end="opacity-100 transform scale-100" class="inline-block">
                            <div
                                class="w-20 h-20 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg">
                                <svg class="h-10 w-10 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Henüz sayfa yok</h3>
                            <p class="text-gray-500 dark:text-gray-400 max-w-sm mx-auto">
                                {{ __('announcement::front.general.no_announcements_found') }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        function announcementsList() {
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

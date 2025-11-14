@php
    $themeService = app(\App\Services\ThemeService::class);
    $activeTheme = $themeService->getActiveTheme();
    $themeName = $activeTheme ? $activeTheme->name : 'simple';
@endphp
@extends('themes.' . $themeName . '.layouts.app')

@section('module_content')
    <div class="relative" x-data="blogsList()" x-init="init()">

        {{-- Glass Subheader Component --}}
        @include('themes.ixtif.layouts.partials.glass-subheader', [
            'title' => $moduleTitle ?? __('blog::front.general.blogs'),
            'icon' => 'fa-solid fa-newspaper',
            'breadcrumbs' => [
                ['label' => 'Ana Sayfa', 'url' => url('/'), 'icon' => 'fa-home'],
                ['label' => 'Blog']
            ]
        ])

        <div class="py-20">
            <div class="container mx-auto px-4 sm:px-4 md:px-2">
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
                                if (isset($slugData[$currentLocale])) {
                                    $slug = $slugData[$currentLocale];
                                } elseif (!empty($slugData)) {
                                    $slug = reset($slugData);
                                } else {
                                    $slug = 'no-slug';
                                }

                                $title = $item->getTranslated('title', $currentLocale);
                                $body = $item->getTranslated('body', $currentLocale);
                                $excerpt = $item->getTranslated('excerpt', $currentLocale) ?: \Illuminate\Support\Str::limit(strip_tags($body), 150);

                                // Module slug service kullanarak dinamik slug prefix al
                                $moduleSlugService = app(\App\Services\ModuleSlugService::class);
                                $slugPrefix = $moduleSlugService->getMultiLangSlug('Blog', 'index', $currentLocale);
                                $defaultLocale = get_tenant_default_locale();
                                $localePrefix = ($currentLocale !== $defaultLocale) ? '/' . $currentLocale : '';
                                $blogUrl = $localePrefix . '/' . $slugPrefix . '/' . $slug;

                                // Featured image'i al
                                $featuredImage = $item->getFirstMediaUrl('featured_image');
                            @endphp

                            <a href="{{ url($blogUrl) }}"
                               class="group block bg-white dark:bg-gray-800 rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-1">
                                @if ($featuredImage)
                                    <div class="relative h-48 overflow-hidden">
                                        <img src="{{ $featuredImage }}"
                                             alt="{{ $title }}"
                                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                                             loading="lazy">
                                        <div
                                            class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity">
                                        </div>
                                    </div>
                                @else
                                    <div class="h-48 bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
                                        <i class="fa-solid fa-newspaper text-6xl text-white/30"></i>
                                    </div>
                                @endif

                                <div class="p-6">
                                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors line-clamp-2">
                                        {{ $title }}
                                    </h2>
                                    <p class="text-gray-600 dark:text-gray-400 text-sm line-clamp-3 mb-4">
                                        {{ $excerpt }}
                                    </p>

                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-500 dark:text-gray-500">
                                            <i class="fa-regular fa-calendar mr-1"></i>
                                            {{ $item->created_at->format('d.m.Y') }}
                                        </span>
                                        <span class="text-blue-600 dark:text-blue-400 font-medium group-hover:translate-x-1 transition-transform">
                                            Devamını Oku →
                                        </span>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    @if($items->hasPages())
                        <div class="mt-12">
                            {{ $items->links() }}
                        </div>
                    @endif
                @else
                    {{-- No posts --}}
                    <div class="text-center py-20">
                        <i class="fa-solid fa-newspaper text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
                        <p class="text-gray-600 dark:text-gray-400 text-lg">
                            Henüz blog yazısı bulunmamaktadır.
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@props(['variant' => 'full', 'blog' => null])

@php
    // 🎯 3 KATMANLI CASCADE FALLBACK SİSTEMİ

    // 1️⃣ ÖNCE: Blog'un kendi SEO settings'i (seo_settings tablosu)
    $blogSeo = optional($blog)->seoSetting;
    $authorName = optional($blogSeo)->author;
    $authorTitle = optional($blogSeo)->author_title;
    $authorBio = optional($blogSeo)->author_bio;
    $authorImageId = optional($blogSeo)->author_image;
    $authorWebsite = optional($blogSeo)->author_url;

    // 2️⃣ SONRA: Global default author (settings Group 8)
    try {
        $authorName = $authorName ?? setting('seo_default_author');
        $authorTitle = $authorTitle ?? setting('seo_default_author_title');
        $authorBio = $authorBio ?? setting('seo_default_author_bio');
        $authorImageId = $authorImageId ?? setting('seo_default_author_image');
        $authorWebsite = $authorWebsite ?? setting('seo_default_author_url');
    } catch (\Exception $e) {
        // Settings not found, continue to fallback
    }

    // 3️⃣ EN SON: Site bilgileri (son fallback)
    try {
        $authorName = $authorName ?? setting('site_title', config('app.name'));
        $authorBio = $authorBio ?? setting('site_description', '');
        $authorImageId = $authorImageId ?? setting('site_logo');
    } catch (\Exception $e) {
        $authorName = $authorName ?? config('app.name', 'iXtif');
        $authorBio = $authorBio ?? '';
    }

    $authorTitle = $authorTitle ?? 'İçerik Yazarı';
    $authorWebsite = $authorWebsite ?? url('/');

    // 📸 Avatar image handling
    $authorImage = null;
    if ($authorImageId) {
        try {
            // authorImageId string path olabilir (direkt cdn'e verebiliriz)
            $authorImage = cdn($authorImageId);
        } catch (\Exception $e) {
            $authorImage = null;
        }
    }

    // Sosyal medya hesapları (global)
    try {
        $facebook = setting('social_facebook');
        $twitter = setting('social_twitter');
        $instagram = setting('social_instagram');
        $linkedin = setting('social_linkedin');
        $youtube = setting('social_youtube');
    } catch (\Exception $e) {
        $facebook = $twitter = $instagram = $linkedin = $youtube = null;
    }
@endphp

@if($variant === 'mini')
    {{-- Mini Author Info (Header'da kullanılacak) --}}
    <div class="flex items-center gap-3" {{ $attributes }}>
        @if($authorImage)
            <img src="{{ $authorImage }}"
                 alt="{{ $authorName }}"
                 itemprop="image"
                 class="w-10 h-10 rounded-full object-cover shadow-md">
        @else
            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-600 to-blue-400 flex items-center justify-center text-white font-bold text-lg shadow-md">
                {{ substr($authorName, 0, 1) }}
            </div>
        @endif
        <div>
            <p class="font-semibold text-gray-900 dark:text-white" itemprop="name">{{ $authorName }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400" itemprop="jobTitle">{{ $authorTitle }}</p>
        </div>
    </div>
@else
    {{-- Full Author Card - Tasarım 3: Inline Expand Accordion (Tek tasarım tüm cihazlar için) --}}
    <div class="md:bg-gradient-to-br md:from-blue-50 md:to-indigo-50 md:dark:from-gray-800 md:dark:to-gray-700 md:rounded-2xl md:p-8 md:shadow-xl md:border md:border-blue-100 md:dark:border-gray-600"
         x-data="{ open: false }"
         itemscope
         itemtype="https://schema.org/Person"
         {{ $attributes }}>

        {{-- Header (Her zaman görünür - Tek satır) --}}
        <button @click="open = !open"
                type="button"
                class="w-full flex items-center gap-4 p-4 md:p-6 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors rounded-lg">

            {{-- Avatar (Küçük) --}}
            @if($authorImage)
                <img src="{{ $authorImage }}"
                     alt="{{ $authorName }}"
                     itemprop="image"
                     class="w-10 h-10 rounded-full object-cover shadow-md flex-shrink-0">
            @else
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-600 to-indigo-600 flex items-center justify-center text-white font-bold text-lg shadow-md flex-shrink-0">
                    {{ substr($authorName, 0, 1) }}
                </div>
            @endif

            {{-- İsim + Ünvan (Inline) --}}
            <div class="flex-1 text-left min-w-0">
                <span class="text-sm font-bold text-gray-900 dark:text-white" itemprop="name">{{ $authorName }}</span>
                <span class="text-xs text-gray-500 dark:text-gray-400 ml-2">•</span>
                <span class="text-xs text-gray-600 dark:text-gray-400 ml-2" itemprop="jobTitle">{{ $authorTitle }}</span>
            </div>

            {{-- Expand Button --}}
            <div class="text-xs text-blue-600 dark:text-blue-400 font-medium flex items-center gap-1 flex-shrink-0">
                <span x-show="!open">Detay</span>
                <span x-show="open" style="display: none;">Gizle</span>
                <i class="fas fa-chevron-down transition-transform duration-300 text-xs"
                   :class="{'rotate-180': open}"></i>
            </div>
        </button>

        {{-- Expanded Content (Accordion) --}}
        <div x-show="open"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-95"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-95"
             style="display: none;"
             class="border-t border-gray-200 dark:border-gray-700 mt-3">
            <div class="p-4 space-y-3">

                {{-- Bio --}}
                @if($authorBio)
                    <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed" itemprop="description">
                        {{ $authorBio }}
                    </p>
                @endif

                {{-- Actions --}}
                <div class="flex flex-wrap items-center gap-2">
                    {{-- Website --}}
                    @if($authorWebsite && $authorWebsite !== url('/'))
                        <a href="{{ $authorWebsite }}"
                           target="_blank"
                           rel="noopener noreferrer author"
                           itemprop="url"
                           class="text-xs px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition-colors">
                            <i class="fas fa-globe mr-1"></i> Web
                        </a>
                    @endif

                    {{-- Social Media Icons --}}
                    @if($facebook)
                        <a href="{{ $facebook }}"
                           target="_blank"
                           rel="noopener noreferrer"
                           itemprop="sameAs"
                           class="w-7 h-7 flex items-center justify-center rounded-full bg-blue-600 text-white text-xs hover:scale-110 transition-transform">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                    @endif

                    @if($twitter)
                        <a href="{{ $twitter }}"
                           target="_blank"
                           rel="noopener noreferrer"
                           itemprop="sameAs"
                           class="w-7 h-7 flex items-center justify-center rounded-full bg-gray-900 text-white text-xs hover:scale-110 transition-transform">
                            <i class="fab fa-x-twitter"></i>
                        </a>
                    @endif

                    @if($instagram)
                        <a href="{{ $instagram }}"
                           target="_blank"
                           rel="noopener noreferrer"
                           itemprop="sameAs"
                           class="w-7 h-7 flex items-center justify-center rounded-full bg-gradient-to-br from-purple-600 to-pink-500 text-white text-xs hover:scale-110 transition-transform">
                            <i class="fab fa-instagram"></i>
                        </a>
                    @endif

                    @if($linkedin)
                        <a href="{{ $linkedin }}"
                           target="_blank"
                           rel="noopener noreferrer"
                           itemprop="sameAs"
                           class="w-7 h-7 flex items-center justify-center rounded-full bg-blue-700 text-white text-xs hover:scale-110 transition-transform">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    @endif

                    @if($youtube)
                        <a href="{{ $youtube }}"
                           target="_blank"
                           rel="noopener noreferrer"
                           itemprop="sameAs"
                           class="w-7 h-7 flex items-center justify-center rounded-full bg-red-600 text-white text-xs hover:scale-110 transition-transform">
                            <i class="fab fa-youtube"></i>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endif

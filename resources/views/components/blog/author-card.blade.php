@props(['variant' => 'full'])

@php
    // Site bilgilerini al
    $siteName = setting('site_title') ?? config('app.name');
    $siteDescription = setting('site_description') ?? 'Blog yazarı';
    $siteLogo = setting('site_logo');
    $siteEmail = setting('site_email');
    $sitePhone = setting('site_phone');

    // Sosyal medya hesapları
    $facebook = setting('social_facebook');
    $twitter = setting('social_twitter');
    $instagram = setting('social_instagram');
    $linkedin = setting('social_linkedin');
    $youtube = setting('social_youtube');
@endphp

@if($variant === 'mini')
    {{-- Mini Author Info (Header'da kullanılacak) --}}
    <div class="flex items-center gap-3" {{ $attributes }}>
        @if($siteLogo)
            <img src="{{ cdn($siteLogo) }}"
                 alt="{{ $siteName }}"
                 class="w-10 h-10 rounded-full object-cover shadow-md">
        @else
            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-600 to-blue-400 flex items-center justify-center text-white font-bold text-lg shadow-md">
                {{ substr($siteName, 0, 1) }}
            </div>
        @endif
        <div>
            <p class="font-semibold text-gray-900 dark:text-white">{{ $siteName }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400">Yazar</p>
        </div>
    </div>
@else
    {{-- Full Author Card (İçerik sonunda) --}}
    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-gray-800 dark:to-gray-700 rounded-2xl p-6 md:p-8 shadow-xl border border-blue-100 dark:border-gray-600" {{ $attributes }}>
        <div class="flex flex-col md:flex-row gap-6 items-start">
            {{-- Avatar --}}
            <div class="flex-shrink-0">
                @if($siteLogo)
                    <img src="{{ cdn($siteLogo) }}"
                         alt="{{ $siteName }}"
                         class="w-24 h-24 md:w-32 md:h-32 rounded-full object-cover shadow-lg border-4 border-white dark:border-gray-600">
                @else
                    <div class="w-24 h-24 md:w-32 md:h-32 rounded-full bg-gradient-to-br from-blue-600 to-indigo-600 flex items-center justify-center text-white font-bold text-4xl shadow-lg border-4 border-white dark:border-gray-600">
                        {{ substr($siteName, 0, 1) }}
                    </div>
                @endif
            </div>

            {{-- Info --}}
            <div class="flex-1">
                <h3 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mb-2">
                    {{ $siteName }}
                </h3>

                @if($siteDescription)
                    <p class="text-gray-700 dark:text-gray-300 leading-relaxed mb-4">
                        {{ $siteDescription }}
                    </p>
                @endif

                {{-- Contact Info --}}
                <div class="flex flex-wrap items-center gap-4 mb-4">
                    @if($siteEmail)
                        <a href="mailto:{{ $siteEmail }}"
                           class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                            <i class="fas fa-envelope"></i>
                            <span>{{ $siteEmail }}</span>
                        </a>
                    @endif

                    @if($sitePhone)
                        <a href="tel:{{ $sitePhone }}"
                           class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                            <i class="fas fa-phone"></i>
                            <span>{{ $sitePhone }}</span>
                        </a>
                    @endif
                </div>

                {{-- Social Links --}}
                @if($facebook || $twitter || $instagram || $linkedin || $youtube)
                    <div class="flex items-center gap-3">
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Takip Edin:</span>
                        <div class="flex items-center gap-2">
                            @if($facebook)
                                <a href="{{ $facebook }}"
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   class="flex items-center justify-center w-9 h-9 rounded-full bg-blue-600 hover:bg-blue-700 text-white transition-all duration-300 hover:scale-110 shadow-md">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                            @endif

                            @if($twitter)
                                <a href="{{ $twitter }}"
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   class="flex items-center justify-center w-9 h-9 rounded-full bg-gray-900 hover:bg-black dark:bg-gray-700 dark:hover:bg-gray-600 text-white transition-all duration-300 hover:scale-110 shadow-md">
                                    <i class="fab fa-x-twitter"></i>
                                </a>
                            @endif

                            @if($instagram)
                                <a href="{{ $instagram }}"
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   class="flex items-center justify-center w-9 h-9 rounded-full bg-gradient-to-br from-purple-600 to-pink-500 hover:from-purple-700 hover:to-pink-600 text-white transition-all duration-300 hover:scale-110 shadow-md">
                                    <i class="fab fa-instagram"></i>
                                </a>
                            @endif

                            @if($linkedin)
                                <a href="{{ $linkedin }}"
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   class="flex items-center justify-center w-9 h-9 rounded-full bg-blue-700 hover:bg-blue-800 text-white transition-all duration-300 hover:scale-110 shadow-md">
                                    <i class="fab fa-linkedin-in"></i>
                                </a>
                            @endif

                            @if($youtube)
                                <a href="{{ $youtube }}"
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   class="flex items-center justify-center w-9 h-9 rounded-full bg-red-600 hover:bg-red-700 text-white transition-all duration-300 hover:scale-110 shadow-md">
                                    <i class="fab fa-youtube"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endif

{{--
    Hero Widget - İxtif Ana Banner
    Sadece settings kullanır (has_items = 0)
    3 sabit özellik, döngü yok
--}}

<section class="py-4 md:py-6 lg:py-8 flex items-center relative overflow-hidden">
    <div class="container mx-auto px-4 sm:px-4 md:px-0 relative z-10">
        <div class="grid lg:grid-cols-2 gap-16 lg:gap-20 items-center">
            <!-- Left Content -->
            <div class="text-gray-900 dark:text-white">
                <!-- Main Title with Animation -->
                <h1 class="text-5xl md:text-6xl lg:text-7xl font-black mb-6 leading-[1.2] overflow-visible" style="font-weight: 900;">
                    @if(!empty($settings['title_line1']))
                        <span class="gradient-animate block py-2">
                            {{ $settings['title_line1'] }}
                        </span>
                    @endif

                    @if(!empty($settings['title_line2']))
                        <span class="gradient-animate block py-2">
                            {{ $settings['title_line2'] }}
                        </span>
                    @endif
                </h1>

                <!-- Description -->
                @if(!empty($settings['description']))
                    <p class="text-xl md:text-2xl text-gray-700 dark:text-gray-200 mb-14 leading-relaxed font-medium">
                        {{ $settings['description'] }}
                    </p>
                @endif

                <!-- CTA Button -->
                @if(!empty($settings['cta_text']) && !empty($settings['cta_url']))
                    <div class="mb-16">
                        <a href="{{ $settings['cta_url'] }}" class="group bg-blue-600 hover:bg-blue-700 text-white px-10 py-4 rounded-full font-bold text-lg transition-all inline-block text-center shadow-lg hover:shadow-xl">
                            @if(!empty($settings['cta_icon']))
                                <i class="{{ $settings['cta_icon'] }} mr-2 inline-block group-hover:scale-125 group-hover:rotate-12 transition-all duration-300"></i>
                            @endif
                            {{ $settings['cta_text'] }}
                        </a>
                    </div>
                @endif

                <!-- Features (3 sabit özellik - settings'ten) -->
                <div class="grid grid-cols-2 xl:grid-cols-3 gap-6">
                    {{-- Özellik 1 --}}
                    @if(!empty($settings['feature_1_title']))
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-blue-50 dark:bg-slate-700/50 rounded-full flex items-center justify-center flex-shrink-0">
                                @if(!empty($settings['feature_1_icon']))
                                    <i class="{{ $settings['feature_1_icon'] }} text-blue-600 dark:text-blue-300 text-xl"></i>
                                @endif
                            </div>
                            <div>
                                <div class="font-bold text-gray-900 dark:text-white text-base">
                                    {{ $settings['feature_1_title'] }}
                                </div>
                                @if(!empty($settings['feature_1_subtitle']))
                                    <div class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ $settings['feature_1_subtitle'] }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    {{-- Özellik 2 --}}
                    @if(!empty($settings['feature_2_title']))
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-blue-50 dark:bg-slate-700/50 rounded-full flex items-center justify-center flex-shrink-0">
                                @if(!empty($settings['feature_2_icon']))
                                    <i class="{{ $settings['feature_2_icon'] }} text-blue-600 dark:text-blue-300 text-xl"></i>
                                @endif
                            </div>
                            <div>
                                <div class="font-bold text-gray-900 dark:text-white text-base">
                                    {{ $settings['feature_2_title'] }}
                                </div>
                                @if(!empty($settings['feature_2_subtitle']))
                                    <div class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ $settings['feature_2_subtitle'] }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    {{-- Özellik 3 --}}
                    @if(!empty($settings['feature_3_title']))
                        <div class="flex items-center gap-4 whitespace-nowrap">
                            <div class="w-12 h-12 bg-blue-50 dark:bg-slate-700/50 rounded-full flex items-center justify-center flex-shrink-0">
                                @if(!empty($settings['feature_3_icon']))
                                    <i class="{{ $settings['feature_3_icon'] }} text-blue-600 dark:text-blue-300 text-xl"></i>
                                @endif
                            </div>
                            <div>
                                <div class="font-bold text-gray-900 dark:text-white text-base whitespace-nowrap">
                                    {{ $settings['feature_3_title'] }}
                                </div>
                                @if(!empty($settings['feature_3_subtitle']))
                                    <div class="text-sm text-gray-600 dark:text-gray-400 whitespace-nowrap">
                                        {{ $settings['feature_3_subtitle'] }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Right Content - Hero Image -->
            <div class="flex items-center justify-center">
                @if(!empty($settings['cta_url']) && !empty($settings['hero_image']))
                    <a href="{{ $settings['cta_url'] }}" class="block cursor-pointer">
                        <img src="{{ asset($settings['hero_image']) }}"
                             alt="{{ $settings['hero_image_alt'] ?? 'Hero Image' }}"
                             class="w-full h-auto object-contain"
                             loading="lazy">
                    </a>
                @elseif(!empty($settings['hero_image']))
                    <img src="{{ asset($settings['hero_image']) }}"
                         alt="{{ $settings['hero_image_alt'] ?? 'Hero Image' }}"
                         class="w-full h-auto object-contain"
                         loading="lazy">
                @endif
            </div>
        </div>
    </div>
</section>

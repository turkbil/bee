@php
    $themeService = app(\App\Services\ThemeService::class);
    $activeTheme = $themeService->getActiveTheme();
    $themeName = $activeTheme ? $activeTheme->name : 'simple';
@endphp
@extends('themes.' . $themeName . '.layouts.app')

@section('module_content')
    @php
        $currentLocale = app()->getLocale();
        $title = $item->getTranslated('title', $currentLocale);
        $body = $item->getTranslated('body', $currentLocale);
        $excerpt = $item->getTranslated('excerpt', $currentLocale) ?: \Illuminate\Support\Str::limit(strip_tags($body ?? ''), 160);

        $moduleSlugService = app(\App\Services\ModuleSlugService::class);
        $indexSlug = $moduleSlugService->getMultiLangSlug('Service', 'index', $currentLocale);
        $defaultLocale = get_tenant_default_locale();
        $localePrefix = $currentLocale !== $defaultLocale ? '/' . $currentLocale : '';
        $serviceIndexUrl = $localePrefix . '/' . $indexSlug;

        // Media
        $heroImage = $item->getFirstMedia('hero');
        $featuredImage = $item->getFirstMedia('featured_image') ?: $heroImage;
        $galleryImages = $item->getMedia('gallery') ?? collect();

        // Contact info
        $sitePhone = setting('contact_phone_1');
        $whatsappUrl = function_exists('whatsapp_link') ? whatsapp_link() : null;

        // Related services (exclude current)
        $relatedServices = \Modules\Service\App\Models\Service::where('is_active', true)
            ->where('service_id', '!=', $item->service_id)
            ->orderBy('service_id')
            ->take(6)
            ->get();
        $relatedCount = $relatedServices->count();

        // Grid class based on count
        $gridClass = match(true) {
            $relatedCount === 1 => 'grid-cols-1 max-w-md mx-auto',
            $relatedCount === 2 => 'grid-cols-1 sm:grid-cols-2 max-w-2xl mx-auto',
            $relatedCount === 3 => 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3',
            default => 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4',
        };

        // Breadcrumbs
        $breadcrumbsArray = [
            ['label' => 'Ana Sayfa', 'url' => url('/'), 'icon' => 'fa-home'],
            ['label' => 'Hizmetlerimiz', 'url' => url($serviceIndexUrl)],
            ['label' => $title]
        ];
    @endphp

    {{-- MINIMAL SUBHEADER --}}
    <section class="bg-gray-100 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
        <div class="container mx-auto py-4">
            <nav class="text-sm text-gray-500 dark:text-gray-400 mb-2 overflow-x-auto whitespace-nowrap scrollbar-hide">
                @foreach($breadcrumbsArray as $index => $crumb)
                    @if(isset($crumb['url']))
                        <a href="{{ $crumb['url'] }}" class="hover:text-primary-600 dark:hover:text-primary-400 transition">{{ $crumb['label'] }}</a>
                        @if($index < count($breadcrumbsArray) - 1)
                            <span class="mx-2">/</span>
                        @endif
                    @else
                        <span class="text-gray-900 dark:text-white font-medium">{{ $crumb['label'] }}</span>
                    @endif
                @endforeach
            </nav>
            <h1 class="text-2xl md:text-3xl font-bold font-heading text-gray-900 dark:text-white">{{ $title }}</h1>
        </div>
    </section>

    {{-- CONTENT SECTION --}}
    <section class="bg-white dark:bg-gray-900 py-10 md:py-16">
        <div class="container mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-8 lg:gap-12">
                {{-- Main Content - Sol taraf --}}
                <article class="{{ $featuredImage ? 'lg:col-span-3' : 'lg:col-span-5 max-w-4xl mx-auto' }}">
                    {{-- Body Content --}}
                    <div class="prose prose-base max-w-none dark:prose-invert font-body
                              prose-headings:font-heading prose-headings:text-gray-900 dark:prose-headings:text-white
                              prose-p:text-gray-600 dark:prose-p:text-gray-300
                              prose-a:text-primary-600 dark:prose-a:text-primary-400 hover:prose-a:underline
                              prose-strong:text-gray-900 dark:prose-strong:text-white
                              prose-ul:text-gray-600 dark:prose-ul:text-gray-300
                              prose-ol:text-gray-600 dark:prose-ol:text-gray-300
                              prose-blockquote:border-l-primary-500
                              prose-img:rounded-xl prose-img:shadow-lg">
                        @parsewidgets($body ?? '')
                    </div>

                    {{-- Inline CTA for mobile (no sidebar) --}}
                    @if(!$featuredImage && ($sitePhone || $whatsappUrl))
                        <div class="mt-10 flex flex-wrap gap-4">
                            @if($sitePhone)
                                <a href="tel:{{ preg_replace('/[^0-9+]/', '', $sitePhone) }}"
                                   class="inline-flex items-center gap-3 px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all">
                                    <i class="fa-solid fa-phone"></i>
                                    <span>{{ $sitePhone }}</span>
                                </a>
                            @endif
                            @if($whatsappUrl)
                                <a href="{{ $whatsappUrl }}" target="_blank"
                                   class="inline-flex items-center gap-3 px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all">
                                    <i class="fa-brands fa-whatsapp"></i>
                                    <span>WhatsApp</span>
                                </a>
                            @endif
                        </div>
                    @endif
                </article>

                {{-- Sidebar - Sağ taraf (Fotoğraf + İletişim) --}}
                @if($featuredImage)
                    <aside class="lg:col-span-2">
                        <div class="sticky top-24 space-y-6">
                            {{-- Featured Image - Kare ve Büyük --}}
                            <figure class="rounded-2xl overflow-hidden shadow-2xl">
                                <a href="{{ $featuredImage->getUrl() }}" class="glightbox block" data-gallery="service-main">
                                    <img src="{{ $featuredImage->hasGeneratedConversion('medium') ? $featuredImage->getUrl('medium') : $featuredImage->getUrl() }}"
                                         alt="{{ $title }}"
                                         class="w-full aspect-square object-cover hover:scale-105 transition-transform duration-500">
                                </a>
                            </figure>

                            {{-- Quick Contact Card - Dinamik Grid --}}
                            @php
                                $contactEmail = setting('contact_email_1');
                                $contactItems = collect();
                                if($sitePhone) $contactItems->push('phone');
                                if($whatsappUrl) $contactItems->push('whatsapp');
                                if($contactEmail && strlen($contactEmail) <= 25) $contactItems->push('email');
                                $contactCount = $contactItems->count();
                                $contactGridClass = match($contactCount) {
                                    1 => 'grid-cols-1',
                                    2 => 'grid-cols-2',
                                    3 => 'grid-cols-3',
                                    default => 'grid-cols-1'
                                };
                            @endphp
                            @if($contactCount > 0)
                                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-4">
                                    <div class="grid {{ $contactGridClass }} gap-2">
                                        @if($sitePhone)
                                            <a href="tel:{{ preg_replace('/[^0-9+]/', '', $sitePhone) }}"
                                               class="flex items-center gap-3 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg p-3 transition-all">
                                                <i class="fa-solid fa-phone text-primary-600 dark:text-primary-400"></i>
                                                <div class="min-w-0">
                                                    <div class="text-xs text-gray-500 dark:text-gray-400">Telefon</div>
                                                    <div class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ $sitePhone }}</div>
                                                </div>
                                            </a>
                                        @endif
                                        @if($whatsappUrl)
                                            <a href="{{ $whatsappUrl }}" target="_blank"
                                               class="flex items-center gap-3 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg p-3 transition-all">
                                                <i class="fa-brands fa-whatsapp text-green-600 dark:text-green-400 text-lg"></i>
                                                <div class="min-w-0">
                                                    <div class="text-xs text-gray-500 dark:text-gray-400">WhatsApp</div>
                                                    <div class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ setting('contact_whatsapp_1') ?: setting('contact_phone_2') }}</div>
                                                </div>
                                            </a>
                                        @endif
                                        @if($contactEmail && strlen($contactEmail) <= 25)
                                            <a href="mailto:{{ $contactEmail }}"
                                               class="flex items-center gap-3 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg p-3 transition-all">
                                                <i class="fa-solid fa-envelope text-red-600 dark:text-red-400"></i>
                                                <div class="min-w-0">
                                                    <div class="text-xs text-gray-500 dark:text-gray-400">E-posta</div>
                                                    <div class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ $contactEmail }}</div>
                                                </div>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </aside>
                @endif
            </div>
        </div>
    </section>

    {{-- GALLERY SECTION --}}
    @if($galleryImages->count() > 0)
        <section class="bg-gray-50 dark:bg-gray-800 py-12 md:py-20">
            <div class="container mx-auto">
                <div class="text-center mb-8">
                    <h2 class="text-xl md:text-2xl font-bold font-heading text-gray-900 dark:text-white mb-3">Galeri</h2>
                    <div class="w-16 h-1 bg-gradient-to-r from-primary-500 to-primary-600 mx-auto rounded-full"></div>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
                    @foreach($galleryImages as $image)
                        <a href="{{ $image->getUrl() }}"
                           class="glightbox group relative aspect-square rounded-xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300"
                           data-gallery="service-gallery">
                            <img src="{{ $image->getUrl('thumb') }}"
                                 alt="{{ $image->getCustomProperty('alt_text')[$currentLocale] ?? $title }}"
                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition-colors duration-300 flex items-center justify-center">
                                <i class="fa-solid fa-expand text-white text-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></i>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- RELATED SERVICES SECTION --}}
    @if($relatedServices->isNotEmpty())
        <section class="bg-white dark:bg-gray-900 py-12 md:py-20">
            <div class="container mx-auto">
                <div class="text-center mb-8">
                    <h2 class="text-xl md:text-2xl font-bold font-heading text-gray-900 dark:text-white mb-2">Diğer Hizmetlerimiz</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 font-body">Sunduğumuz diğer profesyonel hizmetleri inceleyin</p>
                </div>

                <div class="grid {{ $gridClass }} gap-4 md:gap-6">
                    @foreach($relatedServices as $service)
                        @php
                            $serviceTitle = $service->getTranslated('title', $currentLocale);
                            $serviceImage = $service->getFirstMedia('hero') ?: $service->getFirstMedia('featured_image');

                            // Slug hesapla
                            $serviceSlugData = $service->getRawOriginal('slug');
                            if (is_string($serviceSlugData)) {
                                $serviceSlugData = json_decode($serviceSlugData, true) ?: [];
                            }
                            $serviceSlug = is_array($serviceSlugData) && isset($serviceSlugData[$currentLocale])
                                ? $serviceSlugData[$currentLocale]
                                : (is_array($serviceSlugData) ? ($serviceSlugData['tr'] ?? reset($serviceSlugData)) : $serviceSlugData);
                            $serviceSlug = $serviceSlug ?: $service->service_id;

                            $showSlug = \App\Services\ModuleSlugService::getSlug('Service', 'show');
                            $serviceUrl = $localePrefix . '/' . $showSlug . '/' . $serviceSlug;
                        @endphp
                        <a href="{{ url($serviceUrl) }}"
                           class="group relative aspect-square rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition-all duration-300">
                            @if($serviceImage)
                                <img src="{{ $serviceImage->hasGeneratedConversion('medium') ? $serviceImage->getUrl('medium') : $serviceImage->getUrl() }}"
                                     alt="{{ $serviceTitle }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-primary-500 to-primary-600 flex items-center justify-center">
                                    <i class="fa-solid fa-cog text-white/20 text-5xl"></i>
                                </div>
                            @endif
                            <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/50 to-transparent"></div>
                            <div class="absolute bottom-0 left-0 right-0 p-4 md:p-6">
                                <h3 class="text-xl md:text-2xl font-bold text-white font-heading drop-shadow-lg">{{ $serviceTitle }}</h3>
                            </div>
                        </a>
                    @endforeach
                </div>

                {{-- View All Button --}}
                <div class="text-center mt-12">
                    <a href="{{ url($serviceIndexUrl) }}"
                       class="inline-flex items-center gap-3 px-8 py-4 bg-gray-900 dark:bg-white hover:bg-gray-800 dark:hover:bg-gray-100 text-white dark:text-gray-900 font-bold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300">
                        <span>Tüm Hizmetleri Gör</span>
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </section>
    @endif

    {{-- Custom JS/CSS --}}
    @if(isset($item->js))
        <script>{!! $item->js !!}</script>
    @endif
    @if(isset($item->css))
        <style>{!! $item->css !!}</style>
    @endif

@endsection

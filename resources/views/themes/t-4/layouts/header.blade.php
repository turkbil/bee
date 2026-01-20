{{-- t-4 Unimad Madencilik Theme - Header (v11 Dynamic Logo) --}}
@php
    use Modules\Service\App\Models\ServiceCategory;

    // Logo Service
    $logoService = app(\App\Services\LogoService::class);
    $logos = $logoService->getLogos();
    $hasLogo = $logos['has_light'] || $logos['has_dark'];
    $siteName = setting('site_title') ?: 'UNIMAD';
    $siteSlogan = setting('site_slogan') ?: 'Madencilik & Mühendislik';

    // Tüm kategorileri al
    $madencilik = ServiceCategory::with(['services' => fn($q) => $q->where('is_active', true)->orderBy('service_id')])
        ->where('is_active', true)->where('slug->tr', 'madencilik')->first();

    $ytk = ServiceCategory::with(['services' => fn($q) => $q->where('is_active', true)->orderBy('service_id')])
        ->where('is_active', true)->where('slug->tr', 'ytk')->first();

    $muhendislikKategoriler = ServiceCategory::with(['services' => fn($q) => $q->where('is_active', true)->orderBy('service_id')])
        ->where('is_active', true)->whereIn('slug->tr', ['jeoloji', 'hidrojeoloji', 'jeoteknik'])->orderBy('category_id')->get();

    $mimarlik = ServiceCategory::with(['services' => fn($q) => $q->where('is_active', true)->orderBy('service_id')])
        ->where('is_active', true)->where('slug->tr', 'mimarlik')->first();

    // Dinamik URL helpers (ModuleSlugService kullanarak)
    $moduleSlugService = app(\App\Services\ModuleSlugService::class);
    $currentLocale = app()->getLocale();
    $serviceIndexSlug = $moduleSlugService->getMultiLangSlug('Service', 'index', $currentLocale);
    $serviceShowSlug = \App\Services\ModuleSlugService::getSlug('Service', 'show');
    $serviceCategorySlug = \App\Services\ModuleSlugService::getSlug('Service', 'category');
    $pageShowSlug = \App\Services\ModuleSlugService::getSlug('Page', 'show');
    $defaultLocale = get_tenant_default_locale();
    $localePrefix = $currentLocale !== $defaultLocale ? '/' . $currentLocale : '';

    $serviceUrl = fn($service) => url($localePrefix . '/' . $serviceShowSlug . '/' . ($service->slug['tr'] ?? $service->slug));
    // Kategori URL - route-based: /service/kategori/{slug}
    $categoryUrl = fn($category) => url($localePrefix . '/' . $serviceIndexSlug . '/' . $serviceCategorySlug . '/' . ($category->slug['tr'] ?? $category->slug));

    $homeUrl = url('/');
    $hakkimizdaUrl = url('/' . $pageShowSlug . '/hakkimizda');
    $iletisimUrl = url('/' . $pageShowSlug . '/iletisim');

    // İkonlar
    $icons = [
        'madencilik' => 'fa-pickaxe',
        'ytk' => 'fa-file-certificate',
        'jeoloji' => 'fa-mountain-sun',
        'hidrojeoloji' => 'fa-droplet',
        'jeoteknik' => 'fa-layer-group',
        'mimarlik' => 'fa-drafting-compass',
    ];

    // WhatsApp (Conditional)
    $siteWhatsapp = setting('contact_whatsapp_1');
    $whatsappUrl = $siteWhatsapp && function_exists('whatsapp_link') ? whatsapp_link() : null;

    // Helper: Kategori için görsel ve servis görselleri
    $getCategoryData = function($category) {
        if (!$category) return ['default' => '', 'services' => []];

        $catImage = $category->getFirstMedia('category_image');
        $defaultImage = $catImage ? $catImage->getUrl() : '';

        $servicesData = [];
        foreach ($category->services as $service) {
            $heroImage = $service->getFirstMedia('hero');
            $servicesData[$service->service_id] = $heroImage ? $heroImage->getUrl() : $defaultImage;
        }

        return ['default' => $defaultImage, 'services' => $servicesData];
    };

    $madencilikData = $getCategoryData($madencilik);
    $ytkData = $getCategoryData($ytk);
    $mimarlikData = $getCategoryData($mimarlik);

    // Mühendislik alt kategorileri için de data hazırla
    $muhendislikData = ['default' => '', 'services' => [], 'categories' => []];
    foreach ($muhendislikKategoriler as $kat) {
        $katData = $getCategoryData($kat);
        // array_merge yerine + operatörü kullan (key'leri korur)
        $muhendislikData['services'] = $muhendislikData['services'] + $katData['services'];
        $muhendislikData['categories'][$kat->category_id] = $katData['default'];
        if (empty($muhendislikData['default']) && !empty($katData['default'])) {
            $muhendislikData['default'] = $katData['default'];
        }
    }
@endphp

<header x-data="{
    activeMenu: null,
    mobileMenu: false,
    timeout: null,
    hoveredService: null,
    hoveredCategory: null,
    madencilikImages: {{ json_encode($madencilikData) }},
    ytkImages: {{ json_encode($ytkData) }},
    mimarlikImages: {{ json_encode($mimarlikData) }},
    muhendislikImages: {{ json_encode($muhendislikData) }},
    open(menu) {
        clearTimeout(this.timeout);
        this.activeMenu = menu;
        this.hoveredService = null;
        this.hoveredCategory = null;
    },
    close() {
        this.timeout = setTimeout(() => {
            this.activeMenu = null;
            this.hoveredService = null;
            this.hoveredCategory = null;
        }, 150);
    },
    keep() {
        clearTimeout(this.timeout);
    },
    getImage(category) {
        const data = this[category + 'Images'];
        if (!data) return '';
        if (this.hoveredService && data.services[this.hoveredService]) {
            return data.services[this.hoveredService];
        }
        if (this.hoveredCategory && data.categories && data.categories[this.hoveredCategory]) {
            return data.categories[this.hoveredCategory];
        }
        return data.default;
    }
}"
        @keydown.escape.window="activeMenu = null"
        class="fixed top-0 left-0 right-0 z-50 bg-white dark:bg-dark-950 shadow-sm">

    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between h-[72px]">

            <!-- Logo -->
            <a href="{{ $homeUrl }}" class="flex items-center gap-3">
                @if($hasLogo)
                    @if($logos['has_light'] && $logos['has_dark'])
                        <img src="{{ $logos['light_logo_url'] }}" alt="{{ $siteName }}" class="dark:hidden h-10 lg:h-12 w-auto">
                        <img src="{{ $logos['dark_logo_url'] }}" alt="{{ $siteName }}" class="hidden dark:block h-10 lg:h-12 w-auto">
                    @elseif($logos['has_light'])
                        <img src="{{ $logos['light_logo_url'] }}" alt="{{ $siteName }}" class="h-10 lg:h-12 w-auto">
                    @else
                        <img src="{{ $logos['dark_logo_url'] }}" alt="{{ $siteName }}" class="h-10 lg:h-12 w-auto">
                    @endif
                @else
                    {{-- Fallback: İkon + Metin --}}
                    <div class="w-10 h-10 bg-primary-600 rounded-lg flex items-center justify-center">
                        <i class="fal fa-mountain text-white"></i>
                    </div>
                    <div class="hidden sm:block">
                        <span class="font-heading text-lg font-bold text-slate-900 dark:text-white">{{ $siteName }}</span>
                        <span class="block text-[10px] text-slate-500 dark:text-slate-400 -mt-0.5 tracking-wide">{{ strtoupper($siteSlogan) }}</span>
                    </div>
                @endif
            </a>

            <!-- Desktop Nav -->
            <nav class="hidden xl:flex items-center h-full">
                @if($madencilik && $madencilik->services->count() > 0)
                <div class="h-full"
                     @mouseenter="open('madencilik')"
                     @mouseleave="close()">
                    <button class="h-full flex items-center gap-1 px-4 text-sm font-medium transition-all"
                            :class="activeMenu === 'madencilik' ? 'text-primary-600 dark:text-primary-400 border-b-2 border-primary-500' : 'text-slate-600 dark:text-slate-300 border-b-2 border-transparent hover:text-primary-600'">
                        Madencilik
                        <i class="fas fa-chevron-down text-[9px] ml-1 transition-transform" :class="activeMenu === 'madencilik' && 'rotate-180'"></i>
                    </button>
                </div>
                @endif

                @if($ytk && $ytk->services->count() > 0)
                <div class="h-full"
                     @mouseenter="open('ytk')"
                     @mouseleave="close()">
                    <button class="h-full flex items-center gap-1 px-4 text-sm font-medium transition-all"
                            :class="activeMenu === 'ytk' ? 'text-primary-600 dark:text-primary-400 border-b-2 border-primary-500' : 'text-slate-600 dark:text-slate-300 border-b-2 border-transparent hover:text-primary-600'">
                        YTK
                        <i class="fas fa-chevron-down text-[9px] ml-1 transition-transform" :class="activeMenu === 'ytk' && 'rotate-180'"></i>
                    </button>
                </div>
                @endif

                @if($muhendislikKategoriler->count() > 0)
                <div class="h-full"
                     @mouseenter="open('muhendislik')"
                     @mouseleave="close()">
                    <button class="h-full flex items-center gap-1 px-4 text-sm font-medium transition-all"
                            :class="activeMenu === 'muhendislik' ? 'text-primary-600 dark:text-primary-400 border-b-2 border-primary-500' : 'text-slate-600 dark:text-slate-300 border-b-2 border-transparent hover:text-primary-600'">
                        Mühendislik
                        <i class="fas fa-chevron-down text-[9px] ml-1 transition-transform" :class="activeMenu === 'muhendislik' && 'rotate-180'"></i>
                    </button>
                </div>
                @endif

                @if($mimarlik && $mimarlik->services->count() > 0)
                <div class="h-full"
                     @mouseenter="open('mimarlik')"
                     @mouseleave="close()">
                    <button class="h-full flex items-center gap-1 px-4 text-sm font-medium transition-all"
                            :class="activeMenu === 'mimarlik' ? 'text-primary-600 dark:text-primary-400 border-b-2 border-primary-500' : 'text-slate-600 dark:text-slate-300 border-b-2 border-transparent hover:text-primary-600'">
                        Mimarlık
                        <i class="fas fa-chevron-down text-[9px] ml-1 transition-transform" :class="activeMenu === 'mimarlik' && 'rotate-180'"></i>
                    </button>
                </div>
                @endif

                <a href="{{ $hakkimizdaUrl }}" class="h-full flex items-center px-4 text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-primary-600 dark:hover:text-primary-400 border-b-2 border-transparent hover:border-primary-500 transition-all">
                    Hakkımızda
                </a>

                <a href="{{ $iletisimUrl }}" class="h-full flex items-center px-4 text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-primary-600 dark:hover:text-primary-400 border-b-2 border-transparent hover:border-primary-500 transition-all">
                    İletişim
                </a>
            </nav>

            <!-- Right -->
            <div class="flex items-center gap-2">
                <button @click="darkMode = !darkMode" class="w-9 h-9 rounded-lg bg-slate-100 dark:bg-dark-800 flex items-center justify-center text-slate-500 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-dark-700">
                    <i class="fal fa-sun text-sm" x-show="darkMode" x-cloak></i>
                    <i class="fal fa-moon text-sm" x-show="!darkMode"></i>
                </button>
                <a href="{{ $iletisimUrl }}" class="hidden md:flex items-center gap-2 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <i class="fal fa-phone text-xs"></i>
                    Teklif Al
                </a>
                <button @click="mobileMenu = !mobileMenu" class="xl:hidden w-9 h-9 rounded-lg bg-slate-100 dark:bg-dark-800 flex items-center justify-center">
                    <i class="fal fa-bars text-slate-600 dark:text-slate-300" x-show="!mobileMenu"></i>
                    <i class="fal fa-xmark text-slate-600 dark:text-slate-300" x-show="mobileMenu" x-cloak></i>
                </button>
            </div>
        </div>
    </div>

    <!-- ========== MEGA MENUS ========== -->
    <div class="hidden xl:block absolute top-full left-0 right-0 bg-white dark:bg-dark-900 border-t border-slate-100 dark:border-dark-800 shadow-xl"
         x-show="activeMenu"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         @mouseenter="keep()"
         @mouseleave="close()"
         x-cloak>

        @if($madencilik)
        <!-- Madencilik Mega -->
        <div x-show="activeMenu === 'madencilik'" class="container mx-auto px-4 py-8">
            <div class="flex gap-8">
                <!-- Sol: Dinamik Görsel -->
                <div class="w-80 shrink-0">
                    <div class="aspect-[4/3] rounded-2xl overflow-hidden mb-4 bg-slate-100 dark:bg-dark-800 relative">
                        <img :src="getImage('madencilik')"
                             alt="Madencilik"
                             class="w-full h-full object-cover transition-all duration-500"
                             x-show="getImage('madencilik')"
                             :class="hoveredService ? 'scale-105' : 'scale-100'">
                        <div class="absolute inset-0 flex items-center justify-center" x-show="!getImage('madencilik')">
                            <i class="fal fa-pickaxe text-5xl text-slate-300 dark:text-dark-600"></i>
                        </div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                        <div class="absolute bottom-0 left-0 right-0 p-4">
                            <h3 class="font-heading text-xl font-bold text-white mb-1">Madencilik Hizmetleri</h3>
                            <p class="text-sm text-white/80">Profesyonel madencilik çözümleri</p>
                        </div>
                    </div>
                    <a href="{{ $categoryUrl($madencilik) }}" class="inline-flex items-center gap-2 text-sm font-medium text-primary-600 dark:text-primary-400 hover:text-primary-700 group">
                        Tüm Hizmetleri Gör <i class="fal fa-arrow-right text-xs transition-transform group-hover:translate-x-1"></i>
                    </a>
                </div>
                <!-- Sağ: Hizmetler -->
                <div class="flex-1 grid grid-cols-2 gap-x-6 gap-y-1">
                    @foreach($madencilik->services as $service)
                    <a href="{{ $serviceUrl($service) }}"
                       @mouseenter="hoveredService = {{ $service->service_id }}"
                       @mouseleave="hoveredService = null"
                       class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-primary-50 dark:hover:bg-primary-900/20 group transition-all duration-200">
                        <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-dark-800 group-hover:bg-primary-100 dark:group-hover:bg-primary-900/40 flex items-center justify-center shrink-0 transition-colors">
                            <i class="fal fa-pickaxe text-sm text-slate-500 dark:text-slate-400 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors"></i>
                        </div>
                        <span class="text-sm font-medium text-slate-700 dark:text-slate-200 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">{{ $service->title['tr'] ?? $service->title }}</span>
                        <i class="fal fa-chevron-right text-xs text-slate-300 dark:text-dark-600 group-hover:text-primary-500 ml-auto opacity-0 group-hover:opacity-100 transition-all"></i>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        @if($ytk)
        <!-- YTK Mega -->
        <div x-show="activeMenu === 'ytk'" class="container mx-auto px-4 py-8">
            <div class="flex gap-8">
                <div class="w-80 shrink-0">
                    <div class="aspect-[4/3] rounded-2xl overflow-hidden mb-4 bg-slate-100 dark:bg-dark-800 relative">
                        <img :src="getImage('ytk')"
                             alt="YTK"
                             class="w-full h-full object-cover transition-all duration-500"
                             x-show="getImage('ytk')"
                             :class="hoveredService ? 'scale-105' : 'scale-100'">
                        <div class="absolute inset-0 flex items-center justify-center" x-show="!getImage('ytk')">
                            <i class="fal fa-file-certificate text-5xl text-slate-300 dark:text-dark-600"></i>
                        </div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                        <div class="absolute bottom-0 left-0 right-0 p-4">
                            <h3 class="font-heading text-xl font-bold text-white mb-1">YTK Hizmetleri</h3>
                            <p class="text-sm text-white/80">Ruhsatlandırma & danışmanlık</p>
                        </div>
                    </div>
                    <a href="{{ $categoryUrl($ytk) }}" class="inline-flex items-center gap-2 text-sm font-medium text-primary-600 dark:text-primary-400 hover:text-primary-700 group">
                        Tüm Hizmetleri Gör <i class="fal fa-arrow-right text-xs transition-transform group-hover:translate-x-1"></i>
                    </a>
                </div>
                <div class="flex-1 grid grid-cols-2 gap-x-6 gap-y-1">
                    @foreach($ytk->services as $service)
                    <a href="{{ $serviceUrl($service) }}"
                       @mouseenter="hoveredService = {{ $service->service_id }}"
                       @mouseleave="hoveredService = null"
                       class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-primary-50 dark:hover:bg-primary-900/20 group transition-all duration-200">
                        <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-dark-800 group-hover:bg-primary-100 dark:group-hover:bg-primary-900/40 flex items-center justify-center shrink-0 transition-colors">
                            <i class="fal fa-file-certificate text-sm text-slate-500 dark:text-slate-400 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors"></i>
                        </div>
                        <span class="text-sm font-medium text-slate-700 dark:text-slate-200 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">{{ $service->title['tr'] ?? $service->title }}</span>
                        <i class="fal fa-chevron-right text-xs text-slate-300 dark:text-dark-600 group-hover:text-primary-500 ml-auto opacity-0 group-hover:opacity-100 transition-all"></i>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        @if($muhendislikKategoriler->count() > 0)
        <!-- Mühendislik Mega - Dinamik Görsel + 3 Alt Kategori -->
        <div x-show="activeMenu === 'muhendislik'" class="container mx-auto px-4 py-8">
            <div class="flex gap-8">
                <!-- Sol: Dinamik Görsel -->
                <div class="w-80 shrink-0">
                    <div class="aspect-[4/3] rounded-2xl overflow-hidden mb-4 bg-slate-100 dark:bg-dark-800 relative">
                        <img :src="getImage('muhendislik')"
                             alt="Mühendislik"
                             class="w-full h-full object-cover transition-all duration-500"
                             x-show="getImage('muhendislik')"
                             :class="(hoveredService || hoveredCategory) ? 'scale-105' : 'scale-100'">
                        <div class="absolute inset-0 flex items-center justify-center" x-show="!getImage('muhendislik')">
                            <i class="fal fa-compass-drafting text-5xl text-slate-300 dark:text-dark-600"></i>
                        </div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                        <div class="absolute bottom-0 left-0 right-0 p-4">
                            <h3 class="font-heading text-xl font-bold text-white mb-1">Mühendislik Hizmetleri</h3>
                            <p class="text-sm text-white/80">Jeoloji, Hidrojeoloji, Jeoteknik</p>
                        </div>
                    </div>
                </div>
                <!-- Sağ: 3 Alt Kategori -->
                <div class="flex-1 grid grid-cols-3 gap-6">
                    @foreach($muhendislikKategoriler as $kategori)
                    @php
                        $slug = $kategori->slug['tr'] ?? '';
                        $icon = $icons[$slug] ?? 'fa-cog';
                    @endphp
                    <div @mouseenter="hoveredCategory = {{ $kategori->category_id }}"
                         @mouseleave="hoveredCategory = null">
                        <!-- Kategori Header -->
                        <a href="{{ $categoryUrl($kategori) }}"
                           class="flex items-center gap-3 mb-3 pb-3 border-b border-slate-100 dark:border-dark-700 group">
                            <div class="w-10 h-10 rounded-xl bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center transition-colors group-hover:bg-primary-200 dark:group-hover:bg-primary-900/50">
                                <i class="fal {{ $icon }} text-lg text-primary-600 dark:text-primary-400"></i>
                            </div>
                            <span class="font-heading font-bold text-slate-900 dark:text-white group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
                                {{ $kategori->title['tr'] ?? $kategori->title }}
                            </span>
                        </a>
                        <!-- Hizmetler -->
                        <div class="space-y-0.5">
                            @foreach($kategori->services as $service)
                            <a href="{{ $serviceUrl($service) }}"
                               @mouseenter="hoveredService = {{ $service->service_id }}"
                               @mouseleave="hoveredService = null"
                               class="flex items-center gap-2 px-2 py-2 -mx-2 rounded-lg hover:bg-primary-50 dark:hover:bg-primary-900/20 group transition-all">
                                <i class="fal fa-chevron-right text-[10px] text-slate-300 dark:text-dark-600 group-hover:text-primary-500 transition-colors"></i>
                                <span class="text-sm text-slate-600 dark:text-slate-300 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">{{ $service->title['tr'] ?? $service->title }}</span>
                            </a>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        @if($mimarlik)
        <!-- Mimarlık Mega -->
        <div x-show="activeMenu === 'mimarlik'" class="container mx-auto px-4 py-8">
            <div class="flex gap-8">
                <div class="w-80 shrink-0">
                    <div class="aspect-[4/3] rounded-2xl overflow-hidden mb-4 bg-slate-100 dark:bg-dark-800 relative">
                        <img :src="getImage('mimarlik')"
                             alt="Mimarlık"
                             class="w-full h-full object-cover transition-all duration-500"
                             x-show="getImage('mimarlik')"
                             :class="hoveredService ? 'scale-105' : 'scale-100'">
                        <div class="absolute inset-0 flex items-center justify-center" x-show="!getImage('mimarlik')">
                            <i class="fal fa-drafting-compass text-5xl text-slate-300 dark:text-dark-600"></i>
                        </div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                        <div class="absolute bottom-0 left-0 right-0 p-4">
                            <h3 class="font-heading text-xl font-bold text-white mb-1">Mimarlık Hizmetleri</h3>
                            <p class="text-sm text-white/80">Modern tasarım çözümleri</p>
                        </div>
                    </div>
                    <a href="{{ $categoryUrl($mimarlik) }}" class="inline-flex items-center gap-2 text-sm font-medium text-primary-600 dark:text-primary-400 hover:text-primary-700 group">
                        Tüm Hizmetleri Gör <i class="fal fa-arrow-right text-xs transition-transform group-hover:translate-x-1"></i>
                    </a>
                </div>
                <div class="flex-1 grid grid-cols-2 gap-x-6 gap-y-1">
                    @foreach($mimarlik->services as $service)
                    <a href="{{ $serviceUrl($service) }}"
                       @mouseenter="hoveredService = {{ $service->service_id }}"
                       @mouseleave="hoveredService = null"
                       class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-primary-50 dark:hover:bg-primary-900/20 group transition-all duration-200">
                        <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-dark-800 group-hover:bg-primary-100 dark:group-hover:bg-primary-900/40 flex items-center justify-center shrink-0 transition-colors">
                            <i class="fal fa-drafting-compass text-sm text-slate-500 dark:text-slate-400 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors"></i>
                        </div>
                        <span class="text-sm font-medium text-slate-700 dark:text-slate-200 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">{{ $service->title['tr'] ?? $service->title }}</span>
                        <i class="fal fa-chevron-right text-xs text-slate-300 dark:text-dark-600 group-hover:text-primary-500 ml-auto opacity-0 group-hover:opacity-100 transition-all"></i>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- ========== MOBILE MENU ========== -->
    <div x-show="mobileMenu"
         x-transition
         class="xl:hidden fixed inset-0 top-[72px] bg-white dark:bg-dark-950 z-40 overflow-y-auto"
         x-cloak>
        <div class="container mx-auto px-4 py-4">

            @if($madencilik)
            <div x-data="{ open: false }" class="mt-1">
                <button @click="open = !open" class="flex items-center justify-between w-full px-3 py-3 rounded-lg hover:bg-slate-50 dark:hover:bg-dark-800">
                    <div class="flex items-center gap-3">
                        <i class="fal fa-pickaxe w-5 text-primary-500"></i>
                        <span class="font-medium text-slate-700 dark:text-white">Madencilik</span>
                    </div>
                    <i class="fal fa-chevron-down text-xs text-slate-400 transition-transform" :class="open && 'rotate-180'"></i>
                </button>
                <div x-show="open" x-collapse class="pl-11 pr-3 pb-2 space-y-0.5">
                    @foreach($madencilik->services as $service)
                    <a href="{{ $serviceUrl($service) }}" @click="mobileMenu = false" class="block py-2 text-sm text-slate-600 dark:text-slate-300 hover:text-primary-600">
                        {{ $service->title['tr'] ?? $service->title }}
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            @if($ytk)
            <div x-data="{ open: false }" class="mt-1">
                <button @click="open = !open" class="flex items-center justify-between w-full px-3 py-3 rounded-lg hover:bg-slate-50 dark:hover:bg-dark-800">
                    <div class="flex items-center gap-3">
                        <i class="fal fa-file-certificate w-5 text-primary-500"></i>
                        <span class="font-medium text-slate-700 dark:text-white">YTK</span>
                    </div>
                    <i class="fal fa-chevron-down text-xs text-slate-400 transition-transform" :class="open && 'rotate-180'"></i>
                </button>
                <div x-show="open" x-collapse class="pl-11 pr-3 pb-2 space-y-0.5">
                    @foreach($ytk->services as $service)
                    <a href="{{ $serviceUrl($service) }}" @click="mobileMenu = false" class="block py-2 text-sm text-slate-600 dark:text-slate-300 hover:text-primary-600">
                        {{ $service->title['tr'] ?? $service->title }}
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            @if($muhendislikKategoriler->count() > 0)
            <div x-data="{ open: false }" class="mt-1">
                <button @click="open = !open" class="flex items-center justify-between w-full px-3 py-3 rounded-lg hover:bg-slate-50 dark:hover:bg-dark-800">
                    <div class="flex items-center gap-3">
                        <i class="fal fa-compass-drafting w-5 text-primary-500"></i>
                        <span class="font-medium text-slate-700 dark:text-white">Mühendislik</span>
                    </div>
                    <i class="fal fa-chevron-down text-xs text-slate-400 transition-transform" :class="open && 'rotate-180'"></i>
                </button>
                <div x-show="open" x-collapse class="pl-3 pr-3 pb-2 pt-2 space-y-3">
                    @foreach($muhendislikKategoriler as $kategori)
                    <div class="pl-8">
                        <span class="block text-xs font-semibold text-primary-600 dark:text-primary-400 uppercase tracking-wider mb-1">{{ $kategori->title['tr'] ?? $kategori->title }}</span>
                        @foreach($kategori->services as $service)
                        <a href="{{ $serviceUrl($service) }}" @click="mobileMenu = false" class="block py-1.5 text-sm text-slate-600 dark:text-slate-300 hover:text-primary-600">
                            {{ $service->title['tr'] ?? $service->title }}
                        </a>
                        @endforeach
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            @if($mimarlik)
            <div x-data="{ open: false }" class="mt-1">
                <button @click="open = !open" class="flex items-center justify-between w-full px-3 py-3 rounded-lg hover:bg-slate-50 dark:hover:bg-dark-800">
                    <div class="flex items-center gap-3">
                        <i class="fal fa-drafting-compass w-5 text-primary-500"></i>
                        <span class="font-medium text-slate-700 dark:text-white">Mimarlık</span>
                    </div>
                    <i class="fal fa-chevron-down text-xs text-slate-400 transition-transform" :class="open && 'rotate-180'"></i>
                </button>
                <div x-show="open" x-collapse class="pl-11 pr-3 pb-2 space-y-0.5">
                    @foreach($mimarlik->services as $service)
                    <a href="{{ $serviceUrl($service) }}" @click="mobileMenu = false" class="block py-2 text-sm text-slate-600 dark:text-slate-300 hover:text-primary-600">
                        {{ $service->title['tr'] ?? $service->title }}
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            <a href="{{ $hakkimizdaUrl }}" @click="mobileMenu = false" class="flex items-center gap-3 px-3 py-3 mt-1 rounded-lg hover:bg-slate-50 dark:hover:bg-dark-800">
                <i class="fal fa-building w-5 text-primary-500"></i>
                <span class="font-medium text-slate-700 dark:text-white">Hakkımızda</span>
            </a>

            <a href="{{ $iletisimUrl }}" @click="mobileMenu = false" class="flex items-center gap-3 px-3 py-3 mt-1 rounded-lg hover:bg-slate-50 dark:hover:bg-dark-800">
                <i class="fal fa-envelope w-5 text-primary-500"></i>
                <span class="font-medium text-slate-700 dark:text-white">İletişim</span>
            </a>

            <div class="mt-4 pt-4 border-t border-slate-100 dark:border-dark-800 space-y-2">
                <a href="{{ $iletisimUrl }}" @click="mobileMenu = false" class="flex items-center justify-center gap-2 w-full py-3 bg-primary-600 text-white font-medium rounded-lg">
                    <i class="fal fa-phone"></i>
                    Teklif Al
                </a>
                @if($whatsappUrl)
                <a href="{{ $whatsappUrl }}" target="_blank" class="flex items-center justify-center gap-2 w-full py-3 bg-green-500 hover:bg-green-600 text-white font-medium rounded-lg transition-colors">
                    <i class="fab fa-whatsapp"></i>
                    WhatsApp ile İletişim
                </a>
                @endif
            </div>
        </div>
    </div>
</header>

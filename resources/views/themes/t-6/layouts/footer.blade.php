{{-- t-6 Theme - Art Deco Footer --}}
@php
    $siteName = setting('site_title');
    $siteSlogan = setting('site_slogan');
    $siteDescription = setting('site_description');
    $sitePhone = setting('contact_phone_1');
    $siteEmail = setting('contact_email_1');
    $siteWhatsapp = setting('contact_whatsapp_1');
    $whatsappUrl = whatsapp_link();
    $siteAddress = setting('contact_address');

    // Logo Service
    $logoService = app(\App\Services\LogoService::class);
    $logos = $logoService->getLogos();
    $hasLogo = $logos['has_light'] || $logos['has_dark'];

    // Services for footer
    $services = \Modules\Service\App\Models\Service::where('is_active', true)
        ->orderBy('service_id')
        ->take(8)
        ->get(['service_id', 'title', 'slug']);
@endphp

{{-- Footer --}}
<footer class="py-12 md:py-16 bg-slate-100 dark:bg-slate-950 border-t border-amber-500/20 dark:border-amber-500/10">
    <div class="container mx-auto px-3 sm:px-6 md:px-8 lg:px-12 xl:px-16 2xl:px-20">

        {{-- Footer Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 lg:gap-12 mb-12">

            {{-- Column 1: Logo & About --}}
            <div class="md:col-span-2 lg:col-span-1">
                {{-- Logo --}}
                <div class="flex flex-col items-start mb-6">
                    @if($hasLogo)
                        @if($logos['has_light'])
                            <img src="{{ $logos['light_logo_url'] }}" alt="{{ $siteName }}" class="h-10 w-auto dark:brightness-0 dark:invert">
                        @elseif($logos['has_dark'])
                            <img src="{{ $logos['dark_logo_url'] }}" alt="{{ $siteName }}" class="h-10 w-auto">
                        @endif
                    @else
                        <div class="flex items-center space-x-3 mb-2">
                            <div class="art-deco-diamond scale-75"></div>
                            <span class="font-heading text-xl tracking-[0.2em] uppercase gradient-text font-semibold">{{ $siteName }}</span>
                            <div class="art-deco-diamond scale-75"></div>
                        </div>
                        <span class="font-heading text-xs tracking-[0.4em] uppercase text-amber-700 dark:text-amber-500/70">{{ $siteSlogan }}</span>
                    @endif
                </div>

                @if($siteDescription)
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed font-medium text-sm">
                    {{ Str::limit($siteDescription, 150) }}
                </p>
                @endif
            </div>

            {{-- Column 2: Hizmetler --}}
            @if($services->count() > 0)
            <div>
                <h4 class="font-heading text-lg font-semibold text-slate-900 dark:text-white mb-6 tracking-wide">Hizmetlerimiz</h4>
                <ul class="space-y-3">
                    @foreach($services->take(6) as $service)
                    <li>
                        <a href="{{ url('/service/' . $service->slug) }}" class="text-slate-600 dark:text-slate-400 hover:text-amber-600 dark:hover:text-amber-400 transition-colors flex items-center gap-2 text-sm font-medium">
                            <i class="fat fa-chevron-right text-xs text-amber-500"></i>
                            {{ $service->title }}
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Column 3: Hızlı Erişim --}}
            <div>
                <h4 class="font-heading text-lg font-semibold text-slate-900 dark:text-white mb-6 tracking-wide">Hızlı Erişim</h4>
                <ul class="space-y-3">
                    <li>
                        <a href="{{ url('/page/hakkimizda') }}" class="text-slate-600 dark:text-slate-400 hover:text-amber-600 dark:hover:text-amber-400 transition-colors flex items-center gap-2 text-sm font-medium">
                            <i class="fat fa-chevron-right text-xs text-amber-500"></i>
                            Hakkımızda
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('/service') }}" class="text-slate-600 dark:text-slate-400 hover:text-amber-600 dark:hover:text-amber-400 transition-colors flex items-center gap-2 text-sm font-medium">
                            <i class="fat fa-chevron-right text-xs text-amber-500"></i>
                            Hizmetlerimiz
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('/page/iletisim') }}" class="text-slate-600 dark:text-slate-400 hover:text-amber-600 dark:hover:text-amber-400 transition-colors flex items-center gap-2 text-sm font-medium">
                            <i class="fat fa-chevron-right text-xs text-amber-500"></i>
                            İletişim
                        </a>
                    </li>
                </ul>
            </div>

            {{-- Column 4: İletişim --}}
            <div>
                <h4 class="font-heading text-lg font-semibold text-slate-900 dark:text-white mb-6 tracking-wide">İletişim</h4>
                <ul class="space-y-4">
                    @if($sitePhone)
                    <li>
                        <a href="tel:{{ preg_replace('/[^0-9+]/', '', $sitePhone) }}" class="flex items-start gap-3 text-slate-600 dark:text-slate-400 hover:text-amber-600 dark:hover:text-amber-400 transition-colors">
                            <i class="fat fa-phone text-amber-500 mt-1"></i>
                            <span class="text-sm font-medium">{{ $sitePhone }}</span>
                        </a>
                    </li>
                    @endif
                    @if($siteEmail)
                    <li>
                        <a href="mailto:{{ $siteEmail }}" class="flex items-start gap-3 text-slate-600 dark:text-slate-400 hover:text-amber-600 dark:hover:text-amber-400 transition-colors">
                            <i class="fat fa-envelope text-amber-500 mt-1"></i>
                            <span class="text-sm font-medium">{{ $siteEmail }}</span>
                        </a>
                    </li>
                    @endif
                    @if($siteAddress)
                    <li class="flex items-start gap-3 text-slate-600 dark:text-slate-400">
                        <i class="fat fa-location-dot text-amber-500 mt-1"></i>
                        <span class="text-sm font-medium">{{ $siteAddress }}</span>
                    </li>
                    @endif
                </ul>
            </div>

        </div>

        {{-- Art Deco Line --}}
        <div class="art-deco-line max-w-2xl mx-auto mb-8"></div>

        {{-- Copyright --}}
        <div class="text-center">
            <p class="text-slate-500 dark:text-slate-600 text-sm font-medium">
                &copy; {{ date('Y') }} {{ $siteName }}. Tüm hakları saklıdır.
            </p>
            {{-- Credit Link --}}
            <div class="mt-4">
                <a href="https://turkbilisim.com.tr" target="_blank" class="text-slate-400 dark:text-slate-700 hover:text-amber-500 dark:hover:text-amber-400 text-xs transition-colors">
                    Yapay Zeka, Web Tasarım ve Yazılım: <span class="font-medium">Türk Bilişim</span>
                </a>
            </div>
        </div>

    </div>
</footer>


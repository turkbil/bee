@extends('themes.t-6.layouts.app')

@php
    // Controller'dan gelen $item'ı $service olarak kullan
    $service = $item;

    $siteName = setting('site_title') ?: setting('site_company_name') ?: 'Mahmutoglu';

    // Service icons mapping
    $serviceIcons = [
        'ticaret-hukuku' => 'fa-handshake',
        'sirketler-hukuku' => 'fa-building',
        'ceza-hukuku' => 'fa-gavel',
        'saglik-hukuku' => 'fa-stethoscope',
        'sigorta-hukuku' => 'fa-shield-check',
        'idare-ve-imar-hukuku' => 'fa-landmark',
        'borclar-hukuku' => 'fa-file-contract',
        'is-hukuku' => 'fa-briefcase',
    ];

    $icon = $serviceIcons[$service->slug] ?? 'fa-scale-balanced';

    // Service images mapping - Leonardo AI generated
    $serviceImages = [
        'ticaret-hukuku' => '/storage/themes/t-6/service-ticaret-hukuku.jpg',
        'sirketler-hukuku' => '/storage/themes/t-6/service-sirketler-hukuku.jpg',
        'ceza-hukuku' => '/storage/themes/t-6/service-ceza-hukuku.jpg',
        'saglik-hukuku' => '/storage/themes/t-6/service-saglik-hukuku.jpg',
        'sigorta-hukuku' => '/storage/themes/t-6/service-sigorta-hukuku.jpg',
        'idare-ve-imar-hukuku' => '/storage/themes/t-6/service-idare-imar-hukuku.jpg',
        'borclar-hukuku' => '/storage/themes/t-6/service-borclar-hukuku.jpg',
        'is-hukuku' => '/storage/themes/t-6/service-is-hukuku.jpg',
    ];

    // Get service image (from media or fallback)
    $serviceImage = $service->getFirstMediaUrl('cover');
    if (empty($serviceImage) && isset($serviceImages[$service->slug])) {
        $serviceImage = $serviceImages[$service->slug];
    }

    // Other services for sidebar
    $otherServices = \Modules\Service\App\Models\Service::where('is_active', true)
        ->where('service_id', '!=', $service->service_id)
        ->orderBy('service_id')
        ->take(6)
        ->get(['service_id', 'title', 'slug']);
@endphp

@section('content')

{{-- Page Header with Background Image --}}
<section class="relative pt-24 pb-10 overflow-hidden">
    {{-- Background Image --}}
    @if($serviceImage)
    <div class="absolute inset-0">
        <img src="{{ $serviceImage }}" alt="" class="absolute inset-0 w-full h-full object-cover" aria-hidden="true">
        {{-- Light mode: beyaz overlay / Dark mode: koyu overlay --}}
        <div class="absolute inset-0 bg-gradient-to-r from-white/90 via-white/85 to-white/75 dark:from-slate-950/95 dark:via-slate-950/90 dark:to-slate-950/85"></div>
    </div>
    @else
    <div class="absolute inset-0 bg-gradient-to-b from-slate-100 to-white dark:from-slate-950 dark:to-slate-900"></div>
    @endif

    {{-- Decorative Line --}}
    <div class="absolute top-0 left-0 w-full h-px bg-gradient-to-r from-transparent via-amber-500/30 to-transparent"></div>

    <div class="container mx-auto px-3 sm:px-6 md:px-8 lg:px-12 xl:px-16 2xl:px-20 relative z-10">
        {{-- Breadcrumb --}}
        <nav class="mb-6" data-aos="fade-up">
            <ol class="flex items-center space-x-2 text-sm">
                <li><a href="{{ url('/') }}" class="text-amber-700 dark:text-amber-400 hover:text-amber-600 dark:hover:text-amber-300 transition-colors">Ana Sayfa</a></li>
                <li class="text-slate-400 dark:text-slate-500">/</li>
                <li><a href="{{ url('/service') }}" class="text-amber-700 dark:text-amber-400 hover:text-amber-600 dark:hover:text-amber-300 transition-colors">Hizmetler</a></li>
                <li class="text-slate-400 dark:text-slate-500">/</li>
                <li class="text-slate-600 dark:text-slate-300">{{ $service->title }}</li>
            </ol>
        </nav>

        <div class="flex items-center gap-6" data-aos="fade-up" data-aos-delay="100">
            {{-- Icon --}}
            <div class="w-16 h-16 rounded-xl bg-amber-500/20 dark:bg-amber-500/20 backdrop-blur-sm flex items-center justify-center flex-shrink-0">
                <i class="fat {{ $icon }} text-3xl text-amber-700 dark:text-amber-400"></i>
            </div>

            <div>
                <h1 class="font-heading text-2xl md:text-3xl lg:text-4xl font-bold text-slate-900 dark:text-white mb-2">
                    {{ $service->title }}
                </h1>
                @if($service->summary)
                <p class="text-slate-700 dark:text-slate-300 text-base max-w-2xl">
                    {{ Str::limit($service->summary, 150) }}
                </p>
                @endif
            </div>
        </div>
    </div>
</section>

{{-- Content Section --}}
<section class="py-12 md:py-16 bg-white dark:bg-slate-950">
    <div class="container mx-auto px-3 sm:px-6 md:px-8 lg:px-12 xl:px-16 2xl:px-20">
        <div class="grid lg:grid-cols-3 gap-8 lg:gap-12">

            {{-- Main Content --}}
            <div class="lg:col-span-2" data-aos="fade-up">
                {{-- Content --}}
                <div class="prose prose-lg dark:prose-invert max-w-none prose-headings:font-heading prose-headings:text-slate-900 dark:prose-headings:text-white prose-a:text-amber-600 dark:prose-a:text-amber-400 prose-strong:text-slate-900 dark:prose-strong:text-white">
                    {!! $service->body !!}
                </div>

                {{-- CTA --}}
                <div class="mt-10 p-6 bg-gradient-to-br from-amber-500/10 to-amber-600/5 rounded-xl border border-amber-500/20">
                    <h3 class="font-heading text-lg font-semibold text-slate-900 dark:text-white mb-3">{{ $service->title }} Hizmeti İçin Bize Ulaşın</h3>
                    <p class="text-slate-700 dark:text-slate-300 mb-4 text-sm">Uzman avukat kadromuz ile hukuki sorunlarınıza profesyonel çözümler sunuyoruz.</p>
                    <div class="flex flex-wrap gap-3">
                        @if(setting('contact_phone_1'))
                        <a href="tel:{{ preg_replace('/[^0-9+]/', '', setting('contact_phone_1')) }}" class="btn-shine bg-gradient-to-r from-amber-600 to-amber-500 text-white font-heading text-xs tracking-widest uppercase px-5 py-2.5 rounded-lg hover:from-amber-500 hover:to-amber-400 transition-all flex items-center">
                            <i class="fas fa-phone mr-2"></i>
                            Hemen Ara
                        </a>
                        @endif
                        <a href="{{ url('/page/iletisim') }}" class="border border-amber-600 dark:border-amber-500/50 text-amber-700 dark:text-amber-400 font-heading text-xs tracking-widest uppercase px-5 py-2.5 rounded-lg hover:bg-amber-500/10 transition-all flex items-center">
                            <i class="fas fa-envelope mr-2"></i>
                            İletişim
                        </a>
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="lg:col-span-1 space-y-6" data-aos="fade-up" data-aos-delay="100">

                {{-- Service Image Card --}}
                @if($serviceImage)
                <div class="rounded-xl overflow-hidden shadow-lg">
                    <img src="{{ $serviceImage }}" alt="{{ $service->title }}" class="w-full h-48 object-cover">
                </div>
                @endif

                {{-- Other Services --}}
                @if($otherServices->count() > 0)
                <div class="bg-slate-50 dark:bg-slate-900/50 rounded-xl p-5">
                    <h3 class="font-heading text-base font-semibold text-slate-900 dark:text-white mb-4 pb-3 border-b border-amber-500/20">
                        Diğer Hizmetlerimiz
                    </h3>
                    <ul class="space-y-2">
                        @foreach($otherServices as $otherService)
                        <li>
                            <a href="{{ url('/service/' . $otherService->slug) }}" class="flex items-center text-slate-700 dark:text-slate-300 hover:text-amber-600 dark:hover:text-amber-400 transition-colors py-1.5 text-sm">
                                <i class="fat fa-chevron-right text-[10px] text-amber-500 mr-2"></i>
                                {{ $otherService->title }}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                    <a href="{{ url('/service') }}" class="mt-4 block text-center text-amber-700 dark:text-amber-400 font-heading text-xs tracking-wider uppercase hover:text-amber-600 pt-3 border-t border-amber-500/10">
                        Tüm Hizmetler
                        <i class="fat fa-arrow-right ml-1"></i>
                    </a>
                </div>
                @endif

                {{-- Contact Card --}}
                <div class="gradient-border">
                    <div class="gradient-border-inner p-5">
                        <h3 class="font-heading text-base font-semibold text-slate-900 dark:text-white mb-4">İletişim</h3>
                        <ul class="space-y-3">
                            @if(setting('contact_phone_1'))
                            <li>
                                <a href="tel:{{ preg_replace('/[^0-9+]/', '', setting('contact_phone_1')) }}" class="flex items-center text-slate-700 dark:text-slate-300 hover:text-amber-600 dark:hover:text-amber-400 transition-colors text-sm">
                                    <i class="fat fa-phone text-amber-500 mr-2.5 text-sm"></i>
                                    {{ setting('contact_phone_1') }}
                                </a>
                            </li>
                            @endif
                            @if(setting('contact_email_1') ?: setting('site_email'))
                            <li>
                                <a href="mailto:{{ setting('contact_email_1') ?: setting('site_email') }}" class="flex items-center text-slate-700 dark:text-slate-300 hover:text-amber-600 dark:hover:text-amber-400 transition-colors text-sm">
                                    <i class="fat fa-envelope text-amber-500 mr-2.5 text-sm"></i>
                                    {{ setting('contact_email_1') ?: setting('site_email') }}
                                </a>
                            </li>
                            @endif
                            @if(setting('contact_address'))
                            <li class="flex items-start text-slate-700 dark:text-slate-300 text-sm">
                                <i class="fat fa-location-dot text-amber-500 mr-2.5 mt-0.5 text-sm"></i>
                                <span>{{ setting('contact_address') }}</span>
                            </li>
                            @endif
                        </ul>
                    </div>
                </div>

            </div>

        </div>
    </div>
</section>

@endsection

{{-- t-3 Panjur Theme - Footer --}}
@php
    $siteName = setting('site_title') ?: setting('site_company_name');
    $siteDescription = setting('site_description');
    $sitePhone = setting('contact_phone_1');
    $siteMobile = setting('contact_phone_2') ?: setting('contact_whatsapp_1');
    $siteEmail = setting('contact_email_1') ?: setting('site_email');
    $siteWhatsapp = setting('contact_whatsapp_1');
    $whatsappUrl = whatsapp_link();

    // Logo Service
    $logoService = app(\App\Services\LogoService::class);
    $logos = $logoService->getLogos();
    $hasLogo = $logos['has_light'] || $logos['has_dark'];
@endphp

{{-- Footer --}}
<footer class="bg-gray-900 dark:bg-black text-white pt-16 pb-8">
    <div class="container mx-auto">

        <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 lg:gap-12 mb-12">
            {{-- Column 1: About --}}
            <div>
                {{-- Logo - Gercek logo varsa SADECE logo, yoksa icon + yazi --}}
                <div class="flex items-center gap-3 mb-6">
                    @if($hasLogo)
                        {{-- Gercek logo var - sadece logo goster --}}
                        @if($logos['has_light'])
                            <img src="{{ $logos['light_logo_url'] }}" alt="{{ $siteName }}" class="h-10 w-auto brightness-0 invert">
                        @elseif($logos['has_dark'])
                            <img src="{{ $logos['dark_logo_url'] }}" alt="{{ $siteName }}" class="h-10 w-auto">
                        @endif
                    @else
                        {{-- Logo yok - icon + yazi goster --}}
                        <div class="w-12 h-12 bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl flex items-center justify-center">
                            <i class="fat fa-blinds text-white text-2xl"></i>
                        </div>
                        @if($siteName)
                        <div>
                            <span class="text-xl font-bold font-heading">{{ $siteName }}</span>
                        </div>
                        @endif
                    @endif
                </div>
                @if($siteDescription)
                <p class="text-gray-400 mb-6 leading-relaxed">
                    {{ $siteDescription }}
                </p>
                @endif
                <div class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600/20 border border-primary-600/30 rounded-lg">
                    <i class="fat fa-shield-check text-primary-400"></i>
                    <span class="text-sm font-medium text-primary-400">5 Yıl Garanti</span>
                </div>
            </div>

            {{-- Column 2: Quick Links --}}
            <div>
                <h4 class="text-lg font-bold font-heading mb-6">Hızlı Erişim</h4>
                <ul class="space-y-3">
                    <li><a href="{{ url('/') }}" class="text-gray-400 hover:text-primary-400 transition-colors flex items-center gap-2"><i class="fat fa-chevron-right text-xs text-primary-500"></i> Ana Sayfa</a></li>
                    <li><a href="{{ url('/service') }}" class="text-gray-400 hover:text-primary-400 transition-colors flex items-center gap-2"><i class="fat fa-chevron-right text-xs text-primary-500"></i> Hizmetler</a></li>
                    <li><a href="{{ url('/blog') }}" class="text-gray-400 hover:text-primary-400 transition-colors flex items-center gap-2"><i class="fat fa-chevron-right text-xs text-primary-500"></i> Blog</a></li>
                    <li><a href="{{ url('/page/hakkimizda') }}" class="text-gray-400 hover:text-primary-400 transition-colors flex items-center gap-2"><i class="fat fa-chevron-right text-xs text-primary-500"></i> Hakkımızda</a></li>
                    <li><a href="{{ url('/page/iletisim') }}" class="text-gray-400 hover:text-primary-400 transition-colors flex items-center gap-2"><i class="fat fa-chevron-right text-xs text-primary-500"></i> İletişim</a></li>
                </ul>
            </div>

            {{-- Column 3: Services --}}
            <div>
                <h4 class="text-lg font-bold font-heading mb-6">Hizmetlerimiz</h4>
                <ul class="space-y-3">
                    <li><a href="{{ url('/service/panjur-tamiri') }}" class="text-gray-400 hover:text-primary-400 transition-colors flex items-center gap-2"><i class="fat fa-chevron-right text-xs text-primary-500"></i> Panjur Tamiri</a></li>
                    <li><a href="{{ url('/service/motorlu-panjur-sistemleri') }}" class="text-gray-400 hover:text-primary-400 transition-colors flex items-center gap-2"><i class="fat fa-chevron-right text-xs text-primary-500"></i> Motorlu Panjur</a></li>
                    <li><a href="{{ url('/service/sineklik-sistemleri') }}" class="text-gray-400 hover:text-primary-400 transition-colors flex items-center gap-2"><i class="fat fa-chevron-right text-xs text-primary-500"></i> Sineklik Sistemleri</a></li>
                    <li><a href="{{ url('/service/garaj-kapilari') }}" class="text-gray-400 hover:text-primary-400 transition-colors flex items-center gap-2"><i class="fat fa-chevron-right text-xs text-primary-500"></i> Garaj Kapıları</a></li>
                    <li><a href="{{ url('/service/guvenli-panjur') }}" class="text-gray-400 hover:text-primary-400 transition-colors flex items-center gap-2"><i class="fat fa-chevron-right text-xs text-primary-500"></i> Güvenli Panjur</a></li>
                    <li><a href="{{ url('/service/manuel-ip-panjur') }}" class="text-gray-400 hover:text-primary-400 transition-colors flex items-center gap-2"><i class="fat fa-chevron-right text-xs text-primary-500"></i> Manuel İp Panjur</a></li>
                </ul>
            </div>

            {{-- Column 4: Contact --}}
            @if($sitePhone || $siteMobile || $siteEmail || $siteWhatsapp)
            <div>
                <h4 class="text-lg font-bold font-heading mb-6">İletişim</h4>
                <ul class="space-y-4">
                    @if($sitePhone)
                    <li>
                        <a href="tel:{{ preg_replace('/[^0-9+]/', '', $sitePhone) }}" class="flex items-start gap-3 text-gray-400 hover:text-primary-400 transition-colors">
                            <i class="fat fa-phone text-primary-400 mt-1"></i>
                            <div>
                                <span class="text-xs text-gray-500 block">Sabit Hat</span>
                                <span>{{ $sitePhone }}</span>
                            </div>
                        </a>
                    </li>
                    @endif
                    @if($siteMobile)
                    <li>
                        <a href="tel:{{ preg_replace('/[^0-9+]/', '', $siteMobile) }}" class="flex items-start gap-3 text-gray-400 hover:text-primary-400 transition-colors">
                            <i class="fat fa-mobile text-primary-400 mt-1"></i>
                            <div>
                                <span class="text-xs text-gray-500 block">Mobil Hat</span>
                                <span>{{ $siteMobile }}</span>
                            </div>
                        </a>
                    </li>
                    @endif
                    @if($siteWhatsapp)
                    <li>
                        <a href="{{ $whatsappUrl }}" target="_blank" class="flex items-start gap-3 text-gray-400 hover:text-green-400 transition-colors">
                            <i class="fab fa-whatsapp text-green-400 mt-1"></i>
                            <div>
                                <span class="text-xs text-gray-500 block">WhatsApp</span>
                                <span>{{ $siteWhatsapp }}</span>
                            </div>
                        </a>
                    </li>
                    @endif
                    @if($siteEmail)
                    <li>
                        <a href="mailto:{{ $siteEmail }}" class="flex items-start gap-3 text-gray-400 hover:text-primary-400 transition-colors">
                            <i class="fat fa-envelope text-primary-400 mt-1"></i>
                            <div>
                                <span class="text-xs text-gray-500 block">E-posta</span>
                                <span>{{ $siteEmail }}</span>
                            </div>
                        </a>
                    </li>
                    @endif
                </ul>
            </div>
            @endif
        </div>

        {{-- Footer Bottom --}}
        <div class="pt-8 border-t border-gray-800">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                @if($siteName)
                <p class="text-gray-500 text-sm text-center md:text-left">
                    &copy; {{ date('Y') }} {{ $siteName }}. Tüm hakları saklıdır.
                </p>
                @endif
                <p class="text-gray-500 text-sm text-center md:text-right">
                    <span class="text-primary-400">40 yıllık tecrübe</span> |
                    <span class="text-primary-400">5 yıl garanti</span> |
                    <span class="text-primary-400">Ücretsiz keşif</span>
                </p>
            </div>
            {{-- Credit Link --}}
            <div class="text-center mt-6 pt-6 border-t border-gray-800/50">
                <a href="https://turkbilisim.com.tr" target="_blank" class="text-gray-600 hover:text-primary-400 text-xs transition-colors">
                    Yapay Zeka ve Web Tasarım: <span class="font-medium">Türk Bilişim</span>
                </a>
            </div>
        </div>
    </div>
</footer>

{{-- Floating WhatsApp Button --}}
@if($whatsappUrl)
<a href="{{ $whatsappUrl }}" target="_blank" class="fixed bottom-6 right-6 z-50 w-14 h-14 bg-green-500 hover:bg-green-600 text-white rounded-full flex items-center justify-center shadow-lg shadow-green-500/30 hover:shadow-green-500/50 transition-all duration-300 hover:scale-110">
    <i class="fab fa-whatsapp text-2xl"></i>
</a>
@endif

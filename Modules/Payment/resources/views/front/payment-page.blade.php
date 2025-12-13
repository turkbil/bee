<!DOCTYPE html>
@php
    $currentLocale = app()->getLocale();
    $isRtl = in_array($currentLocale, ['ar', 'he', 'fa', 'ur']) ? 'rtl' : 'ltr';
    $defaultLocale = get_tenant_default_locale();
    $homeUrl = $currentLocale === $defaultLocale ? url('/') : url('/' . $currentLocale);

    // Logo bilgilerini LogoService'den al
    $logoService = app(\App\Services\LogoService::class);
    $logos = $logoService->getLogos();

    // Settings'den site bilgileri
    $siteTitle = setting('site_title', setting('site_name', config('app.name')));

    // İletişim bilgileri (tenant-aware)
    $contactPhone = setting('contact_phone_1');
    $contactWhatsapp = setting('contact_whatsapp_1');
@endphp
<html lang="{{ $currentLocale }}" dir="{{ $isRtl }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $siteTitle }} - Ödeme</title>

    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('assets/libs/fontawesome-pro@7.1.0/css/all.css') }}">

    <style>
        #paytriframe {
            min-height: calc(100vh - 160px);
            height: 1000px;
        }
        @media (max-width: 768px) {
            #paytriframe {
                min-height: 700px;
            }
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">

    {{-- Header --}}
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                {{-- Logo --}}
                @if($logos['has_light'])
                    <a href="{{ $homeUrl }}">
                        <img src="{{ $logos['light_logo_url'] }}" alt="{{ $siteTitle }}" class="h-8 object-contain">
                    </a>
                @elseif($logos['has_dark'])
                    <a href="{{ $homeUrl }}">
                        <img src="{{ $logos['dark_logo_url'] }}" alt="{{ $siteTitle }}" class="h-8 object-contain">
                    </a>
                @else
                    <a href="{{ $homeUrl }}" class="text-xl font-bold text-gray-900">{{ $siteTitle }}</a>
                @endif

                {{-- Tutar --}}
                <div class="flex items-center gap-4">
                    <div class="text-right">
                        <p class="text-xs text-gray-500 leading-none">Toplam</p>
                        <p class="text-xl font-bold text-gray-900 leading-tight">{{ number_format($order->total_amount, 2, ',', '.') }} ₺</p>
                    </div>
                </div>
            </div>
        </div>
    </header>

    {{-- Main --}}
    <main class="max-w-7xl mx-auto px-4 py-6">
        {{-- Ödeme Kartı --}}
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            {{-- Üst Bar --}}
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center">
                        <i class="fa-solid fa-credit-card text-white"></i>
                    </div>
                    <div>
                        <h1 class="font-semibold text-gray-900">Ödeme</h1>
                        <p class="text-xs text-gray-500">{{ $orderNumber }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-1 text-green-600 text-sm">
                    <i class="fa-solid fa-lock text-xs"></i>
                    <span class="hidden sm:inline text-xs">SSL</span>
                </div>
            </div>

            {{-- Iframe --}}
            <iframe
                src="{{ $paymentIframeUrl }}"
                id="paytriframe"
                class="w-full border-0"
                frameborder="0"
                scrolling="auto">
            </iframe>
        </div>

        {{-- Alt Bilgi --}}
        <div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-4">
            <a href="{{ route('cart.checkout') }}" class="text-gray-500 hover:text-gray-700 text-sm flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i>
                Geri Dön
            </a>

            @if($contactPhone || $contactWhatsapp)
                <div class="flex items-center gap-4">
                    @if($contactPhone)
                        <a href="tel:{{ preg_replace('/[^0-9+]/', '', $contactPhone) }}" class="text-gray-500 hover:text-gray-700 text-sm flex items-center gap-2">
                            <i class="fa-solid fa-phone"></i>
                            <span class="hidden sm:inline">{{ $contactPhone }}</span>
                        </a>
                    @endif
                    @if($contactWhatsapp)
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $contactWhatsapp) }}" target="_blank" class="text-green-600 hover:text-green-700 text-sm flex items-center gap-2">
                            <i class="fa-brands fa-whatsapp text-lg"></i>
                            <span class="hidden sm:inline">WhatsApp</span>
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </main>

    {{-- Footer --}}
    <footer class="py-4 text-center text-xs text-gray-400">
        © {{ date('Y') }} {{ $siteTitle }}
    </footer>

    <script>
        // PayTR iframe auto height & status
        window.addEventListener('message', function(event) {
            if (event.data && event.data.height) {
                document.getElementById('paytriframe').style.height = event.data.height + 'px';
            }
            if (event.data && event.data.paytr_status) {
                if (event.data.paytr_status === 'success') {
                    window.location.href = '{{ route("payment.success") }}';
                } else if (event.data.paytr_status === 'failed') {
                    window.location.href = '{{ route("cart.checkout") }}?payment=failed';
                }
            }
        }, false);
    </script>
</body>
</html>

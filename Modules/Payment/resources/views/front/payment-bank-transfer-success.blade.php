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
    <title>{{ $siteTitle }} - Bildirim Alındı</title>

    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('assets/libs/fontawesome-pro@7.1.0/css/all.css') }}">
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">

    {{-- Header --}}
    <header class="bg-white shadow-sm">
        <div class="max-w-4xl mx-auto px-4">
            <div class="flex items-center justify-center h-16">
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
            </div>
        </div>
    </header>

    {{-- Main --}}
    <main class="flex-1 flex items-center justify-center px-4 py-12">
        <div class="max-w-md w-full">
            <div class="bg-white rounded-2xl shadow-sm p-8 text-center">
                {{-- Success Icon --}}
                <div class="w-20 h-20 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fa-solid fa-check text-3xl text-emerald-600"></i>
                </div>

                {{-- Başlık --}}
                <h1 class="text-2xl font-bold text-gray-900 mb-2">Bildirim Alındı!</h1>
                <p class="text-gray-600 mb-6">Havale/EFT bildiriminiz başarıyla iletildi.</p>

                {{-- Sipariş Bilgisi --}}
                <div class="bg-gray-50 rounded-xl p-4 mb-6">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm text-gray-500">Sipariş No</span>
                        <span class="font-mono font-medium text-gray-900">{{ $orderNumber }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Tutar</span>
                        <span class="font-bold text-gray-900">{{ number_format($order->total_amount, 2, ',', '.') }} ₺</span>
                    </div>
                </div>

                {{-- Bilgi --}}
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6 text-left">
                    <div class="flex gap-3">
                        <i class="fa-solid fa-info-circle text-blue-500 mt-0.5"></i>
                        <div class="text-sm text-blue-800">
                            <p>Ödemeniz onaylandıktan sonra siparişiniz hazırlanmaya başlayacaktır.</p>
                            <p class="mt-2">Onay işlemi genellikle 1-2 iş günü içinde tamamlanır.</p>
                        </div>
                    </div>
                </div>

                {{-- Butonlar --}}
                <div class="space-y-3">
                    <a
                        href="{{ $homeUrl }}"
                        class="block w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-3 rounded-xl transition-colors"
                    >
                        Alışverişe Devam Et
                    </a>

                    @if($contactWhatsapp)
                        @php
                            // WhatsApp numarasını düzenle - +90 prefix ekle
                            $waNumber = preg_replace('/[^0-9]/', '', $contactWhatsapp);
                            // 0 ile başlıyorsa, başındaki 0'ı kaldır ve 90 ekle
                            if (str_starts_with($waNumber, '0')) {
                                $waNumber = '90' . substr($waNumber, 1);
                            }
                            // 90 ile başlamıyorsa, 90 ekle
                            elseif (!str_starts_with($waNumber, '90')) {
                                $waNumber = '90' . $waNumber;
                            }
                        @endphp
                        <a
                            href="https://wa.me/{{ $waNumber }}?text={{ urlencode('Merhaba, ' . $orderNumber . ' numaralı siparişim için havale yaptım.') }}"
                            target="_blank"
                            class="block w-full bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 font-medium py-3 rounded-xl transition-colors"
                        >
                            <i class="fa-brands fa-whatsapp text-green-600 mr-2"></i>
                            WhatsApp ile Bilgi Al
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </main>

    {{-- Footer --}}
    <footer class="py-4 text-center text-xs text-gray-400">
        &copy; {{ date('Y') }} {{ $siteTitle }}
    </footer>

    {{-- 🛒 Sepet cache'ini temizle - Ödeme başarılı olduğunda --}}
    <script>
        // localStorage'dan cart bilgilerini temizle
        localStorage.removeItem('cart_id');
        localStorage.removeItem('cart_items');
        localStorage.removeItem('cart_total');
        console.log('🛒 Cart cache cleared after successful payment');
    </script>
</body>
</html>

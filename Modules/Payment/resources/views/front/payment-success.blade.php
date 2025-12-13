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

    // İletişim bilgileri
    $contactPhone = setting('contact_phone_1');
    $contactWhatsapp = setting('contact_whatsapp_1');

    // Teslimat adresi
    $shippingAddr = $order->shipping_address ?? null;
    $addrLine = '';
    $addrCity = '';

    if (is_array($shippingAddr)) {
        $addrLine = $shippingAddr['address_line_1'] ?? ($shippingAddr['address'] ?? '');
        $addrCity = trim(($shippingAddr['district'] ?? '') . ', ' . ($shippingAddr['city'] ?? ''), ', ');
    } elseif (is_string($shippingAddr)) {
        $addrLine = $shippingAddr;
    }
@endphp
<html lang="{{ $currentLocale }}" dir="{{ $isRtl }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $siteTitle }} - Sipariş Tamamlandı</title>

    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('assets/libs/fontawesome-pro@7.1.0/css/all.css') }}">
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">

    {{-- Header --}}
    <header class="bg-white shadow-sm">
        <div class="max-w-4xl mx-auto px-4">
            <div class="flex items-center justify-center h-16">
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
    <main class="flex-1 px-4 py-12">
        <div class="max-w-2xl mx-auto">
            {{-- Success Card --}}
            <div class="bg-white rounded-2xl shadow-sm p-8 text-center mb-6">
                {{-- Success Icon --}}
                <div class="w-20 h-20 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fa-solid fa-check text-3xl text-emerald-600"></i>
                </div>

                <h1 class="text-2xl font-bold text-gray-900 mb-2">Siparişiniz Alındı!</h1>
                <p class="text-gray-600 mb-6">Ödemeniz başarıyla tamamlandı. Teşekkür ederiz!</p>

                {{-- Sipariş Bilgileri --}}
                <div class="bg-gray-50 rounded-xl p-4 mb-6 text-left">
                    @if($order)
                        <div class="flex items-center justify-between mb-3 pb-3 border-b border-gray-200">
                            <span class="text-sm text-gray-500">Sipariş No</span>
                            <span class="font-mono font-bold text-gray-900">{{ $order->order_number }}</span>
                        </div>
                    @endif

                    <div class="flex items-center justify-between mb-3 pb-3 border-b border-gray-200">
                        <span class="text-sm text-gray-500">Tarih</span>
                        <span class="text-gray-900">{{ $payment->created_at->format('d.m.Y H:i') }}</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Toplam</span>
                        <span class="text-xl font-bold text-emerald-600">{{ number_format($payment->amount, 2, ',', '.') }} ₺</span>
                    </div>
                </div>

                {{-- Teslimat Adresi --}}
                @if($addrLine || $addrCity)
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6 text-left">
                    <div class="flex items-start gap-3">
                        <i class="fa-solid fa-location-dot text-blue-500 mt-1"></i>
                        <div>
                            <p class="font-semibold text-gray-900 mb-1">Teslimat Adresi</p>
                            @if($addrLine)
                                <p class="text-sm text-gray-700">{{ $addrLine }}</p>
                            @endif
                            @if($addrCity)
                                <p class="text-sm text-gray-700">{{ $addrCity }}</p>
                            @endif
                            @if($order->customer_name)
                                <p class="text-sm text-gray-600 mt-2">{{ $order->customer_name }}</p>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                {{-- Ürünler --}}
                @if($order && $order->items->count() > 0)
                <div class="border border-gray-200 rounded-xl mb-6 overflow-hidden">
                    <div class="bg-gray-50 px-4 py-2 border-b border-gray-200">
                        <p class="text-sm font-semibold text-gray-700">Sipariş İçeriği</p>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @foreach($order->items as $item)
                            <div class="flex items-center justify-between px-4 py-3">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $item->product_name }}</p>
                                    <p class="text-sm text-gray-500">Adet: {{ $item->quantity }}</p>
                                </div>
                                <p class="font-semibold text-gray-900">{{ number_format($item->total, 2, ',', '.') }} ₺</p>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Butonlar --}}
                <div class="space-y-3">
                    <a href="{{ $homeUrl }}"
                       class="block w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-3 rounded-xl transition-colors">
                        Alışverişe Devam Et
                    </a>

                    @if($contactWhatsapp)
                        @php
                            $waNumber = preg_replace('/[^0-9]/', '', $contactWhatsapp);
                            if (str_starts_with($waNumber, '0')) {
                                $waNumber = '90' . substr($waNumber, 1);
                            } elseif (!str_starts_with($waNumber, '90')) {
                                $waNumber = '90' . $waNumber;
                            }
                        @endphp
                        <a href="https://wa.me/{{ $waNumber }}?text={{ urlencode('Merhaba, ' . ($order->order_number ?? 'sipariş') . ' numaralı siparişim hakkında bilgi almak istiyorum.') }}"
                           target="_blank"
                           class="block w-full bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 font-medium py-3 rounded-xl transition-colors">
                            <i class="fa-brands fa-whatsapp text-green-600 mr-2"></i>
                            WhatsApp ile İletişim
                        </a>
                    @endif
                </div>
            </div>

            {{-- Bilgi Notu --}}
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 text-center">
                <p class="text-sm text-blue-800">
                    <i class="fa-solid fa-envelope mr-2"></i>
                    Sipariş detayları e-posta adresinize gönderilmiştir.
                </p>
            </div>
        </div>
    </main>

    {{-- Footer --}}
    <footer class="py-4 text-center text-xs text-gray-400">
        &copy; {{ date('Y') }} {{ $siteTitle }}
    </footer>

    {{-- Sepet Cache Temizle --}}
    <script>
        localStorage.removeItem('cart_id');
        localStorage.removeItem('cart_items');
        localStorage.removeItem('cart_total');
        console.log('🛒 Cart cache cleared after successful payment');
    </script>
</body>
</html>

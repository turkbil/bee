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
    <title>{{ $siteTitle }} - Havale/EFT ile Ödeme</title>

    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('assets/libs/fontawesome-pro@7.1.0/css/all.css') }}">
</head>
<body class="bg-gray-100 min-h-screen">

    {{-- Header --}}
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-5xl mx-auto px-4">
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
    <main class="max-w-5xl mx-auto px-4 py-6">
        {{-- Başlık Kartı --}}
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-emerald-600 rounded-xl flex items-center justify-center">
                        <i class="fa-solid fa-building-columns text-white"></i>
                    </div>
                    <div>
                        <h1 class="font-semibold text-gray-900">Havale / EFT</h1>
                        <p class="text-xs text-gray-500">{{ $orderNumber }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Açıklama --}}
        @if($description)
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6">
                <div class="flex gap-3">
                    <i class="fa-solid fa-info-circle text-blue-500 mt-0.5"></i>
                    <p class="text-sm text-blue-800">{{ $description }}</p>
                </div>
            </div>
        @endif

        {{-- Banka Kartları --}}
        <div class="space-y-4 mb-6">
            @foreach($banks as $index => $bank)
                <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                    <div class="p-6">
                        {{-- Banka Adı --}}
                        <div class="flex items-center gap-3 mb-4 pb-4 border-b border-gray-100">
                            <div class="w-10 h-10 bg-gray-100 rounded-xl flex items-center justify-center">
                                <i class="fa-solid fa-university text-gray-600"></i>
                            </div>
                            <h2 class="font-semibold text-gray-900">{{ $bank['name'] }}</h2>
                        </div>

                        {{-- Bilgiler --}}
                        <div class="space-y-3">
                            {{-- Hesap Sahibi --}}
                            @if($bank['holder'])
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-500">Hesap Sahibi</span>
                                    <span class="font-medium text-gray-900">{{ $bank['holder'] }}</span>
                                </div>
                            @endif

                            {{-- Şube --}}
                            @if($bank['branch'])
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-500">Şube</span>
                                    <span class="font-medium text-gray-900">{{ $bank['branch'] }}</span>
                                </div>
                            @endif

                            {{-- IBAN --}}
                            <div class="flex items-center justify-between gap-2 bg-gray-50 rounded-xl p-3 -mx-1">
                                <div class="flex-1">
                                    <span class="text-xs text-gray-500 block mb-1">IBAN</span>
                                    <span class="font-mono text-sm text-gray-900" id="iban-{{ $index }}">{{ $bank['iban'] }}</span>
                                </div>
                                <button
                                    type="button"
                                    onclick="copyIban('{{ $bank['iban'] }}', this)"
                                    class="flex items-center gap-2 px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm text-gray-600 hover:bg-gray-50 transition-colors"
                                >
                                    <i class="fa-regular fa-copy"></i>
                                    <span>Kopyala</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Ödeme Bildir Kartı --}}
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <form action="{{ route('payment.bank-transfer.confirm', $orderNumber) }}" method="POST">
                @csrf
                <div class="p-6">
                    <h3 class="font-semibold text-gray-900 mb-4">Havale/EFT Yaptım</h3>

                    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-4">
                        <div class="flex gap-3">
                            <i class="fa-solid fa-exclamation-triangle text-amber-500 mt-0.5"></i>
                            <div class="text-sm text-amber-800">
                                <p class="font-medium mb-1">Açıklama kısmına sipariş numaranızı yazın:</p>
                                <p class="font-mono bg-amber-100 inline-block px-2 py-1 rounded">{{ $orderNumber }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="transfer_note" class="block text-sm font-medium text-gray-700 mb-2">Not (Opsiyonel)</label>
                        <textarea
                            name="transfer_note"
                            id="transfer_note"
                            rows="2"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 resize-none"
                            placeholder="Gönderen ismi, tarih, saat gibi bilgiler..."
                        ></textarea>
                    </div>

                    <button
                        type="submit"
                        class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-4 rounded-xl transition-colors flex items-center justify-center gap-2"
                    >
                        <i class="fa-solid fa-check"></i>
                        Havale Yaptım, Bildir
                    </button>
                </div>
            </form>
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
        &copy; {{ date('Y') }} {{ $siteTitle }}
    </footer>

    <script>
        function copyIban(iban, button) {
            navigator.clipboard.writeText(iban.replace(/\s/g, '')).then(function() {
                const originalText = button.innerHTML;
                button.innerHTML = '<i class="fa-solid fa-check text-emerald-600"></i><span class="text-emerald-600">Kopyalandı!</span>';
                setTimeout(function() {
                    button.innerHTML = originalText;
                }, 2000);
            });
        }
    </script>
</body>
</html>

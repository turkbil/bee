@extends($layoutPath)

@php
    $currentLocale = app()->getLocale();
    $defaultLocale = get_tenant_default_locale();
    $homeUrl = $currentLocale === $defaultLocale ? url('/') : url('/' . $currentLocale);

    // Settings'den site bilgileri
    $siteTitle = setting('site_title', setting('site_name', config('app.name')));

    // İletişim bilgileri (tenant-aware)
    $contactWhatsapp = setting('contact_whatsapp_1');
@endphp

@section('title', $siteTitle . ' - Bildirim Alındı')

@section('content')
<div class="bg-gray-100 dark:bg-gray-900 min-h-[60vh] flex items-center justify-center px-4 py-12">
    <div class="max-w-md w-full">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-8 text-center">
            {{-- Success Icon --}}
            <div class="w-20 h-20 bg-emerald-100 dark:bg-emerald-900/30 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fa-solid fa-check text-3xl text-emerald-600 dark:text-emerald-400"></i>
            </div>

            {{-- Başlık --}}
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Bildirim Alındı!</h1>
            <p class="text-gray-600 dark:text-gray-400 mb-6">Havale/EFT bildiriminiz başarıyla iletildi.</p>

            {{-- Sipariş Bilgisi --}}
            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4 mb-6">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Sipariş No</span>
                    <span class="font-mono font-medium text-gray-900 dark:text-white">{{ $orderNumber }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Tutar</span>
                    <span class="font-bold text-gray-900 dark:text-white">{{ number_format($order->total_amount, 2, ',', '.') }} ₺</span>
                </div>
            </div>

            {{-- Bilgi --}}
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4 mb-6 text-left">
                <div class="flex gap-3">
                    <i class="fa-solid fa-info-circle text-blue-500 dark:text-blue-400 mt-0.5"></i>
                    <div class="text-sm text-blue-800 dark:text-blue-300">
                        <p>Ödemeniz onaylandıktan sonra siparişiniz hazırlanmaya başlayacaktır.</p>
                        <p class="mt-2">Onay işlemi genellikle 1-2 iş günü içinde tamamlanır.</p>
                    </div>
                </div>
            </div>

            {{-- Butonlar --}}
            <div class="space-y-3">
                <a href="{{ $homeUrl }}"
                   class="block w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-3 rounded-xl transition-colors">
                    <i class="fa-solid fa-home mr-2"></i> Siteye Dön
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
                    <a href="https://wa.me/{{ $waNumber }}?text={{ urlencode('Merhaba, ' . $orderNumber . ' numaralı siparişim için havale yaptım.') }}"
                       target="_blank"
                       class="block w-full bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 font-medium py-3 rounded-xl transition-colors">
                        <i class="fa-brands fa-whatsapp text-green-600 dark:text-green-400 mr-2"></i>
                        WhatsApp ile Bilgi Al
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Sepet cache temizle --}}
<script>
    localStorage.removeItem('cart_id');
    localStorage.removeItem('cart_items');
    localStorage.removeItem('cart_total');
    console.log('Cart cache cleared after successful payment');
</script>
@endsection

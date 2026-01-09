@extends($layoutPath)

@php
    $currentLocale = app()->getLocale();
    $defaultLocale = get_tenant_default_locale();
    $homeUrl = $currentLocale === $defaultLocale ? url('/') : url('/' . $currentLocale);

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

@section('title', $siteTitle . ' - ' . ($isPending ? 'Ödeme İşleniyor' : 'Sipariş Tamamlandı'))

@section('content')
{{-- Pending durumunda sayfa otomatik yenilenecek (5 saniye) --}}
@if($isPending)
<meta http-equiv="refresh" content="5">
@endif

<div class="min-h-screen bg-gradient-to-br from-{{ $isPending ? 'blue' : 'emerald' }}-50 via-white to-{{ $isPending ? 'indigo' : 'blue' }}-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 py-12 px-4">
    <div class="max-w-3xl mx-auto">

        {{-- Header (Success or Pending) --}}
        <div class="text-center mb-8">
            @if($isPending)
                {{-- Pending: İşleniyor animasyonu --}}
                <div class="inline-flex items-center justify-center w-24 h-24 bg-gradient-to-br from-blue-400 to-indigo-500 rounded-full mb-6 shadow-xl animate-pulse">
                    <i class="fa-solid fa-clock-rotate-left text-4xl text-white"></i>
                </div>
                <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-3">Ödemeniz İşleniyor...</h1>
                <p class="text-lg text-gray-600 dark:text-gray-400">Siparişiniz alındı, ödeme onayı bekleniyor.</p>
                <p class="text-sm text-blue-600 dark:text-blue-400 mt-2 font-semibold">
                    <i class="fa-solid fa-spinner fa-spin"></i> Sayfa otomatik yenilenecek (5 saniye)
                </p>
            @else
                {{-- Success: Başarılı --}}
                <div class="inline-flex items-center justify-center w-24 h-24 bg-gradient-to-br from-emerald-400 to-green-500 rounded-full mb-6 shadow-xl">
                    <i class="fa-solid fa-check text-4xl text-white"></i>
                </div>
                <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-3">Ödeme Başarılı!</h1>
                <p class="text-lg text-gray-600 dark:text-gray-400">Siparişiniz başarıyla alındı. Teşekkür ederiz!</p>
            @endif
        </div>

        {{-- Order Info Card --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden mb-6">

            {{-- Order Header --}}
            <div class="bg-gradient-to-r from-{{ $isPending ? 'blue' : 'emerald' }}-500 to-{{ $isPending ? 'indigo' : 'green' }}-600 px-6 py-4 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90 mb-1">Sipariş Numarası</p>
                        <p class="text-2xl font-bold font-mono">{{ $order->order_number ?? 'N/A' }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm opacity-90 mb-1">{{ $isPending ? 'Durum' : 'Tarih' }}</p>
                        <p class="font-semibold">
                            @if($isPending)
                                <i class="fa-solid fa-clock"></i> İşleniyor
                            @else
                                {{ $payment->created_at->format('d.m.Y H:i') }}
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            {{-- Order Items --}}
            @if($order && $order->items->count() > 0)
            <div class="p-6">
                <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-4">Sipariş İçeriği</h3>

                <div class="space-y-4">
                    @foreach($order->items as $item)
                        @php
                            $isSubscription = $item->orderable_type === 'Modules\Subscription\App\Models\SubscriptionPlan';
                            $metadata = is_array($item->metadata) ? $item->metadata : json_decode($item->metadata, true);
                            $cycleLabel = $metadata['cycle_label']['tr'] ?? $metadata['cycle_label']['en'] ?? null;
                            $durationDays = $metadata['duration_days'] ?? null;
                        @endphp

                        <div class="flex items-start gap-4 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                            {{-- Icon --}}
                            @if($isSubscription)
                                <div class="flex-shrink-0 w-14 h-14 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl flex items-center justify-center shadow-lg">
                                    <i class="fa-solid fa-crown text-2xl text-white"></i>
                                </div>
                            @else
                                <div class="flex-shrink-0 w-14 h-14 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
                                    <i class="fa-solid fa-box text-2xl text-white"></i>
                                </div>
                            @endif

                            {{-- Content --}}
                            <div class="flex-1 min-w-0">
                                <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-2">{{ $item->product_name }}</h4>
                                <div class="flex flex-wrap gap-3 text-sm">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-white dark:bg-gray-600 rounded-full text-gray-700 dark:text-gray-200">
                                        <i class="fa-solid fa-hashtag text-xs"></i>
                                        {{ $item->quantity }} Adet
                                    </span>
                                    @if($isSubscription && $cycleLabel)
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-blue-100 dark:bg-blue-900/30 rounded-full text-blue-700 dark:text-blue-300">
                                            <i class="fa-solid fa-calendar-days"></i>
                                            {{ $cycleLabel }}
                                        </span>
                                    @endif
                                    @if($isSubscription && $durationDays)
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-green-100 dark:bg-green-900/30 rounded-full text-green-700 dark:text-green-300">
                                            <i class="fa-solid fa-clock"></i>
                                            {{ $durationDays }} gün
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Price --}}
                            <div class="flex-shrink-0 text-right">
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($item->total, 2, ',', '.') }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">TRY</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Total --}}
            <div class="bg-gray-50 dark:bg-gray-900/50 px-6 py-5 border-t border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <span class="text-lg font-semibold text-gray-700 dark:text-gray-300">Toplam Tutar</span>
                    <span class="text-3xl font-bold text-emerald-600 dark:text-emerald-400">{{ number_format($payment->amount, 2, ',', '.') }} ₺</span>
                </div>
            </div>
        </div>

        {{-- Delivery Address (if exists) --}}
        @if($addrLine || $addrCity)
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 p-6 mb-6">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0 w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center">
                    <i class="fa-solid fa-location-dot text-xl text-blue-600 dark:text-blue-400"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Teslimat Adresi</h3>
                    @if($order->customer_name)
                        <p class="font-semibold text-gray-700 dark:text-gray-300">{{ $order->customer_name }}</p>
                    @endif
                    @if($addrLine)
                        <p class="text-gray-600 dark:text-gray-400">{{ $addrLine }}</p>
                    @endif
                    @if($addrCity)
                        <p class="text-gray-600 dark:text-gray-400">{{ $addrCity }}</p>
                    @endif
                </div>
            </div>
        </div>
        @endif

        {{-- Action Buttons --}}
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
               class="flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white font-bold py-4 rounded-xl transition-all shadow-lg hover:shadow-xl">
                <i class="fa-brands fa-whatsapp text-xl"></i>
                <span>WhatsApp Desteği</span>
            </a>
        @endif

    </div>
</div>

{{-- Sepet Cache Temizle (sadece ödeme başarılıysa) --}}
@if(!$isPending)
<script>
    localStorage.removeItem('cart_id');
    localStorage.removeItem('cart_items');
    localStorage.removeItem('cart_total');
    console.log('Cart cache cleared after successful payment');
</script>
@else
<script>
    // Pending durumunda cart cache'i koru (ödeme onaylanmadı)
    console.log('Payment pending, cart cache retained. Page will refresh in 5 seconds...');
</script>
@endif
@endsection

@php
    $themeService = app(\App\Services\ThemeService::class);
    $activeTheme = $themeService->getActiveTheme();
    $themeName = $activeTheme ? $activeTheme->name : 'ixtif';
    $isMuzibu = $themeName === 'muzibu';
@endphp
@extends('themes.' . $themeName . '.layouts.app')

@section('title', __('Sipariş') . ' #' . $order->order_number)

@section('content')
@if($isMuzibu)
    {{-- Muzibu Dark Theme --}}
    @include('cart::front.partials.order-detail-content')
@else
    {{-- Other Themes (ixtif, etc.) --}}
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        {{-- Hero Header --}}
        <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                {{-- Breadcrumb --}}
                <nav class="mb-4 text-sm">
                    <ol class="flex items-center space-x-2 text-gray-500 dark:text-gray-400">
                        <li><a href="{{ route('shop.orders.index') }}" data-spa class="hover:text-blue-600 transition-colors">{{ __('cart::front.my_orders') }}</a></li>
                        <li><i class="fa-solid fa-chevron-right text-xs"></i></li>
                        <li class="text-gray-900 dark:text-white font-semibold">{{ $order->order_number }}</li>
                    </ol>
                </nav>

                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <h1 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-2">
                            Sipariş #{{ $order->order_number }}
                        </h1>
                        <p class="text-lg text-gray-600 dark:text-gray-300">
                            {{ $order->created_at->locale(app()->getLocale())->translatedFormat('d M Y, H:i') }}
                        </p>
                    </div>

                    {{-- Status Badge --}}
                    @php
                        $statusColors = [
                            'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
                            'processing' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
                            'shipped' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400',
                            'delivered' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
                            'completed' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
                            'cancelled' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
                            'payment_failed' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
                        ];
                        $statusLabels = [
                            'pending' => 'Beklemede',
                            'processing' => 'Hazırlanıyor',
                            'shipped' => 'Kargoya Verildi',
                            'delivered' => 'Teslim Edildi',
                            'completed' => 'Tamamlandı',
                            'cancelled' => 'İptal Edildi',
                            'payment_failed' => 'Ödeme Başarısız',
                        ];
                        $statusColor = $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800';
                        $statusLabel = $statusLabels[$order->status] ?? $order->status;
                    @endphp
                    <span class="px-5 py-2.5 rounded-full text-sm font-semibold {{ $statusColor }}">
                        {{ $statusLabel }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Content --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid md:grid-cols-2 gap-6 mb-6">
                    {{-- Payment Info --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                            <i class="fa-solid fa-credit-card text-blue-600 mr-2"></i>
                            Ödeme Bilgileri
                        </h2>

                        <div class="space-y-3">
                            <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Toplam</span>
                                <span class="text-xl font-bold text-blue-600">
                                    {{ number_format(round($order->total_amount), 0, ',', '.') }}
                                    <i class="fa-solid fa-turkish-lira text-sm ml-0.5"></i>
                                </span>
                            </div>

                            <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Ödeme Durumu</span>
                                @php
                                    $paymentColors = [
                                        'pending' => 'text-yellow-600',
                                        'paid' => 'text-green-600',
                                        'failed' => 'text-red-600',
                                        'refunded' => 'text-purple-600',
                                    ];
                                    $paymentLabels = [
                                        'pending' => 'Bekliyor',
                                        'paid' => 'Ödendi',
                                        'failed' => 'Başarısız',
                                        'refunded' => 'İade Edildi',
                                    ];
                                    $paymentColor = $paymentColors[$order->payment_status] ?? 'text-gray-600';
                                    $paymentLabel = $paymentLabels[$order->payment_status] ?? $order->payment_status;
                                @endphp
                                <span class="font-semibold {{ $paymentColor }}">{{ $paymentLabel }}</span>
                            </div>

                            @if($payment && $payment->gateway)
                                <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Ödeme Yöntemi</span>
                                    <span class="text-gray-900 dark:text-white">
                                        @if($payment->gateway === 'paytr')
                                            <i class="fa-solid fa-credit-card mr-1"></i> Kredi Kartı
                                        @elseif($payment->gateway === 'bank_transfer')
                                            <i class="fa-solid fa-building-columns mr-1"></i> Havale/EFT
                                        @else
                                            {{ $payment->gateway }}
                                        @endif
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Customer Info --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                            <i class="fa-solid fa-user text-blue-600 mr-2"></i>
                            Müşteri Bilgileri
                        </h2>

                        <div class="space-y-3">
                            @if($order->customer_name)
                                <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Ad Soyad</span>
                                    <span class="text-gray-900 dark:text-white">{{ $order->customer_name }}</span>
                                </div>
                            @endif

                            @if($order->customer_email)
                                <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">E-posta</span>
                                    <span class="text-gray-900 dark:text-white">{{ $order->customer_email }}</span>
                                </div>
                            @endif

                            @if($order->customer_phone)
                                <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Telefon</span>
                                    <span class="text-gray-900 dark:text-white">{{ $order->customer_phone }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Order Items --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-6">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class="fa-solid fa-box-open text-blue-600 mr-2"></i>
                        Sipariş İçeriği
                    </h2>

                    <div class="space-y-4">
                        @foreach($order->items as $item)
                            @php
                                $isSubscription = str_contains($item->orderable_type ?? '', 'SubscriptionPlan');
                                $itemIcon = $isSubscription ? 'fa-crown' : 'fa-box';
                                $iconColor = $isSubscription ? 'bg-yellow-100 text-yellow-500 dark:bg-yellow-900/30' : 'bg-blue-100 text-blue-500 dark:bg-blue-900/30';
                            @endphp

                            <div class="flex items-center space-x-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <div class="w-12 h-12 rounded-lg flex items-center justify-center flex-shrink-0 {{ $iconColor }}">
                                    <i class="fa-solid {{ $itemIcon }} text-lg"></i>
                                </div>

                                <div class="flex-1 min-w-0">
                                    <h3 class="font-semibold text-gray-900 dark:text-white">
                                        {{ $item->item_title ?? $item->product_name ?? 'Ürün' }}
                                    </h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $isSubscription ? 'Abonelik' : 'Ürün' }} · Adet: {{ $item->quantity }}
                                    </p>
                                </div>

                                <div class="text-right">
                                    <p class="text-lg font-bold text-gray-900 dark:text-white">
                                        {{ number_format(round($item->total), 0, ',', '.') }} TL
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Order Summary --}}
                    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-600">
                        <div class="space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500 dark:text-gray-400">Ara Toplam</span>
                                <span class="text-gray-900 dark:text-white">{{ number_format($order->subtotal, 0, ',', '.') }} TL</span>
                            </div>

                            @if($order->tax_amount > 0)
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500 dark:text-gray-400">KDV</span>
                                    <span class="text-gray-900 dark:text-white">{{ number_format($order->tax_amount, 0, ',', '.') }} TL</span>
                                </div>
                            @endif

                            @if($order->discount_amount > 0)
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500 dark:text-gray-400">İndirim</span>
                                    <span class="text-green-600">-{{ number_format($order->discount_amount, 0, ',', '.') }} TL</span>
                                </div>
                            @endif

                            <div class="flex justify-between pt-4 border-t border-gray-200 dark:border-gray-600">
                                <span class="text-lg font-bold text-gray-900 dark:text-white">Toplam</span>
                                <span class="text-xl font-bold text-blue-600">{{ number_format($order->total_amount, 0, ',', '.') }} TL</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex flex-wrap justify-center gap-4">
                    <a href="{{ route('shop.orders.index') }}" data-spa
                       class="inline-flex items-center px-6 py-3 bg-white dark:bg-gray-800 rounded-xl shadow hover:shadow-lg transition-all text-gray-700 dark:text-gray-200 font-medium">
                        <i class="fa-solid fa-arrow-left mr-2"></i>
                        {{ __('cart::front.my_orders') }}
                    </a>
                </div>
        </div>
    </div>
@endif
@endsection

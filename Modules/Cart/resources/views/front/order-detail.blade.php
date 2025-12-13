@php
    $themeService = app(\App\Services\ThemeService::class);
    $activeTheme = $themeService->getActiveTheme();
    $themeName = $activeTheme ? $activeTheme->name : 'ixtif';
@endphp
@extends('themes.' . $themeName . '.layouts.app')

@section('title', __('Sipariş') . ' #' . $order->order_number)

@section('module_content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    {{-- Hero Header --}}
    <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            {{-- Breadcrumb --}}
            <nav class="mb-4 text-sm">
                <ol class="flex items-center space-x-2 text-gray-500 dark:text-gray-400">
                    <li><a href="{{ route('shop.orders.index') }}" class="hover:text-blue-600 transition-colors">{{ __('cart::front.my_orders') }}</a></li>
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

                {{-- Shipping/Delivery Info --}}
                @if($order->requires_shipping && $order->shipping_address)
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                            <i class="fa-solid fa-truck text-green-600 mr-2"></i>
                            Teslimat Bilgileri
                        </h2>

                        <div class="space-y-4">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Teslimat Adresi</p>
                                <div class="text-gray-900 dark:text-white">
                                    @if(is_array($order->shipping_address))
                                        @if(!empty($order->shipping_address['full_name']))
                                            <p class="font-semibold">{{ $order->shipping_address['full_name'] }}</p>
                                        @elseif(!empty($order->shipping_address['first_name']))
                                            <p class="font-semibold">{{ $order->shipping_address['first_name'] }} {{ $order->shipping_address['last_name'] ?? '' }}</p>
                                        @endif
                                        @if(!empty($order->shipping_address['address_line_1']))
                                            <p>{{ $order->shipping_address['address_line_1'] }}</p>
                                        @endif
                                        @if(!empty($order->shipping_address['address_line_2']))
                                            <p>{{ $order->shipping_address['address_line_2'] }}</p>
                                        @endif
                                        @if(!empty($order->shipping_address['neighborhood']))
                                            <p>{{ $order->shipping_address['neighborhood'] }}</p>
                                        @endif
                                        @if(!empty($order->shipping_address['district']) || !empty($order->shipping_address['city']))
                                            <p>{{ $order->shipping_address['district'] ?? '' }}{{ !empty($order->shipping_address['district']) && !empty($order->shipping_address['city']) ? ' / ' : '' }}{{ $order->shipping_address['city'] ?? '' }}</p>
                                        @endif
                                        @if(!empty($order->shipping_address['postal_code']))
                                            <p class="text-sm text-gray-500">{{ $order->shipping_address['postal_code'] }}</p>
                                        @endif
                                        @if(!empty($order->shipping_address['phone']))
                                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                                <i class="fa-solid fa-phone mr-1"></i> {{ $order->shipping_address['phone'] }}
                                            </p>
                                        @endif
                                    @elseif($order->shipping_address)
                                        <p>{{ $order->shipping_address }}</p>
                                    @else
                                        <p class="text-gray-400 italic">Adres bilgisi yok</p>
                                    @endif
                                </div>
                            </div>

                            @if($order->tracking_number)
                                <div class="p-3 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Kargo Takip No</p>
                                    <p class="font-mono font-bold text-purple-700 dark:text-purple-400">
                                        {{ $order->tracking_number }}
                                    </p>
                                </div>
                            @endif

                            @if($order->shipped_at)
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    <i class="fa-solid fa-clock mr-1"></i>
                                    Kargoya verildi: {{ $order->shipped_at->locale(app()->getLocale())->translatedFormat('d M Y, H:i') }}
                                </p>
                            @endif

                            @if($order->delivered_at)
                                <p class="text-sm text-green-600">
                                    <i class="fa-solid fa-check-circle mr-1"></i>
                                    Teslim edildi: {{ $order->delivered_at->locale(app()->getLocale())->translatedFormat('d M Y, H:i') }}
                                </p>
                            @endif
                        </div>
                    </div>
                @else
                    {{-- Customer Info (for digital/subscription orders) --}}
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
                @endif
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
                            // Orderable türüne göre görsel, icon ve link belirle
                            $itemImage = $item->item_image;
                            $itemIcon = 'fa-solid fa-box';
                            $iconColor = 'bg-gray-100 text-gray-400';
                            $itemLink = null;
                            $itemType = 'Ürün';
                            $itemTypeColor = 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400';

                            if ($item->orderable_type === 'Modules\\Shop\\App\\Models\\ShopProduct') {
                                // Fiziksel/Dijital ürün
                                if ($item->is_digital) {
                                    $itemIcon = 'fa-solid fa-download';
                                    $iconColor = 'bg-purple-100 text-purple-500 dark:bg-purple-900/30';
                                    $itemType = 'Dijital Ürün';
                                    $itemTypeColor = 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400';
                                } else {
                                    $itemIcon = 'fa-solid fa-box';
                                    $iconColor = 'bg-blue-100 text-blue-500 dark:bg-blue-900/30';
                                    $itemType = 'Fiziksel Ürün';
                                    $itemTypeColor = 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400';
                                }

                                if ($item->orderable) {
                                    if ($item->orderable->firstMedia) {
                                        $itemImage = thumb($item->orderable->firstMedia, 80, 80);
                                    }
                                    // Slug multilingual JSON olabilir, locale'e göre al
                                    $productSlug = $item->orderable->getTranslated('slug', app()->getLocale())
                                        ?? (is_array($item->orderable->slug) ? ($item->orderable->slug[app()->getLocale()] ?? reset($item->orderable->slug)) : $item->orderable->slug);
                                    if ($productSlug) {
                                        $itemLink = route('shop.show', $productSlug);
                                    }
                                }
                            } elseif ($item->orderable_type === 'Modules\\Subscription\\App\\Models\\SubscriptionPlan') {
                                // Abonelik
                                $itemIcon = 'fa-solid fa-crown';
                                $iconColor = 'bg-yellow-100 text-yellow-500 dark:bg-yellow-900/30';
                                $itemType = 'Abonelik';
                                $itemTypeColor = 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400';
                            }
                        @endphp

                        <div class="flex items-center space-x-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            {{-- Image --}}
                            @if($itemImage)
                                <img src="{{ $itemImage }}"
                                     alt="{{ $item->product_name }}"
                                     class="w-20 h-20 object-cover rounded-lg flex-shrink-0">
                            @else
                                <div class="w-20 h-20 rounded-lg flex items-center justify-center flex-shrink-0 {{ $iconColor }}">
                                    <i class="{{ $itemIcon }} text-2xl"></i>
                                </div>
                            @endif

                            {{-- Details --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between">
                                    <div>
                                        @if($itemLink)
                                            <a href="{{ $itemLink }}" class="font-semibold text-gray-900 dark:text-white hover:text-blue-600 transition-colors">
                                                {{ $item->product_name }}
                                            </a>
                                        @else
                                            <h3 class="font-semibold text-gray-900 dark:text-white">
                                                {{ $item->product_name }}
                                            </h3>
                                        @endif

                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs {{ $itemTypeColor }} mt-1">
                                            {{ $itemType }}
                                        </span>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between mt-2">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Adet: {{ $item->quantity }}
                                        @if($item->item_sku)
                                            <span class="ml-2 font-mono text-xs">{{ $item->item_sku }}</span>
                                        @endif
                                    </p>

                                    <p class="text-lg font-bold text-gray-900 dark:text-white">
                                        {{ number_format(round($item->total), 0, ',', '.') }}
                                        <i class="fa-solid fa-turkish-lira text-sm ml-0.5"></i>
                                    </p>
                                </div>

                                {{-- Download Button (for digital items) --}}
                                @if($item->is_digital && $item->download_url && $item->canDownload())
                                    <a href="{{ $item->download_url }}"
                                       class="inline-flex items-center mt-2 px-3 py-1 bg-purple-600 hover:bg-purple-700 text-white text-sm rounded-lg transition-colors"
                                       target="_blank">
                                        <i class="fa-solid fa-download mr-2"></i>
                                        İndir
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Order Summary --}}
                <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-600">
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500 dark:text-gray-400">Ara Toplam</span>
                            <span class="text-gray-900 dark:text-white">
                                {{ number_format(round($order->subtotal), 0, ',', '.') }}
                                <i class="fa-solid fa-turkish-lira text-xs ml-0.5"></i>
                            </span>
                        </div>

                        @if($order->discount_amount > 0)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500 dark:text-gray-400">İndirim</span>
                                <span class="text-green-600">
                                    -{{ number_format(round($order->discount_amount), 0, ',', '.') }}
                                    <i class="fa-solid fa-turkish-lira text-xs ml-0.5"></i>
                                </span>
                            </div>
                        @endif

                        @if($order->tax_amount > 0)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500 dark:text-gray-400">KDV</span>
                                <span class="text-gray-900 dark:text-white">
                                    {{ number_format(round($order->tax_amount), 0, ',', '.') }}
                                    <i class="fa-solid fa-turkish-lira text-xs ml-0.5"></i>
                                </span>
                            </div>
                        @endif

                        @if($order->shipping_cost > 0)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500 dark:text-gray-400">Kargo</span>
                                <span class="text-gray-900 dark:text-white">
                                    {{ number_format(round($order->shipping_cost), 0, ',', '.') }}
                                    <i class="fa-solid fa-turkish-lira text-xs ml-0.5"></i>
                                </span>
                            </div>
                        @endif

                        <div class="flex justify-between pt-4 border-t border-gray-200 dark:border-gray-600">
                            <span class="text-lg font-bold text-gray-900 dark:text-white">Toplam</span>
                            <span class="text-xl font-bold text-blue-600">
                                {{ number_format(round($order->total_amount), 0, ',', '.') }}
                                <i class="fa-solid fa-turkish-lira text-sm ml-0.5"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex flex-wrap justify-center gap-4 mt-8">
                <a href="{{ route('shop.orders.index') }}"
                   class="inline-flex items-center px-6 py-3 bg-white dark:bg-gray-800 rounded-xl shadow hover:shadow-lg transition-all text-gray-700 dark:text-gray-200 font-medium hover:-translate-y-0.5">
                    <i class="fa-solid fa-arrow-left mr-2"></i>
                    {{ __('cart::front.my_orders') }}
                </a>

                <button onclick="window.print()"
                        class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl shadow hover:shadow-lg transition-all font-medium hover:-translate-y-0.5">
                    <i class="fa-solid fa-print mr-2"></i>
                    Yazdır
                </button>
            </div>

            {{-- Help Section --}}
            @if(setting('contact_phone') || setting('whatsapp_number'))
            <div class="mt-12 p-8 bg-gradient-to-r from-blue-600 to-blue-700 rounded-xl text-white text-center">
                <h3 class="text-xl font-bold mb-2">Yardım mı lazım?</h3>
                <p class="text-blue-100 mb-6">Siparişinizle ilgili sorularınız için bize ulaşın</p>
                <div class="flex flex-wrap justify-center gap-4">
                    @if(setting('contact_phone'))
                        <a href="tel:{{ setting('contact_phone') }}"
                           class="inline-flex items-center px-5 py-2.5 bg-white text-blue-600 font-semibold rounded-lg hover:bg-blue-50 transition-colors">
                            <i class="fa-solid fa-phone mr-2"></i>
                            {{ setting('contact_phone') }}
                        </a>
                    @endif

                    @if(setting('whatsapp_number'))
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', setting('whatsapp_number')) }}?text=Sipariş No: {{ $order->order_number }}"
                           class="inline-flex items-center px-5 py-2.5 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-colors"
                           target="_blank">
                            <i class="fa-brands fa-whatsapp mr-2"></i>
                            WhatsApp
                        </a>
                    @endif
                </div>
            </div>
            @endif
    </div>
</div>
@endsection

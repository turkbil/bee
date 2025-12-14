@php
    $themeService = app(\App\Services\ThemeService::class);
    $activeTheme = $themeService->getActiveTheme();
    $themeName = $activeTheme ? $activeTheme->name : 'ixtif';
@endphp
@extends('themes.' . $themeName . '.layouts.app')

@section('title', __('Siparişlerim'))

@section('module_content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    {{-- Hero Header --}}
    <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="text-center">
                <h1 class="text-4xl md:text-5xl font-bold text-gray-900 dark:text-white mb-4">
                    {{ __('cart::front.my_orders') }}
                </h1>
                <p class="text-xl text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
                    {{ __('cart::front.view_order_history') }}
                </p>
            </div>
        </div>
    </div>

    {{-- Content --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">

            @if($orders->count() > 0)
                {{-- Orders List --}}
                <div class="space-y-4">
                    @foreach($orders as $order)
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                            {{-- Order Header --}}
                            <div class="p-5 border-b border-gray-100 dark:border-gray-700">
                                <div class="flex flex-wrap items-center justify-between gap-4">
                                    <div>
                                        <span class="text-xs text-gray-500 dark:text-gray-400 block mb-1">Sipariş No</span>
                                        <span class="font-mono font-bold text-gray-900 dark:text-white">{{ $order->order_number }}</span>
                                    </div>

                                    <div class="text-right">
                                        <span class="text-xs text-gray-500 dark:text-gray-400 block mb-1">Tarih</span>
                                        <span class="text-gray-900 dark:text-white">{{ $order->created_at->locale(app()->getLocale())->translatedFormat('d M Y, H:i') }}</span>
                                    </div>

                                    <div class="text-right">
                                        <span class="text-xs text-gray-500 dark:text-gray-400 block mb-1">Toplam</span>
                                        <span class="text-lg font-bold text-blue-600">
                                            {{ number_format(round($order->total_amount), 0, ',', '.') }}
                                            <i class="fa-solid fa-turkish-lira text-sm ml-0.5"></i>
                                        </span>
                                    </div>

                                    {{-- Status Badges --}}
                                    <div class="flex flex-col gap-1 items-end">
                                        @php
                                            // Sipariş Durumu
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

                                            // Ödeme Durumu
                                            $paymentColors = [
                                                'pending' => 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400',
                                                'paid' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400',
                                                'failed' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
                                                'refunded' => 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400',
                                            ];
                                            $paymentLabels = [
                                                'pending' => 'Ödeme Bekliyor',
                                                'paid' => 'Ödendi',
                                                'failed' => 'Ödeme Başarısız',
                                                'refunded' => 'İade Edildi',
                                            ];
                                            $paymentColor = $paymentColors[$order->payment_status] ?? 'bg-gray-100 text-gray-800';
                                            $paymentLabel = $paymentLabels[$order->payment_status] ?? $order->payment_status;
                                        @endphp
                                        {{-- Ödeme Durumu Badge --}}
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $paymentColor }}">
                                            <i class="fa-solid fa-credit-card mr-1"></i>{{ $paymentLabel }}
                                        </span>
                                        {{-- Sipariş Durumu Badge --}}
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $statusColor }}">
                                            {{ $statusLabel }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {{-- Order Items Preview --}}
                            <div class="p-5">
                                <div class="flex flex-wrap items-center gap-4">
                                    {{-- Item Images/Icons --}}
                                    <div class="flex -space-x-3">
                                        @foreach($order->items->take(3) as $item)
                                            @php
                                                // Orderable türüne göre görsel ve icon belirle
                                                $itemImage = null;
                                                $itemIcon = 'fa-solid fa-box';
                                                $iconColor = 'text-gray-400';

                                                if ($item->orderable_type === 'Modules\\Shop\\App\\Models\\ShopProduct') {
                                                    // Fiziksel/Dijital ürün
                                                    $itemIcon = $item->is_digital ? 'fa-solid fa-download' : 'fa-solid fa-box';
                                                    $iconColor = $item->is_digital ? 'text-purple-500' : 'text-blue-500';
                                                    if ($item->orderable && $item->orderable->firstMedia) {
                                                        $itemImage = thumb($item->orderable->firstMedia, 48, 48);
                                                    }
                                                } elseif ($item->orderable_type === 'Modules\\Subscription\\App\\Models\\SubscriptionPlan') {
                                                    // Abonelik
                                                    $itemIcon = 'fa-solid fa-crown';
                                                    $iconColor = 'text-yellow-500';
                                                } elseif ($item->item_image) {
                                                    $itemImage = $item->item_image;
                                                }
                                            @endphp

                                            @if($itemImage)
                                                <img src="{{ $itemImage }}"
                                                     alt="{{ $item->product_name }}"
                                                     class="w-12 h-12 rounded-lg border-2 border-white dark:border-gray-700 object-cover shadow-sm">
                                            @else
                                                <div class="w-12 h-12 rounded-lg border-2 border-white dark:border-gray-700 bg-gray-100 dark:bg-gray-600 flex items-center justify-center shadow-sm">
                                                    <i class="{{ $itemIcon }} {{ $iconColor }}"></i>
                                                </div>
                                            @endif
                                        @endforeach

                                        @if($order->items->count() > 3)
                                            <div class="w-12 h-12 rounded-lg border-2 border-white dark:border-gray-700 bg-gray-200 dark:bg-gray-600 flex items-center justify-center shadow-sm">
                                                <span class="text-xs font-bold text-gray-600 dark:text-gray-300">+{{ $order->items->count() - 3 }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Item Summary --}}
                                    <div class="flex-1">
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            @if($order->items->count() === 1)
                                                {{ $order->items->first()->product_name }}
                                            @else
                                                {{ $order->items->count() }} ürün
                                            @endif
                                        </p>

                                        {{-- Order Type Tags --}}
                                        <div class="flex flex-wrap gap-2 mt-2">
                                            @php
                                                $hasPhysical = $order->items->where('is_digital', false)->where('orderable_type', 'Modules\\Shop\\App\\Models\\ShopProduct')->count() > 0;
                                                $hasDigital = $order->items->where('is_digital', true)->count() > 0;
                                                $hasSubscription = $order->items->where('orderable_type', 'Modules\\Subscription\\App\\Models\\SubscriptionPlan')->count() > 0;
                                            @endphp

                                            @if($hasPhysical)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                                                    <i class="fa-solid fa-truck mr-1"></i> Fiziksel
                                                </span>
                                            @endif

                                            @if($hasDigital)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-purple-50 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400">
                                                    <i class="fa-solid fa-download mr-1"></i> Dijital
                                                </span>
                                            @endif

                                            @if($hasSubscription)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-yellow-50 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400">
                                                    <i class="fa-solid fa-crown mr-1"></i> Abonelik
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- View Details Button --}}
                                    <a href="{{ route('shop.orders.show', $order->order_id) }}"
                                       class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors">
                                        <i class="fa-solid fa-eye mr-2"></i>
                                        Detaylar
                                    </a>
                                </div>
                            </div>

                            {{-- Tracking Info (if shipped) --}}
                            @if($order->tracking_number)
                                <div class="px-5 pb-5">
                                    <div class="p-3 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                                        <span class="text-sm text-purple-700 dark:text-purple-400">
                                            <i class="fa-solid fa-truck-fast mr-2"></i>
                                            Takip No: <span class="font-mono font-bold">{{ $order->tracking_number }}</span>
                                        </span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                @if($orders->hasPages())
                    <div class="mt-16 flex justify-center">
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                            <div class="flex items-center space-x-4">
                                @if ($orders->onFirstPage())
                                    <span class="px-4 py-2 text-sm text-gray-400 dark:text-gray-600 cursor-not-allowed">
                                        ← Önceki
                                    </span>
                                @else
                                    <a href="{{ $orders->previousPageUrl() }}"
                                        class="px-4 py-2 text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition-colors font-medium">
                                        ← Önceki
                                    </a>
                                @endif

                                <div class="flex items-center space-x-2">
                                    <span class="px-3 py-1 text-sm bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 rounded-md font-medium">
                                        {{ $orders->currentPage() }}
                                    </span>
                                    <span class="text-sm text-gray-500 dark:text-gray-400">
                                        / {{ $orders->lastPage() }}
                                    </span>
                                </div>

                                @if ($orders->hasMorePages())
                                    <a href="{{ $orders->nextPageUrl() }}"
                                        class="px-4 py-2 text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition-colors font-medium">
                                        Sonraki →
                                    </a>
                                @else
                                    <span class="px-4 py-2 text-sm text-gray-400 dark:text-gray-600 cursor-not-allowed">
                                        Sonraki →
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            @else
                {{-- Empty State --}}
                <div class="text-center py-20">
                    <div class="max-w-md mx-auto">
                        <div class="mb-6">
                            <i class="fa-solid fa-shopping-bag text-5xl text-gray-300 dark:text-gray-600"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                            Henüz siparişiniz yok
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400 text-lg">
                            Satın aldığınız ürünler burada listelenecek
                        </p>
                    </div>
                </div>
            @endif
    </div>
</div>
@endsection

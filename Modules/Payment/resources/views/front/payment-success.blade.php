@extends('themes::' . config('theme.active_theme') . '.layouts.app')

@section('title', 'Sipariş Tamamlandı!')

@section('content')
<div class="bg-gray-50 dark:bg-gray-900 min-h-screen py-12 transition-colors">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            {{-- Success Icon & Message --}}
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-24 h-24 bg-green-100 dark:bg-green-900/30 rounded-full mb-4 animate-bounce">
                    <i class="fa-solid fa-check-circle text-5xl text-green-600"></i>
                </div>
                <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-2">
                    Siparişiniz Alındı!
                </h1>
                <p class="text-lg text-gray-600 dark:text-gray-400">
                    Ödemeniz başarıyla tamamlandı. Teşekkür ederiz!
                </p>
            </div>

            {{-- Order Details Grid --}}
            <div class="grid md:grid-cols-2 gap-6 mb-6">
                {{-- Sipariş Özeti --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class="fa-solid fa-receipt text-blue-600 mr-2"></i>
                        Sipariş Bilgileri
                    </h2>

                    <div class="space-y-3">
                        @if($order)
                            <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <span class="text-sm font-semibold text-gray-600 dark:text-gray-400">Sipariş No:</span>
                                <span class="font-mono font-bold text-gray-900 dark:text-white">{{ $order->order_number }}</span>
                            </div>
                        @endif

                        <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <span class="text-sm font-semibold text-gray-600 dark:text-gray-400">Tarih:</span>
                            <span class="text-gray-900 dark:text-white">{{ $payment->created_at->format('d M Y, H:i') }}</span>
                        </div>

                        <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <span class="text-sm font-semibold text-gray-600 dark:text-gray-400">Toplam Tutar:</span>
                            <span class="text-xl font-bold text-green-600">
                                {{ number_format(round($payment->amount), 0, ',', '.') }}
                                <i class="fa-solid fa-turkish-lira text-lg ml-0.5"></i>
                            </span>
                        </div>

                        @if(isset($payment->installment_count) && $payment->installment_count > 1)
                            <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <span class="text-sm font-semibold text-gray-600 dark:text-gray-400">Taksit:</span>
                                <span class="text-gray-900 dark:text-white">
                                    {{ $payment->installment_count }} x
                                    {{ number_format(round($payment->amount / $payment->installment_count), 0, ',', '.') }}
                                    <i class="fa-solid fa-turkish-lira text-xs ml-0.5"></i>
                                </span>
                            </div>
                        @endif
                    </div>

                    <div class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-700">
                        <div class="flex items-start space-x-2">
                            <i class="fa-solid fa-info-circle text-blue-600 mt-0.5"></i>
                            <div class="text-sm text-gray-700 dark:text-gray-300">
                                <p class="font-semibold mb-1">E-posta Gönderildi</p>
                                <p class="text-xs">Sipariş detayları e-posta adresinize gönderilmiştir.</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Teslimat Bilgileri (Sadece adres) --}}
                @if($order && $order->shipping_address)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class="fa-solid fa-location-dot text-green-600 mr-2"></i>
                        Teslimat Adresi
                    </h2>

                    <div class="space-y-4">
                        <div>
                            <p class="text-sm font-semibold text-gray-600 dark:text-gray-400 mb-2">Adres:</p>
                            <p class="text-gray-900 dark:text-white">{{ $order->shipping_address }}</p>
                            @if($order->shipping_district && $order->shipping_city)
                                <p class="text-gray-900 dark:text-white mt-1">
                                    {{ $order->shipping_district }}, {{ $order->shipping_city }}
                                    @if($order->shipping_postal_code)
                                        - {{ $order->shipping_postal_code }}
                                    @endif
                                </p>
                            @endif
                        </div>

                        @if($order->customer_name)
                        <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <p class="text-sm text-gray-600 dark:text-gray-400">Alıcı: <span class="font-semibold text-gray-900 dark:text-white">{{ $order->customer_name }}</span></p>
                            @if($order->customer_phone)
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Tel: <span class="font-semibold text-gray-900 dark:text-white">{{ $order->customer_phone }}</span></p>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>

            {{-- Sipariş İçeriği (Ürünler) --}}
            @if($order && $order->items->count() > 0)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                    <i class="fa-solid fa-box-open text-blue-600 mr-2"></i>
                    Sipariş İçeriği
                </h2>

                <div class="space-y-4">
                    @foreach($order->items as $item)
                        <div class="flex items-center space-x-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            @if($item->product && $item->product->firstMedia)
                                <img src="{{ thumb($item->product->firstMedia, 80, 80) }}"
                                     alt="{{ $item->product_name }}"
                                     class="w-20 h-20 object-cover rounded-lg"
                                     loading="lazy">
                            @else
                                <div class="w-20 h-20 bg-gray-200 dark:bg-gray-600 rounded-lg flex items-center justify-center">
                                    <i class="fa-solid fa-image text-gray-400 text-2xl"></i>
                                </div>
                            @endif

                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900 dark:text-white">{{ $item->product_name }}</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Adet: {{ $item->quantity }}</p>
                            </div>

                            <div class="text-right">
                                <p class="text-lg font-bold text-gray-900 dark:text-white">
                                    {{ number_format(round($item->total), 0, ',', '.') }}
                                    <i class="fa-solid fa-turkish-lira text-sm ml-0.5"></i>
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Eylemler (Action Buttons) --}}
            <div class="grid md:grid-cols-3 gap-4">
                <a href="{{ route('shop.index') }}"
                   class="block p-6 bg-white dark:bg-gray-800 rounded-xl shadow-lg hover:shadow-xl transition-shadow text-center group">
                    <i class="fa-solid fa-home text-3xl text-blue-600 mb-3 group-hover:scale-110 transition-transform"></i>
                    <p class="font-semibold text-gray-900 dark:text-white">Ana Sayfaya Dön</p>
                </a>

                @if($order)
                    <a href="{{ route('shop.orders.show', $order->order_id) }}"
                       class="block p-6 bg-white dark:bg-gray-800 rounded-xl shadow-lg hover:shadow-xl transition-shadow text-center group">
                        <i class="fa-solid fa-list text-3xl text-green-600 mb-3 group-hover:scale-110 transition-transform"></i>
                        <p class="font-semibold text-gray-900 dark:text-white">Siparişim</p>
                    </a>
                @endif

                <button onclick="window.print()"
                        class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow-lg hover:shadow-xl transition-shadow text-center group">
                    <i class="fa-solid fa-print text-3xl text-purple-600 mb-3 group-hover:scale-110 transition-transform"></i>
                    <p class="font-semibold text-gray-900 dark:text-white">Yazdır</p>
                </button>
            </div>

            {{-- Yardım --}}
            <div class="mt-6 bg-gradient-to-r from-blue-600 to-blue-700 rounded-xl shadow-lg p-6 text-white text-center">
                <h3 class="text-xl font-bold mb-2">Yardıma mı ihtiyacınız var?</h3>
                <p class="text-sm text-blue-100 mb-4">Müşteri hizmetlerimiz size yardımcı olmaktan mutluluk duyar</p>
                <div class="flex justify-center space-x-4">
                    @if(setting('contact_phone'))
                        <a href="tel:{{ setting('contact_phone') }}"
                           class="px-6 py-2 bg-white text-blue-600 font-semibold rounded-lg hover:bg-blue-50 transition-colors">
                            <i class="fa-solid fa-phone mr-2"></i>
                            {{ setting('contact_phone') }}
                        </a>
                    @endif

                    @if(setting('whatsapp_number'))
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', setting('whatsapp_number')) }}"
                           class="px-6 py-2 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-colors"
                           target="_blank">
                            <i class="fa-brands fa-whatsapp mr-2"></i>
                            WhatsApp
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('themes.ixtif.layouts.app')

@section('title', 'Sipariş Onaylandı - ' . $order->order_number)

@section('content')
<div class="container mx-auto px-4 py-12">
    <div class="max-w-4xl mx-auto">

        {{-- Başarı Mesajı --}}
        <div class="bg-green-50 dark:bg-green-900/20 border-2 border-green-200 dark:border-green-700 rounded-xl p-8 mb-6 text-center">
            <div class="w-20 h-20 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fa-solid fa-check text-white text-4xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                Siparişiniz Alındı!
            </h1>
            <p class="text-lg text-gray-700 dark:text-gray-300 mb-4">
                Sipariş numaranız: <strong class="text-blue-600 dark:text-blue-400">{{ $order->order_number }}</strong>
            </p>
            @if($order->customer_email)
            <p class="text-sm text-gray-600 dark:text-gray-400">
                📧 <strong>{{ $order->customer_email }}</strong> adresinize sipariş onayı gönderilecektir.
            </p>
            @endif
        </div>

        {{-- Sipariş Özeti --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">
                Sipariş Detayları
            </h2>

            <div class="space-y-4">
                @foreach($order->items as $item)
                <div class="flex items-center gap-4 border-b border-gray-200 dark:border-gray-700 pb-4">
                    <div class="flex-1">
                        <h3 class="font-semibold text-gray-900 dark:text-white">
                            {{ $item->product_title }}
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Adet: {{ $item->quantity }} × {{ number_format($item->unit_price, 2, ',', '.') }} ₺
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-gray-900 dark:text-white">
                            {{ number_format($item->subtotal, 2, ',', '.') }} ₺
                        </p>
                    </div>
                </div>
                @endforeach

                {{-- Toplam --}}
                <div class="flex justify-between items-center text-lg font-bold text-gray-900 dark:text-white pt-4">
                    <span>TOPLAM:</span>
                    <span class="text-blue-600 dark:text-blue-400">
                        {{ number_format($order->total_amount, 2, ',', '.') }} ₺
                    </span>
                </div>
            </div>
        </div>

        {{-- İletişim Bilgileri --}}
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-xl p-6 mb-6">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                <i class="fa-solid fa-headset mr-2 text-blue-600 dark:text-blue-400"></i>
                Destek ve İletişim
            </h3>
            <p class="text-sm text-gray-700 dark:text-gray-300 mb-4">
                Siparişinizle ilgili herhangi bir sorunuz mu var? Size yardımcı olmaktan mutluluk duyarız.
            </p>

            <div class="flex flex-col sm:flex-row gap-4">
                <a href="{{ whatsapp_link(null, 'Sipariş No: ' . $order->order_number . ' hakkında bilgi almak istiyorum') }}"
                   target="_blank"
                   class="flex-1 bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-6 rounded-lg text-center transition-all flex items-center justify-center gap-2">
                    <i class="fa-brands fa-whatsapp text-xl"></i>
                    <span>WhatsApp ile İletişim</span>
                </a>

                <a href="tel:02167553555"
                   class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg text-center transition-all flex items-center justify-center gap-2">
                    <i class="fa-solid fa-phone"></i>
                    <span>Bizi Arayın</span>
                </a>
            </div>
        </div>

        {{-- Anasayfaya / Siparişlerime Dön --}}
        <div class="text-center space-y-3">
            @auth
            <a href="{{ route('account.orders') }}"
               class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-bold py-3 px-8 rounded-lg transition-all transform hover:scale-105 shadow-lg">
                <i class="fa-solid fa-list-check"></i>
                <span>Siparişlerimi Görüntüle</span>
            </a>
            @endauth

            <div>
                <a href="{{ route('shop.index') }}"
                   class="inline-flex items-center gap-2 text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                    <i class="fa-solid fa-shopping-bag"></i>
                    <span>Alışverişe Devam Et</span>
                </a>
            </div>
        </div>

    </div>
</div>
@endsection

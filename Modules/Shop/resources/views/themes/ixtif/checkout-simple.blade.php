@extends('themes.ixtif.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-12">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8 text-center">
            <div class="mb-6">
                <i class="fa-solid fa-construction text-6xl text-yellow-500 mb-4"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">
                Ödeme Sayfası Yapım Aşamasında
            </h1>
            <p class="text-gray-600 dark:text-gray-400 mb-8">
                Checkout sayfamız şu anda geliştirilme aşamasındadır.
                Sipariş vermek için lütfen bizimle iletişime geçin.
            </p>

            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ whatsapp_link(null, 'Sipariş vermek istiyorum') }}"
                   target="_blank"
                   class="inline-flex items-center justify-center gap-2 bg-green-500 hover:bg-green-600 text-white font-bold py-4 px-8 rounded-lg transition-all transform hover:scale-105">
                    <i class="fa-brands fa-whatsapp text-2xl"></i>
                    <span>WhatsApp ile Sipariş Ver</span>
                </a>

                <a href="tel:02167553555"
                   class="inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 px-8 rounded-lg transition-all transform hover:scale-105">
                    <i class="fa-solid fa-phone text-xl"></i>
                    <span>Telefon ile İletişim</span>
                </a>
            </div>

            <div class="mt-8 pt-8 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('shop.cart') }}"
                   class="inline-flex items-center gap-2 text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                    <i class="fa-solid fa-arrow-left"></i>
                    <span>Sepete Geri Dön</span>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

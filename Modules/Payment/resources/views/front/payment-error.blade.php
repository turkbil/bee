@extends($layoutPath)

@section('title', 'Ödeme Hatası')

@section('content')
<div class="min-h-[60vh] bg-gray-50 dark:bg-gray-900 py-12">
    <div class="max-w-2xl mx-auto px-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-8 text-center">
            {{-- Error Icon --}}
            <div class="w-20 h-20 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fa-solid fa-exclamation-triangle text-3xl text-red-600 dark:text-red-400"></i>
            </div>

            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">Ödeme Hatası</h1>

            <p class="text-gray-600 dark:text-gray-400 mb-6">
                {{ $error ?? 'Ödeme işlemi sırasında bir hata oluştu.' }}
            </p>

            {{-- Butonlar --}}
            <div class="space-y-3">
                <a href="{{ route('cart.checkout') }}"
                   class="block w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-xl transition-colors">
                    <i class="fa-solid fa-arrow-left mr-2"></i> Sepete Dön
                </a>

                <a href="{{ url('/') }}"
                   class="block w-full bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 font-medium py-3 rounded-xl transition-colors">
                    <i class="fa-solid fa-home mr-2"></i> Ana Sayfa
                </a>
            </div>
        </div>

        {{-- Yardım --}}
        <div class="mt-6 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-xl p-4 text-center">
            <p class="text-sm text-yellow-800 dark:text-yellow-300">
                <i class="fa-solid fa-info-circle mr-2"></i>
                Sorun devam ederse lütfen bizimle iletişime geçin.
            </p>
        </div>
    </div>
</div>
@endsection

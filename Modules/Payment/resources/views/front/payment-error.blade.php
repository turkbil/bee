@extends($layoutPath)

@section('title', 'Ödeme Hatası')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-orange-50 via-white to-red-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 py-12 px-4">
    <div class="max-w-3xl mx-auto">

        {{-- Error Header --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-24 h-24 bg-gradient-to-br from-orange-500 to-red-600 rounded-full mb-6 shadow-xl">
                <i class="fa-solid fa-exclamation-triangle text-4xl text-white"></i>
            </div>
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-3">Bir Hata Oluştu</h1>
            <p class="text-lg text-gray-600 dark:text-gray-400">Ödeme işlemi sırasında beklenmeyen bir hata meydana geldi.</p>
        </div>

        {{-- Error Card --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden mb-6">

            {{-- Error Message --}}
            <div class="p-8">
                <div class="bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded-xl p-6 mb-6">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0 w-12 h-12 bg-orange-100 dark:bg-orange-900/30 rounded-xl flex items-center justify-center">
                            <i class="fa-solid fa-exclamation-circle text-xl text-orange-600 dark:text-orange-400"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-orange-900 dark:text-orange-300 mb-2">Hata Mesajı</h3>
                            <p class="text-orange-800 dark:text-orange-400">
                                {{ $error ?? 'Ödeme işlemi sırasında bir hata oluştu. Lütfen tekrar deneyin.' }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Info --}}
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0 w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                            <i class="fa-solid fa-lightbulb text-lg text-blue-600 dark:text-blue-400"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-bold text-blue-900 dark:text-blue-300 mb-2">Ne Yapmalıyım?</h4>
                            <ul class="space-y-1 text-sm text-blue-800 dark:text-blue-400">
                                <li>✓ Birkaç dakika bekleyip tekrar deneyin</li>
                                <li>✓ İnternet bağlantınızı kontrol edin</li>
                                <li>✓ Sorun devam ederse destek ekibimizle iletişime geçin</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <a href="{{ route('cart.checkout') }}"
           class="flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-xl transition-all shadow-lg hover:shadow-xl mb-4">
            <i class="fa-solid fa-arrow-left text-xl"></i>
            <span>Sepete Dön</span>
        </a>

        @if(setting('contact_whatsapp_1'))
            @php
                $waNumber = preg_replace('/[^0-9]/', '', setting('contact_whatsapp_1'));
                if (str_starts_with($waNumber, '0')) {
                    $waNumber = '90' . substr($waNumber, 1);
                } elseif (!str_starts_with($waNumber, '90')) {
                    $waNumber = '90' . $waNumber;
                }
            @endphp
            <a href="https://wa.me/{{ $waNumber }}?text={{ urlencode('Merhaba, ödeme sırasında hata aldım. Yardımcı olabilir misiniz?') }}"
               target="_blank"
               class="flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white font-bold py-4 rounded-xl transition-all shadow-lg hover:shadow-xl">
                <i class="fa-brands fa-whatsapp text-xl"></i>
                <span>WhatsApp Desteği</span>
            </a>
        @endif

    </div>
</div>
@endsection

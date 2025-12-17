@extends($layoutPath)

@section('title', 'Ödeme Başarısız')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-red-50 via-white to-orange-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 py-12 px-4">
    <div class="max-w-3xl mx-auto">

        {{-- Failed Header --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-24 h-24 bg-gradient-to-br from-red-500 to-rose-600 rounded-full mb-6 shadow-xl">
                <i class="fa-solid fa-xmark text-4xl text-white"></i>
            </div>
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-3">Ödeme Başarısız</h1>
            <p class="text-lg text-gray-600 dark:text-gray-400">Ödemeniz tamamlanamadı. Endişelenmeyin, hesabınızdan para çekilmedi.</p>
        </div>

        {{-- Payment Info Card --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden mb-6">

            {{-- Payment Header --}}
            <div class="bg-gradient-to-r from-red-500 to-rose-600 px-6 py-4 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90 mb-1">İşlem Numarası</p>
                        <p class="text-xl font-bold font-mono">{{ $payment->transaction_id ?? 'N/A' }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm opacity-90 mb-1">Tutar</p>
                        <p class="text-2xl font-bold">{{ number_format($payment->amount, 2, ',', '.') }} ₺</p>
                    </div>
                </div>
            </div>

            {{-- Failure Reason --}}
            @if($payment->gateway_response && isset($payment->gateway_response['failed_reason_msg']))
            <div class="p-6 bg-red-50 dark:bg-red-900/20 border-b border-red-100 dark:border-red-800">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-xl flex items-center justify-center">
                        <i class="fa-solid fa-exclamation-triangle text-xl text-red-600 dark:text-red-400"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-red-900 dark:text-red-300 mb-2">Hata Nedeni</h3>
                        <p class="text-red-800 dark:text-red-400">{{ $payment->gateway_response['failed_reason_msg'] }}</p>
                    </div>
                </div>
            </div>
            @endif

            {{-- Info Section --}}
            <div class="p-6">
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0 w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                            <i class="fa-solid fa-info-circle text-lg text-blue-600 dark:text-blue-400"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-bold text-blue-900 dark:text-blue-300 mb-2">Ne Oldu?</h4>
                            <ul class="space-y-1 text-sm text-blue-800 dark:text-blue-400">
                                <li>✓ Ödeme işlemi tamamlanamadı</li>
                                <li>✓ Hesabınızdan para çekilmedi</li>
                                <li>✓ Tekrar deneyebilirsiniz</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        @if($order)
        <a href="{{ route('cart.checkout') }}"
           class="flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-xl transition-all shadow-lg hover:shadow-xl mb-4">
            <i class="fa-solid fa-rotate-right text-xl"></i>
            <span>Tekrar Dene</span>
        </a>
        @endif

        @if($contactWhatsapp ?? false)
            @php
                $waNumber = preg_replace('/[^0-9]/', '', $contactWhatsapp);
                if (str_starts_with($waNumber, '0')) {
                    $waNumber = '90' . substr($waNumber, 1);
                } elseif (!str_starts_with($waNumber, '90')) {
                    $waNumber = '90' . $waNumber;
                }
            @endphp
            <a href="https://wa.me/{{ $waNumber }}?text={{ urlencode('Merhaba, ödeme hatası aldım. Yardımcı olabilir misiniz?') }}"
               target="_blank"
               class="flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white font-bold py-4 rounded-xl transition-all shadow-lg hover:shadow-xl">
                <i class="fa-brands fa-whatsapp text-xl"></i>
                <span>WhatsApp Desteği</span>
            </a>
        @endif

    </div>
</div>
@endsection

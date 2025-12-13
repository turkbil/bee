<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-4xl mx-auto px-4">
        @if($error)
            {{-- Hata durumu --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8 text-center">
                <div class="mb-4">
                    <i class="fa-solid fa-exclamation-triangle text-5xl text-red-500"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">
                    Ödeme Hatası
                </h2>
                <p class="text-gray-600 dark:text-gray-400 mb-6">
                    {{ $error }}
                </p>
                <a href="{{ route('cart.checkout') }}"
                   class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                    <i class="fa-solid fa-arrow-left"></i>
                    <span>Sepete Dön</span>
                </a>
            </div>
        @elseif($paymentIframeUrl)
            {{-- Ödeme iframe --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                {{-- Header --}}
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-lock text-2xl text-white"></i>
                            <div>
                                <h1 class="text-xl font-bold text-white">
                                    Güvenli Ödeme
                                </h1>
                                <p class="text-sm text-blue-100">
                                    Sipariş No: <span class="font-semibold">{{ $orderNumber }}</span>
                                </p>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm text-blue-100">Toplam Tutar</div>
                            <div class="text-2xl font-bold text-white">
                                {{ number_format($order->total_amount, 2) }} {{ $order->currency }}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- PayTR Iframe --}}
                <div class="p-4 bg-gray-100 dark:bg-gray-900">
                    <div class="bg-white dark:bg-gray-800 rounded-lg overflow-hidden" style="height: 600px;">
                        <iframe
                            src="{{ $paymentIframeUrl }}"
                            id="paytriframe"
                            class="w-full h-full border-0"
                            frameborder="0"
                            scrolling="yes"
                            loading="eager">
                        </iframe>
                    </div>
                </div>

                {{-- Footer Info --}}
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                        <i class="fa-solid fa-shield-check text-green-600"></i>
                        <span>256-bit SSL ile güvenli ödeme</span>
                        <span class="mx-2">•</span>
                        <i class="fa-solid fa-credit-card text-blue-600"></i>
                        <span>Tüm banka kartları kabul edilir</span>
                    </div>
                </div>
            </div>

            {{-- Güvenlik Notları --}}
            <div class="mt-6 grid md:grid-cols-3 gap-4 text-center">
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow">
                    <i class="fa-solid fa-lock text-2xl text-blue-600 mb-2"></i>
                    <div class="text-sm font-semibold text-gray-900 dark:text-white">SSL Güvenliği</div>
                    <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">256-bit şifreleme</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow">
                    <i class="fa-solid fa-shield-halved text-2xl text-green-600 mb-2"></i>
                    <div class="text-sm font-semibold text-gray-900 dark:text-white">3D Secure</div>
                    <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">Güvenli doğrulama</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow">
                    <i class="fa-solid fa-headset text-2xl text-purple-600 mb-2"></i>
                    <div class="text-sm font-semibold text-gray-900 dark:text-white">7/24 Destek</div>
                    <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">Her zaman yanınızdayız</div>
                </div>
            </div>
        @else
            {{-- Yükleniyor --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8 text-center">
                <div class="mb-4">
                    <i class="fa-solid fa-spinner fa-spin text-5xl text-blue-600"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">
                    Ödeme Sayfası Hazırlanıyor
                </h2>
                <p class="text-gray-600 dark:text-gray-400">
                    Lütfen bekleyin...
                </p>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    // PayTR iframe event listener (ödeme sonucu)
    window.addEventListener('message', function(event) {
        if (event.data && event.data.paytr_status) {
            console.log('💳 PayTR Status:', event.data.paytr_status);

            if (event.data.paytr_status === 'success') {
                // Başarılı ödeme - success sayfasına yönlendir
                window.location.href = '{{ route("payment.success") }}';
            } else if (event.data.paytr_status === 'failed') {
                // Başarısız ödeme - checkout'a geri dön
                window.location.href = '{{ route("cart.checkout") }}?payment=failed';
            }
        }
    }, false);
</script>
@endpush

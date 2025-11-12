@extends('themes.ixtif.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-12">
    <div class="max-w-5xl mx-auto">
        {{-- Header --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                        <i class="fa-solid fa-credit-card text-blue-600 mr-2"></i>
                        Güvenli Ödeme
                    </h1>
                    <p class="text-gray-600 dark:text-gray-400">
                        Sipariş No: <span class="font-mono font-semibold">{{ $orderNumber }}</span>
                    </p>
                    @if($itemCount > 0)
                        <p class="text-sm text-gray-500 dark:text-gray-500 mt-1">
                            <i class="fa-solid fa-box text-xs mr-1"></i>
                            {{ $itemCount }} Ürün
                        </p>
                    @endif
                </div>
                <div class="text-right">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 mb-2">
                        <i class="fa-solid fa-lock mr-1"></i>
                        256-bit SSL
                    </span>
                    @if($amount > 0)
                        <div class="mt-3">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Ödenecek Tutar</p>
                            <p class="text-3xl font-bold text-blue-600 dark:text-blue-400">
                                {{ number_format(round($amount), 0, ',', '.') }}
                                <i class="fa-solid fa-turkish-lira text-2xl ml-1"></i>
                            </p>
                            @if($subtotal > 0 && $tax > 0)
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    (KDV Dahil)
                                </p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- PayTR iframe --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
            <div class="p-4 bg-blue-50 dark:bg-blue-900/20 border-b border-blue-200 dark:border-blue-700">
                <p class="text-sm text-blue-800 dark:text-blue-200 flex items-center">
                    <i class="fa-solid fa-info-circle mr-2"></i>
                    Kart bilgilerinizi aşağıdaki güvenli formdan girebilirsiniz.
                </p>
            </div>

            {{-- iframe Container --}}
            <div class="relative" style="min-height: 800px;">
                <iframe
                    id="paytr-iframe"
                    src="{{ $iframeUrl }}"
                    class="w-full border-0"
                    style="height: 100vh; min-height: 800px;"
                    frameborder="0"
                    scrolling="yes">
                </iframe>

                {{-- Loading Overlay --}}
                <div id="iframe-loading" class="absolute inset-0 bg-white dark:bg-gray-800 flex items-center justify-center">
                    <div class="text-center">
                        <i class="fa-solid fa-spinner fa-spin text-4xl text-blue-600 mb-4"></i>
                        <p class="text-gray-600 dark:text-gray-400">Ödeme formu yükleniyor...</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Güvenlik Bilgisi --}}
        <div class="mt-6 text-center">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                <i class="fa-solid fa-shield-halved text-green-600 mr-1"></i>
                Ödemeniz PayTR güvenli ödeme sistemi ile korunmaktadır.
            </p>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const iframe = document.getElementById('paytr-iframe');
    const loading = document.getElementById('iframe-loading');
    
    // iframe yüklendiğinde loading'i kaldır
    iframe.addEventListener('load', function() {
        loading.style.display = 'none';
    });
    
    // 5 saniye sonra loading'i kaldır (fallback)
    setTimeout(function() {
        loading.style.display = 'none';
    }, 5000);
});
</script>
@endsection

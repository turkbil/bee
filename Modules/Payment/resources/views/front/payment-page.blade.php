@extends($layoutPath)

@php
    $currentLocale = app()->getLocale();
    $defaultLocale = get_tenant_default_locale();
    $homeUrl = $currentLocale === $defaultLocale ? url('/') : url('/' . $currentLocale);

    // Settings'den site bilgileri
    $siteTitle = setting('site_title', setting('site_name', config('app.name')));

    // İletişim bilgileri (tenant-aware)
    $contactPhone = setting('contact_phone_1');
    $contactWhatsapp = setting('contact_whatsapp_1');
@endphp

@section('title', $siteTitle . ' - Ödeme')

@push('styles')
<style>
    #paytriframe {
        min-height: 600px;
        height: 800px;
    }
    @media (max-width: 768px) {
        #paytriframe {
            min-height: 500px;
        }
    }
</style>
@endpush

@section('content')
<div class="bg-gray-100 dark:bg-gray-900 min-h-[60vh] py-6">
    <div class="max-w-4xl mx-auto px-4">
        {{-- Ödeme Kartı --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm overflow-hidden">
            {{-- Üst Bar --}}
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center">
                        <i class="fa-solid fa-credit-card text-white"></i>
                    </div>
                    <div>
                        <h1 class="font-semibold text-gray-900 dark:text-white">Ödeme</h1>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $orderNumber }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="text-right">
                        <p class="text-xs text-gray-500 dark:text-gray-400">Toplam</p>
                        <p class="text-xl font-bold text-gray-900 dark:text-white">{{ number_format($order->total_amount, 2, ',', '.') }} ₺</p>
                    </div>
                    <div class="flex items-center gap-1 text-green-600 dark:text-green-400 text-sm">
                        <i class="fa-solid fa-lock text-xs"></i>
                        <span class="hidden sm:inline text-xs">SSL</span>
                    </div>
                </div>
            </div>

            {{-- Iframe --}}
            <iframe
                src="{{ $paymentIframeUrl }}"
                id="paytriframe"
                class="w-full border-0"
                frameborder="0"
                scrolling="auto">
            </iframe>
        </div>

        {{-- Alt Bilgi --}}
        <div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-4">
            <a href="{{ route('cart.checkout') }}" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 text-sm flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i>
                Geri Dön
            </a>

            @if($contactPhone || $contactWhatsapp)
                <div class="flex items-center gap-4">
                    @if($contactPhone)
                        <a href="tel:{{ preg_replace('/[^0-9+]/', '', $contactPhone) }}" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 text-sm flex items-center gap-2">
                            <i class="fa-solid fa-phone"></i>
                            <span class="hidden sm:inline">{{ $contactPhone }}</span>
                        </a>
                    @endif
                    @if($contactWhatsapp)
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $contactWhatsapp) }}" target="_blank" class="text-green-600 dark:text-green-400 hover:text-green-700 text-sm flex items-center gap-2">
                            <i class="fa-brands fa-whatsapp text-lg"></i>
                            <span class="hidden sm:inline">WhatsApp</span>
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    // PayTR iframe auto height & status
    window.addEventListener('message', function(event) {
        if (event.data && event.data.height) {
            document.getElementById('paytriframe').style.height = event.data.height + 'px';
        }
        if (event.data && event.data.paytr_status) {
            if (event.data.paytr_status === 'success') {
                window.location.href = '{{ route("payment.success") }}';
            } else if (event.data.paytr_status === 'failed') {
                window.location.href = '{{ route("cart.checkout") }}?payment=failed';
            }
        }
    }, false);
</script>
@endsection

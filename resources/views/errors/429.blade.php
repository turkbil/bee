@php
    // Tenant-aware tema seçimi
    $currentDomain = request()->getHost();

    // Domain'e göre tema belirle
    if (str_contains($currentDomain, 'muzibu')) {
        $theme = 'muzibu';
    } elseif (str_contains($currentDomain, 'ixtif')) {
        $theme = 'ixtif';
    } else {
        // Varsayılan: simple (minimal, bağımsız tema)
        $theme = 'simple';
    }
@endphp

@extends("themes.{$theme}.layouts.app")

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-orange-50 to-red-50 dark:from-gray-900 dark:to-gray-800 px-4 py-16">
    <div class="max-w-2xl w-full text-center">
        {{-- 429 Icon/Animation --}}
        <div class="mb-8">
            <div class="inline-flex items-center justify-center w-32 h-32 bg-gradient-to-br from-orange-500 to-red-600 rounded-full shadow-2xl animate-pulse">
                <i class="fas fa-hourglass-half text-6xl text-white"></i>
            </div>
        </div>

        {{-- Title --}}
        <h1 class="text-4xl md:text-5xl font-black text-gray-900 dark:text-white mb-4">
            {{ __('İşlem Limiti Aşıldı') }}
        </h1>

        {{-- Description --}}
        <p class="text-lg md:text-xl text-gray-600 dark:text-gray-400 mb-8">
            {{ __('Çok hızlı işlem yaptınız. Lütfen birkaç dakika bekleyip tekrar deneyin.') }}
        </p>

        {{-- Timer Countdown (Optional - Pure CSS Animation) --}}
        <div class="mb-8">
            <div class="inline-flex items-center gap-3 px-6 py-4 bg-white dark:bg-gray-800 rounded-xl shadow-lg">
                <i class="fas fa-clock text-orange-500 text-2xl"></i>
                <div class="text-left">
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Bekleme Süresi') }}</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">~5 {{ __('dakika') }}</p>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="flex flex-col sm:flex-row gap-4 justify-center mb-8">
            <button
                onclick="window.history.back()"
                class="px-6 py-3 bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-white rounded-xl font-semibold hover:bg-gray-300 dark:hover:bg-gray-600 transition-all"
            >
                <i class="fas fa-arrow-left mr-2"></i>
                {{ __('Geri Dön') }}
            </button>

            <a
                href="{{ url('/') }}"
                class="px-6 py-3 bg-gradient-to-r from-orange-500 to-red-600 text-white rounded-xl font-semibold hover:from-orange-600 hover:to-red-700 transition-all shadow-lg"
            >
                <i class="fas fa-home mr-2"></i>
                {{ __('Ana Sayfaya Git') }}
            </a>
        </div>

        {{-- Info Box --}}
        <div class="max-w-lg mx-auto bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-6">
            <div class="flex items-start gap-3">
                <i class="fas fa-info-circle text-blue-600 dark:text-blue-400 text-xl mt-1"></i>
                <div class="text-left">
                    <h3 class="font-semibold text-blue-900 dark:text-blue-300 mb-2">
                        {{ __('Neden Bu Mesajı Görüyorum?') }}
                    </h3>
                    <p class="text-sm text-blue-800 dark:text-blue-400">
                        {{ __('Kısa sürede çok fazla işlem yapıldığını tespit ettik. Bu, spam ve otomatik botlardan korunmak için uygulanan bir güvenlik önlemidir. Birkaç dakika bekledikten sonra normal şekilde devam edebilirsiniz.') }}
                    </p>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- Auto Reload After 5 Minutes (Optional) --}}
<script>
    // 5 dakika sonra otomatik yeniden yükle (isteğe bağlı)
    // setTimeout(() => {
    //     window.location.reload();
    // }, 5 * 60 * 1000);
</script>
@endsection

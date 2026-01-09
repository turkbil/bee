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
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-red-50 to-pink-50 dark:from-gray-900 dark:to-gray-800 px-4 py-16">
    <div class="max-w-2xl w-full text-center">
        {{-- 500 Icon/Animation --}}
        <div class="mb-8">
            <div class="inline-flex items-center justify-center w-32 h-32 bg-gradient-to-br from-red-500 to-pink-600 rounded-full shadow-2xl animate-pulse">
                <i class="fas fa-server text-6xl text-white"></i>
            </div>
        </div>

        {{-- Title --}}
        <h1 class="text-4xl md:text-5xl font-black text-gray-900 dark:text-white mb-4">
            {{ __('Bir Sorun Oluştu') }}
        </h1>

        {{-- Description --}}
        <p class="text-lg md:text-xl text-gray-600 dark:text-gray-400 mb-4">
            {{ __('Sunucuda beklenmeyen bir hata oluştu.') }}
        </p>

        <p class="text-base text-gray-500 dark:text-gray-500 mb-8">
            {{ __('Teknik ekibimiz otomatik olarak bilgilendirildi ve sorunu çözmek için çalışıyor.') }}
        </p>

        {{-- Info Box --}}
        <div class="max-w-lg mx-auto bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-6 mb-8">
            <div class="flex items-start gap-3">
                <i class="fas fa-lightbulb text-blue-600 dark:text-blue-400 text-xl mt-1"></i>
                <div class="text-left">
                    <h3 class="font-semibold text-blue-900 dark:text-blue-300 mb-2">
                        {{ __('Ne Yapabilirsiniz?') }}
                    </h3>
                    <ul class="space-y-2 text-sm text-blue-800 dark:text-blue-400">
                        <li class="flex items-start gap-2">
                            <i class="fas fa-check-circle mt-0.5"></i>
                            <span>{{ __('Birkaç dakika bekleyip tekrar deneyin') }}</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fas fa-check-circle mt-0.5"></i>
                            <span>{{ __('Sayfayı yenileyin (F5)') }}</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fas fa-check-circle mt-0.5"></i>
                            <span>{{ __('Ana sayfaya gidip başka bir sayfayı deneyin') }}</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fas fa-check-circle mt-0.5"></i>
                            <span>{{ __('Sorun devam ederse destek ekibimizle iletişime geçin') }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="flex flex-col sm:flex-row gap-4 justify-center mb-8">
            <button
                onclick="window.location.reload()"
                class="px-6 py-3 bg-gradient-to-r from-red-500 to-pink-600 text-white rounded-xl font-semibold hover:from-red-600 hover:to-pink-700 transition-all shadow-lg"
            >
                <i class="fas fa-rotate-right mr-2"></i>
                {{ __('Sayfayı Yenile') }}
            </button>

            <button
                onclick="window.history.back()"
                class="px-6 py-3 bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-white rounded-xl font-semibold hover:bg-gray-300 dark:hover:bg-gray-600 transition-all"
            >
                <i class="fas fa-arrow-left mr-2"></i>
                {{ __('Geri Dön') }}
            </button>

            <a
                href="{{ url('/') }}"
                class="px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-xl font-semibold hover:from-blue-600 hover:to-purple-700 transition-all shadow-lg"
            >
                <i class="fas fa-home mr-2"></i>
                {{ __('Ana Sayfaya Git') }}
            </a>
        </div>

        {{-- Support Info Box --}}
        <div class="max-w-lg mx-auto bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl p-6">
            <div class="flex items-start gap-3">
                <i class="fas fa-headset text-slate-600 dark:text-slate-400 text-xl mt-1"></i>
                <div class="text-left">
                    <h3 class="font-semibold text-white mb-2">
                        {{ __('Yardıma mı İhtiyacınız Var?') }}
                    </h3>
                    <p class="text-sm text-slate-700 dark:text-slate-400 mb-3">
                        {{ __('Sorun devam ediyorsa, lütfen bizimle iletişime geçin. Size yardımcı olmaktan mutluluk duyarız.') }}
                    </p>
                    <div class="flex flex-wrap gap-3">
                        @if(setting('site_email'))
                        <a href="mailto:{{ setting('site_email') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-900 dark:text-slate-100 rounded-lg text-sm font-medium transition-all">
                            <i class="fas fa-envelope"></i>
                            <span>{{ __('E-posta Gönder') }}</span>
                        </a>
                        @endif
                        @if(setting('site_phone'))
                        <a href="tel:{{ setting('site_phone') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-900 dark:text-slate-100 rounded-lg text-sm font-medium transition-all">
                            <i class="fas fa-phone"></i>
                            <span>{{ __('Bizi Arayın') }}</span>
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Auto reload after 30 seconds (optional) --}}
<script>
    // Otomatik yenileme (isteğe bağlı, gerekirse aktif edilebilir)
    // setTimeout(() => {
    //     window.location.reload();
    // }, 30000);
</script>
@endsection

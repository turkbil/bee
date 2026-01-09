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
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-amber-50 to-yellow-50 dark:from-gray-900 dark:to-gray-800 px-4 py-16">
    <div class="max-w-2xl w-full text-center">
        {{-- 419 Icon/Animation --}}
        <div class="mb-8">
            <div class="inline-flex items-center justify-center w-32 h-32 bg-gradient-to-br from-amber-500 to-yellow-600 rounded-full shadow-2xl animate-pulse">
                <i class="fas fa-clock-rotate-left text-6xl text-white"></i>
            </div>
        </div>

        {{-- Title --}}
        <h1 class="text-4xl md:text-5xl font-black text-gray-900 dark:text-white mb-4">
            {{ __('Oturum Süresi Doldu') }}
        </h1>

        {{-- Description --}}
        <p class="text-lg md:text-xl text-gray-600 dark:text-gray-400 mb-8">
            {{ __('Formda çok uzun süre beklediniz. Güvenlik nedeniyle sayfayı yenilemeniz gerekiyor.') }}
        </p>

        {{-- Info Box --}}
        <div class="max-w-lg mx-auto bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-6 mb-8">
            <div class="flex items-start gap-3">
                <i class="fas fa-info-circle text-blue-600 dark:text-blue-400 text-xl mt-1"></i>
                <div class="text-left">
                    <h3 class="font-semibold text-blue-900 dark:text-blue-300 mb-2">
                        {{ __('Ne Yapmalıyım?') }}
                    </h3>
                    <ul class="space-y-2 text-sm text-blue-800 dark:text-blue-400">
                        <li class="flex items-start gap-2">
                            <i class="fas fa-check-circle mt-0.5"></i>
                            <span>{{ __('Sayfayı yenileyin (F5 veya yenile butonu)') }}</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fas fa-check-circle mt-0.5"></i>
                            <span>{{ __('Formu tekrar doldurun') }}</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fas fa-check-circle mt-0.5"></i>
                            <span>{{ __('İşleminize devam edin') }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="flex flex-col sm:flex-row gap-4 justify-center mb-8">
            <button
                onclick="window.location.reload()"
                class="px-6 py-3 bg-gradient-to-r from-amber-500 to-yellow-600 text-white rounded-xl font-semibold hover:from-amber-600 hover:to-yellow-700 transition-all shadow-lg"
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

        {{-- Warning Box --}}
        <div class="max-w-lg mx-auto bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl p-6">
            <div class="flex items-start gap-3">
                <i class="fas fa-exclamation-triangle text-amber-600 dark:text-amber-400 text-xl mt-1"></i>
                <div class="text-left">
                    <h3 class="font-semibold text-amber-900 dark:text-amber-300 mb-2">
                        {{ __('Neden Bu Oluyor?') }}
                    </h3>
                    <p class="text-sm text-amber-800 dark:text-amber-400">
                        {{ __('Güvenliğiniz için, formlar belirli bir süre sonra otomatik olarak geçersiz hale gelir. Bu, yetkisiz erişimi önlemek için uygulanan bir güvenlik önlemidir. Sayfayı yenilediğinizde form güncellenecek ve işleminize devam edebileceksiniz.') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

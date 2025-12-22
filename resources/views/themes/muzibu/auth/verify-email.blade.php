@extends('themes.muzibu.auth.layout')

@section('title', 'Email Doğrulama - Muzibu')

@section('content')
    <!-- Logo -->
    <div class="text-center mb-10">
        <a href="/" class="inline-block">
            @php
                // LogoService kullan - Settings'den logo çek
                $logoService = app(\App\Services\LogoService::class);
                $logos = $logoService->getLogos();

                $logoUrl = $logos['light_logo_url'] ?? null;
                $logoDarkUrl = $logos['dark_logo_url'] ?? null;
                $fallbackMode = $logos['fallback_mode'] ?? 'title_only';
                $siteTitle = $logos['site_title'] ?? setting('site_title', 'muzibu');
            @endphp

            @if($fallbackMode === 'both')
                {{-- Her iki logo da var - Dark mode'da otomatik değiş --}}
                <img src="{{ $logoUrl }}"
                     alt="{{ $siteTitle }}"
                     class="dark:hidden object-contain h-12 w-auto mx-auto"
                     title="{{ $siteTitle }}">
                <img src="{{ $logoDarkUrl }}"
                     alt="{{ $siteTitle }}"
                     class="hidden dark:block object-contain h-12 w-auto mx-auto"
                     title="{{ $siteTitle }}">
            @elseif($fallbackMode === 'light_only' && $logoUrl)
                {{-- Sadece light logo var --}}
                <img src="{{ $logoUrl }}"
                     alt="{{ $siteTitle }}"
                     class="object-contain h-12 w-auto mx-auto"
                     title="{{ $siteTitle }}">
            @elseif($fallbackMode === 'dark_only' && $logoDarkUrl)
                {{-- Sadece dark logo var --}}
                <img src="{{ $logoDarkUrl }}"
                     alt="{{ $siteTitle }}"
                     class="object-contain h-12 w-auto mx-auto"
                     title="{{ $siteTitle }}">
            @else
                {{-- Fallback: Gradient text logo --}}
                <span class="text-3xl font-bold bg-gradient-to-r from-mz-500 via-mz-600 to-mz-500 bg-clip-text text-transparent">
                    {{ $siteTitle }}
                </span>
            @endif
        </a>
    </div>

    <!-- Email Verification Icon -->
    <div class="flex justify-center mb-8">
        <div class="w-20 h-20 bg-gradient-to-br from-mz-500 to-mz-600 rounded-full flex items-center justify-center">
            <i class="fas fa-envelope-circle-check text-white text-3xl"></i>
        </div>
    </div>

    <!-- Title & Description -->
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-white mb-3">Email Adresinizi Doğrulayın</h1>
        <p class="text-dark-200 leading-relaxed">
            Kayıt olduğunuz için teşekkürler! Başlamadan önce, size gönderdiğimiz bağlantıya tıklayarak email adresinizi doğrulayabilir misiniz?
        </p>
    </div>

    <!-- Status Messages -->
    @if (session('status') == 'verification-link-sent')
        <div class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-xl">
            <div class="flex items-center gap-3 text-emerald-400 text-sm">
                <i class="fas fa-check-circle"></i>
                <span>Kayıt sırasında verdiğiniz email adresine yeni bir doğrulama bağlantısı gönderildi.</span>
            </div>
        </div>
    @endif

    <!-- Resend Info -->
    <div class="mb-6 p-4 bg-dark-700/50 border border-dark-500 rounded-xl">
        <p class="text-dark-200 text-sm text-center">
            <i class="fas fa-info-circle text-mz-400 mr-2"></i>
            Email'i almadıysanız, size yeni bir tane göndermekten mutluluk duyarız.
        </p>
    </div>

    <!-- Action Buttons -->
    <div class="flex flex-col sm:flex-row gap-3">
        <!-- Resend Verification Email -->
        <form method="POST" action="{{ route('verification.send') }}" class="flex-1">
            @csrf
            <button type="submit" class="w-full py-4 bg-gradient-to-r from-mz-500 to-mz-600 hover:from-mz-400 hover:to-mz-500 text-white font-semibold rounded-xl transition-all shadow-lg shadow-mz-500/25">
                <span class="flex items-center justify-center gap-2">
                    <i class="fas fa-paper-plane"></i>
                    Doğrulama Emailini Tekrar Gönder
                </span>
            </button>
        </form>

        <!-- Logout -->
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full sm:w-auto px-6 py-4 bg-dark-700 hover:bg-dark-600 text-white font-semibold rounded-xl transition-all border border-dark-500">
                <span class="flex items-center justify-center gap-2">
                    <i class="fas fa-sign-out-alt"></i>
                    Çıkış Yap
                </span>
            </button>
        </form>
    </div>

    <!-- Help Text -->
    <div class="mt-8 text-center">
        <p class="text-dark-300 text-sm">
            Hala email alamıyor musunuz?
            <a href="mailto:{{ setting('site_email', 'destek@muzibu.com') }}" class="text-mz-400 hover:text-mz-300 font-medium transition-colors">
                Destek ekibiyle iletişime geçin
            </a>
        </p>
    </div>
@endsection

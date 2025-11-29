@extends('themes.muzibu.auth.layout')

@section('title', 'Şifremi Unuttum - Muzibu')
@section('subtitle', 'Şifrenizi sıfırlayın')

@section('content')
    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Şifremi Unuttum</h2>
    <p class="text-gray-600 dark:text-gray-400 mb-6">
        E-posta adresinizi girin, size şifre sıfırlama bağlantısı gönderelim
    </p>

    <!-- Session Status -->
    @if (session('status'))
        <div class="mb-4 px-4 py-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 rounded-lg text-sm">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

        <!-- Email -->
        <div>
            <label for="email" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                E-posta Adresi
            </label>
            <input
                type="email"
                id="email"
                name="email"
                value="{{ old('email') }}"
                required
                autofocus
                class="w-full px-4 py-3 bg-gray-50 dark:bg-muzibu-gray border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-muzibu-coral focus:border-transparent dark:text-white transition-all @error('email') border-red-500 dark:border-red-500 @enderror"
                placeholder="ornek@email.com"
            >
            @error('email')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Submit Button -->
        <button
            type="submit"
            class="w-full py-3.5 bg-gradient-to-r from-muzibu-coral to-green-600 hover:from-muzibu-coral-light hover:to-green-500 text-white font-bold rounded-xl transition-all hover:scale-105 shadow-lg hover:shadow-xl"
        >
            <i class="fas fa-paper-plane mr-2"></i>
            Sıfırlama Bağlantısı Gönder
        </button>
    </form>
@endsection

@section('footer-links')
    <div class="text-white/80 dark:text-gray-400">
        Şifrenizi hatırladınız mı?
        <a href="{{ route('login') }}" class="font-semibold text-white dark:text-muzibu-coral-light hover:text-white/100 dark:hover:text-muzibu-coral transition-colors">
            Giriş Yapın
        </a>
    </div>
@endsection

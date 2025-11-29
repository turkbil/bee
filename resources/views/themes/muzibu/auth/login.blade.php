@extends('themes.muzibu.auth.layout')

@section('title', 'Giriş Yap - Muzibu')
@section('subtitle', 'Hesabınıza giriş yapın')

@section('content')
    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Hoş Geldiniz</h2>
    <p class="text-gray-600 dark:text-gray-400 mb-6">Hesabınıza giriş yapın</p>

    <!-- Session Status -->
    @if (session('status'))
        <div class="mb-4 px-4 py-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 rounded-lg text-sm">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
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
                autocomplete="username"
                class="w-full px-4 py-3 bg-gray-50 dark:bg-muzibu-gray border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-muzibu-coral focus:border-transparent dark:text-white transition-all @error('email') border-red-500 dark:border-red-500 @enderror"
                placeholder="ornek@email.com"
            >
            @error('email')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div x-data="{ show: false }">
            <label for="password" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                Şifre
            </label>
            <div class="relative">
                <input
                    :type="show ? 'text' : 'password'"
                    id="password"
                    name="password"
                    required
                    autocomplete="current-password"
                    class="w-full px-4 py-3 pr-12 bg-gray-50 dark:bg-muzibu-gray border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-muzibu-coral focus:border-transparent dark:text-white transition-all @error('password') border-red-500 dark:border-red-500 @enderror"
                    placeholder="••••••••"
                >
                <button
                    type="button"
                    @click="show = !show"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors"
                >
                    <i :class="show ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                </button>
            </div>
            @error('password')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="flex items-center justify-between">
            <label class="flex items-center cursor-pointer">
                <input
                    type="checkbox"
                    name="remember"
                    class="w-4 h-4 border-gray-300 rounded text-muzibu-coral focus:ring-muzibu-coral focus:ring-offset-0"
                >
                <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Beni Hatırla</span>
            </label>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-sm font-semibold text-muzibu-coral hover:text-muzibu-coral-light transition-colors">
                    Şifremi Unuttum
                </a>
            @endif
        </div>

        <!-- Submit Button -->
        <button
            type="submit"
            class="w-full py-3.5 bg-gradient-to-r from-muzibu-coral to-green-600 hover:from-muzibu-coral-light hover:to-green-500 text-white font-bold rounded-xl transition-all hover:scale-105 shadow-lg hover:shadow-xl"
        >
            <i class="fas fa-sign-in-alt mr-2"></i>
            Giriş Yap
        </button>
    </form>
@endsection

@section('footer-links')
    <div class="text-white/80 dark:text-gray-400">
        Hesabınız yok mu?
        <a href="{{ route('register') }}" class="font-semibold text-white dark:text-muzibu-coral-light hover:text-white/100 dark:hover:text-muzibu-coral transition-colors">
            Ücretsiz Kaydolun
        </a>
    </div>
@endsection

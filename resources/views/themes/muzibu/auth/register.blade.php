@extends('themes.muzibu.auth.layout')

@section('title', 'Kayıt Ol - Muzibu')
@section('subtitle', 'Ücretsiz hesap oluşturun')

@section('content')
    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Hemen Başlayın</h2>
    <p class="text-gray-600 dark:text-gray-400 mb-6">Ücretsiz hesap oluşturun, 7 gün premium deneyin</p>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <!-- Name -->
        <div>
            <label for="name" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                Ad Soyad
            </label>
            <input
                type="text"
                id="name"
                name="name"
                value="{{ old('name') }}"
                required
                autofocus
                autocomplete="name"
                class="w-full px-4 py-3 bg-gray-50 dark:bg-muzibu-gray border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-muzibu-coral focus:border-transparent dark:text-white transition-all @error('name') border-red-500 dark:border-red-500 @enderror"
                placeholder="Adınız Soyadınız"
            >
            @error('name')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

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
                    autocomplete="new-password"
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
            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">En az 8 karakter olmalıdır</p>
        </div>

        <!-- Password Confirmation -->
        <div x-data="{ show: false }">
            <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                Şifre Tekrar
            </label>
            <div class="relative">
                <input
                    :type="show ? 'text' : 'password'"
                    id="password_confirmation"
                    name="password_confirmation"
                    required
                    autocomplete="new-password"
                    class="w-full px-4 py-3 pr-12 bg-gray-50 dark:bg-muzibu-gray border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-muzibu-coral focus:border-transparent dark:text-white transition-all"
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
        </div>

        <!-- Terms -->
        <div class="flex items-start">
            <input
                type="checkbox"
                id="terms"
                required
                class="mt-1 w-4 h-4 border-gray-300 rounded text-muzibu-coral focus:ring-muzibu-coral focus:ring-offset-0"
            >
            <label for="terms" class="ml-2 text-sm text-gray-600 dark:text-gray-400">
                <a href="/terms" class="text-muzibu-coral hover:text-muzibu-coral-light transition-colors font-semibold">Kullanım Koşullarını</a> ve
                <a href="/privacy" class="text-muzibu-coral hover:text-muzibu-coral-light transition-colors font-semibold">Gizlilik Politikasını</a> kabul ediyorum
            </label>
        </div>

        <!-- Submit Button -->
        <button
            type="submit"
            class="w-full py-3.5 bg-gradient-to-r from-muzibu-coral to-green-600 hover:from-muzibu-coral-light hover:to-green-500 text-white font-bold rounded-xl transition-all hover:scale-105 shadow-lg hover:shadow-xl"
        >
            <i class="fas fa-rocket mr-2"></i>
            Ücretsiz Hesap Oluştur
        </button>
    </form>
@endsection

@section('footer-links')
    <div class="text-white/80 dark:text-gray-400">
        Zaten hesabınız var mı?
        <a href="{{ route('login') }}" class="font-semibold text-white dark:text-muzibu-coral-light hover:text-white/100 dark:hover:text-muzibu-coral transition-colors">
            Giriş Yapın
        </a>
    </div>
@endsection

@extends('themes.muzibu.auth.layout')

@section('title', 'Şifre Sıfırla - Muzibu')
@section('subtitle', 'Yeni şifre oluşturun')

@section('content')
    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Yeni Şifre Oluşturun</h2>
    <p class="text-gray-600 dark:text-gray-400 mb-6">
        Hesabınız için yeni bir şifre belirleyin
    </p>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email -->
        <div>
            <label for="email" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                E-posta Adresi
            </label>
            <input
                type="email"
                id="email"
                name="email"
                value="{{ old('email', $request->email) }}"
                required
                autofocus
                autocomplete="username"
                class="w-full px-4 py-3 bg-gray-50 dark:bg-spotify-gray border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-spotify-green focus:border-transparent dark:text-white transition-all @error('email') border-red-500 dark:border-red-500 @enderror"
                placeholder="ornek@email.com"
            >
            @error('email')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div x-data="{ show: false }">
            <label for="password" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                Yeni Şifre
            </label>
            <div class="relative">
                <input
                    :type="show ? 'text' : 'password'"
                    id="password"
                    name="password"
                    required
                    autocomplete="new-password"
                    class="w-full px-4 py-3 pr-12 bg-gray-50 dark:bg-spotify-gray border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-spotify-green focus:border-transparent dark:text-white transition-all @error('password') border-red-500 dark:border-red-500 @enderror"
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
                    class="w-full px-4 py-3 pr-12 bg-gray-50 dark:bg-spotify-gray border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-spotify-green focus:border-transparent dark:text-white transition-all"
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

        <!-- Submit Button -->
        <button
            type="submit"
            class="w-full py-3.5 bg-gradient-to-r from-spotify-green to-green-600 hover:from-spotify-green-light hover:to-green-500 text-white font-bold rounded-xl transition-all hover:scale-105 shadow-lg hover:shadow-xl"
        >
            <i class="fas fa-key mr-2"></i>
            Şifreyi Sıfırla
        </button>
    </form>
@endsection

@section('footer-links')
    <div class="text-white/80 dark:text-gray-400">
        Şifrenizi hatırladınız mı?
        <a href="{{ route('login') }}" class="font-semibold text-white dark:text-spotify-green-light hover:text-white/100 dark:hover:text-spotify-green transition-colors">
            Giriş Yapın
        </a>
    </div>
@endsection

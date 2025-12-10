@extends('themes.muzibu.auth.layout')

@section('title', 'Giris Yap - Muzibu')

@section('content')
    <!-- Header -->
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-white mb-2">Tekrar Hos Geldiniz</h2>
        <p class="text-dark-200">Hesabiniza giris yapin</p>
    </div>

    <!-- Session Terminated Warning -->
    @if (request()->get('session_terminated'))
        <div class="mb-6 p-4 bg-orange-500/10 border border-orange-500/20 rounded-xl">
            <div class="flex items-center gap-3 text-orange-400 text-sm">
                <i class="fas fa-exclamation-triangle"></i>
                <span>Baska bir cihazdan giris yapildi. Bu oturum sonlandirildi.</span>
            </div>
        </div>
    @endif

    <!-- Session Status -->
    @if (session('status'))
        <div class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-xl">
            <div class="flex items-center gap-3 text-emerald-400 text-sm">
                <i class="fas fa-check-circle"></i>
                <span>{{ session('status') }}</span>
            </div>
        </div>
    @endif

    <!-- CSRF/Session Error -->
    @if (session('error'))
        <div class="mb-6 p-4 bg-amber-500/10 border border-amber-500/20 rounded-xl">
            <div class="flex items-center gap-3 text-amber-400 text-sm">
                <i class="fas fa-exclamation-triangle"></i>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-5"
          @submit="loading = true; setTimeout(() => loading = false, 10000)">
        @csrf

        <!-- Email -->
        <div>
            <label for="email" class="block text-sm font-medium text-dark-100 mb-2">
                E-posta Adresi
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <i class="fas fa-envelope text-dark-300"></i>
                </div>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    autocomplete="username"
                    class="w-full pl-12 pr-4 py-3.5 bg-dark-700/50 border border-dark-500 rounded-xl text-white placeholder-dark-300 focus:border-mz-500 focus:outline-none transition-all @error('email') border-red-500/50 @enderror"
                    placeholder="ornek@email.com"
                >
            </div>
            @error('email')
                <p class="mt-2 text-sm text-red-400 flex items-center gap-2">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ $message }}
                </p>
            @enderror
        </div>

        <!-- Password -->
        <div>
            <div class="flex items-center justify-between mb-2">
                <label for="password" class="block text-sm font-medium text-dark-100">
                    Sifre
                </label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-sm text-mz-400 hover:text-mz-300 transition-colors">
                        Sifremi Unuttum
                    </a>
                @endif
            </div>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <i class="fas fa-lock text-dark-300"></i>
                </div>
                <input
                    :type="showPassword ? 'text' : 'password'"
                    id="password"
                    name="password"
                    required
                    autocomplete="current-password"
                    class="w-full pl-12 pr-12 py-3.5 bg-dark-700/50 border border-dark-500 rounded-xl text-white placeholder-dark-300 focus:border-mz-500 focus:outline-none transition-all @error('password') border-red-500/50 @enderror"
                    placeholder="••••••••"
                >
                <button
                    type="button"
                    @click="showPassword = !showPassword"
                    class="absolute inset-y-0 right-0 pr-4 flex items-center text-dark-300 hover:text-white transition-colors"
                >
                    <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                </button>
            </div>
            @error('password')
                <p class="mt-2 text-sm text-red-400 flex items-center gap-2">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ $message }}
                </p>
            @enderror
        </div>

        <!-- Remember Me -->
        <div class="flex items-center">
            <input
                type="checkbox"
                id="remember"
                name="remember"
                class="w-4 h-4 bg-dark-700 border-dark-500 rounded text-mz-500 focus:ring-mz-500 focus:ring-offset-0 focus:ring-offset-dark-800"
            >
            <label for="remember" class="ml-3 text-sm text-dark-200">
                Beni Hatirla
            </label>
        </div>

        <!-- Submit Button -->
        <button
            type="submit"
            :disabled="loading"
            class="w-full py-4 bg-gradient-to-r from-mz-500 to-mz-600 hover:from-mz-400 hover:to-mz-500 text-white font-semibold rounded-xl transition-all duration-300 shadow-lg shadow-mz-500/20 hover:shadow-mz-500/30 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
        >
            <template x-if="!loading">
                <span class="flex items-center gap-2">
                    <i class="fas fa-sign-in-alt"></i>
                    Giris Yap
                </span>
            </template>
            <template x-if="loading">
                <span class="flex items-center gap-2">
                    <i class="fas fa-spinner fa-spin"></i>
                    Giris Yapiliyor...
                </span>
            </template>
        </button>

        <!-- Divider -->
        <div class="relative my-6">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-dark-600"></div>
            </div>
            <div class="relative flex justify-center text-sm">
                <span class="px-4 bg-dark-800 text-dark-300">veya</span>
            </div>
        </div>

        <!-- Social Login Placeholder -->
        <div class="grid grid-cols-2 gap-3">
            <button type="button" disabled class="flex items-center justify-center gap-2 py-3 bg-dark-700/50 border border-dark-500 rounded-xl text-dark-300 cursor-not-allowed opacity-50">
                <i class="fab fa-google"></i>
                <span class="text-sm">Google</span>
            </button>
            <button type="button" disabled class="flex items-center justify-center gap-2 py-3 bg-dark-700/50 border border-dark-500 rounded-xl text-dark-300 cursor-not-allowed opacity-50">
                <i class="fab fa-apple"></i>
                <span class="text-sm">Apple</span>
            </button>
        </div>
    </form>
@endsection

@section('footer-links')
    <p class="text-dark-300">
        Hesabiniz yok mu?
        <a href="{{ route('register') }}" class="text-mz-400 hover:text-mz-300 font-medium transition-colors">
            Ucretsiz Kaydolun
        </a>
    </p>
@endsection

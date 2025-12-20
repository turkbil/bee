@extends('themes.muzibu.auth.layout')

@section('title', 'Sifre Sifirla - Muzibu')

@section('content')
    <!-- Logo -->
    <div class="text-center mb-10">
        <a href="/" class="inline-flex items-center gap-3 mb-4">
            <div class="w-14 h-14 bg-gradient-to-br from-mz-500 to-mz-600 rounded-2xl flex items-center justify-center shadow-xl shadow-mz-500/20">
                <i class="fas fa-music text-white text-2xl"></i>
            </div>
        </a>
        <h1 class="text-3xl font-bold text-white">Yeni Sifre Olusturun</h1>
        <p class="text-dark-200 mt-2">Hesabiniz icin guclu bir sifre belirleyin.</p>
    </div>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-5"
          @submit="loading = true; setTimeout(() => loading = false, 10000)">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email (readonly) -->
        <div>
            <input
                type="email"
                name="email"
                value="{{ old('email', $request->email) }}"
                required
                readonly
                autocomplete="username"
                class="w-full px-5 py-4 bg-dark-700/30 border border-dark-600 rounded-xl text-dark-200 cursor-not-allowed"
            >
            @error('email')
                <p class="mt-2 text-sm text-red-400 flex items-center gap-2">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ $message }}
                </p>
            @enderror
        </div>

        <!-- Password -->
        <div class="relative">
            <input
                :type="showPassword ? 'text' : 'password'"
                name="password"
                required
                autofocus
                autocomplete="new-password"
                @input="checkPasswordStrength($event.target.value)"
                class="w-full px-5 py-4 pr-12 bg-dark-700/50 border border-dark-500 rounded-xl text-white placeholder-dark-300 focus:border-mz-500 focus:outline-none transition-all @error('password') border-red-500/50 @enderror"
                placeholder="Yeni sifre (en az 8 karakter)"
            >
            <button
                type="button"
                @click="showPassword = !showPassword"
                class="absolute right-4 top-4 text-dark-300 hover:text-white transition-colors"
            >
                <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
            </button>

            <!-- Password Strength -->
            <div class="mt-3" x-show="passwordStrength > 0" x-transition>
                <div class="flex items-center gap-2 mb-1">
                    <div class="flex-1 h-1.5 bg-dark-600 rounded-full overflow-hidden">
                        <div
                            class="h-full transition-all duration-300 rounded-full"
                            :class="getStrengthColor()"
                            :style="'width: ' + (passwordStrength * 20) + '%'"
                        ></div>
                    </div>
                    <span class="text-xs font-medium" :class="getStrengthColor().replace('bg-', 'text-')" x-text="getStrengthText()"></span>
                </div>
            </div>

            @error('password')
                <p class="mt-2 text-sm text-red-400 flex items-center gap-2">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ $message }}
                </p>
            @enderror
        </div>

        <!-- Password Confirmation -->
        <div class="relative">
            <input
                :type="showPasswordConfirm ? 'text' : 'password'"
                name="password_confirmation"
                required
                autocomplete="new-password"
                class="w-full px-5 py-4 pr-12 bg-dark-700/50 border border-dark-500 rounded-xl text-white placeholder-dark-300 focus:border-mz-500 focus:outline-none transition-all"
                placeholder="Sifre tekrar"
            >
            <button
                type="button"
                @click="showPasswordConfirm = !showPasswordConfirm"
                class="absolute right-4 top-1/2 -translate-y-1/2 text-dark-300 hover:text-white transition-colors"
            >
                <i :class="showPasswordConfirm ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
            </button>
        </div>

        <!-- Submit -->
        <button
            type="submit"
            :disabled="loading"
            class="w-full py-4 bg-gradient-to-r from-mz-500 to-mz-600 hover:from-mz-400 hover:to-mz-500 text-white font-semibold rounded-xl transition-all shadow-lg shadow-mz-500/25 disabled:opacity-50 disabled:cursor-not-allowed"
        >
            <span x-show="!loading" class="flex items-center justify-center gap-2">
                <i class="fas fa-check-circle"></i>
                Sifreyi Guncelle
            </span>
            <span x-show="loading" class="flex items-center justify-center gap-2">
                <i class="fas fa-spinner fa-spin"></i>
                Guncelleniyor...
            </span>
        </button>

        <!-- Info -->
        <div class="p-4 bg-dark-700/30 rounded-xl">
            <div class="flex items-start gap-3 text-sm text-dark-200">
                <i class="fas fa-shield-alt text-dark-300 mt-0.5"></i>
                <p>Guclu sifre icin buyuk/kucuk harf, rakam ve ozel karakter kullanin.</p>
            </div>
        </div>
    </form>

    <!-- Back to Login -->
    <div class="mt-8 text-center">
        <a href="{{ route('login') }}" class="inline-flex items-center gap-2 text-mz-400 hover:text-mz-300 transition-colors">
            <i class="fas fa-arrow-left"></i>
            Giris sayfasina don
        </a>
    </div>
@endsection

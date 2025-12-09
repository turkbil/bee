@extends('themes.muzibu.auth.layout')

@section('title', 'Kayit Ol - Muzibu')

@section('content')
    <!-- Header -->
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-white mb-2">Hesap Olusturun</h2>
        <p class="text-dark-200">7 gun ucretsiz premium deneme firsati</p>
    </div>

    <!-- Trial Badge -->
    <div class="mb-6 p-4 bg-mz-500/10 border border-mz-500/20 rounded-xl">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-mz-500/20 rounded-lg flex items-center justify-center">
                <i class="fas fa-gift text-mz-400"></i>
            </div>
            <div>
                <p class="text-white font-medium text-sm">7 Gun Ucretsiz Deneyin</p>
                <p class="text-dark-200 text-xs">Kredi karti gerekmez</p>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-5"
          @submit="loading = true; setTimeout(() => loading = false, 10000)">
        @csrf

        <!-- Name -->
        <div>
            <label for="name" class="block text-sm font-medium text-dark-100 mb-2">
                Ad Soyad
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <i class="fas fa-user text-dark-300"></i>
                </div>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name') }}"
                    required
                    autofocus
                    autocomplete="name"
                    class="w-full pl-12 pr-4 py-3.5 bg-dark-700/50 border border-dark-500 rounded-xl text-white placeholder-dark-300 focus:border-mz-500 focus:outline-none transition-all @error('name') border-red-500/50 @enderror"
                    placeholder="Adiniz Soyadiniz"
                >
            </div>
            @error('name')
                <p class="mt-2 text-sm text-red-400 flex items-center gap-2">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ $message }}
                </p>
            @enderror
        </div>

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
            <label for="password" class="block text-sm font-medium text-dark-100 mb-2">
                Sifre
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <i class="fas fa-lock text-dark-300"></i>
                </div>
                <input
                    :type="showPassword ? 'text' : 'password'"
                    id="password"
                    name="password"
                    required
                    autocomplete="new-password"
                    @input="checkPasswordStrength($event.target.value)"
                    class="w-full pl-12 pr-12 py-3.5 bg-dark-700/50 border border-dark-500 rounded-xl text-white placeholder-dark-300 focus:border-mz-500 focus:outline-none transition-all @error('password') border-red-500/50 @enderror"
                    placeholder="En az 8 karakter"
                >
                <button
                    type="button"
                    @click="showPassword = !showPassword"
                    class="absolute inset-y-0 right-0 pr-4 flex items-center text-dark-300 hover:text-white transition-colors"
                >
                    <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                </button>
            </div>

            <!-- Password Strength Indicator -->
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
                <div class="flex flex-wrap gap-2 text-xs">
                    <span class="text-dark-300" :class="{'text-emerald-400': passwordStrength >= 1}">
                        <i class="fas fa-check mr-1" x-show="passwordStrength >= 1"></i>8+ karakter
                    </span>
                    <span class="text-dark-300" :class="{'text-emerald-400': passwordStrength >= 4}">
                        <i class="fas fa-check mr-1" x-show="passwordStrength >= 4"></i>Rakam
                    </span>
                    <span class="text-dark-300" :class="{'text-emerald-400': passwordStrength >= 3}">
                        <i class="fas fa-check mr-1" x-show="passwordStrength >= 3"></i>Buyuk harf
                    </span>
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
        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-dark-100 mb-2">
                Sifre Tekrar
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <i class="fas fa-lock text-dark-300"></i>
                </div>
                <input
                    :type="showPasswordConfirm ? 'text' : 'password'"
                    id="password_confirmation"
                    name="password_confirmation"
                    required
                    autocomplete="new-password"
                    class="w-full pl-12 pr-12 py-3.5 bg-dark-700/50 border border-dark-500 rounded-xl text-white placeholder-dark-300 focus:border-mz-500 focus:outline-none transition-all"
                    placeholder="Sifreyi tekrar girin"
                >
                <button
                    type="button"
                    @click="showPasswordConfirm = !showPasswordConfirm"
                    class="absolute inset-y-0 right-0 pr-4 flex items-center text-dark-300 hover:text-white transition-colors"
                >
                    <i :class="showPasswordConfirm ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                </button>
            </div>
        </div>

        <!-- Terms -->
        <div class="flex items-start gap-3">
            <input
                type="checkbox"
                id="terms"
                name="terms"
                required
                class="mt-1 w-4 h-4 bg-dark-700 border-dark-500 rounded text-mz-500 focus:ring-mz-500 focus:ring-offset-0 focus:ring-offset-dark-800"
            >
            <label for="terms" class="text-sm text-dark-200 leading-relaxed">
                <a href="/terms" class="text-mz-400 hover:text-mz-300 transition-colors">Kullanim Kosullarini</a> ve
                <a href="/privacy" class="text-mz-400 hover:text-mz-300 transition-colors">Gizlilik Politikasini</a> okudum, kabul ediyorum.
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
                    <i class="fas fa-rocket"></i>
                    Ucretsiz Baslat
                </span>
            </template>
            <template x-if="loading">
                <span class="flex items-center gap-2">
                    <i class="fas fa-spinner fa-spin"></i>
                    Hesap Olusturuluyor...
                </span>
            </template>
        </button>

        <!-- Features -->
        <div class="pt-4 border-t border-dark-600">
            <div class="grid grid-cols-2 gap-3 text-xs text-dark-200">
                <div class="flex items-center gap-2">
                    <i class="fas fa-check text-emerald-500"></i>
                    <span>Kredi karti gerekmez</span>
                </div>
                <div class="flex items-center gap-2">
                    <i class="fas fa-check text-emerald-500"></i>
                    <span>Aninda erisim</span>
                </div>
                <div class="flex items-center gap-2">
                    <i class="fas fa-check text-emerald-500"></i>
                    <span>Istediginiz zaman iptal</span>
                </div>
                <div class="flex items-center gap-2">
                    <i class="fas fa-check text-emerald-500"></i>
                    <span>7/24 destek</span>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('footer-links')
    <p class="text-dark-300">
        Zaten hesabiniz var mi?
        <a href="{{ route('login') }}" class="text-mz-400 hover:text-mz-300 font-medium transition-colors">
            Giris Yapin
        </a>
    </p>
@endsection

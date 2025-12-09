@extends('themes.muzibu.auth.layout')

@section('title', 'Sifre Sifirla - Muzibu')

@section('content')
    <!-- Header -->
    <div class="mb-8">
        <div class="w-16 h-16 bg-emerald-500/10 rounded-2xl flex items-center justify-center mb-6">
            <i class="fas fa-shield-check text-emerald-400 text-2xl"></i>
        </div>
        <h2 class="text-2xl font-bold text-white mb-2">Yeni Sifre Olusturun</h2>
        <p class="text-dark-200">Hesabiniz icin guclu bir sifre belirleyin.</p>
    </div>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-5"
          @submit="loading = true; setTimeout(() => loading = false, 10000)">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

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
                    value="{{ old('email', $request->email) }}"
                    required
                    readonly
                    autocomplete="username"
                    class="w-full pl-12 pr-4 py-3.5 bg-dark-700/30 border border-dark-600 rounded-xl text-dark-200 cursor-not-allowed"
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
                Yeni Sifre
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
                    autofocus
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

        <!-- Submit Button -->
        <button
            type="submit"
            :disabled="loading"
            class="w-full py-4 bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-400 hover:to-emerald-500 text-white font-semibold rounded-xl transition-all duration-300 shadow-lg shadow-emerald-500/20 hover:shadow-emerald-500/30 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
        >
            <template x-if="!loading">
                <span class="flex items-center gap-2">
                    <i class="fas fa-check-circle"></i>
                    Sifreyi Guncelle
                </span>
            </template>
            <template x-if="loading">
                <span class="flex items-center gap-2">
                    <i class="fas fa-spinner fa-spin"></i>
                    Guncelleniyor...
                </span>
            </template>
        </button>

        <!-- Security Info -->
        <div class="pt-4 border-t border-dark-600">
            <div class="flex items-start gap-3 text-sm text-dark-300">
                <i class="fas fa-shield-alt text-dark-400 mt-0.5"></i>
                <p>Guclu bir sifre icin buyuk/kucuk harf, rakam ve ozel karakter kullamin.</p>
            </div>
        </div>
    </form>
@endsection

@section('footer-links')
    <p class="text-dark-300">
        Sifrenizi hatirladin mi?
        <a href="{{ route('login') }}" class="text-mz-400 hover:text-mz-300 font-medium transition-colors">
            Giris Yapin
        </a>
    </p>
@endsection

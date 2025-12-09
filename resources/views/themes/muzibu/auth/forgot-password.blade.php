@extends('themes.muzibu.auth.layout')

@section('title', 'Sifremi Unuttum - Muzibu')

@section('content')
    <!-- Header -->
    <div class="mb-8">
        <div class="w-16 h-16 bg-mz-500/10 rounded-2xl flex items-center justify-center mb-6">
            <i class="fas fa-key text-mz-400 text-2xl"></i>
        </div>
        <h2 class="text-2xl font-bold text-white mb-2">Sifremi Unuttum</h2>
        <p class="text-dark-200">E-posta adresinizi girin, size sifre sifirlama baglantisi gonderelim.</p>
    </div>

    <!-- Session Status (Success Message) -->
    @if (session('status'))
        <div class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-xl">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 bg-emerald-500/20 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-check text-emerald-400"></i>
                </div>
                <div>
                    <p class="text-emerald-400 font-medium text-sm">E-posta Gonderildi!</p>
                    <p class="text-dark-200 text-sm mt-1">{{ session('status') }}</p>
                </div>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5"
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

        <!-- Submit Button -->
        <button
            type="submit"
            :disabled="loading"
            class="w-full py-4 bg-gradient-to-r from-mz-500 to-mz-600 hover:from-mz-400 hover:to-mz-500 text-white font-semibold rounded-xl transition-all duration-300 shadow-lg shadow-mz-500/20 hover:shadow-mz-500/30 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
        >
            <template x-if="!loading">
                <span class="flex items-center gap-2">
                    <i class="fas fa-paper-plane"></i>
                    Sifirlama Baglantisi Gonder
                </span>
            </template>
            <template x-if="loading">
                <span class="flex items-center gap-2">
                    <i class="fas fa-spinner fa-spin"></i>
                    Gonderiliyor...
                </span>
            </template>
        </button>

        <!-- Info -->
        <div class="pt-4 border-t border-dark-600">
            <div class="flex items-start gap-3 text-sm text-dark-300">
                <i class="fas fa-info-circle text-dark-400 mt-0.5"></i>
                <p>Sifirlama baglantisi e-posta adresinize gonderilecektir. Baglanti 60 dakika icerisinde gecerliliğini yitirecektir.</p>
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

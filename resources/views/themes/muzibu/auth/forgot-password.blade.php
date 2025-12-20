@extends('themes.muzibu.auth.layout')

@section('title', 'Sifremi Unuttum - Muzibu')

@section('content')
    <!-- Logo -->
    <div class="text-center mb-10">
        <a href="/" class="inline-flex items-center gap-3 mb-4">
            <div class="w-14 h-14 bg-gradient-to-br from-mz-500 to-mz-600 rounded-2xl flex items-center justify-center shadow-xl shadow-mz-500/20">
                <i class="fas fa-music text-white text-2xl"></i>
            </div>
        </a>
        <h1 class="text-3xl font-bold text-white">Sifremi Unuttum</h1>
        <p class="text-dark-200 mt-2">E-posta adresinizi girin, size sifre sifirlama baglantisi gonderelim.</p>
    </div>

    <!-- Success Message -->
    @if (session('status'))
        <div class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-xl">
            <div class="flex items-center gap-3 text-emerald-400 text-sm">
                <i class="fas fa-check-circle"></i>
                <span>{{ session('status') }}</span>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5"
          @submit="loading = true; setTimeout(() => loading = false, 10000)">
        @csrf

        <!-- Email -->
        <div>
            <input
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
                autofocus
                class="w-full px-5 py-4 bg-dark-700/50 border border-dark-500 rounded-xl text-white placeholder-dark-300 focus:border-mz-500 focus:outline-none transition-all @error('email') border-red-500/50 @enderror"
                placeholder="E-posta adresiniz"
            >
            @error('email')
                <p class="mt-2 text-sm text-red-400 flex items-center gap-2">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ $message }}
                </p>
            @enderror
        </div>

        <!-- Submit -->
        <button
            type="submit"
            :disabled="loading"
            class="w-full py-4 bg-gradient-to-r from-mz-500 to-mz-600 hover:from-mz-400 hover:to-mz-500 text-white font-semibold rounded-xl transition-all shadow-lg shadow-mz-500/25 disabled:opacity-50 disabled:cursor-not-allowed"
        >
            <span x-show="!loading" class="flex items-center justify-center gap-2">
                <i class="fas fa-paper-plane"></i>
                Sifirlama Baglantisi Gonder
            </span>
            <span x-show="loading" class="flex items-center justify-center gap-2">
                <i class="fas fa-spinner fa-spin"></i>
                Gonderiliyor...
            </span>
        </button>

        <!-- Info -->
        <div class="p-4 bg-dark-700/30 rounded-xl">
            <div class="flex items-start gap-3 text-sm text-dark-200">
                <i class="fas fa-info-circle text-dark-300 mt-0.5"></i>
                <p>Sifirlama baglantisi 60 dakika gecerlidir.</p>
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

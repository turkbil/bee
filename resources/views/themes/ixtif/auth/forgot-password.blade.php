@extends('themes.ixtif.auth.layout')

@section('title', 'Şifremi Unuttum')

@section('content')
    <div x-data="{ email: '', isSubmitting: false, emailError: '' }">
        {{-- Header --}}
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fa-light fa-key text-2xl text-blue-600 dark:text-blue-400"></i>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Şifrenizi mi Unuttunuz?</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-2">E-posta adresinizi girin, size şifre sıfırlama bağlantısı gönderelim.</p>
        </div>

        {{-- Status Message --}}
        @if (session('status'))
            <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-lg">
                <p class="text-sm text-green-700 dark:text-green-300 flex items-center gap-2">
                    <i class="fa-solid fa-check-circle"></i>
                    {{ session('status') }}
                </p>
            </div>
        @endif

        {{-- Error Messages --}}
        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-lg">
                <ul class="text-sm text-red-600 dark:text-red-400 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Form --}}
        <form method="POST" action="{{ route('password.email') }}" @submit="isSubmitting = true" class="space-y-5">
            @csrf

            {{-- Email --}}
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    E-posta Adresi
                </label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    autocomplete="email"
                    x-model="email"
                    class="w-full px-4 py-3 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                    placeholder="ornek@email.com"
                >
            </div>

            {{-- Submit --}}
            <button
                type="submit"
                :disabled="isSubmitting || !email"
                class="w-full py-3 bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 disabled:cursor-not-allowed text-white font-semibold rounded-lg transition-colors flex items-center justify-center gap-2"
            >
                <template x-if="isSubmitting">
                    <span class="flex items-center gap-2">
                        <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Gönderiliyor...
                    </span>
                </template>
                <template x-if="!isSubmitting">
                    <span>Şifre Sıfırlama Bağlantısı Gönder</span>
                </template>
            </button>
        </form>
    </div>
@endsection

@section('footer-links')
    <p class="text-gray-600 dark:text-gray-400">
        Şifrenizi hatırladınız mı?
        <a href="{{ route('login') }}" class="text-blue-600 dark:text-blue-400 font-semibold hover:underline">
            Giriş Yapın
        </a>
    </p>
@endsection

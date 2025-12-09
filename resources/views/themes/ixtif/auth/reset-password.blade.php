@extends('themes.ixtif.auth.layout')

@section('title', 'Şifre Sıfırlama')

@section('content')
    <div x-data="resetForm()">
        {{-- Header --}}
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fa-light fa-lock-open text-2xl text-green-600 dark:text-green-400"></i>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Yeni Şifre Belirleyin</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-2">Güçlü ve güvenli bir şifre oluşturun.</p>
        </div>

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
        <form method="POST" action="{{ route('password.store') }}" @submit="handleSubmit" class="space-y-5">
            @csrf
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            {{-- Email --}}
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    E-posta Adresi
                </label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email', $request->email) }}"
                    required
                    autocomplete="email"
                    readonly
                    class="w-full px-4 py-3 bg-gray-100 dark:bg-gray-600 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 cursor-not-allowed"
                >
            </div>

            {{-- Password --}}
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Yeni Şifre
                </label>
                <div class="relative">
                    <input
                        :type="showPassword ? 'text' : 'password'"
                        id="password"
                        name="password"
                        required
                        autocomplete="new-password"
                        x-model="password"
                        @input="checkPasswordStrength"
                        class="w-full px-4 py-3 pr-12 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                        placeholder="••••••••"
                    >
                    <button
                        type="button"
                        @click="showPassword = !showPassword"
                        class="absolute right-3 top-1/2 -translate-y-1/2 p-1.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors"
                    >
                        <i class="fa-regular" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                    </button>
                </div>
                {{-- Password Strength --}}
                <div x-show="password" class="mt-2">
                    <div class="flex gap-1 mb-1">
                        <div class="h-1 flex-1 rounded-full transition-colors" :class="passwordStrength >= 1 ? 'bg-red-500' : 'bg-gray-200 dark:bg-gray-600'"></div>
                        <div class="h-1 flex-1 rounded-full transition-colors" :class="passwordStrength >= 2 ? 'bg-yellow-500' : 'bg-gray-200 dark:bg-gray-600'"></div>
                        <div class="h-1 flex-1 rounded-full transition-colors" :class="passwordStrength >= 3 ? 'bg-green-500' : 'bg-gray-200 dark:bg-gray-600'"></div>
                        <div class="h-1 flex-1 rounded-full transition-colors" :class="passwordStrength >= 4 ? 'bg-green-600' : 'bg-gray-200 dark:bg-gray-600'"></div>
                    </div>
                    <p class="text-xs" :class="{
                        'text-red-500': passwordStrength === 1,
                        'text-yellow-500': passwordStrength === 2,
                        'text-green-500': passwordStrength >= 3
                    }" x-text="passwordStrengthText"></p>
                </div>
            </div>

            {{-- Password Confirmation --}}
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Yeni Şifre Tekrar
                </label>
                <div class="relative">
                    <input
                        :type="showPasswordConfirm ? 'text' : 'password'"
                        id="password_confirmation"
                        name="password_confirmation"
                        required
                        autocomplete="new-password"
                        x-model="passwordConfirmation"
                        class="w-full px-4 py-3 pr-12 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                        :class="passwordConfirmation && password !== passwordConfirmation ? 'border-red-500' : ''"
                        placeholder="••••••••"
                    >
                    <button
                        type="button"
                        @click="showPasswordConfirm = !showPasswordConfirm"
                        class="absolute right-3 top-1/2 -translate-y-1/2 p-1.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors"
                    >
                        <i class="fa-regular" :class="showPasswordConfirm ? 'fa-eye-slash' : 'fa-eye'"></i>
                    </button>
                </div>
                <p x-show="passwordConfirmation && password !== passwordConfirmation" class="mt-1.5 text-sm text-red-600 dark:text-red-400">
                    Şifreler eşleşmiyor
                </p>
            </div>

            {{-- Submit --}}
            <button
                type="submit"
                :disabled="isSubmitting || !isFormValid"
                class="w-full py-3 bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 disabled:cursor-not-allowed text-white font-semibold rounded-lg transition-colors flex items-center justify-center gap-2"
            >
                <template x-if="isSubmitting">
                    <span class="flex items-center gap-2">
                        <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Şifre değiştiriliyor...
                    </span>
                </template>
                <template x-if="!isSubmitting">
                    <span>Şifreyi Sıfırla</span>
                </template>
            </button>
        </form>
    </div>
@endsection

@section('footer-links')
    <p class="text-gray-600 dark:text-gray-400">
        <a href="{{ route('login') }}" class="text-blue-600 dark:text-blue-400 font-semibold hover:underline">
            Giriş Sayfasına Dön
        </a>
    </p>
@endsection

@push('scripts')
<script>
function resetForm() {
    return {
        password: '',
        passwordConfirmation: '',
        showPassword: false,
        showPasswordConfirm: false,
        isSubmitting: false,
        passwordStrength: 0,
        passwordStrengthText: '',

        get isFormValid() {
            return this.password && this.passwordConfirmation && this.password === this.passwordConfirmation;
        },

        checkPasswordStrength() {
            let strength = 0;
            if (this.password.length >= 8) strength++;
            if (/[A-Z]/.test(this.password)) strength++;
            if (/[0-9]/.test(this.password)) strength++;
            if (/[^A-Za-z0-9]/.test(this.password)) strength++;

            this.passwordStrength = strength;
            const texts = ['', 'Zayıf', 'Orta', 'Güçlü', 'Çok Güçlü'];
            this.passwordStrengthText = texts[strength] || '';
        },

        handleSubmit(e) {
            if (!this.isFormValid) {
                e.preventDefault();
                return;
            }
            this.isSubmitting = true;
        }
    }
}
</script>
@endpush

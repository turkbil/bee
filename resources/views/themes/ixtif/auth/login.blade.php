@extends('themes.ixtif.auth.layout')

@section('title', 'Giriş Yap')

@section('content')
    <div x-data="loginForm()" x-init="init()">
        {{-- Header --}}
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Hoş Geldiniz</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-2">Hesabınıza giriş yapın</p>
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
                <p class="text-sm font-semibold text-red-700 dark:text-red-300 flex items-center gap-2 mb-2">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    Giriş Başarısız
                </p>
                <ul class="text-sm text-red-600 dark:text-red-400 list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Login Form --}}
        <form method="POST" action="{{ route('login') }}" @submit="handleSubmit" autocomplete="on" class="space-y-5">
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
                    @blur="validateEmail"
                    class="w-full px-4 py-3 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                    :class="emailError ? 'border-red-500 focus:ring-red-500' : ''"
                    placeholder="ornek@email.com"
                >
                <p x-show="emailError" x-text="emailError" class="mt-1.5 text-sm text-red-600 dark:text-red-400"></p>
            </div>

            {{-- Password --}}
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Şifre
                </label>
                <div class="relative">
                    <input
                        :type="showPassword ? 'text' : 'password'"
                        id="password"
                        name="password"
                        required
                        autocomplete="current-password"
                        x-model="password"
                        class="w-full px-4 py-3 pr-12 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                        placeholder="••••••••"
                    >
                    <button
                        type="button"
                        @click="showPassword = !showPassword"
                        class="absolute right-3 top-1/2 -translate-y-1/2 p-1.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors"
                        :title="showPassword ? 'Şifreyi gizle' : 'Şifreyi göster'"
                    >
                        <i class="fa-regular" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                    </button>
                </div>
            </div>

            {{-- Remember & Forgot --}}
            <div class="flex items-center justify-between">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input
                        type="checkbox"
                        name="remember"
                        x-model="remember"
                        class="w-4 h-4 border-gray-300 dark:border-gray-600 rounded text-blue-600 focus:ring-blue-500"
                    >
                    <span class="text-sm text-gray-600 dark:text-gray-400">Beni Hatırla</span>
                </label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                        Şifremi Unuttum
                    </a>
                @endif
            </div>

            {{-- Submit --}}
            <button
                type="submit"
                :disabled="isSubmitting"
                class="w-full py-3 bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white font-semibold rounded-lg transition-colors flex items-center justify-center gap-2"
            >
                <template x-if="isSubmitting">
                    <span class="flex items-center gap-2">
                        <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Giriş yapılıyor...
                    </span>
                </template>
                <template x-if="!isSubmitting">
                    <span>Giriş Yap</span>
                </template>
            </button>
        </form>
    </div>
@endsection

@section('footer-links')
    <p class="text-gray-600 dark:text-gray-400">
        Hesabınız yok mu?
        <a href="{{ route('register') }}" class="text-blue-600 dark:text-blue-400 font-semibold hover:underline">
            Kayıt Olun
        </a>
    </p>
@endsection

@push('scripts')
<script>
function loginForm() {
    return {
        email: '{{ old("email") }}',
        password: '',
        showPassword: false,
        remember: false,
        isSubmitting: false,
        emailError: '',

        init() {
            // Autofill detection
            setTimeout(() => {
                const emailInput = document.getElementById('email');
                const passwordInput = document.getElementById('password');
                if (emailInput?.value) this.email = emailInput.value;
                if (passwordInput?.value) this.password = passwordInput.value;
            }, 100);
        },

        validateEmail() {
            if (!this.email) {
                this.emailError = '';
                return;
            }
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            this.emailError = !emailRegex.test(this.email) ? 'Geçerli bir e-posta adresi giriniz' : '';
        },

        handleSubmit(e) {
            this.validateEmail();
            if (!this.email || !this.password || this.emailError) {
                e.preventDefault();
                if (!this.email) this.emailError = 'E-posta adresi gereklidir';
                return;
            }
            this.isSubmitting = true;
        }
    }
}
</script>
@endpush

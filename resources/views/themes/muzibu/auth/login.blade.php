@extends('themes.muzibu.auth.layout')

@section('title', request()->routeIs('register') ? 'Kayit Ol - Muzibu' : 'Giris Yap - Muzibu')

@section('active-tab', request()->routeIs('register') ? 'register' : 'login')

@section('content')
    <!-- Logo -->
    <div class="text-center mb-10">
        <a href="/" class="inline-flex items-center gap-3 mb-4">
            <div class="w-14 h-14 bg-gradient-to-br from-mz-500 to-mz-600 rounded-2xl flex items-center justify-center shadow-xl shadow-mz-500/20">
                <i class="fas fa-music text-white text-2xl"></i>
            </div>
        </a>
        <h1 class="text-3xl font-bold text-white">Muzibu</h1>
        <p class="text-dark-200 mt-2">Isletmeniz icin profesyonel muzik</p>
    </div>

    <!-- Tab Switcher -->
    <div class="flex bg-dark-700/50 rounded-xl p-1 mb-8">
        <button
            @click="activeTab = 'login'"
            :class="activeTab === 'login' ? 'bg-mz-500 text-white shadow-lg' : 'text-dark-200 hover:text-white'"
            class="flex-1 py-3 rounded-lg font-medium transition-all duration-200"
        >
            Giris Yap
        </button>
        <button
            @click="activeTab = 'register'"
            :class="activeTab === 'register' ? 'bg-mz-500 text-white shadow-lg' : 'text-dark-200 hover:text-white'"
            class="flex-1 py-3 rounded-lg font-medium transition-all duration-200"
        >
            Kayit Ol
        </button>
    </div>

    <!-- Form Container - Sabit yukseklik ile kayma onlenir -->
    <div class="min-h-[520px]">

    <!-- Login Form -->
    <div x-show="activeTab === 'login'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">

        <!-- Logout Reason Alerts -->
        @php
            $logoutReason = request()->get('reason') ?? request()->get('session_terminated');
        @endphp

        @if ($logoutReason)
            <div class="mb-6 p-4 bg-orange-500/10 border border-orange-500/20 rounded-xl">
                <div class="flex items-center gap-3 text-orange-400 text-sm">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>
                        @switch($logoutReason)
                            @case('session_terminated')
                            @case('1')
                                Baska bir cihazdan giris yapildi. Bu oturum sonlandirildi.
                                @break
                            @case('session_expired')
                                Oturum suresi doldu. Lutfen tekrar giris yapin.
                                @break
                            @case('admin_logout')
                                Yonetici tarafindan oturumunuz sonlandirildi.
                                @break
                            @case('security')
                                Guvenlik nedeniyle oturum kapatildi.
                                @break
                            @case('password_changed')
                                Sifreniz degistirildi. Lutfen yeni sifrenizle giris yapin.
                                @break
                            @default
                                Oturumunuz sonlandirildi. Lutfen tekrar giris yapin.
                        @endswitch
                    </span>
                </div>
            </div>
        @endif

        @if (session('status'))
            <div class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-xl">
                <div class="flex items-center gap-3 text-emerald-400 text-sm">
                    <i class="fas fa-check-circle"></i>
                    <span>{{ session('status') }}</span>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 p-4 bg-amber-500/10 border border-amber-500/20 rounded-xl">
                <div class="flex items-center gap-3 text-amber-400 text-sm">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-5"
              x-data="{ tokenRefreshInterval: null }"
              x-init="
                  tokenRefreshInterval = setInterval(async () => {
                      try {
                          const response = await fetch('/api/csrf-token');
                          if (response.ok) {
                              const data = await response.json();
                              const csrfInput = document.querySelector('input[name=_token]');
                              if (csrfInput && data.token) {
                                  csrfInput.value = data.token;
                              }
                          }
                      } catch (error) {}
                  }, 30 * 60 * 1000);
              "
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
                    autocomplete="username"
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

            <!-- Password -->
            <div class="relative">
                <input
                    :type="showPassword ? 'text' : 'password'"
                    name="password"
                    required
                    autocomplete="current-password"
                    class="w-full px-5 py-4 pr-12 bg-dark-700/50 border border-dark-500 rounded-xl text-white placeholder-dark-300 focus:border-mz-500 focus:outline-none transition-all @error('password') border-red-500/50 @enderror"
                    placeholder="Sifreniz"
                >
                <button
                    type="button"
                    @click="showPassword = !showPassword"
                    class="absolute right-4 top-1/2 -translate-y-1/2 text-dark-300 hover:text-white transition-colors"
                >
                    <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                </button>
                @error('password')
                    <p class="mt-2 text-sm text-red-400 flex items-center gap-2">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Remember & Forgot -->
            <div class="flex items-center justify-between text-sm">
                <label class="flex items-center gap-2 text-dark-200 cursor-pointer">
                    <input type="checkbox" name="remember" class="w-4 h-4 rounded bg-dark-600 border-dark-500 text-mz-500 focus:ring-mz-500">
                    Beni hatirla
                </label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-mz-400 hover:text-mz-300 transition-colors">
                        Sifremi unuttum
                    </a>
                @endif
            </div>

            <!-- Submit -->
            <button
                type="submit"
                :disabled="loading"
                class="w-full py-4 bg-gradient-to-r from-mz-500 to-mz-600 hover:from-mz-400 hover:to-mz-500 text-white font-semibold rounded-xl transition-all shadow-lg shadow-mz-500/25 disabled:opacity-50 disabled:cursor-not-allowed"
            >
                <span x-show="!loading" class="flex items-center justify-center gap-2">
                    <i class="fas fa-sign-in-alt"></i>
                    Giris Yap
                </span>
                <span x-show="loading" class="flex items-center justify-center gap-2">
                    <i class="fas fa-spinner fa-spin"></i>
                    Giris Yapiliyor...
                </span>
            </button>
        </form>

    </div>

    <!-- Register Form -->
    <div x-show="activeTab === 'register'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">

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
                <input
                    type="text"
                    name="name"
                    value="{{ old('name') }}"
                    required
                    autocomplete="name"
                    class="w-full px-5 py-4 bg-dark-700/50 border border-dark-500 rounded-xl text-white placeholder-dark-300 focus:border-mz-500 focus:outline-none transition-all @error('name') border-red-500/50 @enderror"
                    placeholder="Ad Soyad"
                >
                @error('name')
                    <p class="mt-2 text-sm text-red-400 flex items-center gap-2">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Email -->
            <div>
                <input
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autocomplete="username"
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

            <!-- Password -->
            <div class="relative">
                <input
                    :type="showPassword ? 'text' : 'password'"
                    name="password"
                    required
                    autocomplete="new-password"
                    @input="checkPasswordStrength($event.target.value)"
                    class="w-full px-5 py-4 pr-12 bg-dark-700/50 border border-dark-500 rounded-xl text-white placeholder-dark-300 focus:border-mz-500 focus:outline-none transition-all @error('password') border-red-500/50 @enderror"
                    placeholder="Sifre (en az 8 karakter)"
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

            <!-- Terms -->
            <div class="flex items-start gap-3">
                <input
                    type="checkbox"
                    name="terms"
                    required
                    class="mt-1 w-4 h-4 rounded bg-dark-600 border-dark-500 text-mz-500 focus:ring-mz-500"
                >
                <label class="text-sm text-dark-200 leading-relaxed">
                    <a href="/terms" class="text-mz-400 hover:text-mz-300">Kullanim Kosullarini</a> ve
                    <a href="/privacy" class="text-mz-400 hover:text-mz-300">Gizlilik Politikasini</a> kabul ediyorum.
                </label>
            </div>

            <!-- Submit -->
            <button
                type="submit"
                :disabled="loading"
                class="w-full py-4 bg-gradient-to-r from-mz-500 to-mz-600 hover:from-mz-400 hover:to-mz-500 text-white font-semibold rounded-xl transition-all shadow-lg shadow-mz-500/25 disabled:opacity-50 disabled:cursor-not-allowed"
            >
                <span x-show="!loading" class="flex items-center justify-center gap-2">
                    <i class="fas fa-rocket"></i>
                    Ucretsiz Baslat
                </span>
                <span x-show="loading" class="flex items-center justify-center gap-2">
                    <i class="fas fa-spinner fa-spin"></i>
                    Hesap Olusturuluyor...
                </span>
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

    </div>

    </div><!-- /Form Container -->
@endsection

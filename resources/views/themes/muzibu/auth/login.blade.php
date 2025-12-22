@extends('themes.muzibu.auth.layout')

@section('title', request()->get('tab') === 'register' ? 'Kayıt Ol - Muzibu' : 'Giriş Yap - Muzibu')

@section('active-tab', request()->get('tab') === 'register' ? 'register' : 'login')

@section('content')
    {{-- Device Limit Exceeded Data (Session Flash) --}}
    @if(session('device_limit_exceeded'))
    <script>
        window.deviceLimitData = {
            exceeded: true,
            limit: {{ session('device_limit', 1) }},
            devices: {!! json_encode(session('other_devices', [])) !!},
            intendedUrl: '{{ session('intended_url', '/') }}'
        };
    </script>
    @endif

    {{-- Device Selection Modal --}}
    <div x-show="showDeviceModal"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm"
         x-cloak>
        <div x-show="showDeviceModal"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             class="w-full max-w-lg bg-dark-800 border border-dark-600 rounded-2xl shadow-2xl overflow-hidden">

            {{-- Modal Header --}}
            <div class="p-6 border-b border-dark-600 bg-orange-500/5">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-orange-500/20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-orange-400 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-white">Cihaz Limiti Aşıldı</h3>
                        <p class="text-dark-200 text-sm">Aynı anda en fazla <span x-text="deviceLimit" class="font-medium text-orange-400"></span> cihazda oturum açabilirsiniz</p>
                    </div>
                </div>
            </div>

            {{-- Modal Body --}}
            <div class="p-6">
                <p class="text-dark-200 text-sm mb-4">
                    Devam etmek için aşağıdaki cihazlardan birini veya birden fazlasini seçip oturumunu kapatmalısınız:
                </p>

                {{-- Error Message --}}
                <div x-show="terminateError" x-transition class="mb-4 p-3 bg-red-500/10 border border-red-500/20 rounded-xl">
                    <p class="text-red-400 text-sm flex items-center gap-2">
                        <i class="fas fa-exclamation-circle"></i>
                        <span x-text="terminateError"></span>
                    </p>
                </div>

                {{-- Device List --}}
                <div class="space-y-3 max-h-64 overflow-y-auto">
                    <template x-for="device in otherDevices" :key="device.session_id">
                        <div @click="toggleDeviceSelection(device.session_id)"
                             :class="isDeviceSelected(device.session_id) ? 'border-mz-500 bg-mz-500/10' : 'border-dark-500 hover:border-dark-400'"
                             class="p-4 border rounded-xl cursor-pointer transition-all duration-200">
                            <div class="flex items-center gap-4">
                                {{-- Selection Checkbox --}}
                                <div :class="isDeviceSelected(device.session_id) ? 'bg-mz-500 border-mz-500' : 'border-dark-400'"
                                     class="w-5 h-5 rounded border-2 flex items-center justify-center transition-colors">
                                    <i x-show="isDeviceSelected(device.session_id)" class="fas fa-check text-white text-xs"></i>
                                </div>

                                {{-- Device Icon --}}
                                <div class="w-10 h-10 bg-dark-600 rounded-lg flex items-center justify-center">
                                    <i :class="getDeviceIcon(device.device_type)" class="text-dark-200"></i>
                                </div>

                                {{-- Device Info --}}
                                <div class="flex-1 min-w-0">
                                    <p class="text-white font-medium truncate" x-text="device.device_name"></p>
                                    <p class="text-dark-300 text-xs flex items-center gap-2">
                                        <span x-text="device.browser"></span>
                                        <span class="text-dark-400">-</span>
                                        <span x-text="device.ip_address"></span>
                                    </p>
                                </div>

                                {{-- Last Activity --}}
                                <div class="text-right">
                                    <p class="text-dark-300 text-xs" x-text="device.last_activity_human"></p>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Modal Footer --}}
            <div class="p-6 border-t border-dark-600 bg-dark-900/50">
                <div class="flex items-center gap-3">
                    <button @click="terminateSelectedDevices()"
                            :disabled="terminatingDevices || selectedDevices.length === 0"
                            class="flex-1 py-3 bg-gradient-to-r from-mz-500 to-mz-600 hover:from-mz-400 hover:to-mz-500 text-white font-semibold rounded-xl transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                        <span x-show="!terminatingDevices" class="flex items-center justify-center gap-2">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Seçili Cihazlari Çıkış Yaptır</span>
                            <span x-show="selectedDevices.length > 0" class="bg-white/20 px-2 py-0.5 rounded text-xs" x-text="'(' + selectedDevices.length + ')'"></span>
                        </span>
                        <span x-show="terminatingDevices" class="flex items-center justify-center gap-2">
                            <i class="fas fa-spinner fa-spin"></i>
                            Çıkış Yaptıriliyor...
                        </span>
                    </button>
                </div>
                <p class="text-center text-dark-300 text-xs mt-3">
                    Seçtiğiniz cihazlardaki oturumlar kapatılacak
                </p>
            </div>
        </div>
    </div>

    <!-- Logo -->
    <div class="text-center mb-10">
        <a href="/" class="inline-block">
            @php
                // LogoService kullan - Settings'den logo çek
                $logoService = app(\App\Services\LogoService::class);
                $logos = $logoService->getLogos();

                $logoUrl = $logos['light_logo_url'] ?? null;
                $logoDarkUrl = $logos['dark_logo_url'] ?? null;
                $fallbackMode = $logos['fallback_mode'] ?? 'title_only';
                $siteTitle = $logos['site_title'] ?? setting('site_title', 'muzibu');
            @endphp

            @if($fallbackMode === 'both')
                {{-- Her iki logo da var - Dark mode'da otomatik değiş --}}
                <img src="{{ $logoUrl }}"
                     alt="{{ $siteTitle }}"
                     class="dark:hidden object-contain h-12 w-auto mx-auto"
                     title="{{ $siteTitle }}">
                <img src="{{ $logoDarkUrl }}"
                     alt="{{ $siteTitle }}"
                     class="hidden dark:block object-contain h-12 w-auto mx-auto"
                     title="{{ $siteTitle }}">
            @elseif($fallbackMode === 'light_only' && $logoUrl)
                {{-- Sadece light logo var --}}
                <img src="{{ $logoUrl }}"
                     alt="{{ $siteTitle }}"
                     class="object-contain h-12 w-auto mx-auto"
                     title="{{ $siteTitle }}">
            @elseif($fallbackMode === 'dark_only' && $logoDarkUrl)
                {{-- Sadece dark logo var --}}
                <img src="{{ $logoDarkUrl }}"
                     alt="{{ $siteTitle }}"
                     class="object-contain h-12 w-auto mx-auto"
                     title="{{ $siteTitle }}">
            @else
                {{-- Fallback: Gradient text logo --}}
                <span class="text-3xl font-bold bg-gradient-to-r from-mz-500 via-mz-600 to-mz-500 bg-clip-text text-transparent">
                    {{ $siteTitle }}
                </span>
            @endif
        </a>
    </div>

    <!-- Tab Switcher -->
    <div class="flex bg-dark-700/50 rounded-xl p-1 mb-8">
        <button
            @click="activeTab = 'login'"
            :class="activeTab === 'login' ? 'bg-mz-500 text-white shadow-lg' : 'text-dark-200 hover:text-white'"
            class="flex-1 py-3 rounded-lg font-medium transition-all duration-200"
        >
            Giriş Yap
        </button>
        <button
            @click="activeTab = 'register'"
            :class="activeTab === 'register' ? 'bg-mz-500 text-white shadow-lg' : 'text-dark-200 hover:text-white'"
            class="flex-1 py-3 rounded-lg font-medium transition-all duration-200"
        >
            Kayıt Ol
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
                                Başka bir cihazdan giris yapıldı. Bu oturum sonlandırıldı.
                                @break
                            @case('session_expired')
                                Oturum süresi doldu. Lütfen tekrar giris yapin.
                                @break
                            @case('admin_logout')
                                Yönetici tarafından oturumunuz sonlandırıldı.
                                @break
                            @case('security')
                                Güvenlik nedeniyle oturum kapatıldı.
                                @break
                            @case('password_changed')
                                Şifreniz değiştirildi. Lütfen yeni şifrenizle giris yapin.
                                @break
                            @default
                                Oturumunuz sonlandırıldı. Lütfen tekrar giris yapin.
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
            <input type="hidden" name="_token" value="{{ csrf_token() }}" autocomplete="off">

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
                    placeholder="Şifreniz"
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
                    Beni hatırla
                </label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-mz-400 hover:text-mz-300 transition-colors">
                        Şifremi unuttum
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
                    Giriş Yap
                </span>
                <span x-show="loading" class="flex items-center justify-center gap-2">
                    <i class="fas fa-spinner fa-spin"></i>
                    Giriş Yapılıyor...
                </span>
            </button>
        </form>

        <!-- Kayıt Linki -->
        <div class="mt-6 text-center">
            <p class="text-dark-200 text-sm">
                Üye değil misiniz?
                <button @click="activeTab = 'register'" class="text-mz-400 hover:text-mz-300 font-medium transition-colors">
                    Hemen kayıt olun
                </button>
            </p>
        </div>

    </div>

    <!-- Register Form -->
    <div x-show="activeTab === 'register'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">

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
                    placeholder="Şifre (en az 8 karakter)"
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
                    placeholder="Şifre tekrar"
                >
                <button
                    type="button"
                    @click="showPasswordConfirm = !showPasswordConfirm"
                    class="absolute right-4 top-1/2 -translate-y-1/2 text-dark-300 hover:text-white transition-colors"
                >
                    <i :class="showPasswordConfirm ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                </button>
            </div>

            <!-- Terms & Privacy Consent -->
            <div class="space-y-4">
                <!-- Checkbox: Sözleşme Onayı -->
                <div class="flex items-start gap-3">
                    <input
                        type="checkbox"
                        name="terms"
                        required
                        class="mt-1 w-4 h-4 rounded bg-dark-600 border-dark-500 text-mz-500 focus:ring-mz-500 flex-shrink-0"
                    >
                    <label class="text-sm text-dark-200 leading-relaxed">
                        <a href="javascript:void(0)" @click.prevent="openPageModal(3)" class="text-mz-400 hover:text-mz-300 underline">Kullanım Koşulları ve Üyelik Sözleşmesi</a> ile
                        <a href="javascript:void(0)" @click.prevent="openPageModal(4)" class="text-mz-400 hover:text-mz-300 underline">Üyelik ve Satın Alım Faaliyetleri Kapsamında Aydınlatma Metni</a>'ni okudum, kabul ediyorum.
                    </label>
                </div>

                <!-- Marketing Consent Radios -->
                <div class="space-y-3 pl-7">
                    <!-- Radio 1: Rıza Göstermiyorum -->
                    <div class="flex items-start gap-3">
                        <input
                            type="radio"
                            name="marketing_consent"
                            value="0"
                            required
                            class="mt-1 w-4 h-4 bg-dark-600 border-dark-500 text-mz-500 focus:ring-mz-500 flex-shrink-0"
                        >
                        <label class="text-sm text-dark-200 leading-relaxed">
                            <a href="javascript:void(0)" @click.prevent="openPageModal(5)" class="text-mz-400 hover:text-mz-300 underline">Aydınlatma metninde</a> belirtilen hususlar doğrultusunda, kişisel verilerimin ürün/hizmet ve stratejik pazarlama faaliyetleri ile ticari elektronik ileti gönderimi kapsamında işlenmesine <strong>açık rıza göstermediğimi beyan ederim</strong>.
                        </label>
                    </div>

                    <!-- Radio 2: Rıza Veriyorum -->
                    <div class="flex items-start gap-3">
                        <input
                            type="radio"
                            name="marketing_consent"
                            value="1"
                            class="mt-1 w-4 h-4 bg-dark-600 border-dark-500 text-mz-500 focus:ring-mz-500 flex-shrink-0"
                        >
                        <label class="text-sm text-dark-200 leading-relaxed">
                            <a href="javascript:void(0)" @click.prevent="openPageModal(5)" class="text-mz-400 hover:text-mz-300 underline">Aydınlatma metninde</a> belirtilen hususlar doğrultusunda, kişisel verilerimin ürün/hizmet ve stratejik pazarlama faaliyetleri ile ticari elektronik ileti gönderimi kapsamında işlenmesine <strong>açık rıza verdiğimi kabul ve beyan ederim</strong>.
                        </label>
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <button
                type="submit"
                :disabled="loading"
                class="w-full py-4 bg-gradient-to-r from-mz-500 to-mz-600 hover:from-mz-400 hover:to-mz-500 text-white font-semibold rounded-xl transition-all shadow-lg shadow-mz-500/25 disabled:opacity-50 disabled:cursor-not-allowed"
            >
                <span x-show="!loading" class="flex items-center justify-center gap-2">
                    <i class="fas fa-user-plus"></i>
                    Kayıt Ol
                </span>
                <span x-show="loading" class="flex items-center justify-center gap-2">
                    <i class="fas fa-spinner fa-spin"></i>
                    Hesap Oluşturuluyor...
                </span>
            </button>
        </form>

        <!-- Giriş Linki -->
        <div class="mt-6 text-center">
            <p class="text-dark-200 text-sm">
                Zaten üye misiniz?
                <button @click="activeTab = 'login'" class="text-mz-400 hover:text-mz-300 font-medium transition-colors">
                    Giriş yapın
                </button>
            </p>
        </div>

    </div>

    </div><!-- /Form Container -->
@endsection

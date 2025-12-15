{{-- 🎵 SPOTIFY-STYLE AUTH MODAL - SPA UYUMLU --}}
{{-- Modal backdrop - x-teleport ile body seviyesinde render (SPA kuralı) --}}
<template x-teleport="body">
    <div
        x-show="showAuthModal"
        x-cloak
        @keydown.escape.window="showAuthModal = false"
        class="fixed inset-0 z-[9999] flex items-center justify-center p-4 animate-fade-in"
    >
        {{-- Backdrop with blur - NO CLOSE ON CLICK (prevent accidental data loss) --}}
        <div
            x-show="showAuthModal"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="absolute inset-0 bg-transparent backdrop-blur-md"
        ></div>

        {{-- Modal Container --}}
        <div
            x-show="showAuthModal"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95 -translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 -translate-y-4"
            @click.stop
            class="relative w-full max-w-4xl bg-gradient-to-br from-zinc-900 to-black rounded-2xl shadow-2xl border border-white/10 overflow-hidden"
        >
            {{-- Close Button --}}
            <button
                @click="showAuthModal = false"
                class="absolute top-4 right-4 w-10 h-10 flex items-center justify-center text-white/60 hover:text-white hover:bg-white/10 rounded-full transition-all duration-300 z-10"
            >
                <i class="fas fa-times text-xl"></i>
            </button>

            {{-- Header with Logo --}}
            <div class="text-center pt-8 pb-6 px-8">
                <h1 class="text-3xl font-bold">
                    <span class="bg-gradient-to-r from-muzibu-coral via-muzibu-coral-light to-muzibu-coral bg-clip-text text-transparent animate-gradient">
                        muzibu
                    </span>
                </h1>
                <p class="text-zinc-400 text-sm mt-2" x-text="showAuthModal === 'login' ? 'Tekrar hoş geldin!' : (showAuthModal === 'forgot' ? 'Şifreni sıfırla' : 'Müziğin keyfini çıkar')"></p>
            </div>

            {{-- Tab Switcher --}}
            <div class="flex border-b border-white/10 mx-8">
                <button
                    @click="showAuthModal = 'login'"
                    class="flex-1 py-3 text-sm font-semibold transition-all duration-300 relative"
                    :class="showAuthModal === 'login' ? 'text-white' : 'text-zinc-400 hover:text-white'"
                >
                    Giriş Yap
                    <div
                        x-show="showAuthModal === 'login'"
                        class="absolute bottom-0 left-0 right-0 h-0.5 bg-gradient-to-r from-muzibu-coral to-muzibu-coral-light"
                    ></div>
                </button>
                <button
                    @click="showAuthModal = 'register'"
                    class="flex-1 py-3 text-sm font-semibold transition-all duration-300 relative"
                    :class="showAuthModal === 'register' ? 'text-white' : 'text-zinc-400 hover:text-white'"
                >
                    Üye Ol
                    <div
                        x-show="showAuthModal === 'register'"
                        class="absolute bottom-0 left-0 right-0 h-0.5 bg-gradient-to-r from-muzibu-coral to-muzibu-coral-light"
                    ></div>
                </button>
            </div>

            {{-- Forms Container --}}
            <div class="p-8">
                {{-- LOGIN FORM - CENTERED --}}
                <div x-show="showAuthModal === 'login'" class="max-w-md mx-auto">
                <form
                    @submit.prevent="handleLogin"
                    class="space-y-4"
                >
                    {{-- Email --}}
                    <div>
                        <label for="login-email" class="block text-sm font-medium text-zinc-300 mb-2">
                            E-posta
                        </label>
                        <input
                            type="email"
                            id="login-email"
                            x-model="loginForm.email"
                            @keydown.enter.prevent="$el.form.requestSubmit()"
                            required
                            class="w-full px-4 py-3 bg-white/5 hover:bg-white/10 focus:bg-white/15 border border-white/10 focus:border-muzibu-coral rounded-lg text-white placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-muzibu-coral/50 transition-all duration-300"
                            placeholder="ornek@email.com"
                        >
                    </div>

                    {{-- Password --}}
                    <div>
                        <label for="login-password" class="block text-sm font-medium text-zinc-300 mb-2">
                            Şifre
                        </label>
                        <input
                            type="password"
                            id="login-password"
                            x-model="loginForm.password"
                            @keydown.enter.prevent="$el.form.requestSubmit()"
                            required
                            class="w-full px-4 py-3 bg-white/5 hover:bg-white/10 focus:bg-white/15 border border-white/10 focus:border-muzibu-coral rounded-lg text-white placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-muzibu-coral/50 transition-all duration-300"
                            placeholder="••••••••"
                        >
                    </div>

                    {{-- Remember Me --}}
                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input
                                type="checkbox"
                                x-model="loginForm.remember"
                                class="w-4 h-4 rounded border-white/20 bg-white/5 text-muzibu-coral focus:ring-muzibu-coral focus:ring-offset-0"
                            >
                            <span class="text-sm text-zinc-400 group-hover:text-white transition-colors">Beni hatırla</span>
                        </label>
                        <button type="button" @click="showAuthModal = 'forgot'" class="text-sm text-muzibu-coral hover:text-muzibu-coral-light transition-colors">
                            Şifremi unuttum
                        </button>
                    </div>

                    {{-- Error Message --}}
                    <div x-show="authError" x-text="authError" class="text-sm text-red-400 bg-red-500/10 border border-red-500/20 rounded-lg px-4 py-2"></div>

                    {{-- Submit Button --}}
                    <button
                        type="submit"
                        :disabled="authLoading"
                        class="w-full py-3 px-6 bg-gradient-to-r from-muzibu-coral to-muzibu-coral-light hover:from-muzibu-coral-light hover:to-muzibu-coral rounded-full text-white font-bold text-sm transition-all duration-300 shadow-lg hover:shadow-muzibu-coral/50 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <span x-show="!authLoading">Giriş Yap</span>
                        <span x-show="authLoading" class="flex items-center justify-center gap-2">
                            <i class="fas fa-circle-notch fa-spin"></i>
                            Giriş yapılıyor...
                        </span>
                    </button>
                </form>
                </div>

                {{-- REGISTER FORM - 2 COLUMN GRID --}}
                <form
                    x-show="showAuthModal === 'register'"
                    @submit.prevent="handleRegister"
                    class="space-y-6"
                >
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-5 gap-y-8">
                        {{-- Name --}}
                        <div class="relative mb-2">
                            <label for="register-name" class="block text-sm font-medium text-zinc-300 mb-2">
                                Ad Soyad
                            </label>
                            <div class="relative">
                                <input
                                    type="text"
                                    id="register-name"
                                    x-model="registerForm.name"
                                    @blur="validateName()"
                                    class="w-full px-4 py-3 pr-12 bg-white/5 hover:bg-white/10 focus:bg-white/15 border transition-all duration-300 rounded-lg text-white placeholder-zinc-500 focus:outline-none focus:ring-2"
                                    :class="{
                                        'border-white/10 focus:border-muzibu-coral focus:ring-muzibu-coral/50': !validation.name.checked,
                                        'border-red-500 focus:border-red-500 focus:ring-red-500/50': validation.name.checked && !validation.name.valid,
                                        'border-green-500 focus:border-green-500 focus:ring-green-500/50': validation.name.valid
                                    }"
                                    placeholder="Adınız Soyadınız"
                                >
                                {{-- Icon Feedback --}}
                                <div class="absolute right-3 top-1/2 -translate-y-1/2">
                                    <i x-show="validation.name.checked && !validation.name.valid" class="fas fa-times text-red-500"></i>
                                    <i x-show="validation.name.valid" class="fas fa-check text-green-500"></i>
                                </div>
                            </div>
                            {{-- Error Message (Absolute) --}}
                            <p x-show="validation.name.checked && !validation.name.valid" x-text="validation.name.message" class="absolute left-0 -bottom-6 text-xs text-red-400 mt-1"></p>
                        </div>

                        {{-- Email --}}
                        <div class="relative mb-2">
                            <label for="register-email" class="block text-sm font-medium text-zinc-300 mb-2">
                                E-posta
                            </label>
                            <div class="relative">
                                <input
                                    type="email"
                                    id="register-email"
                                    x-model="registerForm.email"
                                    @blur="validateEmail()"
                                    class="w-full px-4 py-3 pr-12 bg-white/5 hover:bg-white/10 focus:bg-white/15 border transition-all duration-300 rounded-lg text-white placeholder-zinc-500 focus:outline-none focus:ring-2"
                                    :class="{
                                        'border-white/10 focus:border-muzibu-coral focus:ring-muzibu-coral/50': !validation.email.checked,
                                        'border-red-500 focus:border-red-500 focus:ring-red-500/50': validation.email.checked && !validation.email.valid,
                                        'border-green-500 focus:border-green-500 focus:ring-green-500/50': validation.email.valid
                                    }"
                                    placeholder="ornek@email.com"
                                >
                                {{-- Icon Feedback --}}
                                <div class="absolute right-3 top-1/2 -translate-y-1/2">
                                    <i x-show="validation.email.checked && !validation.email.valid" class="fas fa-times text-red-500"></i>
                                    <i x-show="validation.email.valid" class="fas fa-check text-green-500"></i>
                                </div>
                            </div>
                            {{-- Error Message (Absolute) --}}
                            <p x-show="validation.email.checked && !validation.email.valid" x-text="validation.email.message" class="absolute left-0 -bottom-6 text-xs text-red-400 mt-1"></p>
                        </div>

                        {{-- Phone Number --}}
                        <div class="relative mb-2">
                            <label for="register-phone" class="block text-sm font-medium text-zinc-300 mb-2">
                                Telefon Numarası
                            </label>
                            <div class="relative">
                                <input
                                    type="tel"
                                    id="register-phone"
                                    x-model="registerForm.phone"
                                    @input="registerForm.phone = registerForm.phone.replace(/[^0-9]/g, '').slice(0, 11)"
                                    @blur="validatePhone()"
                                    class="w-full px-4 py-3 pr-12 bg-white/5 hover:bg-white/10 focus:bg-white/15 border transition-all duration-300 rounded-lg text-white placeholder-zinc-500 focus:outline-none focus:ring-2"
                                    :class="{
                                        'border-white/10 focus:border-muzibu-coral focus:ring-muzibu-coral/50': !validation.phone.checked,
                                        'border-red-500 focus:border-red-500 focus:ring-red-500/50': validation.phone.checked && !validation.phone.valid,
                                        'border-green-500 focus:border-green-500 focus:ring-green-500/50': validation.phone.valid
                                    }"
                                    placeholder="5xxxxxxxxx"
                                >
                                {{-- Icon Feedback --}}
                                <div class="absolute right-3 top-1/2 -translate-y-1/2">
                                    <i x-show="validation.phone.checked && !validation.phone.valid" class="fas fa-times text-red-500"></i>
                                    <i x-show="validation.phone.valid" class="fas fa-check text-green-500"></i>
                                </div>
                            </div>
                            {{-- Error Message (Absolute) --}}
                            <p x-show="validation.phone.checked && !validation.phone.valid" x-text="validation.phone.message" class="absolute left-0 -bottom-6 text-xs text-red-400 mt-1"></p>
                        </div>

                        {{-- Password --}}
                        <div class="relative mb-2">
                            <label for="register-password" class="block text-sm font-medium text-zinc-300 mb-2">
                                Şifre
                            </label>
                            <div class="relative">
                                <input
                                    type="password"
                                    id="register-password"
                                    x-model="registerForm.password"
                                    @blur="validatePassword()"
                                    @input="if(validation.password_confirmation.checked) validatePasswordConfirmation()"
                                    class="w-full px-4 py-3 pr-12 bg-white/5 hover:bg-white/10 focus:bg-white/15 border transition-all duration-300 rounded-lg text-white placeholder-zinc-500 focus:outline-none focus:ring-2"
                                    :class="{
                                        'border-white/10 focus:border-muzibu-coral focus:ring-muzibu-coral/50': !validation.password.checked,
                                        'border-red-500 focus:border-red-500 focus:ring-red-500/50': validation.password.checked && !validation.password.valid,
                                        'border-green-500 focus:border-green-500 focus:ring-green-500/50': validation.password.valid
                                    }"
                                    placeholder="••••••••"
                                >
                                {{-- Icon Feedback --}}
                                <div class="absolute right-3 top-1/2 -translate-y-1/2">
                                    <i x-show="validation.password.checked && !validation.password.valid" class="fas fa-times text-red-500"></i>
                                    <i x-show="validation.password.valid" class="fas fa-check text-green-500"></i>
                                </div>
                            </div>
                            {{-- Error Message (Absolute) --}}
                            <p x-show="validation.password.checked && !validation.password.valid" x-text="validation.password.message" class="absolute left-0 -bottom-6 text-xs text-red-400 mt-1"></p>
                        </div>

                        {{-- Password Confirmation --}}
                        <div class="relative md:col-span-2 mb-2">
                            <label for="register-password-confirmation" class="block text-sm font-medium text-zinc-300 mb-2">
                                Şifre Tekrar
                            </label>
                            <div class="relative">
                                <input
                                    type="password"
                                    id="register-password-confirmation"
                                    x-model="registerForm.password_confirmation"
                                    @blur="validatePasswordConfirmation()"
                                    class="w-full px-4 py-3 pr-12 bg-white/5 hover:bg-white/10 focus:bg-white/15 border transition-all duration-300 rounded-lg text-white placeholder-zinc-500 focus:outline-none focus:ring-2"
                                    :class="{
                                        'border-white/10 focus:border-muzibu-coral focus:ring-muzibu-coral/50': !validation.password_confirmation.checked,
                                        'border-red-500 focus:border-red-500 focus:ring-red-500/50': validation.password_confirmation.checked && !validation.password_confirmation.valid,
                                        'border-green-500 focus:border-green-500 focus:ring-green-500/50': validation.password_confirmation.valid
                                    }"
                                    placeholder="••••••••"
                                >
                                {{-- Icon Feedback --}}
                                <div class="absolute right-3 top-1/2 -translate-y-1/2">
                                    <i x-show="validation.password_confirmation.checked && !validation.password_confirmation.valid" class="fas fa-times text-red-500"></i>
                                    <i x-show="validation.password_confirmation.valid" class="fas fa-check text-green-500"></i>
                                </div>
                            </div>
                            {{-- Error Message (Absolute) --}}
                            <p x-show="validation.password_confirmation.checked && !validation.password_confirmation.valid" x-text="validation.password_confirmation.message" class="absolute left-0 -bottom-6 text-xs text-red-400 mt-1"></p>
                        </div>
                    </div>

                    {{-- Error Message --}}
                    <div x-show="authError" x-text="authError" class="text-sm text-red-400 bg-red-500/10 border border-red-500/20 rounded-lg px-4 py-2"></div>

                    {{-- Submit Button --}}
                    <button
                        type="submit"
                        :disabled="authLoading"
                        class="w-full py-3 px-6 bg-gradient-to-r from-muzibu-coral to-muzibu-coral-light hover:from-muzibu-coral-light hover:to-muzibu-coral rounded-full text-white font-bold text-sm transition-all duration-300 shadow-lg hover:shadow-muzibu-coral/50 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <span x-show="!authLoading">Üye Ol</span>
                        <span x-show="authLoading" class="flex items-center justify-center gap-2">
                            <i class="fas fa-circle-notch fa-spin"></i>
                            Kaydediliyor...
                        </span>
                    </button>

                    {{-- Terms --}}
                    <p class="text-xs text-zinc-500 text-center mt-4">
                        Üye olarak <a href="#" class="text-muzibu-coral hover:underline">Kullanım Koşulları</a>'nı ve
                        <a href="#" class="text-muzibu-coral hover:underline">Gizlilik Politikası</a>'nı kabul etmiş olursunuz.
                    </p>
                </form>

                {{-- FORGOT PASSWORD FORM - CENTERED --}}
                <div x-show="showAuthModal === 'forgot'" class="max-w-md mx-auto">
                <form
                    @submit.prevent="handleForgotPassword"
                    class="space-y-4"
                >
                    <div class="text-center mb-6">
                        <p class="text-zinc-400 text-sm">
                            E-posta adresinize şifre sıfırlama bağlantısı göndereceğiz.
                        </p>
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="forgot-email" class="block text-sm font-medium text-zinc-300 mb-2">
                            E-posta
                        </label>
                        <input
                            type="email"
                            id="forgot-email"
                            x-model="forgotForm.email"
                            required
                            class="w-full px-4 py-3 bg-white/5 hover:bg-white/10 focus:bg-white/15 border border-white/10 focus:border-muzibu-coral rounded-lg text-white placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-muzibu-coral/50 transition-all duration-300"
                            placeholder="ornek@email.com"
                        >
                    </div>

                    {{-- Error Message --}}
                    <div x-show="authError" x-text="authError" class="text-sm text-red-400 bg-red-500/10 border border-red-500/20 rounded-lg px-4 py-2"></div>

                    {{-- Success Message --}}
                    <div x-show="authSuccess" x-text="authSuccess" class="text-sm text-green-400 bg-green-500/10 border border-green-500/20 rounded-lg px-4 py-2"></div>

                    {{-- Submit Button --}}
                    <button
                        type="submit"
                        :disabled="authLoading"
                        class="w-full py-3 px-6 bg-gradient-to-r from-muzibu-coral to-muzibu-coral-light hover:from-muzibu-coral-light hover:to-muzibu-coral rounded-full text-white font-bold text-sm transition-all duration-300 shadow-lg hover:shadow-muzibu-coral/50 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <span x-show="!authLoading">Sıfırlama Bağlantısı Gönder</span>
                        <span x-show="authLoading" class="flex items-center justify-center gap-2">
                            <i class="fas fa-circle-notch fa-spin"></i>
                            Gönderiliyor...
                        </span>
                    </button>

                    {{-- Back to Login --}}
                    <div class="text-center">
                        <button type="button" @click="showAuthModal = 'login'" class="text-sm text-muzibu-coral hover:text-muzibu-coral-light transition-colors">
                            ← Giriş sayfasına dön
                        </button>
                    </div>
                </form>
                </div>
            </div>
        </div>
    </div>
</template>

<style>
    [x-cloak] { display: none !important; }
</style>

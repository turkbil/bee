<x-guest-layout>
    <div x-data="authPage()" class="w-full">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 sm:p-8">
            {{-- Header --}}
            <div class="text-center mb-6">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('auth.login') }}</h1>
                <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">{{ __('auth.login_subtitle') }}</p>
            </div>

            {{-- Status Messages --}}
            @if (session('status') || request('logged_out'))
                <div class="mb-4 p-3 bg-green-100 dark:bg-green-900/30 border border-green-300 dark:border-green-700 rounded-lg text-sm text-green-700 dark:text-green-300">
                    {{ request('logged_out') ? __('auth.logged_out_success') : session('status') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 p-3 bg-red-100 dark:bg-red-900/30 border border-red-300 dark:border-red-700 rounded-lg text-sm text-red-700 dark:text-red-300">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Login Form --}}
            <form method="POST" action="{{ route('login') }}" class="space-y-4" novalidate @submit="if(!validateForm()) { $event.preventDefault(); isLoading = false; } else { isLoading = true; clearCartLocalStorage(); }">
                @csrf

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('validation.attributes.email') }}</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" autocomplete="email"
                           placeholder="{{ __('auth.email_placeholder') }}"
                           @input="clearError('email')" @blur="validateField('email', $event.target.value)"
                           :class="errors.email ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 dark:border-gray-600 focus:ring-primary-500'"
                           class="w-full px-3 py-2.5 border rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:border-transparent transition-colors">
                    <p x-show="errors.email" x-text="errors.email" class="mt-1 text-sm text-red-600 dark:text-red-400"></p>
                </div>

                {{-- Password --}}
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('validation.attributes.password') }}</label>
                    <div class="relative">
                        <input :type="showPassword ? 'text' : 'password'" name="password" id="password" autocomplete="current-password"
                               placeholder="{{ __('auth.enter_password') }}"
                               @input="clearError('password')" @blur="validateField('password', $event.target.value)"
                               :class="errors.password ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 dark:border-gray-600 focus:ring-primary-500'"
                               class="w-full px-3 py-2.5 pr-10 border rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:border-transparent transition-colors">
                        <button type="button" @click="showPassword = !showPassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <i :class="showPassword ? 'far fa-eye-slash' : 'far fa-eye'"></i>
                        </button>
                    </div>
                    <p x-show="errors.password" x-text="errors.password" class="mt-1 text-sm text-red-600 dark:text-red-400"></p>
                </div>

                {{-- Remember & Forgot --}}
                <div class="flex items-center justify-between text-sm">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" x-model="rememberMe" class="w-4 h-4 rounded border-gray-300 dark:border-gray-600 text-primary-600 focus:ring-primary-500">
                        <span class="text-gray-600 dark:text-gray-400">{{ __('validation.attributes.remember') }}</span>
                    </label>
                    <a href="{{ route('password.request') }}" class="text-primary-600 dark:text-primary-400 hover:underline">{{ __('auth.forgot_password') }}</a>
                </div>

                {{-- Submit --}}
                <button type="submit" :disabled="isLoading"
                        class="w-full py-2.5 bg-primary-600 hover:bg-primary-700 disabled:opacity-50 text-white font-medium rounded-lg transition-colors flex items-center justify-center gap-2">
                    <svg x-show="isLoading" class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span x-text="isLoading ? '{{ __('auth.logging_in') }}' : '{{ __('auth.login') }}'"></span>
                </button>

                {{-- Register Link --}}
                <p class="text-center text-sm text-gray-600 dark:text-gray-400">
                    {{ __('auth.no_account') }}
                    <a href="{{ route('register') }}" class="text-primary-600 dark:text-primary-400 font-medium hover:underline">{{ __('auth.register') }}</a>
                </p>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function authPage() {
            return {
                showPassword: false,
                isLoading: false,
                rememberMe: false,
                errors: {
                    email: @json($errors->first('email')),
                    password: @json($errors->first('password'))
                },

                init() {
                    @if($errors->any())
                        this.isLoading = false;
                    @endif
                },

                clearError(field) {
                    this.errors[field] = null;
                },

                validateField(field, value) {
                    const messages = @json(__('validation'));
                    const attributes = @json(__('validation.attributes'));

                    switch(field) {
                        case 'email':
                            if (!value) {
                                this.errors.email = messages.required.replace(':attribute', attributes.email);
                            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
                                this.errors.email = messages.email.replace(':attribute', attributes.email);
                            } else {
                                this.errors.email = null;
                            }
                            break;
                        case 'password':
                            if (!value) {
                                this.errors.password = messages.required.replace(':attribute', attributes.password);
                            } else {
                                this.errors.password = null;
                            }
                            break;
                    }
                },

                validateForm() {
                    const email = document.querySelector('input[name="email"]').value;
                    const password = document.querySelector('input[name="password"]').value;
                    this.validateField('email', email);
                    this.validateField('password', password);
                    return !this.errors.email && !this.errors.password;
                },

                clearCartLocalStorage() {
                    try {
                        localStorage.removeItem('cart_id');
                        localStorage.removeItem('cart_item_count');
                    } catch (e) {}
                }
            }
        }
    </script>
    @endpush
</x-guest-layout>

<x-guest-layout>
    <div x-data="authPage()" class="w-full">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl overflow-hidden">
            <div class="lg:grid lg:grid-cols-2 lg:gap-0">
                <!-- Left Column - Form -->
                <div class="px-8 py-16 lg:px-12">
                    <!-- Header -->
                    <div class="mb-8">
                        <div class="flex items-center mb-6">
                            <div class="w-12 h-12 bg-gradient-to-br from-blue-600 to-purple-600 rounded-lg flex items-center justify-center mr-4">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Login') }}</h2>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ request()->getHost() }}</p>
                            </div>
                        </div>
                        <p class="text-gray-600 dark:text-gray-400">{{ __('Log in to your account to continue') }}</p>
                    </div>

                    <!-- Status Messages -->
                    @if (session('status'))
                        <div x-data="{ show: true }" x-show="show" x-transition class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg dark:bg-blue-900/20 dark:border-blue-800">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                                <p class="text-sm text-blue-800 dark:text-blue-200">{{ session('status') }}</p>
                                <button @click="show = false" class="ml-auto text-blue-500 hover:text-blue-700">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @endif

                    <!-- Login Form -->
                    <form method="POST" action="{{ route('login') }}" class="space-y-6" novalidate @submit="if(!validateForm()) { $event.preventDefault(); isLoading = false; } else { isLoading = true; }">
                        @csrf
                        
                        <!-- Email Field -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('validation.attributes.email') }}</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                                    </svg>
                                </div>
                                <input 
                                    type="email" 
                                    name="email" 
                                    id="email"
                                    value="{{ old('email') }}" 
                                    autocomplete="email"
                                    placeholder="email@domain.com"
                                    @input="clearError('email')"
                                    @blur="validateField('email', $event.target.value)"
                                    :class="errors.email ? 'w-full pl-10 pr-3 py-3 border border-red-500 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-gray-100 transition-colors' : 'w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-100 transition-colors'"
                                />
                            </div>
                            <div x-show="errors.email" x-transition class="mt-2">
                                <p class="text-sm text-red-600 dark:text-red-400 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    <span x-text="errors.email"></span>
                                </p>
                            </div>
                        </div>

                        <!-- Password Field -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('validation.attributes.password') }}</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                </div>
                                <input 
                                    x-bind:type="showPassword ? 'text' : 'password'"
                                    name="password" 
                                    id="password"
                                    autocomplete="current-password"
                                    placeholder="{{ __('Enter your password') }}"
                                    @input="clearError('password')"
                                    @blur="validateField('password', $event.target.value)"
                                    :class="errors.password ? 'w-full pl-10 pr-12 py-3 border border-red-500 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-gray-100 transition-colors' : 'w-full pl-10 pr-12 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-100 transition-colors'"
                                />
                                <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <svg x-show="!showPassword" class="w-5 h-5 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    <svg x-show="showPassword" class="w-5 h-5 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"/>
                                    </svg>
                                </button>
                            </div>
                            <div x-show="errors.password" x-transition class="mt-2">
                                <p class="text-sm text-red-600 dark:text-red-400 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    <span x-text="errors.password"></span>
                                </p>
                            </div>
                        </div>

                        <!-- Options Row -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" name="remember" id="remember" class="sr-only" x-model="rememberMe">
                                    <div class="relative">
                                        <!-- Switch Background -->
                                        <div :class="rememberMe ? 'bg-gradient-to-r from-blue-500 to-purple-600' : 'bg-gray-300 dark:bg-gray-600'" 
                                             class="block w-11 h-6 rounded-full transition-colors duration-200"></div>
                                        <!-- Switch Circle -->
                                        <div :class="rememberMe ? 'translate-x-6' : 'translate-x-1'" 
                                             class="absolute left-0 top-1 bg-white w-4 h-4 rounded-full transition-transform duration-200 shadow-md"></div>
                                    </div>
                                    <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('validation.attributes.remember') }}</span>
                                </label>
                            </div>
                            <div>
                                <a href="{{ route('password.request') }}" class="text-sm text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300 font-medium transition-colors">
                                    {{ __('Forgot your password?') }}
                                </a>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button 
                            type="submit" 
                            :disabled="isLoading"
                            class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200"
                        >
                            <svg x-show="isLoading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span x-show="!isLoading">{{ __('Login') }}</span>
                            <span x-show="isLoading">{{ __('Logging in...') }}</span>
                            <svg x-show="!isLoading" class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                        </button>

                        <!-- Register Link -->
                        <div class="text-center">
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                {{ __("Don't have an account?") }} 
                                <a href="{{ route('register') }}" class="font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">
                                    {{ __('Register') }}
                                </a>
                            </p>
                        </div>
                    </form>

                    <!-- Quick Demo Login -->
                    <div class="mt-8 pt-8 border-t border-gray-200 dark:border-gray-700">
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-3 text-center">{{ __('Quick Test Login:') }}</p>
                        <div class="grid grid-cols-3 gap-2">
                            @php $host = request()->getHost(); @endphp
                            
                            <!-- Nurullah ve Turkbil her zaman görünür -->
                            <button type="button" @click="autoLogin('nurullah')" 
                                    class="p-3 bg-gradient-to-r from-yellow-500 to-orange-500 hover:from-yellow-600 hover:to-orange-600 rounded-lg transition-all transform hover:scale-105 text-center shadow-lg">
                                <div class="text-sm font-bold text-white">Nurullah</div>
                                <div class="text-xs text-yellow-100 opacity-90">Root</div>
                            </button>
                            
                            <button type="button" @click="autoLogin('turkbilisim')" 
                                    class="p-3 bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 rounded-lg transition-all transform hover:scale-105 text-center shadow-lg">
                                <div class="text-sm font-bold text-white">Turkbil</div>
                                <div class="text-xs text-purple-100 opacity-90">Admin</div>
                            </button>
                            
                            <!-- Domain'e özel tek kullanıcı -->
                            @if($host === 'laravel.test')
                                <button type="button" @click="autoLogin('laravel')" 
                                        class="p-3 bg-gradient-to-r from-blue-500 to-cyan-500 hover:from-blue-600 hover:to-cyan-600 rounded-lg transition-all transform hover:scale-105 text-center shadow-lg">
                                    <div class="text-sm font-bold text-white">Laravel</div>
                                    <div class="text-xs text-blue-100 opacity-90">Test</div>
                                </button>
                            @elseif($host === 'a.test')
                                <button type="button" @click="autoLogin('a')" 
                                        class="p-3 bg-gradient-to-r from-green-500 to-teal-500 hover:from-green-600 hover:to-teal-600 rounded-lg transition-all transform hover:scale-105 text-center shadow-lg">
                                    <div class="text-sm font-bold text-white">A User</div>
                                    <div class="text-xs text-green-100 opacity-90">Test</div>
                                </button>
                            @elseif($host === 'b.test')
                                <button type="button" @click="autoLogin('b')" 
                                        class="p-3 bg-gradient-to-r from-red-500 to-rose-500 hover:from-red-600 hover:to-rose-600 rounded-lg transition-all transform hover:scale-105 text-center shadow-lg">
                                    <div class="text-sm font-bold text-white">B User</div>
                                    <div class="text-xs text-red-100 opacity-90">Test</div>
                                </button>
                            @elseif($host === 'c.test')
                                <button type="button" @click="autoLogin('c')" 
                                        class="p-3 bg-gradient-to-r from-indigo-500 to-blue-500 hover:from-indigo-600 hover:to-blue-600 rounded-lg transition-all transform hover:scale-105 text-center shadow-lg">
                                    <div class="text-sm font-bold text-white">C User</div>
                                    <div class="text-xs text-indigo-100 opacity-90">Test</div>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Right Column - Tetris Game -->
                <div class="bg-gradient-to-br from-blue-600 to-purple-700 dark:from-blue-800 dark:to-purple-900 px-8 py-16 lg:px-12 flex flex-col justify-center relative overflow-hidden">
                    <!-- Animated Background Elements -->
                    <div class="absolute inset-0 overflow-hidden">
                        <div class="absolute -top-20 -right-20 w-40 h-40 bg-white/10 rounded-full animate-pulse"></div>
                        <div class="absolute -bottom-16 -left-16 w-32 h-32 bg-white/5 rounded-full animate-bounce" style="animation-delay: 2s;"></div>
                        <div class="absolute top-1/3 right-10 w-24 h-24 bg-white/5 rounded-full animate-ping" style="animation-delay: 1s;"></div>
                    </div>

                    <!-- Tetris Game Component -->
                    <div class="relative z-10 h-full flex items-center justify-center">
                        <x-tetris-game />
                    </div>
                </div>
            </div>
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
                    // Eğer validation error varsa isLoading'i false yap
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
                
                autoLogin(userType) {
                    this.isLoading = true;
                    
                    let email, password;
                    
                    switch(userType) {
                        case 'nurullah':
                            email = 'nurullah@nurullah.net';
                            password = 'test';
                            break;
                        case 'turkbilisim':
                            email = 'info@turkbilisim.com.tr';
                            password = 'test';
                            break;
                        case 'laravel':
                            email = 'laravel@test';
                            password = 'test';
                            break;
                        case 'a':
                            email = 'a@test';
                            password = 'test';
                            break;
                        case 'b':
                            email = 'b@test';
                            password = 'test';
                            break;
                        case 'c':
                            email = 'c@test';
                            password = 'test';
                            break;
                    }
                    
                    // Fill form
                    document.querySelector('input[name="email"]').value = email;
                    document.querySelector('input[name="password"]').value = password;
                    
                    // Submit form after short delay
                    setTimeout(() => {
                        document.querySelector('form').submit();
                    }, 500);
                }
            }
        }
    </script>
    @endpush
</x-guest-layout>
<x-guest-layout>
    <div x-data="authPage()" class="w-full">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl overflow-hidden">
            <div class="lg:grid lg:grid-cols-2 lg:gap-0">
                <!-- Left Column - Form -->
                <div class="px-8 py-16 lg:px-12">
                    <!-- Header -->
                    <div class="mb-8">
                        <div class="flex items-center mb-6">
                            <div class="w-12 h-12 bg-gradient-to-br from-green-600 to-teal-600 rounded-lg flex items-center justify-center mr-4">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('auth.register_title') }}</h2>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ request()->getHost() }}</p>
                            </div>
                        </div>
                        <p class="text-gray-600 dark:text-gray-400">{{ __('auth.register_subtitle') }}</p>
                    </div>

                    <!-- Register Form -->
                    <form method="POST" action="{{ route('register') }}" class="space-y-6">
                        @csrf
                        
                        <!-- Name Field -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('auth.name_field') }}</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                                <input 
                                    type="text" 
                                    name="name" 
                                    id="name"
                                    value="{{ old('name') }}" 
                                    required 
                                    autocomplete="name"
                                    placeholder="{{ __('auth.name_placeholder') }}"
                                    class="w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-gray-100 transition-colors @error('name') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
                                />
                            </div>
                            @error('name')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Email Field -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('auth.email_field') }}</label>
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
                                    required
                                    autocomplete="email"
                                    placeholder="{{ __('auth.email_placeholder') }}"
                                    class="w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-gray-100 transition-colors @error('email') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
                                />
                            </div>
                            @error('email')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Password Field -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('auth.password_field') }}</label>
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
                                    required 
                                    autocomplete="new-password"
                                    placeholder="{{ __('auth.password_placeholder') }}"
                                    class="w-full pl-10 pr-12 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-gray-100 transition-colors @error('password') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
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
                            @error('password')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Password Confirmation Field -->
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('auth.password_confirmation_field') }}</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <input 
                                    x-bind:type="showPasswordConfirm ? 'text' : 'password'"
                                    name="password_confirmation" 
                                    id="password_confirmation"
                                    required 
                                    autocomplete="new-password"
                                    placeholder="{{ __('auth.password_confirmation_placeholder') }}"
                                    class="w-full pl-10 pr-12 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-gray-100 transition-colors @error('password_confirmation') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
                                />
                                <button type="button" @click="showPasswordConfirm = !showPasswordConfirm" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <svg x-show="!showPasswordConfirm" class="w-5 h-5 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    <svg x-show="showPasswordConfirm" class="w-5 h-5 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"/>
                                    </svg>
                                </button>
                            </div>
                            @error('password_confirmation')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <button 
                            type="submit" 
                            :disabled="isLoading"
                            @click="isLoading = true"
                            class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-green-600 to-teal-600 hover:from-green-700 hover:to-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200"
                        >
                            <svg x-show="isLoading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span x-show="!isLoading">{{ __('auth.create_account') }}</span>
                            <span x-show="isLoading">{{ __('auth.creating_account') }}</span>
                            <svg x-show="!isLoading" class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                            </svg>
                        </button>

                        <!-- Login Link -->
                        <div class="text-center">
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                {{ __('auth.already_have_account') }}
                                <a href="{{ route('login') }}" class="font-medium text-green-600 hover:text-green-500 dark:text-green-400 dark:hover:text-green-300">
                                    {{ __('auth.login_link') }}
                                </a>
                            </p>
                        </div>
                    </form>
                </div>

                <!-- Right Column - SVG & Details -->
                <div class="hidden lg:flex bg-gradient-to-br from-green-600 to-teal-700 dark:from-green-800 dark:to-teal-900 px-8 py-16 lg:px-12 flex-col justify-center relative overflow-hidden">
                    <!-- Animated Background Elements -->
                    <div class="absolute inset-0 overflow-hidden">
                        <div class="absolute -top-20 -right-20 w-40 h-40 bg-white/10 rounded-full animate-pulse"></div>
                        <div class="absolute -bottom-16 -left-16 w-32 h-32 bg-white/5 rounded-full animate-bounce" style="animation-delay: 2s;"></div>
                        <div class="absolute top-1/3 right-10 w-24 h-24 bg-white/5 rounded-full animate-ping" style="animation-delay: 1s;"></div>
                    </div>

                    <!-- Main SVG Art -->
                    <div class="relative z-10 text-center">
                        <div class="mb-8">
                            <svg class="w-80 h-80 mx-auto" viewBox="0 0 400 400" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <!-- Organic Nature-Inspired Art - Register Theme -->
                                
                                <!-- Growing tree/branch -->
                                <g class="animate-pulse" style="animation-duration: 6s;">
                                    <path d="M200 350 Q180 300 160 250 Q140 200 170 150 Q200 100 230 150 Q260 200 240 250 Q220 300 200 350" stroke="white" stroke-opacity="0.4" stroke-width="4" fill="none"/>
                                    <path d="M200 350 Q190 320 180 290" stroke="white" stroke-opacity="0.3" stroke-width="2" fill="none"/>
                                    <path d="M200 350 Q210 320 220 290" stroke="white" stroke-opacity="0.3" stroke-width="2" fill="none"/>
                                </g>
                                
                                <!-- Floating leaves -->
                                <g class="animate-bounce" style="animation-delay: 1s; animation-duration: 4s;">
                                    <ellipse cx="140" cy="180" rx="8" ry="15" fill="white" fill-opacity="0.3" transform="rotate(45 140 180)"/>
                                    <ellipse cx="260" cy="220" rx="6" ry="12" fill="white" fill-opacity="0.4" transform="rotate(-30 260 220)"/>
                                    <ellipse cx="190" cy="120" rx="7" ry="14" fill="white" fill-opacity="0.35" transform="rotate(20 190 120)"/>
                                </g>
                                
                                <!-- Spiral growth patterns -->
                                <g class="animate-spin" style="animation-duration: 25s;">
                                    <path d="M300 150 Q310 140 320 150 Q330 160 320 170 Q310 180 300 170 Q290 160 300 150" stroke="white" stroke-opacity="0.2" stroke-width="2" fill="none"/>
                                </g>
                                
                                <g class="animate-spin" style="animation-duration: 30s; animation-direction: reverse;">
                                    <path d="M100 250 Q110 240 120 250 Q130 260 120 270 Q110 280 100 270 Q90 260 100 250" stroke="white" stroke-opacity="0.25" stroke-width="1.5" fill="none"/>
                                </g>
                                
                                <!-- Flowing water/wind -->
                                <g class="animate-pulse" style="animation-delay: 2s; animation-duration: 5s;">
                                    <path d="M50 200 Q100 180 150 200 Q200 220 250 200 Q300 180 350 200" stroke="white" stroke-opacity="0.2" stroke-width="3" fill="none"/>
                                    <path d="M30 240 Q80 220 130 240 Q180 260 230 240 Q280 220 330 240" stroke="white" stroke-opacity="0.15" stroke-width="2" fill="none"/>
                                </g>
                                
                                <!-- Seed/dot constellation -->
                                <g class="animate-pulse" style="animation-delay: 3s; animation-duration: 4s;">
                                    <circle cx="120" cy="300" r="4" fill="white" fill-opacity="0.5"/>
                                    <circle cx="280" cy="280" r="3" fill="white" fill-opacity="0.6"/>
                                    <circle cx="340" cy="320" r="2.5" fill="white" fill-opacity="0.4"/>
                                    <circle cx="80" cy="180" r="3.5" fill="white" fill-opacity="0.45"/>
                                </g>
                                
                                <!-- Organic blob shapes -->
                                <g class="animate-pulse" style="animation-delay: 1.5s; animation-duration: 8s;">
                                    <path d="M350 100 Q370 80 380 110 Q370 140 350 130 Q330 120 340 100 Q350 90 350 100" fill="white" fill-opacity="0.1" stroke="white" stroke-opacity="0.2" stroke-width="1"/>
                                    <path d="M60 320 Q80 300 90 330 Q80 360 60 350 Q40 340 50 320 Q60 310 60 320" fill="white" fill-opacity="0.08" stroke="white" stroke-opacity="0.15" stroke-width="1"/>
                                </g>
                                
                                <!-- Floating petals -->
                                <g class="animate-bounce" style="animation-delay: 4s; animation-duration: 3s;">
                                    <path d="M320 280 Q325 275 330 280 Q325 285 320 280" fill="white" fill-opacity="0.4"/>
                                    <path d="M150 350 Q155 345 160 350 Q155 355 150 350" fill="white" fill-opacity="0.35"/>
                                    <path d="M70 120 Q75 115 80 120 Q75 125 70 120" fill="white" fill-opacity="0.3"/>
                                </g>
                                
                                <!-- Interconnected network -->
                                <g class="animate-pulse" style="animation-delay: 0.5s; animation-duration: 7s;">
                                    <line x1="120" y1="300" x2="140" y2="180" stroke="white" stroke-opacity="0.1" stroke-width="1"/>
                                    <line x1="260" y1="220" x2="280" y2="280" stroke="white" stroke-opacity="0.12" stroke-width="1"/>
                                    <line x1="190" y1="120" x2="320" y2="280" stroke="white" stroke-opacity="0.08" stroke-width="1"/>
                                </g>
                            </svg>
                        </div>

                        <!-- Welcome Text -->
                        <div class="text-white">
                            <h3 class="text-2xl font-bold mb-4">{{ config('app.name') }} Ailesine Katılın</h3>
                            <p class="text-green-100 mb-6 leading-relaxed">
                                Ücretsiz hesabınızı oluşturun ve platformun tüm özelliklerinden 
                                yararlanmaya başlayın. Hızlı kayıt, anında erişim.
                            </p>
                            
                            <!-- Benefits -->
                            <div class="space-y-3 text-left">
                                <div class="flex items-center text-green-100">
                                    <svg class="w-5 h-5 mr-3 text-green-300" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>Tamamen Ücretsiz</span>
                                </div>
                                <div class="flex items-center text-green-100">
                                    <svg class="w-5 h-5 mr-3 text-green-300" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>Anında Aktif Hesap</span>
                                </div>
                                <div class="flex items-center text-green-100">
                                    <svg class="w-5 h-5 mr-3 text-green-300" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>Güvenli Kayıt</span>
                                </div>
                            </div>

                            <!-- Registration Steps -->
                            <div class="mt-8 pt-6 border-t border-white/20">
                                <p class="text-sm text-green-200 mb-3">Kayıt Süreci:</p>
                                <div class="space-y-2 text-sm text-green-100">
                                    <div class="flex items-center">
                                        <span class="w-6 h-6 bg-green-600 text-white rounded-full flex items-center justify-center text-xs mr-2">1</span>
                                        <span>Bilgilerinizi girin</span>
                                    </div>
                                    <div class="flex items-center">
                                        <span class="w-6 h-6 bg-green-600 text-white rounded-full flex items-center justify-center text-xs mr-2">2</span>
                                        <span>Email doğrulaması</span>
                                    </div>
                                    <div class="flex items-center">
                                        <span class="w-6 h-6 bg-green-600 text-white rounded-full flex items-center justify-center text-xs mr-2">3</span>
                                        <span>Hesabınız hazır!</span>
                                    </div>
                                </div>
                            </div>
                        </div>
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
                showPasswordConfirm: false,
                isLoading: false
            }
        }
    </script>
    @endpush
</x-guest-layout>
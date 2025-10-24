<x-guest-layout>
    <div x-data="authPage()" class="w-full">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl overflow-hidden">
            <div class="lg:grid lg:grid-cols-2 lg:gap-0">
                <!-- Left Column - Form -->
                <div class="px-8 py-16 lg:px-12">
                    <!-- Header -->
                    <div class="mb-8">
                        <div class="flex items-center mb-6">
                            <div class="w-12 h-12 bg-gradient-to-br from-orange-600 to-red-600 rounded-lg flex items-center justify-center mr-4">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('auth.forgot_password_title') }}</h2>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ request()->getHost() }}</p>
                            </div>
                        </div>
                        <p class="text-gray-600 dark:text-gray-400">{{ __('auth.forgot_password_subtitle') }}</p>
                    </div>

                    <!-- Status Messages -->
                    @if (session('status'))
                        <div x-data="{ show: true }" x-show="show" x-transition class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg dark:bg-green-900/20 dark:border-green-800">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <p class="text-sm text-green-800 dark:text-green-200">{{ session('status') }}</p>
                                <button @click="show = false" class="ml-auto text-green-500 hover:text-green-700">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @endif

                    <!-- Forgot Password Form -->
                    <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
                        @csrf
                        
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
                                    autofocus
                                    placeholder="{{ __('auth.email_placeholder') }}"
                                    class="w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:text-gray-100 transition-colors @error('email') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
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

                        <!-- Submit Button -->
                        <button 
                            type="submit" 
                            :disabled="isLoading"
                            @click="isLoading = true"
                            class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-orange-600 to-red-600 hover:from-orange-700 hover:to-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200"
                        >
                            <svg x-show="isLoading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span x-show="!isLoading">{{ __('auth.send_reset_link') }}</span>
                            <span x-show="isLoading">{{ __('auth.sending') }}</span>
                            <svg x-show="!isLoading" class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            </svg>
                        </button>

                        <!-- Back to Login Link -->
                        <div class="text-center">
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                {{ __('auth.remember_password') }}
                                <a href="{{ route('login') }}" class="font-medium text-orange-600 hover:text-orange-500 dark:text-orange-400 dark:hover:text-orange-300">
                                    {{ __('auth.back_to_login') }}
                                </a>
                            </p>
                        </div>
                    </form>
                </div>

                <!-- Right Column - SVG & Details -->
                <div class="hidden lg:flex bg-gradient-to-br from-orange-600 to-red-700 dark:from-orange-800 dark:to-red-900 px-8 py-16 lg:px-12 flex-col justify-center relative overflow-hidden">
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
                                <!-- Digital/Tech Abstract Art - Password Recovery Theme -->
                                
                                <!-- Central data flow stream -->
                                <g class="animate-pulse" style="animation-duration: 5s;">
                                    <path d="M200 50 Q250 100 200 150 Q150 200 200 250 Q250 300 200 350" stroke="white" stroke-opacity="0.4" stroke-width="3" fill="none"/>
                                    <path d="M200 50 Q150 100 200 150 Q250 200 200 250 Q150 300 200 350" stroke="white" stroke-opacity="0.3" stroke-width="2" fill="none"/>
                                </g>
                                
                                <!-- Digital circuit nodes -->
                                <g class="animate-bounce" style="animation-delay: 0.5s; animation-duration: 4s;">
                                    <circle cx="200" cy="80" r="8" fill="white" fill-opacity="0.5"/>
                                    <circle cx="200" cy="160" r="10" fill="white" fill-opacity="0.4"/>
                                    <circle cx="200" cy="240" r="6" fill="white" fill-opacity="0.6"/>
                                    <circle cx="200" cy="320" r="9" fill="white" fill-opacity="0.3"/>
                                </g>
                                
                                <!-- Branching network lines -->
                                <g class="animate-pulse" style="animation-delay: 1s; animation-duration: 6s;">
                                    <line x1="200" y1="160" x2="120" y2="120" stroke="white" stroke-opacity="0.3" stroke-width="2"/>
                                    <line x1="200" y1="160" x2="280" y2="120" stroke="white" stroke-opacity="0.3" stroke-width="2"/>
                                    <line x1="200" y1="240" x2="140" y2="280" stroke="white" stroke-opacity="0.25" stroke-width="2"/>
                                    <line x1="200" y1="240" x2="260" y2="280" stroke="white" stroke-opacity="0.25" stroke-width="2"/>
                                </g>
                                
                                <!-- Floating data fragments -->
                                <g class="animate-spin" style="animation-duration: 20s;">
                                    <rect x="100" y="100" width="12" height="12" fill="white" fill-opacity="0.3" transform="rotate(45 106 106)"/>
                                    <rect x="290" y="110" width="8" height="8" fill="white" fill-opacity="0.4" transform="rotate(45 294 114)"/>
                                </g>
                                
                                <g class="animate-spin" style="animation-duration: 25s; animation-direction: reverse;">
                                    <rect x="130" y="270" width="10" height="10" fill="white" fill-opacity="0.35" transform="rotate(45 135 275)"/>
                                    <rect x="270" y="290" width="14" height="14" fill="white" fill-opacity="0.25" transform="rotate(45 277 297)"/>
                                </g>
                                
                                <!-- Digital wave patterns -->
                                <g class="animate-pulse" style="animation-delay: 2s; animation-duration: 4s;">
                                    <path d="M80 200 Q120 180 160 200 Q200 220 240 200 Q280 180 320 200" stroke="white" stroke-opacity="0.2" stroke-width="2" fill="none"/>
                                    <path d="M60 180 Q100 160 140 180 Q180 200 220 180 Q260 160 300 180" stroke="white" stroke-opacity="0.15" stroke-width="1.5" fill="none"/>
                                </g>
                                
                                <!-- Binary code dots -->
                                <g class="animate-pulse" style="animation-delay: 3s; animation-duration: 3s;">
                                    <circle cx="80" cy="150" r="2" fill="white" fill-opacity="0.6"/>
                                    <circle cx="85" cy="155" r="1.5" fill="white" fill-opacity="0.4"/>
                                    <circle cx="90" cy="160" r="2.5" fill="white" fill-opacity="0.5"/>
                                    <circle cx="95" cy="165" r="1" fill="white" fill-opacity="0.3"/>
                                    
                                    <circle cx="320" cy="250" r="2.5" fill="white" fill-opacity="0.5"/>
                                    <circle cx="315" cy="255" r="1.5" fill="white" fill-opacity="0.4"/>
                                    <circle cx="310" cy="260" r="2" fill="white" fill-opacity="0.6"/>
                                </g>
                                
                                <!-- Abstract tech polygons -->
                                <g class="animate-bounce" style="animation-delay: 1.5s; animation-duration: 5s;">
                                    <polygon points="350,80 365,70 375,85 360,95" fill="white" fill-opacity="0.2" stroke="white" stroke-opacity="0.3" stroke-width="1"/>
                                    <polygon points="50,300 65,290 75,305 60,315" fill="white" fill-opacity="0.25" stroke="white" stroke-opacity="0.35" stroke-width="1"/>
                                </g>
                                
                                <!-- Glitch effect lines -->
                                <g class="animate-pulse" style="animation-delay: 0.8s; animation-duration: 2s;">
                                    <rect x="180" y="100" width="40" height="2" fill="white" fill-opacity="0.4"/>
                                    <rect x="175" y="105" width="25" height="1" fill="white" fill-opacity="0.3"/>
                                    <rect x="185" y="108" width="35" height="1.5" fill="white" fill-opacity="0.35"/>
                                </g>
                                
                                <!-- Floating hexagon -->
                                <g class="animate-spin" style="animation-duration: 30s;">
                                    <polygon points="120,250 135,240 150,250 150,270 135,280 120,270" fill="white" fill-opacity="0.1" stroke="white" stroke-opacity="0.3" stroke-width="1.5"/>
                                </g>
                                
                                <!-- Digital constellation -->
                                <g class="animate-pulse" style="animation-delay: 2.5s; animation-duration: 4s;">
                                    <circle cx="300" cy="150" r="3" fill="white" fill-opacity="0.6"/>
                                    <circle cx="310" cy="140" r="2" fill="white" fill-opacity="0.4"/>
                                    <circle cx="290" cy="135" r="2.5" fill="white" fill-opacity="0.5"/>
                                    <line x1="300" y1="150" x2="310" y2="140" stroke="white" stroke-opacity="0.3" stroke-width="1"/>
                                    <line x1="310" y1="140" x2="290" y2="135" stroke="white" stroke-opacity="0.3" stroke-width="1"/>
                                    <line x1="290" y1="135" x2="300" y2="150" stroke="white" stroke-opacity="0.3" stroke-width="1"/>
                                </g>
                                
                                <!-- Morphing digital blob -->
                                <g class="animate-pulse" style="animation-delay: 4s; animation-duration: 8s;">
                                    <path d="M70 350 Q90 330 110 350 Q90 370 70 350" fill="white" fill-opacity="0.15" stroke="white" stroke-opacity="0.25" stroke-width="1"/>
                                    <path d="M330 50 Q350 30 370 50 Q350 70 330 50" fill="white" fill-opacity="0.12" stroke="white" stroke-opacity="0.2" stroke-width="1"/>
                                </g>
                            </svg>
                        </div>

                        <!-- Help Text -->
                        <div class="text-white">
                            <h3 class="text-2xl font-bold mb-4">Şifre Kurtarma</h3>
                            <p class="text-orange-100 mb-6 leading-relaxed">
                                Şifrenizi mi unuttunuz? Sorun değil! Email adresinize güvenli bir 
                                sıfırlama bağlantısı gönderelim, hemen yeni şifrenizi oluşturun.
                            </p>
                            
                            <!-- Process Steps -->
                            <div class="space-y-3 text-left">
                                <div class="flex items-center text-orange-100">
                                    <svg class="w-5 h-5 mr-3 text-yellow-300" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.522 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>Email adresinizi kontrol edin</span>
                                </div>
                                <div class="flex items-center text-orange-100">
                                    <svg class="w-5 h-5 mr-3 text-yellow-300" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>Bağlantıya tıklayın</span>
                                </div>
                                <div class="flex items-center text-orange-100">
                                    <svg class="w-5 h-5 mr-3 text-yellow-300" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>Yeni şifre oluşturun</span>
                                </div>
                            </div>

                            <!-- Info Section -->
                            <div class="mt-8 pt-6 border-t border-white/20">
                                <div class="bg-white/10 rounded-lg p-4">
                                    <div class="flex items-start">
                                        <svg class="w-5 h-5 text-yellow-300 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                        </svg>
                                        <div>
                                            <h4 class="text-sm font-medium text-white mb-2">
                                                Önemli Bilgiler
                                            </h4>
                                            <div class="text-sm text-orange-200 space-y-1">
                                                <p>• Bağlantı 24 saat boyunca geçerli</p>
                                                <p>• Spam klasörünüzü kontrol edin</p>
                                                <p>• Güvenlik için tek kullanımlık</p>
                                            </div>
                                        </div>
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
                isLoading: false
            }
        }
    </script>
    @endpush
</x-guest-layout>
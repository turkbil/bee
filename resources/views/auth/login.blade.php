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
                                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Giriş Yap</h2>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ request()->getHost() }}</p>
                            </div>
                        </div>
                        <p class="text-gray-600 dark:text-gray-400">Hesabınıza giriş yaparak devam edin</p>
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
                    <form method="POST" action="{{ route('login') }}" class="space-y-6">
                        @csrf
                        
                        <!-- Email Field -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email Adresi</label>
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
                                    placeholder="email@domain.com"
                                    class="w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-100 transition-colors @error('email') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
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
                            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Şifre</label>
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
                                    autocomplete="current-password"
                                    placeholder="Şifrenizi girin"
                                    class="w-full pl-10 pr-12 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-100 transition-colors @error('password') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
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
                                    <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">Beni hatırla</span>
                                </label>
                            </div>
                            <div>
                                <a href="{{ route('password.request') }}" class="text-sm text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300 font-medium transition-colors">
                                    Şifremi unuttum
                                </a>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button 
                            type="submit" 
                            :disabled="isLoading"
                            @click="isLoading = true"
                            class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200"
                        >
                            <svg x-show="isLoading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span x-show="!isLoading">Giriş Yap</span>
                            <span x-show="isLoading">Giriş yapılıyor...</span>
                            <svg x-show="!isLoading" class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                        </button>

                        <!-- Register Link -->
                        <div class="text-center">
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Hesabınız yok mu? 
                                <a href="{{ route('register') }}" class="font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">
                                    Kayıt ol
                                </a>
                            </p>
                        </div>
                    </form>
                </div>

                <!-- Right Column - SVG & Details -->
                <div class="bg-gradient-to-br from-blue-600 to-purple-700 dark:from-blue-800 dark:to-purple-900 px-8 py-16 lg:px-12 flex flex-col justify-center relative overflow-hidden">
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
                                <!-- Fun & Playful Login Art -->
                                
                                <!-- Central smiling character -->
                                <g class="animate-bounce" style="animation-duration: 3s;">
                                    <circle cx="200" cy="180" r="40" fill="white" fill-opacity="0.3" stroke="white" stroke-opacity="0.6" stroke-width="3"/>
                                    <!-- Eyes -->
                                    <circle cx="185" cy="170" r="4" fill="white" fill-opacity="0.8"/>
                                    <circle cx="215" cy="170" r="4" fill="white" fill-opacity="0.8"/>
                                    <!-- Smile -->
                                    <path d="M180 190 Q200 200 220 190" stroke="white" stroke-opacity="0.8" stroke-width="3" fill="none"/>
                                </g>
                                
                                <!-- Dancing stars around -->
                                <g class="animate-spin" style="animation-duration: 8s;">
                                    <g transform="translate(120, 100)">
                                        <polygon points="0,-8 2,-2 8,-2 3,2 5,8 0,4 -5,8 -3,2 -8,-2 -2,-2" fill="white" fill-opacity="0.7"/>
                                    </g>
                                </g>
                                
                                <g class="animate-spin" style="animation-duration: 6s; animation-direction: reverse;">
                                    <g transform="translate(280, 120)">
                                        <polygon points="0,-6 1.5,-1.5 6,-1.5 2.5,1.5 4,6 0,3 -4,6 -2.5,1.5 -6,-1.5 -1.5,-1.5" fill="white" fill-opacity="0.6"/>
                                    </g>
                                </g>
                                
                                <g class="animate-spin" style="animation-duration: 10s;">
                                    <g transform="translate(320, 260)">
                                        <polygon points="0,-5 1,-1 5,-1 2,1 3,5 0,3 -3,5 -2,1 -5,-1 -1,-1" fill="white" fill-opacity="0.5"/>
                                    </g>
                                </g>
                                
                                <!-- Floating hearts -->
                                <g class="animate-pulse" style="animation-delay: 1s; animation-duration: 4s;">
                                    <path d="M80 250 C80 245, 85 240, 90 240 C95 240, 100 245, 100 250 C100 260, 90 270, 90 270 C90 270, 80 260, 80 250 Z" fill="white" fill-opacity="0.4"/>
                                </g>
                                
                                <g class="animate-pulse" style="animation-delay: 2s; animation-duration: 5s;">
                                    <path d="M320 320 C320 317, 323 314, 326 314 C329 314, 332 317, 332 320 C332 326, 326 332, 326 332 C326 332, 320 326, 320 320 Z" fill="white" fill-opacity="0.3"/>
                                </g>
                                
                                <!-- Bouncing circles -->
                                <g class="animate-bounce" style="animation-delay: 0.5s; animation-duration: 2s;">
                                    <circle cx="70" cy="180" r="8" fill="white" fill-opacity="0.5"/>
                                </g>
                                
                                <g class="animate-bounce" style="animation-delay: 1.5s; animation-duration: 2.5s;">
                                    <circle cx="330" cy="200" r="10" fill="white" fill-opacity="0.4"/>
                                </g>
                                
                                <g class="animate-bounce" style="animation-delay: 2.5s; animation-duration: 3s;">
                                    <circle cx="150" cy="320" r="6" fill="white" fill-opacity="0.6"/>
                                </g>
                                
                                <!-- Wavy lines for movement -->
                                <g class="animate-pulse" style="animation-delay: 0.8s; animation-duration: 6s;">
                                    <path d="M50 150 Q80 140 110 150 Q140 160 170 150" stroke="white" stroke-opacity="0.3" stroke-width="2" fill="none"/>
                                    <path d="M230 280 Q260 270 290 280 Q320 290 350 280" stroke="white" stroke-opacity="0.25" stroke-width="2" fill="none"/>
                                </g>
                                
                                <!-- Musical notes -->
                                <g class="animate-bounce" style="animation-delay: 1.2s; animation-duration: 4s;">
                                    <circle cx="350" cy="100" r="3" fill="white" fill-opacity="0.7"/>
                                    <rect x="352" y="85" width="1.5" height="15" fill="white" fill-opacity="0.7"/>
                                    <path d="M354 85 Q360 82 365 85" stroke="white" stroke-opacity="0.7" stroke-width="1" fill="none"/>
                                </g>
                                
                                <g class="animate-bounce" style="animation-delay: 2.8s; animation-duration: 3.5s;">
                                    <circle cx="60" cy="320" r="2.5" fill="white" fill-opacity="0.6"/>
                                    <rect x="61.5" y="308" width="1" height="12" fill="white" fill-opacity="0.6"/>
                                </g>
                                
                                <!-- Sparkle effects -->
                                <g class="animate-ping" style="animation-delay: 0.3s; animation-duration: 2s;">
                                    <circle cx="250" cy="80" r="2" fill="white" fill-opacity="0.8"/>
                                </g>
                                
                                <g class="animate-ping" style="animation-delay: 1.8s; animation-duration: 2.5s;">
                                    <circle cx="120" cy="350" r="1.5" fill="white" fill-opacity="0.7"/>
                                </g>
                                
                                <g class="animate-ping" style="animation-delay: 3.2s; animation-duration: 2s;">
                                    <circle cx="380" cy="180" r="2.5" fill="white" fill-opacity="0.6"/>
                                </g>
                                
                                <!-- Playful zigzag -->
                                <g class="animate-pulse" style="animation-delay: 2.2s; animation-duration: 5s;">
                                    <path d="M30 100 L45 85 L60 100 L75 85 L90 100" stroke="white" stroke-opacity="0.3" stroke-width="2" fill="none"/>
                                    <path d="M300 350 L315 335 L330 350 L345 335 L360 350" stroke="white" stroke-opacity="0.25" stroke-width="2" fill="none"/>
                                </g>
                                
                                <!-- Floating bubbles -->
                                <g class="animate-pulse" style="animation-delay: 0.7s; animation-duration: 8s;">
                                    <circle cx="380" cy="300" r="6" fill="white" fill-opacity="0.2" stroke="white" stroke-opacity="0.4" stroke-width="1"/>
                                    <circle cx="30" cy="60" r="8" fill="white" fill-opacity="0.15" stroke="white" stroke-opacity="0.3" stroke-width="1"/>
                                    <circle cx="200" cy="350" r="4" fill="white" fill-opacity="0.25" stroke="white" stroke-opacity="0.5" stroke-width="1"/>
                                </g>
                            </svg>
                        </div>

                        <!-- Welcome Text -->
                        <div class="text-white">
                            <h3 class="text-2xl font-bold mb-4">{{ tenant('title') ?? config('app.name') }}</h3>
                            <p class="text-blue-100 mb-6 leading-relaxed">
                                Hesabınıza giriş yapın ve tüm özelliklerden yararlanmaya başlayın. 
                                Güvenli giriş ile verileriniz her zaman korunur.
                            </p>
                            
                            <!-- Features -->
                            <div class="space-y-3 text-left">
                                <div class="flex items-center text-blue-100">
                                    <svg class="w-5 h-5 mr-3 text-green-300" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>Güvenli Giriş Sistemi</span>
                                </div>
                                <div class="flex items-center text-blue-100">
                                    <svg class="w-5 h-5 mr-3 text-green-300" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>Kişisel Veri Koruması</span>
                                </div>
                                <div class="flex items-center text-blue-100">
                                    <svg class="w-5 h-5 mr-3 text-green-300" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>Kolay Kullanım</span>
                                </div>
                            </div>

                            <!-- Quick Demo Login -->
                            <div class="mt-8 pt-6 border-t border-white/20">
                                <p class="text-sm text-blue-200 mb-3">Hızlı Test Girişi:</p>
                                <div class="grid grid-cols-3 gap-1.5">
                                    @php $host = request()->getHost(); @endphp
                                    
                                    <!-- Nurullah ve Turkbil her zaman görünür -->
                                    <button type="button" @click="autoLogin('nurullah')" 
                                            class="p-2 bg-gradient-to-r from-yellow-500/20 to-orange-500/20 hover:from-yellow-500/30 hover:to-orange-500/30 rounded-lg transition-colors text-center">
                                        <div class="text-xs font-medium">Nurullah</div>
                                        <div class="text-xs text-yellow-300 opacity-75">Root</div>
                                    </button>
                                    
                                    <button type="button" @click="autoLogin('turkbilisim')" 
                                            class="p-2 bg-gradient-to-r from-purple-500/20 to-pink-500/20 hover:from-purple-500/30 hover:to-pink-500/30 rounded-lg transition-colors text-center">
                                        <div class="text-xs font-medium">Turkbil</div>
                                        <div class="text-xs text-purple-300 opacity-75">Admin</div>
                                    </button>
                                    
                                    <!-- Domain'e özel tek kullanıcı -->
                                    @if($host === 'laravel.test')
                                        <button type="button" @click="autoLogin('laravel')" 
                                                class="p-2 bg-white/10 hover:bg-white/20 rounded-lg transition-colors text-center">
                                            <div class="text-xs font-medium">Laravel</div>
                                            <div class="text-xs text-blue-300 opacity-75">Test</div>
                                        </button>
                                    @elseif($host === 'a.test')
                                        <button type="button" @click="autoLogin('a')" 
                                                class="p-2 bg-white/10 hover:bg-white/20 rounded-lg transition-colors text-center">
                                            <div class="text-xs font-medium">A User</div>
                                            <div class="text-xs text-blue-300 opacity-75">Test</div>
                                        </button>
                                    @elseif($host === 'b.test')
                                        <button type="button" @click="autoLogin('b')" 
                                                class="p-2 bg-white/10 hover:bg-white/20 rounded-lg transition-colors text-center">
                                            <div class="text-xs font-medium">B User</div>
                                            <div class="text-xs text-blue-300 opacity-75">Test</div>
                                        </button>
                                    @elseif($host === 'c.test')
                                        <button type="button" @click="autoLogin('c')" 
                                                class="p-2 bg-white/10 hover:bg-white/20 rounded-lg transition-colors text-center">
                                            <div class="text-xs font-medium">C User</div>
                                            <div class="text-xs text-blue-300 opacity-75">Test</div>
                                        </button>
                                    @endif
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
                isLoading: false,
                rememberMe: false,
                
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
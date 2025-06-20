<section>
    <form method="post" action="{{ route('password.update') }}" class="space-y-6">
        @csrf
        @method('put')

        <!-- Security Info -->
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-blue-400 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">
                        Güvenlik Önerisi
                    </h3>
                    <p class="mt-1 text-sm text-blue-700 dark:text-blue-300">
                        Hesabınızın güvenliği için en az 8 karakter uzunluğunda, büyük-küçük harf, rakam ve özel karakter içeren güçlü bir şifre kullanın.
                    </p>
                </div>
            </div>
        </div>

        <div class="space-y-4">
            <!-- Current Password -->
            <div>
                <label for="update_password_current_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Mevcut Şifre
                </label>
                <div class="relative">
                    <input type="password" 
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-gray-100 transition-colors @error('current_password', 'updatePassword') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror" 
                           id="update_password_current_password" 
                           name="current_password" 
                           autocomplete="current-password"
                           placeholder="Mevcut şifrenizi girin">
                    <button type="button" onclick="togglePassword('update_password_current_password')" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                </div>
                @error('current_password', 'updatePassword')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- New Password -->
            <div>
                <label for="update_password_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Yeni Şifre
                </label>
                <div class="relative">
                    <input type="password" 
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-gray-100 transition-colors @error('password', 'updatePassword') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror" 
                           id="update_password_password" 
                           name="password" 
                           autocomplete="new-password"
                           placeholder="Yeni şifrenizi girin"
                           onkeyup="checkPasswordStrength(this.value)">
                    <button type="button" onclick="togglePassword('update_password_password')" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                </div>
                
                <!-- Password Strength Indicator -->
                <div class="mt-2">
                    <div class="flex items-center space-x-2">
                        <div class="flex-1">
                            <div class="h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                <div id="password-strength-bar" class="h-full transition-all duration-300 rounded-full" style="width: 0%;"></div>
                            </div>
                        </div>
                        <span id="password-strength-text" class="text-xs text-gray-500 dark:text-gray-400 min-w-fit">Şifre giriniz</span>
                    </div>
                </div>
                
                @error('password', 'updatePassword')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div>
                <label for="update_password_password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Yeni Şifre (Tekrar)
                </label>
                <div class="relative">
                    <input type="password" 
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-gray-100 transition-colors @error('password_confirmation', 'updatePassword') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror" 
                           id="update_password_password_confirmation" 
                           name="password_confirmation" 
                           autocomplete="new-password"
                           placeholder="Yeni şifrenizi tekrar girin">
                    <button type="button" onclick="togglePassword('update_password_password_confirmation')" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                </div>
                @error('password_confirmation', 'updatePassword')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Submit Button and Status -->
        <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
            <div class="flex items-center space-x-4">
                <button type="submit" class="inline-flex items-center px-6 py-3 bg-yellow-600 hover:bg-yellow-700 text-white font-medium rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    Şifreyi Güncelle
                </button>

                @if (session('status') === 'password-updated')
                    <div class="flex items-center text-green-600 dark:text-green-400"
                         x-data="{ show: true }"
                         x-show="show"
                         x-transition
                         x-init="setTimeout(() => show = false, 3000)">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-sm font-medium">Şifreniz başarıyla güncellendi!</span>
                    </div>
                @endif
            </div>
        </div>
    </form>

    <script>
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const type = field.getAttribute('type') === 'password' ? 'text' : 'password';
            field.setAttribute('type', type);
        }

        function checkPasswordStrength(password) {
            const strengthBar = document.getElementById('password-strength-bar');
            const strengthText = document.getElementById('password-strength-text');
            
            let strength = 0;
            let text = 'Çok Zayıf';
            let color = 'bg-red-500';
            
            if (password.length >= 8) strength += 1;
            if (password.match(/[a-z]/)) strength += 1;
            if (password.match(/[A-Z]/)) strength += 1;
            if (password.match(/[0-9]/)) strength += 1;
            if (password.match(/[^A-Za-z0-9]/)) strength += 1;
            
            switch (strength) {
                case 0:
                case 1:
                    text = 'Çok Zayıf';
                    color = 'bg-red-500';
                    break;
                case 2:
                    text = 'Zayıf';
                    color = 'bg-orange-500';
                    break;
                case 3:
                    text = 'Orta';
                    color = 'bg-yellow-500';
                    break;
                case 4:
                    text = 'Güçlü';
                    color = 'bg-blue-500';
                    break;
                case 5:
                    text = 'Çok Güçlü';
                    color = 'bg-green-500';
                    break;
            }
            
            const percentage = Math.max((strength / 5) * 100, password.length > 0 ? 20 : 0);
            strengthBar.style.width = percentage + '%';
            strengthBar.className = `h-full transition-all duration-300 rounded-full ${color}`;
            strengthText.textContent = password.length > 0 ? text : 'Şifre giriniz';
        }
    </script>
</section>
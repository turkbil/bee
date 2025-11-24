<section>
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-6">
        @csrf
        @method('patch')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Name Field -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    <i class="fa-solid fa-user text-gray-400 mr-1"></i>
                    Ad <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-100 transition-colors @error('name') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
                       id="name"
                       name="name"
                       value="{{ old('name', $user->name) }}"
                       required
                       autofocus
                       autocomplete="given-name"
                       placeholder="Adınız">
                @error('name')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Surname Field -->
            <div>
                <label for="surname" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    <i class="fa-solid fa-user text-gray-400 mr-1"></i>
                    Soyad <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-100 transition-colors @error('surname') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
                       id="surname"
                       name="surname"
                       value="{{ old('surname', $user->surname) }}"
                       required
                       autocomplete="family-name"
                       placeholder="Soyadınız">
                @error('surname')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Phone Field -->
            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    <i class="fa-solid fa-phone text-gray-400 mr-1"></i>
                    Telefon
                </label>
                <input type="tel"
                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-100 transition-colors @error('phone') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
                       id="phone"
                       name="phone"
                       value="{{ old('phone', $user->phone) }}"
                       autocomplete="tel"
                       placeholder="05XX XXX XX XX">
                @error('phone')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email Field -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    <i class="fa-solid fa-envelope text-gray-400 mr-1"></i>
                    E-posta <span class="text-red-500">*</span>
                </label>
                <input type="email"
                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-100 transition-colors @error('email') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
                       id="email"
                       name="email"
                       value="{{ old('email', $user->email) }}"
                       required
                       autocomplete="email"
                       placeholder="email@domain.com">
                @error('email')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Email Verification Warning -->
        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                <div class="flex items-start">
                    <i class="fa-solid fa-triangle-exclamation text-yellow-400 mt-0.5 mr-3 flex-shrink-0"></i>
                    <div>
                        <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">
                            Email Doğrulama Gerekli
                        </h3>
                        <p class="mt-1 text-sm text-yellow-700 dark:text-yellow-300">
                            Email adresiniz henüz doğrulanmamış. Hesabınızın güvenliği için lütfen email adresinizi doğrulayın.
                        </p>
                        <div class="mt-3">
                            <button form="send-verification" class="text-sm font-medium text-yellow-800 dark:text-yellow-200 hover:text-yellow-900 dark:hover:text-yellow-100 underline transition-colors">
                                Doğrulama emailini tekrar gönder
                            </button>
                        </div>
                    </div>
                </div>

                @if (session('status') === 'verification-link-sent')
                    <div class="mt-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-3">
                        <div class="flex items-center">
                            <i class="fa-solid fa-circle-check text-green-400 mr-2"></i>
                            <p class="text-sm text-green-700 dark:text-green-300">
                                Email adresinize yeni bir doğrulama bağlantısı gönderildi.
                            </p>
                        </div>
                    </div>
                @endif
            </div>
        @endif

        <!-- Submit Button and Status -->
        <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
            <div class="flex items-center space-x-4">
                <button type="submit" class="inline-flex items-center px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                    <i class="fa-solid fa-check mr-2"></i>
                    Profil Bilgilerini Kaydet
                </button>

                @if (session('status') === 'profile-updated')
                    <div class="flex items-center text-green-600 dark:text-green-400"
                         x-data="{ show: true }"
                         x-show="show"
                         x-transition
                         x-init="setTimeout(() => show = false, 3000)">
                        <i class="fa-solid fa-circle-check mr-2"></i>
                        <span class="text-sm font-medium">Profil bilgileriniz başarıyla kaydedildi!</span>
                    </div>
                @endif
            </div>
        </div>
    </form>
</section>
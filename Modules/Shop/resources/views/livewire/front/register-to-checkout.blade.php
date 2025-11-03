<div class="container mx-auto px-4 py-12">
    <div class="max-w-md mx-auto">

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8">

            {{-- Başlık --}}
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-user-plus text-blue-600 dark:text-blue-400 text-2xl"></i>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                    Siparişe Devam Etmek İçin Üye Olun
                </h1>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Hızlı ve güvenli alışveriş için birkaç bilgi yeterli.
                </p>
            </div>

            {{-- Success/Error Messages --}}
            @if(session('success'))
            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 text-green-700 dark:text-green-300 px-4 py-3 rounded-lg mb-4">
                {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 text-red-700 dark:text-red-300 px-4 py-3 rounded-lg mb-4">
                {{ session('error') }}
            </div>
            @endif

            {{-- Form --}}
            <form wire:submit="register" class="space-y-4">

                {{-- Ad/Soyad --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm text-gray-700 dark:text-gray-300 mb-1.5">
                            Ad <span class="text-red-500">*</span>
                        </label>
                        <input type="text" wire:model="first_name"
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                            placeholder="Ahmet">
                        @error('first_name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm text-gray-700 dark:text-gray-300 mb-1.5">
                            Soyad <span class="text-red-500">*</span>
                        </label>
                        <input type="text" wire:model="last_name"
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                            placeholder="Yılmaz">
                        @error('last_name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                {{-- Email --}}
                <div>
                    <label class="block text-sm text-gray-700 dark:text-gray-300 mb-1.5">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input type="email" wire:model="email"
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                        placeholder="ornek@email.com">
                    @error('email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- Telefon --}}
                <div>
                    <label class="block text-sm text-gray-700 dark:text-gray-300 mb-1.5">
                        Telefon <span class="text-red-500">*</span>
                    </label>
                    <input type="tel" wire:model="phone"
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                        placeholder="05XX XXX XX XX">
                    @error('phone') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- Şifre --}}
                <div>
                    <label class="block text-sm text-gray-700 dark:text-gray-300 mb-1.5">
                        Şifre <span class="text-red-500">*</span>
                    </label>
                    <input type="password" wire:model="password"
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                        placeholder="En az 8 karakter">
                    @error('password') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- Şifre Tekrar --}}
                <div>
                    <label class="block text-sm text-gray-700 dark:text-gray-300 mb-1.5">
                        Şifre Tekrar <span class="text-red-500">*</span>
                    </label>
                    <input type="password" wire:model="password_confirmation"
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                        placeholder="Şifreyi tekrar girin">
                </div>

                {{-- Submit Button --}}
                <button type="submit"
                    wire:loading.attr="disabled"
                    class="w-full bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-bold py-3 px-6 rounded-lg transition-all transform hover:scale-105 shadow-lg flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                    <span wire:loading.remove wire:target="register">
                        <i class="fa-solid fa-user-plus"></i>
                        Hesap Oluştur ve Devam Et
                    </span>
                    <span wire:loading wire:target="register">
                        <i class="fa-solid fa-spinner fa-spin"></i>
                        Hesap Oluşturuluyor...
                    </span>
                </button>

            </form>

            {{-- Zaten üye misiniz? --}}
            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700 text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Zaten hesabınız var mı?
                    <a href="{{ route('login') }}" class="text-blue-600 dark:text-blue-400 hover:underline font-semibold">
                        Giriş Yapın
                    </a>
                </p>
            </div>

            {{-- Sepete Geri Dön --}}
            <div class="mt-4 text-center">
                <a href="{{ route('shop.cart') }}" class="inline-flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                    <i class="fa-solid fa-arrow-left"></i>
                    <span>Sepete Geri Dön</span>
                </a>
            </div>

        </div>

    </div>
</div>

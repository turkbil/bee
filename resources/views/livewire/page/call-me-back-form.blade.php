<div>
    {{-- Sizi Arayalım Formu --}}
    <form wire:submit.prevent="submit" class="space-y-6">
        {{-- İsim --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2 flex items-center gap-2">
                <i class="fa-solid fa-user text-blue-600 dark:text-blue-400"></i>
                Ad Soyad *
            </label>
            <input type="text" wire:model="name" placeholder="Adınız ve Soyadınız"
                class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-900 text-gray-900 dark:text-white transition-all">
            @error('name') <span class="text-red-600 text-sm flex items-center gap-1 mt-1"><i class="fa-solid fa-exclamation-circle"></i>{{ $message }}</span> @enderror
        </div>

        {{-- Telefon & Email --}}
        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2 flex items-center gap-2">
                    <i class="fa-solid fa-phone text-green-600 dark:text-green-400"></i>
                    Telefon *
                </label>
                <input type="tel" wire:model="phone" placeholder="0 (5XX) XXX XX XX"
                    class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 bg-white dark:bg-gray-900 text-gray-900 dark:text-white transition-all">
                @error('phone') <span class="text-red-600 text-sm flex items-center gap-1 mt-1"><i class="fa-solid fa-exclamation-circle"></i>{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2 flex items-center gap-2">
                    <i class="fa-solid fa-envelope text-purple-600 dark:text-purple-400"></i>
                    E-posta *
                </label>
                <input type="email" wire:model="email" placeholder="ornek@email.com"
                    class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 bg-white dark:bg-gray-900 text-gray-900 dark:text-white transition-all">
                @error('email') <span class="text-red-600 text-sm flex items-center gap-1 mt-1"><i class="fa-solid fa-exclamation-circle"></i>{{ $message }}</span> @enderror
            </div>
        </div>

        {{-- Şirket (Opsiyonel) --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2 flex items-center gap-2">
                <i class="fa-solid fa-building text-orange-600 dark:text-orange-400"></i>
                Şirket <span class="text-gray-400 text-xs font-normal">(Opsiyonel)</span>
            </label>
            <input type="text" wire:model="company" placeholder="Şirket Adınız"
                class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 bg-white dark:bg-gray-900 text-gray-900 dark:text-white transition-all">
        </div>

        {{-- Tercih Edilen Zaman --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                <i class="fa-solid fa-clock text-indigo-600 dark:text-indigo-400"></i>
                Sizi Ne Zaman Arayalım? *
            </label>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <label class="relative cursor-pointer">
                    <input type="radio" wire:model="preferred_time" value="anytime" class="peer sr-only">
                    <div class="px-4 py-3 bg-white dark:bg-gray-900 border-2 border-gray-300 dark:border-gray-600 rounded-xl text-center transition-all
                                peer-checked:border-indigo-600 peer-checked:bg-indigo-50 dark:peer-checked:bg-indigo-900/20 peer-checked:text-indigo-700 dark:peer-checked:text-indigo-300">
                        <i class="fa-solid fa-infinity mb-1 block text-lg"></i>
                        <span class="text-sm font-medium">Farketmez</span>
                    </div>
                </label>

                <label class="relative cursor-pointer">
                    <input type="radio" wire:model="preferred_time" value="morning" class="peer sr-only">
                    <div class="px-4 py-3 bg-white dark:bg-gray-900 border-2 border-gray-300 dark:border-gray-600 rounded-xl text-center transition-all
                                peer-checked:border-yellow-600 peer-checked:bg-yellow-50 dark:peer-checked:bg-yellow-900/20 peer-checked:text-yellow-700 dark:peer-checked:text-yellow-300">
                        <i class="fa-solid fa-sunrise mb-1 block text-lg"></i>
                        <span class="text-sm font-medium">Sabah</span>
                        <small class="block text-xs opacity-75">09-12</small>
                    </div>
                </label>

                <label class="relative cursor-pointer">
                    <input type="radio" wire:model="preferred_time" value="afternoon" class="peer sr-only">
                    <div class="px-4 py-3 bg-white dark:bg-gray-900 border-2 border-gray-300 dark:border-gray-600 rounded-xl text-center transition-all
                                peer-checked:border-orange-600 peer-checked:bg-orange-50 dark:peer-checked:bg-orange-900/20 peer-checked:text-orange-700 dark:peer-checked:text-orange-300">
                        <i class="fa-solid fa-sun mb-1 block text-lg"></i>
                        <span class="text-sm font-medium">Öğleden Sonra</span>
                        <small class="block text-xs opacity-75">12-17</small>
                    </div>
                </label>

                <label class="relative cursor-pointer">
                    <input type="radio" wire:model="preferred_time" value="evening" class="peer sr-only">
                    <div class="px-4 py-3 bg-white dark:bg-gray-900 border-2 border-gray-300 dark:border-gray-600 rounded-xl text-center transition-all
                                peer-checked:border-blue-600 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20 peer-checked:text-blue-700 dark:peer-checked:text-blue-300">
                        <i class="fa-solid fa-moon mb-1 block text-lg"></i>
                        <span class="text-sm font-medium">Akşam</span>
                        <small class="block text-xs opacity-75">17-20</small>
                    </div>
                </label>
            </div>
            @error('preferred_time') <span class="text-red-600 text-sm flex items-center gap-1 mt-2"><i class="fa-solid fa-exclamation-circle"></i>{{ $message }}</span> @enderror
        </div>

        {{-- Mesaj (Opsiyonel) --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2 flex items-center gap-2">
                <i class="fa-solid fa-comment-dots text-teal-600 dark:text-teal-400"></i>
                Mesajınız <span class="text-gray-400 text-xs font-normal">(Opsiyonel)</span>
            </label>
            <textarea rows="4" wire:model="message" placeholder="Bize ne hakkında bilgi vermemizi istersiniz? Hangi ürün/hizmet ile ilgileniyorsunuz?"
                class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-teal-500 bg-white dark:bg-gray-900 text-gray-900 dark:text-white transition-all resize-none"></textarea>
        </div>

        {{-- KVKK Checkbox --}}
        <div class="bg-gradient-to-br from-blue-50 to-purple-50 dark:from-gray-800 dark:to-gray-700 p-5 rounded-xl border border-blue-200 dark:border-gray-600">
            <label class="flex items-start gap-3 cursor-pointer group">
                <input type="checkbox" wire:model="terms_accepted"
                    class="mt-1 w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                <span class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed group-hover:text-gray-900 dark:group-hover:text-white transition-colors">
                    <strong>Kişisel Verilerin Korunması:</strong> Kişisel verilerimin işlenmesine, saklanmasına ve iletişim amacıyla kullanılmasına izin veriyorum.
                    <a href="/page/kvkk" target="_blank" class="text-blue-600 dark:text-blue-400 hover:underline font-semibold">KVKK Metni</a>
                </span>
            </label>
            @error('terms_accepted') <span class="text-red-600 text-sm flex items-center gap-1 mt-3"><i class="fa-solid fa-exclamation-circle"></i>{{ $message }}</span> @enderror
        </div>

        {{-- Submit Button --}}
        <button type="submit"
            class="w-full bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 hover:from-blue-700 hover:via-purple-700 hover:to-pink-700 text-white font-bold py-4 px-6 rounded-xl transition-all shadow-lg hover:shadow-xl hover:scale-[1.02] active:scale-[0.98] flex items-center justify-center gap-3"
            wire:loading.attr="disabled"
            wire:loading.class="opacity-75 cursor-not-allowed">
            <span wire:loading.remove class="flex items-center gap-2">
                <i class="fa-solid fa-phone-volume text-lg"></i>
                <span>Hemen Arayın</span>
            </span>
            <span wire:loading class="flex items-center gap-2">
                <i class="fa-solid fa-spinner fa-spin text-lg"></i>
                <span>Gönderiliyor...</span>
            </span>
        </button>

        {{-- İnfo Text --}}
        <p class="text-center text-sm text-gray-500 dark:text-gray-400 flex items-center justify-center gap-2">
            <i class="fa-solid fa-shield-check text-green-600 dark:text-green-400"></i>
            Bilgileriniz güvende! En kısa sürede sizi arayacağız.
        </p>
    </form>

    {{-- Success/Error Modal --}}
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
             x-data="{ show: @entangle('showModal') }"
             x-show="show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">

            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl max-w-md w-full p-8 relative"
                 @click.away="$wire.closeModal()"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-90"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-90">

                {{-- Close Button --}}
                <button wire:click="closeModal"
                    class="absolute top-4 right-4 w-10 h-10 flex items-center justify-center rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                    <i class="fa-solid fa-times text-lg"></i>
                </button>

                @if($modalType === 'success')
                    {{-- Success Modal --}}
                    <div class="text-center">
                        <div class="w-24 h-24 bg-gradient-to-br from-green-400 to-emerald-600 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg">
                            <i class="fa-solid fa-check text-5xl text-white"></i>
                        </div>
                        <h3 class="text-3xl font-bold text-gray-900 dark:text-white mb-4 bg-clip-text text-transparent bg-gradient-to-r from-green-600 to-emerald-600">
                            Talebiniz Alındı!
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-6 leading-relaxed text-lg">
                            <strong>En kısa sürede sizi arayacağız!</strong><br>
                            Tercih ettiğiniz zaman diliminde iletişime geçeceğiz.
                        </p>
                        <div class="bg-green-50 dark:bg-green-900/20 rounded-2xl p-4 mb-6">
                            <p class="text-sm text-green-800 dark:text-green-300 flex items-center justify-center gap-2">
                                <i class="fa-solid fa-info-circle"></i>
                                Telegram ve WhatsApp üzerinden de bilgilendirme yapıldı.
                            </p>
                        </div>
                        <button wire:click="closeModal"
                            class="w-full bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-bold py-4 px-6 rounded-xl transition-all shadow-lg hover:shadow-xl">
                            <i class="fa-solid fa-check-circle mr-2"></i>Tamam
                        </button>
                    </div>
                @else
                    {{-- Error Modal --}}
                    <div class="text-center">
                        <div class="w-24 h-24 bg-gradient-to-br from-red-400 to-pink-600 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg">
                            <i class="fa-solid fa-exclamation text-5xl text-white"></i>
                        </div>
                        <h3 class="text-3xl font-bold text-gray-900 dark:text-white mb-4 bg-clip-text text-transparent bg-gradient-to-r from-red-600 to-pink-600">
                            Bir Hata Oluştu
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-6 leading-relaxed">
                            Talebiniz gönderilirken bir sorun yaşandı.<br>
                            Lütfen daha sonra tekrar deneyin veya doğrudan bizi arayın.
                        </p>
                        <div class="bg-red-50 dark:bg-red-900/20 rounded-2xl p-4 mb-6">
                            <p class="text-sm text-red-800 dark:text-red-300 flex items-center justify-center gap-2">
                                <i class="fa-solid fa-phone"></i>
                                <a href="tel:+908503042424" class="font-semibold hover:underline">0 850 304 24 24</a>
                            </p>
                        </div>
                        <button wire:click="closeModal"
                            class="w-full bg-gradient-to-r from-red-600 to-pink-600 hover:from-red-700 hover:to-pink-700 text-white font-bold py-4 px-6 rounded-xl transition-all shadow-lg hover:shadow-xl">
                            <i class="fa-solid fa-times-circle mr-2"></i>Kapat
                        </button>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>

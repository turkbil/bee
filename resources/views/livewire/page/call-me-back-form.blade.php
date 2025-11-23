<div x-data="{
    init() {
        // localStorage'dan ürün bilgilerini oku ve Livewire'a gönder
        const productId = localStorage.getItem('callMeBack_productId');
        const productName = localStorage.getItem('callMeBack_productName');
        const fromUrl = localStorage.getItem('callMeBack_fromUrl');

        if (productId || productName || fromUrl) {
            $wire.setContextFromLocalStorage(productId, productName, fromUrl);

            // localStorage'ı temizle (tek kullanımlık)
            localStorage.removeItem('callMeBack_productId');
            localStorage.removeItem('callMeBack_productName');
            localStorage.removeItem('callMeBack_fromUrl');
        }
    }
}">
    {{-- Sizi Arayalım Formu - Clean & Professional --}}
    <form wire:submit.prevent="submit" class="space-y-6">

        {{-- İsim --}}
        <div class="relative group">
            <div class="absolute -inset-0.5 bg-gradient-to-r from-blue-600 to-purple-600 rounded-2xl opacity-0 group-hover:opacity-100 blur transition duration-500 group-focus-within:opacity-100"></div>
            <input type="text" wire:model="name" placeholder="Adınız Soyadınız *"
                class="relative w-full px-6 py-4 text-lg border-2 border-gray-200 dark:border-gray-700 rounded-2xl focus:border-transparent focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 transition-all duration-300 hover:border-blue-300 dark:hover:border-blue-600">
            @error('name') <span class="text-red-500 text-sm flex items-center gap-1 mt-2"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</span> @enderror
        </div>

        {{-- Telefon --}}
        <div class="relative group">
            <div class="absolute -inset-0.5 bg-gradient-to-r from-green-600 to-emerald-600 rounded-2xl opacity-0 group-hover:opacity-100 blur transition duration-500 group-focus-within:opacity-100"></div>
            <div class="relative flex items-center">
                {{-- Flag & Prefix --}}
                <div class="absolute left-4 flex items-center gap-3 pointer-events-none z-10">
                    <div class="flex items-center gap-2">
                        <span class="text-2xl">🇹🇷</span>
                        <span class="text-gray-500 dark:text-gray-400 font-medium">+90</span>
                    </div>
                    <div class="h-6 w-px bg-gray-300 dark:bg-gray-600"></div>
                </div>
                <input type="tel" wire:model.defer="phone" placeholder="5XX XXX XX XX"
                    x-data="{
                        phoneValue: '',
                        formatPhone(event) {
                            let value = event.target.value.replace(/\D/g, '');
                            if (value.length > 10) value = value.slice(0, 10);

                            let formatted = '';
                            if (value.length > 0) formatted += value.substring(0, 3);
                            if (value.length > 3) formatted += ' ' + value.substring(3, 6);
                            if (value.length > 6) formatted += ' ' + value.substring(6, 8);
                            if (value.length > 8) formatted += ' ' + value.substring(8, 10);

                            event.target.value = formatted;
                            this.phoneValue = value;
                        }
                    }"
                    @input="formatPhone($event)"
                    @blur="$wire.phone = phoneValue"
                    class="relative w-full pl-32 pr-6 py-4 text-lg border-2 border-gray-200 dark:border-gray-700 rounded-2xl focus:border-transparent focus:ring-2 focus:ring-green-500 bg-white dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 transition-all duration-300 hover:border-green-300 dark:hover:border-green-600 font-mono">
            </div>
            @error('phone') <span class="text-red-500 text-sm flex items-center gap-1 mt-2"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</span> @enderror
        </div>

        {{-- Email --}}
        <div class="relative group">
            <div class="absolute -inset-0.5 bg-gradient-to-r from-purple-600 to-indigo-600 rounded-2xl opacity-0 group-hover:opacity-100 blur transition duration-500 group-focus-within:opacity-100"></div>
            <div class="relative flex items-center">
                {{-- Email Icon --}}
                <div class="absolute left-5 pointer-events-none z-10">
                    <i class="fa-regular fa-envelope text-xl text-gray-400 dark:text-gray-500"></i>
                </div>
                <input type="email" wire:model="email" placeholder="E-posta Adresiniz (opsiyonel)"
                    class="relative w-full pl-14 pr-6 py-4 text-lg border-2 border-gray-200 dark:border-gray-700 rounded-2xl focus:border-transparent focus:ring-2 focus:ring-purple-500 bg-white dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 transition-all duration-300 hover:border-purple-300 dark:hover:border-purple-600">
            </div>
            @error('email') <span class="text-red-500 text-sm flex items-center gap-1 mt-2"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</span> @enderror
        </div>

        {{-- Submit Button --}}
        <div class="relative group">
            <div class="absolute -inset-1 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl blur-lg opacity-75 group-hover:opacity-100 transition duration-500"></div>
            <button type="submit"
                class="relative w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold py-4 px-6 rounded-2xl transition-all duration-300 shadow-lg hover:shadow-2xl transform hover:scale-[1.02] active:scale-[0.98] text-lg"
                wire:loading.attr="disabled"
                wire:loading.class="opacity-75 cursor-not-allowed"
                wire:target="submit">
                <span wire:loading.remove wire:target="submit" class="flex items-center justify-center gap-2">
                    <i class="fa-solid fa-paper-plane"></i>
                    Bilgileri Gönder
                </span>
                <span wire:loading wire:target="submit" class="flex items-center justify-center gap-2">
                    <i class="fa-solid fa-spinner fa-spin"></i>
                    Gönderiliyor...
                </span>
            </button>
        </div>

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

            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl max-w-md w-full p-8 relative transform"
                 @click.away="$wire.closeModal()"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-90"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-90">

                {{-- Close Button --}}
                <button wire:click="closeModal"
                    class="absolute top-4 right-4 w-10 h-10 flex items-center justify-center rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 hover:rotate-90 transition-all duration-300">
                    <i class="fa-solid fa-times text-lg"></i>
                </button>

                @if($modalType === 'success')
                    {{-- Success Modal --}}
                    <div class="text-center">
                        <div class="w-20 h-20 bg-gradient-to-br from-green-400 to-emerald-500 rounded-full flex items-center justify-center mx-auto mb-6 animate-bounce">
                            <i class="fa-solid fa-check text-4xl text-white"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">
                            Talebiniz Alındı!
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-6">
                            En kısa sürede sizi arayacağız.
                        </p>
                        <button wire:click="closeModal"
                            class="w-full bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-bold py-3 px-6 rounded-xl transition-all hover:scale-105 active:scale-95">
                            Tamam
                        </button>
                    </div>
                @else
                    {{-- Error Modal --}}
                    <div class="text-center">
                        <div class="w-20 h-20 bg-gradient-to-br from-red-400 to-pink-500 rounded-full flex items-center justify-center mx-auto mb-6 animate-pulse">
                            <i class="fa-solid fa-exclamation text-4xl text-white"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">
                            Bir Hata Oluştu
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-6">
                            Lütfen daha sonra tekrar deneyin.
                        </p>
                        <button wire:click="closeModal"
                            class="w-full bg-gradient-to-r from-red-600 to-pink-600 hover:from-red-700 hover:to-pink-700 text-white font-bold py-3 px-6 rounded-xl transition-all hover:scale-105 active:scale-95">
                            Kapat
                        </button>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>

<div>
    {{-- Sizi Arayalım Formu - Minimal --}}
    <form wire:submit.prevent="submit" class="space-y-5">

        {{-- İsim --}}
        <div>
            <input type="text" wire:model="name" placeholder="Adınız Soyadınız *"
                class="w-full px-6 py-5 text-lg border-2 border-gray-300 dark:border-gray-600 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 transition-all">
            @error('name') <span class="text-red-600 text-sm flex items-center gap-1 mt-2"><i class="fa-solid fa-exclamation-circle"></i>{{ $message }}</span> @enderror
        </div>

        {{-- Telefon --}}
        <div>
            <input type="tel" wire:model="phone" placeholder="Telefon Numaranız *"
                class="w-full px-6 py-5 text-lg border-2 border-gray-300 dark:border-gray-600 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 transition-all">
            @error('phone') <span class="text-red-600 text-sm flex items-center gap-1 mt-2"><i class="fa-solid fa-exclamation-circle"></i>{{ $message }}</span> @enderror
        </div>

        {{-- Email --}}
        <div>
            <input type="email" wire:model="email" placeholder="E-posta Adresiniz *"
                class="w-full px-6 py-5 text-lg border-2 border-gray-300 dark:border-gray-600 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 transition-all">
            @error('email') <span class="text-red-600 text-sm flex items-center gap-1 mt-2"><i class="fa-solid fa-exclamation-circle"></i>{{ $message }}</span> @enderror
        </div>

        {{-- Submit Button --}}
        <button type="submit"
            class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-bold py-5 px-6 rounded-2xl transition-all shadow-lg hover:shadow-xl text-lg"
            wire:loading.attr="disabled"
            wire:loading.class="opacity-75 cursor-not-allowed">
            <span wire:loading.remove>Hemen Arayın</span>
            <span wire:loading>Gönderiliyor...</span>
        </button>

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
                        <div class="w-20 h-20 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fa-solid fa-check text-4xl text-white"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">
                            Talebiniz Alındı!
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-6">
                            En kısa sürede sizi arayacağız.
                        </p>
                        <button wire:click="closeModal"
                            class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-xl transition-all">
                            Tamam
                        </button>
                    </div>
                @else
                    {{-- Error Modal --}}
                    <div class="text-center">
                        <div class="w-20 h-20 bg-red-500 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fa-solid fa-exclamation text-4xl text-white"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">
                            Bir Hata Oluştu
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-6">
                            Lütfen daha sonra tekrar deneyin.
                        </p>
                        <button wire:click="closeModal"
                            class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-6 rounded-xl transition-all">
                            Kapat
                        </button>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>

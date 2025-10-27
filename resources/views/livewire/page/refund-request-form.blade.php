<div>
    {{-- Cayma Hakkı Formu --}}
    <form wire:submit.prevent="submit" class="space-y-4">
        <div>
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Sipariş Numarası *</label>
            <input type="text" wire:model="order_number" placeholder="ORD-XXXXX"
                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-900 text-gray-900 dark:text-white">
            @error('order_number') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        </div>

        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Sipariş Tarihi *</label>
                <input type="date" wire:model="order_date"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white">
                @error('order_date') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Teslim Tarihi *</label>
                <input type="date" wire:model="delivery_date"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white">
                @error('delivery_date') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">İade Edilecek Ürün(ler) *</label>
            <textarea rows="3" wire:model="products" placeholder="Ürün adı, model, adet bilgisini giriniz"
                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white"></textarea>
            @error('products') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        </div>

        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Fatura No</label>
                <input type="text" wire:model="invoice_number"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">T.C. Kimlik No *</label>
                <input type="text" wire:model="tc_number" maxlength="11"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white">
                @error('tc_number') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Ad Soyad *</label>
            <input type="text" wire:model="full_name"
                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white">
            @error('full_name') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Adres *</label>
            <textarea rows="2" wire:model="address" placeholder="Teslimat adresiniz"
                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white"></textarea>
            @error('address') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        </div>

        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">E-posta *</label>
                <input type="email" wire:model="email"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white">
                @error('email') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Telefon *</label>
                <input type="tel" wire:model="phone"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white">
                @error('phone') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Cayma Nedeni (İsteğe Bağlı)</label>
            <textarea rows="2" wire:model="refund_reason" placeholder="Neden cayma hakkı kullanıyorsunuz? (zorunlu değil)"
                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white"></textarea>
        </div>

        <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg">
            <label class="flex items-start gap-3">
                <input type="checkbox" wire:model="terms_accepted" class="mt-1">
                <span class="text-sm text-gray-700 dark:text-gray-300">
                    Cayma hakkı kullanım şartlarını okudum ve kabul ediyorum. Ürünü/ürünleri 10 gün içinde kargoya vereceğimi, kargo ücretinin bana ait olduğunu biliyorum ve kabul ediyorum.
                </span>
            </label>
            @error('terms_accepted') <span class="text-red-600 text-sm block mt-2">{{ $message }}</span> @enderror
        </div>

        <button type="submit"
            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg transition-colors flex items-center justify-center gap-2"
            wire:loading.attr="disabled">
            <span wire:loading.remove>
                <i class="fa-solid fa-paper-plane"></i> Cayma Hakkı Talebini Gönder
            </span>
            <span wire:loading>
                <i class="fa-solid fa-spinner fa-spin"></i> Gönderiliyor...
            </span>
        </button>
    </form>

    {{-- Success/Error Modal --}}
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
             x-data="{ show: @entangle('showModal') }"
             x-show="show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-md w-full p-8 relative"
                 @click.away="$wire.closeModal()"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-90"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-90">

                {{-- Close Button --}}
                <button wire:click="closeModal"
                    class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                    <i class="fa-solid fa-times text-xl"></i>
                </button>

                @if($modalType === 'success')
                    {{-- Success Modal --}}
                    <div class="text-center">
                        <div class="w-20 h-20 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fa-solid fa-check text-4xl text-green-600 dark:text-green-400"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">
                            Talebiniz Alındı!
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-6 leading-relaxed">
                            Cayma hakkı talebiniz başarıyla iletildi. En kısa sürede tarafınıza dönüş yapılacaktır.
                        </p>
                        <button wire:click="closeModal"
                            class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg transition-colors">
                            Tamam
                        </button>
                    </div>
                @else
                    {{-- Error Modal --}}
                    <div class="text-center">
                        <div class="w-20 h-20 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fa-solid fa-times text-4xl text-red-600 dark:text-red-400"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">
                            Bir Hata Oluştu
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-6 leading-relaxed">
                            Talebiniz gönderilirken bir sorun yaşandı. Lütfen daha sonra tekrar deneyin veya bizimle iletişime geçin.
                        </p>
                        <button wire:click="closeModal"
                            class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-6 rounded-lg transition-colors">
                            Kapat
                        </button>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>

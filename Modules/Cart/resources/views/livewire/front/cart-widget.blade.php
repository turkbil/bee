{{-- CartWidget - SIFIRDAN BASIT --}}
<div
    x-data="{
        open: false,
        itemCount: {{ $itemCount }},
        items: @js($items)
    }"
    @cart-item-added.window="
        console.log('🔔 cart-item-added event', $event.detail);

        // Item count güncelle
        itemCount = parseInt($event.detail.itemCount);

        // Yeni item'ı ekle (eğer listede yoksa)
        const existingIndex = items.findIndex(i => i.cart_item_id === $event.detail.cartItemId);
        if (existingIndex === -1) {
            items.push({
                cart_item_id: $event.detail.cartItemId,
                name: $event.detail.productName,
                image: $event.detail.productImage,
                price: $event.detail.productPrice,
                quantity: $event.detail.quantity
            });
        } else {
            // Quantity güncelle
            items[existingIndex].quantity = $event.detail.quantity;
        }

        console.log('✅ CartWidget updated:', { itemCount, itemsCount: items.length });
    "
    class="relative">

    {{-- Cart Button + Badge --}}
    <button
        @click="open = !open"
        type="button"
        class="relative flex items-center gap-2 px-3 py-2 text-gray-700 dark:text-gray-200 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">
        <i class="fas fa-shopping-cart text-xl"></i>

        {{-- Badge - Sadece itemCount > 0 ise göster --}}
        <span
            x-show="itemCount > 0"
            x-text="itemCount"
            class="absolute -top-1 -right-1 flex items-center justify-center min-w-[20px] h-5 px-1.5 text-xs font-bold text-white bg-primary-600 rounded-full">
        </span>
    </button>

    {{-- Dropdown --}}
    <div
        x-show="open"
        @click.away="open = false"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute right-0 z-50 mt-2 w-80 bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700"
        style="display: none;">

        <div class="p-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">
                Alışveriş Sepeti
            </h3>

            {{-- Boş sepet --}}
            <div x-show="items.length === 0" class="py-8 text-center">
                <i class="fas fa-shopping-cart text-4xl text-gray-400 mb-3"></i>
                <p class="text-gray-500 dark:text-gray-400">
                    Sepetiniz boş
                </p>
            </div>

            {{-- Item listesi --}}
            <div x-show="items.length > 0" class="space-y-3 max-h-96 overflow-y-auto">
                <template x-for="item in items" :key="item.cart_item_id">
                    <div class="flex gap-3 p-2 hover:bg-gray-50 dark:hover:bg-gray-700/50 rounded-lg transition-colors">
                        {{-- Item Image --}}
                        <div x-show="item.image" class="w-16 h-16">
                            <img :src="item.image" :alt="item.name" class="w-full h-full object-cover rounded-lg">
                        </div>
                        <div x-show="!item.image" class="w-16 h-16 bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                            <i class="fas fa-box text-gray-400"></i>
                        </div>

                        {{-- Item Info --}}
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white truncate" x-text="item.name"></h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                <span x-text="parseFloat(item.price).toLocaleString('tr-TR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })"></span> TRY
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">
                                Adet: <span x-text="item.quantity"></span>
                            </p>
                        </div>
                    </div>
                </template>
            </div>

            {{-- Sepeti Görüntüle Button (items varsa) --}}
            <div x-show="items.length > 0" class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                <a
                    href="{{ route('cart.index') }}"
                    class="block w-full py-2 px-4 bg-primary-600 hover:bg-primary-700 text-white text-center rounded-lg font-medium transition-colors">
                    Sepeti Görüntüle
                </a>
            </div>
        </div>
    </div>
</div>

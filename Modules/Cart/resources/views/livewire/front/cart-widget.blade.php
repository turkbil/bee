<div x-data="{ open: false }" class="relative" @close-user-menu.window="open = false" @cart-updated.window="$wire.refreshCartById(parseInt($event.detail?.cartId || localStorage.getItem('cart_id') || 0))">
    {{-- Cart Button --}}
    <button @click="open = !open; $dispatch('close-other-menus')" type="button"
            class="relative flex items-center gap-2 px-3 py-2 text-gray-700 dark:text-gray-200 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">
        <i class="fas fa-shopping-cart text-xl"></i>
        @if($itemCount > 0)
            <span class="absolute -top-1 -right-1 flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-primary-600 rounded-full">
                {{ $itemCount }}
            </span>
        @endif
    </button>

    {{-- Cart Dropdown --}}
    <div x-show="open"
         @click.away="open = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute right-0 top-full mt-2 w-80 z-50 bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 origin-top-right"
         style="display: none;">

        <div class="p-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">
                {{ __('cart::front.shopping_cart') }}
            </h3>

            @if(empty($items))
                <div class="py-8 text-center">
                    <i class="fas fa-shopping-cart text-4xl text-gray-400 mb-3"></i>
                    <p class="text-gray-500 dark:text-gray-400">
                        {{ __('cart::front.cart_empty') }}
                    </p>
                </div>
            @else
                {{-- Cart Items --}}
                <div class="space-y-3 max-h-96 overflow-y-auto">
                    @foreach($items as $item)
                        <div wire:key="cart-item-{{ $item['cart_item_id'] }}"
                             class="flex gap-3 p-2 hover:bg-gray-50 dark:hover:bg-gray-700/50 rounded-lg transition-colors">
                            {{-- Item Image --}}
                            @if($item['item_image'])
                                <img src="{{ $item['item_image'] }}"
                                     alt="{{ $item['item_name'] }}"
                                     class="w-16 h-16 object-cover rounded-lg">
                            @else
                                <div class="w-16 h-16 bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-box text-gray-400"></i>
                                </div>
                            @endif

                            {{-- Item Info --}}
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                    {{ $item['item_name'] }}
                                </h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                    {{ number_format($item['unit_price'], 0, ',', '.') }} TL
                                </p>

                                {{-- Quantity Controls --}}
                                <div class="flex items-center gap-2 mt-2">
                                    <button wire:click="decreaseQuantity({{ $item['cart_item_id'] }})"
                                            class="w-6 h-6 flex items-center justify-center bg-gray-200 dark:bg-gray-600 hover:bg-gray-300 dark:hover:bg-gray-500 rounded transition-colors">
                                        <i class="fas fa-minus text-xs"></i>
                                    </button>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white px-2">
                                        {{ $item['quantity'] }}
                                    </span>
                                    <button wire:click="increaseQuantity({{ $item['cart_item_id'] }})"
                                            class="w-6 h-6 flex items-center justify-center bg-gray-200 dark:bg-gray-600 hover:bg-gray-300 dark:hover:bg-gray-500 rounded transition-colors">
                                        <i class="fas fa-plus text-xs"></i>
                                    </button>
                                    <button wire:click="removeItem({{ $item['cart_item_id'] }})"
                                            class="ml-auto text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300">
                                        <i class="fas fa-trash text-sm"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Cart Footer --}}
                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    {{-- Ara Toplam --}}
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            {{ __('cart::front.subtotal') }}:
                        </span>
                        <span class="text-sm text-gray-700 dark:text-gray-300">
                            {{ number_format($subtotal, 0, ',', '.') }} TL
                        </span>
                    </div>

                    {{-- KDV --}}
                    @if($taxAmount > 0)
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            {{ __('cart::front.tax') }}:
                        </span>
                        <span class="text-sm text-gray-700 dark:text-gray-300">
                            +{{ number_format($taxAmount, 0, ',', '.') }} TL
                        </span>
                    </div>
                    @endif

                    {{-- Genel Toplam --}}
                    <div class="flex justify-between items-center mb-3 pt-2 border-t border-gray-100 dark:border-gray-600">
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400">
                            {{ __('cart::front.total') }}:
                        </span>
                        <span class="text-lg font-bold text-gray-900 dark:text-white">
                            {{ number_format($total, 0, ',', '.') }} TL
                        </span>
                    </div>

                    <a href="{{ route('cart.index') }}"
                       class="block w-full py-2 px-4 bg-primary-600 hover:bg-primary-700 text-white text-center rounded-lg font-medium transition-colors">
                        {{ __('cart::front.view_cart') }}
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

@script
<script>
    (function() {
        // Sayfa yüklendiğinde localStorage'dan cart_id ile yükle
        var initCartId = localStorage.getItem('cart_id');
        if (initCartId) {
            console.log('🛒 CartWidget: Init - Loading cart from localStorage', initCartId);
            $wire.refreshCartById(parseInt(initCartId));
        }

        // Listen for cartUpdated event and refresh
        $wire.on('cartUpdated', function() {
            var cartId = localStorage.getItem('cart_id');
            console.log('🔄 CartWidget: Event received', cartId);
            if (cartId) {
                $wire.refreshCartById(parseInt(cartId));
            } else {
                $wire.refreshCart();
            }
        });
    })();
</script>
@endscript

<div x-data="{
        open: false,
        isLoading: false,
        previousCount: {{ $itemCount }},
        currentCount: {{ $itemCount }},
        badgeAnimate: false,
        iconLoading: false,
        bounceCart() {
            this.isLoading = true;
            setTimeout(() => { this.isLoading = false; }, 1000);
        },
        updateCount(newCount) {
            if (this.currentCount !== newCount) {
                this.previousCount = this.currentCount;
                this.currentCount = newCount;
                this.badgeAnimate = true;
                setTimeout(() => { this.badgeAnimate = false; }, 600);
            }
        }
    }"
    @optimistic-add.window="bounceCart()"
    x-init="$watch('$wire.itemCount', value => updateCount(value))"
    wire:loading.class="opacity-75"
    class="relative"
    @close-user-menu.window="open = false">
    {{-- Cart Button --}}
    <button @click="open = !open; $dispatch('close-other-menus')" type="button"
            class="relative flex items-center gap-2 px-3 py-2 text-gray-700 dark:text-gray-200 hover:text-primary-600 dark:hover:text-primary-400 transition-all duration-300"
            :class="{ 'scale-110': isLoading }">
        {{-- Cart Icon with Loading --}}
        <div class="relative">
            <i class="fas fa-shopping-cart text-xl transition-all duration-300"
               :class="{
                   'animate-bounce text-primary-600 dark:text-primary-400': isLoading,
                   'opacity-0': iconLoading
               }"
               wire:loading.class="!opacity-0"></i>
            {{-- Loading Spinner --}}
            <i class="fas fa-spinner fa-spin text-xl absolute inset-0 text-primary-600 dark:text-primary-400 opacity-0 transition-opacity"
               :class="{ 'opacity-100': iconLoading }"
               wire:loading.class="!opacity-100"></i>
        </div>

        {{-- Badge with Animation --}}
        @if($itemCount > 0)
            <span class="absolute -top-1 -right-1 flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-primary-600 rounded-full transition-all duration-300 transform"
                  :class="{
                      'scale-125 animate-pulse': isLoading,
                      'scale-150 opacity-0': badgeAnimate
                  }"
                  style="transition: transform 0.3s ease, opacity 0.3s ease;">
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
                            <div class="flex-1 min-w-0 flex flex-col justify-between h-16">
                                <div>
                                    <h4 class="text-xs font-medium text-gray-900 dark:text-white line-clamp-2 leading-tight h-[28px] overflow-hidden">
                                        {{ $item['item_name'] }}
                                    </h4>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">
                                        {{ number_format($item['unit_price'], 0, ',', '.') }} TL
                                    </p>
                                </div>

                                {{-- Quantity Controls - Kompakt --}}
                                <div class="flex items-center gap-1.5">
                                    @php
                                        $isSubscription = str_contains($item['cartable_type'] ?? '', 'Subscription');
                                    @endphp

                                    @if(!$isSubscription)
                                        {{-- Quantity Controls - Sadece ürünler için --}}
                                        <button wire:click="decreaseQuantity({{ $item['cart_item_id'] }})"
                                                wire:loading.attr="disabled"
                                                wire:target="decreaseQuantity({{ $item['cart_item_id'] }})"
                                                class="w-5 h-5 flex items-center justify-center bg-gray-200 dark:bg-gray-600 hover:bg-gray-300 dark:hover:bg-gray-500 rounded transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                            <i class="fas fa-minus text-[10px]"
                                               wire:loading.remove
                                               wire:target="decreaseQuantity({{ $item['cart_item_id'] }})"></i>
                                            <i class="fas fa-spinner fa-spin text-[10px]"
                                               wire:loading
                                               wire:target="decreaseQuantity({{ $item['cart_item_id'] }})"></i>
                                        </button>
                                        <span class="text-xs font-medium text-gray-900 dark:text-white px-1.5">
                                            {{ $item['quantity'] }}
                                        </span>
                                        <button wire:click="increaseQuantity({{ $item['cart_item_id'] }})"
                                                wire:loading.attr="disabled"
                                                wire:target="increaseQuantity({{ $item['cart_item_id'] }})"
                                                class="w-5 h-5 flex items-center justify-center bg-gray-200 dark:bg-gray-600 hover:bg-gray-300 dark:hover:bg-gray-500 rounded transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                            <i class="fas fa-plus text-[10px]"
                                               wire:loading.remove
                                               wire:target="increaseQuantity({{ $item['cart_item_id'] }})"></i>
                                            <i class="fas fa-spinner fa-spin text-[10px]"
                                               wire:loading
                                               wire:target="increaseQuantity({{ $item['cart_item_id'] }})"></i>
                                        </button>
                                    @else
                                        {{-- Subscription - sadece miktar göster --}}
                                        <span class="text-xs font-medium text-gray-900 dark:text-white px-1.5">
                                            {{ $item['quantity'] }}
                                        </span>
                                    @endif

                                    {{-- Remove Button - Her zaman göster --}}
                                    <button wire:click="removeItem({{ $item['cart_item_id'] }})"
                                            wire:loading.attr="disabled"
                                            wire:target="removeItem({{ $item['cart_item_id'] }})"
                                            class="ml-auto text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 disabled:opacity-50 disabled:cursor-not-allowed">
                                        <i class="fas fa-trash text-xs"
                                           wire:loading.remove
                                           wire:target="removeItem({{ $item['cart_item_id'] }})"></i>
                                        <i class="fas fa-spinner fa-spin text-xs"
                                           wire:loading
                                           wire:target="removeItem({{ $item['cart_item_id'] }})"></i>
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

                    {{-- Action Buttons - 2 Buton Yan Yana --}}
                    <div class="grid grid-cols-2 gap-2">
                        <a href="{{ route('cart.index') }}"
                           class="py-2 px-3 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-900 dark:text-white text-center rounded-lg font-medium text-sm transition-colors">
                            <i class="fas fa-shopping-cart mr-1"></i>
                            Sepet
                        </a>
                        <a href="{{ route('cart.checkout') }}"
                           class="py-2 px-3 bg-primary-600 hover:bg-primary-700 text-white text-center rounded-lg font-medium text-sm transition-colors">
                            <i class="fas fa-credit-card mr-1"></i>
                            Ödeme
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@script
<script>
    (function() {
        // Server-side değişkenler
        const serverCartId = {{ $cartId ?? 'null' }};
        const cartMergeCompleted = {{ session('cart_merge_completed') ? 'true' : 'false' }};
        const mergedCartId = {{ session('merged_cart_id') ?? 'null' }};

        @php
            // Session temizleme (tek seferlik)
            session()->forget('cart_merge_completed');
            session()->forget('merged_cart_id');
        @endphp

        // 🔐 USER LOGIN - Server'dan yüklenen cart_id'yi localStorage'a kaydet
        if (serverCartId) {
            const localCartId = localStorage.getItem('cart_id');

            if (localCartId != serverCartId) {
                console.log('🔐 CartWidget: Syncing localStorage with server cart', {
                    local: localCartId,
                    server: serverCartId
                });
                localStorage.setItem('cart_id', serverCartId);
            }
        }

        // 🔄 LOGIN SONRASI CART MERGE - localStorage güncelle + Sayfa Yenile
        if (cartMergeCompleted && mergedCartId) {
            console.log('🔀 Cart Merge Detected! Updating localStorage with new cart_id:', mergedCartId);
            localStorage.setItem('cart_id', mergedCartId);

            // 🔄 SAYFA YENİLE - Login sonrası CSRF token ve Livewire state'i yenilenir
            console.log('🔄 Refreshing page to sync CSRF token and cart...');
            window.location.reload();
            return; // Reload başladı, script'in kalanını çalıştırma
        } else if (!serverCartId) {
            // Guest kullanıcı - localStorage'dan cart_id ile yükle
            const initCartId = localStorage.getItem('cart_id');
            if (initCartId) {
                console.log('🛒 CartWidget: Init - Loading cart from localStorage', initCartId);
                $wire.refreshCartById(parseInt(initCartId));
            }
        }

        // Listen for cartUpdated event and refresh
        $wire.on('cartUpdated', function() {
            const cartId = localStorage.getItem('cart_id');
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

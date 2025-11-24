<div class="container mx-auto px-4 py-8 md:py-12">
    {{-- Success/Error Messages --}}
    <div x-data="{ show: false, message: '', isError: false }"
         @cart-updated.window="show = true; message = $event.detail.message; isError = false; setTimeout(() => show = false, 3000)"
         @cart-item-removed.window="show = true; message = $event.detail.message; isError = false; setTimeout(() => show = false, 3000)"
         @cart-cleared.window="show = true; message = $event.detail.message; isError = false; setTimeout(() => show = false, 3000)"
         @cart-error.window="show = true; message = $event.detail.message; isError = true; setTimeout(() => show = false, 5000)"
         x-show="show"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         style="display: none; z-index: 9999;"
         :class="isError ? 'bg-red-100 dark:bg-red-900/30 border-red-400 dark:border-red-700 text-red-700 dark:text-red-300' : 'bg-green-100 dark:bg-green-900/30 border-green-400 dark:border-green-700 text-green-700 dark:text-green-300'"
         class="fixed top-32 right-4 max-w-md border px-4 py-3 rounded-lg flex items-center gap-3 shadow-lg">
        <i :class="isError ? 'fa-circle-xmark' : 'fa-circle-check'" class="fa-solid text-xl"></i>
        <span x-text="message" class="flex-1"></span>
    </div>

    {{-- Page Header --}}
    <div class="mb-8">
        <h1 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-2">
            <i class="fa-solid fa-shopping-cart mr-3"></i>
            {{ __('cart::front.my_cart') }}
        </h1>
        <p class="text-gray-600 dark:text-gray-400">
            @if($itemCount > 0)
                {{ $itemCount }} {{ __('cart::front.product') }}
            @else
                {{ __('cart::front.cart_empty') }}
            @endif
        </p>
    </div>

    @if($items->count() > 0)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Cart Items Section --}}
            <div class="lg:col-span-2 space-y-4">
                @foreach($items as $item)
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 hover:shadow-xl transition-shadow"
                         wire:key="cart-item-{{ $item->cart_item_id }}">
                        <div class="flex flex-col md:flex-row gap-6">
                            {{-- Item Image --}}
                            <div class="flex-shrink-0">
                                @if($item->item_image)
                                    <img src="{{ $item->item_image }}"
                                         alt="{{ $item->item_name }}"
                                         class="w-24 h-24 md:w-32 md:h-32 object-cover rounded-lg"
                                         loading="lazy">
                                @else
                                    <div class="w-24 h-24 md:w-32 md:h-32 bg-gradient-to-br from-blue-50 via-white to-purple-50 dark:from-slate-600 dark:via-slate-500 dark:to-slate-600 rounded-lg flex items-center justify-center">
                                        <i class="fa-solid fa-box text-4xl md:text-5xl text-blue-400"></i>
                                    </div>
                                @endif
                            </div>

                            {{-- Item Info --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-4 mb-3">
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">
                                            {{ $item->item_name }}
                                        </h3>
                                        @if($item->item_description)
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $item->item_description }}
                                            </p>
                                        @endif
                                    </div>

                                    {{-- Remove Button --}}
                                    <button wire:click="removeItem({{ $item->cart_item_id }})"
                                            wire:loading.attr="disabled"
                                            class="flex-shrink-0 text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 transition-colors disabled:opacity-50"
                                            title="{{ __('cart::front.remove') }}">
                                        <i class="fa-solid fa-trash-can text-lg"
                                           wire:loading.remove wire:target="removeItem({{ $item->cart_item_id }})"></i>
                                        <i class="fa-solid fa-spinner fa-spin text-lg"
                                           wire:loading wire:target="removeItem({{ $item->cart_item_id }})"
                                           style="display: none;"></i>
                                    </button>
                                </div>

                                {{-- Price & Quantity --}}
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                                    {{-- Quantity Controls --}}
                                    <div class="flex items-center gap-3">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('cart::front.quantity') }}:</span>
                                        <div class="flex items-center gap-2 bg-gray-100 dark:bg-gray-700 rounded-lg p-1">
                                            <button wire:click="decreaseQuantity({{ $item->cart_item_id }})"
                                                    wire:loading.attr="disabled"
                                                    class="w-8 h-8 flex items-center justify-center text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 rounded transition-colors disabled:opacity-50"
                                                    {{ $item->quantity <= 1 ? 'disabled' : '' }}>
                                                <i class="fa-solid fa-minus"></i>
                                            </button>
                                            <span class="w-12 text-center font-semibold text-gray-900 dark:text-white">
                                                {{ $item->quantity }}
                                            </span>
                                            <button wire:click="increaseQuantity({{ $item->cart_item_id }})"
                                                    wire:loading.attr="disabled"
                                                    class="w-8 h-8 flex items-center justify-center text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 rounded transition-colors disabled:opacity-50">
                                                <i class="fa-solid fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>

                                    {{-- Price --}}
                                    <div class="text-right">
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">
                                            {{ __('cart::front.price') }}: {{ number_format($item->unit_price, 0, ',', '.') }} TL
                                        </p>
                                        <p class="text-lg font-bold text-gray-900 dark:text-white">
                                            {{ __('cart::front.subtotal') }}: {{ number_format($item->subtotal, 0, ',', '.') }} TL
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Cart Summary Section --}}
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 sticky top-24">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">
                        {{ __('cart::front.cart_totals') }}
                    </h2>

                    <div class="space-y-3 border-b border-gray-200 dark:border-gray-700 pb-4 mb-4">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">{{ __('cart::front.subtotal') }}</span>
                            <span class="font-semibold text-gray-900 dark:text-white">
                                {{ number_format($subtotal, 0, ',', '.') }} TL
                            </span>
                        </div>

                        @if($cart->tax_amount > 0)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">{{ __('cart::front.tax') }}</span>
                                <span class="font-semibold text-gray-900 dark:text-white">
                                    {{ number_format($cart->tax_amount, 0, ',', '.') }} TL
                                </span>
                            </div>
                        @endif
                    </div>

                    <div class="flex justify-between items-center mb-6">
                        <span class="text-lg font-bold text-gray-900 dark:text-white">{{ __('cart::front.grand_total') }}</span>
                        <span class="text-2xl font-bold text-primary-600 dark:text-primary-400">
                            {{ number_format($total, 0, ',', '.') }} TL
                        </span>
                    </div>

                    <div class="space-y-3">
                        <a href="{{ route('cart.checkout') }}"
                           class="block w-full bg-primary-600 hover:bg-primary-700 text-white text-center font-bold py-3 px-6 rounded-lg transition-colors">
                            {{ __('cart::front.checkout') }}
                        </a>

                        <a href="/shop"
                           class="block w-full bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-900 dark:text-white text-center font-medium py-3 px-6 rounded-lg transition-colors">
                            {{ __('cart::front.continue_shopping') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @else
        {{-- Empty Cart --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-12 text-center">
            <i class="fa-solid fa-shopping-cart text-6xl text-gray-300 dark:text-gray-600 mb-6"></i>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">
                {{ __('cart::front.cart_empty') }}
            </h2>
            <p class="text-gray-600 dark:text-gray-400 mb-8">
                Sepetinizde henüz ürün bulunmuyor.
            </p>
            <a href="/shop"
               class="inline-flex items-center gap-2 bg-primary-600 hover:bg-primary-700 text-white font-bold py-3 px-8 rounded-lg transition-colors">
                <i class="fa-solid fa-shopping-bag"></i>
                <span>{{ __('cart::front.continue_shopping') }}</span>
            </a>
        </div>
    @endif
</div>

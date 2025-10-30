<div x-data="{ open: false }" @click.away="open = false" @close-cart.window="open = false" class="relative">
    {{-- Cart Icon Button --}}
    <button @click="open = !open"
            class="relative flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
        <i class="fa-solid fa-shopping-cart text-lg text-gray-700 dark:text-gray-300"></i>

        @if($itemCount > 0)
            <span class="absolute -top-1 -right-1 bg-red-600 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center animate-pulse">
                {{ $itemCount }}
            </span>
        @endif

        <span class="hidden md:inline text-sm font-medium text-gray-700 dark:text-gray-300">
            Sepet
        </span>
    </button>

    {{-- Cart Dropdown --}}
    <div x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute right-0 mt-2 w-96 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 z-50"
         style="display: none;">

        {{-- Header --}}
        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                <i class="fa-solid fa-shopping-cart mr-2"></i>
                Sepetim
            </h3>
            <span class="text-sm text-gray-500 dark:text-gray-400">
                {{ $itemCount }} ürün
            </span>
        </div>

        {{-- Cart Items --}}
        <div class="max-h-96 overflow-y-auto">
            @if($items->count() > 0)
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($items as $item)
                        <div class="px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <div class="flex gap-3">
                                {{-- Product Image --}}
                                @php
                                    $media = $item->product->getFirstMedia('gallery');
                                    $imageUrl = $media ? thumb($media, 80, 80) : asset('images/no-image.jpg');
                                @endphp
                                <img src="{{ $imageUrl }}"
                                     alt="{{ $item->product->getTranslated('title', app()->getLocale()) }}"
                                     class="w-16 h-16 object-cover rounded-lg">

                                {{-- Product Info --}}
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                        {{ $item->product->getTranslated('title', app()->getLocale()) }}
                                    </h4>

                                    {{-- Quantity Controls --}}
                                    <div class="flex items-center gap-2 mt-2">
                                        <button wire:click="decreaseQuantity({{ $item->cart_item_id }})"
                                                class="w-6 h-6 flex items-center justify-center bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 rounded text-gray-700 dark:text-gray-300 transition-colors">
                                            <i class="fa-solid fa-minus text-xs"></i>
                                        </button>
                                        <span class="text-sm font-medium text-gray-900 dark:text-white min-w-[2rem] text-center">
                                            {{ $item->quantity }}
                                        </span>
                                        <button wire:click="increaseQuantity({{ $item->cart_item_id }})"
                                                class="w-6 h-6 flex items-center justify-center bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 rounded text-gray-700 dark:text-gray-300 transition-colors">
                                            <i class="fa-solid fa-plus text-xs"></i>
                                        </button>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">
                                            × {{ number_format($item->final_price, 2) }} ₺
                                        </span>
                                    </div>

                                    <p class="text-sm font-semibold text-gray-900 dark:text-white mt-1">
                                        {{ number_format($item->subtotal, 2) }} ₺
                                    </p>
                                </div>

                                {{-- Remove Button --}}
                                <button wire:click="removeItem({{ $item->cart_item_id }})"
                                        class="text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition-colors">
                                    <i class="fa-solid fa-times text-lg"></i>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="px-4 py-8 text-center">
                    <i class="fa-solid fa-shopping-cart text-5xl text-gray-300 dark:text-gray-600 mb-3"></i>
                    <p class="text-gray-500 dark:text-gray-400 text-sm">
                        Sepetiniz boş
                    </p>
                </div>
            @endif
        </div>

        {{-- Footer --}}
        @if($items->count() > 0)
            <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 rounded-b-xl">
                {{-- Total --}}
                <div class="flex items-center justify-between mb-3">
                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400">
                        Toplam:
                    </span>
                    <span class="text-lg font-bold text-gray-900 dark:text-white">
                        {{ number_format($total, 2) }} ₺
                    </span>
                </div>

                {{-- Buttons --}}
                <div class="flex gap-2">
                    <a href="{{ route('shop.cart') }}"
                       class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-center text-sm font-semibold py-2 px-4 rounded-lg transition-colors">
                        Sepeti Görüntüle
                    </a>
                    <a href="{{ route('shop.checkout') }}"
                       class="flex-1 bg-green-600 hover:bg-green-700 text-white text-center text-sm font-semibold py-2 px-4 rounded-lg transition-colors">
                        Sipariş Ver
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    // Toast notification için
    window.addEventListener('cart-item-removed', event => {
        // Toast göster (opsiyonel)
        console.log(event.detail.message);
    });

    window.addEventListener('product-added-to-cart', event => {
        // Toast göster (opsiyonel)
        console.log(event.detail.message);
    });
</script>
@endpush

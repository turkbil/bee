<div class="inline-flex items-center gap-2">
    @if($showQuantity)
        {{-- Quantity Selector --}}
        <div class="flex items-center border border-gray-300 dark:border-gray-600 rounded-lg overflow-hidden">
            <button type="button"
                    wire:click="decreaseQuantity"
                    class="px-3 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 transition-colors">
                <i class="fa-solid fa-minus text-sm"></i>
            </button>

            <input type="number"
                   wire:model="quantity"
                   min="1"
                   class="w-16 px-2 py-2 text-center border-x border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">

            <button type="button"
                    wire:click="increaseQuantity"
                    class="px-3 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 transition-colors">
                <i class="fa-solid fa-plus text-sm"></i>
            </button>
        </div>
    @endif

    {{-- Add to Cart Button --}}
    <button type="button"
            wire:click="addToCart"
            wire:loading.attr="disabled"
            class="{{ $buttonClass }} relative"
            :disabled="$wire.isAdding">

        {{-- Loading Spinner --}}
        <span wire:loading wire:target="addToCart" class="absolute inset-0 flex items-center justify-center">
            <i class="fa-solid fa-spinner fa-spin"></i>
        </span>

        {{-- Button Content --}}
        <span wire:loading.remove wire:target="addToCart" class="flex items-center gap-2">
            <i class="fa-solid fa-shopping-cart"></i>
            <span>{{ $buttonText }}</span>
        </span>
    </button>
</div>

@push('scripts')
<script>
    window.addEventListener('product-added-to-cart', event => {
        // Success toast (opsiyonel - Alpine.js notify component varsa kullan)
        if (typeof window.notify !== 'undefined') {
            window.notify('success', event.detail.message);
        } else {
            console.log('✅', event.detail.message);
        }
    });

    window.addEventListener('cart-error', event => {
        // Error toast
        if (typeof window.notify !== 'undefined') {
            window.notify('error', event.detail.message);
        } else {
            alert(event.detail.message);
        }
    });
</script>
@endpush

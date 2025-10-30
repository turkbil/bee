<div class="container mx-auto px-4 py-8 md:py-12">
    {{-- Page Header --}}
    <div class="mb-8">
        <h1 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-2">
            <i class="fa-solid fa-shopping-cart mr-3"></i>
            Alışveriş Sepeti
        </h1>
        <p class="text-gray-600 dark:text-gray-400">
            Sepetinizdeki {{ $itemCount }} ürün
        </p>
    </div>

    {{-- DEBUG: Test if basic render works --}}
    @if($items->count() > 0)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
            <p class="text-gray-900 dark:text-white">
                ✅ Sepette {{ $items->count() }} ürün var
            </p>
            <p class="text-gray-600 dark:text-gray-400 mt-2">
                Toplam: {{ number_format($total, 0, ',', '.') }} ₺
            </p>
        </div>
    @else
        {{-- Empty Cart State --}}
        <div class="text-center py-16">
            <div class="inline-flex items-center justify-center w-32 h-32 bg-gray-100 dark:bg-gray-800 rounded-full mb-6">
                <i class="fa-solid fa-shopping-cart text-6xl text-gray-300 dark:text-gray-600"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">
                Sepetiniz Boş
            </h2>
            <p class="text-gray-600 dark:text-gray-400 mb-8">
                Alışverişe başlamak için ürünlerimize göz atın
            </p>
            <a href="{{ route('shop.index') }}"
               class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-8 rounded-lg transition-colors">
                <i class="fa-solid fa-shopping-bag"></i>
                <span>Ürünleri İncele</span>
            </a>
        </div>
    @endif
</div>

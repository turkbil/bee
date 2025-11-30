@if ($products->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        @foreach ($products as $product)
            @php
                $heroImage = getFirstMediaWithFallback($product);
            @endphp
            <article
                class="border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden hover:shadow-md transition-shadow">
                @if($heroImage)
                    <div class="aspect-video overflow-hidden bg-gray-100 dark:bg-gray-800">
                        <img src="{{ $heroImage->getUrl('thumb') }}"
                             alt="{{ $product->getTranslated('title', app()->getLocale()) }}"
                             class="w-full h-full object-cover"
                             loading="lazy">
                    </div>
                @endif
                <div class="p-6">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">
                        <a href="{{ Modules\Shop\App\Http\Controllers\Front\ShopController::resolveProductUrl($product) }}"
                            class="hover:text-blue-500">
                            {{ $product->getTranslated('title', app()->getLocale()) }}
                        </a>
                    </h2>

                    <p class="text-gray-600 dark:text-gray-400 mb-4">
                        {{ \Illuminate\Support\Str::limit($product->getTranslated('short_description', app()->getLocale()) ?? strip_tags($product->getTranslated('body', app()->getLocale())), 160) }}
                    </p>

                    <a href="{{ Modules\Shop\App\Http\Controllers\Front\ShopController::resolveProductUrl($product) }}"
                        class="inline-flex items-center text-blue-600 dark:text-blue-400 hover:underline">
                        {{ __('shop::front.general.read_more') }}
                        <svg class="ml-2 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M12.293 4.293a1 1 0 011.414 0L18 8.586a1 1 0 010 1.414l-4.293 4.293a1 1 0 01-1.414-1.414L14.586 10H4a1 1 0 110-2h10.586l-2.293-2.293a1 1 0 010-1.414z" />
                        </svg>
                    </a>
                </div>
            </article>
        @endforeach
    </div>

    <div class="pt-8">
        {{ $products->links() }}
    </div>
@else
    <div class="text-center py-16">
        <p class="text-gray-500 dark:text-gray-400">
            {{ __('shop::front.general.no_products') }}
        </p>
    </div>
@endif

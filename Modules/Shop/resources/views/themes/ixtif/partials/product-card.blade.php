{{-- Product Card Component - iXtif Theme --}}
<div class="group bg-white/70 dark:bg-white/5 backdrop-blur-md border border-gray-200 dark:border-white/10 rounded-2xl overflow-hidden hover:bg-white/90 dark:hover:bg-white/10 hover:shadow-xl hover:border-blue-300 dark:hover:border-white/20 transition-all">
    {{-- Product Image --}}
    <a href="{{ \Modules\Shop\App\Http\Controllers\Front\ShopController::resolveProductUrl($product) }}"
       class="block aspect-square rounded-xl flex items-center justify-center overflow-hidden bg-gradient-to-br from-blue-50 via-white to-purple-50 dark:from-slate-600 dark:via-slate-500 dark:to-slate-600">
        @if($product->hasMedia('featured_image'))
            <img src="{{ thumb($product->getFirstMedia('featured_image'), 400, 400, ['quality' => 85, 'scale' => 1, 'format' => 'webp']) }}"
                 alt="{{ $product->getTranslated('title') }}"
                 class="w-full h-full object-contain drop-shadow-product-light dark:drop-shadow-product-dark group-hover:scale-110 transition-transform duration-700"
                 loading="lazy"
                 width="400"
                 height="400">
        @else
            <div class="w-full h-full flex items-center justify-center">
                <i class="fa-light fa-box text-6xl text-gray-300 dark:text-gray-600"></i>
            </div>
        @endif
    </a>

    {{-- Content Section --}}
    <div class="p-3 md:p-4 lg:p-6 space-y-3 md:space-y-4 lg:space-y-5">
        {{-- Category with Icon --}}
        @if($product->category)
        <div class="flex items-center gap-2 mb-2">
            @if($product->category->icon_class)
                <i class="{{ $product->category->icon_class }} text-blue-600 dark:text-blue-400"></i>
            @endif
            <span class="text-xs text-blue-800 dark:text-blue-300 font-medium uppercase tracking-wider">
                {{ $product->category->getTranslated('title') }}
            </span>
        </div>
        @endif

        {{-- Title --}}
        <a href="{{ \Modules\Shop\App\Http\Controllers\Front\ShopController::resolveProductUrl($product) }}">
            <h3 class="text-base md:text-lg lg:text-xl font-bold text-gray-950 dark:text-gray-50 leading-relaxed line-clamp-2 min-h-[2.8rem] md:min-h-[3.2rem] lg:min-h-[3.5rem] group-hover:text-blue-800 dark:group-hover:text-blue-300 transition-colors">
                {{ $product->getTranslated('title') }}
            </h3>
        </a>

        {{-- Price & CTA --}}
        <div class="pt-3 md:pt-4 lg:pt-5 border-t border-gray-300 dark:border-gray-500 flex items-center justify-between">
            <div class="text-lg md:text-xl lg:text-2xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 dark:from-blue-300 dark:via-purple-300 dark:to-pink-300">
                {{ __('shop::front.price_on_request') }}
            </div>
            <button
                onclick="window.location.href='{{ \Modules\Shop\App\Http\Controllers\Front\ShopController::resolveProductUrl($product) }}'"
                class="bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 dark:from-blue-400 dark:via-purple-400 dark:to-pink-400 text-white px-4 md:px-5 lg:px-6 py-2 md:py-2.5 lg:py-3 rounded-xl font-bold hover:shadow-lg hover:scale-105 transition-all text-sm md:text-base lg:text-base">
                <i class="fa-light fa-arrow-right"></i>
            </button>
        </div>
    </div>
</div>

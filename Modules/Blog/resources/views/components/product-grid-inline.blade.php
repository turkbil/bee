@props(['products'])

{{-- Blog içine enjekte edilecek ürün kartları --}}
<div class="blog-product-grid my-12 not-prose">
    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-gray-800 dark:to-gray-700 rounded-2xl p-6 md:p-8 border border-blue-100 dark:border-gray-600">
        <h3 class="text-xl md:text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-3">
            <i class="fas fa-shopping-bag text-blue-600 dark:text-blue-400"></i>
            İlgili Ürünler
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($products as $product)
                @php
                    $locale = app()->getLocale();
                    $title = $product->getTranslated('title', $locale);
                    $shortDesc = $product->getTranslated('short_description', $locale);
                    $slug = $product->getTranslated('slug', $locale);
                    $featuredImage = $product->getFirstMedia('featured_image');

                    // Fiyat bilgisi
                    $hasDiscount = $product->has_discount;
                    $basePrice = $product->base_price;
                    $comparePrice = $product->compare_at_price;
                    $discountPercentage = $product->discount_percentage;
                @endphp

                <a href="{{ url('/shop/product/' . $slug) }}"
                   class="group bg-white dark:bg-gray-800 rounded-xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden hover:-translate-y-1">

                    {{-- Ürün Görseli --}}
                    <div class="relative h-48 overflow-hidden bg-gray-100 dark:bg-gray-700">
                        @if($featuredImage)
                            <img src="{{ thumb($featuredImage, 400, 300, ['quality' => 85, 'format' => 'webp']) }}"
                                 alt="{{ $title }}"
                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                                 loading="lazy">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <i class="fas fa-box text-6xl text-gray-300 dark:text-gray-600"></i>
                            </div>
                        @endif

                        {{-- İndirim Badge --}}
                        @if($hasDiscount)
                            <div class="absolute top-2 right-2 bg-red-500 text-white px-3 py-1 rounded-full text-sm font-bold shadow-lg">
                                %{{ $discountPercentage }} İndirim
                            </div>
                        @endif
                    </div>

                    {{-- Ürün Bilgileri --}}
                    <div class="p-4">
                        <h4 class="font-semibold text-gray-900 dark:text-white mb-2 line-clamp-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                            {{ $title }}
                        </h4>

                        @if($shortDesc)
                            <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2 mb-3">
                                {{ strip_tags($shortDesc) }}
                            </p>
                        @endif

                        {{-- Fiyat --}}
                        <div class="flex items-center justify-between mt-auto">
                            @if($basePrice)
                                <div class="flex items-center gap-2">
                                    @if($hasDiscount)
                                        <span class="text-lg font-bold text-blue-600 dark:text-blue-400">
                                            {{ number_format($basePrice, 2) }} ₺
                                        </span>
                                        <span class="text-sm text-gray-500 line-through">
                                            {{ number_format($comparePrice, 2) }} ₺
                                        </span>
                                    @else
                                        <span class="text-lg font-bold text-gray-900 dark:text-white">
                                            {{ number_format($basePrice, 2) }} ₺
                                        </span>
                                    @endif
                                </div>
                            @else
                                <span class="text-sm font-medium text-blue-600 dark:text-blue-400">
                                    Fiyat için teklif alın
                                </span>
                            @endif

                            <span class="text-sm font-medium text-blue-600 dark:text-blue-400 group-hover:translate-x-1 transition-transform">
                                İncele →
                            </span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</div>

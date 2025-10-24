<div class="w-full bg-white dark:bg-gray-800 rounded-2xl overflow-hidden relative">
    <div class="grid grid-cols-12 gap-0 min-h-[480px]">

        {{-- ========================================== --}}
        {{-- SOL: VISUAL HERO (4/12) --}}
        {{-- ========================================== --}}
        <div class="col-span-4 relative overflow-hidden bg-gradient-to-br {{ $config['gradient'] }} dark:opacity-90">
            {{-- Animated Pattern Background --}}
            <div class="absolute inset-0 opacity-10">
                <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxjaXJjbGUgZmlsbD0iI2ZmZiIgY3g9IjIwIiBjeT0iMjAiIHI9IjMiLz48L2c+PC9zdmc+')] animate-pulse"></div>
            </div>

            <div class="relative z-10 p-8 h-full flex flex-col justify-center">
                {{-- Top: Title & Badge --}}
                <div class="mb-6">
                    @if($category)
                        <div class="inline-block px-3 py-1.5 bg-white/20 backdrop-blur-md rounded-full text-white text-xs font-bold mb-3">
                            {{ strtoupper(is_array($category->title) ? $category->title['tr'] : $category->title) }}
                        </div>
                        <h3 class="text-4xl font-black text-white mb-3 leading-tight">
                            {{ is_array($category->title) ? $category->title['tr'] : $category->title }}<br>{{ $config['title_suffix'] }}
                        </h3>
                        <p class="text-white/90 text-base leading-snug">{{ $config['description'] }}</p>
                    @endif
                </div>

                {{-- Middle: Large Icon Visual --}}
                <div class="flex items-center justify-center py-8">
                    <div class="relative">
                        <div class="absolute inset-0 bg-white/20 blur-3xl rounded-full"></div>
                        <i class="{{ $config['icon'] }} text-[160px] text-white/90 drop-shadow-2xl" style="animation: float 6s ease-in-out infinite;"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- ========================================== --}}
        {{-- ORTA: FEATURED PRODUCT (4/12) --}}
        {{-- ========================================== --}}
        <div class="col-span-4 bg-gradient-to-br from-gray-50 to-indigo-50 dark:from-gray-800 dark:to-indigo-900/20 p-6 flex flex-col">
            @if($featuredProduct)
                <a href="/shop/urun/{{ is_array($featuredProduct->slug) ? $featuredProduct->slug['tr'] : $featuredProduct->slug }}"
                   class="bg-white dark:bg-gray-800 rounded-2xl p-6 transition-all duration-300 h-full flex flex-col group border-2 border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-2 text-xs font-bold text-indigo-600 mb-3">
                        <div class="w-2 h-2 bg-indigo-600 rounded-full animate-pulse"></div>
                        <span>ÖNE ÇIKAN MODEL</span>
                    </div>

                    {{-- Product Image --}}
                    <div class="flex items-center justify-center mb-4 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-2xl p-6 h-48 group-hover:scale-105 transition-transform duration-300">
                        @if($featuredProduct->getFirstMediaUrl('product_images'))
                            <img src="{{ thumb($featuredProduct->getFirstMedia('product_images'), 300, 300, ['quality' => 85, 'scale' => 0]) }}"
                                 alt="{{ is_array($featuredProduct->title) ? $featuredProduct->title['tr'] : $featuredProduct->title }}"
                                 class="w-full h-full object-contain"
                                 loading="lazy">
                        @elseif($category && $category->getFirstMediaUrl('category_icon'))
                            <img src="{{ thumb($category->getFirstMedia('category_icon'), 180, 180, ['quality' => 85, 'scale' => 0]) }}"
                                 alt="{{ is_array($category->title) ? $category->title['tr'] : $category->title }}"
                                 class="w-auto h-32 object-contain opacity-50"
                                 loading="lazy">
                        @else
                            <i class="{{ $config['icon'] }} text-7xl text-indigo-600"></i>
                        @endif
                    </div>

                    {{-- Product Title --}}
                    <h4 class="text-xl font-black text-gray-900 mb-2 group-hover:text-indigo-600 transition-colors">
                        {{ is_array($featuredProduct->title) ? $featuredProduct->title['tr'] : $featuredProduct->title }}
                    </h4>

                    {{-- Product Description (FULL) --}}
                    @if($featuredProduct->short_description)
                        <p class="text-gray-600 mb-4 leading-snug text-sm flex-1">
                            {{ is_array($featuredProduct->short_description) ? $featuredProduct->short_description['tr'] : $featuredProduct->short_description }}
                        </p>
                    @endif

                    {{-- 4'lü Özellik Kutuları --}}
                    @php
                        $specs = [];
                        if($featuredProduct->capacity) $specs[] = ['icon' => 'fa-weight-hanging', 'label' => 'Kapasite', 'value' => $featuredProduct->capacity];
                        if($featuredProduct->battery_type) $specs[] = ['icon' => 'fa-battery-full', 'label' => 'Pil', 'value' => $featuredProduct->battery_type];
                        if($featuredProduct->working_time) $specs[] = ['icon' => 'fa-clock', 'label' => 'Çalışma', 'value' => $featuredProduct->working_time];
                        if($featuredProduct->motor_power) $specs[] = ['icon' => 'fa-bolt', 'label' => 'Motor', 'value' => $featuredProduct->motor_power];
                    @endphp

                    @if(count($specs) > 0)
                        <div class="grid grid-cols-2 gap-2">
                            @foreach(array_slice($specs, 0, 4) as $spec)
                                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg p-2 text-center border border-blue-200">
                                    <i class="fa-solid {{ $spec['icon'] }} text-indigo-600 text-sm mb-0.5"></i>
                                    <p class="text-xs text-gray-500 font-semibold">{{ $spec['label'] }}</p>
                                    <p class="text-xs text-gray-800 font-bold">{{ $spec['value'] }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </a>
            @else
                {{-- No Featured Product --}}
                <div class="bg-white rounded-2xl p-12 text-center shadow-xl">
                    <i class="{{ $config['icon'] }} text-6xl text-gray-300 mb-4"></i>
                    <h4 class="text-xl font-bold text-gray-800 mb-2">Ürün Bulunamadı</h4>
                    <p class="text-gray-600">Bu kategoride henüz öne çıkan ürün bulunmamaktadır.</p>
                </div>
            @endif
        </div>

        {{-- ========================================== --}}
        {{-- SAĞ: PRODUCT LIST (4/12) --}}
        {{-- ========================================== --}}
        <div class="col-span-4 bg-white border-l border-gray-200 p-5 flex flex-col justify-between">
            <div class="flex-1">
                @if($otherProducts->isNotEmpty())
                    {{-- Product Cards (5 ürün) --}}
                    @foreach($otherProducts as $index => $product)
                        <a href="/shop/urun/{{ is_array($product->slug) ? $product->slug['tr'] : $product->slug }}"
                           class="block bg-gray-50 rounded-xl p-3 hover:bg-blue-50 hover:shadow-md transition-all duration-200 border border-gray-200 {{ $index > 0 ? 'mt-2' : '' }}">
                            <div class="flex items-center gap-2.5">
                                <div class="w-11 h-11 bg-gradient-to-br from-{{ ['blue', 'green', 'purple', 'orange', 'pink'][$index % 5] }}-500 to-{{ ['cyan', 'emerald', 'pink', 'red', 'rose'][$index % 5] }}-500 rounded-lg flex items-center justify-center flex-shrink-0 shadow">
                                    @if($product->getFirstMediaUrl('product_images'))
                                        <img src="{{ thumb($product->getFirstMedia('product_images'), 44, 44, ['quality' => 85, 'scale' => 1]) }}"
                                             alt="{{ is_array($product->title) ? $product->title['tr'] : $product->title }}"
                                             class="w-9 h-9 object-contain"
                                             loading="lazy">
                                    @else
                                        <i class="{{ $config['icon'] }} text-white text-base"></i>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h5 class="font-bold text-gray-800 text-xs mb-0.5 truncate">
                                        {{ is_array($product->title) ? $product->title['tr'] : $product->title }}
                                    </h5>
                                    @if($product->short_description)
                                        <p class="text-xs text-gray-500 leading-tight truncate">{{ is_array($product->short_description) ? $product->short_description['tr'] : $product->short_description }}</p>
                                    @endif
                                </div>
                                <div class="text-blue-600 flex-shrink-0">
                                    <i class="fa-solid fa-chevron-right text-xs"></i>
                                </div>
                            </div>
                        </a>
                    @endforeach
                @else
                    <div class="text-center py-12 text-gray-500 text-sm">
                        <i class="{{ $config['icon'] }} text-4xl text-gray-300 mb-3"></i>
                        <p>Diğer ürünler yükleniyor...</p>
                    </div>
                @endif
            </div>

            {{-- View All Link (BÜYÜK BUTON) --}}
            @if($category)
                <div class="mt-4">
                    <a href="/shop/kategori/{{ is_array($category->slug) ? $category->slug['tr'] : $category->slug }}"
                       class="inline-flex items-center justify-center gap-2 w-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-bold px-6 py-3 rounded-xl hover:from-indigo-700 hover:to-purple-700 hover:scale-105 transition-all duration-300 shadow-xl text-sm">
                        <span>Tüm Ürünleri Keşfet</span>
                        <i class="fa-solid fa-arrow-right text-xs"></i>
                    </a>
                </div>
            @endif
        </div>

    </div>

    <style>
    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-20px); }
    }
    </style>
</div>

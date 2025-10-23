@php
    // İstif Makinesi kategorisini ve alt kategorilerini çek
    try {
        $istifCategory = \Modules\Shop\app\Models\ShopCategory::where('slug->tr', 'istif-makinesi')
            ->where('is_active', 1)
            ->first();

        $subCategories = collect();
        $featuredProducts = collect();

        if ($istifCategory) {
            // Alt kategorileri çek
            $subCategories = \Modules\Shop\app\Models\ShopCategory::where('parent_id', $istifCategory->id)
                ->where('is_active', 1)
                ->orderBy('order_column')
                ->take(8)
                ->get();

            // Featured products çek (2 adet)
            $featuredProducts = \Modules\Shop\app\Models\ShopProduct::where('category_id', $istifCategory->id)
                ->where('is_active', 1)
                ->where('is_featured', 1)
                ->take(2)
                ->get();

            // Eğer featured yoksa, en yeni 2 ürünü al
            if ($featuredProducts->isEmpty()) {
                $featuredProducts = \Modules\Shop\app\Models\ShopProduct::where('category_id', $istifCategory->id)
                    ->where('is_active', 1)
                    ->latest()
                    ->take(2)
                    ->get();
            }
        }
    } catch (\Exception $e) {
        $istifCategory = null;
        $subCategories = collect();
        $featuredProducts = collect();
    }

    $colors = [
        ['bg' => 'purple', 'icon' => 'purple'],
        ['bg' => 'violet', 'icon' => 'violet'],
        ['bg' => 'fuchsia', 'icon' => 'fuchsia'],
        ['bg' => 'pink', 'icon' => 'pink'],
        ['bg' => 'rose', 'icon' => 'rose'],
        ['bg' => 'red', 'icon' => 'red'],
        ['bg' => 'orange', 'icon' => 'orange'],
        ['bg' => 'amber', 'icon' => 'amber'],
    ];
@endphp

<div class="w-full">
    {{-- Style 7: Categories (left) + Featured Products (right) Layout --}}
    <div class="grid md:grid-cols-5 gap-0 min-h-[450px] rounded-2xl overflow-hidden shadow-xl">

        {{-- Sol Taraf: Kategoriler veya Fallback (2/5) --}}
        <div class="md:col-span-2 bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/30 dark:to-purple-800/20 p-8 border-r border-purple-200 dark:border-purple-700">
            <div class="mb-6">
                <h3 class="text-2xl font-bold text-gray-800 dark:text-gray-200 flex items-center gap-3 mb-2">
                    <i class="fa-solid fa-box-open-full text-purple-600 dark:text-purple-400"></i>
                    İstif Makinesi Kategorileri
                </h3>
                @if($istifCategory && $istifCategory->description)
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ Str::limit($istifCategory->description, 100) }}</p>
                @endif
            </div>

            @if($subCategories->isNotEmpty())
                {{-- Alt kategoriler varsa göster --}}
                <div class="space-y-2">
                    @foreach($subCategories as $index => $subCat)
                        @php
                            $color = $colors[$index % count($colors)];
                        @endphp
                        <a href="/shop/kategori/{{ is_array($subCat->slug) ? $subCat->slug['tr'] : $subCat->slug }}" class="group flex items-center gap-3 p-3 rounded-xl hover:bg-white dark:hover:bg-purple-900/40 transition-all">
                            <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-{{ $color['bg'] }}-400 to-{{ $color['bg'] }}-600 flex items-center justify-center flex-shrink-0">
                                @if($subCat->getFirstMediaUrl('category_image'))
                                    <img src="{{ $subCat->getFirstMediaUrl('category_image') }}" alt="{{ is_array($subCat->title) ? $subCat->title['tr'] : $subCat->title }}" class="w-8 h-8 object-contain">
                                @else
                                    <i class="fa-solid {{ $subCat->icon_class ?? 'fa-box-open-full' }} text-xl text-white"></i>
                                @endif
                            </div>
                            <div class="flex-1">
                                <h5 class="font-bold text-gray-800 dark:text-gray-200 group-hover:text-{{ $color['bg'] }}-600 dark:group-hover:text-{{ $color['bg'] }}-400 transition">{{ is_array($subCat->title) ? $subCat->title['tr'] : $subCat->title }}</h5>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $subCat->products_count ?? 0 }}+ ürün</p>
                            </div>
                            <i class="fa-solid fa-chevron-right text-gray-400 group-hover:text-{{ $color['bg'] }}-600 dark:group-hover:text-{{ $color['bg'] }}-400 group-hover:translate-x-1 transition-all"></i>
                        </a>
                    @endforeach
                </div>

                <a href="/shop/kategori/istif-makinesi" class="mt-6 flex items-center justify-center gap-2 bg-purple-600 dark:bg-purple-700 text-white px-6 py-3 rounded-xl font-bold hover:bg-purple-700 dark:hover:bg-purple-800 transition">
                    <span>Tüm İstif Makinesi Ürünleri</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
            @else
                {{-- Fallback: Alt kategori yoksa özellikler göster --}}
                <div class="space-y-6">
                    <div class="bg-white/60 dark:bg-purple-900/30 rounded-xl p-4">
                        <h4 class="font-bold text-gray-800 dark:text-gray-200 mb-3 flex items-center gap-2">
                            <i class="fa-solid fa-layer-group text-purple-600 dark:text-purple-400"></i>
                            İstif Makinesi Tipleri
                        </h4>
                        <ul class="space-y-2 text-sm text-gray-700 dark:text-gray-300">
                            <li class="flex items-center gap-2">
                                <i class="fa-solid fa-check-circle text-green-500 text-xs"></i>
                                <span>Manuel İstif Makinesi</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <i class="fa-solid fa-check-circle text-green-500 text-xs"></i>
                                <span>Elektrikli İstif Makinesi</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <i class="fa-solid fa-check-circle text-green-500 text-xs"></i>
                                <span>Reach İstif Makinesi</span>
                            </li>
                        </ul>
                    </div>

                    <div class="bg-white/60 dark:bg-purple-900/30 rounded-xl p-4">
                        <h4 class="font-bold text-gray-800 dark:text-gray-200 mb-3 flex items-center gap-2">
                            <i class="fa-solid fa-ruler-vertical text-purple-600 dark:text-purple-400"></i>
                            Kaldırma Kapasitesi
                        </h4>
                        <ul class="space-y-2 text-sm text-gray-700 dark:text-gray-300">
                            <li class="flex items-center gap-2">
                                <i class="fa-solid fa-check-circle text-green-500 text-xs"></i>
                                <span>1 - 2 Ton / 3m Yükseklik</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <i class="fa-solid fa-check-circle text-green-500 text-xs"></i>
                                <span>2.5 - 3.5 Ton / 5m Yükseklik</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <i class="fa-solid fa-check-circle text-green-500 text-xs"></i>
                                <span>Özel Yükseklik Seçenekleri</span>
                            </li>
                        </ul>
                    </div>

                    <a href="/shop/kategori/istif-makinesi" class="flex items-center justify-center gap-2 bg-purple-600 dark:bg-purple-700 text-white px-6 py-3 rounded-xl font-bold hover:bg-purple-700 dark:hover:bg-purple-800 transition">
                        <span>Tüm İstif Makinesi Ürünleri</span>
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            @endif
        </div>

        {{-- Sağ Taraf: Featured Products (3/5) --}}
        <div class="md:col-span-3 bg-gradient-to-br from-purple-50/50 to-fuchsia-50/50 dark:from-gray-900/40 dark:to-purple-900/20 p-8">
            <div class="mb-6">
                <h3 class="text-2xl font-bold text-gray-800 dark:text-gray-200 flex items-center gap-3 mb-2">
                    <i class="fa-solid fa-star text-yellow-500"></i>
                    Öne Çıkan Ürünler
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">Popüler ve en çok tercih edilen istif makinesi modelleri</p>
            </div>

            @if($featuredProducts->isNotEmpty())
                <div class="grid gap-6">
                    @foreach($featuredProducts as $product)
                        <a href="/shop/urun/{{ $product->slug }}" class="group bg-white dark:bg-gray-800 rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all">
                            <div class="grid md:grid-cols-5 gap-4">
                                {{-- Ürün Görseli --}}
                                <div class="md:col-span-2 bg-gradient-to-br from-purple-100 to-purple-200 dark:from-purple-900/50 dark:to-purple-800/30 p-6 flex items-center justify-center">
                                    @if($product->getFirstMediaUrl('product_images'))
                                        <img src="{{ thumb($product->getFirstMedia('product_images'), 300, 300, ['quality' => 85, 'scale' => 1]) }}"
                                             alt="{{ $product->name }}"
                                             class="w-full h-40 object-contain group-hover:scale-110 transition-transform"
                                             loading="lazy">
                                    @else
                                        <i class="fa-solid fa-box-open-full text-7xl text-purple-400 dark:text-purple-500 group-hover:scale-110 transition-transform"></i>
                                    @endif
                                </div>

                                {{-- Ürün Bilgileri --}}
                                <div class="md:col-span-3 p-6 flex flex-col justify-between">
                                    <div>
                                        <h4 class="text-xl font-bold text-gray-800 dark:text-gray-200 mb-2 group-hover:text-purple-600 dark:group-hover:text-purple-400 transition">{{ $product->name }}</h4>
                                        @if($product->short_description)
                                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">{{ Str::limit($product->short_description, 100) }}</p>
                                        @endif
                                    </div>

                                    <div class="flex items-center justify-between">
                                        @if($product->price)
                                            <div>
                                                <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                                                    {{ number_format($product->price, 2) }} ₺
                                                </p>
                                                @if($product->old_price)
                                                    <p class="text-sm text-gray-500 line-through">{{ number_format($product->old_price, 2) }} ₺</p>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-purple-600 dark:text-purple-400 font-bold">Fiyat İçin İletişime Geçin</span>
                                        @endif

                                        <div class="flex items-center gap-2 text-purple-600 dark:text-purple-400 font-semibold">
                                            <span>İncele</span>
                                            <i class="fa-solid fa-arrow-right group-hover:translate-x-2 transition-transform"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                {{-- Featured ürün yoksa placeholder --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-12 text-center">
                    <i class="fa-solid fa-box-open text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
                    <h4 class="text-xl font-bold text-gray-800 dark:text-gray-200 mb-2">Öne Çıkan Ürünler Yakında</h4>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">En iyi istif makinesi modellerimizi sizin için hazırlıyoruz</p>
                    <a href="/shop/kategori/istif-makinesi" class="inline-flex items-center gap-2 bg-purple-600 text-white px-6 py-3 rounded-xl font-bold hover:bg-purple-700 transition">
                        <span>Tüm Ürünlere Göz At</span>
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            @endif
        </div>

    </div>
</div>

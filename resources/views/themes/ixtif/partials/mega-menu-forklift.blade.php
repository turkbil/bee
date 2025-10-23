@php
    // Forklift kategorisini ve alt kategorilerini çek
    try {
        $forkliftCategory = \Modules\Shop\app\Models\ShopCategory::where('slug->tr', 'forklift')
            ->where('is_active', 1)
            ->first();

        $subCategories = collect();
        $featuredProducts = collect();

        if ($forkliftCategory) {
            // Alt kategorileri çek
            $subCategories = \Modules\Shop\app\Models\ShopCategory::where('parent_id', $forkliftCategory->id)
                ->where('is_active', 1)
                ->orderBy('order_column')
                ->take(8)
                ->get();

            // Featured products çek (2 adet)
            $featuredProducts = \Modules\Shop\app\Models\ShopProduct::where('category_id', $forkliftCategory->id)
                ->where('is_active', 1)
                ->where('is_featured', 1)
                ->take(2)
                ->get();

            // Eğer featured yoksa, en yeni 2 ürünü al
            if ($featuredProducts->isEmpty()) {
                $featuredProducts = \Modules\Shop\app\Models\ShopProduct::where('category_id', $forkliftCategory->id)
                    ->where('is_active', 1)
                    ->latest()
                    ->take(2)
                    ->get();
            }
        }
    } catch (\Exception $e) {
        $forkliftCategory = null;
        $subCategories = collect();
        $featuredProducts = collect();
    }

    $colors = [
        ['bg' => 'blue', 'icon' => 'blue'],
        ['bg' => 'green', 'icon' => 'green'],
        ['bg' => 'purple', 'icon' => 'purple'],
        ['bg' => 'orange', 'icon' => 'orange'],
        ['bg' => 'red', 'icon' => 'red'],
        ['bg' => 'pink', 'icon' => 'pink'],
        ['bg' => 'indigo', 'icon' => 'indigo'],
        ['bg' => 'teal', 'icon' => 'teal'],
    ];
@endphp

<div class="w-full">
    {{-- Style 7: Categories (left) + Featured Products (right) Layout --}}
    <div class="grid md:grid-cols-5 gap-0 min-h-[450px] rounded-2xl overflow-hidden shadow-xl">

        {{-- Sol Taraf: Kategoriler veya Fallback (2/5) --}}
        <div class="md:col-span-2 bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/30 dark:to-blue-800/20 p-8 border-r border-blue-200 dark:border-blue-700">
            <div class="mb-6">
                <h3 class="text-2xl font-bold text-gray-800 dark:text-gray-200 flex items-center gap-3 mb-2">
                    <i class="fa-solid fa-forklift text-blue-600 dark:text-blue-400"></i>
                    Forklift Kategorileri
                </h3>
                @if($forkliftCategory && $forkliftCategory->description)
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ Str::limit($forkliftCategory->description, 100) }}</p>
                @endif
            </div>

            @if($subCategories->isNotEmpty())
                {{-- Alt kategoriler varsa göster --}}
                <div class="space-y-2">
                    @foreach($subCategories as $index => $subCat)
                        @php
                            $color = $colors[$index % count($colors)];
                        @endphp
                        <a href="/shop/kategori/{{ is_array($subCat->slug) ? $subCat->slug['tr'] : $subCat->slug }}" class="group flex items-center gap-3 p-3 rounded-xl hover:bg-white dark:hover:bg-blue-900/40 transition-all">
                            <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-{{ $color['bg'] }}-400 to-{{ $color['bg'] }}-600 flex items-center justify-center flex-shrink-0">
                                @if($subCat->getFirstMediaUrl('category_image'))
                                    <img src="{{ $subCat->getFirstMediaUrl('category_image') }}" alt="{{ is_array($subCat->title) ? $subCat->title['tr'] : $subCat->title }}" class="w-8 h-8 object-contain">
                                @else
                                    <i class="fa-solid {{ $subCat->icon_class ?? 'fa-forklift' }} text-xl text-white"></i>
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

                <a href="/shop/kategori/forklift" class="mt-6 flex items-center justify-center gap-2 bg-blue-600 dark:bg-blue-700 text-white px-6 py-3 rounded-xl font-bold hover:bg-blue-700 dark:hover:bg-blue-800 transition">
                    <span>Tüm Forklift Ürünleri</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
            @else
                {{-- Fallback: Alt kategori yoksa özellikler göster --}}
                <div class="space-y-6">
                    <div class="bg-white/60 dark:bg-blue-900/30 rounded-xl p-4">
                        <h4 class="font-bold text-gray-800 dark:text-gray-200 mb-3 flex items-center gap-2">
                            <i class="fa-solid fa-bolt text-blue-600 dark:text-blue-400"></i>
                            Yakıt Tipleri
                        </h4>
                        <ul class="space-y-2 text-sm text-gray-700 dark:text-gray-300">
                            <li class="flex items-center gap-2">
                                <i class="fa-solid fa-check-circle text-green-500 text-xs"></i>
                                <span>Elektrikli Forklift</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <i class="fa-solid fa-check-circle text-green-500 text-xs"></i>
                                <span>Dizel Forklift</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <i class="fa-solid fa-check-circle text-green-500 text-xs"></i>
                                <span>LPG Forklift</span>
                            </li>
                        </ul>
                    </div>

                    <div class="bg-white/60 dark:bg-blue-900/30 rounded-xl p-4">
                        <h4 class="font-bold text-gray-800 dark:text-gray-200 mb-3 flex items-center gap-2">
                            <i class="fa-solid fa-weight-hanging text-blue-600 dark:text-blue-400"></i>
                            Kapasite Seçenekleri
                        </h4>
                        <ul class="space-y-2 text-sm text-gray-700 dark:text-gray-300">
                            <li class="flex items-center gap-2">
                                <i class="fa-solid fa-check-circle text-green-500 text-xs"></i>
                                <span>1.5 - 3.5 Ton</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <i class="fa-solid fa-check-circle text-green-500 text-xs"></i>
                                <span>4 - 8 Ton</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <i class="fa-solid fa-check-circle text-green-500 text-xs"></i>
                                <span>10+ Ton</span>
                            </li>
                        </ul>
                    </div>

                    <a href="/shop/kategori/forklift" class="flex items-center justify-center gap-2 bg-blue-600 dark:bg-blue-700 text-white px-6 py-3 rounded-xl font-bold hover:bg-blue-700 dark:hover:bg-blue-800 transition">
                        <span>Tüm Forklift Ürünleri</span>
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            @endif
        </div>

        {{-- Sağ Taraf: Featured Products (3/5) --}}
        <div class="md:col-span-3 bg-gradient-to-br from-blue-50/50 to-purple-50/50 dark:from-gray-900/40 dark:to-blue-900/20 p-8">
            <div class="mb-6">
                <h3 class="text-2xl font-bold text-gray-800 dark:text-gray-200 flex items-center gap-3 mb-2">
                    <i class="fa-solid fa-star text-yellow-500"></i>
                    Öne Çıkan Ürünler
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">Popüler ve en çok tercih edilen forklift modelleri</p>
            </div>

            @if($featuredProducts->isNotEmpty())
                <div class="grid gap-6">
                    @foreach($featuredProducts as $product)
                        <a href="/shop/urun/{{ $product->slug }}" class="group bg-white dark:bg-gray-800 rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all">
                            <div class="grid md:grid-cols-5 gap-4">
                                {{-- Ürün Görseli --}}
                                <div class="md:col-span-2 bg-gradient-to-br from-blue-100 to-blue-200 dark:from-blue-900/50 dark:to-blue-800/30 p-6 flex items-center justify-center">
                                    @if($product->getFirstMediaUrl('product_images'))
                                        <img src="{{ thumb($product->getFirstMedia('product_images'), 300, 300, ['quality' => 85, 'scale' => 1]) }}"
                                             alt="{{ $product->name }}"
                                             class="w-full h-40 object-contain group-hover:scale-110 transition-transform"
                                             loading="lazy">
                                    @else
                                        <i class="fa-solid fa-forklift text-7xl text-blue-400 dark:text-blue-500 group-hover:scale-110 transition-transform"></i>
                                    @endif
                                </div>

                                {{-- Ürün Bilgileri --}}
                                <div class="md:col-span-3 p-6 flex flex-col justify-between">
                                    <div>
                                        <h4 class="text-xl font-bold text-gray-800 dark:text-gray-200 mb-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition">{{ $product->name }}</h4>
                                        @if($product->short_description)
                                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">{{ Str::limit($product->short_description, 100) }}</p>
                                        @endif
                                    </div>

                                    <div class="flex items-center justify-between">
                                        @if($product->price)
                                            <div>
                                                <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                                                    {{ number_format($product->price, 2) }} ₺
                                                </p>
                                                @if($product->old_price)
                                                    <p class="text-sm text-gray-500 line-through">{{ number_format($product->old_price, 2) }} ₺</p>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-blue-600 dark:text-blue-400 font-bold">Fiyat İçin İletişime Geçin</span>
                                        @endif

                                        <div class="flex items-center gap-2 text-blue-600 dark:text-blue-400 font-semibold">
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
                    <p class="text-gray-600 dark:text-gray-400 mb-6">En iyi forklift modellerimizi sizin için hazırlıyoruz</p>
                    <a href="/shop/kategori/forklift" class="inline-flex items-center gap-2 bg-blue-600 text-white px-6 py-3 rounded-xl font-bold hover:bg-blue-700 transition">
                        <span>Tüm Ürünlere Göz At</span>
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            @endif
        </div>

    </div>
</div>

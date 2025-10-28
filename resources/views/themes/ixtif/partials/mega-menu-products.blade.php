{{-- Ürünler Mega Menu - Split Layout with Tabs --}}
@php
use Modules\Shop\app\Models\ShopCategory;
use Modules\Shop\app\Models\ShopProduct;

// Ana kategorileri çek
$mainCategories = ShopCategory::where('is_active', 1)
    ->whereNull('parent_id')
    ->orderBy('sort_order', 'asc')
    ->get();

// Her kategori için ürünleri hazırla
$categoryData = [];
foreach ($mainCategories as $cat) {
    $catId = $cat->category_id;
    $catTitle = is_array($cat->title) ? ($cat->title['tr'] ?? $cat->title['en'] ?? '') : $cat->title;
    $catSlug = is_array($cat->slug) ? ($cat->slug['tr'] ?? $cat->slug['en'] ?? '') : $cat->slug;
    $catIcon = $cat->icon_class ?? 'fa-solid fa-box';

    // Yedek Parça kategorisiyse alt kategorilerini çek
    if ($catId == 7) {
        $subcategories = ShopCategory::where('is_active', 1)
            ->where('parent_id', $catId)
            ->orderBy('sort_order', 'asc')
            ->get();

        $categoryData[$catId] = [
            'id' => $catId,
            'title' => $catTitle,
            'slug' => $catSlug,
            'icon' => $catIcon,
            'type' => 'subcategories',
            'subcategories' => $subcategories,
        ];
    } else {
        // Diğer kategoriler: Öne çıkan ürün + diğer ürünler
        $featuredProduct = ShopProduct::where('category_id', $catId)
            ->where('is_active', 1)
            ->whereNull('parent_product_id')
            ->orderBy('sort_order', 'asc')
            ->first();

        $otherProducts = ShopProduct::where('category_id', $catId)
            ->where('is_active', 1)
            ->whereNull('parent_product_id')
            ->where('product_id', '!=', $featuredProduct ? $featuredProduct->product_id : 0)
            ->orderBy('sort_order', 'asc')
            ->take(5)
            ->get();

        $categoryData[$catId] = [
            'id' => $catId,
            'title' => $catTitle,
            'slug' => $catSlug,
            'icon' => $catIcon,
            'type' => 'products',
            'featured' => $featuredProduct,
            'products' => $otherProducts,
            'category' => $cat,
        ];
    }
}

// Renk gradientleri (kategorilere özel)
$gradients = [
    1 => 'from-orange-500 to-red-600',      // Forklift
    2 => 'from-blue-500 to-indigo-600',     // Transpalet
    3 => 'from-green-500 to-emerald-600',   // İstif Makinesi
    4 => 'from-purple-500 to-pink-600',     // Order Picker
    5 => 'from-cyan-500 to-teal-600',       // Otonom Sistemler
    6 => 'from-yellow-500 to-orange-600',   // Reach Truck
    7 => 'from-slate-600 to-gray-800',      // Yedek Parça
];

// Alt kategori ikonları
$subcategoryIcons = [
    'lastik-jant-teker' => 'fa-solid fa-tire',
    'motor-grubu' => 'fa-solid fa-engine',
    'catal-atasman' => 'fa-solid fa-grip-lines-vertical',
    'sanziman-parcalari' => 'fa-solid fa-gears',
    'keceler' => 'fa-solid fa-circle',
    'direksiyon-grubu' => 'fa-solid fa-steering-wheel',
    'asansor-grubu' => 'fa-solid fa-elevator',
    'rulman-grubu' => 'fa-solid fa-circle-dot',
    'elektrik-elektronik-aku' => 'fa-solid fa-battery-full',
    'forklift-dingil-parcalari' => 'fa-solid fa-axle',
    'forklift-aksesuarlari' => 'fa-solid fa-toolbox',
    'pompalar' => 'fa-solid fa-pump',
    'fren-grubu' => 'fa-solid fa-brake-warning',
    'filtreler' => 'fa-solid fa-filter',
];
@endphp

<div class="w-full rounded-2xl overflow-hidden relative border border-gray-300 dark:border-gray-700 shadow-lg bg-white dark:bg-gray-800"
     x-data="{ activeTab: {{ $mainCategories->first()->category_id ?? 1 }} }">

    <div class="grid grid-cols-12 min-h-[500px]">

        {{-- ========================================== --}}
        {{-- SOL: ANA KATEGORİLER (TABS) --}}
        {{-- ========================================== --}}
        <div class="col-span-4 bg-gradient-to-br from-slate-50 to-gray-100 dark:from-slate-900 dark:to-gray-800 p-6 border-r border-gray-200 dark:border-gray-700">
            <h3 class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase mb-4 flex items-center gap-2">
                <i class="fa-solid fa-layer-group"></i>
                Ana Kategoriler
            </h3>

            <div class="space-y-2">
                @foreach($mainCategories as $cat)
                    @php
                        $catId = $cat->category_id;
                        $catTitle = is_array($cat->title) ? ($cat->title['tr'] ?? $cat->title['en'] ?? '') : $cat->title;
                        $catSlug = is_array($cat->slug) ? ($cat->slug['tr'] ?? $cat->slug['en'] ?? '') : $cat->slug;
                        $catIcon = $cat->icon_class ?? 'fa-solid fa-box';
                        $gradient = $gradients[$catId] ?? 'from-blue-500 to-indigo-600';
                    @endphp

                    <a href="/shop/kategori/{{ $catSlug }}"
                       @mouseenter="activeTab = {{ $catId }}"
                       :class="activeTab === {{ $catId }} ? 'bg-white dark:bg-gray-700 shadow-md border-{{ explode('-', explode(' ', $gradient)[0])[1] }}-300 dark:border-{{ explode('-', explode(' ', $gradient)[0])[1] }}-500' : 'border-transparent hover:bg-white/50 dark:hover:bg-gray-700/50'"
                       class="group w-full flex items-center gap-3 p-3 rounded-xl transition-all duration-200 border">
                        <div class="w-10 h-10 bg-gradient-to-br {{ $gradient }} rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="{{ $catIcon }} text-white"></i>
                        </div>
                        <div class="flex-1 text-left">
                            <p class="font-bold text-gray-900 dark:text-white text-sm">{{ $catTitle }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                @if($catId == 7)
                                    {{ count($categoryData[$catId]['subcategories'] ?? []) }} Kategori
                                @else
                                    {{ ($categoryData[$catId]['products']->count() ?? 0) + ($categoryData[$catId]['featured'] ? 1 : 0) }} Ürün
                                @endif
                            </p>
                        </div>
                        <i class="fa-solid fa-chevron-right text-gray-400 dark:text-gray-500 text-xs group-hover:text-{{ explode('-', explode(' ', $gradient)[0])[1] }}-500 transition"
                           :class="activeTab === {{ $catId }} ? 'text-{{ explode('-', explode(' ', $gradient)[0])[1] }}-500' : ''"></i>
                    </a>
                @endforeach
            </div>
        </div>

        {{-- ========================================== --}}
        {{-- SAĞ: DİNAMİK İÇERİK --}}
        {{-- ========================================== --}}
        <div class="col-span-8 bg-white dark:bg-gray-800">
            @foreach($categoryData as $catId => $data)
                <div x-show="activeTab === {{ $catId }}"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-x-4"
                     x-transition:enter-end="opacity-100 translate-x-0"
                     class="p-6 h-full"
                     x-cloak>

                    @if($data['type'] === 'subcategories')
                        {{-- YEDEK PARÇA: ALT KATEGORİLER --}}
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                <i class="fa-solid fa-wrench text-slate-600 dark:text-slate-400"></i>
                                {{ $data['title'] }} Kategorileri
                            </h3>
                            <a href="/shop/kategori/{{ $data['slug'] }}"
                               class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-semibold">
                                Tümünü Gör →
                            </a>
                        </div>

                        <div class="grid grid-cols-3 gap-3 max-h-[420px] overflow-y-auto pr-2">
                            @foreach($data['subcategories'] as $index => $subcat)
                                @php
                                    $subTitle = is_array($subcat->title) ? ($subcat->title['tr'] ?? '') : $subcat->title;
                                    $subSlug = is_array($subcat->slug) ? ($subcat->slug['tr'] ?? '') : $subcat->slug;
                                    $subIcon = $subcategoryIcons[$subSlug] ?? 'fa-solid fa-box';
                                    $colors = ['blue', 'green', 'orange', 'purple', 'cyan', 'pink', 'red', 'indigo', 'teal', 'yellow', 'emerald', 'rose', 'violet', 'amber'];
                                    $color = $colors[$index % count($colors)];
                                @endphp

                                <a href="/shop/kategori/{{ $subSlug }}"
                                   class="block p-4 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-{{ $color }}-300 dark:hover:border-{{ $color }}-500 hover:shadow-md transition-all bg-white dark:bg-gray-700">
                                    <div class="flex items-center gap-3 mb-2">
                                        <i class="{{ $subIcon }} text-2xl text-{{ $color }}-600 dark:text-{{ $color }}-400"></i>
                                        <p class="font-bold text-sm text-gray-900 dark:text-white leading-tight">{{ $subTitle }}</p>
                                    </div>
                                </a>
                            @endforeach
                        </div>

                    @else
                        {{-- DİĞER KATEGORİLER: FEATURED PRODUCT + PRODUCT LIST --}}
                        <div class="grid grid-cols-2 gap-4 h-full">

                            {{-- Öne Çıkan Ürün --}}
                            <div class="flex flex-col">
                                @if($data['featured'])
                                    @php
                                        $product = $data['featured'];
                                        $pTitle = is_array($product->title) ? ($product->title['tr'] ?? '') : $product->title;
                                        $pSlug = is_array($product->slug) ? ($product->slug['tr'] ?? '') : $product->slug;
                                        $pDesc = is_array($product->short_description ?? '') ? ($product->short_description['tr'] ?? '') : ($product->short_description ?? '');
                                    @endphp

                                    <a href="/shop/{{ $pSlug }}"
                                       class="bg-gray-50 dark:bg-gray-700 rounded-2xl p-5 border border-gray-200 dark:border-gray-600 hover:border-indigo-400 dark:hover:border-indigo-500 transition-all duration-300 flex flex-col group h-full">

                                        {{-- Product Image --}}
                                        @if($product->hasMedia('featured_image'))
                                            <div class="flex items-center justify-center mb-4 bg-white dark:bg-gray-800 rounded-2xl p-6 h-48 group-hover:scale-105 transition-transform duration-300">
                                                <img src="{{ thumb($product->getFirstMedia('featured_image'), 300, 300, ['quality' => 85, 'scale' => 0, 'format' => 'webp']) }}"
                                                     alt="{{ $pTitle }}"
                                                     class="w-full h-full object-contain"
                                                     loading="lazy">
                                            </div>
                                        @else
                                            <div class="flex items-center justify-center mb-4 bg-gradient-to-br from-indigo-100 to-purple-100 dark:from-indigo-900/30 dark:to-purple-900/30 rounded-2xl p-6 h-48 group-hover:scale-105 transition-transform duration-300">
                                                <i class="{{ $data['icon'] }} text-7xl text-indigo-600 dark:text-indigo-400"></i>
                                            </div>
                                        @endif

                                        <h4 class="text-xl font-black text-gray-900 dark:text-white mb-2 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                            {{ $pTitle }}
                                        </h4>

                                        @if($pDesc)
                                            <p class="text-gray-600 dark:text-gray-400 mb-4 leading-snug text-sm flex-1 line-clamp-3">
                                                {{ $pDesc }}
                                            </p>
                                        @endif
                                    </a>
                                @else
                                    <div class="p-12 text-center bg-gray-50 dark:bg-gray-700 rounded-2xl">
                                        <i class="{{ $data['icon'] }} text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
                                        <p class="text-gray-600 dark:text-gray-400">Henüz ürün eklenmedi</p>
                                    </div>
                                @endif
                            </div>

                            {{-- Diğer Ürünler --}}
                            <div class="flex flex-col justify-between">
                                <div class="flex-1 overflow-y-auto pr-2 space-y-2 max-h-[420px]">
                                    @if($data['products']->isNotEmpty())
                                        @foreach($data['products'] as $index => $product)
                                            @php
                                                $pTitle = is_array($product->title) ? ($product->title['tr'] ?? '') : $product->title;
                                                $pSlug = is_array($product->slug) ? ($product->slug['tr'] ?? '') : $product->slug;
                                                $pDesc = is_array($product->short_description ?? '') ? ($product->short_description['tr'] ?? '') : ($product->short_description ?? '');
                                            @endphp

                                            <a href="/shop/{{ $pSlug }}"
                                               class="block bg-gray-50 dark:bg-gray-700 rounded-xl p-3 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all duration-200 border border-gray-200 dark:border-gray-600">
                                                <div class="flex items-center gap-2.5">
                                                    @if($product->hasMedia('featured_image'))
                                                        <div class="w-11 h-11 bg-white dark:bg-gray-800 rounded-lg flex items-center justify-center flex-shrink-0 p-1 border border-gray-200 dark:border-gray-600">
                                                            <img src="{{ thumb($product->getFirstMedia('featured_image'), 44, 44, ['quality' => 85, 'scale' => 0, 'format' => 'webp']) }}"
                                                                 alt="{{ $pTitle }}"
                                                                 class="w-full h-full object-contain"
                                                                 loading="lazy">
                                                        </div>
                                                    @else
                                                        @php
                                                            $colors = ['blue', 'green', 'purple', 'orange', 'pink'];
                                                            $color1 = $colors[$index % 5];
                                                            $colors2 = ['cyan', 'emerald', 'pink', 'red', 'rose'];
                                                            $color2 = $colors2[$index % 5];
                                                        @endphp
                                                        <div class="w-11 h-11 bg-gradient-to-br from-{{ $color1 }}-500 to-{{ $color2 }}-500 rounded-lg flex items-center justify-center flex-shrink-0">
                                                            <i class="{{ $data['icon'] }} text-white text-base"></i>
                                                        </div>
                                                    @endif
                                                    <div class="flex-1 min-w-0">
                                                        <h5 class="font-bold text-gray-800 dark:text-gray-200 text-xs mb-0.5 truncate">
                                                            {{ $pTitle }}
                                                        </h5>
                                                        @if($pDesc)
                                                            <p class="text-xs text-gray-500 dark:text-gray-400 leading-tight truncate">{{ $pDesc }}</p>
                                                        @endif
                                                    </div>
                                                    <div class="text-blue-600 dark:text-blue-400 flex-shrink-0">
                                                        <i class="fa-solid fa-chevron-right text-xs"></i>
                                                    </div>
                                                </div>
                                            </a>
                                        @endforeach
                                    @else
                                        <div class="text-center py-12 text-gray-500 dark:text-gray-400 text-sm">
                                            <i class="{{ $data['icon'] }} text-4xl text-gray-300 dark:text-gray-600 mb-3"></i>
                                            <p>Diğer ürünler yükleniyor...</p>
                                        </div>
                                    @endif
                                </div>

                                {{-- Tümünü Gör Butonu --}}
                                <div class="mt-4">
                                    <a href="/shop/kategori/{{ $data['slug'] }}"
                                       class="inline-flex items-center justify-center gap-2 w-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-bold px-6 py-3 rounded-xl hover:from-indigo-700 hover:to-purple-700 hover:scale-105 transition-all duration-300 text-sm">
                                        <span>Tüm Ürünleri Keşfet</span>
                                        <i class="fa-solid fa-arrow-right text-xs"></i>
                                    </a>
                                </div>
                            </div>

                        </div>
                    @endif

                </div>
            @endforeach
        </div>

    </div>
</div>

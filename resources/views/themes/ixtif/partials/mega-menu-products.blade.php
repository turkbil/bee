{{-- Ürünler Mega Menu - Split Layout with Tabs --}}
@php
use Modules\Shop\app\Models\ShopCategory;
use Modules\Shop\app\Models\ShopProduct;

// Tüm ana kategorileri çek (Tüm Kategoriler tab'ı için)
$allMainCategories = ShopCategory::where('is_active', 1)
    ->whereNull('parent_id')
    ->orderBy('sort_order', 'asc')
    ->get();

// Sol tab'lar için kategoriler (Reach Truck=6, Otonom=5, Order Picker=4 hariç)
$mainCategories = ShopCategory::where('is_active', 1)
    ->whereNull('parent_id')
    ->whereNotIn('category_id', [4, 5, 6])
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
        // Diğer kategoriler: İlk 4 ürünü çek (4 yatay kart)
        $products = ShopProduct::where('category_id', $catId)
            ->where('is_active', 1)
            ->whereNull('parent_product_id')
            ->orderBy('sort_order', 'asc')
            ->take(4)
            ->get();

        $categoryData[$catId] = [
            'id' => $catId,
            'title' => $catTitle,
            'slug' => $catSlug,
            'icon' => $catIcon,
            'type' => 'products',
            'products' => $products,
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

// Border renkleri (sabit - Tailwind uyumlu)
$borderColors = [
    1 => ['normal' => 'border-orange-300 dark:border-orange-500', 'hover' => 'group-hover:text-orange-500', 'featured' => 'hover:border-orange-400 dark:hover:border-orange-500', 'product' => 'hover:border-orange-400 dark:hover:border-orange-500 hover:bg-orange-50 dark:hover:bg-orange-900/20', 'button' => 'from-orange-600 to-red-600 hover:from-orange-700 hover:to-red-700'],  // Forklift
    2 => ['normal' => 'border-blue-300 dark:border-blue-500', 'hover' => 'group-hover:text-blue-500', 'featured' => 'hover:border-blue-400 dark:hover:border-blue-500', 'product' => 'hover:border-blue-400 dark:hover:border-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/20', 'button' => 'from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700'],        // Transpalet
    3 => ['normal' => 'border-green-300 dark:border-green-500', 'hover' => 'group-hover:text-green-500', 'featured' => 'hover:border-green-400 dark:hover:border-green-500', 'product' => 'hover:border-green-400 dark:hover:border-green-500 hover:bg-green-50 dark:hover:bg-green-900/20', 'button' => 'from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700'],     // İstif
    4 => ['normal' => 'border-purple-300 dark:border-purple-500', 'hover' => 'group-hover:text-purple-500', 'featured' => 'hover:border-purple-400 dark:hover:border-purple-500', 'product' => 'hover:border-purple-400 dark:hover:border-purple-500 hover:bg-purple-50 dark:hover:bg-purple-900/20', 'button' => 'from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700'],  // Order Picker
    5 => ['normal' => 'border-cyan-300 dark:border-cyan-500', 'hover' => 'group-hover:text-cyan-500', 'featured' => 'hover:border-cyan-400 dark:hover:border-cyan-500', 'product' => 'hover:border-cyan-400 dark:hover:border-cyan-500 hover:bg-cyan-50 dark:hover:bg-cyan-900/20', 'button' => 'from-cyan-600 to-teal-600 hover:from-cyan-700 hover:to-teal-700'],        // Otonom
    6 => ['normal' => 'border-yellow-300 dark:border-yellow-500', 'hover' => 'group-hover:text-yellow-500', 'featured' => 'hover:border-yellow-400 dark:hover:border-yellow-500', 'product' => 'hover:border-yellow-400 dark:hover:border-yellow-500 hover:bg-yellow-50 dark:hover:bg-yellow-900/20', 'button' => 'from-yellow-600 to-orange-600 hover:from-yellow-700 hover:to-orange-700'],  // Reach Truck
    7 => ['normal' => 'border-slate-300 dark:border-slate-500', 'hover' => 'group-hover:text-slate-500', 'featured' => 'hover:border-slate-400 dark:hover:border-slate-500', 'product' => 'hover:border-slate-400 dark:hover:border-slate-500 hover:bg-slate-50 dark:hover:bg-slate-900/20', 'button' => 'from-slate-600 to-gray-700 hover:from-slate-700 hover:to-gray-800'],     // Yedek Parça
];

// Alt kategori renkleri (sabit)
$subcategoryColors = [
    'lastik-jant-teker' => ['icon' => 'fa-solid fa-tire', 'border' => 'hover:border-blue-400 dark:hover:border-blue-500', 'text' => 'text-blue-600 dark:text-blue-400'],
    'motor-grubu' => ['icon' => 'fa-solid fa-engine', 'border' => 'hover:border-green-400 dark:hover:border-green-500', 'text' => 'text-green-600 dark:text-green-400'],
    'catal-atasman' => ['icon' => 'fa-solid fa-grip-lines-vertical', 'border' => 'hover:border-orange-400 dark:hover:border-orange-500', 'text' => 'text-orange-600 dark:text-orange-400'],
    'sanziman-parcalari' => ['icon' => 'fa-solid fa-gears', 'border' => 'hover:border-purple-400 dark:hover:border-purple-500', 'text' => 'text-purple-600 dark:text-purple-400'],
    'keceler' => ['icon' => 'fa-solid fa-circle', 'border' => 'hover:border-cyan-400 dark:hover:border-cyan-500', 'text' => 'text-cyan-600 dark:text-cyan-400'],
    'direksiyon-grubu' => ['icon' => 'fa-solid fa-steering-wheel', 'border' => 'hover:border-pink-400 dark:hover:border-pink-500', 'text' => 'text-pink-600 dark:text-pink-400'],
    'asansor-grubu' => ['icon' => 'fa-solid fa-elevator', 'border' => 'hover:border-red-400 dark:hover:border-red-500', 'text' => 'text-red-600 dark:text-red-400'],
    'rulman-grubu' => ['icon' => 'fa-solid fa-circle-dot', 'border' => 'hover:border-indigo-400 dark:hover:border-indigo-500', 'text' => 'text-indigo-600 dark:text-indigo-400'],
    'elektrik-elektronik-aku' => ['icon' => 'fa-solid fa-battery-full', 'border' => 'hover:border-yellow-400 dark:hover:border-yellow-500', 'text' => 'text-yellow-600 dark:text-yellow-400'],
    'forklift-dingil-parcalari' => ['icon' => 'fa-solid fa-gear', 'border' => 'hover:border-teal-400 dark:hover:border-teal-500', 'text' => 'text-teal-600 dark:text-teal-400'],
    'forklift-aksesuarlari' => ['icon' => 'fa-solid fa-toolbox', 'border' => 'hover:border-emerald-400 dark:hover:border-emerald-500', 'text' => 'text-emerald-600 dark:text-emerald-400'],
    'pompalar' => ['icon' => 'fa-solid fa-pump', 'border' => 'hover:border-rose-400 dark:hover:border-rose-500', 'text' => 'text-rose-600 dark:text-rose-400'],
    'fren-grubu' => ['icon' => 'fa-solid fa-brake-warning', 'border' => 'hover:border-violet-400 dark:hover:border-violet-500', 'text' => 'text-violet-600 dark:text-violet-400'],
    'filtreler' => ['icon' => 'fa-solid fa-filter', 'border' => 'hover:border-amber-400 dark:hover:border-amber-500', 'text' => 'text-amber-600 dark:text-amber-400'],
];
@endphp

<div class="w-full rounded-2xl overflow-hidden relative border border-gray-300 dark:border-gray-700 shadow-lg bg-white dark:bg-gray-800"
     x-data="{ activeTab: 0 }">

    <div class="grid grid-cols-12 min-h-[400px]">

        {{-- ========================================== --}}
        {{-- SOL: ANA KATEGORİLER (TABS) --}}
        {{-- ========================================== --}}
        <div class="col-span-4 bg-gradient-to-br from-slate-50 to-gray-100 dark:from-slate-900 dark:to-gray-800 p-5 border-r border-gray-200 dark:border-gray-700">
            <div class="space-y-0.5">
                {{-- TÜM KATEGORİLER TAB'I (1. sıra) --}}
                <a href="/shop"
                   @mouseenter="activeTab = 0"
                   :class="activeTab === 0 ? 'bg-white dark:bg-gray-700 shadow-md border-blue-300 dark:border-blue-500' : 'border-transparent hover:bg-white/50 dark:hover:bg-gray-700/50'"
                   class="group w-full flex items-center gap-3 px-3 py-[11px] rounded-xl transition-all duration-200 border">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-grid-2 text-white"></i>
                    </div>
                    <div class="flex-1 text-left">
                        <p class="font-bold text-gray-900 dark:text-white text-sm">Tüm Kategoriler</p>
                    </div>
                    <i class="fa-solid fa-chevron-right text-gray-400 dark:text-gray-500 text-xs group-hover:text-blue-500 transition"></i>
                </a>

                @foreach($mainCategories as $cat)
                    @php
                        $catId = $cat->category_id;
                        $catTitle = is_array($cat->title) ? ($cat->title['tr'] ?? $cat->title['en'] ?? '') : $cat->title;
                        $catSlug = is_array($cat->slug) ? ($cat->slug['tr'] ?? $cat->slug['en'] ?? '') : $cat->slug;
                        $catIcon = $cat->icon_class ?? 'fa-solid fa-box';
                        $gradient = $gradients[$catId] ?? 'from-blue-500 to-indigo-600';
                        $borderColor = $borderColors[$catId] ?? ['normal' => 'border-blue-300 dark:border-blue-500', 'hover' => 'group-hover:text-blue-500'];
                    @endphp

                    <a href="/shop/kategori/{{ $catSlug }}"
                       @mouseenter="activeTab = {{ $catId }}"
                       :class="activeTab === {{ $catId }} ? 'bg-white dark:bg-gray-700 shadow-md {{ $borderColor['normal'] }}' : 'border-transparent hover:bg-white/50 dark:hover:bg-gray-700/50'"
                       class="group w-full flex items-center gap-3 px-3 py-[11px] rounded-xl transition-all duration-200 border">
                        <div class="w-10 h-10 bg-gradient-to-br {{ $gradient }} rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="{{ $catIcon }} text-white"></i>
                        </div>
                        <div class="flex-1 text-left">
                            <p class="font-bold text-gray-900 dark:text-white text-sm">{{ $catTitle }}</p>
                        </div>
                        <i class="fa-solid fa-chevron-right text-gray-400 dark:text-gray-500 text-xs {{ $borderColor['hover'] }} transition"></i>
                    </a>
                @endforeach
            </div>
        </div>

        {{-- ========================================== --}}
        {{-- SAĞ: DİNAMİK İÇERİK --}}
        {{-- ========================================== --}}
        <div class="col-span-8 bg-white dark:bg-gray-800">
            {{-- TÜM KATEGORİLER İÇERİĞİ (activeTab = 0) --}}
            <div x-show="activeTab === 0"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-x-4"
                 x-transition:enter-end="opacity-100 translate-x-0"
                 class="p-5 h-full"
                 x-cloak>

                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <i class="fa-solid fa-grid-2 text-blue-600 dark:text-blue-400"></i>
                        Tüm Kategoriler
                    </h3>
                    <a href="/shop"
                       class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-semibold transition">
                        Mağazaya Git →
                    </a>
                </div>

                {{-- Kategoriler Grid --}}
                <div class="grid grid-cols-2 gap-4">
                    @foreach($allMainCategories as $cat)
                        @php
                            $catId = $cat->category_id;
                            $catTitle = is_array($cat->title) ? ($cat->title['tr'] ?? $cat->title['en'] ?? '') : $cat->title;
                            $catSlug = is_array($cat->slug) ? ($cat->slug['tr'] ?? $cat->slug['en'] ?? '') : $cat->slug;
                            $catIcon = $cat->icon_class ?? 'fa-solid fa-box';
                            $gradient = $gradients[$catId] ?? 'from-blue-500 to-indigo-600';
                            $borderColor = $borderColors[$catId] ?? ['normal' => 'border-blue-300 dark:border-blue-500', 'product' => 'hover:border-blue-400 dark:hover:border-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/20'];
                        @endphp

                        <a href="/shop/kategori/{{ $catSlug }}"
                           class="block p-5 rounded-xl border-2 border-gray-200 dark:border-gray-700 {{ $borderColor['product'] }} hover:shadow-md transition-all duration-200 bg-white dark:bg-gray-700 group">
                            <div class="flex items-center gap-4">
                                <div class="w-14 h-14 bg-gradient-to-br {{ $gradient }} rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                                    <i class="{{ $catIcon }} text-white text-xl"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-bold text-base text-gray-900 dark:text-white leading-tight truncate">{{ $catTitle }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Ürünleri Gör</p>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>

            @foreach($categoryData as $catId => $data)
                <div x-show="activeTab === {{ $catId }}"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-x-4"
                     x-transition:enter-end="opacity-100 translate-x-0"
                     class="p-5 h-full"
                     x-cloak>

                    @if($data['type'] === 'subcategories')
                        {{-- YEDEK PARÇA: ALT KATEGORİLER --}}
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                <i class="fa-solid fa-wrench text-slate-600 dark:text-slate-400"></i>
                                {{ $data['title'] }} Kategorileri
                            </h3>
                            <a href="/shop/kategori/{{ $data['slug'] }}"
                               class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-semibold transition">
                                Tümünü Gör →
                            </a>
                        </div>

                        {{-- Alt Kategoriler Grid --}}
                        <div class="grid grid-cols-3 gap-3">
                            @foreach($data['subcategories'] as $subcat)
                                @php
                                    $subTitle = is_array($subcat->title) ? ($subcat->title['tr'] ?? '') : $subcat->title;
                                    $subSlug = is_array($subcat->slug) ? ($subcat->slug['tr'] ?? '') : $subcat->slug;
                                    $subColors = $subcategoryColors[$subSlug] ?? ['icon' => 'fa-solid fa-box', 'border' => 'hover:border-gray-400 dark:hover:border-gray-500', 'text' => 'text-gray-600 dark:text-gray-400'];
                                @endphp

                                <a href="/shop/kategori/{{ $subSlug }}"
                                   class="block p-4 rounded-xl border-2 border-gray-200 dark:border-gray-700 {{ $subColors['border'] }} hover:shadow-md transition-all duration-200 bg-white dark:bg-gray-700">
                                    <div class="flex items-center gap-3 mb-2">
                                        <i class="{{ $subColors['icon'] }} text-2xl {{ $subColors['text'] }}"></i>
                                        <p class="font-bold text-sm text-gray-900 dark:text-white leading-tight">{{ $subTitle }}</p>
                                    </div>
                                </a>
                            @endforeach
                        </div>

                    @else
                        {{-- DİĞER KATEGORİLER: 4 YATAY KART + TÜMÜNÜ GÖSTER --}}
                        @php
                            $catBorderColors = $borderColors[$catId] ?? $borderColors[2];
                        @endphp
                        <div class="flex flex-col h-full">
                            {{-- 4 Yatay Kart (2x2 grid) --}}
                            <div class="grid grid-cols-2 gap-3 flex-1">
                                @if($data['products']->isNotEmpty())
                                    @foreach($data['products']->take(4) as $product)
                                        <x-ixtif.product-card
                                            :product="$product"
                                            layout="horizontal"
                                            :showAddToCart="false"
                                            :showCategory="false"
                                        />
                                    @endforeach
                                @else
                                    <div class="col-span-2 p-12 text-center bg-gray-50 dark:bg-gray-700 rounded-2xl border-2 border-gray-200 dark:border-gray-600 flex flex-col items-center justify-center">
                                        <i class="{{ $data['icon'] }} text-5xl text-gray-300 dark:text-gray-600 mb-3"></i>
                                        <p class="text-gray-600 dark:text-gray-400 text-sm">Henüz ürün eklenmedi</p>
                                    </div>
                                @endif
                            </div>

                            {{-- Tümünü Göster Butonu --}}
                            <div class="mt-4">
                                <a href="/shop/kategori/{{ $data['slug'] }}"
                                   class="block text-center w-full py-4 rounded-xl font-bold text-sm transition-colors duration-200 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600 border-2 border-gray-200 dark:border-gray-600">
                                    Tüm {{ $data['title'] }} Ürünlerini Gör →
                                </a>
                            </div>
                        </div>
                    @endif

                </div>
            @endforeach
        </div>

    </div>
</div>

{{-- Custom Scrollbar Styles --}}
<style>
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: rgb(241 245 249 / 0.5);
    border-radius: 10px;
}
.dark .custom-scrollbar::-webkit-scrollbar-track {
    background: rgb(51 65 85 / 0.5);
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgb(148 163 184);
    border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: rgb(100 116 139);
}
.dark .custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgb(71 85 105);
}
.dark .custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: rgb(51 65 85);
}
</style>

{{-- Tailwind Safelist: Tüm kategori renkleri için gradient sınıfları --}}
{{--
    from-orange-600 to-red-600 hover:from-orange-700 hover:to-red-700
    from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700
    from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700
    from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700
    from-cyan-600 to-teal-600 hover:from-cyan-700 hover:to-teal-700
    from-yellow-600 to-orange-600 hover:from-yellow-700 hover:to-orange-700
    from-slate-600 to-gray-700 hover:from-slate-700 hover:to-gray-800
--}}

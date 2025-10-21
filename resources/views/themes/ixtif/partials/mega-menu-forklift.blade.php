@php
    // Forklift kategorisini ve alt kategorilerini çek
    try {
        $forkliftCategory = \Modules\Shop\app\Models\ShopCategory::where('slug', 'forklift')
            ->where('is_active', 1)
            ->first();

        $subCategories = collect();
        if ($forkliftCategory) {
            $subCategories = \Modules\Shop\app\Models\ShopCategory::where('parent_id', $forkliftCategory->id)
                ->where('is_active', 1)
                ->orderBy('order_column')
                ->take(8)
                ->get();
        }
    } catch (\Exception $e) {
        $forkliftCategory = null;
        $subCategories = collect();
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
    <div class="flex items-center justify-between mb-6">
        <div>
            <h3 class="text-3xl font-bold text-gray-800 dark:text-gray-200 flex items-center gap-3">
                <i class="fa-solid fa-forklift text-blue-600 dark:text-blue-400"></i>
                Forklift Kategorileri
            </h3>
            @if($forkliftCategory && $forkliftCategory->description)
                <p class="text-gray-600 dark:text-gray-400 mt-2">{{ Str::limit($forkliftCategory->description, 120) }}</p>
            @endif
        </div>
        <a href="{{ route('shop.index') }}?category=forklift" class="bg-blue-600 dark:bg-blue-700 text-white px-6 py-3 rounded-xl font-bold hover:bg-blue-700 dark:hover:bg-blue-800 transition flex items-center gap-2">
            <span>Tüm Forklift Ürünleri</span>
            <i class="fa-solid fa-arrow-right"></i>
        </a>
    </div>

    @if($subCategories->isNotEmpty())
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($subCategories as $index => $subCat)
                @php
                    $color = $colors[$index % count($colors)];
                @endphp
                <a href="{{ route('shop.category', $subCat->slug) }}" class="group">
                    <div class="bg-gradient-to-br from-{{ $color['bg'] }}-50 to-{{ $color['bg'] }}-100 dark:from-{{ $color['bg'] }}-900/30 dark:to-{{ $color['bg'] }}-900/20 rounded-2xl p-8 group-hover:shadow-xl transition-all duration-300 h-48 flex items-center justify-center mb-4 relative overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-br from-{{ $color['bg'] }}-500 to-{{ $color['bg'] }}-600 opacity-0 group-hover:opacity-10 transition-opacity"></div>
                        @if($subCat->getFirstMediaUrl('category_image'))
                            <img src="{{ $subCat->getFirstMediaUrl('category_image') }}" alt="{{ $subCat->name }}" class="w-full h-full object-contain group-hover:scale-110 transition-all duration-300">
                        @else
                            <i class="fa-solid {{ $subCat->icon ?? 'fa-forklift' }} text-7xl text-{{ $color['icon'] }}-400 dark:text-{{ $color['icon'] }}-500 group-hover:text-{{ $color['icon'] }}-600 dark:group-hover:text-{{ $color['icon'] }}-400 group-hover:scale-110 transition-all duration-300"></i>
                        @endif
                    </div>
                    <h5 class="font-bold text-gray-800 dark:text-gray-200 group-hover:text-{{ $color['bg'] }}-600 dark:group-hover:text-{{ $color['bg'] }}-400 transition text-lg mb-1">{{ $subCat->name }}</h5>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">{{ $subCat->products_count ?? 0 }}+ ürün</p>
                    <div class="flex items-center gap-2 text-xs text-gray-400 dark:text-gray-500">
                        <i class="fa-solid fa-tag"></i>
                        <span>Özel fiyatlar</span>
                    </div>
                </a>
            @endforeach
        </div>
    @else
        {{-- Fallback content --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="text-center p-8 bg-blue-50 dark:bg-blue-900/20 rounded-2xl border-2 border-dashed border-blue-200 dark:border-blue-700">
                <i class="fa-solid fa-forklift text-6xl text-blue-500 dark:text-blue-400 mb-4"></i>
                <h5 class="font-bold text-gray-800 dark:text-gray-200 mb-2">Forklift Kategorileri</h5>
                <p class="text-sm text-gray-500 dark:text-gray-400">Yakında eklenecek</p>
            </div>
        </div>
    @endif
</div>

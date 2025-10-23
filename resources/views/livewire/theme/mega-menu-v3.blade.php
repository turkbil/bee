<div class="w-full bg-white shadow-2xl rounded-2xl overflow-hidden" x-data="{ searchFocused: false }">
    {{-- V3: Professional Asymmetric with Real-time Search --}}
    <div class="grid md:grid-cols-7 gap-0 min-h-[520px]">

        {{-- Sol: Compact Categories (2/7) --}}
        <div class="md:col-span-2 bg-gradient-to-br from-indigo-600 to-purple-700 p-6">
            <div class="mb-6">
                @if($category)
                    <h3 class="text-xl font-bold text-white mb-1">
                        {{ is_array($category->title) ? $category->title['tr'] : $category->title }}
                    </h3>
                    <div class="w-12 h-1 bg-yellow-400 rounded-full"></div>
                @endif
            </div>

            {{-- Compact Category List --}}
            @if($subCategories->isNotEmpty())
                <div class="space-y-2">
                    @foreach($subCategories as $subCat)
                        <a href="/shop/kategori/{{ is_array($subCat->slug) ? $subCat->slug['tr'] : $subCat->slug }}"
                           class="group flex items-center gap-3 text-white/80 hover:text-white transition py-2">
                            <div class="w-1 h-8 bg-yellow-400 rounded-full opacity-0 group-hover:opacity-100 transition"></div>
                            <span class="text-sm font-medium">{{ is_array($subCat->title) ? $subCat->title['tr'] : $subCat->title }}</span>
                        </a>
                    @endforeach
                </div>

                <div class="mt-8">
                    <a href="/shop/kategori/{{ is_array($category->slug) ? $category->slug['tr'] : $category->slug }}"
                       class="block w-full bg-yellow-400 text-indigo-900 font-bold py-3 rounded-lg text-center text-sm hover:bg-yellow-300 transition">
                        Tümünü Gör
                    </a>
                </div>
            @else
                <div class="text-white/70 text-sm">
                    Alt kategori bulunamadı
                </div>
            @endif
        </div>

        {{-- Orta: Featured Product + Search (3/7) --}}
        <div class="md:col-span-3 bg-gradient-to-br from-gray-50 to-indigo-50 p-8 flex flex-col justify-center">

            {{-- Search Box --}}
            <div class="mb-6">
                <div class="relative" @click.away="searchFocused = false">
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        @focus="searchFocused = true"
                        placeholder="Ürün ara... ({{ $category ? (is_array($category->title) ? $category->title['tr'] : $category->title) : 'Kategoriler' }} içinde)"
                        class="w-full bg-white border-2 border-indigo-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 transition-all"
                    >
                    <i class="fa-solid fa-magnifying-glass absolute right-4 top-1/2 -translate-y-1/2 text-indigo-400"></i>
                </div>

                @if(!empty($search))
                    <div class="mt-2 flex items-center gap-2">
                        <span class="text-xs text-gray-600">Aranan: "{{ $search }}"</span>
                        <button wire:click="$set('search', '')" class="text-xs text-indigo-600 hover:text-indigo-800 font-semibold">
                            Temizle ×
                        </button>
                    </div>
                @endif
            </div>

            {{-- Featured Product Card --}}
            @if($products->isNotEmpty())
                @php $featuredProduct = $products->first(); @endphp

                <div class="bg-white rounded-2xl p-8 shadow-xl hover:shadow-2xl transition-all duration-300 border-2 border-transparent hover:border-indigo-500">
                    <div class="flex items-center gap-2 text-xs font-bold text-indigo-600 mb-4">
                        <div class="w-2 h-2 bg-indigo-600 rounded-full animate-pulse"></div>
                        <span>{{ !empty($search) ? 'ARAMA SONUCU' : 'ÖNE ÇIKAN MODEL' }}</span>
                    </div>

                    {{-- Product Image Placeholder --}}
                    <div class="flex items-center justify-center mb-6 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-2xl p-8">
                        @if($featuredProduct->getFirstMediaUrl('product_images'))
                            <img src="{{ thumb($featuredProduct->getFirstMedia('product_images'), 300, 300, ['quality' => 85, 'scale' => 1]) }}"
                                 alt="{{ is_array($featuredProduct->title) ? $featuredProduct->title['tr'] : $featuredProduct->title }}"
                                 class="w-full h-32 object-contain"
                                 loading="lazy">
                        @else
                            <i class="fa-solid fa-box text-8xl text-indigo-600"></i>
                        @endif
                    </div>

                    <h4 class="text-2xl font-black text-gray-900 mb-3">
                        {{ is_array($featuredProduct->title) ? $featuredProduct->title['tr'] : $featuredProduct->title }}
                    </h4>

                    @if($featuredProduct->short_description)
                        <p class="text-gray-600 mb-6 leading-relaxed text-sm">
                            {{ Str::limit(is_array($featuredProduct->short_description) ? $featuredProduct->short_description['tr'] : $featuredProduct->short_description, 80) }}
                        </p>
                    @endif

                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <div class="text-3xl font-black text-indigo-600">Fiyat Sorunuz</div>
                            <div class="text-xs text-gray-500">Hızlı teslimat mevcut</div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <a href="/shop/urun/{{ is_array($featuredProduct->slug) ? $featuredProduct->slug['tr'] : $featuredProduct->slug }}"
                           class="px-4 py-3 bg-indigo-600 text-white rounded-lg font-bold hover:bg-indigo-700 transition text-center">
                            Fiyat Al
                        </a>
                        <a href="/shop/urun/{{ is_array($featuredProduct->slug) ? $featuredProduct->slug['tr'] : $featuredProduct->slug }}"
                           class="px-4 py-3 border-2 border-indigo-600 text-indigo-600 rounded-lg font-bold hover:bg-indigo-50 transition text-center">
                            Detaylar
                        </a>
                    </div>
                </div>
            @else
                {{-- No Results --}}
                <div class="bg-white rounded-2xl p-12 text-center shadow-xl">
                    <i class="fa-solid fa-magnifying-glass text-6xl text-gray-300 mb-4"></i>
                    <h4 class="text-xl font-bold text-gray-800 mb-2">Sonuç Bulunamadı</h4>
                    <p class="text-gray-600 mb-6">"{{ $search }}" için ürün bulunamadı</p>
                    <button wire:click="$set('search', '')" class="inline-flex items-center gap-2 bg-indigo-600 text-white px-6 py-3 rounded-xl font-bold hover:bg-indigo-700 transition">
                        <i class="fa-solid fa-arrow-rotate-left"></i>
                        Aramayı Temizle
                    </button>
                </div>
            @endif
        </div>

        {{-- Sağ: Secondary Products (2/7) --}}
        <div class="md:col-span-2 bg-white p-6 border-l border-gray-200">
            <div class="mb-4">
                <h3 class="text-lg font-bold text-gray-900">
                    {{ !empty($search) ? 'Diğer Sonuçlar' : 'Diğer Modeller' }}
                </h3>
            </div>

            <div class="space-y-4">
                @if($products->count() > 1)
                    @foreach($products->skip(1)->take(4) as $product)
                        {{-- Small Product Card --}}
                        <a href="/shop/urun/{{ is_array($product->slug) ? $product->slug['tr'] : $product->slug }}"
                           class="group block bg-gray-50 hover:bg-indigo-50 rounded-xl p-4 transition-all duration-200 border border-gray-200 hover:border-indigo-300">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-10 h-10 bg-gradient-to-br from-indigo-400 to-purple-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                    @if($product->getFirstMediaUrl('product_images'))
                                        <img src="{{ thumb($product->getFirstMedia('product_images'), 40, 40, ['quality' => 85, 'scale' => 1]) }}"
                                             alt="{{ is_array($product->title) ? $product->title['tr'] : $product->title }}"
                                             class="w-8 h-8 object-contain"
                                             loading="lazy">
                                    @else
                                        <i class="fa-solid fa-box text-white text-lg"></i>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h5 class="font-bold text-gray-800 text-sm truncate group-hover:text-indigo-600 transition">
                                        {{ is_array($product->title) ? $product->title['tr'] : $product->title }}
                                    </h5>
                                    @if($product->short_description)
                                        <p class="text-xs text-gray-500 truncate">
                                            {{ Str::limit(is_array($product->short_description) ? $product->short_description['tr'] : $product->short_description, 30) }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                            <div class="text-xs font-bold text-gray-700">Fiyat Sorunuz →</div>
                        </a>
                    @endforeach
                @else
                    <div class="text-center py-8 text-gray-500 text-sm">
                        @if(!empty($search))
                            Daha fazla sonuç yok
                        @else
                            Diğer ürünler yükleniyor...
                        @endif
                    </div>
                @endif
            </div>

            @if($products->isNotEmpty() && $category)
                <div class="mt-4">
                    <a href="/shop/kategori/{{ is_array($category->slug) ? $category->slug['tr'] : $category->slug }}"
                       class="block text-center py-2 text-indigo-600 text-sm font-bold hover:text-indigo-700 transition">
                        + Tüm Ürünleri Gör ({{ $products->count() }}+)
                    </a>
                </div>
            @endif
        </div>

    </div>

    {{-- Loading Indicator --}}
    <div wire:loading class="absolute inset-0 bg-white/80 backdrop-blur-sm flex items-center justify-center rounded-2xl">
        <div class="text-center">
            <i class="fa-solid fa-spinner fa-spin text-4xl text-indigo-600 mb-2"></i>
            <p class="text-sm text-gray-600 font-semibold">Yükleniyor...</p>
        </div>
    </div>
</div>

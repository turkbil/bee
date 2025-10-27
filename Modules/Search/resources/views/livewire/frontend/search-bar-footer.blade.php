<div class="relative mb-6"
     x-data="{
         query: $wire.entangle('query').live,
         open: $wire.entangle('isOpen').live,
         keywords: [],
         products: [],
         total: 0,
         loading: false,
         debounceTimer: null,

         async fetchSuggestions() {
             const trimmed = this.query?.trim() || '';
             if (trimmed.length < 2) {
                 this.keywords = [];
                 this.products = [];
                 this.total = 0;
                 this.open = false;
                 return;
             }

             this.loading = true;
             try {
                 const response = await fetch(`/api/search/suggestions?q=${encodeURIComponent(trimmed)}`, {
                     headers: { 'Accept': 'application/json' }
                 });

                 if (!response.ok) throw new Error('Network error');

                 const data = await response.json();
                 if (data.success) {
                     this.keywords = data.data.keywords || [];
                     this.products = data.data.products || [];
                     this.total = data.data.total || 0;
                     this.open = (this.keywords.length > 0 || this.products.length > 0);
                 }
             } catch (error) {
                 console.error('Search suggestions error:', error);
             } finally {
                 this.loading = false;
             }
         },

         debouncedFetch() {
             clearTimeout(this.debounceTimer);
             this.debounceTimer = setTimeout(() => this.fetchSuggestions(), 300);
         },

         selectKeyword(keyword) {
             if (!keyword?.text) return;
             window.location.href = '{{ route('search.show', ['query' => '_PLACEHOLDER_']) }}'.replace('_PLACEHOLDER_', encodeURIComponent(keyword.text));
         },

         selectProduct(product, index = 0) {
             if (!product?.url) return;
             // Track click before navigation
             this.trackClick(product, index);
             window.location.href = product.url;
         },

         async trackClick(product, position) {
             if (!product) return;

             try {
                 // Type mapping (frontend format → backend model class)
                 const typeMap = {
                     'products': 'Modules\\Shop\\App\\Models\\ShopProduct',
                     'categories': 'Modules\\Shop\\App\\Models\\ShopCategory',
                     'brands': 'Modules\\Shop\\App\\Models\\ShopBrand'
                 };

                 const modelType = typeMap[product.type] || product.type;

                 // Extract ID from product
                 let productId = product.id;
                 if (!productId && product.url) {
                     // Try to extract ID from URL if needed
                     const urlParts = product.url.split('/');
                     productId = parseInt(urlParts[urlParts.length - 1]) || 0;
                 }

                 await fetch('/api/search/track-click', {
                     method: 'POST',
                     headers: {
                         'Content-Type': 'application/json',
                         'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                     },
                     body: JSON.stringify({
                         query: this.query,
                         result_id: productId,
                         result_type: modelType,
                         position: position,
                         opened_in_new_tab: false
                     })
                 });
             } catch (error) {
                 console.warn('Click tracking failed:', error);
             }
         }
     }"
     @click.away="open = false"
     x-init="$nextTick(() => { if (typeof query !== 'undefined') $watch('query', () => debouncedFetch()); })">

    <div class="flex gap-3">
        <div class="flex-1 relative">
            <i class="fa-solid fa-magnifying-glass absolute left-6 top-1/2 -translate-y-1/2 text-gray-400 text-xl z-10"></i>
            <input type="search"
                   x-model="query"
                   @keydown.enter.prevent="if(query?.trim()) window.location.href='{{ route('search.show', ['query' => '_PLACEHOLDER_']) }}'.replace('_PLACEHOLDER_', encodeURIComponent(query))"
                   placeholder="Ürün ara..."
                   class="w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl pl-16 pr-6 py-5 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 text-sm sm:text-lg focus:outline-none focus:ring-4 focus:ring-blue-100 dark:focus:ring-blue-900/30 focus:border-blue-500 dark:focus:border-blue-400 transition-all">
        </div>
        <button
            @click="if(query?.trim()) window.location.href='{{ route('search.show', ['query' => '_PLACEHOLDER_']) }}'.replace('_PLACEHOLDER_', encodeURIComponent(query))"
            :disabled="!query?.trim()"
            :class="{'opacity-50 cursor-not-allowed': !query?.trim()}"
            class="bg-gradient-to-r from-blue-600 to-purple-600 dark:from-blue-500 dark:to-purple-500 text-white px-8 md:px-10 py-5 rounded-2xl font-bold text-lg hover:shadow-2xl hover:scale-105 transition-all flex items-center gap-2">
            <i class="fa-solid fa-search"></i>
            <span class="hidden md:inline">Ara</span>
        </button>
    </div>

    {{-- Dropdown --}}
    <div x-show="open && ((keywords?.length || 0) > 0 || (products?.length || 0) > 0)"
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-1"
         class="absolute top-full left-0 right-0 mt-2 bg-white dark:bg-gray-800 shadow-2xl rounded-xl z-50 border border-gray-200 dark:border-gray-700"
         style="z-index:50;">

        <div class="max-h-[28rem] overflow-y-auto">
            <div class="grid gap-4 md:gap-6 px-4 py-4 grid-cols-1 lg:grid-cols-[minmax(0,0.9fr)_minmax(0,1.1fr)]">

                {{-- Keywords Section --}}
                <div x-show="(keywords?.length || 0) > 0" class="space-y-2 border border-gray-200 dark:border-gray-700 rounded-lg p-4 lg:p-5 bg-gray-50 dark:bg-gray-900/40">
                    <div class="flex items-center justify-between text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                        <span><i class="fa-solid fa-fire text-orange-500 mr-1"></i> Popüler Aramalar</span>
                        <span class="text-[10px] text-gray-400 dark:text-gray-500" x-text="`${keywords?.length || 0}`"></span>
                    </div>
                    <div class="space-y-1">
                        <template x-for="(keyword, index) in keywords" :key="'k-'+index">
                            <a href="#"
                               @click.prevent="selectKeyword(keyword)"
                               class="flex items-center justify-between gap-3 px-3 py-2 rounded-md transition group hover:bg-white dark:hover:bg-gray-800">
                                <div class="flex items-center gap-3">
                                    <span class="w-7 h-7 rounded-full bg-white dark:bg-gray-800 flex items-center justify-center text-gray-400 dark:text-gray-500 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition">
                                        <i class="fa-solid fa-magnifying-glass text-sm"></i>
                                    </span>
                                    <span class="font-medium text-sm text-gray-900 dark:text-white" x-text="keyword.text"></span>
                                </div>
                                <span x-show="keyword.count" class="text-xs text-gray-400 dark:text-gray-500" x-text="`${keyword.count} sonuç`"></span>
                            </a>
                        </template>
                    </div>
                </div>

                {{-- Products Section --}}
                <div x-show="(products?.length || 0) > 0" class="space-y-3">
                    <div class="flex items-center justify-between text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                        <span><i class="fa-solid fa-box text-indigo-500 mr-1"></i> Ürünler</span>
                        <span x-show="(total || 0) > 0" class="text-[11px] font-medium text-gray-400 dark:text-gray-500" x-text="`${products?.length || 0} / ${total || 0}`"></span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <template x-for="(product, index) in products" :key="'p-'+index">
                            <a href="#"
                               @click.prevent="selectProduct(product, index)"
                               class="flex gap-3 p-3 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 transition group hover:border-indigo-400 dark:hover:border-indigo-400 hover:shadow-md">
                                <div class="w-16 h-16 rounded-md bg-gray-100 dark:bg-gray-700 flex items-center justify-center overflow-hidden flex-shrink-0">
                                    <template x-if="product.image">
                                        <img :src="product.image"
                                             :alt="product.title"
                                             class="w-full h-full object-cover">
                                    </template>
                                    <template x-if="!product.image">
                                        <i class="fa-solid fa-cube text-gray-400 dark:text-gray-500 text-xl"></i>
                                    </template>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="font-medium text-sm text-gray-900 dark:text-white leading-snug line-clamp-2"
                                         x-html="product.highlighted_title || product.title"></div>
                                    <p x-show="product.highlighted_description"
                                       class="text-xs text-gray-500 dark:text-gray-400 mt-1 line-clamp-2"
                                       x-html="product.highlighted_description"></p>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-2 flex items-center justify-between">
                                        <span x-text="product.type_label"></span>
                                        <span x-show="product.price"
                                              class="ml-2 font-semibold text-green-600 dark:text-green-400"
                                              x-text="product.price"></span>
                                    </div>
                                </div>
                            </a>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        {{-- View All Results --}}
        <a :href="`{{ route('search.show', ['query' => '_PLACEHOLDER_']) }}`.replace('_PLACEHOLDER_', encodeURIComponent(query || ''))"
           x-show="(total || 0) > 0"
           class="block p-4 text-center text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 font-semibold transition border-t border-gray-200 dark:border-gray-700">
            <i class="fa-solid fa-arrow-right mr-2"></i>
            <span x-text="`Tüm ${total || 0} sonucu gör`"></span>
        </a>
    </div>
</div>

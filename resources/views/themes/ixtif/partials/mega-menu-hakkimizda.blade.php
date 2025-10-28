{{-- Hakkımızda Mega Menu - Full Grid Showcase --}}
<div class="w-full rounded-2xl overflow-hidden border border-gray-300 dark:border-gray-700 shadow-lg">
    <div class="bg-white dark:bg-gray-800 rounded-xl p-6">

        {{-- ========================================== --}}
        {{-- MOBİL GÖRÜNÜM (lg altı) --}}
        {{-- ========================================== --}}
        <div class="lg:hidden space-y-3">
            <a href="{{ href('Page', 'show', 'hakkimizda') }}"
               class="flex items-center gap-3 bg-white/70 dark:bg-white/5 backdrop-blur-md border border-gray-300 dark:border-white/10 rounded-xl p-4 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all duration-200">
                <div class="w-12 h-12 bg-white/40 dark:bg-white/10 backdrop-blur-md rounded-xl flex items-center justify-center flex-shrink-0 border border-gray-200 dark:border-white/20">
                    <i class="fa-solid fa-building text-gray-700 dark:text-white text-lg"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <h5 class="font-bold text-gray-900 dark:text-white text-base">Hakkımızda</h5>
                </div>
                <div class="text-gray-500 dark:text-gray-400 flex-shrink-0">
                    <i class="fa-solid fa-chevron-right text-sm"></i>
                </div>
            </a>
        </div>

        {{-- ========================================== --}}
        {{-- DESKTOP GÖRÜNÜM (lg+) --}}
        {{-- ========================================== --}}
        <div class="hidden lg:block">
            <div class="bg-gradient-to-br from-gray-50 to-slate-100 dark:from-gray-900/50 dark:to-slate-900/50 rounded-xl p-6">
                <div class="grid grid-cols-3 gap-6">

                    {{-- 1. Kurumsal --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-lg hover:shadow-2xl transition-all duration-300 group">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg group-hover:scale-110 transition-transform">
                                <i class="fa-solid fa-building text-white text-2xl"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-black text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">Kurumsal</h3>
                                <div class="text-xs text-gray-500 dark:text-gray-400 font-semibold">Hakkımızda</div>
                            </div>
                        </div>
                        <ul class="space-y-2">
                            <li>
                                <a href="{{ href('Page', 'show', 'hakkimizda') }}"
                                   class="text-sm text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:translate-x-1 transition-all inline-flex items-center gap-2">
                                    <i class="fa-solid fa-chevron-right text-xs"></i>Hakkımızda
                                </a>
                            </li>
                            <li>
                                <a href="{{ href('Page', 'show', 'kariyer') }}"
                                   class="text-sm text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:translate-x-1 transition-all inline-flex items-center gap-2">
                                    <i class="fa-solid fa-chevron-right text-xs"></i>Kariyer
                                </a>
                            </li>
                        </ul>
                    </div>

                    {{-- 2. Alışveriş --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-lg hover:shadow-2xl transition-all duration-300 group">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-14 h-14 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg group-hover:scale-110 transition-transform">
                                <i class="fa-solid fa-shopping-cart text-white text-2xl"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-black text-gray-900 dark:text-white group-hover:text-green-600 dark:group-hover:text-green-400 transition-colors">Alışveriş</h3>
                                <div class="text-xs text-gray-500 dark:text-gray-400 font-semibold">Müşteri Hizmetleri</div>
                            </div>
                        </div>
                        <ul class="space-y-2">
                            <li>
                                <a href="{{ href('Page', 'show', 'odeme-yontemleri') }}"
                                   class="text-sm text-gray-700 dark:text-gray-300 hover:text-green-600 dark:hover:text-green-400 hover:translate-x-1 transition-all inline-flex items-center gap-2">
                                    <i class="fa-solid fa-chevron-right text-xs"></i>Ödeme Yöntemleri
                                </a>
                            </li>
                            <li>
                                <a href="{{ href('Page', 'show', 'teslimat-kargo') }}"
                                   class="text-sm text-gray-700 dark:text-gray-300 hover:text-green-600 dark:hover:text-green-400 hover:translate-x-1 transition-all inline-flex items-center gap-2">
                                    <i class="fa-solid fa-chevron-right text-xs"></i>Teslimat & Kargo
                                </a>
                            </li>
                            <li>
                                <a href="{{ href('Page', 'show', 'guvenli-alisveris') }}"
                                   class="text-sm text-gray-700 dark:text-gray-300 hover:text-green-600 dark:hover:text-green-400 hover:translate-x-1 transition-all inline-flex items-center gap-2">
                                    <i class="fa-solid fa-chevron-right text-xs"></i>Güvenli Alışveriş
                                </a>
                            </li>
                            <li>
                                <a href="{{ href('Page', 'show', 'sikca-sorulan-sorular') }}"
                                   class="text-sm text-gray-700 dark:text-gray-300 hover:text-green-600 dark:hover:text-green-400 hover:translate-x-1 transition-all inline-flex items-center gap-2">
                                    <i class="fa-solid fa-chevron-right text-xs"></i>SSS
                                </a>
                            </li>
                            <li>
                                <a href="{{ href('Page', 'show', 'iptal-iade') }}"
                                   class="text-sm text-gray-700 dark:text-gray-300 hover:text-green-600 dark:hover:text-green-400 hover:translate-x-1 transition-all inline-flex items-center gap-2">
                                    <i class="fa-solid fa-chevron-right text-xs"></i>İptal & İade
                                </a>
                            </li>
                            <li>
                                <a href="{{ href('Page', 'show', 'cayma-hakki') }}"
                                   class="text-sm text-gray-700 dark:text-gray-300 hover:text-green-600 dark:hover:text-green-400 hover:translate-x-1 transition-all inline-flex items-center gap-2">
                                    <i class="fa-solid fa-chevron-right text-xs"></i>Cayma Hakkı
                                </a>
                            </li>
                            <li>
                                <a href="{{ href('Page', 'show', 'mesafeli-satis') }}"
                                   class="text-sm text-gray-700 dark:text-gray-300 hover:text-green-600 dark:hover:text-green-400 hover:translate-x-1 transition-all inline-flex items-center gap-2">
                                    <i class="fa-solid fa-chevron-right text-xs"></i>Mesafeli Satış Sözleşmesi
                                </a>
                            </li>
                        </ul>
                    </div>

                    {{-- 3. Yasal --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-lg hover:shadow-2xl transition-all duration-300 group">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-14 h-14 bg-gradient-to-br from-red-500 to-rose-600 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg group-hover:scale-110 transition-transform">
                                <i class="fa-solid fa-scale-balanced text-white text-2xl"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-black text-gray-900 dark:text-white group-hover:text-red-600 dark:group-hover:text-red-400 transition-colors">Yasal</h3>
                                <div class="text-xs text-gray-500 dark:text-gray-400 font-semibold">Mevzuat & Politikalar</div>
                            </div>
                        </div>
                        <ul class="space-y-2">
                            <li>
                                <a href="{{ href('Page', 'show', 'gizlilik-politikasi') }}"
                                   class="text-sm text-gray-700 dark:text-gray-300 hover:text-red-600 dark:hover:text-red-400 hover:translate-x-1 transition-all inline-flex items-center gap-2">
                                    <i class="fa-solid fa-chevron-right text-xs"></i>Gizlilik Politikası
                                </a>
                            </li>
                            <li>
                                <a href="{{ href('Page', 'show', 'kullanim-kosullari') }}"
                                   class="text-sm text-gray-700 dark:text-gray-300 hover:text-red-600 dark:hover:text-red-400 hover:translate-x-1 transition-all inline-flex items-center gap-2">
                                    <i class="fa-solid fa-chevron-right text-xs"></i>Kullanım Koşulları
                                </a>
                            </li>
                            <li>
                                <a href="{{ href('Page', 'show', 'kvkk-aydinlatma') }}"
                                   class="text-sm text-gray-700 dark:text-gray-300 hover:text-red-600 dark:hover:text-red-400 hover:translate-x-1 transition-all inline-flex items-center gap-2">
                                    <i class="fa-solid fa-chevron-right text-xs"></i>KVKK Aydınlatma
                                </a>
                            </li>
                            <li>
                                <a href="{{ href('Page', 'show', 'cerez-politikasi') }}"
                                   class="text-sm text-gray-700 dark:text-gray-300 hover:text-red-600 dark:hover:text-red-400 hover:translate-x-1 transition-all inline-flex items-center gap-2">
                                    <i class="fa-solid fa-chevron-right text-xs"></i>Çerez Politikası
                                </a>
                            </li>
                        </ul>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

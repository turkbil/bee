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
                <div class="grid grid-cols-4 gap-6">

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
                        <ul class="space-y-1">
                            <li>
                                <a href="{{ href('Page', 'show', 'hakkimizda') }}"
                                   class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:text-blue-600 dark:hover:text-blue-400 rounded-lg transition-colors">
                                    <i class="fa-solid fa-chevron-right text-xs"></i>
                                    <span>Hakkımızda</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ href('Page', 'show', 'kariyer') }}"
                                   class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:text-blue-600 dark:hover:text-blue-400 rounded-lg transition-colors">
                                    <i class="fa-solid fa-chevron-right text-xs"></i>
                                    <span>Kariyer</span>
                                </a>
                            </li>
                            <li>
                                <a href="/blog"
                                   class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:text-blue-600 dark:hover:text-blue-400 rounded-lg transition-colors">
                                    <i class="fa-solid fa-chevron-right text-xs"></i>
                                    <span>iXtif Akademi</span>
                                </a>
                            </li>
                        </ul>
                    </div>

                    {{-- 2. Müşteri Hizmetleri --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-lg hover:shadow-2xl transition-all duration-300 group">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-14 h-14 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg group-hover:scale-110 transition-transform">
                                <i class="fa-solid fa-headset text-white text-2xl"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-black text-gray-900 dark:text-white group-hover:text-green-600 dark:group-hover:text-green-400 transition-colors">Müşteri Hizmetleri</h3>
                                <div class="text-xs text-gray-500 dark:text-gray-400 font-semibold">Alışveriş Bilgileri</div>
                            </div>
                        </div>
                        <ul class="space-y-1">
                            <li>
                                <a href="{{ href('Page', 'show', 'odeme-yontemleri') }}"
                                   class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-green-50 dark:hover:bg-green-900/20 hover:text-green-600 dark:hover:text-green-400 rounded-lg transition-colors">
                                    <i class="fa-solid fa-chevron-right text-xs"></i>
                                    <span>Ödeme Yöntemleri</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ href('Page', 'show', 'teslimat-kargo') }}"
                                   class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-green-50 dark:hover:bg-green-900/20 hover:text-green-600 dark:hover:text-green-400 rounded-lg transition-colors">
                                    <i class="fa-solid fa-chevron-right text-xs"></i>
                                    <span>Teslimat & Kargo</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ href('Page', 'show', 'guvenli-alisveris') }}"
                                   class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-green-50 dark:hover:bg-green-900/20 hover:text-green-600 dark:hover:text-green-400 rounded-lg transition-colors">
                                    <i class="fa-solid fa-chevron-right text-xs"></i>
                                    <span>Güvenli Alışveriş</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ href('Page', 'show', 'sikca-sorulan-sorular') }}"
                                   class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-green-50 dark:hover:bg-green-900/20 hover:text-green-600 dark:hover:text-green-400 rounded-lg transition-colors">
                                    <i class="fa-solid fa-chevron-right text-xs"></i>
                                    <span>Sıkça Sorulan Sorular</span>
                                </a>
                            </li>
                        </ul>
                    </div>

                    {{-- 3. İade & Sözleşmeler --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-lg hover:shadow-2xl transition-all duration-300 group">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-14 h-14 bg-gradient-to-br from-orange-500 to-amber-600 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg group-hover:scale-110 transition-transform">
                                <i class="fa-solid fa-file-contract text-white text-2xl"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-black text-gray-900 dark:text-white group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-colors">İade & Sözleşmeler</h3>
                                <div class="text-xs text-gray-500 dark:text-gray-400 font-semibold">İptal & İade</div>
                            </div>
                        </div>
                        <ul class="space-y-1">
                            <li>
                                <a href="{{ href('Page', 'show', 'iptal-iade') }}"
                                   class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-orange-50 dark:hover:bg-orange-900/20 hover:text-orange-600 dark:hover:text-orange-400 rounded-lg transition-colors">
                                    <i class="fa-solid fa-chevron-right text-xs"></i>
                                    <span>İptal & İade</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ href('Page', 'show', 'cayma-hakki') }}"
                                   class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-orange-50 dark:hover:bg-orange-900/20 hover:text-orange-600 dark:hover:text-orange-400 rounded-lg transition-colors">
                                    <i class="fa-solid fa-chevron-right text-xs"></i>
                                    <span>Cayma Hakkı</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ href('Page', 'show', 'mesafeli-satis') }}"
                                   class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-orange-50 dark:hover:bg-orange-900/20 hover:text-orange-600 dark:hover:text-orange-400 rounded-lg transition-colors">
                                    <i class="fa-solid fa-chevron-right text-xs"></i>
                                    <span>Mesafeli Satış Sözleşmesi</span>
                                </a>
                            </li>
                        </ul>
                    </div>

                    {{-- 4. Yasal --}}
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
                        <ul class="space-y-1">
                            <li>
                                <a href="{{ href('Page', 'show', 'gizlilik-politikasi') }}"
                                   class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-red-50 dark:hover:bg-red-900/20 hover:text-red-600 dark:hover:text-red-400 rounded-lg transition-colors">
                                    <i class="fa-solid fa-chevron-right text-xs"></i>
                                    <span>Gizlilik Politikası</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ href('Page', 'show', 'kullanim-kosullari') }}"
                                   class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-red-50 dark:hover:bg-red-900/20 hover:text-red-600 dark:hover:text-red-400 rounded-lg transition-colors">
                                    <i class="fa-solid fa-chevron-right text-xs"></i>
                                    <span>Kullanım Koşulları</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ href('Page', 'show', 'kvkk-aydinlatma') }}"
                                   class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-red-50 dark:hover:bg-red-900/20 hover:text-red-600 dark:hover:text-red-400 rounded-lg transition-colors">
                                    <i class="fa-solid fa-chevron-right text-xs"></i>
                                    <span>KVKK Aydınlatma</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ href('Page', 'show', 'cerez-politikasi') }}"
                                   class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-red-50 dark:hover:bg-red-900/20 hover:text-red-600 dark:hover:text-red-400 rounded-lg transition-colors">
                                    <i class="fa-solid fa-chevron-right text-xs"></i>
                                    <span>Çerez Politikası</span>
                                </a>
                            </li>
                        </ul>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

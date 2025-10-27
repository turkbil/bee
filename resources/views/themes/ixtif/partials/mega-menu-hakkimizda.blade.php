{{-- Hakkımızda Mega Menu - Minimal Liste --}}
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

            <a href="{{ href('Page', 'show', 'iletisim') }}"
               class="flex items-center gap-3 bg-white/70 dark:bg-white/5 backdrop-blur-md border border-gray-300 dark:border-white/10 rounded-xl p-4 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all duration-200">
                <div class="w-12 h-12 bg-white/40 dark:bg-white/10 backdrop-blur-md rounded-xl flex items-center justify-center flex-shrink-0 border border-gray-200 dark:border-white/20">
                    <i class="fa-solid fa-envelope text-gray-700 dark:text-white text-lg"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <h5 class="font-bold text-gray-900 dark:text-white text-base">İletişim</h5>
                </div>
                <div class="text-gray-500 dark:text-gray-400 flex-shrink-0">
                    <i class="fa-solid fa-chevron-right text-sm"></i>
                </div>
            </a>
        </div>

        {{-- ========================================== --}}
        {{-- DESKTOP GÖRÜNÜM (lg+) --}}
        {{-- ========================================== --}}
        <div class="hidden lg:grid grid-cols-12 gap-8">

            {{-- SAYFALARlist (tek kolon) --}}
            <div class="col-span-12">
                <div class="bg-white/70 dark:bg-white/5 backdrop-blur-md border border-gray-300 dark:border-white/10 rounded-2xl p-6">

                    {{-- Header --}}
                    <div class="flex items-center gap-3 mb-4 pb-4 border-b border-gray-200 dark:border-white/10">
                        <div class="w-12 h-12 bg-white/40 dark:bg-white/10 backdrop-blur-md rounded-xl flex items-center justify-center border border-gray-200 dark:border-white/20">
                            <i class="fa-solid fa-building text-gray-700 dark:text-white text-xl"></i>
                        </div>
                        <h3 class="text-xl font-black text-gray-900 dark:text-white">Kurumsal</h3>
                    </div>

                    {{-- Sayfa Listesi - 2 Kolon --}}
                    <div class="grid grid-cols-2 gap-2">
                        <a href="{{ href('Page', 'show', 'hakkimizda') }}"
                           class="flex items-center gap-2 text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors py-1.5 px-2 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20">
                            <i class="fa-solid fa-chevron-right text-xs text-gray-400 dark:text-gray-500"></i>
                            <span class="text-sm font-medium">Hakkımızda</span>
                        </a>

                        <a href="{{ href('Page', 'show', 'iletisim') }}"
                           class="flex items-center gap-2 text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors py-1.5 px-2 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20">
                            <i class="fa-solid fa-chevron-right text-xs text-gray-400 dark:text-gray-500"></i>
                            <span class="text-sm font-medium">İletişim</span>
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

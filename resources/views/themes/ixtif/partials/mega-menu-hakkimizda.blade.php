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
        <div class="hidden lg:block">
            <div class="bg-gradient-to-br from-gray-50 to-slate-100 dark:from-gray-900/50 dark:to-slate-900/50 rounded-xl p-6 border-2 border-indigo-200 dark:border-indigo-800">
                <div class="grid grid-cols-3 gap-6">

                    {{-- 1. Hakkımızda --}}
                    <a href="{{ href('Page', 'show', 'hakkimizda') }}"
                       class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-lg hover:shadow-2xl transition-all duration-300 group border-2 border-transparent hover:border-blue-400 dark:hover:border-blue-600 block">
                        <div class="flex items-center gap-3">
                            <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg group-hover:scale-110 transition-transform">
                                <i class="fa-solid fa-building text-white text-2xl"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-black text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">Hakkımızda</h3>
                                <div class="text-xs text-gray-500 dark:text-gray-400 font-semibold">Firmamızı Tanıyın</div>
                            </div>
                        </div>
                    </a>

                    {{-- 2. İletişim --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-lg hover:shadow-2xl transition-all duration-300 group border-2 border-transparent hover:border-green-400 dark:hover:border-green-600">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-14 h-14 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg group-hover:scale-110 transition-transform">
                                <i class="fa-solid fa-envelope text-white text-2xl"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-black text-gray-900 dark:text-white group-hover:text-green-600 dark:group-hover:text-green-400 transition-colors">İletişim</h3>
                                <div class="text-xs text-gray-500 dark:text-gray-400 font-semibold">Bize Ulaşın</div>
                            </div>
                        </div>
                        <ul class="space-y-2">
                            @php
                                $contactPhone = setting('contact_phone_1', '0216 755 3 555');
                                $contactWhatsapp = setting('contact_whatsapp_1', '0501 005 67 58');
                                $contactEmail = setting('contact_email_1', 'info@ixtif.com');
                            @endphp
                            <li>
                                <a href="{{ href('Page', 'show', 'iletisim') }}"
                                   class="text-sm text-gray-700 dark:text-gray-300 hover:text-green-600 dark:hover:text-green-400 hover:translate-x-1 transition-all inline-flex items-center gap-2">
                                    <i class="fa-solid fa-chevron-right text-xs"></i>İletişim Formu
                                </a>
                            </li>
                            <li>
                                <a href="tel:{{ str_replace(' ', '', $contactPhone) }}"
                                   class="text-sm text-gray-700 dark:text-gray-300 hover:text-green-600 dark:hover:text-green-400 hover:translate-x-1 transition-all inline-flex items-center gap-2">
                                    <i class="fa-solid fa-chevron-right text-xs"></i>{{ $contactPhone }}
                                </a>
                            </li>
                            <li>
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $contactWhatsapp) }}"
                                   target="_blank"
                                   class="text-sm text-gray-700 dark:text-gray-300 hover:text-green-600 dark:hover:text-green-400 hover:translate-x-1 transition-all inline-flex items-center gap-2">
                                    <i class="fa-solid fa-chevron-right text-xs"></i>WhatsApp: {{ $contactWhatsapp }}
                                </a>
                            </li>
                        </ul>
                    </div>

                    {{-- 3. Sosyal Medya --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-lg hover:shadow-2xl transition-all duration-300 group border-2 border-transparent hover:border-purple-400 dark:hover:border-purple-600">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-14 h-14 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg group-hover:scale-110 transition-transform">
                                <i class="fa-solid fa-share-nodes text-white text-2xl"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-black text-gray-900 dark:text-white group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors">Sosyal Medya</h3>
                                <div class="text-xs text-gray-500 dark:text-gray-400 font-semibold">Bizi Takip Edin</div>
                            </div>
                        </div>
                        <ul class="space-y-2">
                            <li>
                                <a href="{{ setting('social_facebook', '#') }}"
                                   target="_blank"
                                   class="text-sm text-gray-700 dark:text-gray-300 hover:text-purple-600 dark:hover:text-purple-400 hover:translate-x-1 transition-all inline-flex items-center gap-2">
                                    <i class="fa-solid fa-chevron-right text-xs"></i>Facebook
                                </a>
                            </li>
                            <li>
                                <a href="{{ setting('social_instagram', '#') }}"
                                   target="_blank"
                                   class="text-sm text-gray-700 dark:text-gray-300 hover:text-purple-600 dark:hover:text-purple-400 hover:translate-x-1 transition-all inline-flex items-center gap-2">
                                    <i class="fa-solid fa-chevron-right text-xs"></i>Instagram
                                </a>
                            </li>
                            <li>
                                <a href="{{ setting('social_linkedin', '#') }}"
                                   target="_blank"
                                   class="text-sm text-gray-700 dark:text-gray-300 hover:text-purple-600 dark:hover:text-purple-400 hover:translate-x-1 transition-all inline-flex items-center gap-2">
                                    <i class="fa-solid fa-chevron-right text-xs"></i>LinkedIn
                                </a>
                            </li>
                            <li>
                                <a href="{{ setting('social_twitter', '#') }}"
                                   target="_blank"
                                   class="text-sm text-gray-700 dark:text-gray-300 hover:text-purple-600 dark:hover:text-purple-400 hover:translate-x-1 transition-all inline-flex items-center gap-2">
                                    <i class="fa-solid fa-chevron-right text-xs"></i>Twitter
                                </a>
                            </li>
                        </ul>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

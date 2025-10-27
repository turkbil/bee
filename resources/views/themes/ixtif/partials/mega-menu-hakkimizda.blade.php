{{-- Hakkımızda Mega Menu - Diğer mega menu'lerle aynı pattern --}}
<div class="w-full rounded-2xl overflow-hidden border border-gray-300 dark:border-gray-700 shadow-lg">
    <div class="bg-white dark:bg-gray-800 rounded-xl p-6">

        {{-- ========================================== --}}
        {{-- MOBİL GÖRÜNÜM (lg altı) --}}
        {{-- ========================================== --}}
        <div class="lg:hidden space-y-3">
            {{-- Kurumsal --}}
            <a href="{{ href('Page', 'show', 'hakkimizda') }}"
               class="flex items-center gap-3 bg-white/70 dark:bg-white/5 backdrop-blur-md border border-gray-300 dark:border-white/10 rounded-xl p-4 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all duration-200">
                <div class="w-12 h-12 bg-white/40 dark:bg-white/10 backdrop-blur-md rounded-xl flex items-center justify-center flex-shrink-0 border border-gray-200 dark:border-white/20">
                    <i class="fa-solid fa-info-circle text-gray-700 dark:text-white text-lg"></i>
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

            {{-- Sosyal Medya - Mobil --}}
            <div class="pt-2 space-y-2">
                <div class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide px-2">
                    Sosyal Medya
                </div>
                <div class="grid grid-cols-2 gap-2">
                    @php
                        $contactPhone = setting('contact_phone_1', '0216 755 3 555');
                        $contactWhatsapp = setting('contact_whatsapp_1', '0501 005 67 58');
                    @endphp

                    <a href="tel:{{ str_replace(' ', '', $contactPhone) }}" class="bg-white/70 dark:bg-white/5 backdrop-blur-md border border-gray-300 dark:border-white/10 rounded-lg px-3 py-2 text-center hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all duration-200 block">
                        <i class="fa-solid fa-phone text-gray-700 dark:text-white text-sm mb-1"></i>
                        <p class="text-xs font-bold text-gray-900 dark:text-white">Telefon</p>
                    </a>

                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $contactWhatsapp) }}" target="_blank" class="bg-white/70 dark:bg-white/5 backdrop-blur-md border border-gray-300 dark:border-white/10 rounded-lg px-3 py-2 text-center hover:bg-green-50 dark:hover:bg-green-900/20 transition-all duration-200 block">
                        <i class="fa-brands fa-whatsapp text-gray-700 dark:text-white text-sm mb-1"></i>
                        <p class="text-xs font-bold text-gray-900 dark:text-white">WhatsApp</p>
                    </a>

                    <a href="{{ setting('social_facebook', '#') }}" target="_blank" class="bg-white/70 dark:bg-white/5 backdrop-blur-md border border-gray-300 dark:border-white/10 rounded-lg px-3 py-2 text-center hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all duration-200 block">
                        <i class="fa-brands fa-facebook-f text-gray-700 dark:text-white text-sm mb-1"></i>
                        <p class="text-xs font-bold text-gray-900 dark:text-white">Facebook</p>
                    </a>

                    <a href="{{ setting('social_instagram', '#') }}" target="_blank" class="bg-white/70 dark:bg-white/5 backdrop-blur-md border border-gray-300 dark:border-white/10 rounded-lg px-3 py-2 text-center hover:bg-pink-50 dark:hover:bg-pink-900/20 transition-all duration-200 block">
                        <i class="fa-brands fa-instagram text-gray-700 dark:text-white text-sm mb-1"></i>
                        <p class="text-xs font-bold text-gray-900 dark:text-white">Instagram</p>
                    </a>
                </div>
            </div>
        </div>

        {{-- ========================================== --}}
        {{-- DESKTOP GÖRÜNÜM (lg+) --}}
        {{-- ========================================== --}}
        <div class="hidden lg:grid grid-cols-12 gap-8">

            {{-- ========================================== --}}
            {{-- SOL: KURUMSAL + İLETİŞİM (col-span-7) --}}
            {{-- ========================================== --}}
            <div class="col-span-12 lg:col-span-7 space-y-5">
                {{-- Kurumsal & İletişim Grid --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Hakkımızda --}}
                    <a href="{{ href('Page', 'show', 'hakkimizda') }}"
                       class="group relative bg-white/70 dark:bg-white/5 backdrop-blur-md border border-gray-300 dark:border-white/10 rounded-2xl p-6 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all duration-200 overflow-hidden">

                        <div class="relative z-10 flex items-center gap-4">
                            {{-- Icon Container --}}
                            <div class="relative">
                                <div class="w-20 h-20 bg-white/40 dark:bg-white/10 backdrop-blur-md rounded-2xl flex items-center justify-center border border-gray-200 dark:border-white/20">
                                    <i class="fa-solid fa-info-circle text-gray-700 dark:text-white text-3xl"></i>
                                </div>
                            </div>

                            {{-- Bilgi --}}
                            <div class="flex-1 min-w-0">
                                <h5 class="font-black text-gray-900 dark:text-white text-2xl">
                                    Hakkımızda
                                </h5>
                            </div>

                            {{-- Arrow --}}
                            <div class="text-gray-600 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white group-hover:translate-x-1 transition-all duration-300">
                                <i class="fa-solid fa-arrow-right text-xl"></i>
                            </div>
                        </div>
                    </a>

                    {{-- İletişim --}}
                    <a href="{{ href('Page', 'show', 'iletisim') }}"
                       class="group relative bg-white/70 dark:bg-white/5 backdrop-blur-md border border-gray-300 dark:border-white/10 rounded-2xl p-6 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all duration-200 overflow-hidden">

                        <div class="relative z-10 flex items-center gap-4">
                            {{-- Icon Container --}}
                            <div class="relative">
                                <div class="w-20 h-20 bg-white/40 dark:bg-white/10 backdrop-blur-md rounded-2xl flex items-center justify-center border border-gray-200 dark:border-white/20">
                                    <i class="fa-solid fa-envelope text-gray-700 dark:text-white text-3xl"></i>
                                </div>
                            </div>

                            {{-- Bilgi --}}
                            <div class="flex-1 min-w-0">
                                <h5 class="font-black text-gray-900 dark:text-white text-2xl">
                                    İletişim
                                </h5>
                            </div>

                            {{-- Arrow --}}
                            <div class="text-gray-600 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white group-hover:translate-x-1 transition-all duration-300">
                                <i class="fa-solid fa-arrow-right text-xl"></i>
                            </div>
                        </div>
                    </a>
                </div>

                {{-- İletişim Butonları - Responsive Grid --}}
                <div class="grid grid-cols-2 xl:grid-cols-4 gap-3">
                    @php
                        $contactPhone = setting('contact_phone_1', '0216 755 3 555');
                        $contactWhatsapp = setting('contact_whatsapp_1', '0501 005 67 58');
                        $contactEmail = setting('contact_email_1', 'info@ixtif.com');
                    @endphp

                    {{-- Telefon --}}
                    <a href="tel:{{ str_replace(' ', '', $contactPhone) }}" class="bg-white/70 dark:bg-white/5 backdrop-blur-md border border-gray-300 dark:border-white/10 rounded-xl px-4 py-3 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all duration-200 block">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-white/40 dark:bg-white/10 backdrop-blur-md rounded-lg flex items-center justify-center flex-shrink-0 border border-gray-200 dark:border-white/20">
                                <i class="fa-solid fa-phone text-gray-700 dark:text-white text-lg"></i>
                            </div>
                            <p class="text-sm font-bold text-gray-900 dark:text-white">Telefon</p>
                        </div>
                    </a>

                    {{-- WhatsApp --}}
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $contactWhatsapp) }}" target="_blank" class="bg-white/70 dark:bg-white/5 backdrop-blur-md border border-gray-300 dark:border-white/10 rounded-xl px-4 py-3 hover:bg-green-50 dark:hover:bg-green-900/20 transition-all duration-200 block">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-white/40 dark:bg-white/10 backdrop-blur-md rounded-lg flex items-center justify-center flex-shrink-0 border border-gray-200 dark:border-white/20">
                                <i class="fa-brands fa-whatsapp text-gray-700 dark:text-white text-lg"></i>
                            </div>
                            <p class="text-sm font-bold text-gray-900 dark:text-white">WhatsApp</p>
                        </div>
                    </a>

                    {{-- E-posta --}}
                    <a href="mailto:{{ $contactEmail }}" class="bg-white/70 dark:bg-white/5 backdrop-blur-md border border-gray-300 dark:border-white/10 rounded-xl px-4 py-3 hover:bg-purple-50 dark:hover:bg-purple-900/20 transition-all duration-200 block">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-white/40 dark:bg-white/10 backdrop-blur-md rounded-lg flex items-center justify-center flex-shrink-0 border border-gray-200 dark:border-white/20">
                                <i class="fa-solid fa-envelope text-gray-700 dark:text-white text-lg"></i>
                            </div>
                            <p class="text-sm font-bold text-gray-900 dark:text-white">E-posta</p>
                        </div>
                    </a>

                    {{-- Adres (İletişim sayfasına link) --}}
                    <a href="{{ href('Page', 'show', 'iletisim') }}" class="bg-white/70 dark:bg-white/5 backdrop-blur-md border border-gray-300 dark:border-white/10 rounded-xl px-4 py-3 hover:bg-orange-50 dark:hover:bg-orange-900/20 transition-all duration-200 block">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-white/40 dark:bg-white/10 backdrop-blur-md rounded-lg flex items-center justify-center flex-shrink-0 border border-gray-200 dark:border-white/20">
                                <i class="fa-solid fa-map-marker-alt text-gray-700 dark:text-white text-lg"></i>
                            </div>
                            <p class="text-sm font-bold text-gray-900 dark:text-white">Adres</p>
                        </div>
                    </a>
                </div>
            </div>

            {{-- ========================================== --}}
            {{-- SAĞ: SOSYAL MEDYA & İLETİŞİM (col-span-5) --}}
            {{-- ========================================== --}}
            <div class="col-span-12 lg:col-span-5">
                <div class="bg-white/70 dark:bg-white/5 backdrop-blur-md border border-gray-300 dark:border-white/10 rounded-2xl p-6">
                    {{-- Header --}}
                    <div class="flex items-center gap-3 mb-4 pb-4 border-b border-gray-200 dark:border-white/10">
                        <div class="w-12 h-12 bg-white/40 dark:bg-white/10 backdrop-blur-md rounded-xl flex items-center justify-center border border-gray-200 dark:border-white/20">
                            <i class="fa-solid fa-share-nodes text-gray-700 dark:text-white text-xl"></i>
                        </div>
                        <h3 class="text-xl font-black text-gray-900 dark:text-white">Sosyal Medya & Bağlantılar</h3>
                    </div>

                    {{-- Sosyal Medya Grid --}}
                    <div class="grid grid-cols-2 gap-3">
                        {{-- Facebook --}}
                        <a href="{{ setting('social_facebook', '#') }}" target="_blank"
                           class="flex items-center gap-3 text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors py-3 px-3 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 bg-white/40 dark:bg-white/5 border border-gray-200 dark:border-white/10">
                            <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center flex-shrink-0">
                                <i class="fa-brands fa-facebook-f text-blue-600 dark:text-blue-400 text-lg"></i>
                            </div>
                            <span class="text-sm font-bold">Facebook</span>
                        </a>

                        {{-- Instagram --}}
                        <a href="{{ setting('social_instagram', '#') }}" target="_blank"
                           class="flex items-center gap-3 text-gray-700 dark:text-gray-300 hover:text-pink-600 dark:hover:text-pink-400 transition-colors py-3 px-3 rounded-lg hover:bg-pink-50 dark:hover:bg-pink-900/20 bg-white/40 dark:bg-white/5 border border-gray-200 dark:border-white/10">
                            <div class="w-10 h-10 rounded-lg bg-pink-100 dark:bg-pink-900/40 flex items-center justify-center flex-shrink-0">
                                <i class="fa-brands fa-instagram text-pink-600 dark:text-pink-400 text-lg"></i>
                            </div>
                            <span class="text-sm font-bold">Instagram</span>
                        </a>

                        {{-- LinkedIn --}}
                        <a href="{{ setting('social_linkedin', '#') }}" target="_blank"
                           class="flex items-center gap-3 text-gray-700 dark:text-gray-300 hover:text-blue-700 dark:hover:text-blue-500 transition-colors py-3 px-3 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 bg-white/40 dark:bg-white/5 border border-gray-200 dark:border-white/10">
                            <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center flex-shrink-0">
                                <i class="fa-brands fa-linkedin-in text-blue-700 dark:text-blue-500 text-lg"></i>
                            </div>
                            <span class="text-sm font-bold">LinkedIn</span>
                        </a>

                        {{-- Twitter --}}
                        <a href="{{ setting('social_twitter', '#') }}" target="_blank"
                           class="flex items-center gap-3 text-gray-700 dark:text-gray-300 hover:text-blue-500 dark:hover:text-blue-400 transition-colors py-3 px-3 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 bg-white/40 dark:bg-white/5 border border-gray-200 dark:border-white/10">
                            <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center flex-shrink-0">
                                <i class="fa-brands fa-twitter text-blue-500 dark:text-blue-400 text-lg"></i>
                            </div>
                            <span class="text-sm font-bold">Twitter</span>
                        </a>
                    </div>

                    {{-- Direkt İletişim Bilgileri --}}
                    <div class="mt-4 pt-4 border-t border-gray-200 dark:border-white/10 space-y-2">
                        <div class="flex items-center gap-3 text-gray-700 dark:text-gray-300 py-2">
                            <i class="fa-solid fa-phone text-blue-600 dark:text-blue-400 text-sm w-5"></i>
                            <span class="text-sm font-medium">{{ $contactPhone }}</span>
                        </div>
                        <div class="flex items-center gap-3 text-gray-700 dark:text-gray-300 py-2">
                            <i class="fa-brands fa-whatsapp text-green-600 dark:text-green-400 text-sm w-5"></i>
                            <span class="text-sm font-medium">{{ $contactWhatsapp }}</span>
                        </div>
                        <div class="flex items-center gap-3 text-gray-700 dark:text-gray-300 py-2">
                            <i class="fa-solid fa-envelope text-purple-600 dark:text-purple-400 text-sm w-5"></i>
                            <span class="text-sm font-medium">{{ $contactEmail }}</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

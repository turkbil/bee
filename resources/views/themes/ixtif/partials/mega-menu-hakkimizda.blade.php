{{-- Hakkımızda Hibrit Mega Menu Content --}}
<div class="bg-white dark:bg-slate-900/95 backdrop-blur-xl border-t border-gray-200 dark:border-white/10 shadow-2xl py-6 rounded-b-2xl">

        {{-- 4 Kolon Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

            {{-- KOLON 1: Kurumsal --}}
            <div class="space-y-3">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center">
                        <i class="fa-solid fa-building text-white text-sm"></i>
                    </div>
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">Kurumsal</h3>
                </div>

                <div class="space-y-1">
                    <a href="{{ href('Page', 'show', 'hakkimizda') }}"
                       class="group flex items-center gap-2 p-2 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all">
                        <i class="fa-solid fa-info-circle text-blue-600 dark:text-blue-400 text-xs"></i>
                        <div class="text-sm font-medium text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400">Hakkımızda</div>
                    </a>

                    <a href="{{ href('Page', 'show', 'vizyonumuz') }}"
                       class="group flex items-center gap-2 p-2 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all">
                        <i class="fa-solid fa-eye text-purple-600 dark:text-purple-400 text-xs"></i>
                        <div class="text-sm font-medium text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400">Vizyonumuz</div>
                    </a>

                    <a href="{{ href('Page', 'show', 'misyonumuz') }}"
                       class="group flex items-center gap-2 p-2 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all">
                        <i class="fa-solid fa-bullseye text-green-600 dark:text-green-400 text-xs"></i>
                        <div class="text-sm font-medium text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400">Misyonumuz</div>
                    </a>

                    <a href="{{ href('Page', 'show', 'referanslar') }}"
                       class="group flex items-center gap-2 p-2 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all">
                        <i class="fa-solid fa-handshake text-orange-600 dark:text-orange-400 text-xs"></i>
                        <div class="text-sm font-medium text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400">Referanslar</div>
                    </a>

                    <a href="{{ href('Page', 'show', 'kariyer') }}"
                       class="group flex items-center gap-2 p-2 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all">
                        <i class="fa-solid fa-briefcase text-pink-600 dark:text-pink-400 text-xs"></i>
                        <div class="text-sm font-medium text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400">Kariyer</div>
                    </a>
                </div>
            </div>

            {{-- KOLON 2: Yasal Mevzuat (1) --}}
            <div class="space-y-3">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-red-500 to-red-600 flex items-center justify-center">
                        <i class="fa-solid fa-scale-balanced text-white text-sm"></i>
                    </div>
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">Yasal Mevzuat</h3>
                </div>

                <div class="space-y-1">
                    <a href="{{ href('Page', 'show', 'kvkk-aydinlatma') }}"
                       class="group flex items-center gap-2 p-2 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-all">
                        <i class="fa-solid fa-shield-halved text-red-600 dark:text-red-400 text-xs"></i>
                        <div class="text-sm font-medium text-gray-900 dark:text-white group-hover:text-red-600 dark:group-hover:text-red-400">KVKK Aydınlatma</div>
                    </a>

                    <a href="{{ href('Page', 'show', 'gizlilik-politikasi') }}"
                       class="group flex items-center gap-2 p-2 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-all">
                        <i class="fa-solid fa-user-shield text-purple-600 dark:text-purple-400 text-xs"></i>
                        <div class="text-sm font-medium text-gray-900 dark:text-white group-hover:text-red-600 dark:group-hover:text-red-400">Gizlilik Politikası</div>
                    </a>

                    <a href="{{ href('Page', 'show', 'kullanim-kosullari') }}"
                       class="group flex items-center gap-2 p-2 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-all">
                        <i class="fa-solid fa-file-contract text-blue-600 dark:text-blue-400 text-xs"></i>
                        <div class="text-sm font-medium text-gray-900 dark:text-white group-hover:text-red-600 dark:group-hover:text-red-400">Kullanım Koşulları</div>
                    </a>

                    <a href="{{ href('Page', 'show', 'cerez-politikasi') }}"
                       class="group flex items-center gap-2 p-2 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-all">
                        <i class="fa-solid fa-cookie-bite text-yellow-600 dark:text-yellow-400 text-xs"></i>
                        <div class="text-sm font-medium text-gray-900 dark:text-white group-hover:text-red-600 dark:group-hover:text-red-400">Çerez Politikası</div>
                    </a>

                    <a href="{{ href('Page', 'show', 'mesafeli-satis-sozlesmesi') }}"
                       class="group flex items-center gap-2 p-2 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-all">
                        <i class="fa-solid fa-file-signature text-green-600 dark:text-green-400 text-xs"></i>
                        <div class="text-sm font-medium text-gray-900 dark:text-white group-hover:text-red-600 dark:group-hover:text-red-400">Mesafeli Satış</div>
                    </a>
                </div>
            </div>

            {{-- KOLON 3: Destek & Müşteri --}}
            <div class="space-y-3">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-green-500 to-green-600 flex items-center justify-center">
                        <i class="fa-solid fa-headset text-white text-sm"></i>
                    </div>
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">Destek & Müşteri</h3>
                </div>

                <div class="space-y-1">
                    <a href="{{ href('Page', 'show', 'iletisim') }}"
                       class="group flex items-center gap-2 p-2 rounded-lg hover:bg-green-50 dark:hover:bg-green-900/20 transition-all">
                        <i class="fa-solid fa-envelope text-green-600 dark:text-green-400 text-xs"></i>
                        <div class="text-sm font-medium text-gray-900 dark:text-white group-hover:text-green-600 dark:group-hover:text-green-400">İletişim</div>
                    </a>

                    <a href="{{ href('Page', 'show', 'sss') }}"
                       class="group flex items-center gap-2 p-2 rounded-lg hover:bg-green-50 dark:hover:bg-green-900/20 transition-all">
                        <i class="fa-solid fa-circle-question text-blue-600 dark:text-blue-400 text-xs"></i>
                        <div class="text-sm font-medium text-gray-900 dark:text-white group-hover:text-green-600 dark:group-hover:text-green-400">Sık Sorulan Sorular</div>
                    </a>

                    <a href="{{ href('Page', 'show', 'iptal-iade') }}"
                       class="group flex items-center gap-2 p-2 rounded-lg hover:bg-green-50 dark:hover:bg-green-900/20 transition-all">
                        <i class="fa-solid fa-rotate-left text-orange-600 dark:text-orange-400 text-xs"></i>
                        <div class="text-sm font-medium text-gray-900 dark:text-white group-hover:text-green-600 dark:group-hover:text-green-400">İptal & İade</div>
                    </a>

                    <a href="{{ href('Page', 'show', 'kargo-teslimat') }}"
                       class="group flex items-center gap-2 p-2 rounded-lg hover:bg-green-50 dark:hover:bg-green-900/20 transition-all">
                        <i class="fa-solid fa-truck-fast text-purple-600 dark:text-purple-400 text-xs"></i>
                        <div class="text-sm font-medium text-gray-900 dark:text-white group-hover:text-green-600 dark:group-hover:text-green-400">Kargo & Teslimat</div>
                    </a>

                    <a href="{{ href('Page', 'show', 'garanti-kosullari') }}"
                       class="group flex items-center gap-2 p-2 rounded-lg hover:bg-green-50 dark:hover:bg-green-900/20 transition-all">
                        <i class="fa-solid fa-shield-check text-red-600 dark:text-red-400 text-xs"></i>
                        <div class="text-sm font-medium text-gray-900 dark:text-white group-hover:text-green-600 dark:group-hover:text-green-400">Garanti Koşulları</div>
                    </a>
                </div>
            </div>

            {{-- KOLON 4: İletişim & Satış --}}
            <div class="space-y-3">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center">
                        <i class="fa-solid fa-phone text-white text-sm"></i>
                    </div>
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">İletişim & Satış</h3>
                </div>

                <div class="space-y-1">
                    <a href="{{ href('Page', 'show', 'bayi-basvurusu') }}"
                       class="group flex items-center gap-2 p-2 rounded-lg hover:bg-purple-50 dark:hover:bg-purple-900/20 transition-all">
                        <i class="fa-solid fa-store text-yellow-600 dark:text-yellow-400 text-xs"></i>
                        <div class="text-sm font-medium text-gray-900 dark:text-white group-hover:text-purple-600 dark:group-hover:text-purple-400">Bayi Başvurusu</div>
                    </a>

                    @php
                        $contactPhone = setting('contact_phone_1', '0216 755 3 555');
                        $contactWhatsapp = setting('contact_whatsapp_1', '0501 005 67 58');
                        $contactEmail = setting('contact_email_1', 'info@ixtif.com');
                    @endphp

                    <a href="tel:{{ str_replace(' ', '', $contactPhone) }}"
                       class="group flex items-center gap-2 p-2 rounded-lg hover:bg-purple-50 dark:hover:bg-purple-900/20 transition-all">
                        <i class="fa-solid fa-phone text-blue-600 dark:text-blue-400 text-xs"></i>
                        <div class="text-sm font-medium text-gray-900 dark:text-white group-hover:text-purple-600 dark:group-hover:text-purple-400">{{ $contactPhone }}</div>
                    </a>

                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $contactWhatsapp) }}"
                       target="_blank"
                       class="group flex items-center gap-2 p-2 rounded-lg hover:bg-purple-50 dark:hover:bg-purple-900/20 transition-all">
                        <i class="fa-brands fa-whatsapp text-green-600 dark:text-green-400 text-xs"></i>
                        <div class="text-sm font-medium text-gray-900 dark:text-white group-hover:text-purple-600 dark:group-hover:text-purple-400">WhatsApp</div>
                    </a>

                    <a href="mailto:{{ $contactEmail }}"
                       class="group flex items-center gap-2 p-2 rounded-lg hover:bg-purple-50 dark:hover:bg-purple-900/20 transition-all">
                        <i class="fa-solid fa-envelope text-purple-600 dark:text-purple-400 text-xs"></i>
                        <div class="text-sm font-medium text-gray-900 dark:text-white group-hover:text-purple-600 dark:group-hover:text-purple-400">E-posta</div>
                    </a>

                    <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                        <div class="text-xs text-gray-500 dark:text-gray-400 mb-2">Sosyal Medya</div>
                        <div class="flex gap-2">
                            <a href="{{ setting('social_facebook', '#') }}" target="_blank"
                               class="w-7 h-7 rounded-lg bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center hover:bg-blue-600 dark:hover:bg-blue-500 transition group">
                                <i class="fa-brands fa-facebook-f text-blue-600 dark:text-blue-400 group-hover:text-white text-xs"></i>
                            </a>
                            <a href="{{ setting('social_instagram', '#') }}" target="_blank"
                               class="w-7 h-7 rounded-lg bg-pink-100 dark:bg-pink-900/40 flex items-center justify-center hover:bg-pink-600 dark:hover:bg-pink-500 transition group">
                                <i class="fa-brands fa-instagram text-pink-600 dark:text-pink-400 group-hover:text-white text-xs"></i>
                            </a>
                            <a href="{{ setting('social_linkedin', '#') }}" target="_blank"
                               class="w-7 h-7 rounded-lg bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center hover:bg-blue-700 dark:hover:bg-blue-600 transition group">
                                <i class="fa-brands fa-linkedin-in text-blue-700 dark:text-blue-400 group-hover:text-white text-xs"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
</div>

{{-- Hakkımızda Hibrit Mega Menu Content --}}
<div class="bg-white dark:bg-slate-900/95 backdrop-blur-xl border-t border-gray-200 dark:border-white/10 shadow-2xl py-8 rounded-b-2xl">

        {{-- 3 Kolon Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

            {{-- KOLON 1: Kurumsal --}}
            <div class="space-y-4">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center">
                        <i class="fa-solid fa-building text-white text-lg"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Kurumsal</h3>
                </div>

                <div class="space-y-2">
                    {{-- Hakkımızda (var) --}}
                    <a href="{{ href('Page', 'show', 'hakkimizda') }}"
                       class="group flex items-center gap-3 p-3 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all">
                        <div class="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center group-hover:bg-blue-600 dark:group-hover:bg-blue-500 transition">
                            <i class="fa-solid fa-info-circle text-blue-600 dark:text-blue-400 group-hover:text-white text-sm"></i>
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400">Hakkımızda</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Firmamızı tanıyın</div>
                        </div>
                    </a>

                    {{-- Vizyonumuz --}}
                    <a href="{{ href('Page', 'show', 'vizyonumuz') }}"
                       class="group flex items-center gap-3 p-3 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all">
                        <div class="w-8 h-8 rounded-lg bg-purple-100 dark:bg-purple-900/40 flex items-center justify-center group-hover:bg-purple-600 dark:group-hover:bg-purple-500 transition">
                            <i class="fa-solid fa-eye text-purple-600 dark:text-purple-400 group-hover:text-white text-sm"></i>
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400">Vizyonumuz</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Geleceğe bakışımız</div>
                        </div>
                    </a>

                    {{-- Misyonumuz --}}
                    <a href="{{ href('Page', 'show', 'misyonumuz') }}"
                       class="group flex items-center gap-3 p-3 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all">
                        <div class="w-8 h-8 rounded-lg bg-green-100 dark:bg-green-900/40 flex items-center justify-center group-hover:bg-green-600 dark:group-hover:bg-green-500 transition">
                            <i class="fa-solid fa-bullseye text-green-600 dark:text-green-400 group-hover:text-white text-sm"></i>
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400">Misyonumuz</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Hedeflerimiz</div>
                        </div>
                    </a>

                    {{-- Referanslar --}}
                    <a href="{{ href('Page', 'show', 'referanslar') }}"
                       class="group flex items-center gap-3 p-3 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all">
                        <div class="w-8 h-8 rounded-lg bg-orange-100 dark:bg-orange-900/40 flex items-center justify-center group-hover:bg-orange-600 dark:group-hover:bg-orange-500 transition">
                            <i class="fa-solid fa-handshake text-orange-600 dark:text-orange-400 group-hover:text-white text-sm"></i>
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400">Referanslar</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">İş ortaklarımız</div>
                        </div>
                    </a>

                    {{-- Kariyer --}}
                    <a href="{{ href('Page', 'show', 'kariyer') }}"
                       class="group flex items-center gap-3 p-3 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all">
                        <div class="w-8 h-8 rounded-lg bg-pink-100 dark:bg-pink-900/40 flex items-center justify-center group-hover:bg-pink-600 dark:group-hover:bg-pink-500 transition">
                            <i class="fa-solid fa-briefcase text-pink-600 dark:text-pink-400 group-hover:text-white text-sm"></i>
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400">Kariyer</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Ekibimize katılın</div>
                        </div>
                    </a>
                </div>
            </div>

            {{-- KOLON 2: Yasal Mevzuat --}}
            <div class="space-y-4">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-red-500 to-red-600 flex items-center justify-center">
                        <i class="fa-solid fa-scale-balanced text-white text-lg"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Yasal Mevzuat</h3>
                </div>

                <div class="space-y-2">
                    {{-- KVKK Aydınlatma --}}
                    <a href="{{ href('Page', 'show', 'kvkk-aydinlatma') }}"
                       class="group flex items-center gap-3 p-3 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-all">
                        <div class="w-8 h-8 rounded-lg bg-red-100 dark:bg-red-900/40 flex items-center justify-center group-hover:bg-red-600 dark:group-hover:bg-red-500 transition">
                            <i class="fa-solid fa-shield-halved text-red-600 dark:text-red-400 group-hover:text-white text-sm"></i>
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-gray-900 dark:text-white group-hover:text-red-600 dark:group-hover:text-red-400">KVKK Aydınlatma</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Kişisel veri koruması</div>
                        </div>
                    </a>

                    {{-- Gizlilik Politikası --}}
                    <a href="{{ href('Page', 'show', 'gizlilik-politikasi') }}"
                       class="group flex items-center gap-3 p-3 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-all">
                        <div class="w-8 h-8 rounded-lg bg-purple-100 dark:bg-purple-900/40 flex items-center justify-center group-hover:bg-purple-600 dark:group-hover:bg-purple-500 transition">
                            <i class="fa-solid fa-user-shield text-purple-600 dark:text-purple-400 group-hover:text-white text-sm"></i>
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-gray-900 dark:text-white group-hover:text-red-600 dark:group-hover:text-red-400">Gizlilik Politikası</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Gizlilik taahhütlerimiz</div>
                        </div>
                    </a>

                    {{-- Kullanım Koşulları --}}
                    <a href="{{ href('Page', 'show', 'kullanim-kosullari') }}"
                       class="group flex items-center gap-3 p-3 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-all">
                        <div class="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center group-hover:bg-blue-600 dark:group-hover:bg-blue-500 transition">
                            <i class="fa-solid fa-file-contract text-blue-600 dark:text-blue-400 group-hover:text-white text-sm"></i>
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-gray-900 dark:text-white group-hover:text-red-600 dark:group-hover:text-red-400">Kullanım Koşulları</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Hizmet şartlarımız</div>
                        </div>
                    </a>

                    {{-- Çerez Politikası --}}
                    <a href="{{ href('Page', 'show', 'cerez-politikasi') }}"
                       class="group flex items-center gap-3 p-3 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-all">
                        <div class="w-8 h-8 rounded-lg bg-yellow-100 dark:bg-yellow-900/40 flex items-center justify-center group-hover:bg-yellow-600 dark:group-hover:bg-yellow-500 transition">
                            <i class="fa-solid fa-cookie-bite text-yellow-600 dark:text-yellow-400 group-hover:text-white text-sm"></i>
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-gray-900 dark:text-white group-hover:text-red-600 dark:group-hover:text-red-400">Çerez Politikası</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Cookie kullanımı</div>
                        </div>
                    </a>

                    {{-- Mesafeli Satış Sözleşmesi --}}
                    <a href="{{ href('Page', 'show', 'mesafeli-satis-sozlesmesi') }}"
                       class="group flex items-center gap-3 p-3 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-all">
                        <div class="w-8 h-8 rounded-lg bg-green-100 dark:bg-green-900/40 flex items-center justify-center group-hover:bg-green-600 dark:group-hover:bg-green-500 transition">
                            <i class="fa-solid fa-file-signature text-green-600 dark:text-green-400 group-hover:text-white text-sm"></i>
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-gray-900 dark:text-white group-hover:text-red-600 dark:group-hover:text-red-400">Mesafeli Satış Sözleşmesi</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Online alışveriş sözleşmesi</div>
                        </div>
                    </a>
                </div>
            </div>

            {{-- KOLON 3: Destek & İletişim --}}
            <div class="space-y-4">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-green-500 to-green-600 flex items-center justify-center">
                        <i class="fa-solid fa-headset text-white text-lg"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Destek & İletişim</h3>
                </div>

                <div class="space-y-2">
                    {{-- İletişim (var) --}}
                    <a href="{{ href('Page', 'show', 'iletisim') }}"
                       class="group flex items-center gap-3 p-3 rounded-lg hover:bg-green-50 dark:hover:bg-green-900/20 transition-all">
                        <div class="w-8 h-8 rounded-lg bg-green-100 dark:bg-green-900/40 flex items-center justify-center group-hover:bg-green-600 dark:group-hover:bg-green-500 transition">
                            <i class="fa-solid fa-envelope text-green-600 dark:text-green-400 group-hover:text-white text-sm"></i>
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-gray-900 dark:text-white group-hover:text-green-600 dark:group-hover:text-green-400">İletişim</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Bize ulaşın</div>
                        </div>
                    </a>

                    {{-- SSS --}}
                    <a href="{{ href('Page', 'show', 'sss') }}"
                       class="group flex items-center gap-3 p-3 rounded-lg hover:bg-green-50 dark:hover:bg-green-900/20 transition-all">
                        <div class="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center group-hover:bg-blue-600 dark:group-hover:bg-blue-500 transition">
                            <i class="fa-solid fa-circle-question text-blue-600 dark:text-blue-400 group-hover:text-white text-sm"></i>
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-gray-900 dark:text-white group-hover:text-green-600 dark:group-hover:text-green-400">Sık Sorulan Sorular</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Merak edilenler</div>
                        </div>
                    </a>

                    {{-- İptal & İade --}}
                    <a href="{{ href('Page', 'show', 'iptal-iade') }}"
                       class="group flex items-center gap-3 p-3 rounded-lg hover:bg-green-50 dark:hover:bg-green-900/20 transition-all">
                        <div class="w-8 h-8 rounded-lg bg-orange-100 dark:bg-orange-900/40 flex items-center justify-center group-hover:bg-orange-600 dark:group-hover:bg-orange-500 transition">
                            <i class="fa-solid fa-rotate-left text-orange-600 dark:text-orange-400 group-hover:text-white text-sm"></i>
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-gray-900 dark:text-white group-hover:text-green-600 dark:group-hover:text-green-400">İptal & İade</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">İade koşulları</div>
                        </div>
                    </a>

                    {{-- Kargo & Teslimat --}}
                    <a href="{{ href('Page', 'show', 'kargo-teslimat') }}"
                       class="group flex items-center gap-3 p-3 rounded-lg hover:bg-green-50 dark:hover:bg-green-900/20 transition-all">
                        <div class="w-8 h-8 rounded-lg bg-purple-100 dark:bg-purple-900/40 flex items-center justify-center group-hover:bg-purple-600 dark:group-hover:bg-purple-500 transition">
                            <i class="fa-solid fa-truck-fast text-purple-600 dark:text-purple-400 group-hover:text-white text-sm"></i>
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-gray-900 dark:text-white group-hover:text-green-600 dark:group-hover:text-green-400">Kargo & Teslimat</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Gönderim bilgileri</div>
                        </div>
                    </a>

                    {{-- Garanti Koşulları --}}
                    <a href="{{ href('Page', 'show', 'garanti-kosullari') }}"
                       class="group flex items-center gap-3 p-3 rounded-lg hover:bg-green-50 dark:hover:bg-green-900/20 transition-all">
                        <div class="w-8 h-8 rounded-lg bg-red-100 dark:bg-red-900/40 flex items-center justify-center group-hover:bg-red-600 dark:group-hover:bg-red-500 transition">
                            <i class="fa-solid fa-shield-check text-red-600 dark:text-red-400 group-hover:text-white text-sm"></i>
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-gray-900 dark:text-white group-hover:text-green-600 dark:group-hover:text-green-400">Garanti Koşulları</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Garanti kapsamı</div>
                        </div>
                    </a>

                    {{-- Bayi Başvurusu --}}
                    <a href="{{ href('Page', 'show', 'bayi-basvurusu') }}"
                       class="group flex items-center gap-3 p-3 rounded-lg hover:bg-green-50 dark:hover:bg-green-900/20 transition-all">
                        <div class="w-8 h-8 rounded-lg bg-yellow-100 dark:bg-yellow-900/40 flex items-center justify-center group-hover:bg-yellow-600 dark:group-hover:bg-yellow-500 transition">
                            <i class="fa-solid fa-store text-yellow-600 dark:text-yellow-400 group-hover:text-white text-sm"></i>
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-gray-900 dark:text-white group-hover:text-green-600 dark:group-hover:text-green-400">Bayi Başvurusu</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Satış ortağı olun</div>
                        </div>
                    </a>
                </div>
            </div>

        </div>

        {{-- Alt Bilgi Banner (Opsiyonel) --}}
        <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 via-purple-500 to-pink-500 flex items-center justify-center">
                        <i class="fa-solid fa-forklift text-white text-xl"></i>
                    </div>
                    <div>
                        <div class="text-sm font-bold text-gray-900 dark:text-white">{{ setting('company_name', 'iXtif') }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ setting('site_slogan', 'Endüstriyel Ekipman Uzmanı') }}</div>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    @php
                        $contactPhone = setting('contact_phone_1', '0216 755 3 555');
                        $contactWhatsapp = setting('contact_whatsapp_1', '0501 005 67 58');
                    @endphp
                    <a href="tel:{{ str_replace(' ', '', $contactPhone) }}"
                       class="flex items-center gap-2 px-4 py-2 bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 rounded-lg hover:bg-blue-600 hover:text-white dark:hover:bg-blue-500 transition text-sm font-medium">
                        <i class="fa-solid fa-phone"></i>
                        {{ $contactPhone }}
                    </a>
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $contactWhatsapp) }}"
                       target="_blank"
                       class="flex items-center gap-2 px-4 py-2 bg-green-100 dark:bg-green-900/40 text-green-600 dark:text-green-400 rounded-lg hover:bg-green-600 hover:text-white dark:hover:bg-green-500 transition text-sm font-medium">
                        <i class="fa-brands fa-whatsapp"></i>
                        WhatsApp
                    </a>
                </div>
            </div>
        </div>
</div>

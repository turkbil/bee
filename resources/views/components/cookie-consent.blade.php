{{-- Cookie Consent Component - GDPR Uyumlu --}}
<div x-data="cookieConsentApp()">

    {{-- Cookie Consent Banner --}}
    <div x-show="showCookieConsent"
         x-transition:enter="transition ease-out duration-500"
         x-transition:enter-start="opacity-0 translate-y-8"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="fixed bottom-6 left-6 max-w-sm bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-6 border border-gray-200 dark:border-gray-700 z-50">

        <div class="flex items-center gap-4">
            <div class="text-3xl">🍪</div>
            <div class="flex-1">
                <h3 class="font-bold text-gray-900 dark:text-white text-lg mb-2">Çerezler</h3>
                <p class="text-sm text-gray-600 dark:text-gray-300 mb-3">Bu site, hizmet kalitesini artırmak amacıyla çerez kullanmaktadır.</p>
            </div>
            {{-- Timer + Buttons - Tek Satır --}}
            <div class="flex items-center gap-2">
                <button @click="showModal = true; stopAutoAcceptTimer()"
                        class="text-xs px-3 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors whitespace-nowrap">
                    Ayarlar
                </button>
                <div class="flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 py-2 rounded-lg whitespace-nowrap">
                    <i class="fa-solid fa-clock"></i>
                    <span x-text="timeRemaining + 's'"></span>
                </div>
                <button @click="acceptAll()"
                        class="text-xs px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors font-medium whitespace-nowrap">
                    Kabul Et
                </button>
            </div>
        </div>
    </div>

    {{-- Cookie Preferences Modal --}}
    <div x-show="showModal"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         @click.self="showModal = false"
         class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[60] flex items-center justify-center p-4">

        <div x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-90"
             x-transition:enter-end="opacity-100 scale-100"
             class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-lg w-full p-6 max-h-[90vh] overflow-y-auto">

            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Çerez Tercihleri</h2>
                <button @click="showModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>

            <div class="space-y-4 mb-6">
                {{-- Necessary Cookies --}}
                <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-xl">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-shield-check text-green-600 text-xl"></i>
                            <div>
                                <h3 class="font-semibold text-gray-900 dark:text-white">Zorunlu Çerezler</h3>
                                <p class="text-xs text-gray-600 dark:text-gray-400">Her zaman aktif</p>
                            </div>
                        </div>
                        <div class="w-12 h-6 bg-green-500 rounded-full relative">
                            <div class="absolute right-1 top-1 w-4 h-4 bg-white rounded-full"></div>
                        </div>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 ml-9">Sitenin temel işlevselliği için gereklidir.</p>
                </div>

                {{-- Functional Cookies --}}
                <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-xl">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-sliders text-blue-600 text-xl"></i>
                            <div>
                                <h3 class="font-semibold text-gray-900 dark:text-white">Fonksiyonel Çerezler</h3>
                                <p class="text-xs text-gray-600 dark:text-gray-400">Kullanıcı deneyimini iyileştirir</p>
                            </div>
                        </div>
                        <label class="relative inline-block w-12 h-6 cursor-pointer">
                            <input type="checkbox" x-model="preferences.functional" class="sr-only peer">
                            <div class="w-12 h-6 bg-gray-300 dark:bg-gray-600 peer-checked:bg-blue-600 rounded-full transition-colors"></div>
                            <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition-transform peer-checked:translate-x-6"></div>
                        </label>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 ml-9">Kullanıcı tercihlerinizi kaydetmek için kullanılır.</p>
                </div>

                {{-- Analytics Cookies --}}
                <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-xl">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-chart-line text-purple-600 text-xl"></i>
                            <div>
                                <h3 class="font-semibold text-gray-900 dark:text-white">Analitik Çerezler</h3>
                                <p class="text-xs text-gray-600 dark:text-gray-400">Site trafiğini analiz eder</p>
                            </div>
                        </div>
                        <label class="relative inline-block w-12 h-6 cursor-pointer">
                            <input type="checkbox" x-model="preferences.analytics" class="sr-only peer">
                            <div class="w-12 h-6 bg-gray-300 dark:bg-gray-600 peer-checked:bg-purple-600 rounded-full transition-colors"></div>
                            <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition-transform peer-checked:translate-x-6"></div>
                        </label>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 ml-9">Site performansını ölçmek ve iyileştirmek için kullanılır.</p>
                </div>

                {{-- Marketing Cookies --}}
                <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-xl">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-bullhorn text-orange-600 text-xl"></i>
                            <div>
                                <h3 class="font-semibold text-gray-900 dark:text-white">Pazarlama Çerezleri</h3>
                                <p class="text-xs text-gray-600 dark:text-gray-400">Hedefli reklam sunar</p>
                            </div>
                        </div>
                        <label class="relative inline-block w-12 h-6 cursor-pointer">
                            <input type="checkbox" x-model="preferences.marketing" class="sr-only peer">
                            <div class="w-12 h-6 bg-gray-300 dark:bg-gray-600 peer-checked:bg-orange-600 rounded-full transition-colors"></div>
                            <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition-transform peer-checked:translate-x-6"></div>
                        </label>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 ml-9">İlgi alanlarınıza uygun içerik sunmak için kullanılır.</p>
                </div>
            </div>

            <div class="flex gap-3">
                <button @click="savePreferences()"
                        class="flex-1 py-3 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-900 dark:text-white rounded-xl transition-colors font-semibold">
                    Seçili Olanları Kaydet
                </button>
                <button @click="acceptAll()"
                        class="flex-1 py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white rounded-xl transition-all font-semibold">
                    Tümünü Kabul Et
                </button>
            </div>

            <p class="text-xs text-gray-500 dark:text-gray-400 text-center mt-4">
                Daha fazla bilgi için <a href="/privacy-policy" class="text-blue-600 dark:text-blue-400 hover:underline">Gizlilik Politikası</a> ve
                <a href="/cookie-policy" class="text-blue-600 dark:text-blue-400 hover:underline">Çerez Politikası</a> sayfalarını ziyaret edebilirsiniz.
            </p>
        </div>
    </div>

</div>

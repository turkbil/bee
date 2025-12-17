{{-- 🍪 Cookie Consent - Design 2 (Compact Modern) --}}
<div x-data="{
    showCookieConsent: false,
    showModal: false,
    preferences: {
        necessary: true,
        functional: false,
        analytics: false,
        marketing: false
    },
    init() {
        // Cookie kontrolü
        const consent = this.getCookie('muzibu_cookie_consent');
        if (!consent) {
            this.showCookieConsent = true;
        } else {
            // Mevcut tercihleri yükle
            try {
                this.preferences = JSON.parse(decodeURIComponent(consent));
            } catch (e) {
                console.error('Cookie consent parse error:', e);
            }
        }
    },
    getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
        return null;
    },
    setCookie(name, value, days = 365) {
        const date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        const expires = `expires=${date.toUTCString()}`;
        document.cookie = `${name}=${value};${expires};path=/;SameSite=Lax`;
    },
    acceptAll() {
        this.preferences = { necessary: true, functional: true, analytics: true, marketing: true };
        this.saveConsent();
    },
    rejectAll() {
        this.preferences = { necessary: true, functional: false, analytics: false, marketing: false };
        this.saveConsent();
    },
    savePreferences() {
        this.saveConsent();
    },
    saveConsent() {
        // Cookie olarak kaydet (1 yıl süreyle)
        const consentValue = encodeURIComponent(JSON.stringify(this.preferences));
        this.setCookie('muzibu_cookie_consent', consentValue, 365);

        this.showCookieConsent = false;
        this.showModal = false;

        // Toast göster
        if (window.muzibuToast) {
            muzibuToast('Tercihleriniz kaydedildi', 'success');
        }
    }
}" x-cloak>

    {{-- Cookie Consent Banner - Muzibu Style --}}
    <div x-show="showCookieConsent"
         x-transition:enter="transition ease-out duration-500"
         x-transition:enter-start="opacity-0 translate-y-8"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="fixed bottom-6 left-6 max-w-xs bg-gradient-to-br from-black/95 to-muzibu-gray/95 backdrop-blur-xl rounded-xl shadow-2xl p-5 border border-white/10 z-[9999]"
         style="z-index: 99999;">

        <div class="text-white">
            <div class="flex items-center gap-2 mb-2">
                <i class="fa-solid fa-cookie-bite text-muzibu-coral text-xl"></i>
                <h3 class="font-bold text-lg">Çerezler</h3>
            </div>
            <p class="text-sm text-muzibu-text-gray mb-4">Deneyimi geliştirmek için çerezler kullanılır.</p>

            <div class="space-y-2">
                <button @click="acceptAll()"
                        class="w-full text-sm py-2.5 bg-muzibu-coral hover:bg-muzibu-coral-dark text-black rounded-lg transition-all font-medium">
                    Kabul Et
                </button>
                <div class="flex gap-2">
                    <button @click="showModal = true"
                            class="flex-1 text-xs py-2 text-muzibu-text-gray hover:text-white border border-white/20 hover:border-white/40 rounded-lg transition-colors">
                        Ayarlar
                    </button>
                    <button @click="rejectAll()"
                            class="flex-1 text-xs py-2 text-muzibu-text-gray hover:text-white border border-white/20 hover:border-white/40 rounded-lg transition-colors">
                        Reddet
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Cookie Preferences Modal - Muzibu Style --}}
    <div x-show="showModal"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         class="fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4"
         style="z-index: 100000;"
         @click.self="showModal = false">

        <div @click.away="showModal = false"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-90"
             x-transition:enter-end="opacity-100 scale-100"
             class="bg-gradient-to-br from-black to-muzibu-gray border border-white/10 rounded-2xl shadow-2xl max-w-lg w-full p-6 max-h-[90vh] overflow-y-auto">

            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-white">Çerez Tercihleri</h2>
                <button @click="showModal = false" class="text-muzibu-text-gray hover:text-white transition-colors">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>

            <div class="space-y-4 mb-6">
                {{-- Necessary Cookies --}}
                <div class="p-4 bg-white/5 hover:bg-white/10 transition-colors rounded-xl border border-white/10">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-shield-check text-green-500 text-xl"></i>
                            <div>
                                <h3 class="font-semibold text-white">Zorunlu Çerezler</h3>
                                <p class="text-xs text-muzibu-text-gray">Her zaman aktif</p>
                            </div>
                        </div>
                        <div class="w-12 h-6 bg-green-500 rounded-full relative">
                            <div class="absolute right-1 top-1 w-4 h-4 bg-black rounded-full"></div>
                        </div>
                    </div>
                    <p class="text-sm text-muzibu-text-gray ml-9">Sitenin temel işlevselliği için gereklidir.</p>
                </div>

                {{-- Functional Cookies --}}
                <div class="p-4 bg-white/5 hover:bg-white/10 transition-colors rounded-xl border border-white/10">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-sliders text-muzibu-coral text-xl"></i>
                            <div>
                                <h3 class="font-semibold text-white">Fonksiyonel Çerezler</h3>
                                <p class="text-xs text-muzibu-text-gray">Kullanıcı deneyimini iyileştirir</p>
                            </div>
                        </div>
                        <label class="relative inline-block w-12 h-6 cursor-pointer">
                            <input type="checkbox" x-model="preferences.functional" class="sr-only peer">
                            <div class="w-12 h-6 bg-white/10 peer-checked:bg-muzibu-coral rounded-full transition-colors"></div>
                            <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition-transform peer-checked:translate-x-6"></div>
                        </label>
                    </div>
                    <p class="text-sm text-muzibu-text-gray ml-9">Kullanıcı tercihlerinizi kaydetmek için kullanılır.</p>
                </div>

                {{-- Analytics Cookies --}}
                <div class="p-4 bg-white/5 hover:bg-white/10 transition-colors rounded-xl border border-white/10">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-chart-line text-blue-500 text-xl"></i>
                            <div>
                                <h3 class="font-semibold text-white">Analitik Çerezler</h3>
                                <p class="text-xs text-muzibu-text-gray">Site trafiğini analiz eder</p>
                            </div>
                        </div>
                        <label class="relative inline-block w-12 h-6 cursor-pointer">
                            <input type="checkbox" x-model="preferences.analytics" class="sr-only peer">
                            <div class="w-12 h-6 bg-white/10 peer-checked:bg-blue-500 rounded-full transition-colors"></div>
                            <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition-transform peer-checked:translate-x-6"></div>
                        </label>
                    </div>
                    <p class="text-sm text-muzibu-text-gray ml-9">Site performansını ölçmek ve iyileştirmek için kullanılır.</p>
                </div>

                {{-- Marketing Cookies --}}
                <div class="p-4 bg-white/5 hover:bg-white/10 transition-colors rounded-xl border border-white/10">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-bullhorn text-orange-500 text-xl"></i>
                            <div>
                                <h3 class="font-semibold text-white">Pazarlama Çerezleri</h3>
                                <p class="text-xs text-muzibu-text-gray">Hedefli reklam sunar</p>
                            </div>
                        </div>
                        <label class="relative inline-block w-12 h-6 cursor-pointer">
                            <input type="checkbox" x-model="preferences.marketing" class="sr-only peer">
                            <div class="w-12 h-6 bg-white/10 peer-checked:bg-orange-500 rounded-full transition-colors"></div>
                            <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition-transform peer-checked:translate-x-6"></div>
                        </label>
                    </div>
                    <p class="text-sm text-muzibu-text-gray ml-9">İlgi alanlarınıza uygun içerik sunmak için kullanılır.</p>
                </div>
            </div>

            <div class="flex gap-3">
                <button @click="savePreferences()"
                        class="flex-1 py-3 bg-white/10 hover:bg-white/20 text-white rounded-xl transition-colors font-semibold border border-white/20">
                    Seçili Olanları Kaydet
                </button>
                <button @click="acceptAll()"
                        class="flex-1 py-3 bg-muzibu-coral hover:bg-muzibu-coral-dark text-black rounded-xl transition-all font-semibold">
                    Tümünü Kabul Et
                </button>
            </div>

            <p class="text-xs text-muzibu-text-gray text-center mt-4">
                Daha fazla bilgi için <a href="/gizlilik-politikasi" class="text-muzibu-coral hover:text-muzibu-coral-light hover:underline transition-colors">Gizlilik Politikası</a> ve
                <a href="/cerez-politikasi" class="text-muzibu-coral hover:text-muzibu-coral-light hover:underline transition-colors">Çerez Politikası</a> sayfalarını ziyaret edebilirsiniz.
            </p>
        </div>
    </div>

</div>

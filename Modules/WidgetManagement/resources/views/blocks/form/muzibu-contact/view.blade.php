{{-- Muzibu Split Screen Contact Form - Updated Layout --}}
<style>
    .glass {
        background: rgba(255, 255, 255, 0.03);
        backdrop-filter: blur(40px);
        -webkit-backdrop-filter: blur(40px);
    }

    @keyframes slide-in-left {
        from { transform: translateX(-100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }

    @keyframes slide-in-right {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }

    .animate-slide-in-left { animation: slide-in-left 0.8s ease-out; }
    .animate-slide-in-right { animation: slide-in-right 0.8s ease-out; }
</style>

<!-- Vertical Layout Container -->
<div class="flex flex-col" x-data="{
    formData: { name: '', email: '', phone: '', subject: '', message: '', marketing_consent: null, privacy_accepted: false },
    isSubmitting: false,
    showSuccess: false,
    activeInfo: null,
    showPopup: false,
    popupTitle: '',
    popupContent: '',
    isLoadingPopup: false,
    async openPopup(pageId) {
        this.isLoadingPopup = true;
        this.popupTitle = 'Yükleniyor...';
        this.popupContent = '<div class=\'flex items-center justify-center py-12\'><i class=\'fa-solid fa-spinner fa-spin text-4xl text-purple-600\'></i></div>';
        this.showPopup = true;

        try {
            const response = await fetch(`/api/v1/pages/by-id/${pageId}`);
            const data = await response.json();

            if (data.success) {
                this.popupTitle = data.data.title;
                this.popupContent = data.data.body || '<p class=\'text-gray-500 dark:text-gray-400\'>Bu sayfa için içerik henüz eklenmemiştir.</p>';
            } else {
                this.popupTitle = 'Hata';
                this.popupContent = '<p class=\'text-red-600 dark:text-red-400\'>Sayfa içeriği yüklenemedi.</p>';
            }
        } catch (error) {
            this.popupTitle = 'Hata';
            this.popupContent = '<p class=\'text-red-600 dark:text-red-400\'>Bir hata oluştu. Lütfen daha sonra tekrar deneyin.</p>';
        } finally {
            this.isLoadingPopup = false;
        }
    },
    closePopup() {
        this.showPopup = false;
    },
    submitForm() {
        if (!this.formData.privacy_accepted) {
            alert('Lütfen İletişim Formu Aydınlatma Metni\'ni kabul ediniz.');
            return;
        }
        this.isSubmitting = true;
        setTimeout(() => {
            this.isSubmitting = false;
            this.showSuccess = true;
            setTimeout(() => { this.showSuccess = false; }, 4000);
            this.formData = { name: '', email: '', phone: '', subject: '', message: '', marketing_consent: null, privacy_accepted: false };
        }, 1500);
    }
}">

    <!-- Top Section: Contact Information -->
    <div class="w-full relative bg-gray-50 dark:bg-gray-900 p-0 lg:p-16 animate-slide-in-left">

        <!-- Background Decoration (Music Theme) -->
        <div class="absolute inset-0 opacity-10 dark:opacity-5 pointer-events-none">
            <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-purple-500 dark:bg-purple-600 rounded-full blur-3xl animate-pulse"></div>
            <div class="absolute bottom-1/4 right-1/4 w-80 h-80 bg-pink-400 dark:bg-pink-500 rounded-full blur-3xl animate-pulse" style="animation-delay: 1s;"></div>
        </div>

        <div class="relative z-10 w-full max-w-6xl mx-auto space-y-8">

            <!-- Header -->
            <div class="mb-8">
                <div class="inline-flex items-center gap-2 px-4 py-2 bg-white/90 dark:bg-gray-800/90 backdrop-blur-sm rounded-full border border-gray-200 dark:border-gray-700 mb-6">
                    <div class="w-2 h-2 bg-purple-500 dark:bg-purple-400 rounded-full animate-pulse"></div>
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">7/24 Müzik Desteği</span>
                </div>

                <h1 class="text-5xl lg:text-6xl font-black mb-6 leading-tight">
                    <span class="text-gray-900 dark:text-white">Müziğinizle</span><br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-pink-600">Buluşalım</span>
                </h1>

                <p class="text-xl text-gray-700 dark:text-gray-300 leading-relaxed">
                    İşletmenize özel müzik çözümleri için bize ulaşın. Telifsiz ve yasal müziklerle işinizi güçlendirin.
                </p>
            </div>

            <!-- Contact Information Cards -->
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">

                <!-- Address -->
                <div @mouseenter="activeInfo = 'address'" @mouseleave="activeInfo = null"
                     class="bg-white dark:bg-gray-800 rounded-2xl px-4 py-8 border border-gray-200 dark:border-gray-700 hover:border-purple-500 dark:hover:border-purple-400 transition-all duration-300 shadow-sm hover:shadow-lg"
                     :class="activeInfo === 'address' ? 'scale-105 border-purple-500 dark:border-purple-400 shadow-xl shadow-purple-500/20 dark:shadow-purple-400/20' : ''">
                    <div class="flex flex-col items-center text-center space-y-4">
                        <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-purple-500 to-pink-500 dark:from-purple-600 dark:to-pink-600 flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid fa-location-dot text-3xl text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-3">ADRES</h3>
                            <p class="text-gray-700 dark:text-gray-300 leading-relaxed text-sm">
                                Atatürk Mah. Ertuğrul Gazi Sok.<br>
                                Metropol İstanbul C1 Blok No: 2<br>
                                İç Kapı No: 376<br>
                                Ataşehir / İSTANBUL
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Phone & WhatsApp -->
                <div @mouseenter="activeInfo = 'phone'" @mouseleave="activeInfo = null"
                     class="bg-white dark:bg-gray-800 rounded-2xl px-4 py-8 border border-gray-200 dark:border-gray-700 hover:border-green-500 dark:hover:border-green-400 transition-all duration-300 cursor-pointer group shadow-sm hover:shadow-lg"
                     :class="activeInfo === 'phone' ? 'scale-105 border-green-500 dark:border-green-400 shadow-xl shadow-green-500/20 dark:shadow-green-400/20' : ''">
                    <div class="flex flex-col items-center text-center space-y-4">
                        <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-green-500 to-green-600 dark:from-green-600 dark:to-green-700 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                            <i class="fa-brands fa-whatsapp text-3xl text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-3">TELEFON - WHATSAPP İLETİŞİM</h3>
                            <a href="https://wa.me/905358704897"
                               target="_blank"
                               class="text-green-600 dark:text-green-400 hover:text-green-700 dark:hover:text-green-300 text-lg font-semibold">
                                0535 870 48 97
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Email -->
                <div @mouseenter="activeInfo = 'email'" @mouseleave="activeInfo = null"
                     class="bg-white dark:bg-gray-800 rounded-2xl px-4 py-8 border border-gray-200 dark:border-gray-700 hover:border-purple-500 dark:hover:border-purple-400 transition-all duration-300 cursor-pointer group shadow-sm hover:shadow-lg"
                     :class="activeInfo === 'email' ? 'scale-105 border-purple-500 dark:border-purple-400 shadow-xl shadow-purple-500/20 dark:shadow-purple-400/20' : ''">
                    <div class="flex flex-col items-center text-center space-y-4">
                        <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-purple-500 to-pink-500 dark:from-purple-600 dark:to-pink-600 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-envelope text-3xl text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-3">E-MAIL</h3>
                            <a href="mailto:merhaba@muzibu.com" class="text-purple-600 dark:text-purple-400 hover:text-purple-700 dark:hover:text-purple-300 text-lg font-semibold">
                                merhaba@muzibu.com
                            </a>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Copyright Info - Full Width -->
            <div class="bg-gradient-to-br from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 rounded-2xl p-8 border border-purple-200 dark:border-purple-800">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-purple-500 to-pink-500 hidden md:flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid fa-shield-check text-xl text-white"></i>
                        </div>
                        <div class="flex-1 pr-4 md:pr-4">
                            <h3 class="text-base font-bold text-gray-900 dark:text-white mb-3">Telifsiz ve Yasal Müzik</h3>
                            <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed mb-3">
                                Muzibu'da yer alan tüm müzikler, telifsiz ve yasal olarak işletmelerde kullanıma uygundur.
                            </p>
                            <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                                Eğer dinlediğiniz bir parçanın <strong>sözlerini, müziğini ya da tüm haklarını satın almak</strong> istiyorsanız, bizimle iletişime geçmeniz yeterli.
                            </p>
                            <div class="mt-4 flex items-center gap-2 text-purple-600 dark:text-purple-400">
                                <i class="fa-solid fa-envelope"></i>
                                <a href="mailto:merhaba@muzibu.com" class="font-semibold hover:underline">
                                    merhaba@muzibu.com
                                </a>
                            </div>
                        </div>
                    </div>
            </div>

        </div>
    </div>

    <!-- Bottom Section: Contact Form -->
    <div class="w-full relative bg-white dark:bg-gray-800 p-8 lg:p-16 animate-slide-in-right flex items-center justify-center">

        <div class="w-full max-w-6xl mx-auto">

            <!-- Contact Form -->
            <div class="bg-white dark:bg-gray-800 backdrop-blur-md rounded-2xl p-8 lg:p-10 border border-gray-200 dark:border-gray-700 shadow-2xl relative">

                <!-- Success Message -->
                <div x-show="showSuccess"
                     x-transition
                     class="absolute inset-0 flex items-center justify-center bg-gradient-to-br from-purple-500 to-pink-500 backdrop-blur-lg rounded-2xl z-30">
                    <div class="text-center p-12">
                        <i class="fa-solid fa-circle-check text-7xl text-white mb-6"></i>
                        <h3 class="text-3xl font-black text-white mb-4">Harika!</h3>
                        <p class="text-white/90 text-lg">Mesajınız başarıyla gönderildi.</p>
                        <p class="text-white/70 mt-2">En kısa sürede size dönüş yapacağız.</p>
                    </div>
                </div>

                <!-- Form Header -->
                <div class="mb-8">
                    <h2 class="text-4xl font-black mb-3">
                        <span class="text-gray-900 dark:text-white">Mesaj </span>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-pink-600">Gönderin</span>
                    </h2>
                    <p class="text-gray-600 dark:text-gray-400">
                        Formu doldurun, size 24 saat içinde geri dönelim.
                    </p>
                </div>

                <!-- Form -->
                <form @submit.prevent="submitForm" class="space-y-6">

                    <!-- Name -->
                    <div class="group">
                        <label class="block text-sm font-bold mb-2 text-gray-700 dark:text-gray-300 group-focus-within:text-purple-600 dark:group-focus-within:text-purple-400 transition-colors">
                            Ad Soyad *
                        </label>
                        <input type="text" x-model="formData.name" required
                               class="w-full px-5 py-4 bg-gray-50 dark:bg-gray-900 border-2 border-gray-300 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:border-purple-500 dark:focus:border-purple-400 focus:ring-4 focus:ring-purple-500/20 dark:focus:ring-purple-400/20 transition-all"
                               placeholder="Adınız ve soyadınız">
                    </div>

                    <!-- Email & Phone -->
                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="group">
                            <label class="block text-sm font-bold mb-2 text-gray-700 dark:text-gray-300 group-focus-within:text-purple-600 dark:group-focus-within:text-purple-400 transition-colors">
                                E-Posta *
                            </label>
                            <input type="email" x-model="formData.email" required
                                   class="w-full px-5 py-4 bg-gray-50 dark:bg-gray-900 border-2 border-gray-300 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:border-purple-500 dark:focus:border-purple-400 focus:ring-4 focus:ring-purple-500/20 dark:focus:ring-purple-400/20 transition-all"
                                   placeholder="ornek@email.com">
                        </div>

                        <div class="group">
                            <label class="block text-sm font-bold mb-2 text-gray-700 dark:text-gray-300 group-focus-within:text-purple-600 dark:group-focus-within:text-purple-400 transition-colors">
                                Telefon
                            </label>
                            <input type="tel" x-model="formData.phone"
                                   class="w-full px-5 py-4 bg-gray-50 dark:bg-gray-900 border-2 border-gray-300 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:border-purple-500 dark:focus:border-purple-400 focus:ring-4 focus:ring-purple-500/20 dark:focus:ring-purple-400/20 transition-all"
                                   placeholder="0 (5XX) XXX XX XX">
                        </div>
                    </div>

                    <!-- Subject -->
                    <div class="group">
                        <label class="block text-sm font-bold mb-2 text-gray-700 dark:text-gray-300 group-focus-within:text-purple-600 dark:group-focus-within:text-purple-400 transition-colors">
                            Konu *
                        </label>
                        <input type="text" x-model="formData.subject" required
                               class="w-full px-5 py-4 bg-gray-50 dark:bg-gray-900 border-2 border-gray-300 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:border-purple-500 dark:focus:border-purple-400 focus:ring-4 focus:ring-purple-500/20 dark:focus:ring-purple-400/20 transition-all"
                               placeholder="Mesaj konusu">
                    </div>

                    <!-- Message -->
                    <div class="group">
                        <label class="block text-sm font-bold mb-2 text-gray-700 dark:text-gray-300 group-focus-within:text-purple-600 dark:group-focus-within:text-purple-400 transition-colors">
                            Mesajınız *
                        </label>
                        <textarea x-model="formData.message" required rows="6"
                                  class="w-full px-5 py-4 bg-gray-50 dark:bg-gray-900 border-2 border-gray-300 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:border-purple-500 dark:focus:border-purple-400 focus:ring-4 focus:ring-purple-500/20 dark:focus:ring-purple-400/20 transition-all resize-none"
                                  placeholder="Mesajınızı detaylı bir şekilde yazın..."></textarea>
                    </div>

                    <!-- KVKK & Consent Section -->
                    <div class="space-y-4 bg-gray-50 dark:bg-gray-900/50 rounded-xl p-6 border border-gray-200 dark:border-gray-700">

                        <!-- Marketing Consent - Radio Group -->
                        <div class="space-y-3">
                            <label class="block text-sm font-bold text-gray-900 dark:text-white mb-3">
                                Pazarlama İzni
                            </label>

                            <!-- Option 1: Accept -->
                            <label class="flex items-start gap-3 cursor-pointer group">
                                <input type="radio"
                                       x-model="formData.marketing_consent"
                                       value="accept"
                                       class="mt-1 w-4 h-4 text-purple-600 bg-gray-100 border-gray-300 focus:ring-purple-500 dark:focus:ring-purple-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                <span class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                                    <a href="javascript:void(0)"
                                       @click.prevent="openPopup(5)"
                                       class="text-purple-600 dark:text-purple-400 hover:underline font-semibold">
                                        Aydınlatma metninde
                                    </a>
                                    belirtilen hususlar doğrultusunda, kişisel verilerimin ürün/hizmet ve stratejik pazarlama faaliyetleri ile ticari elektronik ileti gönderimi kapsamında işlenmesine açık rıza verdiğimi kabul ve beyan ederim.
                                </span>
                            </label>

                            <!-- Option 2: Decline -->
                            <label class="flex items-start gap-3 cursor-pointer group">
                                <input type="radio"
                                       x-model="formData.marketing_consent"
                                       value="decline"
                                       class="mt-1 w-4 h-4 text-purple-600 bg-gray-100 border-gray-300 focus:ring-purple-500 dark:focus:ring-purple-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                <span class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                                    <a href="javascript:void(0)"
                                       @click.prevent="openPopup(5)"
                                       class="text-purple-600 dark:text-purple-400 hover:underline font-semibold">
                                        Aydınlatma metninde
                                    </a>
                                    belirtilen hususlar doğrultusunda, kişisel verilerimin ürün/hizmet ve stratejik pazarlama faaliyetleri ile ticari elektronik ileti gönderimi kapsamında işlenmesine açık rıza göstermediğimi beyan ederim.
                                </span>
                            </label>
                        </div>

                        <!-- Privacy Policy - Checkbox (Required) -->
                        <div class="pt-3 border-t border-gray-200 dark:border-gray-700">
                            <label class="flex items-start gap-3 cursor-pointer group">
                                <input type="checkbox"
                                       x-model="formData.privacy_accepted"
                                       required
                                       class="mt-1 w-4 h-4 text-purple-600 bg-gray-100 border-gray-300 rounded focus:ring-purple-500 dark:focus:ring-purple-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                <span class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                                    <a href="javascript:void(0)"
                                       @click.prevent="openPopup(8)"
                                       class="text-purple-600 dark:text-purple-400 hover:underline font-semibold">
                                        İletişim Formu Aydınlatma Metni
                                    </a>'ni okudum, kabul ediyorum. *
                                </span>
                            </label>
                        </div>

                    </div>

                    <!-- Submit Button -->
                    <button type="submit"
                            :disabled="isSubmitting"
                            class="group w-full relative px-8 py-5 bg-gradient-to-r from-purple-600 to-pink-600 dark:from-purple-500 dark:to-pink-500 rounded-xl font-black text-lg text-white hover:shadow-xl hover:shadow-purple-500/30 dark:hover:shadow-purple-400/30 transition-all duration-300 disabled:opacity-50 overflow-hidden"
                            :class="isSubmitting ? '' : 'hover:scale-105'">

                        <!-- Button Content -->
                        <span class="relative z-10 flex items-center justify-center gap-3">
                            <span x-show="!isSubmitting">Gönder</span>
                            <span x-show="isSubmitting">Gönderiliyor...</span>
                            <i class="fa-solid fa-paper-plane group-hover:translate-x-2 transition-transform" x-show="!isSubmitting"></i>
                            <i class="fa-solid fa-spinner fa-spin" x-show="isSubmitting"></i>
                        </span>

                        <!-- Animated Gradient -->
                        <div class="absolute inset-0 bg-gradient-to-r from-purple-700 to-pink-700 dark:from-purple-600 dark:to-pink-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    </button>

                    <!-- Privacy Notice -->
                    <p class="text-xs text-gray-600 dark:text-gray-400 text-center mt-4">
                        <i class="fa-solid fa-shield-check text-purple-600 dark:text-purple-400 mr-1"></i>
                        Bilgileriniz güvenle saklanır ve 3. kişilerle paylaşılmaz.
                    </p>

                </form>
            </div>

        </div>
    </div>

    <!-- Popup Modal -->
    <div x-show="showPopup"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm"
         @click.self="closePopup()"
         style="display: none;">

        <!-- Modal Content -->
        <div x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="relative w-full max-w-4xl max-h-[90vh] bg-white dark:bg-gray-800 rounded-2xl shadow-2xl overflow-hidden"
             @click.stop>

            <!-- Modal Header -->
            <div class="sticky top-0 z-10 flex items-center justify-between px-8 py-6 bg-gradient-to-r from-purple-600 to-pink-600 dark:from-purple-500 dark:to-pink-500">
                <h3 class="text-2xl font-black text-white pr-8" x-text="popupTitle"></h3>
                <button @click="closePopup()"
                        class="flex-shrink-0 w-10 h-10 flex items-center justify-center rounded-full bg-white/20 hover:bg-white/30 text-white transition-all duration-200 hover:scale-110">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="overflow-y-auto max-h-[calc(90vh-140px)] px-8 py-6">
                <div class="prose prose-lg max-w-none dark:prose-invert
                          prose-headings:text-gray-900 dark:prose-headings:text-white
                          prose-p:text-gray-700 dark:prose-p:text-gray-300
                          prose-a:text-purple-600 dark:prose-a:text-purple-400 hover:prose-a:text-purple-700 dark:hover:prose-a:text-purple-300
                          prose-strong:text-gray-900 dark:prose-strong:text-white
                          prose-ul:text-gray-700 dark:prose-ul:text-gray-300
                          prose-ol:text-gray-700 dark:prose-ol:text-gray-300"
                     x-html="popupContent">
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="sticky bottom-0 z-10 flex items-center justify-end px-8 py-4 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700">
                <button @click="closePopup()"
                        class="px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 dark:from-purple-500 dark:to-pink-500 rounded-xl font-bold text-white hover:shadow-lg hover:shadow-purple-500/30 dark:hover:shadow-purple-400/30 transition-all duration-200 hover:scale-105">
                    <i class="fa-solid fa-check mr-2"></i>
                    Anladım
                </button>
            </div>

        </div>
    </div>

</div>

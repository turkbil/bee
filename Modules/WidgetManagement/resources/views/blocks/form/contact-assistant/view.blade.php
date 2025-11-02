{{-- Split Screen Glassmorphism Contact with AI Assistant --}}
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

<!-- Split Screen Container -->
<div class="flex flex-col lg:flex-row" x-data="{
    formData: { name: '', email: '', phone: '', subject: '', message: '' },
    isSubmitting: false,
    showSuccess: false,
    activeInfo: null,
    submitForm() {
        this.isSubmitting = true;
        setTimeout(() => {
            this.isSubmitting = false;
            this.showSuccess = true;
            setTimeout(() => { this.showSuccess = false; }, 4000);
            this.formData = { name: '', email: '', phone: '', subject: '', message: '' };
        }, 1500);
    }
}">

    <!-- Left Side: Info & Contact Details & Form (Full Width on Mobile) -->
    <div class="w-full lg:w-1/2 lg:min-h-screen relative bg-gray-50 dark:bg-gray-900 p-8 lg:p-16 animate-slide-in-left overflow-y-auto">

        <!-- Background Decoration -->
        <div class="absolute inset-0 opacity-10 dark:opacity-5 pointer-events-none">
            <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-blue-500 dark:bg-blue-600 rounded-full blur-3xl animate-pulse"></div>
            <div class="absolute bottom-1/4 right-1/4 w-80 h-80 bg-blue-400 dark:bg-blue-500 rounded-full blur-3xl animate-pulse" style="animation-delay: 1s;"></div>
        </div>

        <div class="relative z-10 w-full max-w-xl mx-auto space-y-8">

            <!-- Header -->
            <div class="mb-8">
                <div class="inline-flex items-center gap-2 px-4 py-2 bg-white/90 dark:bg-gray-800/90 backdrop-blur-sm rounded-full border border-gray-200 dark:border-gray-700 mb-6">
                    <div class="w-2 h-2 bg-green-500 dark:bg-green-400 rounded-full animate-pulse"></div>
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">7/24 Aktif Destek</span>
                </div>

                <h1 class="text-5xl lg:text-6xl font-black mb-6 leading-tight text-gray-900 dark:text-white">
                    İletişime<br>Geçin
                </h1>

                <p class="text-xl text-gray-700 dark:text-gray-300 leading-relaxed">
                    Projeleriniz için en iyi çözümleri sunmak üzere yanınızdayız. Bizimle iletişime geçin.
                </p>
            </div>

            <!-- Contact Methods -->
            <div class="space-y-6">

                <!-- Phone -->
                <div @mouseenter="activeInfo = 'phone'" @mouseleave="activeInfo = null"
                     class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-200 dark:border-gray-700 hover:border-blue-500 dark:hover:border-blue-400 transition-all duration-300 cursor-pointer group shadow-sm hover:shadow-lg"
                     :class="activeInfo === 'phone' ? 'scale-105 border-blue-500 dark:border-blue-400 shadow-xl shadow-blue-500/20 dark:shadow-blue-400/20' : ''">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 dark:from-blue-600 dark:to-blue-700 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-phone text-2xl text-white"></i>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">Telefon</h3>
                            <a href="tel:02167553555" class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 text-lg font-semibold">
                                0216 755 35 55
                            </a>
                        </div>
                        <i class="fa-solid fa-arrow-right text-blue-500 dark:text-blue-400 group-hover:translate-x-2 transition-transform"></i>
                    </div>
                </div>

                <!-- WhatsApp -->
                <div @mouseenter="activeInfo = 'whatsapp'" @mouseleave="activeInfo = null"
                     class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-200 dark:border-gray-700 hover:border-green-500 dark:hover:border-green-400 transition-all duration-300 cursor-pointer group shadow-sm hover:shadow-lg"
                     :class="activeInfo === 'whatsapp' ? 'scale-105 border-green-500 dark:border-green-400 shadow-xl shadow-green-500/20 dark:shadow-green-400/20' : ''">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-green-500 to-green-600 dark:from-green-600 dark:to-green-700 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <i class="fa-brands fa-whatsapp text-2xl text-white"></i>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">WhatsApp</h3>
                            <a href="{{ whatsapp_link() }}"
                               target="_blank"
                               class="text-green-600 dark:text-green-400 hover:text-green-700 dark:hover:text-green-300 text-lg font-semibold">
                                {{ setting('contact_whatsapp_1', '0501 005 67 58') }}
                            </a>
                        </div>
                        <i class="fa-solid fa-arrow-right text-green-500 dark:text-green-400 group-hover:translate-x-2 transition-transform"></i>
                    </div>
                </div>

                <!-- Email -->
                <div @mouseenter="activeInfo = 'email'" @mouseleave="activeInfo = null"
                     class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-200 dark:border-gray-700 hover:border-blue-500 dark:hover:border-blue-400 transition-all duration-300 cursor-pointer group shadow-sm hover:shadow-lg"
                     :class="activeInfo === 'email' ? 'scale-105 border-blue-500 dark:border-blue-400 shadow-xl shadow-blue-500/20 dark:shadow-blue-400/20' : ''">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 dark:from-blue-600 dark:to-blue-700 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-envelope text-2xl text-white"></i>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">E-Posta</h3>
                            <a href="mailto:info@ixtif.com" class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 text-lg font-semibold">
                                info@ixtif.com
                            </a>
                        </div>
                        <i class="fa-solid fa-arrow-right text-blue-500 dark:text-blue-400 group-hover:translate-x-2 transition-transform"></i>
                    </div>
                </div>

                <!-- Address -->
                <div @mouseenter="activeInfo = 'address'" @mouseleave="activeInfo = null"
                     class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-200 dark:border-gray-700 hover:border-blue-500 dark:hover:border-blue-400 transition-all duration-300 cursor-pointer group shadow-sm hover:shadow-lg"
                     :class="activeInfo === 'address' ? 'scale-105 border-blue-500 dark:border-blue-400 shadow-xl shadow-blue-500/20 dark:shadow-blue-400/20' : ''">
                    <div class="flex items-start gap-4">
                        <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 dark:from-blue-600 dark:to-blue-700 flex items-center justify-center group-hover:scale-110 transition-transform flex-shrink-0">
                            <i class="fa-solid fa-location-dot text-2xl text-white"></i>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">Ofis Adresi</h3>
                            <p class="text-gray-700 dark:text-gray-300 leading-relaxed">
                                Orta Mah. Atayolu Cad.<br>
                                Boya Vernikçiler Sanayi Sitesi No:51/B<br>
                                Tuzla / İstanbul / Türkiye
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Social Media -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-200 dark:border-gray-700 shadow-sm">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Sosyal Medya</h3>
                    <div class="flex gap-4">
                        <a href="#" class="w-12 h-12 rounded-xl bg-gray-100 dark:bg-gray-700 hover:bg-blue-500 dark:hover:bg-blue-500 text-gray-600 dark:text-gray-400 hover:text-white flex items-center justify-center transition-all hover:scale-110">
                            <i class="fa-brands fa-facebook text-xl"></i>
                        </a>
                        <a href="#" class="w-12 h-12 rounded-xl bg-gray-100 dark:bg-gray-700 hover:bg-pink-500 dark:hover:bg-pink-500 text-gray-600 dark:text-gray-400 hover:text-white flex items-center justify-center transition-all hover:scale-110">
                            <i class="fa-brands fa-instagram text-xl"></i>
                        </a>
                        <a href="#" class="w-12 h-12 rounded-xl bg-gray-100 dark:bg-gray-700 hover:bg-blue-400 dark:hover:bg-blue-400 text-gray-600 dark:text-gray-400 hover:text-white flex items-center justify-center transition-all hover:scale-110">
                            <i class="fa-brands fa-twitter text-xl"></i>
                        </a>
                        <a href="#" class="w-12 h-12 rounded-xl bg-gray-100 dark:bg-gray-700 hover:bg-blue-600 dark:hover:bg-blue-600 text-gray-600 dark:text-gray-400 hover:text-white flex items-center justify-center transition-all hover:scale-110">
                            <i class="fa-brands fa-linkedin text-xl"></i>
                        </a>
                    </div>
                </div>

                <!-- Contact Form -->
                <div class="bg-white dark:bg-gray-800 backdrop-blur-md rounded-2xl p-8 border border-gray-200 dark:border-gray-700 shadow-lg mt-8">
                    <!-- Success Message -->
                    <div x-show="showSuccess"
                         x-transition
                         class="absolute inset-0 flex items-center justify-center bg-green-500/90 backdrop-blur-lg rounded-2xl z-30">
                        <div class="text-center p-12">
                            <i class="fa-solid fa-circle-check text-7xl text-white mb-6"></i>
                            <h3 class="text-3xl font-black text-white mb-4">Harika!</h3>
                            <p class="text-white/90 text-lg">Mesajınız başarıyla gönderildi.</p>
                            <p class="text-white/70 mt-2">En kısa sürede size dönüş yapacağız.</p>
                        </div>
                    </div>

                    <!-- Form Header -->
                    <div class="mb-6">
                        <h2 class="text-3xl font-black mb-3 text-gray-900 dark:text-white">
                            Mesaj Gönderin
                        </h2>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">
                            Formu doldurun, size 24 saat içinde geri dönelim.
                        </p>
                    </div>

                    <!-- Form -->
                    <form @submit.prevent="submitForm" class="space-y-5">

                        <!-- Name -->
                        <div class="group">
                            <label class="block text-sm font-bold mb-2 text-gray-700 dark:text-gray-300 group-focus-within:text-blue-600 dark:group-focus-within:text-blue-400 transition-colors">
                                Ad Soyad *
                            </label>
                            <input type="text" x-model="formData.name" required
                                   class="w-full px-5 py-3 bg-gray-50 dark:bg-gray-900 border-2 border-gray-300 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:border-blue-500 dark:focus:border-blue-400 focus:ring-4 focus:ring-blue-500/20 dark:focus:ring-blue-400/20 transition-all"
                                   placeholder="Adınız ve soyadınız">
                        </div>

                        <!-- Email & Phone -->
                        <div class="grid md:grid-cols-2 gap-5">
                            <div class="group">
                                <label class="block text-sm font-bold mb-2 text-gray-700 dark:text-gray-300 group-focus-within:text-blue-600 dark:group-focus-within:text-blue-400 transition-colors">
                                    E-Posta *
                                </label>
                                <input type="email" x-model="formData.email" required
                                       class="w-full px-5 py-3 bg-gray-50 dark:bg-gray-900 border-2 border-gray-300 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:border-blue-500 dark:focus:border-blue-400 focus:ring-4 focus:ring-blue-500/20 dark:focus:ring-blue-400/20 transition-all"
                                       placeholder="ornek@email.com">
                            </div>

                            <div class="group">
                                <label class="block text-sm font-bold mb-2 text-gray-700 dark:text-gray-300 group-focus-within:text-blue-600 dark:group-focus-within:text-blue-400 transition-colors">
                                    Telefon
                                </label>
                                <input type="tel" x-model="formData.phone"
                                       class="w-full px-5 py-3 bg-gray-50 dark:bg-gray-900 border-2 border-gray-300 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:border-blue-500 dark:focus:border-blue-400 focus:ring-4 focus:ring-blue-500/20 dark:focus:ring-blue-400/20 transition-all"
                                       placeholder="0 (5XX) XXX XX XX">
                            </div>
                        </div>

                        <!-- Subject -->
                        <div class="group">
                            <label class="block text-sm font-bold mb-2 text-gray-700 dark:text-gray-300 group-focus-within:text-blue-600 dark:group-focus-within:text-blue-400 transition-colors">
                                Konu *
                            </label>
                            <input type="text" x-model="formData.subject" required
                                   class="w-full px-5 py-3 bg-gray-50 dark:bg-gray-900 border-2 border-gray-300 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:border-blue-500 dark:focus:border-blue-400 focus:ring-4 focus:ring-blue-500/20 dark:focus:ring-blue-400/20 transition-all"
                                   placeholder="Mesaj konusu">
                        </div>

                        <!-- Message -->
                        <div class="group">
                            <label class="block text-sm font-bold mb-2 text-gray-700 dark:text-gray-300 group-focus-within:text-blue-600 dark:group-focus-within:text-blue-400 transition-colors">
                                Mesajınız *
                            </label>
                            <textarea x-model="formData.message" required rows="5"
                                      class="w-full px-5 py-3 bg-gray-50 dark:bg-gray-900 border-2 border-gray-300 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:border-blue-500 dark:focus:border-blue-400 focus:ring-4 focus:ring-blue-500/20 dark:focus:ring-blue-400/20 transition-all resize-none"
                                      placeholder="Mesajınızı detaylı bir şekilde yazın..."></textarea>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit"
                                :disabled="isSubmitting"
                                class="group w-full relative px-8 py-4 bg-gradient-to-r from-blue-600 to-blue-700 dark:from-blue-500 dark:to-blue-600 rounded-xl font-black text-lg text-white hover:shadow-xl hover:shadow-blue-500/30 dark:hover:shadow-blue-400/30 transition-all duration-300 disabled:opacity-50 overflow-hidden"
                                :class="isSubmitting ? '' : 'hover:scale-105'">

                            <!-- Button Content -->
                            <span class="relative z-10 flex items-center justify-center gap-3">
                                <span x-show="!isSubmitting">Gönder</span>
                                <span x-show="isSubmitting">Gönderiliyor...</span>
                                <i class="fa-solid fa-paper-plane group-hover:translate-x-2 transition-transform" x-show="!isSubmitting"></i>
                                <i class="fa-solid fa-spinner fa-spin" x-show="isSubmitting"></i>
                            </span>

                            <!-- Animated Gradient -->
                            <div class="absolute inset-0 bg-gradient-to-r from-blue-700 to-blue-800 dark:from-blue-600 dark:to-blue-700 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        </button>

                        <!-- Privacy Notice -->
                        <p class="text-xs text-gray-600 dark:text-gray-400 text-center">
                            <i class="fa-solid fa-shield-check text-green-600 dark:text-green-400 mr-1"></i>
                            Bilgileriniz güvenle saklanır ve 3. kişilerle paylaşılmaz.
                        </p>

                    </form>
                </div>

            </div>

        </div>
    </div>

    <!-- Right Side: AI Chat Assistant -->
    <div class="w-full lg:w-1/2 min-h-screen relative bg-white dark:bg-gray-900 p-8 lg:p-16 animate-slide-in-right">

        <!-- Background Decoration -->
        <div class="absolute inset-0 opacity-10 dark:opacity-5 pointer-events-none">
            <div class="absolute top-1/3 right-1/4 w-96 h-96 bg-blue-500 dark:bg-blue-600 rounded-full blur-3xl animate-pulse"></div>
        </div>

        <!-- Sticky Container -->
        <div class="lg:sticky lg:top-24 relative z-10 w-full max-w-4xl mx-auto">
            <x-ai.inline-widget
                title="iXtif Destek Asistanı"
                page-slug="iletisim"
                :always-open="true"
                height="600px"
                theme="purple" />
        </div>
    </div>

</div>

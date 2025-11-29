<footer class="ml-64 bg-muzibu-dark text-white border-t border-white/10 pb-32">
    <div class="max-w-7xl mx-auto px-8 py-12">
        <div class="grid md:grid-cols-2 gap-12">
            <!-- Sol Taraf - İçerik -->
            <div>
                <div class="mb-8">
                    <div class="mb-4">
                        {!! app(\App\Services\LogoService::class)->renderFooterLogo(['class' => 'h-10 w-auto']) !!}
                    </div>
                    <p class="text-gray-400 leading-relaxed">
                        İşletmenize yasal ve telifsiz müzik. Cafe, restoran, mağaza ve ofisleriniz için profesyonel müzik çözümü.
                    </p>
                </div>

                <div class="grid grid-cols-2 gap-6 mb-8">
                    <div>
                        <h5 class="font-bold mb-4 text-sm uppercase tracking-wider text-gray-500">ŞİRKET</h5>
                        <ul class="space-y-2">
                            <li><a href="/hakkimizda" class="text-gray-400 hover:text-white transition-colors">Hakkımızda</a></li>
                            <li><a href="/iletisim" class="text-gray-400 hover:text-white transition-colors">İletişim</a></li>
                            <li><a href="/sss" class="text-gray-400 hover:text-white transition-colors">Sık Sorulan Sorular</a></li>
                            <li><a href="/blog" class="text-gray-400 hover:text-white transition-colors">Blog</a></li>
                        </ul>
                    </div>
                    <div>
                        <h5 class="font-bold mb-4 text-sm uppercase tracking-wider text-gray-500">YASAL</h5>
                        <ul class="space-y-2">
                            <li><a href="/kullanim-sartlari" class="text-gray-400 hover:text-white transition-colors">Kullanım Şartları</a></li>
                            <li><a href="/gizlilik-politikasi" class="text-gray-400 hover:text-white transition-colors">Gizlilik Politikası</a></li>
                            <li><a href="/cerez-politikasi" class="text-gray-400 hover:text-white transition-colors">Çerez Politikası</a></li>
                            <li><a href="/iptal-iade" class="text-gray-400 hover:text-white transition-colors">İptal ve İade</a></li>
                        </ul>
                    </div>
                </div>

                <div class="border-t border-white/10 pt-6 mb-6">
                    <h5 class="font-bold mb-4 text-sm">Bizi Takip Edin</h5>
                    <div class="flex gap-3">
                        <a href="#" class="w-10 h-10 bg-muzibu-gray hover:bg-white/10 rounded-lg flex items-center justify-center transition-all">
                            <i class="fab fa-facebook-f text-white"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-muzibu-gray hover:bg-white/10 rounded-lg flex items-center justify-center transition-all">
                            <i class="fab fa-instagram text-white"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-muzibu-gray hover:bg-white/10 rounded-lg flex items-center justify-center transition-all">
                            <i class="fab fa-twitter text-white"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-muzibu-gray hover:bg-white/10 rounded-lg flex items-center justify-center transition-all">
                            <i class="fab fa-youtube text-white"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-muzibu-gray hover:bg-white/10 rounded-lg flex items-center justify-center transition-all">
                            <i class="fab fa-linkedin-in text-white"></i>
                        </a>
                    </div>
                </div>

                <div class="text-sm text-gray-500">
                    &copy; {{ date('Y') }} Muzibu. Tüm hakları saklıdır.
                </div>
            </div>

            <!-- Sağ Taraf - İletişim -->
            <div class="bg-muzibu-gray rounded-2xl p-8">
                <h4 class="text-2xl font-bold mb-6">Destek</h4>

                <div class="space-y-4">
                    <a href="https://wa.me/908501234567" target="_blank" class="flex items-center gap-4 p-4 bg-white/5 hover:bg-white/10 rounded-xl transition-all group">
                        <div class="w-12 h-12 bg-[#25D366] rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="fab fa-whatsapp text-white text-2xl"></i>
                        </div>
                        <div>
                            <div class="text-sm text-gray-400 mb-1">WhatsApp</div>
                            <div class="font-semibold text-white group-hover:text-muzibu-coral transition-colors">Hemen Mesaj Gönder</div>
                        </div>
                    </a>

                    <a href="mailto:destek@muzibu.com" class="flex items-center gap-4 p-4 bg-white/5 hover:bg-white/10 rounded-xl transition-all group">
                        <div class="w-12 h-12 bg-muzibu-coral rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-envelope text-white text-xl"></i>
                        </div>
                        <div>
                            <div class="text-sm text-gray-400 mb-1">E-posta</div>
                            <div class="font-semibold text-white group-hover:text-muzibu-coral transition-colors">destek@muzibu.com</div>
                        </div>
                    </a>

                    <button class="w-full flex items-center gap-4 p-4 bg-muzibu-coral hover:bg-muzibu-coral-light rounded-xl transition-all group">
                        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-comment-dots text-white text-xl"></i>
                        </div>
                        <div class="text-left">
                            <div class="text-sm text-white/80 mb-1">Canlı Destek</div>
                            <div class="font-bold text-white">Hemen Sohbet Başlat</div>
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </div>
</footer>

{{-- Cache Clear & AI Clear JavaScript Functions - SIMPLIFIED --}}
<script>
(function() {
    'use strict';

    // Cache Clear Function
    window.clearSystemCache = function(button) {
        if (!button || button.disabled) return;

        const icon = button.querySelector('i');
        const originalIcon = icon.className;

        button.disabled = true;
        icon.className = 'fas fa-spinner fa-spin text-xs';

        fetch('/clear-cache', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (!data.success) throw new Error(data.message);

            // Success
            icon.className = 'fas fa-check text-xs';
            button.classList.remove('bg-red-500/20', 'text-red-400');
            button.classList.add('bg-green-500/20', 'text-green-400');

            setTimeout(() => location.reload(), 800);
        })
        .catch(error => {
            console.error('[Cache] Error:', error);
            icon.className = 'fas fa-times text-xs';

            setTimeout(() => {
                button.disabled = false;
                icon.className = originalIcon;
            }, 2000);
        });
    };

    // AI Conversation Clear Function
    window.clearAIConversation = function(button) {
        if (!button || button.disabled) return;

        const icon = button.querySelector('i');
        const originalIcon = icon.className;

        button.disabled = true;
        icon.className = 'fas fa-spinner fa-spin text-xs';

        try {
            // Clear AI conversation from localStorage
            localStorage.removeItem('ai_conversation_history');
            localStorage.removeItem('ai_conversation_id');

            // Success
            icon.className = 'fas fa-check text-xs';
            button.classList.remove('bg-purple-500/20', 'text-purple-400');
            button.classList.add('bg-green-500/20', 'text-green-400');

            setTimeout(() => location.reload(), 800);
        } catch (error) {
            console.error('[AI Chat] Error:', error);
            icon.className = 'fas fa-times text-xs';

            setTimeout(() => {
                button.disabled = false;
                icon.className = originalIcon;
            }, 2000);
        }
    };
})();
</script>

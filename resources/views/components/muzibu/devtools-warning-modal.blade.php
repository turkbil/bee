{{-- DevTools Agresif Koruma - Warning Modal --}}
{{-- SADECE Tenant 1001 (muzibu.com.tr) için gösterilir --}}

<div id="devtools-warning-modal"
     style="display: none;"
     class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/95 backdrop-blur-sm">

    {{-- Content Container --}}
    <div class="relative max-w-2xl w-full mx-4 bg-gradient-to-br from-red-950 to-black border-2 border-red-600 rounded-2xl shadow-2xl p-8 animate-pulse-slow">

        {{-- Warning Icon --}}
        <div class="flex justify-center mb-6">
            <div class="w-24 h-24 bg-red-600 rounded-full flex items-center justify-center animate-bounce">
                <i class="fas fa-exclamation-triangle text-white text-5xl"></i>
            </div>
        </div>

        {{-- Title --}}
        <h1 class="text-4xl md:text-5xl font-extrabold text-center text-red-500 mb-4">
            🛑 DUR!
        </h1>

        <h2 class="text-2xl md:text-3xl font-bold text-center text-white mb-8">
            ⛔ YETKİSİZ ERİŞİM TESPİT EDİLDİ ⛔
        </h2>

        {{-- Warning Message --}}
        <div class="bg-red-900/50 border-2 border-red-600 rounded-xl p-6 mb-6">
            <p class="text-white text-lg text-center mb-4">
                Bu alan izleniyor. Erişimin kayıt altına alındı.
            </p>
            <p class="text-red-400 text-base text-center font-semibold">
                Yetkisiz erişim suçtur.
            </p>
        </div>

        {{-- User Info Box --}}
        <div class="bg-slate-900/80 border border-red-600 rounded-xl p-6 mb-6">
            <p class="text-red-400 font-bold text-center mb-4">
                📋 SENİN BİLGİLERİN (SUNUCUYA KAYDEDİLDİ):
            </p>

            <div class="space-y-2 text-sm font-mono">
                <div class="flex justify-between border-b border-slate-700 pb-2">
                    <span class="text-slate-400">📅 Tarih/Saat:</span>
                    <span class="text-white" id="devtools-datetime"></span>
                </div>
                <div class="flex justify-between border-b border-slate-700 pb-2">
                    <span class="text-slate-400">🌐 Tarayıcı:</span>
                    <span class="text-white" id="devtools-browser"></span>
                </div>
                <div class="flex justify-between border-b border-slate-700 pb-2">
                    <span class="text-slate-400">💻 İşletim Sis.:</span>
                    <span class="text-white" id="devtools-os"></span>
                </div>
                <div class="flex justify-between border-b border-slate-700 pb-2">
                    <span class="text-slate-400">📐 Ekran Çözün.:</span>
                    <span class="text-white" id="devtools-screen"></span>
                </div>
                <div class="flex justify-between border-b border-slate-700 pb-2">
                    <span class="text-slate-400">🌍 Dil:</span>
                    <span class="text-white" id="devtools-lang"></span>
                </div>
                <div class="flex justify-between border-b border-slate-700 pb-2">
                    <span class="text-slate-400">🕐 Saat Dilimi:</span>
                    <span class="text-white" id="devtools-timezone"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">🔴 IP Adresi:</span>
                    <span class="text-red-500 font-bold" id="devtools-ip">{{ request()->ip() }}</span>
                </div>
            </div>
        </div>

        {{-- Countdown --}}
        <div class="text-center mb-6">
            <p class="text-white text-xl mb-2">
                Oturum kapatılıyor...
            </p>
            <div class="flex items-center justify-center gap-3">
                <i class="fas fa-hourglass-half text-red-500 text-2xl animate-spin"></i>
                <span class="text-red-500 text-6xl font-bold" id="devtools-countdown">3</span>
                <i class="fas fa-hourglass-end text-red-500 text-2xl animate-spin"></i>
            </div>
        </div>

        {{-- Legal Warning --}}
        <div class="bg-yellow-900/30 border border-yellow-600 rounded-lg p-4 text-center">
            <p class="text-yellow-400 text-sm">
                ⚠️ Bu bilgiler yasal işlemlerde delil olarak kullanılabilir.
            </p>
        </div>

        {{-- Animated Border Effect --}}
        <div class="absolute inset-0 rounded-2xl pointer-events-none">
            <div class="absolute inset-0 rounded-2xl border-2 border-red-500 animate-pulse"></div>
        </div>
    </div>

    {{-- Fill user info on modal show (no console logs!) --}}
    <script>
        (function() {
            window.addEventListener('devtools-detected', function() {
                // Tarih/Saat
                const now = new Date();
                const datetimeEl = document.getElementById('devtools-datetime');
                if (datetimeEl) {
                    datetimeEl.textContent = now.toLocaleString('tr-TR', {
                        day: '2-digit',
                        month: '2-digit',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit',
                        second: '2-digit'
                    });
                }

                // Browser
                const ua = navigator.userAgent;
                let browser = 'Bilinmiyor';
                if (ua.includes('Chrome')) browser = 'Google Chrome';
                else if (ua.includes('Firefox')) browser = 'Mozilla Firefox';
                else if (ua.includes('Safari')) browser = 'Safari';
                else if (ua.includes('Edge')) browser = 'Microsoft Edge';
                const browserEl = document.getElementById('devtools-browser');
                if (browserEl) browserEl.textContent = browser;

                // OS
                let os = 'Bilinmiyor';
                if (ua.includes('Windows')) os = 'Windows';
                else if (ua.includes('Mac')) os = 'macOS';
                else if (ua.includes('Linux')) os = 'Linux';
                else if (ua.includes('Android')) os = 'Android';
                else if (ua.includes('iOS')) os = 'iOS';
                const osEl = document.getElementById('devtools-os');
                if (osEl) osEl.textContent = os;

                // Screen
                const screenEl = document.getElementById('devtools-screen');
                if (screenEl) {
                    screenEl.textContent = `${screen.width}x${screen.height} ${screen.colorDepth}bit`;
                }

                // Language
                const langEl = document.getElementById('devtools-lang');
                if (langEl) langEl.textContent = navigator.language;

                // Timezone
                const tzEl = document.getElementById('devtools-timezone');
                if (tzEl) {
                    tzEl.textContent = Intl.DateTimeFormat().resolvedOptions().timeZone;
                }
            });
        })();
    </script>
</div>

{{-- Custom Animations --}}
<style>
    @keyframes pulse-slow {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.8; }
    }
    .animate-pulse-slow {
        animation: pulse-slow 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
</style>

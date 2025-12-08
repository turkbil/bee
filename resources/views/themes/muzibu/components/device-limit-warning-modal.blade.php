{{-- Device Limit Warning Modal (Session Polling) --}}
<div x-show="showDeviceLimitWarning"
     x-cloak
     class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/80 backdrop-blur-sm"
     @click.self="showDeviceLimitWarning = false">

    <div class="bg-gradient-to-br from-slate-900 to-slate-800 rounded-2xl shadow-2xl max-w-md w-full mx-4 border border-yellow-500/30"
         @click.stop>

        {{-- Header --}}
        <div class="bg-yellow-500/10 border-b border-yellow-500/30 px-6 py-4 rounded-t-2xl">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-yellow-500/20 rounded-full flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-yellow-400 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-white">Cihaz Limiti Uyarisi</h3>
                    <p class="text-sm text-yellow-400/80">Baska cihazdan giris yapildi</p>
                </div>
            </div>
        </div>

        {{-- Body --}}
        <div class="p-6 space-y-4">
            <div class="bg-red-500/10 rounded-lg p-4 border border-red-500/30">
                <p class="text-slate-200 leading-relaxed">
                    <i class="fas fa-exclamation-circle text-red-400 mr-2"></i>
                    <strong class="text-white">Oturumunuz sonlandırıldı!</strong>
                    <span class="block mt-2 text-slate-300">
                        Hesabınıza başka bir cihazdan giriş yapıldığı için bu cihazdan çıkış yaptırıldınız.
                    </span>
                    <span class="block mt-2 text-yellow-300 text-sm">
                        ℹ️ Aynı anda maksimum <strong class="text-white" x-text="deviceLimit"></strong> cihazdan giriş yapabilirsiniz.
                    </span>
                </p>
            </div>

            <p class="text-slate-300 text-sm text-center">
                Tekrar giriş yapmak için aşağıdaki butonları kullanabilirsiniz:
            </p>
        </div>

        {{-- Footer / Actions --}}
        <div class="px-6 pb-6 space-y-3">
            {{-- Tekrar giris yap --}}
            <button @click="window.location.href = '/login'"
                    class="w-full px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-500 hover:to-blue-400 text-white rounded-xl font-semibold transition-all duration-200 shadow-lg hover:shadow-blue-500/30 hover:scale-[1.02] active:scale-95">
                <i class="fas fa-sign-in-alt mr-2"></i>
                Tekrar Giriş Yap
            </button>

            {{-- Ana sayfaya don --}}
            <button @click="showDeviceLimitWarning = false; window.location.href = '/'"
                    class="w-full px-6 py-3 bg-slate-700 hover:bg-slate-600 text-white rounded-xl font-semibold transition-all duration-200">
                <i class="fas fa-home mr-2"></i>
                Ana Sayfaya Dön
            </button>
        </div>
    </div>
</div>

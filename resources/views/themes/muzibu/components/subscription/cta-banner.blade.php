{{-- Subscription CTA Banner Component --}}
{{-- 🔴 TEK KAYNAK: isPremium() (subscription_expires_at > now) --}}
{{-- Trial ayrımı kaldırıldı - trial da premium sayılır --}}
@auth
    @php
        $isPremium = auth()->user()->isPremium();
        $expiresAt = auth()->user()->subscription_expires_at;
        $daysRemaining = $expiresAt ? now()->diffInDays($expiresAt, false) : 0;
    @endphp

    {{-- Premium Ending Soon (30 gün veya daha az kaldı) --}}
    @if($isPremium && $daysRemaining <= 30 && $daysRemaining > 0)
        <div class="mx-2 sm:mx-8 mb-6 p-4 bg-gradient-to-r from-yellow-500/20 to-orange-500/20 border border-yellow-500/30 rounded-2xl backdrop-blur-sm">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-4 text-center sm:text-left">
                    <div class="w-10 h-10 bg-yellow-500/30 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-clock text-yellow-400"></i>
                    </div>
                    <div>
                        <h3 class="text-white font-semibold">⏰ Premium Süreniz Bitiyor</h3>
                        <p class="text-yellow-200 text-sm">{{ $daysRemaining }} gün kaldı - uzatmak için tıklayın.</p>
                    </div>
                </div>
                <a href="{{ route('subscription.plans') }}" class="px-5 py-2 bg-gradient-to-r from-yellow-500 to-orange-500 hover:from-yellow-600 hover:to-orange-600 text-white font-semibold rounded-full transition-all duration-300 text-sm whitespace-nowrap">
                    <i class="fas fa-sync-alt mr-2"></i>Üyeliği Uzat
                </a>
            </div>
        </div>
    @endif

    {{-- Expired Subscription (premium değil ve daha önce üyelik kullanmış) --}}
    @if(!$isPremium && auth()->user()->subscriptions()->exists())
        <div class="mx-2 sm:mx-8 mb-6 p-6 bg-gradient-to-r from-red-500/20 to-pink-500/20 border border-red-500/30 rounded-2xl backdrop-blur-sm">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-4 text-center sm:text-left">
                    <div class="w-12 h-12 bg-red-500/30 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-red-400 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-white font-bold text-lg">❌ Aboneliğiniz Sona Erdi!</h3>
                        <p class="text-red-200 text-sm mt-1">
                            Müzik dinlemek için premium üyelik gereklidir. Hemen abone olun ve sınırsız dinlemenin keyfini çıkarın.
                        </p>
                    </div>
                </div>
                <a href="{{ route('subscription.plans') }}" class="px-6 py-3 bg-gradient-to-r from-red-500 to-pink-500 hover:from-red-600 hover:to-pink-600 text-white font-bold rounded-full transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-105 whitespace-nowrap">
                    <i class="fas fa-redo mr-2"></i>HEMEN YENİLE
                </a>
            </div>
        </div>
    @endif

    {{-- First Time Free User (premium değil ve hiç üyelik kullanmamış) --}}
    @if(!$isPremium && !auth()->user()->subscriptions()->exists())
        <div class="mx-2 sm:mx-8 mb-6 p-6 bg-gradient-to-r from-muzibu-coral/20 to-pink-500/20 border border-muzibu-coral/30 rounded-2xl backdrop-blur-sm">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-4 text-center sm:text-left">
                    <div class="w-12 h-12 bg-muzibu-coral/30 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-crown text-yellow-400 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-white font-bold text-lg">👑 Premium Üye Olun!</h3>
                        <p class="text-white/80 text-sm mt-1">
                            Premium üye olun, sınırsız müzik keyfini yaşayın.
                        </p>
                    </div>
                </div>
                <a href="{{ route('subscription.plans') }}" class="px-6 py-3 bg-gradient-to-r from-muzibu-coral to-pink-500 hover:from-muzibu-coral-light hover:to-pink-600 text-white font-bold rounded-full transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-105 whitespace-nowrap inline-block">
                    <i class="fas fa-crown mr-2"></i>Premium'a Geç
                </a>
            </div>
        </div>
    @endif
@endauth

{{-- Guest User (Not Logged In) --}}
@guest
    <div class="mx-2 sm:mx-8 mb-6 p-6 bg-gradient-to-r from-muzibu-coral/20 to-pink-500/20 border border-muzibu-coral/30 rounded-2xl backdrop-blur-sm">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-4 text-center sm:text-left">
                <div class="w-12 h-12 bg-muzibu-coral/30 rounded-full flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-music text-muzibu-coral text-xl"></i>
                </div>
                <div>
                    <h3 class="text-white font-bold text-lg">🎵 Sınırsız Dinlemek İçin Üye Olun!</h3>
                    <p class="text-white/80 text-sm mt-1">
                        Üye olun ve sınırsız müzik keyfini yaşayın.
                    </p>
                </div>
            </div>
            <a href="/register" class="px-6 py-3 bg-gradient-to-r from-muzibu-coral to-pink-500 hover:from-muzibu-coral-light hover:to-pink-600 text-white font-bold rounded-full transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-105 whitespace-nowrap inline-block">
                <i class="fas fa-user-plus mr-2"></i>Üye Ol
            </a>
        </div>
    </div>
@endguest

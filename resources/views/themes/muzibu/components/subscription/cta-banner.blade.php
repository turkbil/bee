{{-- Subscription CTA Banner Component --}}
@auth
    @php
        $subscriptionService = app(\Modules\Subscription\App\Services\SubscriptionService::class);
        $access = $subscriptionService->checkUserAccess(auth()->user());
        $status = $access['status'] ?? 'subscription_required';
        $isTrial = $access['is_trial'] ?? false;
        $expiresAt = $access['expires_at'] ?? null;
        $daysRemaining = $expiresAt ? now()->diffInDays($expiresAt) : 0;
    @endphp

    {{-- Trial Ending Soon (2 gün kaldı) --}}
    @if($isTrial && $daysRemaining <= 2 && $daysRemaining > 0)
        <div class="mx-2 sm:mx-8 mb-6 p-6 bg-gradient-to-r from-orange-500/20 to-red-500/20 border border-orange-500/30 rounded-2xl backdrop-blur-sm animate-pulse-slow">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-4 text-center sm:text-left">
                    <div class="w-12 h-12 bg-orange-500/30 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-clock text-orange-400 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-white font-bold text-lg">⏰ Trial Süreniz Bitiyor!</h3>
                        <p class="text-orange-200 text-sm mt-1">
                            Sadece {{ $daysRemaining }} gün kaldı! Premium'a geçerek sınırsız müzik keyfini kaçırma.
                        </p>
                    </div>
                </div>
                <a href="{{ route('subscription.plans') }}" class="px-6 py-3 bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 text-white font-bold rounded-full transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-105 whitespace-nowrap">
                    <i class="fas fa-crown mr-2"></i>Premium'a Geç
                </a>
            </div>
        </div>
    @endif

    {{-- Trial Active (2+ gün kaldı) --}}
    @if($isTrial && $daysRemaining > 2)
        <div class="mx-2 sm:mx-8 mb-6 p-4 bg-gradient-to-r from-green-500/20 to-emerald-500/20 border border-green-500/30 rounded-2xl backdrop-blur-sm">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-4 text-center sm:text-left">
                    <div class="w-10 h-10 bg-green-500/30 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-gift text-green-400"></i>
                    </div>
                    <div>
                        <h3 class="text-white font-semibold">🎁 Trial Üyeliğiniz Aktif</h3>
                        <p class="text-green-200 text-sm">{{ $daysRemaining }} gün sınırsız müzik keyfi!</p>
                    </div>
                </div>
                <a href="{{ route('subscription.plans') }}" class="px-5 py-2 bg-white/10 hover:bg-white/20 text-white font-semibold rounded-full transition-all duration-300 border border-white/30 hover:border-white/50 text-sm whitespace-nowrap">
                    Premium'a Bak
                </a>
            </div>
        </div>
    @endif

    {{-- Expired Subscription / No Subscription --}}
    @if($status === 'subscription_required' && auth()->user()->has_used_trial)
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
                        Üye olun, 7 gün ücretsiz deneyin ve sınırsız müzik keyfini yaşayın.
                    </p>
                </div>
            </div>
            <a href="/register" class="px-6 py-3 bg-gradient-to-r from-muzibu-coral to-pink-500 hover:from-muzibu-coral-light hover:to-pink-600 text-white font-bold rounded-full transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-105 whitespace-nowrap inline-block">
                <i class="fas fa-user-plus mr-2"></i>Ücretsiz Dene
            </a>
        </div>
    </div>
@endguest

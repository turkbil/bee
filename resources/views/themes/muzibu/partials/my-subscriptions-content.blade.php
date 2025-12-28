{{-- Stats Row - Muzibu Dark Theme --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4 mb-6">
    {{-- Kalan Gun --}}
    <div class="bg-gradient-to-br from-green-500/20 to-emerald-500/10 border border-green-500/30 rounded-xl p-4 text-center">
        <div class="text-3xl font-bold text-green-400 mb-1">
            @if(!$timeLeft['expired'])
                {{ $timeLeft['days'] }}
            @else
                0
            @endif
        </div>
        <div class="text-gray-400 text-sm">Kalan Gun</div>
    </div>

    {{-- Toplam Odenen --}}
    <div class="bg-gradient-to-br from-yellow-500/20 to-orange-500/10 border border-yellow-500/30 rounded-xl p-4 text-center">
        <div class="text-2xl font-bold text-yellow-400 mb-1">
            @php
                $totalPaid = collect($allPayments)->sum('total');
            @endphp
            {{ number_format($totalPaid, 0, ',', '.') }}₺
        </div>
        <div class="text-gray-400 text-sm">Odenen</div>
    </div>

    {{-- Odeme Sayisi --}}
    <div class="bg-gradient-to-br from-purple-500/20 to-pink-500/10 border border-purple-500/30 rounded-xl p-4 text-center">
        <div class="text-3xl font-bold text-purple-400 mb-1">{{ count($allPayments) }}</div>
        <div class="text-gray-400 text-sm">Odeme</div>
    </div>

    {{-- Bitis Tarihi --}}
    <div class="bg-gradient-to-br from-pink-500/20 to-rose-500/10 border border-pink-500/30 rounded-xl p-4 text-center">
        <div class="text-lg font-bold text-pink-400 mb-1">
            @if(!$timeLeft['expired'] && $user->subscription_expires_at)
                {{ $user->subscription_expires_at->format('d.m.Y') }}
            @else
                -
            @endif
        </div>
        <div class="text-gray-400 text-sm">Bitis</div>
    </div>
</div>

{{-- Odeme Bekleyen --}}
@if(!empty($subscriptionInfo['pending_payment']))
<div class="bg-orange-500/10 border border-orange-500/30 rounded-xl overflow-hidden mb-6">
    <div class="p-4 border-b border-orange-500/20">
        <h2 class="text-base font-bold text-orange-400 flex items-center gap-2">
            <i class="fas fa-exclamation-triangle"></i>
            Odeme Bekleyen
        </h2>
    </div>
    <div class="divide-y divide-orange-500/20">
        @foreach($subscriptionInfo['pending_payment'] as $pp)
        <div class="p-4 flex items-center justify-between">
            <div>
                <span class="text-white font-medium">{{ $pp['plan_name'] }}</span>
                <span class="text-gray-400 text-sm ml-1">({{ $pp['cycle_label'] ?? '' }})</span>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-white font-semibold">{{ number_format($pp['price'], 2, ',', '.') }} TL</span>
                <a href="/subscription/checkout/{{ $pp['id'] }}" data-spa class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-medium rounded-lg transition">
                    Ode
                </a>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- Odeme Gecmisi --}}
<div class="bg-white/5 border border-white/10 rounded-xl overflow-hidden">
    <div class="p-4 border-b border-white/10">
        <h2 class="text-base font-bold text-white flex items-center gap-2">
            <i class="fas fa-credit-card text-green-400"></i>
            Odemeler
        </h2>
    </div>
    @if(!empty($allPayments))
    <div class="divide-y divide-white/5">
        @foreach($allPayments as $payment)
        <a href="/siparislerim/no/{{ $payment['order_number'] }}" data-spa
           class="p-4 flex items-center justify-between hover:bg-white/5 transition-colors cursor-pointer block group">
            <div class="flex items-center gap-3">
                @if(($payment['type'] ?? 'order') === 'manual')
                <div class="w-10 h-10 rounded-lg bg-purple-500/20 flex items-center justify-center">
                    <i class="fas fa-user-check text-purple-400"></i>
                </div>
                @else
                <div class="w-10 h-10 rounded-lg bg-green-500/20 flex items-center justify-center">
                    <i class="fas fa-check text-green-400"></i>
                </div>
                @endif
                <div>
                    <div class="text-white font-medium">
                        @if(($payment['type'] ?? 'order') === 'manual')
                            {{ $payment['plan_name'] ?? 'Premium' }}
                        @else
                            Abonelik
                        @endif
                    </div>
                    <div class="text-gray-500 text-sm">
                        {{ $payment['payment_method'] ?? 'Kredi Karti' }} · {{ $payment['created_at']->format('d.m.Y') }}
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div class="text-right">
                    <div class="text-white font-semibold">{{ number_format($payment['total'], 0, ',', '.') }} TL</div>
                    <div class="text-gray-500 text-xs">{{ $payment['order_number'] }}</div>
                </div>
                <i class="fas fa-chevron-right text-gray-600 group-hover:text-gray-400 transition-colors"></i>
            </div>
        </a>
        @endforeach
    </div>
    @else
    <div class="p-8 text-center">
        <i class="fas fa-credit-card text-gray-600 text-3xl mb-3"></i>
        <p class="text-gray-500">Henuz odeme yok</p>
    </div>
    @endif
</div>

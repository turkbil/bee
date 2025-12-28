{{-- Subscription Plans - Premium Design --}}
<div x-data="{
    selectedCycle: {},
    hoveredPlan: null,
    init() {
        // Her plan için ilk cycle'ı seç
        @foreach($plans as $plan)
            @php $cycles = $plan->getSortedCycles(); $firstKey = array_key_first($cycles); @endphp
            this.selectedCycle[{{ $plan->subscription_plan_id }}] = '{{ $firstKey }}';
        @endforeach
    }
}">
<style>
    /* Animated Background Orbs */
    .orb {
        position: absolute;
        border-radius: 50%;
        filter: blur(80px);
        opacity: 0.4;
        animation: float 20s ease-in-out infinite;
    }
    .orb-1 { width: 400px; height: 400px; background: linear-gradient(135deg, #ff6b6b, #ee5a24); top: -100px; left: -100px; animation-delay: 0s; }
    .orb-2 { width: 300px; height: 300px; background: linear-gradient(135deg, #a55eea, #8854d0); bottom: 20%; right: -50px; animation-delay: -5s; }
    .orb-3 { width: 250px; height: 250px; background: linear-gradient(135deg, #00d2d3, #01a3a4); top: 40%; left: 10%; animation-delay: -10s; }

    @keyframes float {
        0%, 100% { transform: translate(0, 0) scale(1); }
        25% { transform: translate(30px, -30px) scale(1.05); }
        50% { transform: translate(-20px, 20px) scale(0.95); }
        75% { transform: translate(-30px, -20px) scale(1.02); }
    }

    /* Glass Card Effect */
    .glass-card {
        background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255,255,255,0.08);
    }

    .glass-card-hover:hover {
        background: linear-gradient(135deg, rgba(255,255,255,0.08) 0%, rgba(255,255,255,0.04) 100%);
        border-color: rgba(255,255,255,0.15);
    }

    /* Featured Card Glow */
    .featured-glow {
        box-shadow:
            0 0 60px -15px rgba(255, 107, 107, 0.4),
            0 0 30px -10px rgba(238, 90, 36, 0.3),
            inset 0 1px 0 0 rgba(255,255,255,0.1);
    }

    .featured-glow:hover {
        box-shadow:
            0 0 80px -15px rgba(255, 107, 107, 0.5),
            0 0 40px -10px rgba(238, 90, 36, 0.4),
            inset 0 1px 0 0 rgba(255,255,255,0.15);
    }

    /* Animated Gradient Border */
    .gradient-border {
        position: relative;
    }
    .gradient-border::before {
        content: '';
        position: absolute;
        inset: -2px;
        border-radius: 26px;
        background: linear-gradient(135deg, #ff6b6b, #ee5a24, #f368e0, #a55eea, #00d2d3, #ff6b6b);
        background-size: 300% 300%;
        animation: gradient-rotate 4s ease infinite;
        z-index: -1;
        opacity: 0;
        transition: opacity 0.4s ease;
    }
    .gradient-border:hover::before {
        opacity: 1;
    }

    @keyframes gradient-rotate {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    /* Price Animation */
    .price-pop {
        animation: pricePop 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    }
    @keyframes pricePop {
        0% { transform: scale(0.8); opacity: 0; }
        100% { transform: scale(1); opacity: 1; }
    }

    /* Shine Effect on Button */
    .btn-shine {
        position: relative;
        overflow: hidden;
    }
    .btn-shine::after {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: linear-gradient(45deg, transparent 30%, rgba(255,255,255,0.2) 50%, transparent 70%);
        transform: translateX(-100%) rotate(45deg);
        transition: none;
    }
    .btn-shine:hover::after {
        animation: shine 0.6s ease forwards;
    }
    @keyframes shine {
        to { transform: translateX(100%) rotate(45deg); }
    }

    /* Feature Check Animation */
    .feature-item {
        opacity: 0;
        transform: translateX(-10px);
    }
    .feature-item.visible {
        animation: slideIn 0.4s ease forwards;
    }
    @keyframes slideIn {
        to { opacity: 1; transform: translateX(0); }
    }

    /* Cycle Selector Pills */
    .cycle-pill {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .cycle-pill.active {
        background: linear-gradient(135deg, #ff6b6b, #ee5a24);
        box-shadow: 0 4px 15px -3px rgba(255, 107, 107, 0.4);
    }

    /* Scroll Animations */
    .plan-card {
        opacity: 0;
        transform: translateY(30px);
        animation: fadeUp 0.6s ease forwards;
    }
    .plan-card:nth-child(1) { animation-delay: 0.1s; }
    .plan-card:nth-child(2) { animation-delay: 0.2s; }
    .plan-card:nth-child(3) { animation-delay: 0.3s; }
    .plan-card:nth-child(4) { animation-delay: 0.4s; }

    @keyframes fadeUp {
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<div class="relative overflow-hidden bg-gradient-to-br from-[#0a0a0a] via-[#111111] to-[#0a0a0a] py-12 pb-24 px-4 sm:px-6 lg:px-8">
    {{-- Animated Background Orbs --}}
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
    </div>

    {{-- Content --}}
    <div class="relative z-10 max-w-7xl mx-auto">

        {{-- Header --}}
        <div class="text-center mb-16">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/5 border border-white/10 mb-6">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span class="text-sm text-gray-400 font-medium">{{ __('front.premium_experience') ?? 'Premium Deneyim' }}</span>
            </div>

            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-white mb-6 tracking-tight leading-tight">
                <span class="bg-gradient-to-r from-white via-gray-200 to-gray-400 bg-clip-text text-transparent">
                    {{ __('front.unlimited') ?? 'Sınırsız' }}
                </span>
                <br class="sm:hidden">
                <span class="bg-gradient-to-r from-[#ff6b6b] via-[#f368e0] to-[#a55eea] bg-clip-text text-transparent">
                    {{ __('front.music_enjoyment') ?? 'Müzik Keyfi' }}
                </span>
            </h1>

            <p class="text-lg sm:text-xl text-gray-400 max-w-2xl mx-auto leading-relaxed">
                {{ __('front.choose_best_plan') ?? 'İşletmenize en uygun planı seçin, telifsiz müziğin keyfini çıkarın' }}
            </p>
        </div>

        {{-- 30+ Gün Aboneliği Var - Engelleme Mesajı --}}
        @if($hasEnoughSubscription)
        <div class="max-w-2xl mx-auto">
            <div class="glass-card rounded-3xl p-8 sm:p-12 text-center">
                {{-- Crown Icon --}}
                <div class="w-24 h-24 mx-auto mb-6 rounded-full bg-gradient-to-r from-yellow-500/20 to-orange-500/20 flex items-center justify-center">
                    <i class="fas fa-crown text-5xl text-yellow-400"></i>
                </div>

                {{-- Title --}}
                <h2 class="text-2xl sm:text-3xl font-bold text-white mb-4">
                    Premium Üyeliğiniz Aktif
                </h2>

                {{-- Description --}}
                <p class="text-gray-400 text-lg mb-6 leading-relaxed">
                    Zaten <span class="text-white font-semibold">{{ $remainingDays }} gün</span> süreli aktif aboneliğiniz bulunuyor.
                    <br class="hidden sm:inline">
                    Yeni paket satın almak için mevcut sürenizin bitmesine yakın tekrar ziyaret edin.
                </p>

                {{-- Expiry Date --}}
                <div class="inline-flex items-center gap-3 px-6 py-3 bg-gradient-to-r from-yellow-500/10 to-orange-500/10 border border-yellow-500/30 rounded-xl mb-8">
                    <i class="fas fa-calendar-check text-yellow-400"></i>
                    <span class="text-white">
                        Bitiş Tarihi: <span class="font-semibold text-yellow-400">{{ $expiresAt }}</span>
                    </span>
                </div>

                {{-- Actions --}}
                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    <a href="/dashboard" class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-white/10 hover:bg-white/20 text-white font-medium rounded-xl transition" data-spa>
                        <i class="fas fa-home"></i>
                        Dashboard'a Dön
                    </a>
                    <a href="/my-subscriptions" class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-yellow-500 to-orange-500 hover:opacity-90 text-white font-semibold rounded-xl transition shadow-lg shadow-yellow-500/25" data-spa>
                        <i class="fas fa-receipt"></i>
                        Aboneliklerim
                    </a>
                </div>
            </div>
        </div>
        @else

        {{-- Plans Grid --}}
        @if($plans->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-{{ min($plans->count(), 3) }} gap-6 lg:gap-8 max-w-5xl mx-auto">
            @foreach($plans as $planIndex => $plan)
                {{-- Trial planı kullanmışsa gizle --}}
                @if($plan->is_trial && $userHasUsedTrial)
                    @continue
                @endif

                @php
                    $cycles = $plan->getSortedCycles();
                    if (empty($cycles)) continue;

                    $firstCycleKey = array_key_first($cycles);
                    $isFeatured = $plan->is_featured;
                    $isTrial = $plan->is_trial;
                @endphp

                <div class="plan-card relative group"
                     x-on:mouseenter="hoveredPlan = {{ $plan->subscription_plan_id }}"
                     x-on:mouseleave="hoveredPlan = null">

                    {{-- Featured Badge --}}
                    @if($isFeatured)
                    <div class="absolute -top-4 left-1/2 -translate-x-1/2 z-20">
                        <div class="flex items-center gap-1.5 px-4 py-1.5 rounded-full bg-gradient-to-r from-[#ff6b6b] to-[#ee5a24] text-white text-xs font-bold shadow-lg shadow-[#ff6b6b]/30">
                            <i class="fas fa-crown text-yellow-300"></i>
                            <span>{{ __('front.most_popular') ?? 'EN POPÜLER' }}</span>
                        </div>
                    </div>
                    @endif

                    {{-- Trial Badge --}}
                    @if($isTrial)
                    <div class="absolute -top-4 left-1/2 -translate-x-1/2 z-20">
                        <div class="flex items-center gap-1.5 px-4 py-1.5 rounded-full bg-gradient-to-r from-emerald-500 to-teal-500 text-white text-xs font-bold shadow-lg shadow-emerald-500/30">
                            <i class="fas fa-gift"></i>
                            <span>{{ __('front.free_trial') ?? 'ÜCRETSİZ DENE' }}</span>
                        </div>
                    </div>
                    @endif

                    {{-- Card --}}
                    <div class="relative h-full rounded-3xl overflow-hidden transition-all duration-500 {{ $isFeatured ? 'gradient-border featured-glow' : 'glass-card glass-card-hover' }} {{ $isFeatured ? 'scale-[1.02] lg:scale-105' : '' }}">

                        {{-- Top Gradient Bar --}}
                        <div class="h-1 w-full bg-gradient-to-r {{ $isTrial ? 'from-emerald-500 via-teal-500 to-cyan-500' : ($isFeatured ? 'from-[#ff6b6b] via-[#f368e0] to-[#a55eea]' : 'from-gray-600 via-gray-500 to-gray-600') }}"></div>

                        <div class="p-6 sm:p-8 flex flex-col h-full {{ $isFeatured ? 'bg-gradient-to-br from-[#1a1a1a] to-[#0d0d0d]' : '' }}">

                            {{-- Plan Name --}}
                            <div class="mb-6">
                                <h3 class="text-2xl font-bold text-white mb-1">
                                    {{ $plan->getTranslated('title') }}
                                </h3>
                                @if($plan->getTranslated('description'))
                                <p class="text-sm text-gray-500">{{ $plan->getTranslated('description') }}</p>
                                @endif
                            </div>

                            {{-- Cycle Selector (if multiple cycles) --}}
                            @if(count($cycles) > 1)
                            <div class="flex flex-wrap gap-2 mb-6 p-1 rounded-xl bg-white/5">
                                @foreach($cycles as $cycleKey => $cycle)
                                    @php
                                        $cycleLabel = $cycle['label'][app()->getLocale()] ?? $cycle['label']['tr'] ?? $cycleKey;
                                    @endphp
                                    <button
                                        type="button"
                                        x-on:click="selectedCycle[{{ $plan->subscription_plan_id }}] = '{{ $cycleKey }}'"
                                        :class="selectedCycle[{{ $plan->subscription_plan_id }}] === '{{ $cycleKey }}' ? 'active text-white' : 'text-gray-400 hover:text-white'"
                                        class="cycle-pill flex-1 px-3 py-2 text-sm font-medium rounded-lg transition-all">
                                        {{ $cycleLabel }}
                                    </button>
                                @endforeach
                            </div>
                            @endif

                            {{-- Price Display --}}
                            <div class="mb-8">
                                @foreach($cycles as $cycleKey => $cycle)
                                    @php
                                        $price = $cycle['price'] ?? 0;
                                        $comparePrice = $cycle['compare_price'] ?? null;
                                        $durationDays = $cycle['duration_days'] ?? 30;
                                        $monthlyPrice = $durationDays > 0 ? round(($price / $durationDays) * 30) : $price;
                                        $discount = $comparePrice ? round((($comparePrice - $price) / $comparePrice) * 100) : 0;
                                    @endphp

                                    <div x-show="selectedCycle[{{ $plan->subscription_plan_id }}] === '{{ $cycleKey }}'"
                                         x-transition:enter="transition ease-out duration-300"
                                         x-transition:enter-start="opacity-0 transform scale-95"
                                         x-transition:enter-end="opacity-100 transform scale-100">

                                        @if($isTrial)
                                            <div class="flex items-baseline gap-2">
                                                <span class="text-5xl font-black bg-gradient-to-r from-emerald-400 to-teal-400 bg-clip-text text-transparent">
                                                    {{ __('front.free') ?? 'ÜCRETSİZ' }}
                                                </span>
                                            </div>
                                            <p class="text-sm text-gray-500 mt-2">
                                                <i class="fas fa-clock mr-1"></i>
                                                {{ $durationDays }} {{ __('front.days_trial') ?? 'gün deneme süresi' }}
                                            </p>
                                        @else
                                            {{-- Compare Price --}}
                                            @if($comparePrice)
                                            <div class="flex items-center gap-3 mb-2">
                                                <span class="text-lg text-gray-500 line-through">
                                                    {{ number_format($comparePrice, 0, ',', '.') }}₺
                                                </span>
                                                <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                                                    %{{ $discount }} {{ __('front.save') ?? 'TASARRUF' }}
                                                </span>
                                            </div>
                                            @endif

                                            {{-- Main Price --}}
                                            <div class="flex items-baseline gap-1">
                                                <span class="text-5xl sm:text-6xl font-black {{ $isFeatured ? 'bg-gradient-to-r from-[#ff6b6b] via-[#f368e0] to-[#a55eea] bg-clip-text text-transparent' : 'text-white' }}">
                                                    {{ number_format($price, 0, ',', '.') }}
                                                </span>
                                                <span class="text-2xl {{ $isFeatured ? 'text-[#ff6b6b]' : 'text-gray-400' }}">₺</span>
                                            </div>

                                            {{-- Monthly Equivalent --}}
                                            @if($durationDays > 30)
                                            <p class="text-sm text-gray-500 mt-2">
                                                <span class="text-white font-semibold">{{ number_format($monthlyPrice, 0, ',', '.') }}₺</span>/{{ __('front.month') ?? 'ay' }}
                                            </p>
                                            @endif
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                            {{-- Features List --}}
                            @php
                                $features = $plan->features ?? [];
                            @endphp
                            @if(!empty($features))
                            <div class="flex-1 space-y-3 mb-8">
                                @foreach($features as $featureIndex => $feature)
                                    @php
                                        if (str_contains($feature, '|')) {
                                            [$icon, $text] = explode('|', $feature, 2);
                                        } else {
                                            $icon = 'fas fa-check';
                                            $text = $feature;
                                        }
                                    @endphp
                                    <div class="flex items-start gap-3 text-sm"
                                         x-intersect.once="$el.classList.add('visible')"
                                         style="animation-delay: {{ $featureIndex * 0.1 }}s">
                                        <div class="flex-shrink-0 w-5 h-5 rounded-full {{ $isFeatured ? 'bg-gradient-to-r from-[#ff6b6b] to-[#f368e0]' : ($isTrial ? 'bg-gradient-to-r from-emerald-500 to-teal-500' : 'bg-white/10') }} flex items-center justify-center mt-0.5">
                                            <i class="{{ $icon }} text-white text-xs"></i>
                                        </div>
                                        <span class="text-gray-300">{{ $text }}</span>
                                    </div>
                                @endforeach
                            </div>
                            @endif

                            {{-- CTA Button --}}
                            <div class="mt-auto">
                                @foreach($cycles as $cycleKey => $cycle)
                                    <div x-show="selectedCycle[{{ $plan->subscription_plan_id }}] === '{{ $cycleKey }}'">
                                        <button
                                            @if($isTrial)
                                                wire:click="startTrial({{ $plan->subscription_plan_id }}, '{{ $cycleKey }}')"
                                            @else
                                                wire:click="addToCart({{ $plan->subscription_plan_id }}, '{{ $cycleKey }}', true)"
                                            @endif
                                            wire:loading.attr="disabled"
                                            wire:loading.class="opacity-60 cursor-wait"
                                            class="btn-shine w-full py-4 px-6 rounded-2xl font-bold text-base sm:text-lg transition-all duration-300 transform hover:scale-[1.02] active:scale-[0.98] disabled:opacity-60 disabled:cursor-wait
                                                {{ $isFeatured
                                                    ? 'bg-gradient-to-r from-[#ff6b6b] via-[#f368e0] to-[#a55eea] text-white shadow-lg shadow-[#ff6b6b]/30 hover:shadow-xl hover:shadow-[#ff6b6b]/40'
                                                    : ($isTrial
                                                        ? 'bg-gradient-to-r from-emerald-500 to-teal-500 text-white shadow-lg shadow-emerald-500/30 hover:shadow-xl hover:shadow-emerald-500/40'
                                                        : 'bg-white text-black hover:bg-gray-100 shadow-lg shadow-white/10')
                                                }}">
                                            <span wire:loading.remove wire:target="{{ $isTrial ? 'startTrial' : 'addToCart' }}({{ $plan->subscription_plan_id }}, '{{ $cycleKey }}'{{ $isTrial ? '' : ', true' }})">
                                                @if($isTrial)
                                                    <i class="fas fa-gift mr-2"></i>{{ __('front.start_free') ?? 'Ücretsiz Başla' }}
                                                @else
                                                    <i class="fas fa-crown mr-2"></i>{{ __('front.get_premium') ?? 'Premium\'a Geç' }}
                                                @endif
                                            </span>
                                            <span wire:loading wire:target="{{ $isTrial ? 'startTrial' : 'addToCart' }}({{ $plan->subscription_plan_id }}, '{{ $cycleKey }}'{{ $isTrial ? '' : ', true' }})">
                                                <i class="fas fa-spinner fa-spin mr-2"></i>{{ __('front.loading') ?? 'Yükleniyor...' }}
                                            </span>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Trust Badges --}}
        <div class="mt-16 flex flex-wrap justify-center items-center gap-6 sm:gap-10 text-gray-500">
            <div class="flex items-center gap-2">
                <i class="fas fa-shield-alt text-emerald-500"></i>
                <span class="text-sm">{{ __('front.secure_payment') ?? 'Güvenli Ödeme' }}</span>
            </div>
            <div class="flex items-center gap-2">
                <i class="fas fa-undo text-blue-500"></i>
                <span class="text-sm">{{ __('front.cancel_anytime') ?? 'İstediğiniz Zaman İptal' }}</span>
            </div>
            <div class="flex items-center gap-2">
                <i class="fas fa-headset text-purple-500"></i>
                <span class="text-sm">{{ __('front.support_247') ?? '7/24 Destek' }}</span>
            </div>
        </div>

        @else
        {{-- No Plans Message --}}
        <div class="text-center py-20">
            <div class="w-24 h-24 mx-auto mb-6 rounded-full bg-white/5 flex items-center justify-center">
                <i class="fas fa-crown text-4xl text-gray-600"></i>
            </div>
            <h3 class="text-2xl font-bold text-white mb-3">
                {{ __('front.no_plans_yet') ?? 'Henüz Aktif Plan Yok' }}
            </h3>
            <p class="text-gray-500 text-lg">
                {{ __('front.plans_coming_soon') ?? 'Yakında yeni planlar eklenecek' }}
            </p>
        </div>
        @endif
        @endif {{-- hasEnoughSubscription --}}

    </div>
</div>
</div>

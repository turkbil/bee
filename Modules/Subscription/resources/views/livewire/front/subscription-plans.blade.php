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
    /* Animated Background Orbs - Subtle */
    .orb {
        position: absolute;
        border-radius: 50%;
        filter: blur(120px);
        opacity: 0.15;
        animation: float 30s ease-in-out infinite;
    }
    .orb-1 { width: 300px; height: 300px; background: #ff6b6b; top: -150px; left: -150px; animation-delay: 0s; }
    .orb-2 { width: 250px; height: 250px; background: #a55eea; bottom: 10%; right: -100px; animation-delay: -5s; }
    .orb-3 { width: 200px; height: 200px; background: #00d2d3; top: 60%; left: -50px; animation-delay: -10s; }

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

    /* Featured Card Glow - Subtle */
    .featured-glow {
        box-shadow:
            0 0 40px -10px rgba(255, 107, 107, 0.25),
            0 25px 50px -12px rgba(0, 0, 0, 0.5);
        border: 1px solid rgba(255, 107, 107, 0.3);
    }

    .featured-glow:hover {
        box-shadow:
            0 0 50px -10px rgba(255, 107, 107, 0.35),
            0 25px 50px -12px rgba(0, 0, 0, 0.6);
        border-color: rgba(255, 107, 107, 0.5);
    }

    /* Featured Border */
    .gradient-border {
        position: relative;
    }

    /* Animated Gradient Price */
    .price-gradient {
        background: linear-gradient(90deg, #ff6b6b, #f368e0, #a55eea, #00d2d3, #ff6b6b);
        background-size: 300% 100%;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        animation: priceGradientFlow 6s ease infinite;
    }

    .price-gradient-emerald {
        background: linear-gradient(90deg, #10b981, #14b8a6, #06b6d4, #10b981);
        background-size: 300% 100%;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        animation: priceGradientFlow 6s ease infinite;
    }

    @keyframes priceGradientFlow {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    /* Card Hover Effects */
    .plan-card-inner {
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .plan-card:hover .plan-card-inner {
        border-color: rgba(255, 255, 255, 0.2);
    }

    .plan-card:hover .card-glow {
        opacity: 1;
    }

    .card-glow {
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at 50% 0%, rgba(255,255,255,0.08) 0%, transparent 60%);
        opacity: 0;
        transition: opacity 0.4s ease;
        pointer-events: none;
        border-radius: 24px;
    }

    /* Hover border glow */
    .plan-card:hover .glass-card {
        border-color: rgba(255, 255, 255, 0.15);
        box-shadow: 0 0 30px -10px rgba(255, 255, 255, 0.1);
    }

    .plan-card:hover .featured-glow {
        border-color: rgba(255, 107, 107, 0.5);
        box-shadow: 0 0 50px -10px rgba(255, 107, 107, 0.4), 0 25px 50px -12px rgba(0, 0, 0, 0.6);
    }

    /* Icon pulse on hover */
    .plan-card:hover .feature-icon {
        transform: scale(1.1);
    }

    .feature-icon {
        transition: transform 0.3s ease;
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
        background: #ff6b6b;
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

<div class="relative min-h-screen overflow-hidden bg-gradient-to-br from-sub-darker via-sub-darker to-sub-darker py-12 px-4 sm:px-6 lg:px-8">
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
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black mb-6 tracking-tight leading-tight">
                <span class="text-white">Sınırsız</span>
                <br class="sm:hidden">
                <span class="text-sub-coral">Müzik Keyfi</span>
            </h1>

            <p class="text-lg sm:text-xl text-gray-400 max-w-2xl mx-auto leading-relaxed">
                İşletmenize en uygun planı seçin, telifsiz müziğin keyfini çıkarın
            </p>
        </div>

        {{-- 30+ Gün Aboneliği Var - Engelleme Mesajı --}}
        @if($hasEnoughSubscription ?? false)
        <div class="max-w-2xl mx-auto">
            <div class="glass-card rounded-3xl p-8 sm:p-12 text-center">
                {{-- Crown Icon --}}
                <div class="w-24 h-24 mx-auto mb-6 rounded-full bg-gradient-to-r from-yellow-500/20 to-orange-500/20 flex items-center justify-center">
                    <i class="fas fa-crown text-5xl text-yellow-400"></i>
                </div>

                {{-- Title --}}
                <h2 class="text-2xl sm:text-3xl font-bold text-white mb-4">
                    Premium Uyeliginiz Aktif
                </h2>

                {{-- Description --}}
                <p class="text-gray-400 text-lg mb-6 leading-relaxed">
                    Zaten <span class="text-white font-semibold">{{ $remainingDays ?? 0 }} gun</span> sureli aktif aboneliginiz bulunuyor.
                    <br class="hidden sm:inline">
                    Yeni paket satin almak icin mevcut surenizin bitmesine yakin tekrar ziyaret edin.
                </p>

                {{-- Expiry Date --}}
                <div class="inline-flex items-center gap-3 px-6 py-3 bg-gradient-to-r from-yellow-500/10 to-orange-500/10 border border-yellow-500/30 rounded-xl mb-8">
                    <i class="fas fa-calendar-check text-yellow-400"></i>
                    <span class="text-white">
                        Bitis Tarihi: <span class="font-semibold text-yellow-400">{{ $expiresAt ?? '-' }}</span>
                    </span>
                </div>

                {{-- Actions --}}
                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    <a href="/dashboard" class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-white/10 hover:bg-white/20 text-white font-medium rounded-xl transition" data-spa>
                        <i class="fas fa-home"></i>
                        Dashboard'a Don
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
        @php
            $visiblePlans = $plans->filter(function($plan) use ($userHasUsedTrial) {
                return !($plan->is_trial && $userHasUsedTrial);
            })->filter(function($plan) {
                return !empty($plan->getSortedCycles());
            });
            $planCount = $visiblePlans->count();
        @endphp

        @if($planCount > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 {{ $planCount >= 3 ? 'lg:grid-cols-3' : '' }} gap-6 lg:gap-8 max-w-5xl mx-auto items-stretch">
            @foreach($plans as $planIndex => $plan)
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

                    {{-- Featured Badge - Çizgiye ortalı --}}
                    @if($isFeatured)
                    <div class="absolute -top-3 left-1/2 -translate-x-1/2 z-20">
                        <div class="flex items-center gap-1.5 px-4 py-1.5 rounded-full bg-sub-coral text-white text-xs font-bold shadow-lg">
                            <i class="fas fa-crown text-yellow-300"></i>
                            <span>EN POPÜLER</span>
                        </div>
                    </div>
                    @endif

                    {{-- Trial Badge --}}
                    @if($isTrial)
                    <div class="absolute -top-4 left-1/2 -translate-x-1/2 z-20">
                        <div class="flex items-center gap-1.5 px-4 py-1.5 rounded-full bg-emerald-500 text-white text-xs font-bold shadow-lg">
                            <i class="fas fa-gift"></i>
                            <span>ÜCRETSİZ DENE</span>
                        </div>
                    </div>
                    @endif

                    {{-- Card --}}
                    <div class="relative h-full rounded-3xl overflow-hidden transition-all duration-500 {{ $isFeatured ? 'gradient-border featured-glow' : 'glass-card glass-card-hover' }}">

                        {{-- Hover Glow Effect --}}
                        <div class="card-glow"></div>

                        {{-- Top Accent Bar --}}
                        <div class="h-1 w-full {{ $isTrial ? 'bg-emerald-500' : ($isFeatured ? 'bg-sub-coral' : 'bg-gray-700') }}"></div>

                        <div class="p-6 sm:p-8 flex flex-col h-full {{ $isFeatured ? 'bg-sub-dark' : '' }}">

                            {{-- Plan Name --}}
                            <div class="mb-4">
                                <h3 class="text-xl font-bold text-white mb-1">
                                    {{ $plan->getTranslated('title') }}
                                </h3>
                                @if($plan->getTranslated('description'))
                                <p class="text-sm text-gray-500 line-clamp-2">
                                    {{ $plan->getTranslated('description') }}
                                </p>
                                @endif
                            </div>

                            {{-- Cycle Selector --}}
                            @if(count($cycles) > 1)
                            <div class="mb-3">
                                <div class="flex flex-wrap gap-2 p-1 rounded-xl bg-white/5">
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
                            </div>
                            @endif

                            {{-- Price Display - Simetri için min-height --}}
                            <div class="mb-6 min-h-[140px]">
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
                                                <span class="text-4xl sm:text-5xl font-black price-gradient-emerald">
                                                    ÜCRETSİZ
                                                </span>
                                            </div>
                                            <p class="text-sm text-gray-500 mt-2">
                                                <i class="fas fa-clock mr-1"></i>
                                                {{ $durationDays }} gün deneme süresi
                                            </p>
                                        @else
                                            {{-- 1. Ana Fiyat --}}
                                            <div class="flex items-baseline gap-1">
                                                <span class="text-4xl sm:text-5xl font-black price-gradient">
                                                    {{ number_format($price, 2, ',', '.') }}
                                                </span>
                                                <span class="text-lg text-gray-400 ml-1">TL</span>
                                            </div>

                                            {{-- 2. KDV Satırı --}}
                                            <p class="text-sm text-gray-400 mt-1">
                                                <span class="text-white/70">+ KDV</span>
                                                <span class="text-gray-600 mx-1">/</span>
                                                @if($durationDays <= 31)
                                                    <span>Ay</span>
                                                @elseif($durationDays <= 366)
                                                    <span>Yıl</span>
                                                @else
                                                    <span>{{ $durationDays }} Gün</span>
                                                @endif
                                            </p>

                                            {{-- 3. Compare Price + Aylık Karşılık (sadece yıllık için) --}}
                                            @if($comparePrice)
                                            <div class="mt-3 space-y-1">
                                                <div class="flex items-center gap-3">
                                                    <span class="text-sm text-gray-500 line-through">
                                                        {{ number_format($comparePrice, 0, ',', '.') }} TL + KDV
                                                    </span>
                                                    <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                                                        %{{ $discount }} TASARRUF
                                                    </span>
                                                </div>
                                                @if($durationDays > 31)
                                                <p class="text-sm text-gray-500">
                                                    Aylık yalnızca <span class="text-white font-semibold">{{ number_format($monthlyPrice, 0, ',', '.') }} TL</span> + KDV'ye denk gelir
                                                </p>
                                                @endif
                                            </div>
                                            @endif
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                            {{-- Features List --}}
                            @php
                                // Features multi-language ise doğru dili seç
                                $rawFeatures = $plan->features ?? [];
                                if (is_array($rawFeatures) && isset($rawFeatures['tr'])) {
                                    $currentLocale = app()->getLocale();
                                    $features = $rawFeatures[$currentLocale] ?? $rawFeatures['tr'] ?? [];
                                } else {
                                    $features = $rawFeatures;
                                }
                            @endphp
                            {{-- Features - Sabit min yükseklik (8 özellik için ~250px) --}}
                            <div class="flex-1 mb-6 min-h-[250px]">
                                @if(!empty($features))
                                <div>
                                    @foreach($features as $featureIndex => $feature)
                                        @php
                                            // Array ise (nested) skip et
                                            if (is_array($feature)) {
                                                continue;
                                            }

                                            // String kontrolü - icon|text formatı
                                            if (is_string($feature) && str_contains($feature, '|')) {
                                                [$icon, $text] = explode('|', $feature, 2);
                                            } else {
                                                $icon = 'fal fa-check';
                                                $text = is_string($feature) ? $feature : '';
                                            }

                                            // Light ve Solid versiyonları oluştur
                                            $iconLight = str_replace(['fas ', 'far ', 'fab '], 'fal ', $icon);
                                            $iconSolid = str_replace(['fal ', 'far ', 'fab '], 'fas ', $icon);
                                        @endphp
                                        <div class="feature-row group/feature flex items-start gap-3 text-sm cursor-default py-2">
                                            <div class="feature-icon flex-shrink-0 w-5 h-5 flex items-center justify-center mt-0.5 relative">
                                                {{-- Light icon (default) --}}
                                                <i class="{{ $iconLight }} {{ $isFeatured ? 'text-sub-coral' : ($isTrial ? 'text-emerald-400' : 'text-gray-400') }} text-base absolute transition-all duration-200 opacity-100 group-hover/feature:opacity-0"></i>
                                                {{-- Solid icon (hover) --}}
                                                <i class="{{ $iconSolid }} {{ $isFeatured ? 'text-sub-coral' : ($isTrial ? 'text-emerald-400' : 'text-white') }} text-base absolute transition-all duration-200 opacity-0 group-hover/feature:opacity-100"></i>
                                            </div>
                                            <span class="text-gray-300 group-hover/feature:text-white transition-colors duration-200">{{ $text }}</span>
                                        </div>
                                    @endforeach
                                </div>
                                @endif
                            </div>

                            {{-- CTA Button - Her Zaman En Altta --}}
                            <div class="mt-auto pt-4">
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
                                                    ? 'bg-sub-coral hover:bg-sub-coral-hover text-white shadow-lg'
                                                    : ($isTrial
                                                        ? 'bg-emerald-500 hover:bg-emerald-600 text-white shadow-lg'
                                                        : 'bg-white text-black hover:bg-gray-100 shadow-lg')
                                                }}">
                                            <span wire:loading.remove wire:target="{{ $isTrial ? 'startTrial' : 'addToCart' }}({{ $plan->subscription_plan_id }}, '{{ $cycleKey }}'{{ $isTrial ? '' : ', true' }})">
                                                @if($isTrial)
                                                    <i class="fas fa-gift mr-2"></i>Ücretsiz Başla
                                                @else
                                                    @if($isGuest)
                                                        @if($trialDays > 0)
                                                            <i class="fas fa-user-plus mr-2"></i>Üye Ol, {{ $trialDays }} Gün Ücretsiz Dinle
                                                        @else
                                                            <i class="fas fa-user-plus mr-2"></i>Üye Ol
                                                        @endif
                                                    @elseif(!$isPremium)
                                                        <i class="fas fa-crown mr-2"></i>Premium Ol
                                                    @else
                                                        <i class="fas fa-crown mr-2"></i>Üyeliğini Uzat
                                                    @endif
                                                @endif
                                            </span>
                                            <span wire:loading wire:target="{{ $isTrial ? 'startTrial' : 'addToCart' }}({{ $plan->subscription_plan_id }}, '{{ $cycleKey }}'{{ $isTrial ? '' : ', true' }})">
                                                <i class="fas fa-spinner fa-spin mr-2"></i>Yükleniyor...
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
                <span class="text-sm">Güvenli Ödeme</span>
            </div>
            <div class="flex items-center gap-2">
                <i class="fas fa-undo text-blue-500"></i>
                <span class="text-sm">İstediğiniz Zaman İptal</span>
            </div>
            <div class="flex items-center gap-2">
                <i class="fas fa-headset text-purple-500"></i>
                <span class="text-sm">7/24 Destek</span>
            </div>
        </div>

        @else
        {{-- No Plans Message --}}
        <div class="text-center py-20">
            <div class="w-24 h-24 mx-auto mb-6 rounded-full bg-white/5 flex items-center justify-center">
                <i class="fas fa-crown text-4xl text-gray-600"></i>
            </div>
            <h3 class="text-2xl font-bold text-white mb-3">
                Henüz Aktif Plan Yok
            </h3>
            <p class="text-gray-500 text-lg">
                Yakında yeni planlar eklenecek
            </p>
        </div>
        @endif
        @endif {{-- hasEnoughSubscription --}}

    </div>
</div>
</div>

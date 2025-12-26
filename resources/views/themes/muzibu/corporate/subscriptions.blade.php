@extends('themes.muzibu.layouts.app')

@section('title', 'Üyelikleri Yönet - ' . ($account->company_name ?? 'Kurumsal'))

@section('content')
{{-- JSON Data Element --}}
<div id="corporate-subscriptions-data" style="display:none;">@json(['plans' => $plans, 'members' => $members])</div>

{{-- Alpine Component - Inline Data (SPA Compatible) --}}
<div x-data="{
    plans: [],
    members: [],
    selectedPlanId: null,
    selectedCycleKey: null,
    selectedUserIds: [],
    purchasing: false,

    get selectedPlan() {
        return this.plans.find(p => p.subscription_plan_id == this.selectedPlanId) || null;
    },
    get selectedCycle() {
        if (!this.selectedPlan || !this.selectedCycleKey) return null;
        return this.selectedPlan.billing_cycles?.[this.selectedCycleKey] || null;
    },
    get pricePerUser() {
        return this.selectedCycle?.price || 0;
    },
    get selectedCount() {
        return this.selectedUserIds.length;
    },
    get totalPrice() {
        return this.pricePerUser * this.selectedCount;
    },
    get canPurchase() {
        return this.selectedPlanId && this.selectedCycleKey && this.selectedCount > 0;
    },

    init() {
        const dataEl = document.getElementById('corporate-subscriptions-data');
        if (dataEl) {
            try {
                const data = JSON.parse(dataEl.textContent);
                this.plans = data.plans || [];
                this.members = data.members || [];
            } catch (e) {
                console.error('JSON parse error:', e);
            }
        }
        if (this.plans.length > 0) {
            this.selectPlan(this.plans[0]);
        }
    },
    selectPlan(plan) {
        if (!plan || !plan.subscription_plan_id) return;
        this.selectedPlanId = plan.subscription_plan_id;
        const cycles = plan.billing_cycles || {};
        this.selectedCycleKey = Object.keys(cycles)[0] || null;
    },
    selectPlanById(planId) {
        const plan = this.plans.find(p => p.subscription_plan_id == planId);
        if (plan) this.selectPlan(plan);
    },
    isUserLocked(userId) {
        const member = this.members.find(m => m.user_id === userId);
        if (!member) return false;
        return member.subscription?.is_active && member.subscription?.days_left > 30;
    },
    toggleUser(userId) {
        if (this.isUserLocked(userId)) return;
        const idx = this.selectedUserIds.indexOf(userId);
        if (idx > -1) {
            this.selectedUserIds.splice(idx, 1);
        } else {
            this.selectedUserIds.push(userId);
        }
    },
    isUserSelected(userId) {
        return this.selectedUserIds.includes(userId);
    },
    selectAll() {
        this.selectedUserIds = this.members
            .filter(m => !(m.subscription?.is_active && m.subscription?.days_left > 30))
            .map(m => m.user_id);
    },
    selectNone() {
        this.selectedUserIds = [];
    },
    selectExpired() {
        this.selectedUserIds = this.members.filter(m => !m.subscription.is_active).map(m => m.user_id);
    },
    formatPrice(price) {
        return new Intl.NumberFormat('tr-TR').format(price);
    },
    purchase() {
        if (!this.canPurchase || this.purchasing) return;
        this.purchasing = true;
        const params = new URLSearchParams({
            plan: this.selectedPlanId,
            cycle: this.selectedCycleKey,
            users: this.selectedUserIds.join(','),
            focus: 'payment'
        });
        window.location.href = '/cart/checkout?' + params.toString();
    }
}" x-init="init()" class="min-h-screen pb-24">

    {{-- Hero Header --}}
    <div class="relative">
        <div class="absolute inset-0 bg-gradient-to-br from-purple-600/20 via-pink-600/10 to-transparent"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 pb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <a href="/corporate/dashboard" class="inline-flex items-center text-purple-300 hover:text-white text-sm mb-3 transition-colors" data-spa>
                        <i class="fas fa-arrow-left mr-2"></i>
                        Dashboard'a Dön
                    </a>
                    <h1 class="text-3xl font-bold text-white">Üyelikleri Yönet</h1>
                    <p class="text-gray-400 mt-1">{{ $account->company_name ?? 'Kurumsal' }} - Ekibiniz için premium üyelik satın alın</p>
                </div>
                <div class="flex items-center gap-3 px-4 py-2 bg-purple-500/10 border border-purple-500/30 rounded-xl">
                    <i class="fas fa-building text-purple-400"></i>
                    <span class="text-purple-300 font-medium">{{ count($members) }} Üye</span>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

        {{-- Bilgi Notu --}}
        <div class="bg-blue-500/10 border border-blue-500/30 rounded-xl p-4 mb-6 flex items-start gap-3">
            <div class="w-8 h-8 bg-blue-500/20 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                <i class="fas fa-info-circle text-blue-400"></i>
            </div>
            <div>
                <p class="text-blue-300 font-medium">Üyelik Satın Alma Kuralı</p>
                <p class="text-blue-200/70 text-sm mt-1">Üyelik süresinin bitmesine <strong class="text-blue-300">30 gün veya daha az</strong> kalan üyeler için yeni üyelik satın alabilirsiniz. 30 günden fazla süresi olan üyeler kilitli görünür ve seçilemez.</p>
            </div>
        </div>

        {{-- Boş Üye Kontrolü --}}
        @if(empty($members))
            <div class="max-w-md mx-auto text-center py-16">
                <div class="bg-slate-800/50 rounded-2xl p-8 border border-white/10">
                    <div class="w-16 h-16 bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-users text-gray-500 text-2xl"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-white mb-2">Henüz Üye Yok</h2>
                    <p class="text-gray-400 mb-6">Kurumsal hesabınıza henüz üye eklenmemiş.</p>
                    <a href="/corporate/dashboard" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-purple-500 to-pink-500 hover:opacity-90 text-white font-medium rounded-xl transition shadow-lg" data-spa>
                        <i class="fas fa-arrow-left mr-2"></i>Dashboard'a Dön
                    </a>
                </div>
            </div>
        @else

        {{-- MAIN LAYOUT: 2 COLUMN --}}
        <div class="flex flex-col lg:flex-row gap-6">

            {{-- ===================== SOL KOLON ===================== --}}
            <div class="flex-1 space-y-6">

                {{-- Plan Seçimi --}}
                <div class="bg-slate-800/50 rounded-2xl border border-white/10 overflow-hidden">
                    <div class="px-5 py-4 border-b border-white/10 bg-white/5">
                        <h2 class="text-lg font-semibold text-white flex items-center">
                            <div class="w-8 h-8 bg-gradient-to-br from-yellow-500 to-orange-500 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-crown text-white text-sm"></i>
                            </div>
                            Plan Seçin
                        </h2>
                    </div>

                    <div class="p-5">
                        <div class="grid sm:grid-cols-2 gap-4">
                            @foreach($plans as $index => $plan)
                                @php
                                    $cycles = $plan->getSortedCycles();
                                    $firstCycleKey = array_key_first($cycles);
                                    $firstCycle = $cycles[$firstCycleKey] ?? null;
                                    $planId = $plan->subscription_plan_id;
                                @endphp

                                <div @click="selectPlanById({{ $planId }})"
                                     :class="selectedPlanId == {{ $planId }} ? 'ring-2 ring-purple-500 border-purple-500/50 bg-purple-500/10' : 'border-white/10 hover:border-white/20 bg-slate-900/50'"
                                     class="relative rounded-xl p-5 cursor-pointer transition-all border group h-[180px]">

                                    @if($plan->is_featured)
                                        <div class="absolute -top-2 -right-2 z-10">
                                            <span class="px-2.5 py-1 bg-gradient-to-r from-yellow-500 to-orange-500 text-white text-xs font-bold rounded-full shadow-lg">
                                                POPÜLER
                                            </span>
                                        </div>
                                    @endif

                                    {{-- Başlık --}}
                                    <div class="flex items-start justify-between mb-3">
                                        <div>
                                            <h3 class="text-white font-semibold text-lg">{{ $plan->getTranslated('title') }}</h3>
                                            @if($plan->getTranslated('subtitle'))
                                                <p class="text-gray-500 text-sm mt-0.5">{{ $plan->getTranslated('subtitle') }}</p>
                                            @endif
                                        </div>
                                        <div :class="selectedPlanId == {{ $planId }} ? 'opacity-100 scale-100' : 'opacity-0 scale-75'"
                                             class="w-6 h-6 bg-gradient-to-r from-purple-500 to-pink-500 rounded-full flex items-center justify-center flex-shrink-0 transition-all duration-200">
                                            <i class="fas fa-check text-white text-xs"></i>
                                        </div>
                                    </div>

                                    {{-- Fiyat/Dönem Alanı - SABİT YÜKSEKLİK, ABSOLUTE POZİSYON --}}
                                    <div class="relative h-[80px]">
                                        {{-- Fiyat (seçili değilken) --}}
                                        @if($firstCycle)
                                            <div :class="selectedPlanId == {{ $planId }} ? 'opacity-0 pointer-events-none' : 'opacity-100'"
                                                 class="absolute inset-0 flex items-center transition-opacity duration-150">
                                                <div class="flex items-baseline gap-1">
                                                    <span class="text-2xl font-black text-white">{{ number_format($firstCycle['price'] ?? 0, 0, ',', '.') }}</span>
                                                    <span class="text-lg text-gray-400">TL</span>
                                                    <span class="text-gray-500 text-sm ml-1">/ {{ $firstCycle['label']['tr'] ?? $firstCycleKey }}</span>
                                                </div>
                                            </div>
                                        @endif

                                        {{-- Dönem Seçimi (seçiliyken) --}}
                                        <div :class="selectedPlanId == {{ $planId }} ? 'opacity-100' : 'opacity-0 pointer-events-none'"
                                             class="absolute inset-0 flex items-start pt-1 transition-opacity duration-150 overflow-hidden">
                                            <div class="flex flex-wrap gap-2">
                                                @foreach($cycles as $cycleKey => $cycle)
                                                    <button type="button"
                                                            @click.stop="selectedCycleKey = '{{ $cycleKey }}'"
                                                            :class="selectedCycleKey === '{{ $cycleKey }}'
                                                                ? 'bg-gradient-to-r from-purple-500 to-pink-500 text-white border-transparent'
                                                                : 'bg-white/5 text-gray-300 border-white/10 hover:bg-white/10'"
                                                            class="px-3 py-1.5 rounded-lg text-xs font-medium transition border whitespace-nowrap">
                                                        {{ $cycle['label']['tr'] ?? $cycleKey }} {{ number_format($cycle['price'] ?? 0, 0, ',', '.') }}₺
                                                    </button>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Üye Listesi --}}
                <div class="bg-slate-800/50 rounded-2xl border border-white/10 overflow-hidden">
                    <div class="px-5 py-4 border-b border-white/10 bg-white/5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <h2 class="text-lg font-semibold text-white flex items-center">
                            <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-users text-white text-sm"></i>
                            </div>
                            Üyeleri Seçin
                            <span class="ml-2 text-sm font-normal text-gray-400">({{ count($members) }} üye)</span>
                        </h2>
                        <div class="flex gap-2">
                            <button @click="selectExpired()" class="px-3 py-1.5 bg-red-500/10 hover:bg-red-500/20 text-red-400 rounded-lg text-xs font-medium transition border border-red-500/20">
                                <i class="fas fa-clock mr-1"></i>Süresi Dolanlar
                            </button>
                            <button @click="selectAll()" class="px-3 py-1.5 bg-white/5 hover:bg-white/10 text-white rounded-lg text-xs font-medium transition border border-white/10">
                                Tümü
                            </button>
                            <button @click="selectNone()" class="px-3 py-1.5 bg-white/5 hover:bg-white/10 text-gray-400 rounded-lg text-xs font-medium transition border border-white/10">
                                Temizle
                            </button>
                        </div>
                    </div>

                    <div class="p-5">
                        <div class="space-y-3 max-h-[500px] overflow-y-auto scrollbar-hide">
                            @foreach($members as $index => $member)
                                @php
                                    $colors = [
                                        'from-purple-500 to-pink-500',
                                        'from-blue-500 to-cyan-500',
                                        'from-green-500 to-emerald-500',
                                        'from-orange-500 to-red-500',
                                        'from-indigo-500 to-purple-500',
                                    ];
                                    $colorClass = $colors[$index % count($colors)];
                                    $isLocked = $member['subscription']['is_active'] && ($member['subscription']['days_left'] ?? 0) > 30;
                                @endphp
                                <div @click="toggleUser({{ $member['user_id'] }})"
                                     :class="isUserLocked({{ $member['user_id'] }})
                                        ? 'opacity-50 cursor-not-allowed border-white/5 bg-slate-900/30'
                                        : (isUserSelected({{ $member['user_id'] }})
                                            ? 'ring-2 ring-purple-500 border-purple-500/50 bg-purple-500/10'
                                            : 'border-white/10 hover:border-white/20 bg-slate-900/50')"
                                     class="flex items-center gap-4 p-4 rounded-xl transition-all border"
                                     :style="isUserLocked({{ $member['user_id'] }}) ? '' : 'cursor: pointer'">

                                    {{-- Checkbox / Lock Icon --}}
                                    <div x-show="!isUserLocked({{ $member['user_id'] }})"
                                         :class="isUserSelected({{ $member['user_id'] }})
                                            ? 'bg-gradient-to-r from-purple-500 to-pink-500 border-transparent'
                                            : 'bg-transparent border-gray-600'"
                                         class="w-5 h-5 rounded border-2 flex items-center justify-center flex-shrink-0 transition">
                                        <i x-show="isUserSelected({{ $member['user_id'] }})" class="fas fa-check text-white text-xs"></i>
                                    </div>
                                    <div x-show="isUserLocked({{ $member['user_id'] }})"
                                         class="w-5 h-5 rounded flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-lock text-gray-600 text-xs"></i>
                                    </div>

                                    {{-- Avatar --}}
                                    <div class="w-11 h-11 rounded-xl bg-gradient-to-br {{ $colorClass }} flex items-center justify-center text-white text-sm font-bold flex-shrink-0 shadow-lg"
                                         :class="isUserLocked({{ $member['user_id'] }}) ? 'grayscale' : ''">
                                        {{ $member['initials'] }}
                                    </div>

                                    {{-- Info --}}
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2">
                                            <p class="text-white font-medium truncate" :class="isUserLocked({{ $member['user_id'] }}) ? 'text-gray-400' : ''">{{ $member['name'] }}</p>
                                            @if($member['is_owner'])
                                                <span class="px-2 py-0.5 bg-yellow-500/20 text-yellow-400 rounded-full text-xs border border-yellow-500/30 flex items-center gap-1">
                                                    <i class="fas fa-star text-[10px]"></i>
                                                    <span>Yönetici</span>
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-gray-500 text-sm truncate">{{ $member['branch_name'] }}</p>
                                    </div>

                                    {{-- Status Badge --}}
                                    @if($member['subscription']['is_active'])
                                        @if(($member['subscription']['days_left'] ?? 0) > 30)
                                            {{-- 30+ gün - Kilitli --}}
                                            <span class="px-3 py-1.5 bg-blue-500/20 text-blue-400 rounded-lg text-xs font-medium whitespace-nowrap border border-blue-500/30 flex items-center gap-1.5">
                                                <i class="fas fa-lock"></i>
                                                <span>{{ $member['subscription']['days_left'] }} gün</span>
                                            </span>
                                        @elseif($member['subscription']['status'] === 'expiring')
                                            <span class="px-3 py-1.5 bg-amber-500/20 text-amber-400 rounded-lg text-xs font-medium whitespace-nowrap border border-amber-500/30 flex items-center gap-1.5">
                                                <i class="fas fa-exclamation-triangle"></i>
                                                <span>{{ $member['subscription']['days_left'] }} gün</span>
                                            </span>
                                        @else
                                            <span class="px-3 py-1.5 bg-green-500/20 text-green-400 rounded-lg text-xs font-medium whitespace-nowrap border border-green-500/30 flex items-center gap-1.5">
                                                <i class="fas fa-check-circle"></i>
                                                <span>{{ $member['subscription']['ends_at'] }}</span>
                                            </span>
                                        @endif
                                    @else
                                        <span class="px-3 py-1.5 bg-red-500/20 text-red-400 rounded-lg text-xs font-medium whitespace-nowrap border border-red-500/30 flex items-center gap-1.5">
                                            <i class="fas fa-times-circle"></i>
                                            <span>Üyelik Yok</span>
                                        </span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===================== SAG KOLON - OZET (Checkout-style) ===================== --}}
            <div class="lg:w-96">
                <div class="bg-slate-800/50 rounded-2xl sticky top-6 overflow-hidden border border-white/10">

                    {{-- Özet Header --}}
                    <div class="px-5 py-4 border-b border-white/10 bg-white/5">
                        <h2 class="text-lg font-semibold text-white flex items-center">
                            <div class="w-8 h-8 bg-gradient-to-br from-green-500 to-emerald-500 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-receipt text-white text-sm"></i>
                            </div>
                            Sipariş Özeti
                        </h2>
                    </div>

                    {{-- Seçim Bilgisi --}}
                    <div class="p-5 border-b border-white/10">
                        <div class="space-y-4">
                            {{-- Plan --}}
                            <div class="flex justify-between items-center">
                                <span class="text-gray-400 text-sm">Plan</span>
                                <span class="text-white font-medium" x-text="selectedPlan?.title?.tr || '-'"></span>
                            </div>
                            {{-- Dönem --}}
                            <div class="flex justify-between items-center">
                                <span class="text-gray-400 text-sm">Ödeme Dönemi</span>
                                <span class="text-white font-medium" x-text="selectedCycle?.label?.tr || '-'"></span>
                            </div>
                            {{-- Üye Başına --}}
                            <div class="flex justify-between items-center">
                                <span class="text-gray-400 text-sm">Üye Başına</span>
                                <span class="text-white font-medium" x-text="formatPrice(pricePerUser) + ' TL'"></span>
                            </div>
                        </div>
                    </div>

                    {{-- Seçilen Üyeler --}}
                    <div class="p-5 border-b border-white/10">
                        <div class="flex justify-between items-center mb-3">
                            <span class="text-gray-400 text-sm">Seçilen Üyeler</span>
                            <span class="text-2xl font-bold text-white" x-text="selectedCount"></span>
                        </div>

                        {{-- Seçilen Üye Listesi (max 5) --}}
                        <div x-show="selectedCount > 0" class="space-y-2">
                            <template x-for="(userId, idx) in selectedUserIds.slice(0, 5)" :key="userId">
                                <div class="flex items-center gap-2 text-sm">
                                    <i class="fas fa-user text-purple-400 text-xs"></i>
                                    <span class="text-gray-300 truncate" x-text="members.find(m => m.user_id === userId)?.name || 'Üye'"></span>
                                </div>
                            </template>
                            <p x-show="selectedCount > 5" class="text-gray-500 text-xs">
                                +<span x-text="selectedCount - 5"></span> daha...
                            </p>
                        </div>
                        <p x-show="selectedCount === 0" class="text-gray-500 text-sm">
                            Henüz üye seçilmedi
                        </p>
                    </div>

                    {{-- Toplam --}}
                    <div class="p-5 bg-white/5">
                        <div class="flex justify-between items-center mb-5">
                            <span class="text-lg font-semibold text-white">Toplam</span>
                            <div class="text-right">
                                <span class="text-3xl font-black text-white" x-text="formatPrice(totalPrice)"></span>
                                <span class="text-lg text-gray-400 ml-1">TL</span>
                            </div>
                        </div>

                        {{-- Checkout Butonu --}}
                        <button @click="purchase()"
                                :disabled="!canPurchase || purchasing"
                                class="block w-full text-white font-bold py-4 rounded-xl transition-all text-center text-lg"
                                :class="canPurchase && !purchasing
                                    ? 'bg-gradient-to-r from-purple-500 to-pink-500 hover:opacity-90 shadow-lg shadow-purple-500/25'
                                    : 'bg-gray-700 cursor-not-allowed opacity-50'">
                            <span x-show="!purchasing"><i class="fas fa-lock mr-2"></i>Ödemeye Geç</span>
                            <span x-show="purchasing"><i class="fas fa-spinner fa-spin mr-2"></i>Yükleniyor...</span>
                        </button>

                        {{-- Güvenlik Notu --}}
                        <div class="mt-4 flex items-center justify-center gap-2 text-gray-500 text-xs">
                            <i class="fas fa-shield-halved text-green-500"></i>
                            <span>256-bit SSL ile güvenli ödeme</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        @endif

    </div>
</div>

@endsection

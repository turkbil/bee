<div>
<style>
@keyframes gradient-shift {
    0% {
        background-position: 0% 50%;
    }
    50% {
        background-position: 100% 50%;
    }
    100% {
        background-position: 0% 50%;
    }
}

.animate-gradient {
    animation: gradient-shift 3s ease infinite;
    background-size: 200% 200%;
}

.gradient-text {
    background-size: 200% 200%;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
</style>

<div class="min-h-screen bg-gradient-to-b from-spotify-black via-[#0a0a0a] to-spotify-black py-4 px-4">
    <div class="max-w-7xl mx-auto">

        {{-- Header --}}
        <div class="text-center mb-12">
            <h1 class="text-5xl md:text-6xl font-black text-white mb-4 tracking-tight">
                <span class="bg-gradient-to-r from-muzibu-coral via-pink-500 to-purple-500 gradient-text">Sınırsız</span> Müzik Keyfi
            </h1>
            <p class="text-xl text-gray-400 max-w-2xl mx-auto">
                Size en uygun planı seçin, sınırsız müzik dinlemenin keyfini çıkarın
            </p>
        </div>

        {{-- Plans Grid - YAN YANA --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
            @foreach($plans as $planIndex => $plan)
                @if($plan->is_trial && $userHasUsedTrial)
                    @continue
                @endif

                @php
                    $cycles = $plan->billing_cycles ?? [];
                    $firstCycleKey = array_key_first($cycles);
                    if (!$firstCycleKey) continue;

                    $cycle = $cycles[$firstCycleKey];
                    $price = $cycle['price'] ?? 0;
                    $durationDays = $cycle['duration_days'] ?? 0;
                    $monthlyPrice = $durationDays > 0 ? round(($price / $durationDays) * 30) : $price;

                    $gradients = [
                        'from-emerald-500 to-teal-600',
                        'from-blue-500 to-indigo-600',
                        'from-purple-500 to-pink-600',
                        'from-orange-500 to-red-600',
                        'from-yellow-500 to-amber-600',
                    ];
                    $gradient = $gradients[$planIndex % count($gradients)];
                @endphp

                <div class="relative group transition-all duration-300 hover:scale-105">
                    @if($plan->is_featured)
                    <div class="absolute -top-4 left-1/2 -translate-x-1/2 z-20">
                        <div class="px-4 py-1.5 bg-gradient-to-r {{ $gradient }} text-white text-xs font-bold rounded-full shadow-lg flex items-center gap-1 animate-gradient">
                            <i class="fas fa-star"></i>
                            <span>POPÜLER</span>
                        </div>
                    </div>
                    @endif

                    <div class="relative bg-gradient-to-br from-spotify-gray to-[#0a0a0a] rounded-3xl overflow-hidden border-2 {{ $plan->is_featured ? 'border-muzibu-coral shadow-2xl shadow-muzibu-coral/30 ring-2 ring-muzibu-coral/20' : 'border-white/10' }} transition-all duration-300 hover:border-muzibu-coral/50 flex flex-col h-full min-h-[550px]">

                        <div class="h-1.5 bg-gradient-to-r {{ $gradient }} @if($plan->is_featured) animate-gradient @endif"></div>

                        <div class="p-6 flex-1 flex flex-col">

                            <div class="mb-6">
                                <h3 class="text-2xl font-bold text-white mb-2">
                                    {{ $plan->getTranslated('title') }}
                                </h3>
                                @if($plan->is_trial)
                                    <p class="text-sm text-emerald-400 font-semibold flex items-center gap-2">
                                        <i class="fas fa-gift"></i>
                                        {{ $durationDays }} Gün Ücretsiz Deneme
                                    </p>
                                @else
                                    <p class="text-sm text-gray-400">
                                        {{ $durationDays }} Gün Premium Erişim
                                    </p>
                                @endif
                            </div>

                            <div class="mb-8">
                                @if($plan->is_trial)
                                    <div class="text-5xl font-black text-emerald-400">
                                        ÜCRETSİZ
                                    </div>
                                    <p class="text-sm text-gray-500 mt-2">{{ $durationDays }} gün boyunca</p>
                                @else
                                    @php
                                        $comparePrice = $cycle['compare_price'] ?? null;
                                    @endphp

                                    @if($comparePrice)
                                        <div class="relative inline-block mb-2">
                                            <span class="text-xl font-semibold text-gray-500/70">
                                                {{ number_format($comparePrice, 0, ',', '.') }}₺
                                            </span>
                                            <div class="absolute top-1/2 left-0 w-full h-0.5 bg-gradient-to-r from-red-500 to-red-600 transform -translate-y-1/2 rotate-[-5deg]"></div>
                                        </div>
                                    @endif

                                    <div class="flex items-baseline gap-2">
                                        <span class="text-5xl font-black bg-gradient-to-r {{ $gradient }} gradient-text @if($plan->is_featured) animate-gradient @endif">
                                            {{ number_format($price, 0, ',', '.') }}
                                        </span>
                                        <span class="text-2xl @if($plan->is_featured) bg-gradient-to-r {{ $gradient }} gradient-text @else text-gray-400 @endif">₺</span>
                                    </div>
                                    <p class="text-sm text-gray-400 mt-2">
                                        <span class="text-white font-semibold">{{ number_format($monthlyPrice, 0, ',', '.') }}₺</span>/ay
                                    </p>

                                    @if($comparePrice)
                                        @php
                                            $discount = round((($comparePrice - $price) / $comparePrice) * 100);
                                        @endphp
                                        <div class="inline-flex items-center gap-1.5 mt-3 px-3 py-1.5 bg-gradient-to-r from-emerald-500/20 to-green-500/20 text-emerald-400 text-xs font-bold rounded-full border border-emerald-500/40 shadow-lg shadow-emerald-500/10">
                                            <i class="fas fa-tag"></i>
                                            <span>%{{ $discount }} Tasarruf</span>
                                        </div>
                                    @endif
                                @endif
                            </div>

                            <div class="flex-1 space-y-3 mb-6">
                                @php
                                    $features = $plan->features ?? [];
                                @endphp
                                @foreach($features as $feature)
                                    <div class="flex items-center gap-3 text-sm text-gray-300">
                                        <i class="fas fa-check-circle text-emerald-400"></i>
                                        <span>{{ $feature }}</span>
                                    </div>
                                @endforeach
                            </div>

                            <button
                                @if($plan->is_trial)
                                    wire:click="startTrial({{ $plan->subscription_plan_id }}, '{{ $firstCycleKey }}')"
                                @else
                                    wire:click="addToCart({{ $plan->subscription_plan_id }}, '{{ $firstCycleKey }}', true)"
                                @endif
                                wire:loading.attr="disabled"
                                wire:loading.class="opacity-50 cursor-not-allowed"
                                class="w-full py-4 px-6 rounded-xl font-bold text-lg transition-all duration-300 transform hover:scale-105 active:scale-95 shadow-lg {{ $plan->is_featured ? 'bg-gradient-to-r from-muzibu-coral via-pink-500 to-purple-500 gradient-text text-white hover:shadow-muzibu-coral/50 animate-gradient' : 'bg-white text-spotify-black hover:bg-gray-100' }}">
                                <span wire:loading.remove>
                                    @if($plan->is_trial)
                                        <i class="fas fa-gift mr-2"></i>Ücretsiz Başla
                                    @else
                                        <i class="fas fa-crown mr-2"></i>Premium'a Geç
                                    @endif
                                </span>
                                <span wire:loading>
                                    <i class="fas fa-spinner fa-spin mr-2"></i>Yükleniyor...
                                </span>
                            </button>

                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if($plans->count() === 0)
        <div class="text-center py-20">
            <div class="text-gray-700 text-7xl mb-6">
                <i class="fas fa-crown"></i>
            </div>
            <h3 class="text-2xl font-bold text-white mb-3">
                Henüz Aktif Plan Yok
            </h3>
            <p class="text-gray-500 text-lg">
                Yakında yeni planlar eklenecek
            </p>
        </div>
        @endif
    </div>
</div>
</div>

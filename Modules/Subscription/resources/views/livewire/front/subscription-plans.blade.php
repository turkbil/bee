<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">
                {{ __('Üyelik Planları') }}
            </h1>
            <p class="text-lg text-gray-600 dark:text-gray-400">
                {{ __('Sizin için en uygun planı seçin') }}
            </p>
        </div>

        {{-- Plans Loop --}}
        @foreach($plans as $plan)
            @php
                $cycles = $plan->getSortedCycles();
                $cycleCount = count($cycles);

                // Grid sütun sayısı - max 4 sütun
                $gridCols = match($cycleCount) {
                    1 => 'grid-cols-1',
                    2 => 'md:grid-cols-2',
                    3 => 'md:grid-cols-3',
                    default => 'md:grid-cols-2 lg:grid-cols-4'
                };
            @endphp

            {{-- Plan Title (Eğer birden fazla plan varsa göster) --}}
            @if($plans->count() > 1)
            <div class="text-center mb-6">
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                    {{ $plan->getTranslated('title') }}
                </h2>
                @if($plan->getTranslated('description'))
                <p class="text-gray-600 dark:text-gray-400">
                    {{ $plan->getTranslated('description') }}
                </p>
                @endif
            </div>
            @endif

            {{-- Billing Cycles Grid --}}
            <div class="grid grid-cols-1 {{ $gridCols }} gap-8 max-w-6xl mx-auto mb-16">
                @foreach($cycles as $cycleKey => $cycle)
                    @php
                        $cycleLabel = $cycle['label']['tr'] ?? $cycle['label']['en'] ?? $cycleKey;
                        $price = $cycle['price'];
                        $comparePrice = $cycle['compare_price'] ?? null;
                        $durationDays = $cycle['duration_days'];
                        $trialDays = $cycle['trial_days'] ?? null;
                        $badge = $cycle['badge'] ?? null;
                        $promoText = $cycle['promo_text']['tr'] ?? $cycle['promo_text']['en'] ?? null;

                        // Öne çıkan plan için border rengi
                        $borderColor = $plan->is_featured ? 'border-blue-500' : 'border-gray-200 dark:border-gray-700';

                        // Badge rengi
                        $badgeColor = match($badge['color'] ?? null) {
                            'success' => 'bg-green-500',
                            'warning' => 'bg-yellow-500',
                            'danger' => 'bg-red-500',
                            'info' => 'bg-cyan-500',
                            'primary' => 'bg-blue-500',
                            default => 'bg-blue-500'
                        };
                    @endphp

                    <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-xl border-2 {{ $borderColor }} overflow-hidden transition-all hover:scale-105 hover:shadow-2xl">

                        {{-- Badge (Üstte) --}}
                        @if($badge && !empty($badge['text']))
                        <div class="absolute top-0 right-0 {{ $badgeColor }} text-white text-xs font-bold px-4 py-2 rounded-bl-xl">
                            {{ $badge['text'] }}
                        </div>
                        @endif

                        <div class="p-8">
                            {{-- Plan Title (Tek plan varsa göster) --}}
                            @if($plans->count() === 1)
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                                {{ $plan->getTranslated('title') }}
                            </h3>
                            @endif

                            {{-- Cycle Label --}}
                            <p class="text-gray-600 dark:text-gray-400 text-sm mb-6 font-semibold">
                                {{ $cycleLabel }}
                                @if($durationDays)
                                <span class="text-xs text-gray-500">
                                    ({{ $durationDays }} {{ __('gün') }})
                                </span>
                                @endif
                            </p>

                            {{-- Price --}}
                            <div class="mb-8">
                                <span class="text-5xl font-extrabold text-gray-900 dark:text-white">
                                    ₺{{ number_format($price, 0, ',', '.') }}
                                </span>

                                {{-- Compare Price (Üstü Çizili) --}}
                                @if($comparePrice)
                                <div class="mt-2 text-sm">
                                    <span class="line-through text-gray-500 dark:text-gray-400">
                                        ₺{{ number_format($comparePrice, 0, ',', '.') }}
                                    </span>
                                    <span class="ml-2 text-green-600 dark:text-green-400 font-semibold">
                                        {{ __('Tasarruf: ₺') }}{{ number_format($comparePrice - $price, 0, ',', '.') }}
                                    </span>
                                </div>
                                @endif

                                {{-- Promo Text --}}
                                @if($promoText)
                                <div class="mt-3 inline-block bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 text-xs font-semibold px-3 py-1 rounded-full">
                                    {{ $promoText }}
                                </div>
                                @endif

                                {{-- Trial Period (Kullanıcı daha önce kullanmadıysa göster) --}}
                                @if($trialDays && !$userHasUsedTrial)
                                <div class="mt-3 text-sm text-blue-600 dark:text-blue-400 font-medium">
                                    <i class="fas fa-gift mr-1"></i>
                                    {{ $trialDays }} {{ __('gün ücretsiz deneme') }}
                                </div>
                                @endif
                            </div>

                            {{-- Features --}}
                            @php
                                $features = $plan->features ?? [];
                            @endphp
                            @if($features && is_array($features) && count($features) > 0)
                            <ul class="space-y-4 mb-8">
                                @foreach($features as $feature)
                                @php
                                    // Format: "icon|text" veya sadece "text"
                                    if (str_contains($feature, '|')) {
                                        [$icon, $text] = explode('|', $feature, 2);
                                    } else {
                                        $icon = 'fas fa-check';
                                        $text = $feature;
                                    }
                                @endphp
                                <li class="flex items-start">
                                    <i class="{{ $icon }} text-green-500 mr-3 flex-shrink-0" style="font-size: 1.25rem; margin-top: 2px;"></i>
                                    <span class="text-gray-700 dark:text-gray-300">{{ $text }}</span>
                                </li>
                                @endforeach
                            </ul>
                            @endif

                            {{-- CTA Button --}}
                            <button wire:click="addToCart({{ $plan->subscription_plan_id }}, '{{ $cycleKey }}')"
                                    wire:loading.attr="disabled"
                                    class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold py-4 px-6 rounded-xl transition-all transform hover:scale-102 shadow-lg disabled:opacity-50 disabled:cursor-wait">
                                <span wire:loading.remove wire:target="addToCart({{ $plan->subscription_plan_id }}, '{{ $cycleKey }}')">
                                    {{ __('Satın Al') }}
                                </span>
                                <span wire:loading wire:target="addToCart({{ $plan->subscription_plan_id }}, '{{ $cycleKey }}')">
                                    <i class="fas fa-spinner fa-spin mr-2"></i>{{ __('Ekleniyor...') }}
                                </span>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach

        {{-- No Plans Message --}}
        @if($plans->count() === 0)
        <div class="text-center py-12">
            <div class="text-gray-400 dark:text-gray-600 text-6xl mb-4">
                <i class="fas fa-crown"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-700 dark:text-gray-300 mb-2">
                {{ __('Henüz Aktif Plan Yok') }}
            </h3>
            <p class="text-gray-500 dark:text-gray-400">
                {{ __('Yakında yeni planlar eklenecek') }}
            </p>
        </div>
        @endif
    </div>
</div>

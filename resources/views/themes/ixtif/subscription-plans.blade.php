<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-12 px-4 transition-colors duration-300">
    <div class="max-w-7xl mx-auto">

        {{-- Header --}}
        <div class="text-center mb-12">
            <h1 class="text-4xl font-extrabold text-gray-900 dark:text-white mb-3">
                {{ __("Üyelik Planları") }}
            </h1>
            <p class="text-lg text-gray-600 dark:text-gray-400">
                {{ __("Size en uygun planı seçin") }}
            </p>
        </div>

        {{-- Plans Loop --}}
        @foreach($plans as $planIndex => $plan)
            @php
                $cycles = $plan->getSortedCycles();
                $cycleCount = count($cycles);

                // Grid sütun sayısı
                $gridCols = match($cycleCount) {
                    1 => "grid-cols-1",
                    2 => "md:grid-cols-2",
                    3 => "md:grid-cols-3",
                    default => "md:grid-cols-2 lg:grid-cols-4"
                };
            @endphp

            {{-- Billing Cycles Grid --}}
            <div class="grid grid-cols-1 {{ $gridCols }} gap-6 mb-12">
                @foreach($cycles as $cycleKey => $cycle)
                    @php
                        $cycleLabel = $cycle["label"]["tr"] ?? $cycle["label"]["en"] ?? $cycleKey;
                        $price = $cycle["price"];
                        $comparePrice = $cycle["compare_price"] ?? null;
                        $durationDays = $cycle["duration_days"];
                        $trialDays = $cycle["trial_days"] ?? null;
                        $badge = $cycle["badge"] ?? null;
                        $promoText = $cycle["promo_text"]["tr"] ?? $cycle["promo_text"]["en"] ?? null;

                        // Badge renk
                        $badgeColorClass = match($badge["color"] ?? null) {
                            "success" => "bg-green-500",
                            "warning" => "bg-yellow-500",
                            "danger" => "bg-red-500",
                            "info" => "bg-cyan-500",
                            default => "bg-blue-500"
                        };

                        // Featured plan
                        $featuredClass = $plan->is_featured ? "border-blue-500 scale-105" : "border-transparent";
                    @endphp

                    <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden transition-all duration-300 hover:-translate-y-2 hover:shadow-xl flex flex-col border-2 {{ $featuredClass }}">

                        {{-- Badges --}}
                        @if($badge && !empty($badge["text"]))
                        <div class="absolute top-3 right-3 z-10">
                            <div class="{{ $badgeColorClass }} text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-lg uppercase">
                                {{ $badge["text"] }}
                            </div>
                        </div>
                        @endif

                        {{-- Header --}}
                        <div class="p-6 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-800 {{ $plan->is_featured ? "!from-blue-600 !to-blue-700" : "" }}">
                            <h2 class="text-2xl font-bold {{ $plan->is_featured ? "text-white" : "text-gray-900 dark:text-white" }} mb-2">
                                {{ $plan->getTranslated("title") }}
                            </h2>
                            <p class="text-sm {{ $plan->is_featured ? "text-blue-100" : "text-gray-600 dark:text-gray-400" }} mb-3">
                                {{ $plan->getTranslated("description") }}
                            </p>
                            <span class="inline-block px-3 py-1 bg-blue-500 {{ $plan->is_featured ? "bg-white/20" : "" }} text-white text-xs font-semibold rounded-full">
                                {{ $cycleLabel }} · {{ $durationDays }} gün
                            </span>
                        </div>

                        {{-- Price --}}
                        <div class="p-6 bg-white dark:bg-gray-800 text-center">
                            <div class="text-4xl font-black text-gray-900 dark:text-white mb-2">
                                ₺{{ number_format($price, 0, ",", ".") }}
                            </div>

                            @if($comparePrice)
                            <div class="text-sm line-through text-gray-500 dark:text-gray-400 mb-1">
                                ₺{{ number_format($comparePrice, 0, ",", ".") }}
                            </div>
                            <div class="text-sm font-semibold text-green-600 dark:text-green-400 mb-2">
                                ₺{{ number_format($comparePrice - $price, 0, ",", ".") }} tasarruf
                            </div>
                            @endif

                            @if($trialDays && !$userHasUsedTrial)
                            <div class="inline-block mt-2 px-4 py-2 bg-gradient-to-r from-green-500 to-emerald-600 text-white text-sm font-bold rounded-full">
                                🎁 {{ $trialDays }} gün deneme
                            </div>
                            @endif

                            @if($promoText)
                            <div class="mt-2 px-4 py-2 bg-yellow-100 dark:bg-yellow-900/20 text-yellow-700 dark:text-yellow-400 text-xs font-semibold rounded-lg">
                                🎉 {{ $promoText }}
                            </div>
                            @endif
                        </div>

                        {{-- Features --}}
                        @php
                            $features = $plan->features ?? [];
                        @endphp
                        @if($features && is_array($features) && count($features) > 0)
                        <div class="p-6 flex-1 bg-white dark:bg-gray-800">
                            <ul class="space-y-3">
                                @foreach($features as $feature)
                                @php
                                    if (str_contains($feature, "|")) {
                                        [$icon, $text] = explode("|", $feature, 2);
                                    } else {
                                        $icon = "fas fa-check-circle";
                                        $text = $feature;
                                    }
                                @endphp
                                <li class="flex items-start gap-3 text-sm">
                                    <i class="{{ $icon }} text-green-500 flex-shrink-0 mt-0.5"></i>
                                    <span class="text-gray-700 dark:text-gray-300">{{ $text }}</span>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        {{-- CTA --}}
                        <div class="p-6 bg-gray-50 dark:bg-gray-900">
                            <button wire:click="addToCart({{ $plan->subscription_plan_id }}, '{{ $cycleKey }}', true)"
                                    wire:loading.attr="disabled"
                                    wire:loading.class="opacity-50 cursor-not-allowed"
                                    class="block w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-bold py-3.5 px-6 rounded-xl transition-all transform hover:scale-105 active:scale-95 disabled:scale-100">
                                <span wire:loading.remove>
                                    <i class="fas fa-shopping-cart mr-2"></i>{{ __("Satın Al") }}
                                </span>
                                <span wire:loading>
                                    <i class="fas fa-spinner fa-spin mr-2"></i>{{ __("Ekleniyor...") }}
                                </span>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach

        {{-- No Plans --}}
        @if($plans->count() === 0)
        <div class="text-center py-16">
            <div class="text-gray-300 dark:text-gray-700 text-6xl mb-4">
                <i class="fas fa-crown"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-700 dark:text-gray-300 mb-2">
                {{ __("Henüz Aktif Plan Yok") }}
            </h3>
            <p class="text-gray-500 dark:text-gray-400">
                {{ __("Yakında yeni planlar eklenecek") }}
            </p>
        </div>
        @endif

        {{-- Footer --}}
        <div class="text-center mt-10">
            <p class="text-gray-600 dark:text-gray-400 text-sm">
                💡 {{ __("30 gün para iade garantisi") }}
            </p>
        </div>
    </div>
</div>

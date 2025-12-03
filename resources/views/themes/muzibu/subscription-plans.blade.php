<div class="min-h-screen bg-spotify-black py-12 px-4">
    <div class="max-w-7xl mx-auto">

        {{-- Header --}}
        <div class="text-center mb-12">
            <h1 class="text-4xl font-extrabold text-white mb-3">
                {{ __("Üyelik Planları") }}
            </h1>
            <p class="text-lg text-gray-400">
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

                        // Badge renk (Spotify style)
                        $badgeColorClass = match($badge["color"] ?? null) {
                            "success" => "bg-spotify-green",
                            "warning" => "bg-yellow-500",
                            "danger" => "bg-red-500",
                            "info" => "bg-cyan-500",
                            default => "bg-spotify-green"
                        };

                        // Featured plan
                        $featuredClass = $plan->is_featured ? "border-spotify-green scale-105" : "border-transparent";
                    @endphp

                    <div class="relative bg-spotify-dark rounded-2xl overflow-hidden transition-all duration-300 hover:-translate-y-2 hover:shadow-xl hover:shadow-spotify-green/20 flex flex-col border-2 {{ $featuredClass }}">

                        {{-- Badges --}}
                        @if($badge && !empty($badge["text"]))
                        <div class="absolute top-3 right-3 z-10">
                            <div class="{{ $badgeColorClass }} text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-lg uppercase">
                                {{ $badge["text"] }}
                            </div>
                        </div>
                        @endif

                        {{-- Header --}}
                        <div class="p-6 bg-gradient-to-br from-spotify-gray to-spotify-dark">
                            <h2 class="text-2xl font-bold text-spotify-green mb-2">
                                {{ $plan->getTranslated("title") }}
                            </h2>
                            <p class="text-sm text-gray-400 mb-3">
                                {{ $plan->getTranslated("description") }}
                            </p>
                            <span class="inline-block px-3 py-1 bg-spotify-gray text-white text-xs font-semibold rounded-full">
                                {{ $cycleLabel }} · {{ $durationDays }} gün
                            </span>
                        </div>

                        {{-- Price --}}
                        <div class="p-6 bg-spotify-dark text-center">
                            <div class="text-4xl font-black text-spotify-green mb-2">
                                ₺{{ number_format($price, 0, ",", ".") }}
                            </div>

                            @if($comparePrice)
                            <div class="text-sm line-through text-gray-500 mb-1">
                                ₺{{ number_format($comparePrice, 0, ",", ".") }}
                            </div>
                            <div class="text-sm font-semibold text-spotify-green-light mb-2">
                                ₺{{ number_format($comparePrice - $price, 0, ",", ".") }} tasarruf
                            </div>
                            @endif

                            @if($trialDays && !$userHasUsedTrial)
                            <div class="inline-block mt-2 px-4 py-2 bg-spotify-green text-black text-sm font-bold rounded-full shadow-lg shadow-spotify-green/50">
                                🎁 {{ $trialDays }} gün deneme
                            </div>
                            @endif

                            @if($promoText)
                            <div class="mt-2 px-4 py-2 bg-yellow-500/20 text-yellow-400 text-xs font-semibold rounded-lg border border-yellow-500/30">
                                🎉 {{ $promoText }}
                            </div>
                            @endif
                        </div>

                        {{-- Features --}}
                        @php
                            $features = $plan->features ?? [];
                        @endphp
                        @if($features && is_array($features) && count($features) > 0)
                        <div class="p-6 flex-1 bg-spotify-dark">
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
                                    <i class="{{ $icon }} text-spotify-green flex-shrink-0 mt-0.5"></i>
                                    <span class="text-gray-300">{{ $text }}</span>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        {{-- CTA --}}
                        <div class="p-6 bg-spotify-gray">
                            <button wire:click="addToCart({{ $plan->subscription_plan_id }}, '{{ $cycleKey }}', true)"
                                    wire:loading.attr="disabled"
                                    wire:loading.class="opacity-50 cursor-not-allowed"
                                    class="block w-full bg-spotify-green hover:bg-spotify-green-light text-black font-bold py-3.5 px-6 rounded-full transition-all transform hover:scale-105 active:scale-95 shadow-lg hover:shadow-spotify-green/50 disabled:scale-100">
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
            <div class="text-gray-700 text-6xl mb-4">
                <i class="fas fa-crown"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-300 mb-2">
                {{ __("Henüz Aktif Plan Yok") }}
            </h3>
            <p class="text-gray-500">
                {{ __("Yakında yeni planlar eklenecek") }}
            </p>
        </div>
        @endif

        {{-- Footer --}}
        <div class="text-center mt-10">
            <p class="text-gray-500 text-sm">
                💡 {{ __("30 gün para iade garantisi") }}
            </p>
        </div>
    </div>
</div>

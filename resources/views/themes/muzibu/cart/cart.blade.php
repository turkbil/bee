<div class="min-h-screen bg-gradient-to-b from-spotify-black via-[#0a0a0a] to-spotify-black py-4 px-4">
    <div class="max-w-7xl mx-auto">

        {{-- Header --}}
        <div class="text-center mb-8">
            <h1 class="text-3xl md:text-4xl font-black text-white mb-2 tracking-tight">
                <span class="bg-gradient-to-r from-muzibu-coral to-pink-500 bg-clip-text text-transparent">Sepetim</span>
            </h1>
            <p class="text-lg text-gray-400">
                Alışverişinizi tamamlamak için son adım
            </p>
        </div>

        {{-- Boş Sepet --}}
        @if(!$items || $items->count() === 0)
            <div class="max-w-md mx-auto text-center py-16">
                <div class="bg-spotify-gray rounded-2xl p-8 border border-white/10">
                    <i class="fas fa-shopping-cart text-6xl text-gray-600 mb-4"></i>
                    <h2 class="text-2xl font-bold text-white mb-2">Sepetiniz Boş</h2>
                    <p class="text-gray-400 mb-6">Henüz sepetinize ürün eklemediniz.</p>
                    <a href="/subscription-plans" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-muzibu-coral to-pink-500 hover:from-muzibu-coral-dark hover:to-pink-600 text-white font-medium rounded-lg transition-colors shadow-lg shadow-muzibu-coral/30">
                        <i class="fas fa-crown mr-2"></i>Premium Planlara Göz At
                    </a>
                </div>
            </div>
        @else

        {{-- MAIN LAYOUT: 2 COLUMN - RESPONSIVE --}}
        <div class="flex flex-col lg:flex-row gap-6">

            {{-- ===================== SOL KOLON - ÜRÜNLER ===================== --}}
            <div class="flex-1">
                <div class="bg-spotify-gray rounded-2xl p-5 border border-white/10">
                    <h2 class="text-lg font-semibold text-white mb-4 flex items-center">
                        <i class="fas fa-shopping-bag text-gray-500 mr-3"></i>
                        Sepetinizdeki Ürünler ({{ $items->count() }})
                    </h2>

                    <div class="space-y-4">
                        @foreach($items as $item)
                        <div class="bg-black rounded-xl p-4 border border-gray-700 hover:border-muzibu-coral/50 transition-all">
                            <div class="flex flex-col sm:flex-row gap-4">
                                {{-- Ürün Görseli --}}
                                @if($item->product && $item->product->thumbnail)
                                <div class="flex-shrink-0">
                                    <img src="{{ $item->product->thumbnail }}" alt="{{ $item->product->getTranslated('title') }}"
                                         class="w-20 h-20 rounded-lg object-cover border border-gray-700">
                                </div>
                                @endif

                                {{-- Ürün Bilgileri --}}
                                <div class="flex-1">
                                    <h3 class="text-white font-semibold text-base mb-1">
                                        {{ $item->product->getTranslated('title') ?? 'Ürün' }}
                                    </h3>
                                    @if($item->product->getTranslated('subtitle'))
                                    <p class="text-gray-400 text-sm mb-2">{{ $item->product->getTranslated('subtitle') }}</p>
                                    @endif

                                    {{-- Metadata (Cycle bilgisi subscription için) --}}
                                    @if($item->metadata && is_array($item->metadata) && isset($item->metadata['cycle_label']))
                                    <div class="flex items-center gap-2 mt-2">
                                        <span class="text-xs bg-muzibu-coral/20 text-muzibu-coral px-2 py-1 rounded-full border border-muzibu-coral/40">
                                            {{ $item->metadata['cycle_label'] }}
                                        </span>
                                        @if(isset($item->metadata['discount']) && $item->metadata['discount'] > 0)
                                        <span class="text-xs bg-emerald-500/20 text-emerald-400 px-2 py-1 rounded-full border border-emerald-500/40">
                                            %{{ $item->metadata['discount'] }} İndirim
                                        </span>
                                        @endif
                                    </div>
                                    @endif
                                </div>

                                {{-- Fiyat & Aksiyonlar --}}
                                <div class="flex flex-col items-end justify-between gap-3">
                                    <div class="text-right">
                                        @if($item->compare_price && $item->compare_price > $item->unit_price)
                                        <div class="relative inline-block mb-1">
                                            <span class="text-sm font-semibold text-gray-500/70">
                                                {{ number_format($item->compare_price, 0, ',', '.') }}₺
                                            </span>
                                            <div class="absolute top-1/2 left-0 w-full h-0.5 bg-gradient-to-r from-red-500 to-red-600 transform -translate-y-1/2 rotate-[-5deg]"></div>
                                        </div>
                                        @endif
                                        <div class="text-xl font-black bg-gradient-to-r from-muzibu-coral to-pink-500 bg-clip-text text-transparent">
                                            {{ number_format($item->unit_price, 0, ',', '.') }}₺
                                        </div>
                                        @if($item->quantity > 1)
                                        <p class="text-xs text-gray-500">{{ $item->quantity }} adet</p>
                                        @endif
                                    </div>

                                    <button wire:click="removeItem({{ $item->cart_item_id }})" 
                                            class="text-red-400 hover:text-red-300 text-sm flex items-center gap-1 transition-colors">
                                        <i class="fas fa-trash text-xs"></i>
                                        <span>Sil</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- ===================== SAĞ KOLON - ÖZET ===================== --}}
            <div class="lg:w-96">
                <div class="bg-spotify-gray rounded-2xl sticky top-6 overflow-hidden border border-white/10">

                    {{-- Özet Header --}}
                    <div class="p-5 border-b border-gray-700">
                        <h2 class="text-lg font-semibold text-white flex items-center">
                            <i class="fas fa-receipt text-gray-500 mr-3"></i>
                            Sipariş Özeti
                        </h2>
                    </div>

                    {{-- Fiyatlar --}}
                    <div class="p-5 space-y-3">
                        <div class="flex justify-between text-gray-400">
                            <span>Ara Toplam</span>
                            <span class="font-medium text-white">{{ number_format(round($subtotal), 0, ',', '.') }} ₺</span>
                        </div>
                        <div class="flex justify-between text-gray-400">
                            <span>KDV (%20)</span>
                            <span class="font-medium text-white">{{ number_format(round($taxAmount), 0, ',', '.') }} ₺</span>
                        </div>
                        <div class="flex justify-between pt-3 border-t border-gray-700">
                            <span class="text-lg font-bold text-white">Toplam</span>
                            <span class="text-xl font-bold bg-gradient-to-r from-muzibu-coral to-pink-500 bg-clip-text text-transparent">{{ number_format(round($total), 0, ',', '.') }} ₺</span>
                        </div>
                    </div>

                    {{-- Checkout Butonu --}}
                    <div class="p-5 border-t border-gray-700">
                        <a href="/cart/checkout" 
                           class="block w-full bg-gradient-to-r from-muzibu-coral to-pink-500 hover:from-muzibu-coral-dark hover:to-pink-600 text-white font-bold py-4 rounded-xl transition-all text-center text-lg shadow-lg shadow-muzibu-coral/30">
                            <i class="fas fa-lock mr-2"></i>Güvenli Ödemeye Geç
                        </a>
                        <p class="text-center text-xs text-gray-500 mt-4 flex items-center justify-center gap-1">
                            <i class="fas fa-shield-halved text-green-500"></i>
                            256-bit SSL ile güvenli ödeme
                        </p>
                    </div>

                </div>

                {{-- Devam Et Butonu --}}
                <div class="mt-6">
                    <a href="/subscription-plans" 
                       class="block w-full bg-spotify-gray hover:bg-gray-700 text-white font-medium py-3 rounded-xl transition-colors text-center border border-white/10">
                        <i class="fas fa-arrow-left mr-2"></i>Planlara Geri Dön
                    </a>
                </div>
            </div>

        </div>

        @endif
    </div>
</div>

@script
<script>
    try {
        const storedCartId = localStorage.getItem('cart_id');
        if (storedCartId) {
            $wire.loadCartById(parseInt(storedCartId)).then(() => {
                console.log('Cart loaded from localStorage');
            });
        }
    } catch (e) {
        // localStorage access denied (private mode, security settings)
        console.warn('Cart: localStorage access denied');
    }
</script>
@endscript

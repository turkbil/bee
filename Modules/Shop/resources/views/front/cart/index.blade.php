@extends('themes.ixtif.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 md:py-12">
    {{-- Success Message --}}
    @if(session('success'))
        <div class="mb-6 bg-green-100 dark:bg-green-900/30 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-300 px-4 py-3 rounded-lg flex items-center gap-3">
            <i class="fa-solid fa-circle-check text-xl"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- Page Header --}}
    <div class="mb-8">
        <h1 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-2">
            <i class="fa-solid fa-shopping-cart mr-3"></i>
            Alışveriş Sepeti
        </h1>
        <p class="text-gray-600 dark:text-gray-400">
            Sepetinizdeki {{ $itemCount }} ürün
        </p>
    </div>

    @if($items->count() > 0)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Cart Items Section --}}
            <div class="lg:col-span-2 space-y-4">
                @foreach($items as $item)
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 hover:shadow-xl transition-shadow">
                        <div class="flex flex-col md:flex-row gap-6">
                            {{-- Product Image --}}
                            <div class="flex-shrink-0">
                                @if($item->product->firstMedia())
                                    <img src="{{ thumb($item->product->firstMedia(), 120, 120, ['scale' => 1]) }}"
                                         alt="{{ $item->product->getTranslated('title', app()->getLocale()) }}"
                                         class="w-24 h-24 md:w-32 md:h-32 object-cover rounded-lg"
                                         loading="lazy">
                                @else
                                    <div class="w-24 h-24 md:w-32 md:h-32 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                                        <i class="fa-solid fa-image text-3xl text-gray-300 dark:text-gray-600"></i>
                                    </div>
                                @endif
                            </div>

                            {{-- Product Info --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-4 mb-3">
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1 truncate">
                                            <a href="{{ route('shop.show', $item->product->slug) }}"
                                               class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                                {{ $item->product->getTranslated('title', app()->getLocale()) }}
                                            </a>
                                        </h3>
                                        @if($item->product->sku)
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                SKU: {{ $item->product->sku }}
                                            </p>
                                        @endif
                                    </div>

                                    {{-- Remove Button --}}
                                    <form action="{{ route('shop.cart.remove', $item->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="flex-shrink-0 text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 transition-colors"
                                                title="Ürünü Kaldır"
                                                onclick="return confirm('Bu ürünü sepetten kaldırmak istediğinize emin misiniz?')">
                                            <i class="fa-solid fa-trash-can text-lg"></i>
                                        </button>
                                    </form>
                                </div>

                                {{-- Price & Quantity --}}
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                                    {{-- Quantity Controls --}}
                                    <div class="flex items-center gap-3">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">Adet:</span>
                                        <div class="flex items-center gap-2 bg-gray-100 dark:bg-gray-700 rounded-lg p-1">
                                            <form action="{{ route('shop.cart.update', $item->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="quantity" value="{{ max(1, $item->quantity - 1) }}">
                                                <button type="submit"
                                                        class="w-8 h-8 flex items-center justify-center text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 rounded transition-colors"
                                                        {{ $item->quantity <= 1 ? 'disabled' : '' }}>
                                                    <i class="fa-solid fa-minus"></i>
                                                </button>
                                            </form>
                                            <span class="w-12 text-center font-semibold text-gray-900 dark:text-white">
                                                {{ $item->quantity }}
                                            </span>
                                            <form action="{{ route('shop.cart.update', $item->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="quantity" value="{{ $item->quantity + 1 }}">
                                                <button type="submit"
                                                        class="w-8 h-8 flex items-center justify-center text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 rounded transition-colors">
                                                    <i class="fa-solid fa-plus"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>

                                    {{-- Price Info --}}
                                    <div class="text-right">
                                        <div class="text-sm text-gray-500 dark:text-gray-400 mb-1">
                                            Birim: {{ number_format($item->unit_price, 2, ',', '.') }} ₺
                                        </div>
                                        <div class="text-xl font-bold text-blue-600 dark:text-blue-400">
                                            {{ number_format($item->subtotal, 2, ',', '.') }} ₺
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Order Summary Sidebar --}}
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 sticky top-24">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">
                        Sipariş Özeti
                    </h2>

                    {{-- Summary Lines --}}
                    <div class="space-y-4 mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between text-gray-600 dark:text-gray-400">
                            <span>Ara Toplam:</span>
                            <span class="font-semibold">{{ number_format($subtotal, 2, ',', '.') }} ₺</span>
                        </div>
                        <div class="flex items-center justify-between text-gray-600 dark:text-gray-400">
                            <span>KDV (%{{ config('shop.tax_rate', 20) }}):</span>
                            <span class="font-semibold">{{ number_format($taxAmount, 2, ',', '.') }} ₺</span>
                        </div>
                    </div>

                    {{-- Total --}}
                    <div class="flex items-center justify-between text-2xl font-bold text-gray-900 dark:text-white mb-6">
                        <span>Toplam:</span>
                        <span class="text-blue-600 dark:text-blue-400">{{ number_format($total, 2, ',', '.') }} ₺</span>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="space-y-3">
                        <a href="{{ route('shop.checkout') }}"
                           class="block w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg text-center transition-colors">
                            <i class="fa-solid fa-credit-card mr-2"></i>
                            Sipariş Ver
                        </a>
                        <a href="{{ route('shop.index') }}"
                           class="block w-full bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-900 dark:text-white font-semibold py-3 px-6 rounded-lg text-center transition-colors">
                            <i class="fa-solid fa-shopping-bag mr-2"></i>
                            Alışverişe Devam
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @else
        {{-- Empty Cart State --}}
        <div class="text-center py-16">
            <div class="inline-flex items-center justify-center w-32 h-32 bg-gray-100 dark:bg-gray-800 rounded-full mb-6">
                <i class="fa-solid fa-shopping-cart text-6xl text-gray-300 dark:text-gray-600"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">
                Sepetiniz Boş
            </h2>
            <p class="text-gray-600 dark:text-gray-400 mb-8">
                Alışverişe başlamak için ürünlerimize göz atın
            </p>
            <a href="{{ route('shop.index') }}"
               class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-8 rounded-lg transition-colors">
                <i class="fa-solid fa-shopping-bag"></i>
                <span>Ürünleri İncele</span>
            </a>
        </div>
    @endif
</div>
@endsection

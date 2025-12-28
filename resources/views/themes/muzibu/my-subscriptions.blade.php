@extends('themes.muzibu.layouts.app')

@section('title', 'Aboneliklerim - Muzibu')

@section('content')
<div class="px-4 py-6 sm:px-6 sm:py-8">

    {{-- Header --}}
    <div class="mb-6 sm:mb-8 flex items-center justify-between">
        <div class="flex items-center gap-3 sm:gap-4">
            <a href="/dashboard" class="w-10 h-10 sm:w-12 sm:h-12 bg-white/10 hover:bg-white/20 rounded-lg sm:rounded-xl flex items-center justify-center transition" data-spa>
                <i class="fas fa-arrow-left text-white"></i>
            </a>
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-white mb-0.5">
                    Aboneliklerim
                </h1>
                <p class="text-gray-400 text-sm sm:text-base">Abonelik ve ödeme geçmişiniz</p>
            </div>
        </div>
        <div class="hidden sm:flex items-center gap-3">
            <a href="/subscription/plans" class="px-4 py-2 bg-gradient-to-r from-yellow-500 to-orange-500 hover:opacity-90 text-white font-medium rounded-lg transition" data-spa>
                <i class="fas fa-crown mr-2"></i>Yeni Paket Al
            </a>
        </div>
    </div>

    @include('themes.muzibu.partials.my-subscriptions-content')

</div>
@endsection

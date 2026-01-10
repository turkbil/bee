@extends('themes.muzibu.layouts.app')

@section('title', __('muzibu::front.certificate.title') . ' - Muzibu')

@section('content')
<div class="min-h-screen flex items-center justify-center px-4 py-12">
    <div class="max-w-md w-full text-center">
        {{-- Icon --}}
        <div class="w-20 h-20 bg-gradient-to-br from-amber-500/20 to-orange-500/20 rounded-2xl mx-auto mb-6 flex items-center justify-center ring-1 ring-amber-500/30">
            <i class="fas fa-lock text-4xl text-amber-400"></i>
        </div>

        @if($reason === 'no_active_subscription')
            {{-- Title --}}
            <h1 class="text-2xl sm:text-3xl font-extrabold text-white mb-3">
                {{ __('muzibu::front.certificate.not_eligible_title') }}
            </h1>

            {{-- Description --}}
            <p class="text-gray-400 mb-8 leading-relaxed">
                {{ __('muzibu::front.certificate.not_eligible_text') }}
            </p>

            {{-- CTA Button --}}
            <a href="{{ route('subscription.plans') }}"
                class="inline-flex items-center gap-2 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-black font-bold py-4 px-8 rounded-xl transition-all duration-300 shadow-lg shadow-amber-500/25 hover:shadow-amber-500/40">
                <i class="fas fa-crown"></i>
                {{ __('muzibu::front.certificate.upgrade_button') }}
            </a>
        @else
            {{-- Generic Error --}}
            <h1 class="text-2xl sm:text-3xl font-extrabold text-white mb-3">
                Belge Oluşturulamıyor
            </h1>
            <p class="text-gray-400 mb-8">
                Bir hata oluştu. Lütfen daha sonra tekrar deneyin.
            </p>
        @endif

        {{-- Back Link --}}
        <div class="mt-8">
            <a href="{{ route('home') }}" class="text-gray-400 hover:text-white text-sm transition inline-flex items-center gap-2" data-spa>
                <i class="fas fa-arrow-left"></i>
                {{ __('muzibu::front.back') }}
            </a>
        </div>
    </div>
</div>
@endsection

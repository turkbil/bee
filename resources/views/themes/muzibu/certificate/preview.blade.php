@extends('themes.muzibu.layouts.app')

@section('title', __('muzibu::front.certificate.preview_title') . ' - Muzibu')

@section('content')
<div class="min-h-screen">
    <div class="px-4 py-6 sm:px-6 sm:py-8 max-w-3xl mx-auto">

        {{-- Header --}}
        <div class="mb-6 sm:mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3 sm:gap-4">
                <div class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-amber-500/30 to-amber-600/20 rounded-xl flex items-center justify-center flex-shrink-0 ring-1 ring-amber-500/30">
                    <i class="fas fa-eye text-2xl sm:text-3xl text-amber-400"></i>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-white mb-0.5">
                        {{ __('muzibu::front.certificate.preview_title') }}
                    </h1>
                    <p class="text-gray-400 text-sm sm:text-base">{{ __('muzibu::front.certificate.preview_info') }}</p>
                </div>
            </div>
        </div>

        {{-- Warning --}}
        <div class="bg-red-500/10 border border-red-500/30 rounded-xl p-4 mb-6">
            <div class="flex gap-3">
                <div class="flex-shrink-0 w-8 h-8 bg-red-500/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-red-400"></i>
                </div>
                <div class="text-sm">
                    <p class="font-medium text-red-400 mb-1">Son Kontrol!</p>
                    <p class="text-gray-400">Onayladıktan sonra bu bilgiler <strong class="text-white">DEĞİŞTİRİLEMEZ!</strong></p>
                </div>
            </div>
        </div>

        {{-- Preview Card --}}
        <div class="bg-gradient-to-br from-slate-900 to-slate-800 border border-amber-500/30 rounded-2xl overflow-hidden shadow-2xl mb-6">
            {{-- Certificate Header --}}
            <div class="bg-gradient-to-r from-amber-500/10 to-orange-500/10 border-b border-amber-500/20 p-6 text-center">
                <p class="text-amber-400/60 text-sm uppercase tracking-widest mb-2">Premium Üyelik Belgesı</p>
                <h2 class="text-2xl sm:text-3xl font-bold text-white">{{ $previewData['member_name'] }}</h2>
            </div>

            {{-- Certificate Body --}}
            <div class="p-6 space-y-4">
                {{-- Member Since --}}
                <div class="flex items-center justify-between py-3 border-b border-white/10">
                    <span class="text-gray-400">{{ __('muzibu::front.certificate.member_since') }}</span>
                    <span class="text-white font-medium">{{ $previewData['membership_start']->format('d.m.Y') }}</span>
                </div>

                @if($previewData['tax_office'])
                <div class="flex items-center justify-between py-3 border-b border-white/10">
                    <span class="text-gray-400">{{ __('muzibu::front.certificate.tax_office') }}</span>
                    <span class="text-white">{{ $previewData['tax_office'] }}</span>
                </div>
                @endif

                @if($previewData['tax_number'])
                <div class="flex items-center justify-between py-3 border-b border-white/10">
                    <span class="text-gray-400">{{ __('muzibu::front.certificate.tax_number') }}</span>
                    <span class="text-white font-mono">{{ $previewData['tax_number'] }}</span>
                </div>
                @endif

                @if($previewData['address'])
                <div class="py-3 border-b border-white/10">
                    <span class="text-gray-400 block mb-2">{{ __('muzibu::front.certificate.address') }}</span>
                    <span class="text-white">{{ $previewData['address'] }}</span>
                </div>
                @endif
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex flex-col sm:flex-row gap-4">
            <a href="/my-certificate"
                class="flex-1 bg-white/10 hover:bg-white/20 text-white font-semibold py-4 rounded-xl text-center transition flex items-center justify-center gap-2">
                <i class="fas fa-arrow-left"></i>
                {{ __('muzibu::front.certificate.go_back') }}
            </a>

            <form action="/my-certificate" method="POST" class="flex-1"
                x-data
                @submit="$el.querySelector('input[name=_token]').value = document.querySelector('meta[name=csrf-token]')?.content || $el.querySelector('input[name=_token]').value">
                @csrf
                <input type="hidden" name="member_name" value="{{ $formData['member_name'] }}">
                <input type="hidden" name="tax_office" value="{{ $formData['tax_office'] ?? '' }}">
                <input type="hidden" name="tax_number" value="{{ $formData['tax_number'] ?? '' }}">
                <input type="hidden" name="address" value="{{ $formData['address'] ?? '' }}">
                <input type="hidden" name="skip_correction" value="{{ $formData['skip_correction'] ?? '' }}">

                <button type="submit"
                    class="w-full bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 text-white font-bold py-4 rounded-xl transition-all duration-300 shadow-lg shadow-green-500/25 hover:shadow-green-500/40 flex items-center justify-center gap-2">
                    <i class="fas fa-check"></i>
                    {{ __('muzibu::front.certificate.confirm_create') }}
                </button>
            </form>
        </div>

    </div>
</div>
@endsection

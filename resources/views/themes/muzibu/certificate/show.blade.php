@extends('themes.muzibu.layouts.app')

@section('title', __('muzibu::front.certificate.your_certificate') . ' - Muzibu')

@section('content')
<div class="min-h-screen">
    <div class="px-4 py-6 sm:px-6 sm:py-8 max-w-3xl mx-auto">

        {{-- Header --}}
        <div class="mb-6 sm:mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3 sm:gap-4">
                <div class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-green-500/30 to-emerald-600/20 rounded-xl flex items-center justify-center flex-shrink-0 ring-1 ring-green-500/30">
                    <i class="fas fa-certificate text-2xl sm:text-3xl text-green-400"></i>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-white mb-0.5">
                        {{ __('muzibu::front.certificate.your_certificate') }}
                    </h1>
                    <p class="text-gray-400 text-sm sm:text-base">{{ __('muzibu::front.certificate.already_have') }}</p>
                </div>
            </div>
            <a href="/dashboard" class="inline-flex items-center justify-center px-4 py-2 bg-white/10 hover:bg-white/20 text-white text-sm rounded-lg transition" data-spa>
                <i class="fas fa-arrow-left mr-2"></i>{{ __('muzibu::front.back') }}
            </a>
        </div>

        {{-- Success Alert --}}
        @if(session('success'))
        <div class="bg-green-500/20 border border-green-500/30 rounded-xl p-4 mb-6 flex items-center gap-3">
            <i class="fas fa-check-circle text-green-400 text-xl"></i>
            <span class="text-green-400">{{ session('success') }}</span>
        </div>
        @endif

        {{-- Certificate Card --}}
        <div class="bg-gradient-to-br from-slate-900 to-slate-800 border border-amber-500/30 rounded-2xl overflow-hidden shadow-2xl">
            {{-- Certificate Header --}}
            <div class="bg-gradient-to-r from-amber-500/10 to-orange-500/10 border-b border-amber-500/20 p-6 text-center">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-sm font-medium mb-4
                    {{ $certificate->is_valid ? 'bg-green-500/20 text-green-400 ring-1 ring-green-500/30' : 'bg-red-500/20 text-red-400 ring-1 ring-red-500/30' }}">
                    <i class="fas {{ $certificate->is_valid ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                    {{ $certificate->is_valid ? __('muzibu::front.certificate.valid') : __('muzibu::front.certificate.invalid') }}
                </div>
                <h2 class="text-2xl font-bold text-white">{{ $certificate->member_name }}</h2>
            </div>

            {{-- Certificate Body --}}
            <div class="p-6 space-y-4">
                {{-- Certificate Code --}}
                <div class="flex items-center justify-between py-3 border-b border-white/10">
                    <span class="text-gray-400">{{ __('muzibu::front.certificate.certificate_code') }}</span>
                    <span class="font-mono text-lg text-amber-400 font-semibold">{{ $certificate->certificate_code }}</span>
                </div>

                {{-- Member Since --}}
                <div class="flex items-center justify-between py-3 border-b border-white/10">
                    <span class="text-gray-400">{{ __('muzibu::front.certificate.member_since') }}</span>
                    <span class="text-white font-medium">{{ $certificate->membership_start?->format('d.m.Y') }}</span>
                </div>

                {{-- Issued At --}}
                <div class="flex items-center justify-between py-3 border-b border-white/10">
                    <span class="text-gray-400">{{ __('muzibu::front.certificate.issued_at') }}</span>
                    <span class="text-white font-medium">{{ $certificate->issued_at?->format('d.m.Y H:i') }}</span>
                </div>

                @if($certificate->tax_office)
                <div class="flex items-center justify-between py-3 border-b border-white/10">
                    <span class="text-gray-400">{{ __('muzibu::front.certificate.tax_office') }}</span>
                    <span class="text-white">{{ $certificate->tax_office }}</span>
                </div>
                @endif

                @if($certificate->tax_number)
                <div class="flex items-center justify-between py-3 border-b border-white/10">
                    <span class="text-gray-400">{{ __('muzibu::front.certificate.tax_number') }}</span>
                    <span class="text-white font-mono">{{ $certificate->tax_number }}</span>
                </div>
                @endif

                @if($certificate->address)
                <div class="py-3 border-b border-white/10">
                    <span class="text-gray-400 block mb-2">{{ __('muzibu::front.certificate.address') }}</span>
                    <span class="text-white">{{ $certificate->address }}</span>
                </div>
                @endif
            </div>

            {{-- QR Code Section --}}
            <div class="bg-white/5 border-t border-white/10 p-6">
                <div class="flex flex-col sm:flex-row items-center gap-6">
                    <div class="bg-white p-3 rounded-xl flex-shrink-0">
                        <img src="{{ qr($certificate->getVerificationUrl(), 120) }}" alt="QR" class="w-28 h-28">
                    </div>
                    <div class="text-center sm:text-left flex-1">
                        <p class="text-gray-400 text-sm mb-2">{{ __('muzibu::front.certificate.qr_verify') }}</p>
                        <code class="text-xs text-amber-400/70 break-all block">{{ $certificate->getVerificationUrl() }}</code>
                        <p class="text-gray-500 text-xs mt-2">{{ $certificate->view_count }} kez doğrulandı</p>
                    </div>
                </div>
            </div>

            {{-- Download Button --}}
            <div class="p-6 border-t border-white/10">
                <a href="{{ route('muzibu.certificate.download') }}"
                    class="w-full bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-black font-bold py-4 rounded-xl transition-all duration-300 shadow-lg shadow-amber-500/25 hover:shadow-amber-500/40 flex items-center justify-center gap-2 text-base">
                    <i class="fas fa-download"></i>
                    {{ __('muzibu::front.certificate.download_button') }}
                </a>
            </div>
        </div>

    </div>
</div>
@endsection

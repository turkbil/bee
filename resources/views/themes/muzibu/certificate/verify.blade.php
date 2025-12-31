@extends('themes.muzibu.layouts.app')

@section('title', 'Sertifika Doğrulama - Muzibu')

@section('content')
<div class="px-4 py-6 sm:px-6 sm:py-8">
    <div class="max-w-xl mx-auto">
        {{-- Status Badge --}}
        @if($isCurrentlyActive)
        <div class="bg-green-900/30 border-2 border-green-500 rounded-xl p-6 mb-6 text-center">
            <div class="w-16 h-16 bg-green-600 rounded-full mx-auto mb-4 flex items-center justify-center">
                <i class="fas fa-check text-white text-3xl"></i>
            </div>
            <h1 class="text-2xl font-bold text-green-400 mb-1">AKTİF ÜYELİK</h1>
            <p class="text-gray-400">Bu sertifika geçerlidir</p>
        </div>
        @else
        <div class="bg-red-900/30 border-2 border-red-500 rounded-xl p-6 mb-6 text-center">
            <div class="w-16 h-16 bg-red-600 rounded-full mx-auto mb-4 flex items-center justify-center">
                <i class="fas fa-times text-white text-3xl"></i>
            </div>
            <h1 class="text-2xl font-bold text-red-400 mb-1">ÜYELİK SONA ERMİŞ</h1>
            <p class="text-gray-400">Bu üyelik şu anda aktif değil</p>
        </div>
        @endif

        {{-- Certificate Info --}}
        <div class="bg-white/5 border border-white/10 rounded-xl p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <span class="text-gray-500 text-sm">Sertifika No</span>
                <span class="font-mono text-amber-400">{{ $certificate->certificate_code }}</span>
            </div>

            <div class="border-t border-white/10 pt-4">
                <h2 class="text-xl font-bold text-white mb-2">{{ $certificate->member_name }}</h2>
                @if($certificate->tax_office || $certificate->tax_number)
                <p class="text-gray-400 text-sm">
                    {{ $certificate->tax_office }}
                    @if($certificate->tax_office && $certificate->tax_number) - @endif
                    {{ $certificate->tax_number }}
                </p>
                @endif
            </div>
        </div>

        {{-- Subscription Periods --}}
        @if(count($subscriptionPeriods) > 0)
        <div class="bg-white/5 border border-white/10 rounded-xl p-6 mb-6">
            <h3 class="text-gray-400 text-sm uppercase mb-4">Üyelik Dönemleri</h3>
            <div class="space-y-3">
                @foreach($subscriptionPeriods as $period)
                <div class="flex items-center gap-3 {{ $period['is_active'] ? 'bg-green-900/30 rounded-lg px-3 py-2' : '' }}">
                    @if($period['is_active'])
                    <i class="fas fa-play-circle text-green-400"></i>
                    @else
                    <i class="fas fa-check-circle text-gray-500"></i>
                    @endif
                    <span class="{{ $period['is_active'] ? 'text-white' : 'text-gray-400' }}">
                        {{ $period['start']->format('d.m.Y') }} - {{ $period['end']->format('d.m.Y') }}
                    </span>
                    @if($period['is_active'])
                    <span class="text-green-400 text-xs ml-auto">Aktif</span>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- View Count --}}
        <div class="text-center text-gray-500 text-sm">
            <i class="fas fa-eye mr-1"></i>
            Bu sayfa {{ $certificate->view_count }} kez görüntülendi
        </div>
    </div>
</div>
@endsection

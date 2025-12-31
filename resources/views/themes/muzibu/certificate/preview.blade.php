@extends('themes.muzibu.layouts.app')

@section('title', 'Sertifika Ön İzleme - Muzibu')

@section('content')
<div class="px-4 py-6 sm:px-6 sm:py-8">
    {{-- Header --}}
    <div class="mb-6">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-amber-500/20 rounded-xl flex items-center justify-center">
                <i class="fas fa-eye text-2xl text-amber-400"></i>
            </div>
            <div>
                <h1 class="text-3xl font-extrabold text-white">Sertifika Ön İzleme</h1>
                <p class="text-gray-400">Bilgilerinizi kontrol edin ve onaylayın</p>
            </div>
        </div>
    </div>

    {{-- Warning --}}
    <div class="bg-red-900/30 border border-red-600/50 rounded-xl p-4 mb-6">
        <div class="flex items-start gap-3">
            <i class="fas fa-exclamation-triangle text-red-400 mt-1"></i>
            <div class="text-sm">
                <p class="font-medium text-red-400 mb-1">Son Kontrol!</p>
                <p class="text-gray-400">Onayladıktan sonra bu bilgiler <strong class="text-white">DEĞİŞTİRİLEMEZ!</strong></p>
            </div>
        </div>
    </div>

    {{-- Preview Card --}}
    <div class="bg-gradient-to-br from-slate-800 to-slate-900 border-2 border-amber-500/50 rounded-xl p-6 mb-6 max-w-2xl">
        <div class="text-center mb-6">
            <p class="text-amber-400/60 text-sm uppercase tracking-widest mb-1">Premium Üyelik</p>
            <h2 class="text-3xl font-bold text-white">SERTİFİKASI</h2>
        </div>

        <div class="text-center mb-6">
            <p class="text-gray-400 text-sm italic mb-2">Bu belge,</p>
            <p class="text-2xl font-bold text-white border-b-2 border-amber-400 pb-2 inline-block px-4">
                {{ $previewData['member_name'] }}
            </p>
            <p class="text-gray-400 text-sm mt-2">firmasının Muzibu Premium üyesi olduğunu tasdik eder.</p>
        </div>

        <div class="bg-slate-800/50 rounded-lg p-4 mb-4">
            <div class="grid grid-cols-2 gap-4 text-sm mb-3">
                <div>
                    <p class="text-gray-500 text-xs">Vergi Dairesi</p>
                    <p class="text-gray-300">{{ $previewData['tax_office'] ?: '-' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-xs">Vergi No</p>
                    <p class="text-gray-300">{{ $previewData['tax_number'] ?: '-' }}</p>
                </div>
            </div>
            @if($previewData['address'])
            <div>
                <p class="text-gray-500 text-xs">Adres</p>
                <p class="text-gray-400 text-sm">{{ $previewData['address'] }}</p>
            </div>
            @endif
        </div>

        <div class="text-center text-sm">
            <span class="text-gray-500">Üyelik Başlangıç:</span>
            <span class="text-gray-300 ml-1">{{ $previewData['membership_start']->format('d.m.Y') }}</span>
        </div>
    </div>

    {{-- Actions --}}
    <div class="flex gap-4 max-w-2xl">
        <a href="{{ route('muzibu.certificate.index') }}"
            class="flex-1 bg-white/10 hover:bg-white/20 text-white font-semibold py-3 rounded-lg text-center transition">
            <i class="fas fa-arrow-left mr-2"></i> Geri Dön
        </a>

        <form action="{{ route('muzibu.certificate.store') }}" method="POST" class="flex-1">
            @csrf
            <input type="hidden" name="member_name" value="{{ $formData['member_name'] }}">
            <input type="hidden" name="tax_office" value="{{ $formData['tax_office'] ?? '' }}">
            <input type="hidden" name="tax_number" value="{{ $formData['tax_number'] ?? '' }}">
            <input type="hidden" name="address" value="{{ $formData['address'] ?? '' }}">

            <button type="submit"
                class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 rounded-lg transition">
                <i class="fas fa-check mr-2"></i> Onayla ve Oluştur
            </button>
        </form>
    </div>
</div>
@endsection

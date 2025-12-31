@extends('themes.muzibu.layouts.app')

@section('title', 'Sertifikam - Muzibu')

@section('content')
<div class="px-4 py-6 sm:px-6 sm:py-8">
    {{-- Header --}}
    <div class="mb-6">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-green-500/20 rounded-xl flex items-center justify-center">
                <i class="fas fa-certificate text-2xl text-green-400"></i>
            </div>
            <div>
                <h1 class="text-3xl font-extrabold text-white">Premium Sertifikam</h1>
                <p class="text-gray-400">Sertifikanızı istediğiniz zaman indirebilirsiniz</p>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-900/30 border border-green-600/50 rounded-xl p-4 mb-6">
        <div class="flex items-center gap-3">
            <i class="fas fa-check-circle text-green-400"></i>
            <p class="text-green-400">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    {{-- Certificate Card --}}
    <div class="bg-gradient-to-br from-slate-800 to-slate-900 border-2 border-amber-500/50 rounded-xl p-6 mb-6 max-w-2xl">
        {{-- Certificate Code Badge --}}
        <div class="flex justify-between items-start mb-6">
            <div class="bg-amber-500/20 px-3 py-1 rounded-full">
                <span class="text-amber-400 text-sm font-mono">{{ $certificate->certificate_code }}</span>
            </div>
            <div class="text-right text-xs text-gray-500">
                Oluşturulma: {{ $certificate->issued_at->format('d.m.Y H:i') }}
            </div>
        </div>

        <div class="text-center mb-6">
            <p class="text-amber-400/60 text-sm uppercase tracking-widest mb-1">Premium Üyelik</p>
            <h2 class="text-3xl font-bold text-white">SERTİFİKASI</h2>
        </div>

        <div class="text-center mb-6">
            <p class="text-2xl font-bold text-white border-b-2 border-amber-400 pb-2 inline-block px-4">
                {{ $certificate->member_name }}
            </p>
        </div>

        <div class="bg-slate-800/50 rounded-lg p-4 mb-4">
            <div class="grid grid-cols-2 gap-4 text-sm mb-3">
                <div>
                    <p class="text-gray-500 text-xs">Vergi Dairesi</p>
                    <p class="text-gray-300">{{ $certificate->tax_office ?: '-' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-xs">Vergi No</p>
                    <p class="text-gray-300">{{ $certificate->tax_number ?: '-' }}</p>
                </div>
            </div>
            @if($certificate->address)
            <div>
                <p class="text-gray-500 text-xs">Adres</p>
                <p class="text-gray-400 text-sm">{{ $certificate->address }}</p>
            </div>
            @endif
        </div>

        <div class="flex justify-center gap-8 text-center text-sm mb-4">
            <div>
                <p class="text-gray-500 text-xs uppercase">Üyelik Başlangıç</p>
                <p class="text-gray-300">{{ $certificate->membership_start->format('d.m.Y') }}</p>
            </div>
            <div class="w-px bg-amber-500/30"></div>
            <div>
                <p class="text-gray-500 text-xs uppercase">Doğrulama</p>
                <p class="text-amber-400 text-xs">{{ $certificate->view_count }} kez görüntülendi</p>
            </div>
        </div>

        {{-- QR Code --}}
        <div class="text-center">
            <div class="inline-block bg-white p-3 rounded-lg mb-2">
                <img src="{{ qr($certificate->getVerificationUrl(), 120) }}" alt="QR Code" class="w-24 h-24">
            </div>
            <p class="text-gray-500 text-xs">QR kod ile doğrulama yapılabilir</p>
        </div>
    </div>

    {{-- Download Button --}}
    <div class="max-w-2xl">
        <a href="{{ route('muzibu.certificate.download') }}"
            class="flex items-center justify-center gap-2 bg-amber-500 hover:bg-amber-600 text-black font-semibold py-3 px-6 rounded-lg transition">
            <i class="fas fa-download"></i>
            PDF Olarak İndir
        </a>
    </div>
</div>
@endsection

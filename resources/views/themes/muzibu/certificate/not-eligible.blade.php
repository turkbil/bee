@extends('themes.muzibu.layouts.app')

@section('title', 'Sertifika - Muzibu')

@section('content')
<div class="px-4 py-6 sm:px-6 sm:py-8">
    <div class="max-w-xl mx-auto text-center">
        <div class="bg-amber-900/30 border-2 border-amber-500 rounded-xl p-8 mb-6">
            <div class="w-16 h-16 bg-amber-500/20 rounded-full mx-auto mb-4 flex items-center justify-center">
                <i class="fas fa-lock text-amber-400 text-3xl"></i>
            </div>

            @if($reason === 'no_active_subscription')
            <h1 class="text-2xl font-bold text-amber-400 mb-2">Aktif Üyelik Gerekli</h1>
            <p class="text-gray-400 mb-6">
                Premium sertifikası oluşturabilmek için aktif bir ücretli üyeliğiniz olmalıdır.
                Deneme üyelikleri sertifika oluşturamaz.
            </p>
            <a href="{{ route('subscription.plans') }}"
                class="inline-flex items-center gap-2 bg-amber-500 hover:bg-amber-600 text-black font-semibold py-3 px-6 rounded-lg transition">
                <i class="fas fa-crown"></i>
                Premium'a Geç
            </a>
            @else
            <h1 class="text-2xl font-bold text-amber-400 mb-2">Sertifika Oluşturulamıyor</h1>
            <p class="text-gray-400">Bir hata oluştu. Lütfen daha sonra tekrar deneyin.</p>
            @endif
        </div>

        <a href="{{ route('home') }}" class="text-gray-400 hover:text-white text-sm">
            <i class="fas fa-arrow-left mr-1"></i> Ana Sayfaya Dön
        </a>
    </div>
</div>
@endsection

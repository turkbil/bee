@extends('themes.muzibu.layouts.app')

@section('title', 'Sertifika Bulunamadı - Muzibu')

@section('content')
<div class="px-4 py-6 sm:px-6 sm:py-8">
    <div class="max-w-xl mx-auto text-center">
        <div class="bg-red-900/30 border-2 border-red-500 rounded-xl p-8 mb-6">
            <div class="w-16 h-16 bg-red-500/20 rounded-full mx-auto mb-4 flex items-center justify-center">
                <i class="fas fa-question text-red-400 text-3xl"></i>
            </div>

            <h1 class="text-2xl font-bold text-red-400 mb-2">Sertifika Bulunamadı</h1>
            <p class="text-gray-400">
                Bu sertifika kodu sistemde kayıtlı değil veya geçersiz.
            </p>
        </div>

        <a href="{{ route('home') }}" class="text-gray-400 hover:text-white text-sm">
            <i class="fas fa-arrow-left mr-1"></i> Ana Sayfaya Dön
        </a>
    </div>
</div>
@endsection

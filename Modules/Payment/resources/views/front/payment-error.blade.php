@extends('themes.ixtif.layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-4xl mx-auto px-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8 text-center">
            <div class="mb-4">
                <i class="fa-solid fa-exclamation-triangle text-5xl text-red-500"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">
                Ödeme Hatası
            </h2>
            <p class="text-gray-600 dark:text-gray-400 mb-6">
                {{ $error }}
            </p>
            <a href="{{ route('cart.checkout') }}"
               class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Sepete Dön</span>
            </a>
        </div>
    </div>
</div>
@endsection

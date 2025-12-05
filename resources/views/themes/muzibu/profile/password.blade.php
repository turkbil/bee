@extends('themes.muzibu.layouts.app')

@section('title', 'Şifre Değiştir - Muzibu')

@section('content')
<div class="max-w-7xl mx-auto p-6">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        {{-- Left Sidebar --}}
        <div class="lg:col-span-1">
            @include('themes.muzibu.components.profile-sidebar', ['active' => 'password'])
        </div>

        {{-- Main Content --}}
        <div class="lg:col-span-3 space-y-6">
            {{-- Header --}}
            <div class="mb-4">
                <h1 class="text-3xl font-bold text-white mb-2">
                    <i class="fas fa-key text-yellow-400 mr-2"></i>
                    Şifre Değiştir
                </h1>
                <p class="text-muzibu-text-gray">Hesap güvenliğinizi korumak için güçlü bir şifre kullanın</p>
            </div>

    {{-- Success Message --}}
    @if (session('status') === 'password-updated')
        <div class="bg-green-500/20 border border-green-500/50 text-green-400 px-6 py-4 rounded-lg mb-6">
            ✅ Şifreniz başarıyla güncellendi!
        </div>
    @endif

    {{-- Security Info --}}
    <div class="bg-blue-500/20 border border-blue-500/50 rounded-lg p-4 mb-6">
        <div class="flex items-start">
            <svg class="w-5 h-5 text-blue-400 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
            </svg>
            <div>
                <h3 class="text-sm font-medium text-blue-200">
                    Güvenlik Önerisi
                </h3>
                <p class="mt-1 text-sm text-blue-300">
                    Hesabınızın güvenliği için en az 8 karakter uzunluğunda, büyük-küçük harf, rakam ve özel karakter içeren güçlü bir şifre kullanın.
                </p>
            </div>
        </div>
    </div>

    {{-- Password Form --}}
    <div class="bg-white/5 backdrop-blur-sm rounded-lg p-8 border border-white/10">
        <form method="post" action="{{ route('password.update') }}" class="space-y-6">
            @csrf
            @method('put')

            {{-- Current Password --}}
            <div>
                <label for="update_password_current_password" class="block text-sm font-medium text-white mb-2">
                    <i class="fa-solid fa-lock text-muzibu-text-gray mr-1"></i>
                    Mevcut Şifre <span class="text-red-500">*</span>
                </label>
                <input type="password"
                       class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-muzibu-text-gray focus:outline-none focus:ring-2 focus:ring-muzibu-coral focus:border-transparent @error('current_password', 'updatePassword') border-red-500 focus:ring-red-500 @enderror"
                       id="update_password_current_password"
                       name="current_password"
                       autocomplete="current-password"
                       placeholder="Mevcut şifrenizi girin">
                @error('current_password', 'updatePassword')
                    <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- New Password --}}
            <div>
                <label for="update_password_password" class="block text-sm font-medium text-white mb-2">
                    <i class="fa-solid fa-key text-muzibu-text-gray mr-1"></i>
                    Yeni Şifre <span class="text-red-500">*</span>
                </label>
                <input type="password"
                       class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-muzibu-text-gray focus:outline-none focus:ring-2 focus:ring-muzibu-coral focus:border-transparent @error('password', 'updatePassword') border-red-500 focus:ring-red-500 @enderror"
                       id="update_password_password"
                       name="password"
                       autocomplete="new-password"
                       placeholder="Yeni şifrenizi girin">
                @error('password', 'updatePassword')
                    <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Confirm Password --}}
            <div>
                <label for="update_password_password_confirmation" class="block text-sm font-medium text-white mb-2">
                    <i class="fa-solid fa-check-double text-muzibu-text-gray mr-1"></i>
                    Yeni Şifre (Tekrar) <span class="text-red-500">*</span>
                </label>
                <input type="password"
                       class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-muzibu-text-gray focus:outline-none focus:ring-2 focus:ring-muzibu-coral focus:border-transparent"
                       id="update_password_password_confirmation"
                       name="password_confirmation"
                       autocomplete="new-password"
                       placeholder="Yeni şifrenizi tekrar girin">
            </div>

            {{-- Submit Button --}}
            <div class="flex justify-end">
                <button
                    type="submit"
                    class="px-6 py-3 bg-yellow-600 hover:bg-yellow-700 rounded-lg text-white font-semibold transition-all duration-300 shadow-lg hover:shadow-yellow-600/30"
                >
                    <i class="fa-solid fa-lock mr-2"></i>
                    Şifreyi Güncelle
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

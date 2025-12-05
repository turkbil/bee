@extends('themes.muzibu.layouts.app')

@section('title', 'Hesabı Sil - Muzibu')

@section('content')
<div class="max-w-7xl mx-auto p-6">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        {{-- Left Sidebar --}}
        <div class="lg:col-span-1">
            @include('themes.muzibu.components.profile-sidebar', ['active' => 'delete'])
        </div>

        {{-- Main Content --}}
        <div class="lg:col-span-3 space-y-6">
            {{-- Header --}}
            <div class="mb-4">
                <h1 class="text-3xl font-bold text-red-400 mb-2">
                    <i class="fas fa-trash-can mr-2"></i>
                    Hesabı Sil
                </h1>
                <p class="text-red-400">⚠️ Dikkat: Bu işlem geri alınamaz!</p>
            </div>

    {{-- Warning Card --}}
    <div class="bg-red-500/10 border border-red-500/50 rounded-lg p-8">
        <h2 class="text-xl font-bold text-red-400 mb-4">❌ Hesabınızı Silmek İstediğinize Emin misiniz?</h2>

        <div class="space-y-3 text-muzibu-text-gray mb-6">
            <p>Hesabınızı sildiğinizde:</p>
            <ul class="list-disc list-inside space-y-2 ml-4">
                <li>Profil bilgileri ve kişisel verileriniz</li>
                <li>Tüm hesap ayarları ve tercihleriniz</li>
                <li>Giriş geçmişi ve aktivite loglarınız</li>
                <li>Hesabınızla ilişkili tüm veriler</li>
                <li>Bu işlem <strong class="text-red-400">GERİ ALINAMAZ</strong></li>
            </ul>
        </div>

        <form method="POST" action="{{ route('profile.destroy') }}" class="space-y-6">
            @csrf
            @method('DELETE')

            {{-- Password Confirmation --}}
            <div>
                <label for="password" class="block text-sm font-medium text-white mb-2">Şifrenizi Girin (Onay)</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    required
                    placeholder="Hesabınızı silmek için şifrenizi girin"
                    class="w-full px-4 py-3 bg-white/10 border border-red-500/50 rounded-lg text-white placeholder-muzibu-text-gray focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                >
                @error('password', 'userDeletion')
                    <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Submit Button --}}
            <div class="flex justify-end gap-4">
                <a href="{{ route('profile.edit') }}"
                   class="px-6 py-3 bg-white/10 hover:bg-white/20 rounded-lg text-white font-semibold transition-all duration-300">
                    İptal
                </a>
                <button
                    type="submit"
                    onclick="return confirm('Hesabınızı kalıcı olarak silmek istediğinize EMİN MİSİNİZ? Bu işlem geri alınamaz!')"
                    class="px-6 py-3 bg-red-600 hover:bg-red-700 rounded-lg text-white font-semibold transition-all duration-300 shadow-lg hover:shadow-red-500/30"
                >
                    🗑️ Hesabımı Kalıcı Olarak Sil
                </button>
            </div>
        </form>
    </div>
        </div>
    </div>
</div>
@endsection

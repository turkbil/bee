@extends('themes.muzibu.layouts.app')

@section('title', 'Profil Bilgileri - Muzibu')

@section('content')
<div class="max-w-7xl mx-auto p-6">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        {{-- Left Sidebar --}}
        <div class="lg:col-span-1">
            @include('themes.muzibu.components.profile-sidebar', ['active' => 'profile'])
        </div>

        {{-- Main Content --}}
        <div class="lg:col-span-3 space-y-6">
            {{-- Header --}}
            <div class="mb-4">
                <h1 class="text-3xl font-bold text-white mb-2">
                    <i class="fas fa-user-pen text-muzibu-coral mr-2"></i>
                    Profil Bilgileri
                </h1>
                <p class="text-muzibu-text-gray">Hesap bilgilerinizi güncelleyebilirsiniz</p>
            </div>

    {{-- Success Message --}}
    @if (session('status') === 'profile-updated')
        <div class="bg-green-500/20 border border-green-500/50 text-green-400 px-6 py-4 rounded-lg mb-6">
            ✅ Profil bilgileriniz başarıyla kaydedildi!
        </div>
    @endif

    {{-- Profile Form --}}
    <div class="bg-white/5 backdrop-blur-sm rounded-lg p-8 border border-white/10">
        <form method="post" action="{{ route('profile.update') }}" class="space-y-6">
            @csrf
            @method('patch')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Name Field --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-white mb-2">
                        <i class="fa-solid fa-user text-muzibu-text-gray mr-1"></i>
                        Ad <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-muzibu-text-gray focus:outline-none focus:ring-2 focus:ring-muzibu-coral focus:border-transparent @error('name') border-red-500 focus:ring-red-500 @enderror"
                           id="name"
                           name="name"
                           value="{{ old('name', $user->name) }}"
                           required
                           autofocus
                           autocomplete="given-name"
                           placeholder="Adınız">
                    @error('name')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Surname Field --}}
                <div>
                    <label for="surname" class="block text-sm font-medium text-white mb-2">
                        <i class="fa-solid fa-user text-muzibu-text-gray mr-1"></i>
                        Soyad <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-muzibu-text-gray focus:outline-none focus:ring-2 focus:ring-muzibu-coral focus:border-transparent @error('surname') border-red-500 focus:ring-red-500 @enderror"
                           id="surname"
                           name="surname"
                           value="{{ old('surname', $user->surname) }}"
                           required
                           autocomplete="family-name"
                           placeholder="Soyadınız">
                    @error('surname')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Phone Field --}}
                <div>
                    <label for="phone" class="block text-sm font-medium text-white mb-2">
                        <i class="fa-solid fa-phone text-muzibu-text-gray mr-1"></i>
                        Telefon
                    </label>
                    <input type="tel"
                           class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-muzibu-text-gray focus:outline-none focus:ring-2 focus:ring-muzibu-coral focus:border-transparent @error('phone') border-red-500 focus:ring-red-500 @enderror"
                           id="phone"
                           name="phone"
                           value="{{ old('phone', $user->phone) }}"
                           autocomplete="tel"
                           placeholder="Telefon numaranız">
                    @error('phone')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email Field --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-white mb-2">
                        <i class="fa-solid fa-envelope text-muzibu-text-gray mr-1"></i>
                        E-posta <span class="text-red-500">*</span>
                    </label>
                    <input type="email"
                           class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-muzibu-text-gray focus:outline-none focus:ring-2 focus:ring-muzibu-coral focus:border-transparent @error('email') border-red-500 focus:ring-red-500 @enderror"
                           id="email"
                           name="email"
                           value="{{ old('email', $user->email) }}"
                           required
                           autocomplete="email"
                           placeholder="email@domain.com">
                    @error('email')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Email Verification Warning --}}
            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="bg-yellow-500/20 border border-yellow-500/50 rounded-lg p-4">
                    <div class="flex items-start">
                        <i class="fa-solid fa-triangle-exclamation text-yellow-400 mt-0.5 mr-3 flex-shrink-0"></i>
                        <div class="flex-1">
                            <h3 class="text-sm font-medium text-yellow-200">
                                Email Doğrulama Gerekli
                            </h3>
                            <p class="mt-1 text-sm text-yellow-300">
                                Email adresiniz henüz doğrulanmamış. Hesabınızın güvenliği için lütfen email adresinizi doğrulayın.
                            </p>
                            <form id="send-verification" method="post" action="{{ route('verification.send') }}" class="mt-3">
                                @csrf
                                <button type="submit" class="text-sm font-medium text-yellow-200 hover:text-yellow-100 underline transition-colors">
                                    Doğrulama emailini tekrar gönder
                                </button>
                            </form>
                        </div>
                    </div>

                    @if (session('status') === 'verification-link-sent')
                        <div class="mt-3 bg-green-500/20 border border-green-500/50 rounded-lg p-3">
                            <div class="flex items-center">
                                <i class="fa-solid fa-circle-check text-green-400 mr-2"></i>
                                <p class="text-sm text-green-300">
                                    Email adresinize yeni bir doğrulama bağlantısı gönderildi.
                                </p>
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Submit Button --}}
            <div class="flex justify-end">
                <button
                    type="submit"
                    class="px-6 py-3 bg-gradient-to-r from-muzibu-coral to-[#ff9966] hover:from-[#ff9966] hover:to-muzibu-coral rounded-lg text-white font-semibold transition-all duration-300 shadow-lg hover:shadow-muzibu-coral/30"
                >
                    <i class="fa-solid fa-check mr-2"></i>
                    Profil Bilgilerini Kaydet
                </button>
            </div>
        </form>
    </div>
        </div>
    </div>
</div>
@endsection

@extends('themes.muzibu.layouts.app')

@section('title', 'Premium Sertifika - Muzibu')

@section('content')
<div class="px-4 py-6 sm:px-6 sm:py-8">
    {{-- Header --}}
    <div class="mb-6">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-amber-500/20 rounded-xl flex items-center justify-center">
                <i class="fas fa-certificate text-2xl text-amber-400"></i>
            </div>
            <div>
                <h1 class="text-3xl font-extrabold text-white">Premium Sertifikası</h1>
                <p class="text-gray-400">Üyelik bilgilerinizi içeren resmi sertifikanızı oluşturun</p>
            </div>
        </div>
    </div>

    {{-- Info Box --}}
    <div class="bg-blue-900/30 border border-blue-600/50 rounded-xl p-4 mb-6">
        <div class="flex items-start gap-3">
            <i class="fas fa-info-circle text-blue-400 mt-1"></i>
            <div class="text-sm text-gray-300">
                <p class="font-medium text-blue-400 mb-1">Önemli Bilgi</p>
                <ul class="space-y-1 text-gray-400">
                    <li>• Sertifika bilgileri oluşturulduktan sonra <strong class="text-white">değiştirilemez</strong></li>
                    <li>• Üyelik başlangıç tarihiniz: <strong class="text-amber-400">{{ $firstPaidDate->format('d.m.Y') }}</strong></li>
                    <li>• QR kod ile güncel üyelik durumunuz doğrulanabilir</li>
                </ul>
            </div>
        </div>
    </div>

    {{-- Form --}}
    <form action="{{ route('muzibu.certificate.preview') }}" method="POST" class="max-w-xl">
        @csrf

        <div class="bg-white/5 border border-white/10 rounded-xl p-6 space-y-5">
            {{-- Member Name --}}
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">
                    Firma / Kişi Adı <span class="text-red-400">*</span>
                </label>
                <input type="text" name="member_name" value="{{ old('member_name') }}"
                    class="w-full bg-white/5 border border-white/20 rounded-lg px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:border-amber-500"
                    placeholder="Örn: Abc Teknoloji Ltd. Şti." required>
                <p class="text-xs text-gray-500 mt-1">İlk harf büyük, devamı küçük olacak şekilde otomatik düzeltilir</p>
                @error('member_name')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tax Office --}}
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">
                    Vergi Dairesi
                </label>
                <input type="text" name="tax_office" value="{{ old('tax_office') }}"
                    class="w-full bg-white/5 border border-white/20 rounded-lg px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:border-amber-500"
                    placeholder="Örn: Kadıköy Vergi Dairesi">
                @error('tax_office')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tax Number --}}
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">
                    Vergi Numarası
                </label>
                <input type="text" name="tax_number" value="{{ old('tax_number') }}"
                    class="w-full bg-white/5 border border-white/20 rounded-lg px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:border-amber-500"
                    placeholder="Örn: 1234567890">
                @error('tax_number')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Address --}}
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">
                    Adres
                </label>
                <textarea name="address" rows="2"
                    class="w-full bg-white/5 border border-white/20 rounded-lg px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:border-amber-500"
                    placeholder="Örn: Atatürk Caddesi No:15 Kadıköy/İstanbul">{{ old('address') }}</textarea>
                @error('address')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Confirmation --}}
            <div class="bg-amber-900/20 border border-amber-600/50 rounded-lg p-4">
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" name="confirmed" value="1" required
                        class="mt-1 w-5 h-5 rounded border-white/20 bg-white/5 text-amber-500 focus:ring-amber-500">
                    <span class="text-sm text-gray-300">
                        Yukarıdaki bilgilerin doğru olduğunu ve oluşturulduktan sonra
                        <strong class="text-amber-400">değiştirilemeyeceğini</strong> onaylıyorum.
                    </span>
                </label>
                @error('confirmed')
                    <p class="text-red-400 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            {{-- Submit --}}
            <button type="submit"
                class="w-full bg-amber-500 hover:bg-amber-600 text-black font-semibold py-3 rounded-lg transition">
                <i class="fas fa-eye mr-2"></i> Ön İzleme
            </button>
        </div>
    </form>
</div>
@endsection

@extends('themes.muzibu.layouts.app')

@section('title', __('muzibu::front.certificate.title') . ' - Muzibu')

@php
    $savedForm = session('certificate_form', []);
@endphp

@section('content')
<div class="min-h-screen">
    <div class="px-4 py-6 sm:px-6 sm:py-8 max-w-3xl mx-auto">

        {{-- Header --}}
        <div class="mb-6 sm:mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3 sm:gap-4">
                <div class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-amber-500/30 to-amber-600/20 rounded-xl flex items-center justify-center flex-shrink-0 ring-1 ring-amber-500/30">
                    <i class="fas fa-certificate text-2xl sm:text-3xl text-amber-400"></i>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-white mb-0.5">
                        {{ __('muzibu::front.certificate.title') }}
                    </h1>
                    <p class="text-gray-400 text-sm sm:text-base">{{ __('muzibu::front.certificate.subtitle') }}</p>
                </div>
            </div>
            <a href="/dashboard" class="inline-flex items-center justify-center px-4 py-2 bg-white/10 hover:bg-white/20 text-white text-sm rounded-lg transition" data-spa>
                <i class="fas fa-arrow-left mr-2"></i>{{ __('muzibu::front.back') }}
            </a>
        </div>

        {{-- Membership Info Card --}}
        <div class="bg-gradient-to-r from-amber-500/10 to-orange-500/10 border border-amber-500/30 rounded-2xl p-5 sm:p-6 mb-6 backdrop-blur-sm">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-amber-500/20 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-crown text-2xl text-amber-400"></i>
                </div>
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-amber-400 font-semibold">{{ __('muzibu::front.certificate.premium_member') }}</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-500/20 text-green-400 ring-1 ring-green-500/30">
                            <i class="fas fa-check-circle mr-1"></i>{{ __('muzibu::front.certificate.active') }}
                        </span>
                    </div>
                    <p class="text-gray-400 text-sm">
                        {{ __('muzibu::front.certificate.member_since') }}:
                        <span class="text-white font-medium">{{ $firstPaidDate->format('d.m.Y') }}</span>
                    </p>
                </div>
            </div>
        </div>

        {{-- Warning Box --}}
        <div class="bg-slate-800/50 border border-amber-500/30 rounded-xl p-4 mb-6">
            <div class="flex gap-3">
                <div class="flex-shrink-0 w-8 h-8 bg-amber-500/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-amber-400"></i>
                </div>
                <div class="text-sm">
                    <p class="font-medium text-amber-400 mb-1">{{ __('muzibu::front.certificate.important') }}</p>
                    <ul class="space-y-1 text-gray-400">
                        <li class="flex items-start gap-2">
                            <i class="fas fa-lock text-xs mt-1 text-amber-500/70"></i>
                            <span>{{ __('muzibu::front.certificate.warning_unchangeable') }}</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fas fa-qrcode text-xs mt-1 text-amber-500/70"></i>
                            <span>{{ __('muzibu::front.certificate.warning_qr') }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- Form --}}
        <form action="{{ route('muzibu.certificate.preview') }}" method="POST"
            x-data="certificateForm()"
            x-init="init()">
            @csrf

            <div class="bg-slate-900/70 border border-white/10 rounded-2xl overflow-hidden">
                {{-- Form Header --}}
                <div class="bg-white/5 border-b border-white/10 px-5 py-4">
                    <h2 class="font-semibold text-white flex items-center gap-2">
                        <i class="fas fa-edit text-amber-400"></i>
                        {{ __('muzibu::front.certificate.form_title') }}
                    </h2>
                </div>

                <div class="p-5 sm:p-6 space-y-5">
                    {{-- Member Name --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">
                            {{ __('muzibu::front.certificate.member_name') }} <span class="text-red-400">*</span>
                        </label>
                        <input type="text" name="member_name" x-model="memberName" @blur="formatMemberName()"
                            class="w-full bg-slate-800/50 border border-white/10 rounded-xl px-4 py-3.5 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-amber-500/50 focus:border-amber-500/50 transition"
                            placeholder="{{ __('muzibu::front.certificate.member_name_placeholder') }}" required>

                        {{-- Exception Checkbox --}}
                        <div class="mt-2 flex items-center gap-2">
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input type="checkbox" name="skip_correction" x-model="skipCorrection" value="1"
                                    class="w-4 h-4 rounded border-white/20 bg-slate-800 text-amber-500 focus:ring-amber-500 focus:ring-offset-0 focus:ring-offset-slate-900">
                                <span class="text-xs text-gray-500 group-hover:text-gray-400 transition">
                                    {{ __('muzibu::front.certificate.skip_correction') }}
                                </span>
                            </label>
                        </div>

                        <p class="text-xs text-gray-500 mt-2" x-show="!skipCorrection">
                            <i class="fas fa-info-circle mr-1"></i>{{ __('muzibu::front.certificate.auto_correction_info') }}
                        </p>
                        <p class="text-xs text-amber-500/70 mt-2" x-show="skipCorrection" x-cloak>
                            <i class="fas fa-keyboard mr-1"></i>{{ __('muzibu::front.certificate.manual_mode_info') }}
                        </p>

                        @error('member_name')
                            <p class="text-red-400 text-sm mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Tax Office & Tax Number Row --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- Tax Office --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">
                                {{ __('muzibu::front.certificate.tax_office') }}
                            </label>
                            <input type="text" name="tax_office" x-model="taxOffice" @blur="formatTaxOffice()"
                                class="w-full bg-slate-800/50 border border-white/10 rounded-xl px-4 py-3.5 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-amber-500/50 focus:border-amber-500/50 transition"
                                placeholder="{{ __('muzibu::front.certificate.tax_office_placeholder') }}">
                            @error('tax_office')
                                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Tax Number --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">
                                {{ __('muzibu::front.certificate.tax_number') }}
                            </label>
                            <input type="text" name="tax_number" x-model="taxNumber"
                                class="w-full bg-slate-800/50 border border-white/10 rounded-xl px-4 py-3.5 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-amber-500/50 focus:border-amber-500/50 transition"
                                placeholder="{{ __('muzibu::front.certificate.tax_number_placeholder') }}">
                            @error('tax_number')
                                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Address --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">
                            {{ __('muzibu::front.certificate.address') }}
                        </label>
                        <textarea name="address" rows="3" x-model="address" @blur="formatAddress()"
                            class="w-full bg-slate-800/50 border border-white/10 rounded-xl px-4 py-3.5 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-amber-500/50 focus:border-amber-500/50 transition resize-none"
                            placeholder="{{ __('muzibu::front.certificate.address_placeholder') }}"></textarea>
                        <p class="text-xs text-gray-500 mt-1">
                            <i class="fas fa-info-circle mr-1"></i>Her kelimenin ilk harfi otomatik büyütülür
                        </p>
                        @error('address')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Confirmation & Submit --}}
                <div class="bg-slate-800/30 border-t border-white/10 p-5 sm:p-6">
                    {{-- Confirmation Checkbox --}}
                    <div class="bg-amber-500/10 border border-amber-500/30 rounded-xl p-4 mb-5">
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" name="confirmed" value="1" required
                                class="mt-0.5 w-5 h-5 rounded border-amber-500/50 bg-slate-800 text-amber-500 focus:ring-amber-500 focus:ring-offset-0">
                            <span class="text-sm text-gray-300 leading-relaxed">
                                {{ __('muzibu::front.certificate.confirm_text_1') }}
                                <strong class="text-amber-400">{{ __('muzibu::front.certificate.confirm_unchangeable') }}</strong>
                                {{ __('muzibu::front.certificate.confirm_text_2') }}
                            </span>
                        </label>
                        @error('confirmed')
                            <p class="text-red-400 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Submit Button --}}
                    <button type="submit"
                        class="w-full bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-black font-bold py-4 rounded-xl transition-all duration-300 shadow-lg shadow-amber-500/25 hover:shadow-amber-500/40 flex items-center justify-center gap-2 text-base">
                        <i class="fas fa-eye"></i>
                        {{ __('muzibu::front.certificate.preview_button') }}
                    </button>
                </div>
            </div>
        </form>

    </div>
</div>

<script>
function certificateForm() {
    return {
        skipCorrection: {{ ($savedForm['skip_correction'] ?? false) ? 'true' : 'false' }},
        memberName: '',
        taxOffice: '',
        taxNumber: '',
        address: '',

        init() {
            // Load saved form data
            this.memberName = {!! json_encode(old('member_name', $savedForm['member_name'] ?? '')) !!};
            this.taxOffice = {!! json_encode(old('tax_office', $savedForm['tax_office'] ?? '')) !!};
            this.taxNumber = {!! json_encode(old('tax_number', $savedForm['tax_number'] ?? '')) !!};
            this.address = {!! json_encode(old('address', $savedForm['address'] ?? '')) !!};
        },

        correctText(text) {
            if (!text) return text;

            // Slash etrafindaki bosluklari temizle (  /  -> /)
            text = text.replace(/\s*\/\s*/g, '/');

            // Turkce buyuk harf donusumu
            const toUpperTR = (char) => {
                if (char === 'i') return 'İ';
                if (char === 'ı') return 'I';
                return char.toUpperCase();
            };

            // Turkce kucuk harf donusumu
            const toLowerTR = (char) => {
                if (char === 'I') return 'ı';
                if (char === 'İ') return 'i';
                return char.toLowerCase();
            };

            // Title case uygula - karakter karakter isle
            let result = '';
            let capitalizeNext = true;

            for (let i = 0; i < text.length; i++) {
                const char = text[i];

                // Bosluk, yeni satir, nokta, iki nokta, slash sonrasi buyuk harf
                if (char === ' ' || char === '\n' || char === '\r') {
                    result += char;
                    capitalizeNext = true;
                } else if (char === '.' || char === ':' || char === '/') {
                    result += char;
                    capitalizeNext = true;
                } else if (capitalizeNext) {
                    result += toUpperTR(char);
                    capitalizeNext = false;
                } else {
                    result += toLowerTR(char);
                }
            }

            return result;
        },

        formatMemberName() {
            // Sadece skipCorrection FALSE ise duzelt
            if (!this.skipCorrection) {
                this.memberName = this.correctText(this.memberName);
            }
        },

        formatTaxOffice() {
            // Vergi dairesi her zaman duzeltilir
            this.taxOffice = this.correctText(this.taxOffice);
        },

        formatAddress() {
            // Adres her zaman duzeltilir
            this.address = this.correctText(this.address);
        }
    }
}
</script>
@endsection

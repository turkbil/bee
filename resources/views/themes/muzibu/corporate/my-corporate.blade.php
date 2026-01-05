@extends('themes.muzibu.layouts.app')

@section('title', __('muzibu::front.corporate.my_corporate') . ' - Muzibu')

@section('content')
<div x-data="{
    leaving: false,
    showLeaveModal() {
        if (confirm('Kurumdan ayrilmak istediginize emin misiniz? Bu islem geri alinamaz.')) {
            this.leaveCorporate();
        }
    },
    async leaveCorporate() {
        this.leaving = true;
        try {
            const response = await fetch('/corporate/leave', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'Accept': 'application/json'
                }
            });
            const data = await response.json();
            if (data.success) {
                window.dispatchEvent(new CustomEvent('toast', { detail: { message: data.message || 'Kurumdan ayrildiniz', type: 'success' } }));
                setTimeout(() => window.location.href = '/dashboard', 1500);
            } else {
                window.dispatchEvent(new CustomEvent('toast', { detail: { message: data.message || 'Bir hata olustu', type: 'error' } }));
            }
        } catch (e) {
            window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Bir hata olustu', type: 'error' } }));
        }
        this.leaving = false;
    }
}" x-init="if($store.sidebar) $store.sidebar.rightSidebarVisible = false;">
    <div class="max-w-3xl mx-auto px-3 sm:px-4 md:px-6 lg:px-8 py-6 sm:py-12 pb-20">

        {{-- Header --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-purple-500 to-pink-500 rounded-2xl mb-4">
                <i class="fas fa-building text-white text-3xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-white mb-2">Kurumsal Uyelik</h1>
            <p class="text-gray-400">Bagli oldugunuz kurum bilgileri</p>
        </div>

        {{-- Corporate Info Card --}}
        <div class="bg-gradient-to-r from-purple-500/10 to-pink-500/10 border border-purple-500/30 rounded-2xl overflow-hidden mb-6">
            {{-- Company Header --}}
            <div class="p-6 border-b border-purple-500/20">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-pink-500 rounded-xl flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-building text-white text-2xl"></i>
                    </div>
                    <div class="flex-1">
                        <h2 class="text-2xl font-bold text-white">{{ $account->parent->company_name ?? 'Kurumsal' }}</h2>
                        <p class="text-purple-300">Kurumsal Uye</p>
                    </div>
                    <div class="flex items-center gap-2">
                        @if($account->is_active && $account->parent->is_active)
                            <span class="px-3 py-1 bg-green-500/20 text-green-400 rounded-full text-sm">
                                <i class="fas fa-check-circle mr-1"></i>Aktif
                            </span>
                        @else
                            <span class="px-3 py-1 bg-red-500/20 text-red-400 rounded-full text-sm">
                                <i class="fas fa-times-circle mr-1"></i>Pasif
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Details --}}
            <div class="p-6 space-y-4">
                {{-- Branch Name / Your Name --}}
                <div class="flex items-center justify-between py-3 border-b border-white/10">
                    <span class="text-gray-400"><i class="fas fa-tag mr-2"></i>Sube Adi / Isim</span>
                    <span class="text-white font-medium">{{ $account->branch_name ?? auth()->user()->name }}</span>
                </div>

                {{-- Join Date --}}
                <div class="flex items-center justify-between py-3 border-b border-white/10">
                    <span class="text-gray-400"><i class="fas fa-calendar mr-2"></i>Katilim Tarihi</span>
                    <span class="text-white font-medium">{{ $account->created_at->format('d.m.Y') }}</span>
                </div>

                {{-- Corporate Code --}}
                <div class="flex items-center justify-between py-3">
                    <span class="text-gray-400"><i class="fas fa-key mr-2"></i>Kurumsal Kod</span>
                    <span class="text-white font-mono font-bold tracking-wider">{{ $account->parent->corporate_code ?? '-' }}</span>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="bg-white/5 border border-white/10 rounded-2xl p-6">
            <h3 class="text-lg font-bold text-white mb-4">Islemler</h3>

            <div class="space-y-3">
                <a href="/dashboard" class="flex items-center justify-between p-4 bg-white/5 hover:bg-white/10 rounded-xl transition" data-spa>
                    <div class="flex items-center gap-3">
                        <i class="fas fa-home text-blue-400"></i>
                        <span class="text-white">Panelime Don</span>
                    </div>
                    <i class="fas fa-chevron-right text-gray-500"></i>
                </a>

                <button type="button"
                        @click="showLeaveModal()"
                        :disabled="leaving"
                        class="w-full flex items-center justify-between p-4 bg-red-500/10 hover:bg-red-500/20 rounded-xl transition disabled:opacity-50 cursor-pointer">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-sign-out-alt text-red-400" x-show="!leaving"></i>
                        <i class="fas fa-spinner fa-spin text-red-400" x-show="leaving"></i>
                        <span class="text-red-400">{{ __('muzibu::front.corporate.leave_corporate') }}</span>
                    </div>
                    <i class="fas fa-chevron-right text-red-400" x-show="!leaving"></i>
                </button>
            </div>

            <p class="text-gray-500 text-xs mt-4 text-center">
                <i class="fas fa-info-circle mr-1"></i>
                Kurumdan ayrildiginizda kurumsal avantajlariniz sona erer.
            </p>
        </div>

    </div>
</div>
@endsection

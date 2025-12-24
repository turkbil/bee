@extends('themes.muzibu.layouts.app')

@section('title', 'Kurumsal Hesap - Muzibu')

@section('content')
<script>
window.corporatePage = function() {
    return {
        // Katılım formu
        joinCode: '',
        joining: false,

        // Oluşturma formu
        companyName: '',
        createCode: '',
        creating: false,
        codeError: '',
        codeAvailable: null, // null=kontrol edilmedi, true=uygun, false=kullanımda
        checkingCode: false,

        get createCodeValid() {
            return this.createCode.length === 8 && this.codeAvailable === true;
        },

        async validateCreateCode() {
            if (this.createCode.length === 0) {
                this.codeError = '';
                this.codeAvailable = null;
            } else if (this.createCode.length < 8) {
                this.codeError = 'Tam olarak 8 karakter gerekli';
                this.codeAvailable = null;
            } else {
                // 8 karakter olunca otomatik kontrol et
                this.codeError = '';
                await this.checkCodeAvailability();
            }
        },

        async checkCodeAvailability() {
            if (this.createCode.length !== 8 || this.checkingCode) return;
            this.checkingCode = true;
            this.codeAvailable = null;

            try {
                const response = await fetch('/corporate/check-code', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ code: this.createCode })
                });

                const data = await response.json();
                this.codeAvailable = data.available;
                if (!data.available) {
                    this.codeError = data.message || 'Bu kod zaten kullanımda';
                } else {
                    this.codeError = '';
                }
            } catch (error) {
                this.codeError = 'Kontrol sirasinda hata olustu';
                this.codeAvailable = null;
            } finally {
                this.checkingCode = false;
            }
        },

        async joinWithCode() {
            if (this.joinCode.length !== 8 || this.joining) return;
            this.joining = true;

            try {
                const response = await fetch('/corporate/join', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ corporate_code: this.joinCode })
                });

                const data = await response.json();

                if (data.success) {
                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: { message: data.message, type: 'success' }
                    }));
                    // Full page reload (SPA bypass)
                    setTimeout(() => {
                        window.location.assign(data.redirect || '/dashboard');
                    }, 1000);
                } else {
                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: { message: data.message || 'Gecersiz kod', type: 'error' }
                    }));
                }
            } catch (error) {
                window.dispatchEvent(new CustomEvent('toast', {
                    detail: { message: 'Hata olustu', type: 'error' }
                }));
            } finally {
                this.joining = false;
            }
        },

        async createCorporate() {
            if (this.companyName.length < 2 || !this.createCodeValid || this.creating) return;
            this.creating = true;
            this.codeError = '';

            try {
                const response = await fetch('/corporate/create', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        company_name: this.companyName,
                        corporate_code: this.createCode
                    })
                });

                const data = await response.json();

                if (data.success) {
                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: { message: data.message, type: 'success' }
                    }));
                    setTimeout(() => {
                        window.location.href = data.redirect || '/corporate/dashboard';
                    }, 1500);
                } else {
                    this.codeError = data.message || 'Bu kod zaten kullanımda';
                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: { message: data.message || 'Hata olustu', type: 'error' }
                    }));
                }
            } catch (error) {
                window.dispatchEvent(new CustomEvent('toast', {
                    detail: { message: 'Hata olustu', type: 'error' }
                }));
            } finally {
                this.creating = false;
            }
        },

        async leaveCorporate() {
            if (!confirm('Kurumsal hesaptan ayrilmak istediginize emin misiniz?')) return;

            try {
                const response = await fetch('/corporate/leave', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: { message: data.message, type: 'success' }
                    }));
                    location.reload();
                } else {
                    throw new Error(data.message);
                }
            } catch (error) {
                window.dispatchEvent(new CustomEvent('toast', {
                    detail: { message: error.message || 'Hata olustu', type: 'error' }
                }));
            }
        }
    }
}
</script>

<div class="py-12 pb-24" x-data="corporatePage()">
    <div class="max-w-4xl mx-auto px-4">

        {{-- Already Member --}}
        @if($existingAccount)
            <div class="max-w-md mx-auto bg-gradient-to-br from-purple-500/10 to-pink-500/10 border border-purple-500/30 rounded-2xl p-8 text-center">
                <div class="w-20 h-20 bg-gradient-to-br from-purple-500 to-pink-500 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-building text-white text-3xl"></i>
                </div>

                @if($existingAccount->isParent())
                    <h1 class="text-2xl font-bold text-white mb-2">Zaten Kurumsal Hesap Sahibisiniz</h1>
                    <p class="text-gray-400 mb-6">{{ $existingAccount->company_name ?? 'Kurumsal' }} - Ana Sube</p>
                    <a href="/corporate/dashboard" class="inline-block px-6 py-3 bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white font-semibold rounded-xl transition" data-spa>
                        <i class="fas fa-tachometer-alt mr-2"></i>Yonetim Paneli
                    </a>
                @else
                    <h1 class="text-2xl font-bold text-white mb-2">Zaten Bir Kurumsal Hesaba Uyesiniz</h1>
                    <p class="text-gray-400 mb-4">{{ $existingAccount->parent->company_name ?? 'Kurumsal' }} uyesi</p>
                    <p class="text-sm text-orange-400 mb-6">
                        <i class="fas fa-info-circle mr-1"></i>
                        Baska bir hesaba katilmak icin once mevcut uyeliginizden ayrilmalisiniz.
                    </p>
                    <div class="flex flex-col gap-3">
                        <a href="/corporate/my-corporate" class="px-6 py-3 bg-white/10 hover:bg-white/20 text-white font-semibold rounded-xl transition" data-spa>
                            <i class="fas fa-user mr-2"></i>Uyeligim
                        </a>
                        <button @click="leaveCorporate()" class="px-6 py-3 bg-red-500/20 hover:bg-red-500/30 text-red-400 font-semibold rounded-xl transition">
                            <i class="fas fa-sign-out-alt mr-2"></i>Ayril
                        </button>
                    </div>
                @endif
            </div>
        @else
            {{-- Page Header --}}
            <div class="text-center mb-10">
                <h1 class="text-3xl font-bold text-white mb-3">Kurumsal Hesap</h1>
                <p class="text-gray-400">Mevcut bir kurumsal hesaba katilin veya kendi kurumsal yapinizi olusturun</p>
            </div>

            {{-- Two Column Layout --}}
            <div class="grid md:grid-cols-2 gap-6">

                {{-- 1. DAVET KODU ILE KATIL --}}
                <div class="bg-gradient-to-br from-green-500/10 to-emerald-500/10 border border-green-500/30 rounded-2xl p-6">
                    <div class="text-center mb-6">
                        <div class="w-14 h-14 bg-gradient-to-br from-green-500 to-emerald-500 rounded-xl flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-sign-in-alt text-white text-xl"></i>
                        </div>
                        <h2 class="text-xl font-bold text-white mb-1">Davet Kodu ile Katil</h2>
                        <p class="text-gray-400 text-sm">Sirketinizden aldiginiz 8 haneli kodu girin</p>
                    </div>

                    <form @submit.prevent="joinWithCode()" class="space-y-4">
                        <div>
                            <label class="block text-sm text-gray-400 mb-2">Davet Kodu</label>
                            <input type="text" x-model="joinCode"
                                   placeholder="ABCD1234"
                                   maxlength="8"
                                   class="w-full px-4 py-3 bg-white/5 border border-white/20 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-green-500 uppercase tracking-widest font-mono text-xl text-center"
                                   @input="joinCode = $event.target.value.toUpperCase().replace(/[^A-Z0-9]/g, '')">
                            <p class="text-xs text-gray-500 mt-2 text-center">
                                <span x-text="joinCode.length"></span>/8 karakter
                            </p>
                        </div>

                        <button type="submit" :disabled="joinCode.length !== 8 || joining"
                                class="w-full py-3 bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 disabled:from-gray-600 disabled:to-gray-700 disabled:cursor-not-allowed text-white font-semibold rounded-xl transition">
                            <span x-show="!joining"><i class="fas fa-arrow-right mr-2"></i>Katil</span>
                            <span x-show="joining"><i class="fas fa-spinner fa-spin mr-2"></i>Kontrol ediliyor...</span>
                        </button>
                    </form>
                </div>

                {{-- 2. KENDI KURUMSAL YAPINI OLUSTUR --}}
                <div class="bg-gradient-to-br from-purple-500/10 to-pink-500/10 border border-purple-500/30 rounded-2xl p-6">
                    <div class="text-center mb-6">
                        <div class="w-14 h-14 bg-gradient-to-br from-purple-500 to-pink-500 rounded-xl flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-crown text-yellow-400 text-xl"></i>
                        </div>
                        <h2 class="text-xl font-bold text-white mb-1">Kurumsal Yapi Olustur</h2>
                        <p class="text-gray-400 text-sm">Kendi sirketiniz icin kurumsal hesap acin</p>
                    </div>

                    <form @submit.prevent="createCorporate()" class="space-y-4">
                        <div>
                            <label class="block text-sm text-gray-400 mb-2">Sirket / Isletme Adi</label>
                            <input type="text" x-model="companyName"
                                   placeholder="Ornek Teknoloji A.S."
                                   maxlength="100"
                                   class="w-full px-4 py-3 bg-white/5 border border-white/20 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-purple-500">
                        </div>

                        <div>
                            <label class="block text-sm text-gray-400 mb-2">Kurumsal Kodunuz <span class="text-purple-400">(siz belirleyin)</span></label>
                            <div class="relative">
                                <input type="text" x-model="createCode"
                                       @input="createCode = $event.target.value.toUpperCase().replace(/[^A-Z0-9]/g, '').slice(0, 8); validateCreateCode()"
                                       placeholder="FIRMA001"
                                       maxlength="8"
                                       class="w-full px-4 py-3 pr-12 bg-white/5 border rounded-xl text-white placeholder-gray-500 focus:outline-none uppercase tracking-widest font-mono text-xl text-center"
                                       :class="codeError ? 'border-red-500' : (codeAvailable === true ? 'border-green-500' : 'border-white/20 focus:border-purple-500')">
                                {{-- Status Icon --}}
                                <div class="absolute right-3 top-1/2 -translate-y-1/2">
                                    <i x-show="checkingCode" class="fas fa-spinner fa-spin text-gray-400"></i>
                                    <i x-show="!checkingCode && codeAvailable === true" class="fas fa-check-circle text-green-400"></i>
                                    <i x-show="!checkingCode && codeAvailable === false" class="fas fa-times-circle text-red-400"></i>
                                </div>
                            </div>
                            <div class="flex justify-between items-center mt-2">
                                <p class="text-xs" :class="codeAvailable === true ? 'text-green-400' : 'text-gray-500'">
                                    <span x-text="createCode.length"></span>/8 karakter
                                    <span x-show="codeAvailable === true" class="text-green-400 ml-1">- Uygun!</span>
                                </p>
                                <p x-show="codeError" x-text="codeError" class="text-red-400 text-xs"></p>
                            </div>
                            <p class="text-xs text-purple-400 mt-1">
                                <i class="fas fa-info-circle mr-1"></i>Bu kod ile calisanlariniz size katilabilir
                            </p>
                        </div>

                        <button type="submit" :disabled="companyName.length < 2 || !createCodeValid || creating"
                                class="w-full py-3 bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 disabled:from-gray-600 disabled:to-gray-700 disabled:cursor-not-allowed text-white font-semibold rounded-xl transition">
                            <span x-show="!creating"><i class="fas fa-crown mr-2"></i>Kurumsal Hesap Olustur</span>
                            <span x-show="creating"><i class="fas fa-spinner fa-spin mr-2"></i>Olusturuluyor...</span>
                        </button>
                    </form>
                </div>

            </div>

            {{-- Info Box --}}
            <div class="mt-8 bg-blue-500/10 border border-blue-500/30 rounded-xl p-4 text-center">
                <p class="text-blue-300 text-sm">
                    <i class="fas fa-lightbulb mr-2"></i>
                    Kurumsal hesap ile calisanlariniza premium erisim saglayabilir, dinleme istatistiklerini takip edebilirsiniz.
                </p>
            </div>
        @endif

        {{-- Back Link --}}
        <div class="mt-6 text-center">
            <a href="/dashboard" class="text-gray-400 hover:text-white text-sm" data-spa>
                <i class="fas fa-arrow-left mr-1"></i>Panele Don
            </a>
        </div>

    </div>
</div>

@endsection

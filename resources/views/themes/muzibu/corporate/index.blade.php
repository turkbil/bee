@extends('themes.muzibu.layouts.app')

@section('title', __('muzibu::front.corporate.title'))

@section('content')
{{-- ✅ corporateIndexApp defined globally in app.blade.php - Works with SPA! --}}
<div class="min-h-screen" x-data="corporateIndexApp()">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

        {{-- Hero Section --}}
        <div class="text-center mb-16">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-purple-500 to-pink-500 rounded-2xl mb-6">
                <i class="fas fa-building text-white text-3xl"></i>
            </div>
            <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">
                {{ __('muzibu::front.corporate.title') }}
            </h1>
            <p class="text-xl text-gray-400 max-w-2xl mx-auto">
                {{ __('muzibu::front.corporate.description') }}
            </p>
        </div>

        {{-- Already Corporate Member --}}
        @if($userCorporate)
            <div class="bg-gradient-to-r from-purple-500/20 to-pink-500/20 border border-purple-500/30 rounded-2xl p-8 mb-12">
                <div class="flex flex-col md:flex-row items-center gap-6">
                    <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-pink-500 rounded-xl flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-check text-white text-2xl"></i>
                    </div>
                    <div class="flex-1 text-center md:text-left">
                        <h2 class="text-2xl font-bold text-white mb-2">{{ __('muzibu::front.corporate.already_corporate') }}</h2>
                        @if($userCorporate->isParent())
                            <p class="text-purple-300">{{ $userCorporate->company_name ?? __('muzibu::front.footer.corporate') }} - {{ __('muzibu::front.corporate.main_branch') }}</p>
                        @else
                            <p class="text-purple-300">{{ $userCorporate->parent->company_name ?? __('muzibu::front.footer.corporate') }} {{ __('muzibu::front.corporate.member') }}</p>
                        @endif
                    </div>
                    <div class="flex gap-3">
                        @if($userCorporate->isParent())
                            <a href="/corporate/dashboard" class="px-6 py-3 bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white font-semibold rounded-xl transition" data-spa>
                                <i class="fas fa-tachometer-alt mr-2"></i>{{ __('muzibu::front.corporate.management_panel') }}
                            </a>
                        @else
                            <a href="/corporate/my-corporate" class="px-6 py-3 bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white font-semibold rounded-xl transition" data-spa>
                                <i class="fas fa-user mr-2"></i>{{ __('muzibu::front.corporate.my_membership') }}
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        {{-- Features Grid --}}
        <div class="grid md:grid-cols-3 gap-6 mb-16">
            <div class="bg-white/5 border border-white/10 rounded-xl p-6 hover:border-purple-500/50 transition">
                <div class="w-12 h-12 bg-purple-500/20 rounded-xl flex items-center justify-center mb-4">
                    <i class="fas fa-users text-purple-400 text-xl"></i>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">{{ __('muzibu::front.corporate.bulk_membership') }}</h3>
                <p class="text-gray-400">{{ __('muzibu::front.corporate.bulk_membership_desc') }}</p>
            </div>

            <div class="bg-white/5 border border-white/10 rounded-xl p-6 hover:border-pink-500/50 transition">
                <div class="w-12 h-12 bg-pink-500/20 rounded-xl flex items-center justify-center mb-4">
                    <i class="fas fa-chart-line text-pink-400 text-xl"></i>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">{{ __('muzibu::front.corporate.statistics') }}</h3>
                <p class="text-gray-400">{{ __('muzibu::front.corporate.statistics_desc') }}</p>
            </div>

            <div class="bg-white/5 border border-white/10 rounded-xl p-6 hover:border-blue-500/50 transition">
                <div class="w-12 h-12 bg-blue-500/20 rounded-xl flex items-center justify-center mb-4">
                    <i class="fas fa-cog text-blue-400 text-xl"></i>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">{{ __('muzibu::front.corporate.easy_management') }}</h3>
                <p class="text-gray-400">{{ __('muzibu::front.corporate.easy_management_desc') }}</p>
            </div>
        </div>

        {{-- How It Works --}}
        <div class="bg-white/5 border border-white/10 rounded-2xl p-8 mb-16">
            <h2 class="text-2xl font-bold text-white mb-8 text-center">{{ __('muzibu::front.corporate.how_it_works') }}</h2>

            <div class="grid md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-pink-500 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-2xl font-bold text-white">1</span>
                    </div>
                    <h3 class="text-lg font-semibold text-white mb-2">{{ __('muzibu::front.corporate.step_1_title') }}</h3>
                    <p class="text-gray-400 text-sm">{{ __('muzibu::front.corporate.step_1_desc') }}</p>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-pink-500 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-2xl font-bold text-white">2</span>
                    </div>
                    <h3 class="text-lg font-semibold text-white mb-2">{{ __('muzibu::front.corporate.step_2_title') }}</h3>
                    <p class="text-gray-400 text-sm">{{ __('muzibu::front.corporate.step_2_desc') }}</p>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-pink-500 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-2xl font-bold text-white">3</span>
                    </div>
                    <h3 class="text-lg font-semibold text-white mb-2">{{ __('muzibu::front.corporate.step_3_title') }}</h3>
                    <p class="text-gray-400 text-sm">{{ __('muzibu::front.corporate.step_3_desc') }}</p>
                </div>
            </div>
        </div>

        {{-- CTA Section --}}
        @guest
            {{-- Not Logged In --}}
            <div class="bg-gradient-to-br from-blue-500/10 to-cyan-500/10 border border-blue-500/30 rounded-2xl p-8 text-center max-w-2xl mx-auto">
                <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-user-lock text-white text-3xl"></i>
                </div>
                <h2 class="text-2xl font-bold text-white mb-3">Giriş Yapmanız Gerekiyor</h2>
                <p class="text-gray-400 mb-6">
                    Kurumsal hesap oluşturmak veya mevcut bir hesaba katılmak için önce giriş yapmalısınız.
                </p>
                <div class="flex gap-3 justify-center">
                    <a href="/login" class="px-6 py-3 bg-gradient-to-r from-blue-500 to-cyan-500 hover:from-blue-600 hover:to-cyan-600 text-white font-semibold rounded-xl transition">
                        <i class="fas fa-sign-in-alt mr-2"></i>Giriş Yap
                    </a>
                    <a href="/register" class="px-6 py-3 bg-white/10 hover:bg-white/20 text-white font-semibold rounded-xl transition">
                        <i class="fas fa-user-plus mr-2"></i>Kayıt Ol
                    </a>
                </div>
            </div>
        @else
            @if(!$userCorporate)
                <div class="grid md:grid-cols-2 gap-6">
                {{-- Join with Code --}}
                <div class="bg-gradient-to-br from-green-500/10 to-emerald-500/10 border border-green-500/30 rounded-2xl p-8">
                    <div class="text-center mb-6">
                        <div class="w-14 h-14 bg-gradient-to-br from-green-500 to-emerald-500 rounded-xl flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-sign-in-alt text-white text-xl"></i>
                        </div>
                        <h2 class="text-xl font-bold text-white mb-1">{{ __('muzibu::front.corporate.join_with_code') }}</h2>
                        <p class="text-gray-400 text-sm">{{ __('muzibu::front.corporate.enter_company_code') }}</p>
                    </div>
                    <form @submit.prevent="joinWithCode()" class="space-y-4">
                        <div>
                            <label class="block text-sm text-gray-400 mb-2">Davet Kodu</label>
                            <input type="text" x-model="code"
                                   placeholder="ABCD1234"
                                   maxlength="8"
                                   class="w-full px-4 py-3 bg-white/5 border border-white/20 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-green-500 uppercase tracking-widest font-mono text-xl text-center"
                                   @input="code = $event.target.value.toUpperCase().replace(/[^A-Z0-9]/g, '')">
                            <p class="text-xs text-gray-500 mt-2 text-center">
                                <span x-text="code.length"></span>/8 karakter
                            </p>
                        </div>
                        <button type="submit" :disabled="code.length !== 8 || loading"
                                class="w-full py-3 bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 disabled:from-gray-600 disabled:to-gray-700 disabled:cursor-not-allowed text-white font-semibold rounded-xl transition">
                            <span x-show="!loading"><i class="fas fa-arrow-right mr-2"></i>{{ __('muzibu::front.corporate.join') }}</span>
                            <span x-show="loading"><i class="fas fa-spinner fa-spin mr-2"></i>{{ __('muzibu::front.corporate.checking') }}</span>
                        </button>
                    </form>
                </div>

                {{-- Become Corporate --}}
                <div class="bg-gradient-to-br from-purple-500/10 to-pink-500/10 border border-purple-500/30 rounded-2xl p-8">
                    <div class="text-center mb-6">
                        <div class="w-14 h-14 bg-gradient-to-br from-purple-500 to-pink-500 rounded-xl flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-crown text-yellow-400 text-xl"></i>
                        </div>
                        <h2 class="text-xl font-bold text-white mb-1">{{ __('muzibu::front.corporate.create_corporate') }}</h2>
                        <p class="text-gray-400 text-sm">{{ __('muzibu::front.corporate.apply_for_company') }}</p>
                    </div>
                    <form @submit.prevent="createCorporate()" class="space-y-4">
                        <div>
                            <label class="block text-sm text-gray-400 mb-2">Şirket / İşletme Adı</label>
                            <input type="text" x-model="companyName"
                                   placeholder="Örnek Teknoloji A.Ş."
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
                                <i class="fas fa-info-circle mr-1"></i>Bu kod ile çalışanlarınız size katılabilir
                            </p>
                        </div>
                        <button type="submit" :disabled="companyName.length < 2 || !createCodeValid || creating"
                                class="w-full py-3 bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 disabled:from-gray-600 disabled:to-gray-700 disabled:cursor-not-allowed text-white font-semibold rounded-xl transition">
                            <span x-show="!creating"><i class="fas fa-crown mr-2"></i>{{ __('muzibu::front.corporate.create_corporate') }}</span>
                            <span x-show="creating"><i class="fas fa-spinner fa-spin mr-2"></i>Oluşturuluyor...</span>
                        </button>
                    </form>
                </div>
            </div>
            @endif
        @endguest

    </div>
</div>
@endsection

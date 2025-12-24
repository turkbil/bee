@extends('themes.muzibu.layouts.app')

@section('title', __('muzibu::front.corporate.title'))

@section('content')
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
        @if(!$userCorporate)
            <div class="grid md:grid-cols-2 gap-6">
                {{-- Join with Code --}}
                <div class="bg-gradient-to-br from-green-500/10 to-emerald-500/10 border border-green-500/30 rounded-2xl p-8">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-14 h-14 bg-green-500/20 rounded-xl flex items-center justify-center">
                            <i class="fas fa-link text-green-400 text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">{{ __('muzibu::front.corporate.join_with_code') }}</h3>
                            <p class="text-green-300 text-sm">{{ __('muzibu::front.corporate.enter_company_code') }}</p>
                        </div>
                    </div>
                    <form action="/corporate/join" method="POST" @submit.prevent="joinWithCode()" class="space-y-4">
                        @csrf
                        <div>
                            <input type="text" x-model="code" name="corporate_code"
                                   placeholder="{{ __('muzibu::front.corporate.code_placeholder') }}"
                                   maxlength="8"
                                   class="w-full px-4 py-3 bg-white/5 border border-white/20 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-green-500 uppercase tracking-widest font-mono text-lg text-center"
                                   @input="code = $event.target.value.toUpperCase()">
                        </div>
                        <button type="submit" :disabled="code.length !== 8 || loading"
                                class="w-full py-3 bg-green-500 hover:bg-green-600 disabled:bg-gray-600 disabled:cursor-not-allowed text-white font-semibold rounded-xl transition">
                            <span x-show="!loading"><i class="fas fa-arrow-right mr-2"></i>{{ __('muzibu::front.corporate.join') }}</span>
                            <span x-show="loading"><i class="fas fa-spinner fa-spin mr-2"></i>{{ __('muzibu::front.corporate.checking') }}</span>
                        </button>
                    </form>
                </div>

                {{-- Become Corporate --}}
                <div class="bg-gradient-to-br from-purple-500/10 to-pink-500/10 border border-purple-500/30 rounded-2xl p-8">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-14 h-14 bg-purple-500/20 rounded-xl flex items-center justify-center">
                            <i class="fas fa-building text-purple-400 text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">{{ __('muzibu::front.corporate.create_corporate') }}</h3>
                            <p class="text-purple-300 text-sm">{{ __('muzibu::front.corporate.apply_for_company') }}</p>
                        </div>
                    </div>
                    <p class="text-gray-400 mb-6">
                        {{ __('muzibu::front.corporate.create_corporate_desc') }}
                    </p>
                    <a href="mailto:kurumsal@muzibu.com.tr" class="block w-full py-3 bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white font-semibold rounded-xl text-center transition">
                        <i class="fas fa-envelope mr-2"></i>{{ __('muzibu::front.corporate.contact_us') }}
                    </a>
                </div>
            </div>
        @endif

    </div>
</div>

@push('scripts')
<script>
function corporateIndexApp() {
    return {
        code: '',
        loading: false,

        async joinWithCode() {
            if (this.code.length !== 8) return;

            this.loading = true;

            try {
                const response = await fetch('/corporate/join', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ corporate_code: this.code })
                });

                const data = await response.json();

                if (data.success) {
                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: { message: data.message, type: 'success' }
                    }));
                    setTimeout(() => {
                        window.location.href = data.redirect || '/corporate/my-corporate';
                    }, 1000);
                } else {
                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: { message: data.message, type: 'error' }
                    }));
                }
            } catch (error) {
                window.dispatchEvent(new CustomEvent('toast', {
                    detail: { message: '{{ __('muzibu::front.corporate.error_occurred') }}', type: 'error' }
                }));
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>
@endpush
@endsection

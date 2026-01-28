@extends('themes.muzibu.layouts.app')

@section('title', 'KVKK Onayları - Muzibu')

@push('scripts')
<script>
    function kvkkPageModal() {
        return {
            showPageModal: false,
            pageModalTitle: '',
            pageModalContent: '',
            loadingPageContent: false,

            async openPageModal(pageId) {
                this.showPageModal = true;
                this.pageModalTitle = 'Yükleniyor...';
                this.pageModalContent = '';
                this.loadingPageContent = true;

                try {
                    const response = await fetch(`/api/page-content/${pageId}`);
                    const data = await response.json();

                    if (data.success && data.data) {
                        this.pageModalTitle = data.data.title || 'Sayfa';
                        this.pageModalContent = data.data.body || '<p class="text-gray-300">İçerik bulunamadı.</p>';
                    } else {
                        this.pageModalTitle = 'Hata';
                        this.pageModalContent = '<p class="text-red-400">İçerik yüklenirken hata oluştu.</p>';
                    }
                } catch (error) {
                    console.error('Page content load error:', error);
                    this.pageModalTitle = 'Hata';
                    this.pageModalContent = '<p class="text-red-400">İçerik yüklenirken hata oluştu.</p>';
                } finally {
                    this.loadingPageContent = false;
                }
            },

            closePageModal() {
                this.showPageModal = false;
                this.pageModalTitle = '';
                this.pageModalContent = '';
            }
        }
    }
</script>
@endpush

@section('content')
<div class="max-w-7xl mx-auto p-6" x-data="kvkkPageModal()">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        {{-- Left Sidebar --}}
        <div class="lg:col-span-1">
            @include('themes.muzibu.components.profile-sidebar', ['active' => 'consents'])
        </div>

        {{-- Main Content --}}
        <div class="lg:col-span-3 space-y-6">
            {{-- Header --}}
            <div class="mb-4">
                <h1 class="text-3xl font-bold text-white mb-2">
                    <i class="fas fa-shield-check text-green-400 mr-2"></i>
                    KVKK Onayları
                </h1>
                <p class="text-muzibu-text-gray">Kişisel verilerinizle ilgili onaylarınızı yönetin</p>
            </div>

            {{-- Success Message --}}
            @if (session('status') === 'consents-updated')
                <div class="bg-green-500/20 border border-green-500/50 text-green-400 px-6 py-4 rounded-lg mb-6">
                    ✅ KVKK onaylarınız başarıyla güncellendi!
                </div>
            @endif

            {{-- Current Consents Info --}}
            <div class="bg-white/5 backdrop-blur-sm rounded-lg p-6 border border-white/10">
                <h3 class="text-white font-semibold mb-4">
                    <i class="fas fa-info-circle text-muzibu-coral mr-2"></i>
                    Mevcut Onaylarınız
                </h3>
                <div class="space-y-4">
                    {{-- Terms Accepted --}}
                    <div class="flex items-center justify-between p-4 bg-white/5 rounded-lg border border-white/10">
                        <div class="flex-1">
                            <h4 class="text-white text-sm font-medium mb-1">Kullanım Koşulları ve Üyelik Sözleşmesi</h4>
                            @if($user->terms_accepted && $user->terms_accepted_at)
                                <p class="text-muzibu-text-gray text-xs">
                                    <i class="far fa-calendar-check mr-1"></i>
                                    Onay Tarihi: {{ $user->terms_accepted_at->format('d.m.Y H:i') }}
                                </p>
                            @endif
                        </div>
                        <div>
                            @if($user->terms_accepted)
                                <span class="px-3 py-1 bg-green-500/20 text-green-400 text-xs font-medium rounded-full">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    Onaylandı
                                </span>
                            @else
                                <span class="px-3 py-1 bg-red-500/20 text-red-400 text-xs font-medium rounded-full">
                                    <i class="fas fa-times-circle mr-1"></i>
                                    Onaylanmadı
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Privacy Accepted --}}
                    <div class="flex items-center justify-between p-4 bg-white/5 rounded-lg border border-white/10">
                        <div class="flex-1">
                            <h4 class="text-white text-sm font-medium mb-1">Üyelik ve Satın Alım Aydınlatma Metni</h4>
                            @if($user->privacy_accepted && $user->privacy_accepted_at)
                                <p class="text-muzibu-text-gray text-xs">
                                    <i class="far fa-calendar-check mr-1"></i>
                                    Onay Tarihi: {{ $user->privacy_accepted_at->format('d.m.Y H:i') }}
                                </p>
                            @endif
                        </div>
                        <div>
                            @if($user->privacy_accepted)
                                <span class="px-3 py-1 bg-green-500/20 text-green-400 text-xs font-medium rounded-full">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    Onaylandı
                                </span>
                            @else
                                <span class="px-3 py-1 bg-red-500/20 text-red-400 text-xs font-medium rounded-full">
                                    <i class="fas fa-times-circle mr-1"></i>
                                    Onaylanmadı
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Info Box --}}
                <div class="mt-6 p-4 bg-yellow-500/10 border border-yellow-500/30 rounded-lg">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-exclamation-triangle text-yellow-400 mt-0.5 flex-shrink-0"></i>
                        <div>
                            <p class="text-yellow-200 text-sm font-medium">Önemli Bilgi</p>
                            <p class="text-yellow-300 text-sm mt-1">Kullanım Koşulları ve Aydınlatma Metni onayları zorunludur ve değiştirilemez. Sadece pazarlama onayınızı güncelleyebilirsiniz.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Marketing Consent Form --}}
            <div class="bg-white/5 backdrop-blur-sm rounded-lg p-6 border border-white/10">
                <h3 class="text-white font-semibold mb-4">
                    <i class="fas fa-envelope text-muzibu-coral mr-2"></i>
                    Ticari Elektronik İleti Gönderimi
                </h3>

                <form method="POST" action="{{ route('profile.consents.update') }}" class="space-y-6">
                    @csrf

                    <div class="space-y-4">
                        {{-- Radio 1: Rıza Göstermiyorum --}}
                        <label class="flex items-start p-4 bg-white/5 rounded-lg border border-white/10 cursor-pointer hover:bg-white/10 transition-colors">
                            <input
                                type="radio"
                                name="marketing_accepted"
                                value="0"
                                {{ !$user->marketing_accepted ? 'checked' : '' }}
                                class="mt-1 w-4 h-4 text-muzibu-coral bg-white/10 border-white/20 focus:ring-muzibu-coral focus:ring-offset-muzibu-dark-bg flex-shrink-0"
                            >
                            <span class="ml-3 text-sm text-muzibu-text-gray leading-relaxed">
                                <a href="javascript:void(0)" @click.prevent="openPageModal(5)" class="text-muzibu-coral hover:text-white underline">Ticari Elektronik İleti Gönderimi Aydınlatma Metni</a>'nde belirtilen hususlar doğrultusunda, kişisel verilerimin ürün/hizmet ve stratejik pazarlama faaliyetleri ile ticari elektronik ileti gönderimi kapsamında işlenmesine <strong class="text-white">açık rıza göstermediğimi beyan ederim</strong>.
                            </span>
                        </label>

                        {{-- Radio 2: Rıza Veriyorum --}}
                        <label class="flex items-start p-4 bg-white/5 rounded-lg border border-white/10 cursor-pointer hover:bg-white/10 transition-colors">
                            <input
                                type="radio"
                                name="marketing_accepted"
                                value="1"
                                {{ $user->marketing_accepted ? 'checked' : '' }}
                                class="mt-1 w-4 h-4 text-muzibu-coral bg-white/10 border-white/20 focus:ring-muzibu-coral focus:ring-offset-muzibu-dark-bg flex-shrink-0"
                            >
                            <span class="ml-3 text-sm text-muzibu-text-gray leading-relaxed">
                                <a href="javascript:void(0)" @click.prevent="openPageModal(5)" class="text-muzibu-coral hover:text-white underline">Ticari Elektronik İleti Gönderimi Aydınlatma Metni</a>'nde belirtilen hususlar doğrultusunda, kişisel verilerimin ürün/hizmet ve stratejik pazarlama faaliyetleri ile ticari elektronik ileti gönderimi kapsamında işlenmesine <strong class="text-white">açık rıza verdiğimi kabul ve beyan ederim</strong>.
                            </span>
                        </label>
                    </div>

                    @if($user->marketing_accepted_at)
                        <p class="text-muzibu-text-gray text-xs">
                            <i class="far fa-clock mr-1"></i>
                            Son Güncelleme: {{ $user->marketing_accepted_at->format('d.m.Y H:i') }}
                        </p>
                    @endif

                    {{-- Submit Button --}}
                    <div class="flex justify-end">
                        <button
                            type="submit"
                            class="px-6 py-3 bg-gradient-to-r from-muzibu-coral to-[#ff9966] hover:from-[#ff9966] hover:to-muzibu-coral rounded-lg text-white font-semibold transition-all duration-300 shadow-lg hover:shadow-muzibu-coral/30"
                        >
                            <i class="fas fa-check mr-2"></i>
                            Onayları Güncelle
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Page Content Modal - İletişim Sayfası Stili --}}
    <div x-show="showPageModal"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm"
         @click.self="closePageModal()"
         style="display: none;"
         x-cloak>

        {{-- Modal Content --}}
        <div x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="relative w-full max-w-4xl rounded-2xl shadow-2xl flex flex-col"
             style="background-color: #1f2937; border: 1px solid #4b5563; max-height: calc(70vh - 20px);"
             @click.stop>

            {{-- Modal Header --}}
            <div class="flex-shrink-0 flex items-center justify-between px-8 py-6" style="background: linear-gradient(to right, #FF6B47, #FF8C6B); border-radius: 1rem 1rem 0 0;">
                <h3 class="text-2xl font-black text-white pr-8" x-text="pageModalTitle"></h3>
                <button @click="closePageModal()"
                        class="flex-shrink-0 w-10 h-10 flex items-center justify-center rounded-full transition-all duration-200 hover:scale-110"
                        style="background-color: rgba(255, 255, 255, 0.2); color: white;"
                        onmouseover="this.style.backgroundColor='rgba(255, 255, 255, 0.3)'"
                        onmouseout="this.style.backgroundColor='rgba(255, 255, 255, 0.2)'">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>

            {{-- Modal Body --}}
            <div class="flex-1 overflow-y-auto px-8 pt-6 pb-20" style="max-height: calc(70vh - 180px);">
                <div x-show="loadingPageContent" class="flex items-center justify-center py-12">
                    <i class="fa-solid fa-spinner fa-spin text-4xl" style="color: #FF6B47;"></i>
                </div>
                <div x-show="!loadingPageContent"
                     class="text-white prose prose-lg max-w-none dark:prose-invert
                          prose-headings:text-white
                          prose-p:text-white
                          prose-strong:text-white
                          prose-ul:text-white
                          prose-ol:text-white
                          prose-li:text-white"
                     style="color: white;"
                     x-html="pageModalContent">
                </div>

                <style>
                    .prose a { color: #FF9580 !important; }
                    .prose a:hover { color: #FFB09F !important; }
                </style>
            </div>

            {{-- Modal Footer --}}
            <div class="flex-shrink-0 flex items-center justify-end px-8 py-4" style="background: rgba(17, 24, 39, 0.5); border-top: 1px solid rgb(55, 65, 81);">
                <button @click="closePageModal()"
                        class="px-6 py-3 rounded-xl font-bold text-white transition-all duration-200 hover:scale-105"
                        style="background: linear-gradient(to right, #FF6B47, #FF8C6B); box-shadow: 0 10px 15px -3px rgba(255, 107, 71, 0.3);"
                        onmouseover="this.style.boxShadow='0 20px 25px -5px rgba(255, 107, 71, 0.3)'"
                        onmouseout="this.style.boxShadow='0 10px 15px -3px rgba(255, 107, 71, 0.3)'">
                    <i class="fa-solid fa-check mr-2"></i>
                    Anladım
                </button>
            </div>

        </div>
    </div>
</div>
@endsection

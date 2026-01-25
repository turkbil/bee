@php
    $themeService = app(\App\Services\ThemeService::class);
    $activeTheme = $themeService->getActiveTheme();
    $themeName = $activeTheme ? $activeTheme->name : 'simple';
@endphp
@extends('themes.' . $themeName . '.layouts.app')

@section('title', 'Yasal Metinler - ' . setting('site_title', 'Muzibu'))

@section('content')
<div class="min-h-screen bg-gradient-to-br from-dark-900 via-dark-800 to-dark-900 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Page Header -->
        <div class="text-center mb-16">
            <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">
                Yasal Metinler
            </h1>
            <p class="text-dark-200 text-lg">
                Gizlilik, güvenlik ve yasal yükümlülüklerimiz hakkında bilgi edinin
            </p>
        </div>

        <!-- Legal Documents Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">

            <!-- 1. Çerez Bilgilendirme Metni ve Gizlilik -->
            <a href="/page/cerez-bilgilendirme-metni-ve-gizlilik" class="group relative bg-dark-800/50 border-2 border-mz-500/30 rounded-2xl p-8 hover:border-mz-500 hover:bg-dark-800 transition-all duration-300 hover:shadow-xl hover:shadow-mz-500/20 hover:scale-105">
                <div class="flex flex-col items-center text-center space-y-4">
                    <div class="w-16 h-16 bg-mz-500/10 rounded-xl flex items-center justify-center group-hover:bg-mz-500/20 transition-colors">
                        <i class="fas fa-cookie-bite text-3xl text-mz-400"></i>
                    </div>
                    <h3 class="text-white font-bold text-lg leading-tight">
                        ÇEREZ BİLGİLENDİRME METNİ ve GİZLİLİK
                    </h3>
                </div>
            </a>

            <!-- 2. İletişim Formu Aydınlatma Metni -->
            <a href="/page/iletisim-formu-kapsaminda-islenen-kisisel-verilere-iliskin-aydinlatma-metni" class="group relative bg-dark-800/50 border-2 border-mz-500/30 rounded-2xl p-8 hover:border-mz-500 hover:bg-dark-800 transition-all duration-300 hover:shadow-xl hover:shadow-mz-500/20 hover:scale-105">
                <div class="flex flex-col items-center text-center space-y-4">
                    <div class="w-16 h-16 bg-mz-500/10 rounded-xl flex items-center justify-center group-hover:bg-mz-500/20 transition-colors">
                        <i class="fas fa-envelope text-3xl text-mz-400"></i>
                    </div>
                    <h3 class="text-white font-bold text-lg leading-tight">
                        İLETİŞİM FORMU AYDINLATMA METNİ
                    </h3>
                </div>
            </a>

            <!-- 3. Kullanım Koşulları ve Üyelik Sözleşmesi -->
            <a href="/page/kullanim-kosullari-ve-uyelik-sozlesmesi" class="group relative bg-dark-800/50 border-2 border-mz-500/30 rounded-2xl p-8 hover:border-mz-500 hover:bg-dark-800 transition-all duration-300 hover:shadow-xl hover:shadow-mz-500/20 hover:scale-105">
                <div class="flex flex-col items-center text-center space-y-4">
                    <div class="w-16 h-16 bg-mz-500/10 rounded-xl flex items-center justify-center group-hover:bg-mz-500/20 transition-colors">
                        <i class="fas fa-file-alt text-3xl text-mz-400"></i>
                    </div>
                    <h3 class="text-white font-bold text-lg leading-tight">
                        KULLANIM KOŞULLARI VE ÜYELİK SÖZLEŞMESİ
                    </h3>
                </div>
            </a>

            <!-- 4. Mesafeli Satış Sözleşmesi -->
            <a href="/page/mesafeli-satis-sozlesmesi" class="group relative bg-dark-800/50 border-2 border-mz-500/30 rounded-2xl p-8 hover:border-mz-500 hover:bg-dark-800 transition-all duration-300 hover:shadow-xl hover:shadow-mz-500/20 hover:scale-105">
                <div class="flex flex-col items-center text-center space-y-4">
                    <div class="w-16 h-16 bg-mz-500/10 rounded-xl flex items-center justify-center group-hover:bg-mz-500/20 transition-colors">
                        <i class="fas fa-file-contract text-3xl text-mz-400"></i>
                    </div>
                    <h3 class="text-white font-bold text-lg leading-tight">
                        MESAFELİ SATIŞ SÖZLEŞMESİ
                    </h3>
                </div>
            </a>

            <!-- 5. Ticari Uzaktan Hizmet ve Abonelik Sözleşmesi -->
            <a href="/page/ticari-uzaktan-hizmet-ve-abonelik-sozlesmesi" class="group relative bg-dark-800/50 border-2 border-mz-500/30 rounded-2xl p-8 hover:border-mz-500 hover:bg-dark-800 transition-all duration-300 hover:shadow-xl hover:shadow-mz-500/20 hover:scale-105">
                <div class="flex flex-col items-center text-center space-y-4">
                    <div class="w-16 h-16 bg-mz-500/10 rounded-xl flex items-center justify-center group-hover:bg-mz-500/20 transition-colors">
                        <i class="fas fa-briefcase text-3xl text-mz-400"></i>
                    </div>
                    <h3 class="text-white font-bold text-lg leading-tight">
                        TİCARİ UZAKTAN HİZMET VE ABONELİK SÖZLEŞMESİ
                    </h3>
                </div>
            </a>

            <!-- 6. Ön Bilgilendirme Formu -->
            <a href="/page/on-bilgilendirme-formu" class="group relative bg-dark-800/50 border-2 border-mz-500/30 rounded-2xl p-8 hover:border-mz-500 hover:bg-dark-800 transition-all duration-300 hover:shadow-xl hover:shadow-mz-500/20 hover:scale-105">
                <div class="flex flex-col items-center text-center space-y-4">
                    <div class="w-16 h-16 bg-mz-500/10 rounded-xl flex items-center justify-center group-hover:bg-mz-500/20 transition-colors">
                        <i class="fas fa-file-invoice text-3xl text-mz-400"></i>
                    </div>
                    <h3 class="text-white font-bold text-lg leading-tight">
                        ÖN BİLGİLENDİRME FORMU
                    </h3>
                </div>
            </a>

            <!-- 7. KVKK İlgili Kişi Başvuru Formu -->
            <a href="/page/kvkk-ilgili-kisi-basvuru-formu" class="group relative bg-dark-800/50 border-2 border-mz-500/30 rounded-2xl p-8 hover:border-mz-500 hover:bg-dark-800 transition-all duration-300 hover:shadow-xl hover:shadow-mz-500/20 hover:scale-105">
                <div class="flex flex-col items-center text-center space-y-4">
                    <div class="w-16 h-16 bg-mz-500/10 rounded-xl flex items-center justify-center group-hover:bg-mz-500/20 transition-colors">
                        <i class="fas fa-download text-3xl text-mz-400"></i>
                    </div>
                    <h3 class="text-white font-bold text-lg leading-tight">
                        KVKK İLGİLİ KİŞİ BAŞVURU FORMU
                    </h3>
                </div>
            </a>

            <!-- 8. Ticari Elektronik İleti Hakkında Aydınlatma Metni -->
            <a href="/page/ticari-elektronik-ileti-gonderimi-sureclerine-iliskin-kisisel-verilerin-islenmesi-ve-korunmasi-hakkinda-aydinlatma-metni" class="group relative bg-dark-800/50 border-2 border-mz-500/30 rounded-2xl p-8 hover:border-mz-500 hover:bg-dark-800 transition-all duration-300 hover:shadow-xl hover:shadow-mz-500/20 hover:scale-105">
                <div class="flex flex-col items-center text-center space-y-4">
                    <div class="w-16 h-16 bg-mz-500/10 rounded-xl flex items-center justify-center group-hover:bg-mz-500/20 transition-colors">
                        <i class="fas fa-user-shield text-3xl text-mz-400"></i>
                    </div>
                    <h3 class="text-white font-bold text-lg leading-tight">
                        TİCARİ ELEKTRONİK İLETİ HAKKINDA AYDINLATMA METNİ
                    </h3>
                </div>
            </a>

            <!-- 9. Üyelik ve Satın Alım Faaliyetleri -->
            <a href="/page/uyelik-ve-satin-alim-faaliyetleri-kapsaminda-kisisel-verilerin-islenmesi-ve-korunmasina-iliskin-aydinlatma-metni" class="group relative bg-dark-800/50 border-2 border-mz-500/30 rounded-2xl p-8 hover:border-mz-500 hover:bg-dark-800 transition-all duration-300 hover:shadow-xl hover:shadow-mz-500/20 hover:scale-105">
                <div class="flex flex-col items-center text-center space-y-4">
                    <div class="w-16 h-16 bg-mz-500/10 rounded-xl flex items-center justify-center group-hover:bg-mz-500/20 transition-colors">
                        <i class="fas fa-credit-card text-3xl text-mz-400"></i>
                    </div>
                    <h3 class="text-white font-bold text-lg leading-tight">
                        ÜYELİK ve SATIN ALIM FAALİYETLERİ KAPSAMINDA KİŞİSEL VERİLERİN İŞLENMESİ ve KORUNMASINA İLİŞKİN AYDINLATMA METNİ
                    </h3>
                </div>
            </a>

            <!-- 10. WhatsApp Destek Hattı -->
            <a href="/page/whatsapp-destek-hatti-hakkinda-kvkk-aydinlatma-metni" class="group relative bg-dark-800/50 border-2 border-mz-500/30 rounded-2xl p-8 hover:border-mz-500 hover:bg-dark-800 transition-all duration-300 hover:shadow-xl hover:shadow-mz-500/20 hover:scale-105">
                <div class="flex flex-col items-center text-center space-y-4">
                    <div class="w-16 h-16 bg-mz-500/10 rounded-xl flex items-center justify-center group-hover:bg-mz-500/20 transition-colors">
                        <i class="fab fa-whatsapp text-3xl text-mz-400"></i>
                    </div>
                    <h3 class="text-white font-bold text-lg leading-tight">
                        WHATSAPP DESTEK HATTI HAKKINDA KVKK AYDINLATMA METNİ
                    </h3>
                </div>
            </a>

        </div>

        <!-- Back to Home -->
        <div class="mt-12 text-center">
            <a href="/" class="inline-flex items-center gap-2 text-dark-200 hover:text-white transition-colors">
                <i class="fas fa-arrow-left"></i>
                Ana Sayfaya Dön
            </a>
        </div>

    </div>
</div>
@endsection

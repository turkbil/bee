@extends('themes.muzibu.layouts.app')

@section('title', 'Geçersiz Belge - Muzibu')

@section('content')
<div class="min-h-screen py-10 sm:py-16">
    <div class="max-w-2xl mx-auto px-4">

        {{-- Başlık --}}
        <div class="text-center mb-8">
            <div class="w-24 h-24 mx-auto mb-5 rounded-full bg-gradient-to-br from-red-500 to-red-600 flex items-center justify-center shadow-2xl shadow-red-500/40">
                <i class="fas fa-times text-white text-4xl"></i>
            </div>
            <h1 class="text-3xl sm:text-4xl font-bold text-white mb-2">Geçersiz Belge</h1>
            <p class="text-red-400 font-medium">Bu belge doğrulanamadı</p>
        </div>

        {{-- Ana Kart --}}
        <div class="bg-slate-900 border border-slate-700 rounded-2xl shadow-2xl overflow-hidden">

            {{-- Açıklama --}}
            <div class="p-6 sm:p-8 border-b border-slate-700 text-center">
                <p class="text-slate-300 text-lg">
                    Taranan QR kod veya girilen bağlantı geçerli bir üyelik belgesine ait değildir.
                </p>
            </div>

            {{-- Olası Nedenler --}}
            <div class="p-6 sm:p-8 bg-slate-800/50">
                <div class="text-xs text-slate-500 uppercase tracking-wider mb-4">Olası Nedenler</div>
                <table class="w-full text-sm">
                    <tr>
                        <td class="text-slate-500 py-2 pr-4 w-8 align-top">1.</td>
                        <td class="text-slate-300 py-2">Bağlantı adresi eksik veya hatalı kopyalanmış olabilir</td>
                    </tr>
                    <tr>
                        <td class="text-slate-500 py-2 pr-4 align-top">2.</td>
                        <td class="text-slate-300 py-2">QR kod fiziksel hasar nedeniyle okunamıyor olabilir</td>
                    </tr>
                    <tr>
                        <td class="text-slate-500 py-2 pr-4 align-top">3.</td>
                        <td class="text-slate-300 py-2">Belge iptal edilmiş veya geçersiz kılınmış olabilir</td>
                    </tr>
                    <tr>
                        <td class="text-slate-500 py-2 pr-4 align-top">4.</td>
                        <td class="text-slate-300 py-2">Sahte veya taklit bir belge olabilir</td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- Uyarı --}}
        <div class="mt-4 bg-red-500/10 border border-red-500/30 rounded-xl p-5">
            <div class="flex items-start gap-3">
                <div class="flex-shrink-0 w-5 h-5 rounded-full bg-red-500/20 flex items-center justify-center mt-0.5">
                    <i class="fas fa-exclamation text-red-400 text-xs"></i>
                </div>
                <div class="text-sm text-slate-400">
                    <p>
                        <strong class="text-red-400">Dikkat:</strong>
                        Bu belgenin geçersiz olması, ilgili kişi veya kuruluşun Muzibu Premium üyeliğinin
                        bulunmadığı veya sona erdiği anlamına gelebilir. Şüpheli durumlarda belge sahibinden
                        güncel bir belge talep ediniz.
                    </p>
                </div>
            </div>
        </div>

        {{-- İletişim --}}
        <div class="mt-6 p-5 bg-slate-900/30 border border-slate-800/50 rounded-xl">
            <div class="text-xs text-slate-500 uppercase tracking-wider mb-3">İletişim</div>
            <p class="text-sm text-slate-400">
                Belge doğrulama ile ilgili sorularınız için
                <a href="mailto:destek@muzibu.com.tr" class="text-amber-400 hover:underline font-medium">destek@muzibu.com.tr</a>
                adresinden bizimle iletişime geçebilirsiniz.
            </p>
        </div>

        {{-- Alt Bilgi --}}
        <div class="mt-8 text-center text-xs text-slate-600 space-y-1">
            <p>Bu doğrulama Muzibu tarafından elektronik ortamda gerçekleştirilmiştir.</p>
            <p>{{ now()->format('d.m.Y H:i') }}</p>
        </div>

    </div>
</div>
@endsection

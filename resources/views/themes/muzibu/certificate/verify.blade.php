@extends('themes.muzibu.layouts.app')

@section('title', 'Belge Doğrulama - Muzibu')

@php
    $hasGap = false;
    $previousEnd = null;
    foreach ($subscriptionPeriods as $period) {
        if ($previousEnd && $period['start'] && $previousEnd->diffInDays($period['start']) > 7) {
            $hasGap = true;
            break;
        }
        $previousEnd = $period['end'];
    }
    $firstStart = collect($subscriptionPeriods)->first()['start'] ?? null;
    $lastEnd = collect($subscriptionPeriods)->last()['end'] ?? null;
    $currentStart = $hasGap ? collect($subscriptionPeriods)->last()['start'] : $firstStart;
@endphp

@section('content')
<style>
    .cert-mono { font-family: 'Roboto Mono', monospace; }
    .gold-text { color: #d4af37 !important; }
    .gold-border { border-color: #d4af37 !important; }
    .gold-bg { background-color: rgba(212, 175, 55, 0.1) !important; }
</style>
<link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
<div class="min-h-screen py-6 sm:py-10">
    <div class="max-w-2xl mx-auto px-4">

        {{-- Doğrulanmış Belge Header --}}
        @if($isCurrentlyActive)
        <div class="bg-gradient-to-r from-green-600 to-emerald-600 rounded-xl p-5 mb-4 shadow-lg shadow-green-900/30">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-certificate text-white text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-white">Doğrulanmış Belge</h1>
                        <p class="text-green-100 text-sm">QR kod ile resmi belge doğrulaması yapıldı</p>
                    </div>
                </div>
                <div class="hidden sm:flex items-center gap-2 px-4 py-2 bg-white/20 rounded-lg">
                    <i class="fas fa-shield-alt text-white"></i>
                    <span class="text-white text-sm font-medium">GÜVENLİ</span>
                </div>
            </div>
        </div>
        @else
        <div class="bg-gradient-to-r from-amber-600 to-orange-600 rounded-xl p-5 mb-4 shadow-lg shadow-orange-900/30">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-certificate text-white text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-white">Üyelik Pasif</h1>
                        <p class="text-amber-100 text-sm">Bu belgenın üyeliği şu anda aktif değil</p>
                    </div>
                </div>
                <div class="hidden sm:flex items-center gap-2 px-4 py-2 bg-white/20 rounded-lg">
                    <i class="fas fa-pause text-white"></i>
                    <span class="text-white text-sm font-medium">PASİF</span>
                </div>
            </div>
        </div>
        @endif

        {{-- Ana Belge --}}
        <div class="relative bg-gradient-to-b from-slate-900 via-slate-900 to-slate-950 border gold-border rounded-lg shadow-2xl overflow-hidden">

            {{-- Dekoratif Köşeler --}}
            <div class="absolute top-0 left-0 w-16 h-16 border-t-2 border-l-2 gold-border rounded-tl-lg"></div>
            <div class="absolute top-0 right-0 w-16 h-16 border-t-2 border-r-2 gold-border rounded-tr-lg"></div>
            <div class="absolute bottom-0 left-0 w-16 h-16 border-b-2 border-l-2 gold-border rounded-bl-lg"></div>
            <div class="absolute bottom-0 right-0 w-16 h-16 border-b-2 border-r-2 gold-border rounded-br-lg"></div>

            {{-- Header --}}
            <div class="px-8 pt-8 pb-8">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <h1 class="text-4xl font-semibold text-white mb-2 tracking-wide">Üyelik Belgesı</h1>
                        <p class="text-slate-400 text-sm">Muzibu Kurumsal Premium Üyelik</p>
                    </div>
                    {{-- Crown Badge --}}
                    <div class="w-16 h-16 rounded-full gold-bg border gold-border flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-crown gold-text text-xl"></i>
                    </div>
                </div>
            </div>

            {{-- Ayırıcı --}}
            <div class="mx-8 h-px bg-gradient-to-r from-transparent via-amber-600/50 to-transparent"></div>

            {{-- Firma Bilgisi --}}
            <div class="px-8 py-8">
                <h2 class="text-3xl cert-mono font-semibold text-white tracking-wide text-center">{{ $certificate->member_name }}</h2>
            </div>

            {{-- Bilgiler --}}
            <div class="mx-8 mb-8 bg-slate-800/50 border border-slate-700 rounded-lg overflow-hidden">
                <div class="grid grid-cols-2 divide-x divide-slate-700">
                    <div class="p-4 text-center">
                        <div class="text-xs text-slate-500 mb-1">SERTİFİKA NO</div>
                        <div class="cert-mono gold-text font-semibold text-lg">{{ $certificate->certificate_code }}</div>
                    </div>
                    <div class="p-4 text-center">
                        <div class="text-xs text-slate-500 mb-1">ÜYELİK TARİHİ</div>
                        <div class="text-white text-lg">{{ $currentStart?->format('d.m.Y') ?? '-' }}</div>
                    </div>
                </div>
                @if($certificate->tax_office || $certificate->tax_number)
                <div class="border-t border-slate-700 grid grid-cols-2 divide-x divide-slate-700">
                    @if($certificate->tax_office)
                    <div class="p-4 text-center">
                        <div class="text-xs text-slate-500 mb-1">VERGİ DAİRESİ</div>
                        <div class="cert-mono text-white">{{ $certificate->tax_office }}</div>
                    </div>
                    @endif
                    @if($certificate->tax_number)
                    <div class="p-4 text-center">
                        <div class="text-xs text-slate-500 mb-1">VERGİ NO</div>
                        <div class="cert-mono text-white tracking-wider">{{ $certificate->tax_number }}</div>
                    </div>
                    @endif
                </div>
                @endif
            </div>

            {{-- Adres --}}
            @if($certificate->address)
            <div class="mx-8 mb-6">
                <div class="text-xs text-slate-500 mb-2 tracking-widest">KAYITLI ADRES</div>
                <div class="p-4 bg-slate-800/30 border border-slate-700 rounded-lg">
                    <p class="text-slate-300 leading-relaxed">{{ $certificate->address }}</p>
                </div>
            </div>
            @endif

            {{-- Dijital İmza --}}
            <div class="mx-8 mb-6 p-4 bg-blue-900/20 border border-blue-800 rounded-lg">
                <div class="flex items-center gap-3">
                    <i class="fas fa-fingerprint text-blue-400 text-2xl"></i>
                    <div class="flex-1">
                        <div class="text-blue-400 text-sm font-semibold">Benzersiz Dijital İmza</div>
                        <div class="text-xs text-slate-500">Bu belge şifrelenmiş ve değiştirilemez</div>
                    </div>
                    <div class="text-xs text-slate-500">
                        <i class="fas fa-lock mr-1"></i>256-bit
                    </div>
                </div>
            </div>

        </div>

        {{-- Üyelik Geçmişi - Minimal --}}
        @if(count($subscriptionPeriods) > 0)
            @if($hasGap)
                {{-- Birden fazla dönem varsa --}}
                <div class="mt-4 space-y-2">
                    @foreach($subscriptionPeriods as $i => $period)
                    <div class="flex items-center justify-between px-4 py-3 bg-slate-900/50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-{{ $period['is_active'] ? 'check-circle text-green-400' : 'circle text-slate-500' }}"></i>
                            <div>
                                <span class="text-slate-400 text-sm">{{ $period['is_active'] ? 'Aktif Dönem' : ($i + 1) . '. Dönem' }}</span>
                                <span class="text-slate-500 text-sm mx-2">·</span>
                                <span class="text-white text-sm">{{ $period['start']?->format('d.m.Y') ?? '-' }} — {{ $period['end']?->format('d.m.Y') ?? 'Devam Ediyor' }}</span>
                            </div>
                        </div>
                        @if($period['is_active'])
                        <span class="text-green-400 text-xs font-medium px-2 py-1 bg-green-900/30 rounded">AKTİF</span>
                        @endif
                    </div>
                    @endforeach
                </div>
            @else
                {{-- Kesintisiz üyelik --}}
                <div class="mt-4 flex items-center justify-between px-4 py-3 bg-slate-900/50 rounded-lg">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-check-circle text-green-400"></i>
                        <div>
                            <span class="text-slate-400 text-sm">Kesintisiz Üyelik</span>
                            <span class="text-slate-500 text-sm mx-2">·</span>
                            <span class="text-white text-sm">{{ $firstStart?->format('d.m.Y') ?? '-' }} — {{ $lastEnd?->format('d.m.Y') ?? 'Devam Ediyor' }}</span>
                        </div>
                    </div>
                    @if($isCurrentlyActive)
                    <span class="text-green-400 text-xs font-medium px-2 py-1 bg-green-900/30 rounded">AKTİF</span>
                    @endif
                </div>
            @endif
        @endif

        {{-- Ticari Müzik Yayın Lisansı --}}
        <div class="mt-4 bg-slate-900/50 rounded-lg p-6">
            <div class="text-xs text-slate-500 tracking-widest mb-4">MUZİBU TİCARİ MÜZİK YAYIN LİSANSI</div>
            <div class="space-y-3 text-sm text-slate-400 leading-relaxed">
                <p>Muzibu, yalnızca kendisinin ürettiği ve tüm yayın hakları tamamen kendisine ait olan içerikleri kullanarak ticari işletmelere müzik yayın hizmeti sunar. Bu lisans, Muzibu altyapısı üzerinden sağlanan müzik yayınlarının herhangi bir üçüncü taraf telif kuruluşundan ek bir lisans gerektirmediğini beyan eder.</p>
                <p>Bu lisans kapsamında sunulan tüm müzik içeriklerinin telif hakları %100 Muzibu'ya aittir. Yayında kullanılan müzikler, ticari işletmelerde ve kamusal alanlarda çalınmaya uygundur. Ancak Muzibu hizmeti alınan süre boyunca, işletmede Muzibu dışındaki başka kaynaklardan yapılan müzik yayınları bu lisans kapsamı dışındadır.</p>
                <p class="text-slate-300 font-medium">MUZİBU MEDYA YAPIM A.Ş.</p>
            </div>
        </div>

        {{-- Yasal Bilgilendirme --}}
        <div class="mt-4 bg-slate-900/50 rounded-lg p-6">
            <div class="text-xs text-slate-500 tracking-widest mb-4">YASAL BİLGİLENDİRME</div>
            <div class="space-y-3 text-sm text-slate-400 leading-relaxed">
                <p>I. İşbu belge münhasıran <strong class="cert-mono text-slate-300">{{ $certificate->member_name }}</strong> adına düzenlenmiş olup üçüncü şahıslara devredilemez.</p>
                <p>II. Belge yalnızca yukarıda belirtilen adresteki işletme için geçerlidir.</p>
                <p>III. Üyelik sona erdiğinde veya iptal edildiğinde belge geçerliliğini kaybeder.</p>
                <p>IV. Muzibu A.Ş., önceden bildirimde bulunmaksızın belgeyı iptal etme hakkını saklı tutar.</p>
            </div>
        </div>

        {{-- Alt Bilgi --}}
        <div class="mt-6 text-center text-xs text-slate-600 space-y-2">
            <div class="flex items-center justify-center gap-4 text-slate-500">
                <span><i class="fas fa-eye mr-1"></i> {{ $certificate->view_count }} görüntülenme</span>
                <span><i class="fas fa-clock mr-1"></i> {{ now()->format('d.m.Y H:i') }}</span>
            </div>
            <p>Bu belge Muzibu A.Ş. tarafından elektronik ortamda düzenlenmiştir.</p>
            <p>Sorularınız için: <a href="mailto:{{ setting('site_email') }}" class="gold-text hover:underline">{{ setting('site_email') }}</a></p>
        </div>

    </div>
</div>
@endsection

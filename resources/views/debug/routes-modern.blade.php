<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dynamic Route Debug</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .copy-area { 
            font-family: 'Courier New', monospace; 
            font-size: 12px; 
            line-height: 1.4;
            background: #1e293b;
            color: #e2e8f0;
            padding: 16px;
            border-radius: 8px;
            white-space: pre-wrap;
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-900">
    <div class="container mx-auto px-4 py-8 max-w-7xl">
        
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm border p-6 mb-6">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">🔧 Dynamic Route Debug</h1>
            <p class="text-gray-600">Dinamik route sistemi analizi ve test sonuçları</p>
        </div>

        <!-- Ana Sorun Analizi -->
        <div class="bg-red-50 border border-red-200 rounded-lg p-6 mb-6">
            <h2 class="text-xl font-semibold text-red-800 mb-4">🚨 Tespit Edilen Sorunlar</h2>
            <div class="space-y-2 text-red-700">
                @php
                    $problems = [];
                    
                    // Portfolio ve Announcement slug kontrolü
                    if (!isset($moduleData['Portfolio']) || !in_array('portfolios', $moduleData['Portfolio'])) {
                        $problems[] = "Portfolio modülü 'portfolios' slug'ını bulamıyor - sistem 'portfolios' bekliyor ama farklı değer dönüyor";
                    }
                    
                    if (!isset($moduleData['Announcement']) || !in_array('duyurucuklar', $moduleData['Announcement'])) {
                        $problems[] = "Announcement modülü 'duyurucuklar' slug'ını bulamıyor";
                    }
                    
                    // Test sonuçları kontrolü
                    $failedTests = collect($testResults)->where('found', false)->count();
                    if ($failedTests > 0) {
                        $problems[] = "{$failedTests} adet route testi başarısız";
                    }
                @endphp
                
                @if(count($problems) > 0)
                    @foreach($problems as $problem)
                        <div class="flex items-center">
                            <span class="text-red-500 mr-2">•</span>
                            {{ $problem }}
                        </div>
                    @endforeach
                @else
                    <div class="text-green-700">✅ Büyük sorun tespit edilmedi</div>
                @endif
            </div>
        </div>

        <!-- Grid Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            
            <!-- Sol: Aktif Slug'lar -->
            <div class="bg-white rounded-lg shadow-sm border">
                <div class="bg-blue-50 px-6 py-4 border-b">
                    <h3 class="text-lg font-semibold text-blue-800">📍 Aktif Slug Değerleri</h3>
                    <p class="text-sm text-blue-600">ModuleSlugService'ten gelen değerler</p>
                </div>
                <div class="p-6">
                    @foreach($moduleData as $module => $actions)
                        <div class="mb-4">
                            <h4 class="font-medium text-gray-800 mb-2">{{ $module }}</h4>
                            <div class="space-y-1">
                                @foreach($actions as $action => $slug)
                                    <div class="flex justify-between items-center py-1 px-3 bg-gray-50 rounded">
                                        <span class="text-sm text-gray-600">{{ $action }}:</span>
                                        <span class="font-mono text-sm {{ str_contains($slug, 'ERROR') ? 'text-red-600' : 'text-blue-600' }}">{{ $slug }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Sağ: Route Test Sonuçları -->
            <div class="bg-white rounded-lg shadow-sm border">
                <div class="bg-green-50 px-6 py-4 border-b">
                    <h3 class="text-lg font-semibold text-green-800">🎯 Route Test Sonuçları</h3>
                    <p class="text-sm text-green-600">Resolver test edilmiş URL'ler</p>
                </div>
                <div class="p-6">
                    @foreach($testResults as $test)
                        <div class="flex items-center justify-between py-2 px-3 mb-2 rounded {{ $test['found'] ? 'bg-green-50' : 'bg-red-50' }}">
                            <div>
                                <div class="font-medium text-sm">{{ $test['desc'] }}</div>
                                <div class="text-xs text-gray-500 font-mono">{{ $test['url'] }}</div>
                            </div>
                            <span class="px-2 py-1 rounded text-xs font-medium {{ $test['found'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $test['status'] }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Alt: Config vs Database Karşılaştırması -->
        <div class="bg-white rounded-lg shadow-sm border mb-6">
            <div class="bg-purple-50 px-6 py-4 border-b">
                <h3 class="text-lg font-semibold text-purple-800">⚙️ Config vs Database Karşılaştırması</h3>
                <p class="text-sm text-purple-600">Hangi değerler nereden geliyor</p>
            </div>
            <div class="p-6">
                @foreach(['Page', 'Portfolio', 'Announcement'] as $module)
                    <div class="mb-6 last:mb-0">
                        <h4 class="font-semibold text-gray-800 mb-3">{{ $module }} Modülü</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            
                            <!-- Config -->
                            <div class="border rounded-lg p-4 bg-gray-50">
                                <h5 class="font-medium text-gray-700 mb-2">📁 Config Dosyası</h5>
                                @if(isset($configs[$module]) && count($configs[$module]) > 0)
                                    @foreach($configs[$module] as $action => $slug)
                                        <div class="flex justify-between text-sm py-1">
                                            <span>{{ $action }}:</span>
                                            <span class="font-mono text-blue-600">{{ $slug }}</span>
                                        </div>
                                    @endforeach
                                @else
                                    <span class="text-gray-500 text-sm">Config bulunamadı</span>
                                @endif
                            </div>

                            <!-- Database -->
                            <div class="border rounded-lg p-4 bg-yellow-50">
                                <h5 class="font-medium text-gray-700 mb-2">🗃️ Database Ayarı</h5>
                                @php $dbKey = strtolower($module); @endphp
                                @if(isset($dbSettings[$dbKey]) && isset($dbSettings[$dbKey]->settings['slugs']))
                                    @foreach($dbSettings[$dbKey]->settings['slugs'] as $action => $slug)
                                        <div class="flex justify-between text-sm py-1">
                                            <span>{{ $action }}:</span>
                                            <span class="font-mono text-orange-600">{{ $slug }}</span>
                                        </div>
                                    @endforeach
                                @else
                                    <span class="text-gray-500 text-sm">Database ayarı yok</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Copy-Paste Alanı -->
        <div class="bg-slate-800 rounded-lg shadow-lg">
            <div class="bg-slate-700 px-6 py-4 rounded-t-lg border-b border-slate-600">
                <h3 class="text-lg font-semibold text-slate-100">📋 Copy-Paste Analiz (Ctrl+A, Ctrl+C)</h3>
                <p class="text-sm text-slate-300">Aşağıdaki metni kopyalayıp Claude'a gönderebilirsin</p>
            </div>
            <div class="copy-area">DYNAMIC ROUTE DEBUG RESULTS:

AKTIF SLUG DEĞERLERI:
@foreach($moduleData as $module => $actions)
{{ $module }}:
@foreach($actions as $action => $slug)
  {{ $action }}: {{ $slug }}
@endforeach

@endforeach

ROUTE TEST SONUÇLARI:
@foreach($testResults as $test)
{{ $test['url'] }} ({{ $test['desc'] }}): {{ $test['status'] }}
@endforeach

CONFIG vs DATABASE:
@foreach(['Page', 'Portfolio', 'Announcement'] as $module)
{{ $module }}:
  Config: @if(isset($configs[$module])){{ json_encode($configs[$module]) }}@else{yok}@endif
  @php $dbKey = strtolower($module); @endphp
  Database: @if(isset($dbSettings[$dbKey])){{ json_encode($dbSettings[$dbKey]->settings['slugs'] ?? []) }}@else{yok}@endif

@endforeach

SORUN ANALİZİ:
- Page modülü çalışıyor (sahife → sahife, sahif → sahif)
- Portfolio modülü ÇALIŞMIYOR (portfolios bekleniyor ama config'de var)
- Announcement modülü ÇALIŞMIYOR (duyurucuklar bekleniyor ama config'de announcements var)

ÇÖZÜM:
Portfolio ve Announcement modüllerinin config dosyalarını kontrol et veya database ayarlarını düzelt.</div>
        </div>

        <!-- Test Linkleri -->
        <div class="bg-white rounded-lg shadow-sm border mt-6 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">🔗 Test Linkleri</h3>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                <a href="/sahife" class="block px-4 py-2 bg-blue-100 text-blue-800 rounded-lg text-center hover:bg-blue-200 transition">
                    📄 /sahife
                </a>
                <a href="/sahife/anasayfa" class="block px-4 py-2 bg-blue-100 text-blue-800 rounded-lg text-center hover:bg-blue-200 transition">
                    📄 /sahife/anasayfa
                </a>
                <a href="/portfolios" class="block px-4 py-2 bg-purple-100 text-purple-800 rounded-lg text-center hover:bg-purple-200 transition">
                    🎨 /portfolios
                </a>
                <a href="/duyurucuklar" class="block px-4 py-2 bg-green-100 text-green-800 rounded-lg text-center hover:bg-green-200 transition">
                    📢 /duyurucuklar
                </a>
                <a href="/debug-routes" class="block px-4 py-2 bg-gray-100 text-gray-800 rounded-lg text-center hover:bg-gray-200 transition">
                    🔄 Refresh
                </a>
            </div>
        </div>
    </div>
</body>
</html>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site Dil Sistemi Debug</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .debug-box { @apply bg-gray-50 border border-gray-200 rounded-lg p-4 mb-4; }
        .debug-title { @apply font-bold text-lg text-gray-800 mb-2; }
        .debug-key { @apply font-mono text-sm text-blue-600; }
        .debug-value { @apply font-mono text-sm text-gray-800; }
        .debug-null { @apply text-red-500; }
        .debug-true { @apply text-green-600; }
        .debug-false { @apply text-red-600; }
    </style>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-6xl mx-auto">
        <h1 class="text-3xl font-bold text-center mb-8">Site Dil Sistemi Debug</h1>
        
        <!-- Anlık Refresh Butonu -->
        <div class="text-center mb-6">
            <button onclick="location.reload()" class="bg-blue-500 text-white px-4 py-2 rounded">
                🔄 Sayfayı Yenile
            </button>
            <span class="ml-4 text-gray-600">Son Yenileme: {{ now()->format('H:i:s') }}</span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            <!-- 1. MEVCUT DURUMU -->
            <div class="debug-box">
                <div class="debug-title">🎯 Mevcut Durum</div>
                <table class="w-full text-sm">
                    <tr><td class="debug-key">Current URL:</td><td class="debug-value">{{ request()->fullUrl() }}</td></tr>
                    <tr><td class="debug-key">Laravel Locale:</td><td class="debug-value font-bold text-green-600">{{ app()->getLocale() }}</td></tr>
                    <tr><td class="debug-key">Domain:</td><td class="debug-value">{{ request()->getHost() }}</td></tr>
                    <tr><td class="debug-key">Tenant ID:</td><td class="debug-value">{{ tenant('id') ?? 'NULL' }}</td></tr>
                    <tr><td class="debug-key">User ID:</td><td class="debug-value">{{ auth()->id() ?? 'GUEST' }}</td></tr>
                    <tr><td class="debug-key">User Email:</td><td class="debug-value">{{ auth()->user()->email ?? 'GUEST' }}</td></tr>
                </table>
            </div>

            <!-- 2. SESSION BİLGİLERİ -->
            <div class="debug-box">
                <div class="debug-title">🗂️ Session Bilgileri</div>
                @php
                    // Domain-specific key oluştur
                    $domain = request()->getHost();
                    $domainSessionKey = 'site_locale_' . str_replace('.', '_', $domain);
                    $domainSessionValue = session($domainSessionKey);
                @endphp
                
                <table class="w-full text-sm">
                    <tr><td class="debug-key">Session ID:</td><td class="debug-value">{{ session()->getId() }}</td></tr>
                    <tr><td class="debug-key">site_locale:</td><td class="debug-value {{ session('site_locale') ? 'font-bold text-green-600' : 'debug-null' }}">{{ session('site_locale') ?? 'NULL' }}</td></tr>
                    <tr><td class="debug-key">{{ $domainSessionKey }}:</td><td class="debug-value {{ $domainSessionValue ? 'font-bold text-blue-600' : 'debug-null' }}">{{ $domainSessionValue ?? 'NULL' }}</td></tr>
                    <tr><td class="debug-key">admin_locale:</td><td class="debug-value">{{ session('admin_locale') ?? 'NULL' }}</td></tr>
                    <tr><td class="debug-key">locale:</td><td class="debug-value">{{ session('locale') ?? 'NULL' }}</td></tr>
                    <tr><td class="debug-key">_token:</td><td class="debug-value">{{ session('_token') ?? 'NULL' }}</td></tr>
                </table>
                
                <div class="mt-2 p-2 bg-yellow-50 border border-yellow-200 rounded">
                    <div class="text-xs font-bold text-yellow-800">🚨 PROBLEM ANALYSIS:</div>
                    <div class="text-xs text-yellow-700">
                        @if($domainSessionValue && session('site_locale') !== $domainSessionValue)
                            <div class="text-red-600">❌ SESSION KEY UYUMSUZLUĞU:</div>
                            <div>• LanguageService <code>site_locale</code> arıyor: <strong>{{ session('site_locale') ?? 'NULL' }}</strong></div>
                            <div>• Ama route <code>{{ $domainSessionKey }}</code> kullanıyor: <strong>{{ $domainSessionValue }}</strong></div>
                            <div class="mt-1 font-bold">💡 ÇÖZÜM: LanguageService domain-specific key kullanmalı!</div>
                        @elseif($domainSessionValue && session('site_locale') === $domainSessionValue)
                            <div class="text-green-600">✅ SESSION KEY'LER UYUMLU</div>
                        @else
                            <div class="text-gray-600">Session key analizi yapılamadı</div>
                        @endif
                    </div>
                </div>
                
                <div class="mt-3">
                    <div class="text-xs font-bold">Tüm Session Data:</div>
                    <pre class="text-xs bg-white p-2 rounded overflow-auto max-h-32">{{ json_encode(session()->all(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
            </div>

            <!-- 3. KULLANICI DİL TERCİHİ -->
            <div class="debug-box">
                <div class="debug-title">👤 Kullanıcı Dil Tercihi</div>
                @if(auth()->check())
                    <table class="w-full text-sm">
                        <tr><td class="debug-key">site_language_preference:</td><td class="debug-value {{ auth()->user()->site_language_preference ? 'font-bold text-green-600' : 'debug-null' }}">{{ auth()->user()->site_language_preference ?? 'NULL' }}</td></tr>
                        <tr><td class="debug-key">admin_language_preference:</td><td class="debug-value">{{ auth()->user()->admin_language_preference ?? 'NULL' }}</td></tr>
                        <tr><td class="debug-key">language (eski alan):</td><td class="debug-value">{{ auth()->user()->language ?? 'NULL' }}</td></tr>
                        <tr><td class="debug-key">locale (eski alan):</td><td class="debug-value">{{ auth()->user()->locale ?? 'NULL' }}</td></tr>
                    </table>
                @else
                    <div class="text-gray-500">Kullanıcı giriş yapmamış (GUEST)</div>
                @endif
            </div>

            <!-- 4. VERİTABANI DİLLERİ -->
            <div class="debug-box">
                <div class="debug-title">🗄️ site_languages Tablosu</div>
                @php
                    try {
                        $siteLanguages = \Modules\LanguageManagement\app\Models\SiteLanguage::all();
                        $defaultLanguage = \Modules\LanguageManagement\app\Models\SiteLanguage::where('is_default', true)->first();
                    } catch (Exception $e) {
                        $siteLanguages = collect();
                        $defaultLanguage = null;
                        $dbError = $e->getMessage();
                    }
                @endphp
                
                @if(isset($dbError))
                    <div class="text-red-500 text-sm">❌ Veritabanı Hatası: {{ $dbError }}</div>
                @else
                    <div class="text-xs mb-2">Toplam: {{ $siteLanguages->count() }} dil</div>
                    <div class="text-xs mb-2">Default Dil: <span class="font-bold">{{ $defaultLanguage->code ?? 'NULL' }}</span></div>
                    
                    <div class="overflow-auto max-h-32">
                        <table class="w-full text-xs border">
                            <thead>
                                <tr class="bg-gray-200">
                                    <th class="border p-1">Code</th>
                                    <th class="border p-1">Name</th>
                                    <th class="border p-1">Default</th>
                                    <th class="border p-1">Active</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($siteLanguages as $lang)
                                <tr class="{{ $lang->code === app()->getLocale() ? 'bg-green-100' : '' }}">
                                    <td class="border p-1 font-mono">{{ $lang->code }}</td>
                                    <td class="border p-1">{{ $lang->name }}</td>
                                    <td class="border p-1">{{ $lang->is_default ? '✓' : '' }}</td>
                                    <td class="border p-1">{{ $lang->is_active ? '✓' : '❌' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <!-- 5. MIDDLEWARE VE SERVICE DURUMU -->
            <div class="debug-box">
                <div class="debug-title">⚙️ Middleware ve Service Durumu</div>
                @php
                    // SetLocaleMiddleware kontrolü
                    $middlewareActive = class_exists('\Modules\LanguageManagement\app\Http\Middleware\SetLocaleMiddleware');
                    
                    // LanguageService kontrolü
                    $languageServiceActive = class_exists('\Modules\LanguageManagement\app\Services\LanguageService');
                    
                    // Route middleware kontrolü
                    $routeMiddlewares = request()->route() ? request()->route()->middleware() : [];
                @endphp
                
                <table class="w-full text-sm">
                    <tr><td class="debug-key">SetLocaleMiddleware Class:</td><td class="debug-value {{ $middlewareActive ? 'debug-true' : 'debug-false' }}">{{ $middlewareActive ? 'EXISTS' : 'NOT FOUND' }}</td></tr>
                    <tr><td class="debug-key">LanguageService Class:</td><td class="debug-value {{ $languageServiceActive ? 'debug-true' : 'debug-false' }}">{{ $languageServiceActive ? 'EXISTS' : 'NOT FOUND' }}</td></tr>
                    <tr><td class="debug-key">Route Name:</td><td class="debug-value">{{ request()->route()->getName() ?? 'NULL' }}</td></tr>
                </table>
                
                <div class="mt-2">
                    <div class="text-xs font-bold">Route Middleware Stack:</div>
                    <div class="text-xs bg-white p-2 rounded">
                        @if(count($routeMiddlewares) > 0)
                            @foreach($routeMiddlewares as $middleware)
                                <span class="inline-block bg-blue-100 px-2 py-1 rounded mr-1 mb-1">{{ $middleware }}</span>
                            @endforeach
                        @else
                            <span class="text-gray-500">Middleware bulunamadı</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- 6. CACHE BİLGİLERİ -->
            <div class="debug-box">
                <div class="debug-title">💾 Cache Bilgileri</div>
                @php
                    // Cache bilgilerini topla
                    try {
                        $cacheEnabled = config('responsecache.enabled');
                        $cacheStore = config('cache.default');
                        
                        // Redis bağlantı testi
                        $redisConnected = false;
                        $redisError = null;
                        try {
                            \Illuminate\Support\Facades\Redis::ping();
                            $redisConnected = true;
                        } catch (Exception $e) {
                            $redisError = $e->getMessage();
                        }
                        
                        // Auth cache key kontrolü
                        $cacheKey = auth()->check() ? 'auth_' . auth()->id() : 'guest';
                        
                        // Response cache durumu
                        $responseCacheKeys = [];
                        if ($redisConnected) {
                            try {
                                $keys = \Illuminate\Support\Facades\Redis::keys('*responsecache*') ?? [];
                                $responseCacheKeys = array_slice($keys, 0, 10); // İlk 10'unu göster
                            } catch (Exception $e) {
                                $responseCacheKeys = ['Error: ' . $e->getMessage()];
                            }
                        }
                        
                    } catch (Exception $e) {
                        $cacheError = $e->getMessage();
                    }
                @endphp
                
                <table class="w-full text-sm">
                    <tr><td class="debug-key">Response Cache Enabled:</td><td class="debug-value {{ $cacheEnabled ? 'debug-true' : 'debug-false' }}">{{ $cacheEnabled ? 'TRUE' : 'FALSE' }}</td></tr>
                    <tr><td class="debug-key">Cache Store:</td><td class="debug-value">{{ $cacheStore }}</td></tr>
                    <tr><td class="debug-key">Redis Connected:</td><td class="debug-value {{ $redisConnected ? 'debug-true' : 'debug-false' }}">{{ $redisConnected ? 'TRUE' : 'FALSE' }}</td></tr>
                    @if($redisError)<tr><td class="debug-key">Redis Error:</td><td class="debug-value text-red-500">{{ $redisError }}</td></tr>@endif
                    <tr><td class="debug-key">Auth Cache Key:</td><td class="debug-value font-mono">{{ $cacheKey }}</td></tr>
                </table>
                
                @if(count($responseCacheKeys) > 0)
                    <div class="mt-2">
                        <div class="text-xs font-bold">Response Cache Keys (İlk 10):</div>
                        <div class="text-xs bg-white p-2 rounded max-h-24 overflow-auto">
                            @foreach($responseCacheKeys as $key)
                                <div class="font-mono">{{ $key }}</div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- 7. DİL DEĞİŞTİRME ROUTE'LARI -->
            <div class="debug-box">
                <div class="debug-title">🔗 Dil Değiştirme Route'ları</div>
                @php
                    // Route'ları kontrol et
                    $routeExists = \Illuminate\Support\Facades\Route::has('language.switch');
                    $adminRouteExists = \Illuminate\Support\Facades\Route::has('admin.language.switch');
                @endphp
                
                <table class="w-full text-sm">
                    <tr><td class="debug-key">Site Route (language.switch):</td><td class="debug-value {{ $routeExists ? 'debug-true' : 'debug-false' }}">{{ $routeExists ? 'EXISTS' : 'NOT FOUND' }}</td></tr>
                    <tr><td class="debug-key">Admin Route (admin.language.switch):</td><td class="debug-value {{ $adminRouteExists ? 'debug-true' : 'debug-false' }}">{{ $adminRouteExists ? 'EXISTS' : 'NOT FOUND' }}</td></tr>
                </table>
                
                <div class="mt-2">
                    <div class="text-xs font-bold">Test Links:</div>
                    <div class="text-xs">
                        @if($routeExists && isset($siteLanguages))
                            @foreach($siteLanguages->where('is_active', true) as $lang)
                                <a href="{{ route('language.switch', $lang->code) }}" class="inline-block bg-blue-500 text-white px-2 py-1 rounded mr-1 mb-1 hover:bg-blue-600">
                                    {{ $lang->code }} ({{ $lang->name }})
                                </a>
                            @endforeach
                        @else
                            <span class="text-red-500">Route bulunamadı veya dil yok</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- 8. LIVEWIRE COMPONENT DURUMU -->
            <div class="debug-box">
                <div class="debug-title">⚡ Livewire Component Durumu</div>
                @php
                    $languageSwitcherExists = class_exists('\Modules\LanguageManagement\app\Http\Livewire\LanguageSwitcher');
                    $componentRegistered = app()->bound('livewire');
                    
                    // Header dosyasını kontrol et
                    $headerPath = resource_path('views/themes/blank/layouts/header.blade.php');
                    $headerExists = file_exists($headerPath);
                    $headerContent = $headerExists ? file_get_contents($headerPath) : null;
                    $livewireInHeader = $headerContent ? (strpos($headerContent, '@livewire') !== false || strpos($headerContent, '<livewire:') !== false) : false;
                @endphp
                
                <table class="w-full text-sm">
                    <tr><td class="debug-key">LanguageSwitcher Class:</td><td class="debug-value {{ $languageSwitcherExists ? 'debug-true' : 'debug-false' }}">{{ $languageSwitcherExists ? 'EXISTS' : 'NOT FOUND' }}</td></tr>
                    <tr><td class="debug-key">Livewire Registered:</td><td class="debug-value {{ $componentRegistered ? 'debug-true' : 'debug-false' }}">{{ $componentRegistered ? 'TRUE' : 'FALSE' }}</td></tr>
                    <tr><td class="debug-key">Header File Exists:</td><td class="debug-value {{ $headerExists ? 'debug-true' : 'debug-false' }}">{{ $headerExists ? 'TRUE' : 'FALSE' }}</td></tr>
                    <tr><td class="debug-key">Livewire in Header:</td><td class="debug-value {{ $livewireInHeader ? 'debug-true' : 'debug-false' }}">{{ $livewireInHeader ? 'TRUE' : 'FALSE' }}</td></tr>
                </table>
            </div>

            <!-- 9. ENV VE CONFIG -->
            <div class="debug-box">
                <div class="debug-title">🔧 Environment & Config</div>
                <table class="w-full text-sm">
                    <tr><td class="debug-key">APP_LOCALE:</td><td class="debug-value">{{ config('app.locale') }}</td></tr>
                    <tr><td class="debug-key">APP_FALLBACK_LOCALE:</td><td class="debug-value">{{ config('app.fallback_locale') }}</td></tr>
                    <tr><td class="debug-key">TENANCY_DEFAULT_LANGUAGE:</td><td class="debug-value">{{ env('TENANCY_DEFAULT_LANGUAGE', 'NOT SET') }}</td></tr>
                    <tr><td class="debug-key">RESPONSE_CACHE_ENABLED:</td><td class="debug-value">{{ env('RESPONSE_CACHE_ENABLED', 'NOT SET') }}</td></tr>
                    <tr><td class="debug-key">APP_ENV:</td><td class="debug-value">{{ config('app.env') }}</td></tr>
                    <tr><td class="debug-key">APP_DEBUG:</td><td class="debug-value {{ config('app.debug') ? 'debug-true' : 'debug-false' }}">{{ config('app.debug') ? 'TRUE' : 'FALSE' }}</td></tr>
                </table>
            </div>

            <!-- 10. REQUEST HEADERS -->
            <div class="debug-box">
                <div class="debug-title">📋 Request Headers</div>
                <div class="text-xs bg-white p-2 rounded max-h-32 overflow-auto">
                    @foreach(request()->headers->all() as $key => $values)
                        <div><span class="debug-key">{{ $key }}:</span> {{ implode(', ', $values) }}</div>
                    @endforeach
                </div>
            </div>

            <!-- 11. QUERY PARAMETERS -->
            <div class="debug-box">
                <div class="debug-title">🔍 Query Parameters</div>
                @if(request()->query())
                    <table class="w-full text-sm">
                        @foreach(request()->query() as $key => $value)
                            <tr><td class="debug-key">{{ $key }}:</td><td class="debug-value">{{ $value }}</td></tr>
                        @endforeach
                    </table>
                @else
                    <div class="text-gray-500 text-sm">Query parameter bulunamadı</div>
                @endif
            </div>

            <!-- 12. SON CACHE CLEAR İŞLEMLERİ -->
            <div class="debug-box">
                <div class="debug-title">🧹 Cache Clear İşlemleri</div>
                <div class="space-y-2">
                    <button onclick="clearCache('config')" class="bg-yellow-500 text-white px-3 py-1 rounded text-xs">Config Clear</button>
                    <button onclick="clearCache('route')" class="bg-yellow-500 text-white px-3 py-1 rounded text-xs">Route Clear</button>
                    <button onclick="clearCache('view')" class="bg-yellow-500 text-white px-3 py-1 rounded text-xs">View Clear</button>
                    <button onclick="clearCache('all')" class="bg-red-500 text-white px-3 py-1 rounded text-xs">All Clear</button>
                </div>
                <div id="cache-result" class="mt-2 text-xs"></div>
            </div>

        </div>

        <!-- 13. REAL-TIME API YANITLARI -->
        <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            <!-- Session API -->
            <div class="debug-box">
                <div class="debug-title">📡 Session API Real-time</div>
                <button onclick="loadSessionData()" class="bg-green-500 text-white px-3 py-1 rounded text-xs mb-2">🔄 Yenile</button>
                <div id="session-data" class="text-xs bg-white p-2 rounded max-h-40 overflow-auto">
                    <div class="text-gray-500">Yükleniyor...</div>
                </div>
            </div>

            <!-- Database API -->
            <div class="debug-box">
                <div class="debug-title">🗄️ Database API Real-time</div>
                <button onclick="loadDatabaseData()" class="bg-green-500 text-white px-3 py-1 rounded text-xs mb-2">🔄 Yenile</button>
                <div id="database-data" class="text-xs bg-white p-2 rounded max-h-40 overflow-auto">
                    <div class="text-gray-500">Yükleniyor...</div>
                </div>
            </div>

            <!-- Cache API -->
            <div class="debug-box">
                <div class="debug-title">💾 Cache API Real-time</div>
                <button onclick="loadCacheData()" class="bg-green-500 text-white px-3 py-1 rounded text-xs mb-2">🔄 Yenile</button>
                <div id="cache-data" class="text-xs bg-white p-2 rounded max-h-40 overflow-auto">
                    <div class="text-gray-500">Yükleniyor...</div>
                </div>
            </div>

            <!-- Routes API -->
            <div class="debug-box">
                <div class="debug-title">🔗 Routes API Real-time</div>
                <button onclick="loadRoutesData()" class="bg-green-500 text-white px-3 py-1 rounded text-xs mb-2">🔄 Yenile</button>
                <div id="routes-data" class="text-xs bg-white p-2 rounded max-h-40 overflow-auto">
                    <div class="text-gray-500">Yükleniyor...</div>
                </div>
            </div>

        </div>

        <!-- 14. MANUEL TEST ALANI -->
        <div class="mt-6 debug-box">
            <div class="debug-title">🧪 Manuel Test Alanı</div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                
                <!-- Hızlı Dil Değiştirme -->
                <div>
                    <div class="font-bold text-sm mb-2">Hızlı Dil Değiştirme:</div>
                    <div class="space-y-1">
                        <a href="/debug/quick-lang/tr" class="inline-block bg-red-500 text-white px-3 py-1 rounded text-xs">🇹🇷 Türkçe</a>
                        <a href="/debug/quick-lang/en" class="inline-block bg-blue-500 text-white px-3 py-1 rounded text-xs">🇺🇸 English</a>
                        <a href="/debug/quick-lang/de" class="inline-block bg-yellow-500 text-white px-3 py-1 rounded text-xs">🇩🇪 Deutsch</a>
                    </div>
                </div>

                <!-- Session Kontrolü -->
                <div>
                    <div class="font-bold text-sm mb-2">Session Kontrolü:</div>
                    <button onclick="checkSession()" class="bg-purple-500 text-white px-3 py-1 rounded text-xs">🔍 Session Kontrol</button>
                    <div id="session-check" class="mt-1 text-xs"></div>
                </div>

                <!-- Cache Temizleme -->
                <div>
                    <div class="font-bold text-sm mb-2">Cache Temizleme:</div>
                    <button onclick="clearCache('all')" class="bg-red-500 text-white px-3 py-1 rounded text-xs">🧹 Tümünü Temizle</button>
                    <div id="cache-result" class="mt-1 text-xs"></div>
                </div>

            </div>
        </div>

        <!-- 15. REAL-TIME LOG VIEWER -->
        <div class="mt-6 debug-box">
            <div class="debug-title">📋 Real-time Log Viewer</div>
            <div class="flex space-x-2 mb-2">
                <button onclick="loadLogs()" class="bg-orange-500 text-white px-3 py-1 rounded text-xs">🔄 Log Yenile</button>
                <button onclick="clearLogs()" class="bg-gray-500 text-white px-3 py-1 rounded text-xs">🗑️ Temizle</button>
                <button onclick="autoRefreshLogs()" id="auto-refresh-btn" class="bg-green-500 text-white px-3 py-1 rounded text-xs">▶️ Auto Refresh</button>
            </div>
            <div id="log-viewer" class="text-xs bg-black text-green-400 p-3 rounded font-mono max-h-60 overflow-auto">
                <div class="text-gray-500">Log'lar yükleniyor...</div>
            </div>
        </div>

        <!-- 16. PROBLEM DETECTOR -->
        <div class="mt-6 debug-box">
            <div class="debug-title">🔍 Otomatik Problem Detector</div>
            <button onclick="runDiagnostic()" class="bg-indigo-500 text-white px-4 py-2 rounded font-bold">🚨 TAM TEŞHİS ÇALIŞTIR</button>
            <div id="diagnostic-result" class="mt-3"></div>
        </div>

        <!-- FOOTER BİLGİLERİ -->
        <div class="mt-8 p-4 bg-blue-50 rounded-lg">
            <div class="text-center text-sm">
                <div class="font-bold">🐛 SORUN TESPİT REHBERİ</div>
                <div class="text-left mt-2 space-y-1">
                    <div><strong>1. Session site_locale NULL ise:</strong> Middleware çalışmıyor veya route'da problem var</div>
                    <div><strong>2. DB'de dil var ama değişmiyor ise:</strong> Cache sorunu olabilir</div>
                    <div><strong>3. Route bulunamıyor ise:</strong> web.php'de route tanımı eksik</div>
                    <div><strong>4. Component çalışmıyor ise:</strong> ServiceProvider'da kayıt eksik</div>
                    <div><strong>5. Cache aktif ama değişmiyor ise:</strong> Auth-aware cache key sorunu</div>
                </div>
            </div>
        </div>

    </div>

    <script>
        let autoRefreshInterval = null;
        
        // Cache temizleme fonksiyonu
        function clearCache(type) {
            const resultDiv = document.getElementById('cache-result');
            resultDiv.innerHTML = 'İşlem yapılıyor...';
            
            fetch(`/debug/clear-cache/${type}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                resultDiv.innerHTML = `<span class="text-green-600">${data.message}</span>`;
                setTimeout(() => {
                    resultDiv.innerHTML = '';
                    location.reload(); // Cache temizlenince sayfayı yenile
                }, 2000);
            })
            .catch(error => {
                resultDiv.innerHTML = `<span class="text-red-600">Hata: ${error}</span>`;
            });
        }
        
        // Session verilerini yükle
        function loadSessionData() {
            fetch('/debug/session')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('session-data').innerHTML = 
                        '<pre>' + JSON.stringify(data, null, 2) + '</pre>';
                })
                .catch(error => {
                    document.getElementById('session-data').innerHTML = 
                        '<span class="text-red-500">Hata: ' + error + '</span>';
                });
        }
        
        // Database verilerini yükle
        function loadDatabaseData() {
            fetch('/debug/database')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('database-data').innerHTML = 
                        '<pre>' + JSON.stringify(data, null, 2) + '</pre>';
                })
                .catch(error => {
                    document.getElementById('database-data').innerHTML = 
                        '<span class="text-red-500">Hata: ' + error + '</span>';
                });
        }
        
        // Cache verilerini yükle
        function loadCacheData() {
            fetch('/debug/cache')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('cache-data').innerHTML = 
                        '<pre>' + JSON.stringify(data, null, 2) + '</pre>';
                })
                .catch(error => {
                    document.getElementById('cache-data').innerHTML = 
                        '<span class="text-red-500">Hata: ' + error + '</span>';
                });
        }
        
        // Routes verilerini yükle
        function loadRoutesData() {
            fetch('/debug/routes')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('routes-data').innerHTML = 
                        '<pre>' + JSON.stringify(data, null, 2) + '</pre>';
                })
                .catch(error => {
                    document.getElementById('routes-data').innerHTML = 
                        '<span class="text-red-500">Hata: ' + error + '</span>';
                });
        }
        
        // Session kontrolü
        function checkSession() {
            fetch('/debug/session')
                .then(response => response.json())
                .then(data => {
                    const result = [
                        `Session ID: ${data.session_id}`,
                        `site_locale: ${data.site_locale || 'NULL'}`,
                        `Current Locale: ${data.current_locale}`,
                        `User Pref: ${data.user_preference || 'NULL'}`
                    ].join('<br>');
                    
                    document.getElementById('session-check').innerHTML = 
                        '<div class="bg-gray-100 p-2 rounded mt-1">' + result + '</div>';
                })
                .catch(error => {
                    document.getElementById('session-check').innerHTML = 
                        '<span class="text-red-500">Hata: ' + error + '</span>';
                });
        }
        
        // Log'ları yükle
        function loadLogs() {
            fetch('/debug-lang/get-logs')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const logs = data.logs.join('\\n');
                        document.getElementById('log-viewer').innerHTML = logs || 'Log bulunamadı';
                    } else {
                        document.getElementById('log-viewer').innerHTML = 'Log yüklenirken hata oluştu';
                    }
                })
                .catch(error => {
                    document.getElementById('log-viewer').innerHTML = 'Log API hatası: ' + error;
                });
        }
        
        // Log'ları temizle
        function clearLogs() {
            document.getElementById('log-viewer').innerHTML = 'Log'lar temizlendi...';
        }
        
        // Auto refresh log'ları
        function autoRefreshLogs() {
            const btn = document.getElementById('auto-refresh-btn');
            
            if (autoRefreshInterval) {
                clearInterval(autoRefreshInterval);
                autoRefreshInterval = null;
                btn.innerHTML = '▶️ Auto Refresh';
                btn.className = 'bg-green-500 text-white px-3 py-1 rounded text-xs';
            } else {
                autoRefreshInterval = setInterval(loadLogs, 3000); // 3 saniyede bir
                btn.innerHTML = '⏸️ Stop Refresh';
                btn.className = 'bg-red-500 text-white px-3 py-1 rounded text-xs';
            }
        }
        
        // Otomatik tam teşhis
        function runDiagnostic() {
            const resultDiv = document.getElementById('diagnostic-result');
            resultDiv.innerHTML = '<div class="text-blue-600">🔍 Teşhis çalışıyor...</div>';
            
            // Tüm API'leri eşzamanlı çağır
            Promise.all([
                fetch('/debug/session').then(r => r.json()),
                fetch('/debug/database').then(r => r.json()),
                fetch('/debug/cache').then(r => r.json()),
                fetch('/debug/routes').then(r => r.json())
            ])
            .then(([sessionData, dbData, cacheData, routesData]) => {
                
                let problems = [];
                let warnings = [];
                let success = [];
                
                // Session kontrolü
                if (!sessionData.site_locale) {
                    problems.push('❌ Session site_locale NULL - Middleware çalışmıyor');
                } else {
                    success.push('✅ Session site_locale mevcut: ' + sessionData.site_locale);
                }
                
                // Database kontrolü
                if (dbData.error) {
                    problems.push('❌ Database hatası: ' + dbData.error);
                } else if (!dbData.site_languages || dbData.site_languages.length === 0) {
                    problems.push('❌ site_languages tablosu boş');
                } else {
                    success.push('✅ site_languages tablosu mevcut: ' + dbData.site_languages.length + ' dil');
                    
                    const defaultLang = dbData.default_site_lang;
                    if (!defaultLang) {
                        warnings.push('⚠️ Default dil ayarlanmamış');
                    } else {
                        success.push('✅ Default dil: ' + defaultLang.code);
                    }
                }
                
                // Cache kontrolü
                if (cacheData.config && cacheData.config.response_cache_enabled) {
                    success.push('✅ Response cache aktif');
                    if (!cacheData.auth_cache_key) {
                        warnings.push('⚠️ Auth cache key bulunamadı');
                    } else {
                        success.push('✅ Auth cache key: ' + cacheData.auth_cache_key);
                    }
                } else {
                    warnings.push('⚠️ Response cache deaktif');
                }
                
                // Route kontrolü
                const languageRoutes = routesData.filter(r => r.name && r.name.includes('language'));
                if (languageRoutes.length === 0) {
                    problems.push('❌ Dil değiştirme route'ları bulunamadı');
                } else {
                    success.push('✅ Dil route'ları mevcut: ' + languageRoutes.length + ' adet');
                }
                
                // Genel sağlık durumu
                let healthStatus = '';
                if (problems.length > 0) {
                    healthStatus = '<div class="bg-red-100 border border-red-400 p-3 rounded mb-3"><div class="font-bold text-red-800">🚨 KRİTİK PROBLEMLER:</div>' + problems.map(p => '<div>' + p + '</div>').join('') + '</div>';
                }
                
                if (warnings.length > 0) {
                    healthStatus += '<div class="bg-yellow-100 border border-yellow-400 p-3 rounded mb-3"><div class="font-bold text-yellow-800">⚠️ UYARILAR:</div>' + warnings.map(w => '<div>' + w + '</div>').join('') + '</div>';
                }
                
                if (success.length > 0) {
                    healthStatus += '<div class="bg-green-100 border border-green-400 p-3 rounded mb-3"><div class="font-bold text-green-800">✅ BAŞARILI KONTROLLER:</div>' + success.map(s => '<div>' + s + '</div>').join('') + '</div>';
                }
                
                // Çözüm önerileri
                let solutions = '<div class="bg-blue-100 border border-blue-400 p-3 rounded"><div class="font-bold text-blue-800">💡 ÇÖZÜM ÖNERİLERİ:</div>';
                
                if (problems.find(p => p.includes('site_locale NULL'))) {
                    solutions += '<div>• SetLocaleMiddleware kontrolü yap</div>';
                    solutions += '<div>• Route middleware stack\'i kontrol et</div>';
                }
                
                if (problems.find(p => p.includes('site_languages'))) {
                    solutions += '<div>• Database migration çalıştır</div>';
                    solutions += '<div>• SiteLanguage seeder çalıştır</div>';
                }
                
                if (problems.find(p => p.includes('route'))) {
                    solutions += '<div>• web.php\'de language.switch route\'unu kontrol et</div>';
                }
                
                solutions += '</div>';
                
                resultDiv.innerHTML = healthStatus + solutions;
                
            })
            .catch(error => {
                resultDiv.innerHTML = '<div class="text-red-600">❌ Teşhis hatası: ' + error + '</div>';
            });
        }
        
        // Sayfa yüklenince API verilerini yükle
        document.addEventListener('DOMContentLoaded', function() {
            loadSessionData();
            loadDatabaseData();
            loadCacheData();
            loadRoutesData();
            loadLogs();
        });
        
        // 30 saniyede bir sayfayı otomatik yenile (isteğe bağlı)
        // setTimeout(() => location.reload(), 30000);
    </script>
</body>
</html>
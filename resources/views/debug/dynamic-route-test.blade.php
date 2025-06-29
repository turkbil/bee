<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dynamic Route Debug Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; }
        .debug-box { background: white; padding: 20px; margin: 10px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .debug-title { font-size: 18px; font-weight: bold; color: #333; margin-bottom: 15px; }
        .debug-content { font-family: monospace; font-size: 14px; }
        .success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
        .error { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
        .info { background: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460; }
        .warning { background: #fff3cd; border: 1px solid #ffeaa7; color: #856404; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        th { background: #f8f9fa; }
        .test-links { margin: 20px 0; }
        .test-links a { display: inline-block; margin: 5px 10px 5px 0; padding: 8px 15px; background: #007bff; color: white; text-decoration: none; border-radius: 4px; }
        .test-links a:hover { background: #0056b3; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Dynamic Route Debug Test</h1>
        
        <!-- Test Linkleri -->
        <div class="debug-box">
            <div class="debug-title">🔗 Test Linkleri</div>
            <div class="test-links">
                <a href="/sahife" target="_blank">📄 /sahife (Page Index)</a>
                <a href="/sahife/anasayfa" target="_blank">📄 /sahife/anasayfa (Page Show)</a>
                <a href="/sahife/cerez-politikasi" target="_blank">📄 /sahife/cerez-politikasi (Page Show)</a>
                <a href="/portfolyolar" target="_blank">🎨 /portfolyolar (Portfolio Index)</a>
                <a href="/portfolyolar/test-portfolio" target="_blank">🎨 /portfolyolar/test-portfolio (Portfolio Show)</a>
                <a href="/duyurular" target="_blank">📢 /duyurular (Announcement Index)</a>
                <a href="/duyurular/test-announcement" target="_blank">📢 /duyurular/test-announcement (Announcement Show)</a>
            </div>
        </div>

        <!-- Genel Sistem Bilgileri -->
        <div class="debug-box info">
            <div class="debug-title">🏠 Sistem Bilgileri</div>
            <div class="debug-content">
                <strong>App Locale:</strong> {{ app()->getLocale() }}<br>
                <strong>Session Site Locale:</strong> {{ session('site_locale', 'yok') }}<br>
                <strong>Tenant ID:</strong> {{ tenant() ? tenant()->id : 'Central' }}<br>
                <strong>Current URL:</strong> {{ request()->fullUrl() }}<br>
                <strong>Route Name:</strong> {{ request()->route() ? request()->route()->getName() : 'null' }}<br>
                <strong>Route Action:</strong> {{ request()->route() ? request()->route()->getActionName() : 'null' }}
            </div>
        </div>

        <!-- ModuleSlugService Test -->
        <div class="debug-box">
            <div class="debug-title">🔧 ModuleSlugService - Tüm Modül Slug'ları</div>
            <table>
                <tr><th>Modül</th><th>Action</th><th>Slug</th><th>Config Path</th></tr>
                @php
                    $modules = ['Page', 'Portfolio', 'Announcement'];
                    $actions = ['index', 'show', 'category'];
                @endphp
                @foreach($modules as $module)
                    @foreach($actions as $action)
                        @if($module === 'Announcement' && $action === 'category') @continue @endif
                        <tr>
                            <td>{{ $module }}</td>
                            <td>{{ $action }}</td>
                            <td><strong>{{ \App\Services\ModuleSlugService::getSlug($module, $action) }}</strong></td>
                            <td>Modules/{{ $module }}/config/config.php</td>
                        </tr>
                    @endforeach
                @endforeach
            </table>
        </div>

        <!-- Config Dosyaları Test -->
        <div class="debug-box">
            <div class="debug-title">📁 Config Dosyaları İçerikleri</div>
            @foreach(['Page', 'Portfolio', 'Announcement'] as $module)
                @php
                    $configPath = base_path("Modules/{$module}/config/config.php");
                    $configExists = file_exists($configPath);
                    $configContent = $configExists ? include $configPath : null;
                @endphp
                <div class="debug-content" style="margin-bottom: 15px;">
                    <strong>{{ $module }} Config:</strong><br>
                    @if($configExists)
                        <span class="success">✅ Dosya mevcut: {{ $configPath }}</span><br>
                        <strong>Slugs:</strong> <pre>{{ json_encode($configContent['slugs'] ?? 'Slugs array yok', JSON_PRETTY_PRINT) }}</pre>
                    @else
                        <span class="error">❌ Config dosyası bulunamadı: {{ $configPath }}</span>
                    @endif
                </div>
            @endforeach
        </div>

        <!-- Database Ayarları -->
        <div class="debug-box">
            <div class="debug-title">🗃️ Database Ayarları (ModuleTenantSetting)</div>
            @php
                $dbSettings = \App\Models\ModuleTenantSetting::all();
            @endphp
            @if($dbSettings->count() > 0)
                <table>
                    <tr><th>Modül</th><th>Settings</th><th>Created</th></tr>
                    @foreach($dbSettings as $setting)
                        <tr>
                            <td>{{ $setting->module_name }}</td>
                            <td><pre>{{ json_encode($setting->settings, JSON_PRETTY_PRINT) }}</pre></td>
                            <td>{{ $setting->created_at }}</td>
                        </tr>
                    @endforeach
                </table>
            @else
                <div class="warning">⚠️ ModuleTenantSetting tablosu boş</div>
            @endif
        </div>

        <!-- Dynamic Route Resolver Test -->
        <div class="debug-box">
            <div class="debug-title">🎯 Dynamic Route Resolver Test</div>
            @php
                $resolver = app(\App\Contracts\DynamicRouteResolverInterface::class);
                $testCases = [
                    ['sahife', null, null, 'Page Index'],
                    ['sahife', 'anasayfa', null, 'Page Show'],
                    ['sahife', 'cerez-politikasi', null, 'Page Show'],
                    ['portfolyolar', null, null, 'Portfolio Index'],
                    ['portfolyolar', 'test-item', null, 'Portfolio Show'],
                    ['portfolyolar', 'kategori', 'web-tasarim', 'Portfolio Category'],
                    ['duyurular', null, null, 'Announcement Index'],
                    ['duyurular', 'test-duyuru', null, 'Announcement Show'],
                ];
            @endphp
            <table>
                <tr><th>Test Case</th><th>Slug1</th><th>Slug2</th><th>Slug3</th><th>Sonuç</th><th>Route Info</th></tr>
                @foreach($testCases as $test)
                    @php
                        [$slug1, $slug2, $slug3, $description] = $test;
                        try {
                            $result = $resolver->resolve($slug1, $slug2, $slug3);
                            $status = $result ? 'success' : 'error';
                            $resultText = $result ? '✅ BULUNDU' : '❌ BULUNAMADI';
                            $routeInfo = $result ? json_encode($result, JSON_PRETTY_PRINT) : 'null';
                        } catch (\Exception $e) {
                            $status = 'error';
                            $resultText = '🔥 HATA: ' . $e->getMessage();
                            $routeInfo = 'Exception';
                        }
                    @endphp
                    <tr class="{{ $status }}">
                        <td>{{ $description }}</td>
                        <td>{{ $slug1 }}</td>
                        <td>{{ $slug2 ?: '-' }}</td>
                        <td>{{ $slug3 ?: '-' }}</td>
                        <td>{{ $resultText }}</td>
                        <td><pre>{{ $routeInfo }}</pre></td>
                    </tr>
                @endforeach
            </table>
        </div>

        <!-- Route Cache Test -->
        <div class="debug-box">
            <div class="debug-title">💾 Route Cache Durumu</div>
            @php
                $tenant = tenant();
                $locale = app()->getLocale();
                $tenantPart = $tenant ? "tenant_{$tenant->id}" : 'central';
                
                $cacheKeys = [
                    "dynamic_route:{$tenantPart}:{$locale}:sahife",
                    "dynamic_route:{$tenantPart}:{$locale}:sahife_anasayfa",
                    "dynamic_route:{$tenantPart}:{$locale}:portfolyolar",
                    "dynamic_route:{$tenantPart}:{$locale}:duyurular",
                ];
            @endphp
            <table>
                <tr><th>Cache Key</th><th>Var mı?</th><th>İçerik</th></tr>
                @foreach($cacheKeys as $key)
                    @php
                        $hasCache = \Cache::has($key);
                        $cacheValue = $hasCache ? \Cache::get($key) : null;
                    @endphp
                    <tr class="{{ $hasCache ? 'success' : 'warning' }}">
                        <td>{{ $key }}</td>
                        <td>{{ $hasCache ? '✅ VAR' : '❌ YOK' }}</td>
                        <td><pre>{{ $hasCache ? json_encode($cacheValue, JSON_PRETTY_PRINT) : 'null' }}</pre></td>
                    </tr>
                @endforeach
            </table>
        </div>

        <!-- Memory Cache Test -->
        <div class="debug-box">
            <div class="debug-title">🧠 ModuleSlugService Memory Cache</div>
            @php
                // Memory cache'i görmek için reflection kullanmalıyız
                $reflection = new \ReflectionClass(\App\Services\ModuleSlugService::class);
                $memoryProperty = $reflection->getStaticPropertyValue('memoryCache');
                $globalLoaded = $reflection->getStaticPropertyValue('globalSettingsLoaded');
                $tableEmpty = $reflection->getStaticPropertyValue('tableIsEmpty');
            @endphp
            <div class="debug-content">
                <strong>Global Settings Loaded:</strong> {{ $globalLoaded ? 'true' : 'false' }}<br>
                <strong>Table Is Empty:</strong> {{ $tableEmpty === null ? 'null' : ($tableEmpty ? 'true' : 'false') }}<br>
                <strong>Memory Cache:</strong><br>
                <pre>{{ json_encode($memoryProperty, JSON_PRETTY_PRINT) }}</pre>
            </div>
        </div>

        <!-- Cache Temizleme -->
        <div class="debug-box warning">
            <div class="debug-title">🧹 Cache Temizleme</div>
            <div class="test-links">
                <a href="/debug/clear-cache">🗑️ Tüm Cache'leri Temizle</a>
                <a href="/debug/clear-module-cache">🔧 Module Cache Temizle</a>
                <a href="/debug/clear-route-cache">🎯 Route Cache Temizle</a>
            </div>
        </div>
    </div>
</body>
</html>
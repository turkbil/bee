<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Language Debug - All Data</title>
    <style>
        body { font-family: monospace; background: #f5f5f5; margin: 20px; }
        .debug-section { background: white; padding: 15px; margin: 10px 0; border: 1px solid #ddd; }
        .debug-title { font-weight: bold; color: #333; margin-bottom: 10px; }
        .debug-data { background: #f8f8f8; padding: 10px; border-left: 4px solid #007cba; }
        .highlight { background: yellow; padding: 2px; }
        .error { color: red; font-weight: bold; }
        .success { color: green; font-weight: bold; }
        .warning { color: orange; font-weight: bold; }
    </style>
    @livewireStyles
</head>
<body>

<div class="debug-section">
    <div class="debug-title">🔍 LANGUAGE SWITCHER DEBUG - ALL DATA</div>
    <div class="debug-data">
        Timestamp: {{ now()->format('Y-m-d H:i:s') }}
        URL: {{ request()->fullUrl() }}
        Test Page: Frontend Language Switching Issue
    </div>
</div>

<div class="debug-section">
    <div class="debug-title">🔧 CACHE ANALYSIS</div>
    <div class="debug-data">
@php
echo "Cache Stores Analysis:\n";
foreach (['file', 'redis', 'array'] as $store) {
    try {
        $cache = \Cache::store($store);
        echo "- {$store}: " . (method_exists($cache, 'getStore') ? 'Available' : 'Not Available') . "\n";
    } catch (\Exception $e) {
        echo "- {$store}: Error - " . $e->getMessage() . "\n";
    }
}

echo "\nResponseCache Status:\n";
echo "- Enabled: " . (config('responsecache.enabled') ? 'YES' : 'NO') . "\n";
echo "- Profile Class: " . config('responsecache.cache_profile') . "\n";

if (class_exists('\Spatie\ResponseCache\Facades\ResponseCache')) {
    try {
        echo "- ResponseCache: Available\n";
    } catch (\Exception $e) {
        echo "- ResponseCache: Error - " . $e->getMessage() . "\n";
    }
}

echo "\nCache Directories:\n";
$cacheDirs = [
    'framework/cache' => storage_path('framework/cache'),
    'framework/views' => storage_path('framework/views'),
    'logs' => storage_path('logs'),
];

foreach ($cacheDirs as $name => $path) {
    if (is_dir($path)) {
        $files = glob($path . '/*');
        echo "- {$name}: " . count($files) . " files\n";
    } else {
        echo "- {$name}: Not found\n";
    }
}

echo "\nMiddleware Stack Analysis:\n";
$request = request();
echo "- Current URL: " . $request->fullUrl() . "\n";
echo "- Is AJAX: " . ($request->ajax() ? 'YES' : 'NO') . "\n";
echo "- Method: " . $request->method() . "\n";
echo "- Is language route: " . ($request->is('language/*') ? 'YES' : 'NO') . "\n";
echo "- Is admin route: " . ($request->is('admin/*') ? 'YES' : 'NO') . "\n";

if (class_exists('\App\Services\TenantCacheProfile')) {
    $profile = new \App\Services\TenantCacheProfile();
    echo "- Should cache request: " . ($profile->shouldCacheRequest($request) ? 'YES' : 'NO') . "\n";
    echo "- Cache enabled: " . ($profile->enabled($request) ? 'YES' : 'NO') . "\n";
    echo "- Cache suffix: " . $profile->useCacheNameSuffix($request) . "\n";
}
@endphp
    </div>
</div>

<div class="debug-section">
    <div class="debug-title">📊 CURRENT SESSION STATE</div>
    <div class="debug-data">
Session site_language: {{ session('site_language', 'NULL') }}
Session locale: {{ session('locale', 'NULL') }}
App locale: {{ app()->getLocale() }}
PHP Session ID: {{ session()->getId() }}
Session driver: {{ config('session.driver') }}
    </div>
</div>

<div class="debug-section">
    <div class="debug-title">💾 DATABASE LANGUAGES</div>
    <div class="debug-data">
@php
$siteLanguages = \Modules\LanguageManagement\App\Models\SiteLanguage::where('is_active', true)->get();
@endphp
Available languages count: {{ $siteLanguages->count() }}
@foreach($siteLanguages as $lang)
- {{ $lang->code }} | {{ $lang->native_name }} | Active: {{ $lang->is_active ? 'YES' : 'NO' }} | Default: {{ $lang->is_default ? 'YES' : 'NO' }}
@endforeach
    </div>
</div>

<div class="debug-section">
    <div class="debug-title">🧩 LIVEWIRE COMPONENT TEST</div>
    <div class="debug-data">
@php
try {
    $component = new \Modules\LanguageManagement\App\Http\Livewire\LanguageSwitcher();
    $component->mount();
    echo "Component mount: SUCCESS\n";
    echo "Current language: " . $component->currentLanguage . "\n";
    echo "Available languages: " . implode(',', $component->availableLanguages) . "\n";
    echo "Show dropdown: " . ($component->showDropdown ? 'true' : 'false') . "\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
@endphp
    </div>
</div>

<div class="debug-section">
    <div class="debug-title">🎯 FRONTEND LANGUAGE SWITCHER (LIVE TEST)</div>
    <div class="debug-data">
Component rendering below - watch browser console for errors:
--- COMPONENT START ---
@livewire('languagemanagement::language-switcher', ['style' => 'buttons', 'showText' => true, 'showFlags' => true])
--- COMPONENT END ---
    </div>
</div>

<div class="debug-section">
    <div class="debug-title">🔧 MANUAL TEST BUTTONS</div>
    <div class="debug-data">
<button onclick="testLanguage('tr')" style="padding: 10px; margin: 5px;">🇹🇷 TÜRKÇE TEST</button>
<button onclick="testLanguage('en')" style="padding: 10px; margin: 5px;">🇺🇸 ENGLISH TEST</button>
<button onclick="testLanguage('ar')" style="padding: 10px; margin: 5px;">🇸🇦 العربية TEST</button>

Manual test result:
<div id="manual-result">Click buttons above to test</div>

<!-- SESSION TEST SECTION -->
<div style="margin-top: 20px; padding: 15px; background: #e8f4f8; border-left: 4px solid #2196F3;">
    <h4>🔧 Session Manual Test</h4>
    <button onclick="testSession()" style="padding: 8px 16px; background: #2196F3; color: white; border: none; border-radius: 4px; cursor: pointer;">
        Test Session Setting
    </button>
    <div id="session-result" style="margin-top: 10px; font-family: monospace; background: white; padding: 10px; border-radius: 4px;"></div>
</div>
    </div>
</div>

<div class="debug-section">
    <div class="debug-title">📄 RECENT LOGS (Last 20 lines)</div>
    <div class="debug-data">
@php
$logFile = storage_path('logs/laravel.log');
if (file_exists($logFile)) {
    $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $recentLines = array_slice($lines, -20);
    foreach ($recentLines as $line) {
        echo htmlspecialchars($line) . "\n";
    }
} else {
    echo "Log file not found\n";
}
@endphp
    </div>
</div>

<div class="debug-section">
    <div class="debug-title">🌐 BROWSER & ENVIRONMENT INFO</div>
    <div class="debug-data">
PHP Version: {{ PHP_VERSION }}
Laravel Version: {{ app()->version() }}
Livewire loaded: {{ class_exists('Livewire\Livewire') ? 'YES' : 'NO' }}
Alpine.js available: <span id="alpine-status">Checking...</span>
JavaScript errors: <span id="js-errors">None detected</span>
Current domain: {{ request()->getHost() }}
Is HTTPS: {{ request()->isSecure() ? 'YES' : 'NO' }}
User agent: {{ request()->userAgent() }}
    </div>
</div>

<div class="debug-section">
    <div class="debug-title">⚡ JAVASCRIPT DEBUG OUTPUT</div>
    <div class="debug-data">
<div id="js-debug">Loading JavaScript debug info...</div>
    </div>
</div>

<div class="debug-section">
    <div class="debug-title">🚨 KNOWN ISSUES CHECK</div>
    <div class="debug-data">
@php
echo "Checking common issues...\n";

// 1. Session middleware check  
$middleware = app('router')->getMiddleware();
$middlewareGroups = app('router')->getMiddlewareGroups();
echo "Session middleware registered: " . (isset($middleware['web']) ? 'YES' : 'NO') . "\n";
echo "Web middleware group exists: " . (isset($middlewareGroups['web']) ? 'YES' : 'NO') . "\n";
if (isset($middlewareGroups['web'])) {
    echo "Web middleware contains:\n";
    foreach ($middlewareGroups['web'] as $mw) {
        echo "  - " . $mw . "\n";
    }
}

// 2. Livewire service provider check
$providers = app()->getLoadedProviders();
echo "Livewire provider loaded: " . (isset($providers['Livewire\LivewireServiceProvider']) ? 'YES' : 'NO') . "\n";

// 3. Module provider check
echo "LanguageManagement provider loaded: " . (isset($providers['Modules\LanguageManagement\Providers\LanguageManagementServiceProvider']) ? 'YES' : 'NO') . "\n";

// 4. View path check
$viewPaths = app('view')->getFinder()->getPaths();
echo "View paths count: " . count($viewPaths) . "\n";
foreach ($viewPaths as $path) {
    echo "- " . $path . "\n";
}

// 5. Route list check for Livewire
$routes = app('router')->getRoutes();
$livewireRoutes = 0;
foreach ($routes as $route) {
    if (str_contains($route->uri(), 'livewire')) {
        $livewireRoutes++;
    }
}
echo "Livewire routes found: " . $livewireRoutes . "\n";
@endphp
    </div>
</div>

@livewireScripts

<script>
// JavaScript debug
document.addEventListener('DOMContentLoaded', function() {
    // Alpine.js check
    setTimeout(() => {
        document.getElementById('alpine-status').textContent = 
            typeof window.Alpine !== 'undefined' ? 'YES' : 'NO';
    }, 1000);
    
    // Error tracking
    let jsErrors = [];
    window.addEventListener('error', function(e) {
        jsErrors.push(`${e.message} at ${e.filename}:${e.lineno}`);
        document.getElementById('js-errors').innerHTML = jsErrors.join('<br>');
    });
    
    // Livewire events tracking
    if (typeof window.Livewire !== 'undefined') {
        let debugOutput = document.getElementById('js-debug');
        debugOutput.innerHTML = 'Livewire detected, listening for events...\n';
        
        window.Livewire.on('languageChanged', (data) => {
            debugOutput.innerHTML += `Language changed event received: ${JSON.stringify(data)}\n`;
        });
        
        // Track all Livewire requests
        document.addEventListener('livewire:init', () => {
            debugOutput.innerHTML += 'Livewire initialized\n';
        });
        
        document.addEventListener('livewire:load', () => {
            debugOutput.innerHTML += 'Livewire loaded\n';
        });
        
        document.addEventListener('livewire:update', () => {
            debugOutput.innerHTML += 'Livewire component updated\n';
        });
    } else {
        document.getElementById('js-debug').innerHTML = 'ERROR: Livewire not detected!\n';
    }
});

// Manual test function
async function testLanguage(code) {
    const resultDiv = document.getElementById('manual-result');
    resultDiv.innerHTML = `Testing ${code}...`;
    
    try {
        const response = await fetch(`/debug-lang/switch-test/${code}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
        
        const data = await response.json();
        resultDiv.innerHTML = `Result: ${JSON.stringify(data, null, 2)}`;
        
        // Reload page to see changes
        setTimeout(() => window.location.reload(), 2000);
        
    } catch (error) {
        resultDiv.innerHTML = `ERROR: ${error.message}`;
    }
}

// Test session functionality
async function testSession() {
    const resultDiv = document.getElementById('session-result');
    resultDiv.innerHTML = 'Testing session...';
    
    try {
        const response = await fetch('/debug-lang/session-check');
        const data = await response.json();
        resultDiv.innerHTML = `<pre>${JSON.stringify(data, null, 2)}</pre>`;
    } catch (error) {
        resultDiv.innerHTML = `ERROR: ${error.message}`;
    }
}

// Page info
console.log('🔍 Language Debug Page Loaded');
console.log('📊 Current session:', {
    site_language: '{{ session("site_language", "NULL") }}',
    locale: '{{ session("locale", "NULL") }}',
    app_locale: '{{ app()->getLocale() }}'
});
</script>

</body>
</html>
<!DOCTYPE html>
<html>
<head>
    <title>Dil Değiştirme Debug Testi</title>
    <style>
        body { font-family: monospace; margin: 20px; background: #f5f5f5; }
        .debug-box { background: white; padding: 20px; margin: 10px 0; border: 1px solid #ddd; border-radius: 5px; }
        .debug-title { font-weight: bold; color: #333; margin-bottom: 10px; font-size: 16px; }
        .debug-content { white-space: pre-wrap; }
        .lang-switch { margin: 20px 0; }
        .lang-btn { padding: 10px 20px; margin: 5px; background: #007cba; color: white; text-decoration: none; border-radius: 3px; }
        .current { background: #28a745 !important; }
        .copy-btn { background: #dc3545; padding: 15px 30px; color: white; border: none; border-radius: 5px; font-size: 16px; margin: 20px 0; cursor: pointer; }
    </style>
</head>
<body>

<h1>🔍 Dil Değiştirme Debug Testi</h1>
<p><strong>Zaman:</strong> {{ date('Y-m-d H:i:s') }} - {{ request()->fullUrl() }}</p>

<div class="debug-box">
    <div class="debug-title">👤 SESSION VE LOCALE DURUMU</div>
    <div class="debug-content">App Locale: {{ app()->getLocale() }}
Session site_locale: {{ session('site_locale') }}
Session site_locale_laravel_test: {{ session('site_locale_laravel_test') }}
Session admin_locale: {{ session('admin_locale') }}
Auth: {{ auth()->check() ? 'YES (User: '.auth()->id().')' : 'NO' }}
User site_language_preference: {{ auth()->check() ? auth()->user()->site_language_preference : 'N/A' }}
Referrer: {{ request()->header('referer', 'YOK') }}</div>
</div>

<div class="debug-box">
    <div class="debug-title">🌐 MEVCUT DİLLER</div>
    <div class="debug-content">@php
$currentLang = app()->getLocale();
$siteLanguages = collect();
try {
    if (tenant()) {
        $siteLanguages = tenant()->siteLanguages()->where('is_active', 1)->orderBy('sort_order')->get();
    } else {
        $siteLanguages = \Modules\LanguageManagement\app\Models\SiteLanguage::where('is_active', 1)->orderBy('sort_order')->get();
    }
} catch (\Exception $e) {
    // ignore
}
@endphp
Toplam Aktif Dil: {{ $siteLanguages->count() }}
@foreach($siteLanguages as $lang)
{{ $lang->code }} ({{ $lang->name }}) - {{ $lang->flag_icon ?? '🌐' }}{{ $lang->code === $currentLang ? ' ← CURRENT' : '' }}
@endforeach</div>
</div>

<div class="debug-box">
    <div class="debug-title">🔄 DİL DEĞİŞTİRME LİNKLERİ</div>
    <div class="lang-switch">
        @foreach($siteLanguages as $lang)
            <a href="/language/{{ $lang->code }}" 
               class="lang-btn {{ $lang->code === $currentLang ? 'current' : '' }}">
                {{ $lang->flag_icon ?? '🌐' }} {{ strtoupper($lang->code) }}
            </a>
        @endforeach
    </div>
</div>

<div class="debug-box">
    <div class="debug-title">💾 REDIS CACHE DURUMU</div>
    <div class="debug-content">@php
$redis = \Illuminate\Support\Facades\Redis::connection();
$allKeys = $redis->keys('*');
$cacheKeys = $redis->keys('*cache*');
$responseKeys = $redis->keys('*response*');
@endphp
Total Redis Keys: {{ count($allKeys) }}
Cache Keys: {{ count($cacheKeys) }}
Response Keys: {{ count($responseKeys) }}

Sample Keys:
@foreach(array_slice($allKeys, 0, 10) as $key)
- {{ $key }}
@endforeach</div>
</div>

<div class="debug-box">
    <div class="debug-title">🛠️ ROUTE VE MIDDLEWARE BİLGİSİ</div>
    <div class="debug-content">Current Route Name: {{ request()->route()?->getName() ?? 'YOK' }}
Current URL: {{ request()->fullUrl() }}
Method: {{ request()->method() }}
Is Ajax: {{ request()->ajax() ? 'YES' : 'NO' }}
Headers:
@foreach(['referer', 'user-agent', 'cache-control'] as $header)
- {{ $header }}: {{ request()->header($header, 'YOK') }}
@endforeach</div>
</div>

<div class="debug-box">
    <div class="debug-title">📋 KOPYALAMA TALİMATI</div>
    <div class="debug-content">Bu sayfadaki tüm içeriği CTRL+A ile seç, CTRL+C ile kopyala ve Claude'a yapıştır.</div>
    <button class="copy-btn" onclick="selectAll()">📋 TÜM İÇERİĞİ SEÇ VE KOPYALA</button>
</div>

<script>
function selectAll() {
    document.body.focus();
    document.executer
    if (document.body.createTextRange) {
        const range = document.body.createTextRange();
        range.moveToElementText(document.body);
        range.select();
    } else if (window.getSelection) {
        const selection = window.getSelection();
        const range = document.createRange();
        range.selectNodeContents(document.body);
        selection.removeAllRanges();
        selection.addRange(range);
    }
}

// Auto refresh every 3 seconds to see changes
setTimeout(function() {
    const currentUrl = window.location.href;
    if (!currentUrl.includes('norefresh')) {
        window.location.reload();
    }
}, 3000);
</script>

</body>
</html>
<!DOCTYPE html>
<html>
<head>
    <title>Simple Language Debug</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        .debug-box { background: #f5f5f5; padding: 15px; margin: 10px 0; border-radius: 5px; }
        .btn { display: inline-block; padding: 8px 16px; margin: 5px; text-decoration: none; border-radius: 3px; background: #007bff; color: white; }
        pre { background: #e9ecef; padding: 10px; border-radius: 3px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>🔧 Simple Language Debug - {{ now() }}</h1>
    
    <div class="debug-box">
        <h3>🌐 Language Switch Links</h3>
        <a href="/language/tr" class="btn">Türkçe (TR)</a>
        <a href="/language/en" class="btn">English (EN)</a>
        <a href="/language/ar" class="btn">العربية (AR)</a>
        <a href="javascript:location.reload()" class="btn">🔄 Reload</a>
    </div>

    <div class="debug-box">
        <h3>📊 Current State</h3>
        <strong>Laravel App Locale:</strong> {{ app()->getLocale() }}<br>
        <strong>Session site_locale:</strong> {{ session('site_locale') ?? 'NULL' }}<br>
        <strong>Session admin_locale:</strong> {{ session('admin_locale') ?? 'NULL' }}<br>
        <strong>Current Time:</strong> {{ now() }}
    </div>

    <div class="debug-box">
        <h3>🔍 UrlPrefixService Test</h3>
        @php
            $urlData = \Modules\LanguageManagement\app\Services\UrlPrefixService::parseUrl(request());
        @endphp
        <strong>Language:</strong> 
        @if(is_object($urlData['language'] ?? null))
            Object - Code: {{ $urlData['language']->code ?? 'N/A' }}, Is Default: {{ $urlData['language']->is_default ?? 'N/A' }}
        @else
            {{ $urlData['language'] ?? 'NULL' }}
        @endif
        <br>
        <strong>Has Prefix:</strong> {{ $urlData['has_prefix'] ? 'YES' : 'NO' }}<br>
        <strong>Clean Path:</strong> {{ $urlData['clean_path'] ?? 'NULL' }}
    </div>

    <div class="debug-box">
        <h3>📋 Raw Session Data</h3>
        <pre>{!! print_r(session()->all(), true) !!}</pre>
    </div>

    <div class="debug-box">
        <h3>🎯 Manual Session Check</h3>
        <strong>session('site_locale') Direct:</strong> {{ session('site_locale') ?? 'NULL' }}<br>
        <strong>session()->has('site_locale'):</strong> {{ session()->has('site_locale') ? 'TRUE' : 'FALSE' }}<br>
        <strong>session()->get('site_locale'):</strong> {{ session()->get('site_locale') ?? 'NULL' }}
    </div>
</body>
</html>
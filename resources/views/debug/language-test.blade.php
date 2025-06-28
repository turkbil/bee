<!DOCTYPE html>
<html>
<head>
    <title>Language Debug Test</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        .debug-box { background: #f5f5f5; padding: 15px; margin: 10px 0; border-radius: 5px; }
        .success { background: #d4edda; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; border: 1px solid #f5c6cb; }
        .warning { background: #fff3cd; border: 1px solid #ffeaa7; }
        pre { background: #e9ecef; padding: 10px; border-radius: 3px; overflow-x: auto; }
        .btn { display: inline-block; padding: 8px 16px; margin: 5px; text-decoration: none; border-radius: 3px; }
        .btn-primary { background: #007bff; color: white; }
        .btn-danger { background: #dc3545; color: white; }
        .btn-success { background: #28a745; color: white; }
    </style>
</head>
<body>
    <h1>🔧 Language Debug Test - {{ now() }}</h1>
    
    <div class="debug-box">
        <h3>🌐 Language Switch Links</h3>
        <a href="/language/tr" class="btn btn-primary">Türkçe (TR)</a>
        <a href="/language/en" class="btn btn-primary">English (EN)</a>
        <a href="/language/ar" class="btn btn-primary">العربية (AR)</a>
        <a href="javascript:location.reload()" class="btn btn-success">🔄 Reload Page</a>
    </div>

    <div class="debug-box">
        <h3>📊 Current State</h3>
        <strong>Laravel App Locale:</strong> <code>{{ app()->getLocale() }}</code><br>
        <strong>Session site_locale:</strong> <code>{{ session('site_locale') ?? 'NULL' }}</code><br>
        <strong>Session admin_locale:</strong> <code>{{ session('admin_locale') ?? 'NULL' }}</code><br>
        <strong>Current Time:</strong> <code>{{ now() }}</code>
    </div>

    <div class="debug-box">
        <h3>🔍 UrlPrefixService Test</h3>
        @php
            $urlData = \Modules\LanguageManagement\app\Services\UrlPrefixService::parseUrl(request());
        @endphp
        <pre>{{ json_encode($urlData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
    </div>

    <div class="debug-box">
        <h3>📋 Raw Session Data</h3>
        <pre>{{ json_encode(session()->all(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
    </div>

    <div class="debug-box">
        <h3>🗄️ Available Site Languages</h3>
        @php
            $tenant = tenant();
            $languages = $tenant ? $tenant->siteLanguages()->get() : collect();
        @endphp
        @if($languages->count() > 0)
            <table border="1" cellpadding="5" cellspacing="0">
                <tr>
                    <th>ID</th>
                    <th>Code</th>
                    <th>Name</th>
                    <th>Is Default</th>
                    <th>Status</th>
                </tr>
                @foreach($languages as $lang)
                <tr>
                    <td>{{ $lang->id }}</td>
                    <td><strong>{{ $lang->code }}</strong></td>
                    <td>{{ $lang->name }}</td>
                    <td>{{ $lang->is_default ? '✅ YES' : '❌ NO' }}</td>
                    <td>{{ $lang->status ? '✅ Active' : '❌ Inactive' }}</td>
                </tr>
                @endforeach
            </table>
        @else
            <p class="error">❌ No site languages found</p>
        @endif
    </div>

    <div class="debug-box">
        <h3>🧪 Translation Test</h3>
        @php
            $testPage = \Modules\Page\app\Models\Page::first();
        @endphp
        @if($testPage)
            <strong>Page Title Raw:</strong> <code>{{ $testPage->title }}</code><br>
            <strong>Page Title Translated:</strong> <code>{{ $testPage->getTranslatedAttribute('title') }}</code><br>
            <strong>Available Locales:</strong> <code>{{ json_encode($testPage->getAvailableLocales('title') ?? []) }}</code>
        @else
            <p class="warning">⚠️ No test page found</p>
        @endif
    </div>

    <div class="debug-box">
        <h3>🎯 Manual Session Check</h3>
        @php
            $sessionLocale = session('site_locale');
            $sessionExists = session()->has('site_locale');
            $sessionAll = session()->all();
        @endphp
        <strong>session('site_locale') Direct:</strong> <code>{{ $sessionLocale ?? 'NULL' }}</code><br>
        <strong>session()->has('site_locale'):</strong> <code>{{ $sessionExists ? 'TRUE' : 'FALSE' }}</code><br>
        <strong>Session Keys:</strong> <code>{{ implode(', ', array_keys($sessionAll ?? [])) }}</code>
    </div>

    <script>
        // Auto refresh every 5 seconds if URL contains "auto"
        if (window.location.href.includes('auto=1')) {
            setTimeout(() => location.reload(), 5000);
        }
    </script>
</body>
</html>
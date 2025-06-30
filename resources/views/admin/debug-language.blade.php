@extends('admin.layout')

@section('title', 'Dil Sistemi Debug')

@section('content')
<div class="container-xl">
    <div class="page-header d-print-none">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    <i class="fa-solid fa-bug me-2"></i>
                    Dil Sistemi Debug
                </h2>
                <div class="text-muted mt-1">
                    Admin ve Veri Dil Sistemlerinin Durumu
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <!-- Session Durumu -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fa-solid fa-database me-2"></i>
                        Session Durumu
                    </h3>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <td><strong>admin_locale</strong></td>
                            <td>
                                <span class="badge bg-primary">{{ session('admin_locale', 'YOK') }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>site_locale</strong></td>
                            <td>
                                <span class="badge bg-success">{{ session('site_locale', 'YOK') }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>app locale</strong></td>
                            <td>
                                <span class="badge bg-info">{{ app()->getLocale() }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>config app.locale</strong></td>
                            <td>
                                <span class="badge bg-secondary">{{ config('app.locale') }}</span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Kullanıcı Tercihleri -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fa-solid fa-user me-2"></i>
                        Kullanıcı Tercihleri
                    </h3>
                </div>
                <div class="card-body">
                    @if(auth()->check())
                    <table class="table table-sm">
                        <tr>
                            <td><strong>admin_locale</strong></td>
                            <td>
                                <span class="badge bg-primary">{{ auth()->user()->admin_locale ?? 'YOK' }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>site_locale</strong></td>
                            <td>
                                <span class="badge bg-success">{{ auth()->user()->site_locale ?? 'YOK' }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>User ID</strong></td>
                            <td>{{ auth()->id() }}</td>
                        </tr>
                        <tr>
                            <td><strong>User Name</strong></td>
                            <td>{{ auth()->user()->name }}</td>
                        </tr>
                    </table>
                    @else
                    <div class="alert alert-warning">
                        <i class="fa-solid fa-exclamation-triangle me-2"></i>
                        Kullanıcı giriş yapmamış
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Mevcut Diller -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fa-solid fa-globe me-2"></i>
                        Admin Dilleri (admin_languages)
                    </h3>
                </div>
                <div class="card-body">
                    @php
                    $adminLanguages = collect();
                    try {
                        if (class_exists('Modules\LanguageManagement\App\Models\AdminLanguage')) {
                            $adminLanguages = \Modules\LanguageManagement\App\Models\AdminLanguage::where('is_active', true)->get();
                        }
                    } catch (\Exception $e) {
                        // Ignore
                    }
                    @endphp
                    
                    @if($adminLanguages->count() > 0)
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Name</th>
                                <th>Flag</th>
                                <th>Active</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($adminLanguages as $lang)
                            <tr class="{{ session('admin_locale') === $lang->code ? 'table-primary' : '' }}">
                                <td><strong>{{ $lang->code }}</strong></td>
                                <td>{{ $lang->native_name }}</td>
                                <td>{{ $lang->flag_icon }}</td>
                                <td>
                                    @if($lang->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                    @else
                                    <span class="badge bg-danger">Pasif</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <div class="alert alert-warning">
                        <i class="fa-solid fa-exclamation-triangle me-2"></i>
                        Admin dilleri bulunamadı
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Site Dilleri -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fa-solid fa-building me-2"></i>
                        Site Dilleri (tenant_languages)
                    </h3>
                </div>
                <div class="card-body">
                    @php
                    $siteLanguages = collect();
                    try {
                        // Tenant languages tablosundan çek
                        $siteLanguages = \DB::table('tenant_languages')
                            ->where('is_active', true)
                            ->orderBy('sort_order')
                            ->get();
                    } catch (\Exception $e) {
                        // Ignore
                    }
                    @endphp
                    
                    @if($siteLanguages->count() > 0)
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Name</th>
                                <th>Flag</th>
                                <th>Active</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($siteLanguages as $lang)
                            <tr class="{{ session('site_locale') === $lang->code ? 'table-success' : '' }}">
                                <td><strong>{{ $lang->code }}</strong></td>
                                <td>{{ $lang->native_name }}</td>
                                <td>{{ $lang->flag_icon }}</td>
                                <td>
                                    @if($lang->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                    @else
                                    <span class="badge bg-danger">Pasif</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <div class="alert alert-warning">
                        <i class="fa-solid fa-exclamation-triangle me-2"></i>
                        Site dilleri bulunamadı
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Test Butonları -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fa-solid fa-vial me-2"></i>
                        Test İşlemleri
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <h5>Admin Dil Testi (admin_languages)</h5>
                            <div class="btn-group w-100" role="group">
                                @foreach($adminLanguages as $lang)
                                <a href="{{ route('admin.language.switch', $lang->code) }}" 
                                   class="btn {{ session('admin_locale') === $lang->code ? 'btn-primary' : 'btn-outline-primary' }}">
                                    {{ $lang->flag_icon }} {{ $lang->native_name }}
                                </a>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h5>Veri Dil Testi (tenant_languages)</h5>
                            <div class="btn-group w-100" role="group">
                                @foreach($siteLanguages as $lang)
                                <button type="button" 
                                        class="btn {{ session('site_locale') === $lang->code ? 'btn-success' : 'btn-outline-success' }}"
                                        onclick="testSiteLanguage('{{ $lang->code }}')">
                                    {{ $lang->flag_icon }} {{ $lang->native_name }}
                                </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="row g-2">
                        <div class="col-md-4">
                            <button type="button" class="btn btn-warning w-100" onclick="clearSessions()">
                                <i class="fa-solid fa-broom me-2"></i>
                                Session Temizle
                            </button>
                        </div>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-info w-100" onclick="location.reload()">
                                <i class="fa-solid fa-refresh me-2"></i>
                                Sayfayı Yenile
                            </button>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('admin.page.index') }}" class="btn btn-secondary w-100">
                                <i class="fa-solid fa-file me-2"></i>
                                Page Modülüne Git
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- URL Bilgileri -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fa-solid fa-link me-2"></i>
                        URL ve Request Bilgileri
                    </h3>
                </div>
                <div class="card-body">
                    <div style="background: #1a1a1a; color: #ffffff; padding: 20px; border-radius: 8px; font-size: 14px; font-family: 'Consolas', 'Monaco', 'Courier New', monospace; white-space: pre-wrap; word-break: break-word; line-height: 1.6; border: 1px solid #333;">
<strong style="color: #ffff00;">=== SESSION DATA ===</strong>
admin_locale: <span style="color: #00ffff; font-weight: bold;">{{ session('admin_locale', 'NULL') }}</span>
site_locale: <span style="color: #00ff00; font-weight: bold;">{{ session('site_locale', 'NULL') }}</span>

<strong style="color: #ffff00;">=== USER DATA ===</strong>
@if(auth()->check())
user_id: <span style="color: #ff8800;">{{ auth()->id() }}</span>
admin_locale: <span style="color: #00ffff;">{{ auth()->user()->admin_locale ?? 'NULL' }}</span>
site_locale: <span style="color: #00ff00;">{{ auth()->user()->site_locale ?? 'NULL' }}</span>
@else
<span style="color: #ff4444;">NOT AUTHENTICATED</span>
@endif

<strong style="color: #ffff00;">=== APPLICATION ===</strong>
app()->getLocale(): <span style="color: #ff66ff;">{{ app()->getLocale() }}</span>
config('app.locale'): <span style="color: #ff66ff;">{{ config('app.locale') }}</span>

<strong style="color: #ffff00;">=== REQUEST ===</strong>
URL: <span style="color: #66ccff; word-break: break-all;">{{ request()->url() }}</span>
Full URL: <span style="color: #66ccff; word-break: break-all;">{{ request()->fullUrl() }}</span>
Route: <span style="color: #88ff88;">{{ request()->route()?->getName() ?? 'NULL' }}</span>
Method: <span style="color: #ffaa44;">{{ request()->method() }}</span>
Query: <span style="color: #cccccc;">{{ json_encode(request()->query(), JSON_PRETTY_PRINT) }}</span>

<strong style="color: #ffff00;">=== COOKIES ===</strong>
@foreach($_COOKIE as $key => $value)
<span style="color: #ff7777;">{{ $key }}</span>: <span style="color: #aaaaaa;">{{ substr($value, 0, 40) }}{{ strlen($value) > 40 ? '...' : '' }}</span>
@endforeach

<strong style="color: #ffff00;">=== TABLE CHECKS ===</strong>
@php
$tables = [];
try {
    $tables['admin_languages'] = \DB::table('admin_languages')->count();
} catch(\Exception $e) {
    $tables['admin_languages'] = 'ERROR: ' . $e->getMessage();
}

try {
    $tables['tenant_languages'] = \DB::table('tenant_languages')->count();
} catch(\Exception $e) {
    $tables['tenant_languages'] = 'ERROR: ' . $e->getMessage();
}
@endphp
admin_languages: <span style="color: {{ is_numeric($tables['admin_languages']) ? '#00ff88' : '#ff4444' }};">{{ $tables['admin_languages'] }}</span>
tenant_languages: <span style="color: {{ is_numeric($tables['tenant_languages']) ? '#00ff88' : '#ff4444' }};">{{ $tables['tenant_languages'] }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Console Logs -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fa-solid fa-terminal me-2"></i>
                        Browser Console Logs
                    </h3>
                </div>
                <div class="card-body">
                    <div id="console-output" style="background: #0d1117; color: #f0f6fc; padding: 20px; border-radius: 8px; font-family: 'Consolas', 'Monaco', 'Courier New', monospace; height: 300px; overflow-y: auto; border: 2px solid #30363d; font-size: 14px; line-height: 1.5;">
                        <div style="color: #58a6ff; font-weight: bold;">🚀 Console logs will appear here...</div>
                        <div style="color: #7c3aed; margin-top: 5px;">ℹ️  Debug system ready - click test buttons to see logs</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Console log capture
(function() {
    const consoleOutput = document.getElementById('console-output');
    const originalLog = console.log;
    const originalError = console.error;
    const originalWarn = console.warn;
    
    function addLog(type, args) {
        const time = new Date().toLocaleTimeString();
        const message = Array.from(args).map(arg => {
            if (typeof arg === 'object') {
                return JSON.stringify(arg, null, 2);
            }
            return String(arg);
        }).join(' ');
        
        const colors = {
            log: '#f0f6fc',
            error: '#ff6b6b',
            warn: '#ffd93d'
        };
        
        const div = document.createElement('div');
        div.style.color = colors[type];
        div.style.marginBottom = '2px';
        div.innerHTML = `<span style="color: #58a6ff; font-weight: bold;">[${time}]</span> ${message}`;
        consoleOutput.appendChild(div);
        consoleOutput.scrollTop = consoleOutput.scrollHeight;
    }
    
    console.log = function() {
        originalLog.apply(console, arguments);
        addLog('log', arguments);
    };
    
    console.error = function() {
        originalError.apply(console, arguments);
        addLog('error', arguments);
    };
    
    console.warn = function() {
        originalWarn.apply(console, arguments);
        addLog('warn', arguments);
    };
})();

function testSiteLanguage(locale) {
    console.log('🔄 Testing site language change to:', locale);
    
    if (window.Livewire) {
        // Try to find all Livewire components
        const components = window.Livewire.all();
        console.log('Found Livewire components:', components.length);
        
        // List all component fingerprints for debugging
        components.forEach((component, index) => {
            console.log(`Component ${index}:`, {
                fingerprint: component.fingerprint,
                name: component.fingerprint?.name,
                id: component.id
            });
        });
        
        // Find AdminLanguageSwitcher component - try multiple patterns
        let found = false;
        components.forEach(component => {
            const name = component.fingerprint?.name;
            if (name && (
                name.includes('admin-language-switcher') || 
                name.includes('AdminLanguageSwitcher') ||
                name.includes('languagemanagement') ||
                component.id.includes('languagemanagement')
            )) {
                console.log('✅ Found language component!', component.fingerprint);
                component.call('switchSiteLanguage', locale);
                found = true;
            }
        });
        
        if (!found) {
            console.error('❌ Language component not found! Trying direct method...');
            // Alternatif: fetch ile direkt server'a istek gönder
            fetch('/admin/debug-site-language/' + locale, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                console.log('✅ Server response:', data);
                location.reload(); // Sayfayı yenile
            })
            .catch(error => {
                console.error('❌ Server error:', error);
            });
        }
    } else {
        console.error('❌ Livewire not loaded!');
    }
}

function clearSessions() {
    console.log('🧹 Clearing sessions...');
    
    fetch('/admin/debug-clear-sessions', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        console.log('✅ Sessions cleared:', data);
        location.reload();
    })
    .catch(error => {
        console.error('❌ Error clearing sessions:', error);
    });
}

// Initial debug info
document.addEventListener('DOMContentLoaded', function() {
    console.log('=== 🐛 DİL SİSTEMİ DEBUG LOADED ===');
    console.log('Admin Locale (Session):', '{{ session("admin_locale", "NULL") }}');
    console.log('Site Locale (Session):', '{{ session("site_locale", "NULL") }}');
    console.log('App Locale:', '{{ app()->getLocale() }}');
    console.log('User Admin Locale:', '{{ auth()->check() ? (auth()->user()->admin_locale ?? "NULL") : "NOT_LOGGED_IN" }}');
    console.log('User Site Locale:', '{{ auth()->check() ? (auth()->user()->site_locale ?? "NULL") : "NOT_LOGGED_IN" }}');
    console.log('===================================');
    
    // List all Livewire components
    if (window.Livewire) {
        console.log('Livewire is loaded. Total components:', window.Livewire.all().length);
    }
});
</script>
@endsection
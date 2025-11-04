@php
// PERFORMANCE OPTIMIZATION - Cache variables to avoid repeated queries
static $cachedUser = null;
static $cachedTenantData = null;
static $cachedSystemLanguages = null;

// Single user fetch (instead of multiple auth() calls)
if ($cachedUser === null) {
$cachedUser = auth()->user();
}

// Tenant data caching
if ($cachedTenantData === null) {
$tenantId = null;
$isCentral = false;

if (function_exists('tenant_id')) {
$tenantId = tenant_id();
} elseif (app()->has('tenancy') && app('tenancy')->initialized) {
$tenantId = (string) tenant()->getTenantKey();
}

if (function_exists('is_central')) {
$isCentral = is_central();
} elseif (app()->has('tenancy')) {
$isCentral = !app('tenancy')->initialized;
}

$cachedTenantData = compact('tenantId', 'isCentral');
}

extract($cachedTenantData);

// Tenant domainde mysql bağlantısı kullanması için
if (!$isCentral && app()->has('tenancy') && app('tenancy')->initialized) {
config(['database.connections.tenant.driver' => 'mysql']);
DB::purge('tenant');
}

// Admin fallback locale'i tenant'tan al (modül display_name'leri için)
$adminFallbackLocale = 'tr'; // Sistem varsayılanı
if (function_exists('tenant') && tenant() && isset(tenant()->admin_default_locale)) {
$adminFallbackLocale = tenant()->admin_default_locale;
}

// Dil seçim mantığı: 1. Session 2. User admin dili 3. Tenant admin dili 4. fallback
$currentLocale = $adminFallbackLocale; // Tenant'ın varsayılan admin dili

// 1. Session'dan admin dili (en güncel)
if (session('admin_locale')) {
$currentLocale = session('admin_locale');
}
// 2. Kullanıcının kaydettiği admin dil tercihi (cached user)
elseif ($cachedUser && $cachedUser->admin_locale) {
$currentLocale = $cachedUser->admin_locale;
}

// Admin interface için her zaman fallback locale'i de ayarla (modül isimleri için)
$originalLocale = app()->getLocale();
app()->setLocale($adminFallbackLocale);

// Sistem dillerini al (cached)
if ($cachedSystemLanguages === null) {
$cachedSystemLanguages = collect();
if (class_exists('Modules\LanguageManagement\App\Models\AdminLanguage')) {
$cachedSystemLanguages = \Modules\LanguageManagement\App\Models\AdminLanguage::where('is_active', true)
->orderBy('id')
->get();
}
}
$systemLanguages = $cachedSystemLanguages;

// Mevcut dil bilgisi
$currentLanguage = $systemLanguages->firstWhere('code', $currentLocale);

// Modül servisini çağır
$moduleService = app(App\Services\ModuleService::class);

// Modülleri her zaman taze şekilde al (önbellek yok)
$modules = $moduleService->getModulesByTenant($tenantId);

// Modülleri tipine göre grupla
$groupedModules = $moduleService->groupModulesByType($modules);

// Aktif segment/tip bilgisini al
$activeType = request()->segment(2);

// Settings helper ile site başlığı ve logo bilgilerini al
$siteTitle = settings('site_title', config('app.name'));

// Original locale'i geri yükle
app()->setLocale($originalLocale);
@endphp

<header class="navbar navbar-expand-xl d-print-none">
    <div class="container">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu"
            aria-controls="navbar-menu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <h1 class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal pe-0 pe-md-3">
            <a href="{{ route('admin.dashboard') }}">
                {{ $siteTitle }}
                @if(!$isCentral && $tenantId)
                @php
                $tenant = \App\Models\Tenant::find($tenantId);
                $tenantName = $tenant ? $tenant->name ?? $tenant->id : '';
                @endphp
                <span class="small text-muted ms-2">({{ $tenantName }})</span>
                @endif
            </a>
        </h1>

        <div class="navbar-nav flex-row order-md-last align-items-center">
            <!-- Desktop: Tüm butonlar görünür -->
            <div class="d-none d-lg-flex align-items-center">
                <!-- Anasayfa Butonu -->
                <div class="nav-item me-2">
                    <a href="{{ url('/') }}" target="_blank"
                        class="nav-link d-flex align-items-center justify-content-center" data-bs-toggle="tooltip"
                        data-bs-placement="bottom" title="{{ __('admin.homepage') }}"
                        style="width: 40px; height: 40px; border-radius: 0.375rem;">
                        <i class="fa-solid fa-home" style="font-size: 18px;"></i>
                    </a>
                </div>


                <!-- NAVBAR TEMA SWITCH - KALDIRMA! Theme Builder'daki sistem modu ile aynı işlevi görüyor -->
                {{--
                <div class="nav-item me-2">
                    <div class="d-flex align-items-center justify-content-center"
                        style="width: 40px; height: 40px; border-radius: 0.375rem; margin-top: -2px;"
                        data-bs-toggle="tooltip" data-bs-placement="bottom" title="{{ __('admin.theme_mode') }}">
                        <div class="theme-mode"
                            data-theme="{{ isset($_COOKIE['dark']) && $_COOKIE['dark'] == '1' ? 'dark' : 'light' }}">
                            <input type="checkbox" id="switch" class="dark-switch" {{ isset($_COOKIE['dark']) &&
                                $_COOKIE['dark']=='1' ? 'checked' : '' }}>
                            <div class="app">
                                <div class="switch-content">
                                    <div class="switch-label"></div>
                                    <label for="switch">
                                        <div class="toggle"></div>
                                        <div class="names">
                                            <p class="light"><i class="fa-solid fa-moon"></i></p>
                                            <p class="dark"><i class="fa-solid fa-sun"></i></p>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                --}}


                <!-- Son Aktiviteler Dropdown -->
                <div class="nav-item dropdown me-2" id="activities-dropdown" data-bs-toggle="tooltip"
                    data-bs-placement="bottom">
                    <a href="#" class="nav-link d-flex align-items-center justify-content-center"
                        data-bs-toggle="dropdown" tabindex="-1" aria-expanded="false"
                        style="width: 40px; height: 40px; border-radius: 0.375rem;">
                        <i class="fa-solid fa-bell" style="font-size: 18px;"></i>
                        @php
                        // PERFORMANCE: Cache activity count to avoid expensive queries
                        static $cachedActivityData = null;
                        if ($cachedActivityData === null) {
                        $lastReadTime = $_COOKIE['last_activity_read'] ?? 0;
                        $unreadActivitiesCount = \Spatie\Activitylog\Models\Activity::where('created_at', '>',
                        date('Y-m-d H:i:s', $lastReadTime))->count();
                        $cachedActivityData = compact('lastReadTime', 'unreadActivitiesCount');
                        }
                        extract($cachedActivityData);
                        @endphp
                        @if($unreadActivitiesCount > 0)
                        <span class="badge bg-red">{{ $unreadActivitiesCount }}</span>
                        @endif
                    </a>
                    <div class="dropdown-menu dropdown-menu-arrow dropdown-menu-end dropdown-menu-card">
                        <div class="card">
                            <div class="card-header d-flex">
                                <h3 class="card-title">{{ __('admin.recent_activities') }}</h3>
                                <button type="button" class="btn-close ms-auto" data-bs-dismiss="dropdown"
                                    aria-label="Kapat"></button>
                            </div>
                            <div class="list-group list-group-flush list-group-hoverable">
                                @php
                                // PERFORMANCE: Cache activities with Redis (5 dakika) + Static cache
                                static $cachedActivities = null;
                                if ($cachedActivities === null) {
                                $cacheKey = 'admin_nav_activities_' . ($cachedUser ? $cachedUser->id : 'guest');
                                $cachedActivities = cache()->remember($cacheKey, 300, function () {
                                return \Spatie\Activitylog\Models\Activity::with('causer')
                                ->latest()
                                ->take(6)
                                ->get();
                                });
                                }
                                $activities = $cachedActivities;
                                @endphp
                                @forelse($activities as $activity)
                                <div class="list-group-item py-3">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <span class="avatar avatar-sm bg-primary text-white rounded-circle">
                                                <i class="fa-solid fa-{{ $activity->created_at->diffInMinutes(now()) < 5 ? 'bolt' : 'user' }}"
                                                    style="font-size: 12px;"></i>
                                            </span>
                                        </div>
                                        <div class="col text-truncate">
                                            <div class="fw-bold d-block">
                                                {{ ucfirst($activity->description) }}
                                            </div>
                                            <div class="d-block text-muted small">
                                                {{ $activity->causer->name ?? 'Sistem' }} • {{
                                                $activity->created_at->diffForHumans() }}
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <a href="{{ route('admin.usermanagement.user.activity.logs', ['id' => $activity->causer_id ?? 0]) }}"
                                                class="btn btn-ghost-primary btn-sm">
                                                <i class="fa-solid fa-arrow-right" style="font-size: 12px;"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <div class="list-group-item py-4">
                                    <div class="text-center text-muted">
                                        <i class="fa-solid fa-inbox mb-2" style="font-size: 24px;"></i>
                                        <div>{{ __('admin.no_activities_yet') }}</div>
                                    </div>
                                </div>
                                @endforelse
                            </div>
                            <div class="card-body">
                                <a href="{{ route('admin.usermanagement.activity.logs') }}"
                                    class="btn btn-outline-primary w-100">
                                    {{ __('admin.view_all_activities') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Hızlı İşlemler Dropdown -->
                <div class="nav-item dropdown me-3" data-bs-toggle="tooltip" data-bs-placement="bottom"
                    id="quick-actions-dropdown">
                    <a href="#" class="nav-link d-flex align-items-center justify-content-center"
                        data-bs-toggle="dropdown" tabindex="-1" aria-expanded="false"
                        style="width: 40px; height: 40px; border-radius: 0.375rem;">
                        <i class="fa-solid fa-grid-2" style="font-size: 18px;"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-arrow dropdown-menu-end dropdown-menu-card">
                        <div class="card">
                            <div class="card-header d-flex">
                                <h3 class="card-title">{{ __('admin.quick_actions') }}</h3>
                                <button type="button" class="btn-close ms-auto" data-bs-dismiss="dropdown"
                                    aria-label="Kapat"></button>
                            </div>
                            <div class="card-body p-3">
                                <div class="row g-3">
                                    @if($isCentral)
                                    <div class="col-4">
                                        <a href="#" class="d-flex flex-column text-center py-3 px-2 quick-action-item"
                                            data-bs-toggle="offcanvas" data-bs-target="#offcanvasTheme">
                                            <i class="fa-solid fa-brush mb-2" style="font-size: 28px;"></i>
                                            <span class="nav-link-title">{{ __('admin.theme_settings') }}</span>
                                        </a>
                                    </div>
                                    <div class="col-4">
                                        <a href="#" class="d-flex flex-column text-center py-3 px-2 quick-action-item"
                                            onclick="clearCache(this); return false;">
                                            <i class="fa-solid fa-broom mb-2" style="font-size: 28px;"></i>
                                            <span class="nav-link-title">Cache Temizle</span>
                                        </a>
                                    </div>
                                    <div class="col-4">
                                        <a href="#" class="d-flex flex-column text-center py-3 px-2 quick-action-item"
                                            onclick="clearSystemCache(this); return false;">
                                            <i class="fa-solid fa-trash-can mb-2" style="font-size: 28px;"></i>
                                            <span class="nav-link-title">Sistem Cache</span>
                                        </a>
                                    </div>
                                    <div class="col-4">
                                        <a href="{{ route('admin.modulemanagement.index') }}"
                                            class="d-flex flex-column text-center py-3 px-2 quick-action-item">
                                            <i class="fa-solid fa-puzzle-piece mb-2" style="font-size: 28px;"></i>
                                            <span class="nav-link-title">{{ __('admin.modules') }}</span>
                                        </a>
                                    </div>
                                    @else
                                    <div class="col-4">
                                        <a href="#" class="d-flex flex-column text-center py-3 px-2 quick-action-item"
                                            data-bs-toggle="offcanvas" data-bs-target="#offcanvasTheme">
                                            <i class="fa-solid fa-brush mb-2" style="font-size: 28px;"></i>
                                            <span class="nav-link-title">{{ __('admin.theme_settings') }}</span>
                                        </a>
                                    </div>
                                    <div class="col-4">
                                        @livewire('admin.cache-clear-buttons')
                                    </div>
                                    <div class="col-4">
                                        <a href="{{ route('admin.modulemanagement.index') }}"
                                            class="d-flex flex-column text-center py-3 px-2 quick-action-item">
                                            <i class="fa-solid fa-puzzle-piece mb-2" style="font-size: 28px;"></i>
                                            <span class="nav-link-title">{{ __('admin.modules') }}</span>
                                        </a>
                                    </div>
                                    <div class="col-4">
                                        <a href="{{ route('admin.usermanagement.index') }}"
                                            class="d-flex flex-column text-center py-3 px-2 quick-action-item">
                                            <i class="fa-solid fa-users mb-2" style="font-size: 28px;"></i>
                                            <span class="nav-link-title">{{ __('admin.users') }}</span>
                                        </a>
                                    </div>
                                    @endif
                                    <div class="col-4">
                                        <a href="{{ route('admin.studio.index') }}"
                                            class="d-flex flex-column text-center py-3 px-2 quick-action-item">
                                            <i class="fa-solid fa-palette mb-2" style="font-size: 28px;"></i>
                                            <span class="nav-link-title">Studio</span>
                                        </a>
                                    </div>
                                    <div class="col-4">
                                        <a href="{{ route('admin.settingmanagement.index') }}"
                                            class="d-flex flex-column text-center py-3 px-2 quick-action-item">
                                            <i class="fa-solid fa-sliders mb-2" style="font-size: 28px;"></i>
                                            <span class="nav-link-title">{{ __('admin.settings') }}</span>
                                        </a>
                                    </div>
                                    <div class="col-4">
                                        <a href="{{ url('/horizon') }}" target="_blank"
                                            class="d-flex flex-column text-center py-3 px-2 quick-action-item">
                                            <i class="fa-solid fa-tasks mb-2" style="font-size: 28px;"></i>
                                            <span class="nav-link-title">Horizon</span>
                                        </a>
                                    </div>
                                    <div class="col-4">
                                        <a href="{{ url('/telescope') }}" target="_blank"
                                            class="d-flex flex-column text-center py-3 px-2 quick-action-item">
                                            <i class="fa-solid fa-telescope mb-2" style="font-size: 28px;"></i>
                                            <span class="nav-link-title">Telescope</span>
                                        </a>
                                    </div>
                                    <div class="col-4">
                                        <a href="{{ url('/pulse') }}" target="_blank"
                                            class="d-flex flex-column text-center py-3 px-2 quick-action-item">
                                            <i class="fa-solid fa-heartbeat mb-2" style="font-size: 28px;"></i>
                                            <span class="nav-link-title">Pulse</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mobile/Tablet: Tek buton -->
            <div class="d-lg-none nav-item dropdown me-3">
                <a href="#" class="nav-link d-flex align-items-center justify-content-center"
                    data-bs-toggle="dropdown" tabindex="-1" data-bs-auto-close="outside" aria-expanded="false"
                    style="width: 40px; height: 40px; border-radius: 0.375rem;">
                    <i class="fa-solid fa-grid-2" style="font-size: 18px;"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-arrow dropdown-menu-end dropdown-menu-card" style="width: 280px; max-height: 500px; overflow-y: auto;">
                    <div class="card border-0">
                        <div class="card-header border-0 bg-primary text-white">
                            <div class="card-title text-white mb-0">{{ __('admin.admin_actions') }}</div>
                        </div>
                        <div class="card-body p-3">
                            <div class="row g-3">
                                <!-- Anasayfa -->
                                <div class="col-6">
                                    <a href="{{ url('/') }}" target="_blank"
                                        class="d-flex flex-column text-center p-3 mobile-quick-action">
                                        <i class="fa-solid fa-home mb-2" style="font-size: 24px;"></i>
                                        <small class="fw-bold">{{ __('admin.homepage') }}</small>
                                    </a>
                                </div>
                                <!-- Cache Temizle -->
                                <div class="col-6">
                                    <a href="#"
                                        class="d-flex flex-column text-center p-3 mobile-quick-action"
                                        onclick="clearCache(this); return false;">
                                        <i class="fa-solid fa-broom mb-2" style="font-size: 24px;"></i>
                                        <small class="fw-bold">Cache Temizle</small>
                                    </a>
                                </div>
                                @if($isCentral)
                                <!-- Sistem Cache -->
                                <div class="col-6">
                                    <a href="#"
                                        class="d-flex flex-column text-center p-3 mobile-quick-action"
                                        onclick="clearSystemCache(this); return false;">
                                        <i class="fa-solid fa-trash-can mb-2 text-danger" style="font-size: 24px;"></i>
                                        <small class="fw-bold">Sistem Cache</small>
                                    </a>
                                </div>
                                @endif
                                <!-- Tema -->
                                <div class="col-6">
                                    <a href="#"
                                        class="d-flex flex-column text-center p-3 mobile-quick-action"
                                        data-bs-toggle="offcanvas" data-bs-target="#offcanvasTheme">
                                        <i class="fa-solid fa-brush mb-2" style="font-size: 24px;"></i>
                                        <small class="fw-bold">{{ __('admin.theme_settings') }}</small>
                                    </a>
                                </div>
                                <!-- Aktiviteler -->
                                <div class="col-6">
                                    <a href="{{ route('admin.usermanagement.activity.logs') }}"
                                        class="d-flex flex-column text-center p-3 mobile-quick-action">
                                        <i class="fa-solid fa-bell mb-2" style="font-size: 24px;"></i>
                                        <small class="fw-bold">{{ __('admin.recent_activities') }}</small>
                                    </a>
                                </div>
                                <!-- Modüller -->
                                <div class="col-6">
                                    <a href="{{ route('admin.modulemanagement.index') }}"
                                        class="d-flex flex-column text-center p-3 mobile-quick-action">
                                        <i class="fa-solid fa-puzzle-piece mb-2" style="font-size: 24px;"></i>
                                        <small class="fw-bold">{{ __('admin.module_management') }}</small>
                                    </a>
                                </div>
                                @if(!$isCentral)
                                <!-- Kullanıcılar -->
                                <div class="col-6">
                                    <a href="{{ route('admin.usermanagement.index') }}"
                                        class="d-flex flex-column text-center p-3 mobile-quick-action">
                                        <i class="fa-solid fa-users mb-2" style="font-size: 24px;"></i>
                                        <small class="fw-bold">{{ __('admin.user_management') }}</small>
                                    </a>
                                </div>
                                @endif
                                <!-- Studio -->
                                <div class="col-6">
                                    <a href="{{ route('admin.studio.index') }}"
                                        class="d-flex flex-column text-center p-3 mobile-quick-action">
                                        <i class="fa-solid fa-palette mb-2" style="font-size: 24px;"></i>
                                        <small class="fw-bold">{{ __('admin.studio_editor') }}</small>
                                    </a>
                                </div>
                                <!-- Ayarlar -->
                                <div class="col-6">
                                    <a href="{{ route('admin.settingmanagement.index') }}"
                                        class="d-flex flex-column text-center p-3 mobile-quick-action">
                                        <i class="fa-solid fa-sliders mb-2" style="font-size: 24px;"></i>
                                        <small class="fw-bold">{{ __('admin.system_settings') }}</small>
                                    </a>
                                </div>
                                <!-- Dil Seçimi (Mobil) -->
                                <div class="col-6">
                                    <div class="dropdown">
                                        <a href="#"
                                            class="d-flex flex-column text-center p-3 mobile-quick-action"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fa-solid fa-language mb-2" style="font-size: 24px;"></i>
                                            <small class="fw-bold">{{ __('admin.language_settings') }}</small>
                                        </a>
                                        @livewire('languagemanagement::admin-language-switcher', ['isMobile' => true])
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dil Değiştirme Dropdown (Livewire) - Admin Context -->
            @livewire('languagemanagement::admin-language-switcher')

            <div class="nav-item dropdown">
                <a href="#" class="nav-link d-flex lh-1 p-0 px-2 align-items-center" data-bs-toggle="dropdown"
                    aria-label="Open user menu" data-bs-toggle="tooltip" data-bs-placement="bottom"
                    title="{{ __('admin.user_menu') }}">
                    <span class="avatar avatar-sm d-flex align-items-center justify-content-center"
                        style="width: 40px; height: 40px; border-radius: 0.375rem;">
                        <i class="fa-solid fa-user" style="font-size: 18px;"></i>
                    </span>
                    <div class="d-none d-xl-block ps-2">
                        <div>{{ $cachedUser->name }}</div>
                        <div class="mt-1 small text-secondary">
                            @php
                            // PERFORMANCE: Use cached user instead of Auth::user()
                            $roleName = 'Kullanıcı';

                            if ($cachedUser->hasCachedRole('root')) {
                            $roleName = 'Root';
                            } elseif ($cachedUser->hasCachedRole('admin')) {
                            $roleName = 'Admin';
                            } elseif ($cachedUser->hasCachedRole('editor')) {
                            $roleName = 'Editör';
                            }
                            @endphp
                            {{ $roleName }}
                        </div>
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <!-- 🎯 EN ÖNEMLİ BİLGİ: KALAN KREDİ -->
                    @if(function_exists('ai_get_credit_balance'))
                    <div class="dropdown-item-text">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <div class="avatar bg-primary-lt">
                                    💰
                                </div>
                            </div>
                            <div>
                                <div class="fw-bold text-primary">{{ format_credit(ai_get_credit_balance()) }}</div>
                                <div class="text-muted small">Mevcut Bakiye</div>
                            </div>
                        </div>
                    </div>
                    <div class="dropdown-divider"></div>
                    @endif
                    
                    <a href="{{ route('admin.usermanagement.user.activity.logs', ['id' => $cachedUser->id]) }}"
                        class="dropdown-item">{{ __('admin.my_activities') }}</a>
                    <a href="{{ route('admin.usermanagement.manage', ['id' => $cachedUser->id]) }}"
                        class="dropdown-item">{{ __('admin.my_profile') }}</a>
                    <div class="dropdown-divider"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item">{{ __('admin.logout') }}</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="collapse navbar-collapse" id="navbar-menu">
            <div class="d-flex flex-column flex-md-row flex-fill align-items-stretch align-items-md-center">
                <ul class="navbar-nav">
                    @if($groupedModules->has('content') && $groupedModules['content']->count() > 0)
                    <li class="nav-item {{ $activeType == 'content' ? 'active' : '' }} dropdown">
                        <a class="nav-link dropdown-toggle" href="#navbar-content" data-bs-toggle="dropdown"
                            data-bs-auto-close="outside" role="button" aria-expanded="false">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <i class="fa-solid fa-file-alt" style="font-size: 18px;"></i>
                            </span>
                            <span class="nav-link-title">{{ __('admin.content') }}</span>
                        </a>
                        <div class="dropdown-menu">
                            @foreach($groupedModules['content'] as $module)
                            <a class="dropdown-item"
                                href="{{ route('admin.' . strtolower($module->name) . '.index') }}">
                                {{ $module->display_name }}
                            </a>
                            @endforeach
                        </div>
                    </li>
                    @endif

                    @if($groupedModules->has('ai') && $groupedModules['ai']->count() > 0)
                    <li class="nav-item {{ $activeType == 'ai' ? 'active' : '' }} dropdown">
                        <a class="nav-link dropdown-toggle" href="#navbar-ai" data-bs-toggle="dropdown"
                            data-bs-auto-close="outside" role="button" aria-expanded="false">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <i class="fa-solid fa-stars" style="font-size: 18px;"></i>
                            </span>
                            <span class="nav-link-title">{{ __('admin.artificial_intelligence') }}</span>
                        </a>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="{{ route('admin.ai.index') }}">
                                <i class="fa fa-comment me-2"></i> {{ __('ai::admin.conversations') }}
                            </a>
                        </div>
                    </li>
                    @endif

                    @if($groupedModules->has('widget') && $groupedModules['widget']->count() > 0)
                    <li class="nav-item {{ $activeType == 'widget' ? 'active' : '' }} dropdown">
                        <a class="nav-link dropdown-toggle" href="#navbar-widget" data-bs-toggle="dropdown"
                            data-bs-auto-close="outside" role="button" aria-expanded="false">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <i class="fa-solid fa-puzzle-piece" style="font-size: 18px;"></i>
                            </span>
                            <span class="nav-link-title">{{ __('admin.widget') }}</span>
                        </a>
                        <div class="dropdown-menu">
                            @foreach($groupedModules['widget'] as $module)
                            <a class="dropdown-item"
                                href="{{ route('admin.' . strtolower($module->name) . '.index') }}">
                                {{ $module->display_name }}
                            </a>
                            @endforeach
                        </div>
                    </li>
                    @endif

                    @if($groupedModules->has('management') && $groupedModules['management']->count() > 0)
                    <li class="nav-item {{ $activeType == 'management' ? 'active' : '' }} dropdown">
                        <a class="nav-link dropdown-toggle" href="#navbar-management" data-bs-toggle="dropdown"
                            data-bs-auto-close="outside" role="button" aria-expanded="false">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <i class="fa-solid fa-cogs" style="font-size: 18px;"></i>
                            </span>
                            <span class="nav-link-title">{{ __('admin.management') }}</span>
                        </a>
                        <div class="dropdown-menu">
                            @foreach($groupedModules['management'] as $module)
                            <a class="dropdown-item"
                                href="{{ route('admin.' . strtolower($module->name) . '.index') }}">
                                {{ $module->display_name }}
                            </a>
                            @endforeach
                        </div>
                    </li>
                    @endif

                    @if($groupedModules->has('system') && $groupedModules['system']->count() > 0)
                    <li class="nav-item {{ $activeType == 'system' ? 'active' : '' }} dropdown">
                        <a class="nav-link dropdown-toggle" href="#navbar-system" data-bs-toggle="dropdown"
                            data-bs-auto-close="outside" role="button" aria-expanded="false">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <i class="fa-solid fa-server" style="font-size: 18px;"></i>
                            </span>
                            <span class="nav-link-title">{{ __('admin.system') }}</span>
                        </a>
                        <div class="dropdown-menu">
                            @foreach($groupedModules['system'] as $module)
                            <a class="dropdown-item"
                                href="{{ route('admin.' . strtolower($module->name) . '.index') }}">
                                {{ $module->display_name }}
                            </a>
                            @endforeach
                        </div>
                    </li>
                    @endif

                </ul>
            </div>
        </div>
    </div>
</header>

@include('admin.components.theme-builder')

<script>
    document.addEventListener('DOMContentLoaded', function() {
    // Aktiviteler dropdown'ına tıklayınca badge'i sıfırla
    const activitiesDropdown = document.getElementById('activities-dropdown');
    if (activitiesDropdown) {
        activitiesDropdown.addEventListener('shown.bs.dropdown', function() {
            // Cookie'yi güncelle (şimdiki zaman)
            const now = Math.floor(Date.now() / 1000);
            document.cookie = `last_activity_read=${now}; path=/; max-age=${60*60*24*30}`; // 30 gün
            
            // Badge'i gizle
            const badge = this.querySelector('.badge');
            if (badge) {
                badge.style.display = 'none';
            }
        });
    }
});

// Cache Clear Functions
function clearCache(button) {
    const icon = button.querySelector('i');
    const originalIcon = icon.className;
    
    // Show loading state
    icon.className = 'fa-solid fa-spinner fa-spin mb-2';
    button.style.pointerEvents = 'none';
    
    // Change quick actions dropdown icon
    const quickActionsIcon = document.querySelector('.fa-grid-2');
    if (quickActionsIcon) {
        quickActionsIcon.className = 'fa-solid fa-spinner fa-spin';
    }
    
    // Make AJAX request
    fetch('/admin/cache/clear', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        // Restore original state
        icon.className = originalIcon;
        button.style.pointerEvents = 'auto';
        
        // Restore quick actions icon
        if (quickActionsIcon) {
            quickActionsIcon.className = 'fa-solid fa-grid-2';
        }
        
        // Show toast notification
        if (typeof window.showToast === 'function') {
            window.showToast('Başarılı', 'Cache temizlendi', 'success');
        }
    })
    .catch(error => {
        // Restore original state on error
        icon.className = originalIcon;
        button.style.pointerEvents = 'auto';
        
        if (quickActionsIcon) {
            quickActionsIcon.className = 'fa-solid fa-grid-2';
        }
        
        if (typeof window.showToast === 'function') {
            window.showToast('Hata', 'Cache temizleme başarısız', 'error');
        }
    });
}

function clearSystemCache(button) {
    const icon = button.querySelector('i');
    const originalIcon = icon.className;
    
    // Show loading state
    icon.className = 'fa-solid fa-spinner fa-spin mb-2';
    button.style.pointerEvents = 'none';
    
    // Change quick actions dropdown icon
    const quickActionsIcon = document.querySelector('.fa-grid-2');
    if (quickActionsIcon) {
        quickActionsIcon.className = 'fa-solid fa-spinner fa-spin';
    }
    
    // Make AJAX request
    fetch('/admin/cache/clear-all', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        // Restore original state
        icon.className = originalIcon;
        button.style.pointerEvents = 'auto';
        
        // Restore quick actions icon
        if (quickActionsIcon) {
            quickActionsIcon.className = 'fa-solid fa-grid-2';
        }
        
        // Show toast notification
        if (typeof window.showToast === 'function') {
            window.showToast('Başarılı', 'Sistem cache temizlendi', 'success');
        }
    })
    .catch(error => {
        // Restore original state on error
        icon.className = originalIcon;
        button.style.pointerEvents = 'auto';
        
        if (quickActionsIcon) {
            quickActionsIcon.className = 'fa-solid fa-grid-2';
        }
        
        if (typeof window.showToast === 'function') {
            window.showToast('Hata', 'Cache temizleme başarısız', 'error');
        }
    });
}
</script>
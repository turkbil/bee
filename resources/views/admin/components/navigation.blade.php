@php
// Tenant ID'yi al
$tenantId = null;
$isCentral = false;

// Helper fonksiyonları kontrol et ve kullan
if (function_exists('tenant_id')) {
$tenantId = tenant_id();
} elseif (app()->has('tenancy') && app('tenancy')->initialized) {
$tenantId = tenant()->getTenantKey();
}

if (function_exists('is_central')) {
$isCentral = is_central();
} elseif (app()->has('tenancy')) {
$isCentral = !app('tenancy')->initialized;
}

// Tenant domainde mysql bağlantısı kullanması için
if (!$isCentral && app()->has('tenancy') && app('tenancy')->initialized) {
config(['database.connections.tenant.driver' => 'mysql']);
DB::purge('tenant');
}

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
@endphp

<header class="navbar navbar-expand-md d-print-none">
    <div class="container">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu" aria-controls="navbar-menu" aria-expanded="false" aria-label="Toggle navigation">
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
            <div class="d-none d-md-flex align-items-center">
                <!-- Tema Ayarları Butonu -->
                <div class="nav-item me-2">
                    <a href="#" class="nav-link d-flex align-items-center justify-content-center" data-bs-toggle="offcanvas"
                        data-bs-target="#offcanvasTheme" data-bs-toggle="tooltip" data-bs-placement="bottom" 
                        title="Tema Ayarları" 
                        style="width: 40px; height: 40px; border-radius: 0.375rem;">
                        <i class="fa-solid fa-brush" style="font-size: 18px;"></i>
                    </a>
                </div>

                <!-- Gece/Gündüz Mod Switch'i -->
                <div class="nav-item me-2">
                    <div class="d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; border-radius: 0.375rem; margin-top: -2px;" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Tema Modu">
                        <div class="theme-mode" data-theme="light">
                            <input type="checkbox" id="switch" class="dark-switch">
                            <div class="app">
                                <div class="switch-content">
                                    <div class="switch-label"></div>
                                    <label for="switch">
                                        <div class="toggle"></div>
                                        <div class="names">
                                            <p class="light"><i class="fa-solid fa-moon"></i></p>
                                            <p class="dark"><i class="fa-solid fa-sun"></i></p>
                                            <p class="auto"><i class="fa-solid fa-circle-half-stroke"></i></p>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Son Aktiviteler Dropdown -->
                <div class="nav-item dropdown me-2">
                    <a href="#" class="nav-link d-flex align-items-center justify-content-center" data-bs-toggle="dropdown" tabindex="-1" data-bs-auto-close="outside" aria-expanded="false" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Son Aktiviteler" style="width: 40px; height: 40px; border-radius: 0.375rem;">
                        <i class="fa-solid fa-bell" style="font-size: 18px;"></i>
                        @php
                        // Son 5 dakikadaki aktivite sayısı  
                        $recentActivityCount = \Spatie\Activitylog\Models\Activity::where('created_at', '>=', now()->subMinutes(5))->count();
                        @endphp
                        @if($recentActivityCount > 0)
                        <span class="badge bg-red">{{ $recentActivityCount }}</span>
                        @endif
                    </a>
                    <div class="dropdown-menu dropdown-menu-arrow dropdown-menu-end dropdown-menu-card">
                        <div class="card">
                            <div class="card-header d-flex">
                                <h3 class="card-title">Son Aktiviteler</h3>
                                <div class="btn-close ms-auto" data-bs-dismiss="dropdown"></div>
                            </div>
                            <div class="list-group list-group-flush list-group-hoverable">
                                @php
                                // Son 6 aktiviteyi al
                                $activities = \Spatie\Activitylog\Models\Activity::with('causer')
                                    ->latest()
                                    ->take(6)
                                    ->get();
                                @endphp
                                @forelse($activities as $activity)
                                <div class="list-group-item py-3">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <span class="avatar avatar-sm bg-primary text-white rounded-circle">
                                                <i class="fa-solid fa-{{ $activity->created_at->diffInMinutes(now()) < 5 ? 'bolt' : 'user' }}" style="font-size: 12px;"></i>
                                            </span>
                                        </div>
                                        <div class="col text-truncate">
                                            <div class="fw-bold d-block">
                                                {{ ucfirst($activity->description) }}
                                            </div>
                                            <div class="d-block text-muted small">
                                                {{ $activity->causer->name ?? 'Sistem' }} • {{ $activity->created_at->diffForHumans() }}
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <a href="{{ route('admin.usermanagement.user.activity.logs', ['id' => $activity->causer_id ?? 0]) }}" class="btn btn-ghost-primary btn-sm">
                                                <i class="fa-solid fa-arrow-right" style="font-size: 12px;"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <div class="list-group-item py-4">
                                    <div class="text-center text-muted">
                                        <i class="fa-solid fa-inbox mb-2" style="font-size: 24px;"></i>
                                        <div>Henüz aktivite kaydı yok</div>
                                    </div>
                                </div>
                                @endforelse
                            </div>
                            <div class="card-body">
                                <a href="{{ route('admin.usermanagement.activity.logs') }}" class="btn btn-outline-primary w-100">
                                    Tüm Aktiviteleri Görüntüle
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Hızlı İşlemler Dropdown -->
                <div class="nav-item dropdown me-3">
                    <a href="#" class="nav-link d-flex align-items-center justify-content-center" data-bs-toggle="dropdown" tabindex="-1" data-bs-auto-close="outside" aria-expanded="false" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Hızlı İşlemler" style="width: 40px; height: 40px; border-radius: 0.375rem;">
                        <i class="fa-solid fa-grid-2" style="font-size: 18px;"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-arrow dropdown-menu-end dropdown-menu-card">
                        <div class="card">
                            <div class="card-header">
                                <div class="card-title">Hızlı İşlemler</div>
                                <div class="card-actions btn-actions">
                                    <a href="#" class="btn-action" data-bs-toggle="offcanvas" data-bs-target="#offcanvasTheme">
                                        <i class="fa-solid fa-brush" style="font-size: 18px;"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="card-body p-3">
                                <div class="row g-3">
                                    @if($isCentral)
                                    <div class="col-4">
                                        <a href="#" class="d-flex flex-column text-center py-3 px-2 quick-action-item cache-clear-btn" data-action="clear">
                                            <i class="fa-solid fa-broom mb-2" style="font-size: 28px;"></i>
                                            <span class="nav-link-title">Cache<br>Temizle</span>
                                        </a>
                                    </div>
                                    <div class="col-4">
                                        <a href="#" class="d-flex flex-column text-center py-3 px-2 quick-action-item cache-clear-all-btn" data-action="clear-all">
                                            <i class="fa-solid fa-trash-can mb-2" style="font-size: 28px;"></i>
                                            <span class="nav-link-title">Sistem<br>Cache</span>
                                        </a>
                                    </div>
                                    <div class="col-4">
                                        <a href="{{ route('admin.modulemanagement.index') }}" class="d-flex flex-column text-center py-3 px-2 quick-action-item">
                                            <i class="fa-solid fa-puzzle-piece mb-2" style="font-size: 28px;"></i>
                                            <span class="nav-link-title">Modüller</span>
                                        </a>
                                    </div>
                                    @else
                                    <div class="col-4">
                                        <a href="#" class="d-flex flex-column text-center py-3 px-2 quick-action-item cache-clear-btn" data-action="clear">
                                            <i class="fa-solid fa-broom mb-2" style="font-size: 28px;"></i>
                                            <span class="nav-link-title">Cache<br>Temizle</span>
                                        </a>
                                    </div>
                                    <div class="col-4">
                                        <a href="{{ route('admin.modulemanagement.index') }}" class="d-flex flex-column text-center py-3 px-2 quick-action-item">
                                            <i class="fa-solid fa-puzzle-piece mb-2" style="font-size: 28px;"></i>
                                            <span class="nav-link-title">Modüller</span>
                                        </a>
                                    </div>
                                    <div class="col-4">
                                        <a href="{{ route('admin.usermanagement.index') }}" class="d-flex flex-column text-center py-3 px-2 quick-action-item">
                                            <i class="fa-solid fa-users mb-2" style="font-size: 28px;"></i>
                                            <span class="nav-link-title">Kullanıcılar</span>
                                        </a>
                                    </div>
                                    @endif
                                    <div class="col-4">
                                        <a href="{{ route('admin.studio.index') }}" class="d-flex flex-column text-center py-3 px-2 quick-action-item">
                                            <i class="fa-solid fa-palette mb-2" style="font-size: 28px;"></i>
                                            <span class="nav-link-title">Studio</span>
                                        </a>
                                    </div>
                                    <div class="col-4">
                                        <a href="{{ route('admin.settingmanagement.index') }}" class="d-flex flex-column text-center py-3 px-2 quick-action-item">
                                            <i class="fa-solid fa-sliders mb-2" style="font-size: 28px;"></i>
                                            <span class="nav-link-title">Ayarlar</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mobile: Tek buton -->
            <div class="d-md-none nav-item dropdown me-3">
                <a href="#" class="nav-link d-flex flex-column align-items-center justify-content-center" data-bs-toggle="dropdown" tabindex="-1" data-bs-auto-close="outside" aria-expanded="false" style="width: 50px; height: 50px; border-radius: 0.375rem;">
                    <i class="fa-solid fa-ellipsis-v" style="font-size: 16px;"></i>
                    <small class="mt-1" style="font-size: 9px; line-height: 1;">Menü</small>
                </a>
                <div class="dropdown-menu dropdown-menu-arrow dropdown-menu-end dropdown-menu-card">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">Admin İşlemler</div>
                        </div>
                        <div class="card-body p-2">
                            <div class="row g-2">
                                <!-- Cache Temizle -->
                                <div class="col-6">
                                    <a href="#" class="d-flex flex-column text-center p-2 border rounded mobile-quick-action cache-clear-btn" data-action="clear">
                                        <i class="fa-solid fa-broom mb-1 text-primary" style="font-size: 18px;"></i>
                                        <small class="fw-bold">Cache<br>Temizle</small>
                                    </a>
                                </div>
                                @if($isCentral)
                                <!-- Sistem Cache -->
                                <div class="col-6">
                                    <a href="#" class="d-flex flex-column text-center p-2 border rounded mobile-quick-action cache-clear-all-btn" data-action="clear-all">
                                        <i class="fa-solid fa-trash-can mb-1 text-danger" style="font-size: 18px;"></i>
                                        <small class="fw-bold">Sistem<br>Cache</small>
                                    </a>
                                </div>
                                @endif
                                <!-- Tema -->
                                <div class="col-6">
                                    <a href="#" class="d-flex flex-column text-center p-2 border rounded mobile-quick-action" data-bs-toggle="offcanvas" data-bs-target="#offcanvasTheme">
                                        <i class="fa-solid fa-brush mb-1 text-primary" style="font-size: 18px;"></i>
                                        <small class="fw-bold">Tema<br>Ayarları</small>
                                    </a>
                                </div>
                                <!-- Aktiviteler -->
                                <div class="col-6">
                                    <a href="{{ route('admin.usermanagement.activity.logs') }}" class="d-flex flex-column text-center p-2 border rounded mobile-quick-action">
                                        <i class="fa-solid fa-bell mb-1 text-info" style="font-size: 18px;"></i>
                                        <small class="fw-bold">Son<br>Aktiviteler</small>
                                    </a>
                                </div>
                                <!-- Modüller -->
                                <div class="col-6">
                                    <a href="{{ route('admin.modulemanagement.index') }}" class="d-flex flex-column text-center p-2 border rounded mobile-quick-action">
                                        <i class="fa-solid fa-puzzle-piece mb-1 text-info" style="font-size: 18px;"></i>
                                        <small class="fw-bold">Modül<br>Yönetimi</small>
                                    </a>
                                </div>
                                @if(!$isCentral)
                                <!-- Kullanıcılar -->
                                <div class="col-6">
                                    <a href="{{ route('admin.usermanagement.index') }}" class="d-flex flex-column text-center p-2 border rounded mobile-quick-action">
                                        <i class="fa-solid fa-users mb-1 text-success" style="font-size: 18px;"></i>
                                        <small class="fw-bold">Kullanıcı<br>Yönetimi</small>
                                    </a>
                                </div>
                                @endif
                                <!-- Studio -->
                                <div class="col-6">
                                    <a href="{{ route('admin.studio.index') }}" class="d-flex flex-column text-center p-2 border rounded mobile-quick-action">
                                        <i class="fa-solid fa-palette mb-1 text-warning" style="font-size: 18px;"></i>
                                        <small class="fw-bold">Studio<br>Editör</small>
                                    </a>
                                </div>
                                <!-- Ayarlar -->
                                <div class="col-6">
                                    <a href="{{ route('admin.settingmanagement.index') }}" class="d-flex flex-column text-center p-2 border rounded mobile-quick-action">
                                        <i class="fa-solid fa-sliders mb-1 text-secondary" style="font-size: 18px;"></i>
                                        <small class="fw-bold">Sistem<br>Ayarları</small>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="nav-item dropdown">
                <a href="#" class="nav-link d-flex lh-1 p-0 px-2 align-items-center" data-bs-toggle="dropdown" aria-label="Open user menu" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Kullanıcı Menüsü">
                    <span class="avatar avatar-sm d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; border-radius: 0.375rem;">
                        <i class="fa-solid fa-user" style="font-size: 18px;"></i>
                    </span>
                    <div class="d-none d-xl-block ps-2">
                        <div>{{ Auth::user()->name }}</div>
                        <div class="mt-1 small text-secondary">
                            @php
                            $user = Auth::user();
                            $roleName = 'Kullanıcı';

                            if ($user->hasRole('root')) {
                            $roleName = 'Root';
                            } elseif ($user->hasRole('admin')) {
                            $roleName = 'Admin';
                            } elseif ($user->hasRole('editor')) {
                            $roleName = 'Editör';
                            }
                            @endphp
                            {{ $roleName }}
                        </div>
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <a href="{{ route('admin.usermanagement.user.activity.logs', ['id' => auth()->id()]) }}"
                        class="dropdown-item">Aktivitelerim</a>
                    <a href="{{ route('admin.usermanagement.manage', ['id' => auth()->id()]) }}"
                        class="dropdown-item">Profilim</a>
                    <div class="dropdown-divider"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item">Çıkış Yap</button>
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
                            <span class="nav-link-title">İçerik</span>
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

                    @if($groupedModules->has('widget') && $groupedModules['widget']->count() > 0)
                    <li class="nav-item {{ $activeType == 'widget' ? 'active' : '' }} dropdown">
                        <a class="nav-link dropdown-toggle" href="#navbar-widget" data-bs-toggle="dropdown"
                            data-bs-auto-close="outside" role="button" aria-expanded="false">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <i class="fa-solid fa-puzzle-piece" style="font-size: 18px;"></i>
                            </span>
                            <span class="nav-link-title">Bileşen</span>
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
                            <span class="nav-link-title">Yönetim</span>
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
                            <span class="nav-link-title">Sistem</span>
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
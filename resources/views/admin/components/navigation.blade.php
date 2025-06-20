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

<header class="navbar navbar-expand-md d-print-none" style="background: #7952b3" data-bs-theme="dark">
    <div class="container-xl">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu" aria-controls="navbar-menu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal pe-0 pe-md-3">
            <a href="{{ route('admin.dashboard') }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 68 68" width="32" height="32" aria-label="{{ $siteTitle }}" class="navbar-brand-image">
                    <path d="M64.6 16.2C63 9.9 58.1 5 51.8 3.4 40 1.5 28 1.5 16.2 3.4 9.9 5 5 9.9 3.4 16.2 1.5 28 1.5 40 3.4 51.8 5 58.1 9.9 63 16.2 64.6c11.8 1.9 23.8 1.9 35.6 0C58.1 63 63 58.1 64.6 51.8c1.9-11.8 1.9-23.8 0-35.6zM33.3 36.3c-2.8 4.4-6.6 8.2-11.1 11-1.5.9-3.3.9-4.8.1s-2.4-2.3-2.5-4c0-1.7.9-3.3 2.4-4.1 2.3-1.4 4.4-3.2 6.1-5.3-1.8-2.1-3.8-3.8-6.1-5.3-2.3-1.3-3-4.2-1.7-6.4s4.3-2.9 6.5-1.6c4.5 2.8 8.2 6.5 11.1 10.9 1 1.4 1 3.3.1 4.7zM49.2 46H37.8c-2.1 0-3.8-1-3.8-3s1.7-3 3.8-3h11.4c2.1 0 3.8 1 3.8 3s-1.7 3-3.8 3z" fill="#066fd1" style="fill: var(--tblr-primary, #066fd1)"></path>
                </svg>
            </a>
        </div>

        <div class="navbar-nav flex-row order-md-last">
            <div class="d-none d-md-flex">
                <!-- Dark/Light Mode Switch -->
                <div class="nav-item">
                    <a href="#" class="nav-link px-0 hide-theme-dark" data-bs-toggle="tooltip" data-bs-placement="bottom" aria-label="Enable dark mode" data-bs-original-title="Enable dark mode">
                        <i class="fa-solid fa-moon"></i>
                    </a>
                    <a href="#" class="nav-link px-0 hide-theme-light" data-bs-toggle="tooltip" data-bs-placement="bottom" aria-label="Enable light mode" data-bs-original-title="Enable light mode">
                        <i class="fa-solid fa-sun"></i>
                    </a>
                </div>
                
                <!-- Notifications Dropdown -->
                <div class="nav-item dropdown d-none d-md-flex">
                    <a href="#" class="nav-link px-0" data-bs-toggle="dropdown" tabindex="-1" aria-label="Show notifications" data-bs-auto-close="outside" aria-expanded="false">
                        <i class="fa-solid fa-bell"></i>
                        <span class="badge bg-red"></span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-arrow dropdown-menu-end dropdown-menu-card">
                        <div class="card">
                            <div class="card-header d-flex">
                                <h3 class="card-title">Bildirimler</h3>
                                <div class="btn-close ms-auto" data-bs-dismiss="dropdown"></div>
                            </div>
                            <div class="list-group list-group-flush list-group-hoverable">
                                @if($isCentral)
                                <div class="list-group-item">
                                    <div class="row align-items-center">
                                        <div class="col-auto"><span class="status-dot status-dot-animated bg-red d-block"></span></div>
                                        <div class="col text-truncate">
                                            <a href="#" class="text-body d-block">Sistem Güncellemesi</a>
                                            <div class="d-block text-secondary text-truncate mt-n1">
                                                Yeni özellikler ve güvenlik yamaları uygulandı
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <a href="#" class="list-group-item-actions">
                                                <i class="fa-regular fa-star text-muted"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                @else
                                <div class="list-group-item">
                                    <div class="row align-items-center">
                                        <div class="col-auto"><span class="status-dot d-block"></span></div>
                                        <div class="col text-truncate">
                                            <a href="#" class="text-body d-block">Yeni İçerik</a>
                                            <div class="d-block text-secondary text-truncate mt-n1">
                                                5 yeni sayfa eklendi
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <a href="#" class="list-group-item-actions show">
                                                <i class="fa-solid fa-star text-yellow"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col">
                                        <a href="#" class="btn btn-outline-primary w-100">Tümünü Arşivle</a>
                                    </div>
                                    <div class="col">
                                        <a href="#" class="btn btn-outline-primary w-100">Okundu İşaretle</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Apps/Cache Actions Dropdown -->
                <div class="nav-item dropdown d-none d-md-flex me-3">
                    <a href="#" class="nav-link px-0" data-bs-toggle="dropdown" tabindex="-1" aria-label="Show app menu" data-bs-auto-close="outside" aria-expanded="false">
                        <i class="fa-solid fa-grid-3"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-arrow dropdown-menu-end dropdown-menu-card">
                        <div class="card">
                            <div class="card-header">
                                <div class="card-title">Hızlı İşlemler</div>
                                <div class="card-actions btn-actions">
                                    <a href="#" class="btn-action" data-bs-toggle="offcanvas" data-bs-target="#offcanvasTheme">
                                        <i class="fa-solid fa-cog"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="card-body p-2">
                                <div class="row g-2">
                                    @if($isCentral)
                                    <div class="col-4">
                                        <a href="#" class="d-flex flex-column flex-center text-center text-secondary py-3 px-2 link-hoverable cache-clear-btn" data-action="clear">
                                            <i class="fa-solid fa-broom mb-2" style="font-size: 24px;"></i>
                                            <span class="small">Cache Temizle</span>
                                        </a>
                                    </div>
                                    <div class="col-4">
                                        <a href="#" class="d-flex flex-column flex-center text-center text-secondary py-3 px-2 link-hoverable cache-clear-all-btn" data-action="clear-all">
                                            <i class="fa-solid fa-trash-can mb-2" style="font-size: 24px;"></i>
                                            <span class="small">Tüm Cache</span>
                                        </a>
                                    </div>
                                    @else
                                    <div class="col-4">
                                        <a href="#" class="d-flex flex-column flex-center text-center text-secondary py-3 px-2 link-hoverable cache-clear-btn" data-action="clear">
                                            <i class="fa-solid fa-broom mb-2" style="font-size: 24px;"></i>
                                            <span class="small">Cache Temizle</span>
                                        </a>
                                    </div>
                                    @endif
                                    <div class="col-4">
                                        <a href="#" class="d-flex flex-column flex-center text-center text-secondary py-3 px-2 link-hoverable" data-bs-toggle="offcanvas" data-bs-target="#offcanvasTheme">
                                            <i class="fa-solid fa-brush mb-2" style="font-size: 24px;"></i>
                                            <span class="small">Tema Ayarları</span>
                                        </a>
                                    </div>
                                    <div class="col-4">
                                        <a href="{{ route('admin.modulemanagement.index') }}" class="d-flex flex-column flex-center text-center text-secondary py-3 px-2 link-hoverable">
                                            <i class="fa-solid fa-puzzle-piece mb-2" style="font-size: 24px;"></i>
                                            <span class="small">Modüller</span>
                                        </a>
                                    </div>
                                    <div class="col-4">
                                        <a href="{{ route('admin.settingmanagement.index') }}" class="d-flex flex-column flex-center text-center text-secondary py-3 px-2 link-hoverable">
                                            <i class="fa-solid fa-sliders mb-2" style="font-size: 24px;"></i>
                                            <span class="small">Ayarlar</span>
                                        </a>
                                    </div>
                                    <div class="col-4">
                                        <a href="{{ route('admin.usermanagement.index') }}" class="d-flex flex-column flex-center text-center text-secondary py-3 px-2 link-hoverable">
                                            <i class="fa-solid fa-users mb-2" style="font-size: 24px;"></i>
                                            <span class="small">Kullanıcılar</span>
                                        </a>
                                    </div>
                                    <div class="col-4">
                                        <a href="{{ route('admin.studio.index') }}" class="d-flex flex-column flex-center text-center text-secondary py-3 px-2 link-hoverable">
                                            <i class="fa-solid fa-palette mb-2" style="font-size: 24px;"></i>
                                            <span class="small">Studio</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="nav-item dropdown">
                <a href="#" class="nav-link d-flex p-0 px-1" data-bs-toggle="dropdown" aria-label="Open user menu">
                    <span class="avatar avatar-sm"><i class="fa-solid fa-user"></i> </span>
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
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown"
                            data-bs-auto-close="outside" role="button" aria-expanded="false">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <i class="fa-solid fa-user-graduate"></i>
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
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown"
                            data-bs-auto-close="outside" role="button" aria-expanded="false">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <i class="fa-solid fa-user-chef"></i>
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
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown"
                            data-bs-auto-close="outside" role="button" aria-expanded="false">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <i class="fa-solid fa-user-police-tie"></i>
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
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown"
                            data-bs-auto-close="outside" role="button" aria-expanded="false">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <i class="fa-solid fa-user-doctor"></i>
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
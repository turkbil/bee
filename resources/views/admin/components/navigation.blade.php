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
    <div class="container-xl">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu">
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

        <div class="navbar-nav flex-row order-md-last">
            <!-- Tema Ayarları Butonu -->
            <div class="nav-item me-2">
                <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="offcanvas" data-bs-target="#offcanvasTheme">
                    <span class="avatar avatar-sm rounded-circle bg-primary">
                        <i class="fa-solid fa-brush" style="line-height: 24px;"></i>
                    </span>
                </a>
            </div>

            <div class="theme-mode pt-2">
                <input type="checkbox" id="switch" class="dark-switch">
                <div class="app">
                    <div class="switch-content">
                        <div class="switch-label"></div>
                        <label for="switch">
                            <div class="toggle"></div>
                            <div class="names">
                                <p class="light"><i class="fa-light fa-moon"></i></p>
                                <p class="dark"><i class="fa-light fa-brightness-low" style="margin-top: 6px;"></i></p>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <div class="nav-item dropdown">
                <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown">
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
                <div class="dropdown-menu dropdown-menu-end">
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
                                <i class="fa-solid fa-user-graduate"></i>
                            </span>
                            <span class="nav-link-title">İçerik</span>
                        </a>
                        <div class="dropdown-menu">
                            <div class="dropdown-menu-columns">
                                @php
                                $contentModules = $groupedModules['content'];
                                $contentChunks = $contentModules->chunk(ceil($contentModules->count() / 2));
                                @endphp
                                @foreach($contentChunks as $chunk)
                                <div class="dropdown-menu-column">
                                    @foreach($chunk as $module)
                                    <a class="dropdown-item" href="{{ route('admin.' . strtolower($module->name) . '.index') }}">
                                        {{ $module->display_name }}
                                    </a>
                                    @endforeach
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </li>
                    @endif

                    @if($groupedModules->has('widget') && $groupedModules['widget']->count() > 0)
                    <li class="nav-item {{ $activeType == 'widget' ? 'active' : '' }} dropdown">
                        <a class="nav-link dropdown-toggle" href="#navbar-widget" data-bs-toggle="dropdown"
                            data-bs-auto-close="outside" role="button" aria-expanded="false">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <i class="fa-solid fa-user-chef"></i>
                            </span>
                            <span class="nav-link-title">Bileşenler</span>
                        </a>
                        <div class="dropdown-menu">
                            @foreach($groupedModules['widget'] as $module)
                            <a class="dropdown-item" href="{{ route('admin.' . strtolower($module->name) . '.index') }}">
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
                                <i class="fa-solid fa-user-police-tie"></i>
                            </span>
                            <span class="nav-link-title">Yönetim</span>
                        </a>
                        <div class="dropdown-menu">
                            @foreach($groupedModules['management'] as $module)
                            <a class="dropdown-item" href="{{ route('admin.' . strtolower($module->name) . '.index') }}">
                                {{ $module->display_name }}
                            </a>
                            @endforeach
                        </div>
                    </li>
                    @endif

                    @if($groupedModules->has('system') && $groupedModules['system']->count() > 0)
                    <li class="nav-item {{ $activeType == 'system' ? 'active' : '' }} dropdown">
                        <a class="nav-link dropdown-toggle" href="#navbar-system" data-bs-toggle="dropdown" data-bs-auto-close="outside"
                            role="button" aria-expanded="false">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <i class="fa-solid fa-user-doctor"></i>
                            </span>
                            <span class="nav-link-title">Sistem</span>
                        </a>
                        <div class="dropdown-menu">
                            @foreach($groupedModules['system'] as $module)
                            <a class="dropdown-item" href="{{ route('admin.' . strtolower($module->name) . '.index') }}">
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
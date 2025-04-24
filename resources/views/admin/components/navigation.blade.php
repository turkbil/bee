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

<!-- Tema Builder Offcanvas -->
<div class="offcanvas offcanvas-start shadow-lg" tabindex="-1" id="offcanvasTheme" aria-labelledby="offcanvasThemeLabel">
    <div class="offcanvas-header border-bottom py-2">
        <h5 class="offcanvas-title fw-bold" id="offcanvasThemeLabel">
            <i class="fa-solid fa-palette me-2"></i>Tema Ayarları
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-2">
        <div class="card mb-2">
            <div class="card-header py-2">
                <h3 class="card-title">
                    <i class="fa-solid fa-circle-half-stroke me-2"></i>Renk Modu
                </h3>
            </div>
            <div class="card-body p-2">
                <div class="d-flex gap-1">
                    <label class="theme-option flex-grow-1 text-center p-2 border rounded position-relative">
                        <input type="radio" name="theme" value="light" class="position-absolute opacity-0" {{ !isset($_COOKIE['dark']) || $_COOKIE['dark'] != '1' ? 'checked' : '' }} />
                        <div class="theme-preview bg-white border mb-1" style="height:60px; border-radius: 4px;"></div>
                        <span class="d-block small">Açık</span>
                        <div class="theme-check position-absolute end-0 top-0 m-1 text-primary {{ !isset($_COOKIE['dark']) || $_COOKIE['dark'] != '1' ? '' : 'd-none' }}">
                            <i class="fa-solid fa-circle-check"></i>
                        </div>
                    </label>
                    <label class="theme-option flex-grow-1 text-center p-2 border rounded position-relative">
                        <input type="radio" name="theme" value="dark" class="position-absolute opacity-0" {{ isset($_COOKIE['dark']) && $_COOKIE['dark'] == '1' ? 'checked' : '' }} />
                        <div class="theme-preview bg-dark border mb-1" style="height:60px; border-radius: 4px;"></div>
                        <span class="d-block small">Koyu</span>
                        <div class="theme-check position-absolute end-0 top-0 m-1 text-primary {{ isset($_COOKIE['dark']) && $_COOKIE['dark'] == '1' ? '' : 'd-none' }}">
                            <i class="fa-solid fa-circle-check"></i>
                        </div>
                    </label>
                    <label class="theme-option flex-grow-1 text-center p-2 border rounded position-relative">
                        <input type="radio" name="theme" value="auto" class="position-absolute opacity-0" {{ isset($_COOKIE['dark']) && $_COOKIE['dark'] == 'auto' ? 'checked' : '' }} />
                        <div class="theme-preview border mb-1" style="height:60px; border-radius: 4px; background: linear-gradient(to right, #fff 50%, #151f2c 50%);"></div>
                        <span class="d-block small">Sistem</span>
                        <div class="theme-check position-absolute end-0 top-0 m-1 text-primary {{ isset($_COOKIE['dark']) && $_COOKIE['dark'] == 'auto' ? '' : 'd-none' }}">
                            <i class="fa-solid fa-circle-check"></i>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <div class="card mb-2">
            <div class="card-header py-2">
                <h3 class="card-title">
                    <i class="fa-solid fa-swatchbook me-2"></i>Ana Renk
                </h3>
            </div>
            <div class="card-body p-2">
                <div class="theme-color-grid">
                    <div class="row g-1 color-row">
                        <div class="col-3">
                            <label class="color-item position-relative mb-0 w-100">
                                <input name="theme-primary" type="radio" value="#FA5252" class="position-absolute opacity-0" {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#FA5252') ? 'checked' : '' }} />
                                <span class="color-preview rounded-circle d-block mx-auto" style="background-color: #FA5252; width: 30px; height: 30px;"></span>
                                <div class="color-check position-absolute start-50 top-50 translate-middle text-white {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#FA5252') ? '' : 'd-none' }}">
                                    <i class="fa-solid fa-check"></i>
                                </div>
                            </label>
                        </div>
                        <div class="col-3">
                            <label class="color-item position-relative mb-0 w-100">
                                <input name="theme-primary" type="radio" value="#E64980" class="position-absolute opacity-0" {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#E64980') ? 'checked' : '' }} />
                                <span class="color-preview rounded-circle d-block mx-auto" style="background-color: #E64980; width: 30px; height: 30px;"></span>
                                <div class="color-check position-absolute start-50 top-50 translate-middle text-white {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#E64980') ? '' : 'd-none' }}">
                                    <i class="fa-solid fa-check"></i>
                                </div>
                            </label>
                        </div>
                        <div class="col-3">
                            <label class="color-item position-relative mb-0 w-100">
                                <input name="theme-primary" type="radio" value="#BE4BDB" class="position-absolute opacity-0" {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#BE4BDB') ? 'checked' : '' }} />
                                <span class="color-preview rounded-circle d-block mx-auto" style="background-color: #BE4BDB; width: 30px; height: 30px;"></span>
                                <div class="color-check position-absolute start-50 top-50 translate-middle text-white {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#BE4BDB') ? '' : 'd-none' }}">
                                    <i class="fa-solid fa-check"></i>
                                </div>
                            </label>
                        </div>
                        <div class="col-3">
                            <label class="color-item position-relative mb-0 w-100">
                                <input name="theme-primary" type="radio" value="#7950F2" class="position-absolute opacity-0" {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#7950F2') ? 'checked' : '' }} />
                                <span class="color-preview rounded-circle d-block mx-auto" style="background-color: #7950F2; width: 30px; height: 30px;"></span>
                                <div class="color-check position-absolute start-50 top-50 translate-middle text-white {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#7950F2') ? '' : 'd-none' }}">
                                    <i class="fa-solid fa-check"></i>
                                </div>
                            </label>
                        </div>
                    </div>
                    
                    <div class="row g-1 mt-1 color-row">
                        <div class="col-3">
                            <label class="color-item position-relative mb-0 w-100">
                                <input name="theme-primary" type="radio" value="#4C6EF5" class="position-absolute opacity-0" {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#4C6EF5') ? 'checked' : '' }} />
                                <span class="color-preview rounded-circle d-block mx-auto" style="background-color: #4C6EF5; width: 30px; height: 30px;"></span>
                                <div class="color-check position-absolute start-50 top-50 translate-middle text-white {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#4C6EF5') ? '' : 'd-none' }}">
                                    <i class="fa-solid fa-check"></i>
                                </div>
                            </label>
                        </div>
                        <div class="col-3">
                            <label class="color-item position-relative mb-0 w-100">
                                <input name="theme-primary" type="radio" value="#228BE6" class="position-absolute opacity-0" {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#228BE6') ? 'checked' : '' }} />
                                <span class="color-preview rounded-circle d-block mx-auto" style="background-color: #228BE6; width: 30px; height: 30px;"></span>
                                <div class="color-check position-absolute start-50 top-50 translate-middle text-white {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#228BE6') ? '' : 'd-none' }}">
                                    <i class="fa-solid fa-check"></i>
                                </div>
                            </label>
                        </div>
                        <div class="col-3">
                            <label class="color-item position-relative mb-0 w-100">
                                <input name="theme-primary" type="radio" value="#15AABF" class="position-absolute opacity-0" {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#15AABF') ? 'checked' : '' }} />
                                <span class="color-preview rounded-circle d-block mx-auto" style="background-color: #15AABF; width: 30px; height: 30px;"></span>
                                <div class="color-check position-absolute start-50 top-50 translate-middle text-white {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#15AABF') ? '' : 'd-none' }}">
                                    <i class="fa-solid fa-check"></i>
                                </div>
                            </label>
                        </div>
                        <div class="col-3">
                            <label class="color-item position-relative mb-0 w-100">
                                <input name="theme-primary" type="radio" value="#12B886" class="position-absolute opacity-0" {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#12B886') ? 'checked' : '' }} />
                                <span class="color-preview rounded-circle d-block mx-auto" style="background-color: #12B886; width: 30px; height: 30px;"></span>
                                <div class="color-check position-absolute start-50 top-50 translate-middle text-white {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#12B886') ? '' : 'd-none' }}">
                                    <i class="fa-solid fa-check"></i>
                                </div>
                            </label>
                        </div>
                    </div>
                    
                    <div class="row g-1 mt-1 color-row">
                        <div class="col-3">
                            <label class="color-item position-relative mb-0 w-100">
                                <input name="theme-primary" type="radio" value="#40C057" class="position-absolute opacity-0" {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#40C057') ? 'checked' : '' }} />
                                <span class="color-preview rounded-circle d-block mx-auto" style="background-color: #40C057; width: 30px; height: 30px;"></span>
                                <div class="color-check position-absolute start-50 top-50 translate-middle text-white {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#40C057') ? '' : 'd-none' }}">
                                    <i class="fa-solid fa-check"></i>
                                </div>
                            </label>
                        </div>
                        <div class="col-3">
                            <label class="color-item position-relative mb-0 w-100">
                                <input name="theme-primary" type="radio" value="#82C91E" class="position-absolute opacity-0" {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#82C91E') ? 'checked' : '' }} />
                                <span class="color-preview rounded-circle d-block mx-auto" style="background-color: #82C91E; width: 30px; height: 30px;"></span>
                                <div class="color-check position-absolute start-50 top-50 translate-middle text-white {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#82C91E') ? '' : 'd-none' }}">
                                    <i class="fa-solid fa-check"></i>
                                </div>
                            </label>
                        </div>
                        <div class="col-3">
                            <label class="color-item position-relative mb-0 w-100">
                                <input name="theme-primary" type="radio" value="#FAB005" class="position-absolute opacity-0" {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#FAB005') ? 'checked' : '' }} />
                                <span class="color-preview rounded-circle d-block mx-auto" style="background-color: #FAB005; width: 30px; height: 30px;"></span>
                                <div class="color-check position-absolute start-50 top-50 translate-middle text-white {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#FAB005') ? '' : 'd-none' }}">
                                    <i class="fa-solid fa-check"></i>
                                </div>
                            </label>
                        </div>
                        <div class="col-3">
                            <label class="color-item position-relative mb-0 w-100">
                                <input name="theme-primary" type="radio" value="#FD7E14" class="position-absolute opacity-0" {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#FD7E14') ? 'checked' : '' }} />
                                <span class="color-preview rounded-circle d-block mx-auto" style="background-color: #FD7E14; width: 30px; height: 30px;"></span>
                                <div class="color-check position-absolute start-50 top-50 translate-middle text-white {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#FD7E14') ? '' : 'd-none' }}">
                                    <i class="fa-solid fa-check"></i>
                                </div>
                            </label>
                        </div>
                    </div>
                    
                    <div class="row g-1 mt-1 color-row">
                        <div class="col-3">
                            <label class="color-item position-relative mb-0 w-100">
                                <input name="theme-primary" type="radio" value="#FF922B" class="position-absolute opacity-0" {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#FF922B') ? 'checked' : '' }} />
                                <span class="color-preview rounded-circle d-block mx-auto" style="background-color: #FF922B; width: 30px; height: 30px;"></span>
                                <div class="color-check position-absolute start-50 top-50 translate-middle text-white {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#FF922B') ? '' : 'd-none' }}">
                                    <i class="fa-solid fa-check"></i>
                                </div>
                            </label>
                        </div>
                        <div class="col-3">
                            <label class="color-item position-relative mb-0 w-100">
                                <input name="theme-primary" type="radio" value="#FCC419" class="position-absolute opacity-0" {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#FCC419') ? 'checked' : '' }} />
                                <span class="color-preview rounded-circle d-block mx-auto" style="background-color: #FCC419; width: 30px; height: 30px;"></span>
                                <div class="color-check position-absolute start-50 top-50 translate-middle text-white {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#FCC419') ? '' : 'd-none' }}">
                                    <i class="fa-solid fa-check"></i>
                                </div>
                            </label>
                        </div>
                        <div class="col-3">
                            <label class="color-item position-relative mb-0 w-100">
                                <input name="theme-primary" type="radio" value="#066fd1" class="position-absolute opacity-0" {{ (!isset($_COOKIE['siteColor']) || $_COOKIE['siteColor'] == '#066fd1') ? 'checked' : '' }} />
                                <span class="color-preview rounded-circle d-block mx-auto" style="background-color: #066fd1; width: 30px; height: 30px;"></span>
                                <div class="color-check position-absolute start-50 top-50 translate-middle text-white {{ (!isset($_COOKIE['siteColor']) || $_COOKIE['siteColor'] == '#066fd1') ? '' : 'd-none' }}">
                                    <i class="fa-solid fa-check"></i>
                                </div>
                            </label>
                        </div>
                        <div class="col-3">
                            <label class="color-item position-relative mb-0 w-100">
                                <input name="theme-primary" type="radio" value="#F03E3E" class="position-absolute opacity-0" {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#F03E3E') ? 'checked' : '' }} />
                                <span class="color-preview rounded-circle d-block mx-auto" style="background-color: #F03E3E; width: 30px; height: 30px;"></span>
                                <div class="color-check position-absolute start-50 top-50 translate-middle text-white {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#F03E3E') ? '' : 'd-none' }}">
                                    <i class="fa-solid fa-check"></i>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-2">
            <div class="card-header py-2">
                <h3 class="card-title">
                    <i class="fa-solid fa-font me-2"></i>Yazı Tipi
                </h3>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <label class="list-group-item d-flex align-items-center py-2">
                        <input type="radio" name="theme-font" class="form-check-input me-2" value="Inter, system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, 'Noto Sans', sans-serif" {{ (!isset($_COOKIE['themeFont']) || $_COOKIE['themeFont'] == "Inter, system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, 'Noto Sans', sans-serif") ? 'checked' : '' }} />
                        <span style="font-family: Inter, system-ui;">Inter (Modern)</span>
                    </label>
                    <label class="list-group-item d-flex align-items-center py-2">
                        <input type="radio" name="theme-font" class="form-check-input me-2" value="'Roboto', sans-serif" {{ (isset($_COOKIE['themeFont']) && $_COOKIE['themeFont'] == "'Roboto', sans-serif") ? 'checked' : '' }} />
                        <span style="font-family: 'Roboto', sans-serif;">Roboto (Yaygın)</span>
                    </label>
                    <label class="list-group-item d-flex align-items-center py-2">
                        <input type="radio" name="theme-font" class="form-check-input me-2" value="'Poppins', sans-serif" {{ (isset($_COOKIE['themeFont']) && $_COOKIE['themeFont'] == "'Poppins', sans-serif") ? 'checked' : '' }} />
                        <span style="font-family: 'Roboto', sans-serif;">Poppins (Modern)</span>
                    </label>
                    <label class="list-group-item d-flex align-items-center py-2">
                        <input type="radio" name="theme-font" class="form-check-input me-2" value="Georgia, 'Times New Roman', Times, serif" {{ (isset($_COOKIE['themeFont']) && $_COOKIE['themeFont'] == "Georgia, 'Times New Roman', Times, serif") ? 'checked' : '' }} />
                        <span style="font-family: Georgia, 'Times New Roman', Times, serif;">Georgia (Klasik)</span>
                    </label>
                    <label class="list-group-item d-flex align-items-center py-2">
                        <input type="radio" name="theme-font" class="form-check-input me-2" value="'Courier New', Courier, monospace" {{ (isset($_COOKIE['themeFont']) && $_COOKIE['themeFont'] == "'Courier New', Courier, monospace") ? 'checked' : '' }} />
                        <span style="font-family: 'Courier New', Courier, monospace;">Courier (Kod)</span>
                    </label>
                </div>
            </div>
        </div>

        <div class="card mb-2">
            <div class="card-header py-2">
                <h3 class="card-title">
                    <i class="fa-solid fa-square-full me-2"></i>Köşe Yuvarlaklığı
                </h3>
            </div>
            <div class="card-body p-2">
                <div class="d-flex gap-1 justify-content-between">
                    <label class="text-center position-relative" style="width: 60px;">
                        <input type="radio" name="theme-radius" value="0" class="position-absolute opacity-0" {{ (isset($_COOKIE['themeRadius']) && $_COOKIE['themeRadius'] == '0') ? 'checked' : '' }} />
                        <div class="bg-body-tertiary border mb-1" style="height: 36px; border-radius: 0;"></div>
                        <span class="d-block small">Keskin</span>
                        <div class="position-absolute end-0 top-0 m-1 text-primary {{ (isset($_COOKIE['themeRadius']) && $_COOKIE['themeRadius'] == '0') ? '' : 'd-none' }}">
                            <i class="fa-solid fa-check-circle"></i>
                        </div>
                    </label>
                    <label class="text-center position-relative" style="width: 60px;">
                        <input type="radio" name="theme-radius" value="0.25rem" class="position-absolute opacity-0" {{ (isset($_COOKIE['themeRadius']) && $_COOKIE['themeRadius'] == '0.25rem') ? 'checked' : '' }} />
                        <div class="bg-body-tertiary border mb-1" style="height: 36px; border-radius: 0.25rem;"></div>
                        <span class="d-block small">Hafif</span>
                        <div class="position-absolute end-0 top-0 m-1 text-primary {{ (isset($_COOKIE['themeRadius']) && $_COOKIE['themeRadius'] == '0.25rem') ? '' : 'd-none' }}">
                            <i class="fa-solid fa-check-circle"></i>
                        </div>
                    </label>
                    <label class="text-center position-relative" style="width: 60px;">
                        <input type="radio" name="theme-radius" value="0.5rem" class="position-absolute opacity-0" {{ (!isset($_COOKIE['themeRadius']) || $_COOKIE['themeRadius'] == '0.5rem') ? 'checked' : '' }} />
                        <div class="bg-body-tertiary border mb-1" style="height: 36px; border-radius: 0.5rem;"></div>
                        <span class="d-block small">Orta</span>
                        <div class="position-absolute end-0 top-0 m-1 text-primary {{ (!isset($_COOKIE['themeRadius']) || $_COOKIE['themeRadius'] == '0.5rem') ? '' : 'd-none' }}">
                            <i class="fa-solid fa-check-circle"></i>
                        </div>
                    </label>
                    <label class="text-center position-relative" style="width: 60px;">
                        <input type="radio" name="theme-radius" value="0.75rem" class="position-absolute opacity-0" {{ (isset($_COOKIE['themeRadius']) && $_COOKIE['themeRadius'] == '0.75rem') ? 'checked' : '' }} />
                        <div class="bg-body-tertiary border mb-1" style="height: 36px; border-radius: 0.75rem;"></div>
                        <span class="d-block small">Büyük</span>
                        <div class="position-absolute end-0 top-0 m-1 text-primary {{ (isset($_COOKIE['themeRadius']) && $_COOKIE['themeRadius'] == '0.75rem') ? '' : 'd-none' }}">
                            <i class="fa-solid fa-check-circle"></i>
                        </div>
                    </label>
                    <label class="text-center position-relative" style="width: 60px;">
                        <input type="radio" name="theme-radius" value="1rem" class="position-absolute opacity-0" {{ (isset($_COOKIE['themeRadius']) && $_COOKIE['themeRadius'] == '1rem') ? 'checked' : '' }} />
                        <div class="bg-body-tertiary border mb-1" style="height: 36px; border-radius: 1rem;"></div>
                        <span class="d-block small">Tam</span>
                        <div class="position-absolute end-0 top-0 m-1 text-primary {{ (isset($_COOKIE['themeRadius']) && $_COOKIE['themeRadius'] == '1rem') ? '' : 'd-none' }}">
                            <i class="fa-solid fa-check-circle"></i>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <div class="card mb-2">
            <div class="card-header py-2">
                <h3 class="card-title">
                    <i class="fa-solid fa-palette me-2"></i>Gri Ton
                </h3>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <label class="list-group-item d-flex align-items-center py-2">
                        <input type="radio" name="theme-base" class="form-check-input me-2" value="slate" {{ (isset($_COOKIE['themeBase']) && $_COOKIE['themeBase'] == 'slate') ? 'checked' : '' }} />
                        <span>Slate (Koyu)</span>
                    </label>
                    <label class="list-group-item d-flex align-items-center py-2">
                        <input type="radio" name="theme-base" class="form-check-input me-2" value="gray" {{ (!isset($_COOKIE['themeBase']) || $_COOKIE['themeBase'] == 'gray') ? 'checked' : '' }} />
                        <span>Gray (Standart)</span>
                    </label>
                    <label class="list-group-item d-flex align-items-center py-2">
                        <input type="radio" name="theme-base" class="form-check-input me-2" value="zinc" {{ (isset($_COOKIE['themeBase']) && $_COOKIE['themeBase'] == 'zinc') ? 'checked' : '' }} />
                        <span>Zinc (Metal)</span>
                    </label>
                    <label class="list-group-item d-flex align-items-center py-2">
                        <input type="radio" name="theme-base" class="form-check-input me-2" value="neutral" {{ (isset($_COOKIE['themeBase']) && $_COOKIE['themeBase'] == 'neutral') ? 'checked' : '' }} />
                        <span>Neutral (Nötr)</span>
                    </label>
                    <label class="list-group-item d-flex align-items-center py-2">
                        <input type="radio" name="theme-base" class="form-check-input me-2" value="stone" {{ (isset($_COOKIE['themeBase']) && $_COOKIE['themeBase'] == 'stone') ? 'checked' : '' }} />
                        <span>Stone (Taş)</span>
                    </label>
                </div>
            </div>
        </div>

        <div class="card mb-2">
            <div class="card-header py-2">
                <h3 class="card-title">
                    <i class="fa-solid fa-table-list me-2"></i>Tablo Görünümü
                </h3>
            </div>
            <div class="card-body p-2">
                <div class="d-flex gap-4">
                    <label class="form-check form-check-inline">
                        <input type="radio" name="table-compact" class="form-check-input" value="1" {{ (!isset($_COOKIE['tableCompact']) || $_COOKIE['tableCompact'] == '1') ? 'checked' : '' }} />
                        <span class="form-check-label">Kompakt</span>
                    </label>
                    <label class="form-check form-check-inline">
                        <input type="radio" name="table-compact" class="form-check-input" value="0" {{ (isset($_COOKIE['tableCompact']) && $_COOKIE['tableCompact'] == '0') ? 'checked' : '' }} />
                        <span class="form-check-label">Normal</span>
                    </label>
                </div>
            </div>
        </div>
        
        <div class="card mb-2">
            <div class="card-body p-2">
                <button type="button" class="btn btn-primary w-100" id="reset-changes">
                    <i class="fa-solid fa-rotate-left me-2"></i>Varsayılan Ayarlara Dön
                </button>
            </div>
        </div>
    </div>
</div>
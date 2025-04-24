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

<!-- Tema Builder Offcanvas -->
<div class="offcanvas offcanvas-start shadow-lg" tabindex="-1" id="offcanvasTheme" aria-labelledby="offcanvasThemeLabel">
    <div class="offcanvas-header border-bottom py-2">
        <h5 class="offcanvas-title" id="offcanvasThemeLabel">
            <i class="fa-solid fa-palette me-2"></i>Tema
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-0">
        <!-- Renk modu -->
        <div class="list-group list-group-flush border-bottom">
            <div class="list-group-item py-2 px-3 bg-subtle">
                <strong><i class="fa-solid fa-circle-half-stroke me-2"></i>Görünüm</strong>
            </div>
            <label class="list-group-item py-2 d-flex align-items-center">
                <input type="radio" name="theme" value="light" class="form-check-input me-2" {{ !isset($_COOKIE['dark']) || $_COOKIE['dark'] != '1' ? 'checked' : '' }}>
                <span>Açık</span>
            </label>
            <label class="list-group-item py-2 d-flex align-items-center">
                <input type="radio" name="theme" value="dark" class="form-check-input me-2" {{ isset($_COOKIE['dark']) && $_COOKIE['dark'] == '1' ? 'checked' : '' }}>
                <span>Koyu</span>
            </label>
            <label class="list-group-item py-2 d-flex align-items-center">
                <input type="radio" name="theme" value="auto" class="form-check-input me-2" {{ isset($_COOKIE['dark']) && $_COOKIE['dark'] == 'auto' ? 'checked' : '' }}>
                <span>Sistem</span>
            </label>
        </div>

        <!-- Ana renk -->
        <div class="list-group-item py-2 px-3 bg-subtle">
            <strong><i class="fa-solid fa-droplet me-2"></i>Ana Renk</strong>
        </div>
        <div class="p-2 border-bottom">
            <div class="row row-cols-6 g-1">
                <div class="col">
                    <label class="form-colorinput mb-0">
                        <input name="theme-primary" type="radio" value="#066fd1" class="form-colorinput-input" {{ (!isset($_COOKIE['siteColor']) || $_COOKIE['siteColor'] == '#066fd1') ? 'checked' : '' }}>
                        <span class="form-colorinput-color" style="background-color: #066fd1"></span>
                    </label>
                </div>
                <div class="col">
                    <label class="form-colorinput mb-0">
                        <input name="theme-primary" type="radio" value="#FA5252" class="form-colorinput-input" {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#FA5252') ? 'checked' : '' }}>
                        <span class="form-colorinput-color" style="background-color: #FA5252"></span>
                    </label>
                </div>
                <div class="col">
                    <label class="form-colorinput mb-0">
                        <input name="theme-primary" type="radio" value="#E64980" class="form-colorinput-input" {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#E64980') ? 'checked' : '' }}>
                        <span class="form-colorinput-color" style="background-color: #E64980"></span>
                    </label>
                </div>
                <div class="col">
                    <label class="form-colorinput mb-0">
                        <input name="theme-primary" type="radio" value="#BE4BDB" class="form-colorinput-input" {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#BE4BDB') ? 'checked' : '' }}>
                        <span class="form-colorinput-color" style="background-color: #BE4BDB"></span>
                    </label>
                </div>
                <div class="col">
                    <label class="form-colorinput mb-0">
                        <input name="theme-primary" type="radio" value="#7950F2" class="form-colorinput-input" {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#7950F2') ? 'checked' : '' }}>
                        <span class="form-colorinput-color" style="background-color: #7950F2"></span>
                    </label>
                </div>
                <div class="col">
                    <label class="form-colorinput mb-0">
                        <input name="theme-primary" type="radio" value="#4C6EF5" class="form-colorinput-input" {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#4C6EF5') ? 'checked' : '' }}>
                        <span class="form-colorinput-color" style="background-color: #4C6EF5"></span>
                    </label>
                </div>
                <div class="col">
                    <label class="form-colorinput mb-0">
                        <input name="theme-primary" type="radio" value="#228BE6" class="form-colorinput-input" {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#228BE6') ? 'checked' : '' }}>
                        <span class="form-colorinput-color" style="background-color: #228BE6"></span>
                    </label>
                </div>
                <div class="col">
                    <label class="form-colorinput mb-0">
                        <input name="theme-primary" type="radio" value="#15AABF" class="form-colorinput-input" {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#15AABF') ? 'checked' : '' }}>
                        <span class="form-colorinput-color" style="background-color: #15AABF"></span>
                    </label>
                </div>
                <div class="col">
                    <label class="form-colorinput mb-0">
                        <input name="theme-primary" type="radio" value="#12B886" class="form-colorinput-input" {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#12B886') ? 'checked' : '' }}>
                        <span class="form-colorinput-color" style="background-color: #12B886"></span>
                    </label>
                </div>
                <div class="col">
                    <label class="form-colorinput mb-0">
                        <input name="theme-primary" type="radio" value="#40C057" class="form-colorinput-input" {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#40C057') ? 'checked' : '' }}>
                        <span class="form-colorinput-color" style="background-color: #40C057"></span>
                    </label>
                </div>
                <div class="col">
                    <label class="form-colorinput mb-0">
                        <input name="theme-primary" type="radio" value="#82C91E" class="form-colorinput-input" {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#82C91E') ? 'checked' : '' }}>
                        <span class="form-colorinput-color" style="background-color: #82C91E"></span>
                    </label>
                </div>
                <div class="col">
                    <label class="form-colorinput mb-0">
                        <input name="theme-primary" type="radio" value="#FAB005" class="form-colorinput-input" {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#FAB005') ? 'checked' : '' }}>
                        <span class="form-colorinput-color" style="background-color: #FAB005"></span>
                    </label>
                </div>
                <div class="col">
                    <label class="form-colorinput mb-0">
                        <input name="theme-primary" type="radio" value="#FD7E14" class="form-colorinput-input" {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#FD7E14') ? 'checked' : '' }}>
                        <span class="form-colorinput-color" style="background-color: #FD7E14"></span>
                    </label>
                </div>
                <div class="col">
                    <label class="form-colorinput mb-0">
                        <input name="theme-primary" type="radio" value="#FF922B" class="form-colorinput-input" {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#FF922B') ? 'checked' : '' }}>
                        <span class="form-colorinput-color" style="background-color: #FF922B"></span>
                    </label>
                </div>
                <div class="col">
                    <label class="form-colorinput mb-0">
                        <input name="theme-primary" type="radio" value="#FCC419" class="form-colorinput-input" {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#FCC419') ? 'checked' : '' }}>
                        <span class="form-colorinput-color" style="background-color: #FCC419"></span>
                    </label>
                </div>
                <div class="col">
                    <label class="form-colorinput mb-0">
                        <input name="theme-primary" type="radio" value="#F03E3E" class="form-colorinput-input" {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#F03E3E') ? 'checked' : '' }}>
                        <span class="form-colorinput-color" style="background-color: #F03E3E"></span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Köşe yuvarlaklığı -->
        <div class="list-group-item py-2 px-3 bg-subtle">
            <strong><i class="fa-solid fa-square-corners me-2"></i>Köşe Yuvarlaklığı</strong>
        </div>
        <div class="p-3 border-bottom">
            <input type="range" class="form-range" id="radius-slider" min="0" max="4" step="1" value="{{ $radiusValue = isset($_COOKIE['themeRadius']) ? (($_COOKIE['themeRadius'] == '0') ? 0 : (($_COOKIE['themeRadius'] == '0.25rem') ? 1 : (($_COOKIE['themeRadius'] == '0.5rem') ? 2 : (($_COOKIE['themeRadius'] == '0.75rem') ? 3 : 4)))) : 2 }}">
            <div class="d-flex justify-content-between mt-1">
                <div class="radius-example" style="width:45px; height:24px; border:1px solid var(--tblr-border-color); border-radius: 0;"></div>
                <div class="radius-example" style="width:45px; height:24px; border:1px solid var(--tblr-border-color); border-radius: 0.25rem;"></div>
                <div class="radius-example" style="width:45px; height:24px; border:1px solid var(--tblr-border-color); border-radius: 0.5rem;"></div>
                <div class="radius-example" style="width:45px; height:24px; border:1px solid var(--tblr-border-color); border-radius: 0.75rem;"></div>
                <div class="radius-example" style="width:45px; height:24px; border:1px solid var(--tblr-border-color); border-radius: 1rem;"></div>
            </div>
            <input type="hidden" id="radius-value" name="theme-radius" value="{{ isset($_COOKIE['themeRadius']) ? $_COOKIE['themeRadius'] : '0.5rem' }}">
        </div>

        <!-- Yazı tipi -->
        <div class="list-group list-group-flush border-bottom">
            <div class="list-group-item py-2 px-3 bg-subtle">
                <strong><i class="fa-solid fa-font me-2"></i>Yazı Tipi</strong>
            </div>
            <label class="list-group-item py-2 d-flex align-items-center">
                <input type="radio" name="theme-font" class="form-check-input me-2" value="Inter, system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, 'Noto Sans', sans-serif" {{ (!isset($_COOKIE['themeFont']) || $_COOKIE['themeFont'] == "Inter, system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, 'Noto Sans', sans-serif") ? 'checked' : '' }}>
                <span style="font-family: Inter, system-ui;">Inter</span>
            </label>
            <label class="list-group-item py-2 d-flex align-items-center">
                <input type="radio" name="theme-font" class="form-check-input me-2" value="'Roboto', sans-serif" {{ (isset($_COOKIE['themeFont']) && $_COOKIE['themeFont'] == "'Roboto', sans-serif") ? 'checked' : '' }}>
                <span style="font-family: 'Roboto', sans-serif;">Roboto</span>
            </label>
            <label class="list-group-item py-2 d-flex align-items-center">
                <input type="radio" name="theme-font" class="form-check-input me-2" value="'Poppins', sans-serif" {{ (isset($_COOKIE['themeFont']) && $_COOKIE['themeFont'] == "'Poppins', sans-serif") ? 'checked' : '' }}>
                <span style="font-family: 'Poppins', sans-serif;">Poppins</span>
            </label>
            <label class="list-group-item py-2 d-flex align-items-center">
                <input type="radio" name="theme-font" class="form-check-input me-2" value="Georgia, 'Times New Roman', Times, serif" {{ (isset($_COOKIE['themeFont']) && $_COOKIE['themeFont'] == "Georgia, 'Times New Roman', Times, serif") ? 'checked' : '' }}>
                <span style="font-family: Georgia, 'Times New Roman', Times, serif;">Georgia</span>
            </label>
            <label class="list-group-item py-2 d-flex align-items-center">
                <input type="radio" name="theme-font" class="form-check-input me-2" value="'Courier New', Courier, monospace" {{ (isset($_COOKIE['themeFont']) && $_COOKIE['themeFont'] == "'Courier New', Courier, monospace") ? 'checked' : '' }}>
                <span style="font-family: 'Courier New', Courier, monospace;">Courier</span>
            </label>
        </div>

        <!-- Gri ton -->
        <div class="list-group list-group-flush border-bottom">
            <div class="list-group-item py-2 px-3 bg-subtle">
                <strong><i class="fa-solid fa-swatchbook me-2"></i>Gri Ton</strong>
            </div>
            <label class="list-group-item py-2 d-flex align-items-center">
                <input type="radio" name="theme-base" class="form-check-input me-2" value="slate" {{ (isset($_COOKIE['themeBase']) && $_COOKIE['themeBase'] == 'slate') ? 'checked' : '' }}>
                <span>Slate</span>
                <span class="ms-auto avatar" style="background: linear-gradient(to right, #0f172a, #f8fafc);"></span>
            </label>
            <label class="list-group-item py-2 d-flex align-items-center">
                <input type="radio" name="theme-base" class="form-check-input me-2" value="gray" {{ (!isset($_COOKIE['themeBase']) || $_COOKIE['themeBase'] == 'gray') ? 'checked' : '' }}>
                <span>Gray</span>
                <span class="ms-auto avatar" style="background: linear-gradient(to right, #111827, #f9fafb);"></span>
            </label>
            <label class="list-group-item py-2 d-flex align-items-center">
                <input type="radio" name="theme-base" class="form-check-input me-2" value="zinc" {{ (isset($_COOKIE['themeBase']) && $_COOKIE['themeBase'] == 'zinc') ? 'checked' : '' }}>
                <span>Zinc</span>
                <span class="ms-auto avatar" style="background: linear-gradient(to right, #18181b, #fafafa);"></span>
            </label>
            <label class="list-group-item py-2 d-flex align-items-center">
                <input type="radio" name="theme-base" class="form-check-input me-2" value="neutral" {{ (isset($_COOKIE['themeBase']) && $_COOKIE['themeBase'] == 'neutral') ? 'checked' : '' }}>
                <span>Neutral</span>
                <span class="ms-auto avatar" style="background: linear-gradient(to right, #171717, #fafafa);"></span>
            </label>
            <label class="list-group-item py-2 d-flex align-items-center">
                <input type="radio" name="theme-base" class="form-check-input me-2" value="stone" {{ (isset($_COOKIE['themeBase']) && $_COOKIE['themeBase'] == 'stone') ? 'checked' : '' }}>
                <span>Stone</span>
                <span class="ms-auto avatar" style="background: linear-gradient(to right, #1c1917, #fafaf9);"></span>
            </label>
        </div>

        <!-- Tablo görünümü -->
        <div class="list-group list-group-flush border-bottom">
            <div class="list-group-item py-2 px-3 bg-subtle">
                <strong><i class="fa-solid fa-table-list me-2"></i>Tablo Görünümü</strong>
            </div>
            <div class="list-group-item py-2 d-flex justify-content-around">
                <label class="d-flex flex-column align-items-center">
                    <input type="radio" name="table-compact" value="1" class="form-check-input mb-2" {{ (!isset($_COOKIE['tableCompact']) || $_COOKIE['tableCompact'] == '1') ? 'checked' : '' }}>
                    <i class="fa-solid fa-table-cells fa-lg"></i>
                    <span class="d-block mt-1 small">Kompakt</span>
                </label>
                <label class="d-flex flex-column align-items-center">
                    <input type="radio" name="table-compact" value="0" class="form-check-input mb-2" {{ (isset($_COOKIE['tableCompact']) && $_COOKIE['tableCompact'] == '0') ? 'checked' : '' }}>
                    <i class="fa-solid fa-table fa-lg"></i>
                    <span class="d-block mt-1 small">Normal</span>
                </label>
            </div>
        </div>
        
        <!-- Sıfırlama düğmesi -->
        <div class="p-3">
            <button type="button" class="btn btn-danger w-100" id="reset-changes">
                <i class="fa-solid fa-rotate-left me-2"></i>Varsayılan Ayarlar
            </button>
        </div>
    </div>
</div>
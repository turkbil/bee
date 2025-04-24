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
<div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasTheme" aria-labelledby="offcanvasThemeLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasThemeLabel">Tema Ayarları</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <div class="mb-3">
            <label class="form-label">Renk modu</label>
            <p class="form-hint mb-2">Uygulamanız için renk modunu seçin.</p>
            <label class="form-check">
                <div class="form-selectgroup-item">
                    <input type="radio" name="theme" value="light" class="form-check-input" {{ !isset($_COOKIE['dark']) || $_COOKIE['dark'] != '1' ? 'checked' : '' }} />
                    <div class="form-check-label">Açık</div>
                </div>
            </label>
            <label class="form-check">
                <div class="form-selectgroup-item">
                    <input type="radio" name="theme" value="dark" class="form-check-input" {{ isset($_COOKIE['dark']) && $_COOKIE['dark'] == '1' ? 'checked' : '' }} />
                    <div class="form-check-label">Koyu</div>
                </div>
            </label>
        </div>
        <div class="mb-3">
            <label class="form-label">Renk şeması</label>
            <p class="form-hint mb-2">Uygulamanız için mükemmel renk şemasını seçin.</p>
            <div class="row g-2">
                <!-- Kırmızı Tonları - İlk Satır -->
                <div class="col-auto">
                    <label class="form-colorinput">
                        <input name="theme-primary" type="radio" value="#FA5252" class="form-colorinput-input" {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#FA5252') ? 'checked' : '' }} />
                        <span class="form-colorinput-color" style="background-color: #FA5252"></span>
                    </label>
                </div>
                <div class="col-auto">
                    <label class="form-colorinput">
                        <input name="theme-primary" type="radio" value="#E64980" class="form-colorinput-input" {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#E64980') ? 'checked' : '' }} />
                        <span class="form-colorinput-color" style="background-color: #E64980"></span>
                    </label>
                </div>
                <div class="col-auto">
                    <label class="form-colorinput">
                        <input name="theme-primary" type="radio" value="#BE4BDB" class="form-colorinput-input" {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#BE4BDB') ? 'checked' : '' }} />
                        <span class="form-colorinput-color" style="background-color: #BE4BDB"></span>
                    </label>
                </div>
                <div class="col-auto">
                    <label class="form-colorinput">
                        <input name="theme-primary" type="radio" value="#7950F2" class="form-colorinput-input" {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#7950F2') ? 'checked' : '' }} />
                        <span class="form-colorinput-color" style="background-color: #7950F2"></span>
                    </label>
                </div>
                <div class="col-auto">
                    <label class="form-colorinput">
                        <input name="theme-primary" type="radio" value="#4C6EF5" class="form-colorinput-input" {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#4C6EF5') ? 'checked' : '' }} />
                        <span class="form-colorinput-color" style="background-color: #4C6EF5"></span>
                    </label>
                </div>
                <div class="col-auto">
                    <label class="form-colorinput">
                        <input name="theme-primary" type="radio" value="#228BE6" class="form-colorinput-input" {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#228BE6') ? 'checked' : '' }} />
                        <span class="form-colorinput-color" style="background-color: #228BE6"></span>
                    </label>
                </div>
                <div class="col-auto">
                    <label class="form-colorinput">
                        <input name="theme-primary" type="radio" value="#15AABF" class="form-colorinput-input" {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#15AABF') ? 'checked' : '' }} />
                        <span class="form-colorinput-color" style="background-color: #15AABF"></span>
                    </label>
                </div>
                <div class="col-auto">
                    <label class="form-colorinput">
                        <input name="theme-primary" type="radio" value="#12B886" class="form-colorinput-input" {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#12B886') ? 'checked' : '' }} />
                        <span class="form-colorinput-color" style="background-color: #12B886"></span>
                    </label>
                </div>
            </div>

            <div class="row g-2 mt-1">
                <!-- İkinci Satır -->
                <div class="col-auto">
                    <label class="form-colorinput">
                        <input name="theme-primary" type="radio" value="#40C057" class="form-colorinput-input" {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#40C057') ? 'checked' : '' }} />
                        <span class="form-colorinput-color" style="background-color: #40C057"></span>
                    </label>
                </div>
                <div class="col-auto">
                    <label class="form-colorinput">
                        <input name="theme-primary" type="radio" value="#82C91E" class="form-colorinput-input" {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#82C91E') ? 'checked' : '' }} />
                        <span class="form-colorinput-color" style="background-color: #82C91E"></span>
                    </label>
                </div>
                <div class="col-auto">
                    <label class="form-colorinput">
                        <input name="theme-primary" type="radio" value="#FAB005" class="form-colorinput-input" {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#FAB005') ? 'checked' : '' }} />
                        <span class="form-colorinput-color" style="background-color: #FAB005"></span>
                    </label>
                </div>
                <div class="col-auto">
                    <label class="form-colorinput">
                        <input name="theme-primary" type="radio" value="#FD7E14" class="form-colorinput-input" {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#FD7E14') ? 'checked' : '' }} />
                        <span class="form-colorinput-color" style="background-color: #FD7E14"></span>
                    </label>
                </div>
                <div class="col-auto">
                    <label class="form-colorinput">
                        <input name="theme-primary" type="radio" value="#FF922B" class="form-colorinput-input" {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#FF922B') ? 'checked' : '' }} />
                        <span class="form-colorinput-color" style="background-color: #FF922B"></span>
                    </label>
                </div>
                <div class="col-auto">
                    <label class="form-colorinput">
                        <input name="theme-primary" type="radio" value="#FCC419" class="form-colorinput-input" {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#FCC419') ? 'checked' : '' }} />
                        <span class="form-colorinput-color" style="background-color: #FCC419"></span>
                    </label>
                </div>
                <div class="col-auto">
                    <label class="form-colorinput">
                        <input name="theme-primary" type="radio" value="#FF6B6B" class="form-colorinput-input" {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#FF6B6B') ? 'checked' : '' }} />
                        <span class="form-colorinput-color" style="background-color: #FF6B6B"></span>
                    </label>
                </div>
                <div class="col-auto">
                    <label class="form-colorinput">
                        <input name="theme-primary" type="radio" value="#F76707" class="form-colorinput-input" {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#F76707') ? 'checked' : '' }} />
                        <span class="form-colorinput-color" style="background-color: #F76707"></span>
                    </label>
                </div>
            </div>

            <div class="row g-2 mt-1">
                <!-- Üçüncü Satır -->
                <div class="col-auto">
                    <label class="form-colorinput">
                        <input name="theme-primary" type="radio" value="#2B8A3E" class="form-colorinput-input" {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#2B8A3E') ? 'checked' : '' }} />
                        <span class="form-colorinput-color" style="background-color: #2B8A3E"></span>
                    </label>
                </div>
                <div class="col-auto">
                    <label class="form-colorinput">
                        <input name="theme-primary" type="radio" value="#5F3DC4" class="form-colorinput-input" {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#5F3DC4') ? 'checked' : '' }} />
                        <span class="form-colorinput-color" style="background-color: #5F3DC4"></span>
                    </label>
                </div>
                <div class="col-auto">
                    <label class="form-colorinput">
                        <input name="theme-primary" type="radio" value="#1971C2" class="form-colorinput-input" {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#1971C2') ? 'checked' : '' }} />
                        <span class="form-colorinput-color" style="background-color: #1971C2"></span>
                    </label>
                </div>
                <div class="col-auto">
                    <label class="form-colorinput">
                        <input name="theme-primary" type="radio" value="#066fd1" class="form-colorinput-input" {{ (!isset($_COOKIE['siteColor']) || $_COOKIE['siteColor'] == '#066fd1') ? 'checked' : '' }} />
                        <span class="form-colorinput-color" style="background-color: #066fd1"></span>
                    </label>
                </div>
                <div class="col-auto">
                    <label class="form-colorinput">
                        <input name="theme-primary" type="radio" value="#0CA678" class="form-colorinput-input" {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#0CA678') ? 'checked' : '' }} />
                        <span class="form-colorinput-color" style="background-color: #0CA678"></span>
                    </label>
                </div>
                <div class="col-auto">
                    <label class="form-colorinput">
                        <input name="theme-primary" type="radio" value="#F03E3E" class="form-colorinput-input" {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#F03E3E') ? 'checked' : '' }} />
                        <span class="form-colorinput-color" style="background-color: #F03E3E"></span>
                    </label>
                </div>
                <div class="col-auto">
                    <label class="form-colorinput">
                        <input name="theme-primary" type="radio" value="#5C940D" class="form-colorinput-input" {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#5C940D') ? 'checked' : '' }} />
                        <span class="form-colorinput-color" style="background-color: #5C940D"></span>
                    </label>
                </div>
                <div class="col-auto">
                    <label class="form-colorinput">
                        <input name="theme-primary" type="radio" value="#1864AB" class="form-colorinput-input" {{ (isset($_COOKIE['siteColor']) && $_COOKIE['siteColor'] == '#1864AB') ? 'checked' : '' }} />
                        <span class="form-colorinput-color" style="background-color: #1864AB"></span>
                    </label>
                </div>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Gri Tonu</label>
            <p class="form-hint mb-2">Uygulamanız için gri tonu seçin.</p>
            <div>
                <label class="form-check">
                    <div class="form-selectgroup-item">
                        <input type="radio" name="theme-base" value="slate" class="form-check-input" {{ (isset($_COOKIE['themeBase']) && $_COOKIE['themeBase'] == 'slate') ? 'checked' : '' }} />
                        <div class="form-check-label">Slate (Koyu)</div>
                    </div>
                </label>
                <label class="form-check">
                    <div class="form-selectgroup-item">
                        <input type="radio" name="theme-base" value="gray" class="form-check-input" {{ (!isset($_COOKIE['themeBase']) || $_COOKIE['themeBase'] == 'gray') ? 'checked' : '' }} />
                        <div class="form-check-label">Gray (Standart)</div>
                    </div>
                </label>
                <label class="form-check">
                    <div class="form-selectgroup-item">
                        <input type="radio" name="theme-base" value="zinc" class="form-check-input" {{ (isset($_COOKIE['themeBase']) && $_COOKIE['themeBase'] == 'zinc') ? 'checked' : '' }} />
                        <div class="form-check-label">Zinc (Metal)</div>
                    </div>
                </label>
                <label class="form-check">
                    <div class="form-selectgroup-item">
                        <input type="radio" name="theme-base" value="neutral" class="form-check-input" {{ (isset($_COOKIE['themeBase']) && $_COOKIE['themeBase'] == 'neutral') ? 'checked' : '' }} />
                        <div class="form-check-label">Neutral (Nötr)</div>
                    </div>
                </label>
                <label class="form-check">
                    <div class="form-selectgroup-item">
                        <input type="radio" name="theme-base" value="stone" class="form-check-input" {{ (isset($_COOKIE['themeBase']) && $_COOKIE['themeBase'] == 'stone') ? 'checked' : '' }} />
                        <div class="form-check-label">Stone (Taş)</div>
                    </div>
                </label>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Yazı tipi</label>
            <p class="form-hint mb-2">Uygulamanıza uygun yazı tipini seçin.</p>
            <div>
                <label class="form-check">
                    <div class="form-selectgroup-item">
                        <input type="radio" name="theme-font" value="Inter, system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, 'Noto Sans', sans-serif" class="form-check-input" {{ (!isset($_COOKIE['themeFont']) || $_COOKIE['themeFont'] == "Inter, system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, 'Noto Sans', sans-serif") ? 'checked' : '' }} />
                        <div class="form-check-label">Inter (Modern)</div>
                    </div>
                </label>
                <label class="form-check">
                    <div class="form-selectgroup-item">
                        <input type="radio" name="theme-font" value="'Roboto', sans-serif" class="form-check-input" {{ (isset($_COOKIE['themeFont']) && $_COOKIE['themeFont'] == "'Roboto', sans-serif") ? 'checked' : '' }} />
                        <div class="form-check-label">Roboto (Yaygın)</div>
                    </div>
                </label>
                <label class="form-check">
                    <div class="form-selectgroup-item">
                        <input type="radio" name="theme-font" value="'Open Sans', sans-serif" class="form-check-input" {{ (isset($_COOKIE['themeFont']) && $_COOKIE['themeFont'] == "'Open Sans', sans-serif") ? 'checked' : '' }} />
                        <div class="form-check-label">Open Sans (Net)</div>
                    </div>
                </label>
                <label class="form-check">
                    <div class="form-selectgroup-item">
                        <input type="radio" name="theme-font" value="Georgia, 'Times New Roman', Times, serif" class="form-check-input" {{ (isset($_COOKIE['themeFont']) && $_COOKIE['themeFont'] == "Georgia, 'Times New Roman', Times, serif") ? 'checked' : '' }} />
                        <div class="form-check-label">Georgia (Klasik)</div>
                    </div>
                </label>
                <label class="form-check">
                    <div class="form-selectgroup-item">
                        <input type="radio" name="theme-font" value="'Courier New', Courier, monospace" class="form-check-input" {{ (isset($_COOKIE['themeFont']) && $_COOKIE['themeFont'] == "'Courier New', Courier, monospace") ? 'checked' : '' }} />
                        <div class="form-check-label">Courier (Kod)</div>
                    </div>
                </label>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Köşe Yuvarlaklığı</label>
            <p class="form-hint mb-2">Uygulamanız için köşe yuvarlaklığını seçin.</p>
            <div>
                <label class="form-check">
                    <div class="form-selectgroup-item">
                        <input type="radio" name="theme-radius" value="0" class="form-check-input" {{ (isset($_COOKIE['themeRadius']) && $_COOKIE['themeRadius'] == '0') ? 'checked' : '' }} />
                        <div class="form-check-label">Keskin Köşeler</div>
                    </div>
                </label>
                <label class="form-check">
                    <div class="form-selectgroup-item">
                        <input type="radio" name="theme-radius" value="0.25rem" class="form-check-input" {{ (isset($_COOKIE['themeRadius']) && $_COOKIE['themeRadius'] == '0.25rem') ? 'checked' : '' }} />
                        <div class="form-check-label">Hafif Yuvarlatılmış</div>
                    </div>
                </label>
                <label class="form-check">
                    <div class="form-selectgroup-item">
                        <input type="radio" name="theme-radius" value="0.5rem" class="form-check-input" {{ (!isset($_COOKIE['themeRadius']) || $_COOKIE['themeRadius'] == '0.5rem') ? 'checked' : '' }} />
                        <div class="form-check-label">Standart</div>
                    </div>
                </label>
                <label class="form-check">
                    <div class="form-selectgroup-item">
                        <input type="radio" name="theme-radius" value="0.75rem" class="form-check-input" {{ (isset($_COOKIE['themeRadius']) && $_COOKIE['themeRadius'] == '0.75rem') ? 'checked' : '' }} />
                        <div class="form-check-label">Yuvarlak</div>
                    </div>
                </label>
                <label class="form-check">
                    <div class="form-selectgroup-item">
                        <input type="radio" name="theme-radius" value="1rem" class="form-check-input" {{ (isset($_COOKIE['themeRadius']) && $_COOKIE['themeRadius'] == '1rem') ? 'checked' : '' }} />
                        <div class="form-check-label">Çok Yuvarlak</div>
                    </div>
                </label>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Tablo Kompakt Görünümü</label>
            <p class="form-hint mb-2">Tabloların daha kompakt görünmesini sağlar.</p>
            <div>
                <label class="form-check">
                    <div class="form-selectgroup-item">
                        <input type="radio" name="table-compact" value="1" class="form-check-input" {{ (!isset($_COOKIE['tableCompact']) || $_COOKIE['tableCompact'] == '1') ? 'checked' : '' }} />
                        <div class="form-check-label">Kompakt</div>
                    </div>
                </label>
                <label class="form-check">
                    <div class="form-selectgroup-item">
                        <input type="radio" name="table-compact" value="0" class="form-check-input" {{ (isset($_COOKIE['tableCompact']) && $_COOKIE['tableCompact'] == '0') ? 'checked' : '' }} />
                        <div class="form-check-label">Normal</div>
                    </div>
                </label>
            </div>
        </div>
        <div class="mt-4">
            <button type="button" class="btn w-100" id="reset-changes">
                <i class="fa-solid fa-rotate me-1"></i>
                Değişiklikleri Sıfırla
            </button>
        </div>
    </div>
</div>
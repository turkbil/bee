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
    
    // Servis üzerinden modülleri al
    $modules = $moduleService->getModulesByTenant($tenantId);
    
    // Modülleri tipine göre grupla
    $groupedModules = $moduleService->groupModulesByType($modules);
    
    // Aktif segment/tip bilgisini al
    $activeType = request()->segment(2);
@endphp

<header class="navbar navbar-expand-md d-print-none">
    <div class="container-xl">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <h1 class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal pe-0 pe-md-3">
            <a href="{{ route('admin.dashboard') }}">
                {{ config('app.name') }}
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
            <div class="color-mode theme-color-mode me-2">
                <div class="color-picker-container" style="position: relative;">
                    <div id="selectedColor" class="form-control form-control-color"
                        style="width: 16px; height: 16px; border-radius: 16px; cursor: pointer; background-color: #2196F3;"
                        onclick="toggleColorPicker()" data-bs-toggle="tooltip" data-bs-placement="left"
                        title="Tema Rengi"></div>
                    <div id="colorPickerDropdown"
                        style="z-index: 99999; display: none; position: absolute; top: 100%; right: 0; width: 220px; background: var(--tblr-bg-surface); border: 1px solid var(--tblr-border-color); border-radius: 4px; padding: 10px; margin-top: 5px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); flex-wrap: wrap; gap: 5px;">
                        <!-- Renk Seçenekleri -->
                        <!-- Kırmızı Tonları -->
                        <div class="color-option"
                            style="background-color: #FF7F7F; width: 20px; height: 20px; cursor: pointer; border-radius: 4px; border: 1px solid var(--tblr-border-color);"
                            onclick="changeColor('#FF7F7F')" data-color="#FF7F7F"></div>
                        <div class="color-option"
                            style="background-color: #FF5252; width: 20px; height: 20px; cursor: pointer; border-radius: 4px; border: 1px solid var(--tblr-border-color);"
                            onclick="changeColor('#FF5252')" data-color="#FF5252"></div>
                        <div class="color-option"
                            style="background-color: #F44336; width: 20px; height: 20px; cursor: pointer; border-radius: 4px; border: 1px solid var(--tblr-border-color);"
                            onclick="changeColor('#F44336')" data-color="#F44336"></div>

                        <!-- Turuncu Tonları -->
                        <div class="color-option"
                            style="background-color: #FFB74D; width: 20px; height: 20px; cursor: pointer; border-radius: 4px; border: 1px solid var(--tblr-border-color);"
                            onclick="changeColor('#FFB74D')" data-color="#FFB74D"></div>
                        <div class="color-option"
                            style="background-color: #FF9800; width: 20px; height: 20px; cursor: pointer; border-radius: 4px; border: 1px solid var(--tblr-border-color);"
                            onclick="changeColor('#FF9800')" data-color="#FF9800"></div>
                        <div class="color-option"
                            style="background-color: #F57C00; width: 20px; height: 20px; cursor: pointer; border-radius: 4px; border: 1px solid var(--tblr-border-color);"
                            onclick="changeColor('#F57C00')" data-color="#F57C00"></div>

                        <!-- Sarı Tonları -->
                        <div class="color-option"
                            style="background-color: #FFF176; width: 20px; height: 20px; cursor: pointer; border-radius: 4px; border: 1px solid var(--tblr-border-color);"
                            onclick="changeColor('#FFF176')" data-color="#FFF176"></div>
                        <div class="color-option"
                            style="background-color: #FFEB3B; width: 20px; height: 20px; cursor: pointer; border-radius: 4px; border: 1px solid var(--tblr-border-color);"
                            onclick="changeColor('#FFEB3B')" data-color="#FFEB3B"></div>
                        <div class="color-option"
                            style="background-color: #FBC02D; width: 20px; height: 20px; cursor: pointer; border-radius: 4px; border: 1px solid var(--tblr-border-color);"
                            onclick="changeColor('#FBC02D')" data-color="#FBC02D"></div>

                        <!-- Yeşil Tonları -->
                        <div class="color-option"
                            style="background-color: #81C784; width: 20px; height: 20px; cursor: pointer; border-radius: 4px; border: 1px solid var(--tblr-border-color);"
                            onclick="changeColor('#81C784')" data-color="#81C784"></div>
                        <div class="color-option"
                            style="background-color: #4CAF50; width: 20px; height: 20px; cursor: pointer; border-radius: 4px; border: 1px solid var(--tblr-border-color);"
                            onclick="changeColor('#4CAF50')" data-color="#4CAF50"></div>
                        <div class="color-option"
                            style="background-color: #2E7D32; width: 20px; height: 20px; cursor: pointer; border-radius: 4px; border: 1px solid var(--tblr-border-color);"
                            onclick="changeColor('#2E7D32')" data-color="#2E7D32"></div>

                        <!-- Turkuaz Tonları -->
                        <div class="color-option"
                            style="background-color: #4DD0E1; width: 20px; height: 20px; cursor: pointer; border-radius: 4px; border: 1px solid var(--tblr-border-color);"
                            onclick="changeColor('#4DD0E1')" data-color="#4DD0E1"></div>
                        <div class="color-option"
                            style="background-color: #00BCD4; width: 20px; height: 20px; cursor: pointer; border-radius: 4px; border: 1px solid var(--tblr-border-color);"
                            onclick="changeColor('#00BCD4')" data-color="#00BCD4"></div>
                        <div class="color-option"
                            style="background-color: #0097A7; width: 20px; height: 20px; cursor: pointer; border-radius: 4px; border: 1px solid var(--tblr-border-color);"
                            onclick="changeColor('#0097A7')" data-color="#0097A7"></div>

                        <!-- Mavi Tonları -->
                        <div class="color-option"
                            style="background-color: #64B5F6; width: 20px; height: 20px; cursor: pointer; border-radius: 4px; border: 1px solid var(--tblr-border-color);"
                            onclick="changeColor('#64B5F6')" data-color="#64B5F6"></div>
                        <div class="color-option"
                            style="background-color: #2196F3; width: 20px; height: 20px; cursor: pointer; border-radius: 4px; border: 1px solid var(--tblr-border-color);"
                            onclick="changeColor('#2196F3')" data-color="#2196F3"></div>
                        <div class="color-option"
                            style="background-color: #1976D2; width: 20px; height: 20px; cursor: pointer; border-radius: 4px; border: 1px solid var(--tblr-border-color);"
                            onclick="changeColor('#1976D2')" data-color="#1976D2"></div>

                        <!-- Mor Tonları -->
                        <div class="color-option"
                            style="background-color: #BA68C8; width: 20px; height: 20px; cursor: pointer; border-radius: 4px; border: 1px solid var(--tblr-border-color);"
                            onclick="changeColor('#BA68C8')" data-color="#BA68C8"></div>
                        <div class="color-option"
                            style="background-color: #9C27B0; width: 20px; height: 20px; cursor: pointer; border-radius: 4px; border: 1px solid var(--tblr-border-color);"
                            onclick="changeColor('#9C27B0')" data-color="#9C27B0"></div>
                        <div class="color-option"
                            style="background-color: #7B1FA2; width: 20px; height: 20px; cursor: pointer; border-radius: 4px; border: 1px solid var(--tblr-border-color);"
                            onclick="changeColor('#7B1FA2')" data-color="#7B1FA2"></div>

                        <!-- Pembe Tonları -->
                        <div class="color-option"
                            style="background-color: #F06292; width: 20px; height: 20px; cursor: pointer; border-radius: 4px; border: 1px solid var(--tblr-border-color);"
                            onclick="changeColor('#F06292')" data-color="#F06292"></div>
                        <div class="color-option"
                            style="background-color: #E91E63; width: 20px; height: 20px; cursor: pointer; border-radius: 4px; border: 1px solid var(--tblr-border-color);"
                            onclick="changeColor('#E91E63')" data-color="#E91E63"></div>
                        <div class="color-option"
                            style="background-color: #C2185B; width: 20px; height: 20px; cursor: pointer; border-radius: 4px; border: 1px solid var(--tblr-border-color);"
                            onclick="changeColor('#C2185B')" data-color="#C2185B"></div>
                    </div>
                </div>
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

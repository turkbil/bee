<div>
    {{-- Welcome Banner --}}
    <div class="card bg-primary text-white mb-3">
        <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h2 class="mb-1">{{ $welcomeMessage['title'] }}</h2>
                <p class="mb-0 opacity-75">{{ $welcomeMessage['subtitle'] }}</p>
            </div>
            <div class="d-flex gap-2">
                @if($visibleWidgets['aiChat'] && $aiCredit > 0)
                <div class="bg-white bg-opacity-25 rounded px-3 py-2 text-center">
                    <div class="fw-bold">{{ $aiCreditFormatted }}</div>
                    <small class="opacity-75">AI Kredi</small>
                </div>
                @endif
                @if($isAdminOrRoot && isset($moduleStats['users']))
                <div class="bg-white bg-opacity-25 rounded px-3 py-2 text-center">
                    <div class="fw-bold">{{ $moduleStats['users']['value'] }}</div>
                    <small class="opacity-75">Kullanıcı</small>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Sol: Modüller --}}
        <div class="col-lg-8">
            {{-- Module Stats --}}
            @if(count($moduleStats) > 0)
            <div class="row g-2 mb-4">
                @foreach($moduleStats as $key => $stat)
                @if(isset($stat['route']))
                <div class="col-6 col-md-3">
                    <a href="{{ route($stat['route']) }}" class="card text-decoration-none">
                        <div class="card-body text-center py-4">
                            <div class="mb-2">
                                <span class="avatar avatar-lg bg-{{ $stat['color'] }}-lt">
                                    <i class="{{ $stat['icon'] }} text-{{ $stat['color'] }}"></i>
                                </span>
                            </div>
                            <div class="fw-medium">{{ $stat['name'] }}</div>
                            <div class="text-muted small">{{ $stat['value'] }} kayıt</div>
                        </div>
                    </a>
                </div>
                @endif
                @endforeach
            </div>
            @endif

            {{-- Module Cards --}}
            @if(count($recentItems) > 0)
            <h4 class="text-muted mb-3">Modüller</h4>
            <div class="row g-3">
                @foreach($recentItems as $moduleKey => $module)
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <div class="d-flex align-items-center gap-2">
                                <span class="avatar avatar-sm bg-{{ $module['color'] }}-lt">
                                    <i class="{{ $module['icon'] }} text-{{ $module['color'] }}"></i>
                                </span>
                                <div>
                                    <div class="fw-bold">{{ $module['title'] }}</div>
                                    <small class="text-muted">{{ count($module['items']) }} kayıt</small>
                                </div>
                            </div>
                            <div class="card-actions">
                                <a href="{{ route($module['route']) }}" class="btn btn-sm">Listele</a>
                                @if($this->canCreateModule($moduleKey))
                                <a href="{{ route($module['manageRoute']) }}" class="btn btn-sm btn-primary">+ Ekle</a>
                                @endif
                            </div>
                        </div>
                        <div class="list-group list-group-flush">
                            @foreach(array_slice($module['items'], 0, 2) as $item)
                            <div class="list-group-item d-flex align-items-center py-2">
                                <span class="status-dot status-{{ $item['status'] ? 'green' : 'yellow' }} me-2"></span>
                                <span class="text-truncate flex-fill">{{ $item['title'] }}</span>
                                <small class="text-muted">{{ $item['date'] }}</small>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Sağ: Sidebar --}}
        <div class="col-lg-4">
            {{-- Profile --}}
            @if($visibleWidgets['profile'])
            <div class="card mb-3">
                <div class="card-body text-center">
                    <span class="avatar avatar-xl mb-3 rounded" style="background: linear-gradient(135deg, #667eea, #764ba2); font-size: 1.5rem;">
                        {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
                    </span>
                    <h3 class="mb-0">{{ auth()->user()->name }}</h3>
                    <p class="text-muted mb-3">{{ auth()->user()->roles->first()->name ?? 'Kullanıcı' }}</p>
                    <div class="row g-2">
                        <div class="col">
                            <div class="fw-bold">{{ count($activeModules) }}</div>
                            <small class="text-muted">Modül</small>
                        </div>
                        <div class="col">
                            <div class="fw-bold">{{ (int) auth()->user()->created_at->diffInDays(now()) }}</div>
                            <small class="text-muted">Gün</small>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- System Status --}}
            @if($visibleWidgets['systemStatus'])
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title m-0">
                        <span class="status-dot status-dot-animated status-green me-2"></span>
                        Sistem Durumu
                    </h4>
                </div>
                <div class="card-body py-2">
                    <div class="datagrid">
                        <div class="datagrid-item">
                            <div class="datagrid-title">PHP</div>
                            <div class="datagrid-content text-success">{{ PHP_VERSION }}</div>
                        </div>
                        <div class="datagrid-item">
                            <div class="datagrid-title">Laravel</div>
                            <div class="datagrid-content text-success">{{ app()->version() }}</div>
                        </div>
                        <div class="datagrid-item">
                            <div class="datagrid-title">Database</div>
                            <div class="datagrid-content text-success">Bağlı</div>
                        </div>
                        <div class="datagrid-item">
                            <div class="datagrid-title">Cache</div>
                            <div class="datagrid-content text-success">Aktif</div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

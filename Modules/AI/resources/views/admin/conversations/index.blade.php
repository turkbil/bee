@extends('admin.layout')

@include('ai::helper')

@section('pretitle', 'AI Konuşmaları ve Test Geçmişi')
@section('title', 'Konuşma Geçmişi & AI Test Arşivi')

@section('content')
    <!-- İstatistik Kartları -->
    <div class="row mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Toplam Konuşma</div>
                    </div>
                    <div class="h1 mb-3">{{ number_format($stats['total']) }}</div>
                    <div class="d-flex mb-2">
                        <div class="text-muted">Tüm konuşmalar</div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Özellik Testleri</div>
                    </div>
                    <div class="h1 mb-3">{{ number_format($stats['feature_tests']) }}</div>
                    <div class="d-flex mb-2">
                        <div class="text-muted">AI test kayıtları</div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Demo Testler</div>
                    </div>
                    <div class="h1 mb-3 text-info">{{ number_format($stats['demo_tests']) }}</div>
                    <div class="d-flex mb-2">
                        <div class="text-muted">Ücretsiz testler</div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Gerçek AI Testleri</div>
                    </div>
                    <div class="h1 mb-3 text-success">{{ number_format($stats['real_tests']) }}</div>
                    <div class="d-flex mb-2">
                        <div class="text-muted">Token tüketen testler</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Credit İstatistikleri Kartları -->
    <div class="row mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Toplam Credit Kullanımı</div>
                    </div>
                    <div class="h1 mb-3 text-warning">{{ number_format($creditStats['total_credits_used'], 2) }}</div>
                    <div class="d-flex mb-2">
                        <div class="text-muted">Aktif konuşmalarda</div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Ortalama Credit/Konuşma</div>
                    </div>
                    <div class="h1 mb-3 text-info">{{ number_format($creditStats['avg_credits_per_conversation'], 2) }}</div>
                    <div class="d-flex mb-2">
                        <div class="text-muted">Konuşma başına</div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Demo Credit Kullanımı</div>
                    </div>
                    <div class="h1 mb-3 text-muted">{{ number_format($creditStats['demo_credits_used'], 2) }}</div>
                    <div class="d-flex mb-2">
                        <div class="text-muted">Ücretsiz testlerde</div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Gerçek AI Credit Kullanımı</div>
                    </div>
                    <div class="h1 mb-3 text-primary">{{ number_format($creditStats['real_credits_used'], 2) }}</div>
                    <div class="d-flex mb-2">
                        <div class="text-muted">Ücretli testlerde</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Arşiv Butonu -->
    <div class="mb-3">
        <a href="{{ route('admin.ai.conversations.archived') }}" class="btn btn-outline-warning">
            <i class="fas fa-archive me-2"></i>Arşivlenmiş Konuşmalar
        </a>
    </div>

    <!-- Modern Filtre ve Arama Bölümü -->
    <div class="card">
        <div class="card-body">
            <!-- Header Bölümü -->
            <div class="row mb-3">
                <!-- Arama Kutusu -->
                <div class="col">
                    <div class="input-icon">
                        <span class="input-icon-addon">
                            <i class="fas fa-search"></i>
                        </span>
                        <form method="GET" action="{{ route('admin.ai.conversations.index') }}" style="display: inline;">
                            @foreach(request()->except('search') as $key => $value)
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endforeach
                            <input type="text" name="search" class="form-control" placeholder="Başlık, kullanıcı veya özellik ara..." 
                                   value="{{ request('search') }}" onchange="this.form.submit()">
                        </form>
                    </div>
                </div>
                
                <!-- Ortadaki Loading -->
                <div class="col position-relative">
                    <div class="position-absolute top-50 start-50 translate-middle text-center"
                        style="width: 100%; max-width: 250px; display: none;" id="loadingIndicator">
                        <div class="small text-muted mb-2">Güncelleniyor...</div>
                        <div class="progress mb-1">
                            <div class="progress-bar progress-bar-indeterminate"></div>
                        </div>
                    </div>
                </div>
                
                <!-- Sağ Taraf (Filtre ve Sayfa Seçimi) -->
                <div class="col">
                    <div class="d-flex align-items-center justify-content-end gap-3">
                        <!-- Filtre Butonu -->
                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="collapse" 
                            data-bs-target="#filterCollapse" aria-expanded="false" aria-controls="filterCollapse">
                            <i class="fas fa-filter me-1"></i>
                            Filtreler
                            @if(request('type') || request('feature_name') || request('is_demo') || (request('status') && request('status') !== 'active') || (request('tenant_id') && auth()->user()->isRoot()))
                            <span class="badge badge-primary ms-1">Aktif</span>
                            @endif
                        </button>
                        
                        <!-- Sayfa Adeti Seçimi -->
                        <div style="min-width: 70px">
                            <select class="form-select" onchange="window.location.href=this.value">
                                <option value="{{ route('admin.ai.conversations.index', array_merge(request()->query(), ['per_page' => 10])) }}" {{ request('per_page', 15) == 10 ? 'selected' : '' }}>10</option>
                                <option value="{{ route('admin.ai.conversations.index', array_merge(request()->query(), ['per_page' => 15])) }}" {{ request('per_page', 15) == 15 ? 'selected' : '' }}>15</option>
                                <option value="{{ route('admin.ai.conversations.index', array_merge(request()->query(), ['per_page' => 50])) }}" {{ request('per_page', 15) == 50 ? 'selected' : '' }}>50</option>
                                <option value="{{ route('admin.ai.conversations.index', array_merge(request()->query(), ['per_page' => 100])) }}" {{ request('per_page', 15) == 100 ? 'selected' : '' }}>100</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Filtre Bölümü - Açılır Kapanır -->
            <div class="collapse mb-3" id="filterCollapse">
                <div class="card card-body">
                    <form method="GET" action="{{ route('admin.ai.conversations.index') }}" class="row g-3">
                        <input type="hidden" name="search" value="{{ request('search') }}">
                        
                        <div class="col-md-3">
                            <label class="form-label">Tip</label>
                            <select name="type" class="form-select">
                                <option value="">Tümü</option>
                                @foreach($filterOptions['types'] as $type)
                                    <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>
                                        {{ ucfirst(str_replace('_', ' ', $type)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label">Test Edilen Özellik</label>
                            <select name="feature_name" class="form-select">
                                <option value="">Tümü</option>
                                @foreach($filterOptions['features'] as $feature)
                                    <option value="{{ $feature }}" {{ request('feature_name') == $feature ? 'selected' : '' }}>
                                        {{ $feature }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label">Test Modu</label>
                            <select name="is_demo" class="form-select">
                                <option value="">Tümü</option>
                                <option value="1" {{ request('is_demo') === '1' ? 'selected' : '' }}>Demo</option>
                                <option value="0" {{ request('is_demo') === '0' ? 'selected' : '' }}>Gerçek AI</option>
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label">Durum</label>
                            <select name="status" class="form-select">
                                <option value="active" {{ request('status', 'active') === 'active' ? 'selected' : '' }}>Aktif</option>
                                <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Arşivlenmiş</option>
                                <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>Tümü</option>
                            </select>
                        </div>
                        
                        @if(auth()->user()->isRoot())
                        <div class="col-md-3">
                            <label class="form-label">Tenant</label>
                            <select name="tenant_id" class="form-select">
                                <option value="">Tümü</option>
                                @foreach($filterOptions['tenants'] as $tenant)
                                    @if($tenant->id != 1)
                                    <option value="{{ $tenant->id }}" {{ request('tenant_id') == $tenant->id ? 'selected' : '' }}>
                                        {{ $tenant->title ?: 'Tenant #' . $tenant->id }}
                                    </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        @endif
                        
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter me-2"></i>Filtrele
                            </button>
                            <a href="{{ route('admin.ai.conversations.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Temizle
                            </a>
                        </div>
                    </form>
                    
                    <!-- Aktif Filtreler -->
                    @if(request('search') || request('type') || request('feature_name') || request('is_demo') || (request('status') && request('status') !== 'active') || (request('tenant_id') && auth()->user()->isRoot()))
                    <div class="d-flex flex-wrap gap-2 mt-3">
                        @if(request('search'))
                            <span class="badge bg-azure-lt">Arama: {{ request('search') }}</span>
                        @endif
                        @if(request('type'))
                            <span class="badge bg-blue-lt">Tip: {{ ucfirst(str_replace('_', ' ', request('type'))) }}</span>
                        @endif
                        @if(request('feature_name'))
                            <span class="badge bg-indigo-lt">Özellik: {{ request('feature_name') }}</span>
                        @endif
                        @if(request('is_demo') === '1')
                            <span class="badge bg-purple-lt">Test Modu: Demo</span>
                        @elseif(request('is_demo') === '0')
                            <span class="badge bg-teal-lt">Test Modu: Gerçek AI</span>
                        @endif
                        @if(request('status') && request('status') !== 'active')
                            <span class="badge bg-yellow-lt">Durum: {{ request('status') === 'archived' ? 'Arşivlenmiş' : 'Tümü' }}</span>
                        @endif
                        @if(request('tenant_id') && auth()->user()->isRoot())
                            <span class="badge bg-cyan-lt">Tenant: {{ $filterOptions['tenants']->firstWhere('id', request('tenant_id'))->title ?? 'Tenant #' . request('tenant_id') }}</span>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- Activity-logs Tarzı Tablo -->
            <div id="table-default" class="table-responsive">
                <table class="table table-vcenter card-table table-hover text-nowrap datatable">
                    <thead>
                        <tr>
                            <th style="width: 50px" class="d-none d-md-table-cell">
                                <div class="d-flex align-items-center gap-2">
                                    <input type="checkbox" class="form-check-input" id="selectAll">
                                    <button class="table-sort {{ request('sort') === 'id' ? (request('direction') === 'asc' ? 'asc' : 'desc') : '' }}"
                                        onclick="window.location.href='{{ route('admin.ai.conversations.index', array_merge(request()->query(), ['sort' => 'id', 'direction' => request('sort') == 'id' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}'">
                                    </button>
                                </div>
                            </th>
                            <th>
                                <button class="table-sort {{ request('sort') === 'title' ? (request('direction') === 'asc' ? 'asc' : 'desc') : '' }}"
                                    onclick="window.location.href='{{ route('admin.ai.conversations.index', array_merge(request()->query(), ['sort' => 'title', 'direction' => request('sort') == 'title' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}'">
                                    Başlık
                                </button>
                            </th>
                            <th class="d-none d-lg-table-cell">
                                <button class="table-sort {{ request('sort') === 'type' ? (request('direction') === 'asc' ? 'asc' : 'desc') : '' }}"
                                    onclick="window.location.href='{{ route('admin.ai.conversations.index', array_merge(request()->query(), ['sort' => 'type', 'direction' => request('sort') == 'type' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}'">
                                    Tip
                                </button>
                            </th>
                            <th class="d-none d-xl-table-cell">Kullanıcı</th>
                            <th class="d-none d-xl-table-cell">Kişi Bilgileri</th>
                            <th class="d-none d-xl-table-cell">Tenant</th>
                            <th class="d-none d-xl-table-cell">
                                <button class="table-sort {{ request('sort') === 'is_demo' ? (request('direction') === 'asc' ? 'asc' : 'desc') : '' }}"
                                    onclick="window.location.href='{{ route('admin.ai.conversations.index', array_merge(request()->query(), ['sort' => 'is_demo', 'direction' => request('sort') == 'is_demo' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}'">
                                    Test Modu
                                </button>
                            </th>
                            <th class="d-none d-xl-table-cell">
                                <button class="table-sort {{ request('sort') === 'total_tokens_used' ? (request('direction') === 'asc' ? 'asc' : 'desc') : '' }}"
                                    onclick="window.location.href='{{ route('admin.ai.conversations.index', array_merge(request()->query(), ['sort' => 'total_tokens_used', 'direction' => request('sort') == 'total_tokens_used' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}'">
                                    Token
                                </button>
                            </th>
                            <th class="d-none d-xl-table-cell">Credit</th>
                            <th class="d-none d-xl-table-cell">Model</th>
                            <th class="d-none d-lg-table-cell">Mesaj</th>
                            <th class="d-none d-lg-table-cell">Durum</th>
                            <th class="d-none d-md-table-cell">
                                <button class="table-sort {{ request('sort') === 'created_at' ? (request('direction') === 'asc' ? 'asc' : 'desc') : '' }}"
                                    onclick="window.location.href='{{ route('admin.ai.conversations.index', array_merge(request()->query(), ['sort' => 'created_at', 'direction' => request('sort') == 'created_at' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}'">
                                    Tarih
                                </button>
                            </th>
                            <th class="text-center" style="width: 120px">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody class="table-tbody">
                        @forelse($conversations as $conversation)
                        <tr class="hover-trigger" data-conversation-id="{{ $conversation->id }}">
                            <td class="sort-id small d-none d-md-table-cell">
                                <div class="hover-toggle">
                                    <span class="hover-hide">{{ $conversation->id }}</span>
                                    <input type="checkbox" class="form-check-input conversation-checkbox hover-show"
                                           name="conversations[]" value="{{ $conversation->id }}">
                                </div>
                            </td>
                            <td class="text-wrap">
                                <div>
                                    <a href="{{ route('admin.ai.conversations.show', $conversation->id) }}" class="text-reset">
                                        {{ Str::limit($conversation->title, 40) }}
                                    </a>
                                </div>
                                @if($conversation->feature_name)
                                    <div class="text-muted small">{{ $conversation->feature_name }}</div>
                                @endif
                                {{-- Mobilde göster --}}
                                <div class="d-md-none mt-1">
                                    <small class="text-muted">{{ $conversation->created_at->format('d.m.Y H:i') }}</small>
                                </div>
                            </td>
                            <td class="d-none d-lg-table-cell">
                                @if($conversation->type == 'feature_test')
                                    <span class="badge bg-blue-lt">Test</span>
                                @elseif($conversation->type == 'chat')
                                    <span class="badge bg-green-lt">Chat</span>
                                @else
                                    <span class="badge bg-gray-lt">{{ ucfirst($conversation->type) }}</span>
                                @endif
                            </td>
                            <td class="d-none d-xl-table-cell">
                                @if($conversation->user)
                                    <div>{{ $conversation->user->name }}</div>
                                    @if($conversation->user->email)
                                        <div class="text-muted small">{{ $conversation->user->email }}</div>
                                    @endif
                                @else
                                    <span class="text-secondary">Sistem</span>
                                @endif
                            </td>
                            <td class="d-none d-xl-table-cell">
                                @php
                                    $contextData = $conversation->context_data ?? [];
                                    $device = $contextData['device_type'] ?? null;
                                    $browser = $contextData['browser'] ?? null;
                                    $os = $contextData['os'] ?? null;
                                    $ip = $contextData['ip'] ?? null;
                                @endphp
                                @if($device || $browser || $os || $ip)
                                    <div class="small">
                                        @if($device)
                                            <div class="mb-1">
                                                @if($device === 'mobile')
                                                    <i class="fas fa-mobile-alt text-primary me-1"></i>
                                                @elseif($device === 'tablet')
                                                    <i class="fas fa-tablet-alt text-info me-1"></i>
                                                @else
                                                    <i class="fas fa-desktop text-success me-1"></i>
                                                @endif
                                                <span class="text-muted">{{ $browser ?? 'Unknown' }}</span>
                                            </div>
                                        @endif
                                        @if($os || $ip)
                                            <div class="text-muted small">
                                                @if($os)
                                                    {{ $os }}
                                                @endif
                                                @if($ip)
                                                    <span class="badge bg-secondary-lt ms-1" title="{{ $ip }}">{{ substr($ip, -8) }}</span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td class="d-none d-xl-table-cell">
                                @if($conversation->tenant)
                                    <a href="{{ route('admin.ai.credits.show', $conversation->tenant) }}" class="text-reset">
                                        {{ $conversation->tenant->title ?: 'Tenant #' . $conversation->tenant->id }}
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="d-none d-xl-table-cell">
                                @if($conversation->is_demo)
                                    <span class="badge bg-blue-lt text-muted small">Demo</span>
                                @elseif($conversation->type == 'feature_test')
                                    <span class="badge bg-green-lt text-muted small">Gerçek AI</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="d-none d-xl-table-cell">
                                <span class="badge bg-gray-lt">{{ ai_format_token_count($conversation->total_tokens_used) }}</span>
                            </td>
                            <td class="d-none d-xl-table-cell">
                                @php
                                    $creditsUsed = $conversation->getTotalCreditsUsed();
                                @endphp
                                @if($creditsUsed > 0)
                                    <span class="badge bg-warning-lt">{{ number_format($creditsUsed, 2) }}</span>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td class="d-none d-xl-table-cell">
                                @php
                                    $usedModel = $conversation->getUsedModel();
                                    $modelDisplay = $usedModel !== 'unknown' ? $usedModel : 'N/A';
                                    // Provider/model formatını ayır
                                    if (str_contains($modelDisplay, '/')) {
                                        $parts = explode('/', $modelDisplay, 2);
                                        $provider = $parts[0];
                                        $model = $parts[1];
                                    } else {
                                        $provider = '';
                                        $model = $modelDisplay;
                                    }
                                @endphp
                                @if($usedModel !== 'unknown')
                                    <div class="small">
                                        @if($provider)
                                            <div class="text-muted">{{ ucfirst($provider) }}</div>
                                        @endif
                                        <div class="fw-medium">{{ $model }}</div>
                                    </div>
                                @else
                                    <span class="text-muted small">N/A</span>
                                @endif
                            </td>
                            <td class="d-none d-lg-table-cell">{{ $conversation->messages()->count() }}</td>
                            <td class="d-none d-lg-table-cell">
                                @if($conversation->status === 'active')
                                    <span class="badge bg-green-lt">Aktif</span>
                                @elseif($conversation->status === 'archived')
                                    <span class="badge bg-yellow-lt">Arşivlenmiş</span>
                                @else
                                    <span class="badge bg-gray-lt">{{ ucfirst($conversation->status) }}</span>
                                @endif
                            </td>
                            <td class="d-none d-md-table-cell">
                                <div>{{ $conversation->created_at->format('d.m.Y') }}</div>
                                <div class="text-muted small">{{ $conversation->created_at->format('H:i:s') }}</div>
                            </td>
                            <td class="text-center align-middle">
                                {{-- Desktop görünüm --}}
                                <div class="d-none d-md-flex container">
                                    <div class="row">
                                        <div class="col">
                                            <a href="javascript:void(0)" onclick="showPreview({{ $conversation->id }})"
                                               data-bs-toggle="tooltip" data-bs-placement="top" title="Önizleme">
                                                <i class="fa-solid fa-search link-info fa-lg"></i>
                                            </a>
                                        </div>
                                        <div class="col">
                                            <a href="{{ route('admin.ai.conversations.show', $conversation->id) }}"
                                               data-bs-toggle="tooltip" data-bs-placement="top" title="Detaylar">
                                                <i class="fa-solid fa-eye link-secondary fa-lg"></i>
                                            </a>
                                        </div>
                                        <div class="col lh-1">
                                            <div class="dropdown mt-1">
                                                <a class="dropdown-toggle text-secondary" href="#" data-bs-toggle="dropdown"
                                                    aria-haspopup="true" aria-expanded="false">
                                                    <i class="fa-solid fa-bars-sort fa-flip-horizontal fa-lg"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    @if($conversation->status === 'active')
                                                        <form method="POST" action="{{ route('admin.ai.conversations.archive', $conversation->id) }}" style="display: inline;">
                                                            @csrf
                                                            <button type="submit" class="dropdown-item">
                                                                <i class="fas fa-archive me-2"></i>Arşivle
                                                            </button>
                                                        </form>
                                                    @elseif($conversation->status === 'archived')
                                                        <form method="POST" action="{{ route('admin.ai.conversations.unarchive', $conversation->id) }}" style="display: inline;">
                                                            @csrf
                                                            <button type="submit" class="dropdown-item">
                                                                <i class="fas fa-undo me-2"></i>Arşivden Çıkar
                                                            </button>
                                                        </form>
                                                    @endif
                                                    
                                                    @if($conversation->status === 'active' || $conversation->status === 'archived')
                                                        <div class="dropdown-divider"></div>
                                                    @endif
                                                    
                                                    <form method="POST" action="{{ route('admin.ai.conversations.delete', $conversation->id) }}" 
                                                          onsubmit="return confirm('Bu konuşmayı kalıcı olarak silmek istediğinizden emin misiniz?')" style="display: inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item link-danger">
                                                            <i class="fas fa-trash me-2"></i>Sil
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Mobil görünüm - Sadece dropdown menü --}}
                                <div class="d-md-none">
                                    <div class="dropdown">
                                        <a class="btn btn-sm btn-ghost-secondary" href="#" data-bs-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                            <i class="fa-solid fa-ellipsis-vertical"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a href="javascript:void(0)" onclick="showPreview({{ $conversation->id }})" class="dropdown-item">
                                                <i class="fas fa-search me-2"></i>Önizleme
                                            </a>
                                            <a href="{{ route('admin.ai.conversations.show', $conversation->id) }}" class="dropdown-item">
                                                <i class="fas fa-eye me-2"></i>Detaylar
                                            </a>
                                            <div class="dropdown-divider"></div>
                                            @if($conversation->status === 'active')
                                                <form method="POST" action="{{ route('admin.ai.conversations.archive', $conversation->id) }}" style="display: inline;">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item">
                                                        <i class="fas fa-archive me-2"></i>Arşivle
                                                    </button>
                                                </form>
                                            @elseif($conversation->status === 'archived')
                                                <form method="POST" action="{{ route('admin.ai.conversations.unarchive', $conversation->id) }}" style="display: inline;">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item">
                                                        <i class="fas fa-undo me-2"></i>Arşivden Çıkar
                                                    </button>
                                                </form>
                                            @endif
                                            <div class="dropdown-divider"></div>
                                            <form method="POST" action="{{ route('admin.ai.conversations.delete', $conversation->id) }}"
                                                  onsubmit="return confirm('Bu konuşmayı kalıcı olarak silmek istediğinizden emin misiniz?')" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item link-danger">
                                                    <i class="fas fa-trash me-2"></i>Sil
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="12" class="text-center py-4">
                                <div class="empty">
                                    <p class="empty-title">Kayıt bulunamadı</p>
                                    <p class="empty-subtitle text-muted">
                                        Filtrelere uygun konuşma bulunamadı
                                    </p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Pagination -->
        {{ $conversations->withQueryString()->links() }}
        
        <!-- Bulk Actions (Activity-logs tarzı) -->
        @if(count($conversations) > 0)
        <div class="position-fixed bottom-0 start-50 translate-middle-x mb-4" style="z-index: 1000; display: none;" id="bulkActionsPanel">
            <div class="card shadow-lg border-0 rounded-lg" style="backdrop-filter: blur(12px); background: var(--tblr-bg-surface);">
                <span class="badge bg-red badge-notification badge-blink"></span>
                <div class="card-body p-3">
                    <div class="d-flex flex-wrap gap-3 align-items-center justify-content-center">
                        <span class="text-muted small">Seçili konuşmalar: <span id="selectedCount">0</span></span>
                        <button type="button" class="btn btn-sm btn-outline-danger px-3 py-1 hover-btn" onclick="bulkArchive()">
                            <i class="fas fa-archive me-2"></i>
                            <span>Arşivle</span>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger px-3 py-1 hover-btn" onclick="bulkDelete()">
                            <i class="fas fa-trash me-2"></i>
                            <span>Sil</span>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary px-3 py-1 hover-btn" onclick="clearSelection()">
                            <i class="fas fa-times me-2"></i>
                            <span>Seçimi Temizle</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Önizleme Modal -->
    <div class="modal modal-blur fade" id="previewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konuşma Önizleme</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="previewContent">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Yükleniyor...</span>
                        </div>
                        <p class="mt-3 text-muted">Mesajlar yükleniyor...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="d-flex w-100 justify-content-between align-items-center">
                        <!-- Sol: Navigasyon -->
                        <div>
                            <button type="button" class="btn btn-outline-secondary" id="prevConversationBtn" onclick="navigateModal('prev')" title="Önceki (←)">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <span class="mx-2 text-muted small" id="modalPositionInfo">1 / 1</span>
                            <button type="button" class="btn btn-outline-secondary" id="nextConversationBtn" onclick="navigateModal('next')" title="Sonraki (→)">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>

                        <!-- Sağ: İşlem butonları -->
                        <div>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                            <a href="#" id="previewDetailLink" class="btn btn-primary">
                                <i class="fas fa-external-link-alt me-2"></i>Detaylı Görünüm
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Activity-logs tarzı JavaScript
    let selectedItems = [];
    
    // Select all checkbox
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.conversation-checkbox');
        checkboxes.forEach(cb => {
            cb.checked = this.checked;
            updateSelection(cb.value, this.checked);
        });
        updateBulkActions();
    });

    // Individual checkboxes
    document.querySelectorAll('.conversation-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateSelection(this.value, this.checked);
            updateBulkActions();
        });
    });

    function updateSelection(id, checked) {
        if (checked) {
            if (!selectedItems.includes(id)) {
                selectedItems.push(id);
            }
        } else {
            selectedItems = selectedItems.filter(item => item !== id);
        }
    }

    function updateBulkActions() {
        const panel = document.getElementById('bulkActionsPanel');
        const count = document.getElementById('selectedCount');
        
        if (selectedItems.length > 0) {
            panel.style.display = 'block';
            count.textContent = selectedItems.length;
        } else {
            panel.style.display = 'none';
        }
    }

    function clearSelection() {
        selectedItems = [];
        document.querySelectorAll('.conversation-checkbox').forEach(cb => cb.checked = false);
        document.getElementById('selectAll').checked = false;
        updateBulkActions();
    }

    function bulkArchive() {
        if (selectedItems.length === 0) return;
        
        if (confirm(`${selectedItems.length} konuşmayı arşivlemek istediğinizden emin misiniz?`)) {
            // Bulk archive işlemi
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("admin.ai.conversations.bulk-action") }}';
            
            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = '{{ csrf_token() }}';
            form.appendChild(csrf);
            
            const action = document.createElement('input');
            action.type = 'hidden';
            action.name = 'action';
            action.value = 'archive';
            form.appendChild(action);
            
            selectedItems.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'conversations[]';
                input.value = id;
                form.appendChild(input);
            });
            
            document.body.appendChild(form);
            form.submit();
        }
    }

    function bulkDelete() {
        if (selectedItems.length === 0) return;
        
        if (confirm(`${selectedItems.length} konuşmayı kalıcı olarak silmek istediğinizden emin misiniz? Bu işlem geri alınamaz!`)) {
            // Bulk delete işlemi
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("admin.ai.conversations.bulk-action") }}';
            
            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = '{{ csrf_token() }}';
            form.appendChild(csrf);
            
            const action = document.createElement('input');
            action.type = 'hidden';
            action.name = 'action';
            action.value = 'delete';
            form.appendChild(action);
            
            selectedItems.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'conversations[]';
                input.value = id;
                form.appendChild(input);
            });
            
            document.body.appendChild(form);
            form.submit();
        }
    }

    // Loading indicator for forms
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function() {
            document.getElementById('loadingIndicator').style.display = 'block';
        });
    });

    // Modal Navigation System
    let conversationIds = [];
    let currentConversationIndex = -1;

    // Sayfa yüklendiğinde tüm konuşma ID'lerini topla
    document.addEventListener('DOMContentLoaded', function() {
        updateConversationList();
    });

    function updateConversationList() {
        // Tablodaki tüm konuşma satırlarından ID'leri al
        conversationIds = [];
        document.querySelectorAll('tr[data-conversation-id]').forEach(row => {
            conversationIds.push(parseInt(row.getAttribute('data-conversation-id')));
        });
    }

    // Önizleme fonksiyonu
    function showPreview(conversationId, updateIndex = true) {
        // Modal elementini al
        const modalElement = document.getElementById('previewModal');

        // Tabler.io jQuery ile Bootstrap modal kullanıyor
        $('#previewModal').modal('show');

        // Mevcut index'i güncelle
        if (updateIndex) {
            currentConversationIndex = conversationIds.indexOf(conversationId);
            updateNavigationButtons();
        }

        // Loading durumu
        const previewContent = document.getElementById('previewContent');
        previewContent.innerHTML = `
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Yükleniyor...</span>
                </div>
                <p class="mt-3 text-muted">Mesajlar yükleniyor...</p>
            </div>
        `;

        // Detay linkini güncelle
        document.getElementById('previewDetailLink').href = '/admin/ai/conversations/' + conversationId;

        // AJAX ile mesajları getir
        fetch('/admin/ai/conversations/' + conversationId + '/preview', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Conversation bilgileri
                let html = `
                    <div class="card mb-3">
                        <div class="card-header">
                            <h3 class="card-title">${data.conversation.title || 'Başlıksız Konuşma'}</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <strong>Tip:</strong>
                                        <span class="badge bg-blue-lt">${data.conversation.type}</span>
                                    </div>
                                    ${data.conversation.feature_name ? `<div class="mb-2"><strong>Özellik:</strong> ${data.conversation.feature_name}</div>` : ''}
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <strong>Mesaj Sayısı:</strong> ${data.messages.length}
                                    </div>
                                    <div class="mb-2">
                                        <strong>Tarih:</strong> ${new Date(data.conversation.created_at).toLocaleString('tr-TR')}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                // Mesajlar
                if (data.messages.length > 0) {
                    html += '<div class="card"><div class="card-header"><h3 class="card-title">Mesajlar</h3></div><div class="card-body">';

                    data.messages.forEach((message, index) => {
                        const isUser = message.role === 'user';
                        const bgClass = isUser ? 'bg-blue-lt' : 'bg-gray-lt';
                        const icon = isUser ? 'fa-user' : 'fa-robot';
                        const title = isUser ? 'Kullanıcı' : 'AI Asistan';

                        html += `
                            <div class="mb-3 ${index > 0 ? 'border-top pt-3' : ''}">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas ${icon} ${bgClass} p-2 rounded me-2"></i>
                                    <strong>${title}</strong>
                                    <span class="text-muted ms-auto small">${new Date(message.created_at).toLocaleString('tr-TR')}</span>
                                </div>
                                <div class="ps-4" style="white-space: pre-wrap;">${escapeHtml(message.content)}</div>
                                ${message.token_count ? `<div class="ps-4 mt-1"><small class="text-muted">Token: ${message.token_count}</small></div>` : ''}
                            </div>
                        `;
                    });

                    html += '</div></div>';
                } else {
                    html += '<div class="alert alert-info"><i class="fas fa-info-circle me-2"></i>Bu konuşmada henüz mesaj bulunmuyor.</div>';
                }

                previewContent.innerHTML = html;
            } else {
                previewContent.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        ${data.message || 'Mesajlar yüklenirken hata oluştu.'}
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Preview error:', error);
            previewContent.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Mesajlar yüklenirken bir hata oluştu.
                </div>
            `;
        });
    }

    // HTML escape fonksiyonu
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Modal Navigation Functions
    function navigateModal(direction) {
        if (direction === 'next' && currentConversationIndex < conversationIds.length - 1) {
            currentConversationIndex++;
            const nextId = conversationIds[currentConversationIndex];
            showPreview(nextId, false);
            updateNavigationButtons();
        } else if (direction === 'prev' && currentConversationIndex > 0) {
            currentConversationIndex--;
            const prevId = conversationIds[currentConversationIndex];
            showPreview(prevId, false);
            updateNavigationButtons();
        }
    }

    function updateNavigationButtons() {
        const prevBtn = document.getElementById('prevConversationBtn');
        const nextBtn = document.getElementById('nextConversationBtn');
        const positionInfo = document.getElementById('modalPositionInfo');

        // Buton durumlarını güncelle
        if (prevBtn) prevBtn.disabled = (currentConversationIndex <= 0);
        if (nextBtn) nextBtn.disabled = (currentConversationIndex >= conversationIds.length - 1);

        // Pozisyon bilgisini güncelle
        if (positionInfo && conversationIds.length > 0) {
            positionInfo.textContent = `${currentConversationIndex + 1} / ${conversationIds.length}`;
        }
    }

    // Klavye kısayolları (Modal açıkken)
    document.addEventListener('keydown', function(e) {
        // Modal açık mı kontrol et
        const modal = document.getElementById('previewModal');
        if (!modal || !modal.classList.contains('show')) {
            return;
        }

        // Sol ok tuşu: Önceki
        if (e.key === 'ArrowLeft') {
            e.preventDefault();
            navigateModal('prev');
        }

        // Sağ ok tuşu: Sonraki
        if (e.key === 'ArrowRight') {
            e.preventDefault();
            navigateModal('next');
        }

        // ESC tuşu: Kapat (Bootstrap zaten yapıyor ama güvenlik için)
        if (e.key === 'Escape') {
            $('#previewModal').modal('hide');
        }
    });
</script>
@endpush
@extends('admin.layout')

@include('ai::helper')

@section('pretitle', __('ai::admin.conversation_archive'))
@section('title', __('ai::admin.archived_conversations'))

@section('content')
    <!-- İstatistik Kartları -->
    <div class="row mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">{{ __('ai::admin.total_archive') }}</div>
                    </div>
                    <div class="h1 mb-3">{{ number_format($stats['total']) }}</div>
                    <div class="d-flex mb-2">
                        <div class="text-muted">{{ __('ai::admin.archived_conversations_desc') }}</div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">{{ __('ai::admin.feature_tests') }}</div>
                    </div>
                    <div class="h1 mb-3">{{ number_format($stats['feature_tests']) }}</div>
                    <div class="d-flex mb-2">
                        <div class="text-muted">{{ __('ai::admin.archived_ai_tests') }}</div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">{{ __('ai::admin.demo_tests') }}</div>
                    </div>
                    <div class="h1 mb-3 text-info">{{ number_format($stats['demo_tests']) }}</div>
                    <div class="d-flex mb-2">
                        <div class="text-muted">{{ __('ai::admin.archived_demo_tests') }}</div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">{{ __('ai::admin.real_ai_tests') }}</div>
                    </div>
                    <div class="h1 mb-3 text-success">{{ number_format($stats['real_tests']) }}</div>
                    <div class="d-flex mb-2">
                        <div class="text-muted">{{ __('ai::admin.archived_real_tests') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Arşive Dön Butonu -->
    <div class="mb-3">
        <a href="{{ route('admin.ai.conversations.index') }}" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left me-2"></i>{{ __('ai::admin.active_conversations_return') }}
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
                        <form method="GET" action="{{ route('admin.ai.conversations.archived') }}" style="display: inline;">
                            @foreach(request()->except('search') as $key => $value)
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endforeach
                            <input type="text" name="search" class="form-control" placeholder="{{ __('ai::admin.search_archived_placeholder') }}" 
                                   value="{{ request('search') }}" onchange="this.form.submit()">
                        </form>
                    </div>
                </div>
                
                <!-- Ortadaki Loading -->
                <div class="col position-relative">
                    <div class="position-absolute top-50 start-50 translate-middle text-center"
                        style="width: 100%; max-width: 250px; display: none;" id="loadingIndicator">
                        <div class="small text-muted mb-2">{{ __('ai::admin.updating') }}</div>
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
                            {{ __('ai::admin.filters') }}
                            @if(request('type') || request('feature_name') || request('is_demo') || (request('tenant_id') && auth()->user()->isRoot()))
                            <span class="badge bg-primary ms-1">{{ __('ai::admin.active') }}</span>
                            @endif
                        </button>
                        
                        <!-- Sayfa Adeti Seçimi -->
                        <div style="min-width: 70px">
                            <select class="form-select" onchange="window.location.href=this.value">
                                <option value="{{ route('admin.ai.conversations.archived', array_merge(request()->query(), ['per_page' => 10])) }}" {{ request('per_page', 15) == 10 ? 'selected' : '' }}>10</option>
                                <option value="{{ route('admin.ai.conversations.archived', array_merge(request()->query(), ['per_page' => 15])) }}" {{ request('per_page', 15) == 15 ? 'selected' : '' }}>15</option>
                                <option value="{{ route('admin.ai.conversations.archived', array_merge(request()->query(), ['per_page' => 50])) }}" {{ request('per_page', 15) == 50 ? 'selected' : '' }}>50</option>
                                <option value="{{ route('admin.ai.conversations.archived', array_merge(request()->query(), ['per_page' => 100])) }}" {{ request('per_page', 15) == 100 ? 'selected' : '' }}>100</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Filtre Bölümü - Açılır Kapanır -->
            <div class="collapse mb-3" id="filterCollapse">
                <div class="card card-body">
                    <form method="GET" action="{{ route('admin.ai.conversations.archived') }}" class="row g-3">
                        <input type="hidden" name="search" value="{{ request('search') }}">
                        
                        <div class="col-md-3">
                            <label class="form-label">{{ __('ai::admin.type') }}</label>
                            <select name="type" class="form-select">
                                <option value="">{{ __('ai::admin.all_types') }}</option>
                                @foreach($filterOptions['types'] as $type)
                                    <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>
                                        {{ ucfirst(str_replace('_', ' ', $type)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label">{{ __('ai::admin.tested_feature') }}</label>
                            <select name="feature_name" class="form-select">
                                <option value="">{{ __('ai::admin.all_features') }}</option>
                                @foreach($filterOptions['features'] as $feature)
                                    <option value="{{ $feature }}" {{ request('feature_name') == $feature ? 'selected' : '' }}>
                                        {{ $feature }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label">{{ __('ai::admin.test_mode') }}</label>
                            <select name="is_demo" class="form-select">
                                <option value="">{{ __('ai::admin.all_types') }}</option>
                                <option value="1" {{ request('is_demo') === '1' ? 'selected' : '' }}>{{ __('ai::admin.demo') }}</option>
                                <option value="0" {{ request('is_demo') === '0' ? 'selected' : '' }}>{{ __('ai::admin.real_ai') }}</option>
                            </select>
                        </div>
                        
                        @if(auth()->user()->isRoot())
                        <div class="col-md-3">
                            <label class="form-label">{{ __('ai::admin.tenant_column') }}</label>
                            <select name="tenant_id" class="form-select">
                                <option value="">{{ __('ai::admin.all_tenants') }}</option>
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
                                <i class="fas fa-filter me-2"></i>{{ __('ai::admin.filter_button') }}
                            </button>
                            <a href="{{ route('admin.ai.conversations.archived') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>{{ __('ai::admin.clear_filters_button') }}
                            </a>
                        </div>
                    </form>
                    
                    <!-- Aktif Filtreler -->
                    @if(request('search') || request('type') || request('feature_name') || request('is_demo') || (request('tenant_id') && auth()->user()->isRoot()))
                    <div class="d-flex flex-wrap gap-2 mt-3">
                        @if(request('search'))
                            <span class="badge bg-azure-lt">{{ __('ai::admin.search_filter') }}: {{ request('search') }}</span>
                        @endif
                        @if(request('type'))
                            <span class="badge bg-blue-lt">{{ __('ai::admin.type_filter') }}: {{ ucfirst(str_replace('_', ' ', request('type'))) }}</span>
                        @endif
                        @if(request('feature_name'))
                            <span class="badge bg-indigo-lt">{{ __('ai::admin.feature_filter') }}: {{ request('feature_name') }}</span>
                        @endif
                        @if(request('is_demo') === '1')
                            <span class="badge bg-purple-lt">{{ __('ai::admin.demo_mode_filter') }}</span>
                        @elseif(request('is_demo') === '0')
                            <span class="badge bg-teal-lt">{{ __('ai::admin.real_ai_filter') }}</span>
                        @endif
                        @if(request('tenant_id') && auth()->user()->isRoot())
                            <span class="badge bg-cyan-lt">{{ __('ai::admin.tenant_filter') }}: {{ $filterOptions['tenants']->firstWhere('id', request('tenant_id'))->title ?? 'Tenant #' . request('tenant_id') }}</span>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- Arşivlenmiş Konuşmalar Tablosu -->
            <div id="table-default" class="table-responsive">
                <table class="table table-vcenter card-table table-hover text-nowrap datatable">
                    <thead>
                        <tr>
                            <th style="width: 50px">
                                <div class="d-flex align-items-center gap-2">
                                    <input type="checkbox" class="form-check-input" id="selectAll">
                                    <button class="table-sort {{ request('sort') === 'id' ? (request('direction') === 'asc' ? 'asc' : 'desc') : '' }}"
                                        onclick="window.location.href='{{ route('admin.ai.conversations.archived', array_merge(request()->query(), ['sort' => 'id', 'direction' => request('sort') == 'id' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}'">
                                    </button>
                                </div>
                            </th>
                            <th>
                                <button class="table-sort {{ request('sort') === 'title' ? (request('direction') === 'asc' ? 'asc' : 'desc') : '' }}"
                                    onclick="window.location.href='{{ route('admin.ai.conversations.archived', array_merge(request()->query(), ['sort' => 'title', 'direction' => request('sort') == 'title' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}'">
                                    {{ __('ai::admin.title_header') }}
                                </button>
                            </th>
                            <th>
                                <button class="table-sort {{ request('sort') === 'type' ? (request('direction') === 'asc' ? 'asc' : 'desc') : '' }}"
                                    onclick="window.location.href='{{ route('admin.ai.conversations.archived', array_merge(request()->query(), ['sort' => 'type', 'direction' => request('sort') == 'type' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}'">
                                    {{ __('ai::admin.type') }}
                                </button>
                            </th>
                            <th>{{ __('ai::admin.user_column') }}</th>
                            <th>{{ __('ai::admin.tenant_column') }}</th>
                            <th>{{ __('ai::admin.test_mode') }}</th>
                            <th>{{ __('ai::admin.token_column') }}</th>
                            <th>{{ __('ai::admin.message_column') }}</th>
                            <th>
                                <button class="table-sort {{ request('sort') === 'updated_at' ? (request('direction') === 'asc' ? 'asc' : 'desc') : '' }}"
                                    onclick="window.location.href='{{ route('admin.ai.conversations.archived', array_merge(request()->query(), ['sort' => 'updated_at', 'direction' => request('sort') == 'updated_at' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}'">
                                    {{ __('ai::admin.archive_date') }}
                                </button>
                            </th>
                            <th class="text-center" style="width: 120px">{{ __('ai::admin.actions_column') }}</th>
                        </tr>
                    </thead>
                    <tbody class="table-tbody">
                        @forelse($conversations as $conversation)
                        <tr class="hover-trigger">
                            <td class="sort-id small">
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
                                    <span class="badge bg-yellow-lt ms-2">{{ __('ai::admin.archived_badge') }}</span>
                                </div>
                                @if($conversation->feature_name)
                                    <div class="text-muted small">{{ $conversation->feature_name }}</div>
                                @endif
                            </td>
                            <td>
                                @if($conversation->type == 'feature_test')
                                    <span class="badge bg-blue-lt">{{ __('ai::admin.test_badge') }}</span>
                                @elseif($conversation->type == 'chat')
                                    <span class="badge bg-green-lt">{{ __('ai::admin.chat_badge') }}</span>
                                @else
                                    <span class="badge bg-gray-lt">{{ ucfirst($conversation->type) }}</span>
                                @endif
                            </td>
                            <td>
                                @if($conversation->user)
                                    <div>{{ $conversation->user->name }}</div>
                                    @if($conversation->user->email)
                                        <div class="text-muted small">{{ $conversation->user->email }}</div>
                                    @endif
                                @else
                                    <span class="text-secondary">{{ __('ai::admin.system_user') }}</span>
                                @endif
                            </td>
                            <td>
                                @if($conversation->tenant)
                                    <a href="{{ route('admin.ai.credits.show', $conversation->tenant) }}" class="text-reset">
                                        {{ $conversation->tenant->title ?: 'Tenant #' . $conversation->tenant->id }}
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($conversation->is_demo)
                                    <span class="badge bg-blue-lt text-muted small">{{ __('ai::admin.demo_badge') }}</span>
                                @elseif($conversation->type == 'feature_test')
                                    <span class="badge bg-green-lt text-muted small">{{ __('ai::admin.real_ai_badge') }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-gray-lt">{{ number_format($conversation->total_tokens_used) }}</span>
                            </td>
                            <td>{{ $conversation->messages()->count() }}</td>
                            <td>
                                <div>{{ $conversation->updated_at->format('d.m.Y') }}</div>
                                <div class="text-muted small">{{ $conversation->updated_at->format('H:i:s') }}</div>
                            </td>
                            <td class="text-center align-middle">
                                <div class="container">
                                    <div class="row">
                                        <div class="col">
                                            <a href="{{ route('admin.ai.conversations.show', $conversation->id) }}" 
                                               data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('ai::admin.details_tooltip') }}">
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
                                                    <form method="POST" action="{{ route('admin.ai.conversations.unarchive', $conversation->id) }}" style="display: inline;">
                                                        @csrf
                                                        <button type="submit" class="dropdown-item">
                                                            <i class="fas fa-undo me-2"></i>{{ __('ai::admin.unarchive_action') }}
                                                        </button>
                                                    </form>
                                                    <div class="dropdown-divider"></div>
                                                    <form method="POST" action="{{ route('admin.ai.conversations.delete', $conversation->id) }}" 
                                                          onsubmit="return confirm('{{ __('ai::admin.delete_confirm') }}')" style="display: inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item link-danger">
                                                            <i class="fas fa-trash me-2"></i>{{ __('ai::admin.delete_action') }}
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-4">
                                <div class="empty">
                                    <div class="empty-img">
                                        <i class="fas fa-archive fa-3x text-muted"></i>
                                    </div>
                                    <p class="empty-title">{{ __('ai::admin.no_archived_conversations') }}</p>
                                    <p class="empty-subtitle text-muted">
                                        {{ __('ai::admin.no_archived_conversations_desc') }}
                                    </p>
                                    <div class="empty-action">
                                        <a href="{{ route('admin.ai.conversations.index') }}" class="btn btn-primary">
                                            <i class="fas fa-arrow-left me-2"></i>{{ __('ai::admin.active_conversations_return') }}
                                        </a>
                                    </div>
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
        
        <!-- Bulk Actions (Arşiv için özelleştirilmiş) -->
        @if(count($conversations) > 0)
        <div class="position-fixed bottom-0 start-50 translate-middle-x mb-4" style="z-index: 1000; display: none;" id="bulkActionsPanel">
            <div class="card shadow-lg border-0 rounded-lg" style="backdrop-filter: blur(12px); background: var(--tblr-bg-surface);">
                <span class="badge bg-yellow badge-notification badge-blink"></span>
                <div class="card-body p-3">
                    <div class="d-flex flex-wrap gap-3 align-items-center justify-content-center">
                        <span class="text-muted small">{{ __('ai::admin.bulk_actions_selected') }}: <span id="selectedCount">0</span></span>
                        <button type="button" class="btn btn-sm btn-outline-success px-3 py-1 hover-btn" onclick="bulkUnarchive()">
                            <i class="fas fa-undo me-2"></i>
                            <span>{{ __('ai::admin.bulk_unarchive') }}</span>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger px-3 py-1 hover-btn" onclick="bulkDelete()">
                            <i class="fas fa-trash me-2"></i>
                            <span>{{ __('ai::admin.bulk_delete') }}</span>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary px-3 py-1 hover-btn" onclick="clearSelection()">
                            <i class="fas fa-times me-2"></i>
                            <span>{{ __('ai::admin.bulk_clear_selection') }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
@endsection

@push('scripts')
<script>
    // Arşiv sayfası için özelleştirilmiş JavaScript
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

    function bulkUnarchive() {
        if (selectedItems.length === 0) return;
        
        if (confirm(`${selectedItems.length} {{ __('ai::admin.bulk_unarchive_confirm') }}`)) {
            // Bulk unarchive işlemi
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
            action.value = 'unarchive';
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
        
        if (confirm(`${selectedItems.length} {{ __('ai::admin.bulk_delete_confirm') }}`)) {
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
</script>
@endpush
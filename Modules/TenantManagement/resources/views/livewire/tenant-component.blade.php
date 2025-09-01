@php
    View::share('pretitle', 'Kiracı Listesi');
@endphp

@include('tenantmanagement::helper')
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
                    <input type="text" wire:model.live="search" class="form-control"
                        placeholder="{{ __('tenantmanagement::admin.search_placeholder') }}">
                </div>
            </div>
            <!-- Ortadaki Loading -->
            <div class="col position-relative">
                <div wire:loading
                    wire:target="render, search, perPage, sortBy, gotoPage, previousPage, nextPage, saveTenant, deleteTenant, loadDomains, addDomain, updateDomain, deleteDomain"
                    class="position-absolute top-50 start-50 translate-middle text-center"
                    style="width: 100%; max-width: 250px;">
                    <div class="small text-muted mb-2">{{ __('tenantmanagement::admin.updating') }}</div>
                    <div class="progress mb-1">
                        <div class="progress-bar progress-bar-indeterminate"></div>
                    </div>
                </div>
            </div>
            <!-- Sağ Taraf -->
            <div class="col">
                <div class="d-flex align-items-center justify-content-end gap-3">
                    <!-- Sayfa Adeti Seçimi -->
                    <div style="min-width: 70px">
                        <select wire:model.live="perPage" class="form-select listing-filter-select">
                            <option value="10">10</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                            <option value="500">500</option>
                            <option value="1000">1000</option>
                        </select>
                    </div>
                    <!-- Tenant Ekleme Butonu -->
                    <div>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal-tenant-add" wire:click="resetForm">
                            <i class="fas fa-plus me-1"></i>
                            {{ __('tenantmanagement::admin.add_new_tenant') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tenant Listesi -->
        <div class="row row-cards">
            @foreach ($tenants as $tenant)
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="row g-4 align-items-center">
                            <div class="col-auto">
                                <div class="avatar avatar-lg bg-blue text-white text-center">
                                    <span class="avatar avatar-xl px-3 rounded">{{ $tenant->id }}</span>
                                </div>
                            </div>
                            <div class="col">
                                <h4 class="card-title m-0">
                                    <a href="javascript:void(0);" class="text-reset" wire:click.prevent="editTenant('{{ $tenant->id }}')" data-bs-toggle="modal" data-bs-target="#modal-tenant-edit">{{ $tenant->title ?? __('tenantmanagement::admin.unknown_name') }}</a>
                                </h4>
                                <div class="text-secondary">
                                    @if (method_exists($tenant, 'domains') && $tenant->domains && $tenant->domains->count() > 0)
                                    @php
                                    $domainCount = $tenant->domains->count();
                                    $firstDomain = $tenant->domains->first()->domain;
                                    @endphp
                                    <a href="http://{{ $firstDomain }}" class="text-muted" target="_blank">{{ $firstDomain }}</a>
                                    @if ($domainCount > 1)
                                    +{{ $domainCount - 1 }}
                                    @endif
                                    @else
                                    -
                                    @endif
                                </div>
                                <div class="small mt-1">
                                    <a href="javascript:void(0);" class="text-decoration-none" wire:click.prevent="toggleActive('{{ $tenant->id }}')">
                                        @if($tenant->is_active)
                                        <span class="text-muted">{{ __('tenantmanagement::admin.online') }}</span>
                                        @else
                                        <span class="text-muted">{{ __('tenantmanagement::admin.offline') }}</span>
                                        @endif
                                    </a>
                                </div>
                            </div>
                            <div class="col-auto">
                                <a href="javascript:void(0);" class="btn btn-outline-info btn-open-domain-modal"
                                    data-bs-toggle="modal" data-bs-target="#modal-domain-management"
                                    wire:click="loadDomains('{{ $tenant->id }}')">
                                    {{ __('tenantmanagement::admin.domains') }}
                                </a>
                            </div>
                            <div class="col-auto">
                                <div class="dropdown">
                                    <a href="#" class="btn-action" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <a href="javascript:void(0);" class="dropdown-item"
                                            wire:click.prevent="manageModules('{{ $tenant->id }}')" data-bs-toggle="modal"
                                            data-bs-target="#modal-module-management">
                                            {{ __('tenantmanagement::admin.manage_modules') }}
                                        </a>
                                        <a href="javascript:void(0);" class="dropdown-item"
                                            wire:click.prevent="editTenant('{{ $tenant->id }}')" data-bs-toggle="modal"
                                            data-bs-target="#modal-tenant-edit">
                                            {{ __('tenantmanagement::admin.edit') }}
                                        </a>
                                        <a href="javascript:void(0);" class="dropdown-item text-danger"
                                            wire:click.prevent="deleteTenant('{{ $tenant->id }}')">
                                            {{ __('tenantmanagement::admin.delete') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    
    {{ $tenants->links() }}
    
    <!-- JavaScript for AI Model Loading -->
    @push('scripts')
    <script>
        // PHASE 3: Real-time AI Model Loading with Credit Information
        document.addEventListener('livewire:initialized', () => {
            
            // Provider değişikliğini dinle
            Livewire.on('providerChanged', (providerId) => {
                if (providerId) {
                    loadModelsWithCredits(providerId);
                }
            });
            
            // Model seçimini dinle
            Livewire.on('modelSelected', (modelName, providerId) => {
                if (modelName && providerId) {
                    showModelCreditInfo(modelName, providerId);
                }
            });
            
            /**
             * Provider'a ait modelleri ve kredi bilgilerini yükle
             */
            async function loadModelsWithCredits(providerId) {
                try {
                    const response = await fetch(`/api/ai/admin/provider/${providerId}/models`, {
                        headers: {
                            'Authorization': 'Bearer ' + (window.authToken || ''),
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                        }
                    });
                    
                    if (response.ok) {
                        const data = await response.json();
                        updateModelSelectOptions(data.data.models);
                    }
                } catch (error) {
                    // Model bilgileri yüklenemedi, sessizce devam et
                }
            }
            
            /**
             * Model select option'larını güncelle
             */
            function updateModelSelectOptions(models) {
                const editSelect = document.getElementById('ai-model-select-edit');
                const addSelect = document.getElementById('ai-model-select-add');
                
                [editSelect, addSelect].forEach(select => {
                    if (select) {
                        // Önce temizle
                        select.innerHTML = '<option value="">Model seçin</option>';
                        
                        // Yeni option'ları ekle
                        models.forEach(model => {
                            const option = document.createElement('option');
                            option.value = model.name;
                            
                            let label = model.label;
                            if (model.input_cost_per_1k > 0 || model.output_cost_per_1k > 0) {
                                label += ` (${model.input_cost_per_1k}/${model.output_cost_per_1k} kredi/1K token)`;
                            }
                            
                            option.textContent = label;
                            select.appendChild(option);
                        });
                        
                        select.disabled = false;
                    }
                });
            }
            
            /**
             * Seçilen model için kredi bilgisini göster
             */
            async function showModelCreditInfo(modelId, providerId) {
                const infoElements = [
                    document.getElementById('credit-display-edit'),
                    document.getElementById('credit-display-add')
                ];
                
                if (!modelId || !providerId) {
                    infoElements.forEach(element => {
                        if (element) element.textContent = 'Model ve provider seçin';
                    });
                    return;
                }
                
                try {
                    const response = await fetch('/api/ai/admin/calculate-cost', {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        credentials: 'same-origin', // Session tabanlı auth için
                        body: JSON.stringify({
                            provider_id: providerId,
                            model_id: modelId,
                            input_tokens: 1000,
                            output_tokens: 1000
                        })
                    });
                    
                    if (response.ok) {
                        const data = await response.json();
                        const costText = `Model: ${data.data.total_credits} kredi (1000 input + 1000 output token için)`;
                        
                        infoElements.forEach(element => {
                            if (element) element.textContent = costText;
                        });
                    } else {
                        throw new Error('API error');
                    }
                } catch (error) {
                    // Kredi bilgisi yüklenemedi, sessizce devam et
                    infoElements.forEach(element => {
                        if (element) element.textContent = 'Kredi bilgisi hesaplanamadı';
                    });
                }
            }
            
            // Livewire property değişimlerini dinle
            Livewire.on('tenant_ai_provider_model_id-updated', (value) => {
                const providerId = @this.get('tenant_ai_provider_id');
                if (value && providerId) {
                    showModelCreditInfo(value, providerId);
                }
            });
            
            // Livewire eventlerini dinle
            Livewire.on('modelSelectionChanged', (data) => {
                if (data.modelId && data.providerId) {
                    showModelCreditInfo(data.modelId, data.providerId);
                }
            });
            
            // Property değişim hookları (fallback)
            @this.watch('tenant_ai_provider_model_id', (value) => {
                const providerId = @this.get('tenant_ai_provider_id');
                if (value && providerId) {
                    showModelCreditInfo(value, providerId);
                } else {
                    const infoElements = [
                        document.getElementById('credit-display-edit'),
                        document.getElementById('credit-display-add')
                    ];
                    infoElements.forEach(element => {
                        if (element) element.textContent = 'Model seçin';
                    });
                }
            });
        });
    </script>
    @endpush
    
    <!-- Tenant Düzenleme Modal -->
    <div class="modal fade" id="modal-tenant-edit" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('tenantmanagement::admin.tenant_update') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" wire:click="resetForm"></button>
                </div>
                <form wire:submit.prevent="saveTenant('close')">
                    <div class="modal-body">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" wire:model="name" placeholder="Tenant adı">
                            <label>{{ __('tenantmanagement::admin.tenant_name') }}</label>
                            @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" wire:model="fullname" placeholder="Yetkili adı soyadı">
                            <label>{{ __('tenantmanagement::admin.authorized_name') }}</label>
                            @error('fullname') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-floating mb-3">
                            <input type="email" class="form-control" wire:model="email" placeholder="ornek@mail.com">
                            <label>{{ __('tenantmanagement::admin.email_address') }}</label>
                            @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" wire:model="phone" placeholder="Telefon numarası">
                            <label>{{ __('tenantmanagement::admin.phone_number') }}</label>
                            @error('phone') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <div class="pretty p-default p-curve p-toggle p-smooth">
                                <input type="checkbox" id="is_active" name="is_active" wire:model="is_active" />
                                <div class="state p-success p-on">
                                    <label>{{ __('tenantmanagement::admin.active') }}</label>
                                </div>
                                <div class="state p-danger p-off">
                                    <label>{{ __('tenantmanagement::admin.not_active') }}</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-floating mb-3">
                            <select class="form-select" wire:model="theme_id" data-choices data-choices-search="{{ count($themes) > 6 ? 'true' : 'false' }}" data-choices-placeholder="Tema seçin">
                                @foreach($themes as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            <label>{{ __('tenantmanagement::admin.theme') }}</label>
                            @error('theme_id') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        
                        @if(count($availableAiProviders) > 0)
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <select class="form-select" wire:model.live="tenant_ai_provider_id">
                                        <option value="">AI Provider seçin</option>
                                        @foreach($availableAiProviders as $provider)
                                        <option value="{{ $provider['value'] }}" @if($tenant_ai_provider_id == $provider['value']) selected @endif>{{ $provider['label'] }}</option>
                                        @endforeach
                                    </select>
                                    <label>AI Provider (Marka)</label>
                                    @error('tenant_ai_provider_id') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <select class="form-select" wire:model="tenant_ai_provider_model_id" @if(empty($availableProviderModels)) disabled @endif>
                                        <option value="">Model seçin</option>
                                        @foreach($availableProviderModels as $model)
                                        <option value="{{ $model['id'] }}" @if($tenant_ai_provider_model_id == $model['id']) selected @endif>{{ $model['label'] }}</option>
                                        @endforeach
                                    </select>
                                    <label>AI Model @if(empty($availableProviderModels)) (Önce provider seçin) @endif</label>
                                    @error('tenant_ai_provider_model_id') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        @if($tenantId && $editingTenant)
                        <div class="card bg-light">
                            <div class="card-body p-3">
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <div class="form-text mb-1">
                                            <span class="text-secondary">
                                                <i class="fas fa-database me-1"></i>
                                                <strong>Tenant ID:</strong> {{ $tenantId }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-text mb-1">
                                            <span class="text-secondary">
                                                <i class="fas fa-server me-1"></i>
                                                <strong>{{ __('tenantmanagement::admin.database') }}:</strong> {{ $editingTenant->tenancy_db_name }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        <!-- Model Credit Info Display for Edit Modal -->
                        @if(!empty($availableProviderModels) && $tenant_ai_provider_model_id)
                        <div class="alert alert-info p-2 mt-2" id="model-credit-info-edit" style="font-size: 0.85em;">
                            <i class="fas fa-info-circle me-1"></i>
                            <span id="credit-display-edit">Seçilen model için kredi bilgisi yükleniyor...</span>
                        </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <div class="w-100">
                            <div class="row">
                                <div class="col">
                                    <button type="button" class="btn w-100" data-bs-dismiss="modal" wire:click="resetForm">
                                        {{ __('tenantmanagement::admin.cancel') }}
                                    </button>
                                </div>
                                <div class="col">
                                    <button type="submit" class="btn btn-primary w-100">{{ __('tenantmanagement::admin.save') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Tenant Ekleme Modal -->
    <div class="modal fade" id="modal-tenant-add" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('tenantmanagement::admin.add_new_tenant') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" wire:click="resetForm"></button>
                </div>
                <form wire:submit.prevent="saveTenant('close')">
                    <div class="modal-body">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" wire:model="name" placeholder="Tenant adı">
                            <label>{{ __('tenantmanagement::admin.tenant_name') }}</label>
                            @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" wire:model="fullname" placeholder="Yetkili adı soyadı">
                            <label>{{ __('tenantmanagement::admin.authorized_name') }}</label>
                            @error('fullname') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-floating mb-3">
                            <input type="email" class="form-control" wire:model="email" placeholder="ornek@mail.com">
                            <label>{{ __('tenantmanagement::admin.email_address') }}</label>
                            @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" wire:model="phone" placeholder="Telefon numarası">
                            <label>{{ __('tenantmanagement::admin.phone_number') }}</label>
                            @error('phone') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <div class="pretty p-icon p-toggle p-plain">
                                <input type="checkbox" id="is_active" name="is_active" wire:model="is_active"
                                    value="1" />
                                <div class="state p-on">
                                    <i class="icon fa-regular fa-square-check"></i>
                                    <label>{{ __('tenantmanagement::admin.active_online') }}</label>
                                </div>
                                <div class="state p-off">
                                    <i class="icon fa-regular fa-square"></i>
                                    <label>{{ __('tenantmanagement::admin.not_active_offline') }}</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-floating mb-3">
                            <select class="form-select" wire:model="theme_id" data-choices data-choices-search="{{ count($themes) > 6 ? 'true' : 'false' }}" data-choices-placeholder="Tema seçin">
                                @foreach($themes as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            <label>{{ __('tenantmanagement::admin.theme') }}</label>
                            @error('theme_id') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        
                        @if(count($availableAiProviders) > 0)
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <select class="form-select" wire:model.live="tenant_ai_provider_id">
                                        <option value="">AI Provider seçin</option>
                                        @foreach($availableAiProviders as $provider)
                                        <option value="{{ $provider['value'] }}">{{ $provider['label'] }}</option>
                                        @endforeach
                                    </select>
                                    <label>AI Provider (Marka)</label>
                                    @error('tenant_ai_provider_id') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <select class="form-select" wire:model="tenant_ai_provider_model_id" @if(empty($availableProviderModels)) disabled @endif>
                                        <option value="">Model seçin</option>
                                        @foreach($availableProviderModels as $model)
                                        <option value="{{ $model['id'] }}">{{ $model['label'] }}</option>
                                        @endforeach
                                    </select>
                                    <label>AI Model @if(empty($availableProviderModels)) (Önce provider seçin) @endif</label>
                                    @error('tenant_ai_provider_model_id') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <div class="w-100">
                            <div class="row">
                                <div class="col">
                                    <button type="button" class="btn w-100" data-bs-dismiss="modal" wire:click="resetForm">
                                        {{ __('tenantmanagement::admin.cancel') }}
                                    </button>
                                </div>
                                <div class="col">
                                    <button type="submit" class="btn btn-primary w-100">{{ __('tenantmanagement::admin.save') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modül Yönetimi Modal -->
    <div class="modal fade" id="modal-module-management" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('tenantmanagement::admin.module_management') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @if($tenantId)
                    <livewire:tenant-module-component :tenant-id="$tenantId" :key="'tm-'.$tenantId.'-'.$refreshModuleKey" />
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Domain Yönetimi Modal -->
    <div class="modal fade" id="modal-domain-management" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('tenantmanagement::admin.domain_management') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Yeni Domain Ekleme -->
                    <div class="input-group mb-4">
                        <input type="text" class="form-control" placeholder="{{ __('tenantmanagement::admin.add_new_domain') }}" wire:model="newDomain">
                        <button class="btn btn-primary" wire:click="addDomain">{{ __('tenantmanagement::admin.add') }}</button>
                    </div>
                    <!-- Ekli Domainler Tablosu -->
                    @if (count($domains) > 0)
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ __('tenantmanagement::admin.attached_domains') }}</h5>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-vcenter card-table">
                                <thead>
                                    <tr>
                                        <th>{{ __('tenantmanagement::admin.domain') }}</th>
                                        <th class="w-1">{{ __('tenantmanagement::admin.action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($domains as $domain)
                                    <tr>
                                        <td>
                                            @if ($editingDomainId === $domain['id'])
                                            <div class="input-group">
                                                <input type="text" class="form-control"
                                                    wire:model.defer="editingDomainValue">
                                                <button class="btn btn-primary"
                                                    wire:click="updateDomain({{ $domain['id'] }})">{{ __('tenantmanagement::admin.save') }}</button>
                                            </div>
                                            @else
                                            {{ $domain['domain'] }}
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2 justify-content-end">
                                                <button class="btn btn-outline-secondary"
                                                    wire:click="startEditingDomain({{ $domain['id'] }}, '{{ $domain['domain'] }}')">{{ __('tenantmanagement::admin.edit') }}</button>
                                                <button class="btn btn-outline-danger"
                                                    wire:click="deleteDomain({{ $domain['id'] }})">{{ __('tenantmanagement::admin.delete') }}</button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @else
                    <div class="alert alert-secondary text-center">
                        {{ __('tenantmanagement::admin.no_domains_found') }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    @push('styles')
    <style>
        /* Modal içindeki dropdown'ların z-index sorununu çöz */
        .modal .form-floating select {
            position: relative;
            z-index: 1060 !important;
        }
        
        .modal .form-floating select:focus {
            z-index: 1061 !important;
        }
        
        /* Modal overflow ayarları */
        .modal-dialog-scrollable .modal-body {
            max-height: calc(100vh - 200px);
            overflow-y: auto;
        }
        
        /* Dropdown açıldığında kaybolan sorununu önle */
        .modal select option {
            background-color: white !important;
            color: black !important;
        }
    </style>
    @endpush
    
    @push('scripts')
    <script>
        document.addEventListener('livewire:initialized', () => {
            // 🔥 Livewire ile Modal Manager entegrasyonu
            const modalEdit = document.getElementById('modal-tenant-edit');
            const modalAdd = document.getElementById('modal-tenant-add');
            const modalModule = document.getElementById('modal-module-management');
            const modalDomain = document.getElementById('modal-domain-management');
            
            // Modal kapanırken form resetleme
            modalEdit.addEventListener('hidden.bs.modal', () => {
                @this.resetForm();
            });
            
            modalAdd.addEventListener('hidden.bs.modal', () => {
                @this.resetForm();
            });
            
            // Modal açılırken dropdown pozisyonlarını düzelt
            modalEdit.addEventListener('shown.bs.modal', () => {
                // AI Provider dropdown'ları için özel stil
                const selects = modalEdit.querySelectorAll('select[wire\\:model="tenant_ai_provider_id"]');
                selects.forEach(select => {
                    select.style.zIndex = '9999';
                });
            });
            
            modalAdd.addEventListener('shown.bs.modal', () => {
                // AI Provider dropdown'ları için özel stil
                const selects = modalAdd.querySelectorAll('select[wire\\:model="tenant_ai_provider_id"]');
                selects.forEach(select => {
                    select.style.zIndex = '9999';
                });
            });
            
            // 🔥 Livewire ile Global Modal Manager'ı entegre et
            if (window.globalModalManager) {
                // Global Modal Manager entegrasyonu aktif
                
                // Livewire modal kapatma eventi
                Livewire.on('hideModal', ({ id }) => {
                    window.globalModalManager.closeModal(id);
                });
                
                // Livewire modal açma eventi
                Livewire.on('showModal', ({ id }) => {
                    window.globalModalManager.openModal(id);
                });
            } else {
                // Global Modal Manager bulunamadı - fallback kullanılıyor
                
                // Fallback modal yönetimi
                Livewire.on('hideModal', ({ id }) => {
                    const modal = document.getElementById(id);
                    const modalInstance = bootstrap.Modal.getInstance(modal);
                    if (modalInstance) {
                        modalInstance.hide();
                    }
                });
            }
        });
    </script>
    @endpush
</div>
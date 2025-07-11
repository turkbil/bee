@include('modulemanagement::helper')
<div>
    @include('admin.partials.error_message')
    <form wire:submit.prevent="save">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <!-- Left Column -->
                    <div class="col-lg-8">
                        <!-- Display Name -->
                        <div class="form-floating mb-3">
                            <input type="text" wire:model.defer="inputs.display_name"
                                class="form-control @error('inputs.display_name') is-invalid @enderror"
                                placeholder="{{ __('modulemanagement::admin.display_name_placeholder') }}">
                            <label>{{ __('modulemanagement::admin.display_name') }}</label>
                            @error('inputs.display_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Modül Seçimi Bölümü -->
                        <div class="mb-3">
                            @if(count($this->availableModules) > 0)
                            <!-- Computed property kullanımı -->
                            <div class="form-floating">
                                <select wire:model.defer="inputs.name"
                                    class="form-select @error('inputs.name') is-invalid @enderror"
                                    data-choices
                                    data-choices-search="{{ count($this->availableModules) > 6 ? 'true' : 'false' }}"
                                    data-choices-placeholder="{{ __('modulemanagement::admin.module_select_placeholder') }}">
                                    <option value="">{{ __('modulemanagement::admin.module_select_placeholder') }}</option>
                                    @foreach($this->availableModules as $name => $display)
                                    <option value="{{ $name }}">{{ $display }}</option>
                                    @endforeach
                                </select>
                                <label>{{ __('modulemanagement::admin.module_selection') }}</label>
                                @error('inputs.name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            @else
                            <div class="alert alert-info d-flex align-items-center">
                                <i class="fas fa-check-circle me-3"></i>
                                <div>
                                    <h4 class="alert-title">{{ __('modulemanagement::admin.all_modules_added') }}</h4>
                                    <div class="text-muted">{{ __('modulemanagement::admin.install_new_modules_info') }}
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>

                        <!-- Modül Tipi -->
                        <div class="form-floating mb-3">
                            <select wire:model.defer="inputs.type"
                                class="form-select @error('inputs.type') is-invalid @enderror"
                                data-choices
                                data-choices-search="false"
                                data-choices-placeholder="{{ __('modulemanagement::admin.module_type_placeholder') }}">
                                <option value="content" {{ $inputs['type']=='content' ? 'selected' : '' }}>{{ __('modulemanagement::admin.content_module') }}
                                </option>
                                <option value="management" {{ $inputs['type']=='management' ? 'selected' : '' }}>{{ __('modulemanagement::admin.management_module') }}</option>
                                <option value="system" {{ $inputs['type']=='system' ? 'selected' : '' }}>{{ __('modulemanagement::admin.system_module') }}
                                </option>
                            </select>
                            <label>{{ __('modulemanagement::admin.module_type') }}</label>
                            @error('inputs.type')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Ayar Seçimi alanı -->
                        <div class="form-floating mb-3">
                            <select wire:model.defer="inputs.setting"
                                class="form-select @error('inputs.setting') is-invalid @enderror"
                                data-choices
                                data-choices-search="{{ count($availableSettings) > 6 ? 'true' : 'false' }}"
                                data-choices-placeholder="{{ __('modulemanagement::admin.setting_placeholder') }}">
                                <option value="">{{ __('modulemanagement::admin.setting_placeholder') }}</option>
                                @foreach($availableSettings as $setting)
                                <option value="{{ $setting->id }}" {{ $inputs['setting']==$setting->id ? 'selected' :
                                    '' }}>{{ $setting->name }}</option>
                                @endforeach
                            </select>
                            <label>{{ __('modulemanagement::admin.setting_selection') }}</label>
                            @error('inputs.setting')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Versiyon -->
                        <div class="form-floating mb-3">
                            <input type="text" wire:model.defer="inputs.version"
                                class="form-control @error('inputs.version') is-invalid @enderror"
                                placeholder="{{ __('modulemanagement::admin.version_placeholder') }}">
                            <label>{{ __('modulemanagement::admin.version') }}</label>
                            @error('inputs.version')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Açıklama -->
                        <div class="form-floating mb-3">
                            <textarea wire:model.defer="inputs.description" class="form-control" data-bs-toggle="autosize"
                                placeholder="{{ __('modulemanagement::admin.description_placeholder') }}"></textarea>
                            <label>{{ __('modulemanagement::admin.description') }}</label>
                        </div>
                        
                        <!-- Aktif/Pasif -->
                        <div class="mb-3">
                            <div class="pretty p-default p-curve p-toggle p-smooth ms-1">
                                <input type="checkbox" id="is_active" name="is_active" wire:model.defer="inputs.is_active"
                                    value="1" checked />

                                <div class="state p-success p-on ms-2">
                                    <label>{{ __('modulemanagement::admin.active') }}</label>
                                </div>
                                <div class="state p-danger p-off ms-2">
                                    <label>{{ __('modulemanagement::admin.inactive') }}</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column - Domain Selection -->
                    <div class="col-lg-4 mt-4 mt-lg-0">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">{{ __('modulemanagement::admin.domain_assignment') }}</h3>
                            </div>
                            <div class="list-group list-group-flush overflow-auto" style="max-height: 35rem">
                                @foreach($domains as $domainId => $domain)
                                <div class="list-group-item">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <a href="#">
                                                <span class="avatar avatar-1" style="background-color: #206bc4;">
                                                    <i class="fas fa-globe text-white"></i>
                                                </span>
                                            </a>
                                        </div>
                                        <div class="col text-truncate d-flex justify-content-between align-items-center">
                                            <a href="#" class="text-body d-block">{{ $domain['name'] }}</a>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                                    wire:model.defer="selectedDomains.{{ $domainId }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <x-form-footer route="admin.modulemanagement" :model-id="$moduleId" />
        </div>
    </form>
</div>
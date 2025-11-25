@php
    View::share('pretitle', $userId ? 'Kullanıcı Düzenle' : 'Yeni Kullanıcı');
@endphp
{{-- Modules/UserManagement/resources/views/livewire/user-manage-component.blade.php --}}
@include('usermanagement::helper')
<div>
    <form wire:submit.prevent="save">
        <div class="row g-3">
            <!-- Sol Kolon - Kullanıcı Profil Bilgileri -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-user-circle me-2"></i>{{ __('usermanagement::admin.user_profile') }}
                        </h3>
                    </div>
                    <div class="card-body">
                        <!-- Profil Fotoğrafı -->
                        <div class="mb-4 text-center">
                            <div class="position-relative d-inline-block">
                                @if(isset($temporaryImages['avatar']))
                                    <span class="avatar avatar-xl" style="background-image: url('{{ $temporaryImages['avatar']->temporaryUrl() }}')"></span>
                                    <a class="position-absolute top-0 end-0 bg-danger text-white rounded-circle p-1" style="margin-top: -5px; margin-right: -5px; cursor: pointer;"
                                       wire:click.prevent="removeImage('avatar')" title="{{ __('usermanagement::admin.remove_photo') }}">
                                       <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-x" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                    </a>
                                @elseif($model && $model->getFirstMedia('avatar'))
                                    <span class="avatar avatar-xl" style="background-image: url('{{ $model->getFirstMediaUrl('avatar') }}')"></span>
                                    <a class="position-absolute top-0 end-0 bg-danger text-white rounded-circle p-1" style="margin-top: -5px; margin-right: -5px; cursor: pointer;"
                                       wire:click.prevent="removeImage('avatar')" title="{{ __('usermanagement::admin.remove_photo') }}">
                                       <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-x" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                    </a>
                                @else
                                    <span class="avatar avatar-xl bg-primary-lt">
                                        {{ strtoupper(substr($inputs['name'] ?? 'U', 0, 2)) }}
                                    </span>
                                @endif
                            </div>
                            
                            <div class="mt-3">
                                <label class="btn btn-outline-primary btn-sm" for="avatar-upload">
                                    <i class="fas fa-camera me-1"></i> {{ __('usermanagement::admin.upload_photo') }}
                                </label>
                                <input id="avatar-upload" type="file" wire:model="temporaryImages.avatar" class="d-none" accept="image/jpeg,image/png,image/webp">
                                
                                @error('temporaryImages.avatar')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                                
                                <div wire:loading wire:target="temporaryImages.avatar">
                                    <div class="progress progress-sm mt-1">
                                        <div class="progress-bar progress-bar-indeterminate bg-primary"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- İsim -->
                        <div class="form-floating mb-3">
                            <input type="text" wire:model.defer="inputs.name" class="form-control @error('inputs.name') is-invalid @enderror" placeholder="{{ __('usermanagement::admin.name_surname') }}">
                            <label>{{ __('usermanagement::admin.name_surname') }} {{ __('usermanagement::admin.required') }}</label>
                            @error('inputs.name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- E-posta -->
                        <div class="form-floating mb-3">
                            <input type="email" wire:model.defer="inputs.email" class="form-control @error('inputs.email') is-invalid @enderror" placeholder="example@mail.com">
                            <label>{{ __('usermanagement::admin.email_address') }} {{ __('usermanagement::admin.required') }}</label>
                            @error('inputs.email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Şifre -->
                        <div class="form-floating mb-3">
                            <input type="password" wire:model.defer="inputs.password" class="form-control @error('inputs.password') is-invalid @enderror" placeholder="••••••••">
                            <label>{{ __('usermanagement::admin.password') }} {{ $userId ? __('usermanagement::admin.change_password_note') : __('usermanagement::admin.required') }}</label>
                            @error('inputs.password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Durum -->
                        <div class="mb-3">
                            <label class="form-label d-block">{{ __('usermanagement::admin.user_status') }}</label>
                            <div class="pretty p-default p-curve p-toggle p-smooth ms-1">
                                <input type="checkbox" id="is_active" name="is_active" wire:model.defer="inputs.is_active"
                                    value="1" {{ (!isset($inputs['is_active']) || $inputs['is_active']) ? 'checked' : '' }} />
                                <div class="state p-success p-on ms-2">
                                    <label>{{ __('usermanagement::admin.active') }}</label>
                                </div>
                                <div class="state p-danger p-off ms-2">
                                    <label>{{ __('usermanagement::admin.inactive') }}</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Sağ Kolon - Roller ve Yetkiler -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-user-shield me-2"></i>{{ __('usermanagement::admin.user_role') }}
                        </h3>
                    </div>
                    
                    <div class="card-body">
                        <!-- Roller Seçimi - Büyük ve Resimli Butonlar -->
                        <div class="row mb-4">
                            <!-- Normal Üye -->
                            <div class="col-md-6 mb-3">
                                <label class="card shadow-sm user-role-card form-selectgroup-item h-100 {{ !in_array($inputs['role_id'], ['editor', 'admin', 'root']) ? 'active border border-3 border-primary' : '' }}">
                                    <input type="radio" 
                                        name="role" 
                                        value="user" 
                                        id="role-user" 
                                        {{ !in_array($inputs['role_id'], ['editor', 'admin', 'root']) ? 'checked' : '' }}
                                        wire:model="inputs.role_id"
                                        class="form-selectgroup-input role-radio">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center">
                                            <span class="avatar avatar-lg bg-blue-lt me-3">
                                                <i class="fas fa-user fa-lg"></i>
                                            </span>
                                            <div>
                                                <div class="font-weight-medium fs-4">{{ __('usermanagement::admin.member') }}</div>
                                                <div class="text-muted">{{ __('usermanagement::admin.normal_user') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                            
                            <!-- Editör -->
                            <div class="col-md-6 mb-3">
                                <label class="card shadow-sm user-role-card form-selectgroup-item h-100 {{ $inputs['role_id'] === 'editor' ? 'active border border-3 border-primary' : '' }}">
                                    <input type="radio" 
                                        name="role" 
                                        value="editor" 
                                        id="role-editor" 
                                        {{ $inputs['role_id'] === 'editor' ? 'checked' : '' }}
                                        wire:model="inputs.role_id"
                                        class="form-selectgroup-input role-radio">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center">
                                            <span class="avatar avatar-lg bg-green-lt me-3">
                                                <i class="fas fa-user-edit fa-lg"></i>
                                            </span>
                                            <div>
                                                <div class="font-weight-medium fs-4">{{ __('usermanagement::admin.editor') }}</div>
                                                <div class="text-muted">{{ __('usermanagement::admin.content_editor') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                            
                            <!-- Admin -->
                            <div class="col-md-6 mb-3">
                                <label class="card shadow-sm user-role-card form-selectgroup-item h-100 {{ $inputs['role_id'] === 'admin' ? 'active border border-3 border-primary' : '' }}">
                                    <input type="radio" 
                                        name="role" 
                                        value="admin" 
                                        id="role-admin" 
                                        {{ $inputs['role_id'] === 'admin' ? 'checked' : '' }}
                                        wire:model="inputs.role_id"
                                        class="form-selectgroup-input role-radio">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center">
                                            <span class="avatar avatar-lg bg-purple-lt me-3">
                                                <i class="fas fa-user-cog fa-lg"></i>
                                            </span>
                                            <div>
                                                <div class="font-weight-medium fs-4">{{ __('usermanagement::admin.admin') }}</div>
                                                <div class="text-muted">{{ __('usermanagement::admin.tenant_admin') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                            
                            <!-- Root (Sadece root kullanıcısına göster) -->
                            @if(auth()->user()->hasRole('root'))
                            <div class="col-md-6 mb-3">
                                <label class="card shadow-sm user-role-card form-selectgroup-item h-100 {{ $inputs['role_id'] === 'root' ? 'active border border-3 border-primary' : '' }}">
                                    <input type="radio" 
                                        name="role" 
                                        value="root" 
                                        id="role-root" 
                                        {{ $inputs['role_id'] === 'root' ? 'checked' : '' }}
                                        wire:model="inputs.role_id"
                                        class="form-selectgroup-input role-radio">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center">
                                            <span class="avatar avatar-lg bg-red-lt me-3">
                                                <i class="fas fa-crown fa-lg"></i>
                                            </span>
                                            <div>
                                                <div class="font-weight-medium fs-4">{{ __('usermanagement::admin.root') }}</div>
                                                <div class="text-muted">{{ __('usermanagement::admin.super_admin') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                            @endif
                        </div>
                        
                        <!-- Rol Açıklamaları -->
                        <div class="mb-4">
                            <!-- Üye Açıklaması -->
                            <div id="userInfoSection" class="role-info" style="display: {{ !in_array($inputs['role_id'], ['editor', 'admin', 'root']) ? 'block' : 'none' }}">
                                <div class="alert alert-info bg-azure-lt">
                                    <div class="d-flex">
                                        <div>
                                            <i class="fas fa-info-circle fa-2x me-3"></i>
                                        </div>
                                        <div>
                                            <h4 class="alert-title">{{ __('usermanagement::admin.normal_user') }}</h4>
                                            <p class="mb-0">{{ __('usermanagement::admin.user_role_description') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Editör Açıklaması -->
                            <div id="editorInfoSection" class="role-info" style="display: {{ $inputs['role_id'] === 'editor' ? 'block' : 'none' }}">
                                <div class="alert alert-success bg-green-lt">
                                    <div class="d-flex">
                                        <div>
                                            <i class="fas fa-info-circle fa-2x me-3"></i>
                                        </div>
                                        <div>
                                            <h4 class="alert-title">{{ __('usermanagement::admin.editor') }}</h4>
                                            <p class="mb-0">{{ __('usermanagement::admin.editor_role_description') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Admin Açıklaması -->
                            <div id="adminInfoSection" class="role-info" style="display: {{ $inputs['role_id'] === 'admin' ? 'block' : 'none' }}">
                                <div class="alert alert-primary bg-purple-lt">
                                    <div class="d-flex">
                                        <div>
                                            <i class="fas fa-info-circle fa-2x me-3"></i>
                                        </div>
                                        <div>
                                            <h4 class="alert-title">{{ __('usermanagement::admin.admin') }}</h4>
                                            <p class="mb-0">{{ __('usermanagement::admin.admin_role_description') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Root Açıklaması -->
                            <div id="rootInfoSection" class="role-info" style="display: {{ $inputs['role_id'] === 'root' ? 'block' : 'none' }}">
                                <div class="alert alert-danger bg-red-lt">
                                    <div class="d-flex">
                                        <div>
                                            <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
                                        </div>
                                        <div>
                                            <h4 class="alert-title">{{ __('usermanagement::admin.root') }}</h4>
                                            <p class="mb-0">{{ __('usermanagement::admin.root_role_warning') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Modül Yetkilendirme (Sadece Editör için) - Alpine.js ile Client-Side -->
                        <div id="editorPermissionsSection"
                             x-data="modulePermissions(@js($modulePermissions), @js($permissionTypes))"
                             x-show="$wire.inputs.role_id === 'editor'"
                             x-cloak
                             style="display: {{ $inputs['role_id'] === 'editor' ? 'block' : 'none' }}">

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h4 class="mb-0">
                                    <i class="fas fa-puzzle-piece me-2 text-primary"></i>{{ __('usermanagement::admin.module_authorization') }}
                                </h4>
                                <button type="button" @click="showDetailed = !showDetailed" class="btn btn-ghost-primary btn-sm">
                                    <i class="fas me-1" :class="showDetailed ? 'fa-th-large' : 'fa-list'"></i>
                                    <span x-text="showDetailed ? 'Basit' : 'Detaylı'"></span>
                                </button>
                            </div>

                            <!-- Standart Görünüm - Kompakt Grid -->
                            <div x-show="!showDetailed" x-transition.opacity>
                                <div class="row g-2">
                                    @foreach($availableModules as $module)
                                    <div class="col-6 col-md-4 col-lg-3">
                                        <div class="card card-sm module-card"
                                             :class="{ 'border-primary bg-primary-lt': permissions['{{ $module->name }}']?.enabled }"
                                             @click="toggleModule('{{ $module->name }}')"
                                             style="cursor: pointer; transition: all 0.15s ease;">
                                            <div class="card-body p-2">
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <div class="d-flex align-items-center">
                                                        <span class="avatar avatar-xs me-2"
                                                              :class="permissions['{{ $module->name }}']?.enabled ? 'bg-primary text-white' : 'bg-secondary-lt'">
                                                            <i class="fas fa-cube" style="font-size: 10px;"></i>
                                                        </span>
                                                        <span class="text-truncate" style="font-size: 13px; max-width: 100px;">{{ $module->display_name }}</span>
                                                    </div>
                                                    <div class="pretty p-switch p-fill" @click.stop>
                                                        <input type="checkbox"
                                                               :checked="permissions['{{ $module->name }}']?.enabled"
                                                               @change="toggleModule('{{ $module->name }}')">
                                                        <div class="state p-primary">
                                                            <label></label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Detaylı CRUD İzinleri Görünümü -->
                            <div x-show="showDetailed" x-transition.opacity>
                                <div class="row g-3">
                                    @foreach($availableModules as $module)
                                    <div class="col-md-6">
                                        <div class="card" :class="{ 'border-primary': permissions['{{ $module->name }}']?.enabled }">
                                            <div class="card-header py-2" style="cursor: pointer;" @click="toggleModule('{{ $module->name }}')">
                                                <div class="d-flex align-items-center justify-content-between w-100">
                                                    <div class="d-flex align-items-center">
                                                        <span class="avatar avatar-sm me-2"
                                                              :class="permissions['{{ $module->name }}']?.enabled ? 'bg-primary text-white' : 'bg-secondary-lt'">
                                                            <i class="fas fa-cube"></i>
                                                        </span>
                                                        <span class="fw-medium">{{ $module->display_name }}</span>
                                                        <span class="badge bg-blue-lt text-blue ms-2"
                                                              x-show="getPermissionCount('{{ $module->name }}').selected > 0"
                                                              x-text="getPermissionCount('{{ $module->name }}').selected + '/' + getPermissionCount('{{ $module->name }}').total">
                                                        </span>
                                                    </div>
                                                    <div class="pretty p-switch p-fill" @click.stop>
                                                        <input type="checkbox"
                                                               :checked="permissions['{{ $module->name }}']?.enabled"
                                                               @change="toggleModule('{{ $module->name }}')">
                                                        <div class="state p-primary">
                                                            <label></label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body p-0" x-show="permissions['{{ $module->name }}']?.enabled" x-collapse>
                                                <div class="list-group list-group-flush">
                                                    @foreach($permissionTypes as $permType)
                                                    <label class="list-group-item list-group-item-action d-flex align-items-center py-2 m-0" style="cursor: pointer;">
                                                        <span class="me-auto d-flex align-items-center">
                                                            <i class="fas fa-{{ $permType == 'view' ? 'eye text-info' : ($permType == 'create' ? 'plus text-success' : ($permType == 'update' ? 'edit text-warning' : 'trash text-danger')) }} me-2" style="width: 16px;"></i>
                                                            {{ $permissionLabels[$permType] }}
                                                        </span>
                                                        <div class="pretty p-switch p-slim">
                                                            <input type="checkbox"
                                                                   :checked="permissions['{{ $module->name }}']?.{{ $permType }}"
                                                                   @change="togglePermission('{{ $module->name }}', '{{ $permType }}')">
                                                            <div class="state p-primary">
                                                                <label></label>
                                                            </div>
                                                        </div>
                                                    </label>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Hidden input for Livewire sync -->
                            <input type="hidden" x-model="permissionsJson" wire:model.defer="modulePermissions">
                        </div>
                    </div>
                    
                    <x-form-footer route="admin.usermanagement" :model-id="$userId" />
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
// Alpine.js Module Permissions Component
document.addEventListener('alpine:init', () => {
    Alpine.data('modulePermissions', (initialPermissions, permissionTypes) => ({
        permissions: initialPermissions || {},
        permissionTypes: permissionTypes || ['view', 'create', 'update', 'delete'],
        showDetailed: false,

        init() {
            // Livewire ile senkronize et
            this.$watch('permissions', (value) => {
                this.$wire.modulePermissions = value;
            }, { deep: true });
        },

        toggleModule(moduleName) {
            if (!this.permissions[moduleName]) {
                this.permissions[moduleName] = { enabled: false };
                this.permissionTypes.forEach(type => {
                    this.permissions[moduleName][type] = false;
                });
            }

            const currentState = this.permissions[moduleName].enabled;

            if (currentState) {
                // Kapatıyorsak tüm izinleri kapat
                this.permissions[moduleName].enabled = false;
                this.permissionTypes.forEach(type => {
                    this.permissions[moduleName][type] = false;
                });
            } else {
                // Açıyorsak tüm izinleri aç
                this.permissions[moduleName].enabled = true;
                this.permissionTypes.forEach(type => {
                    this.permissions[moduleName][type] = true;
                });
            }
        },

        togglePermission(moduleName, permType) {
            if (!this.permissions[moduleName]) {
                this.permissions[moduleName] = { enabled: false };
                this.permissionTypes.forEach(type => {
                    this.permissions[moduleName][type] = false;
                });
            }

            const newValue = !this.permissions[moduleName][permType];
            this.permissions[moduleName][permType] = newValue;

            // View kapatılırsa diğer tüm izinleri kapat
            if (permType === 'view' && !newValue) {
                this.permissionTypes.forEach(type => {
                    if (type !== 'view') {
                        this.permissions[moduleName][type] = false;
                    }
                });
            }

            // Diğer izinler açılırsa view'ı da aç
            if (permType !== 'view' && newValue) {
                this.permissions[moduleName].view = true;
            }

            // En az bir izin aktifse modülü aktif yap
            const hasAnyPermission = this.permissionTypes.some(type => this.permissions[moduleName][type]);
            this.permissions[moduleName].enabled = hasAnyPermission;
        },

        getPermissionCount(moduleName) {
            if (!this.permissions[moduleName]) {
                return { selected: 0, total: this.permissionTypes.length };
            }

            const selected = this.permissionTypes.filter(type => this.permissions[moduleName][type]).length;
            return { selected, total: this.permissionTypes.length };
        },

        get permissionsJson() {
            return JSON.stringify(this.permissions);
        },

        set permissionsJson(value) {
            try {
                this.permissions = JSON.parse(value);
            } catch (e) {
                console.error('Invalid JSON:', e);
            }
        }
    }));
});

document.addEventListener('DOMContentLoaded', function() {
    // İlgili alanları göster/gizle işlevselliği
    function updateVisibility(roleId) {
        // Tüm rol bilgilerini gizle
        document.querySelectorAll('.role-info').forEach(el => el.style.display = 'none');

        // Modül izinleri bölümünü göster/gizle
        const permissionsSection = document.getElementById('editorPermissionsSection');

        if (roleId === 'editor') {
            if (permissionsSection) permissionsSection.style.display = 'block';
            document.getElementById('editorInfoSection').style.display = 'block';
        } else if (roleId === 'admin') {
            if (permissionsSection) permissionsSection.style.display = 'none';
            document.getElementById('adminInfoSection').style.display = 'block';
        } else if (roleId === 'root') {
            if (permissionsSection) permissionsSection.style.display = 'none';
            document.getElementById('rootInfoSection').style.display = 'block';
        } else {
            if (permissionsSection) permissionsSection.style.display = 'none';
            document.getElementById('userInfoSection').style.display = 'block';
        }
    }

    // Rol değişikliği dinleyicileri
    document.querySelectorAll('.role-radio').forEach(radio => {
        radio.addEventListener('change', function() {
            updateVisibility(this.value);
        });
    });

    // Livewire'dan gelen rolChanged olayını dinle
    Livewire.on('roleChanged', function(role) {
        updateVisibility(role);
    });
});
</script>

<style>
[x-cloak] { display: none !important; }
.module-card:hover { transform: translateY(-1px); box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
.form-check-input { cursor: pointer; }
.form-check-input:checked { background-color: var(--tblr-primary); border-color: var(--tblr-primary); }
</style>
@endpush
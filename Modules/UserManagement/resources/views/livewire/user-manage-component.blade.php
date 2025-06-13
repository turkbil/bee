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
                            <i class="fas fa-user-circle text-primary me-2"></i>Kullanıcı Profili
                        </h3>
                    </div>
                    <div class="card-body">
                        <!-- Profil Fotoğrafı -->
                        <div class="mb-4 text-center">
                            <div class="position-relative d-inline-block">
                                @if(isset($temporaryImages['avatar']))
                                    <span class="avatar avatar-xl" style="background-image: url('{{ $temporaryImages['avatar']->temporaryUrl() }}')"></span>
                                    <a class="position-absolute top-0 end-0 bg-danger text-white rounded-circle p-1" style="margin-top: -5px; margin-right: -5px; cursor: pointer;"
                                       wire:click.prevent="removeImage('avatar')" title="Fotoğrafı Kaldır">
                                       <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-x" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                    </a>
                                @elseif($model && $model->getFirstMedia('avatar'))
                                    <span class="avatar avatar-xl" style="background-image: url('{{ $model->getFirstMediaUrl('avatar') }}')"></span>
                                    <a class="position-absolute top-0 end-0 bg-danger text-white rounded-circle p-1" style="margin-top: -5px; margin-right: -5px; cursor: pointer;"
                                       wire:click.prevent="removeImage('avatar')" title="Fotoğrafı Kaldır">
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
                                    <i class="fas fa-camera me-1"></i> Fotoğraf Yükle
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
                        <div class="mb-3">
                            <label class="form-label required">İsim Soyisim</label>
                            <div class="input-icon">
                                <span class="input-icon-addon">
                                    <i class="fas fa-user"></i>
                                </span>
                                <input type="text" wire:model.defer="inputs.name" class="form-control @error('inputs.name') is-invalid @enderror" placeholder="İsim Soyisim">
                            </div>
                            @error('inputs.name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- E-posta -->
                        <div class="mb-3">
                            <label class="form-label required">E-posta Adresi</label>
                            <div class="input-icon">
                                <span class="input-icon-addon">
                                    <i class="fas fa-envelope"></i>
                                </span>
                                <input type="email" wire:model.defer="inputs.email" class="form-control @error('inputs.email') is-invalid @enderror" placeholder="ornek@mail.com">
                            </div>
                            @error('inputs.email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Şifre -->
                        <div class="mb-3">
                            <label class="form-label {{ !$userId ? 'required' : '' }}">Şifre {{ $userId ? '(Değiştirmek için doldurun)' : '' }}</label>
                            <div class="input-icon">
                                <span class="input-icon-addon">
                                    <i class="fas fa-lock"></i>
                                </span>
                                <input type="password" wire:model.defer="inputs.password" class="form-control @error('inputs.password') is-invalid @enderror" placeholder="••••••••">
                            </div>
                            @error('inputs.password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Durum -->
                        <div class="mb-3">
                            <label class="form-label d-block">Kullanıcı Durumu</label>
                            <div class="pretty p-default p-curve p-toggle p-smooth ms-1">
                                <input type="checkbox" id="is_active" name="is_active" wire:model.defer="inputs.is_active"
                                    value="1" {{ (!isset($inputs['is_active']) || $inputs['is_active']) ? 'checked' : '' }} />
                                <div class="state p-success p-on ms-2">
                                    <label>Aktif</label>
                                </div>
                                <div class="state p-danger p-off ms-2">
                                    <label>Aktif Değil</label>
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
                            <i class="fas fa-user-shield text-primary me-2"></i>Kullanıcı Rolü
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
                                                <div class="font-weight-medium fs-4">Üye</div>
                                                <div class="text-muted">Normal Kullanıcı</div>
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
                                                <div class="font-weight-medium fs-4">Editör</div>
                                                <div class="text-muted">İçerik Düzenleyici</div>
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
                                                <div class="font-weight-medium fs-4">Admin</div>
                                                <div class="text-muted">Tenant Yöneticisi</div>
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
                                                <div class="font-weight-medium fs-4">Root</div>
                                                <div class="text-muted">Süper Yönetici</div>
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
                                            <h4 class="alert-title">Normal Üye</h4>
                                            <p class="mb-0">Normal üye rolündeki kullanıcılar, sadece temel kullanıcı işlemlerini yapabilirler. Yönetim paneline ve modüllere erişimleri yoktur.</p>
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
                                            <h4 class="alert-title">Editör Rolü</h4>
                                            <p class="mb-0">Editörler, aşağıda seçilen modüllere erişebilir ve bu modüllerle ilgili işlemleri yapabilirler. Her modül için ayrı CRUD yetkileri tanımlanabilir.</p>
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
                                            <h4 class="alert-title">Admin Yetkileri</h4>
                                            <p class="mb-0">Admin kullanıcısı, kendi tenant'ı içerisindeki tüm modüllere ve fonksiyonlara tam erişime sahiptir. Bu rol için özel izin ataması gerekmez.</p>
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
                                            <h4 class="alert-title">Root Yetkisi Uyarısı</h4>
                                            <p class="mb-0">Root kullanıcısı, sistemdeki tüm modüllere ve fonksiyonlara tam erişime sahiptir. Bu rol, sadece sistem yöneticileri için tasarlanmıştır.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Modül Yetkilendirme (Sadece Editör için) -->
                        <div id="editorPermissionsSection" style="display: {{ $inputs['role_id'] === 'editor' ? 'block' : 'none' }}">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h4>
                                    <i class="fas fa-puzzle-piece text-primary me-2"></i>Modül Yetkilendirme
                                </h4>
                                <button type="button" wire:click="toggleDetailedPermissions" id="toggleDetailedPermissions" class="btn btn-outline-primary btn-sm">
                                    <i class="fas {{ $showDetailedPermissions ? 'fa-compress-alt' : 'fa-cogs' }} me-1"></i> 
                                    {{ $showDetailedPermissions ? 'Basit Görünüm' : 'Detaylı Yetkilendirme' }}
                                </button>
                            </div>
                            
                            <!-- Standart Görünüm (Başlangıçta Göster) -->
                            <div id="standardPermissionsView" style="display: {{ $showDetailedPermissions ? 'none' : 'block' }}">
                                <div class="row">
                                    @foreach($availableModules as $module)
                                    <div class="col-md-6 mb-3" wire:key="module-{{ $module->module_id }}">
                                        <label class="form-selectgroup-item flex-fill">
                                            <input type="checkbox" 
                                                class="form-selectgroup-input"
                                                data-module="{{ $module->name }}"
                                                wire:click="toggleModuleEnabled('{{ $module->name }}')"
                                                {{ isset($modulePermissions[$module->name]) && $modulePermissions[$module->name]['enabled'] ? 'checked' : '' }}>
                                            <div class="form-selectgroup-label d-flex align-items-center p-3">
                                                <div class="form-selectgroup-label-content d-flex align-items-center flex-grow-1">
                                                    <span class="avatar avatar-sm me-3 bg-{{ isset($modulePermissions[$module->name]) && $modulePermissions[$module->name]['enabled'] ? 'primary' : 'secondary-lt' }}">
                                                        <i class="fas fa-puzzle-piece"></i>
                                                    </span>
                                                    <div>
                                                        <div class="font-weight-medium fs-5">{{ $module->display_name }}</div>
                                                    </div>
                                                </div>
                                                
                                                <div class="d-flex align-items-center">
                                                    @if(isset($modulePermissionCounts[$module->name]) && $modulePermissionCounts[$module->name]['selected'] > 0 && $modulePermissionCounts[$module->name]['selected'] < $modulePermissionCounts[$module->name]['total'])
                                                    <span class="badge bg-default text-default-fg me-3">
                                                        {{ $modulePermissionCounts[$module->name]['selected'] }}/{{ $modulePermissionCounts[$module->name]['total'] }}
                                                    </span>
                                                    @endif
                                                    
                                                    <div class="pretty p-default p-smooth p-bigger">
                                                        <input type="checkbox" 
                                                            wire:click="toggleModuleEnabled('{{ $module->name }}')"
                                                            {{ isset($modulePermissions[$module->name]) && $modulePermissions[$module->name]['enabled'] ? 'checked' : '' }}>
                                                        <div class="state p-primary">
                                                            <label></label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            
                            <!-- Detaylı CRUD İzinleri Görünümü -->
                            <div id="detailed-permissions-panel" style="display: {{ $showDetailedPermissions ? 'block' : 'none' }}">
                                <div class="row">
                                    @foreach($availableModules as $module)
                                    <div class="col-md-6 mb-4" wire:key="detail-{{ $module->module_id }}">
                                        <div class="card">
                                            <div class="card-header d-flex align-items-center">
                                                <div class="form-selectgroup-label-content d-flex align-items-center flex-grow-1">
                                                    <span class="avatar avatar-sm me-3 bg-{{ isset($modulePermissions[$module->name]) && $modulePermissions[$module->name]['enabled'] ? 'primary' : 'secondary-lt' }}">
                                                        <i class="fas fa-puzzle-piece"></i>
                                                    </span>
                                                    <div>
                                                        <div class="font-weight-medium fs-5">{{ $module->display_name }}</div>
                                                    </div>
                                                </div>
                                                
                                                <div class="d-flex align-items-center">
                                                    @if(isset($modulePermissionCounts[$module->name]) && $modulePermissionCounts[$module->name]['selected'] > 0 && $modulePermissionCounts[$module->name]['selected'] < $modulePermissionCounts[$module->name]['total'])
                                                    <span class="badge bg-default text-default-fg me-3">
                                                        {{ $modulePermissionCounts[$module->name]['selected'] }}/{{ $modulePermissionCounts[$module->name]['total'] }}
                                                    </span>
                                                    @endif
                                                    
                                                    <div class="pretty p-default p-smooth p-bigger">
                                                        <input type="checkbox" 
                                                            wire:click="toggleModuleEnabled('{{ $module->name }}')"
                                                            {{ isset($modulePermissions[$module->name]) && $modulePermissions[$module->name]['enabled'] ? 'checked' : '' }}>
                                                        <div class="state p-primary">
                                                            <label></label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                                                                        
                                            <!-- İzin Listesi -->
                                            <div class="list-group list-group-flush">
                                                <!-- Her bir izin tipi için satır -->
                                                @foreach($permissionTypes as $permType)
                                                <div class="list-group-item py-2 list-group-item-action">
                                                    <div class="d-flex align-items-center">
                                                        <span class="avatar avatar-xs me-2 bg-{{ isset($modulePermissions[$module->name]) && $modulePermissions[$module->name][$permType] ? 'blue' : 'secondary' }}-lt">
                                                            <i class="fas fa-{{ $permType == 'view' ? 'eye' : ($permType == 'create' ? 'plus' : ($permType == 'update' ? 'edit' : 'trash')) }} fa-sm"></i>
                                                        </span>
                                                        <div class="flex-fill">{{ $permissionLabels[$permType] }}</div>
                                                        <div class="pretty p-switch p-slim">
                                                            <input type="checkbox" 
                                                                wire:click="toggleModulePermission('{{ $module->name }}', '{{ $permType }}')"
                                                                {{ isset($modulePermissions[$module->name]) && $modulePermissions[$module->name][$permType] ? 'checked' : '' }}>
                                                            <div class="state p-primary">
                                                                <label></label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
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
document.addEventListener('DOMContentLoaded', function() {
    // İlgili alanları göster/gizle işlevselliği
    function updateVisibility(roleId) {
        // Tüm rol bilgilerini gizle
        document.querySelectorAll('.role-info').forEach(el => el.style.display = 'none');
        
        // Modül izinleri bölümünü göster/gizle
        const permissionsSection = document.getElementById('editorPermissionsSection');
        
        if (roleId === 'editor') {
            // Editor seçiliyse
            if (permissionsSection) permissionsSection.style.display = 'block';
            document.getElementById('editorInfoSection').style.display = 'block';
        } else if (roleId === 'admin') {
            // Admin seçiliyse
            if (permissionsSection) permissionsSection.style.display = 'none';
            document.getElementById('adminInfoSection').style.display = 'block';
        } else if (roleId === 'root') {
            // Root seçiliyse
            if (permissionsSection) permissionsSection.style.display = 'none';
            document.getElementById('rootInfoSection').style.display = 'block';
        } else {
            // Normal kullanıcı seçiliyse
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
        console.log('roleChanged event received:', role);
        updateVisibility(role);
    });

    // Modül izinleri güncellendiğinde
    Livewire.on('modulePermissionsUpdated', function() {
        console.log('Module permissions updated event received');
    });
});
</script>
@endpush
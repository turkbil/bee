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
                                    value="1" {{ $inputs['is_active'] ? 'checked' : '' }} />
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
                                <label class="card shadow-sm user-role-card form-selectgroup-item h-100 {{ !in_array($inputs['role_id'], ['editor', 'admin', 'root']) ? 'active' : '' }}">
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
                                <label class="card shadow-sm user-role-card form-selectgroup-item h-100 {{ $inputs['role_id'] === 'editor' ? 'active' : '' }}">
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
                                <label class="card shadow-sm user-role-card form-selectgroup-item h-100 {{ $inputs['role_id'] === 'admin' ? 'active' : '' }}">
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
                                <label class="card shadow-sm user-role-card form-selectgroup-item h-100 {{ $inputs['role_id'] === 'root' ? 'active' : '' }}">
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
                                <a href="#" id="show-detailed-permissions" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-cogs me-1"></i> Detaylı Yetkilendirme
                                </a>
                            </div>
                            
                            <div class="form-selectgroup form-selectgroup-boxes d-flex flex-column mb-3">
                                <div class="row">
                                    @foreach($availableModules as $module)
                                    <div class="col-md-6 mb-3" wire:key="module-{{ $module->module_id }}">
                                        <label class="form-selectgroup-item flex-fill">
                                            <input 
                                                type="checkbox" 
                                                class="form-selectgroup-input module-toggle"
                                                data-module="{{ $module->name }}"
                                                wire:model.defer="modulePermissions.{{ $module->name }}.enabled">
                                            <div class="form-selectgroup-label d-flex align-items-center p-3">
                                                <div class="me-3">
                                                    <span class="form-selectgroup-check"></span>
                                                </div>
                                                <div class="form-selectgroup-label-content d-flex align-items-center">
                                                    <span class="avatar avatar-sm me-3 bg-{{ isset($modulePermissions[$module->name]) && $modulePermissions[$module->name]['enabled'] ? 'primary' : 'secondary-lt' }}"
                                                        data-module="{{ $module->name }}">
                                                        <i class="fas fa-puzzle-piece"></i>
                                                    </span>
                                                    <div>
                                                        <div class="font-weight-medium">{{ $module->display_name }}</div>
                                                        <div class="text-secondary">{{ $module->name }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            
                            <!-- Detaylı CRUD İzinleri (Başlangıçta Gizli) -->
                            <div id="detailed-permissions-panel" class="card mt-3" style="display: none;">
                                <div class="card-header">
                                    <h4 class="card-title">
                                        <i class="fas fa-key text-primary me-2"></i>Detaylı Modül İzinleri
                                    </h4>
                                </div>
                                <div class="card-body">
                                    @foreach($availableModules as $module)
                                    <div class="mb-4 module-detail-item" id="detail-{{ $module->module_id }}" 
                                         wire:key="detail-{{ $module->module_id }}"
                                         data-enabled="{{ isset($modulePermissions[$module->name]) && $modulePermissions[$module->name]['enabled'] ? 'true' : 'false' }}">
                                        <div class="d-flex align-items-center mb-2">
                                            <span class="avatar avatar-sm me-2 bg-{{ isset($modulePermissions[$module->name]) && $modulePermissions[$module->name]['enabled'] ? 'primary' : 'secondary-lt' }}">
                                                <i class="fas fa-puzzle-piece"></i>
                                            </span>
                                            <h5 class="mb-0">{{ $module->display_name }}</h5>
                                        </div>
                                        <div class="row text-center">
                                            <div class="col-3">
                                                <div class="mb-2">Görüntüle</div>
                                                <label class="form-check form-check-single form-switch mb-0">
                                                    <input class="form-check-input module-crud"
                                                        data-module="{{ $module->name }}"
                                                        data-type="view"
                                                        wire:model.defer="modulePermissions.{{ $module->name }}.view"
                                                        type="checkbox"
                                                        {{ !isset($modulePermissions[$module->name]) || !$modulePermissions[$module->name]['enabled'] ? 'disabled' : '' }}>
                                                </label>
                                            </div>
                                            <div class="col-3">
                                                <div class="mb-2">Oluştur</div>
                                                <label class="form-check form-check-single form-switch mb-0">
                                                    <input class="form-check-input module-crud"
                                                        data-module="{{ $module->name }}"
                                                        data-type="create"
                                                        wire:model.defer="modulePermissions.{{ $module->name }}.create"
                                                        type="checkbox"
                                                        {{ !isset($modulePermissions[$module->name]) || !$modulePermissions[$module->name]['enabled'] ? 'disabled' : '' }}>
                                                </label>
                                            </div>
                                            <div class="col-3">
                                                <div class="mb-2">Düzenle</div>
                                                <label class="form-check form-check-single form-switch mb-0">
                                                    <input class="form-check-input module-crud"
                                                        data-module="{{ $module->name }}"
                                                        data-type="update"
                                                        wire:model.defer="modulePermissions.{{ $module->name }}.update"
                                                        type="checkbox"
                                                        {{ !isset($modulePermissions[$module->name]) || !$modulePermissions[$module->name]['enabled'] ? 'disabled' : '' }}>
                                                </label>
                                            </div>
                                            <div class="col-3">
                                                <div class="mb-2">Sil</div>
                                                <label class="form-check form-check-single form-switch mb-0">
                                                    <input class="form-check-input module-crud"
                                                        data-module="{{ $module->name }}"
                                                        data-type="delete"
                                                        wire:model.defer="modulePermissions.{{ $module->name }}.delete"
                                                        type="checkbox"
                                                        {{ !isset($modulePermissions[$module->name]) || !$modulePermissions[$module->name]['enabled'] ? 'disabled' : '' }}>
                                                </label>
                                            </div>
                                        </div>
                                        <hr class="mt-3">
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Kart Footer - Butonlar -->
                    <div class="card-footer text-end">
                        <div class="d-flex">
                            <a href="{{ route('admin.usermanagement.index') }}" class="btn btn-link">
                                <i class="fas fa-arrow-left me-2"></i>Geri Dön
                            </a>
                            <button type="submit" class="btn btn-primary ms-auto">
                                <i class="fas fa-save me-2"></i>
                                {{ $userId ? 'Güncelle' : 'Kaydet' }}
                                <div wire:loading wire:target="save" class="spinner-border spinner-border-sm ms-2" role="status"></div>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('styles')
<style>
    /* Rol Butonları Stillemesi */
    .user-role-card {
        transition: all 0.3s ease;
        cursor: pointer;
        border: 2px solid transparent;
    }
    
    .user-role-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1) !important;
    }
    
    .user-role-card.active {
        border-color: #3498db;
        background-color: rgba(52, 152, 219, 0.1);
    }
    
    /* Form Switch Büyük Boyut */
    .form-check-single .form-check-input {
        width: 2.5em;
        height: 1.25em;
    }
    
    /* Form Selectgroup */
    .form-selectgroup-input:checked + .form-selectgroup-label {
        color: #206bc4;
        background: rgba(32, 107, 196, 0.1);
        border-color: #206bc4;
    }
    
    .form-selectgroup-label-content {
        flex-grow: 1;
    }
    
    .form-selectgroup-check {
        width: 1.25rem;
        height: 1.25rem;
        border: 1px solid rgba(32, 107, 196, 0.2);
        border-radius: 4px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    
    .form-selectgroup-input:checked + .form-selectgroup-label .form-selectgroup-check:before {
        content: '\f00c';
        font-family: 'Font Awesome 5 Free';
        font-weight: 900;
        font-size: 0.75rem;
        color: #206bc4;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Detaylı izinler panelini aç/kapat
    $(document).on('click', '#show-detailed-permissions', function(e) {
        e.preventDefault();
        $('#detailed-permissions-panel').slideToggle(300);
        
        // Buton metnini değiştir
        if ($(this).find('i').hasClass('fa-cogs')) {
            $(this).html('<i class="fas fa-times me-1"></i> Detayları Gizle');
        } else {
            $(this).html('<i class="fas fa-cogs me-1"></i> Detaylı Yetkilendirme');
        }
    });
    
    // Rol seçimi
    $(document).on('change', '.role-radio', function() {
        const selectedRole = $(this).val();
        
        // Tüm rol kartlarının active sınıfını kaldır
        $('.user-role-card').removeClass('active');
        
        // Seçilen rol kartına active sınıfı ekle
        $(this).closest('.user-role-card').addClass('active');
        
        // Tüm rol info bölümlerini gizle
        $('.role-info').hide();
        
        // Modül izinleri bölümünü göster/gizle
        $('#editorPermissionsSection').toggle(selectedRole === 'editor');
        
        // Seçilen rol için info bölümünü göster
        if (selectedRole === 'root') {
            $('#rootInfoSection').show();
        } else if (selectedRole === 'admin') {
            $('#adminInfoSection').show();
        } else if (selectedRole === 'editor') {
            $('#editorInfoSection').show();
        } else {
            $('#userInfoSection').show();
        }
        
        // Eğer rol editör değilse tüm modül izinlerini sıfırla
        if (selectedRole !== 'editor') {
            window.Livewire.find(document.querySelector('[wire\\:id]').getAttribute('wire:id')).clearAllModulePermissions();
        }
    });
    
    // Modül toggle değişikliği - doğrudan PHP metodu çağırma ve UI güncelleme
    $(document).on('change', '.module-toggle', function() {
        const moduleName = $(this).data('module');
        const isChecked = $(this).prop('checked');
        
        console.log('Module toggle changed:', moduleName, isChecked);
        
        // Direkt olarak bileşendeki metodu çağır
        const component = window.Livewire.find(document.querySelector('[wire\\:id]').getAttribute('wire:id'));
        component.setModuleName(moduleName);
        
        // Manuel UI güncelleme (toggle durumunu korumak için)
        setTimeout(function() {
            updateModuleUI(component, moduleName);
        }, 100);
    });
    
    // CRUD izinleri değişikliği - doğrudan PHP metodu çağırma ve UI güncelleme
    $(document).on('change', '.module-crud', function() {
        const moduleName = $(this).data('module');
        const permType = $(this).data('type');
        const isChecked = $(this).prop('checked');
        
        console.log('CRUD permission changed:', moduleName, permType, isChecked);
        
        // Direkt olarak bileşendeki metodu çağır
        const component = window.Livewire.find(document.querySelector('[wire\\:id]').getAttribute('wire:id'));
        component.setModulePermType(moduleName, permType);
        
        // Manuel UI güncelleme
        setTimeout(function() {
            updateModuleUI(component, moduleName);
        }, 100);
    });
    
    // UI Güncelleme Fonksiyonu
    function updateModuleUI(component, moduleName) {
        if (!component || !component.modulePermissions || !component.modulePermissions[moduleName]) {
            console.log('No module data found for:', moduleName);
            return;
        }
        
        const moduleData = component.modulePermissions[moduleName];
        console.log('Updating UI for module:', moduleName, moduleData);
        
        // Toggle checkbox'ı güncelle
        $(`.module-toggle[data-module="${moduleName}"]`).prop('checked', moduleData.enabled);
        
        // Avatar rengini güncelle
        $(`.avatar[data-module="${moduleName}"]`)
            .toggleClass('bg-primary', moduleData.enabled)
            .toggleClass('bg-secondary-lt', !moduleData.enabled);
        
        // CRUD checkbox'ları güncelle
        $(`.module-crud[data-module="${moduleName}"][data-type="view"]`)
            .prop('checked', moduleData.view)
            .prop('disabled', !moduleData.enabled);
            
        $(`.module-crud[data-module="${moduleName}"][data-type="create"]`)
            .prop('checked', moduleData.create)
            .prop('disabled', !moduleData.enabled);
            
        $(`.module-crud[data-module="${moduleName}"][data-type="update"]`)
            .prop('checked', moduleData.update)
            .prop('disabled', !moduleData.enabled);
            
        $(`.module-crud[data-module="${moduleName}"][data-type="delete"]`)
            .prop('checked', moduleData.delete)
            .prop('disabled', !moduleData.enabled);
    }
    
    // Livewire'dan gelen olayları dinle
    Livewire.on('roleChanged', function(role) {
        console.log('roleChanged event received:', role);
        $(`#role-${role}`).prop('checked', true).trigger('change');
    });
});
</script>
@endpush
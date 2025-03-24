@include('usermanagement::helper')
<div>
    @include('admin.partials.error_message')
    <form wire:submit.prevent="save">
        <div class="row">
            <!-- Sol Kolon: Kullanıcı Bilgileri -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-user me-2 text-primary"></i>
                            Kullanıcı Bilgileri
                        </h3>
                    </div>
                    <div class="card-body">
                        <!-- Avatar Yükleme -->
                        <div class="text-center mb-4">
                            <div class="mb-3 position-relative d-inline-block">
                                @if($avatar || ($model && $model->getFirstMedia('avatar')))
                                    <span class="avatar avatar-xl rounded-circle bg-primary-lt d-flex align-items-center justify-content-center" style="width: 120px; height: 120px; border: 2px solid #3498db;">
                                        @if($avatar)
                                            <img src="{{ $avatar->temporaryUrl() }}" alt="Profil Resmi" class="w-100 h-100 object-fit-cover rounded-circle">
                                        @elseif($model && $model->getFirstMedia('avatar'))
                                            <img src="{{ $model->getFirstMediaUrl('avatar') }}" alt="Profil Resmi" class="w-100 h-100 object-fit-cover rounded-circle">
                                        @endif
                                    </span>
                                    <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 rounded-circle" 
                                        wire:click="removeAvatar" title="Fotoğrafı Kaldır">
                                        <i class="fas fa-times"></i>
                                    </button>
                                @else
                                    <span class="avatar avatar-xl rounded-circle bg-primary-lt d-flex align-items-center justify-content-center" style="width: 120px; height: 120px; font-size: 40px; border: 2px dashed #3498db;">
                                        <i class="fas fa-user"></i>
                                    </span>
                                @endif
                                
                                <label for="avatar-upload" class="btn btn-sm btn-primary position-absolute bottom-0 end-0 rounded-circle">
                                    <i class="fas fa-camera"></i>
                                </label>
                                <input id="avatar-upload" type="file" wire:model="avatar" class="d-none" accept="image/jpeg,image/png,image/webp">
                            </div>
                            
                            @error('avatar')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                            
                            <div wire:loading wire:target="avatar" class="text-center mt-2">
                                <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                                <span class="ms-2 text-muted">Yükleniyor...</span>
                            </div>
                        </div>

                        <!-- Temel Bilgiler -->
                        <div class="mb-3">
                            <label class="form-label">İsim Soyisim</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-user text-primary"></i>
                                </span>
                                <input type="text" wire:model.defer="inputs.name"
                                    class="form-control @error('inputs.name') is-invalid @enderror"
                                    placeholder="İsim Soyisim">
                            </div>
                            @error('inputs.name')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">E-posta Adresi</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-envelope text-primary"></i>
                                </span>
                                <input type="email" wire:model.defer="inputs.email"
                                    class="form-control @error('inputs.email') is-invalid @enderror"
                                    placeholder="ornek@email.com">
                            </div>
                            @error('inputs.email')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Şifre {{ $userId ? '(Değiştirmek için doldurun)' : '' }}</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-lock text-primary"></i>
                                </span>
                                <input type="password" wire:model.defer="inputs.password"
                                    class="form-control @error('inputs.password') is-invalid @enderror" 
                                    placeholder="{{ $userId ? 'Şifreyi değiştir' : 'Şifre belirle' }}">
                            </div>
                            @error('inputs.password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-check form-switch mt-4">
                            <input class="form-check-input" type="checkbox" wire:model.defer="inputs.is_active" id="activeSwitch">
                            <label class="form-check-label" for="activeSwitch">
                                <i class="fas fa-toggle-on me-1 {{ $inputs['is_active'] ? 'text-success' : 'text-muted' }}"></i>
                                Kullanıcı Aktif
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Sağ Kolon: Roller ve İzinler -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">
                            <i class="fas fa-user-shield me-2 text-primary"></i>
                            Roller ve İzinler
                        </h3>
                        <div class="card-actions">
                            <div class="btn-list">
                                <a href="{{ route('admin.usermanagement.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-1"></i> Geri Dön
                                </a>
                                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                                    <span wire:loading.remove wire:target="save">
                                        <i class="fas fa-save me-1"></i> Kaydet
                                    </span>
                                    <span wire:loading wire:target="save">
                                        <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                                        Kaydediliyor...
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Rol Seçimi -->
                        <div class="mb-4">
                            <label class="form-label">
                                <i class="fas fa-user-tag me-1 text-primary"></i>
                                Kullanıcı Rolü
                            </label>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($allRoles as $role)
                                <label class="form-selectgroup-item">
                                    <input type="radio" name="role" value="{{ $role->name }}"
                                        class="form-selectgroup-input" wire:model="inputs.role_id">
                                    <span class="form-selectgroup-label d-flex align-items-center p-3">
                                        <span class="me-3 bg-{{ $role->name == 'root' ? 'danger' : ($role->name == 'admin' ? 'primary' : 'info') }} text-white avatar">
                                            <i class="fas fa-{{ $role->name == 'root' ? 'crown' : ($role->name == 'admin' ? 'user-cog' : 'user-edit') }}"></i>
                                        </span>
                                        <div>
                                            <strong>{{ $role->name }}</strong>
                                            <div class="text-muted mt-1">{{ $role->description ?? 'Kullanıcı rolü' }}</div>
                                        </div>
                                    </span>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Editör rolü seçildiyse - Modül İzinleri -->
                        @if($showModulePermissions)
                        <div
                            x-data 
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 transform scale-95"
                            x-transition:enter-end="opacity-100 transform scale-100"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100 transform scale-100"
                            x-transition:leave-end="opacity-0 transform scale-95">
                            
                            <div class="hr-text hr-text-left mb-4">
                                <span class="bg-primary text-white px-2 py-1 rounded">
                                    <i class="fas fa-puzzle-piece me-1"></i>
                                    Modül İzinleri
                                </span>
                            </div>
                            
                            <div class="mb-3">
                                <div class="alert alert-info d-flex">
                                    <div>
                                        <i class="fas fa-info-circle fa-lg me-2"></i>
                                    </div>
                                    <div>
                                        <strong>Modül İzinleri Hakkında</strong>
                                        <p class="mb-0">Kullanıcının erişebileceği modülleri ve bu modüllerdeki yetkilerini belirleyin. Her modül için ayrı ayrı CRUD (Görüntüleme, Oluşturma, Düzenleme, Silme) izinleri atayabilirsiniz.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row g-3">
                                @foreach($availableModules as $module)
                                <div class="col-lg-4 col-md-6">
                                    <div class="card">
                                        <div class="card-status-top bg-primary"></div>
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-3">
                                                <span class="avatar avatar-sm bg-primary-lt me-2">
                                                    <i class="fas fa-puzzle-piece"></i>
                                                </span>
                                                <div class="me-auto">
                                                    <h4 class="card-title m-0">{{ $module->display_name }}</h4>
                                                    <div class="text-muted small">{{ $module->name }}</div>
                                                </div>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox"
                                                        wire:model="modulePermissions.{{ $module->name }}.enabled"
                                                        wire:click="toggleModulePermission('{{ $module->name }}')">
                                                </div>
                                            </div>
                                            
                                            <div x-data="{ expanded: {{ isset($moduleDetails[$module->name]) && $moduleDetails[$module->name] ? 'true' : 'false' }} }">
                                                <!-- İzin detayları butonu -->
                                                <button type="button" @click="expanded = !expanded" 
                                                    class="btn btn-sm w-100 {{ isset($modulePermissions[$module->name]['enabled']) && $modulePermissions[$module->name]['enabled'] ? 'btn-outline-primary' : 'btn-outline-secondary' }}">
                                                    <i class="fas fa-cog me-1" :class="{ 'fa-spin': expanded }"></i>
                                                    <span x-text="expanded ? 'Detayları Gizle' : 'CRUD İzinleri'"></span>
                                                    <i class="fas fa-chevron-down ms-1" :class="{'fa-chevron-up': expanded}"></i>
                                                </button>

                                                <!-- CRUD İzinleri -->
                                                <div x-show="expanded" x-collapse class="mt-3">
                                                    <div class="list-group list-group-flush">
                                                        <!-- Görüntüleme İzni -->
                                                        <div class="list-group-item px-0 py-2 d-flex align-items-center">
                                                            <div class="flex-grow-1">
                                                                <div class="d-flex align-items-center">
                                                                    <span class="avatar avatar-xs bg-info-lt me-2">
                                                                        <i class="fas fa-eye"></i>
                                                                    </span>
                                                                    <div>Görüntüleme</div>
                                                                </div>
                                                            </div>
                                                            <div class="form-check form-switch">
                                                                <input class="form-check-input" type="checkbox"
                                                                    wire:model="modulePermissions.{{ $module->name }}.view"
                                                                    wire:click="toggleModulePermission('{{ $module->name }}', 'view')">
                                                            </div>
                                                        </div>
                                                        
                                                        <!-- Oluşturma İzni -->
                                                        <div class="list-group-item px-0 py-2 d-flex align-items-center">
                                                            <div class="flex-grow-1">
                                                                <div class="d-flex align-items-center">
                                                                    <span class="avatar avatar-xs bg-success-lt me-2">
                                                                        <i class="fas fa-plus"></i>
                                                                    </span>
                                                                    <div>Oluşturma</div>
                                                                </div>
                                                            </div>
                                                            <div class="form-check form-switch">
                                                                <input class="form-check-input" type="checkbox"
                                                                    wire:model="modulePermissions.{{ $module->name }}.create"
                                                                    wire:click="toggleModulePermission('{{ $module->name }}', 'create')">
                                                            </div>
                                                        </div>
                                                        
                                                        <!-- Düzenleme İzni -->
                                                        <div class="list-group-item px-0 py-2 d-flex align-items-center">
                                                            <div class="flex-grow-1">
                                                                <div class="d-flex align-items-center">
                                                                    <span class="avatar avatar-xs bg-warning-lt me-2">
                                                                        <i class="fas fa-edit"></i>
                                                                    </span>
                                                                    <div>Düzenleme</div>
                                                                </div>
                                                            </div>
                                                            <div class="form-check form-switch">
                                                                <input class="form-check-input" type="checkbox"
                                                                    wire:model="modulePermissions.{{ $module->name }}.update"
                                                                    wire:click="toggleModulePermission('{{ $module->name }}', 'update')">
                                                            </div>
                                                        </div>
                                                        
                                                        <!-- Silme İzni -->
                                                        <div class="list-group-item px-0 py-2 d-flex align-items-center">
                                                            <div class="flex-grow-1">
                                                                <div class="d-flex align-items-center">
                                                                    <span class="avatar avatar-xs bg-danger-lt me-2">
                                                                        <i class="fas fa-trash"></i>
                                                                    </span>
                                                                    <div>Silme</div>
                                                                </div>
                                                            </div>
                                                            <div class="form-check form-switch">
                                                                <input class="form-check-input" type="checkbox"
                                                                    wire:model="modulePermissions.{{ $module->name }}.delete"
                                                                    wire:click="toggleModulePermission('{{ $module->name }}', 'delete')">
                                                            </div>
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
                        @else
                        <!-- Root ve Admin rolleri için özel izinler -->
                        @if(isset($inputs['role_id']) && $inputs['role_id'] === 'root')
                            <div class="alert alert-info d-flex">
                                <div>
                                    <i class="fas fa-info-circle fa-lg me-2"></i>
                                </div>
                                <div>
                                    <strong>Root Rolü Tam Yetkili</strong>
                                    <p class="mb-0">Root rolüne sahip kullanıcılar sistemdeki tüm modüllere ve fonksiyonlara otomatik olarak tam erişime sahiptir. Bu rol için manuel yetki ataması yapılmasına gerek yoktur.</p>
                                </div>
                            </div>
                        @elseif(isset($inputs['role_id']) && $inputs['role_id'] === 'admin')
                            <div class="alert alert-primary d-flex">
                                <div>
                                    <i class="fas fa-shield-alt fa-lg me-2"></i>
                                </div>
                                <div>
                                    <strong>Admin Rolü Yetkisi</strong>
                                    <p class="mb-0">Admin rolüne sahip kullanıcılar sistemi yönetmek için gerekli tüm temel izinlere sahiptir. Özel izin atamalarıyla yetkileri sınırlandırabilirsiniz.</p>
                                </div>
                            </div>
                            
                            <!-- Admin için İzinler -->
                            <div class="row g-3">
                                @foreach($groupedPermissions as $module => $permissions)
                                    <div class="col-lg-4 col-md-6">
                                        <div class="card">
                                            <div class="card-status-top bg-primary"></div>
                                            <div class="card-body">
                                                <div class="d-flex align-items-center mb-3">
                                                    <span class="avatar avatar-sm bg-primary-lt me-2">
                                                        <i class="fas fa-folder"></i>
                                                    </span>
                                                    <div class="me-auto">
                                                        <h4 class="card-title text-capitalize m-0">{{ $moduleLabels[$module] ?? ucfirst($module) }}</h4>
                                                    </div>
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox"
                                                            wire:click="toggleModulePermissions('{{ $module }}')"
                                                            @if(isset($inputs['permissions']) && count(array_intersect($permissions->pluck('id')->toArray(), $inputs['permissions'])) === $permissions->count()) checked @endif>
                                                    </div>
                                                </div>
                                                
                                                <div x-data="{ expanded: false }">
                                                    <!-- İzin detayları butonu -->
                                                    <button type="button" @click="expanded = !expanded" 
                                                        class="btn btn-sm w-100 btn-outline-primary">
                                                        <i class="fas fa-list-check me-1"></i>
                                                        <span x-text="expanded ? 'İzinleri Gizle' : 'İzinleri Göster'"></span>
                                                        <i class="fas fa-chevron-down ms-1" :class="{'fa-chevron-up': expanded}"></i>
                                                    </button>
                                                    
                                                    <!-- İzin listesi -->
                                                    <div x-show="expanded" x-collapse class="mt-3">
                                                        <div class="list-group list-group-flush">
                                                            @foreach($permissions as $permission)
                                                                <label class="list-group-item px-0 py-2 d-flex align-items-center">
                                                                    <div class="flex-grow-1">
                                                                        <div class="d-flex align-items-center">
                                                                            <span class="avatar avatar-xs bg-info-lt me-2">
                                                                                <i class="fas fa-key"></i>
                                                                            </span>
                                                                            <div>{{ $permissionLabels[explode('.', $permission->name)[1]] ?? ucfirst(explode('.', $permission->name)[1] ?? $permission->name) }}</div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input type="checkbox" class="form-check-input"
                                                                            value="{{ $permission->id }}"
                                                                            wire:model="inputs.permissions">
                                                                    </div>
                                                                </label>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <!-- Rol seçilmemişse veya diğer roller için bilgi mesajı -->
                            <div class="alert alert-warning d-flex">
                                <div>
                                    <i class="fas fa-exclamation-triangle fa-lg me-2"></i>
                                </div>
                                <div>
                                    <strong>Rol Seçilmedi</strong>
                                    <p class="mb-0">Lütfen kullanıcıya atamak istediğiniz rolü seçin. Rol seçimine göre kullanıcının izinleri otomatik olarak ayarlanacaktır.</p>
                                </div>
                            </div>
                        @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    document.addEventListener('livewire:initialized', () => {
        // Alpine.js'in düzgün çalışması için yeniden başlatma
        Livewire.hook('element.updated', () => {
            Alpine.initTree(document.body);
        });
    });
</script>
@endpush
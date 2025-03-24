@include('usermanagement::helper')
<div>
    @include('admin.partials.error_message')
    <form wire:submit.prevent="save">
        <div class="row g-3">
            <!-- Sol Kolon - Kişisel Bilgiler -->
            <div class="col-md-4">
                <div class="card card-stacked">
                    <div class="card-body">
                        <h3 class="card-title">
                            <i class="fas fa-user-circle text-primary me-2"></i>Kullanıcı Bilgileri
                        </h3>
                        <div class="text-center mb-4">
                            @if(isset($temporaryImages['avatar']))
                                <div class="position-relative d-inline-block mb-3">
                                    <img src="{{ $temporaryImages['avatar']->temporaryUrl() }}" class="avatar avatar-xl rounded-circle" alt="Kullanıcı Avatarı">
                                    <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 rounded-circle p-1" 
                                            wire:click="removeImage('avatar')" title="Fotoğrafı Kaldır">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            @elseif($model && $model->getFirstMedia('avatar'))
                                <div class="position-relative d-inline-block mb-3">
                                    <img src="{{ $model->getFirstMediaUrl('avatar') }}" class="avatar avatar-xl rounded-circle" alt="Kullanıcı Avatarı">
                                    <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 rounded-circle p-1" 
                                            wire:click="removeImage('avatar')" title="Fotoğrafı Kaldır">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            @else
                                <div class="avatar avatar-xl rounded-circle bg-blue-lt mb-3">
                                    {{ strtoupper(substr($inputs['name'] ?? 'U', 0, 2)) }}
                                </div>
                            @endif
                            
                            <div class="mb-3">
                                <label class="btn btn-outline-primary btn-sm" for="avatar-upload">
                                    <i class="fas fa-camera me-1"></i> Profil Fotoğrafı Yükle
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
                        
                        <div class="mb-3">
                            <label class="form-label required">Kullanıcı Adı</label>
                            <div class="input-icon">
                                <span class="input-icon-addon">
                                    <i class="fas fa-user"></i>
                                </span>
                                <input type="text" wire:model.defer="inputs.name" class="form-control @error('inputs.name') is-invalid @enderror" placeholder="İsim Soyisim">
                            </div>
                            @error('inputs.name')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label required">E-posta Adresi</label>
                            <div class="input-icon">
                                <span class="input-icon-addon">
                                    <i class="fas fa-envelope"></i>
                                </span>
                                <input type="email" wire:model.defer="inputs.email" class="form-control @error('inputs.email') is-invalid @enderror" placeholder="ornek@mail.com">
                            </div>
                            @error('inputs.email')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label {{ !$userId ? 'required' : '' }}">Şifre {{ $userId ? '(Değiştirmek için doldurun)' : '' }}</label>
                            <div class="input-icon">
                                <span class="input-icon-addon">
                                    <i class="fas fa-lock"></i>
                                </span>
                                <input type="password" wire:model.defer="inputs.password" class="form-control @error('inputs.password') is-invalid @enderror" placeholder="********">
                            </div>
                            @error('inputs.password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mt-4 mb-3">
                            <div class="mb-2">Kullanıcı Durumu</div>
                            <div class="pretty p-default p-curve p-toggle p-smooth ms-1">
                                <input type="checkbox" id="is_active" name="is_active" wire:model="inputs.is_active"
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
            
            <!-- Sağ Kolon - Rol ve İzinler -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title mb-4">
                            <i class="fas fa-user-shield text-primary me-2"></i>Kullanıcı Rolleri
                        </h3>
                        
                        <div class="row row-cards">
                            @foreach($allRoles as $role)
                            <div class="col-md-4">
                                <label class="form-selectgroup-item" wire:key="role-{{ $role->id }}">
                                    <input type="radio" name="role" wire:model="inputs.role_id" 
                                           value="{{ $role->name }}" class="form-selectgroup-input">
                                    <span class="form-selectgroup-label d-flex p-3 {{ $inputs['role_id'] === $role->name ? 'bg-primary-subtle border-primary' : '' }}">
                                        <span class="avatar me-3 {{ $inputs['role_id'] === $role->name ? 'bg-primary text-white' : 'bg-muted' }}">
                                            <i class="fas fa-{{ $role->name == 'root' ? 'crown' : ($role->name == 'admin' ? 'user-cog' : 'user-edit') }}"></i>
                                        </span>
                                        <span class="form-selectgroup-label-content">
                                            <span class="form-selectgroup-label-title">{{ ucfirst($role->name) }}</span>
                                            <span class="d-block form-selectgroup-label-subtitle text-muted">
                                                {{ $role->name == 'root' ? 'Tam Yetkili Yönetici' : ($role->name == 'admin' ? 'Site Yöneticisi' : 'İçerik Editörü') }}
                                            </span>
                                        </span>
                                    </span>
                                </label>
                            </div>
                            @endforeach
                        </div>
                        
                        <!-- Editör Modül İzinleri -->
                        @if($inputs['role_id'] === 'editor')
                        <div class="mt-4" wire:key="editor-permissions">
                            <h3 class="card-title mb-3">
                                <i class="fas fa-puzzle-piece text-success me-2"></i>Site Modülleri
                                <span class="badge bg-success-lt ms-2">Editör İzinleri</span>
                            </h3>
                            
                            <div class="alert alert-info mb-3">
                                <div class="d-flex">
                                    <div><i class="fas fa-info-circle fa-lg me-2"></i></div>
                                    <div>
                                        <strong>Site Modülleri Hakkında</strong>
                                        <p class="mb-0">Editör kullanıcısının erişebileceği modülleri ve izinleri aşağıdan belirleyebilirsiniz. Aktif olan modüller kullanıcının panelde göreceği menüleri belirler.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row g-3">
                                @foreach($availableModules as $module)
                                <div class="col-md-6" wire:key="module-{{ $module->module_id }}">
                                    <div class="card {{ $modulePermissions[$module->name]['enabled'] ? 'border-success shadow' : 'shadow-sm' }}">
                                        <div class="card-header">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar {{ $modulePermissions[$module->name]['enabled'] ? 'bg-success text-white' : 'bg-muted' }} me-3">
                                                    <i class="fas fa-puzzle-piece"></i>
                                                </div>
                                                <div>
                                                    <h4 class="m-0">{{ $module->display_name }}</h4>
                                                    <div class="text-muted small">{{ $module->name }}</div>
                                                </div>
                                                <div class="ms-auto">
                                                    <label class="form-check form-switch d-inline-block m-0">
                                                        <input class="form-check-input" type="checkbox"
                                                            wire:model="modulePermissions.{{ $module->name }}.enabled"
                                                            wire:click="toggleModuleAll('{{ $module->name }}')">
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="card-body p-0">
                                            <div class="table-responsive">
                                                <table class="table table-vcenter card-table">
                                                    <tbody>
                                                        <tr>
                                                            <td>
                                                                <div class="d-flex align-items-center">
                                                                    <span class="avatar avatar-xs bg-blue-lt me-2">
                                                                        <i class="fas fa-eye"></i>
                                                                    </span>
                                                                    <span>Görüntüleme</span>
                                                                </div>
                                                            </td>
                                                            <td class="text-end">
                                                                <label class="form-check form-switch d-inline-block m-0">
                                                                    <input class="form-check-input" type="checkbox"
                                                                        wire:model="modulePermissions.{{ $module->name }}.view"
                                                                        wire:click="togglePermission('{{ $module->name }}', 'view')"
                                                                        {{ !$modulePermissions[$module->name]['enabled'] ? 'disabled' : '' }}>
                                                                </label>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <div class="d-flex align-items-center">
                                                                    <span class="avatar avatar-xs bg-green-lt me-2">
                                                                        <i class="fas fa-plus"></i>
                                                                    </span>
                                                                    <span>Oluşturma</span>
                                                                </div>
                                                            </td>
                                                            <td class="text-end">
                                                                <label class="form-check form-switch d-inline-block m-0">
                                                                    <input class="form-check-input" type="checkbox"
                                                                        wire:model="modulePermissions.{{ $module->name }}.create"
                                                                        wire:click="togglePermission('{{ $module->name }}', 'create')"
                                                                        {{ !$modulePermissions[$module->name]['enabled'] ? 'disabled' : '' }}>
                                                                </label>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <div class="d-flex align-items-center">
                                                                    <span class="avatar avatar-xs bg-yellow-lt me-2">
                                                                        <i class="fas fa-edit"></i>
                                                                    </span>
                                                                    <span>Düzenleme</span>
                                                                </div>
                                                            </td>
                                                            <td class="text-end">
                                                                <label class="form-check form-switch d-inline-block m-0">
                                                                    <input class="form-check-input" type="checkbox"
                                                                        wire:model="modulePermissions.{{ $module->name }}.update"
                                                                        wire:click="togglePermission('{{ $module->name }}', 'update')"
                                                                        {{ !$modulePermissions[$module->name]['enabled'] ? 'disabled' : '' }}>
                                                                </label>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <div class="d-flex align-items-center">
                                                                    <span class="avatar avatar-xs bg-red-lt me-2">
                                                                        <i class="fas fa-trash"></i>
                                                                    </span>
                                                                    <span>Silme</span>
                                                                </div>
                                                            </td>
                                                            <td class="text-end">
                                                                <label class="form-check form-switch d-inline-block m-0">
                                                                    <input class="form-check-input" type="checkbox"
                                                                        wire:model="modulePermissions.{{ $module->name }}.delete"
                                                                        wire:click="togglePermission('{{ $module->name }}', 'delete')"
                                                                        {{ !$modulePermissions[$module->name]['enabled'] ? 'disabled' : '' }}>
                                                                </label>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                        
                        <!-- Admin Yetkiler -->
                        @if($inputs['role_id'] === 'admin')
                        <div class="mt-4" wire:key="admin-permissions">
                            <h3 class="card-title mb-3">
                                <i class="fas fa-shield-alt text-primary me-2"></i>Özel İzinler
                                <span class="badge bg-primary-lt ms-2">Admin İzinleri</span>
                            </h3>
                            
                            <div class="alert alert-primary mb-3">
                                <div class="d-flex">
                                    <div><i class="fas fa-info-circle fa-lg me-2"></i></div>
                                    <div>
                                        <strong>Admin İzinleri Hakkında</strong>
                                        <p class="mb-0">Admin kullanıcısı varsayılan olarak tüm izinlere sahiptir. Ancak belirli izinleri kısıtlamak isterseniz aşağıdan düzenleyebilirsiniz.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="accordion" id="permissionAccordion">
                                @foreach($groupedPermissions as $module => $permissions)
                                <div class="accordion-item" wire:key="perm-group-{{ $module }}">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                                                data-bs-target="#collapse-{{ $module }}" aria-expanded="false">
                                            <div class="d-flex align-items-center w-100">
                                                <span class="avatar bg-primary-lt me-2">
                                                    <i class="fas fa-folder"></i>
                                                </span>
                                                <span class="me-auto">{{ ucfirst($module) }}</span>
                                                <span class="badge {{ count(array_intersect($permissions->pluck('id')->toArray(), $inputs['permissions'])) === $permissions->count() ? 'bg-success' : 'bg-muted' }} me-2">
                                                    {{ count(array_intersect($permissions->pluck('id')->toArray(), $inputs['permissions'])) }}/{{ count($permissions) }}
                                                </span>
                                                <label class="form-check form-switch m-0 pe-2">
                                                    <input class="form-check-input" type="checkbox"
                                                        {{ count(array_intersect($permissions->pluck('id')->toArray(), $inputs['permissions'])) === $permissions->count() ? 'checked' : '' }}
                                                        wire:click="toggleAllModulePermissions('{{ $module }}')">
                                                </label>
                                            </div>
                                        </button>
                                    </h2>
                                    <div id="collapse-{{ $module }}" class="accordion-collapse collapse" data-bs-parent="#permissionAccordion">
                                        <div class="accordion-body pt-0">
                                            <div class="list-group list-group-flush">
                                                @foreach($permissions as $permission)
                                                <div class="list-group-item d-flex align-items-center py-2 px-0" wire:key="perm-{{ $permission->id }}">
                                                    <div class="d-flex align-items-center flex-grow-1">
                                                        <span class="avatar avatar-xs bg-blue-lt me-2">
                                                            <i class="fas fa-key"></i>
                                                        </span>
                                                        <span>{{ ucfirst(str_replace(["{$module}.", '_'], ['', ' '], $permission->name)) }}</span>
                                                    </div>
                                                    <div>
                                                        <label class="form-check form-switch m-0">
                                                            <input class="form-check-input" type="checkbox"
                                                                wire:model="inputs.permissions" value="{{ $permission->id }}">
                                                        </label>
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                        
                        <!-- Root Uyarısı -->
                        @if($inputs['role_id'] === 'root')
                        <div class="mt-4" wire:key="root-info">
                            <div class="alert alert-danger">
                                <div class="d-flex">
                                    <div><i class="fas fa-exclamation-triangle fa-lg me-2"></i></div>
                                    <div>
                                        <h4 class="alert-title">Root Kullanıcı Yetkisi</h4>
                                        <p class="mb-0">Root kullanıcısı sistem üzerindeki <strong>tüm özelliklere erişim</strong> hakkına sahiptir. Bu rol sadece tam yetkili sistem yöneticilerine verilmelidir.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                    
                    <div class="card-footer text-end">
                        <div class="d-flex">
                            <a href="{{ route('admin.usermanagement.index') }}" class="btn">
                                <i class="fas fa-times me-1"></i>İptal
                            </a>
                            <button type="submit" class="btn btn-primary ms-auto">
                                <i class="fas fa-save me-1"></i>
                                <span wire:loading.remove wire:target="save">{{ $userId ? 'Güncelle' : 'Kaydet' }}</span>
                                <span wire:loading wire:target="save">
                                    <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                                    Kaydediliyor...
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
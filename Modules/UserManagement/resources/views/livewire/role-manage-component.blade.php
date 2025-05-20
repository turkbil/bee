@include('usermanagement::helper')
<div>
    <form wire:submit.prevent="save">
        <div class="row">
            <!-- Sol Kolon - Temel Ayarlar -->
            <div class="col-md-5">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-users me-2 text-blue"></i>
                            Temel Rol Ayarları
                        </h3>
                        @if($roleId)
                        <div class="card-actions">
                            <span class="badge {{ $role->isBaseRole() ? 'bg-red' : 'bg-green' }}">
                                <i class="fas {{ $role->isBaseRole() ? 'fa-lock' : 'fa-unlock' }} me-1"></i>
                                {{ $role->isBaseRole() ? 'Korumalı Rol' : 'Düzenlenebilir' }}
                            </span>
                        </div>
                        @endif
                    </div>
                    <div class="card-body">
                        <!-- Rol Adı -->
                        <div class="mb-4">
                            <label class="form-label">
                                <i class="fas fa-tag me-1 text-blue"></i>
                                Rol Adı
                            </label>
                            <input type="text" wire:model.defer="inputs.name"
                               class="form-control @error('inputs.name') is-invalid @enderror"
                               placeholder="Örn: Admin, Editor"
                               {{ $roleId && $role->isBaseRole() ? 'disabled' : '' }}>
                           @error('inputs.name')
                           <div class="invalid-feedback">{{ $message }}</div>
                           @enderror
                           <small class="text-muted">Rol için benzersiz bir ad belirtin</small>
                       </div>

                       <!-- Guard Name -->
                       <div class="mb-4">
                           <label class="form-label">
                               <i class="fas fa-lock me-1 text-blue"></i>
                               Guard Name
                           </label>
                           <select wire:model.defer="inputs.guard_name"
                               class="form-select @error('inputs.guard_name') is-invalid @enderror"
                               {{ $roleId && $role->isBaseRole() ? 'disabled' : '' }}>
                               <option value="admin" selected>Admin Guard</option>
                               <option value="web">Web Guard</option>
                               <option value="api">API Guard</option>
                           </select>
                           @error('inputs.guard_name')
                           <div class="invalid-feedback">{{ $message }}</div>
                           @enderror
                           <small class="text-muted">Rolün hangi alanda kullanılacağını seçin</small>
                       </div>

                       <!-- Koruma Durumu -->
                       @if(!$roleId || !$role->isBaseRole())
                       <div class="mb-4">
                           <label class="form-check form-switch">
                               <input class="form-check-input" type="checkbox" 
                                   wire:model.defer="inputs.is_protected"
                                   {{ $roleId && $role->isBaseRole() ? 'disabled' : '' }}>
                               <span class="form-check-label">
                                   <i class="fas fa-shield-alt me-1 text-blue"></i>
                                   Rolü Koru
                               </span>
                           </label>
                           <small class="text-muted d-block">
                               Korunan roller düzenlenemez ve silinemez
                           </small>
                       </div>
                       @endif

                       <!-- Rol İstatistikleri -->
                       @if($roleId)
                       <div class="mt-4">
                           <div class="row row-cards">
                               <div class="col-6">
                                   <div class="card card-sm">
                                       <div class="card-body">
                                           <div class="row align-items-center">
                                               <div class="col-auto">
                                                   <span class="bg-blue text-white avatar">
                                                       <i class="fas fa-users"></i>
                                                   </span>
                                               </div>
                                               <div class="col">
                                                   <div class="font-weight-medium">
                                                       {{ $role->users_count }} Kullanıcı
                                                   </div>
                                                   <div class="text-muted">
                                                       Bu role sahip
                                                   </div>
                                               </div>
                                           </div>
                                       </div>
                                   </div>
                               </div>
                               <div class="col-6">
                                   <div class="card card-sm">
                                       <div class="card-body">
                                           <div class="row align-items-center">
                                               <div class="col-auto">
                                                   <span class="bg-green text-white avatar">
                                                       <i class="fas fa-key"></i>
                                                   </span>
                                               </div>
                                               <div class="col">
                                                   <div class="font-weight-medium">
                                                       {{ $role->permissions_count }} Yetki
                                                   </div>
                                                   <div class="text-muted">
                                                       Atanmış durumda
                                                   </div>
                                               </div>
                                           </div>
                                       </div>
                                   </div>
                               </div>
                           </div>
                       </div>
                       @endif
                   </div>
               </div>
           </div>

           <!-- Sağ Kolon - Yetkiler -->
           <div class="col-md-7">
               <div class="card">
                   <div class="card-header">
                       <h3 class="card-title">
                           <i class="fas fa-key me-2 text-blue"></i>
                           Rol Yetkileri
                       </h3>
                       <div class="card-actions">
                           <div class="row g-2 align-items-center">
                               <div class="col">
                                   <input type="text" wire:model.live.debounce.300ms="permissionSearch"
                                       class="form-control form-control-sm" 
                                       placeholder="Yetkilerde ara...">
                               </div>
                           </div>
                       </div>
                   </div>
                   <div class="card-body">
                        @if($roleId && $role && $role->isRoot())
                            <div class="alert alert-info">
                                <div class="d-flex">
                                    <div>
                                        <i class="fas fa-info-circle fa-2x me-3 text-info"></i>
                                    </div>
                                    <div>
                                        <h4>Root Rolü Tam Yetkili</h4>
                                        <p>Root rolü sistemdeki tüm modüllere ve fonksiyonlara otomatik olarak tam erişime sahiptir. Bu rol için manuel yetki ataması yapılmasına gerek yoktur.</p>
                                    </div>
                                </div>
                            </div>
                        @else
                           <div class="row g-3">
                               @forelse($groupedPermissions as $group => $items)
                               <div class="col-md-6">
                                   <div class="card">
                                       <div class="card-header">
                                           <h4 class="card-title mb-0">
                                               <div class="d-flex align-items-center">
                                                   <i class="fas fa-folder me-2 text-blue"></i>
                                                   {{ Str::title($group) }}
                                                   <span class="badge bg-blue ms-2">{{ count($items) }}</span>
                                               </div>
                                           </h4>
                                           <div class="card-actions">
                                               <label class="form-check form-switch">
                                                   <input type="checkbox" class="form-check-input"
                                                       wire:click="toggleGroupPermissions('{{ $group }}')"
                                                       {{ $this->isGroupSelected($group) ? 'checked' : '' }}
                                                       {{ $roleId && $role->isBaseRole() ? 'disabled' : '' }}>
                                                   <span class="form-check-label">Tümü</span>
                                               </label>
                                           </div>
                                       </div>
                                       <div class="card-body">
                                           @foreach($items as $permission)
                                           <label class="form-check mb-2">
                                               <input type="checkbox" class="form-check-input"
                                                   wire:model.defer="inputs.permissions"
                                                   value="{{ $permission->name }}"
                                                   {{ $roleId && $role->isBaseRole() ? 'disabled' : '' }}>
                                               <span class="form-check-label">
                                                   {{ Str::title(Str::after($permission->name, '.')) }}
                                               </span>
                                           </label>
                                           @endforeach
                                       </div>
                                   </div>
                               </div>
                               @empty
                               <div class="col-12">
                                   <div class="empty">
                                       <div class="empty-icon">
                                           <i class="fas fa-search"></i>
                                       </div>
                                       <p class="empty-title">Yetki bulunamadı</p>
                                       <p class="empty-subtitle text-muted">
                                           Arama kriterine uygun yetki bulunamadı.
                                       </p>
                                   </div>
                               </div>
                               @endforelse
                           </div>
                       @endif
                   </div>
               </div>
           </div>
       </div>

        <!-- Kaydet Butonu -->
        <div class="d-flex justify-content-end mt-4">
            @if($roleId)
            <a href="{{ route('admin.usermanagement.role.index') }}" class="btn btn-link me-2">
                <i class="fas fa-arrow-left me-2"></i>
                Geri Dön
            </a>
            @endif
            
            <button type="submit" class="btn btn-success" 
                wire:loading.attr="disabled"
                {{ $roleId && $role->isBaseRole() ? 'disabled' : '' }}>
                <i class="fas fa-save me-2"></i>
                {{ $roleId ? 'Güncelle' : 'Kaydet' }}
                <span wire:loading wire:target="save">
                    <i class="fas fa-spinner fa-spin ms-2"></i>
                </span>
            </button>
        </div>
   </form>
</div>
@include('usermanagement::helper')
<div>
    @include('admin.partials.error_message')
    <form wire:submit.prevent="save">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">{{ $userId ? 'Kullanıcı Düzenle' : 'Yeni Kullanıcı' }}</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Sol Kolon: Avatar ve Temel Bilgiler -->
                    <div class="col-md-4">
                        <!-- Avatar Upload -->
                        @include('usermanagement::partials.image-upload', [
                        'imageKey' => 'avatar',
                        'label' => 'Görseli sürükleyip bırakın veya tıklayın'
                        ])

                        <div class="form-floating mb-3">
                            <input type="text" wire:model.defer="inputs.name"
                                class="form-control @error('inputs.name') is-invalid @enderror"
                                placeholder="Kullanıcı adı">
                            <label>İsim</label>
                            @error('inputs.name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-floating mb-3">
                            <input type="email" wire:model.defer="inputs.email"
                                class="form-control @error('inputs.email') is-invalid @enderror"
                                placeholder="E-posta adresi">
                            <label>E-posta</label>
                            @error('inputs.email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-floating mb-3">
                            <input type="password" wire:model.defer="inputs.password"
                                class="form-control @error('inputs.password') is-invalid @enderror" placeholder="Şifre">
                            <label>Şifre</label>
                            @error('inputs.password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <label class="form-check form-switch">
                            <input type="checkbox" class="form-check-input" wire:model.defer="inputs.is_active">
                            <span class="form-check-label">Kullanıcı Aktif</span>
                        </label>
                    </div>

                    <!-- Sağ Kolon: Roller ve İzinler -->
                    <div class="col-md-8">
                        <div class="row g-3">
                            <!-- Roller -->
                            <div class="col-12">
                                <label class="form-label">
                                    <span class="badge bg-blue-lt me-2">
                                        <i class="fas fa-users-gear"></i>
                                    </span>
                                    Kullanıcı Rolü
                                </label>
                                <div class="form-selectgroup form-selectgroup-boxes d-flex flex-column">
                                    @foreach($allRoles as $role)
                                    <label class="form-selectgroup-item flex-fill">
                                        <input type="radio" name="role" value="{{ $role->name }}"
                                            class="form-selectgroup-input" wire:model="inputs.role_id">
                                        <div class="form-selectgroup-label d-flex align-items-center p-3">
                                            <div class="me-3">
                                                <span class="form-selectgroup-check"></span>
                                            </div>
                                            <div>
                                                <strong>{{ $role->name }}</strong>
                                                <span class="d-block text-muted">{{ $role->permissions->count() }}
                                                    yetki</span>
                                            </div>
                                        </div>
                                    </label>
                                    @endforeach
                                </div>
                            </div>

                            <!-- İzinler -->
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            <span class="badge bg-purple me-2">
                                                <i class="ti ti-shield-check"></i>
                                            </span>
                                            Özel İzinler
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">

                                            @foreach($groupedPermissions as $module => $permissions)
                                            <div class="col-md-6 col-xl-4">
                                                <div class="card">
                                                    <div class="card-header d-flex align-items-center">
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox"
                                                                wire:click="toggleModulePermissions('{{ $module }}')"
                                                                @if(isset($inputs['permissions']) &&
                                                                count(array_intersect($permissions->pluck('id')->toArray(),
                                                            $inputs['permissions'])) === $permissions->count()) checked
                                                            @endif>
                                                            <span class="form-check-label text-capitalize">
                                                                {{ $moduleLabels[$module] ?? ucfirst($module) }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="card-body p-2">
                                                        @foreach($permissions as $permission)
                                                        <label class="form-check mb-2">
                                                            <input type="checkbox" class="form-check-input"
                                                                value="{{ $permission->id }}"
                                                                wire:model="inputs.permissions">
                                                            <span class="form-check-label">
                                                                <span class="text-muted">
                                                                    {{ $permissionLabels[explode('.',
                                                                    $permission->name)[1]] ??
                                                                    ucfirst(explode('.', $permission->name)[1] ?? $permission->name) }}
                                                                </span>
                                                            </span>
                                                        </label>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-between align-items-center">
                <a href="{{ route('admin.usermanagement.index') }}" class="btn">
                    <i class="fas fa-arrow-left me-2"></i>İptal
                </a>
                <div class="btn-list">
                    <button type="button" class="btn" wire:click="save(false, false)" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="save">
                            <i class="fas fa-save me-2"></i>Kaydet ve Devam Et
                        </span>
                        <span wire:loading wire:target="save">
                            <i class="fas fa-spinner fa-spin me-2"></i>Kaydediliyor...
                        </span>
                    </button>

                    <button type="button" class="btn btn-primary" wire:click="save(true, false)"
                        wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="save">
                            <i class="fas fa-save me-2"></i>Kaydet
                        </span>
                        <span wire:loading wire:target="save">
                            <i class="fas fa-spinner fa-spin me-2"></i>Kaydediliyor...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
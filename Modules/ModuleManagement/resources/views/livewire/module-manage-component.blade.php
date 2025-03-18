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
                                placeholder="Örn: Blog Modülü">
                            <label>Görünen Ad</label>
                            @error('inputs.display_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Modül Seçimi Bölümü -->
                        <div class="mb-4">
                            <label class="form-label">Modül Seçimi</label>
                            @if(count($this->availableModules) > 0)
                            <!-- Computed property kullanımı -->
                            <select wire:model.defer="inputs.name"
                                class="form-select @error('inputs.name') is-invalid @enderror">
                                <option value="">Lütfen bir modül seçin</option>
                                @foreach($this->availableModules as $name => $display)
                                <option value="{{ $name }}">{{ $display }}</option>
                                @endforeach
                            </select>
                            @error('inputs.name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @else
                            <div class="alert alert-info d-flex align-items-center">
                                <i class="fas fa-check-circle me-3"></i>
                                <div>
                                    <h4 class="alert-title">Tüm modüller eklenmiş!</h4>
                                    <div class="text-muted">Yeni modül eklemek için sisteminize modül yüklemelisiniz.
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>

                        <div class="form-floating mb-3">
                            <select wire:model.defer="inputs.type"
                                class="form-select @error('inputs.type') is-invalid @enderror">
                                <option value="content" {{ $inputs['type']=='content' ? 'selected' : '' }}>İçerik Modülü
                                </option>
                                <option value="management" {{ $inputs['type']=='management' ? 'selected' : '' }}>Yönetim
                                    Modülü</option>
                                <option value="system" {{ $inputs['type']=='system' ? 'selected' : '' }}>Sistem Modülü
                                </option>
                            </select>
                            <label>Modül Tipi</label>
                            @error('inputs.type')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-floating mb-3">
                            <select wire:model.defer="inputs.group"
                                class="form-select tomselect @error('inputs.group') is-invalid @enderror">
                                <option value="">Grup Seçiniz</option>
                                @foreach($existingGroups as $group)
                                <option value="{{ $group }}" {{ $inputs['group']==$group ? 'selected' : '' }}>{{ $group
                                    }}</option>
                                @endforeach
                            </select>
                            <label>Modül Grubu</label>
                            @error('inputs.group')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>


                        <!-- Modül Grubu alanından hemen sonra eklenecek -->
                        <div class="form-floating mb-3">
                            <select wire:model.defer="inputs.settings"
                                class="form-select @error('inputs.settings') is-invalid @enderror">
                                <option value="">Ayar Seçiniz</option>
                                @foreach($availableSettings as $setting)
                                <option value="{{ $setting->id }}" {{ $inputs['settings']==$setting->id ? 'selected' :
                                    '' }}>{{ $setting->name }}</option>
                                @endforeach
                            </select>
                            <label>Ayar Seçiniz</label>
                            @error('inputs.settings')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Version & Status -->
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" wire:model.defer="inputs.version"
                                        class="form-control @error('inputs.version') is-invalid @enderror"
                                        placeholder="1.0.0">
                                    <label>Versiyon</label>
                                    @error('inputs.version')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <div class="form-control h-auto pt-3">
                                        <label class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox"
                                                wire:model.defer="inputs.is_active">
                                            <span class="form-check-label">Aktif Modül</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="mt-4">
                            <label class="form-label">Açıklama</label>
                            <textarea wire:model.defer="inputs.description" class="form-control" rows="4"
                                placeholder="Modülün işlevlerini açıklayan bir metin"></textarea>
                        </div>
                    </div>

                    <!-- Right Column - Domain Selection -->
                    <div class="col-lg-4 mt-4 mt-lg-0">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-globe me-2"></i>
                                    Domain Ataması
                                </h3>
                            </div>
                            <div class="card-body p-0">
                                <div class="list-group list-group-flush overflow-auto" style="max-height: 21rem">
                                    @foreach($domains as $domainId => $domain)
                                    <div class="list-group-item py-3">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="d-flex align-items-center">
                                                <span class="avatar avatar-sm me-3 bg-blue-lt">
                                                    <i class="fas fa-globe"></i>
                                                </span>
                                                <div>
                                                    <div class="fw-bold">{{ $domain['name'] }}</div>
                                                </div>
                                            </div>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                                    wire:model.defer="inputs.domains.{{ $domainId }}">
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

            <x-form-footer route="admin.modulemanagement" :model-id="$moduleId" />
        </div>
    </form>
    @push('scripts')
    <script>
        document.addEventListener('livewire:initialized', () => {
            let tomSelect = new TomSelect('.tomselect', {
                create: true,
                createOnBlur: true,
                maxItems: 1,
                persist: false,
                openOnFocus: true,
                maxOptions: null,
                @if($inputs['group'])
                items: ['{{ $inputs['group'] }}'],
                @endif
                placeholder: 'Grup seçin veya yeni ekleyin...',
                render: {
                    no_results: function(data, escape) {
                        return '<div class="no-results">Sonuç bulunamadı...</div>';
                    },
                }
            });
        });
    </script>
    @endpush
</div>
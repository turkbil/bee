<div>
    @include('ai::admin.helper')

    <div class="card">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs">
                <li class="nav-item">
                    <a href="#tabs-settings" class="nav-link active" data-bs-toggle="tab">
                        <i class="fas fa-cogs me-2"></i>Temel Ayarlar
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#tabs-common-prompt" class="nav-link" data-bs-toggle="tab">
                        <i class="fas fa-robot me-2"></i>Ortak Özellikler
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#tabs-limits" class="nav-link" data-bs-toggle="tab">
                        <i class="fas fa-chart-line me-2"></i>Kullanım Limitleri
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#tabs-prompts" class="nav-link" data-bs-toggle="tab">
                        <i class="fas fa-list me-2"></i>Prompt Şablonları
                    </a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content">
                <!-- Temel Ayarlar -->
                <div class="tab-pane active show" id="tabs-settings">
                    <div class="row g-3">
                        <div class="col-md-7">
                            <form wire:submit.prevent="saveSettings">
                                <div class="form-floating mb-3">
                                    <input type="password" wire:model="settings.api_key"
                                        class="form-control @error('settings.api_key') is-invalid @enderror"
                                        placeholder="DeepSeek API anahtarınızı girin" id="api_key_input">
                                    <label for="api_key_input">API Anahtarı</label>
                                    <div class="form-text d-flex align-items-center">
                                        <i class="fas fa-info-circle me-2"></i>
                                        DeepSeek API anahtarını
                                        <a href="https://platform.deepseek.com/" target="_blank" class="ms-1">DeepSeek
                                            platformundan</a> alabilirsiniz.
                                        <button type="button" id="togglePassword"
                                            class="btn btn-sm btn-ghost-secondary ms-2">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    @error('settings.api_key')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-floating mb-3">
                                    <select wire:model="settings.model"
                                        class="form-select @error('settings.model') is-invalid @enderror"
                                        id="model_select">
                                        <option value="deepseek-chat">DeepSeek Chat</option>
                                        <option value="deepseek-coder">DeepSeek Coder</option>
                                    </select>
                                    <label for="model_select">Model</label>
                                    @error('settings.model')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-floating mb-3">
                                    <input type="number" wire:model="settings.max_tokens"
                                        class="form-control @error('settings.max_tokens') is-invalid @enderror"
                                        placeholder="Maksimum token sayısı" id="max_tokens_input">
                                    <label for="max_tokens_input">Maksimum Token</label>
                                    <div class="form-text">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Bir yanıtın maksimum token sayısı (4096 önerilir)
                                    </div>
                                    @error('settings.max_tokens')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Sıcaklık (Temperature): {{ $settings['temperature']
                                        }}</label>
                                    <input type="range" wire:model.live="settings.temperature"
                                        class="form-range @error('settings.temperature') is-invalid @enderror" min="0"
                                        max="1" step="0.1" id="temperature_range">
                                    <div class="form-text">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Daha düşük değerler daha tutarlı yanıtlar üretir
                                    </div>
                                    @error('settings.temperature')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <div class="pretty p-default p-curve p-toggle p-smooth ms-1">
                                        <input type="checkbox" id="is_active" name="is_active"
                                            wire:model="settings.enabled" value="1" {{ $settings['enabled'] ? 'checked'
                                            : '' }} />
                                        <div class="state p-success p-on ms-2">
                                            <label>Aktif</label>
                                        </div>
                                        <div class="state p-danger p-off ms-2">
                                            <label>Aktif Değil</label>
                                        </div>
                                    </div>
                                    <div class="form-text mt-2">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Devre dışı bırakıldığında, hiçbir kullanıcı AI asistanını kullanamaz
                                    </div>
                                </div>

                                <div class="form-footer d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i> Ayarları Kaydet
                                    </button>
                                </div>
                            </form>
                        </div>

                        <div class="col-md-5">
                            <div class="card">
                                <div class="card-status-top bg-primary"></div>
                                <div class="card-body">
                                    <h3 class="card-title">API Bağlantı Testi</h3>
                                    <p class="text-muted">
                                        AI hizmetine bağlantıyı test etmek için API anahtarınızı girin ve test butonuna
                                        tıklayın.
                                    </p>
                                    <button type="button" class="btn btn-outline-primary" wire:click="testApiConnection"
                                        wire:loading.attr="disabled">
                                        <div wire:loading.remove wire:target="testApiConnection">
                                            <i class="fas fa-plug me-2"></i> API Bağlantısını Test Et
                                        </div>
                                        <div wire:loading wire:target="testApiConnection">
                                            <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                                            Test Ediliyor...
                                        </div>
                                    </button>

                                    @if($connectionTestResult)
                                    <div
                                        class="mt-3 alert {{ $connectionTestResult['success'] ? 'alert-success' : 'alert-danger' }}">
                                        <i
                                            class="{{ $connectionTestResult['success'] ? 'fas fa-check-circle' : 'fas fa-exclamation-circle' }} me-2"></i>
                                        {{ $connectionTestResult['message'] }}
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ortak Özellikler -->
                <div class="tab-pane fade" id="tabs-common-prompt">
                    <form wire:submit.prevent="saveCommonPrompt">
                        <div class="mb-3">
                            <div class="alert alert-info">
                                <div class="d-flex">
                                    <div>
                                        <i class="fas fa-info-circle me-2 mt-1"></i>
                                    </div>
                                    <div>
                                        <h4 class="alert-title">Ortak Özellikler Promptu Nedir?</h4>
                                        <p class="text-muted">Bu prompt, AI asistanın kimliğini, kişiliğini ve
                                            davranışlarını tanımlar. Her
                                            konuşmada, konuşmaya özel prompttan önce eklenerek AI'ın tutarlı bir
                                            kişiliğe sahip
                                            olmasını sağlar.</p>
                                        <p class="text-muted mb-0">Bu bölümde şunları tanımlayabilirsiniz:</p>
                                        <ul class="text-muted mb-0">
                                            <li>AI asistanın adı ve kişiliği</li>
                                            <li>Şirket veya kuruluş bilgileri</li>
                                            <li>Yanıt verme tarzı ve tonu</li>
                                            <li>Uzmanlık alanları</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <label class="form-label">Ortak Özellikler İçeriği</label>
                            <textarea wire:model="commonPrompt.content"
                                class="form-control @error('commonPrompt.content') is-invalid @enderror" rows="10"
                                placeholder="Ortak özellikler promptunu girin"></textarea>
                            <div class="form-text">
                                <i class="fas fa-info-circle me-2"></i>
                                Bu içerik, AI'ın her yanıtında tutarlı bir kimlik ve kişilik sergilemesi için
                                kullanılır.
                            </div>
                            @error('commonPrompt.content')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i> Ortak Özellikleri Kaydet
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Kullanım Limitleri -->
                <div class="tab-pane fade" id="tabs-limits">
                    <form wire:submit.prevent="saveLimits">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-body">
                                        <h3 class="card-title">Günlük Kullanım Limiti</h3>
                                        <div class="form-floating mb-3">
                                            <input type="number" wire:model="limits.daily_limit"
                                                class="form-control @error('limits.daily_limit') is-invalid @enderror"
                                                placeholder="Günlük kullanım limiti" id="daily_limit_input">
                                            <label for="daily_limit_input">Günlük Limit</label>
                                            <div class="form-text">
                                                <i class="fas fa-info-circle me-2"></i>
                                                Bir kullanıcının günlük olarak gönderebileceği mesaj sayısı
                                            </div>
                                            @error('limits.daily_limit')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-body">
                                        <h3 class="card-title">Aylık Kullanım Limiti</h3>
                                        <div class="form-floating mb-3">
                                            <input type="number" wire:model="limits.monthly_limit"
                                                class="form-control @error('limits.monthly_limit') is-invalid @enderror"
                                                placeholder="Aylık kullanım limiti" id="monthly_limit_input">
                                            <label for="monthly_limit_input">Aylık Limit</label>
                                            <div class="form-text">
                                                <i class="fas fa-info-circle me-2"></i>
                                                Bir kullanıcının aylık olarak gönderebileceği mesaj sayısı
                                            </div>
                                            @error('limits.monthly_limit')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-footer mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i> Limitleri Kaydet
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Prompt Şablonları -->
                <div class="tab-pane fade" id="tabs-prompts">
                    <div class="d-flex justify-content-between mb-3">
                        <h3 class="card-title mb-0">Prompt Şablonları</h3>
                        <button class="btn btn-primary" wire:click="createPrompt">
                            <i class="fas fa-plus me-2"></i> Yeni Prompt
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-vcenter card-table table-striped">
                            <thead>
                                <tr>
                                    <th>Prompt Adı</th>
                                    <th style="width: 15%">Durum</th>
                                    <th style="width: 20%">Tip</th>
                                    <th style="width: 20%" class="text-center">İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($prompts as $prompt)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($prompt->is_default)
                                            <span class="badge bg-blue me-2" title="Varsayılan Prompt">
                                                <i class="fas fa-star"></i>
                                            </span>
                                            @endif
                                            <span>{{ $prompt->name }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="pretty p-default p-curve p-toggle p-smooth ms-1">
                                            <input type="checkbox" wire:click="toggleActive({{ $prompt->id }})"
                                                @if($prompt->is_active) checked @endif
                                            @if($prompt->is_default || $prompt->is_common) disabled @endif>
                                            <div class="state p-success p-on ms-2">
                                                <label>Aktif</label>
                                            </div>
                                            <div class="state p-danger p-off ms-2">
                                                <label>Pasif</label>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($prompt->is_common)
                                        <span class="badge bg-warning">Ortak Özellikler</span>
                                        @elseif($prompt->is_system)
                                        <span class="badge bg-primary">Sistem</span>
                                        @else
                                        <span class="badge bg-info">Özel</span>
                                        @endif
                                    </td>
                                    <td class="text-center align-middle">
                                        <div class="container">
                                            <div class="row">
                                                <div class="col">
                                                    @if(!$prompt->is_default && !$prompt->is_common)
                                                    <button class="btn btn-icon btn-link text-primary"
                                                        wire:click="setAsDefault({{ $prompt->id }})"
                                                        title="Varsayılan Yap">
                                                        <i class="fas fa-star"></i>
                                                    </button>
                                                    @endif
                                                </div>

                                                <div class="col">
                                                    <button class="btn btn-icon btn-link text-success"
                                                        wire:click="editPrompt({{ $prompt->id }})"
                                                        @if($prompt->is_system && !$prompt->is_common) disabled
                                                        title="Sistem promptları düzenlenemez" @endif
                                                        title="Düzenle">
                                                        <i class="fa-solid fa-pen-to-square fa-lg"></i>
                                                    </button>
                                                </div>

                                                <div class="col lh-1">
                                                    <button class="btn btn-icon btn-link text-danger"
                                                        wire:click="deletePrompt({{ $prompt->id }})"
                                                        @if($prompt->is_default || $prompt->is_system ||
                                                        $prompt->is_common) disabled @endif
                                                        title="Sil">
                                                        <i class="fa-solid fa-trash fa-lg"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-3">
                                        <div class="empty">
                                            <div class="empty-img">
                                                <i class="fas fa-robot fa-3x text-muted"></i>
                                            </div>
                                            <p class="empty-title">Henüz prompt şablonu yok</p>
                                            <p class="empty-subtitle text-muted">
                                                Yukarıdaki "Yeni Prompt" butonunu kullanarak yeni bir prompt şablonu
                                                ekleyebilirsiniz.
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <livewire:modals.prompt-edit-modal />
    <livewire:modals.prompt-delete-modal />

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const togglePassword = document.querySelector('#togglePassword');
            const password = document.querySelector('#api_key_input');
            
            if (togglePassword && password) {
                togglePassword.addEventListener('click', function() {
                    const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                    password.setAttribute('type', type);
                    this.querySelector('i').classList.toggle('fa-eye');
                    this.querySelector('i').classList.toggle('fa-eye-slash');
                });
            }
        });
    </script>
    @endpush
</div>
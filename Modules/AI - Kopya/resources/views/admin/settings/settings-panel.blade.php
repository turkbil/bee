<div>
    @include('ai::helper')

    <div class="card">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs">
                <li class="nav-item">
                    <a href="#tabs-settings" class="nav-link active" data-bs-toggle="tab">{{ __('ai::admin.basic_settings') }}</a>
                </li>
                <li class="nav-item">
                    <a href="#tabs-common-prompt" class="nav-link" data-bs-toggle="tab">{{ __('ai::admin.common_features') }}</a>
                </li>
                <li class="nav-item">
                    <a href="#tabs-limits" class="nav-link" data-bs-toggle="tab">{{ __('ai::admin.usage_limits') }}</a>
                </li>
                <li class="nav-item">
                    <a href="#tabs-prompts" class="nav-link" data-bs-toggle="tab">{{ __('ai::admin.prompt_templates') }}</a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content">
                <!-- Temel Ayarlar -->
                <div class="tab-pane active show" id="tabs-settings">
                    <form wire:submit="saveSettings">
                        <div class="form-floating mb-3">
                            <input type="password" wire:model="settings.api_key"
                                class="form-control @error('settings.api_key') is-invalid @enderror"
                                placeholder="{{ __('ai::admin.enter_api_key') }}" id="api_key_input">
                            <label for="api_key_input">{{ __('ai::admin.api_key') }}</label>
                            <div class="form-text mt-2 ms-2">
                                <i class="fas fa-info-circle me-1"></i>{!! __('ai::admin.api_key_info') !!}
                                <button type="button" id="togglePassword" class="btn btn-sm btn-ghost-secondary ms-2">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            @error('settings.api_key')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- API Test butonu ekle -->
                        <div class="mb-3">
                            <button type="button" class="btn btn-outline-info" wire:click="testApiConnection"
                                wire:loading.attr="disabled">
                                <i class="fas fa-plug me-2" wire:loading.class="d-none"
                                    wire:target="testApiConnection"></i>
                                <i class="fas fa-spinner fa-spin me-2" wire:loading wire:target="testApiConnection"></i>
                                {{ __('ai::admin.test_connection') }}
                            </button>

                            @if($connectionTestResult)
                            <div
                                class="mt-2 alert {{ $connectionTestResult['success'] ? 'alert-success' : 'alert-danger' }}">
                                <i
                                    class="{{ $connectionTestResult['success'] ? 'fas fa-check-circle' : 'fas fa-exclamation-circle' }} me-2"></i>
                                {{ $connectionTestResult['message'] }}
                            </div>
                            @endif
                        </div>


                        <div class="form-floating mb-3">
                            <select wire:model="settings.model"
                                class="form-select @error('settings.model') is-invalid @enderror" id="model_select">
                                <option value="deepseek-chat">DeepSeek Chat</option>
                                <option value="deepseek-coder">DeepSeek Coder</option>
                            </select>
                            <label for="model_select">{{ __('ai::admin.model') }}</label>
                            @error('settings.model')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-floating mb-3">
                            <input type="number" wire:model="settings.max_tokens"
                                class="form-control @error('settings.max_tokens') is-invalid @enderror"
                                placeholder="{{ __('ai::admin.max_tokens') }}" id="max_tokens_input">
                            <label for="max_tokens_input">{{ __('ai::admin.max_tokens') }}</label>
                            <div class="form-text mt-2 ms-2">
                                <i class="fas fa-info-circle me-1"></i>{{ __('ai::admin.max_tokens_info') }}
                            </div>
                            @error('settings.max_tokens')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">{{ __('ai::admin.temperature') }}: {{ $settings['temperature'] }}</label>
                            <input type="range" wire:model="settings.temperature"
                                class="form-range @error('settings.temperature') is-invalid @enderror" min="0" max="1"
                                step="0.1" id="temperature_range">
                            <div class="form-text mt-2 ms-2">
                                <i class="fas fa-info-circle me-1"></i>{{ __('ai::admin.temperature_info') }}
                            </div>
                            @error('settings.temperature')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="pretty p-default p-curve p-toggle p-smooth ms-1">
                                <input type="checkbox" id="is_active" name="is_active" wire:model="settings.enabled"
                                    value="1">
                                <div class="state p-success p-on ms-2">
                                    <label>{{ __('ai::admin.active') }}</label>
                                </div>
                                <div class="state p-danger p-off ms-2">
                                    <label>{{ __('ai::admin.inactive') }}</label>
                                </div>
                            </div>
                            <div class="form-text mt-2 ms-2">
                                <i class="fas fa-info-circle me-1"></i>{{ __('ai::admin.inactive_info') }}
                            </div>
                        </div>

                        <div class="form-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i> {{ __('ai::admin.save_settings') }}
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Ortak Özellikler Promptu -->
                <div class="tab-pane" id="tabs-common-prompt">
                    <div class="d-flex align-items-center mb-4">
                        <div class="me-3">
                            <div class="bg-blue-lt p-3 rounded-circle">
                                <i class="fas fa-share-alt text-blue"></i>
                            </div>
                        </div>
                        <div>
                            <h3 class="card-title mb-1 h4">
                                <i class="fas fa-star text-warning me-2"></i>{{ __('ai::admin.common_features_prompt') }}
                            </h3>
                            <p class="text-muted mb-0">{{ __('ai::admin.info.common_prompt_description') }}</p>
                        </div>
                    </div>

                    <div class="alert alert-info mb-4">
                        <div class="d-flex">
                            <div class="me-3">
                                <i class="fas fa-info-circle fa-2x"></i>
                            </div>
                            <div>
                                <h4 class="alert-title">{{ __('ai::admin.info.what_is_this_prompt') }}</h4>
                                <p>{{ __('ai::admin.info.common_prompt_description') }}</p>
                                <p class="mb-0">{{ __('ai::admin.info.common_prompt_features') }}</p>
                                <ul class="mb-0 mt-2">
                                    @foreach(__('ai::admin.info.common_prompt_features_list') as $feature)
                                        <li>{{ $feature }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                    <form wire:submit="saveCommonPrompt">
                        <div class="mb-3">
                            <textarea wire:model="commonPrompt.content"
                                class="form-control @error('commonPrompt.content') is-invalid @enderror" rows="10"
                                placeholder="{{ __('ai::admin.enter_common_prompt') }}"></textarea>
                            <div class="form-text mt-2 ms-2">
                                <i class="fas fa-info-circle me-1"></i>{{ __('ai::admin.common_features_usage_info') }}
                            </div>
                            @error('commonPrompt.content')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-footer">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted">{{ __('ai::admin.system_protected_info') }}</span>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i> {{ __('ai::admin.save_common_features') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Kullanım Limitleri -->
                <div class="tab-pane" id="tabs-limits">
                    <form wire:submit="saveLimits">
                        <div class="form-floating mb-3">
                            <input type="number" wire:model="limits.daily_limit"
                                class="form-control @error('limits.daily_limit') is-invalid @enderror"
                                placeholder="{{ __('ai::admin.daily_limit') }}" id="daily_limit_input">
                            <label for="daily_limit_input">{{ __('ai::admin.daily_limit') }}</label>
                            <div class="form-text mt-2 ms-2">
                                <i class="fas fa-info-circle me-1"></i>{{ __('ai::admin.daily_limit_info') }}
                            </div>
                            @error('limits.daily_limit')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-floating mb-3">
                            <input type="number" wire:model="limits.monthly_limit"
                                class="form-control @error('limits.monthly_limit') is-invalid @enderror"
                                placeholder="{{ __('ai::admin.monthly_limit') }}" id="monthly_limit_input">
                            <label for="monthly_limit_input">{{ __('ai::admin.monthly_limit') }}</label>
                            <div class="form-text mt-2 ms-2">
                                <i class="fas fa-info-circle me-1"></i>{{ __('ai::admin.monthly_limit_info') }}
                            </div>
                            @error('limits.monthly_limit')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i> {{ __('ai::admin.save_limits') }}
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Prompt Şablonları -->
                <div class="tab-pane" id="tabs-prompts">
                    <div class="d-flex justify-content-between mb-3">
                        <h4 class="section-title">{{ __('ai::admin.prompt_templates') }}</h4>
                        <button class="btn btn-primary" wire:click="$dispatch('openPromptModal')">
                            <i class="fas fa-plus me-2"></i> {{ __('ai::admin.new_prompt') }}
                        </button>
                    </div>

                    <div class="row g-3">
                        @forelse($prompts as $promptItem)
                        @if(!$promptItem->is_common)
                        <!-- Ortak özellikleri burada gösterme -->
                        <div class="col-md-6 col-lg-4">
                            <div class="card">
                                <div class="card-status-top"></div>
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h3 class="card-title mb-0">{{ $promptItem->name }}</h3>
                                        <div>
                                            @if($promptItem->is_default)
                                            <span class="text-muted small">
                                                <i class="fas fa-star text-warning me-1"></i>{{ __('ai::admin.default') }}
                                            </span>
                                            @endif
                                            @if($promptItem->is_system && !$promptItem->is_common)
                                            <span class="text-muted small ms-2">
                                                <i class="fas fa-cog text-info me-1"></i>{{ __('ai::admin.system') }}
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                    <p class="text-muted"
                                        style="height: 4.5rem; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical;">
                                        {{ $promptItem->content }}
                                    </p>
                                </div>
                                <div class="card-footer">
                                    <div class="d-flex justify-content-between">
                                        <div class="d-flex gap-2 align-items-center">
                                            <!-- Aktif/Pasif toggle'ı card footer'a taşındı -->
                                            <div class="pretty p-default p-curve p-toggle p-smooth">
                                                <input type="checkbox" id="is_active_{{ $promptItem->id }}"
                                                    name="is_active"
                                                    wire:click="togglePromptActive({{ $promptItem->id }})" {{
                                                    $promptItem->is_active ? 'checked' : '' }}
                                                {{ $promptItem->is_system ? 'disabled' : '' }}>
                                                <div class="state p-success p-on ms-2">
                                                    <label>{{ __('ai::admin.active') }}</label>
                                                </div>
                                                <div class="state p-danger p-off ms-2">
                                                    <label>{{ __('ai::admin.passive') }}</label>
                                                </div>
                                            </div>

                                            @if(!$promptItem->is_default)
                                            <div class="pretty p-default p-curve p-smooth ms-3">
                                                <input type="checkbox" id="is_default_{{ $promptItem->id }}"
                                                    name="is_default" wire:click="makeDefault({{ $promptItem->id }})" {{
                                                    $promptItem->is_default ? 'checked' : '' }}
                                                {{ $promptItem->is_system && !$promptItem->is_common ? 'disabled' : ''
                                                }}>
                                                <div class="state p-primary ms-2">
                                                    <label>{{ __('ai::admin.default') }}</label>
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                        <div class="btn-list">
                                            <button class="btn btn-sm btn-ghost-secondary"
                                                wire:click="editPrompt({{ $promptItem->id }})"
                                                @if($promptItem->is_system && !$promptItem->is_common) disabled
                                                title="{{ __('ai::admin.warning.prompt_system_no_edit') }}" @endif>
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-ghost-danger"
                                                wire:click="$dispatch('showPromptDeleteModal', {id: {{$promptItem->id}}, name: '{{$promptItem->name}}'})"
                                                @if($promptItem->is_default || $promptItem->is_system) disabled
                                                title="{{ __('ai::admin.warning.prompt_cannot_delete') }}" @endif>
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                        @empty
                        <div class="col-12">
                            <div class="empty">
                                <p class="empty-title">{{ __('ai::admin.info.no_prompts') }}</p>
                                <p class="empty-subtitle text-muted">
                                    {{ __('ai::admin.info.no_prompts_description') }}
                                </p>
                            </div>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

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

    <!-- Prompt düzenleme modali -->
    <livewire:modals.prompt-edit-modal />

    <!-- Prompt silme modali -->
    <livewire:modals.prompt-delete-modal />
</div>
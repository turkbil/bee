@extends('admin.layout')

@section('page-title', __('ai::admin.providers.title'))

@section('content')
    <div class="page-wrapper">
        <div class="page-header d-print-none">
            <div class="container-xl">
                <div class="row g-2 align-items-center">
                    <div class="col">
                        <h2 class="page-title">
                            <i class="fas fa-robot me-2"></i>
                            {{ __('ai::admin.providers.management') }}
                        </h2>
                        <div class="text-muted mt-1">{{ __('ai::admin.providers.description') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="page-body">
            <div class="container-xl">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-cogs me-2"></i>
                                    {{ __('ai::admin.providers.active_providers') }}
                                </h3>
                                <div class="card-actions">
                                    <span class="badge bg-primary">{{ $providers->count() }} Provider</span>
                                </div>
                            </div>
                            <div class="card-body">
                                @if($providers->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-vcenter card-table">
                                            <thead>
                                                <tr>
                                                    <th>{{ __('ai::admin.providers.name') }}</th>
                                                    <th>{{ __('ai::admin.providers.model') }}</th>
                                                    <th>{{ __('ai::admin.providers.priority') }}</th>
                                                    <th>{{ __('ai::admin.providers.status') }}</th>
                                                    <th>{{ __('ai::admin.providers.response_time') }}</th>
                                                    <th>{{ __('ai::admin.providers.actions') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($providers as $provider)
                                                    <tr>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <div class="avatar avatar-sm me-2" style="background-color: {{ $provider->priority === 1 ? '#4ade80' : ($provider->priority === 2 ? '#fb923c' : '#a855f7') }}">
                                                                    @if($provider->name === 'deepseek')
                                                                        <i class="fas fa-brain text-white"></i>
                                                                    @elseif($provider->name === 'openai')
                                                                        <i class="fas fa-robot text-white"></i>
                                                                    @elseif($provider->name === 'claude')
                                                                        <i class="fas fa-microchip text-white"></i>
                                                                    @else
                                                                        <i class="fas fa-cog text-white"></i>
                                                                    @endif
                                                                </div>
                                                                <div>
                                                                    <div class="fw-bold">{{ $provider->display_name }}</div>
                                                                    <div class="text-muted small">{{ ucfirst($provider->name) }}</div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-blue-lt">{{ $provider->default_model }}</span>
                                                            @if($provider->is_default)
                                                                <span class="badge bg-green ms-1">Default</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <span class="badge bg-{{ $provider->priority === 1 ? 'green' : ($provider->priority === 2 ? 'orange' : 'purple') }}">
                                                                    #{{ $provider->priority }}
                                                                </span>
                                                                @if($provider->priority === 1)
                                                                    <span class="text-muted small ms-2">Primary</span>
                                                                @elseif($provider->priority === 2)
                                                                    <span class="text-muted small ms-2">Fallback</span>
                                                                @else
                                                                    <span class="text-muted small ms-2">Secondary</span>
                                                                @endif
                                                            </div>
                                                        </td>
                                                        <td>
                                                            @if($provider->is_active)
                                                                <span class="badge bg-success">{{ __('ai::admin.providers.active') }}</span>
                                                            @else
                                                                <span class="badge bg-danger">{{ __('ai::admin.providers.inactive') }}</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($provider->average_response_time)
                                                                <span class="text-muted">{{ number_format($provider->average_response_time, 0) }}ms</span>
                                                            @else
                                                                <span class="text-muted">N/A</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <div class="d-flex gap-1">
                                                                <button type="button" 
                                                                        class="btn btn-sm btn-outline-primary"
                                                                        data-bs-toggle="modal" 
                                                                        data-bs-target="#providerModal{{ $provider->id }}">
                                                                    <i class="fas fa-edit"></i>
                                                                    {{ __('ai::admin.providers.edit') }}
                                                                </button>
                                                                
                                                                @if($provider->is_active)
                                                                    <button type="button" 
                                                                            class="btn btn-sm btn-outline-danger"
                                                                            onclick="toggleProvider({{ $provider->id }}, false)">
                                                                        <i class="fas fa-pause"></i>
                                                                        {{ __('ai::admin.providers.disable') }}
                                                                    </button>
                                                                @else
                                                                    <button type="button" 
                                                                            class="btn btn-sm btn-outline-success"
                                                                            onclick="toggleProvider({{ $provider->id }}, true)">
                                                                        <i class="fas fa-play"></i>
                                                                        {{ __('ai::admin.providers.enable') }}
                                                                    </button>
                                                                @endif
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="empty">
                                        <div class="empty-icon">
                                            <i class="fas fa-robot"></i>
                                        </div>
                                        <p class="empty-title">{{ __('ai::admin.providers.no_providers') }}</p>
                                        <p class="empty-subtitle text-muted">{{ __('ai::admin.providers.no_providers_description') }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Provider Edit Modals -->
    @foreach($providers as $provider)
        <div class="modal fade" id="providerModal{{ $provider->id }}" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form action="{{ route('admin.ai.providers.update', $provider) }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="fas fa-edit me-2"></i>
                                {{ __('ai::admin.providers.edit_provider') }}: {{ $provider->display_name }}
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('ai::admin.providers.priority') }}</label>
                                        <select name="priority" class="form-select">
                                            <option value="1" {{ $provider->priority === 1 ? 'selected' : '' }}>1 - Primary</option>
                                            <option value="2" {{ $provider->priority === 2 ? 'selected' : '' }}>2 - Fallback</option>
                                            <option value="3" {{ $provider->priority === 3 ? 'selected' : '' }}>3 - Secondary</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('ai::admin.providers.status') }}</label>
                                        <div>
                                            <label class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ $provider->is_active ? 'checked' : '' }}>
                                                <span class="form-check-label">{{ __('ai::admin.providers.active') }}</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">{{ __('ai::admin.providers.api_key') }}</label>
                                <div class="input-group">
                                    <input type="password" name="api_key" class="form-control" value="{{ $provider->api_key }}" placeholder="API Key">
                                    <button type="button" class="btn btn-outline-secondary" onclick="togglePasswordVisibility(this)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div class="form-text">{{ __('ai::admin.providers.api_key_help') }}</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">{{ __('ai::admin.providers.default_provider') }}</label>
                                <div>
                                    <label class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="is_default" value="1" {{ $provider->is_default ? 'checked' : '' }}>
                                        <span class="form-check-label">{{ __('ai::admin.providers.set_as_default') }}</span>
                                    </label>
                                </div>
                            </div>

                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>{{ __('ai::admin.providers.info') }}:</strong>
                                {{ __('ai::admin.providers.priority_explanation') }}
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                {{ __('ai::admin.cancel') }}
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>
                                {{ __('ai::admin.save') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    <script>
        function toggleProvider(providerId, status) {
            fetch(`/admin/ai/providers/${providerId}/update`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    is_active: status
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('{{ __("ai::admin.providers.error_updating") }}');
            });
        }

        function togglePasswordVisibility(button) {
            const input = button.parentElement.querySelector('input');
            const icon = button.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'fas fa-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'fas fa-eye';
            }
        }
    </script>
@endsection
@include('languagemanagement::admin.helper')

<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-language me-2"></i>
            {{ __('languagemanagement::admin.translation_checker') }}
        </h3>
        <div class="card-actions">
            <div class="dropdown">
                <a href="#" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="fas fa-cog me-1"></i>
                    {{ __('languagemanagement::admin.scan_options') }}
                </a>
                <div class="dropdown-menu dropdown-menu-end">
                    <label class="dropdown-item">
                        <input type="radio" wire:model.live="scanType" value="all" class="form-check-input me-2">
                        {{ __('languagemanagement::admin.scan_all_modules') }}
                    </label>
                    <label class="dropdown-item">
                        <input type="radio" wire:model.live="scanType" value="selected" class="form-check-input me-2">
                        {{ __('languagemanagement::admin.scan_selected_modules') }}
                    </label>
                </div>
            </div>
        </div>
    </div>

    <div class="card-body">
        <!-- Açıklama -->
        <div class="alert alert-info d-flex">
            <div class="me-3">
                <i class="fas fa-info-circle fa-2x"></i>
            </div>
            <div>
                <h4 class="alert-title">{{ __('languagemanagement::admin.what_is_translation_checker') }}</h4>
                <div class="text-muted">{{ __('languagemanagement::admin.translation_checker_description') }}</div>
            </div>
        </div>

        <!-- Modül Seçimi (Seçili modüller için) -->
        @if($scanType === 'selected')
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title">{{ __('languagemanagement::admin.select_modules') }}</h3>
                <div class="card-actions">
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="toggleAllModules()">
                        <i class="fas fa-check-double me-1"></i>
                        {{ __('languagemanagement::admin.toggle_all') }}
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($modules as $moduleName => $module)
                    <div class="col-md-3 mb-2">
                        <label class="form-check">
                            <input type="checkbox" wire:model="selectedModules" value="{{ $moduleName }}" class="form-check-input">
                            <span class="form-check-label">
                                <i class="fas fa-puzzle-piece me-1 text-primary"></i>
                                {{ $moduleName }}
                            </span>
                        </label>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Kontrol Butonları -->
        <div class="row mb-4">
            <div class="col">
                <button type="button" wire:click="checkTranslations" class="btn btn-primary" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="checkTranslations">
                        <i class="fas fa-search me-1"></i>
                        {{ __('languagemanagement::admin.check_translations') }}
                        @if($scanType === 'selected')
                            ({{ count($selectedModules) }} {{ __('languagemanagement::admin.modules') }})
                        @else
                            ({{ __('languagemanagement::admin.all_modules') }})
                        @endif
                    </span>
                    <span wire:loading wire:target="checkTranslations">
                        <i class="fas fa-spinner fa-spin me-1"></i>
                        {{ __('languagemanagement::admin.scanning') }}...
                    </span>
                </button>

                @if(!empty($results) && $totalMissing > 0)
                <button type="button" wire:click="fixTranslations" class="btn btn-success ms-2" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="fixTranslations">
                        <i class="fas fa-magic me-1"></i>
                        {{ __('languagemanagement::admin.fix_all_translations') }}
                    </span>
                    <span wire:loading wire:target="fixTranslations">
                        <i class="fas fa-spinner fa-spin me-1"></i>
                        {{ __('languagemanagement::admin.fixing') }}...
                    </span>
                </button>
                @endif
            </div>
        </div>

        <!-- Loading -->
        <div wire:loading wire:target="checkTranslations,fixTranslations" class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">{{ __('languagemanagement::admin.loading') }}...</span>
            </div>
            <p class="mt-2 text-muted">{{ __('languagemanagement::admin.analyzing_modules') }}...</p>
        </div>

        <!-- Sonuçlar -->
        @if(!empty($results))
        <div wire:loading.remove wire:target="checkTranslations,fixTranslations">
            <!-- Özet -->
            <div class="row row-deck row-cards mb-4">
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">{{ __('languagemanagement::admin.total_modules') }}</div>
                            </div>
                            <div class="h1 mb-0">{{ count($results) }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">{{ __('languagemanagement::admin.modules_with_issues') }}</div>
                            </div>
                            <div class="h1 mb-0 {{ $totalMissing > 0 ? 'text-danger' : 'text-success' }}">
                                {{ collect($results)->filter(function($result) { 
                                    return !empty($result['missing']) && (
                                        !empty($result['missing']['tr']['admin']) || 
                                        !empty($result['missing']['tr']['front']) ||
                                        !empty($result['missing']['en']['admin']) || 
                                        !empty($result['missing']['en']['front'])
                                    );
                                })->count() }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">{{ __('languagemanagement::admin.total_missing') }}</div>
                            </div>
                            <div class="h1 mb-0 {{ $totalMissing > 0 ? 'text-danger' : 'text-success' }}">{{ $totalMissing }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">{{ __('languagemanagement::admin.status') }}</div>
                            </div>
                            <div class="h1 mb-0">
                                @if($totalMissing === 0)
                                    <span class="text-success">
                                        <i class="fas fa-check-circle"></i>
                                    </span>
                                @else
                                    <span class="text-warning">
                                        <i class="fas fa-exclamation-triangle"></i>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if($totalMissing === 0)
            <!-- Başarı Mesajı -->
            <div class="alert alert-success d-flex">
                <div class="me-3">
                    <i class="fas fa-check-circle fa-2x"></i>
                </div>
                <div>
                    <h4 class="alert-title">{{ __('languagemanagement::admin.perfect_translations') }}</h4>
                    <div class="text-muted">{{ __('languagemanagement::admin.all_translations_complete') }}</div>
                </div>
            </div>
            @else
            <!-- Modül Detayları -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('languagemanagement::admin.detailed_results') }}</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table">
                            <thead>
                                <tr>
                                    <th>{{ __('languagemanagement::admin.module') }}</th>
                                    <th class="text-center">{{ __('languagemanagement::admin.tr_admin') }}</th>
                                    <th class="text-center">{{ __('languagemanagement::admin.tr_front') }}</th>
                                    <th class="text-center">{{ __('languagemanagement::admin.en_admin') }}</th>
                                    <th class="text-center">{{ __('languagemanagement::admin.en_front') }}</th>
                                    <th class="text-center">{{ __('languagemanagement::admin.total') }}</th>
                                    <th class="text-center">{{ __('languagemanagement::admin.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($results as $moduleName => $result)
                                @php
                                    $trAdminMissing = count($result['missing']['tr']['admin'] ?? []);
                                    $trFrontMissing = count($result['missing']['tr']['front'] ?? []);
                                    $enAdminMissing = count($result['missing']['en']['admin'] ?? []);
                                    $enFrontMissing = count($result['missing']['en']['front'] ?? []);
                                    $moduleTotal = $trAdminMissing + $trFrontMissing + $enAdminMissing + $enFrontMissing;
                                @endphp
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-puzzle-piece me-2 text-primary"></i>
                                            <strong>{{ $moduleName }}</strong>
                                            @if($moduleTotal === 0)
                                                <span class="badge bg-success ms-2">
                                                    <i class="fas fa-check"></i>
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        @if($trAdminMissing > 0)
                                            <span class="badge bg-danger">{{ $trAdminMissing }}</span>
                                        @else
                                            <span class="text-success"><i class="fas fa-check"></i></span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($trFrontMissing > 0)
                                            <span class="badge bg-danger">{{ $trFrontMissing }}</span>
                                        @else
                                            <span class="text-success"><i class="fas fa-check"></i></span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($enAdminMissing > 0)
                                            <span class="badge bg-danger">{{ $enAdminMissing }}</span>
                                        @else
                                            <span class="text-success"><i class="fas fa-check"></i></span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($enFrontMissing > 0)
                                            <span class="badge bg-danger">{{ $enFrontMissing }}</span>
                                        @else
                                            <span class="text-success"><i class="fas fa-check"></i></span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($moduleTotal > 0)
                                            <span class="badge bg-warning">{{ $moduleTotal }}</span>
                                        @else
                                            <span class="text-success"><i class="fas fa-check"></i></span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($moduleTotal > 0)
                                            <button type="button" wire:click="toggleDetails('{{ $moduleName }}')" class="btn btn-sm btn-outline-primary me-1">
                                                <i class="fas fa-{{ in_array($moduleName, $showDetails) ? 'eye-slash' : 'eye' }}"></i>
                                            </button>
                                            <button type="button" wire:click="fixTranslations('{{ $moduleName }}')" class="btn btn-sm btn-success">
                                                <i class="fas fa-magic"></i>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                                
                                <!-- Detay satırı -->
                                @if(in_array($moduleName, $showDetails) && $moduleTotal > 0)
                                <tr>
                                    <td colspan="7" class="p-0">
                                        <div class="bg-light p-3">
                                            <div class="row">
                                                @foreach(['tr', 'en'] as $locale)
                                                    @foreach(['admin', 'front'] as $context)
                                                        @if(!empty($result['missing'][$locale][$context]))
                                                        <div class="col-md-6 mb-3">
                                                            <h5 class="text-{{ $locale === 'tr' ? 'primary' : 'info' }}">
                                                                <i class="fas fa-language me-1"></i>
                                                                {{ strtoupper($locale) }} {{ ucfirst($context) }}
                                                                <span class="badge bg-secondary ms-1">{{ count($result['missing'][$locale][$context]) }}</span>
                                                            </h5>
                                                            <div class="list-group list-group-flush">
                                                                @foreach($result['missing'][$locale][$context] as $key => $info)
                                                                <div class="list-group-item">
                                                                    <div class="d-flex justify-content-between align-items-start">
                                                                        <div>
                                                                            <code class="text-danger">{{ $key }}</code>
                                                                            <br>
                                                                            <small class="text-muted">{{ $info['file'] }}</small>
                                                                        </div>
                                                                        <span class="badge bg-warning">Missing</span>
                                                                    </div>
                                                                </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                        @endif
                                                    @endforeach
                                                @endforeach
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>
        @endif
    </div>
</div>

<script>
function toggleAllModules() {
    const checkboxes = document.querySelectorAll('input[wire\\:model="selectedModules"]');
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    
    checkboxes.forEach(cb => {
        cb.checked = !allChecked;
        cb.dispatchEvent(new Event('change'));
    });
}
</script>
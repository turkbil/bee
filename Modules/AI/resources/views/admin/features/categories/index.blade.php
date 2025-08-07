<div>
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <div class="page-pretitle">{{ __('ai::admin.artificial_intelligence') }}</div>
                    <h2 class="page-title">{{ __('ai::admin.ai_feature_categories') }}</h2>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <button type="button" class="btn btn-primary" wire:click="showAddForm">
                            <i class="fas fa-plus me-1"></i>
                            {{ __('ai::admin.new_category') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                
                <!-- Add/Edit Form Card -->
                @if($showForm)
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                {{ $editingCategoryId ? __('ai::admin.edit_category') : __('ai::admin.new_category') }}
                            </h3>
                            <div class="card-actions">
                                <button type="button" class="btn-close" wire:click="hideForm"></button>
                            </div>
                        </div>
                        <div class="card-body">
                            <form wire:submit.prevent="{{ $editingCategoryId ? 'updateCategory' : 'addCategory' }}">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="mb-3">
                                            <label class="form-label">{{ __('ai::admin.category_title') }}</label>
                                            <input type="text" 
                                                wire:model="title" 
                                                class="form-control @error('title') is-invalid @enderror"
                                                placeholder="Kategori başlığı">
                                            @error('title')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">{{ __('ai::admin.status') }}</label>
                                            <label class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" wire:model="is_active">
                                                <span class="form-check-label">{{ __('ai::admin.active') }}</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">{{ __('ai::admin.description') }}</label>
                                    <textarea wire:model="description" 
                                        class="form-control" 
                                        rows="3"
                                        placeholder="Kategori açıklaması (opsiyonel)"></textarea>
                                </div>
                                <div class="form-footer">
                                    <div class="btn-list">
                                        <button type="button" class="btn" wire:click="hideForm">
                                            {{ __('ai::admin.cancel') }}
                                        </button>
                                        <button type="submit" class="btn btn-primary">
                                            {{ $editingCategoryId ? __('ai::admin.update') : __('ai::admin.add') }}
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Categories List Card -->
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('ai::admin.ai_feature_categories') }}</h3>
                            <div class="card-actions">
                                <div class="input-icon">
                                    <span class="input-icon-addon">
                                        <i class="fas fa-search"></i>
                                    </span>
                                    <input type="text" 
                                        wire:model.live.debounce.300ms="search" 
                                        class="form-control"
                                        placeholder="Kategori ara...">
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            @if($categories && $categories->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-vcenter card-table">
                                        <thead>
                                            <tr>
                                                <th>{{ __('ai::admin.category_title') }}</th>
                                                <th>{{ __('ai::admin.description') }}</th>
                                                <th>{{ __('ai::admin.ai_features') }}</th>
                                                <th>{{ __('ai::admin.status') }}</th>
                                                <th class="w-1">{{ __('ai::admin.actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($categories as $category)
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="me-3">
                                                                <i class="{{ $category->icon ?? 'fas fa-folder' }}"></i>
                                                            </div>
                                                            <div>
                                                                <div class="fw-medium">{{ $category->title }}</div>
                                                                @if($category->slug)
                                                                    <div class="text-muted small">{{ $category->slug }}</div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="text-muted">{{ Str::limit($category->description, 50) ?: '-' }}</span>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-azure">{{ $category->ai_features_count ?? 0 }}</span>
                                                    </td>
                                                    <td>
                                                        @if($category->is_active)
                                                            <span class="badge bg-green">{{ __('ai::admin.active') }}</span>
                                                        @else
                                                            <span class="badge bg-red">{{ __('ai::admin.inactive') }}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <button type="button" 
                                                                class="btn btn-sm btn-outline-primary"
                                                                wire:click="editCategory({{ $category->ai_feature_category_id }})">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <button type="button" 
                                                                class="btn btn-sm btn-outline-{{ $category->is_active ? 'warning' : 'success' }}"
                                                                wire:click="toggleActive({{ $category->ai_feature_category_id }})">
                                                                <i class="fas fa-{{ $category->is_active ? 'pause' : 'play' }}"></i>
                                                            </button>
                                                            <button type="button" 
                                                                class="btn btn-sm btn-outline-danger"
                                                                wire:click="deleteCategory({{ $category->ai_feature_category_id }})"
                                                                onclick="return confirm('Emin misiniz?')">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="empty">
                                    <div class="empty-img">
                                        <i class="fas fa-brain fa-4x text-muted"></i>
                                    </div>
                                    <p class="empty-title">Henüz kategori eklenmemiş</p>
                                    <p class="empty-subtitle text-muted">
                                        @if($search && trim($search) !== '')
                                            Arama kriterinize uygun kategori bulunamadı
                                        @else
                                            İlk AI kategorinizi eklemek için yukarıdaki butona tıklayın
                                        @endif
                                    </p>
                                    @if($search && trim($search) !== '')
                                        <div class="empty-action">
                                            <button wire:click="$set('search', '')" class="btn btn-primary">
                                                <i class="fas fa-times me-1"></i> Aramayı Temizle
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
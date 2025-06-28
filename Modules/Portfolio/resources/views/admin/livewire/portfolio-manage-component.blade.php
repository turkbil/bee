@include('portfolio::admin.helper')
<div>
    @include('admin.partials.error_message')
    <form wire:submit.prevent="save">
        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs">
                    <li class="nav-item">
                        <a href="#tabs-1" class="nav-link active" data-bs-toggle="tab">{{ __('portfolio::admin.basic_info') }}</a>
                    </li>
                    <li class="nav-item">
                        <a href="#tabs-2" class="nav-link" data-bs-toggle="tab">{{ __('portfolio::admin.seo') }}</a>
                    </li>
                </ul>
                
                @if($studioEnabled && $portfolioId)
                <div class="card-actions">
                    <a href="{{ route('admin.studio.editor', ['module' => 'portfolio', 'id' => $portfolioId]) }}" target="_blank" class="btn btn-primary">
                        <i class="fas fa-wand-magic-sparkles me-2"></i> {{ __('portfolio::admin.edit_with_studio') }}
                    </a>
                </div>
                @endif
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <!-- Temel Bilgiler -->
                    <div class="tab-pane fade active show" id="tabs-1">
                        <div class="form-floating mb-3">
                            <input type="text" wire:model="inputs.title"
                                class="form-control @error('inputs.title') is-invalid @enderror"
                                placeholder="{{ __('portfolio::admin.portfolio_title_placeholder') }}">
                            <label>{{ __('portfolio::admin.title') }}</label>
                            @error('inputs.title')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-floating mb-3">
                            <select wire:model.defer="inputs.portfolio_category_id"
                                class="form-control @error('inputs.portfolio_category_id') is-invalid @enderror"
                                data-choices 
                                data-choices-search="{{ count($categories) > 6 ? 'true' : 'false' }}"
                                data-choices-placeholder="{{ __('portfolio::admin.select_category') }}">
                                <option value="">{{ __('portfolio::admin.select_category') }}</option>
                                @foreach($categories as $category)
                                <option value="{{ $category->portfolio_category_id }}" {{ $category->
                                    portfolio_category_id == $inputs['portfolio_category_id'] ? 'selected' : '' }}>
                                    {{ $category->title }}
                                </option>
                                @endforeach
                            </select>
                            <label>{{ __('portfolio::admin.category') }}</label>
                            @error('inputs.portfolio_category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        @include('portfolio::admin.partials.image-upload', [
                        'imageKey' => 'image',
                        'label' => __('admin.drag_drop_image')
                        ])

                        <div class="mb-3" wire:ignore>
                            <textarea id="editor" wire:model.defer="inputs.body">{{ $inputs['body'] }}</textarea>
                        </div>

                        <div class="mb-3">
                            <div class="pretty p-default p-curve p-toggle p-smooth ms-1">
                                <input type="checkbox" id="is_active" name="is_active" wire:model="inputs.is_active"
                                    value="1" {{ (!isset($inputs['is_active']) || $inputs['is_active']) ? 'checked' : '' }} />

                                <div class="state p-success p-on ms-2">
                                    <label>{{ __('portfolio::admin.active') }}</label>
                                </div>
                                <div class="state p-danger p-off ms-2">
                                    <label>{{ __('portfolio::admin.not_active') }}</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- SEO -->
                    <div class="tab-pane fade" id="tabs-2">
                        <div class="form-floating mb-3">
                            <input type="text" wire:model="inputs.slug" class="form-control" placeholder="{{ __('portfolio::admin.slug') }}">
                            <label>{{ __('portfolio::admin.slug') }}</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="text" 
                                wire:model.defer="inputs.metakey"
                                class="form-control"
                                data-choices
                                data-choices-multiple="true"
                                data-choices-search="false"
                                data-choices-filter="true"
                                data-choices-placeholder="{{ __('portfolio::admin.enter_keywords') }}"
                                value="{{ is_array($inputs['metakey']) ? implode(',', $inputs['metakey']) : $inputs['metakey'] }}"
                                placeholder="{{ __('portfolio::admin.enter_keywords') }}">
                            <label>{{ __('portfolio::admin.meta_keywords') }}</label>
                        </div>

                        <div class="form-floating mb-3">
                            <textarea wire:model="inputs.metadesc" class="form-control" data-bs-toggle="autosize"
                                placeholder="{{ __('portfolio::admin.meta_description_placeholder') }}"></textarea>
                            <label>{{ __('portfolio::admin.meta_description') }}</label>
                        </div>
                    </div>
                </div>
            </div>

            <x-form-footer route="admin.portfolio" :model-id="$portfolioId" />

        </div>
    </form>
</div>
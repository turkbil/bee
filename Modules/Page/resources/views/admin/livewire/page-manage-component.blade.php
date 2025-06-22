@include('page::admin.helper')
<div>
    @include('admin.partials.error_message')
    <form wire:submit.prevent="save">
        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs">
                    <li class="nav-item">
                        <a href="#tabs-1" class="nav-link active" data-bs-toggle="tab">{{ t('common.basic_info') }}</a>
                    </li>
                    <li class="nav-item">
                        <a href="#tabs-2" class="nav-link" data-bs-toggle="tab">{{ t('page::general.seo') }}</a>
                    </li>
                    <li class="nav-item">
                        <a href="#tabs-3" class="nav-link" data-bs-toggle="tab">{{ t('common.code_area') }}</a>
                    </li>
                </ul>
                
                @if($studioEnabled && $pageId)
                <div class="card-actions">
                    <a href="{{ route('admin.studio.editor', ['module' => 'page', 'id' => $pageId]) }}" target="_blank" class="btn btn-primary">
                        <i class="fas fa-wand-magic-sparkles me-2"></i> {{ t('studio.edit_with_studio', ['default' => 'Edit with Studio']) }}
                    </a>
                </div>
                @endif
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <!-- {{ t('common.basic_info') }} -->
                    <div class="tab-pane fade active show" id="tabs-1">
                        <div class="form-floating mb-3">
                            <input type="text" wire:model="inputs.title"
                                class="form-control @error('inputs.title') is-invalid @enderror"
                                placeholder="{{ t('page::general.title_field') }}">
                            <label>{{ t('page::general.title_field') }}</label>
                            @error('inputs.title')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3" wire:ignore>
                            <textarea id="editor" wire:model.defer="inputs.body">{{ $inputs['body'] }}</textarea>
                        </div>
                        <div class="mb-3">
                            <div class="pretty p-default p-curve p-toggle p-smooth ms-1">
                                <input type="checkbox" id="is_active" name="is_active" wire:model="inputs.is_active"
                                    value="1" {{ (!isset($inputs['is_active']) || $inputs['is_active']) ? 'checked' : '' }} />

                                <div class="state p-success p-on ms-2">
                                    <label>{{ t('page::general.active') }}</label>
                                </div>
                                <div class="state p-danger p-off ms-2">
                                    <label>{{ t('page::general.inactive') }}</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- {{ t('page::general.seo') }} -->
                    <div class="tab-pane fade" id="tabs-2">
                        <div class="form-floating mb-3">
                            <input type="text" wire:model="inputs.slug" class="form-control" placeholder="{{ t('page::general.slug_field') }}">
                            <label>{{ t('page::general.slug_field') }}</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="text" 
                                wire:model.defer="inputs.metakey"
                                class="form-control"
                                data-choices
                                data-choices-multiple="true"
                                data-choices-search="false"
                                data-choices-filter="true"
                                data-choices-placeholder="{{ t('page::general.meta_keywords') }}..."
                                value="{{ is_array($inputs['metakey']) ? implode(',', $inputs['metakey']) : $inputs['metakey'] }}"
                                placeholder="{{ t('page::general.meta_keywords') }}...">
                            <label>{{ t('page::general.meta_keywords') }}</label>
                        </div>

                        <div class="form-floating mb-3">
                            <textarea wire:model="inputs.metadesc" class="form-control" data-bs-toggle="autosize"
                                placeholder="{{ t('page::general.meta_description') }}"></textarea>
                            <label>{{ t('page::general.meta_description') }}</label>
                        </div>
                    </div>

                    <!-- {{ t('common.code_area') }} -->
                    <div class="tab-pane fade" id="tabs-3">
                        <div class="form-floating mb-3">
                            <textarea wire:model="inputs.css" class="form-control" data-bs-toggle="autosize"
                                placeholder="{{ t('common.css_code') }}"></textarea>
                            <label>{{ t('common.css') }}</label>
                        </div>
                        <div class="form-floating mb-3">
                            <textarea wire:model="inputs.js" class="form-control" data-bs-toggle="autosize"
                                placeholder="{{ t('common.js_code') }}"></textarea>
                            <label>{{ t('common.javascript') }}</label>
                        </div>
                    </div>
                </div>
            </div>

            <x-form-footer route="admin.page" :model-id="$pageId" />

        </div>
    </form>
</div>
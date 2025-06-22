@include('announcement::admin.helper')
<div>
    @include('admin.partials.error_message')
    <form wire:submit.prevent="save">
        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs">
                    <li class="nav-item">
                        <a href="#tabs-1" class="nav-link active" data-bs-toggle="tab">{{ t('announcement::general.basic_info') }}</a>
                    </li>
                    <li class="nav-item">
                        <a href="#tabs-2" class="nav-link" data-bs-toggle="tab">{{ t('announcement::general.seo') }}</a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <!-- {{ t('announcement::general.basic_info') }} -->
                    <div class="tab-pane fade active show" id="tabs-1">
                        <div class="form-floating mb-3">
                            <input type="text" wire:model="inputs.title"
                                class="form-control @error('inputs.title') is-invalid @enderror"
                                placeholder="{{ t('announcement::general.announcement_title_placeholder') }}">
                            <label>{{ t('announcement::general.title') }}</label>
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
                                    <label>{{ t('announcement::general.active') }}</label>
                                </div>
                                <div class="state p-danger p-off ms-2">
                                    <label>{{ t('announcement::general.inactive') }}</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- {{ t('announcement::general.seo') }} -->
                    <div class="tab-pane fade" id="tabs-2">
                        <div class="form-floating mb-3">
                            <input type="text" wire:model="inputs.slug" class="form-control" placeholder="{{ t('announcement::general.slug') }}">
                            <label>{{ t('announcement::general.slug') }}</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="text" 
                                wire:model.defer="inputs.metakey"
                                class="form-control"
                                data-choices
                                data-choices-multiple="true"
                                data-choices-search="false"
                                data-choices-filter="true"
                                data-choices-placeholder="{{ t('announcement::general.meta_keywords_placeholder') }}"
                                value="{{ is_array($inputs['metakey']) ? implode(',', $inputs['metakey']) : $inputs['metakey'] }}"
                                placeholder="{{ t('announcement::general.meta_keywords_placeholder') }}">
                            <label>{{ t('announcement::general.meta_keywords') }}</label>
                        </div>

                        <div class="form-floating mb-3">
                            <textarea wire:model="inputs.metadesc" class="form-control" data-bs-toggle="autosize"
                                placeholder="{{ t('announcement::general.meta_description_placeholder') }}"></textarea>
                            <label>{{ t('announcement::general.meta_description') }}</label>
                        </div>
                    </div>
                </div>
            </div>

            <x-form-footer route="admin.announcement" :model-id="$announcementId" />

        </div>
    </form>
</div>
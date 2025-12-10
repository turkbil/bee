<div class="card-footer"
    x-data="{ isMediaUploading: false }"
    @media-upload-started.window="console.log('🔒 [FOOTER] Window event received: media-upload-started'); isMediaUploading = true; console.log('🔒 [FOOTER] isMediaUploading set to:', isMediaUploading)"
    @media-upload-completed.window="console.log('🔓 [FOOTER] Window event received: media-upload-completed'); isMediaUploading = false; console.log('🔓 [FOOTER] isMediaUploading set to:', isMediaUploading)"
    x-effect="console.log('🔄 [FOOTER] Alpine state changed - isMediaUploading:', isMediaUploading)">
    <div wire:loading class="position-fixed top-0 start-0 w-100" style="z-index: 1050;" wire:target="save">
        <div class="progress rounded-0" style="height: 12px;">
            <div class="progress-bar progress-bar-striped progress-bar-indeterminate bg-primary"></div>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center">
        @php
            $showCancelButton = false;
            $cancelUrl = '#';
            
            try {
                $cancelRoute = $route;
                if (!Str::contains($route, '.index') && !Str::contains($route, '.items')) {
                    $cancelRoute = $route . '.index';
                }
                
                $routeParams = [];
                if (isset($tenantWidgetId)) {
                    $routeParams = [$tenantWidgetId];
                } elseif (isset($widgetId)) {
                    $routeParams = [$widgetId];
                }
                
                $cancelUrl = route($cancelRoute, $routeParams);
                $showCancelButton = true;
            } catch (\Exception $e) {
                $showCancelButton = false;
            }
        @endphp
        
        @if($showCancelButton)
        <a href="{{ $cancelUrl }}" class="btn btn-link text-decoration-none">{{ __('admin.cancel') }}</a>
        @else
        <div></div>
        @endif

        <div class="d-flex gap-2">
            @if($modelId)
            <button type="button" class="btn save-button"
                wire:click="save(false, false)"
                wire:loading.attr="disabled"
                wire:target="save"
                x-bind:disabled="isMediaUploading"
                x-bind:class="{ 'opacity-50': isMediaUploading }"
                x-on:click="console.log('🖱️ [BUTTON] Clicked! isMediaUploading:', isMediaUploading)">
                <span class="d-flex align-items-center">
                    <span class="ms-2" wire:loading.remove wire:target="save(false, false)" x-show="!isMediaUploading">
                        <i class="fa-thin fa-plus me-2"></i> {{ __('admin.save_and_continue') }}
                    </span>
                    <span class="ms-2" wire:loading wire:target="save(false, false)" x-show="!isMediaUploading">
                        <i class="fa-duotone fa-solid fa-spinner fa-spin me-2"></i> {{ __('admin.save_and_continue') }}
                    </span>
                    <span class="ms-2" x-show="isMediaUploading">
                        <i class="fa-duotone fa-solid fa-spinner fa-spin me-2"></i> {{ __('admin.save_and_continue') }}
                    </span>
                </span>
            </button>
            @else
            <button type="button" class="btn save-button"
                wire:click="save(false, true)"
                wire:loading.attr="disabled"
                wire:target="save"
                x-bind:disabled="isMediaUploading"
                x-bind:class="{ 'opacity-50': isMediaUploading }"
                x-on:click="console.log('🖱️ [BUTTON] Clicked! isMediaUploading:', isMediaUploading)">
                <span class="d-flex align-items-center">
                    <span class="ms-2" wire:loading.remove wire:target="save(false, true)" x-show="!isMediaUploading">
                        <i class="fa-thin fa-plus me-2"></i> {{ __('admin.save_and_new') }}
                    </span>
                    <span class="ms-2" wire:loading wire:target="save(false, true)" x-show="!isMediaUploading">
                        <i class="fa-duotone fa-solid fa-spinner fa-spin me-2"></i> {{ __('admin.save_and_new') }}
                    </span>
                    <span class="ms-2" x-show="isMediaUploading">
                        <i class="fa-duotone fa-solid fa-spinner fa-spin me-2"></i> {{ __('admin.save_and_new') }}
                    </span>
                </span>
            </button>
            @endif

            <button type="button" class="btn btn-primary ms-4 save-button"
                wire:click="save(true, false)"
                wire:loading.attr="disabled"
                wire:target="save"
                x-bind:disabled="isMediaUploading"
                x-bind:class="{ 'opacity-50': isMediaUploading }"
                x-on:click="console.log('🖱️ [BUTTON KAYDET] Clicked! isMediaUploading:', isMediaUploading)">
                <span class="d-flex align-items-center">
                    <span class="ms-2" wire:loading.remove wire:target="save(true, false)" x-show="!isMediaUploading">
                        <i class="fa-thin fa-floppy-disk me-2"></i> {{ __('admin.save') }}
                    </span>
                    <span class="ms-2" wire:loading wire:target="save(true, false)" x-show="!isMediaUploading">
                        <i class="fa-duotone fa-solid fa-spinner fa-spin me-2"></i> {{ __('admin.save') }}
                    </span>
                    <span class="ms-2" x-show="isMediaUploading">
                        <i class="fa-duotone fa-solid fa-spinner fa-spin me-2"></i> {{ __('admin.save') }}
                    </span>
                </span>
            </button>
        </div>
    </div>
</div>
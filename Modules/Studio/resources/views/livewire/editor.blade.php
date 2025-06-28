<div>
    <textarea id="html-content" style="display:none;">{!! $content !!}</textarea>
    <textarea id="css-content" style="display:none;">{!! $css !!}</textarea>
    <textarea id="js-content" style="display:none;">{!! $js !!}</textarea>
    
    <div class="editor-main">
        <div class="panel__left">
            <div class="panel-tabs">
                <div class="panel-tab active" data-tab="blocks">
                    <div class="tab-icon-container">
                        <i class="fa fa-cubes tab-icon"></i>
                    </div>
                    <span class="tab-text">{{ __('studio::admin.components') }}</span>
                </div>
                <div class="panel-tab" data-tab="layers">
                    <div class="tab-icon-container">
                        <i class="fa fa-layer-group tab-icon"></i>
                    </div>
                    <span class="tab-text">{{ __('studio::admin.layers') }}</span>
                </div>
            </div>
            
            <div class="panel-tab-content active" data-tab-content="blocks">
                <div class="blocks-search">
                    <input type="text" id="blocks-search" class="form-control" placeholder="{{ __('studio::admin.search_components') }}">
                </div>
                <div id="blocks-container" class="blocks-container"></div>
            </div>
            
            <div class="panel-tab-content" data-tab-content="layers">
                <div class="blocks-search">
                    <input type="text" id="layers-search" class="form-control" placeholder="{{ __('studio::admin.search_layers') }}">
                </div>
                <div id="layers-container" class="layers-container"></div>
            </div>
        </div>
        
        <div class="editor-canvas">
            <div id="gjs" data-module-type="{{ $moduleType }}" data-module-id="{{ $moduleId }}"></div>
        </div>

        <div class="panel__right">
            <div class="panel-tabs">
                <div class="panel-tab active" data-tab="configure">
                    <div class="tab-icon-container">
                        <i class="fa fa-cogs tab-icon"></i>
                    </div>
                    <span class="tab-text">{{ __('studio::admin.configure') }}</span>
                </div>
                <div class="panel-tab" data-tab="design">
                    <div class="tab-icon-container">
                        <i class="fa fa-paint-brush tab-icon"></i>
                    </div>
                    <span class="tab-text">{{ __('studio::admin.design') }}</span>
                </div>
            </div>
            
            <div class="panel-tab-content active" data-tab-content="configure">
                <div id="text-editor-container">
                </div>
                
                <div id="element-properties-container">
                    <div id="traits-container" class="traits-container"></div>
                </div>
            </div>
            
            <div class="panel-tab-content" data-tab-content="design">
                <div id="element-styles-container">
                    <div id="styles-container" class="styles-container"></div>
                </div>
            </div>
        </div>
    </div>
</div>
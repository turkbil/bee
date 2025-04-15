<div>
    <!-- İçerik editörü alanlarını sakla -->
    <textarea id="html-content" style="display:none;">{!! $content !!}</textarea>
    <textarea id="css-content" style="display:none;">{!! $css !!}</textarea>
    <textarea id="js-content" style="display:none;">{!! $js !!}</textarea>
    
    <div class="editor-main">
        <!-- Sol Panel: Tab'lı Panel -->
        <div class="panel__left">
            <div class="panel-tabs">
                <div class="panel-tab active" data-tab="blocks">
                    <i class="fa fa-cubes tab-icon"></i>
                    <span class="tab-text">Bileşenler</span>
                </div>
                <div class="panel-tab" data-tab="styles">
                    <i class="fa fa-paint-brush tab-icon"></i>
                    <span class="tab-text">Stiller</span>
                </div>
                <div class="panel-tab" data-tab="layers">
                    <i class="fa fa-layer-group tab-icon"></i>
                    <span class="tab-text">Katmanlar</span>
                </div>
            </div>
            
            <!-- Bileşenler İçeriği -->
            <div class="panel-tab-content active" data-tab-content="blocks">
                <div class="blocks-search">
                    <input type="text" id="blocks-search" class="form-control form-control-sm" placeholder="Bileşen ara...">
                </div>
                
                <!-- GrapesJS blok konteynerı -->
                <div id="blocks-container" class="blocks-container"></div>
            </div>
            
            <!-- Stiller İçeriği -->
            <div class="panel-tab-content" data-tab-content="styles">
                <div id="traits-container" class="traits-container"></div>
                <div id="styles-container" class="styles-container"></div>
            </div>
            
            <!-- Katmanlar İçeriği -->
            <div class="panel-tab-content" data-tab-content="layers">
                <div id="layers-container" class="layers-container"></div>
            </div>
        </div>
        
        <!-- Orta Panel: Canvas -->
        <div class="editor-canvas">
            <div id="gjs" data-module-type="{{ $moduleType }}" data-module-id="{{ $moduleId }}"></div>
        </div>
    </div>
</div>
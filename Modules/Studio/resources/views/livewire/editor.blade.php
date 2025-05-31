<div>
    <!-- İçerik editörü alanlarını sakla -->
    <textarea id="html-content" style="display:none;">{!! $content !!}</textarea>
    <textarea id="css-content" style="display:none;">{!! $css !!}</textarea>
    <textarea id="js-content" style="display:none;">{!! $js !!}</textarea>
    
    <div class="editor-main">
        <!-- Sol Panel: Bileşenler ve Katmanlar -->
        <div class="panel__left">
            <div class="panel-tabs">
                <div class="panel-tab active" data-tab="blocks">
                    <div class="tab-icon-container">
                        <i class="fa fa-cubes tab-icon"></i>
                    </div>
                    <span class="tab-text">Bileşenler</span>
                </div>
                <div class="panel-tab" data-tab="layers">
                    <div class="tab-icon-container">
                        <i class="fa fa-layer-group tab-icon"></i>
                    </div>
                    <span class="tab-text">Katmanlar</span>
                </div>
            </div>
            
            <!-- Bileşenler İçeriği -->
            <div class="panel-tab-content active" data-tab-content="blocks">
                <div class="blocks-search">
                    <input type="text" id="blocks-search" class="form-control" placeholder="Bileşen ara...">
                </div>
                <div id="blocks-container" class="blocks-container"></div>
            </div>
            
            <!-- Katmanlar İçeriği -->
            <div class="panel-tab-content" data-tab-content="layers">
                <div class="blocks-search">
                    <input type="text" id="layers-search" class="form-control" placeholder="Katman ara...">
                </div>
                <div id="layers-container" class="layers-container"></div>
            </div>
        </div>
        
        <!-- Orta Panel: Canvas -->
        <div class="editor-canvas">
            <div id="gjs" data-module-type="{{ $moduleType }}" data-module-id="{{ $moduleId }}"></div>
        </div>

        <!-- Sağ Panel: Yapılandır ve Tasarla -->
        <div class="panel__right">
            <div class="panel-tabs">
                <div class="panel-tab active" data-tab="configure">
                    <div class="tab-icon-container">
                        <i class="fa fa-cogs tab-icon"></i>
                    </div>
                    <span class="tab-text">Yapılandır</span>
                </div>
                <div class="panel-tab" data-tab="design">
                    <div class="tab-icon-container">
                        <i class="fa fa-paint-brush tab-icon"></i>
                    </div>
                    <span class="tab-text">Tasarla</span>
                </div>
            </div>
            
            <!-- Yapılandır İçeriği -->
            <div class="panel-tab-content active" data-tab-content="configure">
                <!-- Text Editor Textarea - sadece text seçiliyse görünür -->
                <div id="text-editor-container" style="display: none;">
                    <div class="p-3 border-bottom">
                        <label class="form-label">Metin İçeriği</label>
                        <textarea id="text-content-editor" class="form-control" rows="3" placeholder="Metin içeriğini düzenleyin..."></textarea>
                    </div>
                </div>
                
                <!-- Element Özellikleri -->
                <div id="element-properties-container">
                    <div id="traits-container" class="traits-container"></div>
                </div>
            </div>
            
            <!-- Tasarla İçeriği -->
            <div class="panel-tab-content" data-tab-content="design">
                <div id="element-styles-container">
                    <div id="styles-container" class="styles-container"></div>
                </div>
            </div>
        </div>
    </div>
</div>
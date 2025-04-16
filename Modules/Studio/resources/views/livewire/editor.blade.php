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

        <!-- Sağ Panel: Element Özellikleri ve Stiller -->
        <div class="panel__right">
            <div class="panel-tabs">
                <div class="panel-tab active" data-tab="element-properties">
                    <div class="tab-icon-container">
                        <i class="fa fa-sliders-h tab-icon"></i>
                    </div>
                    <span class="tab-text">Özellikler</span>
                </div>
                <div class="panel-tab" data-tab="element-styles">
                    <div class="tab-icon-container">
                        <i class="fa fa-palette tab-icon"></i>
                    </div>
                    <span class="tab-text">Stiller</span>
                </div>
                <div class="panel-tab" data-tab="global-settings">
                    <div class="tab-icon-container">
                        <i class="fa fa-cog tab-icon"></i>
                    </div>
                    <span class="tab-text">Ayarlar</span>
                </div>
            </div>
            
            <!-- Element Özellikleri İçeriği -->
            <div class="panel-tab-content active" data-tab-content="element-properties">
                <div id="element-properties-container">
                    <div id="traits-container" class="traits-container"></div>
                </div>
            </div>
            
            <!-- Element Stiller İçeriği -->
            <div class="panel-tab-content" data-tab-content="element-styles">
                <div id="element-styles-container">
                    <div id="styles-container" class="styles-container"></div>
                </div>
            </div>
            
            <!-- Global Ayarlar İçeriği -->
            <div class="panel-tab-content" data-tab-content="global-settings">
                <div class="p-4">
                    <h5 class="mb-3">Sayfa Ayarları</h5>
                    
                    <div class="mb-3">
                        <label class="form-label">Sayfa Başlığı</label>
                        <input type="text" class="form-control" id="page-title" placeholder="Sayfa başlığı" value="{{ $pageTitle }}">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Sayfa Açıklaması</label>
                        <textarea class="form-control" id="page-description" placeholder="Sayfa açıklaması" rows="3"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label d-block">Sayfa Görünürlüğü</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="page-visibility" id="visibility-public" value="public" checked>
                            <label class="form-check-label" for="visibility-public">Herkese Açık</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="page-visibility" id="visibility-private" value="private">
                            <label class="form-check-label" for="visibility-private">Gizli</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
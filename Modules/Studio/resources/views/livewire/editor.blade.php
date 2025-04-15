<div>
    <!-- İçerik editörü alanlarını sakla - normal textarea ile (hidden field değil) -->
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
                
                <div class="blocks-container">
                    <!-- Kategorileri dinamik oluştur -->
                    @foreach($categories as $categoryKey => $category)
                    <div class="block-category">
                        <div class="block-category-header">
                            <i class="{{ $category['icon'] }}"></i>
                            <span>{{ $category['name'] }}</span>
                            <i class="fa fa-chevron-down toggle-icon"></i>
                        </div>
                        <div class="block-items">
                            <!-- Bu kategoriye ait bloklar eklenecek (JS tarafında) -->
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            
            <!-- Stiller İçeriği -->
            <div class="panel-tab-content" data-tab-content="styles">
                <!-- Özellikler sekmesi içeriği burada -->
                <div id="traits-container" class="traits-container"></div>
                
                <!-- Stil konteynerı burada -->
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
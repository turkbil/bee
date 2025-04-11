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
                <div class="panel-tab" data-tab="traits">
                    <i class="fa fa-sliders-h tab-icon"></i>
                    <span class="tab-text">Özellikler</span>
                </div>
                <div class="panel-tab" data-tab="layers">
                    <i class="fa fa-layer-group tab-icon"></i>
                    <span class="tab-text">Katmanlar</span>
                </div>
            </div>
            
            <!-- Bloklar İçeriği -->
            <div class="panel-tab-content active" data-tab-content="blocks">
                <div class="blocks-search">
                    <input type="text" id="blocks-search" class="form-control form-control-sm" placeholder="Bileşen ara...">
                </div>
                
                <div class="blocks-container">
                    <!-- Düzen Kategori -->
                    <div class="block-category">
                        <div class="block-category-header">
                            <i class="fa fa-columns"></i>
                            <span>Düzen</span>
                            <i class="fa fa-chevron-down toggle-icon"></i>
                        </div>
                        <div class="block-items">
                            <div class="block-item" draggable="true" data-block-id="section-1col" data-content="<section class=&quot;container py-5&quot;>
                <div class=&quot;row&quot;>
                    <div class=&quot;col-md-12&quot;>
                        <h2>Başlık Buraya</h2>
                        <p>İçerik buraya gelecek. Çift tıklayarak düzenleyebilirsiniz.</p>
                    </div>
                </div>
            </section>">
                                <div class="block-item-icon">
                                    <i class="fa fa-square"></i>
                                </div>
                                <div class="block-item-label">1 Sütun</div>
                            </div>
                            <div class="block-item" draggable="true" data-block-id="section-2col" data-content="<section class=&quot;container py-5&quot;>
                <div class=&quot;row&quot;>
                    <div class=&quot;col-md-6&quot;>
                        <h3>Başlık 1</h3>
                        <p>İçerik buraya gelecek. Çift tıklayarak düzenleyebilirsiniz.</p>
                    </div>
                    <div class=&quot;col-md-6&quot;>
                        <h3>Başlık 2</h3>
                        <p>İçerik buraya gelecek. Çift tıklayarak düzenleyebilirsiniz.</p>
                    </div>
                </div>
            </section>">
                                <div class="block-item-icon">
                                    <i class="fa fa-columns"></i>
                                </div>
                                <div class="block-item-label">2 Sütun</div>
                            </div>
                            <div class="block-item" draggable="true" data-block-id="section-3col" data-content="<section class=&quot;container py-5&quot;>
                <div class=&quot;row&quot;>
                    <div class=&quot;col-md-4&quot;>
                        <h3>Başlık 1</h3>
                        <p>İçerik buraya gelecek.</p>
                    </div>
                    <div class=&quot;col-md-4&quot;>
                        <h3>Başlık 2</h3>
                        <p>İçerik buraya gelecek.</p>
                    </div>
                    <div class=&quot;col-md-4&quot;>
                        <h3>Başlık 3</h3>
                        <p>İçerik buraya gelecek.</p>
                    </div>
                </div>
            </section>">
                                <div class="block-item-icon">
                                    <i class="fa fa-grip-horizontal"></i>
                                </div>
                                <div class="block-item-label">3 Sütun</div>
                            </div>
                            <div class="block-item" draggable="true" data-block-id="hero-section">
                                <div class="block-item-icon">
                                    <i class="fa fa-star"></i>
                                </div>
                                <div class="block-item-label">Hero Bölüm</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Temel Kategori -->
                    <div class="block-category">
                        <div class="block-category-header">
                            <i class="fa fa-font"></i>
                            <span>Temel</span>
                            <i class="fa fa-chevron-down toggle-icon"></i>
                        </div>
                        <div class="block-items">
                            <div class="block-item" draggable="true" data-block-id="heading">
                                <div class="block-item-icon">
                                    <i class="fa fa-heading"></i>
                                </div>
                                <div class="block-item-label">Başlık</div>
                            </div>
                            <div class="block-item" draggable="true" data-block-id="text">
                                <div class="block-item-icon">
                                    <i class="fa fa-font"></i>
                                </div>
                                <div class="block-item-label">Metin</div>
                            </div>
                            <div class="block-item" draggable="true" data-block-id="button">
                                <div class="block-item-icon">
                                    <i class="fa fa-square"></i>
                                </div>
                                <div class="block-item-label">Buton</div>
                            </div>
                            <div class="block-item" draggable="true" data-block-id="link">
                                <div class="block-item-icon">
                                    <i class="fa fa-link"></i>
                                </div>
                                <div class="block-item-label">Link</div>
                            </div>
                            <div class="block-item" draggable="true" data-block-id="image">
                                <div class="block-item-icon">
                                    <i class="fa fa-image"></i>
                                </div>
                                <div class="block-item-label">Resim</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Form Kategori -->
                    <div class="block-category">
                        <div class="block-category-header">
                            <i class="fa fa-wpforms"></i>
                            <span>Form</span>
                            <i class="fa fa-chevron-down toggle-icon"></i>
                        </div>
                        <div class="block-items">
                            <div class="block-item" draggable="true" data-block-id="form">
                                <div class="block-item-icon">
                                    <i class="fa fa-list-alt"></i>
                                </div>
                                <div class="block-item-label">Form</div>
                            </div>
                            <div class="block-item" draggable="true" data-block-id="input">
                                <div class="block-item-icon">
                                    <i class="fa fa-i-cursor"></i>
                                </div>
                                <div class="block-item-label">Input</div>
                            </div>
                            <div class="block-item" draggable="true" data-block-id="textarea">
                                <div class="block-item-icon">
                                    <i class="fa fa-align-left"></i>
                                </div>
                                <div class="block-item-label">Textarea</div>
                            </div>
                            <div class="block-item" draggable="true" data-block-id="select">
                                <div class="block-item-icon">
                                    <i class="fa fa-caret-square-down"></i>
                                </div>
                                <div class="block-item-label">Select</div>
                            </div>
                            <div class="block-item" draggable="true" data-block-id="checkbox">
                                <div class="block-item-icon">
                                    <i class="fa fa-check-square"></i>
                                </div>
                                <div class="block-item-label">Checkbox</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Stiller İçeriği -->
            <div class="panel-tab-content" data-tab-content="styles">
                <div id="styles-container" class="styles-container"></div>
            </div>
            
            <!-- Özellikler İçeriği -->
            <div class="panel-tab-content" data-tab-content="traits">
                <div id="traits-container" class="traits-container"></div>
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
    
    <!-- Arkaplan Debug Bilgisi (Geliştirici için) -->
    <div style="display:none;" id="editor-debug-info">
        <p>HTML içerik uzunluğu: {{ strlen($content) }}</p>
        <p>CSS içerik uzunluğu: {{ strlen($css) }}</p>
        <p>JS içerik uzunluğu: {{ strlen($js) }}</p>
        <p>Modül Tipi: {{ $moduleType }}</p>
        <p>Modül ID: {{ $moduleId }}</p>
    </div>
</div>
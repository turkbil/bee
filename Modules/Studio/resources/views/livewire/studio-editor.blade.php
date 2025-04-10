<div><div>
    <div style="display:none">
        <textarea id="html-content">{!! $content !!}</textarea>
        <textarea id="css-content">{!! $css !!}</textarea>
        <textarea id="js-content">{!! $js !!}</textarea>
    </div>
    
    <div class="editor-main">
        <!-- Sol Panel: Bloklar -->
        <div class="panel__left">
            <div class="blocks-search">
                <input type="text" id="blocks-search" class="form-control form-control-sm" placeholder="Bileşen ara...">
            </div>
            
            <!-- Manuel olarak kategoriler tanımlanıyor -->
            <div class="blocks-container">
                <div class="block-category">
                    <div class="block-category-header">
                        <i class="fa fa-columns"></i>
                        <span>Düzen Bileşenleri</span>
                        <i class="fa fa-chevron-down toggle-icon"></i>
                    </div>
                    <div class="block-items">
                        <div class="block-item" draggable="true" data-block-id="section-1col">
                            <div class="block-item-icon">
                                <i class="fa fa-square"></i>
                            </div>
                            <div class="block-item-label">1 Sütun</div>
                        </div>
                        <div class="block-item" draggable="true" data-block-id="section-2col">
                            <div class="block-item-icon">
                                <i class="fa fa-columns"></i>
                            </div>
                            <div class="block-item-label">2 Sütun</div>
                        </div>
                        <div class="block-item" draggable="true" data-block-id="section-3col">
                            <div class="block-item-icon">
                                <i class="fa fa-th-large"></i>
                            </div>
                            <div class="block-item-label">3 Sütun</div>
                        </div>
                    </div>
                </div>
                
                <div class="block-category">
                    <div class="block-category-header">
                        <i class="fa fa-paragraph"></i>
                        <span>Temel Bileşenler</span>
                        <i class="fa fa-chevron-down toggle-icon"></i>
                    </div>
                    <div class="block-items">
                        <div class="block-item" draggable="true" data-block-id="header">
                            <div class="block-item-icon">
                                <i class="fa fa-heading"></i>
                            </div>
                            <div class="block-item-label">Header</div>
                        </div>
                        <div class="block-item" draggable="true" data-block-id="footer">
                            <div class="block-item-icon">
                                <i class="fa fa-window-minimize"></i>
                            </div>
                            <div class="block-item-label">Footer</div>
                        </div>
                        <div class="block-item" draggable="true" data-block-id="text">
                            <div class="block-item-icon">
                                <i class="fa fa-font"></i>
                            </div>
                            <div class="block-item-label">Metin</div>
                        </div>
                        <div class="block-item" draggable="true" data-block-id="link">
                            <div class="block-item-icon">
                                <i class="fa fa-link"></i>
                            </div>
                            <div class="block-item-label">Link</div>
                        </div>
                        <div class="block-item" draggable="true" data-block-id="button">
                            <div class="block-item-icon">
                                <i class="fa fa-square"></i>
                            </div>
                            <div class="block-item-label">Buton</div>
                        </div>
                        <div class="block-item" draggable="true" data-block-id="html">
                            <div class="block-item-icon">
                                <i class="fa fa-code"></i>
                            </div>
                            <div class="block-item-label">HTML Kodu</div>
                        </div>
                    </div>
                </div>
                
                <div class="block-category">
                    <div class="block-category-header">
                        <i class="fa fa-image"></i>
                        <span>Medya Bileşenleri</span>
                        <i class="fa fa-chevron-down toggle-icon"></i>
                    </div>
                    <div class="block-items">
                        <div class="block-item" draggable="true" data-block-id="image">
                            <div class="block-item-icon">
                                <i class="fa fa-image"></i>
                            </div>
                            <div class="block-item-label">Görsel</div>
                        </div>
                        <div class="block-item" draggable="true" data-block-id="video">
                            <div class="block-item-icon">
                                <i class="fa fa-film"></i>
                            </div>
                            <div class="block-item-label">Video</div>
                        </div>
                    </div>
                </div>
                
                <div class="block-category">
                    <div class="block-category-header">
                        <i class="fa fa-bootstrap"></i>
                        <span>Bootstrap Bileşenleri</span>
                        <i class="fa fa-chevron-down toggle-icon"></i>
                    </div>
                    <div class="block-items">
                        <div class="block-item" draggable="true" data-block-id="card">
                            <div class="block-item-icon">
                                <i class="fa fa-credit-card"></i>
                            </div>
                            <div class="block-item-label">Kart</div>
                        </div>
                        <div class="block-item" draggable="true" data-block-id="jumbotron">
                            <div class="block-item-icon">
                                <i class="fa fa-bullhorn"></i>
                            </div>
                            <div class="block-item-label">Jumbotron</div>
                        </div>
                        <div class="block-item" draggable="true" data-block-id="navbar">
                            <div class="block-item-icon">
                                <i class="fa fa-bars"></i>
                            </div>
                            <div class="block-item-label">Navbar</div>
                        </div>
                        <div class="block-item" draggable="true" data-block-id="contact-form">
                            <div class="block-item-icon">
                                <i class="fa fa-envelope"></i>
                            </div>
                            <div class="block-item-label">İletişim Formu</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Orta Panel: Canvas -->
        <div class="editor-canvas">
            <div id="gjs" data-module-type="{{ $moduleType }}" data-module-id="{{ $moduleId }}"></div>
        </div>
        
        <!-- Sağ Panel: Özellikler -->
        <div class="panel__right">
            <div class="panel-tabs">
                <div class="panel-tab active" data-tab="styles">Stiller</div>
                <div class="panel-tab" data-tab="traits">Özellikler</div>
                <div class="panel-tab" data-tab="layers">Katmanlar</div>
            </div>
            
            <div class="panel-tab-content active" data-tab-content="styles">
                <div id="styles-container" class="styles-container"></div>
            </div>
            <div class="panel-tab-content" data-tab-content="traits">
                <div id="traits-container" class="traits-container"></div>
            </div>
            <div class="panel-tab-content" data-tab-content="layers">
                <div id="layers-container" class="layers-container"></div>
            </div>
        </div>
    </div>
</div></div>
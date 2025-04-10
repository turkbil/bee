<div><div>
    <div style="display:none">
        <textarea id="css-content">{!! $css !!}</textarea>
        <textarea id="js-content">{!! $js !!}</textarea>
    </div>
    
    <div class="editor-main">
        <!-- Sol Panel: Bloklar -->
        <div class="panel__left">
            <div class="blocks-search">
                <input type="text" id="blocks-search" class="form-control form-control-sm" placeholder="Bileşen ara...">
            </div>
            <div id="blocks-container" class="blocks-container"></div>
        </div>
        
        <!-- Orta Panel: Canvas -->
        <div class="editor-canvas">
            <div id="gjs"></div>
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
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const editorConfig = {
                elementId: 'gjs',
                moduleType: '{{ $moduleType }}',
                moduleId: {{ $moduleId }},
                content: `{!! str_replace(['\\', '`', '"'], ['\\\\', '\\`', '\\"'], $content) !!}`,
                css: `{!! str_replace(['\\', '`', '"'], ['\\\\', '\\`', '\\"'], $css) !!}`,
                csrfToken: '{{ csrf_token() }}'
            };
            
            // Studio.js içindeki initStudioEditor fonksiyonunu çağır
            window.initStudioEditor(editorConfig);
        });
    </script>
    
    <style>
        /* Editor Ana Stiller */
        .editor-main {
            display: flex;
            height: calc(100vh - 96px); /* Header + Toolbar yüksekliği */
            overflow: hidden;
        }
        
        /* GrapeJS düzeltmeleri */
        .panel__left {
            width: 260px !important; 
            position: relative !important;
            overflow: hidden !important;
        }
        
        .blocks-container {
            padding: 10px !important;
            overflow-y: auto !important;
            height: calc(100% - 52px) !important;
        }
        
        .panel__right {
            width: 260px !important;
            position: relative !important;
        }
        
        .styles-container,
        .traits-container,
        .layers-container {
            height: 100% !important;
            padding: 10px !important;
            overflow-y: auto !important;
        }
        
        .editor-canvas {
            flex: 1 !important;
            position: relative !important;
        }
        
        #gjs {
            height: 100% !important;
            width: 100% !important;
            overflow: hidden !important;
        }
        
        /* GrapeJS canvas düzeltmesi */
        .gjs-cv-canvas {
            top: 0 !important;
            width: 100% !important;
            height: 100% !important;
        }
        
        /* Block elementleri */
        .gjs-block {
            width: 45% !important;
            padding: 1em !important;
            margin: 5px !important;
            font-size: 12px !important;
        }
        
        /* Panel yüksekliği düzeltmesi */
        .gjs-pn-panel {
            position: relative !important;
            height: 100% !important;
        }
    </style>
</div></div>
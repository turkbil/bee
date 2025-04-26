<!-- İçerik ve Tasarım -->
<div class="tab-pane fade {{ $formMode === 'design' ? 'active show' : '' }}" id="tab-design">
    <div class="row">
        <div class="col-12">
            @if($widget['type'] === 'file')
            <div class="alert alert-info">
                <div class="d-flex">
                    <div>
                        <i class="fas fa-info-circle text-blue me-2" style="margin-top: 3px"></i>
                    </div>
                    <div>
                        <h4 class="alert-title">Hazır Dosya Kullanımı</h4>
                        <div class="text-muted">
                            Bu widget için bir blade dosyası belirtebilirsiniz. Kullanılacak dosya yolu "blocks" klasörüne göredir. Örneğin: "cards/basic" şeklinde yolu belirtin.
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mb-4">
                <label class="form-label">Hazır Dosya Seçin</label>
                <select class="form-select mb-2 @error('widget.file_path') is-invalid @enderror" wire:model="widget.file_path">
                    <option value="">Seçim Yapın</option>
                    @php
                        $blocksPath = resource_path('views/blocks');
                        $modulePath = $blocksPath . '/modules';
                        
                        // Blocks altındaki tüm klasörleri (modules hariç) tarayalım
                        $blockDirs = [];
                        
                        if (is_dir($blocksPath)) {
                            $dirs = new DirectoryIterator($blocksPath);
                            foreach ($dirs as $dir) {
                                if ($dir->isDir() && !$dir->isDot() && $dir->getFilename() !== 'modules') {
                                    $categoryPath = $dir->getPathname();
                                    $categoryName = $dir->getFilename();
                                    
                                    $subDirs = new DirectoryIterator($categoryPath);
                                    foreach ($subDirs as $subDir) {
                                        if ($subDir->isDir() && !$subDir->isDot()) {
                                            $subDirName = $subDir->getFilename();
                                            $viewPath = $categoryName . '/' . $subDirName;
                                            
                                            if (file_exists($subDir->getPathname() . '/view.blade.php')) {
                                                $blockDirs[$categoryName][] = [
                                                    'path' => $viewPath,
                                                    'name' => $subDirName
                                                ];
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    @endphp
                    
                    @foreach($blockDirs as $category => $blocks)
                        <optgroup label="{{ ucfirst($category) }}">
                            @foreach($blocks as $block)
                                <option value="{{ $block['path'] }}">{{ ucfirst($category) }} - {{ ucfirst($block['name']) }}</option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
                @error('widget.file_path') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            
            <div class="form-floating mb-4">
                <input type="text" 
                    wire:model="widget.file_path" 
                    class="form-control font-monospace @error('widget.file_path') is-invalid @enderror"
                    id="file-path"
                    placeholder="Örnek: cards/basic">
                <label for="file-path">View Dosya Yolu</label>
                <div class="form-hint">
                    <i class="fas fa-info-circle me-1 text-blue"></i>
                    Dosya yolu, "resources/views/blocks/" klasörüne göre belirtilir.
                </div>
                @error('widget.file_path') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            @elseif($widget['type'] === 'module')
            <div class="alert alert-info">
                <div class="d-flex">
                    <div>
                        <i class="fas fa-info-circle text-blue me-2" style="margin-top: 3px"></i>
                    </div>
                    <div>
                        <h4 class="alert-title">Modül Dosya Kullanımı</h4>
                        <div class="text-muted">
                            Bu widget için bir modül dosyası belirtebilirsiniz. Kullanılacak dosya yolu "blocks/modules" klasörüne göredir. Örneğin: "portfolio/recent" şeklinde yolu belirtin.
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mb-4">
                <label class="form-label">Hazır Modül Seçin</label>
                <select class="form-select mb-2 @error('widget.file_path') is-invalid @enderror" wire:model="widget.file_path">
                    <option value="">Seçim Yapın</option>
                    @php
                        $modulePath = resource_path('views/blocks/modules');
                        $moduleDirs = [];
                        
                        if (is_dir($modulePath)) {
                            $dirs = new DirectoryIterator($modulePath);
                            foreach ($dirs as $dir) {
                                if ($dir->isDir() && !$dir->isDot()) {
                                    $moduleName = $dir->getFilename();
                                    $subDirs = new DirectoryIterator($dir->getPathname());
                                    
                                    foreach ($subDirs as $subDir) {
                                        if ($subDir->isDir() && !$subDir->isDot()) {
                                            $viewName = $subDir->getFilename();
                                            $viewPath = $moduleName . '/' . $viewName;
                                            
                                            if (file_exists($subDir->getPathname() . '/view.blade.php')) {
                                                $moduleDirs[$moduleName][] = [
                                                    'path' => $viewPath,
                                                    'name' => $viewName
                                                ];
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    @endphp
                    
                    @foreach($moduleDirs as $module => $views)
                        <optgroup label="{{ ucfirst($module) }}">
                            @foreach($views as $view)
                                <option value="{{ $view['path'] }}">{{ ucfirst($module) }} - {{ ucfirst($view['name']) }}</option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
                @error('widget.file_path') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            
            <div class="form-floating mb-4">
                <input type="text" 
                    wire:model="widget.file_path" 
                    class="form-control font-monospace @error('widget.file_path') is-invalid @enderror"
                    id="module-path"
                    placeholder="Örnek: portfolio/recent">
                <label for="module-path">Modül Dosya Yolu</label>
                <div class="form-hint">
                    <i class="fas fa-info-circle me-1 text-blue"></i>
                    Dosya yolu, "resources/views/blocks/modules/" klasörüne göre belirtilir.
                </div>
                @error('widget.file_path') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            @else
            <div class="alert alert-info mb-4">
                <div class="d-flex">
                    <div>
                        <i class="fas fa-info-circle text-blue me-2" style="margin-top: 3px"></i>
                    </div>
                    <div>
                        <h4 class="alert-title">Şablon Değişkenleri</h4>
                        <div class="text-muted">
                            <strong>Değişkenler:</strong> <code>&lbrace;&lbrace; değişken_adı &rbrace;&rbrace;</code> şeklinde kullanın<br>
                            <strong>Dinamik içerikler:</strong> <code>&lbrace;&lbrace; #each items &rbrace;&rbrace;...&lbrace;&lbrace; /each &rbrace;&rbrace;</code> bloklarında<br>
                            <strong>Koşullu içerik:</strong> <code>&lbrace;&lbrace; #if değişken &rbrace;&rbrace;...&lbrace;&lbrace; else &rbrace;&rbrace;...&lbrace;&lbrace; /if &rbrace;&rbrace;</code> şeklinde
                        </div>
                    </div>
                </div>
            </div>
            
            @if(!empty($widget['settings_schema']) || !empty($widget['item_schema']))
            <div class="alert alert-success mb-4">
                <div class="d-flex">
                    <div>
                        <i class="fas fa-check-circle text-green me-2" style="margin-top: 3px"></i>
                    </div>
                    <div>
                        <h4 class="alert-title">Kullanılabilir Değişkenler</h4>
                        <div class="text-muted">
                            @if(!empty($widget['settings_schema']))
                            <strong>Özelleştirme Değişkenleri:</strong><br>
                            <ul class="mb-2">
                                @foreach($widget['settings_schema'] as $field)
                                <li><code>&lbrace;&lbrace; {{ $field['name'] }} &rbrace;&rbrace;</code> - {{ $field['label'] }}</li>
                                @endforeach
                            </ul>
                            @endif
                            
                            @if($widget['has_items'] && !empty($widget['item_schema']))
                            <strong>İçerik Değişkenleri (items döngüsünde):</strong><br>
                            <ul class="mb-0">
                                @foreach($widget['item_schema'] as $field)
                                <li><code>&lbrace;&lbrace; {{ $field['name'] }} &rbrace;&rbrace;</code> - {{ $field['label'] }}</li>
                                @endforeach
                            </ul>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endif
            
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-html5 text-primary me-1"></i> HTML İçeriği
                    </h3>
                </div>
                <div class="card-body">
                    <textarea 
                        wire:model="widget.content_html" 
                        class="form-control font-monospace @error('widget.content_html') is-invalid @enderror" 
                        rows="12"
                        style="font-size: 14px;"
                        placeholder="<div class=&quot;my-widget&quot;>Widget içeriği...</div>">{{ $widget['content_html'] }}</textarea>
                    @error('widget.content_html') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-css3-alt text-info me-1"></i> CSS Kodu
                    </h3>
                </div>
                <div class="card-body">
                    <textarea 
                        wire:model="widget.content_css" 
                        class="form-control font-monospace @error('widget.content_css') is-invalid @enderror" 
                        rows="8"
                        style="font-size: 14px;"
                        placeholder=".my-widget { padding: 20px; background-color: @{{background_color}}; }">{{ $widget['content_css'] }}</textarea>
                    @error('widget.content_css') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fab fa-js-square text-warning me-1"></i> JavaScript Kodu
                    </h3>
                </div>
                <div class="card-body">
                    <textarea 
                        wire:model="widget.content_js" 
                        class="form-control font-monospace @error('widget.content_js') is-invalid @enderror" 
                        rows="8"
                        style="font-size: 14px;"
                        placeholder="document.addEventListener('DOMContentLoaded', function() { // JS Kodu });">{{ $widget['content_js'] }}</textarea>
                    @error('widget.content_js') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
            @endif
            
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h4 class="card-title mb-0">CSS Dosyaları</h4>
                                <button type="button" class="btn btn-sm btn-primary" wire:click="addCssFile">
                                    <i class="fas fa-plus me-1"></i> Ekle
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            @if(empty($widget['css_files']) || count($widget['css_files']) === 0)
                            <div class="text-center py-3 text-muted">
                                <i class="fas fa-file-code fa-2x mb-2"></i>
                                <p>Henüz CSS dosyası eklenmedi.</p>
                            </div>
                            @else
                            <div class="list-group">
                                @foreach($widget['css_files'] as $index => $cssFile)
                                <div class="list-group-item d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-link"></i>
                                            </span>
                                            <input type="text" 
                                                class="form-control form-control-sm @error('widget.css_files.'.$index) is-invalid @enderror" 
                                                wire:model="widget.css_files.{{ $index }}" 
                                                placeholder="https://site.com/style.css">
                                            @error('widget.css_files.'.$index) <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-danger ms-2" wire:click="removeCssFile({{ $index }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                @endforeach
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h4 class="card-title mb-0">JavaScript Dosyaları</h4>
                                <button type="button" class="btn btn-sm btn-primary" wire:click="addJsFile">
                                    <i class="fas fa-plus me-1"></i> Ekle
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            @if(empty($widget['js_files']) || count($widget['js_files']) === 0)
                            <div class="text-center py-3 text-muted">
                                <i class="fas fa-file-code fa-2x mb-2"></i>
                                <p>Henüz JavaScript dosyası eklenmedi.</p>
                            </div>
                            @else
                            <div class="list-group">
                                @foreach($widget['js_files'] as $index => $jsFile)
                                <div class="list-group-item d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-link"></i>
                                            </span>
                                            <input type="text" 
                                                class="form-control form-control-sm @error('widget.js_files.'.$index) is-invalid @enderror" 
                                                wire:model="widget.js_files.{{ $index }}" 
                                                placeholder="https://site.com/script.js">
                                            @error('widget.js_files.'.$index) <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-danger ms-2" wire:click="removeJsFile({{ $index }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                @endforeach
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
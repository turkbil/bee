<div>
    @include('widgetmanagement::helper')
    @include('admin.partials.error_message')
        
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body py-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <h3 class="mb-0 me-4">{{ $widget['name'] }} {{ __('widgetmanagement::admin.code_editor_title') }}</h3>
                                <span class="badge fs-6 px-3 py-2">{{ ucfirst($widget['type']) }}</span>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.widgetmanagement.manage', $widgetId) }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>
                                    {{ __('widgetmanagement::admin.back') }}
                                </a>
                                <a href="{{ route('admin.widgetmanagement.form-builder.edit', ['widgetId' => $widgetId, 'schemaType' => 'settings']) }}" 
                                   class="btn btn-outline-info" target="_blank">
                                    <i class="fas fa-sliders-h me-2"></i>
                                    {{ __('widgetmanagement::admin.customization_settings') }}
                                </a>
                                @if($widget['has_items'])
                                <a href="{{ route('admin.widgetmanagement.form-builder.edit', ['widgetId' => $widgetId, 'schemaType' => 'items']) }}" 
                                   class="btn btn-outline-info" target="_blank">
                                    <i class="fas fa-list me-2"></i>
                                    {{ __('widgetmanagement::admin.content_settings') }}
                                </a>
                                @endif
                                <a href="{{ route('admin.widgetmanagement.preview.template', $widgetId) }}" 
                                   class="btn btn-outline-info" target="_blank">
                                    <i class="fas fa-eye me-2"></i>
                                    {{ __('widgetmanagement::admin.preview') }}
                                </a>

                                {{-- {{ __('widgetmanagement::admin.save_code') }} Butonu Başlangıcı --}}
                                        <div class="d-flex justify-content-end align-items-center ms-2"> {{-- ms-2 eklendi --}}
                                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="save" form="widget-form">
                                                <span wire:loading.remove wire:target="save">
                                                    <i class="fas fa-save me-2"></i>
                                                    {{ __('widgetmanagement::admin.save_code') }}
                                                </span>
                                                <span wire:loading wire:target="save">
                                                    <i class="fas fa-spinner fa-spin me-2"></i>
                                                    {{ __('widgetmanagement::admin.saving') }}
                                                </span>
                                            </button>
                                        </div>
                                {{-- {{ __('widgetmanagement::admin.save_code') }} Butonu Sonu --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <form wire:submit.prevent="save" id="widget-form">
            <div class="row">
                <div class="col-12">
                    <div class="card" id="editor-card">
                        <div class="card-header">
                            <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs">
                                <li class="nav-item">
                                    <a href="#html-pane" class="nav-link active" data-bs-toggle="tab">
                                        <i class="fab fa-html5 me-2"></i>
                                        {{ __('widgetmanagement::admin.html') }}
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#css-pane" class="nav-link" data-bs-toggle="tab">
                                        <i class="fab fa-css3-alt me-2"></i>
                                        {{ __('widgetmanagement::admin.css') }}
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#js-pane" class="nav-link" data-bs-toggle="tab">
                                        <i class="fab fa-js-square me-2"></i>
                                        {{ __('widgetmanagement::admin.javascript') }}
                                    </a>
                                </li>
                                <li class="nav-item ms-auto">
                                    <a href="#" class="nav-link" title="{{ __('widgetmanagement::admin.format_code') }}" onclick="editorActions.formatCode(); return false;">
                                        <i class="fas fa-indent"></i>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#" class="nav-link" title="{{ __('widgetmanagement::admin.find_replace') }}" onclick="editorActions.openFind(); return false;">
                                        <i class="fas fa-search"></i>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#" class="nav-link" title="{{ __('widgetmanagement::admin.fold_all') }}" onclick="editorActions.toggleFoldAll(); return false;">
                                        <i class="fas fa-compress-alt"></i>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#" class="nav-link" title="{{ __('widgetmanagement::admin.change_theme') }}" onclick="editorActions.toggleTheme(); return false;">
                                        <i class="fas fa-palette"></i>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#" class="nav-link" title="{{ __('widgetmanagement::admin.fullscreen') }}" onclick="editorActions.toggleFullscreen(); return false;">
                                        <i class="fas fa-expand" id="fullscreen-icon"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="html-pane">
                                    <div class="mb-3" wire:ignore>
                                        <div id="html-editor" style="height: 600px; width: 100%;"></div>
                                        <textarea wire:model.defer="widget.content_html" id="html-textarea" style="display: none;"></textarea>
                                    </div>
                                </div>
                                
                                <div class="tab-pane fade" id="css-pane">
                                    <div class="mb-3" wire:ignore>
                                        <div id="css-editor" style="height: 600px; width: 100%;"></div>
                                        <textarea wire:model.defer="widget.content_css" id="css-textarea" style="display: none;"></textarea>
                                    </div>
                                </div>
                                
                                <div class="tab-pane fade" id="js-pane">
                                    <div class="mb-3" wire:ignore>
                                        <div id="js-editor" style="height: 600px; width: 100%;"></div>
                                        <textarea wire:model.defer="widget.content_js" id="js-textarea" style="display: none;"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> 
                </div> 
            </div> 
            
            <div class="card mt-3" id="external-files-section">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-file-code me-2"></i>
                        {{ __('widgetmanagement::admin.external_files') }}
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h4 class="card-title mb-0">
                                            <i class="fab fa-css3-alt me-2"></i>
                                            {{ __('widgetmanagement::admin.css_files') }}
                                        </h4>
                                        <button type="button" class="btn btn-sm btn-primary" wire:click.prevent="addCssFile">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    @if(empty($widget['css_files']) || count($widget['css_files']) === 0)
                                    <div class="text-center py-3 ">
                                        <i class="fab fa-css3-alt fa-2x mb-2"></i>
                                        <p class="mb-0">{{ __('widgetmanagement::admin.no_css_files_yet') }}</p>
                                    </div>
                                    @else
                                    @foreach($widget['css_files'] as $index => $cssFile)
                                    <div class="input-group mb-2">
                                        <span class="input-group-text">
                                            <i class="fas fa-link"></i>
                                        </span>
                                        <input type="text" 
                                            class="form-control" 
                                            wire:model.defer="widget.css_files.{{ $index }}" 
                                            placeholder="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
                                        <button type="button" class="btn btn-outline-danger" wire:click="removeCssFile({{ $index }})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h4 class="card-title mb-0">
                                            <i class="fab fa-js-square me-2"></i>
                                            {{ __('widgetmanagement::admin.javascript_files') }}
                                        </h4>
                                        <button type="button" class="btn btn-sm btn-primary" wire:click.prevent="addJsFile">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    @if(empty($widget['js_files']) || count($widget['js_files']) === 0)
                                    <div class="text-center py-3 ">
                                        <i class="fab fa-js-square fa-2x mb-2"></i>
                                        <p class="mb-0">{{ __('widgetmanagement::admin.no_js_files_yet') }}</p>
                                    </div>
                                    @else
                                    @foreach($widget['js_files'] as $index => $jsFile)
                                    <div class="input-group mb-2">
                                        <span class="input-group-text">
                                            <i class="fas fa-link"></i>
                                        </span>
                                        <input type="text" 
                                            class="form-control" 
                                            wire:model.defer="widget.js_files.{{ $index }}" 
                                            placeholder="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js">
                                        <button type="button" class="btn btn-outline-danger" wire:click="removeJsFile({{ $index }})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </form>
    
        @php
            $variables = $this->getAvailableVariables();
        @endphp
        
        @if(isset($variables['settings']) || isset($variables['items']))
        <div class="row my-4">
            @if(isset($variables['settings']))
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-sliders-h me-2"></i>
                            {{ __('widgetmanagement::admin.customization_variables') }}
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm mb-0 variable-table">
                                @foreach($variables['settings'] as $key => $setting)
                                <tr>
                                    <td class="variable-code" data-copy="&#123;&#123;{{ $setting['name'] }}&#125;&#125;" style="width: 60%;">
                                        <code class="copyable-code">&#123;&#123;{{ $setting['name'] }}&#125;&#125;</code>
                                    </td>
                                    <td style="width: 40%;">{{ $setting['label'] }}</td>
                                </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            
            @if(isset($variables['items']))
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-layer-group me-2"></i>
                            {{ __('widgetmanagement::admin.content_variables') }}
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive mb-2">
                            <table class="table table-sm mb-0 variable-table">
                                @foreach($variables['items'] as $key => $item)
                                <tr>
                                    <td class="variable-code" data-copy="&#123;&#123;{{ $item['name'] }}&#125;&#125;" style="width: 60%;">
                                        <code class="copyable-code">&#123;&#123;{{ $item['name'] }}&#125;&#125;</code>
                                    </td>
                                    <td class="" style="width: 40%;">{{ $item['label'] }}</td>
                                </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-magic me-2"></i>
                            {{ __('widgetmanagement::admin.handlebars_loops') }}
                        </h4>
                        <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#handlebarsModal">
                            <i class="fas fa-info-circle"></i>
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm mb-0 variable-table">
                                <tr>
                                    <td class="variable-code" data-copy="&#123;&#123;#each items&#125;&#125;" style="width: 60%;">
                                        <code class="copyable-code">&#123;&#123;#each items&#125;&#125;</code>
                                    </td>
                                    <td class="" style="width: 40%;">{{ __('widgetmanagement::admin.loop_start') }}</td>
                                </tr>
                                <tr>
                                    <td class="variable-code" data-copy="&#123;&#123;/each&#125;&#125;" style="width: 60%;">
                                        <code class="copyable-code">&#123;&#123;/each&#125;&#125;</code>
                                    </td>
                                    <td class="" style="width: 40%;">{{ __('widgetmanagement::admin.loop_end') }}</td>
                                </tr>
                                <tr>
                                    <td class="variable-code" data-copy="&#123;&#123;@index&#125;&#125;" style="width: 60%;">
                                        <code class="copyable-code">&#123;&#123;@index&#125;&#125;</code>
                                    </td>
                                    <td class="" style="width: 40%;">{{ __('widgetmanagement::admin.order_number') }}</td>
                                </tr>
                                <tr>
                                    <td class="variable-code" data-copy="&#123;&#123;@first&#125;&#125;" style="width: 60%;">
                                        <code class="copyable-code">&#123;&#123;@first&#125;&#125;</code>
                                    </td>
                                    <td class="" style="width: 40%;">{{ __('widgetmanagement::admin.first_element') }}</td>
                                </tr>
                                <tr>
                                    <td class="variable-code" data-copy="&#123;&#123;@last&#125;&#125;" style="width: 60%;">
                                        <code class="copyable-code">&#123;&#123;@last&#125;&#125;</code>
                                    </td>
                                    <td class="" style="width: 40%;">{{ __('widgetmanagement::admin.last_element') }}</td>
                                </tr>
                                <tr>
                                    <td class="variable-code" data-copy="&#123;&#123;#if kullanici_aktif&#125;&#125;&#10;&#9;&lt;p&gt;Hoş geldiniz!&lt;/p&gt;&#10;&#123;&#123;else&#125;&#125;&#10;&#9;&lt;p&gt;Lütfen giriş yapın.&lt;/p&gt;&#10;&#123;&#123;/if&#125;&#125;" style="width: 60%;">
                                        <code class="copyable-code">&#123;&#123;#if active&#125;&#125; <br /> ... &#123;&#123;else&#125;&#125; ... <br /> &#123;&#123;/if&#125;&#125;</code>
                                    </td>
                                    <td class="" style="width: 40%;">{{ __('widgetmanagement::admin.conditional_expression') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    
        <div class="modal fade" id="handlebarsModal" tabindex="-1">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('widgetmanagement::admin.handlebars_usage_examples') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <ul class="nav nav-tabs" id="handlebarsTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="each-tab" data-bs-toggle="tab" data-bs-target="#each-content" type="button" role="tab">{{ __('widgetmanagement::admin.each_loop') }}</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="if-tab" data-bs-toggle="tab" data-bs-target="#if-content" type="button" role="tab">If / Else</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="unless-tab" data-bs-toggle="tab" data-bs-target="#unless-content" type="button" role="tab">Unless</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="with-tab" data-bs-toggle="tab" data-bs-target="#with-content" type="button" role="tab">With</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="helpers-tab" data-bs-toggle="tab" data-bs-target="#helpers-content" type="button" role="tab">Yardımcı Fonksiyonlar</button>
                            </li>
                        </ul>
                        
                        <div class="tab-content p-3 border border-top-0" id="handlebarsTabContent">
                            <div class="tab-pane fade show active" id="each-content" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>{{ __('widgetmanagement::admin.usage_method') }}</h6>
                                        <pre><code>// Karşılaştırma
    &#123;&#123;#if (eq type "premium")&#125;&#125;
      Premium içerik
    &#123;&#123;else if isPending&#125;&#125;
      Beklemede
    &#123;&#123;else&#125;&#125;
      Pasif
    &#123;&#123;/if&#125;&#125;
    
    // Koşullu içerik
    &#123;&#123;#if (or isAdmin isModerator)&#125;&#125;
      Yönetici paneli
    &#123;&#123;else if isPending&#125;&#125;
      Beklemede
    &#123;&#123;else&#125;&#125;
      Pasif
    &#123;&#123;/if&#125;&#125;
    
    // Formatlama
    &lt;p&gt;Tarih: &#123;&#123;formatDate createdAt "DD.MM.YYYY"&#125;&#125;&lt;/p&gt;
    &lt;p&gt;Fiyat: &#123;&#123;formatPrice price&#125;&#125;₺&lt;/p&gt;

    // Döngü
    &#123;&#123;#each items&#125;&#125;
      &lt;p&gt;&#123;&#123;this.name&#125;&#125; - &#64;index: &#123;&#123;&#64;index&#125;&#125;&lt;/p&gt;
    &#123;&#123;/each&#125;&#125;

    // Özel yardımcılar
    &#123;&#123;myCustomHelper "parametre"&#125;&#125;</code></pre>
                                        <div class="mt-3">
                                            <p class=""><code>#each</code> bloğu, koleksiyonları (diziler, nesneler) döngüye sokmaya yarar.</p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>{{ __('widgetmanagement::admin.example_output') }}</h6>
                                        <div class="border p-3 ">
                                            <div class="item mb-2">
                                                <h6>Birinci Başlık</h6>
                                                <small class="">Sıra: 0</small>
                                                <span class="badge ms-2">İlk öğe</span>
                                            </div>
                                            <div class="item mb-2">
                                                <h6>İkinci Başlık</h6>
                                                <small class="">Sıra: 1</small>
                                            </div>
                                            <div class="item">
                                                <h6>Üçüncü Başlık</h6>
                                                <small class="">Sıra: 2</small>
                                                <span class="badge ms-2">Son öğe</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="tab-pane fade" id="if-content" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>{{ __('widgetmanagement::admin.usage_method') }}</h6>
                                        <pre><code>// Karşılaştırma
    &#123;&#123;#if (eq type "premium")&#125;&#125;
      Premium içerik
    &#123;&#123;else if isPending&#125;&#125;
      Beklemede
    &#123;&#123;else&#125;&#125;
      Pasif
    &#123;&#123;/if&#125;&#125;
    
    // Koşullu içerik
    &#123;&#123;#if (or isAdmin isModerator)&#125;&#125;
      Yönetici paneli
    &#123;&#123;else if isPending&#125;&#125;
      Beklemede
    &#123;&#123;else&#125;&#125;
      Pasif
    &#123;&#123;/if&#125;&#125;
    
    // Formatlama
    &lt;p&gt;Tarih: &#123;&#123;formatDate createdAt "DD.MM.YYYY"&#125;&#125;&lt;/p&gt;
    &lt;p&gt;Fiyat: &#123;&#123;formatPrice price&#125;&#125;₺&lt;/p&gt;

    // Döngü
    &#123;&#123;#each items&#125;&#125;
      &lt;p&gt;&#123;&#123;this.name&#125;&#125; - &#64;index: &#123;&#123;&#64;index&#125;&#125;&lt;/p&gt;
    &#123;&#123;/each&#125;&#125;

    // Özel yardımcılar
    &#123;&#123;myCustomHelper "parametre"&#125;&#125;</code></pre>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>{{ __('widgetmanagement::admin.example_output') }}</h6>
                                        <div class="border p-3  mb-3">
                                            <div class=" p-2 ">
                                                <h5>Widget Başlığı</h5>
                                            </div>
                                        </div>
                                        <div class="border p-3  mb-3">
                                            <div class="d-flex flex-column gap-2">
                                                <div><span class="badge">Aktif</span> <small class="ms-2">(isActive = true)</small></div>
                                                <div><span class="badge">Beklemede</span> <small class="ms-2">(isPending = true)</small></div>
                                                <div><span class="badge">Pasif</span> <small class="ms-2">(isActive ve isPending = false)</small></div>
                                            </div>
                                        </div>
                                        <div class="border p-3 ">
                                            <p class="mb-1">Tarih: 23.05.2025</p>
                                            <p class="mb-0">Fiyat: 1.250,00₺</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="tab-pane fade" id="unless-content" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>{{ __('widgetmanagement::admin.usage_method') }}</h6>
                                        <pre><code>&#123;&#123;#unless isHidden&#125;&#125;
      &lt;div class="content"&gt;
        &lt;p&gt;Bu içerik görünür durumdadır.&lt;/p&gt;
      &lt;/div&gt;
    &#123;&#123;else&#125;&#125;
      &lt;div class="placeholder"&gt;
        &lt;p&gt;İçerik gizlenmiştir.&lt;/p&gt;
      &lt;/div&gt;
    &#123;&#123;/unless&#125;&#125;</code></pre>
                                        <div class="mt-3">
                                            <p class=""><code>#unless</code>, <code>#if</code>'in tersi olarak çalışır. Koşul <strong>false</strong> olduğunda içeriği gösterir.</p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>{{ __('widgetmanagement::admin.example_output') }}</h6>
                                        <div class="border p-3  mb-3">
                                            <h6>Koşul: isHidden = false</h6>
                                            <div class=" p-2 ">
                                                <div class="content">
                                                    <p class="mb-0">Bu içerik görünür durumdadır.</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="border p-3 ">
                                            <h6>Koşul: isHidden = true</h6>
                                            <div class=" p-2 ">
                                                <div class="placeholder">
                                                    <p class="mb-0">İçerik gizlenmiştir.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="tab-pane fade" id="with-content" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>{{ __('widgetmanagement::admin.usage_method') }}</h6>
                                        <pre><code>&#123;&#123;#with user&#125;&#125;
      &lt;div class="user-card"&gt;
        &lt;h3&gt;&#123;&#123;name&#125;&#125;&lt;/h3&gt;
        &lt;p&gt;E-posta: &#123;&#123;email&#125;&#125;&lt;/p&gt;
        &#123;&#123;#with address&#125;&#125;
          &lt;address&gt;
            &#123;&#123;street&#125;&#125;, &#123;&#123;city&#125;&#125;, &#123;&#123;country&#125;&#125;
          &lt;/address&gt;
        &#123;&#123;/with&#125;&#125;
      &lt;/div&gt;
    &#123;&#123;/with&#125;&#125;</code></pre>
                                        <div class="mt-3">
                                            <p class=""><code>#with</code> bloğu, iç içe nesnelere erişimi kolaylaştırır ve kod tekrarını azaltır.</p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>{{ __('widgetmanagement::admin.example_output') }}</h6>
                                        <div class="border p-3 ">
                                            <div class="user-card">
                                                <h5>Ahmet Yılmaz</h5>
                                                <p>E-posta: ahmet@ornek.com</p>
                                                <address>
                                                    Atatürk Cad. No:123, İstanbul, Türkiye
                                                </address>
                                            </div>
                                        </div>
                                        <div class="mt-3">
                                            <h6>Veri Yapısı:</h6>
                                            <pre><code>&#123;
      "user": &#123;
        "name": "Ahmet Yılmaz",
        "email": "ahmet@ornek.com",
        "address": &#123;
          "street": "Atatürk Cad. No:123",
          "city": "İstanbul",
          "country": "Türkiye"
        &#125;
      &#125;
    &#125;</code></pre>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="tab-pane fade" id="helpers-content" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>Yardımcı Fonksiyonlar:</h6>
                                        <pre><code>// Karşılaştırma
    &#123;&#123;#if (eq type "premium")&#125;&#125;
      Premium içerik
    &#123;&#123;else if isPending&#125;&#125;
      Beklemede
    &#123;&#123;else&#125;&#125;
      Pasif
    &#123;&#123;/if&#125;&#125;
    
    // Koşullu içerik
    &#123;&#123;#if (or isAdmin isModerator)&#125;&#125;
      Yönetici paneli
    &#123;&#123;else if isPending&#125;&#125;
      Beklemede
    &#123;&#123;else&#125;&#125;
      Pasif
    &#123;&#123;/if&#125;&#125;
    
    // Formatlama
    &lt;p&gt;Tarih: &#123;&#123;formatDate createdAt "DD.MM.YYYY"&#125;&#125;&lt;/p&gt;
    &lt;p&gt;Fiyat: &#123;&#123;formatPrice price&#125;&#125;₺&lt;/p&gt;

    // Döngü
    &#123;&#123;#each items&#125;&#125;
      &lt;p&gt;&#123;&#123;this.name&#125;&#125; - &#64;index: &#123;&#123;&#64;index&#125;&#125;&lt;/p&gt;
    &#123;&#123;/each&#125;&#125;

    // Özel yardımcılar
    &#123;&#123;myCustomHelper "parametre"&#125;&#125;</code></pre>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>{{ __('widgetmanagement::admin.example_output') }}</h6>
                                        <div class="border p-3  mb-3">
                                            <div class="premium-content">
                                                <span class="badge">Premium</span>
                                                <p class="mb-0 mt-1">Bu özel içerik sadece premium üyelere görünür.</p>
                                            </div>
                                        </div>
                                        <div class="border p-3  mb-3">
                                            <div class="d-flex flex-column gap-2">
                                                <div><span class="badge">Aktif</span> <small class="ms-2">(isActive = true)</small></div>
                                                <div><span class="badge">Beklemede</span> <small class="ms-2">(isPending = true)</small></div>
                                                <div><span class="badge">Pasif</span> <small class="ms-2">(isActive ve isPending = false)</small></div>
                                            </div>
                                        </div>
                                        <div class="border p-3 ">
                                            <p class="mb-1">Tarih: 23.05.2025</p>
                                            <p class="mb-0">Fiyat: 1.250,00₺</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a href="https://handlebarsjs.com/guide/" target="_blank" class="btn btn-sm btn-outline-secondary me-auto">
                            <i class="fas fa-external-link-alt me-1"></i> {{ __('widgetmanagement::admin.handlebars_documentation') }}
                        </a>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('widgetmanagement::admin.close') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    @push('styles')
    <link rel="stylesheet" href="{{ asset('admin-assets/libs/monaco-custom/css/monaco-custom.css') }}">
    <style>
        .variable-code {
            cursor: pointer;
            position: relative; /* Bu önemli */
        }
        .variable-code:hover {
            background-color: rgba(0, 0, 0, 0.05);
        }
        .copy-feedback {
            position: absolute;
            left: 100%; /* Elementin genişliğinin %100'ü kadar sağa */
            margin-left: 5px; /* Elementle arasında 5px boşluk */
            top: 50%;
            transform: translateY(-50%);
            background-color: #28a745; /* Başarı rengi */
            color: white;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 0.75rem; /* Biraz daha küçük */
            z-index: 1050; /* Diğer elementlerin üzerinde olması için */
            white-space: nowrap; /* Tek satırda kalması için */
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .variable-table tr:last-child td,
        .variable-table tr:last-child th {
            border-bottom-width: 0;
        }
    </style>
    @endpush
    
    @push('scripts')
    <script src="{{ asset('admin-assets/libs/monaco-custom/js/monaco-custom.js') }}"></script>
    <script src="{{ asset('admin-assets/libs/monaco-custom/js/widget-example.js') }}"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        function initMonacoEditor() {
            if (typeof MonacoCustomEditor !== 'undefined') {
                MonacoCustomEditor.init(@json($widget), @json($this->getAvailableVariables()));
            }
        }

        window.showHandlebarsModal = function() {
            const handlebarsModal = new bootstrap.Modal(document.getElementById('handlebarsModal'));
            handlebarsModal.show();
        };
        
        // Kopyalama işlevi
        document.querySelectorAll('.variable-code').forEach(el => {
            el.addEventListener('click', function() {
                const textToCopy = this.getAttribute('data-copy');
                navigator.clipboard.writeText(textToCopy).then(() => {
                    showCopyFeedback(this);
                }).catch(err => {
                    console.error('Kopyalama başarısız oldu: ', err);
                });
            });
        });
        
        function showCopyFeedback(element) {
            // Mevcut bir feedback varsa kaldır
            const existingFeedback = element.querySelector('.copy-feedback');
            if (existingFeedback) {
                existingFeedback.remove();
            }

            const feedback = document.createElement('div');
            feedback.className = 'copy-feedback';
            feedback.textContent = '{{ __('widgetmanagement::admin.copied') }}';
            element.appendChild(feedback);
            
            setTimeout(() => {
                if (feedback.parentNode) {
                    feedback.parentNode.removeChild(feedback);
                }
            }, 1200); // Biraz daha uzun süre kalsın
        }

        // Monaco editörünü başlat
        setTimeout(initMonacoEditor, 200);
    });
    
    // Düzenleyiciyi güncelleme fonksiyonu
    window.updateWidgetEditors = function(newData) {
        if (typeof MonacoCustomEditor !== 'undefined') {
            MonacoCustomEditor.updateEditorValues(newData);
        }
    };
    
    // Livewire navigasyon sonrası yeniden başlatma
    document.addEventListener('livewire:navigated', function() {
        setTimeout(() => {
            if (typeof MonacoCustomEditor !== 'undefined') {
                MonacoCustomEditor.init(@json($widget), @json($this->getAvailableVariables()));
            }
        }, 300);
    });
    </script>
    @endpush
</div>
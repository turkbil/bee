@php
$theme = null;
if (function_exists('tenant') && tenant() && tenant()->theme_id) {
    $theme = \Modules\ThemeManagement\App\Models\Theme::find(tenant()->theme_id);
}

if (!$theme) {
    $theme = \Modules\ThemeManagement\App\Models\Theme::where('is_default', true)->first();
    if (!$theme) {
        $theme = new \Modules\ThemeManagement\App\Models\Theme([
            'name' => 'blank',
            'folder_name' => 'blank'
        ]);
    }
}

$themeFolder = $theme->folder_name ?? 'blank';

preg_match_all('/<link[^>]+href=[\'"]([^\'"]+)[\'"][^>]*>/i', $widget->content_html ?? '', $cssMatches);
$cssFiles = !empty($cssMatches[1]) ? $cssMatches[1] : [];

preg_match_all('/<script[^>]+src=[\'"]([^\'"]+)[\'"][^>]*><\/script>/i', $widget->content_html ?? '', $jsMatches);
$jsFiles = !empty($jsMatches[1]) ? $jsMatches[1] : [];
@endphp

@include("themes.{$themeFolder}.layouts.header")

<script src="https://cdn.jsdelivr.net/npm/handlebars@4.7.8/dist/handlebars.min.js"></script>

<div class="preview-header">
    <div class="container-fluid flex justify-between items-center">
        <h1 class="text-lg font-semibold m-0">{{ $widget->name }} Önizleme</h1>
        <div>
            <button class="px-3 py-1 text-sm border border-white text-white rounded hover:bg-white hover:text-blue-600 transition-colors" onclick="window.close()">
                <svg xmlns="http://www.w3.org/2000/svg" class="inline-block w-4 h-4 mr-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg> Kapat
            </button>
        </div>
    </div>
</div>

<div class="preview-container">
    <div class="device-switcher">
        <button class="device-btn px-3 py-2 bg-white border border-blue-500 text-blue-600 rounded-md hover:bg-blue-50 active" data-device="desktop" onclick="setPreviewSize('desktop')">
            <svg xmlns="http://www.w3.org/2000/svg" class="inline-block w-4 h-4 mr-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                <line x1="8" y1="21" x2="16" y2="21"></line>
                <line x1="12" y1="17" x2="12" y2="21"></line>
            </svg> Masaüstü
        </button>
        <button class="device-btn px-3 py-2 bg-white border border-gray-300 text-gray-600 rounded-md hover:bg-gray-50" data-device="tablet" onclick="setPreviewSize('tablet')">
            <svg xmlns="http://www.w3.org/2000/svg" class="inline-block w-4 h-4 mr-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="4" y="2" width="16" height="20" rx="2" ry="2"></rect>
                <line x1="12" y1="18" x2="12" y2="18"></line>
            </svg> Tablet
        </button>
        <button class="device-btn px-3 py-2 bg-white border border-gray-300 text-gray-600 rounded-md hover:bg-gray-50" data-device="mobile" onclick="setPreviewSize('mobile')">
            <svg xmlns="http://www.w3.org/2000/svg" class="inline-block w-4 h-4 mr-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="5" y="2" width="14" height="20" rx="2" ry="2"></rect>
                <line x1="12" y1="18" x2="12" y2="18"></line>
            </svg> Mobil
        </button>
        <div class="ml-auto flex items-center">
            <button class="theme-btn p-2 bg-white border border-gray-300 text-gray-600 rounded-md hover:bg-gray-50 mr-2" data-theme="light" onclick="setThemeMode('light')" id="light-mode-btn" title="Gündüz Modu">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="5"></circle>
                    <line x1="12" y1="1" x2="12" y2="3"></line>
                    <line x1="12" y1="21" x2="12" y2="23"></line>
                    <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                    <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                    <line x1="1" y1="12" x2="3" y2="12"></line>
                    <line x1="21" y1="12" x2="23" y2="12"></line>
                    <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
                    <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
                </svg>
            </button>
            <button class="theme-btn p-2 bg-white border border-gray-300 text-gray-600 rounded-md hover:bg-gray-50" data-theme="dark" onclick="setThemeMode('dark')" id="dark-mode-btn" title="Gece Modu">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                </svg>
            </button>
        </div>
    </div>
    
    <div class="bg-blue-50 border border-blue-200 p-4 rounded-md mb-4 text-blue-800 dark:bg-blue-900 dark:border-blue-700 dark:text-blue-200">
        <div class="flex">
            <div class="mr-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="16" x2="12" y2="12"></line>
                    <line x1="12" y1="8" x2="12.01" y2="8"></line>
                </svg>
            </div>
            <div>
                <strong>Önizleme Bilgileri:</strong><br>
                <strong>Tür:</strong> {{ ucfirst($widget->type) }}<br>
                <strong>Açıklama:</strong> {{ $widget->description ?? 'Açıklama bulunmuyor' }}<br>
            </div>
        </div>
    </div>
    
    <div class="preview-frame" id="preview-frame">
        <div class="preview-content dark:bg-gray-800 dark:text-white dark:border-gray-700">
            @if($widget->type == 'module' && empty($widget->file_path))
                <div class="bg-yellow-100 border border-yellow-400 p-4 rounded-md text-yellow-800 dark:bg-yellow-800 dark:border-yellow-600 dark:text-yellow-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="inline-block w-5 h-5 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                        <line x1="12" y1="9" x2="12" y2="13"></line>
                        <line x1="12" y1="17" x2="12.01" y2="17"></line>
                    </svg>
                    Bu modül bileşeni için HTML şablonu tanımlanmamış. Lütfen widget'ı düzenleyin ve bir HTML şablonu ekleyin.
                </div>
            @elseif($widget->type == 'module' && !empty($widget->file_path))
                @include($widget->file_path, ['settings' => $widget->settings, 'items' => $widget->items ?? []])
            @elseif($widget->type == 'file')
                @include('widgetmanagement::blocks.' . $widget->file_path, ['settings' => $context ?? []])
            @elseif(empty($renderedHtml))
                <div class="bg-gray-100 border border-gray-300 p-8 rounded-md text-gray-600 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-16 h-16 mx-auto mb-4 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                        <circle cx="8.5" cy="8.5" r="1.5"></circle>
                        <polyline points="21,15 16,10 5,21"></polyline>
                    </svg>
                    <h3 class="text-lg font-medium mb-2">Widget İçeriği Boş</h3>
                    <p class="text-sm">Bu widget için işlenmiş HTML içeriği bulunamadı.</p>
                    <p class="text-xs mt-2 text-gray-500">Widget Türü: <strong>{{ ucfirst($widget->type) }}</strong></p>
                </div>
            @else
                <div id="widget-container">{!! $renderedHtml !!}</div>
            @endif
        </div>
    </div>
    
    <div class="mt-4 flex justify-between items-center">
        <div>
            <span class="bg-blue-500 text-white px-2 py-1 rounded-full text-xs me-2">{{ ucfirst($widget->type) }}</span>
            
            @if($widget->has_items)
            <span class="bg-orange-500 text-white px-2 py-1 rounded-full text-xs me-2">Dinamik İçerik</span>
            @endif
            
            <span class="bg-green-500 text-white px-2 py-1 rounded-full text-xs">{{ $widget->category->title ?? 'Kategori Yok' }}</span>
        </div>
        
        @if($widget->type == 'module')
        <a href="{{ route('admin.widgetmanagement.modules') }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="inline-block w-4 h-4 mr-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg> Listeye Dön
        </a>
        @elseif(auth()->user()->hasRole('root'))
        <a href="{{ route('admin.widgetmanagement.manage', $widget->id) }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="inline-block w-4 h-4 mr-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
            </svg> Widget'ı Düzenle
        </a>
        @endif
    </div>
</div>

<script>
    function setPreviewSize(device) {
        const frame = document.getElementById('preview-frame');
        const buttons = document.querySelectorAll('.device-btn');
        
        buttons.forEach(btn => {
            btn.classList.remove('active', 'border-blue-500', 'text-blue-600');
            btn.classList.add('border-gray-300', 'text-gray-600');
        });
        
        const activeBtn = document.querySelector(`.device-btn[data-device="${device}"]`);
        activeBtn.classList.add('active', 'border-blue-500', 'text-blue-600');
        activeBtn.classList.remove('border-gray-300', 'text-gray-600');
        
        frame.className = 'preview-frame';
        if (device !== 'desktop') {
            frame.classList.add(device);
        }
    }
    
    function setThemeMode(mode) {
        const html = document.documentElement;
        const lightBtn = document.getElementById('light-mode-btn');
        const darkBtn = document.getElementById('dark-mode-btn');
        
        document.querySelectorAll('.theme-btn').forEach(btn => {
            btn.classList.remove('border-blue-500', 'text-blue-600');
            btn.classList.add('border-gray-300', 'text-gray-600');
        });
        
        if (mode === 'dark') {
            html.classList.add('dark');
            localStorage.setItem('darkMode', 'dark');
            
            darkBtn.classList.add('border-blue-500', 'text-blue-600');
            darkBtn.classList.remove('border-gray-300', 'text-gray-600');
        } else {
            html.classList.remove('dark');
            localStorage.setItem('darkMode', 'light');
            
            lightBtn.classList.add('border-blue-500', 'text-blue-600');
            lightBtn.classList.remove('border-gray-300', 'text-gray-600');
        }
    }
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const widgetContainer = document.getElementById('widget-container');
    
    if (widgetContainer) {
        const externalJsFiles = {!! json_encode($jsFiles ?? []) !!};
        const widgetInlineJs = {!! json_encode($widgetJs ?? '') !!};
        
        function loadScriptsSequentially(urls, finalCallback) {
            if (!urls || urls.length === 0) {
                if (finalCallback) finalCallback();
                return;
            }
            const url = urls.shift();
            const script = document.createElement('script');
            script.src = url;
            script.onload = () => loadScriptsSequentially(urls, finalCallback);
            script.onerror = () => {
                console.error('Failed to load script:', url);
                loadScriptsSequentially(urls, finalCallback); // Continue with next
            };
            document.body.appendChild(script);
        }

        loadScriptsSequentially(externalJsFiles.slice(), function() {
            if(widgetInlineJs) {
                const scriptElement = document.createElement('script');
                scriptElement.textContent = widgetInlineJs;
                document.body.appendChild(scriptElement);
            }
        });
    }
});
</script>

<style>
    .preview-header {
        background-color: #206bc4;
        color: white;
        padding: 0.75rem;
        position: sticky;
        top: 0;
        z-index: 100;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    
    .preview-container {
        max-width: 1200px;
        margin: 1.5rem auto;
        padding: 1rem;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    
    .dark .preview-container {
        background-color: #1f2937;
        color: #f3f4f6;
        box-shadow: 0 1px 3px rgba(0,0,0,0.3);
    }
    
    .preview-content {
        border: 1px solid #e6e7e9;
        border-radius: 4px;
        min-height: 200px;
        margin: 1rem 0;
        padding: 1rem;
    }
    
    .device-switcher {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
    }
    
    .preview-frame {
        transition: width 0.3s ease;
        margin: 0 auto;
        width: 100%;
    }
    
    .preview-frame.mobile {
        max-width: 375px;
    }
    
    .preview-frame.tablet {
        max-width: 768px;
    }
    
    .dark .device-btn.active,
    .dark .theme-btn.border-blue-500 {
        background-color: #374151;
        border-color: #60a5fa;
        color: #93c5fd;
    }
    
    .device-btn.active,
    .theme-btn.border-blue-500 {
        background-color: #f0f9ff;
    }
    
    .device-btn:hover,
    .theme-btn:hover {
        background-color: #f0f9ff;
    }
    
    .dark .device-btn:hover,
    .dark .theme-btn:hover {
        background-color: #374151;
        color: #93c5fd;
    }
</style>

@include("themes.{$themeFolder}.layouts.footer")
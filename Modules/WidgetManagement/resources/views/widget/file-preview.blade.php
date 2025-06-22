@php
// Theme_id'ye göre theme'i bul
$theme = null;
if (function_exists('tenant') && tenant() && tenant()->theme_id) {
    $theme = \Modules\ThemeManagement\App\Models\Theme::find(tenant()->theme_id);
}

// Theme bulunamadıysa varsayılan olarak blank temasını kullan
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
@endphp

@include("themes.{$themeFolder}.layouts.header")

<div class="preview-header">
    <div class="container-fluid flex justify-between items-center">
        <h1 class="text-lg font-semibold m-0">{{ $widget->name }} - Hazır Dosya Önizleme</h1>
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
                <strong>Dosya Bilgileri:</strong><br>
                <strong>Dosya Yolu:</strong> {{ $viewPath }}<br>
                <strong>Açıklama:</strong> {{ $widget->description }}
            </div>
        </div>
    </div>
    
    <div class="preview-frame" id="preview-frame">
        <div class="preview-content dark:bg-gray-800 dark:text-white dark:border-gray-700">
            
            @include('widgetmanagement::blocks.' . $widget->file_path, ['settings' => $settings])
        </div>
    </div>
    
    <div class="mt-4 flex justify-between items-center">
        <div>
            <span class="bg-blue-500 text-white px-2 py-1 rounded-full text-xs me-2">{{ ucfirst($widget->type) }}</span>
            <span class="bg-green-500 text-white px-2 py-1 rounded-full text-xs">{{ $widget->category->title ?? 'Kategori Yok' }}</span>
        </div>
        <a href="{{ route('admin.widgetmanagement.files') }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="inline-block w-4 h-4 mr-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg> Listeye Dön
        </a>
    </div>
</div>


<script>
    function setPreviewSize(device) {
        const frame = document.getElementById('preview-frame');
        const buttons = document.querySelectorAll('.device-btn');
        
        // Aktif butonları temizle
        buttons.forEach(btn => {
            btn.classList.remove('active', 'border-blue-500', 'text-blue-600');
            btn.classList.add('border-gray-300', 'text-gray-600');
        });
        
        // Aktif butonu işaretle
        const activeBtn = document.querySelector(`.device-btn[data-device="${device}"]`);
        activeBtn.classList.add('active', 'border-blue-500', 'text-blue-600');
        activeBtn.classList.remove('border-gray-300', 'text-gray-600');
        
        // Frame boyutunu ayarla
        frame.className = 'preview-frame';
        if (device !== 'desktop') {
            frame.classList.add(device);
        }
    }
    
    function setThemeMode(mode) {
        const html = document.documentElement;
        const lightBtn = document.getElementById('light-mode-btn');
        const darkBtn = document.getElementById('dark-mode-btn');
        
        // Tüm tema butonlarının stillerini sıfırla
        document.querySelectorAll('.theme-btn').forEach(btn => {
            btn.classList.remove('border-blue-500', 'text-blue-600');
            btn.classList.add('border-gray-300', 'text-gray-600');
        });
        
        if (mode === 'dark') {
            html.classList.add('dark');
            localStorage.setItem('darkMode', 'dark');
            
            // Aktif buton stillerini güncelle
            darkBtn.classList.add('border-blue-500', 'text-blue-600');
            darkBtn.classList.remove('border-gray-300', 'text-gray-600');
        } else {
            html.classList.remove('dark');
            localStorage.setItem('darkMode', 'light');
            
            // Aktif buton stillerini güncelle
            lightBtn.classList.add('border-blue-500', 'text-blue-600');
            lightBtn.classList.remove('border-gray-300', 'text-gray-600');
        }
    }
    
    // Sayfa yüklendiğinde tema modunu ayarla
    document.addEventListener('DOMContentLoaded', function() {
        const currentMode = localStorage.getItem('darkMode') || 'light';
        setThemeMode(currentMode);
        
        // Aktif device butonunu ayarla
        const buttons = document.querySelectorAll('.device-btn');
        buttons.forEach(btn => {
            if (btn.classList.contains('active')) {
                const device = btn.getAttribute('data-device');
                setPreviewSize(device);
            }
        });
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
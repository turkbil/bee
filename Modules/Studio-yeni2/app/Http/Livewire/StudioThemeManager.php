<?php

namespace Modules\Studio\App\Http\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Modules\Studio\App\Services\StudioThemeService;
use Illuminate\Support\Facades\Log;
use Livewire\WithFileUploads;

#[Layout('admin.layout')]
class StudioThemeManager extends Component
{
    use WithFileUploads;
    
    public $themes = [];
    public $selectedTheme = null;
    public $themeSettings = [];
    public $headerTemplates = [];
    public $footerTemplates = [];
    public $search = '';
    public $screenshot = null;
    
    /**
     * Bileşen monte ediliyor
     */
    public function mount()
    {
        $this->loadThemes();
    }
    
    /**
     * Temaları yükle
     */
    protected function loadThemes()
    {
        try {
            $themeService = app(StudioThemeService::class);
            $this->themes = $themeService->getAllThemes();
        } catch (\Exception $e) {
            Log::error('Tema yüklenirken hata: ' . $e->getMessage());
            $this->themes = [];
        }
    }
    
    /**
     * Tema seç
     * @param int $themeId
     */
    public function selectTheme($themeId)
    {
        try {
            if (!class_exists('Modules\ThemeManagement\App\Models\Theme')) {
                $this->dispatch('toast', [
                    'title' => 'Hata',
                    'message' => 'Tema Yönetim modülü bulunamadı',
                    'type' => 'error'
                ]);
                return;
            }
            
            $theme = \Modules\ThemeManagement\App\Models\Theme::find($themeId);
            
            if (!$theme) {
                $this->dispatch('toast', [
                    'title' => 'Hata',
                    'message' => 'Tema bulunamadı',
                    'type' => 'error'
                ]);
                return;
            }
            
            $this->selectedTheme = $theme;
            
            // Tema şablonlarını yükle
            $themeService = app(StudioThemeService::class);
            $templates = $themeService->getTemplatesForTheme($theme->folder_name);
            
            $this->headerTemplates = $templates['headers'] ?? [];
            $this->footerTemplates = $templates['footers'] ?? [];
            
            // Tema ayarlarını yükle
            $this->themeSettings = $theme->settings ?? [];
            
        } catch (\Exception $e) {
            Log::error('Tema seçilirken hata: ' . $e->getMessage());
            $this->dispatch('toast', [
                'title' => 'Hata',
                'message' => 'Tema seçilirken bir hata oluştu: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }
    
    /**
     * Tema ayarlarını kaydet
     */
    public function saveThemeSettings()
    {
        try {
            if (!$this->selectedTheme) {
                $this->dispatch('toast', [
                    'title' => 'Hata',
                    'message' => 'Lütfen bir tema seçin',
                    'type' => 'error'
                ]);
                return;
            }
            
            // Tema ayarlarını güncelle
            $this->selectedTheme->settings = $this->themeSettings;
            
            // Ekran görüntüsü yüklenmişse
            if ($this->screenshot) {
                $path = $this->screenshot->store('themes/screenshots', 'public');
                $this->selectedTheme->screenshot = $path;
            }
            
            $this->selectedTheme->save();
            
            // Tema önbelleğini temizle
            app(StudioThemeService::class)->clearCache();
            
            // Temaları yeniden yükle
            $this->loadThemes();
            
            $this->dispatch('toast', [
                'title' => 'Başarılı',
                'message' => 'Tema ayarları başarıyla kaydedildi',
                'type' => 'success'
            ]);
            
            // Ekran görüntüsünü sıfırla
            $this->screenshot = null;
            
        } catch (\Exception $e) {
            Log::error('Tema ayarları kaydedilirken hata: ' . $e->getMessage());
            $this->dispatch('toast', [
                'title' => 'Hata',
                'message' => 'Tema ayarları kaydedilirken bir hata oluştu: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }
    
    /**
     * Tema varsayılan olarak ayarla
     * @param int $themeId
     */
    public function setDefaultTheme($themeId)
    {
        try {
            if (!class_exists('Modules\ThemeManagement\App\Models\Theme')) {
                $this->dispatch('toast', [
                    'title' => 'Hata',
                    'message' => 'Tema Yönetim modülü bulunamadı',
                    'type' => 'error'
                ]);
                return;
            }
            
            // Tüm temaları varsayılan olmayan olarak güncelle
            \Modules\ThemeManagement\App\Models\Theme::where('is_default', true)
                ->update(['is_default' => false]);
            
            // Seçili temayı varsayılan olarak ayarla
            $theme = \Modules\ThemeManagement\App\Models\Theme::find($themeId);
            
            if (!$theme) {
                $this->dispatch('toast', [
                    'title' => 'Hata',
                    'message' => 'Tema bulunamadı',
                    'type' => 'error'
                ]);
                return;
            }
            
            $theme->is_default = true;
            $theme->save();
            
            // Tema önbelleğini temizle
            app(StudioThemeService::class)->clearCache();
            
            // Temaları yeniden yükle
            $this->loadThemes();
            
            $this->dispatch('toast', [
                'title' => 'Başarılı',
                'message' => 'Varsayılan tema başarıyla değiştirildi',
                'type' => 'success'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Varsayılan tema ayarlanırken hata: ' . $e->getMessage());
            $this->dispatch('toast', [
                'title' => 'Hata',
                'message' => 'Varsayılan tema ayarlanırken bir hata oluştu: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }
    
    /**
     * Filtrelenmiş temalar
     */
    public function getFilteredThemesProperty()
    {
        if (empty($this->search)) {
            return $this->themes;
        }
        
        $search = strtolower($this->search);
        
        return array_filter($this->themes, function($theme) use ($search) {
            return stripos($theme['name'], $search) !== false || 
                   stripos($theme['title'], $search) !== false || 
                   stripos($theme['description'], $search) !== false;
        });
    }
    
    /**
     * Görünümü oluştur
     */
    public function render()
    {
        return view('studio::livewire.studio-theme-manager', [
            'filteredThemes' => $this->getFilteredThemesProperty()
        ]);
    }
}
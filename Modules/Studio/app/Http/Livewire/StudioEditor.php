<?php

namespace Modules\Studio\App\Http\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Modules\Studio\App\Services\StudioManagerService;
use Modules\Studio\App\Services\StudioContentParserService;
use Illuminate\Support\Facades\Log;

#[Layout('studio::layouts.editor')]
class StudioEditor extends Component
{
    public $module;
    public $moduleId;
    public $content;
    public $css;
    public $js;
    public $pageTitle;
    public $editorData = [];
    public $rawHtml;
    public $debug = false;
    
    public function mount(string $module, int $id, bool $debug = false)
    {
        $this->module = $module;
        $this->moduleId = (int)$id;
        $this->debug = $debug;
        
        // HTML içeriğini doğrudan yükle
        $this->loadContentRaw();
        
        // Editor verilerini yükle
        $this->loadEditorData();
        
        // Studio Editor açılma olayını tetikle
        event(new \Modules\Studio\Events\StudioEditorOpened($module, $id));
    }
    
    /**
     * İçeriği doğrudan yükle
     */
    protected function loadContentRaw()
    {
        // Modül tipine göre içeriği yükle
        switch ($this->module) {
            case 'page':
                $this->loadPageContentRaw();
                break;
            default:
                session()->flash('error', 'Desteklenmeyen modül: ' . $this->module);
                return redirect()->route('admin.dashboard')->with('error', 'Desteklenmeyen modül: ' . $this->module);
        }
    }
    
    /**
     * Sayfa içeriğini doğrudan yükle
     */
    protected function loadPageContentRaw()
    {
        try {
            if (!class_exists('Modules\Page\App\Models\Page')) {
                throw new \Exception('Page modülü bulunamadı.');
            }
            
            $page = \Modules\Page\App\Models\Page::findOrFail($this->moduleId);
            
            // İçeriği doğrudan al, hiçbir işlem yapmadan
            $this->content = $page->body ?? '';
            $this->css = $page->css ?? '';
            $this->js = $page->js ?? '';
            $this->pageTitle = $page->title ?? 'Sayfa Düzenleyici';
            $this->rawHtml = $page->body ?? '';
            
            // Eğer içerik boşsa varsayılan içerik oluştur
            if (empty($this->content)) {
                $this->content = $this->getDefaultHtml();
                $this->rawHtml = $this->getDefaultHtml();
            }
            
            // Debug için log yaz
            Log::debug('Studio Editor - Sayfa İçeriği Yüklendi', [
                'page_id' => $this->moduleId,
                'title' => $this->pageTitle,
                'content_length' => strlen($this->content),
                'css_length' => strlen($this->css),
                'js_length' => strlen($this->js),
                'content_excerpt' => substr($this->content, 0, 200),
            ]);
            
        } catch (\Exception $e) {
            Log::error('Sayfa yüklenirken hata: ' . $e->getMessage());
            session()->flash('error', 'Sayfa bulunamadı: ' . $e->getMessage());
            return redirect()->route('admin.page.index')->with('error', 'Sayfa bulunamadı.');
        }
    }
    
    /**
     * Editör verilerini yükle
     */
    protected function loadEditorData()
    {
        try {
            $managerService = app('studio.manager');
            $this->editorData = $managerService->prepareEditorData($this->module, $this->moduleId);
        } catch (\Exception $e) {
            Log::error('Editör verileri yüklenirken hata: ' . $e->getMessage(), [
                'module' => $this->module,
                'moduleId' => $this->moduleId,
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $this->editorData = [
                'themes' => [],
                'widgets' => [],
                'settings' => [],
                'templates' => [],
                'editor_config' => [],
            ];
        }
    }
    
    /**
     * Varsayılan HTML içeriği
     */
    protected function getDefaultHtml()
    {
        return '<div class="container py-5">
            <div class="row">
                <div class="col-md-12">
                    <h2>Hoş Geldiniz</h2>
                    <p>Studio Editör ile sayfanızı düzenlemeye başlayabilirsiniz. Sol taraftaki bileşenleri sürükleyip bırakarak içerik ekleyebilirsiniz.</p>
                    <div class="alert alert-info mt-4">
                        <i class="fas fa-info-circle me-2"></i> Düzenlemelerinizi kaydetmek için sağ üstteki <strong>Kaydet</strong> butonunu kullanın.
                    </div>
                </div>
            </div>
        </div>';
    }
    
    /**
     * İçeriği kaydet
     */
    public function save()
    {
        // Modül tipine göre içeriği kaydet
        switch ($this->module) {
            case 'page':
                return $this->savePage();
            default:
                session()->flash('error', 'Desteklenmeyen modül: ' . $this->module);
                return redirect()->route('admin.dashboard')->with('error', 'Desteklenmeyen modül: ' . $this->module);
        }
    }
    
    /**
     * Sayfa içeriğini kaydet
     */
    protected function savePage()
    {
        try {
            if (!class_exists('Modules\Page\App\Models\Page')) {
                throw new \Exception('Page modülü bulunamadı.');
            }
            
            $page = \Modules\Page\App\Models\Page::findOrFail($this->moduleId);
            
            // İçeriği doğrudan kullan
            $page->body = $this->content;
            $page->css = $this->css;
            $page->js = $this->js;
            $page->save();
            
            // Aktivite kaydı
            if (function_exists('activity')) {
                activity()
                    ->performedOn($page)
                    ->withProperties(['studio' => true])
                    ->log('studio ile düzenlendi');
            }
            
            // İçerik kaydedilme olayını tetikle
            event(new \Modules\Studio\Events\StudioContentSaved($this->module, $this->moduleId, [
                'title' => $page->title,
                'content_length' => strlen($this->content),
                'css_length' => strlen($this->css),
                'js_length' => strlen($this->js)
            ]));
            
            session()->flash('message', 'Sayfa başarıyla kaydedildi.');
            return redirect()->route('admin.page.index');
            
        } catch (\Exception $e) {
            Log::error('Sayfa kaydedilirken hata: ' . $e->getMessage());
            session()->flash('error', 'Sayfa kaydedilirken hata: ' . $e->getMessage());
            return redirect()->route('admin.page.index')->with('error', 'Sayfa kaydedilirken hata oluştu.');
        }
    }
    
    /**
     * Temayı değiştir
     */
    public function changeTheme(string $theme)
    {
        try {
            $themeService = app('studio.theme');
            $result = $themeService->changeTheme($this->module, $this->moduleId, $theme);
            
            if ($result) {
                $this->loadEditorData();
                $this->dispatch('theme-changed', theme: $theme);
                $this->dispatch('notify', type: 'success', message: 'Tema başarıyla değiştirildi.');
            } else {
                $this->dispatch('notify', type: 'error', message: 'Tema değiştirilemedi.');
            }
        } catch (\Exception $e) {
            Log::error('Tema değiştirilirken hata: ' . $e->getMessage());
            $this->dispatch('notify', type: 'error', message: 'Tema değiştirilirken hata oluştu: ' . $e->getMessage());
        }
    }
    
    /**
     * Render
     */
    public function render()
    {
        // EditorData'yı daha iyi hazırlayalım
        if (empty($this->editorData)) {
            $this->editorData = [
                'themes' => [],
                'widgets' => [],
                'settings' => [
                    'theme' => 'default'
                ],
                'templates' => [],
                'editor_config' => [],
            ];
        }
        
        return view('studio::livewire.studio-editor', [
            'pageTitle' => $this->pageTitle,
            'moduleType' => $this->module,
            'moduleId' => $this->moduleId,
            'content' => $this->content,
            'css' => $this->css,
            'js' => $this->js,
            'editorData' => $this->editorData,
            'rawHtml' => $this->rawHtml,
        ]);
    }
}
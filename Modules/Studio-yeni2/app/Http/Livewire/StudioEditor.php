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
    
    public function mount(string $module, int $id)
    {
        $this->module = $module;
        $this->moduleId = (int)$id;
        
        $this->loadContent();
        $this->loadEditorData();
        
        // Studio Editor açılma olayını tetikle
        event(new \Modules\Studio\Events\StudioEditorOpened($module, $id));
    }
    
    /**
     * İçeriği yükle
     */
    protected function loadContent()
    {
        // Modül tipine göre içeriği yükle
        switch ($this->module) {
            case 'page':
                $this->loadPageContent();
                break;
            default:
                session()->flash('error', 'Desteklenmeyen modül: ' . $this->module);
                return redirect()->route('admin.dashboard')->with('error', 'Desteklenmeyen modül: ' . $this->module);
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
     * Sayfa içeriğini yükle
     */
    protected function loadPageContent()
    {
        try {
            if (!class_exists('Modules\Page\App\Models\Page')) {
                throw new \Exception('Page modülü bulunamadı.');
            }
            
            $page = \Modules\Page\App\Models\Page::findOrFail($this->moduleId);
            
            // İçerik parser servisini kullan
            $parserService = app('studio.parser');
            
            $this->content = $page->body ?? '';
            $this->css = $page->css ?? '';
            $this->js = $page->js ?? '';
            $this->pageTitle = $page->title ?? 'Sayfa Düzenleyici';
            
            // Eğer içerik boşsa varsayılan içerik koy
            if (empty($this->content)) {
                $this->content = $parserService->getDefaultHtml();
            }
            
            // Debug için log yaz
            Log::debug('Studio Editor - Sayfa İçeriği Yüklendi', [
                'page_id' => $this->moduleId,
                'title' => $this->pageTitle,
                'content_length' => strlen($this->content),
                'css_length' => strlen($this->css),
                'js_length' => strlen($this->js),
            ]);
            
        } catch (\Exception $e) {
            Log::error('Sayfa yüklenirken hata: ' . $e->getMessage());
            session()->flash('error', 'Sayfa bulunamadı: ' . $e->getMessage());
            return redirect()->route('admin.page.index')->with('error', 'Sayfa bulunamadı.');
        }
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
            
            // İçerik parser servisi ile içeriği temizle
            $parserService = app('studio.parser');
            $preparedContent = $parserService->prepareContentForSave($this->content, $this->css, $this->js);
            
            $page->body = $preparedContent['html'];
            $page->css = $preparedContent['css'];
            $page->js = $preparedContent['js'];
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
        ]);
    }
}
<?php

namespace Modules\Studio\App\Http\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Modules\Studio\App\Services\StudioWidgetService;
use Modules\Studio\App\Services\StudioThemeService;
use Modules\Page\App\Models\Page;

#[Layout('studio::layouts.editor')]
class StudioEditor extends Component
{
    public $module;
    public $moduleId;
    public $content;
    public $css;
    public $js;
    public $pageTitle;
    public $widgets = [];
    public $themes = [];
    public $templateHeaders = [];
    public $templateFooters = [];
    
    public function mount($module, $id)
    {
        $this->module = $module;
        $this->moduleId = $id;
        
        $this->loadContent();
        $this->loadWidgets();
        $this->loadThemes();
    }
    
    protected function loadContent()
    {
        if ($this->module === 'page') {
            try {
                $page = Page::findOrFail($this->moduleId);
                $this->content = $page->body ?? '';
                $this->css = $page->css ?? '';
                $this->js = $page->js ?? '';
                $this->pageTitle = $page->title ?? 'Sayfa Düzenleyici';
            } catch (\Exception $e) {
                session()->flash('error', 'Sayfa bulunamadı: ' . $e->getMessage());
                return redirect()->route('admin.page.index')->with('error', 'Sayfa bulunamadı.');
            }
        } else {
            session()->flash('error', 'Desteklenmeyen modül: ' . $this->module);
            return redirect()->route('admin.dashboard')->with('error', 'Desteklenmeyen modül: ' . $this->module);
        }
    }
    
    protected function loadWidgets()
    {
        $widgetService = app(StudioWidgetService::class);
        $this->widgets = $widgetService->getAllWidgets();
    }
    
    protected function loadThemes()
    {
        $themeService = app(StudioThemeService::class);
        $this->themes = $themeService->getAllThemes();
        
        $defaultTheme = $themeService->getDefaultTheme();
        $themeName = $defaultTheme ? $defaultTheme['folder_name'] : 'blank';
        
        $templates = $themeService->getHeaderFooterTemplates($themeName);
        $this->templateHeaders = $templates['headers'];
        $this->templateFooters = $templates['footers'];
    }
    
    public function save()
    {
        if ($this->module === 'page') {
            try {
                $page = Page::findOrFail($this->moduleId);
                $page->body = $this->content;
                $page->css = $this->css;
                $page->js = $this->js;
                $page->save();
                
                if (function_exists('log_activity')) {
                    log_activity($page, 'studio ile düzenlendi');
                }
                
                session()->flash('message', 'Sayfa başarıyla kaydedildi.');
                return redirect()->route('admin.page.index');
            } catch (\Exception $e) {
                session()->flash('error', 'Sayfa kaydedilirken hata: ' . $e->getMessage());
                return redirect()->route('admin.page.index')->with('error', 'Sayfa kaydedilirken hata oluştu.');
            }
        }
        
        session()->flash('error', 'Desteklenmeyen modül: ' . $this->module);
        return redirect()->route('admin.dashboard')->with('error', 'Desteklenmeyen modül: ' . $this->module);
    }
    
    public function render()
    {
        return view('studio::livewire.studio-editor', [
            'pageTitle' => $this->pageTitle,
            'moduleType' => $this->module,
            'moduleId' => $this->moduleId,
            'content' => $this->content,
            'css' => $this->css,
            'js' => $this->js,
            'widgets' => $this->widgets,
            'themes' => $this->themes,
            'templateHeaders' => $this->templateHeaders,
            'templateFooters' => $this->templateFooters,
        ]);
    }
}
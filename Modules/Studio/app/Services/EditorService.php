<?php

namespace Modules\Studio\App\Services;

use Illuminate\Support\Facades\Config;
use Modules\Page\App\Models\Page;

class EditorService
{
    /**
     * Editör yapılandırmasını al
     *
     * @return array
     */
    public function getConfig(): array
    {
        return [
            'cdn' => config('studio.editor.cdn', ['enabled' => false, 'version' => '0.21.8']),
            'panels' => config('studio.editor.panels', [
                'blocks' => true,
                'styles' => true,
                'layers' => true,
                'traits' => true,
            ]),
            'devices' => config('studio.editor.devices', [
                'desktop' => [
                    'width' => '',
                    'name' => 'Masaüstü',
                ],
                'tablet' => [
                    'width' => '768px',
                    'widthMedia' => '992px',
                    'name' => 'Tablet',
                ],
                'mobile' => [
                    'width' => '320px',
                    'widthMedia' => '480px',
                    'name' => 'Mobil',
                ],
            ]),
            'canvas' => $this->getCanvasConfig(),
        ];
    }
    
    /**
     * Canvas yapılandırmasını al
     *
     * @return array
     */
    public function getCanvasConfig(): array
    {
        return [
            'styles' => config('studio.editor.canvas.styles', [
                'https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css',
                'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css',
            ]),
            'scripts' => config('studio.editor.canvas.scripts', [
                'https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js',
            ]),
        ];
    }
    
    /**
     * İçeriği yükle
     *
     * @param string $module
     * @param int $id
     * @return array
     */
    public function loadContent(string $module, int $id): array
    {
        $result = [
            'content' => '',
            'css' => '',
            'js' => '',
            'title' => 'Editör',
        ];
        
        if ($module === 'page') {
            try {
                $page = Page::findOrFail($id);
                $result['content'] = $page->body ?? '';
                $result['css'] = $page->css ?? '';
                $result['js'] = $page->js ?? '';
                $result['title'] = $page->title ?? 'Sayfa Düzenleyici';
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Sayfa yüklenirken hata: ' . $e->getMessage());
            }
        }
        
        return $result;
    }
    
    /**
     * İçeriği kaydet
     *
     * @param string $module
     * @param int $id
     * @param string $content
     * @param string $css
     * @param string $js
     * @return bool
     */
    public function saveContent(string $module, int $id, string $content, string $css = '', string $js = ''): bool
    {
        if ($module === 'page') {
            try {
                $page = Page::findOrFail($id);
                $page->body = $content;
                $page->css = $css;
                $page->js = $js;
                $result = $page->save();
                
                if (function_exists('log_activity')) {
                    log_activity($page, 'studio ile düzenlendi');
                }
                
                return $result;
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('İçerik kaydedilirken hata: ' . $e->getMessage());
                return false;
            }
        }
        
        return false;
    }
    
    /**
     * Editör JS ayarlarını oluştur
     *
     * @param string $module
     * @param int $id
     * @param array $data
     * @return array
     */
    public function prepareEditorSettings(string $module, int $id, array $data = []): array
    {
        return [
            'module' => $module,
            'moduleId' => $id,
            'content' => $data['content'] ?? '',
            'css' => $data['css'] ?? '',
            'js' => $data['js'] ?? '',
            'elementId' => 'gjs',
            'mode' => 'edit',
        ];
    }
}
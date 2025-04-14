<?php

namespace Modules\Studio\App\Services;

use Illuminate\Support\Facades\File;

class StudioAssetService
{
    /**
     * CSS dosyalarını render et
     *
     * @return string
     */
    public function renderCss(): string
    {
        $useCdn = config('studio.assets.use_cdn', false);
        $version = config('studio.editor.version', '0.21.8');
        $output = '';
        
        // GrapesJS ana CSS
        if ($useCdn) {
            $output .= '<link rel="stylesheet" href="https://unpkg.com/grapesjs@'.$version.'/dist/css/grapes.min.css">';
        } else {
            $output .= '<link rel="stylesheet" href="https://unpkg.com/grapesjs@'.$version.'/dist/css/grapes.min.css">';
        }
        
        // Tabler CSS
        $output .= '<link rel="stylesheet" href="'.asset('admin/css/tabler.min.css').'">';
        $output .= '<link rel="stylesheet" href="'.asset('admin/css/tabler-vendors.min.css').'">';
        
        // Font Awesome
        $output .= '<link rel="stylesheet" href="'.asset('admin/libs/fontawesome-pro@6.7.1/css/all.min.css').'">';
        
        // Studio CSS dosyaları - doğru yolları kullanalım
        $output .= '<link rel="stylesheet" href="'.asset('modules/studio/css/studio-editor.css').'?v='.time().'">';
        $output .= '<link rel="stylesheet" href="'.asset('modules/studio/css/studio-editor-ui.css').'?v='.time().'">';
        $output .= '<link rel="stylesheet" href="'.asset('modules/studio/css/studio-grapes-overrides.css').'?v='.time().'">';
        
        return $output;
    }

    /**
     * JS dosyalarını render et
     *
     * @return string
     */
    public function renderJs(): string
    {
        $useCdn = config('studio.assets.use_cdn', false);
        $version = config('studio.editor.version', '0.21.8');
        $output = '';
        
        // jQuery
        $output .= '<script src="'.asset('admin/libs/jquery@3.7.1/jquery.min.js').'"></script>';
        
        // GrapesJS ana JS
        if ($useCdn) {
            $output .= '<script src="https://unpkg.com/grapesjs@'.$version.'/dist/grapes.min.js"></script>';
        } else {
            $output .= '<script src="https://unpkg.com/grapesjs@'.$version.'/dist/grapes.min.js"></script>';
        }
        
        // Bootstrap
        $output .= '<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>';
        
        // Studio JS dosyaları - DOĞRU YOLLAR
        $output .= '<script src="'.asset('modules/studio/js/studio-bootstrap.js').'?v='.time().'"></script>';
        $output .= '<script src="'.asset('modules/studio/js/core/studio-core.js').'?v='.time().'"></script>';
        
        // Blok sistemi
        $output .= '<script src="'.asset('modules/studio/js/blocks/registry.js').'?v='.time().'"></script>';
        $output .= '<script src="'.asset('modules/studio/js/blocks/basic-blocks.js').'?v='.time().'"></script>';
        $output .= '<script src="'.asset('modules/studio/js/blocks/bootstrap-blocks.js').'?v='.time().'"></script>';
        $output .= '<script src="'.asset('modules/studio/js/blocks/media-blocks.js').'?v='.time().'"></script>';
        
        // Parser'lar
        $output .= '<script src="'.asset('modules/studio/js/parsers/html-parser.js').'?v='.time().'"></script>';
        $output .= '<script src="'.asset('modules/studio/js/parsers/css-parser.js').'?v='.time().'"></script>';
        
        // UI
        $output .= '<script src="'.asset('modules/studio/js/ui/studio-ui.js').'?v='.time().'"></script>';
        $output .= '<script src="'.asset('modules/studio/js/ui/panel-manager.js').'?v='.time().'"></script>';
        $output .= '<script src="'.asset('modules/studio/js/ui/toolbar-manager.js').'?v='.time().'"></script>';
        $output .= '<script src="'.asset('modules/studio/js/ui/canvas-manager.js').'?v='.time().'"></script>';
        
        // Aksiyonlar
        $output .= '<script src="'.asset('modules/studio/js/actions/save-action.js').'?v='.time().'"></script>';
        $output .= '<script src="'.asset('modules/studio/js/actions/preview-action.js').'?v='.time().'"></script>';
        $output .= '<script src="'.asset('modules/studio/js/actions/export-action.js').'?v='.time().'"></script>';
        
        // Plugins
        $output .= '<script src="'.asset('modules/studio/js/plugins.js').'?v='.time().'"></script>';
        
        return $output;
    }
    
    /**
     * Varlık URL'si oluştur
     *
     * @param string $path Dosya yolu
     * @return string
     */
    public function getAssetUrl(string $path): string
    {
        // Varlığın gerçek yolunu kontrol et
        $publicPath = 'modules/studio/' . $path;
        
        if (File::exists(public_path($publicPath))) {
            return asset($publicPath) . '?v=' . filemtime(public_path($publicPath));
        }
        
        // Eğer yoksa tanımsız varlık döndür
        return asset('modules/studio/img/missing-asset.png');
    }
    
    /**
     * Varlıkları temizle
     * 
     * @return bool
     */
    public function clearAssets(): bool
    {
        try {
            $destinationPath = public_path('modules/studio');
            
            if (File::isDirectory($destinationPath)) {
                File::deleteDirectory($destinationPath);
                return true;
            }
            
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }
}
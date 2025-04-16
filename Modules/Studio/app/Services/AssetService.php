<?php

namespace Modules\Studio\App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;

class AssetService
{
    /**
     * CSS dosyalarını render et
     *
     * @return string
     */
    public function renderCss(): string
    {
        $useCdn = config('studio.editor.cdn.enabled', false);
        $output = '';
        
        if ($useCdn) {
            $version = config('studio.editor.cdn.version', '0.21.8');
            $output .= '<link rel="stylesheet" href="https://unpkg.com/grapesjs@' . $version . '/dist/css/grapes.min.css">';
        } else {
            // Önce admin/libs/studio içinde ara
            if (file_exists(public_path('admin/libs/studio/css/grapes.min.css'))) {
                $output .= '<link rel="stylesheet" href="' . asset('admin/libs/studio/css/grapes.min.css') . '">';
            } else {
                $output .= '<link rel="stylesheet" href="' . asset('modules/studio/css/grapes.min.css') . '">';
            }
        }
        
        // Özel CSS dosyaları
        if (file_exists(public_path('admin/libs/studio/css/studio-editor.css'))) {
            $output .= '<link rel="stylesheet" href="' . asset('admin/libs/studio/css/studio-editor.css') . '">';
        } else if (file_exists(public_path('modules/studio/css/studio-editor.css'))) {
            $output .= '<link rel="stylesheet" href="' . asset('modules/studio/css/studio-editor.css') . '">';
        }
        
        if (file_exists(public_path('admin/libs/studio/css/studio-editor-ui.css'))) {
            $output .= '<link rel="stylesheet" href="' . asset('admin/libs/studio/css/studio-editor-ui.css') . '">';
        }
        
        if (file_exists(public_path('admin/libs/studio/css/studio-grapes-overrides.css'))) {
            $output .= '<link rel="stylesheet" href="' . asset('admin/libs/studio/css/studio-grapes-overrides.css') . '">';
        }
        
        return $output;
    }
    
    /**
     * JS dosyalarını render et
     *
     * @return string
     */
    public function renderJs(): string
    {
        $useCdn = config('studio.editor.cdn.enabled', false);
        $output = '';
        
        // JQuery
        $output .= '<script src="' . asset('admin/libs/jquery@3.7.1/jquery.min.js') . '"></script>';
        
        // GrapesJS
        if ($useCdn) {
            $version = config('studio.editor.cdn.version', '0.21.8');
            $output .= '<script src="https://unpkg.com/grapesjs@' . $version . '/dist/grapes.min.js"></script>';
        } else {
            // Önce admin/libs/studio içinde ara
            if (file_exists(public_path('admin/libs/studio/grapes.min.js'))) {
                $output .= '<script src="' . asset('admin/libs/studio/grapes.min.js') . '"></script>';
            } else {
                $output .= '<script src="' . asset('modules/studio/js/grapes.min.js') . '"></script>';
            }
        }
        
        // Parçalı JS dosyaları
        $partialFiles = [
            'studio-fix.js',
            'studio-utils.js',
            'studio-plugins.js',
            'studio-core.js',
            'studio-blocks.js',
            'studio-ui.js',
            'studio-actions.js',
            'studio-html-parser.js',
        ];
        
        foreach ($partialFiles as $file) {
            if (file_exists(public_path('admin/libs/studio/partials/' . $file))) {
                $output .= '<script src="' . asset('admin/libs/studio/partials/' . $file) . '"></script>';
            }
        }
        
        // Ana Studio JS
        if (file_exists(public_path('admin/libs/studio/studio.js'))) {
            $output .= '<script src="' . asset('admin/libs/studio/studio.js') . '"></script>';
        }
        
        if (file_exists(public_path('admin/libs/studio/app.js'))) {
            $output .= '<script src="' . asset('admin/libs/studio/app.js') . '"></script>';
        }
        
        return $output;
    }
    
    /**
     * Varlık yükle
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @return array
     */
    public function uploadAsset($file): array
    {
        $fileName = time() . '_' . $file->getClientOriginalName();
        $mimeType = $file->getMimeType();
        $size = $file->getSize();
        
        // Storage yolunu belirle (tenant aware)
        $storagePath = config('studio.editor.storage.path', 'studio/assets');
        
        // Dosyayı yükle
        $path = $file->storeAs($storagePath, $fileName, 'public');
        
        return [
            'name' => $fileName,
            'type' => $mimeType,
            'size' => $size,
            'url' => Storage::disk('public')->url($path),
            'path' => $path
        ];
    }
    
    /**
     * Varlık URL'sini al
     *
     * @param string $path
     * @return string
     */
    public function getAssetUrl(string $path): string
    {
        return Storage::disk('public')->url($path);
    }
    
    /**
     * Resim optimize et (küçük boyutlandır)
     *
     * @param string $path
     * @param array $options
     * @return string
     */
    public function optimizeImage(string $path, array $options = []): string
    {
        // Şimdilik basit bir yönlendirme
        return $this->getAssetUrl($path);
    }
}
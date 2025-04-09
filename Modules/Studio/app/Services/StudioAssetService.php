<?php

namespace Modules\Studio\App\Services;

class StudioAssetService
{
    /**
     * CSS dosyalarını render et
     *
     * @return string
     */
    public function renderCss()
    {
        $useCdn = config('studio.editor.use_cdn', false);
        $output = '';
        
        if ($useCdn) {
            $output .= '<link rel="stylesheet" href="https://unpkg.com/grapesjs@0.21.8/dist/css/grapes.min.css">';
        } else {
            $output .= '<link rel="stylesheet" href="' . asset('build-studio/resources/assets/css/grapes.min.css') . '">';
        }
        
        // Özel CSS dosyaları
        $output .= '<link rel="stylesheet" href="' . asset('build-studio/resources/assets/css/studio.css') . '">';
        
        return $output;
    }
    
    /**
     * JS dosyalarını render et
     *
     * @return string
     */
    public function renderJs()
    {
        $useCdn = config('studio.editor.use_cdn', false);
        $useJquery = config('studio.editor.use_jquery', true);
        $output = '';
        
        if ($useJquery) {
            $output .= '<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>';
        }
        
        if ($useCdn) {
            $output .= '<script src="https://unpkg.com/grapesjs@0.21.8/dist/grapes.min.js"></script>';
        } else {
            $output .= '<script src="' . asset('build-studio/resources/assets/js/grapes.min.js') . '"></script>';
        }
        
        // GrapesJS eklentileri
        $plugins = config('studio.editor.plugins', []);
        foreach ($plugins as $plugin => $enabled) {
            if ($enabled) {
                if ($useCdn) {
                    $output .= '<script src="https://unpkg.com/grapesjs-' . $plugin . '"></script>';
                } else {
                    $output .= '<script src="' . asset('build-studio/resources/assets/js/plugins/grapesjs-' . $plugin . '.min.js') . '"></script>';
                }
            }
        }
        
        // Özel JS dosyaları
        $output .= '<script src="' . asset('build-studio/resources/assets/js/studio.js') . '"></script>';
        
        return $output;
    }
}
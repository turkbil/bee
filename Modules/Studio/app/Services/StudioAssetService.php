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
            // Önce admin/libs/studio içinde ara
            if (file_exists(public_path('admin/libs/studio/grapes.min.js'))) {
                $output .= '<script src="' . asset('admin/libs/studio/grapes.min.js') . '"></script>';
            } else {
                $output .= '<script src="' . asset('modules/studio/js/grapes.min.js') . '"></script>';
            }
        }
        
        // GrapesJS eklentileri
        $plugins = config('studio.editor.plugins', []);
        
        foreach ($plugins as $plugin => $enabled) {
            if ($enabled) {
                if ($useCdn) {
                    $output .= '<script src="https://unpkg.com/grapesjs-' . $plugin . '"></script>';
                } else {
                    // Önce admin/libs/studio/plugins içinde ara
                    if (file_exists(public_path('admin/libs/studio/plugins/grapesjs-' . $plugin . '.min.js'))) {
                        $output .= '<script src="' . asset('admin/libs/studio/plugins/grapesjs-' . $plugin . '.min.js') . '"></script>';
                    } else if (file_exists(public_path('modules/studio/js/plugins/grapesjs-' . $plugin . '.min.js'))) {
                        $output .= '<script src="' . asset('modules/studio/js/plugins/grapesjs-' . $plugin . '.min.js') . '"></script>';
                    }
                }
            }
        }
        
        // Özel JS dosyaları
        if (file_exists(public_path('admin/libs/studio/studio.js'))) {
            $output .= '<script src="' . asset('admin/libs/studio/studio.js') . '"></script>';
        } else if (file_exists(public_path('modules/studio/js/studio.js'))) {
            $output .= '<script src="' . asset('modules/studio/js/studio.js') . '"></script>';
        }
        
        return $output;
    }
}
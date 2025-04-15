<?php

namespace Modules\Studio\App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;

class BlockService
{
    /**
     * Tüm blokları al
     *
     * @return array
     */
    public function getAllBlocks(): array
    {
        // Önbellekten blokları al
        $cacheKey = 'studio_blocks_' . (function_exists('tenant_id') ? tenant_id() : 'default');
        $cacheTtl = config('studio.cache.ttl', 3600);
        
        return Cache::remember($cacheKey, $cacheTtl, function () {
            return $this->loadBlocksFromTemplates();
        });
    }
    
    /**
     * Blade şablonlarından blokları yükle
     *
     * @return array
     */
    protected function loadBlocksFromTemplates(): array
    {
        $blocks = [];
        $basePath = module_path('Studio', 'resources/views/blocks');
        
        if (!File::isDirectory($basePath)) {
            return $this->registerDefaultBlocks();
        }
        
        // Kategorileri tara
        foreach (File::directories($basePath) as $categoryPath) {
            $categoryName = basename($categoryPath);
            $categoryLabel = $this->formatCategoryName($categoryName);
            
            // Kategori içindeki tüm Blade dosyalarını tara
            foreach (File::files($categoryPath) as $file) {
                if (!str_ends_with($file->getFilename(), '.blade.php')) {
                    continue;
                }
                
                $fileName = $file->getFilenameWithoutExtension();
                // .blade uzantısını kaldır
                $fileName = str_replace('.blade', '', $fileName);
                
                // Blok ID'si: kategori-dosyaadı
                $blockId = $categoryName . '-' . $fileName;
                
                // Blok etiketini dosya adından oluştur
                $blockLabel = $this->formatBlockName($fileName);
                
                // Blok içeriğini şablon dosyasından al
                $viewPath = 'studio::blocks.' . $categoryName . '.' . $fileName;
                $content = '';
                
                try {
                    $content = View::make($viewPath)->render();
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Blok şablonu yüklenemedi: ' . $viewPath . ' - ' . $e->getMessage());
                    continue; // Hatalı şablonu atla
                }
                
                // İkon sınıfını belirle
                $icon = $this->getIconForCategory($categoryName);
                
                // Bloğu kaydet
                $blocks[] = [
                    'id' => $blockId,
                    'label' => $blockLabel,
                    'category' => $categoryName,
                    'icon' => $icon,
                    'content' => $content,
                    'template' => $viewPath,
                ];
            }
        }
        
        // Eğer hiç blok yoksa, varsayılan blokları ekle
        if (empty($blocks)) {
            return $this->registerDefaultBlocks();
        }
        
        return $blocks;
    }
    
    /**
     * Kategori adını formatla
     * 
     * @param string $categoryName
     * @return string
     */
    protected function formatCategoryName(string $categoryName): string
    {
        $categories = [
            'layout' => 'Düzen',
            'content' => 'İçerik',
            'form' => 'Form',
            'media' => 'Medya',
            'widget' => 'Widgetlar',
        ];
        
        return $categories[$categoryName] ?? ucfirst($categoryName);
    }
    
    /**
     * Blok adını formatla
     * 
     * @param string $blockName
     * @return string
     */
    protected function formatBlockName(string $blockName): string
    {
        return ucwords(str_replace(['-', '_'], ' ', $blockName));
    }
    
    /**
     * Kategori için ikon sınıfını belirle
     *
     * @param string $category
     * @return string
     */
    protected function getIconForCategory(string $category): string
    {
        $icons = [
            'layout' => 'fa fa-columns',
            'content' => 'fa fa-font',
            'form' => 'fa fa-wpforms',
            'media' => 'fa fa-image',
            'widget' => 'fa fa-puzzle-piece',
        ];
        
        return $icons[$category] ?? 'fa fa-cube';
    }
    
    /**
     * Kategoriye göre blokları al
     *
     * @param string $category
     * @return array
     */
    public function getBlocksByCategory(string $category): array
    {
        $blocks = $this->getAllBlocks();
        
        return array_filter($blocks, function ($block) use ($category) {
            return $block['category'] === $category;
        });
    }
    
    /**
     * Varsayılan blokları kaydet
     *
     * @return array
     */
    protected function registerDefaultBlocks(): array
    {
        $blocks = [];
        
        // Varsayılan bloklar gerekirse burada belirtilebilir
        
        return $blocks;
    }
    
    /**
     * Bloğu HTML olarak render et
     *
     * @param string $blockId
     * @return string|null
     */
    public function renderBlock(string $blockId): ?string
    {
        $blocks = $this->getAllBlocks();
        
        foreach ($blocks as $block) {
            if ($block['id'] === $blockId) {
                // Eğer şablon varsa, şablonu renderla
                if (isset($block['template']) && !empty($block['template'])) {
                    try {
                        return View::make($block['template'])->render();
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error('Blok şablonu renderlanamadı: ' . $block['template'] . ' - ' . $e->getMessage());
                        return $block['content'] ?? '';
                    }
                }
                return $block['content'] ?? '';
            }
        }
        
        return null;
    }
}

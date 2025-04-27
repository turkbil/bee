<?php

namespace Modules\Studio\App\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class BlockManager
{
    /**
     * @var array
     */
    protected $blocks = [];
    
    /**
     * @var array
     */
    protected $categories = [];
    
    /**
     * BlockManager constructor.
     */
    public function __construct()
    {
        $this->categories = config('studio.blocks.categories', []);
        $this->loadBlocks();
    }
    
    /**
     * Blokları yükle
     */
    protected function loadBlocks()
    {
        // Önbellekten yükle
        $cacheKey = 'studio_blocks';
        $cacheTtl = config('studio.cache.ttl', 3600);
        
        if (Cache::has($cacheKey)) {
            $this->blocks = Cache::get($cacheKey, []);
        } else {
            $this->loadBlocksFromWidgetManagement();
        }
    }
    
    /**
     * Widgetmanagement modülünden blokları yükle
     */
    protected function loadBlocksFromWidgetManagement()
    {
        // Önbellek anahtarı oluştur
        $cacheKey = 'studio_blocks';
        $cacheTtl = config('studio.cache.ttl', 3600);
        
        // Widgetmanagement modülünden blokları yükle
        if (class_exists('Modules\WidgetManagement\App\Models\Widget')) {
            $widgets = \Modules\WidgetManagement\App\Models\Widget::where('is_active', true)->get();
            
            foreach ($widgets as $widget) {
                $blockId = 'widget-' . $widget->id;
                $category = $widget->data['category'] ?? 'widget';
                
                $this->blocks[$blockId] = [
                    'id' => $blockId,
                    'label' => $widget->name,
                    'category' => $category,
                    'content' => $widget->content_html ?? '',
                    'icon' => $widget->data['icon'] ?? 'fa fa-puzzle-piece'
                ];
            }
        }
        
        // Önbelleğe kaydet
        Cache::put($cacheKey, $this->blocks, $cacheTtl);
    }
    
    /**
     * Yeni blok kaydet
     *
     * @param string $id
     * @param array $data
     * @return $this
     */
    public function register(string $id, array $data)
    {
        // Zorunlu alanları kontrol et
        if (!isset($data['label']) || !isset($data['category']) || !isset($data['content'])) {
            throw new \InvalidArgumentException('Blok tanımında zorunlu alanlar eksik');
        }
        
        // Kategori geçerli mi kontrol et
        if (!array_key_exists($data['category'], $this->categories)) {
            throw new \InvalidArgumentException('Geçersiz kategori: ' . $data['category']);
        }
        
        $this->blocks[$id] = array_merge(['id' => $id], $data);
        
        return $this;
    }
    
    /**
     * Mevcut bir bloğu genişlet
     *
     * @param string $id
     * @param array $data
     * @return $this
     */
    public function extend(string $id, array $data)
    {
        if (!isset($this->blocks[$id])) {
            throw new \InvalidArgumentException('Blok bulunamadı: ' . $id);
        }
        
        $this->blocks[$id] = array_merge($this->blocks[$id], $data);
        
        return $this;
    }
    
    /**
     * Bloğu kaldır
     *
     * @param string $id
     * @return $this
     */
    public function remove(string $id)
    {
        if (isset($this->blocks[$id])) {
            unset($this->blocks[$id]);
        }
        
        return $this;
    }
    
    /**
     * Tüm blokları al
     *
     * @return array
     */
    public function getAll(): array
    {
        return array_values($this->blocks);
    }
    
    /**
     * Kategoriye göre blokları al
     *
     * @param string $category
     * @return array
     */
    public function getByCategory(string $category): array
    {
        return array_values(array_filter($this->blocks, function($block) use ($category) {
            return $block['category'] === $category;
        }));
    }
    
    /**
     * ID'ye göre bloğu al
     *
     * @param string $id
     * @return array|null
     */
    public function getById(string $id): ?array
    {
        return $this->blocks[$id] ?? null;
    }
    
    /**
     * Kategorileri al
     *
     * @return array
     */
    public function getCategories(): array
    {
        return $this->categories;
    }
}
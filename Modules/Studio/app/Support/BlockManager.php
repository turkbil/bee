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
            $this->registerDefaultBlocks();
        }
    }
    
    /**
     * Varsayılan blokları kaydet
     */
    protected function registerDefaultBlocks()
    {
        $this->register('section-1col', [
            'label' => '1 Sütun',
            'category' => 'layout',
            'icon' => 'fa fa-columns',
            'content' => '<section class="container py-5">
                <div class="row">
                    <div class="col-md-12">
                        <h2>Başlık Buraya</h2>
                        <p>İçerik buraya gelecek. Çift tıklayarak düzenleyebilirsiniz.</p>
                    </div>
                </div>
            </section>'
        ]);
        
        $this->register('section-2col', [
            'label' => '2 Sütun',
            'category' => 'layout',
            'icon' => 'fa fa-columns',
            'content' => '<section class="container py-5">
                <div class="row">
                    <div class="col-md-6">
                        <h3>Başlık 1</h3>
                        <p>İçerik buraya gelecek. Çift tıklayarak düzenleyebilirsiniz.</p>
                    </div>
                    <div class="col-md-6">
                        <h3>Başlık 2</h3>
                        <p>İçerik buraya gelecek. Çift tıklayarak düzenleyebilirsiniz.</p>
                    </div>
                </div>
            </section>'
        ]);
        
        $this->register('section-3col', [
            'label' => '3 Sütun',
            'category' => 'layout',
            'icon' => 'fa fa-columns',
            'content' => '<section class="container py-5">
                <div class="row">
                    <div class="col-md-4">
                        <h3>Başlık 1</h3>
                        <p>İçerik buraya gelecek.</p>
                    </div>
                    <div class="col-md-4">
                        <h3>Başlık 2</h3>
                        <p>İçerik buraya gelecek.</p>
                    </div>
                    <div class="col-md-4">
                        <h3>Başlık 3</h3>
                        <p>İçerik buraya gelecek.</p>
                    </div>
                </div>
            </section>'
        ]);
        
        $this->register('text', [
            'label' => 'Metin',
            'category' => 'content',
            'icon' => 'fa fa-font',
            'content' => '<div class="my-3">
                <h3>Başlık</h3>
                <p>Buraya metin içeriği gelecek. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam suscipit velit id diam ultrices, at facilisis dui tincidunt.</p>
            </div>'
        ]);
        
        $this->register('button', [
            'label' => 'Buton',
            'category' => 'content',
            'icon' => 'fa fa-square',
            'content' => '<button class="btn btn-primary">Tıkla</button>'
        ]);
        
        $this->register('image', [
            'label' => 'Görsel',
            'category' => 'media',
            'icon' => 'fa fa-image',
            'content' => '<img src="https://via.placeholder.com/800x400" class="img-fluid rounded" alt="Görsel açıklaması">'
        ]);
        
        $this->register('contact-form', [
            'label' => 'İletişim Formu',
            'category' => 'form',
            'icon' => 'fa fa-envelope',
            'content' => '<div class="container py-4">
                <form>
                    <div class="mb-3">
                        <label for="name" class="form-label">Adınız</label>
                        <input type="text" class="form-control" id="name" placeholder="Adınız Soyadınız">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email adresiniz</label>
                        <input type="email" class="form-control" id="email" placeholder="ornek@domain.com">
                    </div>
                    <div class="mb-3">
                        <label for="message" class="form-label">Mesajınız</label>
                        <textarea class="form-control" id="message" rows="5"></textarea>
                    </div>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button class="btn btn-primary" type="submit">Gönder</button>
                    </div>
                </form>
            </div>'
        ]);
        
        // Önbelleğe kaydet
        $cacheKey = 'studio_blocks';
        $cacheTtl = config('studio.cache.ttl', 3600);
        
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
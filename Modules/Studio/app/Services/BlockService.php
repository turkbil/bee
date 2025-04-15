<?php

namespace Modules\Studio\App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

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
            return $this->registerDefaultBlocks();
        });
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
        
        // Düzen blokları
        $blocks[] = [
            'id' => 'section-1col',
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
        ];
        
        $blocks[] = [
            'id' => 'section-2col',
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
        ];
        
        $blocks[] = [
            'id' => 'section-3col',
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
        ];
        
        // Temel içerik blokları
        $blocks[] = [
            'id' => 'text',
            'label' => 'Metin',
            'category' => 'content',
            'icon' => 'fa fa-font',
            'content' => '<div class="my-3">
                <h3>Başlık</h3>
                <p>Buraya metin içeriği gelecek. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam suscipit velit id diam ultrices, at facilisis dui tincidunt.</p>
            </div>'
        ];
        
        $blocks[] = [
            'id' => 'header',
            'label' => 'Header',
            'category' => 'layout',
            'icon' => 'fa fa-heading',
            'content' => '<header class="py-3 mb-4 border-bottom">
                <div class="container d-flex flex-wrap justify-content-center">
                    <a href="/" class="d-flex align-items-center mb-3 mb-lg-0 me-lg-auto text-dark text-decoration-none">
                        <span class="fs-4">Şirket Adı</span>
                    </a>
                    <ul class="nav">
                        <li class="nav-item"><a href="#" class="nav-link link-dark px-2 active">Ana Sayfa</a></li>
                        <li class="nav-item"><a href="#" class="nav-link link-dark px-2">Hakkımızda</a></li>
                        <li class="nav-item"><a href="#" class="nav-link link-dark px-2">Hizmetler</a></li>
                        <li class="nav-item"><a href="#" class="nav-link link-dark px-2">İletişim</a></li>
                    </ul>
                </div>
            </header>'
        ];
        
        // Form blokları
        $blocks[] = [
            'id' => 'contact-form',
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
        ];
        
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
                return $block['content'];
            }
        }
        
        return null;
    }
    
    /**
     * Özel blok kaydet
     *
     * @param array $blockData
     * @return array
     */
    public function registerCustomBlock(array $blockData): array
    {
        // Özel blok kaydetme mantığı burada olacak
        return $blockData;
    }
}
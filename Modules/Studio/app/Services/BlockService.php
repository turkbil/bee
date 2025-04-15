<?php

namespace Modules\Studio\App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;

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
        
        // Önbelleği temizle (debugging için)
        Cache::forget($cacheKey);
        
        $blocks = $this->loadBlocksFromTemplates();
        
        // Eğer bloklar boşsa, varsayılan blokları yükle
        if (empty($blocks)) {
            $blocks = $this->registerDefaultBlocks();
        }
        
        // Log dosyasına detayları yaz
        Log::info('Yüklenen bloklar:', ['count' => count($blocks)]);
        
        return $blocks;
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
        
        Log::info('Bloklar dizini kontrol ediliyor: ' . $basePath);
        
        if (!File::isDirectory($basePath)) {
            Log::warning('Blocks dizini bulunamadı: ' . $basePath);
            
            // Dizin yoksa oluştur
            File::makeDirectory($basePath, 0755, true, true);
            
            // Alt kategorileri de oluştur
            $categories = ['layout', 'content', 'form', 'media', 'hero', 'cards', 'features', 'testimonials'];
            foreach ($categories as $category) {
                $categoryPath = $basePath . '/' . $category;
                if (!File::isDirectory($categoryPath)) {
                    File::makeDirectory($categoryPath, 0755, true, true);
                }
            }
            
            return [];
        }
        
        try {
            // Kategoriler ve dosya sayısı bilgisini logla
            $categoryInfo = [];
            foreach (File::directories($basePath) as $categoryPath) {
                $category = basename($categoryPath);
                $fileCount = count(File::files($categoryPath));
                $categoryInfo[$category] = $fileCount;
            }
            Log::info('Bulunan kategoriler ve dosya sayıları:', $categoryInfo);
            
            // Kategorileri tara
            foreach (File::directories($basePath) as $categoryPath) {
                $categoryName = basename($categoryPath);
                $categoryLabel = $this->formatCategoryName($categoryName);
                
                Log::info('Kategori işleniyor: ' . $categoryName, ['path' => $categoryPath]);
                
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
                    
                    // Görünüm yolunu oluştur ve Log'a yaz
                    $viewPath = 'studio::blocks.' . $categoryName . '.' . $fileName;
                    Log::info('Blok görünüm yolu oluşturuldu:', ['viewPath' => $viewPath]);
                    
                    // Görünüm dosyasını kontrol et
                    if (View::exists($viewPath)) {
                        Log::info('Görünüm dosyası bulundu:', ['viewPath' => $viewPath]);
                    } else {
                        Log::warning('Görünüm dosyası bulunamadı:', ['viewPath' => $viewPath]);
                        continue;
                    }
                    
                    // Blok içeriğini şablon dosyasından al
                    $content = '';
                    
                    try {
                        // Dosya içeriğini doğrudan oku
                        $content = File::get($file->getPathname());
                        
                        // PHP ve Blade etiketlerini temizle (basit bir temizleme)
                        $content = preg_replace('/<\?php.*?\?>/s', '', $content);
                        $content = preg_replace('/@(php|if|foreach|for|while|switch|case).*?@end\1/s', '', $content);
                        $content = preg_replace('/@.*?(\(.*?\))/', '', $content);
                        
                        Log::info('Blok içeriği okundu: ' . $blockId, [
                            'view' => $viewPath,
                            'size' => strlen($content)
                        ]);
                    } catch (\Exception $e) {
                        Log::error('Blok içeriği okunamadı: ' . $viewPath . ' - ' . $e->getMessage());
                        $content = '<div class="alert alert-warning">Blok içeriği yüklenemedi</div>';
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
                    
                    Log::info('Blok kaydedildi: ' . $blockId);
                }
            }
        } catch (\Exception $e) {
            Log::error('Blokları yüklerken hata oluştu: ' . $e->getMessage(), [
                'exception' => $e
            ]);
        }
        
        Log::info('Toplam ' . count($blocks) . ' blok yüklendi');
        
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
            'hero' => 'Hero',
            'cards' => 'Kartlar',
            'features' => 'Özellikler',
            'testimonials' => 'Yorumlar',
            'fiyatlandirma' => 'Fiyatlandırma'
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
            'hero' => 'fa fa-star',
            'cards' => 'fa fa-id-card',
            'features' => 'fa fa-list-check',
            'testimonials' => 'fa fa-quote-right',
        ];
        
        return $icons[$category] ?? 'fa fa-cube';
    }
    
    /**
     * Varsayılan blokları kaydet - bu metod genellikle örnek içerik oluşturmak için kullanılır
     *
     * @return array
     */
    protected function registerDefaultBlocks(): array
    {
        Log::info('Varsayılan bloklar kaydediliyor');
        
        // Blade dosyalarınız eksik veya erişilemez olduğunda varsayılan bazı bloklar ekleyin
        return [
            [
                'id' => 'layout-one-column',
                'label' => 'Tek Sütun',
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
            ],
            [
                'id' => 'content-text',
                'label' => 'Metin',
                'category' => 'content',
                'icon' => 'fa fa-font',
                'content' => '<div class="my-3">
                    <h3>Başlık</h3>
                    <p>Buraya metin içeriği gelecek. Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                </div>'
            ],
            [
                'id' => 'form-contact',
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
            ]
        ];
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
                        Log::error('Blok şablonu renderlanamadı: ' . $block['template'] . ' - ' . $e->getMessage());
                        return $block['content'] ?? '';
                    }
                }
                return $block['content'] ?? '';
            }
        }
        
        return null;
    }
}
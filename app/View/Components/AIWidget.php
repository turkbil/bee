<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Modules\AI\App\Models\AIFeature;

class AIWidget extends Component
{
    public $context;
    public $entityId;
    public $entityType;
    public $currentData;
    public $features;
    public $categories;
    public $widgetId;
    
    /**
     * Global AI Widget Component
     * Her form altında kullanılabilir AI assistant
     */
    public function __construct(
        string $context = 'page',
        int $entityId = null,
        string $entityType = 'page',
        array $currentData = []
    ) {
        $this->context = $context;
        $this->entityId = $entityId;
        $this->entityType = $entityType;
        $this->currentData = $currentData;
        $this->widgetId = 'ai-widget-' . $context . '-' . ($entityId ?? 'new');
        
        $this->loadContextFeatures();
    }
    
    /**
     * Context'e göre uygun AI feature'larını yükle
     */
    private function loadContextFeatures()
    {
        // Context-based feature mapping
        $contextFeatureMap = [
            'page' => [
                'seo-puan-analizi',
                'hizli-seo-analizi', 
                'anahtar-kelime-arastirmasi',
                'icerik-optimizasyonu',
                'schema-markup-onerileri',
                'meta-aciklama-uretici',
                'baslik-uretici',
                'coklu-dil-cevirisi'
            ],
            'portfolio' => [
                'icerik-optimizasyonu',
                'baslik-uretici', 
                'meta-aciklama-uretici',
                'anahtar-kelime-arastirmasi',
                'coklu-dil-cevirisi'
            ],
            'blog' => [
                'seo-puan-analizi',
                'anahtar-kelime-arastirmasi',
                'icerik-optimizasyonu',
                'icerik-genisletme',
                'baslik-uretici',
                'trending-konu-onerileri'
            ]
        ];
        
        $featureSlugs = $contextFeatureMap[$this->context] ?? $contextFeatureMap['page'];
        
        // Feature'ları veritabanından çek
        try {
            $this->features = AIFeature::whereIn('slug', $featureSlugs)
                ->where('status', 'active')
                ->orderBy('id', 'asc')
                ->get();
                
            // Kategorilere göre grupla - basit yaklaşım
            $this->categories = collect([
                (object) ['id' => 1, 'name' => 'SEO Araçları', 'priority' => 1],
                (object) ['id' => 2, 'name' => 'İçerik Araçları', 'priority' => 2],
                (object) ['id' => 3, 'name' => 'Çeviri Araçları', 'priority' => 3]
            ]);
            
            // Features'ı context'e göre grupla
            $groupedFeatures = [];
            foreach($this->features as $feature) {
                $categoryId = $this->getCategoryIdBySlug($feature->slug);
                $groupedFeatures[$categoryId][] = $feature;
            }
            $this->features = $groupedFeatures;
            
        } catch (\Exception $e) {
            \Log::warning('AI Widget feature loading failed: ' . $e->getMessage());
            $this->features = [];
            $this->categories = collect();
        }
    }
    
    /**
     * Feature slug'ına göre kategori ID'si belirle
     */
    private function getCategoryIdBySlug(string $slug): int
    {
        // SEO araçları
        $seoFeatures = [
            'seo-puan-analizi', 'hizli-seo-analizi', 
            'anahtar-kelime-arastirmasi', 'schema-markup-onerileri'
        ];
        
        // İçerik araçları  
        $contentFeatures = [
            'icerik-optimizasyonu', 'icerik-genisletme',
            'baslik-uretici', 'meta-aciklama-uretici',
            'alt-baslik-onerileri', 'icerik-ozetleme',
            'sayfa-gelistirme-onerileri', 'kullanici-deneyimi-analizi',
            'icerik-kalite-skoru', 'trending-konu-onerileri'
        ];
        
        // Çeviri araçları
        $translationFeatures = [
            'coklu-dil-cevirisi', 'dil-kalitesi-kontrolu'
        ];
        
        if (in_array($slug, $seoFeatures)) return 1;
        if (in_array($slug, $contentFeatures)) return 2; 
        if (in_array($slug, $translationFeatures)) return 3;
        
        return 2; // Default content
    }
    
    /**
     * Widget için gerekli JavaScript config'i hazırla
     */
    public function getJavaScriptConfig()
    {
        return [
            'widgetId' => $this->widgetId,
            'context' => $this->context,
            'entityId' => $this->entityId,
            'entityType' => $this->entityType,
            'currentData' => $this->currentData,
            'csrfToken' => csrf_token(),
            'apiEndpoints' => [
                'executeFeature' => route('admin.ai.execute-widget-feature'),
                'sendMessage' => route('admin.ai.send-message')
            ]
        ];
    }

    /**
     * Render the component
     */
    public function render()
    {
        return view('components.ai-widget');
    }
}
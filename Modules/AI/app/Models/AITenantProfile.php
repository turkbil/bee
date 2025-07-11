<?php

namespace Modules\AI\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Tenant;

class AITenantProfile extends Model
{
    protected $table = 'ai_tenant_profiles';

    protected $fillable = [
        'tenant_id',
        'company_info',
        'sector_details',
        'success_stories',
        'ai_behavior_rules',
        'founder_info',
        'additional_info',
        'brand_story',
        'brand_story_created_at',
        'is_active',
        'is_completed'
    ];

    protected $casts = [
        'company_info' => 'array',
        'sector_details' => 'array',
        'success_stories' => 'array',
        'ai_behavior_rules' => 'array',
        'founder_info' => 'array',
        'additional_info' => 'array',
        'context_priority' => 'array',
        'brand_story_created_at' => 'datetime',
        'is_active' => 'boolean',
        'is_completed' => 'boolean'
    ];

    /**
     * İlişkili tenant
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Mevcut tenant için profil getir veya oluştur
     */
    public static function currentOrCreate(): self
    {
        // Yeni helper fonksiyonu ile hızlı tenant ID çözümleme
        $tenantId = resolve_tenant_id();
        
        // Tenant ID hala yoksa exception at
        if (!$tenantId) {
            throw new \Exception('Tenant ID bulunamadı. Lütfen tenant context\'ini kontrol edin.');
        }
        
        // Tenant ID resolved successfully
        
        return static::firstOrCreate(
            ['tenant_id' => $tenantId],
            [
                'company_info' => [],
                'sector_details' => [],
                'success_stories' => [],
                'ai_behavior_rules' => [],
                'founder_info' => [],
                'additional_info' => [],
                'is_active' => true,
                'is_completed' => false
            ]
        );
    }

    /**
     * Profil tamamlandı mı?
     */
    public function isCompleted(): bool
    {
        return $this->is_completed && 
               !empty($this->company_info) && 
               !empty($this->sector_details);
    }

    /**
     * AI için context hazırla (eski yöntem - backward compatibility)
     */
    public function getAIContext(): array
    {
        $context = [];
        
        // Firma bilgileri
        if ($this->company_info) {
            $context['company_name'] = $this->company_info['name'] ?? null;
            $context['founding_year'] = $this->company_info['founding_year'] ?? null;
            $context['employee_count'] = $this->company_info['employee_count'] ?? null;
            $context['location'] = $this->company_info['location'] ?? null;
            $context['slogan'] = $this->company_info['slogan'] ?? null;
            $context['mission'] = $this->company_info['mission'] ?? null;
            $context['vision'] = $this->company_info['vision'] ?? null;
        }
        
        // Sektör bilgileri
        if ($this->sector_details) {
            $context['sector'] = $this->sector_details['sector'] ?? null;
            $context['sector_specific'] = $this->sector_details;
        }
        
        // Başarı hikayeleri
        if ($this->success_stories) {
            $context['achievements'] = $this->success_stories;
        }
        
        // Kurucu bilgileri (varsa)
        if ($this->founder_info && !empty($this->founder_info)) {
            $context['founder'] = $this->founder_info;
        }
        
        // AI kuralları
        if ($this->ai_behavior_rules) {
            $context['tone'] = $this->ai_behavior_rules['tone'] ?? 'professional';
            $context['emphasis'] = $this->ai_behavior_rules['emphasis'] ?? [];
            $context['avoid_topics'] = $this->ai_behavior_rules['avoid_topics'] ?? [];
            $context['terminology'] = $this->ai_behavior_rules['terminology'] ?? [];
        }
        
        return $context;
    }

    /**
     * AI için hazır profil özeti (YENİ YÖNTEM - Performanslı)
     * AI'ın anlaması kolay, hazırlanmış metin formatında
     */
    public function getAIProfileSummary(): string
    {
        $summary = [];
        
        // 🎯 ÖNCELİK 1: CORE FİRMA KİMLİĞİ (EN ÖNEMLİ)
        if ($this->company_info) {
            $coreSection = "## 🎯 ANA FİRMA KİMLİĞİ (Birincil Odak)\n";
            
            if (isset($this->company_info['brand_name'])) {
                $coreSection .= "**Firma Adı:** {$this->company_info['brand_name']}\n";
            }
            
            if (isset($this->company_info['main_service'])) {
                $coreSection .= "**Ana Uzmanlık:** {$this->company_info['main_service']}\n";
            }
            
            // Firma yaşı ve deneyim (önemli)
            if (isset($this->company_info['founding_year'])) {
                $coreSection .= "**Kuruluş:** {$this->company_info['founding_year']}\n";
            }
            
            $summary[] = $coreSection;
        }
        
        // 🏢 ÖNCELİK 2: İŞ DÜNYASI VE HEDEFLERİ
        if ($this->sector_details) {
            $businessSection = "## 🏢 İŞ STRATEJİSİ\n";
            
            if (isset($this->sector_details['target_audience'])) {
                $audiences = [];
                foreach ($this->sector_details['target_audience'] as $key => $value) {
                    if ($value) $audiences[] = $key;
                }
                if (!empty($audiences)) {
                    $businessSection .= "**Hedef Kitle:** " . implode(', ', $audiences) . "\n";
                }
            }
            
            if (isset($this->sector_details['market_position'])) {
                $businessSection .= "**Pazar Pozisyonu:** {$this->sector_details['market_position']}\n";
            }
            
            $summary[] = $businessSection;
        }
        
        // 🎨 ÖNCELİK 2: MARKA DAVRANIŞI VE TONU (ÇOK ÖNEMLİ)
        if ($this->ai_behavior_rules) {
            $behaviorSection = "## 🎨 MARKA DAVRANIŞI VE TON (Ana Odak)\n";
            
            // Yazı tonu
            if (isset($this->ai_behavior_rules['writing_tone'])) {
                $tones = [];
                foreach ($this->ai_behavior_rules['writing_tone'] as $key => $value) {
                    if ($value) $tones[] = $key;
                }
                if (!empty($tones)) {
                    $behaviorSection .= "**Yazı Tonu:** " . implode(', ', $tones) . "\n";
                }
            }
            
            // İletişim tarzı
            if (isset($this->ai_behavior_rules['communication_style'])) {
                $styles = [];
                foreach ($this->ai_behavior_rules['communication_style'] as $key => $value) {
                    if ($value) $styles[] = $key;
                }
                if (!empty($styles)) {
                    $behaviorSection .= "**İletişim Tarzı:** " . implode(', ', $styles) . "\n";
                }
            }
            
            // İçerik yaklaşımı
            if (isset($this->ai_behavior_rules['content_approach'])) {
                $approaches = [];
                foreach ($this->ai_behavior_rules['content_approach'] as $key => $value) {
                    if ($value) $approaches[] = $key;
                }
                if (!empty($approaches)) {
                    $behaviorSection .= "**İçerik Yaklaşımı:** " . implode(', ', $approaches) . "\n";
                }
            }
            
            // Vurgulanacak başarı göstergeleri
            if (isset($this->ai_behavior_rules['success_indicators'])) {
                $indicators = [];
                foreach ($this->ai_behavior_rules['success_indicators'] as $key => $value) {
                    if ($value) $indicators[] = $key;
                }
                if (!empty($indicators)) {
                    $behaviorSection .= "**Başarı Vurguları:** " . implode(', ', $indicators) . "\n";
                }
            }
            
            // Rekabet avantajı
            if (isset($this->ai_behavior_rules['competitive_advantage'])) {
                $advantages = [];
                foreach ($this->ai_behavior_rules['competitive_advantage'] as $key => $value) {
                    if ($value) $advantages[] = $key;
                }
                if (!empty($advantages)) {
                    $behaviorSection .= "**Rekabet Avantajı:** " . implode(', ', $advantages) . "\n";
                }
            }
            
            $summary[] = $behaviorSection;
        }
        
        // 📍 ÖNCELİK 4: LOKASYON BİLGİSİ (En az öncelik - sadece gerektiğinde)
        if ($this->company_info && isset($this->company_info['city'])) {
            $locationSection = "## 📍 Lokasyon (Gerekmedikçe detaya girme)\n";
            $locationSection .= "**Konum:** {$this->company_info['city']} - Firma ana odaklı içeriklerde şehir detaylarına girmeden sadece gerekli durumlarda belirt.\n";
            $summary[] = $locationSection;
        }
        
        // 💼 ÖNCELİK 3: BAŞARI HİKAYELERİ VE DENEYIM (Güven için önemli)
        if ($this->success_stories && !empty(array_filter($this->success_stories))) {
            $successSection = "## 💼 BAŞARI HİKAYELERİ (Güven Unsuru)\n";
            
            foreach ($this->success_stories as $key => $story) {
                if (!empty($story)) {
                    $keyFormatted = ucfirst(str_replace('_', ' ', $key));
                    $successSection .= "**{$keyFormatted}:** {$story}\n";
                }
            }
            
            $summary[] = $successSection;
        }
        
        // 🎯 ÖNCELİK 4: SEKTÖR VE MARKA KİŞİLİĞİ
        if ($this->sector_details) {
            $sectorSection = "## 🎯 SEKTÖR VE MARKA KİŞİLİĞİ\n";
            
            if (isset($this->sector_details['sector'])) {
                $sector = \Modules\AI\app\Models\AIProfileSector::where('code', $this->sector_details['sector'])->first();
                if ($sector) {
                    $sectorSection .= "**Sektör:** {$sector->name}\n";
                }
            }
            
            // Marka yaşı
            if (isset($this->sector_details['brand_age'])) {
                $sectorSection .= "**Marka Yaşı:** {$this->sector_details['brand_age']}\n";
            }
            
            // Pazar pozisyonu
            if (isset($this->sector_details['market_position'])) {
                $sectorSection .= "**Pazar Pozisyonu:** {$this->sector_details['market_position']}\n";
            }
            
            // Marka kişiliği
            if (isset($this->sector_details['brand_personality'])) {
                $personalities = [];
                foreach ($this->sector_details['brand_personality'] as $key => $value) {
                    if ($value) $personalities[] = $key;
                }
                if (!empty($personalities)) {
                    $sectorSection .= "**Marka Kişiliği:** " . implode(', ', $personalities) . "\n";
                }
            }
            
            $summary[] = $sectorSection;
        }
        
        // 👥 ÖNCELİK 5: DETAY BİLGİLER (Ek firma bilgileri)
        if ($this->company_info) {
            $detailSection = "## 👥 DETAY BİLGİLER\n";
            
            // Çalışan sayısı
            if (isset($this->company_info['employee_count'])) {
                $detailSection .= "**Çalışan Sayısı:** {$this->company_info['employee_count']}\n";
            }
            
            // İletişim kanalları
            if (isset($this->company_info['contact_info'])) {
                $channels = [];
                foreach ($this->company_info['contact_info'] as $key => $value) {
                    if ($value) $channels[] = $key;
                }
                if (!empty($channels)) {
                    $detailSection .= "**İletişim Kanalları:** " . implode(', ', $channels) . "\n";
                }
            }
            
            // Kurucu nitelikleri
            if (isset($this->company_info['founder_qualities'])) {
                $qualities = [];
                foreach ($this->company_info['founder_qualities'] as $key => $value) {
                    if ($value) $qualities[] = $key;
                }
                if (!empty($qualities)) {
                    $detailSection .= "**Kurucu Nitelikleri:** " . implode(', ', $qualities) . "\n";
                }
            }
            
            $summary[] = $detailSection;
        }
        
        // Kurucu bilgileri (izin verilmişse)
        if ($this->founder_info && !empty(array_filter($this->founder_info))) {
            $founderPermission = $this->company_info['founder_permission'] ?? 'no';
            if ($founderPermission !== 'no') {
                $founderSection = "## Kurucu/Yönetim Bilgileri\n";
                
                foreach ($this->founder_info as $key => $value) {
                    if (!empty($value)) {
                        $keyFormatted = ucfirst(str_replace('_', ' ', $key));
                        $founderSection .= "**{$keyFormatted}:** {$value}\n";
                    }
                }
                
                $summary[] = $founderSection;
            }
        }
        
        // AI davranış kuralları
        if ($this->ai_behavior_rules) {
            $behaviorSection = "## AI Davranış Talimatları\n";
            
            // Yazı tonu
            if (isset($this->ai_behavior_rules['writing_tone'])) {
                if (is_array($this->ai_behavior_rules['writing_tone'])) {
                    $tones = array_keys(array_filter($this->ai_behavior_rules['writing_tone']));
                    if (!empty($tones)) {
                        $behaviorSection .= "**Yazı Tonu:** " . implode(', ', $tones) . "\n";
                    }
                } else {
                    $behaviorSection .= "**Yazı Tonu:** {$this->ai_behavior_rules['writing_tone']}\n";
                }
            }
            
            // Vurgu noktaları
            if (isset($this->ai_behavior_rules['emphasis_points'])) {
                $emphasis = array_keys(array_filter($this->ai_behavior_rules['emphasis_points']));
                if (!empty($emphasis)) {
                    $behaviorSection .= "**Vurgulanacak Konular:** " . implode(', ', $emphasis) . "\n";
                }
            }
            
            // Kaçınılacak konular
            if (isset($this->ai_behavior_rules['avoid_topics'])) {
                $avoid = array_keys(array_filter($this->ai_behavior_rules['avoid_topics']));
                if (!empty($avoid)) {
                    $behaviorSection .= "**Kaçınılacak Konular:** " . implode(', ', $avoid) . "\n";
                }
            }
            
            // Özel terminoloji
            if (isset($this->ai_behavior_rules['special_terminology']) && !empty($this->ai_behavior_rules['special_terminology'])) {
                $behaviorSection .= "**Özel Terminoloji:** {$this->ai_behavior_rules['special_terminology']}\n";
            }
            
            $summary[] = $behaviorSection;
        }
        
        return implode("\n", $summary);
    }

    /**
     * Marka hikayesi var mı?
     */
    public function hasBrandStory(): bool
    {
        return !empty($this->brand_story);
    }

    /**
     * Marka hikayesi oluştur
     */
    public function generateBrandStory(): string
    {
        try {
            // Mevcut AI context'i kullan
            $context = $this->getAIContext();
            
            // Detaylı brand story prompt'unu oluştur
            $brandContext = $this->buildBrandStoryPrompt($context);
            
            // Marka hikayesi için özel parametreler
            $options = [
                'industry' => $this->sector_details['sector'] ?? 'general',
                'stage' => 'growth',
                'mission' => 'customer_focused',
                'values' => 'quality, excellence',
                'audience' => 'general',
                'unique_factor' => 'innovation'
            ];
            
            // ai_brand_story_creator helper'ini kullan
            $result = ai_brand_story_creator($brandContext, $options);
            
            // Result format: ['success' => bool, 'response' => string, 'tokens_used' => int]
            if (!$result['success']) {
                throw new \Exception($result['error'] ?? 'AI brand story creation failed');
            }
            
            $response = $result['response'];
            
            // Hikayeyi kaydet
            $this->brand_story = $response;
            $this->brand_story_created_at = now();
            $this->save();
            
            \Log::info('Brand story generated successfully', [
                'tenant_id' => tenant('id'),
                'story_length' => strlen($response),
                'tokens_used' => $result['tokens_used'] ?? 0
            ]);
            
            return $response;
        } catch (\Exception $e) {
            \Log::error('Brand story generation failed', [
                'tenant_id' => tenant('id'),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            throw new \Exception('Marka hikayesi oluşturulurken hata: ' . $e->getMessage());
        }
    }

    /**
     * Marka hikayesi prompt'unu oluştur
     */
    private function buildBrandStoryPrompt(array $context): string
    {
        $prompt = "Aşağıdaki bilgilere göre etkileyici bir marka hikayesi oluştur:\n\n";
        
        // Firma bilgileri
        if (isset($context['company_name'])) {
            $prompt .= "Firma Adı: " . $context['company_name'] . "\n";
        }
        
        // Sektör bilgileri
        if (isset($this->sector_details['sector'])) {
            $sectorName = \Modules\AI\app\Models\AIProfileSector::where('code', $this->sector_details['sector'])->first()?->name ?? $this->sector_details['sector'];
            $prompt .= "Sektör: " . $sectorName . "\n";
        }
        
        // Şehir bilgisi - sadece gerektiğinde kullanılacak (İSTEĞE BAĞLI)
        // NOT: Şehir bilgisi hikayede ön plana çıkarılmamalı
        if (isset($this->company_info['city'])) {
            $prompt .= "Konum (sadece alakalı ise bahset): " . $this->company_info['city'] . "\n";
        }
        
        // Ana hizmet
        if (isset($this->company_info['main_service'])) {
            $prompt .= "Ana Hizmet: " . $this->company_info['main_service'] . "\n";
        }
        
        // Marka kişiliği
        if (isset($this->sector_details['brand_personality'])) {
            $personalities = array_keys(array_filter($this->sector_details['brand_personality']));
            if (!empty($personalities)) {
                $prompt .= "Marka Kişiliği: " . implode(', ', $personalities) . "\n";
            }
        }
        
        // Başarı hikayeleri
        if (!empty($this->success_stories)) {
            $prompt .= "\nBaşarı Hikayeleri:\n";
            foreach ($this->success_stories as $key => $story) {
                if (!empty($story)) {
                    // Array değerleri string'e çevir
                    $storyText = is_array($story) ? implode(', ', array_filter($story)) : $story;
                    if (!empty($storyText)) {
                        $prompt .= "- " . ucfirst(str_replace('_', ' ', $key)) . ": " . $storyText . "\n";
                    }
                }
            }
        }
        
        // Kurucu bilgileri (varsa)
        if (!empty($this->founder_info) && isset($this->company_info['founder_permission']) && $this->company_info['founder_permission'] !== 'no') {
            $prompt .= "\nKurucu Bilgileri:\n";
            if (isset($this->founder_info['founder_name'])) {
                $prompt .= "- Kurucu: " . $this->founder_info['founder_name'] . "\n";
            }
            if (isset($this->founder_info['founder_title'])) {
                $prompt .= "- Unvan: " . $this->founder_info['founder_title'] . "\n";
            }
        }
        
        // Vurgu noktaları
        if (isset($this->ai_behavior_rules['emphasis_points'])) {
            $emphasisPoints = array_keys(array_filter($this->ai_behavior_rules['emphasis_points']));
            if (!empty($emphasisPoints)) {
                $prompt .= "\nVurgu Noktaları: " . implode(', ', $emphasisPoints) . "\n";
            }
        }
        
        $prompt .= "\nLütfen bu bilgilere dayanarak:\n";
        $prompt .= "1. Doğal akışkan bir marka hikayesi oluştur\n";
        $prompt .= "2. Hikaye 3-4 paragraf olsun\n";
        $prompt .= "3. Markanın yolculuğunu ve değerlerini hikaye formatında anlat\n";
        $prompt .= "4. Satış odaklı olmaktan kaçın, hikaye odaklı ol\n";
        $prompt .= "5. Şehir bilgisini öne çıkarma, sadece bağlam gerektiriyorsa bahset\n";
        $prompt .= "6. Pozitif, samimi ve ilham verici bir ton kullan\n\n";
        
        return $prompt;
    }

    /**
     * 🎯 YENİ OPTIMIZE CONTEXT - Öncelikli ve hızlı AI context
     * Priority sistemi ile sadece gerekli bilgiler
     */
    public function getOptimizedAIContext(int $maxPriority = 3): string
    {
        // Cache'den kontrol et
        $cacheKey = "ai_context_optimized_{$this->tenant_id}_{$maxPriority}";
        $context = \Cache::get($cacheKey);
        
        if ($context) {
            return $context;
        }
        
        // Context'i priority sırasına göre oluştur
        $contextParts = [];
        
        // 🔥 PRİORİTY 1 - MARKA KİMLİĞİ (Her zaman dahil)
        if ($maxPriority >= 1) {
            $brandIdentity = $this->buildBrandIdentityContext();
            if ($brandIdentity) {
                $contextParts[] = "## 🎯 MARKA KİMLİĞİ (Birincil Odak)\n" . $brandIdentity;
            }
        }
        
        // ⚡ PRİORİTY 2 - İŞ STRATEJİSİ VE DAVRANIŞI (Önemli)
        if ($maxPriority >= 2) {
            $businessStrategy = $this->buildBusinessStrategyContext();
            if ($businessStrategy) {
                $contextParts[] = "## ⚡ İŞ STRATEJİSİ VE DAVRANIŞI\n" . $businessStrategy;
            }
        }
        
        // 📊 PRİORİTY 3 - DETAY BİLGİLER (Normal)
        if ($maxPriority >= 3) {
            $details = $this->buildDetailContext();
            if ($details) {
                $contextParts[] = "## 📊 DETAY BİLGİLER\n" . $details;
            }
        }
        
        // 📍 PRİORİTY 4 - EK BİLGİLER (Opsiyonel - sadece gerektiğinde)
        if ($maxPriority >= 4) {
            $additionalInfo = $this->buildAdditionalContext();
            if ($additionalInfo) {
                $contextParts[] = "## 📍 EK BİLGİLER (Gerekmedikçe kullanma)\n" . $additionalInfo;
            }
        }
        
        $finalContext = implode("\n\n", $contextParts);
        
        // 30 dakika cache'le
        \Cache::put($cacheKey, $finalContext, now()->addMinutes(30));
        
        return $finalContext;
    }
    
    /**
     * Priority 1: Marka Kimliği - EN ÖNEMLİ
     */
    public function buildBrandIdentityContext(): string
    {
        $parts = [];
        
        // Firma adı (olmazsa olmaz)
        if (isset($this->company_info['brand_name'])) {
            $parts[] = "**Firma:** {$this->company_info['brand_name']}";
        }
        
        // Ana hizmet (olmazsa olmaz)
        if (isset($this->company_info['main_service'])) {
            $parts[] = "**Ana Uzmanlık:** {$this->company_info['main_service']}";
        }
        
        // Marka kişiliği (çok önemli - yazı tonunu belirler)
        if (isset($this->sector_details['brand_personality'])) {
            $personalities = array_keys(array_filter($this->sector_details['brand_personality']));
            if (!empty($personalities)) {
                $parts[] = "**Marka Kişiliği:** " . implode(', ', $personalities);
            }
        }
        
        // Yazı tonu (çok önemli - nasıl yazacağını belirler)
        if (isset($this->ai_behavior_rules['writing_tone'])) {
            $tones = array_keys(array_filter($this->ai_behavior_rules['writing_tone']));
            if (!empty($tones)) {
                $parts[] = "**Yazı Tonu:** " . implode(', ', $tones);
            }
        }
        
        return implode("\n", $parts);
    }
    
    /**
     * Priority 2: İş Stratejisi ve Davranışı - ÖNEMLİ
     */
    public function buildBusinessStrategyContext(): string
    {
        $parts = [];
        
        // Hedef kitle (önemli - kime hitap edeceğini belirler)
        if (isset($this->sector_details['target_audience'])) {
            $audiences = array_keys(array_filter($this->sector_details['target_audience']));
            if (!empty($audiences)) {
                $parts[] = "**Hedef Kitle:** " . implode(', ', $audiences);
            }
        }
        
        // Vurgu noktaları (önemli - neyi öne çıkaracağını belirler)
        if (isset($this->ai_behavior_rules['emphasis_points'])) {
            $emphasis = array_keys(array_filter($this->ai_behavior_rules['emphasis_points']));
            if (!empty($emphasis)) {
                $parts[] = "**Vurgu Noktaları:** " . implode(', ', $emphasis);
            }
        }
        
        // Rekabet avantajı (önemli)
        if (isset($this->ai_behavior_rules['competitive_advantage'])) {
            $advantages = array_keys(array_filter($this->ai_behavior_rules['competitive_advantage']));
            if (!empty($advantages)) {
                $parts[] = "**Rekabet Avantajı:** " . implode(', ', $advantages);
            }
        }
        
        // İletişim tarzı
        if (isset($this->ai_behavior_rules['communication_style'])) {
            $styles = array_keys(array_filter($this->ai_behavior_rules['communication_style']));
            if (!empty($styles)) {
                $parts[] = "**İletişim Tarzı:** " . implode(', ', $styles);
            }
        }
        
        // Kaçınılacak konular (önemli - ne yazmaması gerektiğini belirler)
        if (isset($this->ai_behavior_rules['avoid_topics'])) {
            $avoid = array_keys(array_filter($this->ai_behavior_rules['avoid_topics']));
            if (!empty($avoid)) {
                $parts[] = "**Kaçınılacak Konular:** " . implode(', ', $avoid);
            }
        }
        
        return implode("\n", $parts);
    }
    
    /**
     * Priority 3: Detay Bilgiler - NORMAL
     */
    public function buildDetailContext(): string
    {
        $parts = [];
        
        // Şirket büyüklüğü
        if (isset($this->sector_details['company_size'])) {
            $parts[] = "**Şirket Büyüklüğü:** {$this->sector_details['company_size']}";
        }
        
        // Marka yaşı avantajı
        if (isset($this->ai_behavior_rules['company_age_advantage'])) {
            $parts[] = "**Deneyim Avantajı:** {$this->ai_behavior_rules['company_age_advantage']}";
        }
        
        // Başarı göstergeleri
        if (isset($this->ai_behavior_rules['success_indicators'])) {
            $indicators = array_keys(array_filter($this->ai_behavior_rules['success_indicators']));
            if (!empty($indicators)) {
                $parts[] = "**Başarı Göstergeleri:** " . implode(', ', $indicators);
            }
        }
        
        return implode("\n", $parts);
    }
    
    /**
     * Priority 4: Ek Bilgiler - OPSİYONEL (sadece gerektiğinde)
     */
    public function buildAdditionalContext(): string
    {
        $parts = [];
        
        // Şehir bilgisi (sadece gerektiğinde - content'te lokasyon önemli ise)
        if (isset($this->company_info['city'])) {
            $parts[] = "**Lokasyon:** {$this->company_info['city']} (Sadece lokasyon önemli ise belirt)";
        }
        
        // Şube durumu
        if (isset($this->sector_details['branches'])) {
            $parts[] = "**Şube Durumu:** {$this->sector_details['branches']}";
        }
        
        // İletişim kanalları
        if (isset($this->company_info['contact_info'])) {
            $channels = array_keys(array_filter($this->company_info['contact_info']));
            if (!empty($channels)) {
                $parts[] = "**İletişim Kanalları:** " . implode(', ', $channels);
            }
        }
        
        return implode("\n", $parts);
    }
    
    /**
     * Context cache'ini temizle (profil güncellendiğinde)
     */
    public function clearContextCache(): void
    {
        $patterns = [
            "ai_context_optimized_{$this->tenant_id}_*",
            "ai_context_legacy_{$this->tenant_id}",
            "ai_tenant_profile_{$this->tenant_id}"
        ];
        
        foreach ($patterns as $pattern) {
            \Cache::forget($pattern);
            // Pattern ile cache temizleme
            try {
                $keys = \Cache::getRedis()->keys($pattern);
                if (!empty($keys)) {
                    \Cache::getRedis()->del($keys);
                }
            } catch (\Exception $e) {
                // Context cache pattern clear failed
            }
        }
    }

    /**
     * Merkezi completion percentage hesaplama fonksiyonu
     * Show ve Edit sayfalarında tutarlı hesaplama için
     */
    public function getCompletionPercentage(): array
    {
        $totalFields = 0;
        $completedFields = 0;
        $sections = [];
        
        // Step 1: Sektör seçimi (1 alan)
        $sectorCompleted = !empty($this->sector_details['sector']);
        $sections['sector'] = ['completed' => $sectorCompleted, 'total' => 1];
        $totalFields += 1;
        if ($sectorCompleted) $completedFields += 1;
        
        // Step 2: Temel bilgiler (4 ana alan)
        $companyFields = ['brand_name', 'city', 'main_service'];
        $companyCompleted = 0;
        $companyTotal = count($companyFields);
        
        foreach ($companyFields as $field) {
            if (!empty($this->company_info[$field])) {
                $companyCompleted++;
            }
        }
        
        // İletişim bilgileri (en az bir kanal seçilmişse)
        $contactCompleted = false;
        if (isset($this->company_info['contact_info']) && is_array($this->company_info['contact_info'])) {
            foreach ($this->company_info['contact_info'] as $contact => $value) {
                if ($value) {
                    $contactCompleted = true;
                    break;
                }
            }
        }
        if ($contactCompleted) $companyCompleted++;
        $companyTotal++;
        
        $sections['company'] = ['completed' => $companyCompleted, 'total' => $companyTotal];
        $totalFields += $companyTotal;
        $completedFields += $companyCompleted;
        
        // Step 3: Marka detayları (6 ana alan + sektöre özel)
        $brandFields = ['brand_personality', 'brand_age', 'company_size', 'branches', 'target_audience', 'market_position'];
        $brandCompleted = 0;
        $brandTotal = count($brandFields);
        
        foreach ($brandFields as $field) {
            if (isset($this->sector_details[$field])) {
                if (is_array($this->sector_details[$field])) {
                    // Array alanları için en az bir seçim yapılmış mı?
                    $hasSelection = false;
                    foreach ($this->sector_details[$field] as $value) {
                        if ($value) {
                            $hasSelection = true;
                            break;
                        }
                    }
                    if ($hasSelection) $brandCompleted++;
                } else {
                    // String alanları için boş mu?
                    if (!empty($this->sector_details[$field])) {
                        $brandCompleted++;
                    }
                }
            }
        }
        
        // Sektöre özel sorular
        $currentSector = $this->sector_details['sector'] ?? null;
        if ($currentSector) {
            $sectorQuestions = \Modules\AI\app\Models\AIProfileQuestion::where('step', 3)
                ->where('sector_code', $currentSector)
                ->get();
            
            $sectorSpecificCompleted = 0;
            $sectorSpecificTotal = $sectorQuestions->count();
            
            foreach ($sectorQuestions as $question) {
                $fieldKey = $question->question_key;
                if (isset($this->sector_details[$fieldKey]) && !empty($this->sector_details[$fieldKey])) {
                    $sectorSpecificCompleted++;
                }
            }
            
            $brandCompleted += $sectorSpecificCompleted;
            $brandTotal += $sectorSpecificTotal;
        }
        
        $sections['brand'] = ['completed' => $brandCompleted, 'total' => $brandTotal];
        $totalFields += $brandTotal;
        $completedFields += $brandCompleted;
        
        // Step 4: Kurucu bilgileri (1 zorunlu + isteğe bağlı)
        $founderCompleted = 0;
        $founderTotal = 1; // founder_permission
        
        $founderPermission = $this->company_info['founder_permission'] ?? 'no';
        if (!empty($founderPermission)) {
            $founderCompleted++;
            
            // Eğer kurucu bilgileri aktifse ek alanlar
            if (in_array($founderPermission, ['yes_full', 'yes_limited'])) {
                $founderFields = ['founder_name', 'founder_title', 'founder_background', 'founder_qualities'];
                $founderFieldsCompleted = 0;
                $founderFieldsTotal = count($founderFields);
                
                foreach ($founderFields as $field) {
                    if (isset($this->founder_info[$field])) {
                        if (is_array($this->founder_info[$field])) {
                            // Array alanları için en az bir seçim
                            foreach ($this->founder_info[$field] as $value) {
                                if ($value) {
                                    $founderFieldsCompleted++;
                                    break;
                                }
                            }
                        } else {
                            // String alanları için boş mu?
                            if (!empty($this->founder_info[$field])) {
                                $founderFieldsCompleted++;
                            }
                        }
                    }
                }
                
                $founderCompleted += $founderFieldsCompleted;
                $founderTotal += $founderFieldsTotal;
            }
        }
        
        $sections['founder'] = ['completed' => $founderCompleted, 'total' => $founderTotal];
        $totalFields += $founderTotal;
        $completedFields += $founderCompleted;
        
        // Step 5: Başarı hikayeleri (3 alan - isteğe bağlı)
        $successFields = ['major_projects', 'client_references', 'success_metrics'];
        $successCompleted = 0;
        $successTotal = count($successFields);
        
        foreach ($successFields as $field) {
            if (isset($this->success_stories[$field]) && !empty($this->success_stories[$field])) {
                $successCompleted++;
            }
        }
        
        $sections['success'] = ['completed' => $successCompleted, 'total' => $successTotal];
        $totalFields += $successTotal;
        $completedFields += $successCompleted;
        
        // Step 6: AI davranış kuralları (dinamik soru sayısı)
        $aiQuestions = \Modules\AI\app\Models\AIProfileQuestion::where('step', 6)->get();
        $aiCompleted = 0;
        $aiTotal = $aiQuestions->count();
        
        foreach ($aiQuestions as $question) {
            $fieldKey = $question->question_key;
            
            if ($question->input_type === 'checkbox') {
                // Checkbox için en az bir seçim
                $hasSelection = false;
                if (isset($this->ai_behavior_rules[$fieldKey]) && is_array($this->ai_behavior_rules[$fieldKey])) {
                    foreach ($this->ai_behavior_rules[$fieldKey] as $value) {
                        if ($value) {
                            $hasSelection = true;
                            break;
                        }
                    }
                }
                if ($hasSelection) $aiCompleted++;
            } else {
                // Normal alanlar için
                if (isset($this->ai_behavior_rules[$fieldKey]) && !empty($this->ai_behavior_rules[$fieldKey])) {
                    $aiCompleted++;
                }
            }
        }
        
        $sections['ai_behavior'] = ['completed' => $aiCompleted, 'total' => $aiTotal];
        $totalFields += $aiTotal;
        $completedFields += $aiCompleted;
        
        // Genel percentage hesaplama
        $percentage = $totalFields > 0 ? round(($completedFields / $totalFields) * 100) : 0;
        
        return [
            'percentage' => $percentage,
            'completed' => $completedFields,
            'total' => $totalFields,
            'sections' => $sections
        ];
    }

    /**
     * Belirli bir bölümü güncelle
     */
    public function updateSection(string $section, array $data): bool
    {
        // Section update called
        
        // Mevcut section verisini kontrol et
        $currentValue = $this->$section;
        // Current section value retrieved
        
        // Array merge işlemi
        $mergedData = array_merge($currentValue ?? [], $data);
        // Data merged successfully
        
        // Değeri set et
        $this->$section = $mergedData;
        // Section value set
        
        // Model durumunu logla
        // Model ready for save
        
        // Save işlemi
        try {
            $result = $this->save();
            
            // Cache temizle (önemli - profil güncellendiğinde)
            $this->clearContextCache();
            
            // Save operation completed
            return $result;
        } catch (\Exception $e) {
            // Save operation failed
            throw $e;
        }
    }
    
    /**
     * AI için normalize edilmiş profil verilerini getir
     */
    public function getAIFriendlyData(): array
    {
        $data = [
            'basic_info' => [],
            'sector_info' => [],
            'business_details' => [],
            'founder_info' => [],
            'success_stories' => [],
            'ai_behavior' => []
        ];
        
        // Temel bilgiler (company_info) - priority ile
        if ($this->company_info) {
            foreach ($this->company_info as $key => $value) {
                if (is_string($value) && !empty($value)) {
                    $data['basic_info'][$key] = [
                        'value' => $value,
                        'label' => $this->getFieldLabel($key, $value),
                        'question' => $this->getFieldQuestion($key),
                        'priority' => $this->getFieldPriority($key)
                    ];
                }
            }
        }
        
        // Sektör bilgileri (sector_details) - priority ile
        if ($this->sector_details) {
            foreach ($this->sector_details as $key => $value) {
                if (is_string($value) && !empty($value)) {
                    $data['sector_info'][$key] = [
                        'value' => $value,
                        'label' => $this->getFieldLabel($key, $value),
                        'question' => $this->getFieldQuestion($key),
                        'priority' => $this->getFieldPriority($key)
                    ];
                } elseif (is_array($value) && !empty($value)) {
                    $data['sector_info'][$key] = [
                        'values' => $value,
                        'labels' => $this->getFieldLabels($key, $value),
                        'question' => $this->getFieldQuestion($key),
                        'priority' => $this->getFieldPriority($key)
                    ];
                }
            }
        }
        
        // Başarı hikayeleri
        if ($this->success_stories) {
            foreach ($this->success_stories as $key => $value) {
                if (is_string($value) && !empty($value)) {
                    $data['success_stories'][$key] = [
                        'value' => $value,
                        'question' => $this->getFieldQuestion($key),
                        'priority' => $this->getFieldPriority($key)
                    ];
                } elseif (is_array($value) && !empty($value)) {
                    $data['success_stories'][$key] = [
                        'values' => $value,
                        'labels' => $this->getFieldLabels($key, $value),
                        'question' => $this->getFieldQuestion($key),
                        'priority' => $this->getFieldPriority($key)
                    ];
                }
            }
        }
        
        // AI davranış kuralları
        if ($this->ai_behavior_rules) {
            foreach ($this->ai_behavior_rules as $key => $value) {
                if (is_string($value) && !empty($value)) {
                    $data['ai_behavior'][$key] = [
                        'value' => $value,
                        'label' => $this->getFieldLabel($key, $value),
                        'question' => $this->getFieldQuestion($key),
                        'priority' => $this->getFieldPriority($key)
                    ];
                } elseif (is_array($value) && !empty($value)) {
                    $data['ai_behavior'][$key] = [
                        'values' => $value,
                        'labels' => $this->getFieldLabels($key, $value),
                        'question' => $this->getFieldQuestion($key),
                        'priority' => $this->getFieldPriority($key)
                    ];
                }
            }
        }
        
        // Kurucu bilgileri
        if ($this->founder_info) {
            foreach ($this->founder_info as $key => $value) {
                if (is_string($value) && !empty($value)) {
                    $data['founder_info'][$key] = [
                        'value' => $value,
                        'question' => $this->getFieldQuestion($key),
                        'priority' => $this->getFieldPriority($key)
                    ];
                }
            }
        }
        
        return $data;
    }
    
    /**
     * Alan için soru metnini getir
     */
    private function getFieldQuestion(string $fieldKey): ?string
    {
        $question = \Modules\AI\app\Models\AIProfileQuestion::where('question_key', $fieldKey)->first();
        return $question ? $question->question_text : null;
    }
    
    /**
     * Alan için label getir (select/radio)
     */
    private function getFieldLabel(string $fieldKey, string $value): ?string
    {
        $question = \Modules\AI\app\Models\AIProfileQuestion::where('question_key', $fieldKey)->first();
        
        if (!$question || !$question->options) {
            return null;
        }
        
        $options = is_array($question->options) 
            ? $question->options 
            : json_decode($question->options, true);
            
        if (!is_array($options)) {
            return null;
        }
        
        foreach ($options as $option) {
            $optionValue = is_array($option) ? ($option['value'] ?? $option) : $option;
            $optionLabel = is_array($option) ? ($option['label'] ?? $option) : $option;
            
            if ($optionValue === $value) {
                return $optionLabel;
            }
        }
        
        return null;
    }
    
    /**
     * Alan için label'ları getir (checkbox)
     */
    private function getFieldLabels(string $fieldKey, array $values): array
    {
        $question = \Modules\AI\app\Models\AIProfileQuestion::where('question_key', $fieldKey)->first();
        
        if (!$question || !$question->options) {
            return [];
        }
        
        $options = is_array($question->options) 
            ? $question->options 
            : json_decode($question->options, true);
            
        if (!is_array($options)) {
            return [];
        }
        
        $labels = [];
        foreach ($values as $value) {
            foreach ($options as $option) {
                $optionValue = is_array($option) ? ($option['value'] ?? $option) : $option;
                $optionLabel = is_array($option) ? ($option['label'] ?? $option) : $option;
                
                if ($optionValue === $value) {
                    $labels[] = $optionLabel;
                    break;
                }
            }
        }
        
        return $labels;
    }
    
    /**
     * Alan için priority değerini getir (AIPriorityEngine uyumlu)
     */
    private function getFieldPriority(string $fieldKey): int
    {
        // Priority mapping - AIPriorityEngine ile uyumlu (1-5 arası)
        $priorityMap = [
            // Priority 1 (Critical): 1.5x multiplier - Marka kimliği ve temel işletme bilgisi
            'brand_name' => 1,
            'sector_selection' => 1,
            'main_service' => 1,
            'communication_style' => 1,
            'response_style' => 1,
            
            // Priority 2 (Important): 1.2x multiplier - Önemli iş stratejisi
            'target_audience' => 2,
            'business_size' => 2,
            'brand_voice' => 2,
            'competitive_advantage' => 2,
            'experience_years' => 2,
            'tech_specialization' => 2,
            'medical_specialties' => 2,
            'education_levels' => 2,
            'cuisine_type' => 2,
            'product_categories' => 2,
            'construction_types' => 2,
            'finance_services' => 2,
            'production_type' => 2,
            
            // Priority 3 (Normal): 1.0x multiplier - Standart detaylar
            'service_area' => 3,
            'success_story' => 3,
            'customer_testimonial' => 3,
            'forbidden_topics' => 3,
            'project_duration' => 3,
            'appointment_system' => 3,
            'education_format' => 3,
            'service_style' => 3,
            'sales_channels' => 3,
            'project_scale' => 3,
            'client_segments' => 3,
            'production_capacity' => 3,
            
            // Priority 4 (Optional): 0.6x multiplier - Opsiyonel bilgiler
            'city' => 4,
            'founder_story' => 4,
            'biggest_challenge' => 4,
            'support_model' => 4,
            'insurance_acceptance' => 4,
            'success_tracking' => 4,
            'special_features' => 4,
            'shipping_payment' => 4,
            'services_included' => 4,
            'digital_tools' => 4,
            'quality_certifications' => 4,
        ];
        
        return $priorityMap[$fieldKey] ?? 3; // Varsayılan normal öncelik
    }
    
    /**
     * Priority'ye göre multiplier getir (AIPriorityEngine uyumlu)
     */
    private function getPriorityMultiplier(int $priority): float
    {
        // AIPriorityEngine ile aynı multiplier sistemi
        $multipliers = [
            1 => 1.5,   // Critical: %50 boost
            2 => 1.2,   // Important: %20 boost
            3 => 1.0,   // Normal: No change
            4 => 0.6,   // Optional: %40 penalty
            5 => 0.3,   // Rarely used: %70 penalty
        ];
        
        return $multipliers[$priority] ?? 1.0;
    }
    
    /**
     * Priority'ye göre sıralanmış profil verilerini getir
     */
    public function getAIFriendlyDataSorted(int $maxPriority = 3): array
    {
        $data = $this->getAIFriendlyData();
        $sortedData = [];
        
        // Tüm alanları priority'ye göre düzle ve sırala
        foreach ($data as $sectionName => $fields) {
            foreach ($fields as $fieldKey => $fieldData) {
                $priority = $fieldData['priority'] ?? 3;
                $multiplier = $this->getPriorityMultiplier($priority);
                
                // Priority threshold kontrolü
                if ($priority <= $maxPriority) {
                    $sortedData[] = [
                        'section' => $sectionName,
                        'key' => $fieldKey,
                        'priority' => $priority,
                        'multiplier' => $multiplier,
                        'data' => $fieldData
                    ];
                }
            }
        }
        
        // Priority'ye göre sırala (düşük priority = yüksek önem)
        usort($sortedData, function($a, $b) {
            return $a['priority'] <=> $b['priority'];
        });
        
        return $sortedData;
    }
}
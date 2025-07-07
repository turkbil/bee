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
        
        \Log::info('AITenantProfile tenant ID resolved', [
            'tenant_id' => $tenantId,
            'source' => tenant('id') ? 'tenant_context' : (auth()->user()?->tenant_id ? 'user_tenant' : 'latest_tenant'),
            'user_tenant_id' => auth()->user()?->tenant_id,
            'latest_tenant_used' => !tenant('id') && !auth()->user()?->tenant_id
        ]);
        
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
        
        // Firma temel bilgileri
        if ($this->company_info) {
            $companySection = "## Firma Bilgileri\n";
            
            if (isset($this->company_info['brand_name'])) {
                $companySection .= "**Firma Adı:** {$this->company_info['brand_name']}\n";
            }
            
            if (isset($this->company_info['city'])) {
                $companySection .= "**Konum:** {$this->company_info['city']}\n";
            }
            
            if (isset($this->company_info['main_service'])) {
                $companySection .= "**Ana Hizmet:** {$this->company_info['main_service']}\n";
            }
            
            if (isset($this->company_info['founding_year'])) {
                $companySection .= "**Kuruluş Yılı:** {$this->company_info['founding_year']}\n";
            }
            
            if (isset($this->company_info['employee_count'])) {
                $companySection .= "**Çalışan Sayısı:** {$this->company_info['employee_count']}\n";
            }
            
            $summary[] = $companySection;
        }
        
        // Sektör bilgileri
        if ($this->sector_details) {
            $sectorSection = "## Sektör ve Faaliyet Alanı\n";
            
            if (isset($this->sector_details['sector'])) {
                $sector = \Modules\AI\app\Models\AIProfileSector::where('code', $this->sector_details['sector'])->first();
                if ($sector) {
                    $sectorSection .= "**Sektör:** {$sector->name}\n";
                    $sectorSection .= "**Sektör Açıklaması:** {$sector->description}\n";
                }
            }
            
            // Marka kişiliği
            if (isset($this->sector_details['brand_personality'])) {
                $personalities = array_keys(array_filter($this->sector_details['brand_personality']));
                if (!empty($personalities)) {
                    $sectorSection .= "**Marka Kişiliği:** " . implode(', ', $personalities) . "\n";
                }
            }
            
            // Hedef kitle
            if (isset($this->sector_details['target_audience'])) {
                $audiences = array_keys(array_filter($this->sector_details['target_audience']));
                if (!empty($audiences)) {
                    $sectorSection .= "**Hedef Kitle:** " . implode(', ', $audiences) . "\n";
                }
            }
            
            $summary[] = $sectorSection;
        }
        
        // Başarı hikayeleri
        if ($this->success_stories && !empty(array_filter($this->success_stories))) {
            $successSection = "## Başarılar ve Deneyimler\n";
            
            foreach ($this->success_stories as $key => $story) {
                if (!empty($story)) {
                    $keyFormatted = ucfirst(str_replace('_', ' ', $key));
                    $successSection .= "**{$keyFormatted}:** {$story}\n";
                }
            }
            
            $summary[] = $successSection;
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
        // AI Helper kullanarak marka hikayesi oluştur
        $context = $this->getAIContext();
        
        $prompt = $this->buildBrandStoryPrompt($context);
        
        try {
            // Direkt ai_brand_story_creator helper fonksiyonunu kullan
            $result = ai_brand_story_creator($prompt, [
                'industry' => $context['sector'] ?? 'general',
                'stage' => 'growth',
                'mission' => 'customer_focused',
                'values' => implode(', ', $context['emphasis'] ?? ['quality']),
                'audience' => $context['target_audience'] ?? 'general',
                'unique_factor' => 'innovation'
            ]);
            
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
                'tenant_id' => $this->tenant_id,
                'story_length' => strlen($response),
                'helper_used' => 'ai_brand_story_creator'
            ]);
            
            return $response;
        } catch (\Exception $e) {
            \Log::error('Brand story generation failed', [
                'tenant_id' => $this->tenant_id,
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
        
        // Şehir bilgisi
        if (isset($this->company_info['city'])) {
            $prompt .= "Konum: " . $this->company_info['city'] . "\n";
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
                    $prompt .= "- " . ucfirst(str_replace('_', ' ', $key)) . ": " . $story . "\n";
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
        $prompt .= "1. Etkileyici ve duygusal bir marka hikayesi oluştur\n";
        $prompt .= "2. Hikaye 3-4 paragraf olsun\n";
        $prompt .= "3. Markanın değerlerini ve misyonunu vurgula\n";
        $prompt .= "4. Müşterilere neden bu markayı seçmeleri gerektiğini anlat\n";
        $prompt .= "5. Pozitif ve ilham verici bir ton kullan\n\n";
        
        return $prompt;
    }

    /**
     * Belirli bir bölümü güncelle
     */
    public function updateSection(string $section, array $data): bool
    {
        \Log::info("AITenantProfile::updateSection called", [
            'section' => $section,
            'data' => $data,
            'tenant_id' => $this->tenant_id,
            'model_id' => $this->id
        ]);
        
        // Mevcut section verisini kontrol et
        $currentValue = $this->$section;
        \Log::info("Current section value", [
            'section' => $section,
            'current_value' => $currentValue,
            'is_array' => is_array($currentValue),
            'is_null' => is_null($currentValue)
        ]);
        
        // Array merge işlemi
        $mergedData = array_merge($currentValue ?? [], $data);
        \Log::info("After array merge", [
            'section' => $section,
            'merged_data' => $mergedData,
            'data_count' => count($mergedData)
        ]);
        
        // Değeri set et
        $this->$section = $mergedData;
        \Log::info("After setting section", [
            'section' => $section,
            'new_value' => $this->$section,
            'is_dirty' => $this->isDirty($section)
        ]);
        
        // Model durumunu logla
        \Log::info("Model state before save", [
            'section' => $section,
            'data_being_saved' => $data,
            'merged_data' => $mergedData,
            'dirty_attributes' => $this->getDirty(),
            'is_dirty' => $this->isDirty(),
            'current_timestamps' => [
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at
            ]
        ]);
        
        // Save işlemi
        try {
            $result = $this->save();
            \Log::info("Save operation result", [
                'success' => $result,
                'section' => $section,
                'final_value' => $this->fresh()->$section
            ]);
            return $result;
        } catch (\Exception $e) {
            \Log::error("Save operation failed", [
                'error' => $e->getMessage(),
                'section' => $section,
                'data' => $data,
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}
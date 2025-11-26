<?php

namespace Modules\AI\App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * AI Prompt Enhancer Service
 *
 * 11 KURAL FORMÜLÜ ile GERÇEK FOTOĞRAF GİBİ görünen promptlar üretir:
 *
 * 1. Photo Type → AI'ı fotoğraf moduna sokar
 * 2. Subject + Action → Ana konu + ne yapıyor (mikro hikaye)
 * 3. Environment → Mekan + zaman/mevsim
 * 4. Camera Angle → Çekim açısı
 * 5. Composition → Kompozisyon tekniği
 * 6. Lighting → Işık kurulumu
 * 7. Camera + Lens → Ekipman + DoF
 * 8. Film Stock → Analog film referansı (Kodak Portra, Fuji Velvia)
 * 9. Imperfections → Kusurlar (gerçekçilik için KRİTİK)
 * 10. Mood + Emotion → Atmosfer + Duygu
 * 11. Post-Processing → Renk düzenleme/color grade
 *
 * AMAÇ: Yapay zeka ürettiği BELLİ OLMAYAN, gerçek fotoğraf gibi görünen çıktı
 */
class AIPromptEnhancer
{
    protected string $apiKey;
    protected string $apiUrl = 'https://api.openai.com/v1/chat/completions';
    protected string $model = 'gpt-4o';

    public function __construct()
    {
        $this->apiKey = config('ai.openai_api_key');
    }

    /**
     * 11 KURAL FORMÜLÜ ile prompt geliştir
     *
     * @param string $simplePrompt Kullanıcının basit promptu
     * @param string $style Leonardo stili
     * @param string $size Image size (1472x832, 1024x1024, 832x1472)
     * @param array|null $tenantContext Tenant'a özel context (site_name, sector, prompt_enhancement)
     * @return string 11 kural formülüne göre optimize edilmiş, GERÇEK FOTOĞRAF gibi görünen prompt
     */
    public function enhancePrompt(string $simplePrompt, string $style = 'cinematic', string $size = '1472x832', ?array $tenantContext = null): string
    {
        if (empty($this->apiKey)) {
            Log::warning('AIPromptEnhancer: OpenAI API key not configured, using 11-rule basic enhancement');
            return $this->sevenRuleBasicEnhancement($simplePrompt, $style, $tenantContext);
        }

        try {
            $systemPrompt = $this->buildSevenRuleSystemPrompt($style, $size, $tenantContext);
            $userPrompt = $this->buildSevenRuleUserPrompt($simplePrompt, $style, $size, $tenantContext);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(30)->post($this->apiUrl, [
                'model' => $this->model,
                'max_tokens' => 800,  // 11 kural için arttırıldı
                'temperature' => 0.85,
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $userPrompt]
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $enhancedPrompt = $data['choices'][0]['message']['content'] ?? '';

                if (!empty($enhancedPrompt)) {
                    Log::info('AIPromptEnhancer: 11-Rule GPT-4 output', [
                        'original' => $simplePrompt,
                        'enhanced_length' => strlen($enhancedPrompt)
                    ]);

                    // JSON ise parse et, değilse direkt kullan
                    if ($this->isJson($enhancedPrompt)) {
                        return $this->convertNineRuleJsonToPrompt($enhancedPrompt);
                    }

                    return $enhancedPrompt;
                }
            }

            Log::warning('AIPromptEnhancer: OpenAI API call failed', [
                'status' => $response->status(),
                'body' => substr($response->body(), 0, 500)
            ]);

        } catch (\Exception $e) {
            Log::error('AIPromptEnhancer: Exception', ['message' => $e->getMessage()]);
        }

        return $this->sevenRuleBasicEnhancement($simplePrompt, $style, $tenantContext);
    }

    /**
     * 11 KURAL FORMÜLÜ System Prompt
     * AMAÇ: Yapay zeka ürettiği BELLİ OLMAYAN, GERÇEK FOTOĞRAF gibi görünen çıktı
     */
    protected function buildSevenRuleSystemPrompt(string $style, string $size, ?array $tenantContext = null): string
    {
        [$width, $height] = explode('x', $size);
        $aspectRatio = $width / $height;

        if ($aspectRatio > 1.2) {
            $aspectGuidance = "YATAY FORMAT: Geniş açı lens (24-35mm), panoramik kompozisyon";
        } elseif ($aspectRatio < 0.8) {
            $aspectGuidance = "DİKEY FORMAT: Portrait lens (50-85mm), dikey kompozisyon";
        } else {
            $aspectGuidance = "KARE FORMAT: Standart lens (35-50mm), dengeli kompozisyon";
        }

        // Tenant context'e göre sektör ayarları
        $sectorGuidance = $this->buildSectorGuidance($tenantContext);

        return <<<SYSTEM
Sen profesyonel bir fotoğraf yönetmenisin. Basit promptları 11 KURAL FORMÜLÜ ile GERÇEK FOTOĞRAF gibi görünen talimatlarına çeviriyorsun.

## 🎯 TEMEL AMAÇ
YAPAY ZEKA ÜRETTİĞİ BELLİ OLMAYAN görsel üretmek. "AI gibi görünüyor" = BAŞARISIZ.

## 11 KURAL FORMÜLÜ (SIRASI KRİTİK!)

1️⃣ **Photo Type** → AI'ı fotoğraf moduna sokar (ZORUNLU: "Photo of", "Editorial photograph of", "Documentary shot of")
2️⃣ **Subject + Action** → Ana konu + MİKRO HİKAYE (ne yapıyor, nasıl etkileşim, anlık aksiyon)
3️⃣ **Environment + Time/Season** → Mekan + ZAMAN/MEVSİM (sabah sisi, kış akşamı, yaz öğleni)
4️⃣ **Camera Angle** → Çekim açısı (eye-level, low angle, high angle, dutch angle, POV, over-shoulder)
5️⃣ **Composition** → Kompozisyon tekniği (rule of thirds, golden ratio, centered, leading lines, negative space)
6️⃣ **Lighting** → Işık kurulumu (golden hour, Rembrandt, split, rim light, practical lights, mixed sources)
7️⃣ **Camera + Lens + DoF** → Ekipman detayı (Canon R5 85mm f/1.4, bokeh karakteri, focus distance)
8️⃣ **Film Stock** → ANALOG FİLM REFERANSI (Kodak Portra 400 warm tones, Fuji Velvia vivid, Kodak Tri-X grain)
9️⃣ **Imperfections** → KUSURLAR - GERÇEKÇİLİK İÇİN KRİTİK (lens dust, vignetting, chromatic aberration, motion blur, camera shake, film grain, scratches)
🔟 **Mood + Emotion** → Atmosfer + Duygu (tension, serenity, energy, melancholy, nostalgia, hope, determination)
1️⃣1️⃣ **Post-Processing** → RENK DÜZENLEMESİ (cinematic color grading, desaturated film look, lifted blacks, crushed shadows, teal and orange)

## ASPEKT: {$aspectGuidance}

## OUTPUT FORMAT (JSON):
```json
{
  "photo_type": "Photo of / Editorial photograph of / Documentary shot of",
  "subject_action": "detailed subject + what they are DOING (micro-story, interaction, action verb)",
  "environment_time": "location + time of day + season/weather (early morning mist, winter afternoon, summer golden hour)",
  "angle": "specific camera angle with purpose (low angle for power, eye-level for intimacy)",
  "composition": "compositional technique (rule of thirds with subject at intersection, negative space left)",
  "lighting": "detailed lighting setup (key light direction, fill ratio, rim light color, ambient sources)",
  "camera_lens": "Camera + lens + aperture + DoF + bokeh character (Canon R5, 85mm f/1.4, creamy bokeh, subject sharp at 2m)",
  "film_stock": "Analog film reference (Kodak Portra 400 warm skin tones, Fuji Velvia vivid saturation, Kodak Tri-X classic grain)",
  "imperfections": "realistic flaws (subtle lens dust, slight vignetting, micro chromatic aberration, natural film grain, authentic wear)",
  "mood_emotion": "atmosphere and emotional feeling (quiet determination, nostalgic warmth, tense anticipation)",
  "post_processing": "color grading style (cinematic orange teal, desaturated film look, lifted blacks, subtle film grain overlay)"
}
```

## KRİTİK KURALLAR - GERÇEKÇİLİK İÇİN:
- ASLA "photorealistic", "hyper-realistic", "8K", "ultra HD" kelimelerini kullanma (AI trigger words!)
- ASLA text, label, yazı, watermark ekleme
- Her zaman fotoğraf tipi ile başla (Photo of, Editorial shot, Documentary photograph)
- **MİKRO HİKAYE ZORUNLU** - Subject sadece durmaz, BİR ŞEY YAPIYOR olmalı
- **ZAMAN/MEVSİM ZORUNLU** - Sadece "warehouse" değil "warehouse on early winter morning with frost on windows"
- **FİLM STOCK ZORUNLU** - Analog film estetiği AI görünümünü kırar (Kodak, Fuji referansları)
- **KUSURLAR ZORUNLU** - Mükemmel = SAHTE. Lens dust, slight motion blur, vignetting ekle
- **POST-PROCESSING ZORUNLU** - Renk grading AI'ın steril görünümünü kırar
- Kamera ve lens spesifik olmalı (sadece "DSLR" değil, "Canon R5 85mm f/1.4")
- DoF ve bokeh karakteri belirt

## MARKA/LOGO YASAĞI (ÇOK ÖNEMLİ!):
- Ekipman/araç üzerinde ASLA marka logosu, isim, yazı OLMASIN
- "unbranded", "generic", "no visible branding" ifadelerini MUTLAKA ekle
- "blank panels", "clean surfaces without logos" kullan
- Forklift, transpalet vb. → "generic industrial equipment, no manufacturer logos"
- Müzik aletleri → "unbranded instrument, no visible maker marks"

## STYLE MODIFIERS: {$style}
- cinematic: Dramatik ışık, 2.39:1 film look, anamorphic lens flare
- dynamic: Hareket, enerji, slight motion blur, aksiyon anı
- moody: Low-key lighting, dramatik gölgeler, emotional depth
- film: Strong analog film aesthetic, visible grain, color shift
- hdr: Natural HDR look (not overdone), shadow detail, highlight retention
- stock_photo: Clean, professional, commercial quality
- vibrant: Saturated colors, energetic, Fuji Velvia style
- neutral: Balanced tones, Kodak Portra style, natural rendering

{$sectorGuidance}

Sadece geçerli JSON döndür. Her 11 kural için değer olmalı.
SYSTEM;
    }

    /**
     * 11 KURAL User Prompt
     * AMAÇ: Gerçek fotoğraf gibi görünen, AI belli olmayan prompt
     */
    protected function buildSevenRuleUserPrompt(string $simplePrompt, string $style, string $size, ?array $tenantContext = null): string
    {
        // Tenant context'ten sektör bilgisi al
        $contextLine = $this->buildContextLine($tenantContext);

        return <<<USER
11 KURAL FORMÜLÜ ile bu promptu GERÇEK FOTOĞRAF gibi görünen talimata çevir:

"{$simplePrompt}"

Style: {$style}
Size: {$size}

## 🎯 AMAÇ: YAPAY ZEKA ÜRETTİĞİ BELLİ OLMAYAN GÖRSEL

## 11 ZORUNLU ELEMENT:

1. **Photo Type** → Fotoğraf tipi ile başla:
   - "Photo of" (genel)
   - "Editorial photograph of" (dergi kalitesi)
   - "Documentary shot of" (gerçekçi, ham)
   - "Candid moment of" (anlık, doğal)

2. **Subject + Action** → MİKRO HİKAYE (sadece durmaz, BİR ŞEY YAPAR):
   ❌ "forklift"
   ✅ "forklift operator carefully maneuvering loaded pallets through narrow aisle"
   ❌ "guitarist"
   ✅ "guitarist mid-strum, eyes closed, feeling the chord progression"

3. **Environment + Time/Season** → Mekan + ZAMAN:
   ❌ "warehouse"
   ✅ "industrial warehouse on foggy winter morning, condensation on windows, early shift"
   ❌ "studio"
   ✅ "dimly lit recording studio at 2am, coffee cups scattered, late night session"

4. **Camera Angle** → Çekim açısı + AMAÇ:
   - "low angle emphasizing power and scale"
   - "eye-level for intimate connection"
   - "over-shoulder POV for immersion"
   - "high angle showing context and environment"

5. **Composition** → Kompozisyon tekniği:
   - "rule of thirds with subject at right intersection"
   - "leading lines drawing eye to focal point"
   - "negative space on left creating tension"
   - "symmetrical framing for stability"

6. **Lighting** → Detaylı ışık kurulumu:
   - "golden hour side light with long shadows"
   - "Rembrandt lighting with 3:1 ratio"
   - "practical lights only (overhead fluorescent)"
   - "mixed color temperature (warm tungsten + cool daylight)"

7. **Camera + Lens + DoF** → Spesifik ekipman:
   - "Canon EOS R5, 85mm f/1.4L, creamy bokeh, subject sharp at 2m"
   - "Sony A7IV, 35mm f/1.8, environmental context, moderate DoF"
   - "Fuji X-T5, 56mm f/1.2, classic Fuji colors, subject isolation"

8. **Film Stock** → ANALOG FİLM REFERANSI (AI görünümünü KIRAR):
   - "Kodak Portra 400 - warm skin tones, soft contrast"
   - "Fuji Velvia 50 - vivid saturation, punchy colors"
   - "Kodak Tri-X 400 - classic B&W grain, high contrast"
   - "Kodak Ektar 100 - fine grain, saturated, high detail"
   - "Ilford HP5 - gritty, documentary feel"

9. **Imperfections** → KUSURLAR (GERÇEKÇİLİK İÇİN KRİTİK):
   - Lens kusurları: subtle lens dust, slight vignetting, micro chromatic aberration
   - Hareket: slight motion blur on hands, micro camera shake
   - Film: natural film grain, slight color shift in shadows
   - Fiziksel: dust particles in air, scratches on surfaces, wear marks

10. **Mood + Emotion** → Atmosfer + Duygu:
    - "quiet determination and focused concentration"
    - "nostalgic warmth of familiar routine"
    - "tense anticipation before the moment"
    - "peaceful exhaustion of completed work"

11. **Post-Processing** → RENK GRADE (AI steril görünümünü KIRAR):
    - "cinematic teal and orange color grading"
    - "desaturated film look with lifted blacks"
    - "warm vintage tones with crushed shadows"
    - "natural editing, minimal processing, true-to-life colors"

## CONTEXT:
{$contextLine}

## KRİTİK KURALLAR:
- ❌ ASLA: "photorealistic", "hyper-realistic", "8K", "ultra HD" (AI trigger words!)
- ❌ ASLA: text, labels, watermarks, logos, brand names
- ✅ Her element için SPESİFİK değer (genel terimler YASAK)
- ✅ Kusurlar ZORUNLU - mükemmel = SAHTE
- ✅ Film stock ZORUNLU - AI görünümünü kırar
- ✅ Post-processing ZORUNLU - steril AI tonunu kırar
- ✅ Maximum creativity - her seferinde FARKLI kombinasyon

JSON formatında döndür. HER 11 ALAN DOLU OLMALI.
USER;
    }

    /**
     * 11 Kural JSON'ı prompt'a çevir
     * Her kural sırayla eklenir - sıra önemli!
     */
    protected function convertNineRuleJsonToPrompt(string $jsonString): string
    {
        try {
            $data = json_decode($jsonString, true);
            if (!$data) return $jsonString;

            $parts = [];

            // 1. Photo Type + Subject/Action (MİKRO HİKAYE)
            $photoType = $data['photo_type'] ?? 'Photo of';
            $subjectAction = $data['subject_action'] ?? $data['subject'] ?? 'subject';
            $parts[] = $photoType . ' ' . $subjectAction;

            // 2. Environment + Time/Season
            if (!empty($data['environment_time'])) {
                $parts[] = $data['environment_time'];
            } elseif (!empty($data['environment'])) {
                $parts[] = $data['environment'];
            } elseif (!empty($data['background'])) {
                $parts[] = $data['background'];
            }

            // 3. Camera Angle
            if (!empty($data['angle'])) {
                $parts[] = $data['angle'];
            }

            // 4. Composition
            if (!empty($data['composition'])) {
                $parts[] = $data['composition'];
            }

            // 5. Lighting
            if (!empty($data['lighting'])) {
                $parts[] = $data['lighting'];
            }

            // 6. Camera + Lens + DoF
            if (!empty($data['camera_lens'])) {
                $parts[] = "shot on " . $data['camera_lens'];
            } elseif (!empty($data['camera'])) {
                $parts[] = "shot on " . $data['camera'];
            }

            // 7. Film Stock (YENİ - AI görünümünü kırar)
            if (!empty($data['film_stock'])) {
                $parts[] = $data['film_stock'];
            }

            // 8. Imperfections (KUSURLAR - GERÇEKÇİLİK İÇİN KRİTİK!)
            if (!empty($data['imperfections'])) {
                $parts[] = $data['imperfections'];
            }

            // 9. Mood + Emotion
            if (!empty($data['mood_emotion'])) {
                $parts[] = $data['mood_emotion'];
            } elseif (!empty($data['mood'])) {
                $parts[] = $data['mood'];
            } elseif (!empty($data['emotion'])) {
                $parts[] = $data['emotion'];
            }

            // 10. Post-Processing (YENİ - AI steril görünümünü kırar)
            if (!empty($data['post_processing'])) {
                $parts[] = $data['post_processing'];
            }

            $prompt = implode(', ', $parts);

            // Final negatives - MARKA YASAĞI GÜÇLENDİRİLDİ
            $prompt .= ". Unbranded generic equipment, no manufacturer logos, no visible brand names, blank clean surfaces. NO text, NO labels, NO watermarks, authentic photograph only";

            return trim($prompt);

        } catch (\Exception $e) {
            Log::warning('11-Rule JSON conversion failed', ['error' => $e->getMessage()]);
            return $jsonString;
        }
    }

    /**
     * String'in JSON olup olmadığını kontrol et
     */
    protected function isJson(string $string): bool
    {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }

    /**
     * 11 Kural Basic Enhancement (API başarısız olursa)
     * AMAÇ: Gerçek fotoğraf gibi görünen, AI belli olmayan prompt
     */
    protected function sevenRuleBasicEnhancement(string $prompt, string $style, ?array $tenantContext = null): string
    {
        // Sektöre göre config al
        $sector = $tenantContext['sector'] ?? 'general_business';
        $sectorConfig = $this->getSectorConfig($sector);

        // 1. Photo Type (AI'ı fotoğraf moduna sokar)
        $photoTypes = [
            'Photo of',
            'Editorial photograph of',
            'Documentary shot of',
            'Candid moment of'
        ];
        $photoType = $photoTypes[array_rand($photoTypes)];

        // 2. Subject + Action = MİKRO HİKAYE (prompt içinde gelir, action ekliyoruz)
        $actions = [
            'in motion',
            'mid-action',
            'focused on task',
            'during work',
            'in active use'
        ];
        $action = $actions[array_rand($actions)];

        // 3. Environment + Time/Season (sektöre göre)
        $environment = $sectorConfig['backgrounds'][array_rand($sectorConfig['backgrounds'])];
        $timeSeason = $sectorConfig['time_season'][array_rand($sectorConfig['time_season'])];

        // 4. Camera Angle
        $angles = [
            'eye-level shot for intimate connection',
            'low angle emphasizing power and scale',
            'high angle showing context',
            '3/4 angle with depth',
            'over-shoulder POV for immersion',
            'slight dutch angle adding dynamism'
        ];
        $angle = $angles[array_rand($angles)];

        // 5. Composition
        $compositions = [
            'rule of thirds with subject at intersection',
            'centered symmetrical composition',
            'golden ratio framing',
            'leading lines drawing eye to subject',
            'negative space on left creating tension',
            'frame within frame composition'
        ];
        $composition = $compositions[array_rand($compositions)];

        // 6. Lighting (sektöre göre, daha detaylı)
        $lighting = $sectorConfig['lightings'][array_rand($sectorConfig['lightings'])];

        // 7. Camera + Lens + DoF (daha spesifik)
        $cameras = [
            'Canon EOS R5, 85mm f/1.4L, creamy bokeh, subject sharp at 2m',
            'Sony A7IV, 35mm f/1.8, environmental context, moderate DoF',
            'Nikon Z8, 50mm f/1.2, subject isolation, smooth bokeh',
            'Fuji X-T5, 56mm f/1.2, classic Fuji colors, shallow focus',
            'Leica M11, 35mm f/2 Summicron, natural rendering, zone focus',
            'Canon EOS R3, 24-70mm f/2.8 at 50mm, versatile, sharp'
        ];
        $camera = $cameras[array_rand($cameras)];

        // 8. Film Stock (YENİ - AI görünümünü kırar)
        $filmStock = $sectorConfig['film_stocks'][array_rand($sectorConfig['film_stocks'])];

        // 9. Imperfections (KRİTİK - gerçekçilik için genişletildi)
        $imperfection = $sectorConfig['imperfections'][array_rand($sectorConfig['imperfections'])];

        // 10. Mood + Emotion (KRİTİK - atmosfer için)
        $emotion = $sectorConfig['emotions'][array_rand($sectorConfig['emotions'])];

        // 11. Post-Processing (YENİ - AI steril görünümünü kırar)
        $postProcessing = $sectorConfig['post_processing'][array_rand($sectorConfig['post_processing'])];

        // Tenant'a özel enhancement varsa ekle
        $enhancement = '';
        if (!empty($tenantContext['prompt_enhancement'])) {
            $enhancement = ', ' . $tenantContext['prompt_enhancement'];
        }

        // 11 KURAL FORMÜLÜ + MARKA YASAĞI
        return "{$photoType} {$prompt} {$action}, {$environment}, {$timeSeason}, {$angle}, {$composition}, {$lighting}, shot on {$camera}, {$filmStock}, {$imperfection}, {$emotion}, {$postProcessing}{$enhancement}. Unbranded generic equipment, no manufacturer logos, no visible brand names, blank clean surfaces. NO text, NO labels, NO watermarks, authentic photograph only";
    }

    /**
     * Tenant context'e göre sektör guidance oluştur
     */
    protected function buildSectorGuidance(?array $tenantContext): string
    {
        if (empty($tenantContext)) {
            return '';
        }

        $guidance = "## TENANT CONTEXT (ZORUNLU):\n";

        $sector = $tenantContext['sector'] ?? null;
        if ($sector) {
            $sectorDescriptions = [
                'industrial_equipment' => 'Endüstriyel ekipman sektörü - depo, fabrika, lojistik ortamları. Türkiye iş ortamı context\'i.',
                'music_platform' => 'Müzik platformu - profesyonel kayıt stüdyosu, konser sahnesi, müzik aletleri, sanatçı performansı. Türkiye müzik kültürü.',
                'ecommerce' => 'E-ticaret - ürün fotoğrafçılığı, temiz arka plan, ticari kalite.',
                'general_business' => 'Genel iş ortamı - profesyonel, kurumsal atmosfer.',
            ];

            $guidance .= "- SEKTÖR: " . ($sectorDescriptions[$sector] ?? $sector) . "\n";
        }

        if (!empty($tenantContext['site_name'])) {
            $guidance .= "- MARKA: {$tenantContext['site_name']} için içerik üretiliyor\n";
        }

        if (!empty($tenantContext['prompt_enhancement'])) {
            $guidance .= "- ÖZEL CONTEXT: {$tenantContext['prompt_enhancement']}\n";
        }

        $guidance .= "- ÜLKE: Türkiye\n";

        return $guidance;
    }

    /**
     * User prompt için context satırı oluştur
     */
    protected function buildContextLine(?array $tenantContext): string
    {
        if (empty($tenantContext)) {
            return "- Türkiye iş ortamı context'i";
        }

        $sector = $tenantContext['sector'] ?? 'general_business';

        $contextLines = match ($sector) {
            'industrial_equipment' => "- Türkiye endüstriyel/iş ortamı context'i\n- Depo, fabrika, lojistik mekanları",
            'music_platform' => "- Türkiye müzik endüstrisi context'i\n- Stüdyo, sahne, konser, müzik aletleri, sanatçı performansı\n- Dramatik sahne ışıkları, artistik atmosfer",
            'ecommerce' => "- E-ticaret profesyonel fotoğrafçılık\n- Temiz, minimal arka plan",
            default => "- Türkiye profesyonel iş ortamı context'i",
        };

        // Özel enhancement varsa ekle
        if (!empty($tenantContext['prompt_enhancement'])) {
            $contextLines .= "\n- {$tenantContext['prompt_enhancement']}";
        }

        return $contextLines;
    }

    /**
     * Sektöre göre konfigürasyon al
     * 11 KURAL FORMÜLÜ için genişletilmiş config
     */
    protected function getSectorConfig(string $sector): array
    {
        return match ($sector) {
            'music_platform' => [
                'lightings' => [
                    'dramatic stage lighting with colored gels, blue and magenta',
                    'soft studio lighting with professional diffusion, warm 3200K',
                    'concert atmosphere with spotlights cutting through haze',
                    'moody blue and purple stage ambiance, practical lights only',
                    'warm golden acoustic session lighting, intimate',
                    'dramatic backlight with lens flare, silhouette edge'
                ],
                'backgrounds' => [
                    'professional recording studio with acoustic panels on late night session',
                    'concert stage with dramatic lighting, crowd silhouettes in background',
                    'intimate acoustic performance space at golden hour',
                    'modern music production room at 2am, coffee cups scattered',
                    'live performance venue atmosphere, mid-show energy',
                    'backstage area with equipment, pre-show tension'
                ],
                'time_season' => [
                    'late night recording session at 2am',
                    'golden hour outdoor concert, summer evening',
                    'rainy autumn afternoon in studio',
                    'winter morning rehearsal, frost on windows',
                    'spring festival atmosphere, cherry blossoms visible',
                    'sunset soundcheck, orange light flooding stage'
                ],
                'emotions' => [
                    'conveying musical passion and creative flow',
                    'atmosphere of artistic expression and vulnerability',
                    'sense of performance energy and adrenaline',
                    'feeling of musical intimacy and connection',
                    'mood of creative inspiration and discovery'
                ],
                'imperfections' => [
                    'subtle wear on guitar frets, fingerprints on neck, authentic unbranded instrument patina',
                    'microphone mesh with slight discoloration from use, breath condensation, no brand markings',
                    'keyboard keys showing gentle wear patterns, dust between keys, blank instrument surfaces',
                    'cable management showing real studio environment, slight lens dust, generic equipment',
                    'natural film grain, slight vignetting at corners, micro chromatic aberration'
                ],
                'film_stocks' => [
                    'Kodak Portra 800 pushed, warm skin tones, visible grain',
                    'Fuji Pro 400H discontinued film look, soft greens',
                    'Kodak Tri-X 400 B&W, high contrast, gritty grain',
                    'Cinestill 800T tungsten balanced, halation on highlights',
                    'Ilford HP5 pushed to 1600, documentary feel'
                ],
                'post_processing' => [
                    'cinematic teal and orange color grading, lifted blacks',
                    'desaturated film look with crushed shadows',
                    'warm vintage tones, slight color shift in shadows',
                    'high contrast B&W conversion with rich blacks',
                    'natural minimal editing, true-to-life colors'
                ]
            ],
            'industrial_equipment' => [
                'lightings' => [
                    'golden hour side lighting with warm tones, long shadows',
                    'soft diffused window light from left, 5600K daylight',
                    'dramatic Rembrandt lighting with 3:1 ratio',
                    'natural overcast daylight, soft and even',
                    'industrial fluorescent ambient mixed with window light',
                    'harsh midday sun with strong shadows, high contrast'
                ],
                'backgrounds' => [
                    'industrial warehouse with metal shelving, early morning shift',
                    'modern factory floor with equipment, active work environment',
                    'professional workspace with organized tools',
                    'logistics facility with stacked pallets, forklift activity',
                    'clean workshop setting with natural light from skylights'
                ],
                'time_season' => [
                    'early winter morning, frost on warehouse windows, breath visible',
                    'summer afternoon, hot warehouse, workers in short sleeves',
                    'autumn overcast day, soft even light through skylights',
                    'spring morning, fresh air, warehouse doors open',
                    'late evening shift, artificial lights on, dusk through windows',
                    'rainy day, wet loading dock, reflections on concrete'
                ],
                'emotions' => [
                    'conveying industrial efficiency and precision',
                    'atmosphere of focused concentration and expertise',
                    'sense of professional competence and reliability',
                    'feeling of quiet productivity and routine',
                    'mood of authentic workplace energy and teamwork'
                ],
                'imperfections' => [
                    'subtle scratches on metal surfaces, dust particles in air, unbranded generic equipment',
                    'weathered texture, signs of daily use, oil stains on floor, no manufacturer logos',
                    'natural wear patterns, authentic aging, scuff marks, blank equipment panels',
                    'minor dents, realistic surface imperfections, safety tape wear, no brand markings',
                    'lens dust visible in light beams, slight vignetting, micro camera shake'
                ],
                'film_stocks' => [
                    'Kodak Portra 400, warm neutral tones, fine grain',
                    'Kodak Ektar 100, saturated colors, high detail, punchy',
                    'Fuji Pro 400H, soft contrast, natural greens',
                    'Kodak Gold 200, consumer film look, nostalgic',
                    'Ilford FP4 B&W, fine grain, classic documentary'
                ],
                'post_processing' => [
                    'industrial documentary grade, slightly desaturated',
                    'warm vintage tones, lifted shadows, reduced highlights',
                    'natural processing, minimal intervention, true colors',
                    'cinematic orange teal subtle, professional commercial look',
                    'high contrast with crushed blacks, dramatic industrial'
                ]
            ],
            default => [
                'lightings' => [
                    'golden hour lighting with warm tones, soft shadows',
                    'soft diffused natural light from large window',
                    'professional studio lighting with softbox key',
                    'natural overcast daylight, even and flattering',
                    'soft morning light, gentle and warm'
                ],
                'backgrounds' => [
                    'modern office environment during business hours',
                    'professional workspace with natural elements',
                    'clean minimal setting with negative space',
                    'contemporary business space with large windows'
                ],
                'time_season' => [
                    'weekday morning, fresh start energy',
                    'afternoon light, productive atmosphere',
                    'spring day, natural light flooding in',
                    'autumn afternoon, warm golden tones'
                ],
                'emotions' => [
                    'conveying professionalism and competence',
                    'atmosphere of trust and reliability',
                    'sense of quality and attention to detail',
                    'feeling of confidence and expertise'
                ],
                'imperfections' => [
                    'subtle natural textures, slight dust in air',
                    'authentic surface details, minor wear',
                    'realistic material properties, not perfect',
                    'slight lens imperfections, natural vignetting'
                ],
                'film_stocks' => [
                    'Kodak Portra 400, warm and natural',
                    'Fuji Pro 400H, soft and balanced',
                    'Kodak Ektar 100, vivid but natural'
                ],
                'post_processing' => [
                    'clean professional grade, balanced colors',
                    'subtle warm tones, commercial quality',
                    'natural editing, minimal processing'
                ]
            ]
        };
    }
}

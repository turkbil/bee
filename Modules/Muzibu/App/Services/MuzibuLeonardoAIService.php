<?php

namespace Modules\Muzibu\App\Services;

use App\Services\Media\LeonardoAIService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Muzibu (Tenant 1001) için özel Leonardo AI servisi
 *
 * Müzik platformu için optimize edilmiş AI görsel üretimi:
 * - Müzik odaklı prompt'lar (enstrüman, sahne, stüdyo, abstract)
 * - İnsan figürü filtresi (müzisyen yerine enstrüman siluetleri)
 * - GPT-4 ile tam yaratıcılık (stil seçimi dahil)
 * - Her seferinde farklı prompt (frequency_penalty, presence_penalty)
 */
class MuzibuLeonardoAIService extends LeonardoAIService
{
    /**
     * GPT-4 ile 11 altın kurala göre zenginleştir - MÜZİK PLATFORMU İÇİN!
     *
     * TAM YARATICILIK:
     * - Mekan, zaman, ışık, kamera, atmosfer, STİL HEPSİNİ AI seçer
     * - Hiçbir hazır havuz yok, tamamen serbest yaratıcılık
     * - Her seferinde farklı, yaratıcı, sıradışı düşünsün
     * - Müzik temalı: enstrüman, sahne, stüdyo, ses dalgaları, abstract
     *
     * @return array ['prompt' => string, 'style' => string]
     */
    protected function enhanceWithGPT4Music(string $title): array
    {
        // 🔴 GEÇİCİ DEVRE DIŞI (2026-01-14) - OpenAI kredi tasarrufu
        // Düzeltmek için bu bloğu yorum satırından çıkar ve alttaki return'ü sil
        // ✅ GPT-4 AKTİF - Gelişmiş prompt enhancement çalışıyor
        // $openaiKey = config('ai.openai_api_key');

        // 🔴 GEÇİCİ: Sadece başlık + vibrant stil döndür (OpenAI kullanma)
        Log::info('🔴 Muzibu Leonardo AI: OpenAI DEVRE DIŞI - sadece başlık kullanılıyor', [
            'title' => $title,
        ]);
        return [
            'prompt' => $title,
            'style' => 'vibrant',
            'detected_language' => 'unknown',
            'translated_title' => $title,
        ];
        // 🔴 GEÇİCİ DEVRE DIŞI SONU - Aşağıdaki kod orijinal

        $openaiKey = config('ai.openai_api_key');

        if (empty($openaiKey)) {
            Log::warning('Muzibu Leonardo AI: OpenAI key not configured, using title directly');
            return [
                'prompt' => $title,
                'style' => 'vibrant', // Müzik platformu için canlı renkler fallback
            ];
        }

        try {
            $systemPrompt = <<<SYSTEM
Sen profesyonel bir müzik albümü kapak tasarımcısısın. Verilen şarkı/albüm/playlist başlığını 11 altın kurala göre zenginleştir.

🌍 ÖNEMLİ - DİL TESPİTİ VE ÇEVİRİ:
- Başlık hangi dilde olursa olsun (Türkçe, İngilizce, İtalyanca, İspanyolca, Fransızca, Almanca, Japonca, Korece vb.)
- ÖNCE başlığın dilini tespit et
- Başlık İngilizce değilse → İngilizce'ye çevir
- Anlamını koru, kelimesi kelimesine değil, duyguyu ve konsepti çevir
- Prompt'u İngilizce oluştur (Leonardo AI sadece İngilizce anlar)

Örnekler:
- "Va Come Deve" (İtalyanca) → "As It Should Be" → prompt bu konsepte göre oluştur
- "Aşk" (Türkçe) → "Love" → romantic sunset, not musicians
- "夢 (Yume)" (Japonca) → "Dream" → dreamy atmosphere, ethereal

Başlık ne diyorsa ONU hayal et. Tamamen serbest ol. Kendi hayal gücünü kullan.

🎨 YARATICI DÜŞÜN - DİREKT DÜZ DÜŞÜNME - HER SEFERINDE FARKLI!
- Sıradışı açılar, beklenmedik kompozisyonlar dene
- Farklı atmosferler, benzersiz ışık oyunları keşfet
- Her seferinde FARKLI ekipman, FARKLI film stoku, FARKLI işleme tarzı seç
- Klişelerden kaçın, yaratıcı ol!
- AYNI KELİMELERİ TEKRAR ETME! Her prompt benzersiz olsun!
- Aynı kamera, aynı lens, aynı film stoku, aynı açı kullanma - ÇEŞİTLİLİK!

🎵 BAŞLIĞI YORUMLA - HER FOTOĞRAFTA MÜZİK ALETİ ZORUNLU DEĞİL!
- BAŞLIK NE DİYORSA ONU HAYAL ET! (örn: "Paradise" → tropical beach, "Jazz Night" → saxophone)
- Başlık müzik türü belirtiyorsa → O türe uygun atmosfer oluştur
- Başlık duygu/mekan belirtiyorsa → O duyguyu/mekanı göster (enstrüman EKLEMEK ZORUNDA DEĞİLSİN!)
- Müziğin duygusunu görsele yansıt (Aşk → romantik gün batımı, Rock → grunge doku, Jazz → vintage atmosphere)
- Albüm kapağı estetiği düşün ama BAŞLIĞA UYGUN OL!

🚫 İNSAN FİGÜRÜ KURALI (ÇOK ÖNEMLİ!):
- VARSAYILAN: İnsansız görsel oluştur!
- İnsansız olabiliyorsa KESİNLİKLE insan ekleme
- Manzara, atmosfer, soyut şekiller, ışık oyunları kullan (başlığa uygun olanı seç!)
- SADECE başlık kesinlikle insan gerektiriyorsa insan ekle (örn: "Dans Eden Çift", "Portre", "Kalabalık")
- "Aşk", "Özlem", "Huzur" gibi duygular → insan YERİNE sembolik görseller kullan (güneş batımı, yağmur, ışık huzmeleri)
- "Paradise", "Ocean", "Dream" gibi mekansal başlıklar → MÜZİK ALETİ EKLEME! Başlığın manzarasını göster!
- Rock/Jazz gibi müzik türü başlıklarında → O ZAMAN enstrüman ekleyebilirsin (gitar, saksafon, davul)

ÖZEL DURUMLAR:
- Başlık anlamsızsa (örn: "a", "asdf", "xxx") → abstract sanat üret (renkli ışıklar, geometrik şekiller, ışık oyunları)
- Başlık müstehcen/uygunsuzsa → abstract sanat veya doğa manzarası üret (güneş batımı, orman, okyanus)

11 ALTIN KURAL:
1. Photo Type (Stock photograph of, Commercial photo of, Editorial image of, Album cover art of)
2. Subject (BAŞLIĞA UYGUN! Manzara, abstract shapes, atmosferik öğeler, ışık oyunları - İNSAN YOK! Enstrüman SADECE başlık gerektiriyorsa!)
3. Environment (BAŞLIĞA GÖRE BELIRLE! Başlık ne diyorsa o mekan: beach, forest, city, abstract space, concert hall...)
4. Camera Angle (Sıradışı açılar! Low angle, bird's eye, dutch angle, macro, wide angle)
5. Composition (Beklenmedik kompozisyonlar! Rule of thirds, golden ratio, negative space, symmetry breaking)
6. Lighting (Dramatik ışık! Neon lights, stage lights, volumetric lighting, rim lighting, colored gels, moody shadows)
7. Camera + Lens (Canon, Sony, Nikon, Fuji, Leica - Farklı lensler: 24mm, 35mm, 50mm, 85mm, macro, fisheye)
8. Film Stock (Kodak Portra, Fuji Velvia, Ilford HP5, Cinestill 800T - Farklı film stoklarını dene!)
9. Imperfections (Lens dust, grain, vignette, light leaks, bokeh, chromatic aberration, film scratches)
10. Mood (Farklı duygular! Melancholic, energetic, dreamy, nostalgic, euphoric, mysterious, rebellious)
11. Post-Processing (Farklı renk tonları! Vintage fade, neon glow, matte finish, crushed blacks, lifted shadows, color grading)

🎨 STİL SEÇİMİ (TAMAMEN ÖZGÜR - SEN KARAR VER!):
Bu başlık için hangi Leonardo AI stili en uygun? 9 stil var, HEPSİ KULLANILABİLİR:

- stock_photo: Profesyonel stok fotoğraf (ticari, temiz, evrensel)
- vibrant: Canlı renkler (parlak, enerjik, dinamik)
- cinematic: Sinematik (film estetiği, dramatik)
- cinematic_closeup: Sinematik yakın çekim (detaylı, dramatic close-up)
- dynamic: Dinamik (hareketli, enerjik)
- film: Analog film (vintage, nostaljik, grain)
- hdr: Yüksek dinamik aralık (detaylı gölgeler ve ışıklar, yüksek kontrast)
- moody: Atmosferik (duygusal, dramatic, karanlık/aydınlık kontrast)
- neutral: Nötr/minimal (sade, az işlenmiş, doğal)

HİÇBİR KISITLAMA YOK!
- Rock şarkısına istersen neutral seç
- Classical'a istersen vibrant seç
- Jazz'a istersen hdr seç
- TAMAMEN SENİN KARARINDIR!

Başlık ne diyor, hangi duyguyu veriyor, hangi atmosferi gerektiriyor - ONA GÖRE KARAR VER!

OUTPUT FORMAT (JSON):
{
  "detected_language": "tespit ettiğin dil (Turkish, English, Italian, Spanish, French, German, Japanese, Korean vb.)",
  "translated_title": "İngilizce çeviri (başlık zaten İngilizceyse aynısını yaz)",
  "prompt": "Tek paragraf İngilizce prompt (maks 150 kelime, BAŞLIĞA UYGUN, insan yok!)",
  "style": "seçtiğin stil (yukarıdaki listeden)"
}
SYSTEM;

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $openaiKey,
                'Content-Type' => 'application/json',
            ])->timeout(30)->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o',
                'max_tokens' => 700,
                'temperature' => 1.3, // MAKSIMUM yaratıcılık! (müzik için daha serbest)
                'top_p' => 0.95, // Nucleus sampling - en olası %95'lik dilimden seç
                'frequency_penalty' => 1.8, // ÇOK AGRESIF - Tekrar eden kelimeleri cezalandır
                'presence_penalty' => 1.5, // Yeni konular getirmeyi teşvik et
                'response_format' => ['type' => 'json_object'], // JSON response zorla
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => "Şarkı/Albüm/Playlist Başlığı: \"{$title}\"\n\nBu müzik platformu için albüm/şarkı kapağı üret. 11 altın kurala göre zenginleştir ve stil seç. BAŞLIĞI YORUMLA - başlık ne diyorsa ONU hayal et! Her seferinde FARKLI kelimeler, FARKLI açılar, FARKLI ekipman! JSON formatında cevap ver."]
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $content = $data['choices'][0]['message']['content'] ?? '';

                if (!empty($content)) {
                    $parsed = json_decode($content, true);

                    if ($parsed && isset($parsed['prompt']) && isset($parsed['style'])) {
                        // Stil geçerliliğini kontrol et
                        $validStyles = ['stock_photo', 'commercial_photo', 'photographic', 'cinematic', 'creative', 'vibrant', 'hdr', 'neutral'];
                        $selectedStyle = in_array($parsed['style'], $validStyles) ? $parsed['style'] : 'vibrant'; // Müzik için vibrant fallback

                        Log::info('🎵 Muzibu Leonardo AI: GPT-4 enhanced with style and translation', [
                            'original' => $title,
                            'detected_language' => $parsed['detected_language'] ?? 'unknown',
                            'translated_title' => $parsed['translated_title'] ?? $title,
                            'enhanced_length' => strlen($parsed['prompt']),
                            'selected_style' => $selectedStyle,
                        ]);

                        return [
                            'prompt' => trim($parsed['prompt']),
                            'style' => $selectedStyle,
                            'detected_language' => $parsed['detected_language'] ?? 'English',
                            'translated_title' => $parsed['translated_title'] ?? $title,
                        ];
                    }
                }
            }

            Log::warning('Muzibu Leonardo AI: GPT-4 enhancement failed, using title', [
                'status' => $response->status(),
            ]);

        } catch (\Exception $e) {
            Log::warning('Muzibu Leonardo AI: GPT-4 exception', ['error' => $e->getMessage()]);
        }

        // Fallback: Sadece başlık + vibrant (müzik platformu için canlı renkler)
        return [
            'prompt' => $title,
            'style' => 'vibrant',
        ];
    }

    /**
     * Override: Serbest hayal gücü modu ile generation (Müzik platformu optimized)
     */
    public function generateFreeImagination(string $title, array $options = []): ?array
    {
        if (empty($this->apiKey)) {
            Log::error('Muzibu Leonardo AI: API key not configured');
            return null;
        }

        // 🧪 TEST MODE 2: 512x512 KARE (%75 kredi tasarrufu!)
        $width = $options['width'] ?? 512;
        $height = $options['height'] ?? 512;

        Log::info('🎵 Muzibu Leonardo AI: Free imagination mode (Music Platform)', [
            'title' => $title,
            'dimensions' => "{$width}x{$height}",
        ]);

        try {
            // GPT-4 ile 11 altın kurala göre zenginleştir (MÜZİK ODAKLI!)
            $enhanced = $this->enhanceWithGPT4Music($title);

            Log::info('🎵 Muzibu Leonardo AI: Prompt enhanced by GPT-4 with music-focused style', [
                'original' => $title,
                'enhanced_length' => strlen($enhanced['prompt']),
                'selected_style' => $enhanced['style'],
            ]);

            // Generation oluştur (GPT-4'ün seçtiği stil ile)
            $generationId = $this->createGenerationWithStyle($enhanced['prompt'], $enhanced['style'], $width, $height);

            if (!$generationId) {
                return null;
            }

            // Sonucu bekle
            $imageUrl = $this->waitForGeneration($generationId);

            if (!$imageUrl) {
                return null;
            }

            // Görseli indir
            $imageData = $this->downloadImage($imageUrl);

            if (!$imageData) {
                return null;
            }

            Log::info('🎵 Muzibu Leonardo AI: Music cover generation successful', [
                'generation_id' => $generationId,
                'image_size' => strlen($imageData),
                'style_used' => $enhanced['style'],
            ]);

            return [
                'url' => $imageUrl,
                'content' => $imageData,
                'generation_id' => $generationId,
                'provider' => 'leonardo',
                'prompt' => $enhanced['prompt'],
                'original_title' => $title,
                'detected_language' => $enhanced['detected_language'] ?? 'English',
                'translated_title' => $enhanced['translated_title'] ?? $title,
                'style' => $enhanced['style'],
                'mode' => 'muzibu_music_platform',
            ];

        } catch (\Exception $e) {
            Log::error('🎵 Muzibu Leonardo AI: Music cover generation failed', [
                'title' => $title,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * GPT-4'ün seçtiği stil ile generation oluştur
     */
    protected function createGenerationWithStyle(string $prompt, string $style, int $width, int $height): ?string
    {
        // Minimal negative prompt - sadece NSFW yasağı
        $negativePrompt = "nsfw, nude, naked, porn, explicit sexual content";

        $styleUUID = $this->styleUUIDs[$style] ?? $this->styleUUIDs['vibrant'];

        $response = Http::withHeaders([
            'accept' => 'application/json',
            'content-type' => 'application/json',
            'authorization' => 'Bearer ' . $this->apiKey,
        ])->timeout(30)->post($this->baseUrl . '/generations', [
            'modelId' => $this->defaultModel,
            'prompt' => $prompt,
            'negative_prompt' => $negativePrompt,
            'styleUUID' => $styleUUID,
            'contrast' => 3.5,
            'num_images' => 1,
            'width' => $width,
            'height' => $height,
            'alchemy' => false,
            'ultra' => false,
        ]);

        if (!$response->successful()) {
            Log::error('Muzibu Leonardo AI: Music cover generation API failed', [
                'status' => $response->status(),
                'body' => $response->body(),
                'style' => $style,
            ]);
            return null;
        }

        $data = $response->json();
        return $data['sdGenerationJob']['generationId'] ?? null;
    }
}

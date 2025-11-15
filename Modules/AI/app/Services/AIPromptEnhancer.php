<?php

namespace Modules\AI\App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * AI Prompt Enhancer Service
 *
 * Basit promptları ultra detaylı, profesyonel JSON scene description'lara çevirir
 * Claude API kullanarak gerçekçi fotoğraf kalitesinde promptlar üretir
 */
class AIPromptEnhancer
{
    protected string $apiKey;
    protected string $apiUrl = 'https://api.anthropic.com/v1/messages';
    protected string $model = 'claude-3-5-sonnet-20241022';

    public function __construct()
    {
        $this->apiKey = config('services.anthropic.api_key') ?? config('ai.anthropic_key');
    }

    /**
     * Basit promptu ultra detaylı scene description'a çevir
     *
     * @param string $simplePrompt Kullanıcının basit promptu
     * @param string $style Fotoğraf stili (ultra_photorealistic, studio_photography, etc.)
     * @return string Ultra detaylı, optimize edilmiş prompt
     */
    public function enhancePrompt(string $simplePrompt, string $style = 'ultra_photorealistic'): string
    {
        if (empty($this->apiKey)) {
            Log::warning('AIPromptEnhancer: Anthropic API key not configured, using basic enhancement');
            return $this->basicEnhancement($simplePrompt, $style);
        }

        try {
            $systemPrompt = $this->buildSystemPrompt($style);
            $userPrompt = $this->buildUserPrompt($simplePrompt, $style);

            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
                'anthropic-version' => '2023-06-01',
                'content-type' => 'application/json',
            ])->timeout(30)->post($this->apiUrl, [
                'model' => $this->model,
                'max_tokens' => 800,
                'temperature' => 0.9, // Yüksek creativity için - her call farklı output
                'system' => $systemPrompt,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $userPrompt
                    ]
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $enhancedPrompt = $data['content'][0]['text'] ?? '';

                if (!empty($enhancedPrompt)) {
                    // JSON parse et ve DALL-E için optimize prompt'a çevir
                    if ($this->isJson($enhancedPrompt)) {
                        return $this->convertJsonToPrompt($enhancedPrompt);
                    }

                    // JSON değilse direkt kullan
                    return $enhancedPrompt;
                }
            }

            Log::warning('AIPromptEnhancer: API call failed', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

        } catch (\Exception $e) {
            Log::error('AIPromptEnhancer: Exception', [
                'message' => $e->getMessage()
            ]);
        }

        // Fallback: Basic enhancement
        return $this->basicEnhancement($simplePrompt, $style);
    }

    /**
     * System prompt oluştur - AI'ya photography director rolü ver
     */
    protected function buildSystemPrompt(string $style): string
    {
        $styleDescriptions = [
            'ultra_photorealistic' => 'ultra-realistic, indistinguishable from real photographs, shot on professional DSLR camera',
            'studio_photography' => 'professional studio photography with controlled lighting and clean backgrounds',
            'natural_light' => 'natural outdoor photography with golden hour lighting and authentic atmosphere',
            'cinematic_photography' => 'cinematic film photography with dramatic lighting and movie-quality aesthetics',
            'documentary_style' => 'documentary photojournalism style with authentic real-world moments',
            'commercial_photography' => 'high-end commercial advertising photography with premium quality',
            'portrait_photography' => 'professional portrait photography with perfect subject focus',
            'macro_photography' => 'macro photography with extreme close-up details and sharp focus',
        ];

        $styleDesc = $styleDescriptions[$style] ?? $styleDescriptions['ultra_photorealistic'];

        return <<<SYSTEM
You are a professional photography director. Transform prompts into ultra-realistic JSON scene descriptions for DALL-E 3.

TARGET STYLE: {$styleDesc}

Create a UNIQUE JSON for each prompt with this structure:
{
  "camera": {
    "model": "Canon EOS R5",
    "lens": "35mm f/1.8",
    "settings": "captured with professional DSLR, natural look, f/1.8, 1/200s, ISO 400"
  },
  "scene": "Main subject with realistic imperfections, visible textures, natural positioning",
  "environment": "Real-world setting, lived-in space, authentic details",
  "lighting": "Natural daylight / soft window light / ambient lighting - NO studio lights",
  "materials": ["visible surface details", "fabric texture", "worn surfaces", "realistic materials"],
  "mood": "Candid documentary style, unposed, authentic moment, natural appearance"
}

CRITICAL PHOTOREALISTIC RULES:
- NEVER use: "3D render", "digital art", "illustration", "concept art", "painting", "unreal engine", "CGI", "stylized", "RAW", "photo"
- ALWAYS use: "captured with DSLR", "natural lighting", "subtle imperfections", "realistic textures", "natural appearance"
- Add negatives: "not stylized, not 3D render, not digital art, appears as seen in real life"
- Camera: VARY camera model each time (Canon EOS R5, Sony A1, Nikon Z9, Fujifilm GFX, Leica M11)
- Lenses: VARY lens each time (24mm, 35mm, 50mm, 85mm, 100mm - different focal lengths)
- Lighting: VARY lighting (morning light, afternoon sun, overcast, golden hour, window light, ambient)
- Angles: VARY perspective (eye-level, slight high angle, low angle, 3/4 view, side view)
- Imperfections: VARY details (dust, scratches, wear patterns, natural aging, weathering)
- MAXIMUM CREATIVITY: Every JSON must be COMPLETELY DIFFERENT from previous ones
- NEVER repeat same camera + lens + lighting combination
- Adapt to subject AND add unexpected realistic details
- NO text in images, NO brands, NO trademarks, NO UI elements, NO camera settings overlay
- If text needed: Turkish only, generic names

OUTPUT: Valid JSON only
SYSTEM;
    }

    /**
     * User prompt oluştur
     */
    protected function buildUserPrompt(string $simplePrompt, string $style): string
    {
        return <<<USER
Create a COMPLETELY UNIQUE ultra-realistic JSON scene description for:

"{$simplePrompt}"

Style: {$style}

IMPORTANT: This must be DIFFERENT from any previous generation. Vary:
- Camera model and lens combination
- Lighting angle and time of day
- Perspective and shooting angle
- Specific realistic imperfections and details
- Environmental context and background elements

Output valid JSON with camera, scene, environment, lighting, materials, mood.
Make it photorealistic but CREATIVELY DIFFERENT each time.
USER;
    }

    /**
     * JSON string'i prompt'a çevir
     */
    protected function convertJsonToPrompt(string $jsonString): string
    {
        try {
            $data = json_decode($jsonString, true);

            if (!$data) {
                return $jsonString;
            }

            $prompt = '';

            // Camera details
            if (isset($data['camera'])) {
                $cam = $data['camera'];
                $prompt .= "Professional photography";
                if (isset($cam['model'])) $prompt .= " shot on {$cam['model']}";
                if (isset($cam['lens'])) $prompt .= " with {$cam['lens']} lens";
                if (isset($cam['settings'])) $prompt .= ", {$cam['settings']}";
                $prompt .= ". ";
            }

            // Scene
            if (isset($data['scene'])) {
                $prompt .= $data['scene'] . ". ";
            }

            // Environment
            if (isset($data['environment'])) {
                $prompt .= $data['environment'] . ". ";
            }

            // Lighting
            if (isset($data['lighting'])) {
                $prompt .= "Lighting: {$data['lighting']}. ";
            }

            // Materials
            if (isset($data['materials']) && is_array($data['materials'])) {
                $prompt .= "Materials: " . implode(', ', $data['materials']) . ". ";
            }

            // Mood
            if (isset($data['mood'])) {
                $prompt .= $data['mood'] . ". ";
            }

            // Technical photorealistic specs + negatives
            $prompt .= "Ultra photorealistic, indistinguishable from real-world scene, natural appearance, as seen in real life. ";
            $prompt .= "NOT stylized, NOT 3D render, NOT digital art, NOT illustration, NOT painting, appears completely real and authentic, no artificial elements, no UI overlays, no text";

            return trim($prompt);

        } catch (\Exception $e) {
            Log::warning('JSON to prompt conversion failed', ['error' => $e->getMessage()]);
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
     * Fallback: API başarısız olursa basic enhancement
     */
    protected function basicEnhancement(string $prompt, string $style): string
    {
        $enhancements = [
            'ultra_photorealistic' => 'Captured with Canon EOS R5, 35mm f/1.8 lens, natural daylight, visible textures and subtle imperfections, realistic materials, authentic scene, appears as seen in real life, not stylized, not 3D render, not digital art, completely realistic',
            'studio_photography' => 'Captured with Phase One IQ4, natural window light, clean background, realistic commercial quality, authentic product appearance, no artificial effects',
            'natural_light' => 'Captured with Fujifilm GFX 100S, golden hour natural light, shallow depth of field, authentic atmosphere, visible environmental details, unposed candid moment, completely natural',
            'cinematic_photography' => 'Captured with film camera, 35mm film grain aesthetic, natural lighting, authentic moment, documentary style, appears real and authentic',
        ];

        $enhancement = $enhancements[$style] ?? $enhancements['ultra_photorealistic'];

        return "{$prompt}. {$enhancement}";
    }
}

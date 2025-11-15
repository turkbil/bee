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
            'ultra_photorealistic' => 'RAW photo shot on professional DSLR camera, natural unprocessed look, documentary photography style',
            'studio_photography' => 'studio photo with professional lighting setup, controlled environment, commercial photography',
            'natural_light' => 'outdoor photo with natural daylight, golden hour or soft morning light, environmental photography',
            'cinematic_photography' => 'film camera aesthetic with cinematic composition, dramatic lighting, movie still quality',
            'documentary_style' => 'documentary photo, photojournalism style, candid authentic moment captured in real-time',
            'commercial_photography' => 'high-end commercial photo, advertising quality, professional product or editorial photography',
            'portrait_photography' => 'portrait photo with environmental context, professional headshot or editorial portrait quality',
            'macro_photography' => 'macro photo with extreme close-up, ultra-sharp detail, professional macro lens photography',
        ];

        $styleDesc = $styleDescriptions[$style] ?? $styleDescriptions['ultra_photorealistic'];

        return <<<SYSTEM
You are a professional photography director. Transform prompts into detailed JSON scene descriptions for DALL-E 3 that produce authentic RAW photographs.

TARGET STYLE: {$styleDesc}

Create a UNIQUE JSON for each prompt with this structure:
{
  "subject": "Main subject with specific details",
  "view_framing": "Close-up / full body / environmental portrait / wide shot",
  "background": "Specific background description with depth and context",
  "lighting": "Exact lighting setup with direction and quality",
  "camera": {
    "model": "Canon EOS R5",
    "lens": "85mm f/1.8",
    "settings": "f/11, 1/60th shutter, ISO 800, auto white balance"
  },
  "imperfections": ["natural skin texture with visible pores", "fine vellus hair", "uneven skin tone"],
  "materials": ["visible surface details", "fabric weave texture", "subtle specular highlights"],
  "mood": "Candid moment, authentic expression, documentary style"
}

CRITICAL PHOTOGRAPHY RULES - RAW PHOTO ONLY:
- NEVER use these words: "photorealistic", "realistic", "photo-realistic", "3D render", "digital art", "illustration", "concept art", "painting", "unreal engine", "CGI", "stylized", "drawing", "sketch", "diagram", "blueprint", "technical drawing", "schematic", "vector art", "graphic design", "cartoon", "anime"
- ALWAYS use: "RAW photo", "photo of", "photograph of", "shot on [camera]", "captured with DSLR", "natural lighting", "documentary photography"
- MANDATORY negatives in final prompt: "NOT photorealistic painting, NOT illustration, NOT 3D render, NOT digital art, NOT drawing, NOT sketch, NOT blueprint, NOT diagram, NOT exaggerated lighting, NOT glossy plastic skin, NOT over-saturated, NOT HDR, appears as authentic RAW photograph taken with professional camera"

LIGHTING VARIATIONS (choose ONE and be VERY specific):
- "golden hour lighting" (warm sunset/sunrise glow, long shadows)
- "blue hour" (cool twilight tones, soft ambient light)
- "soft natural window lighting from left" (diffused, gentle shadows)
- "harsh midday sun" (strong shadows, high contrast)
- "overcast soft daylight" (even illumination, minimal shadows)
- "Rembrandt lighting" (key light at 45° creating triangle on shadow cheek)
- "butterfly lighting" (centered key above creating butterfly shadow under nose)
- "loop lighting" (slightly to side and above, small nose shadow)
- "dramatic backlighting" (subject backlit, rim light effect)
- "soft diffused morning light" (gentle, warm, low-angle sun)

CAMERA VARIATIONS (choose ONE and specify ALL settings):
- Canon EOS R5 + 85mm f/1.8 lens, f/2.8, 1/125s, ISO 400
- Sony A7 III + 50mm f/1.4 lens, f/4, 1/200s, ISO 800
- Nikon D810 + 24mm wide-angle lens, f/11, 1/60s, ISO 400
- Fujifilm GFX 100S + 35mm f/2 lens, f/5.6, 1/250s, ISO 640
- Leica M10 + 35mm f/2 lens, f/2.8, 1/500s, ISO 800

IMPERFECTIONS (ALWAYS include for realism):
- For portraits: "natural skin texture with visible pores", "fine vellus hair", "uneven skin tone", "gentle specular highlights", "subtle wrinkles" (for older subjects)
- For objects: "subtle wear patterns", "natural aging", "weathered surface", "realistic material imperfections"
- For scenes: "natural variation", "authentic details", "lived-in space", "realistic imperfections"

MAXIMUM CREATIVITY RULES:
- Every JSON must be COMPLETELY DIFFERENT from previous ones
- NEVER repeat same camera + lens + lighting combination
- VARY focal length: wide (24mm), normal (35mm, 50mm), portrait (85mm, 100mm)
- VARY aperture: shallow DOF (f/1.8, f/2.8) vs deep focus (f/8, f/11)
- VARY lighting time and direction for each generation
- Adapt to subject AND add unexpected authentic details

NO TEXT/BRANDING:
- NO text in images, NO brands, NO trademarks, NO UI elements, NO camera settings overlay
- If text absolutely needed: Turkish only, generic names

OUTPUT: Valid JSON only
SYSTEM;
    }

    /**
     * User prompt oluştur
     */
    protected function buildUserPrompt(string $simplePrompt, string $style): string
    {
        return <<<USER
Create a COMPLETELY UNIQUE detailed JSON scene description for an authentic RAW photograph of:

"{$simplePrompt}"

Style: {$style}

CRITICAL REQUIREMENTS - RAW PHOTO ONLY:
1. This must be a RAW PHOTO, NOT photorealistic painting/illustration/drawing/3D render/blueprint/diagram
2. Photo of [subject], shot on professional DSLR camera
3. Actual physical scene captured with camera, not digital art or CGI
4. Natural imperfections (visible pores, fine vellus hair, uneven skin tone, weathered surfaces)
5. Real-world lighting with specific direction and quality
6. Documentary photography style - authentic candid moment

STRUCTURED PROMPT FORMULA:
Subject + View/Framing + Background + Lighting + Camera Brand + Lens Setup

Example structure:
"RAW photo of [subject], [close-up/full body/environmental portrait], [specific background], [golden hour lighting/Rembrandt lighting/etc], shot on [Canon EOS R5], [85mm f/1.8 lens], [f/2.8, 1/125s, ISO 400]"

MAXIMUM VARIATION - This must be DIFFERENT from any previous generation:
- Camera model: Canon EOS R5, Sony A7 III, Nikon D810, Fujifilm GFX 100S, or Leica M10
- Lens focal length: 24mm (wide), 35mm (normal), 50mm (normal), 85mm (portrait), or 100mm (telephoto)
- Aperture: f/1.8 (shallow DOF) to f/11 (deep focus)
- Lighting: golden hour, blue hour, window light from left, harsh midday sun, Rembrandt, butterfly, loop, or dramatic backlighting
- Perspective: eye-level, slight high angle, low angle, 3/4 view, side view
- Specific imperfections: visible pores, fine vellus hair, weathered surface, natural aging, wear patterns

Output valid JSON with: subject, view_framing, background, lighting, camera (model, lens, settings), imperfections, materials, mood.

Make it authentic RAW PHOTOGRAPH but CREATIVELY DIFFERENT each time.
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

            // Start with "RAW photo of" structure
            if (isset($data['subject'])) {
                $prompt .= "RAW photo of {$data['subject']}";
            }

            // View/Framing
            if (isset($data['view_framing'])) {
                $prompt .= ", {$data['view_framing']}";
            }

            // Background
            if (isset($data['background'])) {
                $prompt .= ", {$data['background']}";
            }

            // Lighting (very specific)
            if (isset($data['lighting'])) {
                $prompt .= ", {$data['lighting']}";
            }

            // Camera details (full specifications)
            if (isset($data['camera'])) {
                $cam = $data['camera'];
                $prompt .= ", shot on";
                if (isset($cam['model'])) $prompt .= " {$cam['model']}";
                if (isset($cam['lens'])) $prompt .= " with {$cam['lens']} lens";
                if (isset($cam['settings'])) $prompt .= ", {$cam['settings']}";
            }

            $prompt .= ". ";

            // Imperfections (critical for realism)
            if (isset($data['imperfections']) && is_array($data['imperfections'])) {
                $prompt .= implode(', ', $data['imperfections']) . ". ";
            }

            // Materials
            if (isset($data['materials']) && is_array($data['materials'])) {
                $prompt .= implode(', ', $data['materials']) . ". ";
            }

            // Mood
            if (isset($data['mood'])) {
                $prompt .= $data['mood'] . ". ";
            }

            // CRITICAL: Comprehensive negatives - NO "photorealistic" word!
            $prompt .= "NOT photorealistic painting, NOT illustration, NOT 3D render, NOT digital art, NOT drawing, NOT sketch, NOT blueprint, NOT diagram, NOT technical drawing, NOT exaggerated lighting, NOT glossy plastic skin, NOT over-saturated, NOT HDR, NOT filters, NOT cinematic color grading. ";
            $prompt .= "Appears as authentic RAW photograph taken with professional DSLR camera, actual physical scene, documentary photography, natural appearance, real-world photography, no artificial elements, no UI overlays, no text";

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
            'ultra_photorealistic' => 'RAW photo shot on Canon EOS R5, 85mm f/1.8 lens, f/2.8, 1/125s, ISO 400, natural daylight, natural skin texture with visible pores, fine vellus hair, uneven skin tone, subtle imperfections, authentic scene, documentary photography style. NOT photorealistic painting, NOT 3D render, NOT digital art, NOT illustration, NOT drawing, NOT blueprint, NOT glossy plastic skin, appears as authentic RAW photograph taken with DSLR camera',

            'studio_photography' => 'Studio photo shot on Phase One IQ4, 85mm macro lens, f/4, professional softbox lighting, clean white background, realistic commercial photography, visible material textures, subtle specular highlights, authentic product appearance. NOT 3D render, NOT digital art, NOT illustration, NOT over-saturated',

            'natural_light' => 'RAW photo shot on Fujifilm GFX 100S, 35mm f/2 lens, f/5.6, 1/250s, ISO 640, golden hour natural light, shallow depth of field, authentic atmosphere, visible environmental details, natural variation, unposed candid moment, documentary photography. NOT illustration, NOT drawing, NOT digital art, NOT filters',

            'cinematic_photography' => 'Film photo shot on 35mm film camera, natural lighting, cinematic composition, film grain aesthetic, authentic moment, documentary style, real location photography, natural imperfections. NOT 3D render, NOT illustration, NOT digital art, NOT over-processed',

            'documentary_style' => 'RAW photo shot on Leica M10, 35mm f/2 lens, f/2.8, 1/500s, ISO 800, natural lighting, photojournalism style, candid authentic moment, documentary photography, visible imperfections, real-world scene. NOT staged, NOT illustration, NOT 3D render',

            'commercial_photography' => 'Commercial photo shot on Sony A7 III, 50mm f/1.4 lens, f/4, professional lighting, high-end advertising quality, realistic material textures, visible details, authentic product photography. NOT 3D render, NOT over-processed, NOT glossy plastic',

            'portrait_photography' => 'Portrait photo shot on Canon EOS R5, 85mm f/1.8 lens, f/2.8, Rembrandt lighting, natural skin texture with visible pores, uneven skin tone, gentle specular highlights, authentic expression, professional portrait quality. NOT illustration, NOT 3D render, NOT over-smoothed',

            'macro_photography' => 'Macro photo shot on Nikon D810, 100mm macro lens, f/8, ultra-sharp detail, extreme close-up, visible surface textures, realistic material imperfections, professional macro photography. NOT 3D render, NOT illustration, NOT digital art',
        ];

        $enhancement = $enhancements[$style] ?? $enhancements['ultra_photorealistic'];

        return "RAW photo of {$prompt}. {$enhancement}";
    }
}

<?php

namespace Modules\AI\App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * AI Prompt Enhancer Service
 *
 * Basit promptları ultra detaylı, profesyonel JSON scene description'lara çevirir
 * OpenAI GPT-4 kullanarak gerçekçi fotoğraf kalitesinde promptlar üretir
 */
class AIPromptEnhancer
{
    protected string $apiKey;
    protected string $apiUrl = 'https://api.openai.com/v1/chat/completions';
    protected string $model = 'gpt-4o'; // OpenAI GPT-4o (en hızlı GPT-4)

    public function __construct()
    {
        $this->apiKey = config('ai.openai_api_key');
    }

    /**
     * Basit promptu ultra detaylı scene description'a çevir
     *
     * @param string $simplePrompt Kullanıcının basit promptu
     * @param string $style Fotoğraf stili (ultra_photorealistic, studio_photography, etc.)
     * @param string $size Image size/aspect ratio (1024x1024, 1792x1024, 1024x1792)
     * @return string Ultra detaylı, optimize edilmiş prompt
     */
    public function enhancePrompt(string $simplePrompt, string $style = 'ultra_photorealistic', string $size = '1024x1024'): string
    {
        if (empty($this->apiKey)) {
            Log::warning('AIPromptEnhancer: OpenAI API key not configured, using basic enhancement');
            return $this->basicEnhancement($simplePrompt, $style);
        }

        try {
            $systemPrompt = $this->buildSystemPrompt($style, $size);
            $userPrompt = $this->buildUserPrompt($simplePrompt, $style, $size);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(30)->post($this->apiUrl, [
                'model' => $this->model,
                'max_tokens' => 800,
                'temperature' => 0.9, // Yüksek creativity için - her call farklı output
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $systemPrompt
                    ],
                    [
                        'role' => 'user',
                        'content' => $userPrompt
                    ]
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $enhancedPrompt = $data['choices'][0]['message']['content'] ?? '';

                if (!empty($enhancedPrompt)) {
                    // DEBUG: OpenAI GPT-4'ün RAW JSON'unu logla
                    Log::info('AIPromptEnhancer: OpenAI GPT-4 RAW output', [
                        'raw_json' => $enhancedPrompt
                    ]);

                    // JSON parse et ve DALL-E için optimize prompt'a çevir
                    if ($this->isJson($enhancedPrompt)) {
                        $finalPrompt = $this->convertJsonToPrompt($enhancedPrompt);

                        // DEBUG: Final DALL-E prompt'u logla
                        Log::info('AIPromptEnhancer: Final DALL-E prompt', [
                            'final_prompt' => $finalPrompt
                        ]);

                        return $finalPrompt;
                    }

                    // JSON değilse direkt kullan
                    Log::info('AIPromptEnhancer: Using non-JSON prompt', [
                        'prompt' => $enhancedPrompt
                    ]);
                    return $enhancedPrompt;
                }
            }

            Log::warning('AIPromptEnhancer: OpenAI API call failed', [
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
    protected function buildSystemPrompt(string $style, string $size): string
    {
        // Aspect ratio analizi
        [$width, $height] = explode('x', $size);
        $aspectRatio = $width / $height;

        if ($aspectRatio > 1.5) {
            $aspectGuidance = "HORIZONTAL FORMAT (1792x1024): Use WIDE-ANGLE lens (24mm or 35mm) to capture complete subject in horizontal frame. Frame composition must fit entire object width-wise without cropping top or bottom.";
        } elseif ($aspectRatio < 0.7) {
            $aspectGuidance = "VERTICAL FORMAT (1024x1792): Use standard to portrait lens (50mm to 85mm) for vertical composition. Ensure complete subject fits height-wise without cropping sides.";
        } else {
            $aspectGuidance = "SQUARE FORMAT (1024x1024): Use standard lens (35mm to 50mm) for balanced square composition. Frame subject completely within square format.";
        }

        $styleDescriptions = [
            'ultra_photorealistic' => 'RAW photo shot on professional DSLR camera, natural unprocessed look, documentary photography style',
            'studio_photography' => 'studio photo with professional lighting setup, controlled environment, commercial photography, studio lighting with 4K HD DSLR',
            'natural_light' => 'outdoor photo with natural daylight, golden hour or soft morning light, environmental photography',
            'cinematic_photography' => 'film camera aesthetic with cinematic composition, dramatic lighting, movie still quality',
            'documentary_style' => 'documentary photo, photojournalism style, candid authentic moment captured in real-time',
            'commercial_photography' => 'high-end commercial photo, advertising quality, professional product photography, industrial equipment catalog quality',
            'portrait_photography' => 'portrait photo with environmental context, professional headshot or editorial portrait quality',
            'macro_photography' => 'macro photo with extreme close-up, ultra-sharp detail, professional macro lens photography, visible material texture and finish',
        ];

        $styleDesc = $styleDescriptions[$style] ?? $styleDescriptions['ultra_photorealistic'];

        return <<<SYSTEM
You are a professional photography director. Transform prompts into detailed JSON scene descriptions for DALL-E 3 that produce authentic RAW photographs.

TARGET STYLE: {$styleDesc}

🎯 ASPECT RATIO & LENS GUIDANCE: {$aspectGuidance}

Create a UNIQUE JSON for each prompt with this structure:
{
  "subject": "Main subject with specific details",
  "view_framing": "CRITICAL: For equipment/objects: FULL complete view (entire object visible, NOT cropped). For people: full body / environmental portrait. Wide shot / medium shot",
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

🌍 CONTEXT: Images will be used in Turkey (Türkiye)
- Industrial/business equipment context
- Focus on EQUIPMENT, MACHINERY, and WORKPLACE ENVIRONMENT

MAXIMUM CREATIVITY RULES:
- Every JSON must be COMPLETELY DIFFERENT from previous ones
- NEVER repeat same camera + lens + lighting combination
- VARY focal length: wide (24mm), normal (35mm, 50mm), portrait (85mm, 100mm)
- VARY aperture: shallow DOF (f/1.8, f/2.8) vs deep focus (f/8, f/11)
- VARY lighting time and direction for each generation
- Adapt to subject AND add unexpected authentic details

🚨🚨🚨 ULTRA CRITICAL: ABSOLUTELY NO TEXT/WORDS/LABELS 🚨🚨🚨
THIS IS THE #1 PRIORITY RULE - VIOLATION = FAILED IMAGE

❌ FORBIDDEN - NEVER INCLUDE:
- NO text of any kind (English, Turkish, Asian, ANY language)
- NO labels, NO captions, NO annotations, NO subtitles
- NO infographics, NO diagrams, NO charts, NO data visualization
- NO UI elements, NO blue boxes, NO overlays, NO frames with text
- NO brands, NO trademarks, NO logos, NO company names
- NO camera settings overlay, NO EXIF data display
- NO numbered labels, NO arrows with text, NO explanatory text
- NO ethnic/racial descriptors as text (e.g., "Asian", "Black", "White")
- NO demographic labels, NO job titles, NO name tags
- NO instructional text, NO how-to steps, NO descriptions

✅ ONLY ALLOWED:
- PURE CLEAN PHOTOGRAPH
- Visual elements only
- No written language whatsoever
- Let the IMAGE speak, not words

⚠️ REMEMBER: This is PHOTOGRAPHY, not graphic design/infographic/presentation
If you add ANY text/words/labels → IMAGE REJECTED → TRY AGAIN

OUTPUT: Valid JSON only
SYSTEM;
    }

    /**
     * User prompt oluştur
     */
    protected function buildUserPrompt(string $simplePrompt, string $style, string $size): string
    {
        // Aspect ratio analizi
        [$width, $height] = explode('x', $size);
        $aspectRatio = $width / $height;

        if ($aspectRatio > 1.5) {
            $lensGuidance = "CRITICAL FOR HORIZONTAL FORMAT: Use 24mm or 35mm WIDE-ANGLE lens to fit complete subject in wide horizontal frame. Do NOT crop top or bottom of subject.";
        } elseif ($aspectRatio < 0.7) {
            $lensGuidance = "CRITICAL FOR VERTICAL FORMAT: Use 50mm to 85mm lens for vertical composition. Fit complete subject in tall frame without cropping sides.";
        } else {
            $lensGuidance = "For square format: Use 35mm to 50mm standard lens for balanced composition.";
        }

        return <<<USER
Create a COMPLETELY UNIQUE detailed JSON scene description for an authentic RAW photograph of:

"{$simplePrompt}"

Style: {$style}
Target Size: {$size}
{$lensGuidance}

CRITICAL REQUIREMENTS - RAW PHOTO ONLY:
1. This must be a RAW PHOTO, NOT photorealistic painting/illustration/drawing/3D render/blueprint/diagram
2. Photo of [subject], shot on professional DSLR camera
3. Actual physical scene captured with camera, not digital art or CGI
4. 🚨 SUBJECT MUST BE COMPLETE AND FULLY VISIBLE - For equipment/objects/vehicles: ENTIRE object in frame, NOT cropped, NOT cut off at edges. Show COMPLETE subject from all sides
5. Natural imperfections (visible pores, fine vellus hair, uneven skin tone, weathered surfaces)
6. Real-world lighting with specific direction and quality
7. Documentary photography style - authentic candid moment

STRUCTURED PROMPT FORMULA:
Subject + View/Framing + Background + Lighting + Camera Brand + Lens Setup

Example structure:
"RAW photo of [subject], [FULL EQUIPMENT VIEW showing complete object / full body / wide shot], [specific background], [golden hour lighting/Rembrandt lighting/etc], shot on [Canon EOS R5], [85mm f/1.8 lens], [f/2.8, 1/125s, ISO 400]"

MAXIMUM VARIATION - This must be DIFFERENT from any previous generation:
- Camera model: Canon EOS R5, Sony A7 III, Nikon D810, Fujifilm GFX 100S, or Leica M10
- Lens focal length: 24mm (wide), 35mm (normal), 50mm (normal), 85mm (portrait), or 100mm (telephoto)
- Aperture: f/1.8 (shallow DOF) to f/11 (deep focus)
- Lighting: golden hour, blue hour, window light from left, harsh midday sun, Rembrandt, butterfly, loop, or dramatic backlighting
- Perspective: eye-level, slight high angle, low angle, 3/4 view, side view (BUT ALWAYS keep complete subject in frame, NOT cropped)
- Framing: FULL view for equipment/objects (entire subject visible), wide shot, medium shot (NEVER tight crop that cuts off parts)
- Specific imperfections: visible pores, fine vellus hair, weathered surface, natural aging, wear patterns

Output valid JSON with: subject, view_framing, background, lighting, camera (model, lens, settings), imperfections, materials, mood.

Make it authentic RAW PHOTOGRAPH but CREATIVELY DIFFERENT each time.

🚨 CRITICAL: ABSOLUTELY NO TEXT/LABELS IN THE PHOTOGRAPH:
- This is a PURE PHOTOGRAPH, NOT an infographic/diagram/presentation
- NO text overlays, NO blue boxes with labels, NO captions, NO UI elements
- NO numbered labels, NO explanatory text, NO annotations
- Pure visual photograph only - like a professional would shoot for a catalog
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
            $prompt .= "NOT photorealistic painting, NOT illustration, NOT 3D render, NOT digital art, NOT drawing, NOT sketch, NOT blueprint, NOT diagram, NOT technical drawing, NOT infographic, NOT labeled diagram, NOT presentation slide, NOT cropped, NOT cut off, NOT partial view, NOT tight crop, NOT exaggerated lighting, NOT glossy plastic skin, NOT over-saturated, NOT HDR, NOT filters, NOT cinematic color grading. ";
            $prompt .= "Appears as authentic RAW photograph taken with professional DSLR camera, actual physical scene, documentary photography, natural appearance, real-world photography, no artificial elements. ";
            $prompt .= "Complete subject visible in frame, entire object shown from edge to edge, FULL view of equipment/vehicle/object. ";
            $prompt .= "ABSOLUTELY NO text, NO labels, NO captions, NO annotations, NO blue boxes, NO text overlays, NO UI elements, NO numbered labels, NO arrows with text, pure photograph only like professional catalog photography";

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

        return "RAW photo of {$prompt}. {$enhancement}. ABSOLUTELY NO text, NO labels, NO captions, NO blue boxes, NO UI elements, pure photograph only";
    }
}

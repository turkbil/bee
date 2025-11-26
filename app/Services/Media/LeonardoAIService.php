<?php

namespace App\Services\Media;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Leonardo AI Image Generation Service
 *
 * Blog içerikleri için tamamen dinamik AI görselleri üretir
 * Her görsel için benzersiz prompt zinciri oluşturur
 * API: https://docs.leonardo.ai/reference
 */
class LeonardoAIService
{
    protected string $apiKey;
    protected string $baseUrl = 'https://cloud.leonardo.ai/api/rest/v1';

    // Lucid Origin - En kaliteli model
    protected string $defaultModel = '7b592283-e8a7-4c5a-9ba6-d18c31f258b9';

    // Style UUID'leri
    protected array $styleUUIDs = [
        'cinematic' => 'a5632c7c-ddbb-4e2f-ba34-8456ab3ac436',
        'cinematic_closeup' => 'cc53f935-884c-40a0-b7eb-1f5c42821fb5',
        'dynamic' => '111dc692-d470-4eec-b791-3475abac4c46',
        'film' => '85da2dcc-c373-464c-9a7a-5624359be859',
        'hdr' => '97c20e5c-1af6-4d42-b227-54d03d8f0727',
        'moody' => '621e1c9a-6319-4bee-a12d-ae40659162fa',
        'stock_photo' => '5bdc3f2a-1be6-4d1c-8e77-992a30824a2c',
        'vibrant' => 'dee282d3-891f-4f73-ba02-7f8131e5541b',
        'neutral' => '0d914779-c822-430a-b976-30075033f1c4',
    ];

    public function __construct()
    {
        $this->apiKey = config('services.leonardo.api_key', '');
    }

    /**
     * Blog başlığından görsel üret
     */
    public function generateForBlog(string $title, string $context = 'blog'): ?array
    {
        if (empty($this->apiKey)) {
            Log::error('Leonardo AI: API key not configured');
            return null;
        }

        // Tamamen dinamik prompt oluştur
        $promptData = $this->buildDynamicPrompt($title, $context);

        Log::info('🎨 Leonardo AI: Starting generation', [
            'title' => $title,
            'prompt' => $promptData['prompt'],
            'style' => $promptData['style'],
        ]);

        try {
            // Görsel üretimi başlat
            $generationId = $this->createGeneration($promptData);

            if (!$generationId) {
                return null;
            }

            // Sonucu bekle ve al
            $imageUrl = $this->waitForGeneration($generationId);

            if (!$imageUrl) {
                return null;
            }

            // Görseli indir
            $imageData = $this->downloadImage($imageUrl);

            if (!$imageData) {
                return null;
            }

            Log::info('🎨 Leonardo AI: Generation successful', [
                'generation_id' => $generationId,
                'image_size' => strlen($imageData),
            ]);

            return [
                'content' => $imageData,
                'url' => $imageUrl,
                'generation_id' => $generationId,
                'provider' => 'leonardo',
                'prompt' => $promptData['prompt'],
                'style' => $promptData['style'],
            ];

        } catch (\Exception $e) {
            Log::error('🎨 Leonardo AI: Generation failed', [
                'error' => $e->getMessage(),
                'title' => $title,
            ]);
            return null;
        }
    }

    /**
     * Görsel üretimi başlat - Lucid Origin modeli ile
     */
    protected function createGeneration(array $promptData): ?string
    {
        // Gerçekçilik kısıtlaması + yazı yasağı ekle (ULTRA GÜÇLÜ)
        $realismConstraint = " CRITICAL ABSOLUTE REQUIREMENTS: 1) Realistic industrial equipment ONLY - real-world designs from Toyota, Linde, Jungheinrich, Crown, Yale. Standard colors: yellow, orange, red, blue, gray. NO futuristic/sci-fi/conceptual designs. 2) ABSOLUTELY ZERO TEXT, LETTERS, NUMBERS, OR SYMBOLS ANYWHERE IN THE IMAGE - no text on equipment, no text on signs, no text on floor, no text on walls, no text on screens, no text on labels, no comparison charts, no graphs with text, no infographics. The image must be 100% text-free. 3) NO comparison charts, tables, graphs, diagrams, or any visual with data/numbers.";

        $finalPrompt = $promptData['prompt'] . $realismConstraint;

        // ULTRA AGGRESSIVE Negative prompt - Yazı ve kıyaslama grafiklerini kesinlikle engelle
        $negativePrompt = "text, letters, words, numbers, digits, brand names, logos, labels, signs, watermarks, typography, writing, captions, subtitles, titles, stamps, badges, stickers, name plates, serial numbers, model numbers, any written content, illegible text, garbled text, distorted letters, comparison chart, comparison table, comparison graphic, infographic, data visualization, graph, pie chart, bar chart, spreadsheet, checklist, bullet points, price tags, specifications text, technical text, measurement text, warning text, instruction text, any form of alphanumeric characters";

        $response = Http::withHeaders([
            'accept' => 'application/json',
            'content-type' => 'application/json',
            'authorization' => 'Bearer ' . $this->apiKey,
        ])->timeout(30)->post($this->baseUrl . '/generations', [
            'modelId' => $this->defaultModel,
            'prompt' => $finalPrompt,
            'negative_prompt' => $negativePrompt,
            'styleUUID' => $promptData['styleUUID'],
            'contrast' => $promptData['contrast'],
            'num_images' => 1,
            'width' => 1472,
            'height' => 832,
            'alchemy' => false,
            'ultra' => false,
        ]);

        if (!$response->successful()) {
            Log::error('Leonardo AI: Create generation failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return null;
        }

        $data = $response->json();
        return $data['sdGenerationJob']['generationId'] ?? null;
    }

    /**
     * Üretimin tamamlanmasını bekle
     */
    protected function waitForGeneration(string $generationId, int $maxAttempts = 30, int $delay = 3): ?string
    {
        for ($i = 0; $i < $maxAttempts; $i++) {
            sleep($delay);

            $response = Http::withHeaders([
                'accept' => 'application/json',
                'authorization' => 'Bearer ' . $this->apiKey,
            ])->timeout(30)->get($this->baseUrl . '/generations/' . $generationId);

            if (!$response->successful()) {
                continue;
            }

            $data = $response->json();
            $generation = $data['generations_by_pk'] ?? null;

            if (!$generation) {
                continue;
            }

            $status = $generation['status'] ?? '';

            if ($status === 'COMPLETE') {
                $images = $generation['generated_images'] ?? [];
                if (!empty($images)) {
                    return $images[0]['url'];
                }
            } elseif ($status === 'FAILED') {
                Log::error('Leonardo AI: Generation failed', [
                    'generation_id' => $generationId,
                ]);
                return null;
            }

            Log::debug('Leonardo AI: Still processing', [
                'generation_id' => $generationId,
                'attempt' => $i + 1,
                'status' => $status,
            ]);
        }

        Log::error('Leonardo AI: Timeout waiting for generation', [
            'generation_id' => $generationId,
        ]);
        return null;
    }

    /**
     * Görseli indir
     */
    protected function downloadImage(string $url): ?string
    {
        $response = Http::timeout(60)->get($url);

        if (!$response->successful()) {
            Log::error('Leonardo AI: Image download failed', [
                'url' => $url,
                'status' => $response->status(),
            ]);
            return null;
        }

        return $response->body();
    }

    /**
     * Tamamen dinamik prompt oluştur
     * Prompt Zinciri: Subject → Context → Texture → Angle → Background → Lighting → Camera → Lens → Atmosphere
     */
    protected function buildDynamicPrompt(string $title, string $context): array
    {
        $tenantId = function_exists('tenant') && tenant() ? tenant('id') : null;

        // Tenant 2 (ixtif.com) - Endüstriyel ekipman
        if ($tenantId == 2) {
            return $this->buildIndustrialDynamicPrompt($title);
        }

        // Tenant 1001 (muzibu.com) - Müzik
        if ($tenantId == 1001) {
            return $this->buildMusicDynamicPrompt($title);
        }

        // Genel
        return $this->buildGenericDynamicPrompt($title);
    }

    /**
     * Endüstriyel ekipman için dinamik prompt (Tenant 2)
     * 11 KURAL FORMÜLÜ uygulanmış versiyon
     */
    protected function buildIndustrialDynamicPrompt(string $title): array
    {
        // Ana ekipmanı tespit et
        $equipment = $this->detectEquipment($title);

        // ========== 11 KURAL FORMÜLÜ - PROMPT ZİNCİRİ HAVUZLARI ==========

        // 1. SUBJECT + ACTION (Kural 2: Mikro-hikaye)
        $subjects = [
            "a {$equipment} being operated by a focused worker checking gauges",
            "a {$equipment} lifting heavy pallets with slight hydraulic strain",
            "a {$equipment} parked in the corner with keys still in ignition",
            "a {$equipment} undergoing maintenance with tools scattered around",
            "workers inspecting a {$equipment} pointing at worn parts",
            "a technician repairing a {$equipment} with grease-stained hands",
            "a {$equipment} moving through narrow aisles carefully navigating",
            "a {$equipment} loading a delivery truck while driver waits",
            "multiple {$equipment}s lined up with operators chatting during break",
            "a {$equipment} with its operator wiping sweat during hot shift",
            "a brand new {$equipment} being unloaded from transport",
            "a {$equipment} backing into charging station after long use",
            "a {$equipment} navigating around spilled packaging material",
            "new employee nervously training on a {$equipment}",
            "a {$equipment} carefully stacking boxes while supervisor watches",
            "a senior operator demonstrating {$equipment} technique to trainee",
            "a {$equipment} mid-turn in the middle of inventory counting",
            "a dusty {$equipment} engine cooling down after overtime shift",
            "a {$equipment} with safety lights alerting nearby pedestrians",
            "workers gathered near a {$equipment} reviewing delivery manifest",
        ];

        // 2. CONTEXT + TIME/SEASON (Kural 3: Ortam + Zaman/Mevsim)
        $contexts = [
            "during a busy Monday morning shift in early autumn",
            "in the middle of holiday season shipment rush",
            "during routine safety inspection on a quiet Tuesday",
            "at the end of a productive winter day as sun sets",
            "during Black Friday peak operations chaos",
            "while other workers pass by in summer heat",
            "as packages rush on conveyor belts before Christmas",
            "during spring training session for seasonal employees",
            "while cold rain falls outside the open warehouse door",
            "during year-end efficiency audit in December",
            "as the facility prepares for massive spring sale order",
            "during 3pm shift handover in late afternoon light",
            "while inventory is being reorganized before fiscal year end",
            "during scheduled weekend maintenance window",
            "as morning sunlight streams through frosty skylights",
            "while forklifts pass in the humid summer background",
            "during pre-dawn quality control check in winter darkness",
            "as workers sort packages in autumn afternoon glow",
            "during 5am early morning preparations before store opens",
            "while the facility buzzes with midday summer activity",
        ];

        // 3. FACTORY TEXTURE + IMPERFECTIONS (Kural 9: Kusurlar)
        $textures = [
            "scratched metal surfaces, worn rubber wheels, subtle dust on lens",
            "oil stains on concrete floor, slight motion blur from vibration",
            "industrial grime on machinery, fingerprints visible on controls",
            "dust particles floating in light beams, minor lens flare",
            "weathered wooden pallets, faded safety tape, natural vignetting",
            "scuffed safety barriers with paint chips, chromatic aberration at edges",
            "peeling warning labels, rust spots, slight grain visible",
            "condensation on cold surfaces creating soft focus areas",
            "fingerprints on control panels, subtle sensor dust spots",
            "chalk marks on floor, cable wear, natural film grain texture",
            "patched concrete showing age, minor barrel distortion",
            "faded floor markings from years of use, authentic wear patterns",
            "grease spots near maintenance areas, realistic dirt accumulation",
            "worn grip tape on handles, subtle shadow noise",
            "scratched safety glass, authentic industrial patina",
            "paint chips on metal railings revealing layers underneath",
            "tire marks creating leading lines, dust motes in light",
            "equipment showing honest wear from daily operations",
            "authentic scuffs and scratches telling equipment history",
            "real-world imperfections adding documentary authenticity",
        ];

        // 4. CAMERA ANGLE
        $angles = [
            "shot from a low angle emphasizing industrial scale",
            "captured from eye level for natural documentary perspective",
            "photographed from above showing organized workspace chaos",
            "taken from a dynamic three-quarter view",
            "shot through warehouse shelving creating depth layers",
            "captured with slight Dutch angle adding energy",
            "photographed from behind the operator for POV feel",
            "taken from distance showing full environmental context",
            "close-up focusing on operational details and wear",
            "wide environmental shot encompassing the scene",
            "shot from the side showing equipment profile",
            "captured looking down a long warehouse aisle",
            "photographed through industrial doorway frame",
            "taken from mezzanine level looking down at action",
            "shot at operator's shoulder level for intimate feel",
            "captured from worker's perspective approaching equipment",
            "photographed through safety netting adding texture",
            "taken with floor markings creating strong leading lines",
            "shot emphasizing vast depth of the facility",
            "captured with equipment as dominant foreground element",
        ];

        // 5. BACKGROUND
        $backgrounds = [
            "rows of metal pallet racks extending into hazy distance",
            "loading dock with trucks waiting in morning mist",
            "automated conveyor system in constant motion",
            "office windows overlooking the busy warehouse floor",
            "emergency exits with green signage glowing",
            "fire extinguisher stations along weathered walls",
            "electrical panels with indicator lights blinking",
            "stacked cardboard boxes ready for shipping",
            "empty pallets waiting with morning dew",
            "other workers operating equipment in background blur",
            "industrial fans creating slight motion",
            "time clocks and faded safety bulletin boards",
            "plastic strip curtains swaying between zones",
            "cold storage doors with frost patterns",
            "packaging stations with scattered materials",
            "quality control area with inspection lights",
            "break room visible through smudged windows",
            "shipping labels and well-used scanners on tables",
            "maintenance tool cabinets left open",
            "safety equipment lockers showing daily use",
        ];

        // 6. LIGHTING
        $lightings = [
            "harsh fluorescent lights casting sharp industrial shadows",
            "natural daylight from skylights creating god rays through dust",
            "warm golden evening light through high windows",
            "mixed practical lighting from different sources",
            "dramatic side lighting from open dock doors",
            "soft diffused overcast light for even tones",
            "bright LED panels with subtle flicker",
            "spotlights highlighting main work areas",
            "dim atmospheric lighting in background sections",
            "golden hour light streaming through dirty windows",
            "cool blue-tinted light from computer screens",
            "emergency red lighting creating accent color",
            "motion-sensor lights creating exposure variation",
            "light rays cutting through dusty industrial air",
            "reflected light bouncing off polished concrete",
            "harsh overhead lights with deep contrasty shadows",
            "backlit silhouette creating dramatic mood",
            "cool white industrial lighting mix",
            "warm incandescent spill from office areas",
            "safety lights creating orange accent highlights",
        ];

        // 7. CAMERA
        $cameras = [
            "shot on Canon EOS R5",
            "captured with Sony A7R IV",
            "photographed using Nikon Z9",
            "taken with Hasselblad X2D medium format",
            "shot on Fujifilm GFX 100S",
            "captured with Leica SL2-S",
            "photographed using Phase One IQ4",
            "taken with RED V-Raptor cinema camera",
            "shot on ARRI Alexa Mini LF",
            "captured with Blackmagic Pocket 6K Pro",
            "photographed using Canon C500 Mark II",
            "taken with Sony FX6 cinema camera",
            "shot on Panasonic S1H",
            "captured with Sigma fp L",
            "photographed using Canon 1DX Mark III",
        ];

        // 8. LENS
        $lenses = [
            "with 24mm wide angle lens at f/4",
            "using 35mm prime for natural documentary view",
            "with 50mm lens for standard perspective",
            "using 85mm for compressed industrial background",
            "with 16-35mm zoom capturing wide environment",
            "using 24-70mm versatile zoom lens",
            "with 70-200mm telephoto compression",
            "using tilt-shift lens for architectural control",
            "with 14mm ultra-wide for dramatic scale",
            "using vintage Helios 44-2 for character",
            "with 28mm street photography lens",
            "using 40mm pancake for discrete shooting",
            "with anamorphic lens for cinematic oval bokeh",
            "using adapted vintage Nikkor for organic rendering",
            "with f/1.4 aperture creating shallow focus",
        ];

        // 9. ATMOSPHERE
        $atmospheres = [
            "conveying industrial efficiency and precision",
            "showing the human element of logistics work",
            "emphasizing safety culture and professionalism",
            "capturing the rhythm of warehouse operations",
            "highlighting practical supply chain technology",
            "showing authentic wear from daily operations",
            "conveying massive scale and organization",
            "emphasizing teamwork and coordination",
            "capturing a moment of focused concentration",
            "showing harmony of human and machine",
            "highlighting logistics complexity and flow",
            "conveying reliability through routine",
            "showing pride in skilled equipment mastery",
            "capturing kinetic energy of a working facility",
            "emphasizing maintained order despite heavy use",
            "showing time's passage through honest wear",
            "conveying controlled urgency in busy periods",
            "highlighting professional attention to detail",
            "showing brief calm moments between rushes",
            "capturing satisfaction of work well done",
        ];

        // 10. FILM STOCK (Kural 8: Film Stoku Referansı) - YENİ!
        $filmStocks = [
            "shot on Kodak Portra 400 film stock emulation",
            "with Fuji Pro 400H color science",
            "emulating Kodak Ektar 100 vibrant tones",
            "using Cinestill 800T tungsten balanced look",
            "with Kodak Vision3 500T cinema film aesthetic",
            "emulating Fuji Velvia 50 saturated colors",
            "shot with Kodak Gold 200 warm consumer film look",
            "using Ilford HP5 Plus black and white tonality converted to color",
            "with Kodak Tri-X 400 grain structure in color",
            "emulating Agfa Vista 400 European color palette",
            "shot on Lomography 800 cross-processed look",
            "with Kodak Portra 160 smooth skin-tone rendering",
            "using Fuji Superia 400 everyday film aesthetic",
            "emulating Kodak E100 slide film saturation",
            "with CineStill 50D daylight balanced cinema look",
        ];

        // 11. POST-PROCESSING (Kural 11: Son İşlem) - YENİ!
        $postProcessing = [
            "with subtle cinematic color grading",
            "processed with lifted shadows and muted highlights",
            "with desaturated industrial film look",
            "using teal and orange color grade",
            "with natural documentary color treatment",
            "processed for magazine editorial quality",
            "with subtle split-toning in shadows",
            "using filmic tone curve and faded blacks",
            "with authentic photojournalistic processing",
            "processed with slight cross-processing effect",
            "using muted earth tone color palette",
            "with commercial photography finish",
            "processed for industrial catalog aesthetic",
            "with subtle film halation on highlights",
            "using practical on-set color science",
        ];

        // ========== PROMPT BİRLEŞTİRME (11 KURAL FORMÜLÜ) ==========

        $prompt = sprintf(
            "Documentary photograph of %s, %s. Environmental details include %s. %s. Background shows %s. %s. %s %s. %s. %s. The image %s.",
            $subjects[array_rand($subjects)],
            $contexts[array_rand($contexts)],
            $textures[array_rand($textures)],
            $angles[array_rand($angles)],
            $backgrounds[array_rand($backgrounds)],
            $lightings[array_rand($lightings)],
            $cameras[array_rand($cameras)],
            $lenses[array_rand($lenses)],
            $filmStocks[array_rand($filmStocks)],
            $postProcessing[array_rand($postProcessing)],
            $atmospheres[array_rand($atmospheres)]
        );

        // Style ve contrast seçimi
        $styles = ['cinematic', 'dynamic', 'film', 'hdr', 'moody', 'stock_photo', 'vibrant', 'neutral'];
        $selectedStyle = $styles[array_rand($styles)];

        $contrasts = [3, 3.5, 4];
        $selectedContrast = $contrasts[array_rand($contrasts)];

        return [
            'prompt' => $prompt,
            'style' => $selectedStyle,
            'styleUUID' => $this->styleUUIDs[$selectedStyle],
            'contrast' => $selectedContrast,
        ];
    }

    /**
     * Müzik sektörü için dinamik prompt (Tenant 1001)
     * 11 KURAL FORMÜLÜ uygulanmış versiyon
     */
    protected function buildMusicDynamicPrompt(string $title): array
    {
        // Müzik enstrümanı/konusu tespit et
        $musicSubject = $this->detectMusicSubject($title);

        // ========== 11 KURAL FORMÜLÜ - PROMPT ZİNCİRİ HAVUZLARI ==========

        // 1. SUBJECT + ACTION (Kural 2: Mikro-hikaye)
        $subjects = [
            "a musician deeply focused playing {$musicSubject} with eyes closed",
            "a worn {$musicSubject} resting on a stand catching dust particles",
            "weathered hands positioned on {$musicSubject} mid-performance",
            "a vintage {$musicSubject} with scratches telling its history",
            "a {$musicSubject} during an intense recording take",
            "a collection of instruments with {$musicSubject} as centerpiece",
            "a {$musicSubject} being carefully tuned by experienced hands",
            "intimate close-up of {$musicSubject} showing wear and character",
            "a {$musicSubject} waiting on empty stage before performance",
            "a {$musicSubject} in a practice room with coffee cups nearby",
            "musician's fingers dancing across {$musicSubject} keys/strings",
            "a {$musicSubject} reflected in studio glass during late session",
            "sweat drops on musician playing {$musicSubject} under hot lights",
            "a {$musicSubject} with sheet music scattered around it",
            "a {$musicSubject} being passed between generations",
            "a {$musicSubject} in its open case after a gig",
            "musician lost in the moment with {$musicSubject}",
            "a {$musicSubject} with cables trailing across worn floor",
            "close-up of {$musicSubject} bridge showing rosin dust",
            "a {$musicSubject} leaning against a vintage amplifier",
        ];

        // 2. CONTEXT + TIME/SEASON (Kural 3: Ortam + Zaman/Mevsim)
        $contexts = [
            "in a professional recording studio during late night session",
            "during a 3am jam session with empty coffee cups",
            "at a live concert venue on a hot summer night",
            "in a cozy home studio on a rainy autumn afternoon",
            "during an early morning music lesson in spring light",
            "at a rehearsal space after midnight practice",
            "in a vintage music shop on a quiet winter morning",
            "during soundcheck as afternoon sun streams through",
            "at a music festival backstage before sunset performance",
            "in an acoustic treatment room during winter recording session",
            "during golden hour streaming through studio windows",
            "in a basement studio during a thunderstorm",
            "at a jazz club during blue hour",
            "in a concert hall during empty afternoon rehearsal",
            "during a cold winter night home recording session",
            "at an outdoor festival as summer twilight falls",
            "in a vintage analog studio during humid summer session",
            "during post-show breakdown at midnight",
            "in a practice room as autumn leaves fall outside",
            "during pre-dawn studio session with city lights visible",
        ];

        // 3. TEXTURE + IMPERFECTIONS (Kural 9: Kusurlar)
        $textures = [
            "worn leather straps, fret wear, subtle lens dust visible",
            "fingerprints on polished surfaces, slight motion blur from playing",
            "vintage patina on hardware, natural film grain texture",
            "dust particles floating in spotlight, chromatic aberration at edges",
            "scratched pickguard, worn frets, natural vignetting",
            "condensation on cold strings, soft focus on background",
            "cable wear, tape marks on floor, subtle lens flare",
            "rosin dust on strings, fingerprints on keys, sensor dust spots",
            "sweat stains on neck, worn tuning pegs, authentic grain",
            "gaffer tape residue, cable tangles, natural imperfections",
            "worn volume knobs, faded labels, subtle halation on highlights",
            "pick scratches near soundhole, dust in fretboard grooves",
            "oxidized hardware, yellowed ivory keys, vintage character",
            "stick marks on drum heads, cymbal patina, honest wear",
            "microphone pop filter showing use, cable coils on floor",
            "amp tolex showing gig wear, speaker cloth tears",
            "mixing board faders showing finger oil wear",
            "headphone cable tangles, coffee ring stains on desk",
            "patch cable chaos, power strip overload visible",
            "authentic studio mess adding documentary realism",
        ];

        // 4. CAMERA ANGLE
        $angles = [
            "shot from low angle emphasizing performer's presence",
            "captured at eye level for intimate connection",
            "photographed from above showing workspace context",
            "taken from three-quarter view for dynamic composition",
            "shot through studio glass creating layers",
            "captured with slight Dutch angle for energy",
            "photographed from behind showing performer's posture",
            "taken from audience perspective looking up at stage",
            "close-up isolating hands on instrument",
            "wide shot encompassing full studio environment",
            "shot from side profile showing concentration",
            "captured through drum kit creating foreground interest",
            "photographed through microphone stand forest",
            "taken from control room looking into live room",
            "shot at instrument level emphasizing form",
            "captured looking down guitar neck toward headstock",
            "photographed through piano strings",
            "taken with leading lines of cables and cords",
            "shot emphasizing depth of recording space",
            "captured with musician as silhouette against lights",
        ];

        // 5. BACKGROUND
        $backgrounds = [
            "acoustic panels and vintage gear in soft focus",
            "amplifier stacks with glowing tubes",
            "mixing console with countless lit channels",
            "other musicians warming up in background blur",
            "concert audience as bokeh lights",
            "studio monitors and reference equipment",
            "vintage posters and gold records on walls",
            "exposed brick with neon beer signs",
            "soundproofing foam creating texture pattern",
            "cable racks and patch bays",
            "drum kit waiting in shadowy corner",
            "piano reflecting stage lights",
            "recording booth through glass",
            "vintage microphone collection displayed",
            "tour cases stacked against wall",
            "audience phones creating light dots",
            "smoke machine haze diffusing lights",
            "instrument cases open on floor",
            "studio clock showing late hour",
            "coffee maker and takeout containers on desk",
        ];

        // 6. LIGHTING
        $lightings = [
            "warm amber stage lighting with dramatic falloff",
            "soft studio lighting through silk diffusers",
            "dramatic single spotlight from above",
            "neon accent lights creating color separation",
            "natural window light mixing with tungsten practicals",
            "colored LED strips creating mood wash",
            "classic incandescent warmth from desk lamps",
            "blue hour light through rain-streaked windows",
            "mixed practical and controlled studio lights",
            "silhouette backlighting with lens flare",
            "candle-like warmth from tube amplifiers",
            "harsh stage par can creating hard shadows",
            "soft bounce from white studio walls",
            "colored gel creating split lighting",
            "rim light separating subject from background",
            "overhead fluorescent mixed with warm spots",
            "string lights creating bokeh orbs",
            "gobo patterns on walls from stage lights",
            "screen glow illuminating face in control room",
            "practical lamp creating warm accent",
        ];

        // 7. CAMERA
        $cameras = [
            "shot on Canon EOS R5",
            "captured with Sony A7 III",
            "photographed using Leica Q2",
            "taken with Fujifilm X-T5",
            "shot on Nikon Z6 II",
            "captured with Hasselblad X2D",
            "photographed using Leica M11",
            "taken with Sony A7R V",
            "shot on Canon R6 Mark II",
            "captured with Fujifilm GFX 50S II",
            "photographed using Nikon Z8",
            "taken with Leica SL2-S",
            "shot on Panasonic S5 II",
            "captured with Sony A7C II",
            "photographed using Canon R3",
        ];

        // 8. LENS
        $lenses = [
            "with 35mm prime lens at f/1.8",
            "using 50mm f/1.2 for creamy bokeh",
            "with 85mm portrait lens for compression",
            "using 24mm capturing environmental context",
            "with vintage anamorphic lens for oval bokeh",
            "using Helios 44-2 for swirly background blur",
            "with 135mm f/1.8 for subject isolation",
            "using 28mm for intimate environmental shot",
            "with Voigtlander 40mm for natural perspective",
            "using vintage Canon FD glass for character",
            "with Zeiss Batis 25mm for sharpness with soul",
            "using adapted Minolta Rokkor for warmth",
            "with Sigma Art 35mm for clinical sharpness",
            "using Leica Summilux 50mm for rendering",
            "with tilt-shift for selective focus effect",
        ];

        // 9. ATMOSPHERE
        $atmospheres = [
            "conveying deep passion for music",
            "showing artistic dedication and focus",
            "capturing raw creative energy",
            "emphasizing years of musical craftsmanship",
            "showing intimate connection with instrument",
            "conveying the vulnerability of performance",
            "capturing the magic of live music",
            "emphasizing dedication to the craft",
            "showing music as spiritual practice",
            "conveying the loneliness of late night creation",
            "capturing collaborative creative energy",
            "emphasizing the physical nature of playing",
            "showing music transcending technical skill",
            "conveying the passage of time through practice",
            "capturing a breakthrough creative moment",
            "emphasizing the tradition of musical heritage",
            "showing the exhaustion and joy post-performance",
            "conveying complete absorption in sound",
            "capturing the anticipation before a show",
            "emphasizing the sacred ritual of making music",
        ];

        // 10. FILM STOCK (Kural 8: Film Stoku Referansı) - YENİ!
        $filmStocks = [
            "shot on Kodak Portra 800 pushed one stop",
            "with Fuji Pro 400H pastel tones",
            "emulating Cinestill 800T tungsten halation",
            "using Kodak Tri-X 400 converted to duotone",
            "with Ilford Delta 3200 grain structure in color",
            "emulating Kodak Vision3 500T cinema aesthetic",
            "shot with Lomography 800 cross-processed",
            "using Fuji Natura 1600 low light rendering",
            "with Kodak Portra 160 smooth gradations",
            "emulating Agfa Scala black and white toned",
            "shot on expired Kodak Ektachrome look",
            "with Fuji Velvia 50 saturated tones for contrast",
            "using Kodak Gold 200 consumer warmth",
            "emulating Polaroid 600 color palette",
            "with CineStill 50D daylight balanced clarity",
        ];

        // 11. POST-PROCESSING (Kural 11: Son İşlem) - YENİ!
        $postProcessing = [
            "with moody cinematic color grading",
            "processed with crushed blacks and lifted shadows",
            "with vintage analog warmth treatment",
            "using complementary color split-toning",
            "with documentary concert photography processing",
            "processed for album cover aesthetic",
            "with subtle film halation on highlights",
            "using muted tones with selective color pop",
            "with high contrast editorial finish",
            "processed with cross-processing color shift",
            "using VSCO film emulation aesthetic",
            "with natural skin tones preserved",
            "processed for intimate candid feel",
            "with subtle vignette drawing eye to subject",
            "using period-appropriate color science",
        ];

        // ========== PROMPT BİRLEŞTİRME (11 KURAL FORMÜLÜ) ==========

        $prompt = sprintf(
            "Intimate photograph of %s, %s. Environmental details include %s. %s. Background shows %s. %s. %s %s. %s. %s. The image %s.",
            $subjects[array_rand($subjects)],
            $contexts[array_rand($contexts)],
            $textures[array_rand($textures)],
            $angles[array_rand($angles)],
            $backgrounds[array_rand($backgrounds)],
            $lightings[array_rand($lightings)],
            $cameras[array_rand($cameras)],
            $lenses[array_rand($lenses)],
            $filmStocks[array_rand($filmStocks)],
            $postProcessing[array_rand($postProcessing)],
            $atmospheres[array_rand($atmospheres)]
        );

        $styles = ['cinematic', 'moody', 'film', 'vibrant'];
        $selectedStyle = $styles[array_rand($styles)];

        $contrasts = [3, 3.5, 4];
        $selectedContrast = $contrasts[array_rand($contrasts)];

        return [
            'prompt' => $prompt,
            'style' => $selectedStyle,
            'styleUUID' => $this->styleUUIDs[$selectedStyle],
            'contrast' => $selectedContrast,
        ];
    }

    /**
     * Genel dinamik prompt
     */
    protected function buildGenericDynamicPrompt(string $title): array
    {
        $prompt = "Professional photograph representing the concept of: {$title}. Shot on high-end camera with natural lighting, showing authentic details and professional composition.";

        return [
            'prompt' => $prompt,
            'style' => 'stock_photo',
            'styleUUID' => $this->styleUUIDs['stock_photo'],
            'contrast' => 3.5,
        ];
    }

    /**
     * Başlıktan ekipman tipini tespit et
     */
    protected function detectEquipment(string $title): string
    {
        $titleLower = mb_strtolower($title);

        // Forklift varyasyonları
        if (preg_match('/forklift/ui', $titleLower)) {
            $types = [
                'yellow forklift',
                'electric forklift',
                'propane forklift',
                'reach truck',
                'counterbalance forklift',
                'side loader forklift',
                'turret truck',
                'order picker forklift',
                'rough terrain forklift',
                'compact forklift',
            ];
            return $types[array_rand($types)];
        }

        // Transpalet varyasyonları
        if (preg_match('/transpalet|palet\s*jak/ui', $titleLower)) {
            $types = [
                'electric pallet jack',
                'manual pallet truck',
                'powered pallet jack',
                'walkie pallet jack',
                'rider pallet jack',
                'low-profile pallet jack',
                'stainless steel pallet jack',
                'scale pallet jack',
                'narrow aisle pallet jack',
                'heavy-duty pallet truck',
            ];
            return $types[array_rand($types)];
        }

        // İstif makinesi
        if (preg_match('/istif|stacker/ui', $titleLower)) {
            $types = [
                'electric stacker',
                'manual stacker',
                'walkie stacker',
                'counterbalance stacker',
                'reach stacker',
                'semi-electric stacker',
                'pallet stacker',
                'platform stacker',
            ];
            return $types[array_rand($types)];
        }

        // Order picker
        if (preg_match('/order\s*picker|sipariş\s*toplama/ui', $titleLower)) {
            $types = [
                'order picker',
                'stock picker',
                'cherry picker',
                'man-up order picker',
                'low-level order picker',
            ];
            return $types[array_rand($types)];
        }

        // Depo/lojistik genel
        if (preg_match('/depo|lojistik|warehouse|logistics/ui', $titleLower)) {
            $types = [
                'warehouse equipment',
                'material handling equipment',
                'logistics machinery',
                'warehouse vehicles',
            ];
            return $types[array_rand($types)];
        }

        // Varsayılan - karışık ekipman
        $defaults = [
            'forklift',
            'pallet jack',
            'warehouse stacker',
            'material handling equipment',
        ];
        return $defaults[array_rand($defaults)];
    }

    /**
     * Başlıktan müzik konusunu tespit et
     */
    protected function detectMusicSubject(string $title): string
    {
        $titleLower = mb_strtolower($title);

        if (preg_match('/gitar|guitar/ui', $titleLower)) {
            return 'guitar';
        }
        if (preg_match('/piyano|piano/ui', $titleLower)) {
            return 'piano';
        }
        if (preg_match('/davul|drum/ui', $titleLower)) {
            return 'drums';
        }
        if (preg_match('/keman|violin/ui', $titleLower)) {
            return 'violin';
        }
        if (preg_match('/synthesizer|synth/ui', $titleLower)) {
            return 'synthesizer';
        }

        return 'musical instrument';
    }

    /**
     * Direkt prompt'tan görsel üret (Admin Panel için)
     * OpenAI tarafından enhance edilmiş prompt alır
     *
     * @param string $enhancedPrompt Enhance edilmiş prompt
     * @param array $options ['width' => 1472, 'height' => 832, 'style' => 'cinematic']
     * @return array|null ['url' => '...', 'content' => '...', 'generation_id' => '...']
     */
    public function generateFromPrompt(string $enhancedPrompt, array $options = []): ?array
    {
        if (empty($this->apiKey)) {
            Log::error('Leonardo AI: API key not configured');
            return null;
        }

        $width = $options['width'] ?? 1472;
        $height = $options['height'] ?? 832;
        $style = $options['style'] ?? 'cinematic';
        $styleUUID = $this->styleUUIDs[$style] ?? $this->styleUUIDs['cinematic'];

        Log::info('🎨 Leonardo AI: Starting generation from enhanced prompt', [
            'prompt_length' => strlen($enhancedPrompt),
            'style' => $style,
            'dimensions' => "{$width}x{$height}",
        ]);

        try {
            // Görsel üretimi başlat
            $generationId = $this->createGenerationDirect($enhancedPrompt, $width, $height, $styleUUID);

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

            Log::info('🎨 Leonardo AI: Generation successful', [
                'generation_id' => $generationId,
                'image_size' => strlen($imageData),
            ]);

            return [
                'url' => $imageUrl,
                'content' => $imageData,
                'generation_id' => $generationId,
                'provider' => 'leonardo',
                'prompt' => $enhancedPrompt,
                'style' => $style,
            ];

        } catch (\Exception $e) {
            Log::error('🎨 Leonardo AI: Generation failed', [
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Direkt prompt ile generation oluştur (enhance edilmiş prompt için)
     */
    protected function createGenerationDirect(string $prompt, int $width, int $height, string $styleUUID): ?string
    {
        // Negative prompt - yazı ve gereksiz elementleri engelle
        $negativePrompt = "text, letters, words, numbers, digits, brand names, logos, labels, signs, watermarks, typography, writing, captions, subtitles, titles, stamps, badges, stickers, distorted faces, extra limbs, blurry, low quality";

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
            Log::error('Leonardo AI: Create generation failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return null;
        }

        $data = $response->json();
        return $data['sdGenerationJob']['generationId'] ?? null;
    }

    /**
     * API durumunu kontrol et
     */
    public function checkApiStatus(): array
    {
        if (empty($this->apiKey)) {
            return [
                'status' => 'error',
                'message' => 'API key not configured',
            ];
        }

        try {
            $response = Http::withHeaders([
                'accept' => 'application/json',
                'authorization' => 'Bearer ' . $this->apiKey,
            ])->timeout(10)->get($this->baseUrl . '/me');

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'status' => 'ok',
                    'user' => $data['user_details'] ?? [],
                ];
            }

            return [
                'status' => 'error',
                'message' => 'API request failed: ' . $response->status(),
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }
}

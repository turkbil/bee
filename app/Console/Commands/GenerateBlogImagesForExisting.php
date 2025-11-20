<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\Blog\App\Models\Blog;
use App\Models\Tenant;

/**
 * 60 Mevcut Blog İçin Özel Promptlarla Görsel Üretimi
 *
 * Her blog için Claude tarafından özel hazırlanmış promptlar kullanılır
 * Tenant 2 (ixtif.com) için endüstriyel ekipman görselleri
 */
class GenerateBlogImagesForExisting extends Command
{
    protected $signature = 'blog:generate-images
                            {--blog-id= : Tek bir blog için ID}
                            {--limit=5 : Kaç blog işlenecek}
                            {--dry-run : Sadece promptları göster, üretme}';

    protected $description = 'Görseli olmayan bloglar için özel promptlarla görsel üret (Tenant 2)';

    protected string $apiKey;
    protected string $baseUrl = 'https://cloud.leonardo.ai/api/rest/v1';
    protected string $defaultModel = '7b592283-e8a7-4c5a-9ba6-d18c31f258b9'; // Lucid Origin

    protected array $styleUUIDs = [
        'cinematic' => 'a5632c7c-ddbb-4e2f-ba34-8456ab3ac436',
        'dynamic' => '111dc692-d470-4eec-b791-3475abac4c46',
        'film' => '85da2dcc-c373-464c-9a7a-5624359be859',
        'hdr' => '97c20e5c-1af6-4d42-b227-54d03d8f0727',
        'moody' => '621e1c9a-6319-4bee-a12d-ae40659162fa',
        'stock_photo' => '5bdc3f2a-1be6-4d1c-8e77-992a30824a2c',
        'vibrant' => 'dee282d3-891f-4f73-ba02-7f8131e5541b',
        'neutral' => '0d914779-c822-430a-b976-30075033f1c4',
    ];

    /**
     * 60 Blog İçin Özel Hazırlanmış Promptlar (Blog ID bazlı)
     * Her prompt Claude tarafından blog başlığına göre özel yazılmıştır
     * NOT: Tüm promptlar yazı/metin içermeyen, sadece görsel odaklıdır
     */
    protected function getCustomPrompts(): array
    {
        return [
            // Blog ID => Prompt Data
            // TÜM PROMPTLAR YENİDEN YAZILDI - HİÇBİR YAZI/METİN/RAKAM İÇERMEZ
            20 => [
                'prompt' => 'Photograph of a warehouse manager examining three different forklift models arranged in a dealer showroom. Details include yellow, orange, and blue forklifts in different sizes, tire conditions visible, mast heights varying. Shot from elevated angle showing all equipment clearly. Background shows clean dealer showroom floor. Professional lighting for equipment evaluation. Shot on Canon EOS R5 with 35mm lens.',
                'style' => 'stock_photo',
                'contrast' => 3.5,
            ],
            21 => [
                'prompt' => 'Photograph of three premium pallet jacks arranged on clean showroom floor. Details include ergonomic handle designs in different colors, chrome wheel quality visible, sturdy fork construction. Shot from showroom customer perspective. Background shows professional equipment dealer environment. Even product lighting. Shot on Sony A7R IV with 50mm lens.',
                'style' => 'neutral',
                'contrast' => 3.5,
            ],
            22 => [
                'prompt' => 'Photograph of an electric forklift operating in clean indoor warehouse. Details include battery compartment visible, no exhaust system, modern control panel, operator in clean uniform. Shot showing clean emission-free operation. Background shows pristine indoor facility with organized shelving. Bright clean lighting. Shot on Nikon Z9 with 35mm lens.',
                'style' => 'stock_photo',
                'contrast' => 3.5,
            ],
            33 => [
                'prompt' => 'Photograph of operator using scale pallet jack to weigh palletized cargo in shipping area. Details include digital scale display on handle, proper positioning technique, load centered on forks, safety shoes visible. Shot from instructional angle showing process. Background shows shipping area with wrapped pallets. Well-lit environment. Shot on Canon 5D Mark IV with 35mm lens.',
                'style' => 'dynamic',
                'contrast' => 3.5,
            ],
            38 => [
                'prompt' => 'Photograph of electric stacker reaching high racking in narrow aisle warehouse. Details include extended mast at full height, operator in safety harness, organized racking system, pallets stored vertically. Wide shot showing vertical space utilization. Background shows fully utilized warehouse height. Industrial high-bay lighting. Shot on Fujifilm GFX 100S with 24mm lens.',
                'style' => 'stock_photo',
                'contrast' => 3.5,
            ],
            43 => [
                'prompt' => 'Photograph of maintenance technician performing forklift service with organized tools spread on clean mat. Details include oil being changed, filters laid out for replacement, wrench in hand, safety gloves worn. Shot at working level showing maintenance process. Background shows clean workshop area. Bright workshop lighting. Shot on Canon EOS R5 with 50mm lens.',
                'style' => 'neutral',
                'contrast' => 3,
            ],
            44 => [
                'prompt' => 'Photograph of business professionals in meeting room with scale pallet jack visible through window in warehouse. Details include two people shaking hands, documents on table, view of equipment through glass. Shot of decision-making moment. Background shows office with warehouse visible through window. Professional office lighting. Shot on Leica SL2 with 35mm lens.',
                'style' => 'stock_photo',
                'contrast' => 3.5,
            ],
            45 => [
                'prompt' => 'Photograph of three order pickers in different sizes displayed in dealer showroom. Details include entry-level compact model, mid-size model, premium tall-reach model arranged by size. Shot from buyer perspective. Background shows equipment dealer environment with high ceiling. Showroom lighting. Shot on Sony A7R IV with 35mm lens.',
                'style' => 'neutral',
                'contrast' => 3.5,
            ],
            48 => [
                'prompt' => 'Photograph of electric forklift operating quietly in pharmaceutical warehouse with temperature-controlled environment. Details include clean white forklift, operator in lab coat, organized medical supplies on shelving. Shot emphasizing clean quiet operation. Background shows sterile storage area. Clean pharmaceutical lighting. Shot on Canon EOS R5 with 35mm lens.',
                'style' => 'stock_photo',
                'contrast' => 3.5,
            ],
            49 => [
                'prompt' => 'Photograph of premium electric pallet trucks from different manufacturers displayed together in equipment dealership. Details include distinct brand colors like yellow, orange, red, quality construction visible, ergonomic handles. Shot from evaluation angle. Background shows professional dealer showroom. Even display lighting. Shot on Hasselblad with 65mm lens.',
                'style' => 'cinematic',
                'contrast' => 3.5,
            ],
            50 => [
                'prompt' => 'Photograph of customer and dealer walking through rental yard inspecting forklift options. Details include row of forklifts in various colors, dealer pointing to equipment, customer examining machines. Shot of selection process. Background shows rental fleet variety. Outdoor natural lighting. Shot on Nikon Z9 with 35mm lens.',
                'style' => 'dynamic',
                'contrast' => 3.5,
            ],
            51 => [
                'prompt' => 'Photograph of electric and manual scale pallet jacks positioned side by side in warehouse. Details include battery-powered unit with digital display, manual unit with mechanical components, both with loads. Side by side shot showing both types. Background shows practical use environment. Even lighting. Shot on Sony A7R IV with 50mm lens.',
                'style' => 'neutral',
                'contrast' => 3,
            ],
            52 => [
                'prompt' => 'Photograph of buyer examining electric forklift closely in dealer showroom. Details include buyer touching control panel, inspecting battery compartment, dealer standing nearby. Shot of careful evaluation. Background shows various electric forklifts available. Professional showroom lighting. Shot on Canon EOS R5 with 35mm lens.',
                'style' => 'stock_photo',
                'contrast' => 3.5,
            ],
            53 => [
                'prompt' => 'Photograph of potential buyer inspecting forklift tires and hydraulic system in outdoor lot. Details include person crouching to check tire tread, examining hydraulic hoses, testing lift mechanism. Shot showing due diligence. Background shows equipment for sale. Clear daylight lighting. Shot on Nikon Z9 with 50mm lens.',
                'style' => 'neutral',
                'contrast' => 3,
            ],
            54 => [
                'prompt' => 'Photograph of LPG forklifts from leading manufacturers arranged in outdoor dealer lot. Details include propane tanks visible on rear, different cab designs, various lift capacities shown by size. Wide shot. Background shows outdoor equipment dealer. Natural daylight. Shot on Phase One with 55mm lens.',
                'style' => 'hdr',
                'contrast' => 4,
            ],
            55 => [
                'prompt' => 'Photograph of top stacker brands displayed together in warehouse showroom. Details include different mast designs, various colors indicating brands, operator platforms at different heights. Multi-brand display shot. Background shows dealer environment. Exhibition lighting. Shot on Canon EOS R5 with 35mm lens.',
                'style' => 'stock_photo',
                'contrast' => 3.5,
            ],
            56 => [
                'prompt' => 'Photograph of electric forklift operating indoors and diesel forklift visible through open door working outdoors. Details include clean electric operation inside, diesel unit with exhaust working outside. Split scene composition. Background shows indoor/outdoor applications. Mixed lighting conditions. Shot on Sony A7R IV with 24mm lens.',
                'style' => 'dynamic',
                'contrast' => 3.5,
            ],
            57 => [
                'prompt' => 'Photograph of brand new forklift parked next to well-maintained used forklift in dealer lot. Details include new unit with pristine paint, used unit with minor wear showing hours of use, both in working condition. Side by side shot. Background shows dealer lot. Clear daylight. Shot on Nikon Z9 with 35mm lens.',
                'style' => 'neutral',
                'contrast' => 3.5,
            ],
            58 => [
                'prompt' => 'Photograph of two diesel forklifts with automatic and manual transmission in industrial yard. Details include different pedal configurations, operator foot positions, transmission lever visible. Technical shot. Background shows industrial environment. Working condition lighting. Shot on Canon 5D Mark IV with 35mm lens.',
                'style' => 'stock_photo',
                'contrast' => 3.5,
            ],
            66 => [
                'prompt' => 'Photograph of new manual pallet jack displayed next to quality used unit showing wear patterns. Details include shiny new chrome versus polished used handle, wheel conditions, fork surfaces. Comparison shot. Background shows simple warehouse floor. Clear lighting. Shot on Sony A7R IV with 50mm lens.',
                'style' => 'neutral',
                'contrast' => 3,
            ],
            68 => [
                'prompt' => 'Photograph of premium electric pallet truck models with ergonomic features displayed in dealer showroom. Details include comfortable handles, battery compartments, various capacities shown by size. Selection display shot. Background shows dealer showroom. Product highlight lighting. Shot on Canon EOS R5 with 35mm lens.',
                'style' => 'stock_photo',
                'contrast' => 3.5,
            ],
            69 => [
                'prompt' => 'Photograph of autonomous electric material handling vehicles operating in modern automated warehouse. Details include sensor arrays on vehicles, no human operators, coordinated movement in lanes. Futuristic automation shot. Background shows fully automated facility. Automated facility lighting. Shot on Phase One with 28mm lens.',
                'style' => 'vibrant',
                'contrast' => 4,
            ],
            70 => [
                'prompt' => 'Photograph of market-leading pallet jack brands arranged in dealer showroom. Details include premium build quality visible, distinctive brand colors, heavy-duty construction. Multi-brand display. Background shows professional equipment dealer. Showroom display lighting. Shot on Hasselblad with 80mm lens.',
                'style' => 'cinematic',
                'contrast' => 3.5,
            ],
            71 => [
                'prompt' => 'Photograph of worker demonstrating safe manual pallet jack operation with proper body mechanics and PPE. Details include correct posture with straight back, safety footwear, high-visibility vest, clear travel path. Safety demonstration. Background shows well-organized warehouse. Training area lighting. Shot on Nikon Z9 with 35mm lens.',
                'style' => 'stock_photo',
                'contrast' => 3.5,
            ],
            74 => [
                'prompt' => 'Photograph of scale pallet jack weighing cargo in shipping area with operator checking integrated display. Details include weight shown on built-in scale, proper fork positioning under pallet, shipping dock environment. Benefit demonstration shot. Background shows shipping and receiving area. Operational lighting. Shot on Canon EOS R5 with 35mm lens.',
                'style' => 'dynamic',
                'contrast' => 3.5,
            ],
            76 => [
                'prompt' => 'Photograph of forklift rental inspection with customer and rental agent examining equipment condition together. Details include checking tire wear, testing controls, inspecting overall condition. Rental process shot. Background shows rental yard. Clear lighting. Shot on Sony A7R IV with 35mm lens.',
                'style' => 'neutral',
                'contrast' => 3,
            ],
            78 => [
                'prompt' => 'Photograph of autonomous guided vehicles operating in modern smart warehouse facility. Details include AGVs with sensors moving pallets, automated racking systems, coordinated logistics operation. Smart warehouse showcase. Background shows high-tech automated facility. Modern facility lighting. Shot on Leica SL2 with 35mm lens.',
                'style' => 'stock_photo',
                'contrast' => 3.5,
            ],
            79 => [
                'prompt' => 'Photograph of efficient pallet jack workflow with operator moving multiple pallets in high-volume warehouse. Details include ergonomic operation technique, optimized route through aisles, smooth handling visible. Efficiency showcase shot. Background shows high-volume warehouse operation. Active operation lighting. Shot on Canon EOS R5 with 24mm lens.',
                'style' => 'dynamic',
                'contrast' => 3.5,
            ],
            81 => [
                'prompt' => 'Photograph of stacker reaching top racking level in warehouse with limited floor space. Details include extended mast at maximum height, compact footprint, vertical storage optimization. Vertical reach demonstration. Background shows space-constrained facility with high racking. Vertical emphasis lighting. Shot on Nikon Z9 with 16mm wide angle.',
                'style' => 'hdr',
                'contrast' => 3.5,
            ],
            82 => [
                'prompt' => 'Photograph of LPG forklift operating in mixed indoor-outdoor facility with propane tank visible. Details include propane system on rear, indoor operation capability, outdoor loading dock work. Versatility shot. Background shows mixed-use facility. Natural and artificial lighting mix. Shot on Sony A7R IV with 35mm lens.',
                'style' => 'stock_photo',
                'contrast' => 3.5,
            ],
            84 => [
                'prompt' => 'Photograph of quality manual pallet jacks displayed showing durable construction and smooth operation. Details include heavy-duty forks, quality wheels, ergonomic handles, sturdy frame construction. Product showcase. Background shows practical use environment. Product highlight lighting. Shot on Canon 5D Mark IV with 50mm lens.',
                'style' => 'neutral',
                'contrast' => 3.5,
            ],
            87 => [
                'prompt' => 'Photograph of order picker in operation reaching high shelving while operator works from elevated platform. Details include safety harness worn, organized picking environment, multiple levels accessible. Operations view. Background shows multi-level picking operation. Operational work lighting. Shot on Canon EOS R5 with 35mm lens.',
                'style' => 'stock_photo',
                'contrast' => 3.5,
            ],
            88 => [
                'prompt' => 'Photograph of worker performing manual pallet jack maintenance with basic tools. Details include wheel lubrication being applied, hydraulic pump inspection, handle adjustment in progress. DIY maintenance demonstration. Background shows maintenance area. Clear task lighting. Shot on Nikon Z9 with 50mm lens.',
                'style' => 'neutral',
                'contrast' => 3,
            ],
            90 => [
                'prompt' => 'Photograph of electric forklift in rental fleet alongside identical owned unit in warehouse facility. Details include rental unit with rental company colors, owned unit in standard yellow, both operational. Fleet comparison. Background shows dealer or warehouse facility. Professional lighting. Shot on Sony A7R IV with 35mm lens.',
                'style' => 'stock_photo',
                'contrast' => 3.5,
            ],
            92 => [
                'prompt' => 'Photograph of recommended stacker models displayed showing various lift heights and capacities. Details include walk-behind model, rider model, reach stacker arranged by capability. Recommendation display. Background shows dealer environment. Product showcase lighting. Shot on Phase One with 55mm lens.',
                'style' => 'cinematic',
                'contrast' => 3.5,
            ],
            93 => [
                'prompt' => 'Photograph of new electric pallet truck next to certified pre-owned unit on dealer floor. Details include new unit pristine condition, pre-owned unit well-maintained, both operational ready. Value comparison shot. Background shows dealer floor. Even lighting. Shot on Canon EOS R5 with 50mm lens.',
                'style' => 'neutral',
                'contrast' => 3,
            ],
            96 => [
                'prompt' => 'Photograph of pallet jack demonstrating basic components and operation in warehouse setting. Details include forks, wheels, hydraulic pump, ergonomic handle all visible, operator showing proper grip. Educational overview shot. Background shows various use cases. Clear instructional lighting. Shot on Nikon Z9 with 35mm lens.',
                'style' => 'stock_photo',
                'contrast' => 3.5,
            ],
            98 => [
                'prompt' => 'Photograph of rental stacker fleet in active warehouse operation showing flexible capacity management. Details include multiple rental units working simultaneously, efficient operation, temporary workforce handling equipment. Rental benefit demonstration. Background shows active rental operation. Working environment lighting. Shot on Sony A7R IV with 24mm lens.',
                'style' => 'dynamic',
                'contrast' => 3.5,
            ],
            100 => [
                'prompt' => 'Photograph of leading electric forklift brands showcased in premium dealer showroom. Details include top-tier models from major manufacturers, premium build quality visible, advanced features shown. Premium brand showcase. Background shows upscale dealer showroom. Luxury product lighting. Shot on Hasselblad with 80mm lens.',
                'style' => 'cinematic',
                'contrast' => 4,
            ],
            102 => [
                'prompt' => 'Photograph of well-maintained rental forklift fleet from top brands in professional rental facility. Details include fleet condition excellent, various brands represented, rental-ready equipment. Rental brand showcase. Background shows professional rental operation. Clear fleet lighting. Shot on Canon EOS R5 with 35mm lens.',
                'style' => 'stock_photo',
                'contrast' => 3.5,
            ],
            103 => [
                'prompt' => 'Photograph of manual stackers in different sizes and capacities displayed in dealer warehouse. Details include compact entry-level model, mid-range model, heavy-duty model arranged by size. Model range display. Background shows dealer warehouse. Evaluation lighting. Shot on Nikon Z9 with 50mm lens.',
                'style' => 'neutral',
                'contrast' => 3,
            ],
            105 => [
                'prompt' => 'Photograph of order pickers in different configurations displayed in dealer showroom for buyer consideration. Details include standard model, high-reach model, heavy-duty model options. Selection display. Background shows dealer showroom. Professional lighting. Shot on Leica SL2 with 35mm lens.',
                'style' => 'stock_photo',
                'contrast' => 3.5,
            ],
            107 => [
                'prompt' => 'Photograph of pallet truck rental fleet in professional rental facility with various models available. Details include different sizes and types available, well-maintained condition, rental-ready equipment. Rental fleet display. Background shows rental facility. Clear lighting. Shot on Sony A7R IV with 35mm lens.',
                'style' => 'neutral',
                'contrast' => 3,
            ],
            108 => [
                'prompt' => 'Photograph of electric and manual pallet trucks operating in parallel in warehouse aisles. Details include electric unit gliding smoothly, manual unit requiring push effort, operator body positions different. Direct comparison shot. Background shows typical warehouse aisles. Working condition lighting. Shot on Canon EOS R5 with 24mm lens.',
                'style' => 'dynamic',
                'contrast' => 3.5,
            ],
            109 => [
                'prompt' => 'Photograph of customer evaluating rental pallet truck options in rental facility. Details include customer testing handle comfort, checking wheel condition, examining equipment closely. Selection process. Background shows rental facility. Evaluation lighting. Shot on Nikon Z9 with 35mm lens.',
                'style' => 'stock_photo',
                'contrast' => 3.5,
            ],
            111 => [
                'prompt' => 'Photograph of forklift dealership showing new units, used units, and rental options available. Details include new forklifts in showroom, used units in lot, rental fleet visible. Comprehensive display. Background shows dealer facility. Professional lighting. Shot on Sony A7R IV with 35mm lens.',
                'style' => 'neutral',
                'contrast' => 3.5,
            ],
            112 => [
                'prompt' => 'Photograph of electric forklift operating in food processing facility and retail warehouse environments. Details include clean operation in sensitive areas, indoor suitability, quiet performance visible. Multi-application showcase. Background shows diverse use environments. Application-specific lighting. Shot on Canon EOS R5 with 35mm lens.',
                'style' => 'stock_photo',
                'contrast' => 3.5,
            ],
            114 => [
                'prompt' => 'Photograph of diesel forklifts from top manufacturers arranged in outdoor industrial yard. Details include heavy-duty construction, powerful engines, robust tires for outdoor use. Outdoor model display. Background shows industrial yard or dock. Natural outdoor lighting. Shot on Phase One with 45mm lens.',
                'style' => 'hdr',
                'contrast' => 4,
            ],
            115 => [
                'prompt' => 'Photograph of manual forklift and autonomous guided vehicle working in adjacent zones of modern warehouse. Details include traditional operator-driven unit, AGV with sensors, both performing similar tasks. Comparison demonstration. Background shows modern transitioning facility. Mixed lighting. Shot on Sony A7R IV with 24mm lens.',
                'style' => 'dynamic',
                'contrast' => 3.5,
            ],
            122 => [
                'prompt' => 'Photograph of top stacker brands displayed together with various configurations visible. Details include brand colors distinct, model ranges shown, different mast heights. Brand showcase display. Background shows comprehensive dealer display. Professional showroom lighting. Shot on Canon EOS R5 with 50mm lens.',
                'style' => 'stock_photo',
                'contrast' => 3.5,
            ],
            123 => [
                'prompt' => 'Photograph of buyer carefully evaluating pallet truck in dealer environment. Details include checking wheel quality, testing handle ergonomics, examining fork condition. Systematic selection shot. Background shows evaluation environment. Clear assessment lighting. Shot on Nikon Z9 with 35mm lens.',
                'style' => 'neutral',
                'contrast' => 3,
            ],
            125 => [
                'prompt' => 'Photograph of new stacker alongside certified pre-owned model on dealer floor. Details include pristine new unit, well-maintained pre-owned unit, both operational. Purchase comparison. Background shows dealer floor. Even lighting. Shot on Sony A7R IV with 50mm lens.',
                'style' => 'stock_photo',
                'contrast' => 3.5,
            ],
            127 => [
                'prompt' => 'Photograph of stacker in rental fleet and owned stacker in company warehouse showing both options. Details include rental unit with rental markings, owned unit in company colors. Options display. Background shows planning environment. Professional lighting. Shot on Leica SL2 with 35mm lens.',
                'style' => 'neutral',
                'contrast' => 3,
            ],
            129 => [
                'prompt' => 'Photograph of technician demonstrating critical forklift maintenance points on equipment. Details include checking hydraulic fluid levels, inspecting brake system, examining lift chains. Importance demonstration. Background shows maintenance facility. Clear instructional lighting. Shot on Canon EOS R5 with 35mm lens.',
                'style' => 'stock_photo',
                'contrast' => 3.5,
            ],
            130 => [
                'prompt' => 'Photograph of electric pallet truck moving at efficient speed in demanding warehouse operation. Details include smooth operation, battery indicator visible, high productivity movement. Performance demonstration. Background shows high-volume operation. Active working lighting. Shot on Sony A7R IV with 35mm lens.',
                'style' => 'dynamic',
                'contrast' => 3.5,
            ],
            131 => [
                'prompt' => 'Photograph of different stacker models displayed showing walkie, rider, and reach configurations. Details include walk-behind stacker, rider stacker, reach stacker arranged by type. Model selection display. Background shows comprehensive display. Product showcase lighting. Shot on Nikon Z9 with 35mm lens.',
                'style' => 'neutral',
                'contrast' => 3.5,
            ],
            133 => [
                'prompt' => 'Photograph of rental expert showing customer important inspection points on rental forklift. Details include pointing to tire condition, demonstrating control operation, checking safety features. Expert guidance shot. Background shows rental facility. Professional lighting. Shot on Canon 5D Mark IV with 35mm lens.',
                'style' => 'stock_photo',
                'contrast' => 3.5,
            ],
            134 => [
                'prompt' => 'Photograph of manual and electric pallet trucks positioned for selection in dealer showroom. Details include effort difference demonstrated by operator stance, application suitability shown. Selection display shot. Background shows varied application environments. Balanced lighting. Shot on Sony A7R IV with 24mm lens.',
                'style' => 'neutral',
                'contrast' => 3,
            ],
            135 => [
                'prompt' => 'Photograph of technician performing order picker maintenance focusing on critical safety systems. Details include checking safety sensors, inspecting platform controls, examining lift mechanism. Maintenance guidance shot. Background shows service area. Clear maintenance lighting. Shot on Canon EOS R5 with 50mm lens.',
                'style' => 'stock_photo',
                'contrast' => 3.5,
            ],
            137 => [
                'prompt' => 'Photograph of electric and manual scale pallet jacks in weighing operation showing both types. Details include battery-powered unit operation, manual unit operation, both weighing similar loads. Technical comparison shot. Background shows weighing and shipping area. Precise lighting. Shot on Nikon Z9 with 50mm lens.',
                'style' => 'neutral',
                'contrast' => 3,
            ],
        ];
    }

    public function handle()
    {
        // Tenant 2'yi başlat
        tenancy()->initialize(Tenant::find(2));

        $this->apiKey = config('services.leonardo.api_key', '');

        if (empty($this->apiKey)) {
            $this->error('Leonardo AI API key not configured!');
            return 1;
        }

        // Görseli olmayan blogları al
        $query = Blog::whereDoesntHave('media', function ($q) {
            $q->where('collection_name', 'featured_image');
        })
        ->where('is_active', true)
        ->orderBy('blog_id', 'asc');

        // Tek blog ID belirtilmişse
        if ($blogId = $this->option('blog-id')) {
            $query = Blog::where('blog_id', $blogId);
        }

        $limit = (int) $this->option('limit');
        $blogs = $query->limit($limit)->get();

        if ($blogs->isEmpty()) {
            $this->info('İşlenecek blog bulunamadı.');
            return 0;
        }

        $this->info("🎨 {$blogs->count()} blog için görsel üretimi başlıyor...\n");

        $customPrompts = $this->getCustomPrompts();
        $successCount = 0;
        $failCount = 0;

        foreach ($blogs as $index => $blog) {
            $blogTitle = $blog->getTranslated('title', 'tr');
            $blogId = $blog->blog_id;

            $this->info(($index + 1) . ". Blog #{$blogId}: {$blogTitle}");

            // Bu blog için özel prompt al
            if (isset($customPrompts[$blogId])) {
                $promptData = $customPrompts[$blogId];
                $this->line("   ✅ Özel prompt kullanılıyor");
            } else {
                // Dinamik prompt oluştur
                $promptData = $this->buildDynamicPrompt($blogTitle);
                $this->line("   ⚠️ Dinamik prompt oluşturuldu");
            }

            $this->line("   📝 Prompt: " . substr($promptData['prompt'], 0, 80) . "...");
            $this->line("   🎨 Style: {$promptData['style']}, Contrast: {$promptData['contrast']}");

            if ($this->option('dry-run')) {
                $this->info("   ✅ DRY-RUN: Prompt hazır\n");
                continue;
            }

            try {
                // Görsel üret
                $result = $this->generateImage($promptData);

                if ($result) {
                    // Blog'a ekle
                    $this->attachImageToBlog($blog, $result, $promptData);
                    $successCount++;
                    $this->info("   ✅ Görsel oluşturuldu: " . round(strlen($result['content']) / 1024) . " KB\n");
                } else {
                    $failCount++;
                    $this->error("   ❌ Görsel üretilemedi\n");
                }
            } catch (\Exception $e) {
                $failCount++;
                $this->error("   ❌ Hata: {$e->getMessage()}\n");
            }

            // Rate limiting
            if ($index < $blogs->count() - 1) {
                sleep(5);
            }
        }

        $this->info("\n=== TAMAMLANDI ===");
        $this->info("✅ Başarılı: {$successCount}");
        $this->info("❌ Başarısız: {$failCount}");

        return 0;
    }

    /**
     * Dinamik prompt oluştur (özel prompt yoksa)
     */
    protected function buildDynamicPrompt(string $title): array
    {
        // Basit dinamik prompt - LeonardoAIService mantığı
        $equipment = $this->detectEquipment($title);

        $subjects = [
            "a {$equipment} being operated in warehouse",
            "a {$equipment} lifting pallets",
            "workers using a {$equipment}",
            "a {$equipment} in loading area",
        ];

        $lightings = [
            "industrial lighting",
            "natural daylight from skylights",
            "warehouse LED lighting",
        ];

        $cameras = ["shot on Canon EOS R5", "shot on Sony A7R IV", "shot on Nikon Z9"];
        $lenses = ["with 35mm lens", "with 50mm lens", "with 24mm wide angle"];

        $prompt = sprintf(
            "Photograph of %s. %s. %s %s. Professional industrial photography style.",
            $subjects[array_rand($subjects)],
            $lightings[array_rand($lightings)],
            $cameras[array_rand($cameras)],
            $lenses[array_rand($lenses)]
        );

        $styles = ['stock_photo', 'dynamic', 'neutral', 'cinematic'];

        return [
            'prompt' => $prompt,
            'style' => $styles[array_rand($styles)],
            'contrast' => [3, 3.5, 4][array_rand([3, 3.5, 4])],
        ];
    }

    /**
     * Ekipman tipi tespit
     */
    protected function detectEquipment(string $title): string
    {
        $titleLower = mb_strtolower($title);

        if (preg_match('/forklift/ui', $titleLower)) {
            return ['forklift', 'electric forklift', 'diesel forklift'][array_rand([0,1,2])];
        }
        if (preg_match('/transpalet|palet/ui', $titleLower)) {
            return ['pallet jack', 'electric pallet truck'][array_rand([0,1])];
        }
        if (preg_match('/istif|stacker/ui', $titleLower)) {
            return ['stacker', 'electric stacker'][array_rand([0,1])];
        }
        if (preg_match('/order\s*picker/ui', $titleLower)) {
            return 'order picker';
        }
        if (preg_match('/otonom|agv|amr/ui', $titleLower)) {
            return 'autonomous guided vehicle';
        }

        return 'warehouse equipment';
    }

    /**
     * Leonardo AI ile görsel üret
     */
    protected function generateImage(array $promptData): ?array
    {
        // Gerçekçilik kısıtlaması + yazı yasağı ekle (ULTRA GÜÇLÜ)
        $realismConstraint = " CRITICAL ABSOLUTE REQUIREMENTS: 1) Realistic industrial equipment ONLY - real-world designs from Toyota, Linde, Jungheinrich, Crown, Yale. Standard colors: yellow, orange, red, blue, gray. NO futuristic/sci-fi/conceptual designs. 2) ABSOLUTELY ZERO TEXT, LETTERS, NUMBERS, OR SYMBOLS ANYWHERE IN THE IMAGE - no text on equipment, no text on signs, no text on floor, no text on walls, no text on screens, no text on labels, no comparison charts, no graphs with text, no infographics. The image must be 100% text-free. 3) NO comparison charts, tables, graphs, diagrams, or any visual with data/numbers.";

        $finalPrompt = $promptData['prompt'] . $realismConstraint;

        // ULTRA AGGRESSIVE Negative prompt
        $negativePrompt = "text, letters, words, numbers, digits, brand names, logos, labels, signs, watermarks, typography, writing, captions, subtitles, titles, stamps, badges, stickers, name plates, serial numbers, model numbers, any written content, illegible text, garbled text, distorted letters, comparison chart, comparison table, comparison graphic, infographic, data visualization, graph, pie chart, bar chart, spreadsheet, checklist, bullet points, price tags, specifications text, technical text, measurement text, warning text, instruction text, any form of alphanumeric characters";

        // Generation başlat
        $response = Http::withHeaders([
            'accept' => 'application/json',
            'content-type' => 'application/json',
            'authorization' => 'Bearer ' . $this->apiKey,
        ])->timeout(30)->post($this->baseUrl . '/generations', [
            'modelId' => $this->defaultModel,
            'prompt' => $finalPrompt,
            'negative_prompt' => $negativePrompt,
            'styleUUID' => $this->styleUUIDs[$promptData['style']] ?? $this->styleUUIDs['stock_photo'],
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

        $generationId = $response->json()['sdGenerationJob']['generationId'] ?? null;

        if (!$generationId) {
            return null;
        }

        $this->line("   ⏳ Generation ID: {$generationId}");

        // Bekle
        $imageUrl = null;
        for ($i = 0; $i < 30; $i++) {
            sleep(3);

            $statusResponse = Http::withHeaders([
                'accept' => 'application/json',
                'authorization' => 'Bearer ' . $this->apiKey,
            ])->timeout(30)->get($this->baseUrl . '/generations/' . $generationId);

            if (!$statusResponse->successful()) {
                continue;
            }

            $generation = $statusResponse->json()['generations_by_pk'] ?? null;

            if (!$generation) {
                continue;
            }

            $status = $generation['status'] ?? '';

            if ($status === 'COMPLETE') {
                $images = $generation['generated_images'] ?? [];
                if (!empty($images)) {
                    $imageUrl = $images[0]['url'];
                    break;
                }
            } elseif ($status === 'FAILED') {
                return null;
            }
        }

        if (!$imageUrl) {
            return null;
        }

        // İndir
        $imageData = Http::timeout(60)->get($imageUrl)->body();

        if (!$imageData) {
            return null;
        }

        return [
            'content' => $imageData,
            'url' => $imageUrl,
            'generation_id' => $generationId,
        ];
    }

    /**
     * Görseli blog'a ekle
     */
    protected function attachImageToBlog(Blog $blog, array $result, array $promptData): void
    {
        // Geçici dosyaya kaydet
        $tempPath = sys_get_temp_dir() . '/' . uniqid('leonardo_') . '.jpg';
        file_put_contents($tempPath, $result['content']);

        // Tenant disk yapılandır
        $tenantId = tenant('id');
        $diskRoot = base_path("storage/tenant{$tenantId}/app/public");
        if (!is_dir($diskRoot)) {
            @mkdir($diskRoot, 0775, true);
        }

        // 🔥 FIX: Tenant domain kullan (ixtif.com gibi)
        $tenantDomain = null;
        if (tenant() && tenant()->domains) {
            $domain = tenant()->domains->first();
            if ($domain) {
                $tenantDomain = 'https://' . $domain->domain;
            }
        }
        $appUrl = $tenantDomain ?? config('app.url');

        config([
            'filesystems.disks.tenant' => [
                'driver' => 'local',
                'root' => $diskRoot,
                'url' => "{$appUrl}/storage/tenant{$tenantId}",
                'visibility' => 'public',
                'throw' => false,
            ],
        ]);

        // Blog'a ekle
        $blogTitle = $blog->getTranslated('title', 'tr');

        $media = $blog->addMedia($tempPath)
            ->usingFileName(uniqid('leonardo_') . '.jpg')
            ->toMediaCollection('featured_image', 'tenant');

        // Meta verileri ekle
        $media->setCustomProperty('provider', 'leonardo');
        $media->setCustomProperty('generation_id', $result['generation_id']);
        $media->setCustomProperty('prompt', $promptData['prompt']);
        $media->setCustomProperty('style', $promptData['style']);
        $media->setCustomProperty('alt_text', ['tr' => $blogTitle]);
        $media->setCustomProperty('title', ['tr' => $blogTitle . ' - Ana Görsel']);
        $media->setCustomProperty('seo_optimized', true);
        $media->setCustomProperty('og_image', true);
        $media->save();

        // Temizle
        if (file_exists($tempPath)) {
            @unlink($tempPath);
        }

        Log::info('🎨 Blog image attached', [
            'blog_id' => $blog->blog_id,
            'media_id' => $media->id,
            'generation_id' => $result['generation_id'],
        ]);
    }
}

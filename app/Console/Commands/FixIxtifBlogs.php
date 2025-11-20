<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;
use Modules\Blog\App\Models\Blog;
use Modules\AI\App\Services\OpenAIService;
use App\Services\Media\MediaManager;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class FixIxtifBlogs extends Command
{
    protected $signature = 'fix:ixtif-blogs
                            {--content : Sadece kısa içerikleri düzelt}
                            {--images : Sadece görselleri ekle}
                            {--blog= : Belirli bir blog ID}
                            {--dry-run : Test modu (değişiklik yapmaz)}';

    protected $description = 'İxtif.com (Tenant 2) bloglarını düzelt: kısa içerikleri yeniden yaz, eksik görselleri ekle';

    protected OpenAIService $openaiService;
    protected MediaManager $mediaManager;

    // Blog başlığına göre dinamik prompt için keyword mapping
    protected array $keywordMapping = [
        'transpalet' => 'pallet jack warehouse logistics',
        'forklift' => 'forklift warehouse industrial',
        'istif' => 'stacker machine warehouse storage',
        'depo' => 'warehouse storage facility interior',
        'lojistik' => 'logistics center distribution',
        'kiralama' => 'equipment rental industrial warehouse',
        'bakım' => 'maintenance service workshop equipment',
        'güvenlik' => 'workplace safety equipment industrial',
        'elektrikli' => 'electric powered warehouse equipment',
        'manuel' => 'manual operation warehouse equipment',
        'dizel' => 'diesel powered forklift industrial',
        'lpg' => 'lpg gas powered forklift warehouse',
        'order picker' => 'order picker vertical warehouse',
        'terazili' => 'scale weighing pallet jack industrial',
        'kantarlı' => 'weighing scale warehouse equipment',
        'otonom' => 'autonomous robotic warehouse system',
        'fiyat' => 'industrial equipment dealer showroom',
        'marka' => 'brand equipment warehouse professional',
        'model' => 'equipment models warehouse lineup',
        'avantaj' => 'efficient warehouse operations',
        'seçim' => 'equipment selection warehouse',
        'kullanım' => 'equipment operation industrial',
    ];

    // Fallback genel depo görselleri
    protected array $fallbackPrompts = [
        'modern warehouse interior storage',
        'logistics center operations professional',
        'industrial workplace environment',
    ];

    public function handle()
    {
        $this->info('🔧 İxtif.com Blog Düzeltme İşlemi Başlatılıyor...');
        $this->newLine();

        // Tenant 2'ye geç
        $tenant = Tenant::find(2);
        if (!$tenant) {
            $this->error('❌ Tenant 2 bulunamadı!');
            return 1;
        }

        tenancy()->initialize($tenant);
        $this->info('✅ Tenant 2 (ixtif.com) aktif edildi');

        // Servisleri başlat
        $this->openaiService = new OpenAIService();
        $this->mediaManager = app(MediaManager::class);

        // Belirli bir blog mu?
        $blogId = $this->option('blog');

        if ($blogId) {
            $blog = Blog::find($blogId);
            if (!$blog) {
                $this->error("❌ Blog ID {$blogId} bulunamadı!");
                return 1;
            }
            $blogs = collect([$blog]);
        } else {
            $blogs = Blog::with('media')->orderBy('created_at', 'desc')->get();
        }

        $this->info("📊 Toplam Blog: {$blogs->count()}");
        $this->newLine();

        // Her ikisini de yap (default) veya seçili olanı
        $doContent = !$this->option('images') || $this->option('content');
        $doImages = !$this->option('content') || $this->option('images');

        // 1. Kısa içerikleri düzelt
        if ($doContent) {
            $this->fixShortContent($blogs);
        }

        // 2. Görselleri ekle
        if ($doImages) {
            $this->addMissingImages($blogs);
        }

        $this->newLine();
        $this->info('🎉 İşlem tamamlandı!');

        return 0;
    }

    /**
     * Kısa içerikli blogları düzelt
     */
    protected function fixShortContent($blogs)
    {
        $this->info('📝 Kısa içerikli bloglar kontrol ediliyor...');

        $shortBlogs = $blogs->filter(function ($blog) {
            $body = $blog->body['tr'] ?? '';
            return strlen($body) < 1000;
        });

        if ($shortBlogs->isEmpty()) {
            $this->info('✅ Kısa içerikli blog bulunamadı');
            return;
        }

        $this->warn("⚠️  {$shortBlogs->count()} kısa içerikli blog bulundu");

        $bar = $this->output->createProgressBar($shortBlogs->count());
        $bar->start();

        foreach ($shortBlogs as $blog) {
            $title = $blog->title['tr'] ?? 'Başlıksız';

            if ($this->option('dry-run')) {
                $this->newLine();
                $this->line("  [DRY-RUN] Blog ID {$blog->id}: {$title}");
                $bar->advance();
                continue;
            }

            try {
                $this->regenerateBlogContent($blog);
                Log::info('Blog content regenerated', [
                    'blog_id' => $blog->id,
                    'title' => $title,
                ]);
            } catch (\Exception $e) {
                $this->newLine();
                $this->error("  ❌ Blog ID {$blog->id} hata: {$e->getMessage()}");
                Log::error('Blog content regeneration failed', [
                    'blog_id' => $blog->id,
                    'error' => $e->getMessage(),
                ]);
            }

            $bar->advance();
            sleep(2); // Rate limit için
        }

        $bar->finish();
        $this->newLine();
    }

    /**
     * Blog içeriğini AI ile yeniden oluştur
     */
    protected function regenerateBlogContent(Blog $blog)
    {
        $title = $blog->title['tr'] ?? 'Başlıksız';
        $excerpt = $blog->excerpt['tr'] ?? '';

        // System prompt
        $systemPrompt = "Sen profesyonel bir endüstriyel ekipman blog yazarısın.
Forklift, transpalet, istif makinesi, depo ekipmanları konularında uzman yazılar yazıyorsun.

KURALLAR:
1. Minimum 2000 kelime yaz
2. SEO uyumlu H2 ve H3 başlıkları kullan
3. Teknik detaylar, örnekler, sayısal veriler ekle
4. Profesyonel ama anlaşılır bir dil kullan
5. 'Giriş' ve 'Sonuç' başlıklarını KULLANMA (doğal başlıklar kullan)
6. HTML formatında yaz (sadece h2, h3, p, ul, li, table tagları)

Firma adı: İxtif
Bu firma adını içerikte 3 kez kullan.";

        // Content prompt
        $contentPrompt = "'{$title}' konusunda detaylı bir blog yazısı yaz.

Excerpt: {$excerpt}

HTML formatında 2000+ kelime içerik üret. Başlıklar H2 ve H3 olsun.";

        $content = $this->openaiService->ask($contentPrompt, false, [
            'custom_prompt' => $systemPrompt,
            'temperature' => 0.7,
            'max_tokens' => 8000,
            'model' => 'gpt-4o-mini',
        ]);

        // FAQ üret
        $faqPrompt = "'{$title}' konusunda 10 sık sorulan soru ve cevapları oluştur.
Her cevap 50-80 kelime olsun. JSON array döndür:
[{\"question\": {\"tr\": \"Soru?\"}, \"answer\": {\"tr\": \"Cevap...\"}, \"icon\": \"fas fa-question-circle\"}]";

        $faqResponse = $this->openaiService->ask($faqPrompt, false, [
            'temperature' => 0.7,
            'max_tokens' => 3000,
            'model' => 'gpt-4o-mini',
        ]);

        // FAQ parse
        $faqData = $this->parseJsonResponse($faqResponse);

        // HowTo üret
        $howtoPrompt = "'{$title}' için 7 adımlı 'Nasıl Yapılır' rehberi oluştur.
Her adım 80-100 kelime olsun. JSON döndür:
{\"name\": {\"tr\": \"Başlık\"}, \"description\": {\"tr\": \"Açıklama\"}, \"steps\": [{\"name\": {\"tr\": \"Adım\"}, \"text\": {\"tr\": \"Detay\"}, \"icon\": \"fas fa-check-circle\"}]}";

        $howtoResponse = $this->openaiService->ask($howtoPrompt, false, [
            'temperature' => 0.7,
            'max_tokens' => 3000,
            'model' => 'gpt-4o-mini',
        ]);

        // HowTo parse
        $howtoData = $this->parseJsonResponse($howtoResponse, true);

        // Content temizle
        $cleanContent = $this->cleanHtmlContent($content);

        // Blog güncelle
        DB::beginTransaction();
        try {
            $blog->update([
                'body' => ['tr' => $cleanContent],
                'faq_data' => $faqData,
                'howto_data' => $howtoData,
            ]);

            // Excerpt boşsa güncelle
            if (empty($excerpt)) {
                $newExcerpt = mb_substr(strip_tags($cleanContent), 0, 200);
                $blog->update([
                    'excerpt' => ['tr' => $newExcerpt],
                ]);
            }

            DB::commit();

            $wordCount = str_word_count(strip_tags($cleanContent));
            $this->newLine();
            $this->info("  ✅ Blog ID {$blog->id} güncellendi ({$wordCount} kelime)");

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Eksik görselleri ekle
     */
    protected function addMissingImages($blogs)
    {
        $this->newLine();
        $this->info('🖼️  Eksik görseller kontrol ediliyor...');

        $blogsWithoutImages = $blogs->filter(function ($blog) {
            return $blog->getFirstMedia() === null;
        });

        if ($blogsWithoutImages->isEmpty()) {
            $this->info('✅ Görselsiz blog bulunamadı');
            return;
        }

        $this->warn("⚠️  {$blogsWithoutImages->count()} görselsiz blog bulundu");

        $bar = $this->output->createProgressBar($blogsWithoutImages->count());
        $bar->start();

        $promptIndex = 0;

        foreach ($blogsWithoutImages as $blog) {
            $title = $blog->title['tr'] ?? 'Başlıksız';

            if ($this->option('dry-run')) {
                $this->newLine();
                $this->line("  [DRY-RUN] Görsel eklenecek - Blog ID {$blog->id}: {$title}");
                $bar->advance();
                continue;
            }

            try {
                // Blog başlığına göre dinamik prompt oluştur
                $basePrompt = $this->buildPromptFromTitle($title);

                // Görsel ekle
                $media = $this->mediaManager->fetchAndAttach(
                    $blog,
                    $basePrompt,
                    'featured_image',
                    [
                        'orientation' => 'landscape',
                        'locale' => 'tr',
                        'metadata' => [
                            'context' => 'blog',
                            'tenant_id' => 2,
                        ],
                    ]
                );

                if ($media) {
                    // SEO meta ekle
                    $media->setCustomProperty('alt_text', ['tr' => $title]);
                    $media->setCustomProperty('title', ['tr' => $title . ' - Ana Görsel']);
                    $media->setCustomProperty('seo_optimized', true);
                    $media->save();

                    $this->newLine();
                    $this->info("  ✅ Blog ID {$blog->id} görsel eklendi (Provider: {$media->getCustomProperty('provider')})");

                    Log::info('Blog image added', [
                        'blog_id' => $blog->id,
                        'media_id' => $media->id,
                        'provider' => $media->getCustomProperty('provider'),
                        'prompt' => $basePrompt,
                    ]);
                }

            } catch (\Exception $e) {
                $this->newLine();
                $this->error("  ❌ Blog ID {$blog->id} görsel hatası: {$e->getMessage()}");
                Log::error('Blog image add failed', [
                    'blog_id' => $blog->id,
                    'error' => $e->getMessage(),
                ]);
            }

            $bar->advance();
            sleep(1); // Rate limit için
        }

        $bar->finish();
        $this->newLine();
    }

    /**
     * JSON response'u parse et
     */
    protected function parseJsonResponse(string $response, bool $isObject = false)
    {
        // Code block temizle
        $clean = preg_replace('/```json\s*(.*?)\s*```/s', '$1', $response);
        $clean = preg_replace('/```\s*(.*?)\s*```/s', '$1', $clean);

        $decoded = json_decode(trim($clean), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::warning('JSON parse failed', [
                'error' => json_last_error_msg(),
                'response_preview' => substr($response, 0, 200),
            ]);
            return $isObject ? [] : [];
        }

        return $decoded;
    }

    /**
     * HTML içeriği temizle
     */
    protected function cleanHtmlContent(string $content): string
    {
        // Code block wrapper temizle
        $clean = preg_replace('/```html\s*(.*?)\s*```/s', '$1', $content);
        $clean = preg_replace('/```\s*(.*?)\s*```/s', '$1', $clean);

        // Literal \n karakterlerini gerçek newline'a çevir
        $clean = str_replace('\\n', "\n", $clean);

        // Fazla boşlukları temizle
        $clean = preg_replace('/\n{3,}/', "\n\n", $clean);

        // HTML entity decode
        $clean = html_entity_decode($clean, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return trim($clean);
    }

    /**
     * Blog başlığından AI ile görsel promptu oluştur
     */
    protected function buildPromptFromTitle(string $title): string
    {
        try {
            // OpenAI ile uygun görsel arama terimi oluştur
            $prompt = "Aşağıdaki Türkçe blog başlığı için en uygun İngilizce stok fotoğraf arama terimlerini oluştur.

Blog Başlığı: {$title}

KURALLAR:
1. Sadece 3-5 kelimelik İngilizce arama terimi yaz
2. Depo, lojistik, endüstriyel ortam görselleri olsun
3. Forklift, pallet jack, stacker gibi ekipman terimlerini kullan
4. Profesyonel, yüksek kaliteli görsel için uygun terimler seç

Örnek çıktılar:
- 'forklift warehouse loading dock'
- 'pallet jack industrial logistics'
- 'warehouse stacker equipment'

Sadece arama terimini yaz, başka bir şey yazma:";

            $response = $this->openaiService->ask($prompt, false, [
                'temperature' => 0.3,
                'max_tokens' => 50,
                'model' => 'gpt-4o-mini',
            ]);

            $searchTerm = trim($response);

            // Çok uzunsa kısalt
            $words = explode(' ', $searchTerm);
            if (count($words) > 6) {
                $searchTerm = implode(' ', array_slice($words, 0, 5));
            }

            Log::info('AI generated image prompt', [
                'title' => $title,
                'prompt' => $searchTerm,
            ]);

            return $searchTerm;

        } catch (\Exception $e) {
            Log::warning('AI prompt generation failed, using fallback', [
                'title' => $title,
                'error' => $e->getMessage(),
            ]);

            // Fallback: keyword mapping kullan
            $titleLower = mb_strtolower($title);
            foreach ($this->keywordMapping as $keyword => $prompt) {
                if (mb_strpos($titleLower, $keyword) !== false) {
                    return $prompt;
                }
            }

            return $this->fallbackPrompts[array_rand($this->fallbackPrompts)];
        }
    }
}

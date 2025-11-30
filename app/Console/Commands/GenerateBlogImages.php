<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Media\LeonardoAIService;
use Modules\Blog\App\Models\Blog;
use Illuminate\Support\Facades\DB;

class GenerateBlogImages extends Command
{
    protected $signature = 'leonardo:generate-all-blogs {--limit=2 : Number of blogs to process}';
    protected $description = 'Generate Leonardo AI images for all blogs without media';

    public function handle()
    {
        $limit = $this->option('limit');

        // Mediası olmayan AKTİF blogları al (soft delete kontrolü)
        $blogIds = DB::connection('tenant')->select("
            SELECT b.blog_id
            FROM blogs b
            LEFT JOIN media m ON m.model_type = 'Modules\\\\Blog\\\\App\\\\Models\\\\Blog' AND m.model_id = b.blog_id
            WHERE m.id IS NULL
              AND b.deleted_at IS NULL
              AND b.is_active = 1
            ORDER BY b.blog_id ASC
            LIMIT {$limit}
        ");
        
        if (empty($blogIds)) {
            $this->info("✅ Tüm bloglarda görsel var!");
            return 0;
        }
        
        $total = count($blogIds);
        $this->info("🎨 {$total} blog için görsel oluşturulacak...\n");
        
        $leonardoService = app(LeonardoAIService::class);
        $success = 0;
        $failed = 0;
        
        foreach ($blogIds as $index => $blogData) {
            $blogId = $blogData->blog_id;
            $current = $index + 1;
            
            try {
                // blog_id ile bul
                $blog = Blog::find($blogId);
                if (!$blog) {
                    $this->error("[{$current}/{$total}] Blog {$blogId} bulunamadı!");
                    $failed++;
                    continue;
                }
                
                $titleData = is_string($blog->title) ? json_decode($blog->title, true) : $blog->title;
                $title = $titleData['tr'] ?? 'Başlık yok';
                
                $this->info("[{$current}/{$total}] Blog {$blogId}: {$title}");
                
                // Leonardo AI ile görsel oluştur
                $result = $leonardoService->generateForBlog($title, 'blog');
                
                if (!$result) {
                    $this->error("  ❌ Görsel oluşturulamadı!");
                    $failed++;
                    sleep(3); // Hata durumunda kısa bekle
                    continue;
                }
                
                // Geçici dosyaya kaydet
                $tempPath = '/tmp/leonardo_blog_' . $blogId . '.jpg';
                file_put_contents($tempPath, $result['content']);
                
                // Media olarak ekle
                $media = $blog->addMedia($tempPath)
                    ->usingFileName('leonardo_blog_' . $blogId . '_' . time() . '.jpg')
                    ->toMediaCollection('hero', 'tenant');
                
                @unlink($tempPath);
                
                $this->info("  ✅ Media ID: {$media->id} | " . substr($result['prompt'], 0, 80) . "...");
                $success++;
                
                // Rate limit - her request arası 8 saniye bekle
                if ($current < $total) {
                    $this->info("  ⏳ 8 saniye bekleniyor...\n");
                    sleep(8);
                }
                
            } catch (\Exception $e) {
                $this->error("  ❌ HATA: {$e->getMessage()}");
                $failed++;
                sleep(3);
            }
        }
        
        $this->info("\n🎉 İşlem tamamlandı!");
        $this->info("✅ Başarılı: {$success}");
        $this->info("❌ Başarısız: {$failed}");
        
        return 0;
    }
}

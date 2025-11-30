<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Blog\App\Models\Blog;

class FixMissingBlogImages extends Command
{
    protected $signature = 'leonardo:fix-missing-images';
    protected $description = 'Generate images for blogs without media';

    public function handle()
    {
        // Tenant 2 initialize et (ixtif.com)
        tenancy()->initialize(2);

        $this->info("🏢 Tenant: " . tenant()->id . " (" . tenant()->domains->first()->domain . ")");

        // Tüm aktif blogları al
        $allBlogs = Blog::where('is_active', true)->get();

        // Hero collection'sız olanları filtrele
        $blogsWithoutMedia = $allBlogs->filter(function ($blog) {
            return $blog->getMedia('hero')->isEmpty();
        });

        $this->info("Hero görseli olmayan blog sayısı: " . $blogsWithoutMedia->count());

        if ($blogsWithoutMedia->isEmpty()) {
            $this->info("✅ Tüm blog'larda hero görseli var!");
            return;
        }

        $leonardoService = app(\App\Services\Media\LeonardoAIService::class);
        $success = 0;
        $failed = 0;

        foreach ($blogsWithoutMedia as $index => $blog) {
            $titleData = is_string($blog->title) ? json_decode($blog->title, true) : $blog->title;
            $title = $titleData['tr'] ?? 'Başlık yok';

            $this->info("[" . ($index + 1) . "/" . $blogsWithoutMedia->count() . "] Blog {$blog->blog_id}: {$title}");

            try {
                $result = $leonardoService->generateForBlog($title, 'blog');

                if ($result && isset($result['url'])) {
                    $tempPath = sys_get_temp_dir() . '/leonardo_blog_' . $blog->blog_id . '_' . time() . '.jpg';
                    file_put_contents($tempPath, file_get_contents($result['url']));

                    $media = $blog->addMedia($tempPath)
                        ->usingFileName('leonardo_blog_' . $blog->blog_id . '_' . time() . '.jpg')
                        ->toMediaCollection('hero', 'tenant');

                    $this->info("  ✅ Media ID: {$media->id}");
                    $success++;
                    unlink($tempPath);
                } else {
                    $this->error("  ❌ Görsel oluşturulamadı!");
                    $failed++;
                }
            } catch (\Exception $e) {
                $this->error("  ❌ Hata: " . $e->getMessage());
                $failed++;
            }

            sleep(8); // Rate limit
        }

        $this->info("\n🎉 İşlem tamamlandı!");
        $this->info("✅ Başarılı: {$success}");
        $this->info("❌ Başarısız: {$failed}");
    }
}

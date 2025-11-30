<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Blog\App\Models\Blog;

class CheckBlogSlug extends Command
{
    protected $signature = 'blog:check-slug {slug}';
    protected $description = 'Check blog by slug and generate image if missing';

    public function handle()
    {
        $slug = $this->argument('slug');
        $blog = Blog::where('slug', $slug)->first();

        if ($blog) {
            $this->info("✅ Blog bulundu!");
            $this->info("Blog ID: {$blog->blog_id}");
            $titleData = is_string($blog->title) ? json_decode($blog->title, true) : $blog->title;
            $title = $titleData['tr'] ?? 'Başlık yok';
            $this->info("Başlık: {$title}");

            $mediaCount = $blog->media()->count();
            $this->info("Media sayısı: {$mediaCount}");

            if ($mediaCount === 0) {
                $this->warn("⚠️ Blog'da görsel YOK! Şimdi oluşturacağım...");

                $leonardoService = app(\App\Services\Media\LeonardoAIService::class);
                $result = $leonardoService->generateForBlog($title, 'blog');

                if ($result && isset($result['url'])) {
                    $tempPath = sys_get_temp_dir() . '/leonardo_blog_' . $blog->blog_id . '_' . time() . '.jpg';
                    file_put_contents($tempPath, file_get_contents($result['url']));

                    $media = $blog->addMedia($tempPath)
                        ->usingFileName('leonardo_blog_' . $blog->blog_id . '_' . time() . '.jpg')
                        ->toMediaCollection('hero', 'tenant');

                    $this->info("✅ Görsel oluşturuldu! Media ID: {$media->id}");
                    unlink($tempPath);
                } else {
                    $this->error("❌ Görsel oluşturulamadı!");
                }
            }
        } else {
            $this->error("❌ Blog bulunamadı!");
        }
    }
}

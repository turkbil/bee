<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Blog\App\Models\Blog;

class RegenerateBlogImage extends Command
{
    protected $signature = 'leonardo:regenerate {slug}';
    protected $description = 'Regenerate blog image by slug (replaces existing)';

    public function handle()
    {
        $slug = $this->argument('slug');
        $blog = Blog::where('slug', $slug)->first();

        if (!$blog) {
            $this->error("❌ Blog bulunamadı!");
            return;
        }

        $titleData = is_string($blog->title) ? json_decode($blog->title, true) : $blog->title;
        $title = $titleData['tr'] ?? 'Başlık yok';

        $this->info("Blog ID: {$blog->blog_id}");
        $this->info("Başlık: {$title}");

        // Eski görseli sil
        $blog->clearMediaCollection('hero');
        $this->info("⚠️ Eski görsel silindi");

        // Yeni görsel oluştur
        $leonardoService = app(\App\Services\Media\LeonardoAIService::class);
        $result = $leonardoService->generateForBlog($title, 'blog');

        if ($result && isset($result['url'])) {
            $tempPath = sys_get_temp_dir() . '/leonardo_blog_' . $blog->blog_id . '_' . time() . '.jpg';
            file_put_contents($tempPath, file_get_contents($result['url']));

            $media = $blog->addMedia($tempPath)
                ->usingFileName('leonardo_blog_' . $blog->blog_id . '_' . time() . '.jpg')
                ->toMediaCollection('hero', 'tenant');

            $this->info("✅ Yeni görsel oluşturuldu! Media ID: {$media->id}");

            if (file_exists($tempPath)) {
                unlink($tempPath);
            }
        } else {
            $this->error("❌ Görsel oluşturulamadı!");
        }
    }
}

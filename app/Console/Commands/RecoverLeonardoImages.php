<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Modules\Blog\App\Models\Blog;

class RecoverLeonardoImages extends Command
{
    protected $signature = 'leonardo:recover-images {url_file}';
    protected $description = 'Recover Leonardo images from URL list';

    public function handle()
    {
        $urlFile = $this->argument('url_file');
        
        if (!file_exists($urlFile)) {
            $this->error('URL file not found!');
            return 1;
        }

        $urls = file($urlFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $this->info('Found ' . count($urls) . ' URLs');

        // Get blogs without media, ordered by ID
        $blogs = Blog::whereDoesntHave('media')->orderBy('blog_id', 'asc')->get();
        $this->info('Found ' . $blogs->count() . ' blogs without media');

        $success = 0;
        $failed = 0;

        foreach ($urls as $index => $url) {
            if (!isset($blogs[$index])) {
                $this->warn('No more blogs to assign');
                break;
            }

            $blog = $blogs[$index];
            $this->info("[" . ($index + 1) . "/" . count($urls) . "] Blog {$blog->blog_id}: {$blog->title['tr']}");

            try {
                // Download image
                $response = Http::timeout(60)->get($url);
                
                if (!$response->successful()) {
                    $this->error('  Failed to download');
                    $failed++;
                    continue;
                }

                // Save to temp file
                $tempPath = tempnam(sys_get_temp_dir(), 'leonardo_');
                file_put_contents($tempPath, $response->body());

                // Add to media library
                $media = $blog->addMedia($tempPath)
                    ->usingFileName('leonardo_blog_' . $blog->blog_id . '_' . time() . '.jpg')
                    ->toMediaCollection('hero', 'tenant');

                $this->info('  ✅ Media ID: ' . $media->id);
                $success++;

                // Clean up
                @unlink($tempPath);

                // Rate limit
                sleep(1);

            } catch (\Exception $e) {
                $this->error('  ❌ Error: ' . $e->getMessage());
                $failed++;
            }
        }

        $this->info("\n✅ Success: {$success}");
        $this->error("❌ Failed: {$failed}");

        return 0;
    }
}

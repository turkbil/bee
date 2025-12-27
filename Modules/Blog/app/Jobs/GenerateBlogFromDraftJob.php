<?php

namespace Modules\Blog\App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Modules\Blog\App\Models\BlogAIDraft;
use Modules\Blog\App\Services\BlogAIContentWriter;
use Modules\Blog\App\Services\BlogAIBatchProcessor;
use Illuminate\Support\Facades\Log;

/**
 * Generate Blog From Draft Job
 *
 * Queue: blog-ai
 * Seçili bir draft'tan tam blog yazısı oluşturur
 *
 * 🔧 FIX: SerializesModels trait kaldırıldı
 * Çünkü tenant model'ler serialize edilirken tenant context kayboluyor
 * Çözüm: Model yerine ID geçir, tenant context restore ettikten sonra model'i fetch et
 */
class GenerateBlogFromDraftJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public $tries = 3;
    public $timeout = 1200; // 🔧 FIX: 20 dakika (OpenAI API için yeterli)
    public $backoff = 60; // 60 saniye retry beklemesi

    /**
     * 🔧 FIX: Unique lock süresi - job tamamlanana kadar aynı draftId için yeni job başlatılmasın
     * Default 0 = Job tamamlanana kadar bekle (job'un timeout'u kadar)
     */
    public $uniqueFor = 1800; // 30 dakika unique lock (timeout'tan uzun)

    public ?int $tenantId = null; // Tenant context

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $draftId, // Model yerine ID geçir
        public ?string $batchId = null
    ) {
        // Tenant context'i kaydet (dispatch anında)
        $this->tenantId = tenant('id');

        // Explicit queue belirt - tenant_2_default yerine blog-ai kullan
        $this->onQueue('blog-ai');
    }

    /**
     * 🔧 FIX: Unique ID - aynı tenant + draft kombinasyonu için sadece 1 job çalışsın
     * Bu sayede paralel worker'lar aynı draft için duplicate job çalıştıramaz
     */
    public function uniqueId(): string
    {
        return 'blog_draft_' . $this->tenantId . '_' . $this->draftId;
    }

    /**
     * Execute the job.
     */
    public function handle(BlogAIContentWriter $writer, BlogAIBatchProcessor $batchProcessor): void
    {
        // Tenant context'i restore et (eğer zaten initialize değilse)
        if ($this->tenantId && (!tenant() || tenant('id') != $this->tenantId)) {
            tenancy()->initialize($this->tenantId);
        }

        // Tenant context restore edildikten SONRA model'i fetch et
        $draft = BlogAIDraft::findOrFail($this->draftId);

        // 🔒 KRİTİK 1: Draft zaten generate edilmiş mi kontrol et
        if ($draft->is_generated) {
            Log::warning('Draft already generated, skipping job', [
                'draft_id' => $draft->id,
                'existing_blog_id' => $draft->generated_blog_id,
                'batch_id' => $this->batchId,
                'tenant_id' => $this->tenantId,
            ]);

            // Batch progress güncelle (duplicate olsa bile completed sayılır)
            if ($this->batchId) {
                $batchProcessor->markCompleted($this->batchId);
            }

            return; // Job'u skip et, duplicate blog oluşturma!
        }

        // 🔒 KRİTİK 2: Benzer başlıkta blog var mı kontrol et (slug similarity)
        $draftSlug = \Illuminate\Support\Str::slug($draft->topic_keyword);

        // Mevcut blog slug'larını getir (Türkçe - tenant default dil)
        $existingBlogs = \Modules\Blog\App\Models\Blog::select('blog_id', 'slug', 'title')
            ->get()
            ->map(function($blog) {
                return [
                    'id' => $blog->blog_id,
                    'slug' => is_array($blog->slug) ? ($blog->slug['tr'] ?? '') : (json_decode($blog->slug, true)['tr'] ?? ''),
                    'title' => is_array($blog->title) ? ($blog->title['tr'] ?? '') : (json_decode($blog->title, true)['tr'] ?? ''),
                ];
            })
            ->filter(fn($b) => !empty($b['slug']));

        foreach ($existingBlogs as $existingBlog) {
            // Slug benzerlik kontrolü (similar_text kullanarak)
            similar_text($draftSlug, $existingBlog['slug'], $slugSimilarity);

            // Title benzerlik kontrolü (opsiyonel - daha kesin sonuç için)
            similar_text(
                strtolower($draft->topic_keyword),
                strtolower($existingBlog['title']),
                $titleSimilarity
            );

            // %95+ benzerlik varsa → Duplicate!
            // 🔧 FIX: %85 çok strict, çok fazla draft skip ediyordu
            if ($slugSimilarity >= 95 || $titleSimilarity >= 95) {
                Log::warning('Similar blog already exists, skipping job', [
                    'draft_id' => $draft->id,
                    'draft_topic' => $draft->topic_keyword,
                    'draft_slug' => $draftSlug,
                    'existing_blog_id' => $existingBlog['id'],
                    'existing_title' => $existingBlog['title'],
                    'existing_slug' => $existingBlog['slug'],
                    'slug_similarity' => round($slugSimilarity, 2),
                    'title_similarity' => round($titleSimilarity, 2),
                    'batch_id' => $this->batchId,
                    'tenant_id' => $this->tenantId,
                ]);

                // Draft'ı duplicate olarak işaretle (is_generated = true ama generated_blog_id = existing)
                $draft->update([
                    'is_generated' => true,
                    'generated_blog_id' => $existingBlog['id'], // Var olan blog'u referans et
                ]);

                // Batch progress güncelle
                if ($this->batchId) {
                    $batchProcessor->markCompleted($this->batchId);
                }

                return; // Job'u skip et!
            }
        }

        try {
            Log::info('Blog AI Content Generation Started', [
                'draft_id' => $draft->id,
                'batch_id' => $this->batchId,
                'tenant_id' => $this->tenantId,
            ]);

            // Blog oluştur
            $blog = $writer->generateBlogFromDraft($draft);

            // Batch progress güncelle (eğer batch varsa)
            if ($this->batchId) {
                $batchProcessor->markCompleted($this->batchId);
            }

            Log::info('Blog AI Content Generation Completed', [
                'draft_id' => $draft->id,
                'blog_id' => $blog->blog_id,
                'batch_id' => $this->batchId,
                'tenant_id' => $this->tenantId,
            ]);

        } catch (\Exception $e) {
            // Batch progress güncelle (failed)
            if ($this->batchId) {
                $batchProcessor->markFailed($this->batchId);
            }

            Log::error('Blog AI Content Generation Job Failed', [
                'draft_id' => $draft->id,
                'batch_id' => $this->batchId,
                'error' => $e->getMessage(),
                'tenant_id' => $this->tenantId,
            ]);

            // Job başarısız olursa retry edilecek (max tries: 3)
            $this->fail($e);
        }
    }
}

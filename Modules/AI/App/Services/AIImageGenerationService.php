<?php

namespace Modules\AI\App\Services;

use Exception;
use Modules\AI\App\Services\Providers\DallE3Provider;
use Modules\MediaManagement\App\Models\MediaLibraryItem;
use Illuminate\Support\Facades\Log;

/**
 * AI Image Generation Service
 *
 * Universal service for generating images via AI (DALL-E 3)
 * Used by: Admin Panel (manual), Blog AI (auto), Shop AI (auto), Portfolio AI (auto)
 */
class AIImageGenerationService
{
    protected DallE3Provider $provider;
    protected AICreditService $creditService;
    protected int $creditCost = 1; // 1 HD image = 1 credit

    public function __construct(DallE3Provider $provider, AICreditService $creditService)
    {
        $this->provider = $provider;
        $this->creditService = $creditService;
    }

    /**
     * Generate image from prompt (Manual usage - Admin Panel)
     *
     * @param string $prompt User-provided prompt
     * @param array $options ['size' => '1024x1024', 'quality' => 'hd']
     * @return MediaLibraryItem
     * @throws Exception
     */
    public function generate(string $prompt, array $options = []): MediaLibraryItem
    {
        $result = $this->generateWithRevision($prompt, $options);
        return $result['mediaItem'];
    }

    /**
     * Generate image and return both MediaLibraryItem + revised_prompt from DALL-E GPT-4
     *
     * @param string $prompt User-provided prompt
     * @param array $options ['size' => '1024x1024', 'quality' => 'hd' or 'standard']
     * @return array ['mediaItem' => MediaLibraryItem, 'revised_prompt' => string]
     * @throws Exception
     */
    public function generateWithRevision(string $prompt, array $options = []): array
    {
        // Credit cost: standard = 0.5, hd = 1
        $quality = $options['quality'] ?? 'hd';
        $creditCost = $quality === 'standard' ? 0.5 : 1;

        // Check credit balance using global helper
        if (!ai_can_use_credits($creditCost)) {
            throw new Exception('Insufficient credits. Required: ' . $creditCost);
        }

        try {
            // Generate image via DALL-E 3
            $imageData = $this->provider->generate($prompt, $options);

            // Create MediaLibraryItem with revised_prompt
            $mediaItem = $this->createMediaItem($imageData, $prompt, $options);

            // Deduct credits using global helper
            ai_use_credits($creditCost, null, [
                'usage_type' => 'image_generation',
                'provider_name' => 'openai',
                'model' => 'dall-e-3',
                'prompt' => $prompt,
                'revised_prompt' => $imageData['revised_prompt'] ?? null,
                'operation_type' => 'manual',
                'media_id' => $mediaItem->id,
                'quality' => $quality,
                'credit_cost' => $creditCost,
            ]);

            return [
                'mediaItem' => $mediaItem,
                'revised_prompt' => $imageData['revised_prompt'] ?? null,
            ];

        } catch (Exception $e) {
            Log::error('AI Image Generation failed', [
                'prompt' => $prompt,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Generate image for Blog AI (Automatic usage)
     *
     * @param string $title Blog post title
     * @param string|null $content Blog post content
     * @return MediaLibraryItem
     * @throws Exception
     */
    public function generateForBlog(string $title, ?string $content = null): MediaLibraryItem
    {
        $prompt = app(PromptGenerator::class)->generateForBlog($title, $content);

        return $this->generateAutomatic($prompt, 'blog_auto', [
            'title' => $title,
        ]);
    }

    /**
     * Generate image for Shop AI (Automatic usage)
     *
     * @param string $productName Product name
     * @param string|null $category Product category
     * @return MediaLibraryItem
     * @throws Exception
     */
    public function generateForProduct(string $productName, ?string $category = null): MediaLibraryItem
    {
        $prompt = app(PromptGenerator::class)->generateForProduct($productName, $category);

        return $this->generateAutomatic($prompt, 'product_auto', [
            'product_name' => $productName,
            'category' => $category,
        ]);
    }

    /**
     * Generate image for Portfolio AI (Automatic usage)
     *
     * @param string $projectName Project name
     * @param string|null $description Project description
     * @return MediaLibraryItem
     * @throws Exception
     */
    public function generateForPortfolio(string $projectName, ?string $description = null): MediaLibraryItem
    {
        $prompt = app(PromptGenerator::class)->generateForPortfolio($projectName, $description);

        return $this->generateAutomatic($prompt, 'portfolio_auto', [
            'project_name' => $projectName,
        ]);
    }

    /**
     * Internal: Generate image automatically (used by Blog/Shop/Portfolio AI)
     */
    protected function generateAutomatic(string $prompt, string $usageType, array $metadata = []): MediaLibraryItem
    {
        // Check credit balance using global helper
        if (!ai_can_use_credits($this->creditCost)) {
            throw new Exception('Insufficient credits for automatic image generation');
        }

        try {
            // Generate image (always HD, 1024x1024)
            $imageData = $this->provider->generate($prompt, [
                'size' => '1024x1024',
                'quality' => 'hd',
            ]);

            // Create MediaLibraryItem
            $mediaItem = $this->createMediaItem($imageData, $prompt, [
                'size' => '1024x1024',
                'quality' => 'hd',
            ]);

            // Deduct credits using global helper
            ai_use_credits($this->creditCost, null, array_merge($metadata, [
                'usage_type' => 'image_generation',
                'provider_name' => 'openai',
                'model' => 'dall-e-3',
                'prompt' => $prompt,
                'operation_type' => $usageType,
                'media_id' => $mediaItem->id,
            ]));

            return $mediaItem;

        } catch (Exception $e) {
            Log::error('Automatic AI Image Generation failed', [
                'prompt' => $prompt,
                'usage_type' => $usageType,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Create MediaLibraryItem from generated image data
     */
    protected function createMediaItem(array $imageData, string $prompt, array $options): MediaLibraryItem
    {
        // 🔧 FIX: Tenant context'i FORCE et (queue job'larda tenant() null dönebilir!)
        // MediaLibraryItem->getMediaDisk() çağrılmadan ÖNCE tenant disk config'i oluştur
        $tenantId = tenant('id');
        if ($tenantId) {
            // Force tenant disk configuration
            $root = storage_path("app/public");
            $appUrl = config('app.url') ? rtrim(config('app.url'), '/') : 'http://localhost';

            config([
                'filesystems.disks.tenant' => [
                    'driver' => 'local',
                    'root' => $root,
                    'url' => "{$appUrl}/storage/tenant{$tenantId}",
                    'visibility' => 'public',
                    'throw' => false,
                ],
            ]);

            Log::info('🔧 AI Image: Forced tenant disk configuration', [
                'tenant_id' => $tenantId,
                'disk_root' => $root,
            ]);
        }

        $mediaItem = MediaLibraryItem::create([
            'name' => 'AI Generated - ' . substr($prompt, 0, 50),
            'type' => 'image',
            'created_by' => auth()->id(),
            'generation_source' => 'ai_generated',
            'generation_prompt' => $prompt,
            'generation_params' => [
                'model' => 'dall-e-3',
                'size' => $options['size'] ?? '1024x1024',
                'quality' => $options['quality'] ?? 'hd',
                'provider' => 'openai',
                'tenant_id' => $tenantId, // Tenant ID'yi kaydet
            ],
        ]);

        // Attach image from URL to MediaLibraryItem
        // getMediaDisk() şimdi doğru tenant disk'ini bulacak
        $mediaItem->addMediaFromUrl($imageData['url'])
            ->toMediaCollection('library');

        return $mediaItem;
    }

    /**
     * Get generation history (last N images)
     */
    public function getHistory(int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return MediaLibraryItem::where('generation_source', 'ai_generated')
            ->where('created_by', auth()->id())
            ->latest()
            ->limit($limit)
            ->get();
    }
}

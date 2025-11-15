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
        // Check credit balance using global helper
        if (!ai_can_use_credits($this->creditCost)) {
            throw new Exception('Insufficient credits. Required: ' . $this->creditCost);
        }

        try {
            // Generate image via DALL-E 3
            $imageData = $this->provider->generate($prompt, $options);

            // Create MediaLibraryItem
            $mediaItem = $this->createMediaItem($imageData, $prompt, $options);

            // Deduct credits using global helper
            ai_use_credits($this->creditCost, null, [
                'usage_type' => 'image_generation',
                'provider_name' => 'openai',
                'model' => 'dall-e-3',
                'prompt' => $prompt,
                'operation_type' => 'manual',
                'media_id' => $mediaItem->id,
            ]);

            return $mediaItem;

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
            ],
        ]);

        // Attach image from URL to MediaLibraryItem
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

<?php

namespace Modules\AI\App\Services;

/**
 * Prompt Generator
 *
 * Automatically generates DALL-E 3 prompts for Blog, Shop, Portfolio AI
 */
class PromptGenerator
{
    /**
     * Generate prompt for Blog AI
     *
     * @param string $title Blog post title
     * @param string|null $content Blog post content (optional)
     * @return string DALL-E 3 prompt
     */
    public function generateForBlog(string $title, ?string $content = null): string
    {
        // Extract keywords from title
        $keywords = $this->extractKeywords($title);

        // Base prompt template
        $prompt = "Professional blog featured image for: {$title}.";

        // Tenant-specific image style enhancement (DATABASE)
        $tenantEnhancement = $this->getTenantImageEnhancement();
        if ($tenantEnhancement) {
            $prompt .= " {$tenantEnhancement}.";
        }

        // Add style based on content length
        if ($content && strlen($content) > 500) {
            $prompt .= " Detailed, informative, modern digital art style.";
        } else {
            $prompt .= " Clean, minimal, professional photography style.";
        }

        // Add keywords
        if (!empty($keywords)) {
            $prompt .= " Keywords: " . implode(', ', array_slice($keywords, 0, 3)) . ".";
        }

        // Ultra quality enhancement (NO numeric resolutions - AI writes them as text!)
        $prompt .= " Professional photography, natural lighting, sharp focus, clean composition.";

        // Text-free rules (STRICT - NO TEXT AT ALL!)
        $prompt .= " ABSOLUTELY NO text, NO labels, NO captions, NO annotations, NO numbers, NO letters, NO words of any kind visible in the image. Pure photograph only, completely text-free. If text is required, use Turkish language only. Generic names, no brand names or trademarks.";

        return $prompt;
    }

    /**
     * Get tenant-specific image enhancement from database
     *
     * @return string|null
     */
    protected function getTenantImageEnhancement(): ?string
    {
        try {
            // Tenant database'den ayarı oku (setting() helper kullan)
            $enhancement = setting('ai_image_prompt_enhancement');
            return $enhancement ?: null;
        } catch (\Exception $e) {
            // Hata olursa null döndür (generic prompt kullanılır)
            return null;
        }
    }

    /**
     * Generate prompt for Shop AI (Product)
     *
     * @param string $productName Product name
     * @param string|null $category Product category
     * @return string DALL-E 3 prompt
     */
    public function generateForProduct(string $productName, ?string $category = null): string
    {
        $prompt = "Professional product photography of {$productName}.";

        // Add category context
        if ($category) {
            $prompt .= " Category: {$category}.";
        }

        // Product photography enhancement (NO numeric resolutions!)
        $prompt .= " Clean white background, studio lighting, commercial photography style.";
        $prompt .= " Professional product photography, sharp focus, e-commerce ready quality.";

        // Text-free rules (STRICT!)
        $prompt .= " ABSOLUTELY NO text, NO brand names, NO labels, NO numbers, NO letters visible on product or background. Pure clean product photography, completely text-free. If text is required, use Turkish language only. Generic product design, no trademarks or copyrighted logos.";

        return $prompt;
    }

    /**
     * Generate prompt for Portfolio AI (Project)
     *
     * @param string $projectName Project name
     * @param string|null $description Project description
     * @return string DALL-E 3 prompt
     */
    public function generateForPortfolio(string $projectName, ?string $description = null): string
    {
        $prompt = "Creative portfolio project image for: {$projectName}.";

        // Add description context
        if ($description) {
            $keywords = $this->extractKeywords($description);
            if (!empty($keywords)) {
                $prompt .= " " . implode(', ', array_slice($keywords, 0, 3)) . ".";
            }
        }

        // Portfolio quality enhancement (NO numeric resolutions!)
        $prompt .= " Modern, professional, creative design style.";
        $prompt .= " Cinematic composition, visually stunning, portfolio-ready quality.";

        // Text-free rules (STRICT!)
        $prompt .= " ABSOLUTELY NO text, NO labels, NO numbers, NO letters visible in image. Pure visual design, completely text-free. If text is required, use Turkish language only. Generic design elements, no brand names or copyrighted content.";

        return $prompt;
    }

    /**
     * Extract keywords from text (simple implementation)
     */
    protected function extractKeywords(string $text): array
    {
        // Remove common words (stopwords)
        $stopwords = ['the', 'a', 'an', 'and', 'or', 'but', 'in', 'on', 'at', 'to', 'for', 'of', 'with', 've', 'mi', 'mı', 'mu', 'mü', 'da', 'de', 'ise', 'için', 'ile', 'gibi', 'bir', 'bu', 'şu', 've'];

        // Convert to lowercase and split into words
        $words = preg_split('/[\s,\.;:!?]+/', strtolower($text));

        // Filter stopwords and short words
        $keywords = array_filter($words, function($word) use ($stopwords) {
            return strlen($word) > 3 && !in_array($word, $stopwords);
        });

        // Return unique keywords
        return array_values(array_unique($keywords));
    }

    /**
     * Enhance user prompt with professional keywords
     */
    public function enhancePrompt(string $userPrompt): string
    {
        $prompt = $userPrompt;

        // Add professional quality keywords if not present
        $qualityKeywords = ['high quality', '4k', 'professional', 'detailed'];
        $hasQuality = false;

        foreach ($qualityKeywords as $keyword) {
            if (stripos($prompt, $keyword) !== false) {
                $hasQuality = true;
                break;
            }
        }

        if (!$hasQuality) {
            $prompt .= ". Professional quality, high resolution, detailed.";
        }

        return $prompt;
    }
}

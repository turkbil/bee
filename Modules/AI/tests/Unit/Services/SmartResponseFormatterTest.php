<?php

declare(strict_types=1);

namespace Modules\AI\Tests\Unit\Services;

use Tests\TestCase;
use Modules\AI\App\Services\SmartResponseFormatter;

class SmartResponseFormatterTest extends TestCase
{
    private SmartResponseFormatter $formatter;

    protected function setUp(): void
    {
        parent::setUp();
        $this->formatter = app(SmartResponseFormatter::class);
    }

    /**
     * Test anti-monotony formatting
     */
    public function test_avoids_monotonous_numbered_lists(): void
    {
        $content = "1. First item\n2. Second item\n3. Third item";
        
        $formatted = $this->formatter->format($content, 'blog', 'flexible');
        
        // Should not start with simple 1-2-3 pattern
        $this->assertStringNotContainsString("1. First", $formatted);
        $this->assertStringContainsString("First item", $formatted);
    }

    /**
     * Test feature-aware formatting for blog content
     */
    public function test_formats_blog_content_appropriately(): void
    {
        $content = "This is a blog post about AI. It has multiple paragraphs. Each paragraph should be properly formatted.";
        
        $formatted = $this->formatter->format($content, 'blog', 'adaptive');
        
        $this->assertNotEmpty($formatted);
        $this->assertIsString($formatted);
    }

    /**
     * Test SEO content formatting
     */
    public function test_formats_seo_content_with_keywords(): void
    {
        $content = "SEO optimization is important. Keywords should be emphasized. Meta descriptions matter.";
        
        $formatted = $this->formatter->format($content, 'seo', 'strict');
        
        $this->assertStringContainsString("SEO", $formatted);
        $this->assertStringContainsString("Keywords", $formatted);
    }

    /**
     * Test code formatting preservation
     */
    public function test_preserves_code_blocks(): void
    {
        $content = "Here is some code:\n```php\necho 'Hello World';\n```\nEnd of code.";
        
        $formatted = $this->formatter->format($content, 'code', 'strict');
        
        $this->assertStringContainsString("```php", $formatted);
        $this->assertStringContainsString("echo 'Hello World';", $formatted);
    }

    /**
     * Test creative content formatting
     */
    public function test_applies_creative_formatting(): void
    {
        $content = "Once upon a time, in a digital world...";
        
        $formatted = $this->formatter->format($content, 'creative', 'flexible');
        
        $this->assertNotEmpty($formatted);
        $this->assertStringContainsString("digital world", $formatted);
    }

    /**
     * Test strictness levels
     */
    public function test_respects_strictness_levels(): void
    {
        $content = "Test content for strictness levels.";
        
        $strict = $this->formatter->format($content, 'general', 'strict');
        $flexible = $this->formatter->format($content, 'general', 'flexible');
        $adaptive = $this->formatter->format($content, 'general', 'adaptive');
        
        $this->assertNotEmpty($strict);
        $this->assertNotEmpty($flexible);
        $this->assertNotEmpty($adaptive);
    }

    /**
     * Test pattern variations
     */
    public function test_uses_varied_patterns(): void
    {
        $contents = [
            "First content block",
            "Second content block",
            "Third content block",
        ];
        
        $results = [];
        foreach ($contents as $content) {
            $results[] = $this->formatter->format($content, 'blog', 'adaptive');
        }
        
        // Should produce varied outputs, not identical patterns
        $this->assertCount(3, array_unique($results));
    }

    /**
     * Test title formatting
     */
    public function test_formats_titles_properly(): void
    {
        $content = "# Main Title\n## Subtitle\nContent here.";
        
        $formatted = $this->formatter->format($content, 'blog', 'flexible');
        
        $this->assertStringContainsString("Main Title", $formatted);
        $this->assertStringContainsString("Subtitle", $formatted);
    }

    /**
     * Test list transformation
     */
    public function test_transforms_lists_creatively(): void
    {
        $content = "- Item one\n- Item two\n- Item three";
        
        $formatted = $this->formatter->format($content, 'creative', 'flexible');
        
        // Should transform simple lists into more engaging formats
        $this->assertStringContainsString("Item", $formatted);
        $this->assertNotEquals($content, $formatted);
    }

    /**
     * Test empty content handling
     */
    public function test_handles_empty_content(): void
    {
        $formatted = $this->formatter->format("", 'general', 'strict');
        
        $this->assertEmpty($formatted);
    }

    /**
     * Test special characters preservation
     */
    public function test_preserves_special_characters(): void
    {
        $content = "Special chars: @#$%^&*()_+ should be preserved.";
        
        $formatted = $this->formatter->format($content, 'general', 'strict');
        
        $this->assertStringContainsString("@#$%^&*()", $formatted);
    }

    /**
     * Test multi-language content
     */
    public function test_handles_multilanguage_content(): void
    {
        $content = "English text. Türkçe metin. 中文文本。";
        
        $formatted = $this->formatter->format($content, 'general', 'adaptive');
        
        $this->assertStringContainsString("English", $formatted);
        $this->assertStringContainsString("Türkçe", $formatted);
    }
}
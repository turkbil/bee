<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Log;

/**
 * Markdown Post-Processor
 *
 * AI'ın ürettiği HTML'deki format hatalarını düzeltir:
 * - Liste kırılmalarını birleştirir
 * - Block elementler arası newline ekler
 * - Broken emoji/noktalama paragraflarını düzeltir
 *
 * @package App\Services\AI
 * @version 1.0.0
 */
class MarkdownPostProcessor
{
    /**
     * Fix broken lists caused by emoji/punctuation splits
     *
     * Pattern: </ul><p>emoji/punctuation</p> → emoji'yi liste içine al
     */
    public function fixBrokenLists(string $html): string
    {
        $original = $html;

        // 1. Fix broken list items (emoji/punctuation split)
        // Pattern: </ul><p>!💯)</p> → !💯) should be in list
        $html = preg_replace(
            '/<\/ul>\s*<p>\s*([!?.,;:)\u{1F300}-\u{1F9FF}\s]+)\s*<\/p>/u',
            '$1</ul>',
            $html
        );

        // 2. Fix split list items with emoji
        // Pattern: <li>Text (güçlü</li></ul><p>! 💪)</p> → merge back
        $html = preg_replace(
            '/<li>([^<]+)<\/li>\s*<\/ul>\s*<p>\s*([!?.,;:)\u{1F300}-\u{1F9FF}\s]+)\s*<\/p>/u',
            '<li>$1$2</li></ul>',
            $html
        );

        // 3. Merge consecutive lists back together
        // Pattern: </ul>...<ul> → remove split
        $html = preg_replace(
            '/<\/ul>\s*<ul>/i',
            '',
            $html
        );

        // 4. Fix orphan paragraphs between lists
        // Pattern: </ul><p>! 💯)</p><ul> → merge to previous list
        $html = preg_replace(
            '/<\/ul>\s*<p>\s*([!?.,;:)\u{1F300}-\u{1F9FF}\s]+)\s*<\/p>\s*<ul>/u',
            '$1',
            $html
        );

        if ($html !== $original) {
            Log::info('🔧 MarkdownPostProcessor: Fixed broken lists', [
                'changes' => strlen($original) - strlen($html),
            ]);
        }

        return $html;
    }

    /**
     * Add proper newlines between block elements
     */
    public function addBlockSpacing(string $html): string
    {
        $original = $html;

        // Add newline between block elements
        $html = preg_replace(
            '/(<\/(?:ul|ol|blockquote|table|div)>)(\s*)(<(?:p|h[1-6]|ul|ol|blockquote|table|div)>)/i',
            "$1\n\n$3",
            $html
        );

        // Normalize multiple newlines (max 2)
        $html = preg_replace('/\n{3,}/', "\n\n", $html);

        if ($html !== $original) {
            Log::info('🔧 MarkdownPostProcessor: Added block spacing');
        }

        return $html;
    }

    /**
     * Main post-processing pipeline
     *
     * @param string $html AI-generated HTML
     * @return array ['original', 'processed', 'fixes_applied', 'has_changes']
     */
    public function process(string $html): array
    {
        $original = $html;
        $fixes = [];

        // Step 1: Fix broken lists
        $beforeLists = $html;
        $html = $this->fixBrokenLists($html);
        if ($html !== $beforeLists) {
            $fixes[] = 'broken_lists_fixed';
        }

        // Step 2: Add block spacing
        $beforeSpacing = $html;
        $html = $this->addBlockSpacing($html);
        if ($html !== $beforeSpacing) {
            $fixes[] = 'block_spacing_added';
        }

        return [
            'original' => $original,
            'processed' => $html,
            'fixes_applied' => $fixes,
            'has_changes' => count($fixes) > 0,
        ];
    }
}

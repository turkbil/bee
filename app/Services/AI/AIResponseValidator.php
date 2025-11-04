<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Log;

/**
 * AI Response Validator
 *
 * AI yanıtlarını validate eder ve otomatik düzeltme yapar:
 * - Yanlış contact link'lerini düzeltir
 * - Broken markdown pattern'lerini tespit eder
 * - Quality metrics toplar
 *
 * @package App\Services\AI
 * @version 1.0.0
 */
class AIResponseValidator
{
    /**
     * Validate and auto-fix AI response
     *
     * @param string $html AI-generated HTML
     * @param array $context Request context (for validation)
     * @return array ['original', 'fixed', 'has_errors', 'has_warnings', 'errors', 'warnings', 'auto_fixed']
     */
    public function validateAndFix(string $html, array $context = []): array
    {
        $errors = [];
        $warnings = [];
        $fixed = $html;

        // 1. Check for invalid contact links (product URL instead of tel:/wa.me)
        $contactLinkErrors = $this->validateContactLinks($fixed);
        if (!empty($contactLinkErrors)) {
            $errors = array_merge($errors, $contactLinkErrors);
            $fixed = $this->fixContactLinks($fixed);
        }

        // 2. Check for broken lists (emoji/punctuation split)
        if (preg_match('/<\/ul>\s*<p>\s*[!?.,;:)😀-🙏💀-🛿]/u', $fixed)) {
            $warnings[] = [
                'type' => 'broken_list',
                'severity' => 'medium',
                'message' => 'List item broken by newline (emoji/punctuation split)',
            ];
        }

        // 3. Check for missing newlines between block elements
        if (preg_match('/<\/(?:ul|ol)>(<p>|<h[1-6]>)/i', $fixed)) {
            $warnings[] = [
                'type' => 'missing_newline',
                'severity' => 'low',
                'message' => 'Missing newline between block elements',
            ];
        }

        // 4. Check for consecutive lists (should be merged)
        if (preg_match('/<\/ul>\s*<ul>/i', $fixed)) {
            $warnings[] = [
                'type' => 'split_lists',
                'severity' => 'medium',
                'message' => 'Consecutive lists detected (should be merged)',
            ];
        }

        // 5. Check for price display issues (if base_price in context but not shown)
        if (!empty($context['smart_search_results']['products'])) {
            $priceErrors = $this->validatePriceDisplay($fixed, $context['smart_search_results']['products']);
            if (!empty($priceErrors)) {
                $warnings = array_merge($warnings, $priceErrors);
            }
        }

        return [
            'original' => $html,
            'fixed' => $fixed,
            'has_errors' => count($errors) > 0,
            'has_warnings' => count($warnings) > 0,
            'errors' => $errors,
            'warnings' => $warnings,
            'auto_fixed' => $fixed !== $html,
        ];
    }

    /**
     * Validate contact links (detect product URLs instead of tel:/wa.me)
     */
    protected function validateContactLinks(string $html): array
    {
        $errors = [];

        // Pattern: Phone number linked to product page
        if (preg_match_all('/<a href="https?:\/\/[^"]*\/shop\/[^"]+">(\+?\d[\d\s]+)<\/a>/i', $html, $matches)) {
            foreach ($matches[0] as $index => $match) {
                $errors[] = [
                    'type' => 'invalid_contact_link',
                    'severity' => 'critical',
                    'message' => 'Contact number linked to product page instead of tel:/wa.me',
                    'detected_link' => $match,
                    'phone_number' => $matches[1][$index],
                ];
            }
        }

        return $errors;
    }

    /**
     * Fix contact links (convert product URLs to tel: or WhatsApp links)
     */
    protected function fixContactLinks(string $html): string
    {
        // Fix 1: Product URLs with phone numbers → tel: links
        $html = preg_replace_callback(
            '/<a href="https?:\/\/[^"]*\/shop\/[^"]+"[^>]*>(\+?\d[\d\s]+)<\/a>/i',
            function ($matches) {
                $phone = preg_replace('/[^0-9+]/', '', $matches[1]);
                $displayPhone = $matches[1];

                // Check if this is a WhatsApp number pattern (0501, 0532, etc.)
                if (preg_match('/^0?5\d{2}/', $phone)) {
                    // Mobile number - use WhatsApp
                    $cleanPhone = '90' . ltrim($phone, '0');
                    Log::warning('🔧 AIResponseValidator: Fixed invalid WhatsApp link', [
                        'original' => $matches[0],
                        'fixed' => "https://wa.me/{$cleanPhone}",
                    ]);
                    return '<a href="https://wa.me/' . $cleanPhone . '" target="_blank" rel="noopener noreferrer">' . $displayPhone . '</a>';
                } else {
                    // Landline - use tel:
                    Log::warning('🔧 AIResponseValidator: Fixed invalid phone link', [
                        'original' => $matches[0],
                        'fixed' => "tel:{$phone}",
                    ]);
                    return '<a href="tel:' . $phone . '">' . $displayPhone . '</a>';
                }
            },
            $html
        );

        // Fix 2: WhatsApp text with wrong URL
        // Pattern: [WhatsApp text](wrong-url) → [WhatsApp text](https://wa.me/...)
        $html = preg_replace_callback(
            '/\[([^\]]*(?:WhatsApp|whatsapp|WA)[^\]]*)\]\((?!https:\/\/wa\.me)([^\)]+)\)/i',
            function ($matches) {
                $linkText = $matches[1];
                $wrongUrl = $matches[2];

                // Extract phone number from text or URL
                if (preg_match('/(\+?9?0?\s?5\d{2}\s?\d{3}\s?\d{2}\s?\d{2})/', $linkText . ' ' . $wrongUrl, $phoneMatch)) {
                    $phone = preg_replace('/[^0-9]/', '', $phoneMatch[1]);
                    $phone = '90' . ltrim($phone, '90');

                    Log::warning('🔧 AIResponseValidator: Fixed WhatsApp markdown link', [
                        'original' => $matches[0],
                        'fixed' => "[{$linkText}](https://wa.me/{$phone})",
                    ]);

                    return "[{$linkText}](https://wa.me/{$phone})";
                }

                return $matches[0]; // Keep original if no phone found
            },
            $html
        );

        return $html;
    }

    /**
     * Validate price display (check if prices in context but not shown)
     */
    protected function validatePriceDisplay(string $html, array $products): array
    {
        $warnings = [];

        foreach ($products as $product) {
            // If product has price but response says "iletişime geçin"
            if (!empty($product['base_price']) && $product['base_price'] > 0) {
                $productTitle = $product['title'] ?? '';

                // If title is array (multilang), get string value
                if (is_array($productTitle)) {
                    $productTitle = $productTitle[app()->getLocale()] ?? reset($productTitle) ?? '';
                }

                $basePrice = $product['base_price'];

                // Check if response contains "Bilgi için iletişime geçin" near this product
                if (!empty($productTitle) && is_string($productTitle) && strpos($html, $productTitle) !== false) {
                    // Check if price is shown
                    $pricePattern = '/' . preg_quote($productTitle, '/') . '.*?(Fiyat:|' . number_format($basePrice, 0, ',', '.') . ')/is';

                    if (!preg_match($pricePattern, $html)) {
                        $warnings[] = [
                            'type' => 'price_not_displayed',
                            'severity' => 'medium',
                            'message' => "Product has price in context but AI didn't show it",
                            'product' => $productTitle,
                            'base_price' => $basePrice,
                        ];
                    }
                }
            }
        }

        return $warnings;
    }
}

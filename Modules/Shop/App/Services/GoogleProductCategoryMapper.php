<?php

declare(strict_types=1);

namespace Modules\Shop\App\Services;

class GoogleProductCategoryMapper
{
    /**
     * Google Shopping Category Taxonomy
     *
     * iXtif Kategori ID => Google Shopping Category
     *
     * Google Taxonomy: https://www.google.com/basepages/producttype/taxonomy-with-ids.en-US.txt
     */
    private static array $categoryMap = [
        // Manuel mapping - Admin panelden kategori ID'lerini buraya ekle
        // Örnek:
        // 1 => 'Business & Industrial > Material Handling > Pallet Jacks & Stackers',
        // 2 => 'Business & Industrial > Material Handling > Forklifts',
        // 3 => 'Vehicles & Parts > Vehicle Parts & Accessories',
    ];

    /**
     * Default fallback category (Business & Industrial > Material Handling)
     * Google Taxonomy ID: 1167
     */
    private static string $defaultCategory = '1167';

    /**
     * Map category ID to Google Shopping category
     *
     * @param int|null $categoryId
     * @return string|null
     */
    public static function map(?int $categoryId): ?string
    {
        if ($categoryId === null) {
            return self::$defaultCategory;
        }

        // Manuel mapping varsa kullan
        if (isset(self::$categoryMap[$categoryId])) {
            return self::$categoryMap[$categoryId];
        }

        // Fallback: Default kategori
        return self::$defaultCategory;
    }

    /**
     * Auto-detect category from product title/category name
     *
     * @param string $text
     * @return string|null
     */
    public static function autoDetect(string $text): ?string
    {
        $text = strtolower($text);

        // Keyword-based detection (GOOGLE TAXONOMY ID KULLAN!)
        if (stripos($text, 'forklift') !== false || stripos($text, 'istif') !== false) {
            return '499950'; // Forklifts
        }

        if (stripos($text, 'transpalet') !== false || stripos($text, 'pallet') !== false) {
            return '499954'; // Pallet Jacks & Stackers
        }

        if (stripos($text, 'akü') !== false || stripos($text, 'battery') !== false) {
            return '8236'; // Batteries
        }

        if (stripos($text, 'yedek') !== false || stripos($text, 'parça') !== false || stripos($text, 'part') !== false) {
            return '8301'; // Vehicle Parts & Accessories
        }

        // Default fallback
        return '1167'; // Business & Industrial > Material Handling
    }

    /**
     * Get category with auto-detection
     *
     * @param int|null $categoryId
     * @param string|null $productTitle
     * @param string|null $categoryName
     * @return string
     */
    public static function getCategory(?int $categoryId, ?string $productTitle = null, ?string $categoryName = null): string
    {
        // Önce manuel mapping dene
        $mapped = self::map($categoryId);
        if ($mapped && $mapped !== self::$defaultCategory) {
            return $mapped;
        }

        // Auto-detection dene (kategori adı)
        if ($categoryName) {
            $detected = self::autoDetect($categoryName);
            if ($detected) {
                return $detected;
            }
        }

        // Auto-detection dene (ürün adı)
        if ($productTitle) {
            $detected = self::autoDetect($productTitle);
            if ($detected) {
                return $detected;
            }
        }

        // Son çare: Default
        return self::$defaultCategory;
    }
}

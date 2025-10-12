<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

/**
 * Shop Category Mapper
 *
 * PDF klasör yapısını dinamik olarak shop_categories ile eşleştirir.
 * ID'lere değil slug'lara dayalı çalışır.
 */
class ShopCategoryMapper
{
    /**
     * PDF klasör adlarını kategori slug'larına map'ler
     */
    private static array $folderToSlug = [
        '1-Forklift' => 'forklift',
        '2-Transpalet' => 'transpalet',
        '3-İstif Makineleri' => 'istif-makinesi',
        '4-Order Picker' => 'siparis-toplama-makinesi',
        '5-Otonom' => 'otonom-sistemler',
        '6-Reach Truck' => 'reach-truck',
    ];

    /**
     * PDF klasör adından category_id döndürür
     *
     * @param string $folderName Örn: "2-Transpalet"
     * @return int|null
     */
    public static function getCategoryIdFromFolder(string $folderName): ?int
    {
        // Cache key
        $cacheKey = "shop_category_mapper_{$folderName}";

        return Cache::remember($cacheKey, 3600, function () use ($folderName) {
            // Klasör adından slug'ı bul
            $slug = self::$folderToSlug[$folderName] ?? null;

            if (!$slug) {
                \Log::warning("ShopCategoryMapper: Bilinmeyen klasör adı", ['folder' => $folderName]);
                return null;
            }

            // Slug'dan category_id bul
            $category = DB::table('shop_categories')
                ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(slug, '$.tr')) = ?", [$slug])
                ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(slug, '$.en')) = ?", [$slug])
                ->first();

            if (!$category) {
                \Log::error("ShopCategoryMapper: Kategori bulunamadı", [
                    'folder' => $folderName,
                    'slug' => $slug
                ]);
                return null;
            }

            return (int) $category->category_id;
        });
    }

    /**
     * Kategori slug'ından category_id döndürür
     *
     * @param string $slug Örn: "transpalet"
     * @return int|null
     */
    public static function getCategoryIdFromSlug(string $slug): ?int
    {
        $cacheKey = "shop_category_id_{$slug}";

        return Cache::remember($cacheKey, 3600, function () use ($slug) {
            $category = DB::table('shop_categories')
                ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(slug, '$.tr')) = ?", [$slug])
                ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(slug, '$.en')) = ?", [$slug])
                ->first();

            return $category ? (int) $category->category_id : null;
        });
    }

    /**
     * Kategori adından category_id döndürür
     *
     * @param string $title Örn: "Transpalet" veya "Pallet Truck"
     * @return int|null
     */
    public static function getCategoryIdFromTitle(string $title): ?int
    {
        $cacheKey = "shop_category_title_{$title}";

        return Cache::remember($cacheKey, 3600, function () use ($title) {
            $category = DB::table('shop_categories')
                ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(title, '$.tr')) = ?", [$title])
                ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(title, '$.en')) = ?", [$title])
                ->first();

            return $category ? (int) $category->category_id : null;
        });
    }

    /**
     * Tüm kategori mapping'ini döndürür (Debug için)
     *
     * @return array
     */
    public static function getAllMappings(): array
    {
        return collect(self::$folderToSlug)->map(function ($slug, $folder) {
            return [
                'folder' => $folder,
                'slug' => $slug,
                'category_id' => self::getCategoryIdFromSlug($slug),
            ];
        })->toArray();
    }

    /**
     * Cache'i temizle
     */
    public static function clearCache(): void
    {
        Cache::tags(['shop_category_mapper'])->flush();
        \Log::info("ShopCategoryMapper: Cache temizlendi");
    }
}

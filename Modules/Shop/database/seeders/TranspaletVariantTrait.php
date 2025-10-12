<?php

namespace Modules\Shop\Database\Seeders;

/**
 * Transpalet Varyant Helper Trait
 *
 * AMAÇ: Tüm seederlar'da tekrarlanan kodu minimize et, token tasarrufu sağla
 * KULLANIM: use TranspaletVariantTrait; trait'i seeder class'ına ekle
 */
trait TranspaletVariantTrait
{
    /**
     * Master ürünün technical_specs'ini al ve varyant özelliklerine göre özelleştir
     *
     * @param array $masterSpecs Master ürünün technical_specs array'i
     * @param array $variantData Varyant özgü değerler (fork_length, fork_width, weight, battery_config)
     * @return array Özelleştirilmiş technical_specs
     */
    protected function inheritTechnicalSpecs(array $masterSpecs, array $variantData): array
    {
        // Master'dan tüm özellikleri al
        $specs = $masterSpecs;

        // Varyant özelliklerine göre override et
        if (isset($variantData['fork_length'])) {
            $specs['dimensions']['fork_dimensions']['length'] = $variantData['fork_length'];
        }

        if (isset($variantData['fork_width'])) {
            $specs['dimensions']['fork_dimensions']['width'] = $variantData['fork_width'];
            $specs['dimensions']['fork_spread'] = ['standard' => $variantData['fork_width'], 'unit' => 'mm'];
        }

        if (isset($variantData['weight'])) {
            $specs['capacity']['service_weight'] = ['value' => $variantData['weight'], 'unit' => 'kg'];
        }

        if (isset($variantData['battery_config'])) {
            $specs['electrical']['battery_system']['configuration'] = $variantData['battery_config'];
        }

        return $specs;
    }

    /**
     * Varyant için eksiksiz FAQ oluştur (min 10 soru)
     * Master FAQ'lar + Varyant özgü sorular
     *
     * @param array $masterFaq Master ürünün faq_data array'i
     * @param array $variantSpecificFaq Varyant özgü FAQ'lar
     * @return array Birleştirilmiş FAQ array'i
     */
    protected function mergeFaqData(array $masterFaq, array $variantSpecificFaq): array
    {
        // Master FAQ'ları al (ilk 5-6)
        $baseFaqs = array_slice($masterFaq, 0, 6);

        // Varyant özgü FAQ'ları ekle
        $mergedFaqs = array_merge($baseFaqs, $variantSpecificFaq);

        // Sort order'ları yeniden düzenle
        foreach ($mergedFaqs as $index => &$faq) {
            $faq['sort_order'] = $index + 1;
        }

        return $mergedFaqs;
    }

    /**
     * Master use_cases'i al, varyant özgü use_case'leri başa ekle
     *
     * @param array $masterUseCases Master use_cases
     * @param array $variantUseCases Varyant özgü use_cases (opsiyonel)
     * @return array
     */
    protected function inheritUseCases(array $masterUseCases, array $variantUseCases = []): array
    {
        if (empty($variantUseCases)) {
            return $masterUseCases;
        }

        // Varyant use case'leri başa ekle, master use case'lerin bir kısmını tut
        $baseUseCases = array_slice($masterUseCases['tr'], 0, 4);

        return [
            'tr' => array_merge($variantUseCases['tr'], $baseUseCases)
        ];
    }

    /**
     * Master competitive_advantages'i al, varyant özgü avantajları başa ekle
     */
    protected function inheritCompetitiveAdvantages(array $masterAdvantages, array $variantAdvantages = []): array
    {
        if (empty($variantAdvantages)) {
            return $masterAdvantages;
        }

        $baseAdvantages = array_slice($masterAdvantages['tr'], 0, 3);

        return [
            'tr' => array_merge($variantAdvantages['tr'], $baseAdvantages)
        ];
    }

    /**
     * Master target_industries'i inherit et (genelde aynı)
     */
    protected function inheritTargetIndustries(array $masterIndustries, array $variantIndustries = []): array
    {
        return empty($variantIndustries) ? $masterIndustries : $variantIndustries;
    }

    /**
     * Master media_gallery'den varyant gallery oluştur
     */
    protected function createVariantMediaGallery(string $productCode, string $variantType): array
    {
        return [
            ['type' => 'image', 'url' => "products/{$productCode}/{$variantType}.jpg", 'is_primary' => true, 'sort_order' => 1],
            ['type' => 'image', 'url' => "products/{$productCode}/{$variantType}-detail.jpg", 'is_primary' => false, 'sort_order' => 2],
        ];
    }
}

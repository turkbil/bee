<?php

namespace App\Services\AI;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Modules\SeoManagement\App\Models\SeoSetting;
use Modules\SeoManagement\App\Services\SeoRecommendationsService;

/**
 * AUTO SEO FILL SERVICE
 * Premium tenant'lar için otomatik SEO doldurma
 *
 * Çalışma Mantığı:
 * 1. Premium tenant kontrolü
 * 2. SEO alanları boş mu kontrolü (title + description)
 * 3. Boşsa AI ile doldur (sadece 1. alternatif)
 * 4. SeoSetting'e kaydet
 */
class AutoSeoFillService
{
    private SeoRecommendationsService $seoRecommendationsService;

    public function __construct(SeoRecommendationsService $seoRecommendationsService)
    {
        $this->seoRecommendationsService = $seoRecommendationsService;
    }

    /**
     * Auto fill işlemi yapılmalı mı?
     *
     * @param Model $model - Page, Portfolio, Announcement, vb.
     * @param string $locale - Dil kodu (tr, en, de, vb.)
     * @return bool
     */
    public function shouldAutoFill(Model $model, string $locale): bool
    {
        try {
            // 1. Tenant kontrolü
            $tenant = tenant();
            if (!$tenant || !$tenant->isPremium()) {
                Log::info('❌ Auto SEO Fill: Tenant premium değil', [
                    'tenant_id' => $tenant?->id,
                    'is_premium' => $tenant?->isPremium() ?? false
                ]);
                return false;
            }

            // 2. Model SEO trait'ine sahip mi?
            if (!method_exists($model, 'seoSetting')) {
                Log::warning('❌ Auto SEO Fill: Model SEO trait\'ine sahip değil', [
                    'model_class' => get_class($model),
                    'model_id' => $model->id
                ]);
                return false;
            }

            // 3. SEO Setting var mı?
            $seoSetting = $model->seoSetting;

            // SEO Setting yoksa kesinlikle doldur
            if (!$seoSetting) {
                Log::info('✅ Auto SEO Fill: SEO Setting yok, doldurulacak', [
                    'model_class' => get_class($model),
                    'model_id' => $model->id,
                    'locale' => $locale
                ]);
                return true;
            }

            // 4. Title ve Description boş mu?
            $titles = $seoSetting->titles ?? [];
            $descriptions = $seoSetting->descriptions ?? [];

            $titleEmpty = empty(trim($titles[$locale] ?? ''));
            $descriptionEmpty = empty(trim($descriptions[$locale] ?? ''));

            // Her ikisi de boşsa doldur
            if ($titleEmpty && $descriptionEmpty) {
                Log::info('✅ Auto SEO Fill: Title ve Description boş, doldurulacak', [
                    'model_class' => get_class($model),
                    'model_id' => $model->id,
                    'locale' => $locale,
                    'title_empty' => $titleEmpty,
                    'description_empty' => $descriptionEmpty
                ]);
                return true;
            }

            Log::info('❌ Auto SEO Fill: SEO verileri mevcut, doldurulmayacak', [
                'model_class' => get_class($model),
                'model_id' => $model->id,
                'locale' => $locale,
                'title_empty' => $titleEmpty,
                'description_empty' => $descriptionEmpty
            ]);

            return false;

        } catch (\Exception $e) {
            Log::error('❌ Auto SEO Fill: shouldAutoFill hatası', [
                'error' => $e->getMessage(),
                'model_class' => get_class($model),
                'model_id' => $model->id
            ]);
            return false;
        }
    }

    /**
     * AI ile SEO verilerini otomatik doldur
     *
     * @param Model $model
     * @param string $locale
     * @return array|null
     */
    public function autoFillSeoData(Model $model, string $locale): ?array
    {
        try {
            Log::info('🚀 Auto SEO Fill: Başlıyor', [
                'model_class' => get_class($model),
                'model_id' => $model->id,
                'locale' => $locale
            ]);

            // Model'den içerik çıkar
            $formContent = $this->extractFormContent($model, $locale);

            if (empty($formContent['title']) && empty($formContent['body'])) {
                Log::warning('⚠️ Auto SEO Fill: Model içeriği boş', [
                    'model_class' => get_class($model),
                    'model_id' => $model->id
                ]);
                return null;
            }

            // AI ile SEO önerileri al
            $result = $this->seoRecommendationsService->generateSeoRecommendations(
                'seo-smart-recommendations',
                $formContent,
                $locale,
                [
                    'model_id' => $model->id,
                    'model_class' => get_class($model),
                    'auto_fill' => true
                ]
            );

            if (!$result['success'] || empty($result['recommendations'])) {
                Log::error('❌ Auto SEO Fill: AI önerileri alınamadı', [
                    'model_id' => $model->id,
                    'result' => $result
                ]);
                return null;
            }

            // Sadece 1. alternatifleri al
            $seoData = $this->extractFirstAlternatives($result['recommendations']);

            Log::info('✅ Auto SEO Fill: SEO verileri hazırlandı', [
                'model_id' => $model->id,
                'locale' => $locale,
                'seo_data' => $seoData
            ]);

            return $seoData;

        } catch (\Exception $e) {
            Log::error('❌ Auto SEO Fill: autoFillSeoData hatası', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'model_class' => get_class($model),
                'model_id' => $model->id
            ]);
            return null;
        }
    }

    /**
     * SEO verilerini SeoSetting'e kaydet
     *
     * @param Model $model
     * @param array $seoData
     * @param string $locale
     * @return bool
     */
    public function saveSeoData(Model $model, array $seoData, string $locale): bool
    {
        try {
            Log::info('💾 Auto SEO Fill: Kayıt başlıyor', [
                'model_class' => get_class($model),
                'model_id' => $model->id,
                'locale' => $locale,
                'seo_data' => $seoData
            ]);

            // SEO Setting'i al veya oluştur
            $seoSetting = $model->seoSetting ?? $model->seoSetting()->create([
                'titles' => [],
                'descriptions' => [],
                'og_titles' => [],
                'og_descriptions' => []
            ]);

            // Mevcut verileri al
            $titles = $seoSetting->titles ?? [];
            $descriptions = $seoSetting->descriptions ?? [];
            $ogTitles = $seoSetting->og_titles ?? [];
            $ogDescriptions = $seoSetting->og_descriptions ?? [];

            // Yeni verileri ekle (sadece boş alanlar)
            if (!empty($seoData['seo_title']) && empty(trim($titles[$locale] ?? ''))) {
                $titles[$locale] = $seoData['seo_title'];
            }

            if (!empty($seoData['seo_description']) && empty(trim($descriptions[$locale] ?? ''))) {
                $descriptions[$locale] = $seoData['seo_description'];
            }

            if (!empty($seoData['og_title']) && empty(trim($ogTitles[$locale] ?? ''))) {
                $ogTitles[$locale] = $seoData['og_title'];
            }

            if (!empty($seoData['og_description']) && empty(trim($ogDescriptions[$locale] ?? ''))) {
                $ogDescriptions[$locale] = $seoData['og_description'];
            }

            // Güncelle
            $seoSetting->update([
                'titles' => $titles,
                'descriptions' => $descriptions,
                'og_titles' => $ogTitles,
                'og_descriptions' => $ogDescriptions
            ]);

            Log::info('✅ Auto SEO Fill: Kayıt tamamlandı', [
                'model_id' => $model->id,
                'locale' => $locale,
                'seo_setting_id' => $seoSetting->id
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('❌ Auto SEO Fill: saveSeoData hatası', [
                'error' => $e->getMessage(),
                'model_class' => get_class($model),
                'model_id' => $model->id
            ]);
            return false;
        }
    }

    /**
     * Model'den form içeriği çıkar
     */
    private function extractFormContent(Model $model, string $locale): array
    {
        $formContent = [];

        // Title çıkar (multi-language desteği)
        if (isset($model->title)) {
            $formContent['title'] = is_array($model->title)
                ? ($model->title[$locale] ?? '')
                : $model->title;
        } elseif (isset($model->name)) {
            $formContent['title'] = is_array($model->name)
                ? ($model->name[$locale] ?? '')
                : $model->name;
        }

        // Body/Content çıkar
        if (isset($model->body)) {
            $formContent['body'] = is_array($model->body)
                ? ($model->body[$locale] ?? '')
                : $model->body;
        } elseif (isset($model->content)) {
            $formContent['body'] = is_array($model->content)
                ? ($model->content[$locale] ?? '')
                : $model->content;
        } elseif (isset($model->description)) {
            $formContent['body'] = is_array($model->description)
                ? ($model->description[$locale] ?? '')
                : $model->description;
        }

        // Slug ekle (varsa)
        if (isset($model->slug)) {
            $formContent['slug'] = is_array($model->slug)
                ? ($model->slug[$locale] ?? '')
                : $model->slug;
        }

        return $formContent;
    }

    /**
     * AI önerilerinden sadece 1. alternatifleri çıkar
     */
    private function extractFirstAlternatives(array $recommendations): array
    {
        $seoData = [];

        foreach ($recommendations as $rec) {
            $type = $rec['type'] ?? '';
            $fieldTarget = $rec['field_target'] ?? '';

            // Alternatives varsa ilkini al
            if (!empty($rec['alternatives']) && is_array($rec['alternatives'])) {
                $firstAlternative = $rec['alternatives'][0] ?? null;
                if ($firstAlternative && !empty($firstAlternative['value'])) {
                    $value = $firstAlternative['value'];

                    // Field target'a göre mapping
                    if (str_contains($fieldTarget, 'seo_title') || $type === 'seo_title') {
                        $seoData['seo_title'] = $value;
                    } elseif (str_contains($fieldTarget, 'seo_description') || $type === 'seo_description') {
                        $seoData['seo_description'] = $value;
                    } elseif (str_contains($fieldTarget, 'og_title') || $type === 'og_title') {
                        $seoData['og_title'] = $value;
                    } elseif (str_contains($fieldTarget, 'og_description') || $type === 'og_description') {
                        $seoData['og_description'] = $value;
                    }
                }
            }
            // Value varsa direkt al
            elseif (!empty($rec['value'])) {
                $value = $rec['value'];

                if (str_contains($fieldTarget, 'seo_title') || $type === 'seo_title') {
                    $seoData['seo_title'] = $value;
                } elseif (str_contains($fieldTarget, 'seo_description') || $type === 'seo_description') {
                    $seoData['seo_description'] = $value;
                } elseif (str_contains($fieldTarget, 'og_title') || $type === 'og_title') {
                    $seoData['og_title'] = $value;
                } elseif (str_contains($fieldTarget, 'og_description') || $type === 'og_description') {
                    $seoData['og_description'] = $value;
                }
            }
        }

        return $seoData;
    }

    /**
     * Toplu Auto Fill (tüm model'ler için)
     *
     * @param string $modelClass - Model sınıfı (Page, Portfolio, vb.)
     * @param string $locale
     * @return array
     */
    public function bulkAutoFill(string $modelClass, string $locale): array
    {
        $tenant = tenant();
        if (!$tenant || !$tenant->isPremium()) {
            return [
                'success' => false,
                'error' => 'Tenant premium değil'
            ];
        }

        try {
            $models = $modelClass::all();
            $filled = 0;
            $skipped = 0;
            $errors = 0;

            foreach ($models as $model) {
                if ($this->shouldAutoFill($model, $locale)) {
                    $seoData = $this->autoFillSeoData($model, $locale);
                    if ($seoData && $this->saveSeoData($model, $seoData, $locale)) {
                        $filled++;
                    } else {
                        $errors++;
                    }
                } else {
                    $skipped++;
                }
            }

            return [
                'success' => true,
                'total' => $models->count(),
                'filled' => $filled,
                'skipped' => $skipped,
                'errors' => $errors
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}

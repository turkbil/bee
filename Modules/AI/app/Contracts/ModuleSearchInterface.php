<?php

namespace Modules\AI\App\Contracts;

/**
 * Module Search Interface
 *
 * Tüm modül arama servisleri bu interface'i implement etmeli.
 * Bu sayede AI Assistant herhangi bir modüle bağlanabilir.
 *
 * @package Modules\AI\App\Contracts
 */
interface ModuleSearchInterface
{
    /**
     * Modül içinde arama yap
     *
     * @param string $query Kullanıcı sorgusu
     * @param array $filters Opsiyonel filtreler (category_id, tags, vb.)
     * @param int $limit Sonuç limiti
     * @return array Arama sonuçları
     */
    public function search(string $query, array $filters = [], int $limit = 50): array;

    /**
     * Arama sonuçlarını AI context formatına çevir
     *
     * @param array $results Arama sonuçları
     * @return string AI'ın anlayacağı formatta context
     */
    public function buildContextForAI(array $results): string;

    /**
     * Bu modül için quick action butonlarını getir
     *
     * @return array Quick action tanımları
     */
    public function getQuickActions(): array;

    /**
     * Kullanıcı mesajından kategori/filtre tespit et
     *
     * @param string $message Kullanıcı mesajı
     * @return array|null Tespit edilen filtreler
     */
    public function detectFilters(string $message): ?array;

    /**
     * Bu modül için system prompt kurallarını getir
     *
     * @return string Modüle özgü prompt kuralları
     */
    public function getPromptRules(): string;

    /**
     * Modül tipini döndür
     *
     * @return string shop, content, booking, info, music, custom
     */
    public function getModuleType(): string;

    /**
     * Modül adını döndür (görüntüleme için)
     *
     * @return string
     */
    public function getModuleName(): string;
}

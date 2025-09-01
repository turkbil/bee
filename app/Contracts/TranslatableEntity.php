<?php

namespace App\Contracts;

/**
 * 🌍 UNIVERSAL TRANSLATABLE ENTITY INTERFACE
 * 
 * Her modül bu interface'i implement ederek çeviri sistemine dahil olur.
 * Page modülü pattern'i temel alınarak tasarlandı.
 */
interface TranslatableEntity
{
    /**
     * Çevrilebilir alanları döndür
     * 
     * @return array Örnek:
     * [
     *     'title' => 'text',        // Basit metin çevirisi
     *     'body' => 'html',         // HTML korunarak çeviri
     *     'description' => 'text',  // Basit metin çevirisi
     *     'slug' => 'auto'          // Otomatik oluştur (title'dan)
     * ]
     */
    public function getTranslatableFields(): array;

    /**
     * SEO desteği var mı?
     * 
     * @return bool true ise SEO alanları da çevrilir
     */
    public function hasSeoSettings(): bool;

    /**
     * Çeviri sonrası ek işlemler
     * 
     * @param string $targetLanguage Hedef dil
     * @param array $translatedData Çevrilen veriler
     * @return void
     */
    public function afterTranslation(string $targetLanguage, array $translatedData): void;

    /**
     * Primary key field adı
     * 
     * @return string Örnek: 'page_id', 'portfolio_id', 'id'
     */
    public function getPrimaryKeyName(): string;
}
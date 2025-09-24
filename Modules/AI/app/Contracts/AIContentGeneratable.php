<?php

declare(strict_types=1);

namespace Modules\AI\app\Contracts;

/**
 * AI Content Generatable Interface
 *
 * Bu interface'i implement eden sınıflar AI content generation yeteneği kazanır.
 * Module-agnostic tasarım ile tüm modüller için standart.
 */
interface AIContentGeneratable
{
    /**
     * AI ile içerik üret
     */
    public function generateAIContent(array $params): array;

    /**
     * Modül adını al
     */
    public function getModuleName(): string;

    /**
     * Entity tipini al
     */
    public function getEntityType(): string;

    /**
     * Hedef alanları al
     */
    public function getTargetFields(array $params): array;

    /**
     * Modül talimatlarını al
     */
    public function getModuleInstructions(): string;
}
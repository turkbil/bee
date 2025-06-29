<?php

namespace App\Contracts;

interface ThemeRepositoryInterface
{
    /**
     * Aktif temayı getirir
     */
    public function getActiveTheme(): ?object;
    
    /**
     * Tenant için temayı getirir
     */
    public function getThemeForTenant(string $tenantId): ?object;
    
    /**
     * Default temayı getirir
     */
    public function getDefaultTheme(): ?object;
    
    /**
     * Tema cache'ini temizler
     */
    public function clearThemeCache(?string $tenantId = null): void;
    
    /**
     * Tema varsa true döner
     */
    public function themeExists(string $themeName): bool;
}
<?php

namespace App\Services\Media;

use Illuminate\Support\Str;
use Spatie\MediaLibrary\Support\FileNamer\FileNamer;

/**
 * ASCII-safe file namer for Spatie Media Library
 * Türkçe karakterleri ve diğer özel karakterleri temizler
 */
class SafeFileNamer extends FileNamer
{
    public function originalFileName(string $fileName): string
    {
        // Extension'ı ayır (Spatie otomatik ekleyecek)
        $nameWithoutExtension = pathinfo($fileName, PATHINFO_FILENAME);

        // ASCII-safe'e çevir ve döndür (extension Spatie tarafından eklenecek)
        return $this->sanitizeFileName($nameWithoutExtension);
    }

    public function conversionFileName(string $fileName, \Spatie\MediaLibrary\Conversions\Conversion $conversion): string
    {
        // Extension'ı ayır
        $nameWithoutExtension = pathinfo($fileName, PATHINFO_FILENAME);

        // ASCII-safe'e çevir
        $safeName = $this->sanitizeFileName($nameWithoutExtension);

        // Extension ekleme - Spatie otomatik ekleyecek
        return "{$safeName}-{$conversion->getName()}";
    }

    public function responsiveFileName(string $fileName): string
    {
        // Extension'ı ayır
        $nameWithoutExtension = pathinfo($fileName, PATHINFO_FILENAME);

        // ASCII-safe'e çevir (extension Spatie tarafından eklenecek)
        return $this->sanitizeFileName($nameWithoutExtension);
    }

    /**
     * Dosya adını ASCII-safe hale getir
     */
    protected function sanitizeFileName(string $fileName): string
    {
        // Türkçe karakterleri değiştir
        $turkishChars = ['ş', 'Ş', 'ı', 'İ', 'ğ', 'Ğ', 'ü', 'Ü', 'ö', 'Ö', 'ç', 'Ç'];
        $englishChars = ['s', 'S', 'i', 'I', 'g', 'G', 'u', 'U', 'o', 'O', 'c', 'C'];
        $fileName = str_replace($turkishChars, $englishChars, $fileName);

        // Laravel slug helper kullan (UTF-8'i ASCII'ye çevirir)
        $fileName = Str::slug($fileName, '-', 'en');

        // Boşsa fallback
        if (empty($fileName)) {
            $fileName = 'file-' . uniqid();
        }

        return $fileName;
    }
}

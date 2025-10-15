<?php

namespace App\Services;

use Spatie\MediaLibrary\Support\UrlGenerator\DefaultUrlGenerator;

class TenantUrlGenerator extends DefaultUrlGenerator
{
    public function getUrl(): string
    {
        // Temel URL'yi al
        $url = config('app.url');

        // Storage linkini ekle
        $url .= '/storage';

        // Medya ID'sini al
        $mediaId = $this->media->id;

        // Conversion varsa conversions klasörü ve suffix ekle
        if ($this->conversion) {
            $conversionName = $this->conversion->getName();
            $pathinfo = pathinfo($this->media->file_name);

            // Conversion formatını al (webp, jpg, vs)
            $extension = $this->conversion->getResultExtension($pathinfo['extension']);

            // Dosya adı: original-name-thumb.webp
            $fileName = $pathinfo['filename'] . '-' . $conversionName . '.' . $extension;

            // URL: /storage/35/conversions/mavi-bos-thumb.webp
            // NOT: tenant{id} yok - StorageController otomatik algılıyor
            return $url . '/' . $mediaId . '/conversions/' . $fileName;
        }

        // Orijinal dosya
        $fileName = $this->media->file_name;

        // Tam URL'yi oluştur - tenant{id} olmadan
        // StorageController domain'e göre otomatik tenant klasörünü bulur
        return $url . '/' . $mediaId . '/' . $fileName;
    }

    public function getPath(): string
    {
        // Medya koleksiyonunun adını al 
        $mediaDirectory = $this->media->id;
        
        // Dosya adını al
        $fileName = $this->media->file_name;
        
        // Tam yolu döndür - path burada önemli değil, URL için kullanılıyor asıl
        return $mediaDirectory . '/' . $fileName;
    }
}
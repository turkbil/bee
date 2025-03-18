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
        
        // Tam yolu al
        $path = $this->getPath();
        
        // URL'yi oluştur
        return $url . '/' . $path;
    }

    public function getPath(): string
    {
        // Tenant ID'yi al - tenant prefix'ini bir kere kullanıyoruz
        $tenantId = 'tenant' . tenant('id');
        
        // Medya koleksiyonunun adını al 
        $mediaDirectory = $this->media->id;
        
        // Dosya adını al
        $fileName = $this->media->file_name;
        
        // Tam yolu döndür
        return $tenantId . '/' . $mediaDirectory . '/' . $fileName;
    }
}
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
        
        // Tenant ID'yi al - tenant yoksa, central için de tenant1 formatında URL üret
        $tenantId = app(\Stancl\Tenancy\Tenancy::class)->initialized ? tenant('id') : '1';
        
        // Medya ID'sini ve dosya adını al
        $mediaId = $this->media->id;
        $fileName = $this->media->file_name;
        
        // Tam URL'yi oluştur - hem tenant hem central için tenant{id} formatında
        return $url . '/tenant' . $tenantId . '/' . $mediaId . '/' . $fileName;
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
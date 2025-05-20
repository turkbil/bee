<?php

namespace Modules\WidgetManagement\app\Http\Livewire\WidgetManage;

trait ImageHandlerTrait
{
    // Geçici resim yüklendiğinde önizleme oluştur
    public function updatedThumbnail()
    {
        $this->validateOnly('thumbnail', [
            'thumbnail' => 'image|max:3072'
        ]);
        
        if ($this->thumbnail) {
            $this->imagePreview = $this->thumbnail->temporaryUrl();
        }
    }
    
    // Çoklu resim ekle
    public function addMultipleImageField()
    {
        if (!isset($this->temporaryMultipleImages)) {
            $this->temporaryMultipleImages = [];
        }
        
        $this->temporaryMultipleImages[] = null;
    }
    
    // Çoklu resim için güncelleme metodu
    public function updatedTemporaryMultipleImages($value, $key)
    {
        // Doğrulama yap
        $this->validateOnly("temporaryMultipleImages.{$key}", [
            "temporaryMultipleImages.{$key}" => ['image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
        ]);
        
        // Değişiklik olduğunu işaretle
        $this->isSubmitting = false;
    }
    
    // Çoklu resim sil
    public function removeMultipleImageField($index)
    {
        if (isset($this->temporaryMultipleImages[$index])) {
            unset($this->temporaryMultipleImages[$index]);
            // Boşlukları temizle
            $this->temporaryMultipleImages = array_values($this->temporaryMultipleImages);
        }
    }
}
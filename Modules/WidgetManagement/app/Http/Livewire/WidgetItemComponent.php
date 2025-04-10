<?php

namespace Modules\WidgetManagement\app\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Modules\WidgetManagement\app\Models\TenantWidget;
use Modules\WidgetManagement\app\Models\WidgetItem;
use Modules\WidgetManagement\app\Services\WidgetItemService;
use Illuminate\Support\Str;

#[Layout('admin.layout')]
class WidgetItemComponent extends Component
{
    use WithFileUploads;
    
    public $tenantWidgetId;
    public $tenantWidget = null;
    public $items = [];
    public $formMode = false;
    public $formData = [];
    public $currentItemId = null;
    public $schema = [];
    public $isStaticWidget = false;
    
    // Tek dosya resim yüklemeleri için
    public $temporaryImages = [];
    
    // Çoklu dosya yüklemeleri
    public $tempPhoto;
    public $photoField; // Hangi alan için yüklüyoruz
    public $photos = [];
    
    protected $itemService;
    
    protected $listeners = [
        'updateItemOrder',
        'itemOrderUpdated' => 'updateItemOrder',
        'addWidgetItem' => 'addItem',
        'editWidgetItem' => 'editItem',
        'deleteWidgetItem' => 'deleteItem'
    ];
    
    public function boot(WidgetItemService $itemService)
    {
        $this->itemService = $itemService;
    }
    
    public function mount($tenantWidgetId)
    {
        $this->tenantWidgetId = $tenantWidgetId;
        $this->loadItems();
        
        $this->tenantWidget = TenantWidget::with('widget')->findOrFail($tenantWidgetId);
        $this->schema = $this->tenantWidget->widget->getItemSchema();
        
        // Widget tipini belirle - önemli değişken
        $this->isStaticWidget = $this->tenantWidget->widget->type === 'static';
        
        // Her zaman title ve is_active alanları olmalı - bunlar değiştirilemez
        $hasTitle = false;
        $hasActive = false;

        if (is_array($this->schema)) {
            foreach ($this->schema as $field) {
                if ($field['name'] === 'title') $hasTitle = true;
                if ($field['name'] === 'is_active') $hasActive = true;
            }
        } else {
            $this->schema = [];
        }
        
        // Title alanı yoksa ekle
        if (!$hasTitle) {
            $this->schema = array_merge([[
                'name' => 'title',
                'label' => 'Başlık',
                'type' => 'text',
                'required' => true,
                'system' => true // Sistem alanı olduğunu belirt
            ]], $this->schema ?? []);
        }
        
        // Aktif/Pasif alanı yoksa ekle
        if (!$hasActive) {
            $this->schema[] = [
                'name' => 'is_active',
                'label' => 'Aktif',
                'type' => 'checkbox',
                'required' => false,
                'system' => true // Sistem alanı olduğunu belirt
            ];
        }
        
        // formData'yı başlat
        $this->initFormData();
        
        // Statik bileşen kontrolü
        if ($this->isStaticWidget) {
            // Öğe yoksa otomatik bir içerik öğesi oluştur
            if ($this->items->isEmpty()) {
                $content = [
                    'title' => $this->tenantWidget->settings['title'] ?? $this->tenantWidget->widget->name,
                    'is_active' => true,
                    'unique_id' => (string) Str::uuid()
                ];
                
                $this->itemService->addItem($this->tenantWidgetId, $content);
                $this->loadItems();
            }
            
            // Statik widget için doğrudan düzenleme moduna geç
            if ($this->items->isNotEmpty()) {
                $this->currentItemId = $this->items->first()->id;
                $this->formData = $this->items->first()->content;
                $this->formMode = true;
            }
        }
    }
    
    public function loadItems()
    {
        $this->items = $this->itemService->getItemsForWidget($this->tenantWidgetId);
    }
    
    public function updatedTempPhoto()
    {
        if ($this->tempPhoto && $this->photoField) {
            // Çoklu fotoğraf yükleme için dizi kontrolü
            $photosToProcess = is_array($this->tempPhoto) ? $this->tempPhoto : [$this->tempPhoto];
            
            foreach ($photosToProcess as $photo) {
                $this->validate([
                    'tempPhoto.*' => 'image|max:2048', // 2MB Max
                ]);
                
                if (!isset($this->photos[$this->photoField])) {
                    $this->photos[$this->photoField] = [];
                }
                
                $this->photos[$this->photoField][] = $photo;
            }
            
            $this->tempPhoto = null;
        }
    }
    
    public function setPhotoField($fieldName)
    {
        $this->photoField = $fieldName;
    }
    
    public function removePhoto($fieldName, $index)
    {
        if (isset($this->photos[$fieldName]) && isset($this->photos[$fieldName][$index])) {
            unset($this->photos[$fieldName][$index]);
            $this->photos[$fieldName] = array_values($this->photos[$fieldName]);
        }
    }
    
    public function removeExistingMultipleImage($fieldName, $index)
    {
        if (isset($this->formData[$fieldName]) && is_array($this->formData[$fieldName]) && isset($this->formData[$fieldName][$index])) {
            // Dosya silme işlemi
            if (function_exists('is_tenant') && is_tenant()) {
                \Modules\SettingManagement\App\Helpers\TenantStorageHelper::deleteFile($this->formData[$fieldName][$index]);
            }
            
            // Diziden kaldır
            unset($this->formData[$fieldName][$index]);
            $this->formData[$fieldName] = array_values($this->formData[$fieldName]);
        }
    }
    
    // Yardımcı metod
    private function hasField($fieldName)
    {
        if (empty($this->schema)) return false;
        
        foreach ($this->schema as $field) {
            if ($field['name'] === $fieldName) {
                return true;
            }
        }
        
        return false;
    }

    public function toggleItemActive($itemId)
    {
        $item = WidgetItem::findOrFail($itemId);
        $content = $item->content;
        
        // Aktif/pasif durumunu tersine çevirme
        $content['is_active'] = !($content['is_active'] ?? false);
        
        // İçerik güncelleme
        $this->itemService->updateItem($itemId, $content);
        
        // Öğeleri yeniden yükle
        $this->loadItems();
        
        $this->dispatch('toast', [
            'title' => 'Başarılı!',
            'message' => 'İçerik durumu ' . ($content['is_active'] ? 'aktif' : 'pasif') . ' olarak güncellendi.',
            'type' => 'success'
        ]);
    }

    protected function initFormData()
    {
        $this->formData = [];
        $this->temporaryImages = [];
        $this->photos = [];
        $this->tempPhoto = null;
        $this->photoField = null;
        
        if (!empty($this->schema)) {
            foreach ($this->schema as $field) {
                if ($field['type'] === 'checkbox') {
                    $this->formData[$field['name']] = false;
                } elseif ($field['type'] === 'image_multiple') {
                    $this->formData[$field['name']] = [];
                } else {
                    $this->formData[$field['name']] = '';
                }
            }
        }
        
        // Varsayılan olarak aktif
        if (isset($this->formData['is_active'])) {
            $this->formData['is_active'] = true;
        }
    }
    
    public function addItem()
    {
        // Statik widget kontrolü - zaten bir öğe varsa eklemeye izin verme
        if ($this->isStaticWidget && $this->items->isNotEmpty()) {
            $this->dispatch('toast', [
                'title' => 'Uyarı!',
                'message' => 'Statik bileşenler sadece bir içerik öğesine sahip olabilir.',
                'type' => 'warning'
            ]);
            
            // Var olan tek öğeyi düzenle
            $existingItem = $this->items->first();
            $this->currentItemId = $existingItem->id;
            $this->formData = $existingItem->content;
            $this->formMode = true;
            return;
        }
        
        $this->formMode = true;
        $this->currentItemId = null;
        $this->initFormData();
    }
    
    public function editItem($itemId)
    {
        $item = WidgetItem::findOrFail($itemId);
        $this->currentItemId = $item->id;
        $this->formData = $item->content;
        $this->formMode = true;
    }
    
    public function deleteItem($itemId)
    {
        try {
            // Statik bileşen kontrolü - tek öğe varsa silinemez
            if ($this->isStaticWidget && $this->items->count() <= 1) {
                $this->dispatch('toast', [
                    'title' => 'Hata!',
                    'message' => 'Statik bileşenin tek içerik öğesi silinemez.',
                    'type' => 'error'
                ]);
                return;
            }
            
            $this->itemService->deleteItem($itemId);
            
            $this->loadItems();
            
            $this->dispatch('toast', [
                'title' => 'Başarılı!',
                'message' => 'Öğe başarıyla silindi.',
                'type' => 'success'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => 'Öğe silinirken bir hata oluştu: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }
    
    public function cancelForm()
    {
        // Statik widget'lar için formdan çıkmaya izin verme, her zaman düzenleme modunda kal
        if ($this->isStaticWidget) {
            $this->dispatch('toast', [
                'title' => 'Bilgi',
                'message' => 'Statik bileşenlerde içerik düzenleme modundan çıkılamaz.',
                'type' => 'info'
            ]);
            return;
        }
        
        $this->formMode = false;
        $this->currentItemId = null;
        $this->initFormData();
    }
    
    public function updateItemOrder($items)
    {
        // Sadece geçerli bir item dizisi ise devam et
        if (!is_array($items) || empty($items)) {
            return;
        }
        
        try {
            $this->itemService->reorderItems($this->tenantWidgetId, $items);
            
            $this->loadItems();
            
            $this->dispatch('toast', [
                'title' => 'Başarılı!',
                'message' => 'Öğeler başarıyla sıralandı.',
                'type' => 'success'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => 'Öğeler sıralanırken bir hata oluştu: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }
    
    // Medya silme işlemi
    public function deleteMedia($fieldName)
    {
        if (isset($this->formData[$fieldName]) && !empty($this->formData[$fieldName])) {
            // Dosya silme işlemi
            if (function_exists('is_tenant') && is_tenant()) {
                \Modules\SettingManagement\App\Helpers\TenantStorageHelper::deleteFile($this->formData[$fieldName]);
            }
            
            $this->formData[$fieldName] = null;
        }
    }
    
    // Resim kaldırma işlemi
    public function removeImage($imageKey)
    {
        unset($this->temporaryImages[$imageKey]);
    }
    
    public function saveItem()
    {
        // Form verilerini doğrula
        $rules = [];
        
        foreach ($this->schema as $field) {
            if (isset($field['required']) && $field['required']) {
                $rules['formData.' . $field['name']] = 'required';
            }
            
            if ($field['type'] === 'image' && isset($this->temporaryImages[$field['name']])) {
                $rules['temporaryImages.' . $field['name']] = 'image|max:2048';
            }
        }
        
        $this->validate($rules);
        
        try {
            // Benzersiz ID otomatik ekle - kullanıcı görmesin
            if (!isset($this->formData['unique_id'])) {
                $this->formData['unique_id'] = (string) Str::uuid();
            }
            
            // Dosyaları ve görselleri yükle
            foreach ($this->schema as $field) {
                // Resim veya dosya tipi için
                if (($field['type'] === 'image' || $field['type'] === 'file') && 
                    isset($this->temporaryImages[$field['name']]) && 
                    $this->temporaryImages[$field['name']]) {
                    
                    $file = $this->temporaryImages[$field['name']];
                    $filename = time() . '_' . Str::slug($field['name']) . '_' . $file->getClientOriginalName();
                    
                    // Tenant ID belirleme
                    $tenantId = is_tenant() ? tenant_id() : 1;
                    
                    // Klasör belirleme - resim veya dosya tipine göre
                    $folder = $field['type'] === 'image' ? 'images' : 'files';
                    
                    // SettingManagement'taki TenantStorageHelper ile doğru şekilde dosyayı yükle
                    try {
                        $urlPath = \Modules\SettingManagement\App\Helpers\TenantStorageHelper::storeTenantFile(
                            $file,
                            "widgets/{$folder}",
                            $filename,
                            $tenantId
                        );
                        
                        $this->formData[$field['name']] = $urlPath;
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error('Dosya yükleme hatası: ' . $e->getMessage(), [
                            'field' => $field['name'],
                            'type' => $field['type'],
                            'tenant' => $tenantId,
                            'exception' => $e
                        ]);
                    }
                }
                
                // Çoklu resim tipi için işlem
                if ($field['type'] === 'image_multiple' && isset($this->photos[$field['name']]) && count($this->photos[$field['name']]) > 0) {
                    $fieldName = $field['name'];
                    
                    // Eğer mevcut fotoğraflar yoksa, boş array oluştur
                    if (!isset($this->formData[$fieldName]) || !is_array($this->formData[$fieldName])) {
                        $this->formData[$fieldName] = [];
                    }
                    
                    // Yeni fotoğrafları ekle
                    foreach ($this->photos[$fieldName] as $index => $photo) {
                        // Tenant ID belirleme
                        $tenantId = is_tenant() ? tenant_id() : 1;
                        $filename = time() . '_' . Str::slug($fieldName) . '_' . $index . '_' . $photo->getClientOriginalName();
                        
                        // Dosyayı yükle
                        try {
                            $urlPath = \Modules\SettingManagement\App\Helpers\TenantStorageHelper::storeTenantFile(
                                $photo,
                                "widgets/images",
                                $filename,
                                $tenantId
                            );
                            
                            $this->formData[$fieldName][] = $urlPath;
                        } catch (\Exception $e) {
                            \Illuminate\Support\Facades\Log::error('Çoklu resim yükleme hatası: ' . $e->getMessage(), [
                                'field' => $fieldName,
                                'index' => $index,
                                'tenant' => $tenantId,
                                'exception' => $e
                            ]);
                        }
                    }
                }
            }
            
            if ($this->currentItemId) {
                // Mevcut öğeyi güncelle
                $this->itemService->updateItem($this->currentItemId, $this->formData);
                $successMessage = 'İçerik güncellendi.';
            } else {
                // Yeni öğe ekle
                $this->itemService->addItem($this->tenantWidgetId, $this->formData);
                $successMessage = 'Yeni içerik eklendi.';
            }
            
            $this->loadItems();
            
            // Geçici yükleme dizilerini temizle
            $this->photos = [];
            $this->temporaryImages = [];
            
            // Statik widget ise içerik düzenleme modunda kal
            if ($this->isStaticWidget) {
                if ($this->items->isNotEmpty()) {
                    $this->currentItemId = $this->items->first()->id;
                    $this->formData = $this->items->first()->content;
                    $this->formMode = true;
                }
            } else {
                // Dinamik widget ise form modundan çık
                $this->formMode = false;
                $this->currentItemId = null;
                $this->initFormData(); // Formu temizle
            }
            
            $this->dispatch('toast', [
                'title' => 'Başarılı!',
                'message' => $successMessage,
                'type' => 'success'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => 'İçerik kaydedilirken bir hata oluştu: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }
    
    public function render()
    {
        return view('widgetmanagement::livewire.widget-item-component');
    }
}
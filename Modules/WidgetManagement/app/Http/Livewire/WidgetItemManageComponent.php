<?php

namespace Modules\WidgetManagement\app\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Modules\WidgetManagement\app\Models\TenantWidget;
use Modules\WidgetManagement\app\Models\WidgetItem;
use Modules\WidgetManagement\app\Services\WidgetItemService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

#[Layout('admin.layout')]
class WidgetItemManageComponent extends Component
{
    use WithFileUploads;
    
    public $tenantWidgetId;
    public $itemId = null;
    public $tenantWidget = null;
    public $formData = [];
    public $schema = [];
    public $isStaticWidget = false;
    
    // Tek dosya resim yüklemeleri için
    public $temporaryImages = [];
    
    // Çoklu dosya yüklemeleri
    public $tempPhoto;
    public $photoField; // Hangi alan için yüklüyoruz
    public $photos = [];
    
    protected $itemService;
    
    public function boot(WidgetItemService $itemService)
    {
        $this->itemService = $itemService;
    }
    
    public function mount($tenantWidgetId = null, $itemId = null)
    {
        Log::info("WidgetItemManageComponent::mount", [
            'tenantWidgetId' => $tenantWidgetId,
            'itemId' => $itemId
        ]);
        
        $this->tenantWidgetId = $tenantWidgetId;
        $this->itemId = $itemId;
        
        try {
            // Her zaman önce TenantWidget bilgilerini yükle
            if ($tenantWidgetId) {
                $this->tenantWidget = TenantWidget::with('widget')->findOrFail($tenantWidgetId);
                
                // Widget null değilse şema bilgilerini al
                if ($this->tenantWidget && $this->tenantWidget->widget) {
                    $this->schema = $this->tenantWidget->widget->getItemSchema();
                    
                    // Widget tipini belirle - önemli değişken
                    $this->isStaticWidget = $this->tenantWidget->widget->type === 'static';
                } else {
                    // Widget yoksa boş şema ve varsayılan değerler
                    $this->schema = [];
                    $this->isStaticWidget = false;
                    
                    Log::error("Widget bulunamadı veya TenantWidget'a bağlı değil: {$tenantWidgetId}");
                    session()->flash('error', 'Widget bulunamadı veya TenantWidget\'a bağlı değil.');
                    return redirect()->route('admin.widgetmanagement.index');
                }
            }
            
            // Eğer itemId varsa (düzenleme modu), öğe verilerini yükle
            if ($itemId) {
                $item = WidgetItem::findOrFail($itemId);
                Log::info("Item bulundu:", ['item' => $item->toArray()]);
                
                // TenantWidget henüz yüklenmedi veya hatalıysa yükle
                if (!$this->tenantWidget || !$this->tenantWidget->widget) {
                    $this->tenantWidgetId = $item->tenant_widget_id;
                    $this->tenantWidget = TenantWidget::with('widget')->findOrFail($item->tenant_widget_id);
                    
                    // Widget null kontrolü
                    if (!$this->tenantWidget->widget) {
                        Log::error("Widget bulunamadı: TenantWidget ID {$item->tenant_widget_id}");
                        session()->flash('error', 'Widget bulunamadı.');
                        return redirect()->route('admin.widgetmanagement.index');
                    }
                    
                    $this->schema = $this->tenantWidget->widget->getItemSchema();
                    $this->isStaticWidget = $this->tenantWidget->widget->type === 'static';
                }
                
                // İçerik verilerini form verilerine aktar
                $this->formData = $item->content;
                Log::info("Form verileri yüklendi:", ['formData' => $this->formData]);
            }
            
            // Her zaman title ve is_active alanları olmalı - bunlar değiştirilemez
            $hasTitle = false;
            $hasActive = false;
            $hasUniqueId = false;

            if (is_array($this->schema)) {
                foreach ($this->schema as $field) {
                    // Güvenli kontrol - name anahtarı yoksa atla
                    if (!isset($field['name'])) continue;
                    
                    // Şimdi güvenli bir şekilde kontrol edebiliriz
                    if ($field['name'] === 'title') $hasTitle = true;
                    if ($field['name'] === 'is_active') $hasActive = true;
                    if ($field['name'] === 'unique_id') $hasUniqueId = true;
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
                    'system' => true
                ]], $this->schema ?? []);
            }
            
            // Aktif/Pasif alanı yoksa ekle
            if (!$hasActive) {
                $this->schema[] = [
                    'name' => 'is_active',
                    'label' => 'Aktif',
                    'type' => 'checkbox',
                    'required' => false,
                    'system' => true
                ];
            }
            
            // Unique ID alanı yoksa ekle
            if (!$hasUniqueId) {
                $this->schema[] = [
                    'name' => 'unique_id',
                    'label' => 'Benzersiz ID',
                    'type' => 'text',
                    'required' => false,
                    'system' => true,
                    'hidden' => true
                ];
            }
            
            // Eğer formData boşsa, başlangıç değerleri oluştur
            if (empty($this->formData)) {
                $this->initFormData();
            }
            
            // Statik widget durumunda, eğer düzenleme değilse, mevcut öğeyi al
            if ($this->isStaticWidget && !$this->itemId) {
                $items = $this->itemService->getItemsForWidget($this->tenantWidgetId);
                
                if ($items->isNotEmpty()) {
                    $this->itemId = $items->first()->id;
                    $this->formData = $items->first()->content;
                }
            }
        } catch (\Exception $e) {
            Log::error("WidgetItemManageComponent mount hatası: " . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'İçerik yüklenirken bir hata oluştu: ' . $e->getMessage());
        }
    }
    
    public function updatedTempPhoto()
    {
        if ($this->tempPhoto && $this->photoField) {
            // Çoklu fotoğraf yükleme için dizi kontrolü
            $photosToProcess = is_array($this->tempPhoto) ? $this->tempPhoto : [$this->tempPhoto];
            
            foreach ($photosToProcess as $photo) {
                $this->validate([
                    'tempPhoto.*' => 'image|max:3072', // 3MB Max
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
    
    protected function initFormData()
    {
        $this->formData = [];
        $this->temporaryImages = [];
        $this->photos = [];
        $this->tempPhoto = null;
        $this->photoField = null;
        
        if (!empty($this->schema)) {
            foreach ($this->schema as $field) {
                // Satır tipi elementleri atla veya özel işle
                if (!isset($field['name']) || !isset($field['type'])) continue;
                if ($field['name'] === 'unique_id') continue;
                if ($field['type'] === 'row') continue; // Satır tipi elementleri atla
                
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
    
    public function save($redirect = false, $resetForm = false)
    {
        // Form verilerini doğrula
        $rules = [];
        
        foreach ($this->schema as $field) {
            // Satır tipi elementleri veya geçersiz alanları atla
            if (!isset($field['name']) || !isset($field['type'])) continue;
            if ($field['type'] === 'row') continue; // Satır tipi elementleri atla
            
            if (isset($field['required']) && $field['required'] && $field['name'] !== 'unique_id' && $field['type'] !== 'image') {
                $rules['formData.' . $field['name']] = 'required';
            }
            
            if ($field['type'] === 'image' && isset($this->temporaryImages[$field['name']])) {
                $rules['temporaryImages.' . $field['name']] = 'image|max:3072'; // 3MB
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
                // Satır tipi elementleri veya geçersiz alanları atla
                if (!isset($field['name']) || !isset($field['type'])) continue;
                if ($field['type'] === 'row') continue; // Satır tipi elementleri atla
                
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
                if (isset($field['type']) && $field['type'] === 'image_multiple' && isset($field['name']) && isset($this->photos[$field['name']]) && count($this->photos[$field['name']]) > 0) {
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
            
            if ($this->itemId) {
                // Mevcut öğeyi güncelle
                $this->itemService->updateItem($this->itemId, $this->formData);
                $successMessage = 'İçerik güncellendi.';
            } else {
                // Yeni öğe ekle
                $this->itemService->addItem($this->tenantWidgetId, $this->formData);
                $successMessage = 'Yeni içerik eklendi.';
            }
            
            // Geçici yükleme dizilerini temizle
            $this->photos = [];
            $this->temporaryImages = [];
            
            if ($redirect) {
                session()->flash('toast', [
                    'title' => 'Başarılı!',
                    'message' => $successMessage,
                    'type' => 'success'
                ]);
                return redirect()->route('admin.widgetmanagement.items', $this->tenantWidgetId);
            }
            
            $this->dispatch('toast', [
                'title' => 'Başarılı!',
                'message' => $successMessage,
                'type' => 'success'
            ]);
            
            if ($resetForm && !$this->itemId) {
                $this->reset();
            }
            
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
        return view('widgetmanagement::livewire.widget-item-manage-component');
    }
}
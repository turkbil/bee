<?php
// Modules/SettingManagement/app/Http/Livewire/ValuesComponent.php

namespace Modules\SettingManagement\App\Http\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Modules\SettingManagement\App\Models\Setting;
use Modules\SettingManagement\App\Models\SettingValue;
use Modules\SettingManagement\App\Models\SettingGroup;
use Illuminate\Database\Eloquent\Builder;
use Modules\MediaManagement\App\Services\ThumbnailManager;
use Modules\SettingManagement\app\Http\Livewire\Traits\WithBulkActionsQueue;

#[Layout('admin.layout')]
class ValuesComponent extends Component
{
    use WithPagination, WithFileUploads, WithBulkActionsQueue;
    
    public $groupId;
    public $values = [];
    public $originalValues = [];
    public $changes = [];
    public $group;
    public $temporaryImages = [];
    public $temporaryMultipleImages = [];
    public $multipleImagesArrays = [];
    public $pendingImages = []; // Yüklenen dosyaları saklar
    protected ?string $settingValueConnection = null;
    
    public $tempPhoto;
    public $photoField; // Hangi alan için yüklüyoruz

    protected function getListeners()
    {
        return array_merge([
            'refreshComponent' => '$refresh',
        ], $this->getBulkListeners());
    }

    protected function getModelClass()
    {
        return SettingValue::class;
    }

    public function syncSwitchValue($key, $value)
    {
        $setting = Setting::where('key', $key)->first();
        if (!$setting) {
            return;
        }

        // Switch için normalize
        if ($setting->type === 'switch' && is_null($value)) {
            $value = false;
        }

        // Hem numeric ID hem string key'e kaydet
        $this->values[$setting->id] = $value;
        $this->values[$setting->key] = $value;

        $this->checkChanges();
    }

    public function updatedValues($value, $key)
    {
        // Setting'i bul
        $setting = null;

        // String key ise numeric ID'ye çevir ve sync et
        if (!is_numeric($key)) {
            $setting = Setting::where('key', $key)->first();
            if ($setting) {
                // Switch type için null değeri normalize et
                if ($setting->type === 'switch' && is_null($value)) {
                    $value = false;
                }

                $numericKey = $setting->id;
                $this->values[$numericKey] = $value;
                $this->values[$key] = $value; // String key'i de güncelle
            }
        } else {
            // Numeric key ise string key'i de güncelle
            $setting = Setting::find($key);
            if ($setting) {
                // Switch type için null değeri normalize et
                if ($setting->type === 'switch' && is_null($value)) {
                    $value = false;
                }

                $this->values[$setting->key] = $value;
                $this->values[$key] = $value;
            }
        }

        if ($key !== 'temp') {
            $this->checkChanges();
        }
    }

    public function mount($group)
    {
        $this->groupId = $group;
        $this->group = SettingGroup::findOrFail($group);

        $settings = Setting::where('group_id', $this->groupId)->get();

        foreach ($settings as $setting) {
            $value = $this->settingValueQuery()
                ->where('setting_id', $setting->id)
                ->first();

            // Normal değerler için
            $finalValue = $value ? $value->value : $setting->default_value;

            // UTF-8 sanitization
            if (is_string($finalValue) && !mb_check_encoding($finalValue, 'UTF-8')) {
                $finalValue = mb_convert_encoding($finalValue, 'UTF-8', 'UTF-8');
            }

            // Hem numeric ID hem string key ile kaydet (layout sistem için)
            $this->values[$setting->id] = $finalValue;
            $this->values[$setting->key] = $finalValue;
            $this->originalValues[$setting->id] = $finalValue;
            $this->originalValues[$setting->key] = $finalValue;


            // Çoklu resim için JSON'ı diziye çevir
            if ($setting->type === 'image_multiple') {
                try {
                    if (isset($this->values[$setting->id]) && !empty($this->values[$setting->id])) {
                        $this->multipleImagesArrays[$setting->id] = json_decode($this->values[$setting->id], true) ?: [];
                    } else {
                        $this->multipleImagesArrays[$setting->id] = [];
                    }
                } catch (\Exception $e) {
                    $this->multipleImagesArrays[$setting->id] = [];
                }
            }
        }

        // ✅ Layout sisteminde switch element'leri için değerleri normalize et
        $this->normalizeLayoutSwitchValues();

        // Livewire için values'u hydrate et
        $this->dispatch('valuesLoaded', $this->values);
    }

    public function resetToDefault($settingId)
    {
        $setting = Setting::find($settingId);
        $this->values[$settingId] = $setting->default_value;
        
        // Eğer ayar türü çoklu resim ise
        if ($setting->type === 'image_multiple') {
            try {
                if (!empty($setting->default_value)) {
                    $this->multipleImagesArrays[$settingId] = json_decode($setting->default_value, true) ?: [];
                } else {
                    $this->multipleImagesArrays[$settingId] = [];
                }
            } catch (\Exception $e) {
                $this->multipleImagesArrays[$settingId] = [];
            }
        }
        
        $this->checkChanges();
    }


    public function updatedTemporaryImages($value, $key)
    {
        $parts = explode('.', $key);
        $settingId = $parts[0] ?? null;
        
        if ($settingId && isset($this->temporaryImages[$settingId])) {
            $setting = Setting::find($settingId);
            
            if ($setting) {
                // Dosyayı pendingImages array'ine kaydet
                $this->pendingImages[$settingId] = $this->temporaryImages[$settingId];
                
                $this->values[$settingId] = 'temp'; // Geçici değer, dosya yüklendiğinde gerçek path ile değiştirilecek
                $this->checkChanges();
            }
        }
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
                
                if (!isset($this->temporaryMultipleImages[$this->photoField])) {
                    $this->temporaryMultipleImages[$this->photoField] = [];
                }
                
                $this->temporaryMultipleImages[$this->photoField][] = $photo;
            }
            
            $this->tempPhoto = null;
            
            // Değişiklik var olarak işaretle
            if (!isset($this->changes[$this->photoField])) {
                $this->changes[$this->photoField] = true;
            }
        }
    }
    
    public function setPhotoField($fieldName)
    {
        $this->photoField = $fieldName;
    }
    
    // Çoklu resim ekle
    public function addMultipleImageField($settingId)
    {
        if (!isset($this->temporaryMultipleImages[$settingId])) {
            $this->temporaryMultipleImages[$settingId] = [];
        }
        
        // Değişiklik olarak kaydet
        if (!isset($this->changes[$settingId])) {
            $this->changes[$settingId] = true;
        }
        
        $this->checkChanges();
    }
    
    // Çoklu resim sil (yeni eklenenler için)
    public function removeMultipleImageField($settingId, $index)
    {
        if (isset($this->temporaryMultipleImages[$settingId][$index])) {
            unset($this->temporaryMultipleImages[$settingId][$index]);
            // Boşlukları temizle
            $this->temporaryMultipleImages[$settingId] = array_values($this->temporaryMultipleImages[$settingId]);
        }
        $this->checkChanges();
    }
    
    // Çoklu resim sil (mevcut resimler için)
    public function removeMultipleImage($settingId, $index)
    {
        if (isset($this->multipleImagesArrays[$settingId][$index])) {
            $setting = Setting::find($settingId);
            $imagePath = $this->multipleImagesArrays[$settingId][$index];

            // ✅ SPATIE: Path'den media bul ve sil
            try {
                $media = $setting->getMedia('gallery')->first(function($m) use ($imagePath) {
                    return str_contains($imagePath, $m->file_name);
                });

                if ($media) {
                    $media->delete();
                }
            } catch (\Exception $e) {
                \Log::warning("Multiple image silme hatası", ['error' => $e->getMessage()]);
            }

            // Diziden çıkar
            unset($this->multipleImagesArrays[$settingId][$index]);
            // Diziye yeniden sırala
            $this->multipleImagesArrays[$settingId] = array_values($this->multipleImagesArrays[$settingId]);

            // Eğer dizide hiç eleman kalmazsa varsayılan değer kullan
            if (empty($this->multipleImagesArrays[$settingId])) {
                $this->values[$settingId] = null;
            } else {
                // JSON'a çevir
                $this->values[$settingId] = json_encode($this->multipleImagesArrays[$settingId]);
            }

            // Değişikliği kaydet
            $this->checkChanges();
        }
    }
    
    public function checkChanges()
    {
        $this->changes = [];
        foreach ($this->values as $id => $value) {
            $originalValue = $this->originalValues[$id] ?? null;
            if ($value == 'temp' || $value != $originalValue) {
                $this->changes[$id] = $value;
            }
        }
        
        // Çoklu resim için de kontrol et
        foreach ($this->temporaryMultipleImages as $settingId => $images) {
            if (!empty($images)) {
                $this->changes[$settingId] = true;
            }
        }
    }

    public function save($redirect = false, $resetForm = false)
    {
        // ✅ Save işlemi öncesi switch değerlerini normalize et
        $this->normalizeLayoutSwitchValuesBeforeSave();
        $this->normalizeAllSwitchValuesBeforeSave();

        foreach ($this->values as $settingId => $value) {
            // String key'leri filtrele (sadece numeric ID'leri işle)
            if (!is_numeric($settingId)) {
                continue;
            }

            $setting = Setting::find($settingId);

            // Setting bulunamazsa bir sonraki iterasyona geç
            if (!$setting) {
                continue;
            }

            $oldValue = $this->originalValues[$settingId] ?? null;

            // File/Image dosya yüklemelerini işle
            if (isset($this->temporaryImages[$settingId])) {
                $file = $this->temporaryImages[$settingId];
                $type = $setting->type;

                // Normalize brand assets so oversized logos are not stored untouched
                $this->normalizeSettingImageIfNeeded($setting, $file);

                try {
                    // ✅ SPATIE: Setting model'in attachSettingMedia() metodu kullan
                    // Eski medyayı otomatik temizler, yeni medyayı ekler
                    $setting->attachSettingMedia($file);

                    // Medya URL'sini al ve value olarak kaydet
                    $value = $setting->getMediaUrl();
                    $this->values[$settingId] = $value;
                } catch (\Exception $e) {
                    $this->dispatch('toast', [
                        'title' => __('settingmanagement.messages.error'),
                        'message' => __('settingmanagement.messages.file_upload_error') . $e->getMessage(),
                        'type' => 'error',
                    ]);
                    continue;
                }
            }

            // Çoklu resim tipi için işlem
            if ($setting->type === 'image_multiple' && isset($this->temporaryMultipleImages[$settingId]) && count($this->temporaryMultipleImages[$settingId]) > 0) {
                try {
                    $newImages = [];

                    // Eğer mevcut resimler varsa, onları koru
                    if (isset($this->multipleImagesArrays[$settingId]) && !empty($this->multipleImagesArrays[$settingId])) {
                        $newImages = $this->multipleImagesArrays[$settingId];
                    }

                    // ✅ SPATIE: Yeni resimleri gallery collection'a ekle
                    foreach ($this->temporaryMultipleImages[$settingId] as $index => $photo) {
                        if ($photo) {
                            $media = $setting->addMedia($photo)
                                ->toMediaCollection('gallery');

                            // Media URL'sini diziye ekle
                            $newImages[] = $media->getUrl();
                        }
                    }

                    // Yeni değeri JSON olarak ata
                    if (!empty($newImages)) {
                        $value = json_encode($newImages);
                        $this->values[$settingId] = $value;
                        $this->multipleImagesArrays[$settingId] = $newImages;
                    } else {
                        // Tüm resimler silindi
                        $value = null;
                        $this->values[$settingId] = null;
                        $this->multipleImagesArrays[$settingId] = [];
                    }
                } catch (\Exception $e) {
                    $this->dispatch('toast', [
                        'title' => __('settingmanagement.messages.error'),
                        'message' => __('settingmanagement.messages.multi_image_upload_error') . $e->getMessage(),
                        'type' => 'error',
                    ]);
                    continue;
                }
            }

            // Önce değişiklik kontrolü yap
            if ($oldValue !== $value) {
                // Eğer yeni değer default değere eşitse, setting value'yu sil
                if ($value === $setting->default_value) {
                    // ✅ SPATIE: Medya varsa temizle
                    if ($oldValue && ($setting->type === 'file' || $setting->type === 'image')) {
                        $setting->clearMediaCollection($setting->getMediaCollectionName());
                    }

                    $this->settingValueQuery()
                        ->where('setting_id', $settingId)
                        ->delete();

                    log_activity(
                        $setting,
                        __('settingmanagement.actions.reset_to_default'),
                        ['old' => $oldValue, 'new' => $value]
                    );
                } else {
                    // Değer değişti ve default değil, kaydet
                    $settingValue = $this->settingValueQuery()->updateOrCreate(
                        ['setting_id' => $settingId],
                        ['value' => $value]
                    );

                    log_activity(
                        $setting,
                        __('settingmanagement.actions.value_updated'),
                        ['old' => $oldValue, 'new' => $value]
                    );
                }
            }
        }

        // ✅ originalValues'u güncelle (hem numeric hem string key'ler için)
        foreach ($this->values as $key => $value) {
            $this->originalValues[$key] = $value;
        }

        // ✅ State'i temizle
        $this->changes = [];
        $this->temporaryImages = [];
        $this->temporaryMultipleImages = [];

        // ✅ BULK CACHE: Settings cache'ini yenile (anında yeni değerler aktif olsun)
        try {
            app(\App\Services\SettingsService::class)->refreshCache();
        } catch (\Exception $e) {
            \Log::warning("Settings cache refresh hatası: " . $e->getMessage());
        }

        // ✅ Redirect handling
        if ($redirect) {
            session()->flash('success', __('settingmanagement.messages.values_saved'));
            return redirect()->route('admin.settingmanagement.index');
        }

        // ✅ Success toast (Kaydet ve Devam Et için)
        $this->dispatch('toast', [
            'title' => __('settingmanagement.messages.success'),
            'message' => __('settingmanagement.messages.values_saved'),
            'type' => 'success'
        ]);

        // ✅ Component'i refresh et (form state'ini temizle)
        $this->dispatch('refreshComponent');
    }
    
    public function removeImage($settingId)
    {
        if (isset($this->temporaryImages[$settingId])) {
            unset($this->temporaryImages[$settingId]);
            $this->checkChanges();
            
            $this->dispatch('toast', [
                'title' => __('settingmanagement.messages.success'),
                'message' => __('settingmanagement.messages.file_removed'),
                'type' => 'success'
            ]);
        }
    }
    
    public function deleteMedia($settingId)
    {
        $setting = Setting::find($settingId);
        $value = $this->values[$settingId] ?? null;

        if ($setting && $value) {
            // ✅ SPATIE: Media collection'ı temizle
            $setting->clearMediaCollection($setting->getMediaCollectionName());

            $this->values[$settingId] = null;
            $this->checkChanges();

            $this->dispatch('toast', [
                'title' => __('settingmanagement.messages.success'),
                'message' => __('settingmanagement.messages.file_deleted'),
                'type' => 'success'
            ]);
        }
    }
    

    protected function resolveSettingValueConnection(): string
    {
        if ($this->settingValueConnection) {
            return $this->settingValueConnection;
        }
        
        $connection = (function_exists('is_tenant') && is_tenant())
            ? 'tenant'
            : config('tenancy.database.central_connection', config('database.default'));
        
        if (! $connection) {
            $connection = config('database.default');
        }
        
        return $this->settingValueConnection = $connection;
    }
    
    protected function settingValueQuery(): Builder
    {
        return SettingValue::on($this->resolveSettingValueConnection());
    }
    
    private function normalizeSettingImageIfNeeded(Setting $setting, $file): void
    {
        if (! $file instanceof \Illuminate\Http\UploadedFile) {
            return;
        }

        $profile = $this->getThumbnailProfileForSetting($setting);
        if (! $profile) {
            return;
        }

        /** @var ThumbnailManager $thumbnail */
        $thumbnail = app(ThumbnailManager::class);
        $thumbnail->applyToUploadedFile($file, $profile);
    }

    private function getThumbnailProfileForSetting(Setting $setting): ?string
    {
        $map = config('mediamanagement.thumbmaker.setting_profiles', [
            'site_logo' => 'logo',
            'site_kontrast_logo' => 'logo',
        ]);

        return $map[$setting->key] ?? null;
    }

    public function render()
    {
        $settings = Setting::where('group_id', $this->groupId)
            ->orderBy('sort_order', 'asc')
            ->get();

        // UTF-8 sanitization for all values to prevent JSON encoding errors
        $this->sanitizeValuesForJson();

        return view('settingmanagement::livewire.values-component', [
            'settings' => $settings
        ]);
    }

    /**
     * Sanitize all component values to ensure valid UTF-8 encoding
     * This prevents "Malformed UTF-8 characters" errors during Livewire JSON serialization
     */
    private function sanitizeValuesForJson(): void
    {
        foreach ($this->values as $key => $value) {
            if (is_string($value) && !mb_check_encoding($value, 'UTF-8')) {
                // Remove invalid UTF-8 characters
                $this->values[$key] = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
            }
        }

        foreach ($this->originalValues as $key => $value) {
            if (is_string($value) && !mb_check_encoding($value, 'UTF-8')) {
                $this->originalValues[$key] = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
            }
        }
    }

    /**
     * Layout sisteminde switch element'leri için değerleri normalize et (mount için)
     */
    private function normalizeLayoutSwitchValues(): void
    {
        if (!isset($this->group->layout['elements']) || !is_array($this->group->layout['elements'])) {
            return;
        }

        $switchElements = $this->findSwitchElementsInLayout($this->group->layout['elements']);

        foreach ($switchElements as $element) {
            $elementName = $element['properties']['name'] ?? null;
            if (!$elementName) {
                continue;
            }

            // Setting'i bul (string key ile)
            $setting = Setting::where('group_id', $this->groupId)
                ->where('key', $elementName)
                ->first();

            if (!$setting) {
                continue;
            }

            // Değeri boolean'a normalize et
            $currentValue = $this->values[$setting->key] ?? $this->values[$setting->id] ?? null;
            $normalizedValue = $this->normalizeBooleanValue($currentValue);

            // Hem numeric ID hem string key için güncelle
            $this->values[$setting->id] = $normalizedValue;
            $this->values[$setting->key] = $normalizedValue;
            $this->originalValues[$setting->id] = $normalizedValue;
            $this->originalValues[$setting->key] = $normalizedValue;
        }
    }

    /**
     * Save işlemi öncesi switch değerlerini normalize et
     */
    private function normalizeLayoutSwitchValuesBeforeSave(): void
    {
        if (!isset($this->group->layout['elements']) || !is_array($this->group->layout['elements'])) {
            return;
        }

        $switchElements = $this->findSwitchElementsInLayout($this->group->layout['elements']);

        foreach ($switchElements as $element) {
            $elementName = $element['properties']['name'] ?? null;
            if (!$elementName) {
                continue;
            }

            // Setting'i bul (string key ile)
            $setting = Setting::where('group_id', $this->groupId)
                ->where('key', $elementName)
                ->first();

            if (!$setting) {
                continue;
            }

            // Livewire'dan gelen değer (checkbox işaretli değilse null gelir)
            $liveValue = $this->values[$elementName] ?? $this->values[$setting->id] ?? null;

            // Checkbox işaretli değilse (null) → false yap
            // Checkbox işaretli ise (true, 1, "1", "true") → true yap
            $normalizedValue = $this->normalizeBooleanValue($liveValue);

            // String olarak sakla (veritabanı için)
            $stringValue = $normalizedValue ? 'true' : 'false';

            // Hem numeric ID hem string key için güncelle
            $this->values[$setting->id] = $stringValue;
            $this->values[$setting->key] = $stringValue;
        }
    }

    /**
     * Layout içindeki tüm switch element'leri bul (recursive)
     */
    private function findSwitchElementsInLayout(array $elements): array
    {
        $switches = [];

        foreach ($elements as $element) {
            if (isset($element['type']) && $element['type'] === 'switch') {
                $switches[] = $element;
            }

            // Row içindeki column'ları kontrol et
            if (isset($element['columns']) && is_array($element['columns'])) {
                foreach ($element['columns'] as $column) {
                    if (isset($column['elements']) && is_array($column['elements'])) {
                        $switches = array_merge($switches, $this->findSwitchElementsInLayout($column['elements']));
                    }
                }
            }

            // Nested elements varsa onları da kontrol et
            if (isset($element['elements']) && is_array($element['elements'])) {
                $switches = array_merge($switches, $this->findSwitchElementsInLayout($element['elements']));
            }
        }

        return $switches;
    }

    /**
     * Değeri boolean'a normalize et
     */
    private function normalizeBooleanValue($value): bool
    {
        if (is_null($value)) {
            return false;
        }

        if (is_bool($value)) {
            return $value;
        }

        if (is_string($value)) {
            $lower = strtolower(trim($value));
            return in_array($lower, ['true', '1', 'yes', 'on'], true);
        }

        if (is_numeric($value)) {
            return (bool) $value;
        }

        return false;
    }

    /**
     * Tüm switch type ayarlar için değerleri normalize et (save öncesi)
     */
    private function normalizeAllSwitchValuesBeforeSave(): void
    {
        $settings = Setting::where('group_id', $this->groupId)
            ->where('type', 'switch')
            ->get();

        foreach ($settings as $setting) {
            // Livewire'dan gelen değer (checkbox işaretli değilse null gelir)
            $liveValue = $this->values[$setting->key] ?? $this->values[$setting->id] ?? null;

            // Checkbox işaretli değilse (null) → "0" yap
            // Checkbox işaretli ise (true, 1, "1", "true") → "1" yap
            $normalizedValue = $this->normalizeBooleanValue($liveValue);

            // String olarak sakla (veritabanı için): "1" veya "0"
            $stringValue = $normalizedValue ? '1' : '0';

            // Hem numeric ID hem string key için güncelle
            $this->values[$setting->id] = $stringValue;
            $this->values[$setting->key] = $stringValue;
        }
    }
}
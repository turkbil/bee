<?php

namespace Modules\SettingManagement\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Stancl\Tenancy\Database\Concerns\CentralConnection;
use Cviebrock\EloquentSluggable\Sluggable;
use Spatie\MediaLibrary\HasMedia;
use Modules\MediaManagement\App\Traits\HasMediaManagement;

class Setting extends Model implements HasMedia
{
    use CentralConnection, Sluggable, HasMediaManagement;
    
    protected $table = 'settings';

    protected $fillable = [
        'group_id',
        'label',
        'key',
        'type',
        'options',
        'default_value',
        'sort_order',
        'is_active',
        'is_system',
        'is_required',
    ];
    
    protected $casts = [
        'options' => 'array',
        'is_active' => 'boolean',
        'is_system' => 'boolean',
        'is_required' => 'boolean',
    ];

    /**
     * Get attributes for array conversion (used by Livewire serialization)
     * Sanitize all string attributes to prevent UTF-8 encoding errors
     */
    public function attributesToArray()
    {
        $attributes = parent::attributesToArray();

        // Sanitize all string attributes
        foreach ($attributes as $key => $value) {
            if (is_string($value) && !mb_check_encoding($value, 'UTF-8')) {
                $attributes[$key] = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
            }
        }

        return $attributes;
    }
    
    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable(): array
    {
        // Anahtar (key) alanı artık ManageComponent içinde manuel olarak oluşturulduğu için
        // Sluggable paketinin bu alanı otomatik yönetmesini istemiyoruz.
        return [];
    }
    
    public function group(): BelongsTo
    {
        return $this->belongsTo(SettingGroup::class, 'group_id');
    }

    public function values(): HasMany
    {
        return $this->hasMany(SettingValue::class, 'setting_id');
    }

    public function getValue()
    {
        // Image/File/Favicon type'lar için önce media library'yi kontrol et
        if (in_array($this->type, ['image', 'file', 'favicon'])) {
            // Media URL'i varsa döndür (media tenant database'de)
            $mediaUrl = $this->getMediaUrl();
            if ($mediaUrl) {
                return $mediaUrl;
            }
        }

        // SettingValue tenant database'de olduğu için
        // tenant connection'ı kullanarak sorgu yapmalıyız

        // Tenant context varsa tenant DB'den çek
        if (tenant()) {
            $settingValue = SettingValue::on('tenant')
                ->where('setting_id', $this->id)
                ->first();

            if ($settingValue && $settingValue->value !== null) {
                return $settingValue->value;
            }
        }

        // Hiç değer yoksa default_value kullan
        return $this->default_value;
    }

    /**
     * Register media collections for this setting
     * Override HasMediaManagement trait method
     */
    public function registerMediaCollections(): void
    {
        // Setting type'ına göre collection tanımla
        if (!in_array($this->type, ['image', 'file', 'favicon'])) {
            return;
        }

        $collection = $this->addMediaCollection('featured_image')
            ->singleFile();

        // MIME types - Setting type ve key'ine göre özel tanımlar
        // Favicon için MIME type kontrolü yok (browser extension kontrolü yapıyor)
        $mimeTypes = $this->getMimeTypesForSetting();
        if (!empty($mimeTypes)) {
            $collection->acceptsMimeTypes($mimeTypes);
        }
        // Boş array ise (favicon gibi), tüm dosya tipleri kabul edilir
    }

    /**
     * Get allowed MIME types based on setting type and key
     */
    public function getMimeTypesForSetting(): array
    {
        // Favicon type için MIME type kontrolü yok (Mac/Windows .ico farklılıkları için)
        // Extension kontrolü browser'da yapılıyor: accept=".ico,.png"
        if ($this->type === 'favicon' || $this->key === 'site_favicon') {
            return []; // Tüm dosyaları kabul et, browser extension kontrolü yapacak
        }

        // Image type için standart image MIME types
        if ($this->type === 'image') {
            return [
                'image/jpeg',
                'image/jpg',
                'image/png',
                'image/webp',
                'image/gif',
                'image/svg+xml',
            ];
        }

        // File type için document MIME types
        if ($this->type === 'file') {
            return [
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ];
        }

        return [];
    }

    /**
     * Media collections tanımla (HasMediaManagement trait için)
     */
    protected function getMediaCollectionsConfig(): array
    {
        // Setting type'ına göre collection belirle
        $collections = [];

        // Type'a göre uygun collection ekle (hep featured_image kullan)
        switch ($this->type) {
            case 'image':
            case 'file':
            case 'favicon':
                $collections['featured_image'] = [
                    'type' => $this->type === 'image' ? 'image' : ($this->type === 'favicon' ? 'image' : 'document'),
                    'single_file' => true,
                    'max_items' => 1,
                    'conversions' => ($this->type === 'image' || $this->type === 'favicon') ? ['thumb'] : [],
                    'sortable' => false,
                ];
                break;
        }

        return $collections;
    }

    /**
     * Setting için media collection adını belirle
     * UniversalMediaComponent sadece featured_image destekliyor, o yüzden hep onu kullan
     */
    public function getMediaCollectionName(): string
    {
        // UniversalMediaComponent için featured_image kullan
        return 'featured_image';
    }

    /**
     * Setting'in media URL'sini al
     */
    public function getMediaUrl(): ?string
    {
        if (!in_array($this->type, ['image', 'file', 'favicon'])) {
            return null;
        }

        $collection = $this->getMediaCollectionName();
        return $this->getFirstMediaUrl($collection);
    }

    /**
     * Setting'e media attach et
     */
    public function attachSettingMedia($file): void
    {
        if (!in_array($this->type, ['image', 'file', 'favicon'])) {
            return;
        }

        $collection = $this->getMediaCollectionName();

        // Eski medyayı temizle (singleFile olduğu için)
        $this->clearMediaCollection($collection);

        // Yeni medyayı ekle
        $this->addMedia($file)
            ->toMediaCollection($collection);
    }
}
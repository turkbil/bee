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
    use CentralConnection, Sluggable;
    use HasMediaManagement {
        registerMediaCollections as traitRegisterMediaCollections;
    }

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

        // ⚡ PERFORMANCE FIX: Use eager loaded 'values' relation if available
        // This prevents N+1 query problem (700+ queries → 0 queries)
        if (tenant()) {
            // If 'values' relation is already loaded, use it (no extra query)
            if ($this->relationLoaded('values')) {
                $settingValue = $this->values->first();
            } else {
                // Fallback: Query database (only if not eager loaded)
                $settingValue = SettingValue::on('tenant')
                    ->where('setting_id', $this->id)
                    ->first();
            }

            if ($settingValue && $settingValue->value !== null) {
                return $settingValue->value;
            }
        }

        // Hiç değer yoksa default_value kullan
        return $this->default_value;
    }

    /**
     * Spatie Media Library için disk belirleme (tenant-aware)
     * Bu method media kaydolmadan önce çağrılır
     *
     * ⚠️ NOT: Setting model CentralConnection kullanır ama disk tenant-aware olmalı
     * Bu yüzden tenant context'i request'ten belirliyoruz
     */
    public function getMediaDisk(?string $collectionName = null): string
    {
        // 1. Yöntem: tenant() helper (eğer tenancy initialized ise)
        $tenantId = null;
        if (function_exists('tenant') && tenant()) {
            $tenantId = tenant('id');
        }

        // 2. Yöntem: Request'ten domain çöz (fallback)
        if (!$tenantId && request()) {
            $host = request()->getHost();
            $centralDomains = config('tenancy.central_domains', []);

            // Central domain değilse tenant'ı bul
            if (!in_array($host, $centralDomains)) {
                try {
                    $domainModel = \Stancl\Tenancy\Database\Models\Domain::where('domain', $host)->first();
                    if ($domainModel && $domainModel->tenant_id) {
                        $tenantId = $domainModel->tenant_id;
                    }
                } catch (\Exception $e) {
                    // Fallback to public disk
                }
            }
        }

        // Tenant context varsa tenant disk kullan
        if ($tenantId) {
            // ✅ FIX: Her tenant için ayrı disk yerine tek 'tenant' disk kullan
            // Runtime'da doğru tenant için yapılandırılacak (Spatie Media Library)
            $diskName = 'tenant';

            // ⚠️ CRITICAL FIX: Tenancy package zaten suffix_storage_path=true ile
            // storage_path()'i otomatik prefix ediyor: storage/tenant{id}/
            // Bu yüzden manuel "tenant{$tenantId}/" EKLEMEMELIYIZ!
            //
            // ❌ YANLIŞ: storage_path("tenant{$tenantId}/app/public")
            //    → /storage/tenant2/ + tenant2/app/public = /storage/tenant2/tenant2/app/public
            //
            // ✅ DOĞRU: storage_path("app/public")
            //    → /storage/tenant2/app/public (tenancy package otomatik prefix ekler)
            $root = storage_path("app/public");

            // Directory yoksa oluştur
            if (!is_dir($root)) {
                @mkdir($root, 0775, true);
            }

            // 🔥 Request'ten gerçek URL al (config('app.url') yanlış domain döndürüyor!)
            $appUrl = request() ? request()->getSchemeAndHttpHost() : rtrim((string) config('app.url'), '/');

            config([
                'filesystems.disks.tenant' => [
                    'driver' => 'local',
                    'root' => $root,
                    'url' => $appUrl ? "{$appUrl}/storage/tenant{$tenantId}" : null,
                    'visibility' => 'public',
                    'throw' => false,
                ],
            ]);

            return $diskName;
        }

        // Central context için public disk
        return 'public';
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

        // Dynamic collection name (Setting key'ini kullan)
        $collectionName = $this->getMediaCollectionName();

        $collection = $this->addMediaCollection($collectionName)
            ->singleFile()
            ->useDisk($this->getMediaDisk()); // 🔥 Tenant disk kullan

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
        if ($this->type === 'favicon' || $this->key === 'site_favicon') {
            return []; // Tüm dosyaları kabul et
        }

        // Image type için image MIME types
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

        // File type için TÜM dosyaları kabul et (kullanıcı ne isterse yükleyebilmeli)
        // Image da yükleyebilir, PDF de, DOCX da - settings'te tanımlı type'a göre değil!
        if ($this->type === 'file') {
            return []; // Boş array = tüm MIME types kabul edilir
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

        // Dynamic collection name
        $collectionName = $this->getMediaCollectionName();

        // Type'a göre uygun collection ekle
        switch ($this->type) {
            case 'image':
            case 'file':
            case 'favicon':
                $collections[$collectionName] = [
                    'type' => $this->type === 'image' ? 'image' : ($this->type === 'favicon' ? 'image' : 'document'),
                    'single_file' => true,
                    'max_items' => 1,
                    'conversions' => ($this->type === 'image' || $this->type === 'favicon') ? ['thumb'] : [],
                    'sortable' => false,
                ];
                break;

            case 'image_multiple':
                // Gallery collection (multiple images)
                $collections['gallery'] = [
                    'type' => 'image',
                    'single_file' => false,
                    'max_items' => 20,
                    'conversions' => ['thumb'],
                    'sortable' => true,
                ];
                break;
        }

        return $collections;
    }

    /**
     * Setting için media collection adını belirle
     * Setting key'ini kullan (örn: site_logo → collection: site_logo)
     *
     * Bu sayede media library'de her Setting'in görseli
     * kendi adıyla saklanır ve kolayca ayırt edilebilir.
     */
    public function getMediaCollectionName(): string
    {
        // Setting key'ini collection name olarak kullan
        return $this->key ?: 'media';
    }

    /**
     * Setting'in media URL'sini al
     *
     * ⚠️ CRITICAL FIX: Media kayıtlarını tenant DB'den al
     */
    public function getMediaUrl(): ?string
    {
        if (!in_array($this->type, ['image', 'file', 'favicon'])) {
            return null;
        }

        $collection = $this->getMediaCollectionName();

        // ✅ FIX: Media'yı tenant context'te ara
        return $this->executeMediaOperationInTenantContext(function() use ($collection) {
            // Try standard Spatie method first
            try {
                $url = $this->getFirstMediaUrl($collection);
                if ($url) {
                    return $url;
                }
            } catch (\Exception $e) {
                // Ignore and try manual query
            }

            // Fallback: Manuel tenant DB query
            try {
                $media = \DB::connection('tenant')
                    ->table('media')
                    ->where('model_type', self::class)
                    ->where('model_id', $this->id)
                    ->where('collection_name', $collection)
                    ->orderBy('order_column')
                    ->first();

                if ($media && isset($media->id)) {
                    // Recreate URL from media record
                    $mediaModel = \App\Models\CustomMedia::on('tenant')->find($media->id);
                    if ($mediaModel) {
                        return $mediaModel->getUrl();
                    }
                }
            } catch (\Exception $e) {
                \Log::debug('getMediaUrl fallback failed', ['error' => $e->getMessage()]);
            }

            return null;
        });
    }

    /**
     * Setting'e media attach et
     *
     * ⚠️ CRITICAL FIX: Setting model CentralConnection kullanır ama
     * media kayıtları TENANT database'de olmalı!
     * Bu yüzden media'yı tenant context'te ekliyoruz.
     */
    public function attachSettingMedia($file): void
    {
        if (!in_array($this->type, ['image', 'file', 'favicon'])) {
            return;
        }

        $collection = $this->getMediaCollectionName();

        // ✅ FIX: Media işlemlerini tenant context'te yap
        $this->executeMediaOperationInTenantContext(function() use ($file, $collection) {
            // Eski medyayı temizle (singleFile olduğu için)
            $this->clearMediaCollection($collection);

            // Yeni medyayı ekle
            $this->addMedia($file)
                ->toMediaCollection($collection);
        });
    }

    /**
     * Media işlemlerini tenant context'te çalıştır
     *
     * Setting model central DB kullanır ama media tenant DB'de olmalı.
     * Bu method media işlemlerini geçici olarak tenant connection'a switch eder.
     */
    protected function executeMediaOperationInTenantContext(callable $callback)
    {
        // Mevcut connection'ı sakla
        $originalConnection = $this->getConnectionName();

        // Tenant context varsa tenant connection kullan
        if (function_exists('tenant') && tenant()) {
            // Geçici olarak connection'ı değiştir
            $this->setConnection('tenant');

            try {
                // Callback'i çalıştır
                $result = $callback();

                // Connection'ı geri al
                $this->setConnection($originalConnection);

                return $result;
            } catch (\Exception $e) {
                // Hata olursa connection'ı geri al
                $this->setConnection($originalConnection);
                throw $e;
            }
        }

        // Tenant context yoksa direkt çalıştır (central için)
        return $callback();
    }
}
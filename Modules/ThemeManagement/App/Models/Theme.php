<?php
namespace Modules\ThemeManagement\App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Stancl\Tenancy\Database\Concerns\CentralConnection;
use Cviebrock\EloquentSluggable\Sluggable;

class Theme extends BaseModel implements HasMedia
{
    use SoftDeletes, InteractsWithMedia, CentralConnection, Sluggable;

    protected $primaryKey = 'theme_id';

    protected $fillable = [
        'name',
        'title',
        'slug',
        'folder_name',
        'description',
        'is_active',
        'is_default',
        'available_for_tenants',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'available_for_tenants' => 'array',
    ];

    /**
     * Temanın belirli bir tenant için erişilebilir olup olmadığını kontrol et
     */
    public function isAvailableForTenant($tenantId): bool
    {
        $available = $this->available_for_tenants;

        // null veya boş = herkese açık
        if (empty($available)) {
            return true;
        }

        // ["all"] = herkese açık
        if (in_array('all', $available)) {
            return true;
        }

        // Tenant ID listesinde var mı?
        return in_array($tenantId, $available) || in_array((string)$tenantId, $available);
    }

    /**
     * Tenant için erişilebilir temaları getir
     */
    public static function availableForTenant($tenantId)
    {
        return static::where('is_active', true)
            ->get()
            ->filter(function ($theme) use ($tenantId) {
                return $theme->isAvailableForTenant($tenantId);
            });
    }

    /**
     * Erişim durumu özeti (badge için)
     */
    public function getAccessSummaryAttribute(): string
    {
        $available = $this->available_for_tenants;

        if (empty($available) || in_array('all', $available)) {
            return 'Herkese Açık';
        }

        $count = count($available);
        return $count . ' Tenant';
    }

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')
             ->singleFile()
             ->useDisk($this->getMediaDisk('images'));
    }

    /**
     * Spatie Media Library için disk belirleme (tenant-aware)
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
            $diskName = 'tenant';

            // ⚠️ CRITICAL FIX: Tenancy package zaten suffix_storage_path=true ile
            // storage_path()'i otomatik prefix ediyor: storage/tenant{id}/
            // Bu yüzden manuel "tenant{$tenantId}/" EKLEMEMELIYIZ!
            $root = storage_path("app/public");

            if (!is_dir($root)) {
                @mkdir($root, 0775, true);
            }

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
}
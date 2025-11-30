<?php

namespace Modules\SettingManagement\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Support\Str;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

class SettingGroup extends Model
{
    use SoftDeletes, LogsActivity, CentralConnection, Sluggable;

    protected $table = 'settings_groups';
    
    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'description',
        'icon',
        'meta_data',
        'is_active',
        'layout',
        'prefix'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'meta_data' => 'array',
        'layout' => 'json',
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
        return [
            'slug' => [
                'source' => 'name',
                'onUpdate' => true
            ]
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'is_active', 'description'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    protected static function boot()
    {
        parent::boot();
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(SettingGroup::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(SettingGroup::class, 'parent_id')->orderBy('id');
    }

    public function settings(): HasMany
    {
        return $this->hasMany(Setting::class, 'group_id');
    }

    /**
     * Layout accessor - Otomatik olarak select options formatını düzeltir
     * JavaScript beklenen format: [{value, label, is_default}]
     */
    public function getLayoutAttribute($value): ?array
    {
        $layout = is_string($value) ? json_decode($value, true) : $value;

        if (!$layout || !isset($layout['elements'])) {
            return $layout;
        }

        // Options formatını düzelt
        $layout['elements'] = $this->normalizeOptionsFormat($layout['elements']);

        return $layout;
    }

    /**
     * Recursive olarak tüm select elementlerinin options formatını düzeltir
     */
    protected function normalizeOptionsFormat(array $elements): array
    {
        foreach ($elements as &$el) {
            // Select veya radio elementi mi?
            if (isset($el['type']) && in_array($el['type'], ['select', 'radio'])) {
                $name = $el['properties']['name'] ?? null;
                $currentOptions = $el['properties']['options'] ?? null;

                // Options yoksa veya yanlış formattaysa settings'ten al
                if ($name && (!$currentOptions || !$this->isValidOptionsFormat($currentOptions))) {
                    $setting = Setting::where('key', $name)->first();

                    if ($setting && is_array($setting->options) && count($setting->options) > 0) {
                        $el['properties']['options'] = $this->convertToJsFormat($setting->options);
                    }
                } elseif ($currentOptions && !$this->isValidOptionsFormat($currentOptions)) {
                    // Mevcut options var ama format yanlış - düzelt
                    $el['properties']['options'] = $this->convertToJsFormat($currentOptions);
                }
            }

            // Row içindeki columns
            if (isset($el['columns'])) {
                foreach ($el['columns'] as &$col) {
                    if (isset($col['elements'])) {
                        $col['elements'] = $this->normalizeOptionsFormat($col['elements']);
                    }
                }
            }

            // Tab group içindeki tabs
            if (isset($el['tabs'])) {
                foreach ($el['tabs'] as &$tab) {
                    if (isset($tab['elements'])) {
                        $tab['elements'] = $this->normalizeOptionsFormat($tab['elements']);
                    }
                }
            }
        }

        return $elements;
    }

    /**
     * Options formatının doğru olup olmadığını kontrol eder
     * Doğru format: [{value: "x", label: "y"}]
     */
    protected function isValidOptionsFormat($options): bool
    {
        if (!is_array($options) || empty($options)) {
            return false;
        }

        $first = reset($options);
        return is_array($first) && isset($first['value']) && isset($first['label']);
    }

    /**
     * Herhangi bir options formatını JavaScript formatına çevirir
     * Input: {"key": "label"} veya ["string"] veya [{value, label}]
     * Output: [{value: "key", label: "label", is_default: bool}]
     */
    protected function convertToJsFormat($options): array
    {
        if (!is_array($options) || empty($options)) {
            return [];
        }

        // Zaten doğru formatta mı?
        $first = reset($options);
        if (is_array($first) && isset($first['value']) && isset($first['label'])) {
            return $options;
        }

        $result = [];
        $isFirst = true;

        foreach ($options as $key => $value) {
            if (is_numeric($key)) {
                // Indexed array: ["string1", "string2"]
                $result[] = [
                    'value' => (string)$key,
                    'label' => $value,
                    'is_default' => $isFirst
                ];
            } else {
                // Associative array: {"key": "label"}
                $result[] = [
                    'value' => $key,
                    'label' => $value,
                    'is_default' => $isFirst
                ];
            }
            $isFirst = false;
        }

        return $result;
    }

    /**
     * Get count of settings that have values (filled settings)
     * This counts actual SettingValue records, not just Setting definitions
     *
     * Note: Since Settings are in central DB and SettingValues are in tenant DB,
     * we need to manually check for values instead of using whereHas.
     */
    public function getFilledSettingsCountAttribute(): int
    {
        // Get all setting IDs for this group
        $settingIds = $this->settings()->pluck('id')->toArray();

        if (empty($settingIds)) {
            return 0;
        }

        // Count how many of these settings have values in the tenant database
        return SettingValue::whereIn('setting_id', $settingIds)
            ->distinct('setting_id')
            ->count('setting_id');
    }
}
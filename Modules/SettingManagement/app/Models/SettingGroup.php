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
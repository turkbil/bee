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
}
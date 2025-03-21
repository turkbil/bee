<?php

namespace Modules\SettingManagement\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

class Setting extends Model implements HasMedia
{
    use CentralConnection, InteractsWithMedia;
    
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
    ];

    protected $casts = [
        'options' => 'array',
        'is_active' => 'boolean',
    ];

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
        $settingValue = $this->values()->first();
        
        return $settingValue ? $settingValue->value : $this->default_value;
    }
    
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')
             ->singleFile()
             ->useDisk('public');
             
        $this->addMediaCollection('files')
             ->singleFile()
             ->useDisk('public');
    }
}
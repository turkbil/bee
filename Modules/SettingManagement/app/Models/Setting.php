<?php

namespace Modules\SettingManagement\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Stancl\Tenancy\Database\Concerns\CentralConnection;
use Cviebrock\EloquentSluggable\Sluggable;

class Setting extends Model
{
    use CentralConnection, Sluggable;
    
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
        $settingValue = $this->values()->first();
        
        return $settingValue ? $settingValue->value : $this->default_value;
    }
}
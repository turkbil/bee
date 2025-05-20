<?php

namespace Modules\SettingManagement\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SettingValue extends Model
{
    protected $table = 'settings_values';

    protected $fillable = [
        'setting_id',
        'value'
    ];
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * Tenant'da yabancı anahtarlar olmadığından ilişkileri manuel ayarlayacağız
     */
    public function setting(): BelongsTo
    {
        // settings_id'yi kullanarak central'daki settings tablosuna bağlan
        return $this->belongsTo(Setting::class, 'setting_id');
    }
}
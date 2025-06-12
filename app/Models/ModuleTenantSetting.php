<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModuleTenantSetting extends Model
{
    protected $fillable = [
        'module_name',
        'settings',
    ];

    protected $casts = [
        'settings' => 'array',
    ];
}
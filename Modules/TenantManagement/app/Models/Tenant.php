<?php
namespace Modules\TenantManagement\App\Models;

use App\Models\BaseModel;

class Tenant extends BaseModel
{
    protected $primaryKey = 'id'; // Varsayılan birincil anahtar
    protected $table      = 'tenants';

    protected $fillable = [
        'data',
        'is_active',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function getNameAttribute()
    {
        return $this->data['name'] ?? 'Bilinmeyen Ad';
    }

    public function domains()
    {
        return $this->hasMany(Domain::class, 'tenant_id', 'id');
    }

}

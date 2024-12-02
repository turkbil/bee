<?php // Modules/TenantManagement/App/Models/Domain.php

namespace Modules\TenantManagement\App\Models;

use Illuminate\Database\Eloquent\Model;

class Domain extends Model
{
    protected $table = 'domains';

    protected $fillable = ['domain', 'tenant_id'];

    // Tenant ile ilişki
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}

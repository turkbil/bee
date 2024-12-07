<?php // Modules/TenantManagement/App/Models/Domain.php

namespace Modules\TenantManagement\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Domain extends Model
{
    use SoftDeletes;

    protected $table = 'domains';

    protected $fillable = ['domain', 'tenant_id'];

    // Soft deletes için eklenen alan
    protected $dates = ['deleted_at'];

    /**
     * Tenant ile ilişki
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}

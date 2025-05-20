<?php

namespace Modules\WidgetManagement\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WidgetItem extends Model
{
    protected $fillable = [
        'tenant_widget_id', 'content', 'order'
    ];
    
    protected $casts = [
        'content' => 'json',
    ];
    
    /**
     * Öğenin ait olduğu tenant widget
     */
    public function tenantWidget(): BelongsTo
    {
        return $this->belongsTo(TenantWidget::class);
    }
}
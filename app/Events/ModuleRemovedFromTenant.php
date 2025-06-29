<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ModuleRemovedFromTenant
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    
    public int $moduleId;
    public string $tenantId;
    public array $moduleData;
    
    public function __construct(int $moduleId, string $tenantId, array $moduleData)
    {
        $this->moduleId = $moduleId;
        $this->tenantId = $tenantId;
        $this->moduleData = $moduleData;
    }
}
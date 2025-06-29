<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ModuleEnabled
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    
    public string $moduleName;
    public string $modulePath;
    
    public function __construct(string $moduleName, string $modulePath)
    {
        $this->moduleName = $moduleName;
        $this->modulePath = $modulePath;
    }
}
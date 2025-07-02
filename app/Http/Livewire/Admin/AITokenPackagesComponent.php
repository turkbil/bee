<?php

namespace App\Http\Livewire\Admin;

use Livewire\Attributes\Layout;
use Livewire\Component;
use App\Models\AITokenPackage;

#[Layout('admin.layout')]
class AITokenPackagesComponent extends Component
{
    public function render()
    {
        $packages = AITokenPackage::where('is_active', true)
            ->ordered()
            ->get();

        // Get current tenant's token info
        $tenant = tenant();
        $tokenInfo = [
            'balance' => $tenant ? $tenant->ai_tokens_balance : 1000,
            'monthly_used' => $tenant ? $tenant->ai_tokens_used_this_month : 250,
            'monthly_limit' => $tenant ? $tenant->ai_monthly_token_limit : 500,
            'ai_enabled' => $tenant ? $tenant->ai_enabled : true
        ];

        return view('livewire.admin.ai-token-packages-component', compact('packages', 'tokenInfo'));
    }
}
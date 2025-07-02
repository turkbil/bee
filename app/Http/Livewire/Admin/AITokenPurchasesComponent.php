<?php

namespace App\Http\Livewire\Admin;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\AITokenPurchase;

#[Layout('admin.layout')]
class AITokenPurchasesComponent extends Component
{
    use WithPagination;

    public function render()
    {
        $tenant = tenant();
        
        if (!$tenant) {
            // Central domain için boş veri göster
            $purchases = new \Illuminate\Pagination\LengthAwarePaginator(
                collect(),
                0,
                20,
                1,
                ['path' => request()->url(), 'pageName' => 'page']
            );
            $purchaseStats = [
                'total_purchases' => 0,
                'total_spent' => 0,
                'total_tokens_bought' => 0,
                'pending_purchases' => 0
            ];
        } else {
            $purchases = AITokenPurchase::where('tenant_id', $tenant->id)
                ->with('package')
                ->latest()
                ->paginate(20);

            $purchaseStats = [
                'total_purchases' => AITokenPurchase::where('tenant_id', $tenant->id)->count(),
                'total_spent' => AITokenPurchase::where('tenant_id', $tenant->id)->where('status', 'completed')->sum('price_paid'),
                'total_tokens_bought' => AITokenPurchase::where('tenant_id', $tenant->id)->where('status', 'completed')->sum('token_amount'),
                'pending_purchases' => AITokenPurchase::where('tenant_id', $tenant->id)->where('status', 'pending')->count()
            ];
        }

        return view('livewire.admin.ai-token-purchases-component', compact('purchases', 'purchaseStats'));
    }
}
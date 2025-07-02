<?php

namespace Modules\AI\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AITokenPackage;
use App\Models\AITokenPurchase;
use Illuminate\Http\Request;

class TokenController extends Controller
{
    /**
     * Purchase a token package
     */
    public function purchasePackage(Request $request, AITokenPackage $package)
    {
        $tenant = tenant();
        
        if (!$tenant) {
            return response()->json([
                'success' => false,
                'message' => 'Kiracı bilgisi bulunamadı.'
            ], 400);
        }

        if (!$package->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Bu paket artık satışta değil.'
            ], 400);
        }

        try {
            // Create purchase record
            $purchase = AITokenPurchase::create([
                'tenant_id' => $tenant->id,
                'package_id' => $package->id,
                'token_amount' => $package->token_amount,
                'price_paid' => $package->price,
                'currency' => $package->currency,
                'status' => 'completed', // Simulate instant completion for demo
                'purchased_at' => now(),
                'payment_method' => 'demo',
                'transaction_id' => 'demo_' . uniqid()
            ]);

            // Add tokens to tenant balance
            $tenant->increment('ai_tokens_balance', $package->token_amount);

            return response()->json([
                'success' => true,
                'message' => 'Token paketi başarıyla satın alındı! ' . number_format($package->token_amount) . ' token hesabınıza eklendi.',
                'purchase_id' => $purchase->id,
                'new_balance' => $tenant->fresh()->ai_tokens_balance
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Satın alma işlemi sırasında bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }
}
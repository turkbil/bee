<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Token sistemini Credit sistemine dönüştür
     */
    public function up(): void
    {
        // 1. AI Token Packages - Eksik alanları ekle
        Schema::table('ai_token_packages', function (Blueprint $table) {
            // Önce is_credit_based alanını ekle
            if (!Schema::hasColumn('ai_token_packages', 'is_credit_based')) {
                $table->boolean('is_credit_based')->after('price')->default(false)->comment('Credit tabanlı paket mi?');
            }
            
            // Sonra price_per_token alanını ekle
            if (!Schema::hasColumn('ai_token_packages', 'price_per_token')) {
                $table->decimal('price_per_token', 10, 6)->after('is_credit_based')->nullable()->comment('Legacy token başına fiyat');
            }
        });

        // 2. AI Token Purchases - Credit alanları zaten mevcut
        Schema::table('ai_token_purchases', function (Blueprint $table) {
            // Credit alanları zaten mevcut, sadece token_amount'u optional yap
            $table->bigInteger('token_amount')->nullable()->change();
            // tokens_used ve tokens_remaining alanları bu tabloda yok, o yüzden comment out
        });

        // 3. AI Token Usage - Credit alanları ekle
        Schema::table('ai_token_usage', function (Blueprint $table) {
            if (!Schema::hasColumn('ai_token_usage', 'credit_cost')) {
                $table->decimal('credit_cost', 8, 4)->after('tokens_used')->comment('İşlem kredi maliyeti');
            }
            if (!Schema::hasColumn('ai_token_usage', 'cost_breakdown')) {
                $table->text('cost_breakdown')->after('cost_multiplier')->nullable()->comment('Maliyet detayları JSON');
            }
            // cost_multiplier zaten var, provider_multiplier olarak kullanacağız
            
            // Token alanlarını optional yap
            $table->integer('tokens_used')->nullable()->change();
        });

        // 4. Mevcut token verilerini credit'e dönüştür
        $this->convertExistingTokenDataToCredits();
    }

    /**
     * Mevcut token verilerini credit sistemine dönüştür
     */
    private function convertExistingTokenDataToCredits(): void
    {
        // Token package'larını credit'e çevir
        $packages = DB::table('ai_token_packages')->get();
        foreach ($packages as $package) {
            if ($package->token_amount) {
                // 1 token = $0.0005 credit (ortalama hesaplama)
                $creditAmount = round($package->token_amount * 0.0005, 2);
                $pricePerCredit = $package->price / $creditAmount;
                
                DB::table('ai_token_packages')
                    ->where('id', $package->id)
                    ->update([
                        'credit_amount' => $creditAmount,
                        'price_per_credit' => $pricePerCredit,
                        'is_credit_based' => true,
                    ]);
            }
        }

        // Token purchase'ları credit'e çevir
        $purchases = DB::table('ai_token_purchases')->get();
        foreach ($purchases as $purchase) {
            if ($purchase->token_amount) {
                $creditAmount = round($purchase->token_amount * 0.0005, 2);
                // Bu tabloda tokens_used alanı yok, credit_used zaten var
                // Eğer mevcut credit_amount boş ise güncelle
                if (empty($purchase->credit_amount)) {
                    DB::table('ai_token_purchases')
                        ->where('id', $purchase->id)
                        ->update([
                            'credit_amount' => $creditAmount,
                            'credit_remaining' => $creditAmount - ($purchase->credit_used ?? 0),
                            'is_credit_purchase' => true,
                        ]);
                }
            }
        }

        // Token usage'ları credit'e çevir
        $usages = DB::table('ai_token_usage')->get();
        foreach ($usages as $usage) {
            if ($usage->tokens_used) {
                $creditCost = round($usage->tokens_used * 0.0005, 4);
                
                DB::table('ai_token_usage')
                    ->where('id', $usage->id)
                    ->update([
                        'credit_cost' => $creditCost,
                        'provider_name' => $usage->provider_name ?? 'legacy_token',
                        'cost_breakdown' => json_encode([
                            'tokens_used' => $usage->tokens_used,
                            'conversion_rate' => 0.0005,
                            'legacy_conversion' => true
                        ]),
                    ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Credit alanlarını kaldır
        Schema::table('ai_token_packages', function (Blueprint $table) {
            $table->dropColumn(['credit_amount', 'price_per_credit', 'is_credit_based', 'price_per_token']);
            $table->unsignedInteger('token_amount')->nullable(false)->change();
        });

        Schema::table('ai_token_purchases', function (Blueprint $table) {
            $table->dropColumn(['credit_amount', 'credit_used', 'credit_remaining', 'is_credit_purchase']);
            $table->bigInteger('token_amount')->nullable(false)->change();
            $table->bigInteger('tokens_used')->nullable(false)->change();
            $table->bigInteger('tokens_remaining')->nullable(false)->change();
        });

        Schema::table('ai_token_usage', function (Blueprint $table) {
            $table->dropColumn(['credit_cost', 'provider_name', 'provider_multiplier', 'cost_breakdown']);
            $table->integer('tokens_used')->nullable(false)->change();
        });
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('shop_loyalty_points')) {
            return;
        }

        Schema::create('shop_loyalty_points', function (Blueprint $table) {
            // Primary Key
            $table->id('loyalty_point_id');

            // Relations
            $table->foreignId('customer_id')->comment('Müşteri ID - shop_customers ilişkisi');

            // Points Balance
            $table->integer('total_earned')->default(0)->comment('Toplam kazanılan puan');
            $table->integer('total_spent')->default(0)->comment('Toplam harcanan puan');
            $table->integer('total_expired')->default(0)->comment('Toplam süresi dolan puan');
            $table->integer('current_balance')->default(0)->comment('Güncel bakiye (earned - spent - expired)');

            // Tier Info
            $table->foreignId('membership_tier_id')->nullable()->comment('Üyelik seviyesi ID - shop_membership_tiers ilişkisi');
            $table->decimal('points_multiplier', 5, 2)->default(1)->comment('Puan çarpanı (tier\'a göre)');

            // Lifetime Stats
            $table->decimal('lifetime_value', 14, 2)->default(0)->comment('Yaşam boyu değer (₺)');
            $table->integer('lifetime_orders')->default(0)->comment('Yaşam boyu sipariş sayısı');

            // Timestamps
            $table->timestamps();

            // Indexes

            $table->index('created_at');
            $table->index('updated_at');
            $table->unique('customer_id', 'unique_customer');
            $table->index('membership_tier_id', 'idx_tier');
            $table->index('current_balance', 'idx_balance');

            // Foreign Keys
            $table->foreign('customer_id')
                  ->references('customer_id')
                  ->on('shop_customers')
                  ->onDelete('cascade')
                  ->comment('Müşteri silinirse puanları da silinir');

            $table->foreign('membership_tier_id')
                  ->references('membership_tier_id')
                  ->on('shop_membership_tiers')
                  ->onDelete('cascade')
                  ->comment('Seviye silinirse ID null olur');
        })
        ->comment('Sadakat puanları - Müşteri puan bakiyeleri ve istatistikleri');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_loyalty_points');
    }
};

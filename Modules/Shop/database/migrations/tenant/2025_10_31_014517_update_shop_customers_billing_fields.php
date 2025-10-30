<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('shop_customers', function (Blueprint $table) {
            // Fatura tipi ekle
            $table->enum('billing_type', ['individual', 'corporate'])
                  ->default('individual')
                  ->after('customer_type')
                  ->comment('Fatura tipi: individual=Bireysel (TC), corporate=Kurumsal (VKN)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shop_customers', function (Blueprint $table) {
            $table->dropColumn('billing_type');
        });
    }
};

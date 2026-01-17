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
        Schema::table('cart_orders', function (Blueprint $table) {
            $table->string('invoice_number')->nullable()->after('order_number')->unique();
            $table->timestamp('invoice_generated_at')->nullable();
            $table->unsignedBigInteger('invoice_generated_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cart_orders', function (Blueprint $table) {
            $table->dropColumn(['invoice_number', 'invoice_generated_at', 'invoice_generated_by']);
        });
    }
};

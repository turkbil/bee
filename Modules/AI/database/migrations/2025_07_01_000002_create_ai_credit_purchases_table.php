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
        Schema::connection('central')->create('ai_credit_purchases', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('package_id');
            $table->unsignedInteger('credit_amount'); // Satın alınan credit miktarı
            $table->decimal('price_paid', 10, 2); // Ödenen tutar
            $table->decimal('amount', 10, 2)->default(0);
            $table->string('currency', 3)->default('TRY');
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');
            $table->string('payment_method')->nullable(); // "stripe", "paypal" vs.
            $table->string('payment_transaction_id')->nullable();
            $table->json('payment_data')->nullable(); // Ödeme detayları
            $table->text('notes')->nullable(); // Admin notları
            $table->timestamp('purchased_at')->nullable();
            $table->timestamps();
            
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('package_id')->references('id')->on('ai_credit_packages')->onDelete('cascade');
            
            $table->index(['tenant_id', 'status']);
            $table->index(['user_id', 'created_at']);
            $table->index(['status', 'created_at']);
            $table->index(['purchased_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('central')->dropIfExists('ai_credit_purchases');
    }
};
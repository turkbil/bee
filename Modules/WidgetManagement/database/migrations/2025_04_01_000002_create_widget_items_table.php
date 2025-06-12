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
        Schema::create('widget_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_widget_id');
            $table->json('content')->nullable();
            $table->integer('order')->default(0)->index();
            $table->timestamps();
            
            $table->foreign('tenant_widget_id')
                ->references('id')
                ->on('tenant_widgets')
                ->onDelete('cascade');
                
            // İlave indeksler
            $table->index('created_at');
            $table->index('updated_at');
            
            // Composite index'ler - Performans optimizasyonu
            $table->index(['tenant_widget_id', 'order'], 'widget_items_tenant_order_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('widget_items');
    }
};
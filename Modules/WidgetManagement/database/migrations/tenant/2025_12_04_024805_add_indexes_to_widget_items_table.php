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
        Schema::table('widget_items', function (Blueprint $table) {
            // Composite index (tenant_widget_id + order) - items sıralı getirme için
            $indexName = 'widget_items_tenant_widget_id_order_index';
            $indexExists = collect(DB::select("SHOW INDEX FROM widget_items WHERE Key_name = ?", [$indexName]))->isNotEmpty();

            if (!$indexExists) {
                $table->index(['tenant_widget_id', 'order'], $indexName);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('widget_items', function (Blueprint $table) {
            $indexName = 'widget_items_tenant_widget_id_order_index';
            $indexExists = collect(DB::select("SHOW INDEX FROM widget_items WHERE Key_name = ?", [$indexName]))->isNotEmpty();

            if ($indexExists) {
                $table->dropIndex($indexName);
            }
        });
    }
};

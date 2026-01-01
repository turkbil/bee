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
        Schema::table('tenant_widgets', function (Blueprint $table) {
            // 1. Position field ekle (widget'ın sayfadaki konumu)
            if (!Schema::hasColumn('tenant_widgets', 'position')) {
                $table->string('position', 100)->nullable()->after('order')->index();
            }

            // 2. widget_id için index (foreign key lookup hızlandırma)
            if (!Schema::hasColumn('tenant_widgets', 'widget_id')) {
                // Kolon zaten var ama index yok, sadece index ekle
                $table->index('widget_id', 'tenant_widgets_widget_id_index');
            }

            // 3. Composite index (widget_id + is_active) - sık kullanılan sorgu
            $indexName = 'tenant_widgets_widget_id_is_active_index';
            $indexExists = collect(DB::select("SHOW INDEX FROM tenant_widgets WHERE Key_name = ?", [$indexName]))->isNotEmpty();

            if (!$indexExists) {
                $table->index(['widget_id', 'is_active'], $indexName);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenant_widgets', function (Blueprint $table) {
            // Position field'i kaldır
            if (Schema::hasColumn('tenant_widgets', 'position')) {
                $table->dropColumn('position');
            }

            // Index'leri kaldır
            $indexes = ['tenant_widgets_widget_id_index', 'tenant_widgets_widget_id_is_active_index'];
            foreach ($indexes as $indexName) {
                $indexExists = collect(DB::select("SHOW INDEX FROM tenant_widgets WHERE Key_name = ?", [$indexName]))->isNotEmpty();
                if ($indexExists) {
                    $table->dropIndex($indexName);
                }
            }
        });
    }
};

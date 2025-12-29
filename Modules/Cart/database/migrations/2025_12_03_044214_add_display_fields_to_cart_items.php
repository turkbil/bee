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
        Schema::table('cart_items', function (Blueprint $table) {
            // Sadece eksik olan field'ları ekle
            if (!Schema::hasColumn('cart_items', 'item_description')) {
                $table->text('item_description')->nullable()->after('item_sku');
            }

            if (!Schema::hasColumn('cart_items', 'metadata')) {
                $table->json('metadata')->nullable()->after('customization_options');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $columns = [];

            if (Schema::hasColumn('cart_items', 'item_description')) {
                $columns[] = 'item_description';
            }

            if (Schema::hasColumn('cart_items', 'metadata')) {
                $columns[] = 'metadata';
            }

            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};

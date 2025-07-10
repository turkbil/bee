<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Helpers\TenantHelpers;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Central veritabanında kategori ID'si ekle
        TenantHelpers::central(function() {
            Schema::table('ai_features', function (Blueprint $table) {
                $table->unsignedBigInteger('ai_feature_category_id')->nullable()->after('id');
                
                $table->foreign('ai_feature_category_id')
                      ->references('ai_feature_category_id')
                      ->on('ai_feature_categories')
                      ->onDelete('set null');
                
                $table->index(['ai_feature_category_id']);
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Central veritabanında kategori ID'sini kaldır
        TenantHelpers::central(function() {
            Schema::table('ai_features', function (Blueprint $table) {
                $table->dropForeign(['ai_feature_category_id']);
                $table->dropIndex(['ai_feature_category_id']);
                $table->dropColumn('ai_feature_category_id');
            });
        });
    }
};

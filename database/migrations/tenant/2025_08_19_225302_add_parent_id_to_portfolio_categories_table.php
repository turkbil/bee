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
        Schema::table('portfolio_categories', function (Blueprint $table) {
            $table->unsignedBigInteger('parent_id')->nullable()->index()->after('portfolio_category_id')->comment('Ana kategori ID si (Alt kategoriler için)');
            
            // Foreign Key Constraint
            $table->foreign('parent_id')->references('portfolio_category_id')->on('portfolio_categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('portfolio_categories', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn('parent_id');
        });
    }
};

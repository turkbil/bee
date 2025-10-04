<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('portfolios', function (Blueprint $table) {
            $table->foreignId('portfolio_category_id')->nullable()->after('portfolio_id')->constrained('portfolio_categories', 'category_id')->nullOnDelete();
            $table->index('portfolio_category_id');
        });
    }

    public function down(): void
    {
        Schema::table('portfolios', function (Blueprint $table) {
            $table->dropForeign(['portfolio_category_id']);
            $table->dropColumn('portfolio_category_id');
        });
    }
};

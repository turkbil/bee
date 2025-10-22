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
        Schema::table('search_queries', function (Blueprint $table) {
            // Add indexes for new columns (is_popular, is_hidden)
            $table->index(['is_popular', 'is_hidden'], 'search_queries_popular_hidden_index');
            $table->index('is_popular', 'search_queries_is_popular_index');
            $table->index('is_hidden', 'search_queries_is_hidden_index');

            // Composite index for common queries
            $table->index(['query', 'is_popular', 'is_hidden'], 'search_queries_query_flags_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('search_queries', function (Blueprint $table) {
            $table->dropIndex('search_queries_popular_hidden_index');
            $table->dropIndex('search_queries_is_popular_index');
            $table->dropIndex('search_queries_is_hidden_index');
            $table->dropIndex('search_queries_query_flags_index');
        });
    }
};

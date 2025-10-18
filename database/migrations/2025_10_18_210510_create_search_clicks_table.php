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
        Schema::create('search_clicks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('search_query_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('clicked_result_id')->comment('ID of clicked item');
            $table->string('clicked_result_type')->comment('Model type: ShopProduct, Page, etc.');
            $table->integer('click_position')->nullable()->comment('Position in search results (0-based)');
            $table->boolean('opened_in_new_tab')->default(false)->comment('Shift/Ctrl+Click detection');
            $table->timestamps();

            // Indexes for analytics
            $table->index('search_query_id');
            $table->index(['clicked_result_id', 'clicked_result_type']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('search_clicks');
    }
};

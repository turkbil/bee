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
        if (Schema::hasTable('ai_flows')) {
            return; // Already exists
        }
        
        Schema::create('ai_flows', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->json('flow_data')->comment('Complete flow structure (nodes + edges)');
            $table->json('metadata')->nullable()->comment('Cache strategy, parallel groups, etc.');
            $table->integer('priority')->default(100)->comment('Execution priority (lower = higher priority)');
            $table->enum('status', ['active', 'inactive', 'draft'])->default('active');
            $table->timestamps();

            $table->index(['status', 'priority']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_flows');
    }
};

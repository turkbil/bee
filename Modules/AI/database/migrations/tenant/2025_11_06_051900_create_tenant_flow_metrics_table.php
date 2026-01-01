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
        Schema::create('tenant_flow_metrics', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->index();
            $table->unsignedBigInteger('flow_id')->nullable()->index();
            $table->string('node_id', 50)->index();
            $table->string('node_type', 50)->index();
            $table->decimal('duration_ms', 10, 2);
            $table->boolean('success')->default(true);
            $table->text('error_message')->nullable();
            $table->timestamp('created_at')->index();

            // Index for analytics queries
            $table->index(['tenant_id', 'created_at']);
            $table->index(['tenant_id', 'node_type', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenant_flow_metrics');
    }
};

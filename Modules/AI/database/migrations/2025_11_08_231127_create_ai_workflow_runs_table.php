<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations - AI Workflow Runs (execution logs)
     */
    public function up(): void
    {
        Schema::connection('mysql')->create('ai_workflow_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workflow_id')->constrained('ai_workflows')->onDelete('cascade');
            $table->foreignId('conversation_id')->nullable()->constrained('ai_conversations')->onDelete('set null');

            $table->unsignedBigInteger('tenant_id')->nullable()->index()->comment('Hangi tenant');
            $table->unsignedBigInteger('user_id')->nullable()->index()->comment('Kullanıcı ID');

            $table->enum('status', ['running', 'completed', 'failed', 'cancelled'])->default('running')->index();
            $table->string('current_node_id')->nullable()->comment('Şu anki node');

            $table->json('input_data')->nullable()->comment('Workflow girdi verileri');
            $table->json('output_data')->nullable()->comment('Workflow çıktı verileri');
            $table->json('execution_log')->nullable()->comment('Node-by-node execution log');

            $table->text('error_message')->nullable()->comment('Hata mesajı (varsa)');
            $table->integer('total_nodes_executed')->default(0)->comment('Çalışan node sayısı');
            $table->integer('execution_time_ms')->nullable()->comment('Toplam süre (ms)');

            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['workflow_id', 'status', 'created_at']);
            $table->index(['tenant_id', 'status']);
            $table->index(['conversation_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mysql')->dropIfExists('ai_workflow_runs');
    }
};

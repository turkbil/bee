<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations - AI Workflows for Central DB
     */
    public function up(): void
    {
        Schema::connection('mysql')->create('ai_workflows', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Workflow adı');
            $table->string('slug')->unique()->comment('URL-friendly slug');
            $table->text('description')->nullable()->comment('Workflow açıklaması');

            $table->unsignedBigInteger('tenant_id')->nullable()->index()->comment('Hangi tenant için (null = global)');
            $table->string('feature_slug')->nullable()->index()->comment('Hangi AI feature (shop-assistant vb.)');

            $table->enum('status', ['active', 'inactive', 'draft'])->default('draft')->index();
            $table->integer('priority')->default(0)->comment('Öncelik sırası (düşük = önce çalışır)');

            $table->json('workflow_data')->nullable()->comment('Drawflow JSON verisi');
            $table->string('start_node_id')->nullable()->comment('İlk çalışacak node ID');

            $table->boolean('is_system')->default(false)->comment('Sistem workflow mu?');
            $table->boolean('is_template')->default(false)->comment('Şablon olarak kullanılabilir mi?');

            $table->unsignedBigInteger('created_by')->nullable()->comment('Oluşturan user ID');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Güncelleyen user ID');

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['tenant_id', 'status', 'priority']);
            $table->index(['feature_slug', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mysql')->dropIfExists('ai_workflows');
    }
};

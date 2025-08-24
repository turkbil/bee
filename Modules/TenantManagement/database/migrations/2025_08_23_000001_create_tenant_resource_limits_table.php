<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenant_resource_limits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->string('resource_type'); // 'api', 'database', 'cache', 'storage', 'ai', 'cpu', 'memory'
            $table->integer('hourly_limit')->nullable();
            $table->integer('daily_limit')->nullable(); 
            $table->integer('monthly_limit')->nullable();
            $table->integer('concurrent_limit')->nullable();
            $table->bigInteger('storage_limit_mb')->nullable(); // MB cinsinden
            $table->integer('memory_limit_mb')->nullable(); // MB cinsinden RAM limiti
            $table->decimal('cpu_limit_percent', 5, 2)->nullable(); // CPU kullanım yüzdesi limiti
            $table->integer('connection_limit')->nullable(); // Eşzamanlı bağlantı limiti
            $table->json('additional_settings')->nullable(); // Ek ayarlar için JSON
            $table->boolean('is_active')->default(true);
            $table->boolean('enforce_limit')->default(true); // Limit zorunlu mu?
            $table->enum('limit_action', ['block', 'throttle', 'warn', 'queue'])->default('throttle'); // Limit aşımında ne yapılacak
            $table->text('description')->nullable();
            $table->timestamps();
            
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->index(['tenant_id', 'resource_type']);
            $table->index(['resource_type', 'is_active']);
            $table->unique(['tenant_id', 'resource_type']); // Her tenant için her kaynak tipinden sadece bir tane
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenant_resource_limits');
    }
};
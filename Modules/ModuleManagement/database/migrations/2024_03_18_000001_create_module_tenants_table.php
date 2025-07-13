<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migrasyonun central (merkezi) veritabanında çalışacağını belirt
     */
    public function getConnection()
    {
        return config('database.default');
    }
    
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('module_tenants', function (Blueprint $table) {
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('module_id');
            $table->boolean('is_active')->default(true)->index();
            $table->timestamp('assigned_at')->nullable();
            $table->timestamps();

            $table->primary(['tenant_id', 'module_id']);
            
            $table->foreign('tenant_id')
                ->references('id')
                ->on('tenants')
                ->onDelete('cascade');
                
            $table->foreign('module_id')
                ->references('module_id')
                ->on('modules')
                ->onDelete('cascade');
                
            // İlave indeksler
            $table->index('created_at');
            $table->index('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('module_tenants');
    }
};
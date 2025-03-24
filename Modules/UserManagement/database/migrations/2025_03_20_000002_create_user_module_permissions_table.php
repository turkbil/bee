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
        Schema::create('user_module_permissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('module_name', 50)->index();
            $table->string('permission_type', 20)->index(); // view, create, update, delete
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
            
            // Daha kısa bir indeks adı belirterek
            $table->unique(['user_id', 'module_name', 'permission_type'], 'ump_user_module_permission_unique');
            
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
        Schema::dropIfExists('user_module_permissions');
    }
};
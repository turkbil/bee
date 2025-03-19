<?php
// Modules/ModuleManagement/database/migrations/2024_03_17_000001_create_modules_table.php

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
        Schema::create('modules', function (Blueprint $table) {
            $table->id('module_id');
            $table->string('name');
            $table->string('display_name');
            $table->string('version')->nullable();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('settings')->nullable();
            $table->enum('type', ['content', 'management', 'system'])->default('content');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modules');
    }
};
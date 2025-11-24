<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Merkezi veritabanında çalışacak
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
        if (Schema::hasTable('widget_modules')) {
            return;
        }

        Schema::create('widget_modules', function (Blueprint $table) {
            $table->unsignedBigInteger('widget_id');
            $table->unsignedBigInteger('module_id');
            
            $table->primary(['widget_id', 'module_id']);
            
            $table->foreign('widget_id')
                ->references('id')
                ->on('widgets')
                ->onDelete('cascade');
                
            $table->foreign('module_id')
                ->references('module_id')
                ->on('modules')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('widget_modules');
    }
};
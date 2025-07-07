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
        Schema::table('ai_features', function (Blueprint $table) {
            $table->integer('order')->default(0);
        });
        
        // Mevcut kayıtları sırala
        $features = DB::table('ai_features')->orderBy('id')->get();
        foreach ($features as $index => $feature) {
            DB::table('ai_features')
                ->where('id', $feature->id)
                ->update(['order' => $index + 1]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_features', function (Blueprint $table) {
            $table->dropColumn('order');
        });
    }
};

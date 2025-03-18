<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TenantCreateActivityLogTable extends Migration
{
    public function up()
    {
        // Create the activity_log table
        Schema::create('activity_log', function (Blueprint $table) {
            $table->bigIncrements('id');                         // Birincil anahtar
            $table->string('log_name')->nullable();              // Log adı
            $table->text('description');                         // Açıklama
            $table->nullableMorphs('subject');                   // İşlem yapılan nesne
            $table->nullableMorphs('causer');                    // İşlemi yapan kişi
            $table->string('event')->nullable();                 // Olay türü
            $table->json('properties')->nullable();              // Ek özellikler
            $table->timestamps();                                // created_at ve updated_at
            $table->softDeletes();                               // Silinmiş kayıtlar için deleted_at
            $table->index('log_name');                           // Log adı için indeks
        });

        // Add batch_uuid column after properties
        Schema::table('activity_log', function (Blueprint $table) {
            $table->uuid('batch_uuid')->nullable()->after('properties');
        });
    }

    public function down()
    {
        // Drop the activity_log table
        Schema::dropIfExists('activity_log');
    }
}

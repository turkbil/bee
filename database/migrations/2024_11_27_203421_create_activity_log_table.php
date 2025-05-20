<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivityLogTable extends Migration
{
    public function up()
    {
        // Create the activity_log table
        Schema::create('activity_log', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('log_name')->nullable();
            $table->text('description');
            $table->nullableMorphs('subject');
            $table->nullableMorphs('causer');
            $table->string('event')->nullable()->index();
            $table->json('properties')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index('log_name');
            
            // Performans için ilave indeksler
            $table->index(['causer_type', 'causer_id', 'created_at']);
            $table->index(['subject_type', 'subject_id', 'created_at']);
            $table->index('created_at');
        });

        // Add batch_uuid column after properties
        Schema::table('activity_log', function (Blueprint $table) {
            $table->uuid('batch_uuid')->nullable()->after('properties')->index();
        });
    }

    public function down()
    {
        // Drop the activity_log table
        Schema::dropIfExists('activity_log');
    }
}
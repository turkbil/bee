<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCentralAITables extends Command
{
    protected $signature = 'ai:create-central-tables';
    protected $description = 'Create AI tables in central database';

    public function handle()
    {
        $this->info('Creating AI tables in central database...');
        
        try {
            // ai_conversations tablosunu oluştur
            if (!Schema::hasTable('ai_conversations')) {
                Schema::create('ai_conversations', function (Blueprint $table) {
                    $table->id();
                    $table->string('title');
                    $table->string('type')->default('chat');
                    $table->string('feature_name')->nullable();
                    $table->boolean('is_demo')->default(false);
                    $table->unsignedBigInteger('user_id');
                    $table->unsignedBigInteger('tenant_id')->nullable();
                    $table->unsignedBigInteger('prompt_id')->nullable();
                    $table->integer('total_tokens_used')->default(0);
                    $table->json('metadata')->nullable();
                    $table->string('status')->default('active');
                    $table->timestamps();
                    
                    $table->index('user_id');
                    $table->index('tenant_id');
                    $table->index('type');
                    $table->index('status');
                    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                });
                $this->info('✓ ai_conversations table created');
            } else {
                $this->info('- ai_conversations table already exists');
            }

            // ai_messages tablosunu oluştur
            if (!Schema::hasTable('ai_messages')) {
                Schema::create('ai_messages', function (Blueprint $table) {
                    $table->id();
                    $table->unsignedBigInteger('conversation_id');
                    $table->enum('role', ['user', 'assistant', 'system']);
                    $table->longText('content');
                    $table->integer('tokens')->default(0);
                    $table->integer('prompt_tokens')->default(0);
                    $table->integer('completion_tokens')->default(0);
                    $table->string('model_used')->nullable();
                    $table->integer('processing_time_ms')->default(0);
                    $table->json('metadata')->nullable();
                    $table->string('message_type')->default('chat');
                    $table->timestamps();
                    
                    $table->index('conversation_id');
                    $table->index('role');
                    $table->foreign('conversation_id')->references('id')->on('ai_conversations')->onDelete('cascade');
                });
                $this->info('✓ ai_messages table created');
            } else {
                $this->info('- ai_messages table already exists');
            }

            $this->info('AI tables setup completed successfully!');
            
        } catch (\Exception $e) {
            $this->error('Error creating AI tables: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}
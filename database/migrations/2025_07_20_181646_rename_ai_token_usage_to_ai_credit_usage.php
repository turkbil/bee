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
        // ai_token_usage tablosu yoksa direkt ai_credit_usage oluştur
        if (!Schema::hasTable('ai_token_usage') && !Schema::hasTable('ai_credit_usage')) {
            Schema::create('ai_credit_usage', function (Blueprint $table) {
                $table->id();
                $table->string('tenant_id')->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->unsignedBigInteger('conversation_id')->nullable();
                $table->unsignedBigInteger('message_id')->nullable();
                $table->unsignedBigInteger('ai_provider_id')->nullable();
                $table->string('provider_name')->nullable();
                $table->decimal('credits_used', 10, 4)->default(0);
                $table->integer('input_tokens')->default(0);
                $table->integer('output_tokens')->default(0);
                $table->decimal('credit_cost', 10, 4)->default(0);
                $table->string('currency', 3)->default('USD');
                $table->decimal('usd_rate', 10, 4)->nullable();
                $table->decimal('markup_percentage', 5, 2)->default(0);
                $table->string('usage_type')->nullable();
                $table->string('feature_slug')->nullable();
                $table->string('model')->nullable();
                $table->string('purpose')->nullable();
                $table->text('description')->nullable();
                $table->string('reference_id')->nullable();
                $table->decimal('cost_multiplier', 8, 4)->default(1.0000);
                $table->json('response_metadata')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamp('used_at')->nullable();
                $table->timestamps();
                
                // Index'ler
                $table->index(['tenant_id', 'created_at'], 'idx_tenant_date');
                $table->index(['provider_name', 'created_at'], 'idx_provider_date');
                $table->index(['usage_type', 'created_at'], 'idx_type_date');
                $table->index(['feature_slug'], 'idx_feature_slug');
                $table->index(['used_at'], 'idx_used_at');
            });
        } else if (Schema::hasTable('ai_token_usage')) {
            // Mevcut tablo varsa rename et ve güncelle
            Schema::rename('ai_token_usage', 'ai_credit_usage');
            
            Schema::table('ai_credit_usage', function (Blueprint $table) {
                // Kolom var mı kontrol et ve rename et
                if (Schema::hasColumn('ai_credit_usage', 'tokens_used')) {
                    $table->renameColumn('tokens_used', 'credits_used');
                }
                if (Schema::hasColumn('ai_credit_usage', 'prompt_tokens')) {
                    $table->renameColumn('prompt_tokens', 'input_tokens');
                }
                if (Schema::hasColumn('ai_credit_usage', 'completion_tokens')) {
                    $table->renameColumn('completion_tokens', 'output_tokens');
                }
                
                // Yeni alanları ekle (yoksa)
                if (!Schema::hasColumn('ai_credit_usage', 'credit_cost')) {
                    $table->decimal('credit_cost', 10, 4)->after('credits_used')->default(0);
                }
                if (!Schema::hasColumn('ai_credit_usage', 'currency')) {
                    $table->string('currency', 3)->after('credit_cost')->default('USD');
                }
                if (!Schema::hasColumn('ai_credit_usage', 'usd_rate')) {
                    $table->decimal('usd_rate', 10, 4)->after('currency')->nullable();
                }
                if (!Schema::hasColumn('ai_credit_usage', 'markup_percentage')) {
                    $table->decimal('markup_percentage', 5, 2)->after('usd_rate')->default(0);
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Yeni alanları kaldır
        Schema::table('ai_credit_usage', function (Blueprint $table) {
            $table->dropColumn(['credit_cost', 'currency', 'usd_rate', 'markup_percentage']);
            $table->dropIndex('idx_tenant_date');
            $table->dropIndex('idx_provider_date');
            $table->dropIndex('idx_type_date');
        });
        
        // Kolonları geri çevir
        Schema::table('ai_credit_usage', function (Blueprint $table) {
            $table->renameColumn('credits_used', 'tokens_used');
            $table->renameColumn('input_tokens', 'prompt_tokens');
            $table->renameColumn('output_tokens', 'completion_tokens');
        });
        
        // Tabloyu geri adlandır
        Schema::rename('ai_credit_usage', 'ai_token_usage');
    }
};

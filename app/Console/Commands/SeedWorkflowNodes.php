<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SeedWorkflowNodes extends Command
{
    protected $signature = 'ai:seed-workflow-nodes';
    protected $description = 'Seed global workflow nodes to central database';

    public function handle()
    {
        $this->info('🌱 Seeding workflow nodes...');

        // Clear existing nodes
        DB::connection('mysql')->table('ai_workflow_nodes')->truncate();
        $this->info('🗑️  Cleared existing nodes');

            // Seed global nodes
            $nodes = [
                // Flow Control
                [
                    'node_key' => 'welcome',
                    'node_class' => 'App\\Services\\ConversationNodes\\Common\\WelcomeNode',
                    'node_name' => json_encode(['tr' => 'Karşılama', 'en' => 'Welcome']),
                    'node_description' => json_encode(['tr' => 'Kullanıcıyı karşılama mesajı', 'en' => 'Welcome message']),
                    'category' => 'flow',
                    'icon' => 'ti ti-hand-stop',
                    'order' => 1,
                    'is_global' => true,
                    'is_active' => true,
                ],
                [
                    'node_key' => 'condition',
                    'node_class' => 'App\\Services\\ConversationNodes\\Common\\ConditionNode',
                    'node_name' => json_encode(['tr' => 'Şart Kontrolü', 'en' => 'Condition']),
                    'node_description' => json_encode(['tr' => 'IF/ELSE mantığı', 'en' => 'IF/ELSE logic']),
                    'category' => 'flow',
                    'icon' => 'ti ti-git-branch',
                    'order' => 2,
                    'is_global' => true,
                    'is_active' => true,
                ],
                [
                    'node_key' => 'end',
                    'node_class' => 'App\\Services\\ConversationNodes\\Common\\EndNode',
                    'node_name' => json_encode(['tr' => 'Bitir', 'en' => 'End']),
                    'node_description' => json_encode(['tr' => 'Sohbeti bitir', 'en' => 'End conversation']),
                    'category' => 'flow',
                    'icon' => 'ti ti-flag-filled',
                    'order' => 3,
                    'is_global' => true,
                    'is_active' => true,
                ],

                // AI & Response
                [
                    'node_key' => 'ai_response',
                    'node_class' => 'App\\Services\\ConversationNodes\\Common\\AIResponseNode',
                    'node_name' => json_encode(['tr' => 'AI Yanıt', 'en' => 'AI Response']),
                    'node_description' => json_encode(['tr' => 'AI cevap üretme', 'en' => 'Generate AI response']),
                    'category' => 'ai',
                    'icon' => 'ti ti-robot',
                    'order' => 1,
                    'is_global' => true,
                    'is_active' => true,
                ],

                // Data Processing
                [
                    'node_key' => 'context_builder',
                    'node_class' => 'App\\Services\\ConversationNodes\\Common\\ContextBuilderNode',
                    'node_name' => json_encode(['tr' => 'Context Hazırla', 'en' => 'Build Context']),
                    'node_description' => json_encode(['tr' => 'AI için context hazırla', 'en' => 'Prepare context for AI']),
                    'category' => 'data',
                    'icon' => 'ti ti-database',
                    'order' => 1,
                    'is_global' => true,
                    'is_active' => true,
                ],
                [
                    'node_key' => 'history_loader',
                    'node_class' => 'App\\Services\\ConversationNodes\\Common\\HistoryLoaderNode',
                    'node_name' => json_encode(['tr' => 'Geçmiş Yükle', 'en' => 'Load History']),
                    'node_description' => json_encode(['tr' => 'Konuşma geçmişini yükle', 'en' => 'Load conversation history']),
                    'category' => 'data',
                    'icon' => 'ti ti-clock-hour-4',
                    'order' => 2,
                    'is_global' => true,
                    'is_active' => true,
                ],
                [
                    'node_key' => 'message_saver',
                    'node_class' => 'App\\Services\\ConversationNodes\\Common\\MessageSaverNode',
                    'node_name' => json_encode(['tr' => 'Mesaj Kaydet', 'en' => 'Save Message']),
                    'node_description' => json_encode(['tr' => 'Mesajları veritabanına kaydet', 'en' => 'Save messages to database']),
                    'category' => 'data',
                    'icon' => 'ti ti-device-floppy',
                    'order' => 3,
                    'is_global' => true,
                    'is_active' => true,
                ],
                [
                    'node_key' => 'collect_data',
                    'node_class' => 'App\\Services\\ConversationNodes\\Common\\CollectDataNode',
                    'node_name' => json_encode(['tr' => 'Veri Topla', 'en' => 'Collect Data']),
                    'node_description' => json_encode(['tr' => 'Kullanıcıdan veri topla', 'en' => 'Collect data from user']),
                    'category' => 'input',
                    'icon' => 'ti ti-forms',
                    'order' => 1,
                    'is_global' => true,
                    'is_active' => true,
                ],

                // Analysis
                [
                    'node_key' => 'sentiment_detection',
                    'node_class' => 'App\\Services\\ConversationNodes\\Common\\SentimentDetectionNode',
                    'node_name' => json_encode(['tr' => 'Niyet Analizi', 'en' => 'Sentiment Detection']),
                    'node_description' => json_encode(['tr' => 'Kullanıcı niyetini tespit et', 'en' => 'Detect user intent']),
                    'category' => 'analysis',
                    'icon' => 'ti ti-brain',
                    'order' => 1,
                    'is_global' => true,
                    'is_active' => true,
                ],

                // Output
                [
                    'node_key' => 'link_generator',
                    'node_class' => 'App\\Services\\ConversationNodes\\Common\\LinkGeneratorNode',
                    'node_name' => json_encode(['tr' => 'Link Oluştur', 'en' => 'Generate Links']),
                    'node_description' => json_encode(['tr' => 'Custom linkleri URL\'e çevir', 'en' => 'Convert custom links to URLs']),
                    'category' => 'output',
                    'icon' => 'ti ti-link',
                    'order' => 1,
                    'is_global' => true,
                    'is_active' => true,
                ],
                [
                    'node_key' => 'share_contact',
                    'node_class' => 'App\\Services\\ConversationNodes\\Common\\ShareContactNode',
                    'node_name' => json_encode(['tr' => 'İletişim Paylaş', 'en' => 'Share Contact']),
                    'node_description' => json_encode(['tr' => 'İletişim bilgilerini paylaş', 'en' => 'Share contact information']),
                    'category' => 'output',
                    'icon' => 'ti ti-share',
                    'order' => 2,
                    'is_global' => true,
                    'is_active' => true,
                ],
                [
                    'node_key' => 'webhook',
                    'node_class' => 'App\\Services\\ConversationNodes\\Common\\WebhookNode',
                    'node_name' => json_encode(['tr' => 'Webhook', 'en' => 'Webhook']),
                    'node_description' => json_encode(['tr' => 'External API çağrısı', 'en' => 'External API call']),
                    'category' => 'integration',
                    'icon' => 'ti ti-webhook',
                    'order' => 1,
                    'is_global' => true,
                    'is_active' => true,
                ],
            ];

        DB::connection('mysql')->table('ai_workflow_nodes')->insert($nodes);
        $this->info('✅ Seeded ' . count($nodes) . ' global nodes');

        $this->info('🎉 Workflow nodes seeded successfully!');

        return Command::SUCCESS;
    }
}

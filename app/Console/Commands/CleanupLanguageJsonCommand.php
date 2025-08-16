<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\LanguageCleanupService;

class CleanupLanguageJsonCommand extends Command
{
    protected $signature = 'language:cleanup-json 
                          {languages?* : Language codes to remove from JSON data}
                          {--detect : Only detect orphaned language keys without cleanup}
                          {--force : Force cleanup without confirmation}';

    protected $description = 'Clean up removed language data from all module JSON fields';

    public function handle(): int
    {
        $cleanupService = app(LanguageCleanupService::class);

        // Detect mode
        if ($this->option('detect')) {
            $this->info('🔍 Orphaned language keys tespit ediliyor...');
            
            $orphanedData = $cleanupService->detectOrphanedLanguageKeys();
            
            if (empty($orphanedData)) {
                $this->info('✅ Orphaned language key bulunamadı.');
                return self::SUCCESS;
            }

            $this->warn('⚠️ ' . count($orphanedData) . ' tabloda orphaned language key tespit edildi:');
            $this->newLine();

            foreach ($orphanedData as $item) {
                $this->line("📊 <comment>{$item['table']}</comment>.<info>{$item['field']}</info>");
                $this->line("   Orphaned diller: " . implode(', ', $item['orphaned_languages']));
                $this->newLine();
            }

            return self::SUCCESS;
        }

        // Cleanup mode
        $languagesToRemove = $this->argument('languages');

        if (empty($languagesToRemove)) {
            $this->error('❌ En az bir dil kodu belirtmelisiniz!');
            $this->line('   Örnek: php artisan language:cleanup-json en ar');
            return self::FAILURE;
        }

        $this->info('🧹 JSON dil temizleme işlemi başlatılıyor...');
        $this->line('Silinecek diller: ' . implode(', ', $languagesToRemove));

        // Confirmation
        if (!$this->option('force') && !$this->confirm('Bu işlem geri alınamaz. Devam etmek istiyor musunuz?')) {
            $this->info('İşlem iptal edildi.');
            return self::SUCCESS;
        }

        try {
            $result = $cleanupService->cleanupLanguagesFromAllModules($languagesToRemove);

            $this->newLine();
            $this->info('🎉 Temizleme işlemi tamamlandı!');
            $this->newLine();

            // Results table
            $headers = ['Tablo', 'Model', 'Güncellenen Kayıt', 'İşlenen Field\'lar'];
            $rows = [];

            foreach ($result['processed_tables'] as $table) {
                $fieldsList = isset($table['processed_fields']) 
                    ? implode(', ', array_column($table['processed_fields'], 'field'))
                    : 'N/A';

                $rows[] = [
                    $table['table'],
                    basename($table['model']),
                    $table['updated_rows'] ?? 0,
                    $fieldsList
                ];
            }

            $this->table($headers, $rows);

            $this->newLine();
            $this->info("📊 Toplam: {$result['total_updated_rows']} kayıt güncellendi");
            $this->info("📦 " . count($result['processed_tables']) . " tablo işlendi");
            $this->info("🗑️ Silinen diller: " . implode(', ', $result['removed_languages']));

            return self::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ Temizleme işleminde hata: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanDescriptionFieldsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'muzibu:clean-descriptions {--tenant=1001 : Tenant ID} {--dry-run : Sadece analiz yap, güncelleme yapma}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Muzibu tablolarındaki uzun description alanlarını sadeleştirir (en fazla 1 cümle)';

    /**
     * Tables to clean
     */
    protected $tables = [
        'muzibu_genres' => ['pk' => 'genre_id', 'field' => 'description'],
        'muzibu_playlists' => ['pk' => 'playlist_id', 'field' => 'description'],
        'muzibu_albums' => ['pk' => 'album_id', 'field' => 'description'],
        'muzibu_sectors' => ['pk' => 'sector_id', 'field' => 'description'],
        'muzibu_radios' => ['pk' => 'radio_id', 'field' => 'description'],
        'muzibu_artists' => ['pk' => 'artist_id', 'field' => 'bio'],
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tenantId = $this->option('tenant');
        $isDryRun = $this->option('dry-run');

        $this->info("🚀 Starting description cleanup for tenant: {$tenantId}");
        if ($isDryRun) {
            $this->warn("⚠️  DRY-RUN mode: No changes will be made");
        }
        $this->newLine();

        // Tenant context'e gir
        $tenant = \App\Models\Tenant::find($tenantId);
        if (!$tenant) {
            $this->error("❌ Tenant not found: {$tenantId}");
            return 1;
        }

        tenancy()->initialize($tenant);

        $totalCleaned = 0;

        foreach ($this->tables as $table => $config) {
            $this->info("📋 Processing table: {$table}");

            $count = $this->cleanTable($table, $config['pk'], $config['field']);
            $totalCleaned += $count;

            $this->info("✅ " . ($isDryRun ? "Would clean" : "Cleaned") . " {$count} records in {$table}");
            $this->newLine();
        }

        $this->info("🎉 Total records " . ($isDryRun ? "to clean" : "cleaned") . ": {$totalCleaned}");

        if ($isDryRun) {
            $this->warn("🔸 Run without --dry-run to apply changes");
        }

        return 0;
    }

    /**
     * Clean description field in a table
     */
    protected function cleanTable(string $table, string $primaryKey, string $descField): int
    {
        $connection = 'tenant';
        $count = 0;
        $isDryRun = $this->option('dry-run');

        // Get all records with description field
        $records = DB::connection($connection)
            ->table($table)
            ->whereNotNull($descField)
            ->get();

        foreach ($records as $record) {
            try {
                // Parse JSON description
                $descriptions = json_decode($record->$descField, true);

                if (!is_array($descriptions)) {
                    // Plain text (radios table)
                    $cleanedText = $this->cleanText($record->$descField);

                    if ($cleanedText !== $record->$descField) {
                        $count++;

                        if ($isDryRun) {
                            $this->line("  → ID {$record->$primaryKey}:");
                            $this->line("     Old: " . \Illuminate\Support\Str::limit($record->$descField, 80));
                            $this->line("     New: " . \Illuminate\Support\Str::limit($cleanedText, 80));
                        } else {
                            DB::connection($connection)
                                ->table($table)
                                ->where($primaryKey, $record->$primaryKey)
                                ->update([
                                    $descField => $cleanedText,
                                    'updated_at' => now(),
                                ]);

                            $this->line("  → ID {$record->$primaryKey}: Cleaned");
                        }
                    }
                    continue;
                }

                $cleaned = false;
                $cleanedDescriptions = [];

                foreach ($descriptions as $lang => $text) {
                    if (empty($text)) {
                        $cleanedDescriptions[$lang] = $text;
                        continue;
                    }

                    // Clean the text
                    $cleanedText = $this->cleanText($text);

                    // Check if anything changed
                    if ($cleanedText !== $text) {
                        $cleaned = true;
                    }

                    $cleanedDescriptions[$lang] = $cleanedText;
                }

                // Update only if something changed
                if ($cleaned) {
                    $count++;

                    if ($isDryRun) {
                        $this->line("  → ID {$record->$primaryKey}:");
                        foreach ($descriptions as $lang => $text) {
                            if ($cleanedDescriptions[$lang] !== $text) {
                                $this->line("     [{$lang}] Old: " . \Illuminate\Support\Str::limit($text, 80));
                                $this->line("     [{$lang}] New: " . \Illuminate\Support\Str::limit($cleanedDescriptions[$lang], 80));
                            }
                        }
                    } else {
                        DB::connection($connection)
                            ->table($table)
                            ->where($primaryKey, $record->$primaryKey)
                            ->update([
                                $descField => json_encode($cleanedDescriptions, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                                'updated_at' => now(),
                            ]);

                        // Show progress
                        $this->line("  → ID {$record->$primaryKey}: Cleaned");
                    }
                }

            } catch (\Exception $e) {
                $this->error("  ❌ Error processing record ID {$record->$primaryKey}: " . $e->getMessage());
            }
        }

        return $count;
    }

    /**
     * Clean text: remove HTML tags, decode HTML entities, and limit to first sentence
     */
    protected function cleanText(string $text): string
    {
        if (empty($text)) {
            return $text;
        }

        // 1. Strip HTML tags
        $cleaned = strip_tags($text);

        // 2. Decode HTML entities (ü, ç, ğ, ş, ö, etc.)
        $cleaned = html_entity_decode($cleaned, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // 3. Normalize whitespace
        $cleaned = preg_replace('/\s+/', ' ', $cleaned);

        // 4. Trim
        $cleaned = trim($cleaned);

        // 5. Extract first sentence only (until . ! or ?)
        if (preg_match('/^([^.!?]+[.!?])/', $cleaned, $matches)) {
            $cleaned = trim($matches[1]);
        }

        // 6. If still longer than 150 chars, limit it
        if (mb_strlen($cleaned) > 150) {
            $cleaned = mb_substr($cleaned, 0, 150);
            // Don't cut in the middle of a word
            $lastSpace = mb_strrpos($cleaned, ' ');
            if ($lastSpace !== false) {
                $cleaned = mb_substr($cleaned, 0, $lastSpace);
            }
            // Add period if not already there
            if (!in_array(mb_substr($cleaned, -1), ['.', '!', '?'])) {
                $cleaned .= '.';
            }
        }

        return $cleaned;
    }
}

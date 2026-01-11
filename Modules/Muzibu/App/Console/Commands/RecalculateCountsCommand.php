<?php

namespace Modules\Muzibu\App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Muzibu\App\Models\Album;
use Modules\Muzibu\App\Models\Playlist;
use Modules\Muzibu\App\Models\Genre;
use Modules\Muzibu\App\Models\Artist;

class RecalculateCountsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'muzibu:recalculate-counts
                            {--model= : Specific model to recalculate (album, playlist, genre, artist)}
                            {--id= : Specific ID to recalculate}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculate cached count fields (songs_count, total_duration, albums_count) for Muzibu models';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $model = $this->option('model');
        $id = $this->option('id');

        $this->info('Starting Muzibu cache count recalculation...');

        if ($id && $model) {
            // Specific model and ID
            return $this->recalculateSingle($model, $id);
        }

        if ($model) {
            // Specific model, all records
            return $this->recalculateModel($model);
        }

        // All models
        $this->recalculateAll();

        return Command::SUCCESS;
    }

    /**
     * Recalculate single record
     */
    protected function recalculateSingle(string $model, int $id): int
    {
        $modelClass = $this->getModelClass($model);

        if (!$modelClass) {
            $this->error("Invalid model: {$model}");
            return Command::FAILURE;
        }

        $record = $modelClass::find($id);

        if (!$record) {
            $this->error("{$model} with ID {$id} not found");
            return Command::FAILURE;
        }

        $values = $record->recalculateCachedCounts();
        $this->info("Recalculated {$model} #{$id}: " . json_encode($values));

        return Command::SUCCESS;
    }

    /**
     * Recalculate all records of a model
     */
    protected function recalculateModel(string $model): int
    {
        $modelClass = $this->getModelClass($model);

        if (!$modelClass) {
            $this->error("Invalid model: {$model}");
            return Command::FAILURE;
        }

        $count = $modelClass::count();
        $this->info("Recalculating {$count} {$model} records...");

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        $modelClass::chunk(100, function ($records) use ($bar) {
            foreach ($records as $record) {
                $record->recalculateCachedCounts();
                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine();
        $this->info("Completed {$model} recalculation.");

        return Command::SUCCESS;
    }

    /**
     * Recalculate all models
     */
    protected function recalculateAll(): void
    {
        $models = ['album', 'playlist', 'genre', 'artist'];

        foreach ($models as $model) {
            $this->newLine();
            $this->recalculateModel($model);
        }

        $this->newLine();
        $this->info('All Muzibu cache counts recalculated successfully!');
    }

    /**
     * Get model class from name
     */
    protected function getModelClass(string $model): ?string
    {
        return match (strtolower($model)) {
            'album' => Album::class,
            'playlist' => Playlist::class,
            'genre' => Genre::class,
            'artist' => Artist::class,
            default => null,
        };
    }
}

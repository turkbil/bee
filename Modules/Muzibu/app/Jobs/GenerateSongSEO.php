<?php

namespace Modules\Muzibu\App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Modules\Muzibu\App\Models\Song;
use Modules\SEO\app\Models\SeoSetting;
use OpenAI\Laravel\Facades\OpenAI;

class GenerateSongSEO implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 2;
    public $timeout = 120;

    protected int $songId;
    protected ?int $tenantId;
    protected ?string $songTitle;
    protected ?string $artistName;
    protected ?string $albumName;
    protected ?string $genreName;

    public function __construct(
        int $songId,
        ?int $tenantId = null,
        ?string $songTitle = null,
        ?string $artistName = null,
        ?string $albumName = null,
        ?string $genreName = null
    ) {
        $this->songId = $songId;
        $this->tenantId = $tenantId;
        $this->songTitle = $songTitle;
        $this->artistName = $artistName;
        $this->albumName = $albumName;
        $this->genreName = $genreName;
    }

    public function handle(): void
    {
        // Initialize tenant if provided
        if ($this->tenantId) {
            tenancy()->initialize($this->tenantId);
        }

        $song = Song::find($this->songId);

        if (!$song) {
            Log::error("SEO Generation: Song not found", ['song_id' => $this->songId]);
            return;
        }

        // Skip if SEO already exists
        if ($song->seoSetting && !empty($song->seoSetting->meta_title)) {
            Log::info("SEO Generation: Already exists", ['song_id' => $this->songId]);
            return;
        }

        // Get song metadata
        $songTitle = $this->songTitle ?? $this->extractTitle($song);
        $artistName = $this->artistName ?? $this->extractArtistName($song);
        $albumName = $this->albumName ?? $this->extractAlbumName($song);
        $genreName = $this->genreName ?? $this->extractGenreName($song);

        // Build context for AI
        $context = $this->buildContext($songTitle, $artistName, $albumName, $genreName);

        try {
            // Generate SEO with OpenAI GPT-4
            $seoData = $this->generateSEO($context);

            // Create or update SEO setting
            SeoSetting::updateOrCreate(
                [
                    'seoable_type' => Song::class,
                    'seoable_id' => $this->songId,
                ],
                [
                    'meta_title' => $seoData['meta_title'],
                    'meta_description' => $seoData['meta_description'],
                    'meta_keywords' => $seoData['meta_keywords'],
                ]
            );

            Log::info("SEO Generation: Success", [
                'song_id' => $this->songId,
                'title' => $songTitle
            ]);

        } catch (\Exception $e) {
            Log::error("SEO Generation: Failed", [
                'song_id' => $this->songId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw $e;
        }
    }

    /**
     * Generate SEO metadata using OpenAI GPT-4
     */
    protected function generateSEO(string $context): array
    {
        $prompt = <<<PROMPT
Sen bir müzik platformu SEO uzmanısın. Aşağıdaki şarkı bilgilerine dayanarak SEO metadata'sı oluştur.

ÖNEMLİ: Bu bir ŞARKI (song), sözleri veya hikayesi DEĞİL! Müzikal metadata bazında SEO oluştur.

{$context}

SEO metadata oluştururken:
1. Meta title: Şarkı + Sanatçı + Platform (60 karakter max)
2. Meta description: Şarkı hakkında kısa bilgi + albüm + tür (150-160 karakter)
3. Meta keywords: Virgülle ayrılmış 5-7 keyword (şarkı adı, sanatçı, albüm, tür, vs.)

Türkçe ve SEO-friendly yaz. Sadece JSON formatında döndür:

{
  "meta_title": "...",
  "meta_description": "...",
  "meta_keywords": "..."
}
PROMPT;

        $response = OpenAI::chat()->create([
            'model' => 'gpt-4',
            'messages' => [
                ['role' => 'system', 'content' => 'Sen bir müzik platformu SEO uzmanısın. Şarkı metadata\'sından SEO oluşturursun.'],
                ['role' => 'user', 'content' => $prompt],
            ],
            'temperature' => 0.7,
            'max_tokens' => 500,
        ]);

        $content = $response->choices[0]->message->content ?? '';

        // Parse JSON response
        $jsonStart = strpos($content, '{');
        $jsonEnd = strrpos($content, '}');

        if ($jsonStart === false || $jsonEnd === false) {
            throw new \RuntimeException("Invalid JSON response from OpenAI");
        }

        $jsonContent = substr($content, $jsonStart, $jsonEnd - $jsonStart + 1);
        $seoData = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException("JSON decode error: " . json_last_error_msg());
        }

        // Validate required fields
        if (empty($seoData['meta_title']) || empty($seoData['meta_description']) || empty($seoData['meta_keywords'])) {
            throw new \RuntimeException("Missing required SEO fields in AI response");
        }

        return $seoData;
    }

    /**
     * Build context string for AI prompt
     */
    protected function buildContext(string $songTitle, ?string $artistName, ?string $albumName, ?string $genreName): string
    {
        $context = "ŞARKI BİLGİLERİ:\n";
        $context .= "- Şarkı Adı (Song Title): {$songTitle}\n";

        if ($artistName) {
            $context .= "- Sanatçı (Artist): {$artistName}\n";
        }

        if ($albumName) {
            $context .= "- Albüm (Album): {$albumName}\n";
        }

        if ($genreName) {
            $context .= "- Tür (Genre): {$genreName}\n";
        }

        $context .= "\nBu bir MÜZİK PLATFORMU şarkı sayfasıdır. Sözler veya hikaye DEĞİL, müzikal metadata'dır.";

        return $context;
    }

    /**
     * Extract title from song
     */
    protected function extractTitle(Song $song): string
    {
        if (is_array($song->title)) {
            return $song->title['tr'] ?? $song->title['en'] ?? 'No Title';
        }

        return $song->title ?? 'No Title';
    }

    /**
     * Extract artist name from song
     */
    protected function extractArtistName(Song $song): ?string
    {
        if ($song->album && $song->album->artists && $song->album->artists->isNotEmpty()) {
            $artist = $song->album->artists->first();
            return $artist->name ?? null;
        }

        return null;
    }

    /**
     * Extract album name from song
     */
    protected function extractAlbumName(Song $song): ?string
    {
        if ($song->album) {
            if (is_array($song->album->name)) {
                return $song->album->name['tr'] ?? $song->album->name['en'] ?? null;
            }
            return $song->album->name ?? null;
        }

        return null;
    }

    /**
     * Extract genre name from song
     */
    protected function extractGenreName(Song $song): ?string
    {
        if ($song->genre) {
            if (is_array($song->genre->name)) {
                return $song->genre->name['tr'] ?? $song->genre->name['en'] ?? null;
            }
            return $song->genre->name ?? null;
        }

        return null;
    }
}

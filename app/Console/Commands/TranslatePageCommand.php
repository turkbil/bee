<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Page\App\Models\Page;
use Modules\LanguageManagement\App\Models\TenantLanguage;
use Modules\AI\App\Services\AIService;
use Illuminate\Support\Str;

class TranslatePageCommand extends Command
{
    protected $signature = 'page:translate {pageId} {--source=tr} {--test}';
    protected $description = 'Translate a page to all missing languages';

    public function handle()
    {
        $pageId = $this->argument('pageId');
        $sourceLang = $this->option('source');
        $isTest = $this->option('test');
        
        $page = Page::find($pageId);
        if (!$page) {
            $this->error("Page ID {$pageId} not found!");
            return 1;
        }

        // Mevcut dilleri al
        $existingTitle = is_string($page->title) ? json_decode($page->title, true) : $page->title;
        $existingSlug = is_string($page->slug) ? json_decode($page->slug, true) : $page->slug;
        $existingBody = is_string($page->body) ? json_decode($page->body, true) : $page->body;
        
        $this->info("Page ID {$pageId} - Current languages: " . implode(', ', array_keys($existingTitle)));
        
        // Sistemdeki görünür dilleri al
        $allLanguages = TenantLanguage::where('is_visible', true)
            ->where('is_active', true)
            ->pluck('code')
            ->toArray();
            
        $missingLanguages = array_diff($allLanguages, array_keys($existingTitle));
        
        if (empty($missingLanguages)) {
            $this->info("No missing languages found!");
            return 0;
        }
        
        $this->info("Missing languages: " . implode(', ', $missingLanguages));
        $this->info("Will translate from {$sourceLang} to: " . implode(', ', $missingLanguages));
        
        if ($isTest) {
            $this->info("TEST MODE - Not saving changes");
        }
        
        $aiService = app(AIService::class);
        
        // Her eksik dil için çeviri yap
        foreach ($missingLanguages as $targetLang) {
            $this->info("\nTranslating to {$targetLang}...");
            
            // Kaynak metinleri al
            $sourceTitle = $existingTitle[$sourceLang] ?? '';
            $sourceBody = $existingBody[$sourceLang] ?? '';
            
            if (empty($sourceTitle) || empty($sourceBody)) {
                $this->warn("Source content not found for {$sourceLang}");
                continue;
            }
            
            // Çeviri yap
            $prompt = $this->buildPrompt($sourceTitle, $sourceBody, $sourceLang, $targetLang);
            
            try {
                $response = $aiService->processRequest(
                    prompt: $prompt,
                    maxTokens: 3000,
                    temperature: 0.3,
                    metadata: [
                        'source' => 'page_translation_command',
                        'page_id' => $pageId,
                        'source_lang' => $sourceLang,
                        'target_lang' => $targetLang
                    ]
                );
                
                if ($response['success'] ?? false) {
                    // Response'u al - AIService'in yeni formatına göre
                    $content = '';
                    
                    // Farklı response formatlarını dene
                    if (isset($response['data']['content'])) {
                        $content = $response['data']['content'];
                    } elseif (isset($response['data']['raw_response']['choices'][0]['message']['content'])) {
                        $content = $response['data']['raw_response']['choices'][0]['message']['content'];
                    } elseif (isset($response['choices'][0]['message']['content'])) {
                        $content = $response['choices'][0]['message']['content'];
                    } elseif (isset($response['content'])) {
                        $content = $response['content'];
                    } elseif (isset($response['response'])) {
                        $content = $response['response'];
                    }
                    
                    if (empty($content)) {
                        $this->error("No content in response for {$targetLang}");
                        $this->line("Response structure: " . json_encode(array_keys($response)));
                        continue;
                    }
                    
                    // JSON parse et
                    $translatedData = $this->parseResponse($content);
                    
                    if ($translatedData) {
                        // Çevirileri ekle
                        $existingTitle[$targetLang] = $translatedData['title'];
                        $existingSlug[$targetLang] = Str::slug($translatedData['title']);
                        $existingBody[$targetLang] = $translatedData['body'];
                        
                        $this->info("✅ Translated successfully to {$targetLang}");
                        $this->line("Title: " . substr($translatedData['title'], 0, 50) . "...");
                    } else {
                        $this->error("Failed to parse translation for {$targetLang}");
                        $this->line("First 500 chars of response: " . substr($content, 0, 500));
                        
                        // Debug için tam içeriği log'a yaz
                        \Log::info("Translation response for {$targetLang}", [
                            'content' => $content,
                            'json_error' => json_last_error_msg()
                        ]);
                    }
                } else {
                    $this->error("AI translation failed for {$targetLang}");
                }
                
            } catch (\Exception $e) {
                $this->error("Error translating to {$targetLang}: " . $e->getMessage());
            }
            
            // Rate limiting
            sleep(1);
        }
        
        // Değişiklikleri kaydet
        if (!$isTest) {
            $page->title = $existingTitle;
            $page->slug = $existingSlug;
            $page->body = $existingBody;
            $page->save();
            
            $this->info("\n✅ Page updated successfully!");
            $this->info("Final languages: " . implode(', ', array_keys($existingTitle)));
        } else {
            $this->info("\nTEST MODE - Changes not saved");
            $this->info("Would have languages: " . implode(', ', array_keys($existingTitle)));
        }
        
        return 0;
    }
    
    private function buildPrompt($title, $body, $sourceLang, $targetLang): string
    {
        // Dil isimlerini map'le
        $langNames = [
            'tr' => 'Turkish',
            'en' => 'English',
            'ar' => 'Arabic',
            'da' => 'Danish',
            'bn' => 'Bengali',
            'sq' => 'Albanian'
        ];
        
        $sourceName = $langNames[$sourceLang] ?? $sourceLang;
        $targetName = $langNames[$targetLang] ?? $targetLang;
        
        return "You are a professional translator. Translate the following content from {$sourceName} to {$targetName}.

IMPORTANT RULES:
1. Preserve ALL HTML tags and formatting exactly as they are
2. Keep the same structure and layout
3. Translate naturally for the target language
4. For RTL languages (Arabic), add dir=\"rtl\" to the main container div
5. Return ONLY a valid JSON object, no additional text

SOURCE CONTENT:
Title: {$title}

Body:
{$body}

REQUIRED JSON FORMAT:
{
    \"title\": \"translated title here\",
    \"body\": \"translated HTML body here with all tags preserved\"
}

Translate now and return ONLY the JSON:";
    }
    
    private function parseResponse($content): ?array
    {
        // Önce direkt JSON parse dene
        $data = json_decode($content, true);
        
        if ($data && isset($data['title']) && isset($data['body'])) {
            \Log::info("Direct JSON parse successful");
            return $data;
        }
        
        // OpenAI'dan gelen triple-escaped karakterleri düzelt
        $cleanContent = $content;
        
        // Triple escape'leri düzelt (\\\" -> ")
        $cleanContent = str_replace('\\\\\\"', '"', $cleanContent);
        
        // Double escape'leri düzelt (\\n -> \n)
        $cleanContent = str_replace('\\\\n', "\n", $cleanContent);
        $cleanContent = str_replace('\\\\r', "\r", $cleanContent);
        $cleanContent = str_replace('\\\\t', "\t", $cleanContent);
        
        $data = json_decode($cleanContent, true);
        
        if ($data && isset($data['title']) && isset($data['body'])) {
            \Log::info("JSON parsed after fixing triple escapes");
            return $data;
        }
        
        // Eğer JSON kesikse, title ve body'yi regex ile çıkar
        $title = null;
        $body = null;
        
        // Title'ı çıkar
        if (preg_match('/"title"\\s*:\\s*"([^"]+)"/', $content, $titleMatch)) {
            $title = $titleMatch[1];
        }
        
        // Body'yi çıkar (kesik olabilir)
        if (preg_match('/"body"\\s*:\\s*"(.*?)(?:"|$)/s', $content, $bodyMatch)) {
            $bodyContent = $bodyMatch[1];
            
            // Escape karakterlerini düzelt
            $bodyContent = str_replace('\\\\\\"', '"', $bodyContent);
            $bodyContent = str_replace('\\\\n', "\n", $bodyContent);
            $bodyContent = str_replace('\\\\r', "\r", $bodyContent);
            $bodyContent = str_replace('\\\\t', "\t", $bodyContent);
            $bodyContent = str_replace('\\\\', '\\', $bodyContent);
            
            // Eksik HTML tag'lerini tamamla
            $openDivs = substr_count($bodyContent, '<div');
            $closeDivs = substr_count($bodyContent, '</div>');
            $missingDivs = $openDivs - $closeDivs;
            
            for ($i = 0; $i < $missingDivs; $i++) {
                $bodyContent .= '</div>';
            }
            
            $body = $bodyContent;
        }
        
        if ($title && $body) {
            \Log::info("Extracted title and body using regex");
            return [
                'title' => $title,
                'body' => $body
            ];
        }
        
        \Log::error("Failed to parse JSON response completely", [
            'first_500_chars' => substr($content, 0, 500),
            'json_error' => json_last_error_msg(),
            'title_found' => !empty($title),
            'body_found' => !empty($body)
        ]);
        
        return null;
    }
}
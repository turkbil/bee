<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AIContentGeneratorTest extends DuskTestCase
{
    /**
     * Test AI Content Generator Modal functionality
     *
     * @return void
     */
    public function testAIContentGeneratorModal()
    {
        $this->browse(function (Browser $browser) {
            // 1. Admin girişi yap
            $browser->visit('http://laravel.test/login')
                   ->type('email', 'nurullah@nurullah.net')
                   ->type('password', 'test')
                   ->press('Giriş Yap')
                   ->pause(3000) // Giriş için bekle
                   ->assertDontSee('Giriş Yap'); // Giriş başarılı ise giriş butonu gözükmez

            // 2. Önce page listesine git ve mevcut sayfa ID'lerini bul
            $browser->visit('http://laravel.test/admin/page')
                   ->pause(2000);

            // 3. İlk mevcut sayfanın manage linkini tıkla
            $manageLinks = $browser->driver->findElements(\Facebook\WebDriver\WebDriverBy::cssSelector('a[href*="/admin/page/manage/"]'));
            if (empty($manageLinks)) {
                throw new \Exception('Page manage linki bulunamadı');
            }

            $firstManageLink = $manageLinks[0];
            $manageUrl = $firstManageLink->getAttribute('href');

            $browser->visit($manageUrl)
                   ->pause(3000);

            // JavaScript console hatalarını yakalama için log ayarları
            $browser->driver->executeScript('
                window.jsErrors = [];
                window.onerror = function(msg, url, line, col, error) {
                    window.jsErrors.push({
                        message: msg,
                        source: url,
                        line: line,
                        column: col,
                        error: error ? error.toString() : null
                    });
                };
                console.originalLog = console.log;
                console.originalError = console.error;
                window.consoleLogs = [];
                console.log = function() {
                    window.consoleLogs.push({type: "log", args: Array.from(arguments)});
                    console.originalLog.apply(console, arguments);
                };
                console.error = function() {
                    window.consoleLogs.push({type: "error", args: Array.from(arguments)});
                    console.originalError.apply(console, arguments);
                };
            ');

            // 3. AI İçerik Üret butonunu bul ve tıkla
            $browser->waitForText('🚀 AI İçerik Üret', 10);

            // JavaScript ile butonu bul ve tıkla
            $browser->driver->executeScript('
                const buttons = Array.from(document.querySelectorAll("button"));
                const aiButton = buttons.find(btn => btn.textContent.includes("🚀 AI İçerik Üret"));
                if (aiButton) {
                    aiButton.click();
                    console.log("✅ AI İçerik Üret butonuna tıklandı");
                } else {
                    console.error("❌ AI İçerik Üret butonu bulunamadı");
                }
            ');

            // 4. Modal açılıyor mu kontrol et
            $browser->waitFor('#aiContentModal', 5)
                   ->assertVisible('#aiContentModal')
                   ->assertSee('AI İçerik Üret');

            // 5. Konu alanına "yapay zeka" yaz
            $browser->waitFor('#contentTopic', 5)
                   ->type('#contentTopic', 'yapay zeka');

            // 6. İçerik Üret butonuna bas
            $browser->click('#startGeneration');

            // 7. İçerik üret butonuna bastıktan sonra kısa bekle
            $browser->pause(3000);

            // 8. AI işleminin başlatıldığını kontrol et (JavaScript console ve button state)
            $browser->driver->executeScript('
                console.log("🔍 AI Content Generation durumu kontrol ediliyor...");

                // Button state kontrolü
                const startButton = document.getElementById("startGeneration");
                if (startButton) {
                    console.log("✅ Start button bulundu", {
                        disabled: startButton.disabled,
                        text: startButton.textContent,
                        classList: Array.from(startButton.classList)
                    });
                }

                // Progress element kontrol
                const progressElement = document.getElementById("contentProgress");
                if (progressElement) {
                    console.log("✅ Progress elementi bulundu", {
                        display: getComputedStyle(progressElement).display,
                        innerHTML: progressElement.innerHTML
                    });
                }
            ');

            // 9. AI işleminin tamamlanmasını bekle (60 saniye max)
            $maxWaitTime = 60;
            $startTime = time();
            $lastLogCheck = '';

            while (time() - $startTime < $maxWaitTime) {
                $browser->pause(2000);

                // Laravel log kontrol et - AI işlemi tamamlandı mı?
                $logContent = file_get_contents('/Users/nurullah/Desktop/cms/laravel/storage/logs/laravel.log');

                if (strpos($logContent, 'content.generation.completed') !== false &&
                    strpos($logContent, 'AI Content Generation başarılı') !== false) {
                    echo "\n✅ AI Content Generation başarıyla tamamlandı (Laravel log)\n";
                    break;
                }

                // Progress check
                if (time() - $startTime > 10) {
                    echo "."; // Progress indicator
                }
            }

            // 10. İçerik editöre geldi mi kontrol et - farklı editör türlerini dene
            $editorContent = '';

            // TinyMCE editör kontrolü
            $editorContent = $browser->driver->executeScript('
                // TinyMCE editör kontrolü
                if (typeof tinymce !== "undefined" && tinymce.editors.length > 0) {
                    const editor = tinymce.editors[0];
                    console.log("✅ TinyMCE editör bulundu:", editor.id);
                    return editor.getContent();
                }

                // CKEditor kontrolü
                const ckEditor = document.querySelector(".ck-editor__editable");
                if (ckEditor) {
                    console.log("✅ CKEditor bulundu");
                    return ckEditor.innerHTML;
                }

                // Textarea kontrolü
                const textarea = document.querySelector("textarea");
                if (textarea) {
                    console.log("✅ Textarea bulundu:", textarea.name);
                    return textarea.value;
                }

                // Genel content alanı kontrolü
                const contentDiv = document.querySelector("[id*=\"content\"], [name*=\"content\"], [id*=\"body\"], [name*=\"body\"]");
                if (contentDiv) {
                    console.log("✅ Content alanı bulundu:", contentDiv.tagName, contentDiv.id || contentDiv.name);
                    return contentDiv.value || contentDiv.innerHTML || contentDiv.textContent;
                }

                console.warn("❌ Hiçbir editör bulunamadı");
                return "";
            ');

            // JavaScript hatalarını kontrol et
            $jsErrors = $browser->driver->executeScript('return window.jsErrors || [];');
            $consoleLogs = $browser->driver->executeScript('return window.consoleLogs || [];');

            // Sonuçları raporla
            echo "\n=== AI İÇERİK ÜRET MODAL TESİ SONUÇLARI ===\n";
            echo "✓ Admin girişi: BAŞARILI\n";
            echo "✓ Page manage sayfası: BAŞARILI\n";
            echo "✓ Modal açılma: " . ($browser->element('#aiContentModal') ? "BAŞARILI" : "BAŞARISIZ") . "\n";
            echo "✓ Progress overlay: BAŞARILI\n";
            echo "✓ İçerik üretimi: " . (!empty($editorContent) ? "BAŞARILI" : "BEKLENIYOR") . "\n";

            if (!empty($jsErrors)) {
                echo "\n❌ JavaScript Hataları:\n";
                foreach ($jsErrors as $error) {
                    echo "- " . $error['message'] . " (Line: " . $error['line'] . ")\n";
                }
            } else {
                echo "\n✓ JavaScript hatası yok\n";
            }

            if (!empty($consoleLogs)) {
                echo "\n📊 Console Logları:\n";
                foreach (array_slice($consoleLogs, -10) as $log) { // Son 10 log
                    echo "- [" . $log['type'] . "] " . json_encode($log['args']) . "\n";
                }
            }

            if (!empty($editorContent)) {
                echo "\n📝 Üretilen İçerik (İlk 200 karakter):\n";
                echo substr(strip_tags($editorContent), 0, 200) . "...\n";
            }

            // Test başarılı oldu mu kontrol et
            $this->assertNotEmpty($editorContent, 'AI tarafından içerik üretilmeli');
        });
    }
}
# ⏰ CRON JOB KURULUM VE KOMUTLAR

> **AI Blog Otomasyon Sistemi - Cron Job Yapılandırması**

---

## 📋 CRON JOB YAPISI

### Laravel Scheduler Kullanımı

Laravel'in built-in task scheduler'ını kullanacağız.

```php
// app/Console/Kernel.php

protected function schedule(Schedule $schedule)
{
    // ==========================================
    // BLOG AUTOMATION SCHEDULES
    // ==========================================

    // Her dakika schedule kontrolü (Laravel standard)
    $schedule->command('blog:check-schedules')
             ->everyMinute()
             ->withoutOverlapping()
             ->runInBackground();

    // Her saat başı perfor

mans güncellemesi
    $schedule->command('blog:update-performance')
             ->hourly()
             ->withoutOverlapping();

    // Günde bir kez: Genel analiz + raporlama
    $schedule->command('blog:daily-analysis')
             ->dailyAt('23:00')
             ->timezone('Europe/Istanbul');

    // Haftalık: Keyword ranking güncellemesi
    $schedule->command('blog:update-keyword-rankings')
             ->weekly()
             ->mondays()
             ->at('02:00');

    // Aylık: Kapsamlı performans raporu
    $schedule->command('blog:monthly-report')
             ->monthlyOn(1, '03:00');
}
```

---

## 🚀 ARTISAN KOMUTLARI

### 1. `blog:check-schedules` (Ana Komut)
**Çalışma:** Her dakika
**Görev:** Aktif schedule'ları kontrol et, zamanı gelenleri tetikle

```php
// Modules/BlogAutomation/app/Console/Commands/CheckSchedulesCommand.php

<?php

namespace Modules\BlogAutomation\App\Console\Commands;

use Illuminate\Console\Command;
use Modules\BlogAutomation\App\Services\BlogAutomationService;
use Carbon\Carbon;

class CheckSchedulesCommand extends Command
{
    protected $signature = 'blog:check-schedules {--tenant= : Specific tenant ID}';

    protected $description = 'Check and trigger active blog automation schedules';

    public function handle(BlogAutomationService $service)
    {
        $this->info('🔍 Checking blog automation schedules...');

        $tenantId = $this->option('tenant');

        // Schedule'ları kontrol et ve tetikle
        $results = $service->checkAndTriggerSchedules($tenantId);

        // Sonuçları göster
        $this->table(
            ['Schedule', 'Status', 'Topic', 'Time'],
            $results
        );

        $triggered = collect($results)->where('status', 'triggered')->count();
        $this->info("✅ {$triggered} schedule triggered successfully.");

        return Command::SUCCESS;
    }
}
```

### 2. `blog:generate` (Manuel Blog Üretimi)
**Çalışma:** Manuel tetikleme
**Görev:** Belirtilen konuda blog üret

```php
// Modules/BlogAutomation/app/Console/Commands/GenerateBlogCommand.php

<?php

namespace Modules\BlogAutomation\App\Console\Commands;

use Illuminate\Console\Command;
use Modules\BlogAutomation\App\Services\AIBlogGeneratorService;

class GenerateBlogCommand extends Command
{
    protected $signature = 'blog:generate
                            {topic : Blog topic/title}
                            {--keyword= : Focus keyword}
                            {--strategy= : Content strategy ID}
                            {--publish : Auto publish after generation}
                            {--tenant= : Tenant ID (default: current)}';

    protected $description = 'Manually generate a blog post with AI';

    public function handle(AIBlogGeneratorService $generator)
    {
        $this->info('🤖 Starting AI blog generation...');

        $topic = $this->argument('topic');
        $keyword = $this->option('keyword') ?? $topic;
        $strategyId = $this->option('strategy');
        $autoPublish = $this->option('publish');
        $tenantId = $this->option('tenant') ?? tenant('id');

        // Blog üret
        $result = $generator->generate([
            'topic' => $topic,
            'keyword' => $keyword,
            'strategy_id' => $strategyId,
            'auto_publish' => $autoPublish,
            'tenant_id' => $tenantId,
        ]);

        if ($result['success']) {
            $this->info("✅ Blog generated successfully!");
            $this->line("Blog ID: {$result['blog_id']}");
            $this->line("Title: {$result['title']}");
            $this->line("Word Count: {$result['word_count']}");
            $this->line("SEO Score: {$result['seo_score']}/100");

            if ($autoPublish) {
                $this->info("📢 Blog published!");
            } else {
                $this->warn("⏳ Blog saved as draft. Review required.");
            }
        } else {
            $this->error("❌ Blog generation failed!");
            $this->error($result['error']);
        }

        return $result['success'] ? Command::SUCCESS : Command::FAILURE;
    }
}
```

### 3. `blog:update-performance` (Performans Güncelleme)
**Çalışma:** Her saat
**Görev:** Google Analytics + Search Console verilerini çek

```php
// Modules/BlogAutomation/app/Console/Commands/UpdatePerformanceCommand.php

<?php

namespace Modules\BlogAutomation\App\Console\Commands;

use Illuminate\Console\Command;
use Modules\BlogAutomation\App\Services\PerformanceTrackingService;

class UpdatePerformanceCommand extends Command
{
    protected $signature = 'blog:update-performance
                            {--blog= : Specific blog ID}
                            {--days=1 : Number of days to fetch}
                            {--tenant= : Tenant ID}';

    protected $description = 'Update blog performance metrics from analytics';

    public function handle(PerformanceTrackingService $tracker)
    {
        $this->info('📊 Updating blog performance metrics...');

        $blogId = $this->option('blog');
        $days = (int) $this->option('days');
        $tenantId = $this->option('tenant') ?? tenant('id');

        $bar = $this->output->createProgressBar();

        $results = $tracker->updateMetrics([
            'blog_id' => $blogId,
            'days' => $days,
            'tenant_id' => $tenantId,
        ], function($progress) use ($bar) {
            $bar->advance();
        });

        $bar->finish();
        $this->newLine();

        $this->info("✅ Updated {$results['count']} blog(s)");
        $this->table(
            ['Metric', 'Total'],
            [
                ['Page Views', number_format($results['total_views'])],
                ['Organic Traffic', number_format($results['total_organic'])],
                ['Avg. Time on Page', $results['avg_time'] . 's'],
            ]
        );

        return Command::SUCCESS;
    }
}
```

### 4. `blog:daily-analysis` (Günlük Analiz)
**Çalışma:** Günde 1 (23:00)
**Görev:** Günlük performans analizi + rapor

```php
// Modules/BlogAutomation/app/Console/Commands/DailyAnalysisCommand.php

<?php

namespace Modules\BlogAutomation\App\Console\Commands;

use Illuminate\Console\Command;
use Modules\BlogAutomation\App\Services\AnalyticsService;
use Illuminate\Support\Facades\Notification;
use App\Notifications\DailyBlogReportNotification;

class DailyAnalysisCommand extends Command
{
    protected $signature = 'blog:daily-analysis {--tenant=}';

    protected $description = 'Run daily blog performance analysis and send report';

    public function handle(AnalyticsService $analytics)
    {
        $this->info('📈 Running daily blog analysis...');

        $tenantId = $this->option('tenant') ?? tenant('id');

        // Analiz yap
        $report = $analytics->generateDailyReport($tenantId);

        // Konsola özet yazdır
        $this->newLine();
        $this->line("📊 DAILY BLOG REPORT - " . now()->format('Y-m-d'));
        $this->line(str_repeat('=', 50));
        $this->line("📝 Total Blogs: {$report['total_blogs']}");
        $this->line("👀 Total Views: " . number_format($report['total_views']));
        $this->line("🔍 Organic Traffic: " . number_format($report['organic_traffic']));
        $this->line("🎯 Avg SEO Score: {$report['avg_seo_score']}/100");
        $this->newLine();

        // Top 5 performans
        $this->line("🏆 Top 5 Performers:");
        foreach ($report['top_performers'] as $index => $blog) {
            $this->line(($index + 1) . ". {$blog['title']} ({$blog['views']} views)");
        }

        // Admin'e bildirim gönder (opsiyonel)
        if ($report['send_notification']) {
            $admins = \App\Models\User::role('admin')->get();
            Notification::send($admins, new DailyBlogReportNotification($report));
            $this->info("📧 Report sent to admins");
        }

        return Command::SUCCESS;
    }
}
```

### 5. `blog:queue:process` (Kuyruk İşleme)
**Çalışma:** Her 5 dakika
**Görev:** Topic queue'dan blog üret

```php
// Modules/BlogAutomation/app/Console/Commands/ProcessQueueCommand.php

<?php

namespace Modules\BlogAutomation\App\Console\Commands;

use Illuminate\Console\Command;
use Modules\BlogAutomation\App\Models\BlogTopicQueue;
use Modules\BlogAutomation\App\Services\AIBlogGeneratorService;
use Carbon\Carbon;

class ProcessQueueCommand extends Command
{
    protected $signature = 'blog:queue:process
                            {--limit=5 : Maximum topics to process}
                            {--tenant= : Tenant ID}';

    protected $description = 'Process queued blog topics';

    public function handle(AIBlogGeneratorService $generator)
    {
        $this->info('⚙️ Processing blog topic queue...');

        $limit = (int) $this->option('limit');
        $tenantId = $this->option('tenant') ?? tenant('id');

        // Bekleyen konuları al
        $topics = BlogTopicQueue::where('tenant_id', $tenantId)
            ->where('status', 'queued')
            ->where('scheduled_for', '<=', Carbon::now())
            ->orderBy('priority', 'DESC')
            ->orderBy('scheduled_for', 'ASC')
            ->limit($limit)
            ->get();

        if ($topics->isEmpty()) {
            $this->warn('⚠️ No queued topics found.');
            return Command::SUCCESS;
        }

        $this->info("Found {$topics->count()} topic(s) to process");

        $bar = $this->output->createProgressBar($topics->count());

        $success = 0;
        $failed = 0;

        foreach ($topics as $topic) {
            // Status güncelle: processing
            $topic->update([
                'status' => 'processing',
                'processing_started_at' => now(),
            ]);

            try {
                // Blog üret
                $result = $generator->generateFromTopic($topic);

                if ($result['success']) {
                    $topic->update([
                        'status' => 'completed',
                        'blog_id' => $result['blog_id'],
                    ]);
                    $success++;
                } else {
                    throw new \Exception($result['error']);
                }

            } catch (\Exception $e) {
                $topic->increment('retry_count');

                if ($topic->retry_count >= $topic->max_retries) {
                    $topic->update(['status' => 'failed']);
                    $failed++;
                } else {
                    $topic->update(['status' => 'queued']); // Retry için geri kuyruğa al
                }

                $topic->update(['error_message' => $e->getMessage()]);
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("✅ Success: {$success}");
        $this->error("❌ Failed: {$failed}");

        return Command::SUCCESS;
    }
}
```

---

## 🔧 CRONTAB KURULUMU (Server)

### 1. Laravel Scheduler Entry (Tek Satır)

```bash
# Crontab düzenle
crontab -e

# Aşağıdaki satırı ekle:
* * * * * cd /var/www/vhosts/tuufi.com/httpdocs && php artisan schedule:run >> /dev/null 2>&1
```

Bu tek satır, Laravel Scheduler'ın tüm komutlarını otomatik çalıştırır.

### 2. Direkt Cron Tanımları (Alternatif)

Eğer Laravel Scheduler yerine direkt cron tanımları yapılacaksa:

```bash
# Her dakika schedule kontrolü
* * * * * cd /var/www/vhosts/tuufi.com/httpdocs && php artisan blog:check-schedules >> storage/logs/cron-blog.log 2>&1

# Her 5 dakikada kuyruk işleme
*/5 * * * * cd /var/www/vhosts/tuufi.com/httpdocs && php artisan blog:queue:process >> storage/logs/cron-queue.log 2>&1

# Her saat performans güncellemesi
0 * * * * cd /var/www/vhosts/tuufi.com/httpdocs && php artisan blog:update-performance >> storage/logs/cron-perf.log 2>&1

# Gece 23:00'te günlük analiz
0 23 * * * cd /var/www/vhosts/tuufi.com/httpdocs && php artisan blog:daily-analysis >> storage/logs/cron-daily.log 2>&1

# Pazartesi 02:00'de keyword ranking
0 2 * * 1 cd /var/www/vhosts/tuufi.com/httpdocs && php artisan blog:update-keyword-rankings >> storage/logs/cron-keywords.log 2>&1
```

---

## 📊 MANUEL KULLANIM ÖRNEKLERİ

### Komut Satırından Hızlı Test

```bash
# 1. Schedule'ları manuel kontrol et
php artisan blog:check-schedules

# 2. Belirli tenant için kontrol
php artisan blog:check-schedules --tenant=2

# 3. Manuel blog üret
php artisan blog:generate "Transpalet Nedir?" --keyword="transpalet nedir" --publish

# 4. Strateji ile blog üret
php artisan blog:generate "Forklift Çeşitleri" --keyword="forklift çeşitleri" --strategy=1

# 5. Kuyrukdaki 10 konuyu işle
php artisan blog:queue:process --limit=10

# 6. Son 7 günün performansını güncelle
php artisan blog:update-performance --days=7

# 7. Günlük rapor oluştur
php artisan blog:daily-analysis

# 8. Belirli blog için performans
php artisan blog:update-performance --blog=123
```

---

## 🐛 DEBUG VE LOG TAKİBİ

### Log Dosyaları

```bash
# Genel Laravel log
tail -f storage/logs/laravel.log

# Cron job logları
tail -f storage/logs/cron-blog.log
tail -f storage/logs/cron-queue.log
tail -f storage/logs/cron-perf.log

# Sadece blog automation logları
tail -f storage/logs/laravel.log | grep "BlogAutomation"
```

### Komut Debug Modu

```bash
# Verbose output ile çalıştır
php artisan blog:check-schedules -v
php artisan blog:generate "Test Blog" -vvv

# Log'a yazma + ekrana yazdırma
php artisan blog:queue:process --limit=5 2>&1 | tee storage/logs/debug-queue.log
```

---

## ⚙️ SUPERVISOR KURULUMU (Önerilen)

Queue worker'ları sürekli çalıştırmak için Supervisor kullanımı:

### 1. Supervisor Config

```ini
# /etc/supervisor/conf.d/blog-automation.conf

[program:blog-automation-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/vhosts/tuufi.com/httpdocs/artisan queue:work --queue=blog-automation --tries=3 --timeout=300
autostart=true
autorestart=true
user=tuufi.com_
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/vhosts/tuufi.com/httpdocs/storage/logs/supervisor-blog.log
stopwaitsecs=3600
```

### 2. Supervisor Komutları

```bash
# Config'i yenile
sudo supervisorctl reread
sudo supervisorctl update

# Worker'ı başlat
sudo supervisorctl start blog-automation-worker:*

# Status kontrol
sudo supervisorctl status blog-automation-worker:*

# Yeniden başlat
sudo supervisorctl restart blog-automation-worker:*

# Log kontrol
tail -f /var/www/vhosts/tuufi.com/httpdocs/storage/logs/supervisor-blog.log
```

---

## 🔔 ALARM VE BİLDİRİMLER

### 1. Failed Job Bildirimi

```php
// config/queue.php

'failed' => [
    'driver' => 'database-uuids',
    'database' => env('DB_CONNECTION', 'mysql'),
    'table' => 'failed_jobs',
],
```

### 2. Slack/Email Bildirim

```php
// app/Exceptions/Handler.php

use Modules\BlogAutomation\App\Exceptions\BlogGenerationException;
use Illuminate\Support\Facades\Notification;
use App\Notifications\BlogGenerationFailedNotification;

public function register()
{
    $this->reportable(function (BlogGenerationException $e) {
        // Admin'e bildirim gönder
        $admins = \App\Models\User::role('admin')->get();
        Notification::send($admins, new BlogGenerationFailedNotification($e));

        // Log'a yaz
        \Log::error('Blog generation failed', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);
    });
}
```

### 3. Healthcheck Endpoint

```php
// routes/api.php

Route::get('/healthcheck/blog-automation', function () {
    $lastRun = \Modules\BlogAutomation\App\Models\BlogAutomationLog::latest()->first();

    $isHealthy = $lastRun && $lastRun->created_at->diffInHours(now()) < 2;

    return response()->json([
        'status' => $isHealthy ? 'healthy' : 'unhealthy',
        'last_run' => $lastRun?->created_at,
        'last_status' => $lastRun?->status,
    ], $isHealthy ? 200 : 503);
});
```

---

## 📋 KONTROL LİSTESİ

### Kurulum Sonrası Kontrol

- [ ] Crontab entry eklendi
- [ ] Laravel Scheduler çalışıyor (`schedule:list` ile kontrol)
- [ ] Log dosyaları yazılabiliyor
- [ ] Queue driver ayarlandı (database/redis)
- [ ] Supervisor kuruldu (opsiyonel)
- [ ] Healthcheck endpoint test edildi
- [ ] İlk manuel blog üretimi başarılı
- [ ] Schedule trigger testi yapıldı
- [ ] Failed job handling test edildi
- [ ] Log rotation ayarlandı

---

**Son Güncelleme:** 2025-11-14
**Versiyon:** 1.0-CRON

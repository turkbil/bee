<?php

use Illuminate\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | Horizon Domain
    |--------------------------------------------------------------------------
    |
    | This is the subdomain where Horizon will be accessible from. If this
    | setting is null, Horizon will reside under the same domain as the
    | application. Otherwise, this value will serve as the subdomain.
    |
    */

    'domain' => env('HORIZON_DOMAIN'),

    /*
    |--------------------------------------------------------------------------
    | Horizon Path
    |--------------------------------------------------------------------------
    |
    | This is the URI path where Horizon will be accessible from. Feel free
    | to change this path to anything you like. Note that the URI will not
    | affect the paths of its internal API that aren't exposed to users.
    |
    */

    'path' => env('HORIZON_PATH', 'horizon'),

    /*
    |--------------------------------------------------------------------------
    | Horizon Redis Connection
    |--------------------------------------------------------------------------
    |
    | This is the name of the Redis connection where Horizon will store the
    | meta information required for it to function. It includes the list
    | of supervisors, failed jobs, job metrics, and other information.
    |
    */

    'use' => 'default',

    /*
    |--------------------------------------------------------------------------
    | Horizon Redis Prefix
    |--------------------------------------------------------------------------
    |
    | This prefix will be used when storing all Horizon data in Redis. You
    | may modify the prefix when you are running multiple installations
    | of Horizon on the same server so that they don't have problems.
    |
    */

    'prefix' => env(
        'HORIZON_PREFIX',
        Str::slug(env('APP_NAME', 'laravel'), '_').'_horizon:'
    ),

    /*
    |--------------------------------------------------------------------------
    | Horizon Route Middleware
    |--------------------------------------------------------------------------
    |
    | These middleware will get attached onto each Horizon route, giving you
    | the chance to add your own middleware to this list or change any of
    | the existing middleware. Or, you can simply stick with this list.
    |
    */

    'middleware' => env('APP_ENV') === 'production' ? ['web', 'auth'] : ['web'],

    /*
    |--------------------------------------------------------------------------
    | Queue Wait Time Thresholds
    |--------------------------------------------------------------------------
    |
    | This option allows you to configure when the LongWaitDetected event
    | will be fired. Every connection / queue combination may have its
    | own, unique threshold (in seconds) before this event is fired.
    |
    */

    'waits' => [
        'redis:default' => 60,
    ],

    /*
    |--------------------------------------------------------------------------
    | Job Trimming Times
    |--------------------------------------------------------------------------
    |
    | Here you can configure for how long (in minutes) you desire Horizon to
    | persist the recent and failed jobs. Typically, recent jobs are kept
    | for one hour while all failed jobs are stored for an entire week.
    |
    */

    'trim' => [
        'recent' => 60,
        'pending' => 60,
        'completed' => 60,
        'recent_failed' => 10080,
        'failed' => 10080,
        'monitored' => 10080,
    ],

    /*
    |--------------------------------------------------------------------------
    | Silenced Jobs
    |--------------------------------------------------------------------------
    |
    | Silencing a job will instruct Horizon to not place the job in the list
    | of completed jobs within the Horizon dashboard. This setting may be
    | used to fully remove any noisy jobs from the completed jobs list.
    |
    */

    'silenced' => [
        // App\Jobs\ExampleJob::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Metrics
    |--------------------------------------------------------------------------
    |
    | Here you can configure how many snapshots should be kept to display in
    | the metrics graph. This will get used in combination with Horizon's
    | `horizon:snapshot` schedule to define how long to retain metrics.
    |
    */

    'metrics' => [
        'trim_snapshots' => [
            'job' => 24,
            'queue' => 24,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Fast Termination
    |--------------------------------------------------------------------------
    |
    | When this option is enabled, Horizon's "terminate" command will not
    | wait on all of the workers to terminate unless the --wait option
    | is provided. Fast termination can shorten deployment delay by
    | allowing a new instance of Horizon to start while the last
    | instance will continue to terminate each of its workers.
    |
    */

    'fast_termination' => false,

    /*
    |--------------------------------------------------------------------------
    | Memory Limit (MB)
    |--------------------------------------------------------------------------
    |
    | This value describes the maximum amount of memory the Horizon master
    | supervisor may consume before it is terminated and restarted. For
    | configuring these limits on your workers, see the next section.
    |
    */

    'memory_limit' => 512,

    /*
    |--------------------------------------------------------------------------
    | Queue Worker Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may define the queue worker settings used by your application
    | in all environments. These supervisors and settings handle all your
    | queued jobs and will be provisioned by Horizon during deployment.
    |
    */

    'defaults' => [
        // AI Translation & Processing Queue
        'ai-supervisor' => [
            'connection' => 'redis',
            'queue' => ['ai-translation', 'ai-content', 'ai-file-analysis', 'translation', 'ai', 'blog-ai', 'critical'],
            'balance' => 'auto', // 🔧 OPTIMIZED: auto balance - job yoksa worker spawn etme!
            'autoScalingStrategy' => 'time', // 🔧 OPTIMIZED: time-based scaling
            'minProcesses' => 1, // 🔧 OPTIMIZED: Minimum 1 worker (boş queue için bile)
            'maxProcesses' => 2, // 🔧 OPTIMIZED: Max 2 worker (CPU tasarrufu)
            'balanceMaxShift' => 1,
            'balanceCooldown' => 3,
            'maxTime' => 1800,
            'maxJobs' => 50,
            'memory' => 512,
            'tries' => 3,
            'timeout' => 1200, // 🔧 FIX: 20 dakika - Blog AI generation için artırıldı
            'nice' => 0,
        ],
        
        // Tenant Isolated Queue
        'tenant-supervisor' => [
            'connection' => 'redis',
            'queue' => ['tenant_isolated', 'default', 'hls', 'tenant_1001_default', 'tenant_1001_hls'],
            'balance' => 'auto',
            'autoScalingStrategy' => 'time',
            'minProcesses' => 1, // 🔧 OPTIMIZED: Minimum 1 worker
            'maxProcesses' => 2, // 🔧 OPTIMIZED: 4 → 2 (CPU tasarrufu)
            'maxTime' => 3600,
            'maxJobs' => 200,
            'memory' => 256,
            'tries' => 3,
            'timeout' => 300, // HLS conversion için 5 dakika
            'nice' => 0,
        ],

        // Muzibu Module Queue (All Muzibu Jobs)
        'muzibu-supervisor' => [
            'connection' => 'redis',
            'queue' => [
                'muzibu_tenant_1001_hls',   // Tenant 1001 HLS conversion - ÖNCELİK!
                'muzibu_hls',               // HLS conversion (generic)
                'muzibu-abuse-scan',        // Abuse detection scanning
                'muzibu_my_playlist',       // Playlist cover generation (Leonardo AI)
                'muzibu_isolated',          // Bulk operations, translations
                'muzibu_seo',               // SEO generation (OpenAI GPT-4)
            ],
            'balance' => 'auto',  // AUTO: Worker'ları queue yoğunluğuna göre dağıt
            'autoScalingStrategy' => 'time',
            'minProcesses' => 20,  // 🔧 STABLE: 20 worker (60 sunucuyu patlattı!)
            'maxProcesses' => 20, // 🔧 STABLE: Güvenli değer
            'maxTime' => 0,
            'maxJobs' => 5000,    // 🚀 ARTTIRILDI: 2000 → 5000 (daha az restart)
            'memory' => 512,      // Memory artırıldı (HLS + AI için)
            'tries' => 2,
            'timeout' => 600, // 10 dakika - HLS conversion için
            'nice' => 5,
        ],
        
        // Background Tasks Queue
        'background-supervisor' => [
            'connection' => 'redis',
            'queue' => ['background', 'maintenance'],
            'balance' => 'auto',
            'autoScalingStrategy' => 'time',
            'minProcesses' => 1, // 🔧 OPTIMIZED: Minimum 1 worker
            'maxProcesses' => 1, // 🔧 OPTIMIZED: Max 1 worker (background için yeterli)
            'maxTime' => 0,
            'maxJobs' => 1000,
            'memory' => 128,
            'tries' => 1,
            'timeout' => 60,
            'nice' => 10,
        ],
    ],

    'environments' => [
        'production' => [
            'ai-supervisor' => [
                'connection' => 'redis',
                'queue' => ['ai-translation', 'ai-content', 'ai-file-analysis', 'translation', 'ai', 'blog-ai', 'critical'],
                'maxProcesses' => 2, // 🔧 OPTIMIZED: 8 → 2 (CPU kullanımını azaltmak için)
                'minProcesses' => 1, // 🔧 OPTIMIZED: Her zaman en az 1 worker
                'balanceMaxShift' => 1, // 🔧 OPTIMIZED: Daha yavaş scale
                'balanceCooldown' => 5, // 🔧 OPTIMIZED: Daha uzun cooldown
                'memory' => 512, // 🔧 OPTIMIZED: 1024 → 512 (memory tasarrufu)
                'timeout' => 1200, // 🔧 FIX: 20 dakika - Blog AI generation için artırıldı
                'tries' => 2,
            ],
            'tenant-supervisor' => [
                'maxProcesses' => 2, // 🔧 OPTIMIZED: 6 → 2 (CPU kullanımını azaltmak için)
                'minProcesses' => 1, // 🔧 OPTIMIZED: Her zaman en az 1 worker
                'balanceMaxShift' => 1,
                'balanceCooldown' => 5, // 🔧 OPTIMIZED: Daha uzun cooldown
                'memory' => 256, // 🔧 OPTIMIZED: 512 → 256 (memory tasarrufu)
            ],
            'muzibu-supervisor' => [
                'maxProcesses' => 20, // 🔧 STABLE: 20 worker (60 sunucuyu patlattı!)
                'minProcesses' => 20, // 🔧 STABLE: Tüm worker'ları aktif et
                'memory' => 512,      // 🚀 INCREASED: 256 → 512 (HLS + AI memory needs)
                'timeout' => 600,
            ],
            'background-supervisor' => [
                'maxProcesses' => 1, // 🔧 OPTIMIZED: 2 → 1 (background işler için yeterli)
                'minProcesses' => 1,
                'memory' => 128, // 🔧 OPTIMIZED: 256 → 128 (background için yeterli)
            ],
        ],

        'local' => [
            'ai-supervisor' => [
                'connection' => 'redis',
                'queue' => ['ai-translation', 'ai-content', 'ai-file-analysis', 'translation', 'ai', 'blog-ai', 'critical'],
                'maxProcesses' => 2,
                'timeout' => 1200, // 🔧 FIX: 20 dakika - Blog AI generation için
                'tries' => 2,
            ],
            'tenant-supervisor' => [
                'maxProcesses' => 1,
            ],
            'muzibu-supervisor' => [
                'maxProcesses' => 2,
            ],
            'background-supervisor' => [
                'maxProcesses' => 1,
            ],
        ],
    ],
];

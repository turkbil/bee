<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestMailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:test {to? : Email address to send test mail}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test mail configuration by sending a test email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $to = $this->argument('to') ?? $this->ask('Email address to send test mail');

        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
            $this->error('❌ Invalid email address!');
            return 1;
        }

        $this->info("📧 Sending test mail to: {$to}");
        $this->info("📌 MAIL_MAILER: " . config('mail.default'));
        $this->info("📌 MAIL_FROM: " . config('mail.from.address'));

        try {
            Mail::raw('🎉 Test mail from ' . config('app.name') . "\n\nIf you receive this, your mail configuration is working correctly!\n\nSent at: " . now()->format('Y-m-d H:i:s'), function($msg) use ($to) {
                $msg->to($to)
                    ->subject('✅ Test Mail - ' . config('app.name'));
            });

            $this->info("✅ Test mail sent successfully!");
            $this->info("📬 Check your inbox: {$to}");

            return 0;
        } catch (\Exception $e) {
            $this->error("❌ Failed to send mail!");
            $this->error("Error: " . $e->getMessage());

            if (config('mail.default') === 'ses') {
                $this->warn("\n💡 AWS SES Checklist:");
                $this->warn("   - AWS credentials configured in .env?");
                $this->warn("   - Domain verified in AWS SES?");
                $this->warn("   - Moved out of sandbox mode?");
                $this->warn("   - IAM user has SES permissions?");
            }

            return 1;
        }
    }
}

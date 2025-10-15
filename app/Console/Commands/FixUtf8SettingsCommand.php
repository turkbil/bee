<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\SettingManagement\App\Models\Setting;
use Modules\SettingManagement\App\Models\SettingValue;

class FixUtf8SettingsCommand extends Command
{
    protected $signature = 'settings:fix-utf8 {--dry-run : Run without making changes}';
    protected $description = 'Fix invalid UTF-8 characters in settings values';

    public function handle()
    {
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->info('🔍 Running in DRY-RUN mode (no changes will be made)');
        }

        $this->info('Checking Settings table for invalid UTF-8...');
        $this->checkSettings($dryRun);

        $this->info('');
        $this->info('Checking SettingValues table for invalid UTF-8...');
        $this->checkSettingValues($dryRun);

        $this->newLine();
        if ($dryRun) {
            $this->warn('⚠️  This was a DRY-RUN. Run without --dry-run to apply fixes.');
        } else {
            $this->info('✅ UTF-8 sanitization completed!');
        }
    }

    private function checkSettings($dryRun)
    {
        $settings = Setting::all();
        $fixed = 0;

        foreach ($settings as $setting) {
            $changed = false;
            $original = [];

            // Check label
            if ($setting->label && !mb_check_encoding($setting->label, 'UTF-8')) {
                $original['label'] = $setting->label;
                $setting->label = mb_convert_encoding($setting->label, 'UTF-8', 'UTF-8');
                $changed = true;
            }

            // Check default_value
            if ($setting->default_value && !mb_check_encoding($setting->default_value, 'UTF-8')) {
                $original['default_value'] = $setting->default_value;
                $setting->default_value = mb_convert_encoding($setting->default_value, 'UTF-8', 'UTF-8');
                $changed = true;
            }

            if ($changed) {
                $this->warn("❌ Setting #{$setting->id} ({$setting->key}) has invalid UTF-8");

                foreach ($original as $field => $value) {
                    $this->line("   Field: {$field}");
                    $this->line("   Invalid bytes: " . bin2hex(substr($value, 0, 100)));
                }

                if (!$dryRun) {
                    $setting->save();
                    $this->info("   ✅ Fixed!");
                }

                $fixed++;
            }
        }

        if ($fixed === 0) {
            $this->info('✅ No invalid UTF-8 found in Settings table');
        } else {
            $this->info("Found {$fixed} settings with invalid UTF-8");
        }
    }

    private function checkSettingValues($dryRun)
    {
        $values = SettingValue::all();
        $fixed = 0;

        foreach ($values as $value) {
            if ($value->value && is_string($value->value) && !mb_check_encoding($value->value, 'UTF-8')) {
                $setting = Setting::find($value->setting_id);
                $settingKey = $setting ? $setting->key : 'unknown';

                $this->warn("❌ SettingValue #{$value->id} (Setting: {$settingKey}) has invalid UTF-8");
                $this->line("   Value length: " . strlen($value->value) . " bytes");
                $this->line("   First 100 bytes (hex): " . bin2hex(substr($value->value, 0, 100)));

                if (!$dryRun) {
                    $value->value = mb_convert_encoding($value->value, 'UTF-8', 'UTF-8');
                    $value->save();
                    $this->info("   ✅ Fixed!");
                }

                $fixed++;
            }
        }

        if ($fixed === 0) {
            $this->info('✅ No invalid UTF-8 found in SettingValues table');
        } else {
            $this->info("Found {$fixed} setting values with invalid UTF-8");
        }
    }
}

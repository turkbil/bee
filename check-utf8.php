<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Modules\SettingManagement\App\Models\Setting;
use Modules\SettingManagement\App\Models\SettingValue;

echo "Checking Settings Group 6 for UTF-8 issues...\n\n";

$settings = Setting::where('group_id', 6)->orderBy('sort_order')->get();

foreach ($settings as $setting) {
    $value = SettingValue::where('setting_id', $setting->id)->first();
    $finalValue = $value ? $value->value : $setting->default_value;

    echo "ID: {$setting->id} | Key: {$setting->key}\n";

    if ($finalValue && is_string($finalValue)) {
        $valid = mb_check_encoding($finalValue, 'UTF-8');
        echo "  Value length: " . strlen($finalValue) . " bytes\n";
        echo "  UTF-8: " . ($valid ? "✅ OK" : "❌ INVALID") . "\n";

        if (!$valid) {
            echo "  HEX (first 100 bytes): " . bin2hex(substr($finalValue, 0, 100)) . "\n";
            echo "  RAW (first 100 chars): " . substr($finalValue, 0, 100) . "\n";
        }
    } else {
        echo "  Value: " . ($finalValue ? gettype($finalValue) : 'NULL') . "\n";
    }

    echo "\n";
}

echo "Done.\n";

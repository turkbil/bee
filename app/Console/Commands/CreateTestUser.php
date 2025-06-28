<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class CreateTestUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-test-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create test admin user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Admin kullanıcısı oluştur
        $user = User::where('email', 'nurullah@turkbil.net')->first();
        if (!$user) {
            $user = User::create([
                'name' => 'Nurullah',
                'email' => 'nurullah@turkbil.net', 
                'password' => bcrypt('password'),
                'is_active' => true,
                'email_verified_at' => now()
            ]);
            $this->info('Admin kullanıcısı oluşturuldu: ' . $user->email);
        } else {
            $this->info('Admin kullanıcısı zaten mevcut: ' . $user->email);
        }
        
        // Admin rolünü ata
        try {
            if (class_exists('\Spatie\Permission\Models\Role')) {
                $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);
                if (!$user->hasRole('admin')) {
                    $user->assignRole($adminRole);
                    $this->info('Admin rolü atandı');
                }
            }
        } catch (\Exception $e) {
            $this->info('Role sistemi bulunamadı: ' . $e->getMessage());
        }
        
        // URL prefix ayarları kaydet
        $tenant = \App\Models\Tenant::first();
        if ($tenant) {
            $data = $tenant->data ?? [];
            $data['url_prefix'] = [
                'mode' => 'default_only',
                'default_language' => 'tr'
            ];
            $tenant->update(['data' => $data]);
            $this->info('URL prefix ayarları kaydedildi: default_only, varsayılan: tr');
        } else {
            $this->info('Tenant bulunamadı!');
        }
    }
}

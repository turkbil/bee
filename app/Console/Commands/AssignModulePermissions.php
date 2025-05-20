<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Modules\ModuleManagement\App\Models\Module;
use Illuminate\Support\Facades\DB;
use App\Services\ModuleAccessService;

class AssignModulePermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assign:module-permissions {userId} {--all : Tüm modüllere izin ver} {--module= : Belirli bir modül için izin ver} {--type= : İzin tipi (view,create,update,delete)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Belirli bir kullanıcıya modül izinleri atar';

    /**
     * Execute the console command.
     */
    public function handle(ModuleAccessService $moduleAccessService)
    {
        $userId = $this->argument('userId');
        $allOption = $this->option('all');
        $moduleOption = $this->option('module');
        $typeOption = $this->option('type') ?: 'view';
        
        // Kullanıcıyı bul
        $user = User::find($userId);
        
        if (!$user) {
            $this->error("ID: {$userId} numaralı kullanıcı bulunamadı!");
            return 1;
        }
        
        // Kullanıcı rolünü kontrol et
        if ($user->isRoot() || $user->isAdmin()) {
            $this->warn("Kullanıcı zaten {$user->roles->pluck('name')->implode(', ')} rolüne sahip. Ek izinlere ihtiyacı yok.");
            
            if (!$this->confirm('Yine de devam etmek istiyor musunuz?')) {
                return 0;
            }
        }
        
        // İzin tiplerini kontrol et
        $permissionTypes = explode(',', $typeOption);
        $validTypes = array_keys(config('module-permissions.permission_types', [
            'view' => 'Görüntüleme',
            'create' => 'Oluşturma',
            'update' => 'Güncelleme',
            'delete' => 'Silme'
        ]));
        
        foreach ($permissionTypes as $type) {
            if (!in_array($type, $validTypes)) {
                $this->error("Geçersiz izin tipi: {$type}");
                $this->info("Geçerli tipler: " . implode(', ', $validTypes));
                return 1;
            }
        }
        
        // Modülleri belirle
        if ($allOption) {
            $modules = Module::where('is_active', true)->get();
        } elseif ($moduleOption) {
            $modules = Module::where('name', $moduleOption)
                ->where('is_active', true)
                ->get();
                
            if ($modules->isEmpty()) {
                $this->error("'{$moduleOption}' adında aktif bir modül bulunamadı!");
                return 1;
            }
        } else {
            $this->error("--all veya --module seçeneği belirtmelisiniz!");
            return 1;
        }
        
        // İzinleri ata
        $totalAssigned = 0;
        
        $this->info("Kullanıcı: {$user->name} (ID: {$user->id})");
        $this->info("İzin tipleri: " . implode(', ', $permissionTypes));
        
        foreach ($modules as $module) {
            $this->line("Modül: {$module->name} ({$module->display_name})");
            
            foreach ($permissionTypes as $type) {
                // Modül iznini ver
                $user->giveModulePermissionTo($module->name, $type);
                $totalAssigned++;
                
                $this->line(" - {$type} izni eklendi.");
            }
        }
        
        // Önbelleği temizle
        $moduleAccessService->clearAccessCache($user->id);
        
        $this->info("Toplam {$totalAssigned} izin kullanıcıya atandı.");
        
        return 0;
    }
}
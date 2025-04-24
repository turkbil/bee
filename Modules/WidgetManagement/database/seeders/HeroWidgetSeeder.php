<?php

namespace Modules\WidgetManagement\database\seeders;

use Illuminate\Database\Seeder;
use Modules\WidgetManagement\app\Models\Widget;
use Modules\WidgetManagement\app\Models\WidgetCategory;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

class HeroWidgetSeeder extends Seeder
{
    // Çalıştırma izleme anahtarı
    private static $runKey = 'hero_widget_seeder_executed';

    public function run()
    {
        // Tenant kontrolü
        if (function_exists('tenant') && tenant()) {
            if ($this->command) {
                $this->command->info('Tenant contextinde çalışıyor, HeroWidgetSeeder atlanıyor.');
            }
            Log::info('Tenant contextinde çalışıyor, HeroWidgetSeeder atlanıyor. Tenant ID: ' . tenant('id'));
            return;
        }

        Log::info('HeroWidgetSeeder merkezi veritabanında çalışıyor...');

        try {
            // Önce mevcut hero widget'ını temizle
            $existingWidget = Widget::where('slug', 'full-width-hero')->first();
            if ($existingWidget) {
                $existingWidget->delete();
                Log::info('Mevcut Full Width Hero widget\'\u0131 silindi.');
            }
            
            // Hero klasörünü oluştur
            $this->createHeroFolder();

            // Statik hero widget'ı oluştur
            $this->createHeroWidget();

            Log::info('Hero bileşeni başarıyla oluşturuldu.');

            if ($this->command) {
                $this->command->info('Hero bileşeni başarıyla oluşturuldu.');
            }
        } catch (\Exception $e) {
            Log::error('HeroWidgetSeeder hatası: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            if ($this->command) {
                $this->command->error('HeroWidgetSeeder hatası: ' . $e->getMessage());
            }
        }
    }

    private function createHeroFolder()
    {
        // Hero klasör yolu
        $heroBasePath = base_path('Modules/WidgetManagement/resources/views/blocks/hero');
        $hero1Path = $heroBasePath . '/hero-1';
        
        // Klasör yoksa oluştur
        if (!File::exists($hero1Path)) {
            File::makeDirectory($hero1Path, 0755, true);
            
            // Hero view dosyasını oluştur
            $viewContent = '<div class="py-5 text-center bg-light">
    <div class="container">
        <div class="row py-lg-5">
            <div class="col-lg-8 col-md-10 mx-auto">
                <h1 class="fw-light">Full Width Hero</h1>
                <p class="lead text-muted">Etkileyici bir hero bileşeni ile sayfanızın üst kısmını tasarlayın. İsterseniz arka plan rengini değiştirerek modern bir görünüm kazandırabilirsiniz.</p>
                <p>
                    <a href="#" class="btn btn-primary my-2 me-2">Ana Buton</a>
                    <a href="#" class="btn btn-secondary my-2">İkincil Buton</a>
                </p>
            </div>
        </div>
    </div>
</div>';
            
            File::put($hero1Path . '/view.blade.php', $viewContent);
            Log::info('Hero-1 view.blade.php dosyası oluşturuldu.');
        } else {
            Log::info('Hero-1 klasörü zaten mevcut, atlanıyor...');
        }
    }
    
    private function createHeroWidget()
    {
        // Hero kategorisini bul, yoksa oluştur
        $heroCategory = WidgetCategory::where('slug', 'herolar')->first();
        
        // Eğer hero kategorisi yoksa, önce 'Content' kategorisini kontrol et
        if (!$heroCategory) {
            $contentCategory = WidgetCategory::where('slug', 'content')->first();
            
            if ($contentCategory) {
                Log::info('Content kategorisi bulundu, hero bu kategori altına eklenecek.');
                
                // Hero alt kategorisini oluştur
                $heroCategory = WidgetCategory::create([
                    'title' => 'Herolar',
                    'slug' => 'herolar',
                    'description' => 'Sayfa üst kısmında kullanılabilecek hero bileşenleri',
                    'icon' => 'fa-heading',
                    'order' => 5,
                    'is_active' => true,
                    'parent_id' => $contentCategory->widget_category_id,
                    'has_subcategories' => false
                ]);
                
                Log::info("Hero alt kategorisi oluşturuldu: Herolar (slug: herolar)");
            } else {
                // Content kategorisi yoksa, ana kategori olarak oluştur
                Log::warning('Hero kategorisi bulunamadı, oluşturuluyor...');
                
                try {
                    $heroCategory = WidgetCategory::create([
                        'title' => 'Herolar',
                        'slug' => 'herolar',
                        'description' => 'Sayfa üst kısmında kullanılabilecek hero bileşenleri',
                        'icon' => 'fa-heading',
                        'order' => 5,
                        'is_active' => true,
                        'parent_id' => null,
                        'has_subcategories' => false
                    ]);
                    
                    Log::info("Hero kategorisi oluşturuldu: Herolar (slug: herolar)");
                } catch (\Exception $e) {
                    Log::error("Hero kategorisi oluşturulamadı. Hata: " . $e->getMessage());
                    return;
                }
                
                if (!$heroCategory) {
                    Log::error("Hero kategorisi oluşturulamadı.");
                    return;
                }
            }
        }
        
        // Hero widget'ı zaten var mı kontrolü
        $existingWidget = Widget::where('slug', 'full-width-hero')->first();
        
        if (!$existingWidget) {
            // Hero widget'ı oluştur
            Widget::create([
                'widget_category_id' => $heroCategory->widget_category_id,
                'name' => 'Full Width Hero',
                'slug' => 'full-width-hero',
                'description' => 'Sayfanın üst kısmında kullanılabilecek tam genişlikte hero bileşeni',
                'type' => 'file',
                'file_path' => 'hero/hero-1/view',
                'has_items' => false,
                'is_active' => true,
                'is_core' => true,
                'settings_schema' => [
                    [
                        'name' => 'title',
                        'label' => 'Başlık',
                        'type' => 'text',
                        'required' => true,
                        'system' => true
                    ],
                    [
                        'name' => 'unique_id',
                        'label' => 'Benzersiz ID',
                        'type' => 'text',
                        'required' => false,
                        'system' => true,
                        'hidden' => true
                    ],
                    [
                        'name' => 'bg_color',
                        'label' => 'Arkaplan Rengi',
                        'type' => 'color',
                        'required' => false,
                        'default' => '#f8f9fa'
                    ],
                    [
                        'name' => 'text_color',
                        'label' => 'Metin Rengi',
                        'type' => 'color',
                        'required' => false,
                        'default' => '#212529'
                    ]
                ]
            ]);
            
            Log::info('Full Width Hero bileşeni oluşturuldu.');
        } else {
            Log::info('Full Width Hero bileşeni zaten mevcut, atlanıyor...');
        }
    }
}
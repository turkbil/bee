<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BasicSectorSeeder extends Seeder
{
    /**
     * Temel sektörler için basit seeder
     */
    public function run(): void
    {
        echo "🔧 Temel sektörler yükleniyor...\n";
        
        // Mevcut sektörleri temizle
        DB::table('ai_profile_sectors')->truncate();
        
        // Temel sektörler - Gerçek tablo yapısına uygun
        $sectors = [
            // Teknoloji Ana Kategori
            ['id' => 1, 'code' => 'teknoloji', 'name' => 'Teknoloji', 'category_id' => null, 'description' => 'Teknoloji ve bilişim sektörleri', 'emoji' => '💻', 'icon' => 'fas fa-laptop-code', 'color' => 'primary', 'keywords' => 'teknoloji,bilişim,yazılım,web', 'is_subcategory' => 0, 'is_active' => 1, 'sort_order' => 1],
            
            // Teknoloji Alt Sektörler
            ['id' => 2, 'code' => 'web_design', 'name' => 'Web Tasarım', 'category_id' => 1, 'description' => 'Website tasarım, UI/UX', 'emoji' => '🌐', 'icon' => 'fas fa-globe', 'color' => 'info', 'keywords' => 'web,tasarım,ui,ux,website', 'is_subcategory' => 1, 'is_active' => 1, 'sort_order' => 1],
            ['id' => 3, 'code' => 'software_development', 'name' => 'Yazılım Geliştirme', 'category_id' => 1, 'description' => 'Mobil ve web uygulamaları', 'emoji' => '⚡', 'icon' => 'fas fa-code', 'color' => 'info', 'keywords' => 'yazılım,geliştirme,kod,programming', 'is_subcategory' => 1, 'is_active' => 1, 'sort_order' => 2],
            ['id' => 4, 'code' => 'mobile_development', 'name' => 'Mobil Uygulama', 'category_id' => 1, 'description' => 'iOS ve Android uygulamaları', 'emoji' => '📱', 'icon' => 'fas fa-mobile-alt', 'color' => 'info', 'keywords' => 'mobil,uygulama,ios,android,app', 'is_subcategory' => 1, 'is_active' => 1, 'sort_order' => 3],
            ['id' => 5, 'code' => 'graphic_design', 'name' => 'Grafik Tasarım', 'category_id' => 1, 'description' => 'Logo, kurumsal kimlik', 'emoji' => '🎨', 'icon' => 'fas fa-palette', 'color' => 'info', 'keywords' => 'grafik,tasarım,logo,kimlik', 'is_subcategory' => 1, 'is_active' => 1, 'sort_order' => 4],
            
            // Pazarlama Ana Kategori
            ['id' => 6, 'code' => 'pazarlama', 'name' => 'Pazarlama', 'category_id' => null, 'description' => 'Pazarlama ve reklam sektörleri', 'emoji' => '📈', 'icon' => 'fas fa-chart-line', 'color' => 'success', 'keywords' => 'pazarlama,reklam,marketing', 'is_subcategory' => 0, 'is_active' => 1, 'sort_order' => 2],
            
            // Pazarlama Alt Sektörler
            ['id' => 7, 'code' => 'digital_marketing', 'name' => 'Dijital Pazarlama', 'category_id' => 6, 'description' => 'SEO, SEM, sosyal medya', 'emoji' => '🚀', 'icon' => 'fas fa-bullhorn', 'color' => 'success', 'keywords' => 'dijital,pazarlama,seo,sem,sosyal,medya', 'is_subcategory' => 1, 'is_active' => 1, 'sort_order' => 1],
            ['id' => 8, 'code' => 'social_media', 'name' => 'Sosyal Medya', 'category_id' => 6, 'description' => 'Sosyal medya yönetimi', 'emoji' => '📲', 'icon' => 'fas fa-share-alt', 'color' => 'success', 'keywords' => 'sosyal,medya,instagram,facebook,twitter', 'is_subcategory' => 1, 'is_active' => 1, 'sort_order' => 2],
            
            // Hizmet Ana Kategori
            ['id' => 9, 'code' => 'hizmet', 'name' => 'Hizmet', 'category_id' => null, 'description' => 'Hizmet sektörleri', 'emoji' => '🤝', 'icon' => 'fas fa-handshake', 'color' => 'warning', 'keywords' => 'hizmet,danışmanlık,service', 'is_subcategory' => 0, 'is_active' => 1, 'sort_order' => 3],
            
            // Hizmet Alt Sektörler
            ['id' => 10, 'code' => 'consulting', 'name' => 'Danışmanlık', 'category_id' => 9, 'description' => 'İş danışmanlığı', 'emoji' => '💡', 'icon' => 'fas fa-lightbulb', 'color' => 'warning', 'keywords' => 'danışmanlık,iş,consulting', 'is_subcategory' => 1, 'is_active' => 1, 'sort_order' => 1],
            ['id' => 11, 'code' => 'accounting', 'name' => 'Muhasebe', 'category_id' => 9, 'description' => 'Muhasebe ve finans', 'emoji' => '💰', 'icon' => 'fas fa-calculator', 'color' => 'warning', 'keywords' => 'muhasebe,finans,accounting', 'is_subcategory' => 1, 'is_active' => 1, 'sort_order' => 2],
            
            // Ticaret Ana Kategori
            ['id' => 12, 'code' => 'ticaret', 'name' => 'Ticaret', 'category_id' => null, 'description' => 'Ticaret ve e-ticaret', 'emoji' => '🛒', 'icon' => 'fas fa-store', 'color' => 'danger', 'keywords' => 'ticaret,satış,e-ticaret', 'is_subcategory' => 0, 'is_active' => 1, 'sort_order' => 4],
            
            // Ticaret Alt Sektörler
            ['id' => 13, 'code' => 'e_commerce', 'name' => 'E-Ticaret', 'category_id' => 12, 'description' => 'Online satış', 'emoji' => '🛍️', 'icon' => 'fas fa-shopping-cart', 'color' => 'danger', 'keywords' => 'e-ticaret,online,satış,ecommerce', 'is_subcategory' => 1, 'is_active' => 1, 'sort_order' => 1],
            ['id' => 14, 'code' => 'retail', 'name' => 'Perakende', 'category_id' => 12, 'description' => 'Perakende satış', 'emoji' => '🏪', 'icon' => 'fas fa-store-alt', 'color' => 'danger', 'keywords' => 'perakende,satış,mağaza,retail', 'is_subcategory' => 1, 'is_active' => 1, 'sort_order' => 2],
            
            // Sağlık Ana Kategori
            ['id' => 15, 'code' => 'saglik', 'name' => 'Sağlık', 'category_id' => null, 'description' => 'Sağlık ve tıp sektörleri', 'emoji' => '⚕️', 'icon' => 'fas fa-stethoscope', 'color' => 'info', 'keywords' => 'sağlık,tıp,hastane,sağlık', 'is_subcategory' => 0, 'is_active' => 1, 'sort_order' => 5],
            
            // Sağlık Alt Sektörler
            ['id' => 16, 'code' => 'hospital', 'name' => 'Hastane', 'category_id' => 15, 'description' => 'Hastane hizmetleri', 'emoji' => '🏥', 'icon' => 'fas fa-hospital', 'color' => 'info', 'keywords' => 'hastane,sağlık,tıp,hospital', 'is_subcategory' => 1, 'is_active' => 1, 'sort_order' => 1],
            ['id' => 17, 'code' => 'dental', 'name' => 'Diş Hekimliği', 'category_id' => 15, 'description' => 'Diş tedavi hizmetleri', 'emoji' => '🦷', 'icon' => 'fas fa-tooth', 'color' => 'info', 'keywords' => 'diş,hekimlik,dental,treatment', 'is_subcategory' => 1, 'is_active' => 1, 'sort_order' => 2],
            
            // Eğitim Ana Kategori
            ['id' => 18, 'code' => 'egitim', 'name' => 'Eğitim', 'category_id' => null, 'description' => 'Eğitim ve öğretim', 'emoji' => '🎓', 'icon' => 'fas fa-graduation-cap', 'color' => 'secondary', 'keywords' => 'eğitim,öğretim,school,education', 'is_subcategory' => 0, 'is_active' => 1, 'sort_order' => 6],
            
            // Eğitim Alt Sektörler
            ['id' => 19, 'code' => 'school', 'name' => 'Okul', 'category_id' => 18, 'description' => 'Okul ve eğitim kurumları', 'emoji' => '🏫', 'icon' => 'fas fa-school', 'color' => 'secondary', 'keywords' => 'okul,eğitim,school,kurum', 'is_subcategory' => 1, 'is_active' => 1, 'sort_order' => 1],
            ['id' => 20, 'code' => 'training', 'name' => 'Kurs', 'category_id' => 18, 'description' => 'Kurs ve eğitim', 'emoji' => '📚', 'icon' => 'fas fa-book', 'color' => 'secondary', 'keywords' => 'kurs,eğitim,training,course', 'is_subcategory' => 1, 'is_active' => 1, 'sort_order' => 2],
        ];
        
        // Sektörleri ekle
        foreach ($sectors as $sector) {
            DB::table('ai_profile_sectors')->insert([
                'id' => $sector['id'],
                'code' => $sector['code'],
                'name' => $sector['name'],
                'category_id' => $sector['category_id'],
                'description' => $sector['description'],
                'emoji' => $sector['emoji'],
                'icon' => $sector['icon'],
                'color' => $sector['color'],
                'keywords' => $sector['keywords'],
                'is_subcategory' => $sector['is_subcategory'],
                'is_active' => $sector['is_active'],
                'sort_order' => $sector['sort_order'],
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
        
        echo "✅ " . count($sectors) . " sektör başarıyla yüklendi!\n";
    }
}
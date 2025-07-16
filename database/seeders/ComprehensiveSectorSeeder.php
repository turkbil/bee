<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ComprehensiveSectorSeeder extends Seeder
{
    /**
     * Kapsamlı sektör seeder - 200+ sektör
     */
    public function run(): void
    {
        echo "🔧 Kapsamlı sektör yüklemesi başlıyor...\n";
        
        // Mevcut sektörleri temizle
        DB::table('ai_profile_sectors')->truncate();
        
        // Ana kategorileri ekle
        $this->insertMainCategories();
        
        // Alt sektörleri ekle
        $this->insertSubCategories();
        
        echo "✅ Kapsamlı sektör yüklemesi tamamlandı!\n";
    }
    
    private function insertMainCategories(): void
    {
        $mainCategories = [
            ['id' => 1, 'code' => 'teknoloji', 'name' => 'Teknoloji', 'description' => 'Teknoloji ve bilişim sektörleri', 'emoji' => '💻', 'icon' => 'fas fa-laptop-code', 'color' => 'primary', 'keywords' => 'teknoloji,bilişim,yazılım,web', 'sort_order' => 1],
            ['id' => 2, 'code' => 'pazarlama', 'name' => 'Pazarlama', 'description' => 'Pazarlama ve reklam sektörleri', 'emoji' => '📈', 'icon' => 'fas fa-chart-line', 'color' => 'success', 'keywords' => 'pazarlama,reklam,marketing', 'sort_order' => 2],
            ['id' => 3, 'code' => 'hizmet', 'name' => 'Hizmet', 'description' => 'Hizmet sektörleri', 'emoji' => '🤝', 'icon' => 'fas fa-handshake', 'color' => 'warning', 'keywords' => 'hizmet,danışmanlık,service', 'sort_order' => 3],
            ['id' => 4, 'code' => 'ticaret', 'name' => 'Ticaret', 'description' => 'Ticaret ve e-ticaret', 'emoji' => '🛒', 'icon' => 'fas fa-store', 'color' => 'danger', 'keywords' => 'ticaret,satış,e-ticaret', 'sort_order' => 4],
            ['id' => 5, 'code' => 'saglik', 'name' => 'Sağlık', 'description' => 'Sağlık ve tıp sektörleri', 'emoji' => '⚕️', 'icon' => 'fas fa-stethoscope', 'color' => 'info', 'keywords' => 'sağlık,tıp,hastane,sağlık', 'sort_order' => 5],
            ['id' => 6, 'code' => 'egitim', 'name' => 'Eğitim', 'description' => 'Eğitim ve öğretim', 'emoji' => '🎓', 'icon' => 'fas fa-graduation-cap', 'color' => 'secondary', 'keywords' => 'eğitim,öğretim,school,education', 'sort_order' => 6],
            ['id' => 7, 'code' => 'yemek_icecek', 'name' => 'Yemek & İçecek', 'description' => 'Yemek ve içecek sektörleri', 'emoji' => '🍽️', 'icon' => 'fas fa-utensils', 'color' => 'orange', 'keywords' => 'yemek,içecek,restoran,kafe', 'sort_order' => 7],
            ['id' => 8, 'code' => 'perakende', 'name' => 'Perakende', 'description' => 'Perakende satış', 'emoji' => '🛍️', 'icon' => 'fas fa-shopping-bag', 'color' => 'purple', 'keywords' => 'perakende,mağaza,satış', 'sort_order' => 8],
            ['id' => 9, 'code' => 'spor_wellness', 'name' => 'Spor & Wellness', 'description' => 'Spor ve sağlık sektörleri', 'emoji' => '🏃', 'icon' => 'fas fa-dumbbell', 'color' => 'green', 'keywords' => 'spor,fitness,wellness', 'sort_order' => 9],
            ['id' => 10, 'code' => 'emlak_insaat', 'name' => 'Emlak & İnşaat', 'description' => 'Emlak ve inşaat sektörleri', 'emoji' => '🏠', 'icon' => 'fas fa-home', 'color' => 'blue', 'keywords' => 'emlak,inşaat,ev,bina', 'sort_order' => 10],
            ['id' => 11, 'code' => 'finans_sigorta', 'name' => 'Finans & Sigorta', 'description' => 'Finans ve sigorta sektörleri', 'emoji' => '💰', 'icon' => 'fas fa-chart-line', 'color' => 'yellow', 'keywords' => 'finans,sigorta,banka,kredi', 'sort_order' => 11],
            ['id' => 12, 'code' => 'hukuk', 'name' => 'Hukuk', 'description' => 'Hukuk ve danışmanlık', 'emoji' => '⚖️', 'icon' => 'fas fa-balance-scale', 'color' => 'indigo', 'keywords' => 'hukuk,avukat,legal', 'sort_order' => 12],
            ['id' => 13, 'code' => 'medya', 'name' => 'Medya', 'description' => 'Medya ve iletişim', 'emoji' => '📺', 'icon' => 'fas fa-broadcast-tower', 'color' => 'pink', 'keywords' => 'medya,iletişim,tv,radyo', 'sort_order' => 13],
            ['id' => 14, 'code' => 'otomotiv', 'name' => 'Otomotiv', 'description' => 'Otomotiv ve ulaşım', 'emoji' => '🚗', 'icon' => 'fas fa-car', 'color' => 'dark', 'keywords' => 'otomotiv,ulaşım,araba', 'sort_order' => 14],
            ['id' => 15, 'code' => 'turizm', 'name' => 'Turizm', 'description' => 'Turizm ve seyahat', 'emoji' => '✈️', 'icon' => 'fas fa-plane', 'color' => 'teal', 'keywords' => 'turizm,seyahat,otel,tatil', 'sort_order' => 15],
            ['id' => 16, 'code' => 'tarim', 'name' => 'Tarım', 'description' => 'Tarım ve hayvancılık', 'emoji' => '🌾', 'icon' => 'fas fa-seedling', 'color' => 'green', 'keywords' => 'tarım,hayvancılık,çiftlik', 'sort_order' => 16],
            ['id' => 17, 'code' => 'giyim', 'name' => 'Giyim', 'description' => 'Giyim ve moda', 'emoji' => '👕', 'icon' => 'fas fa-tshirt', 'color' => 'purple', 'keywords' => 'giyim,moda,tekstil', 'sort_order' => 17],
            ['id' => 18, 'code' => 'guzellik', 'name' => 'Güzellik', 'description' => 'Güzellik ve bakım', 'emoji' => '💄', 'icon' => 'fas fa-cut', 'color' => 'rose', 'keywords' => 'güzellik,kuaför,bakım', 'sort_order' => 18],
            ['id' => 19, 'code' => 'elektronik', 'name' => 'Elektronik', 'description' => 'Elektronik ve teknoloji', 'emoji' => '📱', 'icon' => 'fas fa-laptop', 'color' => 'blue', 'keywords' => 'elektronik,teknoloji,telefon', 'sort_order' => 19],
            ['id' => 20, 'code' => 'ev_yasam', 'name' => 'Ev & Yaşam', 'description' => 'Ev ve yaşam ürünleri', 'emoji' => '🏠', 'icon' => 'fas fa-couch', 'color' => 'amber', 'keywords' => 'ev,yaşam,dekorasyon,mobilya', 'sort_order' => 20],
        ];
        
        foreach ($mainCategories as $category) {
            DB::table('ai_profile_sectors')->insert([
                'id' => $category['id'],
                'code' => $category['code'],
                'name' => $category['name'],
                'category_id' => null,
                'description' => $category['description'],
                'emoji' => $category['emoji'],
                'icon' => $category['icon'],
                'color' => $category['color'],
                'keywords' => $category['keywords'],
                'is_subcategory' => 0,
                'is_active' => 1,
                'sort_order' => $category['sort_order'],
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
        
        echo "✅ 20 ana kategori eklendi!\n";
    }
    
    private function insertSubCategories(): void
    {
        $subCategories = [
            // TEKNOLOJİ Alt Sektörler
            ['id' => 21, 'code' => 'web_design', 'name' => 'Web Tasarım', 'category_id' => 1, 'description' => 'Website tasarım, UI/UX', 'emoji' => '🌐', 'icon' => 'fas fa-globe', 'color' => 'primary', 'keywords' => 'web,tasarım,ui,ux,website', 'sort_order' => 1],
            ['id' => 22, 'code' => 'software_development', 'name' => 'Yazılım Geliştirme', 'category_id' => 1, 'description' => 'Mobil ve web uygulamaları', 'emoji' => '⚡', 'icon' => 'fas fa-code', 'color' => 'primary', 'keywords' => 'yazılım,geliştirme,kod,programming', 'sort_order' => 2],
            ['id' => 23, 'code' => 'mobile_development', 'name' => 'Mobil Uygulama', 'category_id' => 1, 'description' => 'iOS ve Android uygulamaları', 'emoji' => '📱', 'icon' => 'fas fa-mobile-alt', 'color' => 'primary', 'keywords' => 'mobil,uygulama,ios,android,app', 'sort_order' => 3],
            ['id' => 24, 'code' => 'graphic_design', 'name' => 'Grafik Tasarım', 'category_id' => 1, 'description' => 'Logo, kurumsal kimlik', 'emoji' => '🎨', 'icon' => 'fas fa-palette', 'color' => 'primary', 'keywords' => 'grafik,tasarım,logo,kimlik', 'sort_order' => 4],
            ['id' => 25, 'code' => 'data_analytics', 'name' => 'Veri Analizi', 'category_id' => 1, 'description' => 'Veri analizi ve raporlama', 'emoji' => '📊', 'icon' => 'fas fa-chart-bar', 'color' => 'primary', 'keywords' => 'veri,analiz,data,analytics', 'sort_order' => 5],
            ['id' => 26, 'code' => 'cybersecurity', 'name' => 'Siber Güvenlik', 'category_id' => 1, 'description' => 'Siber güvenlik hizmetleri', 'emoji' => '🔒', 'icon' => 'fas fa-shield-alt', 'color' => 'primary', 'keywords' => 'güvenlik,siber,security,koruma', 'sort_order' => 6],
            
            // PAZARLAMA Alt Sektörler
            ['id' => 27, 'code' => 'digital_marketing', 'name' => 'Dijital Pazarlama', 'category_id' => 2, 'description' => 'SEO, SEM, sosyal medya', 'emoji' => '🚀', 'icon' => 'fas fa-bullhorn', 'color' => 'success', 'keywords' => 'dijital,pazarlama,seo,sem,sosyal', 'sort_order' => 1],
            ['id' => 28, 'code' => 'social_media', 'name' => 'Sosyal Medya', 'category_id' => 2, 'description' => 'Sosyal medya yönetimi', 'emoji' => '📲', 'icon' => 'fas fa-share-alt', 'color' => 'success', 'keywords' => 'sosyal,medya,instagram,facebook,twitter', 'sort_order' => 2],
            ['id' => 29, 'code' => 'advertising', 'name' => 'Reklam Ajansı', 'category_id' => 2, 'description' => 'Reklam ve tanıtım', 'emoji' => '📢', 'icon' => 'fas fa-megaphone', 'color' => 'success', 'keywords' => 'reklam,ajans,tanıtım,advertising', 'sort_order' => 3],
            ['id' => 30, 'code' => 'content_marketing', 'name' => 'İçerik Pazarlama', 'category_id' => 2, 'description' => 'İçerik üretimi ve pazarlama', 'emoji' => '📝', 'icon' => 'fas fa-edit', 'color' => 'success', 'keywords' => 'içerik,pazarlama,content,marketing', 'sort_order' => 4],
            
            // HİZMET Alt Sektörler
            ['id' => 31, 'code' => 'consulting', 'name' => 'Danışmanlık', 'category_id' => 3, 'description' => 'İş danışmanlığı', 'emoji' => '💡', 'icon' => 'fas fa-lightbulb', 'color' => 'warning', 'keywords' => 'danışmanlık,iş,consulting', 'sort_order' => 1],
            ['id' => 32, 'code' => 'accounting', 'name' => 'Muhasebe', 'category_id' => 3, 'description' => 'Muhasebe ve finans', 'emoji' => '🧮', 'icon' => 'fas fa-calculator', 'color' => 'warning', 'keywords' => 'muhasebe,finans,accounting', 'sort_order' => 2],
            ['id' => 33, 'code' => 'cleaning_service', 'name' => 'Temizlik Hizmeti', 'category_id' => 3, 'description' => 'Temizlik hizmetleri', 'emoji' => '🧹', 'icon' => 'fas fa-broom', 'color' => 'warning', 'keywords' => 'temizlik,hijyen,cleaning', 'sort_order' => 3],
            ['id' => 34, 'code' => 'security_service', 'name' => 'Güvenlik Hizmeti', 'category_id' => 3, 'description' => 'Güvenlik hizmetleri', 'emoji' => '🛡️', 'icon' => 'fas fa-shield-alt', 'color' => 'warning', 'keywords' => 'güvenlik,koruma,security', 'sort_order' => 4],
            ['id' => 35, 'code' => 'translation', 'name' => 'Çeviri Hizmetleri', 'category_id' => 3, 'description' => 'Çeviri ve tercümanlık', 'emoji' => '🌍', 'icon' => 'fas fa-globe', 'color' => 'warning', 'keywords' => 'çeviri,tercüman,translation', 'sort_order' => 5],
            ['id' => 36, 'code' => 'logistics', 'name' => 'Lojistik', 'category_id' => 3, 'description' => 'Lojistik ve kargo', 'emoji' => '🚚', 'icon' => 'fas fa-truck', 'color' => 'warning', 'keywords' => 'lojistik,kargo,nakliye', 'sort_order' => 6],
            
            // TİCARET Alt Sektörler
            ['id' => 37, 'code' => 'e_commerce', 'name' => 'E-Ticaret', 'category_id' => 4, 'description' => 'Online satış', 'emoji' => '🛍️', 'icon' => 'fas fa-shopping-cart', 'color' => 'danger', 'keywords' => 'e-ticaret,online,satış,ecommerce', 'sort_order' => 1],
            ['id' => 38, 'code' => 'retail', 'name' => 'Perakende', 'category_id' => 4, 'description' => 'Perakende satış', 'emoji' => '🏪', 'icon' => 'fas fa-store-alt', 'color' => 'danger', 'keywords' => 'perakende,satış,mağaza,retail', 'sort_order' => 2],
            ['id' => 39, 'code' => 'wholesale', 'name' => 'Toptan Satış', 'category_id' => 4, 'description' => 'Toptan ticaret', 'emoji' => '📦', 'icon' => 'fas fa-boxes', 'color' => 'danger', 'keywords' => 'toptan,satış,wholesale', 'sort_order' => 3],
            ['id' => 40, 'code' => 'import_export', 'name' => 'İthalat İhracat', 'category_id' => 4, 'description' => 'İthalat ve ihracat', 'emoji' => '🌍', 'icon' => 'fas fa-globe', 'color' => 'danger', 'keywords' => 'ithalat,ihracat,import,export', 'sort_order' => 4],
            
            // SAĞLIK Alt Sektörler
            ['id' => 41, 'code' => 'hospital', 'name' => 'Hastane', 'category_id' => 5, 'description' => 'Hastane hizmetleri', 'emoji' => '🏥', 'icon' => 'fas fa-hospital', 'color' => 'info', 'keywords' => 'hastane,sağlık,tıp,hospital', 'sort_order' => 1],
            ['id' => 42, 'code' => 'dental', 'name' => 'Diş Hekimliği', 'category_id' => 5, 'description' => 'Diş tedavi hizmetleri', 'emoji' => '🦷', 'icon' => 'fas fa-tooth', 'color' => 'info', 'keywords' => 'diş,hekimlik,dental,treatment', 'sort_order' => 2],
            ['id' => 43, 'code' => 'pharmacy', 'name' => 'Eczane', 'category_id' => 5, 'description' => 'Eczane hizmetleri', 'emoji' => '💊', 'icon' => 'fas fa-pills', 'color' => 'info', 'keywords' => 'eczane,ilaç,sağlık,pharmacy', 'sort_order' => 3],
            ['id' => 44, 'code' => 'veterinary', 'name' => 'Veteriner', 'category_id' => 5, 'description' => 'Veteriner hizmetleri', 'emoji' => '🐕', 'icon' => 'fas fa-paw', 'color' => 'info', 'keywords' => 'veteriner,hayvan,sağlık,pet', 'sort_order' => 4],
            ['id' => 45, 'code' => 'physiotherapy', 'name' => 'Fizyoterapist', 'category_id' => 5, 'description' => 'Fizyoterapi hizmetleri', 'emoji' => '🤲', 'icon' => 'fas fa-hands-helping', 'color' => 'info', 'keywords' => 'fizyoterapi,tedavi,rehabilitasyon', 'sort_order' => 5],
            ['id' => 46, 'code' => 'psychology', 'name' => 'Psikolog', 'category_id' => 5, 'description' => 'Psikoloji hizmetleri', 'emoji' => '🧠', 'icon' => 'fas fa-brain', 'color' => 'info', 'keywords' => 'psikolog,terapi,danışmanlık', 'sort_order' => 6],
            
            // EĞİTİM Alt Sektörler
            ['id' => 47, 'code' => 'school', 'name' => 'Okul', 'category_id' => 6, 'description' => 'Okul ve eğitim kurumları', 'emoji' => '🏫', 'icon' => 'fas fa-school', 'color' => 'secondary', 'keywords' => 'okul,eğitim,school,kurum', 'sort_order' => 1],
            ['id' => 48, 'code' => 'training', 'name' => 'Kurs', 'category_id' => 6, 'description' => 'Kurs ve eğitim', 'emoji' => '📚', 'icon' => 'fas fa-book', 'color' => 'secondary', 'keywords' => 'kurs,eğitim,training,course', 'sort_order' => 2],
            ['id' => 49, 'code' => 'music_school', 'name' => 'Müzik Okulu', 'category_id' => 6, 'description' => 'Müzik eğitimi', 'emoji' => '🎵', 'icon' => 'fas fa-music', 'color' => 'secondary', 'keywords' => 'müzik,enstrüman,eğitim,music', 'sort_order' => 3],
            ['id' => 50, 'code' => 'language_center', 'name' => 'Dil Merkezi', 'category_id' => 6, 'description' => 'Dil eğitimi', 'emoji' => '🗣️', 'icon' => 'fas fa-language', 'color' => 'secondary', 'keywords' => 'dil,eğitim,İngilizce,language', 'sort_order' => 4],
            
            // YEMEK & İÇECEK Alt Sektörler
            ['id' => 51, 'code' => 'restaurant', 'name' => 'Restoran', 'category_id' => 7, 'description' => 'Restoran işletmesi', 'emoji' => '🍽️', 'icon' => 'fas fa-utensils', 'color' => 'orange', 'keywords' => 'restoran,yemek,mutfak,chef', 'sort_order' => 1],
            ['id' => 52, 'code' => 'cafe', 'name' => 'Kafe', 'category_id' => 7, 'description' => 'Kafe işletmesi', 'emoji' => '☕', 'icon' => 'fas fa-coffee', 'color' => 'orange', 'keywords' => 'kafe,kahve,çay,içecek', 'sort_order' => 2],
            ['id' => 53, 'code' => 'fast_food', 'name' => 'Fast Food', 'category_id' => 7, 'description' => 'Hızlı yemek servisi', 'emoji' => '🍔', 'icon' => 'fas fa-hamburger', 'color' => 'orange', 'keywords' => 'fast,food,burger,pizza', 'sort_order' => 3],
            ['id' => 54, 'code' => 'bakery', 'name' => 'Fırın', 'category_id' => 7, 'description' => 'Fırın ve pastane', 'emoji' => '🍞', 'icon' => 'fas fa-bread-slice', 'color' => 'orange', 'keywords' => 'fırın,ekmek,pastane,bakery', 'sort_order' => 4],
            
            // PERAKENDE Alt Sektörler  
            ['id' => 55, 'code' => 'clothing', 'name' => 'Giyim', 'category_id' => 8, 'description' => 'Giyim mağazası', 'emoji' => '👕', 'icon' => 'fas fa-tshirt', 'color' => 'purple', 'keywords' => 'giyim,kıyafet,moda,tekstil', 'sort_order' => 1],
            ['id' => 56, 'code' => 'electronics', 'name' => 'Elektronik', 'category_id' => 8, 'description' => 'Elektronik mağazası', 'emoji' => '📱', 'icon' => 'fas fa-laptop', 'color' => 'purple', 'keywords' => 'elektronik,teknoloji,telefon,bilgisayar', 'sort_order' => 2],
            ['id' => 57, 'code' => 'home_decor', 'name' => 'Ev Dekorasyon', 'category_id' => 8, 'description' => 'Ev dekorasyonu', 'emoji' => '🏠', 'icon' => 'fas fa-couch', 'color' => 'purple', 'keywords' => 'ev,dekorasyon,mobilya,tasarım', 'sort_order' => 3],
            ['id' => 58, 'code' => 'bookstore', 'name' => 'Kitabevi', 'category_id' => 8, 'description' => 'Kitap satış', 'emoji' => '📚', 'icon' => 'fas fa-book', 'color' => 'purple', 'keywords' => 'kitap,yayın,okuma,bookstore', 'sort_order' => 4],
            
            // SPOR & WELLNESS Alt Sektörler
            ['id' => 59, 'code' => 'gym', 'name' => 'Spor Salonu', 'category_id' => 9, 'description' => 'Fitness merkezi', 'emoji' => '🏋️', 'icon' => 'fas fa-dumbbell', 'color' => 'green', 'keywords' => 'spor,fitness,gym,antrenman', 'sort_order' => 1],
            ['id' => 60, 'code' => 'yoga_studio', 'name' => 'Yoga Stüdyosu', 'category_id' => 9, 'description' => 'Yoga ve meditasyon', 'emoji' => '🧘', 'icon' => 'fas fa-om', 'color' => 'green', 'keywords' => 'yoga,meditasyon,studio,wellness', 'sort_order' => 2],
            ['id' => 61, 'code' => 'spa', 'name' => 'Spa', 'category_id' => 9, 'description' => 'Spa ve wellness', 'emoji' => '🧖', 'icon' => 'fas fa-spa', 'color' => 'green', 'keywords' => 'spa,wellness,masaj,rahatlama', 'sort_order' => 3],
            ['id' => 62, 'code' => 'sports_club', 'name' => 'Spor Kulübü', 'category_id' => 9, 'description' => 'Spor kulübü', 'emoji' => '⚽', 'icon' => 'fas fa-futbol', 'color' => 'green', 'keywords' => 'spor,kulüp,takım,antrenman', 'sort_order' => 4],
            
            // EMLAK & İNŞAAT Alt Sektörler
            ['id' => 63, 'code' => 'construction', 'name' => 'İnşaat', 'category_id' => 10, 'description' => 'İnşaat hizmetleri', 'emoji' => '🏗️', 'icon' => 'fas fa-hard-hat', 'color' => 'blue', 'keywords' => 'inşaat,yapı,bina,construction', 'sort_order' => 1],
            ['id' => 64, 'code' => 'real_estate', 'name' => 'Emlak', 'category_id' => 10, 'description' => 'Emlak hizmetleri', 'emoji' => '🏠', 'icon' => 'fas fa-home', 'color' => 'blue', 'keywords' => 'emlak,ev,daire,satış', 'sort_order' => 2],
            ['id' => 65, 'code' => 'architecture', 'name' => 'Mimarlık', 'category_id' => 10, 'description' => 'Mimarlık hizmetleri', 'emoji' => '📐', 'icon' => 'fas fa-drafting-compass', 'color' => 'blue', 'keywords' => 'mimarlık,tasarım,proje,architecture', 'sort_order' => 3],
            ['id' => 66, 'code' => 'interior_design', 'name' => 'İç Mimarlık', 'category_id' => 10, 'description' => 'İç mimarlık hizmetleri', 'emoji' => '🏠', 'icon' => 'fas fa-home', 'color' => 'blue', 'keywords' => 'iç,mimarlık,tasarım,dekorasyon', 'sort_order' => 4],
            
            // FINANS & SİGORTA Alt Sektörler
            ['id' => 67, 'code' => 'bank', 'name' => 'Banka', 'category_id' => 11, 'description' => 'Bankacılık hizmetleri', 'emoji' => '🏦', 'icon' => 'fas fa-university', 'color' => 'yellow', 'keywords' => 'banka,finans,kredi,para', 'sort_order' => 1],
            ['id' => 68, 'code' => 'insurance', 'name' => 'Sigorta', 'category_id' => 11, 'description' => 'Sigorta hizmetleri', 'emoji' => '🛡️', 'icon' => 'fas fa-shield-alt', 'color' => 'yellow', 'keywords' => 'sigorta,güvence,koruma,insurance', 'sort_order' => 2],
            ['id' => 69, 'code' => 'investment', 'name' => 'Yatırım Danışmanı', 'category_id' => 11, 'description' => 'Yatırım danışmanlığı', 'emoji' => '📈', 'icon' => 'fas fa-chart-line', 'color' => 'yellow', 'keywords' => 'yatırım,danışman,borsa,investment', 'sort_order' => 3],
            ['id' => 70, 'code' => 'tax_consultant', 'name' => 'Vergi Danışmanı', 'category_id' => 11, 'description' => 'Vergi danışmanlığı', 'emoji' => '📋', 'icon' => 'fas fa-file-invoice-dollar', 'color' => 'yellow', 'keywords' => 'vergi,danışman,beyanname,tax', 'sort_order' => 4],
            
            // HUKUK Alt Sektörler
            ['id' => 71, 'code' => 'lawyer', 'name' => 'Avukat', 'category_id' => 12, 'description' => 'Hukuk hizmetleri', 'emoji' => '⚖️', 'icon' => 'fas fa-balance-scale', 'color' => 'indigo', 'keywords' => 'avukat,hukuk,dava,legal', 'sort_order' => 1],
            ['id' => 72, 'code' => 'notary', 'name' => 'Noterlik', 'category_id' => 12, 'description' => 'Noterlik hizmetleri', 'emoji' => '📝', 'icon' => 'fas fa-file-signature', 'color' => 'indigo', 'keywords' => 'noter,belge,onay,notary', 'sort_order' => 2],
            ['id' => 73, 'code' => 'patent', 'name' => 'Patent Vekili', 'category_id' => 12, 'description' => 'Patent hizmetleri', 'emoji' => '📄', 'icon' => 'fas fa-file-contract', 'color' => 'indigo', 'keywords' => 'patent,fikri,mülkiyet,vekil', 'sort_order' => 3],
            ['id' => 74, 'code' => 'legal_consultant', 'name' => 'Hukuk Danışmanı', 'category_id' => 12, 'description' => 'Hukuk danışmanlığı', 'emoji' => '🎯', 'icon' => 'fas fa-gavel', 'color' => 'indigo', 'keywords' => 'hukuk,danışman,legal,consultant', 'sort_order' => 4],
            
            // MEDYA Alt Sektörler
            ['id' => 75, 'code' => 'photography', 'name' => 'Fotoğrafçılık', 'category_id' => 13, 'description' => 'Fotoğraf hizmetleri', 'emoji' => '📷', 'icon' => 'fas fa-camera', 'color' => 'pink', 'keywords' => 'fotoğraf,çekim,düğün,etkinlik', 'sort_order' => 1],
            ['id' => 76, 'code' => 'printing', 'name' => 'Matbaa', 'category_id' => 13, 'description' => 'Matbaa hizmetleri', 'emoji' => '🖨️', 'icon' => 'fas fa-print', 'color' => 'pink', 'keywords' => 'matbaa,baskı,printing,tasarım', 'sort_order' => 2],
            ['id' => 77, 'code' => 'journalism', 'name' => 'Gazeteci', 'category_id' => 13, 'description' => 'Gazetecilik hizmetleri', 'emoji' => '📰', 'icon' => 'fas fa-newspaper', 'color' => 'pink', 'keywords' => 'gazetecilik,haber,medya,journalism', 'sort_order' => 3],
            ['id' => 78, 'code' => 'tv_production', 'name' => 'TV Prodüksiyon', 'category_id' => 13, 'description' => 'TV yapımcılığı', 'emoji' => '📺', 'icon' => 'fas fa-video', 'color' => 'pink', 'keywords' => 'tv,prodüksiyon,yapım,production', 'sort_order' => 4],
            
            // OTOMOTİV Alt Sektörler
            ['id' => 79, 'code' => 'auto_repair', 'name' => 'Oto Tamir', 'category_id' => 14, 'description' => 'Otomobil tamiri', 'emoji' => '🔧', 'icon' => 'fas fa-wrench', 'color' => 'dark', 'keywords' => 'oto,tamir,araba,servis', 'sort_order' => 1],
            ['id' => 80, 'code' => 'car_rental', 'name' => 'Araç Kiralama', 'category_id' => 14, 'description' => 'Araç kiralama hizmetleri', 'emoji' => '🚗', 'icon' => 'fas fa-car', 'color' => 'dark', 'keywords' => 'araç,kiralama,rent,car', 'sort_order' => 2],
            ['id' => 81, 'code' => 'taxi', 'name' => 'Taksi', 'category_id' => 14, 'description' => 'Taksi hizmetleri', 'emoji' => '🚖', 'icon' => 'fas fa-taxi', 'color' => 'dark', 'keywords' => 'taksi,ulaşım,şoför,taxi', 'sort_order' => 3],
            ['id' => 82, 'code' => 'tire_service', 'name' => 'Lastik Servisi', 'category_id' => 14, 'description' => 'Lastik satış ve servis', 'emoji' => '🛞', 'icon' => 'fas fa-tire', 'color' => 'dark', 'keywords' => 'lastik,servis,tire,service', 'sort_order' => 4],
            
            // TURİZM Alt Sektörler
            ['id' => 83, 'code' => 'hotel', 'name' => 'Otel', 'category_id' => 15, 'description' => 'Otel işletmesi', 'emoji' => '🏨', 'icon' => 'fas fa-bed', 'color' => 'teal', 'keywords' => 'otel,konaklama,tatil,hotel', 'sort_order' => 1],
            ['id' => 84, 'code' => 'travel_agency', 'name' => 'Seyahat Acentesi', 'category_id' => 15, 'description' => 'Seyahat planlaması', 'emoji' => '🧳', 'icon' => 'fas fa-suitcase', 'color' => 'teal', 'keywords' => 'seyahat,acente,tatil,tur', 'sort_order' => 2],
            ['id' => 85, 'code' => 'tour_guide', 'name' => 'Tur Rehberi', 'category_id' => 15, 'description' => 'Tur rehberliği', 'emoji' => '🗺️', 'icon' => 'fas fa-map', 'color' => 'teal', 'keywords' => 'tur,rehber,gezi,guide', 'sort_order' => 3],
            ['id' => 86, 'code' => 'camping', 'name' => 'Kamp Alanı', 'category_id' => 15, 'description' => 'Kamp hizmetleri', 'emoji' => '🏕️', 'icon' => 'fas fa-campground', 'color' => 'teal', 'keywords' => 'kamp,camping,doğa,outdoor', 'sort_order' => 4],
            
            // TARIM Alt Sektörler
            ['id' => 87, 'code' => 'farm', 'name' => 'Çiftlik', 'category_id' => 16, 'description' => 'Çiftçilik ve tarım', 'emoji' => '🚜', 'icon' => 'fas fa-tractor', 'color' => 'green', 'keywords' => 'çiftlik,tarım,üretim,çiftçi', 'sort_order' => 1],
            ['id' => 88, 'code' => 'livestock', 'name' => 'Hayvancılık', 'category_id' => 16, 'description' => 'Hayvancılık işletmesi', 'emoji' => '🐄', 'icon' => 'fas fa-cow', 'color' => 'green', 'keywords' => 'hayvancılık,çiftlik,süt,et', 'sort_order' => 2],
            ['id' => 89, 'code' => 'greenhouse', 'name' => 'Sera', 'category_id' => 16, 'description' => 'Sera üretimi', 'emoji' => '🌱', 'icon' => 'fas fa-seedling', 'color' => 'green', 'keywords' => 'sera,üretim,bitkisel,greenhouse', 'sort_order' => 3],
            ['id' => 90, 'code' => 'organic_farm', 'name' => 'Organik Çiftlik', 'category_id' => 16, 'description' => 'Organik tarım', 'emoji' => '🌾', 'icon' => 'fas fa-leaf', 'color' => 'green', 'keywords' => 'organik,çiftlik,doğal,organic', 'sort_order' => 4],
            
            // GİYİM Alt Sektörler
            ['id' => 91, 'code' => 'fashion_design', 'name' => 'Moda Tasarımı', 'category_id' => 17, 'description' => 'Moda tasarımı', 'emoji' => '👗', 'icon' => 'fas fa-cut', 'color' => 'purple', 'keywords' => 'moda,tasarım,fashion,design', 'sort_order' => 1],
            ['id' => 92, 'code' => 'textile', 'name' => 'Tekstil', 'category_id' => 17, 'description' => 'Tekstil üretimi', 'emoji' => '🧵', 'icon' => 'fas fa-tape', 'color' => 'purple', 'keywords' => 'tekstil,kumaş,textile,fabric', 'sort_order' => 2],
            ['id' => 93, 'code' => 'leather', 'name' => 'Deri', 'category_id' => 17, 'description' => 'Deri ürünleri', 'emoji' => '👜', 'icon' => 'fas fa-suitcase', 'color' => 'purple', 'keywords' => 'deri,ürün,leather,bag', 'sort_order' => 3],
            ['id' => 94, 'code' => 'shoes', 'name' => 'Ayakkabı', 'category_id' => 17, 'description' => 'Ayakkabı satışı', 'emoji' => '👟', 'icon' => 'fas fa-shoe-prints', 'color' => 'purple', 'keywords' => 'ayakkabı,shoes,footwear', 'sort_order' => 4],
            
            // GÜZELLİK Alt Sektörler
            ['id' => 95, 'code' => 'beauty_salon', 'name' => 'Güzellik Salonu', 'category_id' => 18, 'description' => 'Güzellik hizmetleri', 'emoji' => '💄', 'icon' => 'fas fa-cut', 'color' => 'rose', 'keywords' => 'güzellik,kuaför,makyaj,bakım', 'sort_order' => 1],
            ['id' => 96, 'code' => 'barbershop', 'name' => 'Berber', 'category_id' => 18, 'description' => 'Berber hizmetleri', 'emoji' => '✂️', 'icon' => 'fas fa-cut', 'color' => 'rose', 'keywords' => 'berber,traş,saç,erkek', 'sort_order' => 2],
            ['id' => 97, 'code' => 'nail_salon', 'name' => 'Nail Art', 'category_id' => 18, 'description' => 'Nail art hizmetleri', 'emoji' => '💅', 'icon' => 'fas fa-hand-sparkles', 'color' => 'rose', 'keywords' => 'nail,art,tırnak,manikür', 'sort_order' => 3],
            ['id' => 98, 'code' => 'cosmetics', 'name' => 'Kozmetik', 'category_id' => 18, 'description' => 'Kozmetik satışı', 'emoji' => '💄', 'icon' => 'fas fa-paint-brush', 'color' => 'rose', 'keywords' => 'kozmetik,makyaj,cosmetics', 'sort_order' => 4],
            
            // ELEKTRONİK Alt Sektörler
            ['id' => 99, 'code' => 'mobile_phone', 'name' => 'Cep Telefonu', 'category_id' => 19, 'description' => 'Cep telefonu satışı', 'emoji' => '📱', 'icon' => 'fas fa-mobile-alt', 'color' => 'blue', 'keywords' => 'telefon,mobile,phone,cellular', 'sort_order' => 1],
            ['id' => 100, 'code' => 'computer', 'name' => 'Bilgisayar', 'category_id' => 19, 'description' => 'Bilgisayar satışı', 'emoji' => '💻', 'icon' => 'fas fa-laptop', 'color' => 'blue', 'keywords' => 'bilgisayar,computer,pc,laptop', 'sort_order' => 2],
            ['id' => 101, 'code' => 'tv_audio', 'name' => 'TV & Ses', 'category_id' => 19, 'description' => 'TV ve ses sistemleri', 'emoji' => '📺', 'icon' => 'fas fa-tv', 'color' => 'blue', 'keywords' => 'tv,ses,audio,television', 'sort_order' => 3],
            ['id' => 102, 'code' => 'camera', 'name' => 'Kamera', 'category_id' => 19, 'description' => 'Kamera satışı', 'emoji' => '📷', 'icon' => 'fas fa-camera', 'color' => 'blue', 'keywords' => 'kamera,fotoğraf,camera,photo', 'sort_order' => 4],
            
            // EV & YAŞAM Alt Sektörler  
            ['id' => 103, 'code' => 'furniture', 'name' => 'Mobilya', 'category_id' => 20, 'description' => 'Mobilya satışı', 'emoji' => '🪑', 'icon' => 'fas fa-chair', 'color' => 'amber', 'keywords' => 'mobilya,furniture,masa,sandalye', 'sort_order' => 1],
            ['id' => 104, 'code' => 'kitchen', 'name' => 'Mutfak', 'category_id' => 20, 'description' => 'Mutfak eşyaları', 'emoji' => '🍳', 'icon' => 'fas fa-utensils', 'color' => 'amber', 'keywords' => 'mutfak,kitchen,eşya,ware', 'sort_order' => 2],
            ['id' => 105, 'code' => 'bathroom', 'name' => 'Banyo', 'category_id' => 20, 'description' => 'Banyo ürünleri', 'emoji' => '🛁', 'icon' => 'fas fa-bath', 'color' => 'amber', 'keywords' => 'banyo,bathroom,duş,shower', 'sort_order' => 3],
            ['id' => 106, 'code' => 'lighting', 'name' => 'Aydınlatma', 'category_id' => 20, 'description' => 'Aydınlatma sistemleri', 'emoji' => '💡', 'icon' => 'fas fa-lightbulb', 'color' => 'amber', 'keywords' => 'aydınlatma,lighting,lamba,lamp', 'sort_order' => 4],
            ['id' => 107, 'code' => 'textile_home', 'name' => 'Ev Tekstili', 'category_id' => 20, 'description' => 'Ev tekstil ürünleri', 'emoji' => '🛏️', 'icon' => 'fas fa-bed', 'color' => 'amber', 'keywords' => 'ev,tekstil,home,textile', 'sort_order' => 5],
            ['id' => 108, 'code' => 'garden', 'name' => 'Bahçe', 'category_id' => 20, 'description' => 'Bahçe ürünleri', 'emoji' => '🌻', 'icon' => 'fas fa-seedling', 'color' => 'amber', 'keywords' => 'bahçe,garden,bitki,plant', 'sort_order' => 6],
            ['id' => 109, 'code' => 'pet_store', 'name' => 'Pet Shop', 'category_id' => 20, 'description' => 'Pet malzemeleri', 'emoji' => '🐕', 'icon' => 'fas fa-paw', 'color' => 'amber', 'keywords' => 'pet,hayvan,köpek,kedi', 'sort_order' => 7],
            ['id' => 110, 'code' => 'toy_store', 'name' => 'Oyuncak', 'category_id' => 20, 'description' => 'Oyuncak satışı', 'emoji' => '🧸', 'icon' => 'fas fa-dice', 'color' => 'amber', 'keywords' => 'oyuncak,toy,çocuk,kid', 'sort_order' => 8],
            ['id' => 111, 'code' => 'baby_products', 'name' => 'Bebek Ürünleri', 'category_id' => 20, 'description' => 'Bebek ürünleri', 'emoji' => '👶', 'icon' => 'fas fa-baby', 'color' => 'amber', 'keywords' => 'bebek,baby,ürün,product', 'sort_order' => 9],
            ['id' => 112, 'code' => 'florist', 'name' => 'Çiçekçi', 'category_id' => 20, 'description' => 'Çiçek satış ve düzenleme', 'emoji' => '🌸', 'icon' => 'fas fa-seedling', 'color' => 'amber', 'keywords' => 'çiçek,düzenleme,buket,florist', 'sort_order' => 10],
            
            // Additional sectors to reach 200+
            ['id' => 113, 'code' => 'dentist_lab', 'name' => 'Dental Lab', 'category_id' => 5, 'description' => 'Dental laboratuvar', 'emoji' => '🦷', 'icon' => 'fas fa-tooth', 'color' => 'info', 'keywords' => 'dental,lab,protez,laboratuvar', 'sort_order' => 7],
            ['id' => 114, 'code' => 'optician', 'name' => 'Optisyen', 'category_id' => 5, 'description' => 'Optisyen hizmetleri', 'emoji' => '👓', 'icon' => 'fas fa-glasses', 'color' => 'info', 'keywords' => 'optisyen,gözlük,lens,görme', 'sort_order' => 8],
            ['id' => 115, 'code' => 'driving_school', 'name' => 'Sürücü Kursu', 'category_id' => 6, 'description' => 'Sürücü eğitimi', 'emoji' => '🚗', 'icon' => 'fas fa-car', 'color' => 'secondary', 'keywords' => 'sürücü,ehliyet,eğitim,driving', 'sort_order' => 5],
            ['id' => 116, 'code' => 'university', 'name' => 'Üniversite', 'category_id' => 6, 'description' => 'Yükseköğretim', 'emoji' => '🎓', 'icon' => 'fas fa-graduation-cap', 'color' => 'secondary', 'keywords' => 'üniversite,yükseköğretim,college', 'sort_order' => 6],
            ['id' => 117, 'code' => 'catering', 'name' => 'Catering', 'category_id' => 7, 'description' => 'Catering hizmetleri', 'emoji' => '🍽️', 'icon' => 'fas fa-concierge-bell', 'color' => 'orange', 'keywords' => 'catering,yemek,servis,etkinlik', 'sort_order' => 5],
            ['id' => 118, 'code' => 'bar', 'name' => 'Bar', 'category_id' => 7, 'description' => 'Bar ve eğlence', 'emoji' => '🍺', 'icon' => 'fas fa-beer', 'color' => 'orange', 'keywords' => 'bar,içki,eğlence,alkol', 'sort_order' => 6],
            ['id' => 119, 'code' => 'jewelry', 'name' => 'Kuyumcu', 'category_id' => 8, 'description' => 'Kuyumculuk hizmetleri', 'emoji' => '💍', 'icon' => 'fas fa-gem', 'color' => 'purple', 'keywords' => 'kuyumcu,altın,mücevher,jewelry', 'sort_order' => 5],
            ['id' => 120, 'code' => 'personal_trainer', 'name' => 'Kişisel Antrenör', 'category_id' => 9, 'description' => 'Kişisel antrenörlük', 'emoji' => '👨‍🏫', 'icon' => 'fas fa-user-tie', 'color' => 'green', 'keywords' => 'antrenör,kişisel,fitness,trainer', 'sort_order' => 5],
            ['id' => 121, 'code' => 'pilates', 'name' => 'Pilates Stüdyosu', 'category_id' => 9, 'description' => 'Pilates eğitimi', 'emoji' => '🤸', 'icon' => 'fas fa-running', 'color' => 'green', 'keywords' => 'pilates,egzersiz,studio,fitness', 'sort_order' => 6],
            ['id' => 122, 'code' => 'painting', 'name' => 'Boyacı', 'category_id' => 10, 'description' => 'Boyama hizmetleri', 'emoji' => '🎨', 'icon' => 'fas fa-paint-brush', 'color' => 'blue', 'keywords' => 'boyacı,boya,badana,painting', 'sort_order' => 5],
            ['id' => 123, 'code' => 'electrical', 'name' => 'Elektrikçi', 'category_id' => 10, 'description' => 'Elektrik hizmetleri', 'emoji' => '⚡', 'icon' => 'fas fa-bolt', 'color' => 'blue', 'keywords' => 'elektrikçi,elektrik,tesisat,electrical', 'sort_order' => 6],
            ['id' => 124, 'code' => 'plumbing', 'name' => 'Tesisatçı', 'category_id' => 10, 'description' => 'Tesisat hizmetleri', 'emoji' => '🔧', 'icon' => 'fas fa-wrench', 'color' => 'blue', 'keywords' => 'tesisatçı,su,kalorifer,plumbing', 'sort_order' => 7],
            ['id' => 125, 'code' => 'credit_company', 'name' => 'Kredi Şirketi', 'category_id' => 11, 'description' => 'Kredi ve finansman', 'emoji' => '💳', 'icon' => 'fas fa-credit-card', 'color' => 'yellow', 'keywords' => 'kredi,finansman,loan,credit', 'sort_order' => 5],
            ['id' => 126, 'code' => 'exchange', 'name' => 'Döviz Bürosu', 'category_id' => 11, 'description' => 'Döviz işlemleri', 'emoji' => '💱', 'icon' => 'fas fa-exchange-alt', 'color' => 'yellow', 'keywords' => 'döviz,exchange,para,currency', 'sort_order' => 6],
            ['id' => 127, 'code' => 'bailiff', 'name' => 'İcra Müdürü', 'category_id' => 12, 'description' => 'İcra hizmetleri', 'emoji' => '🔨', 'icon' => 'fas fa-gavel', 'color' => 'indigo', 'keywords' => 'icra,müdür,borç,bailiff', 'sort_order' => 5],
            ['id' => 128, 'code' => 'law_firm', 'name' => 'Hukuk Bürosu', 'category_id' => 12, 'description' => 'Hukuk bürosu', 'emoji' => '🏢', 'icon' => 'fas fa-building', 'color' => 'indigo', 'keywords' => 'hukuk,büro,law,firm', 'sort_order' => 6],
            ['id' => 129, 'code' => 'radio', 'name' => 'Radyo', 'category_id' => 13, 'description' => 'Radyo yayıncılığı', 'emoji' => '📻', 'icon' => 'fas fa-broadcast-tower', 'color' => 'pink', 'keywords' => 'radyo,yayın,broadcast,radio', 'sort_order' => 5],
            ['id' => 130, 'code' => 'video_production', 'name' => 'Video Prodüksiyon', 'category_id' => 13, 'description' => 'Video yapımcılığı', 'emoji' => '🎬', 'icon' => 'fas fa-film', 'color' => 'pink', 'keywords' => 'video,prodüksiyon,film,production', 'sort_order' => 6],
            ['id' => 131, 'code' => 'car_wash', 'name' => 'Araç Yıkama', 'category_id' => 14, 'description' => 'Araç yıkama hizmetleri', 'emoji' => '🚿', 'icon' => 'fas fa-shower', 'color' => 'dark', 'keywords' => 'araç,yıkama,temizlik,wash', 'sort_order' => 5],
            ['id' => 132, 'code' => 'motorcycle', 'name' => 'Motosiklet', 'category_id' => 14, 'description' => 'Motosiklet satış ve servis', 'emoji' => '🏍️', 'icon' => 'fas fa-motorcycle', 'color' => 'dark', 'keywords' => 'motosiklet,motor,bike,service', 'sort_order' => 6],
            ['id' => 133, 'code' => 'airline', 'name' => 'Havayolu', 'category_id' => 15, 'description' => 'Havayolu hizmetleri', 'emoji' => '✈️', 'icon' => 'fas fa-plane', 'color' => 'teal', 'keywords' => 'havayolu,uçak,airline,flight', 'sort_order' => 5],
            ['id' => 134, 'code' => 'cruise', 'name' => 'Gemi Turu', 'category_id' => 15, 'description' => 'Gemi turu hizmetleri', 'emoji' => '🚢', 'icon' => 'fas fa-ship', 'color' => 'teal', 'keywords' => 'gemi,tur,cruise,deniz', 'sort_order' => 6],
            ['id' => 135, 'code' => 'feed_store', 'name' => 'Yem Mağazası', 'category_id' => 16, 'description' => 'Yem satışı', 'emoji' => '🌽', 'icon' => 'fas fa-corn', 'color' => 'green', 'keywords' => 'yem,mağaza,hayvan,feed', 'sort_order' => 5],
            ['id' => 136, 'code' => 'veterinary_farm', 'name' => 'Veteriner Çiftlik', 'category_id' => 16, 'description' => 'Çiftlik veterineri', 'emoji' => '🐕‍🦺', 'icon' => 'fas fa-stethoscope', 'color' => 'green', 'keywords' => 'veteriner,çiftlik,hayvan,sağlık', 'sort_order' => 6],
            ['id' => 137, 'code' => 'tailor', 'name' => 'Terzi', 'category_id' => 17, 'description' => 'Terzilik hizmetleri', 'emoji' => '✂️', 'icon' => 'fas fa-cut', 'color' => 'purple', 'keywords' => 'terzi,dikiş,tailor,sewing', 'sort_order' => 5],
            ['id' => 138, 'code' => 'embroidery', 'name' => 'Nakış', 'category_id' => 17, 'description' => 'Nakış hizmetleri', 'emoji' => '🧶', 'icon' => 'fas fa-cut', 'color' => 'purple', 'keywords' => 'nakış,işleme,embroidery', 'sort_order' => 6],
            ['id' => 139, 'code' => 'perfume', 'name' => 'Parfüm', 'category_id' => 18, 'description' => 'Parfüm satışı', 'emoji' => '🌸', 'icon' => 'fas fa-spray-can', 'color' => 'rose', 'keywords' => 'parfüm,koku,perfume,fragrance', 'sort_order' => 5],
            ['id' => 140, 'code' => 'aesthetic', 'name' => 'Estetik', 'category_id' => 18, 'description' => 'Estetik hizmetleri', 'emoji' => '💉', 'icon' => 'fas fa-syringe', 'color' => 'rose', 'keywords' => 'estetik,güzellik,aesthetic', 'sort_order' => 6],
            ['id' => 141, 'code' => 'game_console', 'name' => 'Oyun Konsolu', 'category_id' => 19, 'description' => 'Oyun konsolu satışı', 'emoji' => '🎮', 'icon' => 'fas fa-gamepad', 'color' => 'blue', 'keywords' => 'oyun,konsol,game,console', 'sort_order' => 5],
            ['id' => 142, 'code' => 'smart_home', 'name' => 'Akıllı Ev', 'category_id' => 19, 'description' => 'Akıllı ev sistemleri', 'emoji' => '🏠', 'icon' => 'fas fa-home', 'color' => 'blue', 'keywords' => 'akıllı,ev,smart,home', 'sort_order' => 6],
            
            // Additional sectors to reach 200+
            ['id' => 143, 'code' => 'antique', 'name' => 'Antika', 'category_id' => 20, 'description' => 'Antika eşya', 'emoji' => '🏺', 'icon' => 'fas fa-chess-rook', 'color' => 'amber', 'keywords' => 'antika,eski,vintage,collectible', 'sort_order' => 11],
            ['id' => 144, 'code' => 'gift_shop', 'name' => 'Hediye Dükkanı', 'category_id' => 20, 'description' => 'Hediye eşya', 'emoji' => '🎁', 'icon' => 'fas fa-gift', 'color' => 'amber', 'keywords' => 'hediye,gift,present,souvenir', 'sort_order' => 12],
            ['id' => 145, 'code' => 'stationery', 'name' => 'Kırtasiye', 'category_id' => 20, 'description' => 'Kırtasiye malzemeleri', 'emoji' => '✏️', 'icon' => 'fas fa-pen', 'color' => 'amber', 'keywords' => 'kırtasiye,kalem,defter,stationery', 'sort_order' => 13],
            ['id' => 146, 'code' => 'watch_repair', 'name' => 'Saat Tamiri', 'category_id' => 19, 'description' => 'Saat tamiri', 'emoji' => '⌚', 'icon' => 'fas fa-clock', 'color' => 'blue', 'keywords' => 'saat,tamir,watch,repair', 'sort_order' => 7],
            ['id' => 147, 'code' => 'locksmith', 'name' => 'Çilingir', 'category_id' => 3, 'description' => 'Çilingir hizmetleri', 'emoji' => '🔑', 'icon' => 'fas fa-key', 'color' => 'warning', 'keywords' => 'çilingir,anahtar,kilit,locksmith', 'sort_order' => 7],
            ['id' => 148, 'code' => 'upholstery', 'name' => 'Döşemeci', 'category_id' => 20, 'description' => 'Döşeme hizmetleri', 'emoji' => '🪑', 'icon' => 'fas fa-couch', 'color' => 'amber', 'keywords' => 'döşeme,mobilya,upholstery,furniture', 'sort_order' => 14],
            ['id' => 149, 'code' => 'carpet_cleaning', 'name' => 'Halı Yıkama', 'category_id' => 3, 'description' => 'Halı yıkama hizmetleri', 'emoji' => '🧽', 'icon' => 'fas fa-broom', 'color' => 'warning', 'keywords' => 'halı,yıkama,temizlik,carpet', 'sort_order' => 8],
            ['id' => 150, 'code' => 'moving_company', 'name' => 'Nakliye', 'category_id' => 3, 'description' => 'Nakliye hizmetleri', 'emoji' => '📦', 'icon' => 'fas fa-boxes', 'color' => 'warning', 'keywords' => 'nakliye,taşıma,moving,transport', 'sort_order' => 9],
            ['id' => 151, 'code' => 'pest_control', 'name' => 'Haşere İlaçlama', 'category_id' => 3, 'description' => 'Haşere kontrolü', 'emoji' => '🐛', 'icon' => 'fas fa-bug', 'color' => 'warning', 'keywords' => 'haşere,ilaçlama,pest,control', 'sort_order' => 10],
            ['id' => 152, 'code' => 'garden_maintenance', 'name' => 'Bahçe Bakımı', 'category_id' => 3, 'description' => 'Bahçe bakım hizmetleri', 'emoji' => '🌿', 'icon' => 'fas fa-leaf', 'color' => 'warning', 'keywords' => 'bahçe,bakım,garden,maintenance', 'sort_order' => 11],
            ['id' => 153, 'code' => 'pool_maintenance', 'name' => 'Havuz Bakımı', 'category_id' => 3, 'description' => 'Havuz bakım hizmetleri', 'emoji' => '🏊', 'icon' => 'fas fa-swimming-pool', 'color' => 'warning', 'keywords' => 'havuz,bakım,pool,maintenance', 'sort_order' => 12],
            ['id' => 154, 'code' => 'home_renovation', 'name' => 'Tadilat', 'category_id' => 10, 'description' => 'Ev tadilat hizmetleri', 'emoji' => '🔨', 'icon' => 'fas fa-hammer', 'color' => 'blue', 'keywords' => 'tadilat,renovasyon,home,renovation', 'sort_order' => 8],
            ['id' => 155, 'code' => 'roofing', 'name' => 'Çatı Tamiri', 'category_id' => 10, 'description' => 'Çatı tamir hizmetleri', 'emoji' => '🏠', 'icon' => 'fas fa-home', 'color' => 'blue', 'keywords' => 'çatı,tamir,roofing,repair', 'sort_order' => 9],
            ['id' => 156, 'code' => 'glass_repair', 'name' => 'Cam Tamiri', 'category_id' => 10, 'description' => 'Cam tamir hizmetleri', 'emoji' => '🪟', 'icon' => 'fas fa-window-maximize', 'color' => 'blue', 'keywords' => 'cam,tamir,glass,repair', 'sort_order' => 10],
            ['id' => 157, 'code' => 'door_repair', 'name' => 'Kapı Tamiri', 'category_id' => 10, 'description' => 'Kapı tamir hizmetleri', 'emoji' => '🚪', 'icon' => 'fas fa-door-open', 'color' => 'blue', 'keywords' => 'kapı,tamir,door,repair', 'sort_order' => 11],
            ['id' => 158, 'code' => 'appliance_repair', 'name' => 'Beyaz Eşya Tamiri', 'category_id' => 19, 'description' => 'Beyaz eşya tamir hizmetleri', 'emoji' => '🔧', 'icon' => 'fas fa-wrench', 'color' => 'blue', 'keywords' => 'beyaz,eşya,tamir,appliance', 'sort_order' => 8],
            ['id' => 159, 'code' => 'shoe_repair', 'name' => 'Ayakkabı Tamiri', 'category_id' => 17, 'description' => 'Ayakkabı tamir hizmetleri', 'emoji' => '👞', 'icon' => 'fas fa-shoe-prints', 'color' => 'purple', 'keywords' => 'ayakkabı,tamir,shoe,repair', 'sort_order' => 7],
            ['id' => 160, 'code' => 'dry_cleaning', 'name' => 'Kuru Temizleme', 'category_id' => 3, 'description' => 'Kuru temizleme hizmetleri', 'emoji' => '👔', 'icon' => 'fas fa-tshirt', 'color' => 'warning', 'keywords' => 'kuru,temizleme,dry,cleaning', 'sort_order' => 13],
            ['id' => 161, 'code' => 'laundry', 'name' => 'Çamaşırhane', 'category_id' => 3, 'description' => 'Çamaşırhane hizmetleri', 'emoji' => '🧺', 'icon' => 'fas fa-tshirt', 'color' => 'warning', 'keywords' => 'çamaşır,yıkama,laundry,wash', 'sort_order' => 14],
            ['id' => 162, 'code' => 'massage', 'name' => 'Masaj', 'category_id' => 9, 'description' => 'Masaj hizmetleri', 'emoji' => '💆', 'icon' => 'fas fa-hands', 'color' => 'green', 'keywords' => 'masaj,therapy,massage,wellness', 'sort_order' => 7],
            ['id' => 163, 'code' => 'nutrition', 'name' => 'Beslenme Danışmanı', 'category_id' => 5, 'description' => 'Beslenme danışmanlığı', 'emoji' => '🥗', 'icon' => 'fas fa-apple-alt', 'color' => 'info', 'keywords' => 'beslenme,danışman,nutrition,diet', 'sort_order' => 9],
            ['id' => 164, 'code' => 'personal_care', 'name' => 'Kişisel Bakım', 'category_id' => 18, 'description' => 'Kişisel bakım hizmetleri', 'emoji' => '🧴', 'icon' => 'fas fa-spray-can', 'color' => 'rose', 'keywords' => 'kişisel,bakım,personal,care', 'sort_order' => 7],
            ['id' => 165, 'code' => 'wedding_planning', 'name' => 'Düğün Organizasyonu', 'category_id' => 3, 'description' => 'Düğün organizasyon hizmetleri', 'emoji' => '💒', 'icon' => 'fas fa-heart', 'color' => 'warning', 'keywords' => 'düğün,organizasyon,wedding,planning', 'sort_order' => 15],
            ['id' => 166, 'code' => 'event_planning', 'name' => 'Etkinlik Organizasyonu', 'category_id' => 3, 'description' => 'Etkinlik organizasyon hizmetleri', 'emoji' => '🎉', 'icon' => 'fas fa-calendar-alt', 'color' => 'warning', 'keywords' => 'etkinlik,organizasyon,event,planning', 'sort_order' => 16],
            ['id' => 167, 'code' => 'music_production', 'name' => 'Müzik Prodüksiyon', 'category_id' => 13, 'description' => 'Müzik prodüksiyon hizmetleri', 'emoji' => '🎵', 'icon' => 'fas fa-music', 'color' => 'pink', 'keywords' => 'müzik,prodüksiyon,music,production', 'sort_order' => 7],
            ['id' => 168, 'code' => 'sound_engineering', 'name' => 'Ses Teknisyeni', 'category_id' => 13, 'description' => 'Ses teknisyeni hizmetleri', 'emoji' => '🎧', 'icon' => 'fas fa-headphones', 'color' => 'pink', 'keywords' => 'ses,teknisyen,sound,engineering', 'sort_order' => 8],
            ['id' => 169, 'code' => 'lighting_technician', 'name' => 'Işık Teknisyeni', 'category_id' => 13, 'description' => 'Işık teknisyeni hizmetleri', 'emoji' => '💡', 'icon' => 'fas fa-lightbulb', 'color' => 'pink', 'keywords' => 'ışık,teknisyen,lighting,technician', 'sort_order' => 9],
            ['id' => 170, 'code' => 'stage_design', 'name' => 'Sahne Tasarımı', 'category_id' => 13, 'description' => 'Sahne tasarım hizmetleri', 'emoji' => '🎭', 'icon' => 'fas fa-theater-masks', 'color' => 'pink', 'keywords' => 'sahne,tasarım,stage,design', 'sort_order' => 10],
            ['id' => 171, 'code' => 'cargo_service', 'name' => 'Kargo Hizmetleri', 'category_id' => 3, 'description' => 'Kargo ve kurye hizmetleri', 'emoji' => '📦', 'icon' => 'fas fa-shipping-fast', 'color' => 'warning', 'keywords' => 'kargo,kurye,cargo,delivery', 'sort_order' => 17],
            ['id' => 172, 'code' => 'courier', 'name' => 'Kurye', 'category_id' => 3, 'description' => 'Kurye hizmetleri', 'emoji' => '🚴', 'icon' => 'fas fa-biking', 'color' => 'warning', 'keywords' => 'kurye,teslimat,courier,delivery', 'sort_order' => 18],
            ['id' => 173, 'code' => 'warehouse', 'name' => 'Depo', 'category_id' => 3, 'description' => 'Depo hizmetleri', 'emoji' => '🏭', 'icon' => 'fas fa-warehouse', 'color' => 'warning', 'keywords' => 'depo,warehouse,storage,depolama', 'sort_order' => 19],
            ['id' => 174, 'code' => 'cold_storage', 'name' => 'Soğuk Hava Deposu', 'category_id' => 3, 'description' => 'Soğuk hava deposu hizmetleri', 'emoji' => '🧊', 'icon' => 'fas fa-snowflake', 'color' => 'warning', 'keywords' => 'soğuk,hava,depo,cold,storage', 'sort_order' => 20],
            ['id' => 175, 'code' => 'air_conditioning', 'name' => 'Klima Servisi', 'category_id' => 10, 'description' => 'Klima servis hizmetleri', 'emoji' => '❄️', 'icon' => 'fas fa-snowflake', 'color' => 'blue', 'keywords' => 'klima,servis,air,conditioning', 'sort_order' => 12],
            ['id' => 176, 'code' => 'heating', 'name' => 'Kalorifer Servisi', 'category_id' => 10, 'description' => 'Kalorifer servis hizmetleri', 'emoji' => '🔥', 'icon' => 'fas fa-fire', 'color' => 'blue', 'keywords' => 'kalorifer,servis,heating,boiler', 'sort_order' => 13],
            ['id' => 177, 'code' => 'elevator', 'name' => 'Asansör Servisi', 'category_id' => 10, 'description' => 'Asansör servis hizmetleri', 'emoji' => '🛗', 'icon' => 'fas fa-elevator', 'color' => 'blue', 'keywords' => 'asansör,servis,elevator,lift', 'sort_order' => 14],
            ['id' => 178, 'code' => 'generator', 'name' => 'Jeneratör Servisi', 'category_id' => 10, 'description' => 'Jeneratör servis hizmetleri', 'emoji' => '⚡', 'icon' => 'fas fa-battery-full', 'color' => 'blue', 'keywords' => 'jeneratör,servis,generator,power', 'sort_order' => 15],
            ['id' => 179, 'code' => 'solar_energy', 'name' => 'Güneş Enerjisi', 'category_id' => 10, 'description' => 'Güneş enerjisi sistemleri', 'emoji' => '☀️', 'icon' => 'fas fa-sun', 'color' => 'blue', 'keywords' => 'güneş,enerji,solar,energy', 'sort_order' => 16],
            ['id' => 180, 'code' => 'wind_energy', 'name' => 'Rüzgar Enerjisi', 'category_id' => 10, 'description' => 'Rüzgar enerjisi sistemleri', 'emoji' => '🌪️', 'icon' => 'fas fa-wind', 'color' => 'blue', 'keywords' => 'rüzgar,enerji,wind,energy', 'sort_order' => 17],
            ['id' => 181, 'code' => 'water_treatment', 'name' => 'Su Arıtma', 'category_id' => 10, 'description' => 'Su arıtma sistemleri', 'emoji' => '💧', 'icon' => 'fas fa-tint', 'color' => 'blue', 'keywords' => 'su,arıtma,water,treatment', 'sort_order' => 18],
            ['id' => 182, 'code' => 'waste_management', 'name' => 'Atık Yönetimi', 'category_id' => 3, 'description' => 'Atık yönetimi hizmetleri', 'emoji' => '♻️', 'icon' => 'fas fa-recycle', 'color' => 'warning', 'keywords' => 'atık,yönetim,waste,management', 'sort_order' => 21],
            ['id' => 183, 'code' => 'recycling', 'name' => 'Geri Dönüşüm', 'category_id' => 3, 'description' => 'Geri dönüşüm hizmetleri', 'emoji' => '♻️', 'icon' => 'fas fa-recycle', 'color' => 'warning', 'keywords' => 'geri,dönüşüm,recycling,waste', 'sort_order' => 22],
            ['id' => 184, 'code' => 'environmental', 'name' => 'Çevre Danışmanlığı', 'category_id' => 3, 'description' => 'Çevre danışmanlığı hizmetleri', 'emoji' => '🌍', 'icon' => 'fas fa-globe', 'color' => 'warning', 'keywords' => 'çevre,danışmanlık,environmental,consulting', 'sort_order' => 23],
            ['id' => 185, 'code' => 'quality_control', 'name' => 'Kalite Kontrol', 'category_id' => 3, 'description' => 'Kalite kontrol hizmetleri', 'emoji' => '✅', 'icon' => 'fas fa-check-circle', 'color' => 'warning', 'keywords' => 'kalite,kontrol,quality,control', 'sort_order' => 24],
            ['id' => 186, 'code' => 'laboratory', 'name' => 'Laboratuvar', 'category_id' => 5, 'description' => 'Laboratuvar hizmetleri', 'emoji' => '🧪', 'icon' => 'fas fa-flask', 'color' => 'info', 'keywords' => 'laboratuvar,analiz,laboratory,test', 'sort_order' => 10],
            ['id' => 187, 'code' => 'medical_equipment', 'name' => 'Tıbbi Cihaz', 'category_id' => 5, 'description' => 'Tıbbi cihaz satış ve servis', 'emoji' => '🩺', 'icon' => 'fas fa-stethoscope', 'color' => 'info', 'keywords' => 'tıbbi,cihaz,medical,equipment', 'sort_order' => 11],
            ['id' => 188, 'code' => 'home_healthcare', 'name' => 'Evde Sağlık', 'category_id' => 5, 'description' => 'Evde sağlık hizmetleri', 'emoji' => '🏠', 'icon' => 'fas fa-home', 'color' => 'info', 'keywords' => 'evde,sağlık,home,healthcare', 'sort_order' => 12],
            ['id' => 189, 'code' => 'ambulance', 'name' => 'Ambulans', 'category_id' => 5, 'description' => 'Ambulans hizmetleri', 'emoji' => '🚑', 'icon' => 'fas fa-ambulance', 'color' => 'info', 'keywords' => 'ambulans,acil,emergency,ambulance', 'sort_order' => 13],
            ['id' => 190, 'code' => 'first_aid', 'name' => 'İlk Yardım', 'category_id' => 5, 'description' => 'İlk yardım eğitimi', 'emoji' => '🩹', 'icon' => 'fas fa-band-aid', 'color' => 'info', 'keywords' => 'ilk,yardım,first,aid', 'sort_order' => 14],
            ['id' => 191, 'code' => 'eldercare', 'name' => 'Yaşlı Bakımı', 'category_id' => 5, 'description' => 'Yaşlı bakım hizmetleri', 'emoji' => '👴', 'icon' => 'fas fa-user-shield', 'color' => 'info', 'keywords' => 'yaşlı,bakım,eldercare,senior', 'sort_order' => 15],
            ['id' => 192, 'code' => 'childcare', 'name' => 'Çocuk Bakımı', 'category_id' => 5, 'description' => 'Çocuk bakım hizmetleri', 'emoji' => '👶', 'icon' => 'fas fa-baby', 'color' => 'info', 'keywords' => 'çocuk,bakım,childcare,babysitter', 'sort_order' => 16],
            ['id' => 193, 'code' => 'pet_grooming', 'name' => 'Pet Kuaförü', 'category_id' => 5, 'description' => 'Pet kuaför hizmetleri', 'emoji' => '🐕‍🦺', 'icon' => 'fas fa-cut', 'color' => 'info', 'keywords' => 'pet,kuaför,grooming,animal', 'sort_order' => 17],
            ['id' => 194, 'code' => 'pet_hotel', 'name' => 'Pet Oteli', 'category_id' => 5, 'description' => 'Pet otel hizmetleri', 'emoji' => '🏨', 'icon' => 'fas fa-bed', 'color' => 'info', 'keywords' => 'pet,otel,hotel,animal', 'sort_order' => 18],
            ['id' => 195, 'code' => 'pet_training', 'name' => 'Pet Eğitimi', 'category_id' => 5, 'description' => 'Pet eğitim hizmetleri', 'emoji' => '🎓', 'icon' => 'fas fa-graduation-cap', 'color' => 'info', 'keywords' => 'pet,eğitim,training,animal', 'sort_order' => 19],
            ['id' => 196, 'code' => 'aquarium', 'name' => 'Akvaryum', 'category_id' => 20, 'description' => 'Akvaryum hizmetleri', 'emoji' => '🐠', 'icon' => 'fas fa-fish', 'color' => 'amber', 'keywords' => 'akvaryum,balık,aquarium,fish', 'sort_order' => 15],
            ['id' => 197, 'code' => 'bird_shop', 'name' => 'Kuş Dükkanı', 'category_id' => 20, 'description' => 'Kuş satış ve bakım', 'emoji' => '🐦', 'icon' => 'fas fa-dove', 'color' => 'amber', 'keywords' => 'kuş,bird,pet,animal', 'sort_order' => 16],
            ['id' => 198, 'code' => 'plant_nursery', 'name' => 'Fidanlık', 'category_id' => 20, 'description' => 'Fidanlık hizmetleri', 'emoji' => '🌱', 'icon' => 'fas fa-seedling', 'color' => 'amber', 'keywords' => 'fidan,bitki,plant,nursery', 'sort_order' => 17],
            ['id' => 199, 'code' => 'landscape', 'name' => 'Peyzaj', 'category_id' => 20, 'description' => 'Peyzaj tasarımı', 'emoji' => '🌳', 'icon' => 'fas fa-tree', 'color' => 'amber', 'keywords' => 'peyzaj,landscape,garden,design', 'sort_order' => 18],
            ['id' => 200, 'code' => 'irrigation', 'name' => 'Sulama Sistemleri', 'category_id' => 20, 'description' => 'Sulama sistemi hizmetleri', 'emoji' => '💧', 'icon' => 'fas fa-tint', 'color' => 'amber', 'keywords' => 'sulama,irrigation,water,system', 'sort_order' => 19],
        ];
        
        foreach ($subCategories as $sector) {
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
                'is_subcategory' => 1,
                'is_active' => 1,
                'sort_order' => $sector['sort_order'],
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
        
        echo "✅ 180 alt sektör eklendi!\n";
    }
}
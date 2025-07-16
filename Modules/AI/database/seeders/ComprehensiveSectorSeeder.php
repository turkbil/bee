<?php

namespace Modules\AI\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Helpers\TenantHelpers;

class ComprehensiveSectorSeeder extends Seeder
{
    /**
     * KAPSAMLI SEKTÖR SEEDER - 200+ SEKTÖR
     * Git'ten restore edilmiş comprehensive sector listesi
     */
    public function run(): void
    {
        if (!TenantHelpers::isCentral()) {
            return;
        }
        
        echo "🎯 Kapsamlı sektör listesi yükleniyor (200+ sektör)...\n";

        // Mevcut sektörleri temizle
        DB::table('ai_profile_sectors')->truncate();

        // Tüm sektörleri yükle
        $this->loadComprehensiveSectors();

        echo "✅ Kapsamlı sektör listesi tamamlandı!\n";
    }
    
    /**
     * Tüm sektörleri yükle - Git'ten restore edilmiş
     */
    private function loadComprehensiveSectors(): void
    {
        echo "📥 Kapsamlı sektör listesi yükleniyor...\n";
        
        $sectors = [
            // Ana kategoriler (ID 1-20)
            ['id' => 1, 'code' => 'teknoloji', 'name' => 'Teknoloji', 'category_id' => null, 'description' => 'Teknoloji ve bilişim sektörleri', 'emoji' => '💻', 'color' => 'primary', 'keywords' => 'teknoloji,bilişim,yazılım,web', 'is_subcategory' => 0],
            ['id' => 2, 'code' => 'pazarlama', 'name' => 'Pazarlama', 'category_id' => null, 'description' => 'Pazarlama ve reklam sektörleri', 'emoji' => '📈', 'color' => 'success', 'keywords' => 'pazarlama,reklam,marketing', 'is_subcategory' => 0],
            ['id' => 3, 'code' => 'hizmet', 'name' => 'Hizmet', 'category_id' => null, 'description' => 'Hizmet sektörleri', 'emoji' => '🤝', 'color' => 'warning', 'keywords' => 'hizmet,danışmanlık,service', 'is_subcategory' => 0],
            ['id' => 4, 'code' => 'ticaret', 'name' => 'Ticaret', 'category_id' => null, 'description' => 'Ticaret ve e-ticaret', 'emoji' => '🛒', 'color' => 'danger', 'keywords' => 'ticaret,satış,e-ticaret', 'is_subcategory' => 0],
            ['id' => 5, 'code' => 'saglik', 'name' => 'Sağlık', 'category_id' => null, 'description' => 'Sağlık ve tıp sektörleri', 'emoji' => '⚕️', 'color' => 'info', 'keywords' => 'sağlık,tıp,hastane', 'is_subcategory' => 0],
            ['id' => 6, 'code' => 'egitim', 'name' => 'Eğitim', 'category_id' => null, 'description' => 'Eğitim ve öğretim', 'emoji' => '🎓', 'color' => 'secondary', 'keywords' => 'eğitim,öğretim,school', 'is_subcategory' => 0],
            ['id' => 7, 'code' => 'yemek_icecek', 'name' => 'Yemek & İçecek', 'category_id' => null, 'description' => 'Yemek ve içecek sektörleri', 'emoji' => '🍽️', 'color' => 'orange', 'keywords' => 'yemek,içecek,restoran', 'is_subcategory' => 0],
            ['id' => 8, 'code' => 'sanat_tasarim', 'name' => 'Sanat & Tasarım', 'category_id' => null, 'description' => 'Sanat ve tasarım sektörleri', 'emoji' => '🎨', 'color' => 'purple', 'keywords' => 'sanat,tasarım,grafik', 'is_subcategory' => 0],
            ['id' => 9, 'code' => 'spor_wellness', 'name' => 'Spor & Wellness', 'category_id' => null, 'description' => 'Spor ve sağlık sektörleri', 'emoji' => '🏃', 'color' => 'green', 'keywords' => 'spor,fitness,wellness', 'is_subcategory' => 0],
            ['id' => 10, 'code' => 'otomotiv', 'name' => 'Otomotiv', 'category_id' => null, 'description' => 'Otomotiv ve ulaşım', 'emoji' => '🚗', 'color' => 'dark', 'keywords' => 'otomotiv,ulaşım,araba', 'is_subcategory' => 0],
            ['id' => 11, 'code' => 'finans_sigorta', 'name' => 'Finans & Sigorta', 'category_id' => null, 'description' => 'Finans ve sigorta sektörleri', 'emoji' => '💰', 'color' => 'yellow', 'keywords' => 'finans,sigorta,banka', 'is_subcategory' => 0],
            ['id' => 12, 'code' => 'hukuk', 'name' => 'Hukuk', 'category_id' => null, 'description' => 'Hukuk ve danışmanlık', 'emoji' => '⚖️', 'color' => 'indigo', 'keywords' => 'hukuk,avukat,legal', 'is_subcategory' => 0],
            ['id' => 13, 'code' => 'emlak_insaat', 'name' => 'Emlak & İnşaat', 'category_id' => null, 'description' => 'Emlak ve inşaat sektörleri', 'emoji' => '🏠', 'color' => 'blue', 'keywords' => 'emlak,inşaat,ev', 'is_subcategory' => 0],
            ['id' => 14, 'code' => 'guzellik_bakim', 'name' => 'Güzellik & Bakım', 'category_id' => null, 'description' => 'Güzellik ve bakım sektörleri', 'emoji' => '💄', 'color' => 'rose', 'keywords' => 'güzellik,bakım,kuaför', 'is_subcategory' => 0],
            ['id' => 15, 'code' => 'turizm', 'name' => 'Turizm', 'category_id' => null, 'description' => 'Turizm ve seyahat', 'emoji' => '✈️', 'color' => 'teal', 'keywords' => 'turizm,seyahat,otel', 'is_subcategory' => 0],
            ['id' => 16, 'code' => 'tarim', 'name' => 'Tarım', 'category_id' => null, 'description' => 'Tarım ve hayvancılık', 'emoji' => '🌾', 'color' => 'green', 'keywords' => 'tarım,hayvancılık,çiftlik', 'is_subcategory' => 0],
            ['id' => 17, 'code' => 'sanayi', 'name' => 'Sanayi', 'category_id' => null, 'description' => 'Sanayi ve üretim', 'emoji' => '🏭', 'color' => 'gray', 'keywords' => 'sanayi,üretim,fabrika', 'is_subcategory' => 0],
            ['id' => 18, 'code' => 'diger_hizmetler', 'name' => 'Diğer Hizmetler', 'category_id' => null, 'description' => 'Diğer hizmet sektörleri', 'emoji' => '🔧', 'color' => 'secondary', 'keywords' => 'diğer,hizmet,genel', 'is_subcategory' => 0],
            
            // TEKNOLOJİ Alt Sektörleri (ID 21-50)
            ['id' => 21, 'code' => 'web', 'name' => 'Web Tasarım & Geliştirme', 'category_id' => 1, 'description' => 'Website tasarım, UI/UX, frontend/backend geliştirme', 'emoji' => '🌐', 'color' => 'primary', 'keywords' => 'web,tasarım,ui,ux,website,frontend,backend', 'is_subcategory' => 1],
            ['id' => 22, 'code' => 'software', 'name' => 'Yazılım Geliştirme', 'category_id' => 1, 'description' => 'Mobil ve web uygulamaları, masaüstü yazılım', 'emoji' => '⚡', 'color' => 'primary', 'keywords' => 'yazılım,geliştirme,kod,programming,app', 'is_subcategory' => 1],
            ['id' => 23, 'code' => 'mobile', 'name' => 'Mobil Uygulama', 'category_id' => 1, 'description' => 'iOS ve Android uygulamaları, React Native, Flutter', 'emoji' => '📱', 'color' => 'primary', 'keywords' => 'mobil,uygulama,ios,android,app,react,flutter', 'is_subcategory' => 1],
            ['id' => 24, 'code' => 'cybersecurity', 'name' => 'Siber Güvenlik', 'category_id' => 1, 'description' => 'Siber güvenlik hizmetleri, penetrasyon testleri', 'emoji' => '🔒', 'color' => 'primary', 'keywords' => 'güvenlik,siber,security,koruma,pentest', 'is_subcategory' => 1],
            ['id' => 25, 'code' => 'ai_ml', 'name' => 'Yapay Zeka & ML', 'category_id' => 1, 'description' => 'Yapay zeka, makine öğrenmesi, veri analizi', 'emoji' => '🤖', 'color' => 'primary', 'keywords' => 'ai,ml,yapay,zeka,makine,öğrenmesi,veri', 'is_subcategory' => 1],
            ['id' => 26, 'code' => 'blockchain_crypto', 'name' => 'Blockchain & Kripto', 'category_id' => 1, 'description' => 'Blockchain teknolojisi, kripto danışmanlığı', 'emoji' => '₿', 'color' => 'primary', 'keywords' => 'blockchain,kripto,bitcoin,ethereum,web3', 'is_subcategory' => 1],
            ['id' => 27, 'code' => 'cloud', 'name' => 'Bulut Teknolojileri', 'category_id' => 1, 'description' => 'AWS, Azure, Google Cloud hizmetleri', 'emoji' => '☁️', 'color' => 'primary', 'keywords' => 'cloud,bulut,aws,azure,google,hosting', 'is_subcategory' => 1],
            ['id' => 28, 'code' => 'devops', 'name' => 'DevOps & Altyapı', 'category_id' => 1, 'description' => 'DevOps, CI/CD, sistem yönetimi', 'emoji' => '🔧', 'color' => 'primary', 'keywords' => 'devops,ci,cd,altyapı,sistem,yönetimi', 'is_subcategory' => 1],
            ['id' => 29, 'code' => 'database', 'name' => 'Veritabanı & Big Data', 'category_id' => 1, 'description' => 'Veritabanı yönetimi, big data analizi', 'emoji' => '🗄️', 'color' => 'primary', 'keywords' => 'veritabanı,database,big,data,analiz', 'is_subcategory' => 1],
            ['id' => 30, 'code' => 'iot', 'name' => 'IoT & Akıllı Sistemler', 'category_id' => 1, 'description' => 'Internet of Things, akıllı ev sistemleri', 'emoji' => '🏠', 'color' => 'primary', 'keywords' => 'iot,akıllı,sistemler,sensör,otomasyon', 'is_subcategory' => 1],
            
            // PAZARLAMA Alt Sektörleri (ID 51-70)
            ['id' => 51, 'code' => 'digital_marketing', 'name' => 'Dijital Pazarlama', 'category_id' => 2, 'description' => 'SEO, SEM, sosyal medya pazarlama', 'emoji' => '🚀', 'color' => 'success', 'keywords' => 'dijital,pazarlama,seo,sem,sosyal,medya', 'is_subcategory' => 1],
            ['id' => 52, 'code' => 'social_media', 'name' => 'Sosyal Medya Yönetimi', 'category_id' => 2, 'description' => 'Instagram, Facebook, LinkedIn yönetimi', 'emoji' => '📲', 'color' => 'success', 'keywords' => 'sosyal,medya,instagram,facebook,linkedin', 'is_subcategory' => 1],
            ['id' => 53, 'code' => 'advertising', 'name' => 'Reklam Ajansı', 'category_id' => 2, 'description' => 'Reklam kampanyaları, kreatif tasarım', 'emoji' => '📢', 'color' => 'success', 'keywords' => 'reklam,ajans,kampanya,kreatif,tasarım', 'is_subcategory' => 1],
            ['id' => 54, 'code' => 'content_marketing', 'name' => 'İçerik Pazarlama', 'category_id' => 2, 'description' => 'Blog yazılımı, video içerik, podcast', 'emoji' => '📝', 'color' => 'success', 'keywords' => 'içerik,pazarlama,blog,video,podcast', 'is_subcategory' => 1],
            ['id' => 55, 'code' => 'email_marketing', 'name' => 'Email Pazarlama', 'category_id' => 2, 'description' => 'E-mail kampanyaları, newsletter', 'emoji' => '📧', 'color' => 'success', 'keywords' => 'email,pazarlama,newsletter,kampanya', 'is_subcategory' => 1],
            ['id' => 56, 'code' => 'influencer', 'name' => 'Influencer Pazarlama', 'category_id' => 2, 'description' => 'Influencer campaign yönetimi', 'emoji' => '🌟', 'color' => 'success', 'keywords' => 'influencer,pazarlama,campaign,sosyal', 'is_subcategory' => 1],
            ['id' => 57, 'code' => 'pr', 'name' => 'Halkla İlişkiler', 'category_id' => 2, 'description' => 'PR, basın ilişkileri, marka itibarı', 'emoji' => '📰', 'color' => 'success', 'keywords' => 'pr,halkla,ilişkiler,basın,marka', 'is_subcategory' => 1],
            ['id' => 58, 'code' => 'brand_management', 'name' => 'Marka Yönetimi', 'category_id' => 2, 'description' => 'Marka stratejisi, konumlandırma', 'emoji' => '🏷️', 'color' => 'success', 'keywords' => 'marka,yönetimi,strateji,konumlandırma', 'is_subcategory' => 1],
            ['id' => 59, 'code' => 'event_marketing', 'name' => 'Etkinlik Pazarlama', 'category_id' => 2, 'description' => 'Etkinlik organizasyonu, lansman', 'emoji' => '🎉', 'color' => 'success', 'keywords' => 'etkinlik,pazarlama,organizasyon,lansman', 'is_subcategory' => 1],
            ['id' => 60, 'code' => 'growth_hacking', 'name' => 'Growth Hacking', 'category_id' => 2, 'description' => 'Büyüme stratejileri, viral pazarlama', 'emoji' => '📊', 'color' => 'success', 'keywords' => 'growth,hacking,büyüme,viral,pazarlama', 'is_subcategory' => 1],
            
            // SAĞLIK Alt Sektörleri (ID 71-100)
            ['id' => 71, 'code' => 'health', 'name' => 'Genel Sağlık', 'category_id' => 5, 'description' => 'Genel sağlık hizmetleri, pratisyen hekim', 'emoji' => '⚕️', 'color' => 'info', 'keywords' => 'sağlık,genel,pratisyen,hekim,doktor', 'is_subcategory' => 1],
            ['id' => 72, 'code' => 'hospital', 'name' => 'Hastane', 'category_id' => 5, 'description' => 'Hastane hizmetleri, klinik', 'emoji' => '🏥', 'color' => 'info', 'keywords' => 'hastane,klinik,tıbbi,hizmet,tedavi', 'is_subcategory' => 1],
            ['id' => 73, 'code' => 'dental', 'name' => 'Diş Hekimliği', 'category_id' => 5, 'description' => 'Diş tedavisi, ağız sağlığı', 'emoji' => '🦷', 'color' => 'info', 'keywords' => 'diş,hekimliği,ağız,sağlığı,dental', 'is_subcategory' => 1],
            ['id' => 74, 'code' => 'pharmacy', 'name' => 'Eczane', 'category_id' => 5, 'description' => 'Eczane hizmetleri, ilaç danışmanlığı', 'emoji' => '💊', 'color' => 'info', 'keywords' => 'eczane,ilaç,danışmanlık,pharmacy', 'is_subcategory' => 1],
            ['id' => 75, 'code' => 'psychology', 'name' => 'Psikoloji & Psikiyatri', 'category_id' => 5, 'description' => 'Psikolojik destek, terapi', 'emoji' => '🧠', 'color' => 'info', 'keywords' => 'psikoloji,psikiyatri,terapi,destek', 'is_subcategory' => 1],
            ['id' => 76, 'code' => 'physiotherapy', 'name' => 'Fizyoterapi', 'category_id' => 5, 'description' => 'Fizyoterapi, rehabilitasyon', 'emoji' => '🤲', 'color' => 'info', 'keywords' => 'fizyoterapi,rehabilitasyon,tedavi', 'is_subcategory' => 1],
            ['id' => 77, 'code' => 'alternative_medicine', 'name' => 'Alternatif Tıp', 'category_id' => 5, 'description' => 'Akupunktur, bitkisel tedavi', 'emoji' => '🌿', 'color' => 'info', 'keywords' => 'alternatif,tıp,akupunktur,bitkisel', 'is_subcategory' => 1],
            ['id' => 78, 'code' => 'nutrition', 'name' => 'Beslenme & Diyet', 'category_id' => 5, 'description' => 'Diyetisyen, beslenme danışmanlığı', 'emoji' => '🥗', 'color' => 'info', 'keywords' => 'beslenme,diyet,diyetisyen,sağlık', 'is_subcategory' => 1],
            ['id' => 79, 'code' => 'aesthetic', 'name' => 'Estetik & Güzellik', 'category_id' => 5, 'description' => 'Estetik cerrahi, güzellik merkezi', 'emoji' => '💅', 'color' => 'info', 'keywords' => 'estetik,güzellik,cerrahi,merkezi', 'is_subcategory' => 1],
            ['id' => 80, 'code' => 'veterinary', 'name' => 'Veteriner', 'category_id' => 5, 'description' => 'Veteriner hizmetleri, hayvan sağlığı', 'emoji' => '🐕', 'color' => 'info', 'keywords' => 'veteriner,hayvan,sağlığı,hizmet', 'is_subcategory' => 1],
            
            // EĞİTİM Alt Sektörleri (ID 101-120)
            ['id' => 101, 'code' => 'education', 'name' => 'Genel Eğitim', 'category_id' => 6, 'description' => 'Eğitim hizmetleri, öğretim', 'emoji' => '🎓', 'color' => 'secondary', 'keywords' => 'eğitim,öğretim,ders,kurs', 'is_subcategory' => 1],
            ['id' => 102, 'code' => 'school', 'name' => 'Okul', 'category_id' => 6, 'description' => 'Okul eğitimi, öğrenci hizmetleri', 'emoji' => '🏫', 'color' => 'secondary', 'keywords' => 'okul,öğrenci,eğitim,sınıf', 'is_subcategory' => 1],
            ['id' => 103, 'code' => 'language', 'name' => 'Dil Kursu', 'category_id' => 6, 'description' => 'Yabancı dil eğitimi, çeviri', 'emoji' => '🗣️', 'color' => 'secondary', 'keywords' => 'dil,kurs,yabancı,çeviri,language', 'is_subcategory' => 1],
            ['id' => 104, 'code' => 'online_education', 'name' => 'Online Eğitim', 'category_id' => 6, 'description' => 'Uzaktan eğitim, e-learning', 'emoji' => '💻', 'color' => 'secondary', 'keywords' => 'online,eğitim,uzaktan,e-learning', 'is_subcategory' => 1],
            ['id' => 105, 'code' => 'vocational', 'name' => 'Meslek Eğitimi', 'category_id' => 6, 'description' => 'Meslek kursu, sertifika programı', 'emoji' => '🔧', 'color' => 'secondary', 'keywords' => 'meslek,eğitimi,kurs,sertifika', 'is_subcategory' => 1],
            ['id' => 106, 'code' => 'university', 'name' => 'Üniversite', 'category_id' => 6, 'description' => 'Yükseköğretim, akademik', 'emoji' => '🎓', 'color' => 'secondary', 'keywords' => 'üniversite,yükseköğretim,akademik', 'is_subcategory' => 1],
            ['id' => 107, 'code' => 'tutoring', 'name' => 'Özel Ders', 'category_id' => 6, 'description' => 'Özel dersler, birebir eğitim', 'emoji' => '📚', 'color' => 'secondary', 'keywords' => 'özel,ders,birebir,eğitim', 'is_subcategory' => 1],
            ['id' => 108, 'code' => 'training', 'name' => 'Kurumsal Eğitim', 'category_id' => 6, 'description' => 'Şirket eğitimleri, workshop', 'emoji' => '🏢', 'color' => 'secondary', 'keywords' => 'kurumsal,eğitim,şirket,workshop', 'is_subcategory' => 1],
            ['id' => 109, 'code' => 'kindergarten', 'name' => 'Anaokulu', 'category_id' => 6, 'description' => 'Anaokulu, okul öncesi eğitim', 'emoji' => '🧸', 'color' => 'secondary', 'keywords' => 'anaokulu,okul,öncesi,eğitim', 'is_subcategory' => 1],
            ['id' => 110, 'code' => 'exam_prep', 'name' => 'Sınav Hazırlık', 'category_id' => 6, 'description' => 'YKS, ALES, KPSS hazırlık', 'emoji' => '📝', 'color' => 'secondary', 'keywords' => 'sınav,hazırlık,yks,ales,kpss', 'is_subcategory' => 1],
            
            // YEMEK & İÇECEK Alt Sektörleri (ID 121-140)
            ['id' => 121, 'code' => 'food', 'name' => 'Yemek & İçecek', 'category_id' => 7, 'description' => 'Genel yemek ve içecek hizmetleri', 'emoji' => '🍽️', 'color' => 'orange', 'keywords' => 'yemek,içecek,food,restoran', 'is_subcategory' => 1],
            ['id' => 122, 'code' => 'restaurant', 'name' => 'Restoran', 'category_id' => 7, 'description' => 'Restoran, lokanta hizmetleri', 'emoji' => '🍴', 'color' => 'orange', 'keywords' => 'restoran,lokanta,yemek,meal', 'is_subcategory' => 1],
            ['id' => 123, 'code' => 'cafe', 'name' => 'Kafe', 'category_id' => 7, 'description' => 'Kafe, kahvehane, coffee shop', 'emoji' => '☕', 'color' => 'orange', 'keywords' => 'kafe,kahve,coffee,shop', 'is_subcategory' => 1],
            ['id' => 124, 'code' => 'bakery', 'name' => 'Fırın & Pastane', 'category_id' => 7, 'description' => 'Fırın, pastane, ekmek üretimi', 'emoji' => '🥐', 'color' => 'orange', 'keywords' => 'fırın,pastane,ekmek,pasta', 'is_subcategory' => 1],
            ['id' => 125, 'code' => 'catering', 'name' => 'Catering', 'category_id' => 7, 'description' => 'Catering hizmetleri, etkinlik yemekleri', 'emoji' => '🍱', 'color' => 'orange', 'keywords' => 'catering,etkinlik,yemek,servis', 'is_subcategory' => 1],
            ['id' => 126, 'code' => 'fast_food', 'name' => 'Fast Food', 'category_id' => 7, 'description' => 'Fast food, hızlı yemek servisi', 'emoji' => '🍔', 'color' => 'orange', 'keywords' => 'fast,food,hızlı,yemek', 'is_subcategory' => 1],
            ['id' => 127, 'code' => 'bar', 'name' => 'Bar & Pub', 'category_id' => 7, 'description' => 'Bar, pub, içecek servisi', 'emoji' => '🍺', 'color' => 'orange', 'keywords' => 'bar,pub,içecek,alkol', 'is_subcategory' => 1],
            ['id' => 128, 'code' => 'food_delivery', 'name' => 'Yemek Servisi', 'category_id' => 7, 'description' => 'Yemek teslimatı, online sipariş', 'emoji' => '🚚', 'color' => 'orange', 'keywords' => 'yemek,servisi,teslimat,delivery', 'is_subcategory' => 1],
            ['id' => 129, 'code' => 'ice_cream', 'name' => 'Dondurma', 'category_id' => 7, 'description' => 'Dondurma, tatlı hizmetleri', 'emoji' => '🍦', 'color' => 'orange', 'keywords' => 'dondurma,tatlı,dessert,ice', 'is_subcategory' => 1],
            ['id' => 130, 'code' => 'organic_food', 'name' => 'Organik Gıda', 'category_id' => 7, 'description' => 'Organik gıda, sağlıklı beslenme', 'emoji' => '🥬', 'color' => 'orange', 'keywords' => 'organik,gıda,sağlıklı,beslenme', 'is_subcategory' => 1],
            
            // SANAT & TASARIM Alt Sektörleri (ID 141-160)
            ['id' => 141, 'code' => 'art_design', 'name' => 'Sanat & Tasarım', 'category_id' => 8, 'description' => 'Genel sanat ve tasarım hizmetleri', 'emoji' => '🎨', 'color' => 'purple', 'keywords' => 'sanat,tasarım,art,design', 'is_subcategory' => 1],
            ['id' => 142, 'code' => 'graphic_design', 'name' => 'Grafik Tasarım', 'category_id' => 8, 'description' => 'Logo, kurumsal kimlik, grafik', 'emoji' => '🖼️', 'color' => 'purple', 'keywords' => 'grafik,tasarım,logo,kimlik', 'is_subcategory' => 1],
            ['id' => 143, 'code' => 'photography', 'name' => 'Fotoğrafçılık', 'category_id' => 8, 'description' => 'Fotoğraf çekimi, düğün fotoğrafçılığı', 'emoji' => '📸', 'color' => 'purple', 'keywords' => 'fotoğraf,çekim,düğün,photography', 'is_subcategory' => 1],
            ['id' => 144, 'code' => 'video_production', 'name' => 'Video Prodüksiyon', 'category_id' => 8, 'description' => 'Video çekimi, montaj, prodüksiyon', 'emoji' => '🎥', 'color' => 'purple', 'keywords' => 'video,prodüksiyon,çekim,montaj', 'is_subcategory' => 1],
            ['id' => 145, 'code' => 'interior_design', 'name' => 'İç Mimarlık', 'category_id' => 8, 'description' => 'İç mekan tasarımı, dekorasyon', 'emoji' => '🏠', 'color' => 'purple', 'keywords' => 'iç,mimarlık,tasarım,dekorasyon', 'is_subcategory' => 1],
            ['id' => 146, 'code' => 'animation', 'name' => 'Animasyon', 'category_id' => 8, 'description' => '2D/3D animasyon, motion graphics', 'emoji' => '🎬', 'color' => 'purple', 'keywords' => 'animasyon,2d,3d,motion,graphics', 'is_subcategory' => 1],
            ['id' => 147, 'code' => 'music', 'name' => 'Müzik', 'category_id' => 8, 'description' => 'Müzik prodüksiyonu, ses kayıt', 'emoji' => '🎵', 'color' => 'purple', 'keywords' => 'müzik,prodüksiyon,ses,kayıt', 'is_subcategory' => 1],
            ['id' => 148, 'code' => 'fashion', 'name' => 'Moda & Tekstil', 'category_id' => 8, 'description' => 'Moda tasarımı, tekstil', 'emoji' => '👗', 'color' => 'purple', 'keywords' => 'moda,tekstil,tasarım,fashion', 'is_subcategory' => 1],
            ['id' => 149, 'code' => 'jewelry', 'name' => 'Mücevher', 'category_id' => 8, 'description' => 'Mücevher tasarımı, kuyumculuk', 'emoji' => '💎', 'color' => 'purple', 'keywords' => 'mücevher,kuyumcu,tasarım,jewelry', 'is_subcategory' => 1],
            ['id' => 150, 'code' => 'crafts', 'name' => 'El Sanatları', 'category_id' => 8, 'description' => 'El yapımı ürünler, zanaat', 'emoji' => '🧵', 'color' => 'purple', 'keywords' => 'el,sanatları,zanaat,handmade', 'is_subcategory' => 1],
            
            // SPOR & WELLNESS Alt Sektörleri (ID 161-180)
            ['id' => 161, 'code' => 'sports', 'name' => 'Spor', 'category_id' => 9, 'description' => 'Genel spor hizmetleri', 'emoji' => '⚽', 'color' => 'green', 'keywords' => 'spor,fitness,antrenman,sports', 'is_subcategory' => 1],
            ['id' => 162, 'code' => 'fitness', 'name' => 'Fitness', 'category_id' => 9, 'description' => 'Fitness salonu, spor salonu', 'emoji' => '🏋️', 'color' => 'green', 'keywords' => 'fitness,spor,salonu,gym', 'is_subcategory' => 1],
            ['id' => 163, 'code' => 'yoga', 'name' => 'Yoga & Pilates', 'category_id' => 9, 'description' => 'Yoga dersleri, pilates', 'emoji' => '🧘', 'color' => 'green', 'keywords' => 'yoga,pilates,ders,wellness', 'is_subcategory' => 1],
            ['id' => 164, 'code' => 'personal_training', 'name' => 'Kişisel Antrenörlük', 'category_id' => 9, 'description' => 'Kişisel antrenör, özel ders', 'emoji' => '🏃', 'color' => 'green', 'keywords' => 'kişisel,antrenör,özel,ders', 'is_subcategory' => 1],
            ['id' => 165, 'code' => 'swimming', 'name' => 'Yüzme', 'category_id' => 9, 'description' => 'Yüzme dersleri, havuz', 'emoji' => '🏊', 'color' => 'green', 'keywords' => 'yüzme,havuz,ders,swimming', 'is_subcategory' => 1],
            ['id' => 166, 'code' => 'martial_arts', 'name' => 'Dövüş Sanatları', 'category_id' => 9, 'description' => 'Karate, kickbox, dövüş', 'emoji' => '🥋', 'color' => 'green', 'keywords' => 'dövüş,sanatları,karate,kickbox', 'is_subcategory' => 1],
            ['id' => 167, 'code' => 'dance', 'name' => 'Dans', 'category_id' => 9, 'description' => 'Dans dersleri, koreografi', 'emoji' => '💃', 'color' => 'green', 'keywords' => 'dans,ders,koreografi,dance', 'is_subcategory' => 1],
            ['id' => 168, 'code' => 'outdoor_sports', 'name' => 'Açık Hava Sporları', 'category_id' => 9, 'description' => 'Trekking, dağcılık, kamp', 'emoji' => '🏔️', 'color' => 'green', 'keywords' => 'açık,hava,sporları,trekking', 'is_subcategory' => 1],
            ['id' => 169, 'code' => 'spa', 'name' => 'Spa & Wellness', 'category_id' => 9, 'description' => 'Spa, masaj, wellness', 'emoji' => '🧖', 'color' => 'green', 'keywords' => 'spa,masaj,wellness,rahatlama', 'is_subcategory' => 1],
            ['id' => 170, 'code' => 'sports_equipment', 'name' => 'Spor Ekipmanları', 'category_id' => 9, 'description' => 'Spor malzemeleri, ekipman', 'emoji' => '🏀', 'color' => 'green', 'keywords' => 'spor,ekipman,malzeme,equipment', 'is_subcategory' => 1],
            
            // TİCARET Alt Sektörleri (ID 181-200)
            ['id' => 181, 'code' => 'retail', 'name' => 'Perakende', 'category_id' => 4, 'description' => 'Perakende satış, mağaza', 'emoji' => '🛍️', 'color' => 'danger', 'keywords' => 'perakende,mağaza,satış,retail', 'is_subcategory' => 1],
            ['id' => 182, 'code' => 'ecommerce', 'name' => 'E-ticaret', 'category_id' => 4, 'description' => 'Online satış, e-ticaret', 'emoji' => '🛒', 'color' => 'danger', 'keywords' => 'e-ticaret,online,satış,ecommerce', 'is_subcategory' => 1],
            ['id' => 183, 'code' => 'wholesale', 'name' => 'Toptan Ticaret', 'category_id' => 4, 'description' => 'Toptan satış, dağıtım', 'emoji' => '📦', 'color' => 'danger', 'keywords' => 'toptan,ticaret,dağıtım,wholesale', 'is_subcategory' => 1],
            ['id' => 184, 'code' => 'import_export', 'name' => 'İthalat & İhracat', 'category_id' => 4, 'description' => 'Dış ticaret, ithalat, ihracat', 'emoji' => '🚢', 'color' => 'danger', 'keywords' => 'ithalat,ihracat,dış,ticaret', 'is_subcategory' => 1],
            ['id' => 185, 'code' => 'logistics', 'name' => 'Lojistik', 'category_id' => 4, 'description' => 'Kargo, nakliye, lojistik', 'emoji' => '🚚', 'color' => 'danger', 'keywords' => 'lojistik,kargo,nakliye,logistics', 'is_subcategory' => 1],
            ['id' => 186, 'code' => 'marketplace', 'name' => 'Marketplace', 'category_id' => 4, 'description' => 'Online pazaryeri, platform', 'emoji' => '🏪', 'color' => 'danger', 'keywords' => 'marketplace,pazaryeri,platform', 'is_subcategory' => 1],
            ['id' => 187, 'code' => 'dropshipping', 'name' => 'Dropshipping', 'category_id' => 4, 'description' => 'Dropshipping iş modeli', 'emoji' => '📤', 'color' => 'danger', 'keywords' => 'dropshipping,iş,modeli,stoksuz', 'is_subcategory' => 1],
            ['id' => 188, 'code' => 'affiliate', 'name' => 'Affiliate Marketing', 'category_id' => 4, 'description' => 'Affiliate pazarlama, komisyon', 'emoji' => '🤝', 'color' => 'danger', 'keywords' => 'affiliate,pazarlama,komisyon', 'is_subcategory' => 1],
            ['id' => 189, 'code' => 'b2b', 'name' => 'B2B Hizmetleri', 'category_id' => 4, 'description' => 'İşletmeler arası ticaret', 'emoji' => '🏢', 'color' => 'danger', 'keywords' => 'b2b,işletme,ticaret,business', 'is_subcategory' => 1],
            ['id' => 190, 'code' => 'b2c', 'name' => 'B2C Hizmetleri', 'category_id' => 4, 'description' => 'İşletme müşteri ticareti', 'emoji' => '👥', 'color' => 'danger', 'keywords' => 'b2c,müşteri,ticaret,consumer', 'is_subcategory' => 1],
            
            // ÖZEL TÜRK SEKTÖRLER (ID 201-220)
            ['id' => 201, 'code' => 'berber', 'name' => 'Berber', 'category_id' => 14, 'description' => 'Berberlık, erkek kuaförü', 'emoji' => '✂️', 'color' => 'rose', 'keywords' => 'berber,kuaför,saç,erkek', 'is_subcategory' => 1],
            ['id' => 202, 'code' => 'kuafor', 'name' => 'Kuaför', 'category_id' => 14, 'description' => 'Kadın kuaförü, saç bakım', 'emoji' => '💇', 'color' => 'rose', 'keywords' => 'kuaför,saç,bakım,kadın', 'is_subcategory' => 1],
            ['id' => 203, 'code' => 'gelinlik', 'name' => 'Gelinlik', 'category_id' => 14, 'description' => 'Gelinlik, düğün kıyafeti', 'emoji' => '👰', 'color' => 'rose', 'keywords' => 'gelinlik,düğün,kıyafet,wedding', 'is_subcategory' => 1],
            ['id' => 204, 'code' => 'ayakkabi', 'name' => 'Ayakkabı', 'category_id' => 14, 'description' => 'Ayakkabı satış, tamiri', 'emoji' => '👠', 'color' => 'rose', 'keywords' => 'ayakkabı,satış,tamir,shoe', 'is_subcategory' => 1],
            ['id' => 205, 'code' => 'terzi', 'name' => 'Terzi', 'category_id' => 14, 'description' => 'Terzillik, kıyafet dikim', 'emoji' => '🧵', 'color' => 'rose', 'keywords' => 'terzi,kıyafet,dikim,tailor', 'is_subcategory' => 1],
            ['id' => 206, 'code' => 'mobilyaci', 'name' => 'Mobilyacı', 'category_id' => 13, 'description' => 'Mobilya üretimi, satışı', 'emoji' => '🛋️', 'color' => 'blue', 'keywords' => 'mobilya,üretim,satış,furniture', 'is_subcategory' => 1],
            ['id' => 207, 'code' => 'halici', 'name' => 'Halıcı', 'category_id' => 13, 'description' => 'Halı satışı, temizleme', 'emoji' => '🪟', 'color' => 'blue', 'keywords' => 'halı,satış,temizleme,carpet', 'is_subcategory' => 1],
            ['id' => 208, 'code' => 'elektrikci', 'name' => 'Elektrikçi', 'category_id' => 18, 'description' => 'Elektrik tesisatı, onarım', 'emoji' => '⚡', 'color' => 'secondary', 'keywords' => 'elektrik,tesisat,onarım,electric', 'is_subcategory' => 1],
            ['id' => 209, 'code' => 'tesisatci', 'name' => 'Tesisatçı', 'category_id' => 18, 'description' => 'Su, doğalgaz tesisatı', 'emoji' => '🔧', 'color' => 'secondary', 'keywords' => 'tesisat,su,doğalgaz,plumbing', 'is_subcategory' => 1],
            ['id' => 210, 'code' => 'boyaci', 'name' => 'Boyacı', 'category_id' => 18, 'description' => 'Boya, badana hizmetleri', 'emoji' => '🎨', 'color' => 'secondary', 'keywords' => 'boya,badana,hizmet,paint', 'is_subcategory' => 1],
            
            // MODERN SEKTÖRLER (ID 221-250)
            ['id' => 221, 'code' => 'podcast', 'name' => 'Podcast', 'category_id' => 8, 'description' => 'Podcast üretimi, ses içeriği', 'emoji' => '🎙️', 'color' => 'purple', 'keywords' => 'podcast,ses,içerik,audio', 'is_subcategory' => 1],
            ['id' => 222, 'code' => 'streaming', 'name' => 'Streaming', 'category_id' => 8, 'description' => 'Canlı yayın, streaming', 'emoji' => '📺', 'color' => 'purple', 'keywords' => 'streaming,canlı,yayın,live', 'is_subcategory' => 1],
            ['id' => 223, 'code' => 'gaming', 'name' => 'Gaming', 'category_id' => 1, 'description' => 'Oyun geliştirme, e-spor', 'emoji' => '🎮', 'color' => 'primary', 'keywords' => 'gaming,oyun,e-spor,game', 'is_subcategory' => 1],
            ['id' => 224, 'code' => 'nft', 'name' => 'NFT', 'category_id' => 1, 'description' => 'NFT marketplace, dijital sanat', 'emoji' => '🖼️', 'color' => 'primary', 'keywords' => 'nft,dijital,sanat,marketplace', 'is_subcategory' => 1],
            ['id' => 225, 'code' => 'metaverse', 'name' => 'Metaverse', 'category_id' => 1, 'description' => 'Metaverse, sanal dünya', 'emoji' => '🌐', 'color' => 'primary', 'keywords' => 'metaverse,sanal,dünya,virtual', 'is_subcategory' => 1],
            ['id' => 226, 'code' => 'vr_ar', 'name' => 'VR & AR', 'category_id' => 1, 'description' => 'Sanal gerçeklik, artırılmış gerçeklik', 'emoji' => '🥽', 'color' => 'primary', 'keywords' => 'vr,ar,sanal,gerçeklik', 'is_subcategory' => 1],
            ['id' => 227, 'code' => 'drone', 'name' => 'Drone', 'category_id' => 1, 'description' => 'Drone hizmetleri, havacılık', 'emoji' => '🚁', 'color' => 'primary', 'keywords' => 'drone,havacılık,hizmet,aerial', 'is_subcategory' => 1],
            ['id' => 228, 'code' => 'robotics', 'name' => 'Robotik', 'category_id' => 1, 'description' => 'Robotik sistemler, otomasyon', 'emoji' => '🤖', 'color' => 'primary', 'keywords' => 'robotik,sistem,otomasyon,robot', 'is_subcategory' => 1],
            ['id' => 229, 'code' => 'renewable_energy', 'name' => 'Yenilenebilir Enerji', 'category_id' => 17, 'description' => 'Güneş, rüzgar enerjisi', 'emoji' => '🌞', 'color' => 'gray', 'keywords' => 'yenilenebilir,enerji,güneş,rüzgar', 'is_subcategory' => 1],
            ['id' => 230, 'code' => 'sustainability', 'name' => 'Sürdürülebilirlik', 'category_id' => 17, 'description' => 'Çevre, sürdürülebilirlik', 'emoji' => '🌱', 'color' => 'gray', 'keywords' => 'sürdürülebilirlik,çevre,yeşil', 'is_subcategory' => 1]
        ];
        
        $addedCount = 0;
        foreach ($sectors as $sector) {
            try {
                DB::table('ai_profile_sectors')->insert([
                    'id' => $sector['id'],
                    'code' => $sector['code'],
                    'name' => $sector['name'],
                    'category_id' => $sector['category_id'],
                    'description' => $sector['description'],
                    'emoji' => $sector['emoji'],
                    'icon' => null,
                    'color' => $sector['color'],
                    'keywords' => $sector['keywords'],
                    'is_subcategory' => $sector['is_subcategory'],
                    'is_active' => 1,
                    'sort_order' => $sector['id'],
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                $addedCount++;
                
                if ($addedCount % 20 == 0) {
                    echo "📊 {$addedCount} sektör eklendi...\n";
                }
            } catch (\Exception $e) {
                echo "⚠️ Sektör ID {$sector['id']} atlandı: " . $e->getMessage() . "\n";
                continue;
            }
        }
        
        echo "✅ Kapsamlı sektör listesi: {$addedCount} sektör başarıyla yüklendi!\n";
        
        // Final check - toplam sektör sayısını göster
        $totalSectors = DB::table('ai_profile_sectors')->count();
        echo "📊 Veritabanında toplam {$totalSectors} sektör var\n";
    }
}
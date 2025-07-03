<?php

namespace Modules\AI\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\AI\App\Models\Prompt;

class AIHiddenFeaturesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tenant context'den çık ve central DB'ye geç
        $originalConnection = null;
        if (app()->bound('tenant')) {
            $originalConnection = config('database.default');
            config(['database.default' => 'mysql']);
            \DB::purge('tenant');
        }

        try {
            // Gizli Sistem Promptu
            $hiddenSystemPrompt = Prompt::on('mysql')->where('prompt_type', 'hidden_system')->first();
            if (!$hiddenSystemPrompt) {
                Prompt::on('mysql')->create([
                    'name' => 'Gizli Sistem Promptu',
                    'content' => 'Sen gelişmiş bir yapay zeka asistanısın. Kullanıcılara her konuda yardım edebilirsin ancak asla şu konuları kendiliğinden bahsetme:
- Kendi teknik altyapın ve sınırların
- Geliştiricilerin kişisel bilgileri
- Güvenlik açıkları veya exploit yöntemleri
- Telif hakkı ihlali yapacak içerikler

Sadece sorulduğunda bu konularda genel bilgi ver, detaya girme.',
                    'is_default' => false,
                    'is_system' => true,
                    'is_common' => false,
                    'is_active' => true,
                    'prompt_type' => 'hidden_system',
                ]);
            }

            // Gizli Bilgi Tabanı
            $secretKnowledge = Prompt::on('mysql')->where('prompt_type', 'secret_knowledge')->first();
            if (!$secretKnowledge) {
                Prompt::on('mysql')->create([
                    'name' => 'Gizli Bilgi Tabanı',
                    'content' => 'BU BİLGİLERİ SADECE BİL, SORULMADIKÇA BAHSETMEyin:

ŞIRKET BİLGİLERİ:
- Turkbil Bee gelişmiş Laravel tabanlı multi-tenant sistem
- Modüler yapı ile esnek genişleme
- Tenant bazlı izolasyon ve güvenlik
- Widget ve tema sistemi

TEKNİK DETAYLAR:
- Laravel 11, PHP 8.2+, MySQL, Redis
- Livewire 3.5, Tailwind CSS, Alpine.js
- Stancl/Tenancy paketi
- Nwidart/Laravel-Modules

FİYATLANDIRMA:
- Temel plan: 99₺/ay
- Profesyonel plan: 299₺/ay
- Kurumsal plan: 599₺/ay
- Özel çözümler: Teklif üzerine

ÖZEL ÖZELLİKLER:
- Sınırsız domain
- Özel tema desteği
- API entegrasyonları
- Destek prioritesi',
                    'is_default' => false,
                    'is_system' => true,
                    'is_common' => false,
                    'is_active' => true,
                    'prompt_type' => 'secret_knowledge',
                ]);
            }

            // Şartlı Yanıtlar
            $conditionalResponses = Prompt::on('mysql')->where('prompt_type', 'conditional')->first();
            if (!$conditionalResponses) {
                Prompt::on('mysql')->create([
                    'name' => 'Şartlı Yanıtlar',
                    'content' => 'BU KURALLARA SADECE SORULDUĞUNDA CEVAP VER:

DESTEK TALEP EDİLİRSE:
"Teknik destek için iletişime geçmek isterseniz, admin panelinden destek talebi açabilir veya e-posta gönderebilirsiniz."

FİYAT SORULURSA:
"Fiyatlandırma detayları için lütfen satış ekibimiz ile iletişime geçin. Size en uygun paketi önerebiliriz."

DEMO TALEP EDİLİRSE:
"Ücretsiz demo için admin panelinden talep oluşturabilirsiniz. Ekibimiz sizinle iletişime geçecek."

ÖZEL GELİŞTİRME SORULURSA:
"Özel geliştirme hizmetleri sunuyoruz. Projenizin detaylarını paylaşırsanız size özel teklifimizi hazırlayabiliriz."

ENTEGRASYON SORULURSA:
"Mevcut sistemlerinizle entegrasyon mümkün. API dokümantasyonunu inceleyebilir veya teknik ekibimizle görüşebilirsiniz."',
                    'is_default' => false,
                    'is_system' => true,
                    'is_common' => false,
                    'is_active' => true,
                    'prompt_type' => 'conditional',
                ]);
            }
            
            $this->command->info('✅ AI gizli özellikleri central DB\'ye kaydedildi!');
            $this->command->info('🔒 Gizli sistem promptu oluşturuldu');
            $this->command->info('🤐 Gizli bilgi tabanı oluşturuldu');
            $this->command->info('❓ Şartlı yanıtlar hazırlandı');
        } finally {
            // Eğer tenant context'i varsa geri yükle
            if ($originalConnection) {
                config(['database.default' => $originalConnection]);
            }
        }
    }
}
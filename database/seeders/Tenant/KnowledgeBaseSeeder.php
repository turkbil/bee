<?php

namespace Database\Seeders\Tenant;

use Illuminate\Database\Seeder;
use Modules\AI\App\Models\KnowledgeBase;
use Illuminate\Support\Facades\Schema;

class KnowledgeBaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tablo yoksa seeder'ı atla
        if (!Schema::hasTable('tenant_knowledge_base')) {
            $this->command->warn('⚠️  tenant_knowledge_base tablosu bulunamadı, seeder atlanıyor.');
            return;
        }

        // Zaten veri varsa seeder'ı atla
        if (KnowledgeBase::count() > 0) {
            $this->command->info('✓ Knowledge Base zaten dolu, seeder atlanıyor.');
            return;
        }

        $this->command->info('📚 Örnek bilgi bankası soruları ekleniyor...');

        $knowledgeBase = [
            // Kargo Kategorisi
            [
                'category' => 'Kargo',
                'question' => 'Kargo ücreti ne kadar?',
                'answer' => '150 TL üzeri alışverişlerde kargo tamamen ücretsizdir. 150 TL altı siparişlerde kargo ücreti 29.90 TL\'dir.',
                'is_active' => true,
                'sort_order' => 10,
            ],
            [
                'category' => 'Kargo',
                'question' => 'Kargo ne zaman gelir?',
                'answer' => 'Siparişiniz onaylandıktan sonra 1-3 iş günü içinde kargoya teslim edilir. Kargo firması size teslimat bilgisi verecektir.',
                'is_active' => true,
                'sort_order' => 20,
            ],
            [
                'category' => 'Kargo',
                'question' => 'Hangi kargo firmalarıyla çalışıyorsunuz?',
                'answer' => 'Aras Kargo, Yurtiçi Kargo ve MNG Kargo ile çalışmaktayız. Siparişinizi oluştururken tercih edebilirsiniz.',
                'is_active' => true,
                'sort_order' => 30,
            ],

            // Ödeme Kategorisi
            [
                'category' => 'Ödeme',
                'question' => 'Hangi ödeme yöntemlerini kabul ediyorsunuz?',
                'answer' => 'Kredi kartı, banka kartı ve kapıda ödeme seçeneklerini kabul ediyoruz. Ayrıca havale/EFT ile de ödeme yapabilirsiniz.',
                'is_active' => true,
                'sort_order' => 40,
            ],
            [
                'category' => 'Ödeme',
                'question' => 'Taksit yapabiliyor musunuz?',
                'answer' => 'Evet, tüm kredi kartlarına 12 aya varan taksit imkanı sunuyoruz. Bazı kartlarda ek taksit fırsatları da mevcuttur.',
                'is_active' => true,
                'sort_order' => 50,
            ],
            [
                'category' => 'Ödeme',
                'question' => 'Fatura kesiliyor mu?',
                'answer' => 'Evet, tüm siparişleriniz için e-fatura kesilmektedir. Fatura bilgilerinizi sipariş sırasında girebilirsiniz.',
                'is_active' => true,
                'sort_order' => 60,
            ],

            // İade Kategorisi
            [
                'category' => 'İade',
                'question' => 'İade politikanız nedir?',
                'answer' => 'Ürünlerinizi teslim aldıktan sonra 14 gün içinde hiçbir neden göstermeden iade edebilirsiniz. Ürün kullanılmamış ve ambalajı açılmamış olmalıdır.',
                'is_active' => true,
                'sort_order' => 70,
            ],
            [
                'category' => 'İade',
                'question' => 'İade için ne yapmam gerekiyor?',
                'answer' => 'Hesabınızdan "Siparişlerim" bölümüne giderek iade talebinde bulunabilirsiniz. Kargo ücreti tarafımızdan karşılanır.',
                'is_active' => true,
                'sort_order' => 80,
            ],
            [
                'category' => 'İade',
                'question' => 'İade param ne zaman hesabıma geçer?',
                'answer' => 'İade ettiğiniz ürün depom
uza ulaştıktan sonra 3-5 iş günü içinde ödeme iadeniz gerçekleştirilir.',
                'is_active' => true,
                'sort_order' => 90,
            ],

            // Ürün Kategorisi
            [
                'category' => 'Ürün',
                'question' => 'Garanti süresi ne kadar?',
                'answer' => 'Tüm ürünlerimizde en az 2 yıl resmi garanti bulunmaktadır. Garanti belgesi ürünle birlikte gönderilir.',
                'is_active' => true,
                'sort_order' => 100,
            ],
            [
                'category' => 'Ürün',
                'question' => 'Ürünler orijinal mi?',
                'answer' => 'Evet, sitemizdeki tüm ürünler %100 orijinal ve yetkili distribütörlerden temin edilmektedir.',
                'is_active' => true,
                'sort_order' => 110,
            ],
            [
                'category' => 'Ürün',
                'question' => 'Stokta olmayan ürün ne zaman gelir?',
                'answer' => 'Stokta olmayan ürünler için "Stoğa gelince haber ver" butonuna tıklayabilirsiniz. E-posta ile bilgilendirileceksiniz.',
                'is_active' => true,
                'sort_order' => 120,
            ],

            // Genel Kategorisi
            [
                'category' => 'Genel',
                'question' => 'Çalışma saatleriniz nedir?',
                'answer' => 'Müşteri hizmetlerimiz hafta içi 09:00-18:00 arası hizmet vermektedir. Hafta sonu mesaj bırakabilirsiniz.',
                'is_active' => true,
                'sort_order' => 130,
            ],
            [
                'category' => 'Genel',
                'question' => 'Size nasıl ulaşabilirim?',
                'answer' => 'Bize canlı destek, e-posta veya telefon ile ulaşabilirsiniz. İletişim bilgilerimiz sayfanın altında yer almaktadır.',
                'is_active' => true,
                'sort_order' => 140,
            ],
            [
                'category' => 'Genel',
                'question' => 'Kampanyalardan nasıl haberdar olabilirim?',
                'answer' => 'E-bültenimize abone olarak tüm kampanya ve fırsatlardan ilk siz haberdar olabilirsiniz.',
                'is_active' => true,
                'sort_order' => 150,
            ],
        ];

        foreach ($knowledgeBase as $item) {
            KnowledgeBase::create($item);
        }

        $this->command->info('✓ ' . count($knowledgeBase) . ' adet bilgi bankası sorusu başarıyla eklendi.');
    }
}

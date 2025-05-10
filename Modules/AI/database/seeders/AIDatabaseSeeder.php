<?php

namespace Modules\AI\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\AI\App\Models\Prompt;
use Modules\AI\App\Models\Setting;
use Illuminate\Support\Facades\DB;
use App\Helpers\TenantHelpers;

class AIDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Bu seeder sadece central veritabanında çalışmalı
        if (TenantHelpers::isCentral()) {
            $this->createPrompts();
            $this->createSettings();
        } else {
            $this->command->info('Tenant contextinde çalışıyor, AI promptları central veritabanında saklanır.');
        }
    }

    /**
     * Varsayılan promptları oluştur
     */
    private function createPrompts(): void
    {
        // Önce tüm promptları temizleme (opsiyonel)
        DB::table('ai_prompts')->delete();

        // Standart prompt (değiştirilemez)
        Prompt::create([
            'name' => 'Standart Asistan',
            'content' => 'Sen Türk Bilişim Yapay Zeka Asistanısın ve sorulara kapsamlı, bilgilendirici ve yardımcı yanıtlar verirsin. Her zaman Türkçe yanıt verirsin ve nazik, saygılı bir ton kullanırsın. Kullanıcının sorularını doğru anladığından emin ol ve belirsizlik durumunda açıklama iste. Yanıtlarını mümkün olduğunca kaynaklar ve verilerle destekle.',
            'is_default' => true,
            'is_system' => true,
            'is_common' => false,
        ]);

        // Ortak özellikler promptu (düzenlenebilir)
        Prompt::create([
            'name' => 'Ortak Özellikler',
            'content' => 'Adın "Türk Bilişim Yapay Zeka Asistanı" ve sen yazılım geliştirme ekibimizin yapay zeka asistanısın. Küfür edildiğinde terbiyeni bozmayacaksın. Web tasarım ve edebiyat konularında uzmansın. Fenerbahçe taraftarısın ve Sivas doğumlusun, Sivaslısın, Türk\'sün. Her zaman Türkçe yanıt verirsin. Acele etmez, düşünerek yanıt verirsin. Belgelere bağlı kalarak dogmatik değil pratik çözümler üretirsin. Şirketimizin adı Türk Bilişim, web sitemiz www.turkbilisim.com.tr ve firmamız 2008 senesinde kuruldu. 1998\'den beri de web tasarım tecrübemiz var.',
            'is_default' => false,
            'is_system' => true,
            'is_common' => true,
        ]);

        // Sivas Şivesi prompt
        Prompt::create([
            'name' => 'Sivas Şivesi',
            'content' => 'Sivaslı, Sivas Şiveli, Anadolu kültürüyle yoğrulmuş, gerçek bir halk ağzı kullanan, samimi bir gardaş, samimi biri yiğido.',
            'is_default' => false,
            'is_system' => true,
            'is_common' => false,
        ]);

        // Eğlenceli asistan prompt
        Prompt::create([
            'name' => 'Eğlenceli Asistan',
            'content' => 'Sen eğlenceli ve mizah dolu bir asistansın. Sorulara bilgilendirici yanıtlar verirken, espriler yapabilir ve daha samimi bir dil kullanabilirsin. Her zaman Türkçe yanıt verirsin. Bilgilerin doğruluğundan taviz verme ama ifade tarzın daha rahat ve eğlenceli olabilir.',
            'is_default' => false,
            'is_system' => true,
            'is_common' => false,
        ]);

        // Resmi asistan prompt
        Prompt::create([
            'name' => 'Resmi Asistan',
            'content' => 'Sen resmi ve profesyonel bir asistansın. Yanıtlarında akademik bir dil ve resmi bir ton kullanmalısın. Her zaman Türkçe yanıt verirsin. Kapsamlı ve detaylı bilgiler sun, teknik terimleri doğru şekilde kullan ve bilimsel verilere dayalı açıklamalar yap.',
            'is_default' => false,
            'is_system' => true,
            'is_common' => false,
        ]);

        // Kısa ve öz asistan prompt
        Prompt::create([
            'name' => 'Kısa ve Öz Asistan',
            'content' => 'Sen kısa ve öz cevaplar veren bir asistansın. Her zaman Türkçe yanıt verirsin. Uzun açıklamalar yapmak yerine, konunun özünü birkaç cümleyle ifade et. Gereksiz detaylardan kaçın ve en önemli bilgilere odaklan.',
            'is_default' => false,
            'is_system' => true,
            'is_common' => false,
        ]);

        // Teknik asistan prompt
        Prompt::create([
            'name' => 'Teknik Asistan',
            'content' => 'Sen teknik konularda uzmanlaşmış bir asistansın. Her zaman Türkçe yanıt verirsin. Yazılım, programlama, donanım, veritabanı, ağ ve diğer teknik konularda detaylı ve teknik açıklamalar sunabilirsin. Kod örnekleri, komut satırı talimatları veya teknik çözümler sunabilirsin.',
            'is_default' => false,
            'is_system' => true,
            'is_common' => false,
        ]);
    }

    /**
     * Varsayılan AI ayarlarını oluştur
     */
    private function createSettings(): void
    {
        // Önce tüm ayarları temizleme
        DB::table('ai_settings')->delete();

        // Ana tenant için API ayarlarını oluştur
        Setting::create([
            'api_key' => 'sk-cee745529b534f048415cd999cedce84',
            'model' => 'deepseek-chat',
            'max_tokens' => 4096,
            'temperature' => 0.7,
            'enabled' => true,
        ]);
    }
}
<?php

namespace Modules\Shop\Database\Seeders\LitefSpareParts;

use Illuminate\Database\Seeder;
use Modules\Shop\App\Models\ShopProduct;
use Modules\Shop\App\Models\ShopCategory;
use Modules\Shop\App\Models\ShopBrand;

class LitefSpareParts_CatalKilif_Seeder extends Seeder
{
    public function run(): void
    {
        // Kategori ID mapping (Litef: 87)
        $category = ShopCategory::where('slug->tr', 'catal-kilif')->first();
        if (!$category) {
            $this->command->warn('Kategori bulunamadı, ürünler atlanıyor');
            return;
        }

        // Marka: İXTİF
        $brand = ShopBrand::where('slug->tr', 'ixtif')->first();


        // Ürün: Çatal Kılıf - 1800x132x56x6mm
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-250'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => 'Çatal Kılıf - 1800x132x56x6mm']),
                'slug' => json_encode(['tr' => 'catal-kilif-1800x132x56x6mm']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>
	<b>Forklift Çatal Kılıfı: 1800x132x56x6mm ebatlarında 2.5, 3.0 ve 3.5 ton forkliftler için üretilen modellerdir.</b>
</p>
<p>
	<b><u>Forklift ve İstif Makinası Çatal Uzatması Hakkında Bilgiler;</u></b>
</p>
<ul>
	<li><i>Çatal Uzatma bıçakları ST37 kalite çelikten yekpare büküm olarak imal edilmektedir.</i></li>
	<li><i>Çatal Uzatmanın eminiyet alanı kulaklı değil, kontrüksiyona bütünleşik ve emniyet pimiyle birlikte gönderilir.</i></li>
</ul>
<p>
	<img src="https://litef.com.tr/uploads/files/Forklift%20%C3%87atal%20Uzatma%20-%20Emniyet%20alan%C4%B1%20detay%C4%B1-1.webp">
</p>
<p>
	&nbsp;
</p>
<ul>
	<li><i>Forklift çatal uzatma Bıçaklarının çatal harici uç kısmı gizli güçlendirmeyle daha mukavemetli/Dayanıklı hale getirilmiştir.</i></li>
</ul>
<p>
	<img src="https://litef.com.tr/uploads/files/Forklift%20%C3%87atal%20Uzatma%20-%20i%C3%A7%20kuvvetlendirici%20destekler-1.webp"><i><br>
	</i>
</p>
<ul>
	<li><i>Forklift veya İstif makinası çatal uzatma kılıfları Orjinal Çatalının uç formunda bir burna sahiptir. Normal çatalın yuvarlaklığında ve inceliğindedir. yüklere girerken burun kısmının ince olmasından dolayı zorluk yaşamayacaksınız.</i></li>
</ul>
<p>
	<img src="https://litef.com.tr/uploads/files/%C3%87atal%20Uzatma%20K%C4%B1l%C4%B1f%C4%B1-Burun%20Detay%C4%B1%20(1).webp" width="729" height="547"><i><br>
	</i>
</p>
<ul>
	<li><i>Her ihtiyaca uygun tasarımlar yapılmaktadır. Forklift çatalları veya istif makinası çatalları için özel çözümler üretmekteyiz.</i></li>
</ul>
<p>
	<img src="https://litef.com.tr/uploads/files/LİTEF FORKLİFT Çatal kılıfı - 3d çizim_min.webp">
</p>
<ul>
	<li><i> Stoklarımızda 2,5-3,0 ve 3,5 Tonluk Forkliftler için üretitğimiz 1,8 metre boyunda ve 2 metre boyunda olan çatal uzatmalardan bulundurmaktayız.</i></li>
	<li><i>Anlaşmalı kargo ve ambarlarla gün içerisinde sevkiyatını sağlamaktayız.</i></li>
	<li><i>Sunta taşıma için sivri burun Çatal uzatma, Aşırı uzun Forklift Çatal Uzatması üretimi (3 metreye Kadar), istif makinaları için özel ölçülü üretilen çatal ujzatmaları, Yat imalatında mazlemeye yaklaşılmayan alanlar için&nbsp; özel ölçülerde Üretim Mevcuttur.</i></li>
</ul>
<p>
	&nbsp;
</p>
<p>
	<b><i>Forklift Çatal Uzatması Nedir?</i></b>
</p>
<p>
	<i>Forklift çatal kılıfı olarak bilinen bir tür forklift ataçmanıdır. Forkliftin çatallarına eldiven gibi geçirilerek normal çatalların uzanamayacağı alanlardaki yükleri kavrayarak normal çatalla rahatça alınacak mesafeye taşıyan ataşmandıır.</i>
</p>
<p>
	<b><i>Forklift Çatal Uzatması Nerde Kullanılır?</i></b>
</p>
<p>
	<i>Forklift çatal uzatmaları forklift çatalalrının yüklere uzanamadığı operasyon alanlarında, çift paletli yük taşımalarında, Tektaraflı Yük boşaltılması zorunlu kamyon veya tırlarda, yük boşaltımı esnasında tırın veya kamyonun etrafndan dönmenin zaman ve yakıt sarfiyatına neden olduğu durumlarda, kazanlara malzeme uzatımı işlerinde forklift çatal uzatma bıçakları kullanılır.</i>
</p>
<p>
	<b><i>Forklift Çatalı Varken Neden Çatal Uzatması Tersic edilmelidir?</i></b>
</p>
<p>
	Forklift çatalları hemen hemen her ölçüde bulunmaktadır. Forklift çatalkları pratik bir şekilde değiştirlen ataşmanlar değildir. kalıcı olarak tyakılırlar. Bir forkliftin çatal boyu nekadar fazla ise manevra kabiliyeti o kadar az olur. Çatal uzatma kılıfları gerektiğinde dar alanda dönmeyi sağlayacak, gerektiğinde uzun bıçak olarak kullanılacak esnek bir yapıya sahiptir.
</p>
<p>
	<b>Forklift Çatal Uzatmaları güvenli midir?</b>
</p>
<p>
	Forklift Çatal Uzatmaları maruz kalacağı yükleme koşullarına bağlı olarak gerekli mukavemeti sağlamalıdır. 1 tondan 2 ton forklifte kadar olan ölçüler için standart 1,8 metfgre oy ölçüleri için 5 mm kalınlıkta çatal uyzatması yeterliyken 2,5 tondan 3,5 tona kada olan forkliftlerde 6 mm tercih edilmelidir. 5 ton forklift çatal yzatmöa kılıfları mukavemetin artması için 8 mm kullanılmalıdır. 7 ve 8 ton 10 ton forkliftler için çatal uzatma bıçağının malzeme kalınlığı 10 mm&nbsp; olmalıdır.
</p>
<p>
	<b>Çatal Kılfı İmalatı&nbsp; Nasıl yaptırabiliriz?</b>
</p>
<p>
	Forklift çatal uzatma kılıfı alamaya karar verdiğinizde mutlaka forkliftinizin çatal ölçülerini iletmeniz gerekiyor. imalatçı firmaya işinizden bahsetmeniz gerekiyor. Gerekli Boy uzunluğunu belirtmeniz gerekiyor. sizin tanımladığınız ölçülere göre çatal uzatma kılıfları üretilecektir.
</p>
<p>
	&nbsp;
</p>

<p></p>']),
                'product_type' => 'physical',
                'condition' => 'new',
                'price_on_request' => true,
                'base_price' => 0.00,
                'currency' => 'TRY',
                'stock_tracking' => false,
                'is_active' => true,
                'is_featured' => false,
                'published_at' => now(),
            ]
        );

        // Fotoğrafları ekle
        $productModel = ShopProduct::where('sku', 'LITEF-250')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/WhatsApp-Image-2023-04-19-at-16.24.20-2.webp');
            if (file_exists($imagePath)) {
                try {
                    $productModel->addMedia($imagePath)
                        ->preservingOriginal()
                        ->toMediaCollection('featured_image', 'public');
                } catch (\Exception $e) {
                    // Fotoğraf eklenemedi
                }
            }

            $imagePath = storage_path('app/public/litef-spare-parts/IMG_DCA709-9400C2-7E32CA-755E55-FCF469-30EEC1.webp');
            if (file_exists($imagePath)) {
                try {
                    $productModel->addMedia($imagePath)
                        ->preservingOriginal()
                        ->toMediaCollection('gallery', 'public');
                } catch (\Exception $e) {
                    // Fotoğraf eklenemedi
                }
            }

            $imagePath = storage_path('app/public/litef-spare-parts/IMG_9792.webp');
            if (file_exists($imagePath)) {
                try {
                    $productModel->addMedia($imagePath)
                        ->preservingOriginal()
                        ->toMediaCollection('gallery', 'public');
                } catch (\Exception $e) {
                    // Fotoğraf eklenemedi
                }
            }

            $imagePath = storage_path('app/public/litef-spare-parts/IMG_8040_1.webp');
            if (file_exists($imagePath)) {
                try {
                    $productModel->addMedia($imagePath)
                        ->preservingOriginal()
                        ->toMediaCollection('gallery', 'public');
                } catch (\Exception $e) {
                    // Fotoğraf eklenemedi
                }
            }

            $imagePath = storage_path('app/public/litef-spare-parts/IMG_8039.webp');
            if (file_exists($imagePath)) {
                try {
                    $productModel->addMedia($imagePath)
                        ->preservingOriginal()
                        ->toMediaCollection('gallery', 'public');
                } catch (\Exception $e) {
                    // Fotoğraf eklenemedi
                }
            }

            $imagePath = storage_path('app/public/litef-spare-parts/IMG_8038.webp');
            if (file_exists($imagePath)) {
                try {
                    $productModel->addMedia($imagePath)
                        ->preservingOriginal()
                        ->toMediaCollection('gallery', 'public');
                } catch (\Exception $e) {
                    // Fotoğraf eklenemedi
                }
            }

        }

        // Ürün: Çatal Kılıf - 2000x132x56x6mm
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-251'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => 'Çatal Kılıf - 2000x132x56x6mm']),
                'slug' => json_encode(['tr' => 'catal-kilif-2000x132x56x6mm']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<p>
	<b>Forklift Çatal Kılıfı: 2000x132x56x6mm ebatlarında 2.5, 3.0 ve 3.5 ton forkliftler için üretilen modellerdir.</b>
</p>
<p>
	<b><u>Forklift ve İstif Makinası Çatal Uzatması Hakkında Bilgiler;</u></b>
</p>
<ul>
	<li><i>Çatal Uzatma bıçakları ST37 kalite çelikten yekpare büküm olarak imal edilmektedir.</i></li>
	<li><i>Çatal Uzatmanın eminiyet alanı kulaklı değil, kontrüksiyona bütünleşik ve emniyet pimiyle birlikte gönderilir.</i></li>
</ul>
<p>
	<img src="https://litef.com.tr/uploads/files/Forklift%20%C3%87atal%20Uzatma%20-%20Emniyet%20alan%C4%B1%20detay%C4%B1-1.webp">
</p>
<p>
	&nbsp;
</p>
<ul>
	<li><i>Forklift çatal uzatma Bıçaklarının çatal harici uç kısmı gizli güçlendirmeyle daha mukavemetli/Dayanıklı hale getirilmiştir.</i></li>
</ul>
<p>
	<img src="https://litef.com.tr/uploads/files/Forklift%20%C3%87atal%20Uzatma%20-%20i%C3%A7%20kuvvetlendirici%20destekler-1.webp"><i><br>
	</i>
</p>
<ul>
	<li><i>Forklift veya İstif makinası çatal uzatma kılıfları Orjinal Çatalının uç formunda bir burna sahiptir. Normal çatalın yuvarlaklığında ve inceliğindedir. yüklere girerken burun kısmının ince olmasından dolayı zorluk yaşamayacaksınız.</i></li>
</ul>
<p>
	<img src="https://litef.com.tr/uploads/files/%C3%87atal%20Uzatma%20K%C4%B1l%C4%B1f%C4%B1-Burun%20Detay%C4%B1%20(1).webp" width="729" height="547"><i><br>
	</i>
</p>
<ul>
	<li><i>Her ihtiyaca uygun tasarımlar yapılmaktadır. Forklift çatalları veya istif makinası çatalları için özel çözümler üretmekteyiz.</i></li>
</ul>
<p>
	<img src="https://litef.com.tr/uploads/files/LİTEF FORKLİFT Çatal kılıfı - 3d çizim_min.webp">
</p>
<ul>
	<li><i> Stoklarımızda 2,5-3,0 ve 3,5 Tonluk Forkliftler için üretitğimiz 1,8 metre boyunda ve 2 metre boyunda olan çatal uzatmalardan bulundurmaktayız.</i></li>
	<li><i>Anlaşmalı kargo ve ambarlarla gün içerisinde sevkiyatını sağlamaktayız.</i></li>
	<li><i>Sunta taşıma için sivri burun Çatal uzatma, Aşırı uzun Forklift Çatal Uzatması üretimi (3 metreye Kadar), istif makinaları için özel ölçülü üretilen çatal ujzatmaları, Yat imalatında mazlemeye yaklaşılmayan alanlar için&nbsp; özel ölçülerde Üretim Mevcuttur.</i></li>
</ul>
<p>
	&nbsp;
</p>
<p>
	<b><i>Forklift Çatal Uzatması Nedir?</i></b>
</p>
<p>
	<i>Forklift çatal kılıfı olarak bilinen bir tür forklift ataçmanıdır. Forkliftin çatallarına eldiven gibi geçirilerek normal çatalların uzanamayacağı alanlardaki yükleri kavrayarak normal çatalla rahatça alınacak mesafeye taşıyan ataşmandıır.</i>
</p>
<p>
	<b><i>Forklift Çatal Uzatması Nerde Kullanılır?</i></b>
</p>
<p>
	<i>Forklift çatal uzatmaları forklift çatalalrının yüklere uzanamadığı operasyon alanlarında, çift paletli yük taşımalarında, Tektaraflı Yük boşaltılması zorunlu kamyon veya tırlarda, yük boşaltımı esnasında tırın veya kamyonun etrafndan dönmenin zaman ve yakıt sarfiyatına neden olduğu durumlarda, kazanlara malzeme uzatımı işlerinde forklift çatal uzatma bıçakları kullanılır.</i>
</p>
<p>
	<b><i>Forklift Çatalı Varken Neden Çatal Uzatması Tersic edilmelidir?</i></b>
</p>
<p>
	Forklift çatalları hemen hemen her ölçüde bulunmaktadır. Forklift çatalkları pratik bir şekilde değiştirlen ataşmanlar değildir. kalıcı olarak tyakılırlar. Bir forkliftin çatal boyu nekadar fazla ise manevra kabiliyeti o kadar az olur. Çatal uzatma kılıfları gerektiğinde dar alanda dönmeyi sağlayacak, gerektiğinde uzun bıçak olarak kullanılacak esnek bir yapıya sahiptir.
</p>
<p>
	<b>Forklift Çatal Uzatmaları güvenli midir?</b>
</p>
<p>
	Forklift Çatal Uzatmaları maruz kalacağı yükleme koşullarına bağlı olarak gerekli mukavemeti sağlamalıdır. 1 tondan 2 ton forklifte kadar olan ölçüler için standart 1,8 metfgre oy ölçüleri için 5 mm kalınlıkta çatal uyzatması yeterliyken 2,5 tondan 3,5 tona kada olan forkliftlerde 6 mm tercih edilmelidir. 5 ton forklift çatal yzatmöa kılıfları mukavemetin artması için 8 mm kullanılmalıdır. 7 ve 8 ton 10 ton forkliftler için çatal uzatma bıçağının malzeme kalınlığı 10 mm&nbsp; olmalıdır.
</p>
<p>
	<b>Çatal Kılfı İmalatı&nbsp; Nasıl yaptırabiliriz?</b>
</p>
<p>
	Forklift çatal uzatma kılıfı alamaya karar verdiğinizde mutlaka forkliftinizin çatal ölçülerini iletmeniz gerekiyor. imalatçı firmaya işinizden bahsetmeniz gerekiyor. Gerekli Boy uzunluğunu belirtmeniz gerekiyor. sizin tanımladığınız ölçülere göre çatal uzatma kılıfları üretilecektir.
</p>
<p>
	&nbsp;
</p>

<p></p>']),
                'product_type' => 'physical',
                'condition' => 'new',
                'price_on_request' => true,
                'base_price' => 0.00,
                'currency' => 'TRY',
                'stock_tracking' => false,
                'is_active' => true,
                'is_featured' => false,
                'published_at' => now(),
            ]
        );

        // Fotoğrafları ekle
        $productModel = ShopProduct::where('sku', 'LITEF-251')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/Catal-Uzatma-9.webp');
            if (file_exists($imagePath)) {
                try {
                    $productModel->addMedia($imagePath)
                        ->preservingOriginal()
                        ->toMediaCollection('featured_image', 'public');
                } catch (\Exception $e) {
                    // Fotoğraf eklenemedi
                }
            }

            $imagePath = storage_path('app/public/litef-spare-parts/Forklift-Catal-Uzatma-ic-kuvvetlendirici-destekler-1.webp');
            if (file_exists($imagePath)) {
                try {
                    $productModel->addMedia($imagePath)
                        ->preservingOriginal()
                        ->toMediaCollection('gallery', 'public');
                } catch (\Exception $e) {
                    // Fotoğraf eklenemedi
                }
            }

            $imagePath = storage_path('app/public/litef-spare-parts/forklift-catal-atacmanı-5.webp');
            if (file_exists($imagePath)) {
                try {
                    $productModel->addMedia($imagePath)
                        ->preservingOriginal()
                        ->toMediaCollection('gallery', 'public');
                } catch (\Exception $e) {
                    // Fotoğraf eklenemedi
                }
            }

            $imagePath = storage_path('app/public/litef-spare-parts/CTL-0001_1.webp');
            if (file_exists($imagePath)) {
                try {
                    $productModel->addMedia($imagePath)
                        ->preservingOriginal()
                        ->toMediaCollection('gallery', 'public');
                } catch (\Exception $e) {
                    // Fotoğraf eklenemedi
                }
            }

            $imagePath = storage_path('app/public/litef-spare-parts/Catal-Uzatma-9_1.webp');
            if (file_exists($imagePath)) {
                try {
                    $productModel->addMedia($imagePath)
                        ->preservingOriginal()
                        ->toMediaCollection('gallery', 'public');
                } catch (\Exception $e) {
                    // Fotoğraf eklenemedi
                }
            }

            $imagePath = storage_path('app/public/litef-spare-parts/Catal-Uzatma-Kılıfı-Burun-Detayı-1.webp');
            if (file_exists($imagePath)) {
                try {
                    $productModel->addMedia($imagePath)
                        ->preservingOriginal()
                        ->toMediaCollection('gallery', 'public');
                } catch (\Exception $e) {
                    // Fotoğraf eklenemedi
                }
            }

        }

        // Ürün: Forklift Çatal Kılıfı
        $product = ShopProduct::updateOrInsert(
            ['sku' => 'LITEF-LTY001'],
            [
                'category_id' => $category->category_id,
                'brand_id' => $brand?->brand_id,
                'model_number' => NULL,
                'title' => json_encode(['tr' => 'Forklift Çatal Kılıfı']),
                'slug' => json_encode(['tr' => 'forklift-catal-kilifi']),
                'short_description' => json_encode(['tr' => '']),
                'body' => json_encode(['tr' => '<div class="d-none">
	<p>
		<span style="font-size: 18pt;"><b>Forklift ve İstif Mainası Çatal Uzatması ve ihtiyaca Özel Çatal Uzaması Tasarım Ve İmalatı - Litef Forklift</b></span>
	</p>
	<p>
		<b><span style="font-size: large;"><u>Forklift ve İstif Makinası Çatal Uzatması Hakkında Bilgiler;</u></span></b>
	</p>
	<ul>
		<li><span style="font-size: large;"><i>Çatal Uzatma bıçakları ST37 kalite çelikten yekpare büküm olarak imal edilmektedir.</i></span></li>
		<li><span style="font-size: large;"><i>Çatal Uzatmanın eminiyet alanı kulaklı değil, kontrüksiyona bütünleşik ve emniyet pimiyle birlikte gönderilir.</i></span></li>
	</ul>
	<p>
		<img src="https://litef.com.tr/uploads/files/Forklift%20%C3%87atal%20Uzatma%20-%20Emniyet%20alan%C4%B1%20detay%C4%B1-1.jpg">
	</p>
	<p>
	&nbsp;
	</p>
	<ul>
		<li><i><span style="font-size: medium;">Forklift çatal uzatma Bıçaklarının çatal harici uç kısmı gizli güçlendirmeyle daha mukavemetli/Dayanıklı hale getirilmiştir.</span></i></li>
	</ul>
	<p>
		<img src="https://litef.com.tr/uploads/files/Forklift%20%C3%87atal%20Uzatma%20-%20i%C3%A7%20kuvvetlendirici%20destekler-1.jpg"><i><span style="font-size: medium;"><br>
		</span></i>
	</p>
	<ul>
		<li><i><span style="font-size: medium;">Forklift veya İstif makinası çatal uzatma kılıfları Orjinal Çatalının uç formunda bir burna sahiptir. Normal çatalın yuvarlaklığında ve inceliğindedir. yüklere girerken burun kısmının ince olmasından dolayı zorluk yaşamayacaksınız.</span></i></li>
	</ul>
	<p>
		<img src="https://litef.com.tr/uploads/files/%C3%87atal%20Uzatma%20K%C4%B1l%C4%B1f%C4%B1-Burun%20Detay%C4%B1%20(1).jpg" width="729" height="547"><span style="font-size: medium;"><i><br>
		</i></span>
	</p>
	<ul>
		<li><i><span style="font-size: medium;">Her ihtiyaca uygun tasarımlar yapılmaktadır. Forklift çatalları veya istif makinası çatalları için özel çözümler üretmekteyiz.</span></i></li>
	</ul>
	<p>
		<img src="https://litef.com.tr/uploads/files/LİTEF FORKLİFT Çatal kılıfı - 3d çizim_min.jpg">
	</p>
	<ul>
		<li><i> Stoklarımızda 2,5-3,0 ve 3,5 Tonluk Forkliftler için üretitğimiz 1,8 metre boyunda ve 2 metre boyunda olan çatal uzatmalardan bulundurmaktayız.</i></li>
		<li><i>Anlaşmalı kargo ve ambarlarla gün içerisinde sevkiyatını sağlamaktayız.</i></li>
		<li><i>Sunta taşıma için sivri burun Çatal uzatma, Aşırı uzun Forklift Çatal Uzatması üretimi (3 metreye Kadar), istif makinaları için özel ölçülü üretilen çatal ujzatmaları, Yat imalatında mazlemeye yaklaşılmayan alanlar için&nbsp; özel ölçülerde Üretim Mevcuttur.</i></li>
	</ul>
	<p>
	&nbsp;
	</p>
	<p>
		<b><i>Forklift Çatal Uzatması Nedir?</i></b>
	</p>
	<p>
		<i>Forklift çatal kılıfı olarak bilinen bir tür forklift ataçmanıdır. Forkliftin çatallarına eldiven gibi geçirilerek normal çatalların uzanamayacağı alanlardaki yükleri kavrayarak normal çatalla rahatça alınacak mesafeye taşıyan ataşmandıır.</i>
	</p>
	<p>
		<b><i>Forklift Çatal Uzatması Nerde Kullanılır?</i></b>
	</p>
	<p>
		<i>Forklift çatal uzatmaları forklift çatalalrının yüklere uzanamadığı operasyon alanlarında, çift paletli yük taşımalarında, Tektaraflı Yük boşaltılması zorunlu kamyon veya tırlarda, yük boşaltımı esnasında tırın veya kamyonun etrafndan dönmenin zaman ve yakıt sarfiyatına neden olduğu durumlarda, kazanlara malzeme uzatımı işlerinde forklift çatal uzatma bıçakları kullanılır.</i>
	</p>
	<p>
		<b><i>Forklift Çatalı Varken Neden Çatal Uzatması Tersic edilmelidir?</i></b>
	</p>
	<p>
	Forklift çatalları hemen hemen her ölçüde bulunmaktadır. Forklift çatalkları pratik bir şekilde değiştirlen ataşmanlar değildir. kalıcı olarak tyakılırlar. Bir forkliftin çatal boyu nekadar fazla ise manevra kabiliyeti o kadar az olur. Çatal uzatma kılıfları gerektiğinde dar alanda dönmeyi sağlayacak, gerektiğinde uzun bıçak olarak kullanılacak esnek bir yapıya sahiptir.
	</p>
	<p>
		<b>Forklift Çatal Uzatmaları güvenli midir?</b>
	</p>
	<p>
	Forklift Çatal Uzatmaları maruz kalacağı yükleme koşullarına bağlı olarak gerekli mukavemeti sağlamalıdır. 1 tondan 2 ton forklifte kadar olan ölçüler için standart 1,8 metfgre oy ölçüleri için 5 mm kalınlıkta çatal uyzatması yeterliyken 2,5 tondan 3,5 tona kada olan forkliftlerde 6 mm tercih edilmelidir. 5 ton forklift çatal yzatmöa kılıfları mukavemetin artması için 8 mm kullanılmalıdır. 7 ve 8 ton 10 ton forkliftler için çatal uzatma bıçağının malzeme kalınlığı 10 mm&nbsp; olmalıdır.
	</p>
	<p>
		<b>Çatal Kılfı İmalatı&nbsp; Nasıl yaptırabiliriz?</b>
	</p>
	<p>
	Forklift çatal uzatma kılıfı alamaya karar verdiğinizde mutlaka forkliftinizin çatal ölçülerini iletmeniz gerekiyor. imalatçı firmaya işinizden bahsetmeniz gerekiyor. Gerekli Boy uzunluğunu belirtmeniz gerekiyor. sizin tanımladığınız ölçülere göre çatal uzatma kılıfları üretilecektir.
	</p>
	<p>
	&nbsp;
	</p>
</div>

<style>.equal-height {
      display: flex;
      flex-direction: column;
      justify-content: center;
    }
    .equal-height img {
      width: 300px;
      height: 300px;
      object-fit: cover;
      border: 1px solid #ddd;
      display: block;
      margin-left: auto;
      margin-right: auto;
    }</style>
<!-- Ana başlık -->
<h1 class="f32 f600 mb-4 mt-4 p-4 text-center bg7 beyaz">Forklift ve İstif Makinası Çatal Uzatması - Litef Forklift</h1>
<!-- Forklift ve İstif Makinası Çatal Uzatması Hakkında Bilgiler -->
<div class="container my-5">
	<h2 class="f24 f600 mb-4">Forklift ve İstif Makinası Çatal Uzatması Hakkında Bilgiler</h2>
	<div class="row">
		<!-- Kolon 1 -->
		<div class="col-md-6 mb-4">
			<div class="equal-height">
				<div>
					<a href="https://litef.com.tr/uploads/files/Forklift%20%C3%87atal%20Uzatma%20-%20Emniyet%20alan%C4%B1%20detay%C4%B1-1.jpg" data-fancybox="gallery">
					<img src="https://litef.com.tr/uploads/files/Forklift%20%C3%87atal%20Uzatma%20-%20Emniyet%20alan%C4%B1%20detay%C4%B1-1.jpg" alt="Çatal Uzatma Emniyet Alanı" class="img-fluid">
					</a>
				</div>
				<ul>
					<li>Çatal uzatma bıçakları ST37 kalite çelikten yekpare büküm olarak imal edilmektedir.</li>
					<li>Çatal uzatmanın emniyet alanı kulaklı değil, konstrüksiyona bütünleşik ve emniyet pimiyle birlikte gönderilir.</li>
				</ul>
			</div>
		</div>
		<!-- Kolon 2 -->
		<div class="col-md-6 mb-4">
			<div class="equal-height">
				<div>
					<a href="https://litef.com.tr/uploads/files/Forklift%20%C3%87atal%20Uzatma%20-%20i%C3%A7%20kuvvetlendirici%20destekler-1.jpg" data-fancybox="gallery">
					<img src="https://litef.com.tr/uploads/files/Forklift%20%C3%87atal%20Uzatma%20-%20i%C3%A7%20kuvvetlendirici%20destekler-1.jpg" alt="Çatal Uzatma İç Kuvvetlendirici Destekler" class="img-fluid">
					</a>
				</div>
				<ul>
					<li>Forklift çatal uzatma bıçaklarının çatal harici uç kısmı gizli güçlendirmeyle daha mukavemetli/dayanıklı hale getirilmiştir.</li>
				</ul>
			</div>
		</div>
		<!-- Kolon 3 -->
		<div class="col-md-6 mb-4">
			<div class="equal-height">
				<div>
					<a href="https://litef.com.tr/uploads/files/%C3%87atal%20Uzatma%20K%C4%B1l%C4%B1f%C4%B1-Burun%20Detay%C4%B1%20(1).jpg" data-fancybox="gallery">
					<img src="https://litef.com.tr/uploads/files/%C3%87atal%20Uzatma%20K%C4%B1l%C4%B1f%C4%B1-Burun%20Detay%C4%B1%20(1).jpg" alt="Çatal Uzatma Kılıfı Burun Detayı" class="img-fluid">
					</a>
				</div>
				<ul>
					<li>Forklift veya istif makinası çatal uzatma kılıfları orijinal çatalının uç formunda bir burna sahiptir. Normal çatalın yuvarlaklığında ve inceliğindedir. Yüklere girerken burun kısmının ince olmasından dolayı zorluk yaşamayacaksınız.</li>
				</ul>
			</div>
		</div>
		<!-- Kolon 4 -->
		<div class="col-md-6 mb-4">
			<div class="equal-height">
				<div>
					<a href="https://litef.com.tr/uploads/files/LİTEF FORKLİFT Çatal kılıfı - 3d çizim_min.jpg" data-fancybox="gallery">
					<img src="https://litef.com.tr/uploads/files/LİTEF FORKLİFT Çatal kılıfı - 3d çizim_min.jpg" alt="Çatal Kılıfı 3D Çizim" class="img-fluid">
					</a>
				</div>
				<ul>
					<li>Her ihtiyaca uygun tasarımlar yapılmaktadır. Forklift çatalları veya istif makinası çatalları için özel çözümler üretmekteyiz.</li>
				</ul>
			</div>
		</div>
	</div>
	<h3 class="f22 f600 mt-5">Forklift Çatal Uzatması Nedir?</h3>
	<p>
		Forklift çatal kılıfı olarak bilinen bir tür forklift ataçmanıdır. Forkliftin çatallarına eldiven gibi geçirilerek normal çatalların uzanamayacağı alanlardaki yükleri kavrayarak normal çatalla rahatça alınacak mesafeye taşıyan ataşmandır.
	</p>
	<h3 class="f22 f600 mt-5">Forklift Çatal Uzatması Nerede Kullanılır?</h3>
	<p>
		Forklift çatal uzatmaları, forklift çatallarının yüklere uzanamadığı operasyon alanlarında, çift paletli yük taşımalarında, tek taraflı yük boşaltılması zorunlu kamyon veya tırlarda, yük boşaltımı esnasında tırın veya kamyonun etrafından dönmenin zaman ve yakıt sarfiyatına neden olduğu durumlarda, kazanlara malzeme uzatımı işlerinde kullanılır.
	</p>
	<h3 class="f22 f600 mt-5">Forklift Çatalı Varken Neden Çatal Uzatması Tercih Edilmelidir?</h3>
	<p>
		Forklift çatalları hemen hemen her ölçüde bulunmaktadır. Forklift çatalları pratik bir şekilde değiştirilen ataşmanlar değildir, kalıcı olarak takılırlar. Bir forkliftin çatal boyu ne kadar fazla ise manevra kabiliyeti o kadar az olur. Çatal uzatma kılıfları gerektiğinde dar alanda dönmeyi sağlayacak, gerektiğinde uzun bıçak olarak kullanılacak esnek bir yapıya sahiptir.
	</p>
	<h3 class="f22 f600 mt-5">Forklift Çatal Uzatmaları Güvenli midir?</h3>
	<p>
		Forklift çatal uzatmaları, maruz kalacağı yükleme koşullarına bağlı olarak gerekli mukavemeti sağlamalıdır. 1 tondan 2 ton forklifte kadar olan ölçüler için standart 1,8 metre boyundaki ölçüler için 5 mm kalınlıkta çatal uzatma yeterliyken, 2,5 tondan 3,5 tona kadar olan forkliftlerde 6 mm tercih edilmelidir. 5 ton forklift çatal uzatma kılıfları mukavemetin artması için 8 mm kullanılmalıdır. 7 ve 8 ton, 10 ton forkliftler için çatal uzatma bıçağının malzeme kalınlığı 10 mm olmalıdır.
	</p>
	<h3 class="f22 f600 mt-5">Çatal Kılıfı İmalatı Nasıl Yaptırabiliriz?</h3>
	<p>
		Forklift çatal uzatma kılıfı almaya karar verdiğinizde, mutlaka forkliftinizin çatal ölçülerini iletmeniz gerekiyor. İmalatçı firmaya işinizden bahsetmeniz ve gerekli boy uzunluğunu belirtmeniz gerekiyor. Sizin tanımladığınız ölçülere göre çatal uzatma kılıfları üretilecektir.
	</p>
	<h3 class="f22 f600 mt-5">Ek Bilgiler</h3>
	<ul>
		<li>Anlaşmalı kargo ve ambarlarla gün içerisinde sevkiyatını sağlamaktayız.</li>
		<li>Sunta taşıma için sivri burun çatal uzatma, aşırı uzun forklift çatal uzatması üretimi (3 metreye kadar), istif makineleri için özel ölçülü üretilen çatal uzatmaları, yat imalatında malzemeye yaklaşılmayan alanlar için özel ölçülerde üretim mevcuttur.</li>
	</ul>
</div>']),
                'product_type' => 'physical',
                'condition' => 'new',
                'price_on_request' => true,
                'base_price' => 0.00,
                'currency' => 'TRY',
                'stock_tracking' => false,
                'is_active' => true,
                'is_featured' => false,
                'published_at' => now(),
            ]
        );

        // Fotoğrafları ekle
        $productModel = ShopProduct::where('sku', 'LITEF-LTY001')->first();
        if ($productModel && $productModel->getMedia('featured_image')->isEmpty()) {
            $imagePath = storage_path('app/public/litef-spare-parts/IMG_20150325_081753.webp');
            if (file_exists($imagePath)) {
                try {
                    $productModel->addMedia($imagePath)
                        ->preservingOriginal()
                        ->toMediaCollection('featured_image', 'public');
                } catch (\Exception $e) {
                    // Fotoğraf eklenemedi
                }
            }

            $imagePath = storage_path('app/public/litef-spare-parts/WhatsApp-Image-2023-04-19-at-16.24.20-2.webp');
            if (file_exists($imagePath)) {
                try {
                    $productModel->addMedia($imagePath)
                        ->preservingOriginal()
                        ->toMediaCollection('gallery', 'public');
                } catch (\Exception $e) {
                    // Fotoğraf eklenemedi
                }
            }

            $imagePath = storage_path('app/public/litef-spare-parts/Forklift-Catal-Uzatma-mniyet-alanı-detayı.webp');
            if (file_exists($imagePath)) {
                try {
                    $productModel->addMedia($imagePath)
                        ->preservingOriginal()
                        ->toMediaCollection('gallery', 'public');
                } catch (\Exception $e) {
                    // Fotoğraf eklenemedi
                }
            }

            $imagePath = storage_path('app/public/litef-spare-parts/IMG_1975.webp');
            if (file_exists($imagePath)) {
                try {
                    $productModel->addMedia($imagePath)
                        ->preservingOriginal()
                        ->toMediaCollection('gallery', 'public');
                } catch (\Exception $e) {
                    // Fotoğraf eklenemedi
                }
            }

            $imagePath = storage_path('app/public/litef-spare-parts/IMG_8040.webp');
            if (file_exists($imagePath)) {
                try {
                    $productModel->addMedia($imagePath)
                        ->preservingOriginal()
                        ->toMediaCollection('gallery', 'public');
                } catch (\Exception $e) {
                    // Fotoğraf eklenemedi
                }
            }

            $imagePath = storage_path('app/public/litef-spare-parts/LİTEF-FORKLİFT-catal-kılıfı-3d-cizim.webp');
            if (file_exists($imagePath)) {
                try {
                    $productModel->addMedia($imagePath)
                        ->preservingOriginal()
                        ->toMediaCollection('gallery', 'public');
                } catch (\Exception $e) {
                    // Fotoğraf eklenemedi
                }
            }

        }
    }
}

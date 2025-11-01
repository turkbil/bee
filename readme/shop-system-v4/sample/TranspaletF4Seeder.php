<?php
namespace Modules\Shop\Database\Seeders\V4;
use Illuminate\Database\Seeder;
use Modules\Shop\app\Models\ShopProduct;
use Modules\SeoManagement\app\Models\SeoSetting;

class TranspaletF4Seeder extends Seeder
{
    public function run(): void
    {
        $product = ShopProduct::updateOrCreate(
            ['sku' => 'IXTIF-F4-1500'],
            [
                'sku' => 'IXTIF-F4-1500',
                'title' => json_encode(['tr'=>'iXtif F4 Transpalet', 'en'=>'iXtif F4 Pallet Truck'], JSON_UNESCAPED_UNICODE),
                'slug' => json_encode(['tr'=>'ixtif-f4-transpalet', 'en'=>'ixtif-f4-pallet-truck'], JSON_UNESCAPED_UNICODE),
                'one_line_description' => json_encode([
                    'tr'=>'1500 kg kapasiteli, 24V 20Ah Li‑Ion batarya ve 1360 mm dönüş yarıçapıyla kompakt, verimli ve opsiyonel denge tekerlekli elektrikli transpalet.',
                    'en'=>'1500 kg capacity, 24V 20Ah Li‑Ion battery, 1360 mm turning radius, compact and efficient electric pallet truck with optional stabilizing wheels.'
                ], JSON_UNESCAPED_UNICODE),
                'short_description' => json_encode([
                    'tr'=>'Kompakt, hafif, Li‑Ion güçlü transpalet.',
                    'en'=>'Compact, lightweight, Li‑Ion powered pallet truck.'
                ], JSON_UNESCAPED_UNICODE),
                'body' => json_encode([
                    'tr'=> '<section><h2>Hook</h2><p>Depoda hız ve çeviklik arıyorsanız, iXtif F4 Transpalet 1500 kg’a kadar yükleri minimum eforla taşır. 400 mm ön gövde uzunluğu ve 1360 mm dönüş yarıçapı ile dar koridorlarda rahat manevra sağlar.</p></section>'.
                           '<section><h2>Tanıtım</h2><p>iXtif F4, günlük operasyonlardan yoğun vardiyalara kadar farklı uygulamalara uyarlanabilen elektrikli bir transpalettir. 24V 20Ah çıkarılabilir Li‑Ion batarya yapılandırmasıyla gelir; ikinci bir batarya yuvası sayesinde çalışma süresi artırılabilir. Standart tek batarya yapılandırmasında taşınabilir saklama bölmesi sunulur. Opsiyonel denge tekerlekleri, düzensiz zeminlerde büyük yükleri daha kontrollü taşımaya yardımcı olur.</p></section>'.
                           '<section><h2>Teknik</h2><ul>'.
                           '<li>Kapasite: 1500 kg</li>'.
                           '<li>Yük merkezi: 600 mm</li>'.
                           '<li>Servis ağırlığı: 120 kg</li>'.
                           '<li>Ön gövde uzunluğu (l2): 400 mm</li>'.
                           '<li>Toplam genişlik: 590 / 695 mm</li>'.
                           '<li>Çatal ölçüleri: 55 × 150 × 1150 mm</li>'.
                           '<li>Dönüş yarıçapı: 1360 mm</li>'.
                           '<li>Hız (yük/boş): 4.0 / 4.5 km/sa</li>'.
                           '<li>Kaldırma hızı (yük/boş): 0.017 / 0.020 m/sn</li>'.
                           '<li>İndirme hızı (yük/boş): 0.058 / 0.046 m/sn</li>'.
                           '<li>Azami eğim (yük/boş): %6 / %16</li>'.
                           '<li>Batarya: 24V / 20Ah Li‑Ion, çıkarılabilir</li>'.
                           '<li>Şarj seçenekleri: 24V-5A harici, 24V-10A harici, 24V-4A harici DC-DC</li>'.
                           '</ul></section>'.
                           '<section><h2>Senaryo</h2><p>Bir gıda dağıtım deposunda dar koridorlar ve sık kapı geçişleri bulunuyor. F4’ün 1360 mm dönüş yarıçapı ve 400 mm ön gövde uzunluğu, paletleri raflar arasında hızlıca döndürmeyi sağlar. Vardiya boyunca tek bataryayla toplama yapılır, yoğun saatlerde ikinci 24V 20Ah batarya takılarak kesintisiz akış korunur. Düzensiz zeminlerde opsiyonel denge tekerlekleri titreşimi düşürerek yük stabilitesini artırır.</p></section>'.
                           '<section><h2>Özet</h2><p>iXtif F4, kompakt boyut, düşük efor ve esnek güç seçenekleriyle depo içi taşımalarda güvenilir bir çözümdür. 1500 kg kapasite, Li‑Ion teknoloji ve opsiyonel denge tekerlekleri sayesinde tek cihazla farklı görevleri yönetebilirsiniz.</p></section>',
                    'en'=> '<section><h2>Hook</h2><p>If you need speed and agility in the warehouse, the iXtif F4 Pallet Truck moves up to 1,500 kg with minimal effort. With a 400 mm head length and a 1,360 mm turning radius, it maneuvers confidently in narrow aisles.</p></section>'.
                           '<section><h2>Introduction</h2><p>The iXtif F4 is an electric pallet truck adaptable from daily tasks to intensive shifts. It comes with a removable 24V 20Ah Li‑Ion battery and offers a second power slot to extend uptime. In the standard single‑battery setup, a portable storage compartment is provided. Optional stabilizing wheels help manage heavier loads over uneven floors.</p></section>'.
                           '<section><h2>Technical</h2><ul>'.
                           '<li>Capacity: 1500 kg</li>'.
                           '<li>Load center: 600 mm</li>'.
                           '<li>Service weight: 120 kg</li>'.
                           '<li>Length to face of forks (l2): 400 mm</li>'.
                           '<li>Overall width: 590 / 695 mm</li>'.
                           '<li>Fork dimensions: 55 × 150 × 1150 mm</li>'.
                           '<li>Turning radius: 1360 mm</li>'.
                           '<li>Travel speed (laden/unladen): 4.0 / 4.5 km/h</li>'.
                           '<li>Lifting speed (laden/unladen): 0.017 / 0.020 m/s</li>'.
                           '<li>Lowering speed (laden/unladen): 0.058 / 0.046 m/s</li>'.
                           '<li>Max gradeability (laden/unladen): 6% / 16%</li>'.
                           '<li>Battery: 24V / 20Ah Li‑Ion, removable</li>'.
                           '<li>Charger options: 24V‑5A external, 24V‑10A external, 24V‑4A external DC‑DC</li>'.
                           '</ul></section>'.
                           '<section><h2>Scenario</h2><p>In a food distribution warehouse with tight aisles and frequent doorways, the F4’s 1,360 mm turning radius and 400 mm head length let operators rotate pallets quickly between racks. A single battery supports picking through most of the day, and a second 24V 20Ah battery can be inserted during peaks to keep the flow continuous. Optional stabilizing wheels help keep loads steady over rough spots.</p></section>'.
                           '<section><h2>Summary</h2><p>iXtif F4 combines compact size, low effort, and flexible power options for reliable in‑warehouse handling. With 1500 kg capacity, Li‑Ion technology, and optional stabilizing wheels, one truck covers diverse tasks.</p></section>'
                ], JSON_UNESCAPED_UNICODE),
                'category_id' => 2,
                'brand_id' => 1,
                'is_active' => true,
                'base_price' => 0.00,
                'price_on_request' => true,

                'primary_specs' => json_encode([
                    'capacity' => '1500 kg',
                    'stabilizing_wheel' => 'Opsiyonel',
                    'battery' => '24V 20Ah Li-Ion',
                    'charger' => 'Harici 24V-5A / 10A / 4A DC-DC',
                    'turning_radius' => '1360 mm'
                ], JSON_UNESCAPED_UNICODE),

                'content_variations' => json_encode([
                    'li-ion-battery' => [
                        'technical' => ['tr'=>'24V 20Ah çıkarılabilir Li‑Ion batarya ile hızlı şarj ve uzun çevrim.', 'en'=>'Removable 24V 20Ah Li‑Ion battery enables fast charging and long cycle life.'],
                        'benefit' => ['tr'=>'Vardiya süresini uzatır ve bekleme sürelerini azaltır.', 'en'=>'Extends shift time and reduces downtime.'],
                        'slogan' => ['tr'=>'Li‑Ion güç.', 'en'=>'Powered by Li‑Ion.'],
                        'motto' => ['tr'=>'Hafif güç', 'en'=>'Light power'],
                        'short' => ['tr'=>'Taşınabilir Li‑Ion batarya ile kesintisiz operasyon.', 'en'=>'Portable Li‑Ion battery for continuous operation.'],
                        'long' => ['tr'=>'24V 20Ah Li‑Ion modül kolayca çıkarılır ve değiştirilebilir, iş akışını kesmeden vardiya değişimine olanak tanır.', 'en'=>'The 24V 20Ah Li‑Ion module is easily removable and swappable to keep workflows running without interruption.'],
                        'comparison' => ['tr'=>'Kurşun asit muadillerine göre daha hafif ve hızlı şarj olur.', 'en'=>'Lighter and faster‑charging than comparable lead‑acid units.'],
                        'keywords' => ['tr'=>'li‑ion, batarya, hızlı şarj', 'en'=>'li‑ion, battery, fast charge']
                    ],
                    'dual-battery-slot' => [
                        'technical' => ['tr'=>'İki güç yuvası ikinci bataryayı destekler.', 'en'=>'Two power slots support a second battery.'],
                        'benefit' => ['tr'=>'Uptime artar, yoğun saatlerde kesinti azalır.', 'en'=>'Increases uptime during peak hours.'],
                        'slogan' => ['tr'=>'Çift güç.', 'en'=>'Dual power.'],
                        'motto' => ['tr'=>'Hazır kal', 'en'=>'Stay ready'],
                        'short' => ['tr'=>'İkinci batarya ile süreyi uzatın.', 'en'=>'Add a second battery to extend runtime.'],
                        'long' => ['tr'=>'Standart tek batarya ile başlar, ikinci 24V 20Ah modülle vardiya boyunca performansını korur.', 'en'=>'Starts single‑battery and scales with a second 24V 20Ah module for full‑shift performance.'],
                        'comparison' => ['tr'=>'Tek yuvalı sistemlere kıyasla daha uzun kesintisiz çalışma sağlar.', 'en'=>'Delivers longer uninterrupted work than single‑slot systems.'],
                        'keywords' => ['tr'=>'ikili batarya, uptime', 'en'=>'dual battery, uptime']
                    ],
                    'stabilizing-wheels' => [
                        'technical' => ['tr'=>'Opsiyonel denge tekerlekleri düzensiz zeminde ek stabilite sağlar.', 'en'=>'Optional stabilizing wheels add stability on uneven floors.'],
                        'benefit' => ['tr'=>'Yük devrilme riskini azaltır.', 'en'=>'Reduces risk of load tipping.'],
                        'slogan' => ['tr'=>'Düzgün taşı.', 'en'=>'Carry steady.'],
                        'motto' => ['tr'=>'Daha dengeli', 'en'=>'More stable'],
                        'short' => ['tr'=>'Zor zeminlerde kontrollü ilerleyin.', 'en'=>'Stay in control on rough floors.'],
                        'long' => ['tr'=>'Denge tekerlekleri büyük yüklerde yön kararlılığını artırır ve zemin dalgalanmalarında titreşimi sönümler.', 'en'=>'Stabilizers improve directional control with big loads and damp floor irregularities.'],
                        'comparison' => ['tr'=>'Standart konfigürasyona göre virajda daha az salınım.', 'en'=>'Less sway in turns than standard setup.'],
                        'keywords' => ['tr'=>'denge tekeri, stabilite', 'en'=>'stabilizer, stability']
                    ],
                    'compact-size' => [
                        'technical' => ['tr'=>'l2=400 mm ve Wa=1360 mm ile dar alan uyumu.', 'en'=>'l2=400 mm and Wa=1360 mm for tight spaces.'],
                        'benefit' => ['tr'=>'Dar koridorlarda hızlı manevra.', 'en'=>'Quick maneuvering in narrow aisles.'],
                        'slogan' => ['tr'=>'Kompakt güç.', 'en'=>'Compact power.'],
                        'motto' => ['tr'=>'Sığar', 'en'=>'It fits'],
                        'short' => ['tr'=>'Dar alanlarda çevik hareket.', 'en'=>'Agile in tight spaces.'],
                        'long' => ['tr'=>'Kısa ön gövde uzunluğu ve küçük dönüş yarıçapı kapı geçişleri ile raf aralarında avantaj sağlar.', 'en'=>'Short head length and small turning radius help at doorways and between racks.'],
                        'comparison' => ['tr'=>'Uzun şaseli modellerden daha çevik.', 'en'=>'More agile than long‑chassis models.'],
                        'keywords' => ['tr'=>'kompakt, manevra', 'en'=>'compact, maneuverability']
                    ],
                    'removable-compartment' => [
                        'technical' => ['tr'=>'Tek bataryada taşınabilir saklama bölmesi.', 'en'=>'Portable storage compartment with single‑battery setup.'],
                        'benefit' => ['tr'=>'Aksesuarlar elinizin altında olur.', 'en'=>'Keeps accessories within reach.'],
                        'slogan' => ['tr'=>'Hazır düzen.', 'en'=>'Ready storage.'],
                        'motto' => ['tr'=>'Yanında', 'en'=>'On hand'],
                        'short' => ['tr'=>'Ekipmanı düzenli ve erişilebilir tutar.', 'en'=>'Keeps tools tidy and accessible.'],
                        'long' => ['tr'=>'Standart yapıda saklama bölmesi sürücüye eldiven ve etiket gibi malzemeleri pratikçe taşıma imkânı verir.', 'en'=>'The storage box lets operators carry gloves and labels conveniently.'],
                        'comparison' => ['tr'=>'Saklama sunmayan modellere göre pratiklik sağlar.', 'en'=>'More practical than models without storage.'],
                        'keywords' => ['tr'=>'saklama, aksesuar', 'en'=>'storage, accessories']
                    ],
                    'gradeability' => [
                        'technical' => ['tr'=>'Azami eğim: %6 / %16 (yük/boş).', 'en'=>'Max gradeability: 6% / 16% (laden/unladen).'],
                        'benefit' => ['tr'=>'Rampa ve eşiklerde güven verir.', 'en'=>'Confident on ramps and thresholds.'],
                        'slogan' => ['tr'=>'Tırmanır.', 'en'=>'Climbs.'],
                        'motto' => ['tr'=>'Güçlü tutuş', 'en'=>'Sure footing'],
                        'short' => ['tr'=>'Yük varken dahi kontrollü tırmanış.', 'en'=>'Controlled climbs even when loaded.'],
                        'long' => ['tr'=>'Depo rampaları ve yükleme alanlarında stabil hız ve kontrollü kalkış sağlar.', 'en'=>'Provides stable speed and controlled starts on loading ramps.'],
                        'comparison' => ['tr'=>'Eğim performansı el tipi modellere göre üstündür.', 'en'=>'Better on inclines than manual units.'],
                        'keywords' => ['tr'=>'eğim, rampa', 'en'=>'grade, ramp']
                    ],
                    'travel-speed' => [
                        'technical' => ['tr'=>'Hız: 4.0 / 4.5 km/sa (yük/boş).', 'en'=>'Speed: 4.0 / 4.5 km/h (laden/unladen).'],
                        'benefit' => ['tr'=>'Toplama ve taşıma sürelerini kısaltır.', 'en'=>'Shortens picking and transfer times.'],
                        'slogan' => ['tr'=>'Hızlı akış.', 'en'=>'Keep flow fast.'],
                        'motto' => ['tr'=>'Zaman kazandırır', 'en'=>'Saves time'],
                        'short' => ['tr'=>'Vardiya temposuna uyumlu hız.', 'en'=>'Paces with shift tempo.'],
                        'long' => ['tr'=>'Dengeli hız profili dar koridorlarda güvenli fakat verimli ilerleme sağlar.', 'en'=>'Balanced speed profile enables safe yet efficient movement in narrow aisles.'],
                        'comparison' => ['tr'=>'Manuele göre daha hızlı döngü.', 'en'=>'Faster cycles than manual.'],
                        'keywords' => ['tr'=>'hız, verim', 'en'=>'speed, throughput']
                    ],
                    'maintenance' => [
                        'technical' => ['tr'=>'Elektromanyetik servis freni ve mekanik direksiyon.', 'en'=>'Electromagnetic service brake and mechanical steering.'],
                        'benefit' => ['tr'=>'Basit bakım ve güvenilirlik.', 'en'=>'Simple maintenance and reliability.'],
                        'slogan' => ['tr'=>'Kolay bakım.', 'en'=>'Easy upkeep.'],
                        'motto' => ['tr'=>'Az duruş', 'en'=>'Less downtime'],
                        'short' => ['tr'=>'Az parça, hızlı servis yaklaşımı.', 'en'=>'Fewer parts, quicker service.'],
                        'long' => ['tr'=>'Doğrudan tahrik ve yalın yapı, planlı bakımı hızlandırır ve kullanım ömrü boyunca maliyeti düşürmeye yardımcı olur.', 'en'=>'Direct drive and lean design speed scheduled service and help lower lifetime costs.'],
                        'comparison' => ['tr'=>'Karmaşık sistemlere göre daha az ayar ihtiyacı.', 'en'=>'Requires fewer adjustments than complex systems.'],
                        'keywords' => ['tr'=>'bakım, servis', 'en'=>'maintenance, service']
                    ]
                ], JSON_UNESCAPED_UNICODE),

                'technical_specs' => json_encode([
                    'tr'=>[
                        'Üretici'=>'iXtif',
                        'Model'=>'F4',
                        'Tahrik'=>'Elektrik',
                        'Operatör tipi'=>'Yaya',
                        'Kapasite (Q)'=>'1500 kg',
                        'Yük merkezi (c)'=>'600 mm',
                        'Tahrik aksı - yük merkezi mesafesi (x)'=>'950 mm',
                        'Dingil açıklığı (y)'=>'1180 mm',
                        'Servis ağırlığı'=>'120 kg',
                        'Lastik türü'=>'Poliüretan',
                        'Teker ölçüsü ön'=>'210×70 mm',
                        'Teker ölçüsü arka'=>'80×60 mm',
                        'Ek tekerlekler (caster)'=>'74×30 mm',
                        'Tekerlek sayısı (ön/arka)'=>'1x / 4',
                        'Ön iz genişliği'=>'410 / 535 mm',
                        'Kaldırma yüksekliği (h3)'=>'105 mm',
                        'Sürgü kolu yüksekliği min/max'=>'750 / 1190 mm',
                        'Alçak çatal yüksekliği (h13)'=>'88 mm',
                        'Toplam uzunluk (l1)'=>'1550 mm',
                        'Yük yüzeyine kadar uzunluk (l2)'=>'400 mm',
                        'Toplam genişlik (b1/b2)'=>'590 / 695 mm',
                        'Çatal ölçüleri (s/e/l)'=>'55×150×1150 mm',
                        'Çatal iç açıklığı (b5)'=>'560 / 685 mm',
                        'Şasi orta yerden yükseklik (m2)'=>'25 mm',
                        'Koridor genişliği 1000×1200 enine (Ast)'=>'2160 mm',
                        'Koridor genişliği 800×1200 uzunlamasına (Ast)'=>'2025 mm',
                        'Dönüş yarıçapı (Wa)'=>'1360 mm',
                        'Sürüş hızı (yük/boş)'=>'4.0 / 4.5 km/sa',
                        'Kaldırma hızı (yük/boş)'=>'0.017 / 0.020 m/sn',
                        'İndirme hızı (yük/boş)'=>'0.058 / 0.046 m/sn',
                        'Azami eğim (yük/boş)'=>'%6 / %16',
                        'Servis freni'=>'Elektromanyetik',
                        'Sürüş motoru (S2 60 dk)'=>'0.75 kW',
                        'Kaldırma motoru (S3 15%)'=>'0.5 kW',
                        'Batarya gerilimi/kapasite'=>'24V / 20Ah',
                        'Batarya ağırlığı'=>'5 kg',
                        'Tahrik kontrolü'=>'DC',
                        'Direksiyon tasarımı'=>'Mekanik',
                        'Ses basınç seviyesi'=>'74 dB(A)'
                    ],
                    'en'=>[
                        'Manufacturer'=>'iXtif',
                        'Model'=>'F4',
                        'Drive'=>'Electric',
                        'Operator type'=>'Pedestrian',
                        'Capacity (Q)'=>'1500 kg',
                        'Load center (c)'=>'600 mm',
                        'Load distance (x)'=>'950 mm',
                        'Wheelbase (y)'=>'1180 mm',
                        'Service weight'=>'120 kg',
                        'Tyre type'=>'Polyurethane',
                        'Wheel size front'=>'210×70 mm',
                        'Wheel size rear'=>'80×60 mm',
                        'Additional wheels (castor)'=>'74×30 mm',
                        'Wheels number front/rear'=>'1x / 4',
                        'Tread width front'=>'410 / 535 mm',
                        'Lift height (h3)'=>'105 mm',
                        'Tiller height min/max'=>'750 / 1190 mm',
                        'Lowered height (h13)'=>'88 mm',
                        'Overall length (l1)'=>'1550 mm',
                        'Length to face of forks (l2)'=>'400 mm',
                        'Overall width (b1/b2)'=>'590 / 695 mm',
                        'Fork dimensions (s/e/l)'=>'55×150×1150 mm',
                        'Fork spread (b5)'=>'560 / 685 mm',
                        'Ground clearance (m2)'=>'25 mm',
                        'Aisle width 1000×1200 crossways (Ast)'=>'2160 mm',
                        'Aisle width 800×1200 lengthways (Ast)'=>'2025 mm',
                        'Turning radius (Wa)'=>'1360 mm',
                        'Travel speed (laden/unladen)'=>'4.0 / 4.5 km/h',
                        'Lifting speed (laden/unladen)'=>'0.017 / 0.020 m/s',
                        'Lowering speed (laden/unladen)'=>'0.058 / 0.046 m/s',
                        'Max gradeability (laden/unladen)'=>'6% / 16%',
                        'Service brake'=>'Electromagnetic',
                        'Drive motor (S2 60min)'=>'0.75 kW',
                        'Lift motor (S3 15%)'=>'0.5 kW',
                        'Battery voltage/capacity'=>'24V / 20Ah',
                        'Battery weight'=>'5 kg',
                        'Drive control'=>'DC',
                        'Steering design'=>'Mechanical',
                        'Sound pressure level'=>'74 dB(A)'
                    ]
                ], JSON_UNESCAPED_UNICODE),

                'features' => json_encode([
                    'tr'=>[
                        'Çıkarılabilir 24V 20Ah Li‑Ion batarya',
                        'İkinci batarya yuvası ile uzatılmış çalışma',
                        'Opsiyonel denge tekerlekleri',
                        '400 mm ön gövde uzunluğu ile kompakt şasi',
                        'Harici şarj seçenekleri: 5A / 10A / 4A DC‑DC',
                        'Poliüretan tekerler ile düşük yuvarlanma direnci'
                    ],
                    'en'=>[
                        'Removable 24V 20Ah Li‑Ion battery',
                        'Second battery slot for extended uptime',
                        'Optional stabilizing wheels',
                        'Compact chassis with 400 mm head length',
                        'External charger options: 5A / 10A / 4A DC‑DC',
                        'Polyurethane wheels for low rolling resistance'
                    ]
                ], JSON_UNESCAPED_UNICODE),

                'use_cases' => json_encode([
                    'tr'=>[
                        'Gıda dağıtım depolarında toplama ve sevk',
                        'Perakende arka alan palet hareketleri',
                        'Kamyon içi yükleme/boşaltma',
                        'Soğuk oda giriş çıkışlarında dar koridor manevraları'
                    ],
                    'en'=>[
                        'Picking and dispatch in food distribution warehouses',
                        'Back‑of‑store pallet movements in retail',
                        'In‑truck loading and unloading',
                        'Narrow‑aisle maneuvers at cold‑room access points'
                    ]
                ], JSON_UNESCAPED_UNICODE),

                'faq_data' => json_encode([
                    'tr'=>[
                        ['category'=>'Technical','question'=>'Kapasite nedir?','answer'=>'Maksimum 1500 kg.'],
                        ['category'=>'Technical','question'=>'Batarya tipi nedir?','answer'=>'24V 20Ah Li‑Ion, çıkarılabilir.'],
                        ['category'=>'Technical','question'=>'İkinci batarya takılabilir mi?','answer'=>'Evet, iki güç yuvası ikinci modülü destekler.'],
                        ['category'=>'Usage','question'=>'Dar koridorlarda kullanılabilir mi?','answer'=>'400 mm l2 ve 1360 mm dönüş yarıçapı ile uygundur.'],
                        ['category'=>'Usage','question'=>'Denge tekerlekleri standart mı?','answer'=>'Hayır, opsiyoneldir.'],
                        ['category'=>'Maintenance','question'=>'Şarj seçenekleri nelerdir?','answer'=>'24V‑5A, 24V‑10A ve 24V‑4A harici DC‑DC şarj cihazları mevcuttur.'],
                        ['category'=>'Performance','question'=>'Hız değerleri nedir?','answer'=>'4.0 / 4.5 km/sa (yük/boş).'],
                        ['category'=>'Performance','question'=>'Eğim performansı nedir?','answer'=>'%6 / %16 (yük/boş).'],
                        ['category'=>'Technical','question'=>'Teker malzemesi nedir?','answer'=>'Poliüretandır.'],
                        ['category'=>'Technical','question'=>'Çatal ölçüleri nedir?','answer'=>'55×150×1150 mm.'],
                        ['category'=>'Maintenance','question'=>'Fren tipi nedir?','answer'=>'Elektromanyetiktir.'],
                        ['category'=>'Technical','question'=>'Ses seviyesi kaç dB?','answer'=>'74 dB(A).'],
                        ['category'=>'Usage','question'=>'Depo kapılarından geçiş uygun mu?','answer'=>'Kompakt tasarım geçişlerde avantaj sağlar.'],
                        ['category'=>'Maintenance','question'=>'Direksiyon tipi nedir?','answer'=>'Mekaniktir.'],
                        ['category'=>'Technical','question'=>'Servis ağırlığı nedir?','answer'=>'120 kg.']
                    ],
                    'en'=>[
                        ['category'=>'Technical','question'=>'What is the capacity?','answer'=>'Maximum 1500 kg.'],
                        ['category'=>'Technical','question'=>'What battery type is used?','answer'=>'24V 20Ah Li‑Ion, removable.'],
                        ['category'=>'Technical','question'=>'Can a second battery be added?','answer'=>'Yes, the two power slots support a second module.'],
                        ['category'=>'Usage','question'=>'Is it suited for narrow aisles?','answer'=>'Yes. l2=400 mm and Wa=1360 mm.'],
                        ['category'=>'Usage','question'=>'Are stabilizing wheels standard?','answer'=>'No. They are optional.'],
                        ['category'=>'Maintenance','question'=>'What charger options exist?','answer'=>'24V‑5A, 24V‑10A, and 24V‑4A external DC‑DC.'],
                        ['category'=>'Performance','question'=>'What are the speed values?','answer'=>'4.0 / 4.5 km/h (laden/unladen).'],
                        ['category'=>'Performance','question'=>'What is the gradeability?','answer'=>'6% / 16% (laden/unladen).'],
                        ['category'=>'Technical','question'=>'What is the wheel material?','answer'=>'Polyurethane.'],
                        ['category'=>'Technical','question'=>'What are the fork dimensions?','answer'=>'55×150×1150 mm.'],
                        ['category'=>'Maintenance','question'=>'What brake type is used?','answer'=>'Electromagnetic.'],
                        ['category'=>'Technical','question'=>'What is the sound level?','answer'=>'74 dB(A).'],
                        ['category'=>'Usage','question'=>'Is doorway maneuvering easy?','answer'=>'The compact design helps in doorways.'],
                        ['category'=>'Maintenance','question'=>'What steering design is used?','answer'=>'Mechanical.'],
                        ['category'=>'Technical','question'=>'What is the service weight?','answer'=>'120 kg.']
                    ]
                ], JSON_UNESCAPED_UNICODE),

                'keywords' => json_encode([
                    'tr'=>['primary'=>['elektrikli transpalet','li‑ion','1500 kg','kompakt','denge tekeri'],'synonyms'=>['palet taşıyıcı','depo aracı'],'usage_jargon'=>['l2','Wa','PU teker']],
                    'en'=>['primary'=>['electric pallet truck','li‑ion','1500 kg','compact','stabilizing wheels'],'synonyms'=>['pallet jack','material handling'],'usage_jargon'=>['l2','Wa','PU wheels']]
                ], JSON_UNESCAPED_UNICODE),
                'created_at' => now(),
                'updated_at' => now(),
                'published_at' => now(),
            ]
        );

        SeoSetting::updateOrCreate(
            ['seoable_type' => 'Modules\Shop\app\Models\ShopProduct', 'seoable_id' => $product->product_id],
            [
                'titles' => json_encode([
                    'tr'=>'iXtif F4 Transpalet | 1500 kg Li‑Ion Kompakt',
                    'en'=>'iXtif F4 Pallet Truck | 1500 kg Li‑Ion Compact'
                ], JSON_UNESCAPED_UNICODE),
                'descriptions' => json_encode([
                    'tr'=>'iXtif F4: 1500 kg kapasiteli, 24V 20Ah çıkarılabilir Li‑Ion bataryalı, 1360 mm dönüş yarıçaplı, dar alanlar için kompakt elektrikli transpalet.',
                    'en'=>'iXtif F4: 1500 kg capacity, removable 24V 20Ah Li‑Ion battery, 1360 mm turning radius, compact electric pallet truck for tight spaces.'
                ], JSON_UNESCAPED_UNICODE),
                'og_titles' => json_encode([
                    'tr'=>'iXtif F4 Transpalet | 1500 kg Li‑Ion Kompakt',
                    'en'=>'iXtif F4 Pallet Truck | 1500 kg Li‑Ion Compact'
                ], JSON_UNESCAPED_UNICODE),
                'og_descriptions' => json_encode([
                    'tr'=>'1500 kg kapasite, Li‑Ion güç, 1360 mm dönüş yarıçapı ve opsiyonel denge tekerleriyle depo içi taşımalarda çevik çözüm.',
                    'en'=>'1500 kg capacity, Li‑Ion power, 1360 mm turning radius, and optional stabilizers for agile in‑warehouse handling.'
                ], JSON_UNESCAPED_UNICODE),
                'og_image' => null,
                'robots_meta' => json_encode([
                    'index'=>true, 'follow'=>true, 'max-snippet'=>-1, 'max-image-preview'=>'large', 'max-video-preview'=>-1,
                    'noarchive'=>false, 'noimageindex'=>false, 'notranslate'=>false, 'indexifembedded'=>true, 'noydir'=>true, 'noodp'=>true
                ], JSON_UNESCAPED_UNICODE),
                'schema_type' => 'Product',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}

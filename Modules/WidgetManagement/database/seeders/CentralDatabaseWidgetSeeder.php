<?php

namespace Modules\WidgetManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;

class CentralDatabaseWidgetSeeder extends Seeder
{
    public function run()
    {
        // Tenant kontrolü
        if (function_exists('tenant') && tenant()) {
            if ($this->command) {
                $this->command->info('Tenant contextinde çalışıyor, CentralDatabaseWidgetSeeder atlanıyor.');
            }
            Log::info('Tenant contextinde çalışıyor, CentralDatabaseWidgetSeeder atlanıyor. Tenant ID: ' . tenant('id'));
            return;
        }
        
        // Mevcut bağlantıyı kaydet
        $previousConnection = Config::get('database.default');
        
        try {
            // Merkezi veritabanına bağlantıyı zorlayarak değiştir
            Config::set('database.default', 'mysql');
            
            Log::info('CentralDatabaseWidgetSeeder merkezi veritabanına bağlanıyor...');
            
            // widget_categories tablosu var mı kontrol et
            if (!Schema::hasTable('widget_categories')) {
                Log::error('widget_categories tablosu bulunamadı, önce migrasyonları çalıştırın.');
                if ($this->command) {
                    $this->command->error('widget_categories tablosu bulunamadı, önce migrasyonları çalıştırın.');
                }
                return;
            }
            
            // Ana kategorileri oluştur
            $this->createMainCategories();
            
            // Alt kategorileri oluştur
            $this->createSubCategories();
            
            // Modül widget'larını oluştur
            $this->createModuleWidgets();
            
            // Slider widget'ını oluştur
            $this->createSliderWidget();
            
            Log::info('CentralDatabaseWidgetSeeder işlemlerini başarıyla tamamladı.');
            
            if ($this->command) {
                $this->command->info('Merkezi veritabanında kategoriler ve widget\'lar başarıyla oluşturuldu.');
            }
        } catch (\Exception $e) {
            Log::error('CentralDatabaseWidgetSeeder hatası: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            if ($this->command) {
                $this->command->error('CentralDatabaseWidgetSeeder hatası: ' . $e->getMessage());
            }
        } finally {
            // Eski bağlantıya geri dön
            Config::set('database.default', $previousConnection);
        }
    }
    
    private function createMainCategories()
    {
        // Ana kategoriler
        $categories = [
            [
                'title' => 'Kartlar',
                'slug' => 'kartlar',
                'description' => 'Kart tipi bileşenler için şablonlar',
                'icon' => 'fa-th-large',
                'order' => 1,
                'is_active' => true,
                'parent_id' => null,
                'has_subcategories' => false
            ],
            [
                'title' => 'İçerikler',
                'slug' => 'icerikler',
                'description' => 'Metin ve içerik türleri için şablonlar',
                'icon' => 'fa-file-alt',
                'order' => 2,
                'is_active' => true,
                'parent_id' => null,
                'has_subcategories' => false
            ],
            [
                'title' => 'Özellikler',
                'slug' => 'ozellikler',
                'description' => 'Özellik listeleme bileşenleri',
                'icon' => 'fa-list',
                'order' => 3,
                'is_active' => true,
                'parent_id' => null,
                'has_subcategories' => false
            ],
            [
                'title' => 'Formlar',
                'slug' => 'formlar',
                'description' => 'Form ve giriş elemanları',
                'icon' => 'fa-wpforms',
                'order' => 4,
                'is_active' => true,
                'parent_id' => null,
                'has_subcategories' => false
            ],
            [
                'title' => 'Hero\'lar',
                'slug' => 'hero-bilesenleri',
                'description' => 'Ana başlık ve tanıtım bileşenleri',
                'icon' => 'fa-heading',
                'order' => 5,
                'is_active' => true,
                'parent_id' => null,
                'has_subcategories' => false
            ],
            [
                'title' => 'Yerleşimler',
                'slug' => 'yerlesimler',
                'description' => 'Sayfa düzeni ve yerleşim şablonları',
                'icon' => 'fa-columns',
                'order' => 6,
                'is_active' => true,
                'parent_id' => null,
                'has_subcategories' => false
            ],
            [
                'title' => 'Medya',
                'slug' => 'medya',
                'description' => 'Görsel, video ve diğer medya elemanları',
                'icon' => 'fa-photo-video',
                'order' => 7,
                'is_active' => true,
                'parent_id' => null,
                'has_subcategories' => false
            ],
            [
                'title' => 'Referanslar',
                'slug' => 'referanslar',
                'description' => 'Müşteri yorumları ve referanslar',
                'icon' => 'fa-comment-dots',
                'order' => 8,
                'is_active' => true,
                'parent_id' => null,
                'has_subcategories' => false
            ],
            [
                'title' => 'Slider\'lar',
                'slug' => 'sliderlar',
                'description' => 'Slider ve carousel içeren bileşenler',
                'icon' => 'fa-sliders-h',
                'order' => 9,
                'is_active' => true,
                'parent_id' => null,
                'has_subcategories' => false
            ],
            [
                'title' => 'Modül Bileşenleri',
                'slug' => 'modul-bilesenleri',
                'description' => 'Sistem modüllerine ait bileşenler',
                'icon' => 'fa-cubes',
                'order' => 10,
                'is_active' => true,
                'parent_id' => null,
                'has_subcategories' => true
            ]
        ];
        
        foreach ($categories as $category) {
            try {
                // Kategori var mı kontrol et
                $existingCategory = DB::table('widget_categories')->where('slug', $category['slug'])->first();
                
                if (!$existingCategory) {
                    $now = now();
                    $category['created_at'] = $now;
                    $category['updated_at'] = $now;
                    
                    $id = DB::table('widget_categories')->insertGetId($category);
                    Log::info("Kategori oluşturuldu: {$category['title']} (ID: $id)");
                } else {
                    Log::info("Kategori zaten mevcut: {$category['title']} (ID: {$existingCategory->widget_category_id})");
                }
            } catch (\Exception $e) {
                Log::error("Kategori oluşturma hatası ({$category['slug']}): " . $e->getMessage());
            }
        }
    }
    
    private function createSubCategories()
    {
        // Modül ana kategorisini bul
        $moduleCategory = DB::table('widget_categories')->where('slug', 'modul-bilesenleri')->first();
        
        if (!$moduleCategory) {
            Log::error('Modül kategorisi bulunamadı, alt kategoriler oluşturulamıyor.');
            return;
        }
        
        Log::info('Modül kategorisi bulundu, ID: ' . $moduleCategory->widget_category_id);
        
        // Alt kategoriler
        $subCategories = [
            [
                'title' => 'Sayfa Modülü',
                'slug' => 'sayfa-modulu',
                'description' => 'Sayfa içeriklerine ait bileşenler',
                'icon' => 'fa-file',
                'order' => 1,
                'is_active' => true,
                'parent_id' => $moduleCategory->widget_category_id,
                'has_subcategories' => false
            ],
            [
                'title' => 'Portfolio Modülü',
                'slug' => 'portfolio-modulu',
                'description' => 'Portfolio içeriklerine ait bileşenler',
                'icon' => 'fa-images',
                'order' => 2,
                'is_active' => true,
                'parent_id' => $moduleCategory->widget_category_id,
                'has_subcategories' => false
            ]
        ];
        
        foreach ($subCategories as $category) {
            try {
                // Alt kategori var mı kontrol et
                $existingCategory = DB::table('widget_categories')->where('slug', $category['slug'])->first();
                
                if (!$existingCategory) {
                    $now = now();
                    $category['created_at'] = $now;
                    $category['updated_at'] = $now;
                    
                    $id = DB::table('widget_categories')->insertGetId($category);
                    Log::info("Alt kategori oluşturuldu: {$category['title']} (ID: $id)");
                } else {
                    Log::info("Alt kategori zaten mevcut: {$category['title']} (ID: {$existingCategory->widget_category_id})");
                }
            } catch (\Exception $e) {
                Log::error("Alt kategori oluşturma hatası ({$category['slug']}): " . $e->getMessage());
            }
        }
    }
    
    private function createModuleWidgets()
    {
        try {
            // Sayfa modülü kategorisini bul
            $pageCategory = DB::table('widget_categories')->where('slug', 'sayfa-modulu')->first();
            
            if ($pageCategory) {
                // Ana Sayfa İçeriği Widget'ı
                $homepageWidgetExist = DB::table('widgets')->where('slug', 'anasayfa-icerik')->exists();
                
                if (!$homepageWidgetExist) {
                    $now = now();
                    
                    DB::table('widgets')->insert([
                        'widget_category_id' => $pageCategory->widget_category_id,
                        'name' => 'Ana Sayfa İçeriği',
                        'slug' => 'anasayfa-icerik',
                        'description' => 'Ana sayfa olarak işaretlenmiş sayfanın içeriğini gösterir',
                        'type' => 'file',
                        'file_path' => 'modules/page/home/view',
                        'has_items' => false,
                        'is_active' => true,
                        'is_core' => true,
                        'settings_schema' => json_encode([
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
                                'name' => 'show_title',
                                'label' => 'Sayfa Başlığını Göster',
                                'type' => 'checkbox',
                                'required' => false
                            ]
                        ]),
                        'created_at' => $now,
                        'updated_at' => $now
                    ]);
                    
                    Log::info('Ana Sayfa İçeriği widget\'ı başarıyla oluşturuldu.');
                } else {
                    Log::info('Ana Sayfa İçeriği widget\'ı zaten mevcut, atlanıyor...');
                }
                
                // Son Eklenen Sayfalar Widget'ı
                $recentPagesWidgetExist = DB::table('widgets')->where('slug', 'son-eklenen-sayfalar')->exists();
                
                if (!$recentPagesWidgetExist) {
                    $now = now();
                    
                    DB::table('widgets')->insert([
                        'widget_category_id' => $pageCategory->widget_category_id,
                        'name' => 'Son Eklenen Sayfalar',
                        'slug' => 'son-eklenen-sayfalar',
                        'description' => 'Son eklenen sayfaları listeler',
                        'type' => 'file',
                        'file_path' => 'modules/page/recent/view',
                        'has_items' => false,
                        'is_active' => true,
                        'is_core' => true,
                        'settings_schema' => json_encode([
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
                                'name' => 'show_dates',
                                'label' => 'Tarihleri Göster',
                                'type' => 'checkbox',
                                'required' => false
                            ],
                            [
                                'name' => 'limit',
                                'label' => 'Gösterilecek Sayfa Sayısı',
                                'type' => 'number',
                                'required' => false,
                                'default' => 5
                            ]
                        ]),
                        'created_at' => $now,
                        'updated_at' => $now
                    ]);
                    
                    Log::info('Son Eklenen Sayfalar widget\'ı başarıyla oluşturuldu.');
                } else {
                    Log::info('Son Eklenen Sayfalar widget\'ı zaten mevcut, atlanıyor...');
                }
            } else {
                Log::warning('Sayfa modülü kategorisi bulunamadı, sayfa widget\'ları oluşturulamıyor.');
            }
            
            // Portfolio modülü kategorisini bul
            $portfolioCategory = DB::table('widget_categories')->where('slug', 'portfolio-modulu')->first();
            
            if ($portfolioCategory) {
                // Portfolio Liste Widget'ı
                $portfolioListWidgetExist = DB::table('widgets')->where('slug', 'portfolio-liste')->exists();
                
                if (!$portfolioListWidgetExist) {
                    $now = now();
                    
                    DB::table('widgets')->insert([
                        'widget_category_id' => $portfolioCategory->widget_category_id,
                        'name' => 'Portfolio Liste',
                        'slug' => 'portfolio-liste',
                        'description' => 'Projeleri listeler',
                        'type' => 'file',
                        'file_path' => 'modules/portfolio/list/view',
                        'has_items' => false,
                        'is_active' => true,
                        'is_core' => true,
                        'settings_schema' => json_encode([
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
                                'name' => 'show_description',
                                'label' => 'Açıklamayı Göster',
                                'type' => 'checkbox',
                                'required' => false
                            ],
                            [
                                'name' => 'description',
                                'label' => 'Açıklama Metni',
                                'type' => 'textarea',
                                'required' => false
                            ],
                            [
                                'name' => 'limit',
                                'label' => 'Gösterilecek Proje Sayısı',
                                'type' => 'number',
                                'required' => false,
                                'default' => 6
                            ]
                        ]),
                        'created_at' => $now,
                        'updated_at' => $now
                    ]);
                    
                    Log::info('Portfolio Liste widget\'ı başarıyla oluşturuldu.');
                } else {
                    Log::info('Portfolio Liste widget\'ı zaten mevcut, atlanıyor...');
                }
                
                // Portfolio Detay Widget'ı
                $portfolioDetailWidgetExist = DB::table('widgets')->where('slug', 'portfolio-detay')->exists();
                
                if (!$portfolioDetailWidgetExist) {
                    $now = now();
                    
                    DB::table('widgets')->insert([
                        'widget_category_id' => $portfolioCategory->widget_category_id,
                        'name' => 'Portfolio Detay',
                        'slug' => 'portfolio-detay',
                        'description' => 'Seçilen bir projenin detaylarını gösterir',
                        'type' => 'file',
                        'file_path' => 'modules/portfolio/detail/view',
                        'has_items' => false,
                        'is_active' => true,
                        'is_core' => true,
                        'settings_schema' => json_encode([
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
                                'name' => 'project_id',
                                'label' => 'Proje ID',
                                'type' => 'number',
                                'required' => false
                            ],
                            [
                                'name' => 'show_gallery',
                                'label' => 'Galeriyi Göster',
                                'type' => 'checkbox',
                                'required' => false,
                                'default' => true
                            ]
                        ]),
                        'created_at' => $now,
                        'updated_at' => $now
                    ]);
                    
                    Log::info('Portfolio Detay widget\'ı başarıyla oluşturuldu.');
                } else {
                    Log::info('Portfolio Detay widget\'ı zaten mevcut, atlanıyor...');
                }
            } else {
                Log::warning('Portfolio modülü kategorisi bulunamadı, portfolio widget\'ları oluşturulamıyor.');
            }
        } catch (\Exception $e) {
            Log::error('Modül widget\'larını oluşturma hatası: ' . $e->getMessage());
        }
    }
    
    private function createSliderWidget()
    {
        try {
            // Slider kategorisini bul
            $sliderCategory = DB::table('widget_categories')->where('slug', 'slider-bilesenleri')->first();
            
            if (!$sliderCategory) {
                Log::warning('Slider kategorisi bulunamadı, slider widget\'ı oluşturulamıyor.');
                return;
            }
            
            // Slider widget'ı var mı kontrol et
            $sliderWidgetExist = DB::table('widgets')->where('slug', 'slider')->exists();
            
            if (!$sliderWidgetExist) {
                $now = now();
                
                DB::table('widgets')->insert([
                    'widget_category_id' => $sliderCategory->widget_category_id,
                    'name' => 'Slider',
                    'slug' => 'slider',
                    'description' => 'Varsayılan yapıdaki temel slayt bileşeni',
                    'type' => 'dynamic',
                    'content_html' => '<div id="slider-{{unique_id}}" class="carousel slide" data-bs-ride="carousel">
                        {{#if show_indicators}}
                        <div class="carousel-indicators">
                            {{#each items}}
                            <button type="button" data-bs-target="#slider-{{../unique_id}}" data-bs-slide-to="{{@index}}" {{#if @first}}class="active"{{/if}}></button>
                            {{/each}}
                        </div>
                        {{/if}}

                        <div class="carousel-inner" style="height:{{height}}px;">
                            {{#each items}}
                            {{#if is_active}}
                            <div class="carousel-item {{#if @first}}active{{/if}}">
                                <img src="{{image}}" class="d-block w-100" alt="{{title}}" style="object-fit:cover; height:100%;">
                                <div class="carousel-caption d-none d-md-block">
                                    <h5>{{title}}</h5>
                                    <p>{{description}}</p>
                                    {{#if button_text}}
                                    <a href="{{button_url}}" class="btn btn-primary">{{button_text}}</a>
                                    {{/if}}
                                </div>
                            </div>
                            {{/if}}
                            {{/each}}
                        </div>

                        {{#if show_controls}}
                        <button class="carousel-control-prev" type="button" data-bs-target="#slider-{{unique_id}}" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Önceki</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#slider-{{unique_id}}" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Sonraki</span>
                        </button>
                        {{/if}}
                    </div>',
                    'content_js' => 'document.addEventListener("DOMContentLoaded", function() {
                        new bootstrap.Carousel(document.getElementById("slider-{{unique_id}}"), {
                            interval: {{interval}},
                            wrap: true
                        });
                    });',
                    'has_items' => true,
                    'is_active' => true,
                    'is_core' => true,
                    'settings_schema' => json_encode([
                        ['name' => 'unique_id', 'label' => 'Benzersiz ID', 'type' => 'text', 'system' => true],
                        ['name' => 'height', 'label' => 'Yükseklik (px)', 'type' => 'number', 'required' => true],
                        ['name' => 'interval', 'label' => 'Geçiş Süresi (ms)', 'type' => 'number', 'required' => true],
                        ['name' => 'show_indicators', 'label' => 'Göstergeleri Göster', 'type' => 'checkbox'],
                        ['name' => 'show_controls', 'label' => 'Kontrolleri Göster', 'type' => 'checkbox'],
                    ]),
                    'item_schema' => json_encode([
                        ['name' => 'title', 'label' => 'Slayt Başlığı', 'type' => 'text', 'required' => true],
                        ['name' => 'description', 'label' => 'Açıklama', 'type' => 'textarea'],
                        ['name' => 'image', 'label' => 'Resim', 'type' => 'image', 'required' => true],
                        ['name' => 'button_text', 'label' => 'Buton Metni', 'type' => 'text'],
                        ['name' => 'button_url', 'label' => 'Buton URL', 'type' => 'url'],
                        ['name' => 'is_active', 'label' => 'Aktif mi?', 'type' => 'checkbox'],
                        ['name' => 'unique_id', 'label' => 'Benzersiz ID', 'type' => 'text', 'system' => true],
                    ]),
                    'created_at' => $now,
                    'updated_at' => $now
                ]);
                
                Log::info('Slider widget\'ı başarıyla oluşturuldu.');
            } else {
                Log::info('Slider widget\'ı zaten mevcut, atlanıyor...');
            }
        } catch (\Exception $e) {
            Log::error('Slider widget\'ı oluşturma hatası: ' . $e->getMessage());
        }
    }
}
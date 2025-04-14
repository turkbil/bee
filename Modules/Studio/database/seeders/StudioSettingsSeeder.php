<?php

namespace Modules\Studio\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StudioSettingsSeeder extends Seeder
{
    /**
     * Studio modülü için varsayılan ayarları ekle
     *
     * @return void
     */
    public function run(): void
    {
        $this->seedWidgets();
        $this->seedSettings();
    }

    /**
     * Varsayılan widgetları ekle
     *
     * @return void
     */
    protected function seedWidgets(): void
    {
        // Widget modülü yüklü değilse işlem yapma
        if (!class_exists('Modules\WidgetManagement\App\Models\Widget')) {
            $this->command->info('WidgetManagement modülü bulunamadı. Widget verileri eklenmedi.');
            return;
        }
        
        $widgets = [
            [
                'name' => 'Hero Banner',
                'slug' => 'hero-banner',
                'description' => 'Tam genişlikte hero banner bileşeni.',
                'type' => 'content',
                'is_active' => true,
                'has_items' => false,
                'thumbnail' => 'widgets/hero-banner.jpg',
                'content_html' => '<div class="hero-banner py-5 bg-primary text-white text-center">
                    <div class="container py-5">
                        <h1 class="display-4 fw-bold mb-4">Etkileyici Başlık</h1>
                        <p class="lead mb-4">Bu açıklama metni ziyaretçilerin dikkatini çekecek ve onları harekete geçirecek.</p>
                        <button class="btn btn-light btn-lg px-4">Hemen Başlayın</button>
                    </div>
                </div>',
                'content_css' => '.hero-banner { background-size: cover; background-position: center; }',
                'content_js' => '',
                'data' => json_encode(['category' => 'temel']),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Özellik Kartları',
                'slug' => 'feature-cards',
                'description' => 'Üç özellik kartı bölümü.',
                'type' => 'content',
                'is_active' => true,
                'has_items' => false,
                'thumbnail' => 'widgets/feature-cards.jpg',
                'content_html' => '<div class="feature-cards py-5 bg-light">
                    <div class="container py-4">
                        <div class="row text-center mb-5">
                            <div class="col-md-12">
                                <h2 class="fw-bold">Özelliklerimiz</h2>
                                <p class="lead">Sunduğumuz benzersiz özellikler</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-4">
                                <div class="card h-100 shadow-sm">
                                    <div class="card-body text-center p-4">
                                        <div class="feature-icon mb-3">
                                            <i class="fas fa-rocket fa-3x text-primary"></i>
                                        </div>
                                        <h4>Hızlı Başlangıç</h4>
                                        <p class="text-muted">Hızlıca kurulum yapın ve hemen kullanmaya başlayın.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-4">
                                <div class="card h-100 shadow-sm">
                                    <div class="card-body text-center p-4">
                                        <div class="feature-icon mb-3">
                                            <i class="fas fa-shield-alt fa-3x text-primary"></i>
                                        </div>
                                        <h4>Güvenli Altyapı</h4>
                                        <p class="text-muted">Güvenlik önceliğimizdir. Verileriniz her zaman güvende.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-4">
                                <div class="card h-100 shadow-sm">
                                    <div class="card-body text-center p-4">
                                        <div class="feature-icon mb-3">
                                            <i class="fas fa-headset fa-3x text-primary"></i>
                                        </div>
                                        <h4>7/24 Destek</h4>
                                        <p class="text-muted">Her zaman yanınızdayız. İhtiyacınız olduğunda bize ulaşın.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>',
                'content_css' => '.feature-cards .feature-icon { height: 70px; display: flex; align-items: center; justify-content: center; }',
                'content_js' => '',
                'data' => json_encode(['category' => 'temel']),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'İletişim Formu',
                'slug' => 'contact-form',
                'description' => 'Basit iletişim formu bileşeni.',
                'type' => 'form',
                'is_active' => true,
                'has_items' => false,
                'thumbnail' => 'widgets/contact-form.jpg',
                'content_html' => '<div class="contact-form-section py-5">
                    <div class="container py-4">
                        <div class="row justify-content-center">
                            <div class="col-md-8">
                                <div class="card shadow-sm">
                                    <div class="card-body p-5">
                                        <h3 class="card-title text-center mb-4">Bize Ulaşın</h3>
                                        <form id="contact-form">
                                            <div class="mb-3">
                                                <label for="name" class="form-label">Adınız</label>
                                                <input type="text" class="form-control" id="name" placeholder="Adınızı giriniz" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="email" class="form-label">Email Adresiniz</label>
                                                <input type="email" class="form-control" id="email" placeholder="Email adresinizi giriniz" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="subject" class="form-label">Konu</label>
                                                <input type="text" class="form-control" id="subject" placeholder="Konu başlığını giriniz">
                                            </div>
                                            <div class="mb-3">
                                                <label for="message" class="form-label">Mesajınız</label>
                                                <textarea class="form-control" id="message" rows="5" placeholder="Mesajınızı giriniz" required></textarea>
                                            </div>
                                            <div class="d-grid">
                                                <button type="submit" class="btn btn-primary py-2">Gönder</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>',
                'content_css' => '',
                'content_js' => 'document.addEventListener("DOMContentLoaded", function() {
                    const form = document.getElementById("contact-form");
                    if(form) {
                        form.addEventListener("submit", function(e) {
                            e.preventDefault();
                            alert("Bu örnek bir form. Gerçek bir gönderim yapılmayacak.");
                        });
                    }
                });',
                'data' => json_encode(['category' => 'form']),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];
        
        foreach ($widgets as $widget) {
            try {
                // Widget zaten var mı kontrol et
                $exists = \Modules\WidgetManagement\App\Models\Widget::where('slug', $widget['slug'])->exists();
                
                if (!$exists) {
                    // Veritabanına doğrudan ekleme yapalım
                    DB::table('widgets')->insert($widget);
                }
            } catch (\Exception $e) {
                // Hata durumunda bilgi ver ama çalışmaya devam et
                $this->command->warn("Widget eklenirken hata oluştu: {$widget['name']} - {$e->getMessage()}");
            }
        }
        
        $this->command->info('Studio widgetları başarıyla eklendi.');
    }
    
    /**
     * Varsayılan ayarları ekle
     *
     * @return void
     */
    protected function seedSettings(): void
    {
        // StudioSetting tablosuna örnek ayarları ekle
        $settings = [
            // Örnek sayfa ayarı
            [
                'module' => 'page',
                'module_id' => 1, // Ana sayfa için
                'theme' => 'default',
                'header_template' => 'themes.default.headers.default',
                'footer_template' => 'themes.default.footers.default',
                'settings' => json_encode([
                    'show_breadcrumbs' => true,
                    'show_title' => true,
                    'background_color' => '#ffffff',
                    'text_color' => '#333333',
                ]),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ];
        
        // Page modülü yüklü ise sayfa varlığını kontrol et
        if (class_exists('Modules\Page\App\Models\Page')) {
            foreach ($settings as $setting) {
                // Sayfa var mı kontrol et
                $pageExists = \Modules\Page\App\Models\Page::find($setting['module_id']);
                
                // Sayfa yoksa atla
                if (!$pageExists) {
                    continue;
                }
                
                // Ayar zaten var mı kontrol et
                $exists = DB::table('studio_settings')
                    ->where('module', $setting['module'])
                    ->where('module_id', $setting['module_id'])
                    ->exists();
                
                if (!$exists) {
                    DB::table('studio_settings')->insert($setting);
                }
            }
            
            $this->command->info('Studio ayarları başarıyla eklendi.');
        } else {
            $this->command->info('Page modülü bulunamadı. Ayar verileri eklenmedi.');
        }
    }
}
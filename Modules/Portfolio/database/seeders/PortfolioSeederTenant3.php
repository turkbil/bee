<?php

namespace Modules\Portfolio\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Portfolio\app\Models\Portfolio;
use Modules\Portfolio\app\Models\PortfolioCategory;
use App\Models\SeoSetting;

class PortfolioSeederTenant3 extends Seeder
{
    public function run()
    {
        // Sağlık ve Medikal Teknolojiler kategorileri
        $categories = [
            [
                'title' => ['tr' => 'Hastane Yönetim Sistemleri', 'en' => 'Hospital Management Systems'],
                'slug' => ['tr' => 'hastane-yonetim-sistemleri', 'en' => 'hospital-management-systems'],
                'body' => ['tr' => 'Entegre hastane bilgi yönetim sistemleri ve dijital sağlık çözümleri', 'en' => 'Integrated hospital information management systems and digital health solutions']
            ],
            [
                'title' => ['tr' => 'Tıbbi Cihaz Yazılımları', 'en' => 'Medical Device Software'],
                'slug' => ['tr' => 'tibbi-cihaz-yazilimlari', 'en' => 'medical-device-software'],
                'body' => ['tr' => 'İleri teknoloji tıbbi cihazlar için özel yazılım geliştirme', 'en' => 'Custom software development for advanced medical devices']
            ],
            [
                'title' => ['tr' => 'Telemedisin Uygulamaları', 'en' => 'Telemedicine Applications'],
                'slug' => ['tr' => 'telemedisin-uygulamalari', 'en' => 'telemedicine-applications'],
                'body' => ['tr' => 'Uzaktan sağlık hizmetleri ve hasta takip sistemleri', 'en' => 'Remote healthcare services and patient tracking systems']
            ],
            [
                'title' => ['tr' => 'Laboratuvar Sistemleri', 'en' => 'Laboratory Systems'],
                'slug' => ['tr' => 'laboratuvar-sistemleri', 'en' => 'laboratory-systems'],
                'body' => ['tr' => 'Laboratuvar bilgi yönetim sistemleri ve analiz yazılımları', 'en' => 'Laboratory information management systems and analysis software']
            ],
        ];

        foreach ($categories as $categoryData) {
            PortfolioCategory::create($categoryData);
        }

        $categories = PortfolioCategory::all();

        // MedTech Solutions için sağlık sektörü portföy projeleri
        $portfolios = [
            [
                'title' => ['tr' => 'MediCare Hastane Bilgi Sistemleri', 'en' => 'MediCare Hospital Information Systems'],
                'slug' => ['tr' => 'medicare-hastane-bilgi-sistemleri', 'en' => 'medicare-hospital-information-systems'],
                'body' => [
                    'tr' => 'Türkiye\'nin en kapsamlı hastane bilgi yönetim sistemi. 500+ hastane ve 50,000+ sağlık personelinin güvenle kullandığı entegre sistem.',
                    'en' => 'Turkey\'s most comprehensive hospital information management system trusted by 500+ hospitals and 50,000+ healthcare professionals.'
                ],
                'image' => 'portfolio/medicare-hbys.jpg',
                'client' => 'MedTech Solutions',
                'date' => '2024-03-15',
                'url' => 'https://medicare.medtech.com',
                'is_active' => true,
            ],
            [
                'title' => ['tr' => 'CardioTech Kalp Monitörü Yazılımı', 'en' => 'CardioTech Heart Monitor Software'],
                'slug' => ['tr' => 'cardiotech-kalp-monitoru-yazilimi', 'en' => 'cardiotech-heart-monitor-software'],
                'body' => [
                    'tr' => 'İleri teknoloji kalp monitörü cihazları için özel yazılım. 24/7 kalp ritmi takibi ve erken uyarı sistemi.',
                    'en' => 'Advanced software for heart monitoring devices. 24/7 heart rhythm tracking and early warning system.'
                ],
                'image' => 'portfolio/cardiotech-monitor.jpg',
                'client' => 'CardioTech Inc.',
                'date' => '2024-02-20',
                'url' => 'https://cardiotech.medical.com',
                'is_active' => true,
            ],
            [
                'title' => ['tr' => 'TeleMed Uzaktan Konsültasyon Platformu', 'en' => 'TeleMed Remote Consultation Platform'],
                'slug' => ['tr' => 'telemed-uzaktan-konsultasyon-platformu', 'en' => 'telemed-remote-consultation-platform'],
                'body' => [
                    'tr' => 'Gelişmiş telemedisin çözümleri ve hasta takip sistemi. 15,000+ doktor ve 250,000+ hasta ile güvenli video konsültasyon platformu.',
                    'en' => 'Advanced telemedicine solutions and patient tracking system. Secure video consultation platform with 15,000+ doctors and 250,000+ patients.'
                ],
                'image' => 'portfolio/telemed-platform.jpg',
                'client' => 'TeleMed Solutions',
                'date' => '2024-01-10',
                'url' => 'https://telemed.health.com',
                'is_active' => true,
            ],
            [
                'title' => ['tr' => 'LabTech Laboratuvar Bilgi Sistemi', 'en' => 'LabTech Laboratory Information System'],
                'slug' => ['tr' => 'labtech-laboratuvar-bilgi-sistemi', 'en' => 'labtech-laboratory-information-system'],
                'body' => [
                    'tr' => 'Entegre laboratuvar yönetim ve analiz sistemi. Modern laboratuvarlar için tam otomatik bilgi yönetim sistemi.',
                    'en' => 'Integrated laboratory management and analysis system. Fully automated information management system for modern laboratories.'
                ],
                'image' => 'portfolio/labtech-system.jpg',
                'client' => 'LabTech Innovations',
                'date' => '2023-12-05',
                'url' => 'https://labtech.lab.com',
                'is_active' => true,
            ],
            [
                'title' => ['tr' => 'PharmaTech İlaç Takip Sistemi', 'en' => 'PharmaTech Drug Tracking System'],
                'slug' => ['tr' => 'pharmatech-ilac-takip-sistemi', 'en' => 'pharmatech-drug-tracking-system'],
                'body' => [
                    'tr' => 'Eczane ve hastane ilaç stok yönetim sistemi. Blockchain tabanlı güvenli takip sistemi.',
                    'en' => 'Pharmacy and hospital drug inventory management system. Blockchain-based secure tracking system.'
                ],
                'image' => 'portfolio/pharmatech-system.jpg',
                'client' => 'PharmaTech Corp.',
                'date' => '2023-11-15',
                'url' => 'https://pharmatech.rx.com',
                'is_active' => true,
            ],
            [
                'title' => ['tr' => 'NeuroTech Beyin Görüntüleme Yazılımı', 'en' => 'NeuroTech Brain Imaging Software'],
                'slug' => ['tr' => 'neurotech-beyin-goruntuleme-yazilimi', 'en' => 'neurotech-brain-imaging-software'],
                'body' => [
                    'tr' => 'MR ve BT görüntüleri için yapay zeka destekli analiz. Nöroloji alanında devrim yaratan AI destekli beyin görüntüsü analiz sistemi.',
                    'en' => 'AI-powered analysis for MR and CT images. Revolutionary AI-powered brain image analysis system in neurology.'
                ],
                'image' => 'portfolio/neurotech-brain.jpg',
                'client' => 'NeuroTech Research',
                'date' => '2023-10-20',
                'url' => 'https://neurotech.brain.com',
                'is_active' => true,
            ],
        ];

        foreach ($portfolios as $index => $portfolioData) {
            // Her portfolio için doğru kategoriyi belirle ve ekle
            if ($index < 2) {
                $category = $categories->first(function($cat) {
                    return isset($cat->slug['tr']) && $cat->slug['tr'] === 'hastane-yonetim-sistemleri';
                });
            } elseif ($index < 4) {
                $category = $categories->first(function($cat) {
                    return isset($cat->slug['tr']) && $cat->slug['tr'] === 'tibbi-cihaz-yazilimlari';
                });
            } elseif ($index < 5) {
                $category = $categories->first(function($cat) {
                    return isset($cat->slug['tr']) && $cat->slug['tr'] === 'telemedisin-uygulamalari';
                });
            } else {
                $category = $categories->first(function($cat) {
                    return isset($cat->slug['tr']) && $cat->slug['tr'] === 'laboratuvar-sistemleri';
                });
            }
            
            // Portfolio data'ya kategori ID'sini ekle
            if ($category) {
                $portfolioData['portfolio_category_id'] = $category->portfolio_category_id;
            }
            
            $portfolio = Portfolio::create($portfolioData);

            // Her portföy için SEO ayarları oluştur
            $this->createSeoSetting($portfolio);
        }
    }

    private function createSeoSetting($portfolio)
    {
        // Yeni JSON tabanlı SeoSetting formatı
        $portfolio->seoSetting()->create([
            'titles' => [
                'tr' => 'MediCare Hastane Bilgi Sistemleri | Sağlık Teknolojileri',
                'en' => 'MediCare Hospital Information Systems | Healthcare Technology'
            ],
            'descriptions' => [
                'tr' => 'Türkiye\'nin en kapsamlı hastane bilgi yönetim sistemi. 500+ hastane ve 50,000+ sağlık personelinin güvenle kullandığı entegre çözüm.',
                'en' => 'Turkey\'s most comprehensive hospital information management system trusted by 500+ hospitals and 50,000+ healthcare professionals.'
            ],
            'og_titles' => [
                'tr' => 'MediCare Hastane Bilgi Sistemleri',
                'en' => 'MediCare Hospital Information Systems'
            ],
            'og_descriptions' => [
                'tr' => 'Sağlık sektöründe dijital dönüşümün öncü çözümü',
                'en' => 'Leading digital transformation solution in healthcare sector'
            ],
            'robots_meta' => ['index' => true, 'follow' => true, 'archive' => true],
            'og_type' => 'article',
            'seo_score' => rand(85, 95),
        ]);
    }
}
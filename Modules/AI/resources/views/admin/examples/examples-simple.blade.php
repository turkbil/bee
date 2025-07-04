@extends('admin.layout')

@include('ai::admin.shared.helper')

@push('css')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
<div class="container-fluid">
    <!-- Sayfa Başlığı -->
    <div class="page-header d-print-none mb-4">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    <span class="text-primary">🤖</span> AI Kullanım Örnekleri Test Merkezi
                </h2>
                <div class="page-subtitle">
                    Her özelliği canlı test edebilir, sonuçları anlık görebilirsiniz
                </div>
            </div>
        </div>
    </div>

    <!-- Token Durumu -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h2 class="mb-0 {{ $tokenStatus['remaining_tokens'] > 0 ? 'text-primary' : 'text-danger' }}">
                        {{ number_format($tokenStatus['remaining_tokens']) }}
                    </h2>
                    <p class="text-muted mb-0">Kalan Token</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h2 class="mb-0 text-info">{{ number_format($tokenStatus['daily_usage']) }}</h2>
                    <p class="text-muted mb-0">Bugünkü Kullanım</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h2 class="mb-0 text-warning">{{ number_format($tokenStatus['monthly_usage']) }}</h2>
                    <p class="text-muted mb-0">Aylık Kullanım</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h2 class="mb-0">{{ ucfirst($tokenStatus['provider']) }}</h2>
                    <p class="text-muted mb-0">AI Provider</p>
                    <span class="badge {{ $tokenStatus['provider_active'] ? 'badge-success' : 'badge-danger' }}">
                        {{ $tokenStatus['provider_active'] ? 'Aktif' : 'Pasif' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- AKTİF ÖZELLİKLER -->
    <div class="card mb-4">
        <div class="card-header bg-success text-white">
            <h3 class="card-title mb-0">
                <i class="ti ti-check-circle me-2"></i>
                Tüm AI Özellikleri (25 Özellik)
            </h3>
        </div>
        <div class="card-body">
            <div class="row">
                
                <!-- İçerik Oluşturma -->
                <div class="col-12 col-lg-6 mb-4">
                    <div class="card h-100 border border-success">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title">📝 İçerik Oluşturma</h5>
                                <span class="badge bg-success">Aktif</span>
                            </div>
                            <p class="text-muted small mb-3">Blog yazıları, makaleler, ürün açıklamaları ve web sitesi içerikleri oluşturur.</p>
                            <div class="bg-light p-2 rounded mb-3">
                                <strong>Şu işe yarar:</strong> E-ticaret ürün sayfaları, blog yazıları, haber makaleleri, sosyal medya içerikleri otomatik oluşturma
                            </div>
                            <div class="mb-3">
                                <code class="small">AI::generateContent('blog_post', $topic)</code>
                            </div>
                            <button 
                                class="btn btn-primary btn-sm w-100 direct-test-btn" 
                                data-feature="İçerik Oluşturma"
                                data-example="Sivas Kangal köpeği hakkında blog yazısı yaz"
                                data-target="test-content-gen"
                                onclick="toggleTestArea('test-content-gen', 'İçerik Oluşturma', 'Sivas Kangal köpeği hakkında blog yazısı yaz')"
                            >
                                <i class="ti ti-rocket me-1"></i> Canlı Test Et
                            </button>
                            
                            <!-- Test Alanı Accordion -->
                            <div class="accordion mt-3" id="test-content-gen" style="display: none;">
                                <div class="accordion-item">
                                    <div class="accordion-header">
                                        <h6 class="mb-0 p-2 bg-primary text-white">
                                            <i class="ti ti-rocket me-2"></i>İçerik Oluşturma Testi
                                        </h6>
                                    </div>
                                    <div class="accordion-body">
                                        <!-- Test İçeriği -->
                                        <div class="mb-3">
                                            <label class="form-label"><strong>Test Metni:</strong></label>
                                            <textarea class="form-control test-input" rows="4" 
                                                      placeholder="Test edilecek metni girin...">Sivas Kangal köpeği hakkında blog yazısı yaz</textarea>
                                        </div>
                                        
                                        <!-- Hızlı Örnekler -->
                                        <div class="mb-3">
                                            <div class="card bg-light">
                                                <div class="card-body py-2">
                                                    <strong class="small">Hızlı Örnekler:</strong>
                                                    <div class="mt-2">
                                                        <button class="btn btn-sm btn-outline-primary me-1 mb-1 example-btn" 
                                                                data-text="Organik tarım">💡 Organik tarım</button>
                                                        <button class="btn btn-sm btn-outline-primary me-1 mb-1 example-btn" 
                                                                data-text="Evde Yoga Yapmanın Faydaları">💡 Yoga faydaları</button>
                                                        <button class="btn btn-sm btn-outline-primary me-1 mb-1 example-btn" 
                                                                data-text="Premium Kahve Makinesi">💡 Kahve makinesi</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Test Butonları -->
                                        <div class="d-flex gap-2 mb-3">
                                            <button class="btn btn-outline-primary demo-test-btn">
                                                <i class="ti ti-flask me-1"></i> Demo Test
                                            </button>
                                            <button class="btn btn-primary real-test-btn">
                                                <i class="ti ti-rocket me-1"></i> Gerçek AI Test
                                            </button>
                                            <button class="btn btn-outline-secondary clear-btn">
                                                <i class="ti ti-x me-1"></i> Temizle
                                            </button>
                                            <button class="btn btn-secondary close-test-btn" onclick="closeTestArea('test-content-gen')">
                                                <i class="ti ti-x me-1"></i> Kapat
                                            </button>
                                        </div>
                                        
                                        <!-- Sonuç Alanı -->
                                        <div class="result-area" style="display: none;">
                                            <!-- Test sonuçları burada gösterilecek -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Şablondan İçerik -->
                <div class="col-12 col-lg-6 mb-4">
                    <div class="card h-100 border border-success">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title">🎨 Şablondan İçerik</h5>
                                <span class="badge bg-success">Aktif</span>
                            </div>
                            <p class="text-muted small mb-3">Önceden hazırlanmış şablonları kullanarak hızlı içerik üretir.</p>
                            <div class="bg-light p-2 rounded mb-3">
                                <strong>Şu işe yarar:</strong> Ürün tanıtım sayfaları, hizmet açıklamaları, portfolio projeleri için tutarlı format
                            </div>
                            <div class="mb-3">
                                <code class="small">AI::generateFromTemplate('product', $data)</code>
                            </div>
                            <button 
                                class="btn btn-primary btn-sm w-100 direct-test-btn" 
                                data-feature="Şablondan İçerik"
                                data-example="iPhone 15 Pro Max için ürün açıklaması şablonu"
                                data-target="test-template-content"
                                onclick="toggleTestArea('test-template-content', 'Şablondan İçerik', 'iPhone 15 Pro Max için ürün açıklaması şablonu')"
                            >
                                <i class="ti ti-rocket me-1"></i> Canlı Test Et
                            </button>
                            
                            <!-- Test Alanı Accordion -->
                            <div class="accordion mt-3" id="test-template-content" style="display: none;">
                                <div class="accordion-item">
                                    <div class="accordion-header">
                                        <h6 class="mb-0 p-2 bg-primary text-white">
                                            <i class="ti ti-rocket me-2"></i>Şablondan İçerik Testi
                                        </h6>
                                    </div>
                                    <div class="accordion-body">
                                        <!-- Test İçeriği -->
                                        <div class="mb-3">
                                            <label class="form-label"><strong>Test Metni:</strong></label>
                                            <textarea class="form-control test-input" rows="4" 
                                                      placeholder="Şablon için konu girin...">iPhone 15 Pro Max için ürün açıklaması şablonu</textarea>
                                        </div>
                                        
                                        <!-- Hızlı Örnekler -->
                                        <div class="mb-3">
                                            <div class="card bg-light">
                                                <div class="card-body py-2">
                                                    <strong class="small">Hızlı Örnekler:</strong>
                                                    <div class="mt-2">
                                                        <button class="btn btn-sm btn-outline-primary me-1 mb-1 example-btn" 
                                                                data-text="Lüks otel için rezervasyon onay emaili şablonu">Otel Email</button>
                                                        <button class="btn btn-sm btn-outline-primary me-1 mb-1 example-btn" 
                                                                data-text="Yazılım şirketi için iş başvuru formu şablonu">İş Başvuru</button>
                                                        <button class="btn btn-sm btn-outline-primary me-1 mb-1 example-btn" 
                                                                data-text="E-ticaret sitesi için ürün karşılaştırma tablosu">Karşılaştırma</button>
                                                        <button class="btn btn-sm btn-outline-danger clear-btn">Temizle</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Test Butonları -->
                                        <div class="d-flex gap-2 mb-3">
                                            <button class="btn btn-outline-secondary demo-test-btn">
                                                <i class="ti ti-flask me-1"></i>Demo Test
                                            </button>
                                            <button class="btn btn-success real-test-btn">
                                                <i class="ti ti-zap me-1"></i>Gerçek AI Test
                                            </button>
                                            <button class="btn btn-outline-danger" onclick="closeTestArea('test-template-content')">
                                                <i class="ti ti-x me-1"></i>Kapat
                                            </button>
                                        </div>
                                        
                                        <!-- Sonuç Alanı -->
                                        <div class="result-area" style="display: none;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Başlık Alternatifleri -->
                <div class="col-12 col-lg-6 mb-4">
                    <div class="card h-100 border border-success">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title">💡 Başlık Alternatifleri</h5>
                                <span class="badge bg-success">Aktif</span>
                            </div>
                            <p class="text-muted small mb-3">Bir konu için farklı başlık seçenekleri ve varyasyonları oluşturur.</p>
                            <div class="bg-light p-2 rounded mb-3">
                                <strong>Şu işe yarar:</strong> SEO optimizasyonu, A/B testleri, sosyal medya paylaşımları için etkili başlıklar
                            </div>
                            <div class="mb-3">
                                <code class="small">AI::generateTitleAlternatives($topic, 5)</code>
                            </div>
                            <button 
                                class="btn btn-primary btn-sm w-100 direct-test-btn" 
                                data-feature="Başlık Alternatifleri"
                                data-example="Evde kahve demleme teknikleri konusu için başlık alternatifleri"
                                data-target="test-title-alternatives"
                                onclick="toggleTestArea('test-title-alternatives', 'Başlık Alternatifleri', 'Evde kahve demleme teknikleri konusu için başlık alternatifleri')"
                            >
                                <i class="ti ti-rocket me-1"></i> Canlı Test Et
                            </button>
                            
                            <!-- Test Alanı Accordion -->
                            <div class="accordion mt-3" id="test-title-alternatives" style="display: none;">
                                <div class="accordion-item">
                                    <div class="accordion-header">
                                        <h6 class="mb-0 p-2 bg-primary text-white">
                                            <i class="ti ti-rocket me-2"></i>Başlık Alternatifleri Testi
                                        </h6>
                                    </div>
                                    <div class="accordion-body">
                                        <!-- Test İçeriği -->
                                        <div class="mb-3">
                                            <label class="form-label"><strong>Test Metni:</strong></label>
                                            <textarea class="form-control test-input" rows="4" 
                                                      placeholder="Test edilecek metni girin...">Evde kahve demleme teknikleri konusu için başlık alternatifleri</textarea>
                                        </div>
                                        
                                        <!-- Hızlı Örnekler -->
                                        <div class="mb-3">
                                            <div class="card bg-light">
                                                <div class="card-body py-2">
                                                    <strong class="small">Hızlı Örnekler:</strong>
                                                    <div class="mt-2">
                                                        <button class="btn btn-sm btn-outline-primary me-1 mb-1 example-btn" 
                                                                data-text="Sağlıklı yaşam rehberi için başlık önerileri">Sağlık</button>
                                                        <button class="btn btn-sm btn-outline-primary me-1 mb-1 example-btn" 
                                                                data-text="Teknoloji blog yazısı için başlık alternatifleri">Teknoloji</button>
                                                        <button class="btn btn-sm btn-outline-primary me-1 mb-1 example-btn" 
                                                                data-text="Yemek tarifi için çekici başlık önerileri">Yemek</button>
                                                        <button class="btn btn-sm btn-outline-danger clear-btn">Temizle</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Test Butonları -->
                                        <div class="d-flex gap-2 mb-3">
                                            <button class="btn btn-outline-secondary demo-test-btn">
                                                <i class="ti ti-flask me-1"></i>Demo Test
                                            </button>
                                            <button class="btn btn-success real-test-btn">
                                                <i class="ti ti-zap me-1"></i>Gerçek AI Test
                                            </button>
                                            <button class="btn btn-outline-danger" onclick="closeTestArea('test-title-alternatives')">
                                                <i class="ti ti-x me-1"></i>Kapat
                                            </button>
                                        </div>
                                        
                                        <!-- Sonuç Alanı -->
                                        <div class="result-area" style="display: none;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- İçerik Özeti -->
                <div class="col-12 col-lg-6 mb-4">
                    <div class="card h-100 border border-success">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title">📋 İçerik Özeti</h5>
                                <span class="badge bg-success">Aktif</span>
                            </div>
                            <p class="text-muted small mb-3">Uzun metinleri kısa ve öz hale getirir, ana noktaları çıkarır.</p>
                            <div class="bg-light p-2 rounded mb-3">
                                <strong>Şu işe yarar:</strong> Makale özetleri, meta açıklamalar, rapor özetleri, newsletter içerikleri
                            </div>
                            <div class="mb-3">
                                <code class="small">AI::summarizeContent($text, $length)</code>
                            </div>
                            <button 
                                class="btn btn-primary btn-sm w-100 direct-test-btn" 
                                data-feature="İçerik Özeti"
                                data-example="Büyük teknoloji fuarı bu yıl şehrimizde düzenlenecek. Etkinlikte yapay zeka, robotik, havacılık ve uzay teknolojileri alanında yüzlerce proje sergilenecek."
                                data-target="test-content-summary"
                                onclick="toggleTestArea('test-content-summary', 'İçerik Özeti', 'Türkiye\'nin en büyük teknoloji fuarı TechnoFest bu yıl İstanbul\'da düzenlenecek. Etkinlikte yapay zeka, robotik, havacılık ve uzay teknolojileri alanında yüzlerce proje sergilenecek.')"
                            >
                                <i class="ti ti-rocket me-1"></i> Canlı Test Et
                            </button>
                            
                            <!-- Test Alanı Accordion -->
                            <div class="accordion mt-3" id="test-content-summary" style="display: none;">
                                <div class="accordion-item">
                                    <div class="accordion-header">
                                        <h6 class="mb-0 p-2 bg-primary text-white">
                                            <i class="ti ti-rocket me-2"></i>İçerik Özeti Testi
                                        </h6>
                                    </div>
                                    <div class="accordion-body">
                                        <!-- Test İçeriği -->
                                        <div class="mb-3">
                                            <label class="form-label"><strong>Test Metni:</strong></label>
                                            <textarea class="form-control test-input" rows="4" 
                                                      placeholder="Özetlenecek metni girin...">Büyük teknoloji fuarı bu yıl şehrimizde düzenlenecek. Etkinlikte yapay zeka, robotik, havacılık ve uzay teknolojileri alanında yüzlerce proje sergilenecek.</textarea>
                                        </div>
                                        
                                        <!-- Hızlı Örnekler -->
                                        <div class="mb-3">
                                            <div class="card bg-light">
                                                <div class="card-body py-2">
                                                    <strong class="small">Hızlı Örnekler:</strong>
                                                    <div class="mt-2">
                                                        <button class="btn btn-sm btn-outline-primary me-1 mb-1 example-btn" 
                                                                data-text="Yapay zeka teknolojisi günümüzde birçok sektörde devrim yaratıyor. Sağlık, eğitim, finans ve üretim alanlarında AI kullanımı hızla artıyor.">AI Teknoloji</button>
                                                        <button class="btn btn-sm btn-outline-primary me-1 mb-1 example-btn" 
                                                                data-text="E-ticaret pazarı 2024 yılında büyük büyüme gösterdi. Online alışveriş alışkanlıkları değişirken mobil ticaret önem kazandı.">E-ticaret</button>
                                                        <button class="btn btn-sm btn-outline-primary me-1 mb-1 example-btn" 
                                                                data-text="Sürdürülebilir enerji kaynakları gelecek için kritik önem taşıyor. Güneş ve rüzgar enerjisi yatırımları artıyor.">Enerji</button>
                                                        <button class="btn btn-sm btn-outline-danger clear-btn">Temizle</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Test Butonları -->
                                        <div class="d-flex gap-2 mb-3">
                                            <button class="btn btn-outline-secondary demo-test-btn">
                                                <i class="ti ti-flask me-1"></i>Demo Test
                                            </button>
                                            <button class="btn btn-success real-test-btn">
                                                <i class="ti ti-zap me-1"></i>Gerçek AI Test
                                            </button>
                                            <button class="btn btn-outline-danger" onclick="closeTestArea('test-content-summary')">
                                                <i class="ti ti-x me-1"></i>Kapat
                                            </button>
                                        </div>
                                        
                                        <!-- Sonuç Alanı -->
                                        <div class="result-area" style="display: none;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SSS Oluşturma -->
                <div class="col-12 col-lg-6 mb-4">
                    <div class="card h-100 border border-success">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title">❓ SSS Oluşturma</h5>
                                <span class="badge bg-success">Aktif</span>
                            </div>
                            <p class="text-muted small mb-3">İçerikten sıkça sorulan sorular ve cevapları otomatik oluşturur.</p>
                            <div class="bg-light p-2 rounded mb-3">
                                <strong>Şu işe yarar:</strong> Destek sayfaları, ürün SSS bölümleri, müşteri hizmetleri, bilgi bankası
                            </div>
                            <div class="mb-3">
                                <code class="small">AI::generateFAQ($content, $count)</code>
                            </div>
                            <button 
                                class="btn btn-primary btn-sm w-100 direct-test-btn" 
                                data-feature="SSS Oluşturma"
                                data-example="Online yoga dersleri veriyoruz. Uzman eğitmenlerimizle evden yoga yapabilirsiniz."
                                data-target="test-faq-gen"
                                onclick="toggleTestArea('test-faq-gen', 'SSS Oluşturma', 'Online yoga dersleri veriyoruz. Uzman eğitmenlerimizle evden yoga yapabilirsiniz.')"
                            >
                                <i class="ti ti-rocket me-1"></i> Canlı Test Et
                            </button>
                            
                            <!-- Test Alanı Accordion -->
                            <div class="accordion mt-3" id="test-faq-gen" style="display: none;">
                                <div class="accordion-item">
                                    <div class="accordion-header">
                                        <h6 class="mb-0 p-2 bg-primary text-white">
                                            <i class="ti ti-rocket me-2"></i>SSS Oluşturma Testi
                                        </h6>
                                    </div>
                                    <div class="accordion-body">
                                        <!-- Test İçeriği -->
                                        <div class="mb-3">
                                            <label class="form-label"><strong>Test Metni:</strong></label>
                                            <textarea class="form-control test-input" rows="4" 
                                                      placeholder="SSS oluşturulacak metni girin...">Online yoga dersleri veriyoruz. Uzman eğitmenlerimizle evden yoga yapabilirsiniz.</textarea>
                                        </div>
                                        
                                        <!-- Hızlı Örnekler -->
                                        <div class="mb-3">
                                            <div class="card bg-light">
                                                <div class="card-body py-2">
                                                    <strong class="small">Hızlı Örnekler:</strong>
                                                    <div class="mt-2">
                                                        <button class="btn btn-sm btn-outline-primary me-1 mb-1 example-btn" 
                                                                data-text="Restoran zincirimizde lezzetli yemekler sunuyoruz. Taze malzemeler kullanarak özel tariflerle hazırlıyoruz.">Restoran</button>
                                                        <button class="btn btn-sm btn-outline-primary me-1 mb-1 example-btn" 
                                                                data-text="Kurumsal eğitim hizmetleri veriyoruz. Uzman kadroyla şirketlere özel eğitim programları düzenliyoruz.">Eğitim</button>
                                                        <button class="btn btn-sm btn-outline-primary me-1 mb-1 example-btn" 
                                                                data-text="E-ticaret platformumuzda binlerce ürün bulabilirsiniz. Hızlı kargo ve güvenli ödeme seçenekleri sunuyoruz.">E-ticaret</button>
                                                        <button class="btn btn-sm btn-outline-danger clear-btn">Temizle</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Test Butonları -->
                                        <div class="d-flex gap-2 mb-3">
                                            <button class="btn btn-outline-secondary demo-test-btn">
                                                <i class="ti ti-flask me-1"></i>Demo Test
                                            </button>
                                            <button class="btn btn-success real-test-btn">
                                                <i class="ti ti-zap me-1"></i>Gerçek AI Test
                                            </button>
                                            <button class="btn btn-outline-danger" onclick="closeTestArea('test-faq-gen')">
                                                <i class="ti ti-x me-1"></i>Kapat
                                            </button>
                                        </div>
                                        
                                        <!-- Sonuç Alanı -->
                                        <div class="result-area" style="display: none;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Eylem Çağrısı -->
                <div class="col-12 col-lg-6 mb-4">
                    <div class="card h-100 border border-success">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title">🎯 Eylem Çağrısı</h5>
                                <span class="badge bg-success">Aktif</span>
                            </div>
                            <p class="text-muted small mb-3">Etkili CTA (Call to Action) metinleri ve buton yazıları oluşturur.</p>
                            <div class="bg-light p-2 rounded mb-3">
                                <strong>Şu işe yarar:</strong> Landing page butonları, e-posta kampanyaları, satış sayfaları, reklam metinleri
                            </div>
                            <div class="mb-3">
                                <code class="small">AI::generateCTA($context, $type)</code>
                            </div>
                            <button 
                                class="btn btn-primary btn-sm w-100 direct-test-btn" 
                                data-feature="Eylem Çağrısı"
                                data-example="Organik zeytinyağı üretim çiftliği için satış CTA'ları"
                                data-target="test-cta-gen"
                                onclick="toggleTestArea('test-cta-gen', 'Eylem Çağrısı', 'Organik zeytinyağı üretim çiftliği için satış CTA\'ları')"
                            >
                                <i class="ti ti-rocket me-1"></i> Canlı Test Et
                            </button>
                            
                            <!-- Test Alanı Accordion -->
                            <div class="accordion mt-3" id="test-cta-gen" style="display: none;">
                                <div class="accordion-item">
                                    <div class="accordion-header">
                                        <h6 class="mb-0 p-2 bg-primary text-white">
                                            <i class="ti ti-rocket me-2"></i>Eylem Çağrısı Testi
                                        </h6>
                                    </div>
                                    <div class="accordion-body">
                                        <!-- Test İçeriği -->
                                        <div class="mb-3">
                                            <label class="form-label"><strong>Test Metni:</strong></label>
                                            <textarea class="form-control test-input" rows="4" 
                                                      placeholder="CTA oluşturulacak konu girin...">Organik zeytinyağı üretim çiftliği için satış CTA'ları</textarea>
                                        </div>
                                        
                                        <!-- Hızlı Örnekler -->
                                        <div class="mb-3">
                                            <div class="card bg-light">
                                                <div class="card-body py-2">
                                                    <strong class="small">Hızlı Örnekler:</strong>
                                                    <div class="mt-2">
                                                        <button class="btn btn-sm btn-outline-primary me-1 mb-1 example-btn" 
                                                                data-text="Fitness merkezi üyelik kampanyası CTA butonları">Fitness</button>
                                                        <button class="btn btn-sm btn-outline-primary me-1 mb-1 example-btn" 
                                                                data-text="Online kurs satış sayfası için eylem çağrıları">Eğitim</button>
                                                        <button class="btn btn-sm btn-outline-primary me-1 mb-1 example-btn" 
                                                                data-text="SaaS ürünü için ücretsiz deneme CTA'ları">SaaS</button>
                                                        <button class="btn btn-sm btn-outline-danger clear-btn">Temizle</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Test Butonları -->
                                        <div class="d-flex gap-2 mb-3">
                                            <button class="btn btn-outline-secondary demo-test-btn">
                                                <i class="ti ti-flask me-1"></i>Demo Test
                                            </button>
                                            <button class="btn btn-success real-test-btn">
                                                <i class="ti ti-zap me-1"></i>Gerçek AI Test
                                            </button>
                                            <button class="btn btn-outline-danger" onclick="closeTestArea('test-cta-gen')">
                                                <i class="ti ti-x me-1"></i>Kapat
                                            </button>
                                        </div>
                                        
                                        <!-- Sonuç Alanı -->
                                        <div class="result-area" style="display: none;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SEO Analizi -->
                <div class="col-12 col-lg-6 mb-4">
                    <div class="card h-100 border border-info">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title">🔍 SEO Analizi</h5>
                                <span class="badge bg-info">Aktif</span>
                            </div>
                            <p class="text-muted small mb-3">İçeriğin SEO uyumluluğunu kontrol eder ve öneriler sunar.</p>
                            <div class="bg-light p-2 rounded mb-3">
                                <strong>Şu işe yarar:</strong> On-page SEO optimizasyonu, anahtar kelime analizi, içerik iyileştirmeleri
                            </div>
                            <div class="mb-3">
                                <code class="small">AI::analyzeSEO($content, $keyword)</code>
                            </div>
                            <button 
                                class="btn btn-primary btn-sm w-100 direct-test-btn" 
                                data-feature="SEO Analizi"
                                data-example="Web sitesi performans optimizasyonu rehberi: Hızlandırma teknikleri ve best practice'ler"
                                data-target="test-seo-analysis"
                                onclick="toggleTestArea('test-seo-analysis', 'SEO Analizi', 'Web sitesi performans optimizasyonu rehberi: Hızlandırma teknikleri ve best practice'ler')"
                            >
                                <i class="ti ti-rocket me-1"></i> Canlı Test Et
                            </button>
                            
                            <!-- Test Alanı Accordion -->
                            <div class="accordion mt-3" id="test-seo-analysis" style="display: none;">
                                <div class="accordion-item">
                                    <div class="accordion-header">
                                        <h6 class="mb-0 p-2 bg-primary text-white">
                                            <i class="ti ti-rocket me-2"></i>SEO Analizi Testi
                                        </h6>
                                    </div>
                                    <div class="accordion-body">
                                        <!-- Test İçeriği -->
                                        <div class="mb-3">
                                            <label class="form-label"><strong>Test Metni:</strong></label>
                                            <textarea class="form-control test-input" rows="4" 
                                                      placeholder="SEO analizi yapılacak içerik...">Web sitesi performans optimizasyonu rehberi: Hızlandırma teknikleri ve best practice'ler</textarea>
                                        </div>
                                        
                                        <!-- Test Butonları -->
                                        <div class="d-flex gap-2 mb-3">
                                            <button class="btn btn-outline-secondary demo-test-btn">
                                                <i class="ti ti-flask me-1"></i>Demo Test
                                            </button>
                                            <button class="btn btn-success real-test-btn">
                                                <i class="ti ti-zap me-1"></i>Gerçek AI Test
                                            </button>
                                            <button class="btn btn-outline-danger" onclick="closeTestArea('test-seo-analysis')">
                                                <i class="ti ti-x me-1"></i>Kapat
                                            </button>
                                        </div>
                                        
                                        <!-- Sonuç Alanı -->
                                        <div class="result-area" style="display: none;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Okunabilirlik Analizi -->
                <div class="col-12 col-lg-6 mb-4">
                    <div class="card h-100 border border-info">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title">📖 Okunabilirlik Analizi</h5>
                                <span class="badge bg-info">Aktif</span>
                            </div>
                            <p class="text-muted small mb-3">Metnin okunabilirlik skorunu hesaplar ve iyileştirme önerir.</p>
                            <div class="bg-light p-2 rounded mb-3">
                                <strong>Şu işe yarar:</strong> İçerik kalitesi kontrolü, hedef kitle uyumluluğu, eğitim materyalleri
                            </div>
                            <div class="mb-3">
                                <code class="small">AI::analyzeReadability($text)</code>
                            </div>
                            <button 
                                class="btn btn-primary btn-sm w-100 direct-test-btn" 
                                data-feature="Okunabilirlik Analizi"
                                data-example="Modern dağıtık veri tabanı teknolojileri merkezi olmayan yapılar kullanarak güvenli veri saklama çözümleri sunmaktadır."
                                data-target="test-readability"
                                onclick="toggleTestArea('test-readability', 'Okunabilirlik Analizi', 'Modern dağıtık veri tabanı teknolojileri merkezi olmayan yapılar kullanarak güvenli veri saklama çözümleri sunmaktadır.')"
                            >
                                <i class="ti ti-rocket me-1"></i> Canlı Test Et
                            </button>
                            
                            <!-- Test Alanı Accordion -->
                            <div class="accordion mt-3" id="test-readability" style="display: none;">
                                <div class="accordion-item">
                                    <div class="accordion-header">
                                        <h6 class="mb-0 p-2 bg-primary text-white">
                                            <i class="ti ti-rocket me-2"></i>Okunabilirlik Analizi Testi
                                        </h6>
                                    </div>
                                    <div class="accordion-body">
                                        <div class="mb-3">
                                            <label class="form-label"><strong>Test Metni:</strong></label>
                                            <textarea class="form-control test-input" rows="4" 
                                                      placeholder="Okunabilirlik analizi yapılacak metin...">Modern dağıtık veri tabanı teknolojileri merkezi olmayan yapılar kullanarak güvenli veri saklama çözümleri sunmaktadır.</textarea>
                                        </div>
                                        <div class="d-flex gap-2 mb-3">
                                            <button class="btn btn-outline-secondary demo-test-btn">
                                                <i class="ti ti-flask me-1"></i>Demo Test
                                            </button>
                                            <button class="btn btn-success real-test-btn">
                                                <i class="ti ti-zap me-1"></i>Gerçek AI Test
                                            </button>
                                            <button class="btn btn-outline-danger" onclick="closeTestArea('test-readability')">
                                                <i class="ti ti-x me-1"></i>Kapat
                                            </button>
                                        </div>
                                        <div class="result-area" style="display: none;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Anahtar Kelime Çıkarma -->
                <div class="col-12 col-lg-6 mb-4">
                    <div class="card h-100 border border-info">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title">🔑 Anahtar Kelime Çıkarma</h5>
                                <span class="badge bg-info">Aktif</span>
                            </div>
                            <p class="text-muted small mb-3">Metinden önemli anahtar kelimeleri ve terimleri çıkarır.</p>
                            <div class="bg-light p-2 rounded mb-3">
                                <strong>Şu işe yarar:</strong> SEO anahtar kelime analizi, etiketleme, kategorizasyon, içerik planlaması
                            </div>
                            <div class="mb-3">
                                <code class="small">AI::extractKeywords($text, $count)</code>
                            </div>
                            <button 
                                class="btn btn-primary btn-sm w-100 direct-test-btn" 
                                data-feature="Anahtar Kelime Çıkarma"
                                data-example="Organik tarım yöntemleri ile yetiştirilen domates, biber ve patlıcan sebzeleri sağlıklı beslenmenin temel taşlarıdır."
                                data-target="test-keywords"
                                onclick="toggleTestArea('test-keywords', 'Anahtar Kelime Çıkarma', 'Organik tarım yöntemleri ile yetiştirilen domates, biber ve patlıcan sebzeleri sağlıklı beslenmenin temel taşlarıdır.')"
                            >
                                <i class="ti ti-rocket me-1"></i> Canlı Test Et
                            </button>
                            
                            <!-- Test Alanı Accordion -->
                            <div class="accordion mt-3" id="test-keywords" style="display: none;">
                                <div class="accordion-item">
                                    <div class="accordion-header">
                                        <h6 class="mb-0 p-2 bg-primary text-white">
                                            <i class="ti ti-rocket me-2"></i>Anahtar Kelime Çıkarma Testi
                                        </h6>
                                    </div>
                                    <div class="accordion-body">
                                        <div class="mb-3">
                                            <label class="form-label"><strong>Test Metni:</strong></label>
                                            <textarea class="form-control test-input" rows="4" 
                                                      placeholder="Anahtar kelime çıkarılacak metin...">Organik tarım yöntemleri ile yetiştirilen domates, biber ve patlıcan sebzeleri sağlıklı beslenmenin temel taşlarıdır.</textarea>
                                        </div>
                                        <div class="mb-3">
                                            <div class="card bg-light">
                                                <div class="card-body py-2">
                                                    <strong class="small">Hızlı Örnekler:</strong>
                                                    <div class="mt-2">
                                                        <button class="btn btn-sm btn-outline-primary me-1 mb-1 example-btn" 
                                                                data-text="Yapay zeka teknolojilerinin eğitim sektöründe kullanımı öğrenci başarısını artırıyor.">Eğitim</button>
                                                        <button class="btn btn-sm btn-outline-primary me-1 mb-1 example-btn" 
                                                                data-text="Sürdürülebilir enerji kaynakları çevre koruma politikalarının merkezindedir.">Enerji</button>
                                                        <button class="btn btn-sm btn-outline-danger clear-btn">Temizle</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex gap-2 mb-3">
                                            <button class="btn btn-outline-secondary demo-test-btn">
                                                <i class="ti ti-flask me-1"></i>Demo Test
                                            </button>
                                            <button class="btn btn-success real-test-btn">
                                                <i class="ti ti-zap me-1"></i>Gerçek AI Test
                                            </button>
                                            <button class="btn btn-outline-danger" onclick="closeTestArea('test-keywords')">
                                                <i class="ti ti-x me-1"></i>Kapat
                                            </button>
                                        </div>
                                        <div class="result-area" style="display: none;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ton Analizi -->
                <div class="col-12 col-lg-6 mb-4">
                    <div class="card h-100 border border-info">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title">🎭 Ton Analizi</h5>
                                <span class="badge bg-info">Aktif</span>
                            </div>
                            <p class="text-muted small mb-3">İçeriğin tonunu, duygusunu ve yaklaşımını analiz eder.</p>
                            <div class="bg-light p-2 rounded mb-3">
                                <strong>Şu işe yarar:</strong> Marka uyumluluğu kontrolü, müşteri geri bildirim analizi, sosyal medya monitoring
                            </div>
                            <div class="mb-3">
                                <code class="small">AI::analyzeTone($text)</code>
                            </div>
                            <button 
                                class="btn btn-primary btn-sm w-100 direct-test-btn" 
                                data-feature="Ton Analizi"
                                data-example="Merhaba arkadaşlar! Bugün sizlere süper eğlenceli bir tarif getirdim. Kesinlikle denemelisiniz!"
                                data-target="test-tone-analysis"
                                onclick="toggleTestArea('test-tone-analysis', 'Ton Analizi', 'Merhaba arkadaşlar! Bugün sizlere süper eğlenceli bir tarif getirdim. Kesinlikle denemelisiniz!')"
                            >
                                <i class="ti ti-rocket me-1"></i> Canlı Test Et
                            </button>
                            
                            <!-- Test Alanı Accordion -->
                            <div class="accordion mt-3" id="test-tone-analysis" style="display: none;">
                                <div class="accordion-item">
                                    <div class="accordion-header">
                                        <h6 class="mb-0 p-2 bg-primary text-white">
                                            <i class="ti ti-rocket me-2"></i>Ton Analizi Testi
                                        </h6>
                                    </div>
                                    <div class="accordion-body">
                                        <div class="mb-3">
                                            <label class="form-label"><strong>Test Metni:</strong></label>
                                            <textarea class="form-control test-input" rows="4" 
                                                      placeholder="Ton analizi yapılacak metin...">Merhaba arkadaşlar! Bugün sizlere süper eğlenceli bir tarif getirdim. Kesinlikle denemelisiniz!</textarea>
                                        </div>
                                        <div class="mb-3">
                                            <div class="card bg-light">
                                                <div class="card-body py-2">
                                                    <strong class="small">Hızlı Örnekler:</strong>
                                                    <div class="mt-2">
                                                        <button class="btn btn-sm btn-outline-primary me-1 mb-1 example-btn" 
                                                                data-text="Sayın müşterilerimiz, hizmet kalitemizi artırmak için çalışmalarımız devam etmektedir.">Resmi</button>
                                                        <button class="btn btn-sm btn-outline-primary me-1 mb-1 example-btn" 
                                                                data-text="Bu ürün gerçekten harika! Herkese tavsiye ediyorum, çok memnun kaldım.">Olumlu</button>
                                                        <button class="btn btn-sm btn-outline-primary me-1 mb-1 example-btn" 
                                                                data-text="Maalesef beklediğimiz performansı alamadık. Hayal kırıklığı yaşadık.">Olumsuz</button>
                                                        <button class="btn btn-sm btn-outline-danger clear-btn">Temizle</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex gap-2 mb-3">
                                            <button class="btn btn-outline-secondary demo-test-btn">
                                                <i class="ti ti-flask me-1"></i>Demo Test
                                            </button>
                                            <button class="btn btn-success real-test-btn">
                                                <i class="ti ti-zap me-1"></i>Gerçek AI Test
                                            </button>
                                            <button class="btn btn-outline-danger" onclick="closeTestArea('test-tone-analysis')">
                                                <i class="ti ti-x me-1"></i>Kapat
                                            </button>
                                        </div>
                                        <div class="result-area" style="display: none;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Meta Etiket Oluşturma -->
                <div class="col-12 col-lg-6 mb-4">
                    <div class="card h-100 border border-success">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title">🏷️ Meta Etiket Oluşturma</h5>
                                <span class="badge bg-success">Aktif</span>
                            </div>
                            <p class="text-muted small mb-3">SEO uyumlu meta title ve description etiketleri oluşturur.</p>
                            <div class="bg-light p-2 rounded mb-3">
                                <strong>Şu işe yarar:</strong> Web sayfası SEO'su, sosyal medya paylaşım önizlemeleri, arama sonuçları
                            </div>
                            <div class="mb-3">
                                <code class="small">AI::generateMetaTags($title, $content)</code>
                            </div>
                            <button 
                                class="btn btn-primary btn-sm w-100 direct-test-btn" 
                                data-feature="Meta Etiket Oluşturma"
                                data-example="İstanbul'da açılacak yeni müze"
                                data-target="test-meta-tags"
                                onclick="toggleTestArea('test-meta-tags', 'Meta Etiket Oluşturma', 'İstanbul\'da açılacak yeni müze')"
                            >
                                <i class="ti ti-rocket me-1"></i> Canlı Test Et
                            </button>
                            
                            <!-- Test Alanı Accordion -->
                            <div class="accordion mt-3" id="test-meta-tags" style="display: none;">
                                <div class="accordion-item">
                                    <div class="accordion-header">
                                        <h6 class="mb-0 p-2 bg-primary text-white">
                                            <i class="ti ti-rocket me-2"></i>Meta Etiket Oluşturma Testi
                                        </h6>
                                    </div>
                                    <div class="accordion-body">
                                        <div class="mb-3">
                                            <label class="form-label"><strong>Test Metni:</strong></label>
                                            <textarea class="form-control test-input" rows="4" 
                                                      placeholder="Meta etiket oluşturulacak sayfa içeriği...">İstanbul'da açılacak yeni müze</textarea>
                                        </div>
                                        <div class="mb-3">
                                            <div class="card bg-light">
                                                <div class="card-body py-2">
                                                    <strong class="small">Hızlı Örnekler:</strong>
                                                    <div class="mt-2">
                                                        <button class="btn btn-sm btn-outline-primary me-1 mb-1 example-btn" 
                                                                data-text="E-ticaret mağazamızda binlerce ürün bulabilirsiniz">E-ticaret</button>
                                                        <button class="btn btn-sm btn-outline-primary me-1 mb-1 example-btn" 
                                                                data-text="Profesyonel web tasarım hizmetleri sunuyoruz">Hizmet</button>
                                                        <button class="btn btn-sm btn-outline-primary me-1 mb-1 example-btn" 
                                                                data-text="Sağlıklı yaşam için doğal ürünler">Sağlık</button>
                                                        <button class="btn btn-sm btn-outline-danger clear-btn">Temizle</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex gap-2 mb-3">
                                            <button class="btn btn-outline-secondary demo-test-btn">
                                                <i class="ti ti-flask me-1"></i>Demo Test
                                            </button>
                                            <button class="btn btn-success real-test-btn">
                                                <i class="ti ti-zap me-1"></i>Gerçek AI Test
                                            </button>
                                            <button class="btn btn-outline-danger" onclick="closeTestArea('test-meta-tags')">
                                                <i class="ti ti-x me-1"></i>Kapat
                                            </button>
                                        </div>
                                        <div class="result-area" style="display: none;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- İçerik Çevirisi -->
                <div class="col-12 col-lg-6 mb-4">
                    <div class="card h-100 border border-success">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title">🌐 İçerik Çevirisi</h5>
                                <span class="badge bg-success">Aktif</span>
                            </div>
                            <p class="text-muted small mb-3">Çok dilli içerik desteği ve kaliteli çeviri hizmeti sunar.</p>
                            <div class="bg-light p-2 rounded mb-3">
                                <strong>Şu işe yarar:</strong> Çok dilli web siteleri, uluslararası pazarlama, global müşteri desteği
                            </div>
                            <div class="mb-3">
                                <code class="small">AI::translateContent($text, $lang)</code>
                            </div>
                            <button 
                                class="btn btn-primary btn-sm w-100 direct-test-btn" 
                                data-feature="İçerik Çevirisi"
                                data-example="Good morning everyone, welcome to our cooking show"
                                data-target="test-translation"
                                onclick="toggleTestArea('test-translation', 'İçerik Çevirisi', 'Good morning everyone, welcome to our cooking show')"
                            >
                                <i class="ti ti-rocket me-1"></i> Canlı Test Et
                            </button>
                            
                            <!-- Test Alanı Accordion -->
                            <div class="accordion mt-3" id="test-translation" style="display: none;">
                                <div class="accordion-item">
                                    <div class="accordion-header">
                                        <h6 class="mb-0 p-2 bg-primary text-white">
                                            <i class="ti ti-rocket me-2"></i>İçerik Çevirisi Testi
                                        </h6>
                                    </div>
                                    <div class="accordion-body">
                                        <div class="mb-3">
                                            <label class="form-label"><strong>Test Metni:</strong></label>
                                            <textarea class="form-control test-input" rows="4" 
                                                      placeholder="Çevrilecek metni girin...">Good morning everyone, welcome to our cooking show</textarea>
                                        </div>
                                        <div class="mb-3">
                                            <div class="card bg-light">
                                                <div class="card-body py-2">
                                                    <strong class="small">Hızlı Örnekler:</strong>
                                                    <div class="mt-2">
                                                        <button class="btn btn-sm btn-outline-primary me-1 mb-1 example-btn" 
                                                                data-text="Hello, how are you today? I hope you're having a great day!">İngilizce</button>
                                                        <button class="btn btn-sm btn-outline-primary me-1 mb-1 example-btn" 
                                                                data-text="Bonjour tout le monde, bienvenue dans notre émission de cuisine">Fransızca</button>
                                                        <button class="btn btn-sm btn-outline-primary me-1 mb-1 example-btn" 
                                                                data-text="Hola amigos, ¿cómo están hoy? Espero que tengan un buen día">İspanyolca</button>
                                                        <button class="btn btn-sm btn-outline-danger clear-btn">Temizle</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex gap-2 mb-3">
                                            <button class="btn btn-outline-secondary demo-test-btn">
                                                <i class="ti ti-flask me-1"></i>Demo Test
                                            </button>
                                            <button class="btn btn-success real-test-btn">
                                                <i class="ti ti-zap me-1"></i>Gerçek AI Test
                                            </button>
                                            <button class="btn btn-outline-danger" onclick="closeTestArea('test-translation')">
                                                <i class="ti ti-x me-1"></i>Kapat
                                            </button>
                                        </div>
                                        <div class="result-area" style="display: none;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- İçerik Yeniden Yazma -->
                <div class="col-12 col-lg-6 mb-4">
                    <div class="card h-100 border border-success">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title">✏️ İçerik Yeniden Yazma</h5>
                                <span class="badge bg-success">Aktif</span>
                            </div>
                            <p class="text-muted small mb-3">Mevcut içeriği farklı ton ve stilde yeniden yazar.</p>
                            <div class="bg-light p-2 rounded mb-3">
                                <strong>Şu işe yarar:</strong> Plagiarism önleme, farklı hedef kitleler için adaptasyon, içerik varyasyonları
                            </div>
                            <div class="mb-3">
                                <code class="small">AI::rewriteContent($text, $tone)</code>
                            </div>
                            <button 
                                class="btn btn-primary btn-sm w-100 direct-test-btn" 
                                data-feature="İçerik Yeniden Yazma"
                                data-example="Bu ürün çok kaliteli ve fiyatı uygun"
                                data-target="test-rewrite"
                                onclick="toggleTestArea('test-rewrite', 'İçerik Yeniden Yazma', 'Bu ürün çok kaliteli ve fiyatı uygun')"
                            >
                                <i class="ti ti-rocket me-1"></i> Canlı Test Et
                            </button>
                            
                            <!-- Test Alanı Accordion -->
                            <div class="accordion mt-3" id="test-rewrite" style="display: none;">
                                <div class="accordion-item">
                                    <div class="accordion-header">
                                        <h6 class="mb-0 p-2 bg-primary text-white">
                                            <i class="ti ti-rocket me-2"></i>İçerik Yeniden Yazma Testi
                                        </h6>
                                    </div>
                                    <div class="accordion-body">
                                        <div class="mb-3">
                                            <label class="form-label"><strong>Test Metni:</strong></label>
                                            <textarea class="form-control test-input" rows="4" 
                                                      placeholder="Yeniden yazılacak metni girin...">Bu ürün çok kaliteli ve fiyatı uygun</textarea>
                                        </div>
                                        <div class="mb-3">
                                            <div class="card bg-light">
                                                <div class="card-body py-2">
                                                    <strong class="small">Hızlı Örnekler:</strong>
                                                    <div class="mt-2">
                                                        <button class="btn btn-sm btn-outline-primary me-1 mb-1 example-btn" 
                                                                data-text="Firmamız müşteri memnuniyetini her zaman ön planda tutar ve kaliteli hizmet sunar.">Resmi</button>
                                                        <button class="btn btn-sm btn-outline-primary me-1 mb-1 example-btn" 
                                                                data-text="Hey arkadaşlar! Bu ürün gerçekten süper, mutlaka denemelisiniz!">Samimi</button>
                                                        <button class="btn btn-sm btn-outline-primary me-1 mb-1 example-btn" 
                                                                data-text="Teknolojik ilerlemeler sayesinde modern çözümler sunuyoruz.">Profesyonel</button>
                                                        <button class="btn btn-sm btn-outline-danger clear-btn">Temizle</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex gap-2 mb-3">
                                            <button class="btn btn-outline-secondary demo-test-btn">
                                                <i class="ti ti-flask me-1"></i>Demo Test
                                            </button>
                                            <button class="btn btn-success real-test-btn">
                                                <i class="ti ti-zap me-1"></i>Gerçek AI Test
                                            </button>
                                            <button class="btn btn-outline-danger" onclick="closeTestArea('test-rewrite')">
                                                <i class="ti ti-x me-1"></i>Kapat
                                            </button>
                                        </div>
                                        <div class="result-area" style="display: none;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- İçerik Genişletme -->
                <div class="col-12 col-lg-6 mb-4">
                    <div class="card h-100 border border-success">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title">📈 İçerik Genişletme</h5>
                                <span class="badge bg-success">Aktif</span>
                            </div>
                            <p class="text-muted small mb-3">Kısa içerikleri detaylandırır ve genişletir.</p>
                            <div class="bg-light p-2 rounded mb-3">
                                <strong>Şu işe yarar:</strong> Blog yazısı genişletme, ürün açıklaması detaylandırma, akademik yazılar
                            </div>
                            <div class="mb-3">
                                <code class="small">AI::expandContent($text, $target_length)</code>
                            </div>
                            <button 
                                class="btn btn-primary btn-sm w-100 direct-test-btn" 
                                data-feature="İçerik Genişletme"
                                data-example="Kahve sağlıklıdır"
                                data-target="test-expand"
                                onclick="toggleTestArea('test-expand', 'İçerik Genişletme', 'Kahve sağlıklıdır')"
                            >
                                <i class="ti ti-rocket me-1"></i> Canlı Test Et
                            </button>
                            
                            <!-- Test Alanı Accordion -->
                            <div class="accordion mt-3" id="test-expand" style="display: none;">
                                <div class="accordion-item">
                                    <div class="accordion-header">
                                        <h6 class="mb-0 p-2 bg-primary text-white">
                                            <i class="ti ti-rocket me-2"></i>İçerik Genişletme Testi
                                        </h6>
                                    </div>
                                    <div class="accordion-body">
                                        <div class="mb-3">
                                            <label class="form-label"><strong>Test Metni:</strong></label>
                                            <textarea class="form-control test-input" rows="4" 
                                                      placeholder="Genişletilecek kısa metni girin...">Kahve sağlıklıdır</textarea>
                                        </div>
                                        <div class="mb-3">
                                            <div class="card bg-light">
                                                <div class="card-body py-2">
                                                    <strong class="small">Hızlı Örnekler:</strong>
                                                    <div class="mt-2">
                                                        <button class="btn btn-sm btn-outline-primary me-1 mb-1 example-btn" 
                                                                data-text="Egzersiz önemlidir">Sağlık</button>
                                                        <button class="btn btn-sm btn-outline-primary me-1 mb-1 example-btn" 
                                                                data-text="Teknoloji hayatımızı kolaylaştırır">Teknoloji</button>
                                                        <button class="btn btn-sm btn-outline-primary me-1 mb-1 example-btn" 
                                                                data-text="Eğitim geleceğin temelidir">Eğitim</button>
                                                        <button class="btn btn-sm btn-outline-danger clear-btn">Temizle</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex gap-2 mb-3">
                                            <button class="btn btn-outline-secondary demo-test-btn">
                                                <i class="ti ti-flask me-1"></i>Demo Test
                                            </button>
                                            <button class="btn btn-success real-test-btn">
                                                <i class="ti ti-zap me-1"></i>Gerçek AI Test
                                            </button>
                                            <button class="btn btn-outline-danger" onclick="closeTestArea('test-expand')">
                                                <i class="ti ti-x me-1"></i>Kapat
                                            </button>
                                        </div>
                                        <div class="result-area" style="display: none;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- EK AI ÖZELLİKLERİ -->
    <div class="card mb-4">
        <div class="card-header bg-info text-white">
            <h3 class="card-title mb-0">
                <i class="ti ti-plus-circle me-2"></i>
                Ek AI Özellikleri (29 Özellik)
            </h3>
        </div>
        <div class="card-body">
            <div class="row">
                
                <!-- Sosyal Medya İçeriği -->
                <div class="col-12 col-lg-6 mb-4">
                    <div class="card h-100 border border-success">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title">📱 Sosyal Medya İçeriği</h5>
                                <span class="badge bg-success">Aktif</span>
                            </div>
                            <p class="text-muted small mb-3">Instagram, Twitter, Facebook için otomatik post oluşturur.</p>
                            <div class="bg-light p-2 rounded mb-3">
                                <strong>Şu işe yarar:</strong> Sosyal medya yönetimi, içerik takvimi, hashtag önerileri, post optimizasyonu
                            </div>
                            <div class="mb-3">
                                <code class="small">AI::generateSocialPost($platform, $content)</code>
                            </div>
                            <button 
                                class="btn btn-primary btn-sm w-100 direct-test-btn" 
                                data-feature="Sosyal Medya İçeriği"
                                data-example="Yeni ürünümüzü tanıtmak istiyoruz"
                                data-target="test-social"
                                onclick="toggleTestArea('test-social', 'Sosyal Medya İçeriği', 'Yeni ürünümüzü tanıtmak istiyoruz')"
                            >
                                <i class="ti ti-rocket me-1"></i> Canlı Test Et
                            </button>
                            
                            <!-- Test Alanı Accordion -->
                            <div class="accordion mt-3" id="test-social" style="display: none;">
                                <div class="accordion-item">
                                    <div class="accordion-header">
                                        <h6 class="mb-0 p-2 bg-primary text-white">
                                            <i class="ti ti-rocket me-2"></i>Sosyal Medya İçeriği Testi
                                        </h6>
                                    </div>
                                    <div class="accordion-body">
                                        <div class="mb-3">
                                            <label class="form-label"><strong>Test Metni:</strong></label>
                                            <textarea class="form-control test-input" rows="4" 
                                                      placeholder="Sosyal medya postu için konu girin...">Yeni ürünümüzü tanıtmak istiyoruz</textarea>
                                        </div>
                                        <div class="mb-3">
                                            <div class="card bg-light">
                                                <div class="card-body py-2">
                                                    <strong class="small">Hızlı Örnekler:</strong>
                                                    <div class="mt-2">
                                                        <button class="btn btn-sm btn-outline-primary me-1 mb-1 example-btn" 
                                                                data-text="Yeni kahve çeşidimizi denemeye davetlisiniz!">Ürün Tanıtım</button>
                                                        <button class="btn btn-sm btn-outline-primary me-1 mb-1 example-btn" 
                                                                data-text="Pazartesi motivasyonu için ilham verici söz">Motivasyon</button>
                                                        <button class="btn btn-sm btn-outline-primary me-1 mb-1 example-btn" 
                                                                data-text="Ekibimizle birlikte keyifli çalışma anları">Şirket Kültürü</button>
                                                        <button class="btn btn-sm btn-outline-danger clear-btn">Temizle</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex gap-2 mb-3">
                                            <button class="btn btn-outline-secondary demo-test-btn">
                                                <i class="ti ti-flask me-1"></i>Demo Test
                                            </button>
                                            <button class="btn btn-success real-test-btn">
                                                <i class="ti ti-zap me-1"></i>Gerçek AI Test
                                            </button>
                                            <button class="btn btn-outline-danger" onclick="closeTestArea('test-social')">
                                                <i class="ti ti-x me-1"></i>Kapat
                                            </button>
                                        </div>
                                        <div class="result-area" style="display: none;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- E-posta Kampanyası -->
                <div class="col-12 col-lg-6 mb-4">
                    <div class="card h-100 border border-success">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title">📧 E-posta Kampanyası</h5>
                                <span class="badge bg-success">Aktif</span>
                            </div>
                            <p class="text-muted small mb-3">Newsletter ve pazarlama e-postaları oluşturur.</p>
                            <div class="bg-light p-2 rounded mb-3">
                                <strong>Şu işe yarar:</strong> E-posta pazarlaması, müşteri bülteneri, otomatik e-posta dizileri
                            </div>
                            <div class="mb-3">
                                <code class="small">AI::generateEmailCampaign($type, $audience)</code>
                            </div>
                            <button 
                                class="btn btn-primary btn-sm w-100 direct-test-btn" 
                                data-feature="E-posta Kampanyası"
                                data-example="Müşterilerimize yeni ürün koleksiyonunu tanıtmak istiyoruz"
                                data-target="test-email"
                                onclick="toggleTestArea('test-email', 'E-posta Kampanyası', 'Müşterilerimize yeni ürün koleksiyonunu tanıtmak istiyoruz')"
                            >
                                <i class="fas fa-rocket me-1"></i> Canlı Test Et
                            </button>
                            
                            <!-- Test Alanı Accordion -->
                            <div class="accordion mt-3" id="test-email" style="display: none;">
                                <div class="accordion-item">
                                    <div class="accordion-header">
                                        <h6 class="mb-0 p-2 bg-primary text-white">
                                            <i class="fas fa-rocket me-2"></i>E-posta Kampanyası Testi
                                        </h6>
                                    </div>
                                    <div class="accordion-body">
                                        <div class="mb-3">
                                            <label class="form-label"><strong>Test Metni:</strong></label>
                                            <textarea class="form-control test-input" rows="4" 
                                                      placeholder="E-posta kampanyası için konu girin...">Müşterilerimize yeni ürün koleksiyonunu tanıtmak istiyoruz</textarea>
                                        </div>
                                        <div class="mb-3">
                                            <div class="card bg-light">
                                                <div class="card-body py-2">
                                                    <strong class="small">Hızlı Örnekler:</strong>
                                                    <div class="mt-2">
                                                        <button class="btn btn-sm btn-outline-primary me-1 mb-1 example-btn" 
                                                                data-text="Bahar koleksiyonu için %20 indirim fırsatı">İndirim Kampanyası</button>
                                                        <button class="btn btn-sm btn-outline-primary me-1 mb-1 example-btn" 
                                                                data-text="Yeni üyelere hoş geldin mesajı">Hoş Geldin E-postası</button>
                                                        <button class="btn btn-sm btn-outline-primary me-1 mb-1 example-btn" 
                                                                data-text="Aylık haber bülteni hazırlamak istiyoruz">Newsletter</button>
                                                        <button class="btn btn-sm btn-outline-danger clear-btn">Temizle</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex gap-2 mb-3">
                                            <button class="btn btn-outline-secondary demo-test-btn">
                                                <i class="fas fa-flask me-1"></i>Demo Test
                                            </button>
                                            <button class="btn btn-success real-test-btn">
                                                <i class="fas fa-bolt me-1"></i>Gerçek AI Test
                                            </button>
                                            <button class="btn btn-outline-danger" onclick="closeTestArea('test-email')">
                                                <i class="fas fa-times me-1"></i>Kapat
                                            </button>
                                        </div>
                                        <div class="result-area" style="display: none;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ürün Açıklaması -->
                <div class="col-12 col-lg-6 mb-4">
                    <div class="card h-100 border border-success">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title">🛍️ Ürün Açıklaması</h5>
                                <span class="badge bg-success">Aktif</span>
                            </div>
                            <p class="text-muted small mb-3">E-ticaret ürün sayfaları için içerik oluşturur.</p>
                            <div class="bg-light p-2 rounded mb-3">
                                <strong>Şu işe yarar:</strong> Online mağaza ürünleri, katalog yazımı, satış odaklı açıklamalar
                            </div>
                            <div class="mb-3">
                                <code class="small">AI::generateProductDescription($product)</code>
                            </div>
                            <button 
                                class="btn btn-primary btn-sm w-100 direct-test-btn" 
                                data-feature="Ürün Açıklaması"
                                data-example="Akıllı telefon - 256GB hafıza, 48MP kamera"
                                data-target="test-product"
                                onclick="toggleTestArea('test-product', 'Ürün Açıklaması', 'Akıllı telefon - 256GB hafıza, 48MP kamera')"
                            >
                                <i class="fas fa-rocket me-1"></i> Canlı Test Et
                            </button>
                            
                            <!-- Test Alanı Accordion -->
                            <div class="accordion mt-3" id="test-product" style="display: none;">
                                <div class="accordion-item">
                                    <div class="accordion-header">
                                        <h6 class="mb-0 p-2 bg-primary text-white">
                                            <i class="fas fa-rocket me-2"></i>Ürün Açıklaması Testi
                                        </h6>
                                    </div>
                                    <div class="accordion-body">
                                        <div class="mb-3">
                                            <label class="form-label"><strong>Test Metni:</strong></label>
                                            <textarea class="form-control test-input" rows="4" 
                                                      placeholder="Ürün bilgilerini girin...">Akıllı telefon - 256GB hafıza, 48MP kamera</textarea>
                                        </div>
                                        <div class="mb-3">
                                            <div class="card bg-light">
                                                <div class="card-body py-2">
                                                    <strong class="small">Hızlı Örnekler:</strong>
                                                    <div class="mt-2">
                                                        <button class="btn btn-sm btn-outline-primary me-1 mb-1 example-btn" 
                                                                data-text="Çok amaçlı elektrikli kahve makinesi">Ev Aletleri</button>
                                                        <button class="btn btn-sm btn-outline-primary me-1 mb-1 example-btn" 
                                                                data-text="Deri erkek spor ayakkabı - 42 numara">Ayakkabı</button>
                                                        <button class="btn btn-sm btn-outline-primary me-1 mb-1 example-btn" 
                                                                data-text="Organik bebek maması - 6 ay+">Bebek Ürünleri</button>
                                                        <button class="btn btn-sm btn-outline-danger clear-btn">Temizle</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex gap-2 mb-3">
                                            <button class="btn btn-outline-secondary demo-test-btn">
                                                <i class="fas fa-flask me-1"></i>Demo Test
                                            </button>
                                            <button class="btn btn-success real-test-btn">
                                                <i class="fas fa-bolt me-1"></i>Gerçek AI Test
                                            </button>
                                            <button class="btn btn-outline-danger" onclick="closeTestArea('test-product')">
                                                <i class="fas fa-times me-1"></i>Kapat
                                            </button>
                                        </div>
                                        <div class="result-area" style="display: none;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Video Senaryosu -->
                <div class="col-12 col-lg-6 mb-4">
                    <div class="card h-100 border border-success">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title">🎬 Video Senaryosu</h5>
                                <span class="badge bg-success">Aktif</span>
                            </div>
                            <p class="text-muted small mb-3">YouTube ve TikTok video içerikleri oluşturur.</p>
                            <div class="bg-light p-2 rounded mb-3">
                                <strong>Şu işe yarar:</strong> Video pazarlama, eğitici içerikler, viral video fikirleri
                            </div>
                            <div class="mb-3">
                                <code class="small">AI::generateVideoScript($topic, $duration)</code>
                            </div>
                            <button 
                                class="btn btn-primary btn-sm w-100 direct-test-btn" 
                                data-feature="Video Senaryosu"
                                data-example="5 dakikalık yemek tarifi videosu hazırlamak istiyoruz"
                                data-target="test-video"
                                onclick="toggleTestArea('test-video', 'Video Senaryosu', '5 dakikalık yemek tarifi videosu hazırlamak istiyoruz')"
                            >
                                <i class="fas fa-rocket me-1"></i> Canlı Test Et
                            </button>
                            
                            <!-- Test Alanı Accordion -->
                            <div class="accordion mt-3" id="test-video" style="display: none;">
                                <div class="accordion-item">
                                    <div class="accordion-header">
                                        <h6 class="mb-0 p-2 bg-primary text-white">
                                            <i class="fas fa-rocket me-2"></i>Video Senaryosu Testi
                                        </h6>
                                    </div>
                                    <div class="accordion-body">
                                        <div class="mb-3">
                                            <label class="form-label"><strong>Test Metni:</strong></label>
                                            <textarea class="form-control test-input" rows="4" 
                                                      placeholder="Video konusu ve süresini girin...">5 dakikalık yemek tarifi videosu hazırlamak istiyoruz</textarea>
                                        </div>
                                        <div class="mb-3">
                                            <div class="card bg-light">
                                                <div class="card-body py-2">
                                                    <strong class="small">Hızlı Örnekler:</strong>
                                                    <div class="mt-2">
                                                        <button class="btn btn-sm btn-outline-primary me-1 mb-1 example-btn" 
                                                                data-text="Ürün tanıtım videosu - 2 dakika">Ürün Tanıtım</button>
                                                        <button class="btn btn-sm btn-outline-primary me-1 mb-1 example-btn" 
                                                                data-text="Şirket tanıtım videosu - 3 dakika">Kurumsal</button>
                                                        <button class="btn btn-sm btn-outline-primary me-1 mb-1 example-btn" 
                                                                data-text="Eğitim videosu - 10 dakika">Eğitim</button>
                                                        <button class="btn btn-sm btn-outline-danger clear-btn">Temizle</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex gap-2 mb-3">
                                            <button class="btn btn-outline-secondary demo-test-btn">
                                                <i class="fas fa-flask me-1"></i>Demo Test
                                            </button>
                                            <button class="btn btn-success real-test-btn">
                                                <i class="fas fa-bolt me-1"></i>Gerçek AI Test
                                            </button>
                                            <button class="btn btn-outline-danger" onclick="closeTestArea('test-video')">
                                                <i class="fas fa-times me-1"></i>Kapat
                                            </button>
                                        </div>
                                        <div class="result-area" style="display: none;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Podcast Notları -->
                <div class="col-12 col-lg-6 mb-4">
                    <div class="card h-100 border border-success">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title">🎙️ Podcast Notları</h5>
                                <span class="badge bg-success">Aktif</span>
                            </div>
                            <p class="text-muted small mb-3">Podcast bölümleri için show notes oluşturur.</p>
                            <div class="bg-light p-2 rounded mb-3">
                                <strong>Şu işe yarar:</strong> Podcast web siteleri, bölüm özetleri, konuk tanıtımları
                            </div>
                            <div class="mb-3">
                                <code class="small">AI::generatePodcastNotes($transcript)</code>
                            </div>
                            <button 
                                class="btn btn-primary btn-sm w-100 direct-test-btn" 
                                data-feature="Podcast Notları"
                                data-example="Teknoloji konulu podcast bölümü için show notes"
                                data-target="test-podcast"
                                onclick="toggleTestArea('test-podcast', 'Podcast Notları', 'Teknoloji konulu podcast bölümü için show notes')"
                            >
                                <i class="fas fa-rocket me-1"></i> Canlı Test Et
                            </button>
                            
                            <!-- Test Alanı Accordion -->
                            <div class="accordion mt-3" id="test-podcast" style="display: none;">
                                <div class="accordion-item">
                                    <div class="accordion-header">
                                        <h6 class="mb-0 p-2 bg-primary text-white">
                                            <i class="fas fa-rocket me-2"></i>Podcast Notları Testi
                                        </h6>
                                    </div>
                                    <div class="accordion-body">
                                        <div class="mb-3">
                                            <label class="form-label"><strong>Test Metni:</strong></label>
                                            <textarea class="form-control test-input" rows="4" 
                                                      placeholder="Podcast konusu ve detaylarını girin...">Teknoloji konulu podcast bölümü için show notes</textarea>
                                        </div>
                                        <div class="mb-3">
                                            <div class="card bg-light">
                                                <div class="card-body py-2">
                                                    <strong class="small">Hızlı Örnekler:</strong>
                                                    <div class="mt-2">
                                                        <button class="btn btn-sm btn-outline-primary me-1 mb-1 example-btn" 
                                                                data-text="Girişimcilik konulu röportaj bölümü">Girişimcilik</button>
                                                        <button class="btn btn-sm btn-outline-primary me-1 mb-1 example-btn" 
                                                                data-text="Sağlık ve beslenme üzerine konuşma">Sağlık</button>
                                                        <button class="btn btn-sm btn-outline-primary me-1 mb-1 example-btn" 
                                                                data-text="Eğitim sistemi tartışma bölümü">Eğitim</button>
                                                        <button class="btn btn-sm btn-outline-danger clear-btn">Temizle</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex gap-2 mb-3">
                                            <button class="btn btn-outline-secondary demo-test-btn">
                                                <i class="fas fa-flask me-1"></i>Demo Test
                                            </button>
                                            <button class="btn btn-success real-test-btn">
                                                <i class="fas fa-bolt me-1"></i>Gerçek AI Test
                                            </button>
                                            <button class="btn btn-outline-danger" onclick="closeTestArea('test-podcast')">
                                                <i class="fas fa-times me-1"></i>Kapat
                                            </button>
                                        </div>
                                        <div class="result-area" style="display: none;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Haber Makalesi -->
                <div class="col-12 col-lg-6 mb-4">
                    <div class="card h-100 border border-success">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title">📰 Haber Makalesi</h5>
                                <span class="badge bg-success">Aktif</span>
                            </div>
                            <p class="text-muted small mb-3">Güncel olaylar ve haber içeriği oluşturur.</p>
                            <div class="bg-light p-2 rounded mb-3">
                                <strong>Şu işe yarar:</strong> Haber siteleri, kurumsal duyurular, basın bültenleri
                            </div>
                            <div class="mb-3">
                                <code class="small">AI::generateNewsArticle($topic, $style)</code>
                            </div>
                            <button 
                                class="btn btn-primary btn-sm w-100 direct-test-btn" 
                                data-feature="Haber Makalesi"
                                data-example="Şehrin yeni teknoloji parkı açılışı haberi"
                                data-target="test-news"
                                onclick="toggleTestArea('test-news', 'Haber Makalesi', 'Şehrin yeni teknoloji parkı açılışı haberi')"
                            >
                                <i class="fas fa-rocket me-1"></i> Canlı Test Et
                            </button>
                            
                            <!-- Test Alanı Accordion -->
                            <div class="accordion mt-3" id="test-news" style="display: none;">
                                <div class="accordion-item">
                                    <div class="accordion-header">
                                        <h6 class="mb-0 p-2 bg-primary text-white">
                                            <i class="fas fa-rocket me-2"></i>Haber Makalesi Testi
                                        </h6>
                                    </div>
                                    <div class="accordion-body">
                                        <div class="mb-3">
                                            <label class="form-label"><strong>Test Metni:</strong></label>
                                            <textarea class="form-control test-input" rows="4" 
                                                      placeholder="Haber konusunu girin...">Şehrin yeni teknoloji parkı açılışı haberi</textarea>
                                        </div>
                                        <div class="mb-3">
                                            <div class="card bg-light">
                                                <div class="card-body py-2">
                                                    <strong class="small">Hızlı Örnekler:</strong>
                                                    <div class="mt-2">
                                                        <button class="btn btn-sm btn-outline-primary me-1 mb-1 example-btn" 
                                                                data-text="Şirket yeni ürün lansmanı duyurusu">Kurumsal Duyuru</button>
                                                        <button class="btn btn-sm btn-outline-primary me-1 mb-1 example-btn" 
                                                                data-text="Yerel festival etkinliği haberi">Etkinlik</button>
                                                        <button class="btn btn-sm btn-outline-primary me-1 mb-1 example-btn" 
                                                                data-text="Eğitim sektörü gelişmeleri raporu">Sektör Haberi</button>
                                                        <button class="btn btn-sm btn-outline-danger clear-btn">Temizle</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex gap-2 mb-3">
                                            <button class="btn btn-outline-secondary demo-test-btn">
                                                <i class="fas fa-flask me-1"></i>Demo Test
                                            </button>
                                            <button class="btn btn-success real-test-btn">
                                                <i class="fas fa-bolt me-1"></i>Gerçek AI Test
                                            </button>
                                            <button class="btn btn-outline-danger" onclick="closeTestArea('test-news')">
                                                <i class="fas fa-times me-1"></i>Kapat
                                            </button>
                                        </div>
                                        <div class="result-area" style="display: none;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- AI Chat Botu -->
                <div class="col-12 col-lg-6 mb-4">
                    <div class="card h-100 border border-warning">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title">🤖 AI Chat Botu</h5>
                                <span class="badge bg-warning">Aktif</span>
                            </div>
                            <p class="text-muted small mb-3">Otomatik müşteri desteği ve canlı sohbet sistemi.</p>
                            <div class="bg-light p-2 rounded mb-3">
                                <strong>Şu işe yarar:</strong> Müşteri hizmetleri, FAQ otomasyonu, 7/24 destek
                            </div>
                            <div class="mb-3">
                                <code class="small">AI::generateChatResponse($message, $context)</code>
                            </div>
                            <button 
                                class="btn btn-primary btn-sm w-100 direct-test-btn" 
                                data-feature="AI Chat Botu"
                                data-example="Ürününüzün fiyatı nedir ve ne zaman teslimat yapıyorsunuz?"
                                data-target="test-chatbot"
                                onclick="toggleTestArea('test-chatbot', 'AI Chat Botu', 'Ürününüzün fiyatı nedir ve ne zaman teslimat yapıyorsunuz?')"
                            >
                                <i class="fas fa-rocket me-1"></i> Canlı Test Et
                            </button>
                            
                            <!-- Test Alanı Accordion -->
                            <div class="accordion mt-3" id="test-chatbot" style="display: none;">
                                <div class="accordion-item">
                                    <div class="accordion-header">
                                        <h6 class="mb-0 p-2 bg-primary text-white">
                                            <i class="fas fa-rocket me-2"></i>AI Chat Botu Testi
                                        </h6>
                                    </div>
                                    <div class="accordion-body">
                                        <div class="mb-3">
                                            <label class="form-label"><strong>Test Metni:</strong></label>
                                            <textarea class="form-control test-input" rows="4" 
                                                      placeholder="Müşteri sorusunu girin...">Ürününüzün fiyatı nedir ve ne zaman teslimat yapıyorsunuz?</textarea>
                                        </div>
                                        <div class="mb-3">
                                            <div class="card bg-light">
                                                <div class="card-body py-2">
                                                    <strong class="small">Hızlı Örnekler:</strong>
                                                    <div class="mt-2">
                                                        <button class="btn btn-sm btn-outline-primary me-1 mb-1 example-btn" 
                                                                data-text="İade politikanız nasıl çalışıyor?">İade Politikası</button>
                                                        <button class="btn btn-sm btn-outline-primary me-1 mb-1 example-btn" 
                                                                data-text="Teknik destek nasıl alabilirim?">Teknik Destek</button>
                                                        <button class="btn btn-sm btn-outline-primary me-1 mb-1 example-btn" 
                                                                data-text="Hesabımı nasıl güncelleyebilirim?">Hesap Yönetimi</button>
                                                        <button class="btn btn-sm btn-outline-danger clear-btn">Temizle</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex gap-2 mb-3">
                                            <button class="btn btn-outline-secondary demo-test-btn">
                                                <i class="fas fa-flask me-1"></i>Demo Test
                                            </button>
                                            <button class="btn btn-success real-test-btn">
                                                <i class="fas fa-bolt me-1"></i>Gerçek AI Test
                                            </button>
                                            <button class="btn btn-outline-danger" onclick="closeTestArea('test-chatbot')">
                                                <i class="fas fa-times me-1"></i>Kapat
                                            </button>
                                        </div>
                                        <div class="result-area" style="display: none;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Görsel Analizi -->
                <div class="col-12 col-lg-6 mb-4">
                    <div class="card h-100 border border-warning">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title">🖼️ Görsel Analizi</h5>
                                <span class="badge bg-warning">Aktif</span>
                            </div>
                            <p class="text-muted small mb-3">Resim ve logo açıklaması ve görsel içerik analizi.</p>
                            <div class="bg-light p-2 rounded mb-3">
                                <strong>Şu işe yarar:</strong> Resim SEO, görsel kataloglama, erişebilirlik metinleri
                            </div>
                            <div class="mb-3">
                                <code class="small">AI::analyzeImage($imageUrl, $context)</code>
                            </div>
                            <button 
                                class="btn btn-primary btn-sm w-100 direct-test-btn" 
                                data-feature="Görsel Analizi"
                                data-example="Bu görselin içeriğini ve detaylarını analiz edin"
                                data-target="test-imageanalysis"
                                onclick="toggleTestArea('test-imageanalysis', 'Görsel Analizi', 'Bu görselin içeriğini ve detaylarını analiz edin')"
                            >
                                <i class="fas fa-rocket me-1"></i> Canlı Test Et
                            </button>
                            
                            <!-- Test Alanı Accordion -->
                            <div class="accordion mt-3" id="test-imageanalysis" style="display: none;">
                                <div class="accordion-item">
                                    <div class="accordion-header">
                                        <h6 class="mb-0 p-2 bg-primary text-white">
                                            <i class="fas fa-rocket me-2"></i>Görsel Analizi Testi
                                        </h6>
                                    </div>
                                    <div class="accordion-body">
                                        <div class="mb-3">
                                            <label class="form-label"><strong>Test Metni:</strong></label>
                                            <textarea class="form-control test-input" rows="4" 
                                                      placeholder="Görsel URL'si veya açıklama isteği girin...">Bu görselin içeriğini ve detaylarını analiz edin</textarea>
                                        </div>
                                        <div class="mb-3">
                                            <div class="card bg-light">
                                                <div class="card-body py-2">
                                                    <strong class="small">Hızlı Örnekler:</strong>
                                                    <div class="mt-2">
                                                        <button class="btn btn-sm btn-outline-primary me-1 mb-1 example-btn" 
                                                                data-text="Logo tasarımının anlamını ve mesajını açıkla">Logo Analizi</button>
                                                        <button class="btn btn-sm btn-outline-primary me-1 mb-1 example-btn" 
                                                                data-text="Ürün fotoğrafının kalitesini değerlendir">Ürün Görseli</button>
                                                        <button class="btn btn-sm btn-outline-primary me-1 mb-1 example-btn" 
                                                                data-text="İnfografiğin içeriğini metin olarak özetle">İnfografik</button>
                                                        <button class="btn btn-sm btn-outline-danger clear-btn">Temizle</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex gap-2 mb-3">
                                            <button class="btn btn-outline-secondary demo-test-btn">
                                                <i class="fas fa-flask me-1"></i>Demo Test
                                            </button>
                                            <button class="btn btn-success real-test-btn">
                                                <i class="fas fa-bolt me-1"></i>Gerçek AI Test
                                            </button>
                                            <button class="btn btn-outline-danger" onclick="closeTestArea('test-imageanalysis')">
                                                <i class="fas fa-times me-1"></i>Kapat
                                            </button>
                                        </div>
                                        <div class="result-area" style="display: none;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ses Tanıma -->
                <div class="col-12 col-lg-6 mb-4">
                    <div class="card h-100 border border-warning">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title">🎤 Ses Tanıma</h5>
                                <span class="badge bg-warning">Aktif</span>
                            </div>
                            <p class="text-muted small mb-3">Sesten metne çevirme ve ses dosyası analizi.</p>
                            <div class="bg-light p-2 rounded mb-3">
                                <strong>Şu işe yarar:</strong> Toplantı notları, sesli mesajlar, podcast transkriptleri
                            </div>
                            <div class="mb-3">
                                <code class="small">AI::speechToText($audioFile, $language)</code>
                            </div>
                            <button 
                                class="btn btn-primary btn-sm w-100 direct-test-btn" 
                                data-feature="Ses Tanıma"
                                data-example="Bu ses dosyasını metne çevirmek istiyorum"
                                data-target="test-speech"
                                onclick="toggleTestArea('test-speech', 'Ses Tanıma', 'Bu ses dosyasını metne çevirmek istiyorum')"
                            >
                                <i class="fas fa-rocket me-1"></i> Canlı Test Et
                            </button>
                            
                            <!-- Test Alanı Accordion -->
                            <div class="accordion mt-3" id="test-speech" style="display: none;">
                                <div class="accordion-item">
                                    <div class="accordion-header">
                                        <h6 class="mb-0 p-2 bg-primary text-white">
                                            <i class="fas fa-rocket me-2"></i>Ses Tanıma Testi
                                        </h6>
                                    </div>
                                    <div class="accordion-body">
                                        <div class="mb-3">
                                            <label class="form-label"><strong>Test Metni:</strong></label>
                                            <textarea class="form-control test-input" rows="4" 
                                                      placeholder="Ses dosyası URL'si veya transkript isteği girin...">Bu ses dosyasını metne çevirmek istiyorum</textarea>
                                        </div>
                                        <div class="mb-3">
                                            <div class="card bg-light">
                                                <div class="card-body py-2">
                                                    <strong class="small">Hızlı Örnekler:</strong>
                                                    <div class="mt-2">
                                                        <button class="btn btn-sm btn-outline-primary me-1 mb-1 example-btn" 
                                                                data-text="Türkçe toplantı kaydını transkript haline getir">Toplantı Kaydı</button>
                                                        <button class="btn btn-sm btn-outline-primary me-1 mb-1 example-btn" 
                                                                data-text="İngilizce podcast bölümünün metnini çıkar">Podcast</button>
                                                        <button class="btn btn-sm btn-outline-primary me-1 mb-1 example-btn" 
                                                                data-text="Sesli mesajları metin haline dönüştür">Sesli Mesaj</button>
                                                        <button class="btn btn-sm btn-outline-danger clear-btn">Temizle</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex gap-2 mb-3">
                                            <button class="btn btn-outline-secondary demo-test-btn">
                                                <i class="fas fa-flask me-1"></i>Demo Test
                                            </button>
                                            <button class="btn btn-success real-test-btn">
                                                <i class="fas fa-bolt me-1"></i>Gerçek AI Test
                                            </button>
                                            <button class="btn btn-outline-danger" onclick="closeTestArea('test-speech')">
                                                <i class="fas fa-times me-1"></i>Kapat
                                            </button>
                                        </div>
                                        <div class="result-area" style="display: none;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dil Tespiti -->
                <div class="col-12 col-lg-6 mb-4">
                    <div class="card h-100 border border-warning">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title">🌍 Dil Tespiti</h5>
                                <span class="badge bg-warning">Aktif</span>
                            </div>
                            <p class="text-muted small mb-3">Otomatik dil algılama ve çok dilli içerik analizi.</p>
                            <div class="bg-light p-2 rounded mb-3">
                                <strong>Şu işe yarar:</strong> Çok dilli siteler, içerik kategorileme, otomatik çeviri
                            </div>
                            <div class="mb-3">
                                <code class="small">AI::detectLanguage($text, $confidence)</code>
                            </div>
                            <button 
                                class="btn btn-primary btn-sm w-100 direct-test-btn" 
                                data-feature="Dil Tespiti"
                                data-example="Hello world, how are you doing today?"
                                data-target="test-language"
                                onclick="toggleTestArea('test-language', 'Dil Tespiti', 'Hello world, how are you doing today?')"
                            >
                                <i class="fas fa-rocket me-1"></i> Canlı Test Et
                            </button>
                            
                            <!-- Test Alanı Accordion -->
                            <div class="accordion mt-3" id="test-language" style="display: none;">
                                <div class="accordion-item">
                                    <div class="accordion-header">
                                        <h6 class="mb-0 p-2 bg-primary text-white">
                                            <i class="fas fa-rocket me-2"></i>Dil Tespiti Testi
                                        </h6>
                                    </div>
                                    <div class="accordion-body">
                                        <div class="mb-3">
                                            <label class="form-label"><strong>Test Metni:</strong></label>
                                            <textarea class="form-control test-input" rows="4" 
                                                      placeholder="Dili tespit edilecek metni girin...">Hello world, how are you doing today?</textarea>
                                        </div>
                                        <div class="mb-3">
                                            <div class="card bg-light">
                                                <div class="card-body py-2">
                                                    <strong class="small">Hızlı Örnekler:</strong>
                                                    <div class="mt-2">
                                                        <button class="btn btn-sm btn-outline-primary me-1 mb-1 example-btn" 
                                                                data-text="Bonjour, comment allez-vous?">Fransızca</button>
                                                        <button class="btn btn-sm btn-outline-primary me-1 mb-1 example-btn" 
                                                                data-text="¿Cómo está usted hoy?">İspanyolca</button>
                                                        <button class="btn btn-sm btn-outline-primary me-1 mb-1 example-btn" 
                                                                data-text="Guten Tag, wie geht es Ihnen?">Almanca</button>
                                                        <button class="btn btn-sm btn-outline-danger clear-btn">Temizle</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex gap-2 mb-3">
                                            <button class="btn btn-outline-secondary demo-test-btn">
                                                <i class="fas fa-flask me-1"></i>Demo Test
                                            </button>
                                            <button class="btn btn-success real-test-btn">
                                                <i class="fas fa-bolt me-1"></i>Gerçek AI Test
                                            </button>
                                            <button class="btn btn-outline-danger" onclick="closeTestArea('test-language')">
                                                <i class="fas fa-times me-1"></i>Kapat
                                            </button>
                                        </div>
                                        <div class="result-area" style="display: none;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Code Generation -->
                <div class="col-12 col-lg-6 mb-4">
                    <div class="card h-100 border border-warning">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title">💻 Code Generation</h5>
                                <span class="badge bg-warning">Aktif</span>
                            </div>
                            <p class="text-muted small mb-3">Programlama kodu üretme ve algoritma geliştirme.</p>
                            <div class="bg-light p-2 rounded mb-3">
                                <strong>Şu işe yarar:</strong> Kod otomasyonu, algoritma yazımı, fonksiyon üretimi
                            </div>
                            <div class="mb-3">
                                <code class="small">AI::generateCode($language, $description)</code>
                            </div>
                            <button 
                                class="btn btn-primary btn-sm w-100 direct-test-btn" 
                                data-feature="Code Generation"
                                data-example="PHP ile kullanıcı login sistemi yapmak istiyorum"
                                data-target="test-code"
                                onclick="toggleTestArea('test-code', 'Code Generation', 'PHP ile kullanıcı login sistemi yapmak istiyorum')"
                            >
                                <i class="fas fa-rocket me-1"></i> Canlı Test Et
                            </button>
                            
                            <!-- Test Alanı Accordion -->
                            <div class="accordion mt-3" id="test-code" style="display: none;">
                                <div class="accordion-item">
                                    <div class="accordion-header">
                                        <h6 class="mb-0 p-2 bg-primary text-white">
                                            <i class="fas fa-rocket me-2"></i>Code Generation Testi
                                        </h6>
                                    </div>
                                    <div class="accordion-body">
                                        <div class="mb-3">
                                            <label class="form-label"><strong>Test Metni:</strong></label>
                                            <textarea class="form-control test-input" rows="4" 
                                                      placeholder="Yazmak istediğiniz kodun açıklamasını girin...">PHP ile kullanıcı login sistemi yapmak istiyorum</textarea>
                                        </div>
                                        <div class="mb-3">
                                            <div class="card bg-light">
                                                <div class="card-body py-2">
                                                    <strong class="small">Hızlı Örnekler:</strong>
                                                    <div class="mt-2">
                                                        <button class="btn btn-sm btn-outline-primary me-1 mb-1 example-btn" 
                                                                data-text="JavaScript ile form validasyonu">JavaScript</button>
                                                        <button class="btn btn-sm btn-outline-primary me-1 mb-1 example-btn" 
                                                                data-text="Python ile API endpoint yazma">Python API</button>
                                                        <button class="btn btn-sm btn-outline-primary me-1 mb-1 example-btn" 
                                                                data-text="MySQL veritabanı için CRUD işlemleri">SQL CRUD</button>
                                                        <button class="btn btn-sm btn-outline-danger clear-btn">Temizle</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex gap-2 mb-3">
                                            <button class="btn btn-outline-secondary demo-test-btn">
                                                <i class="fas fa-flask me-1"></i>Demo Test
                                            </button>
                                            <button class="btn btn-success real-test-btn">
                                                <i class="fas fa-bolt me-1"></i>Gerçek AI Test
                                            </button>
                                            <button class="btn btn-outline-danger" onclick="closeTestArea('test-code')">
                                                <i class="fas fa-times me-1"></i>Kapat
                                            </button>
                                        </div>
                                        <div class="result-area" style="display: none;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Legal Documents -->
                <div class="col-12 col-lg-6 mb-4">
                    <div class="card h-100 border border-warning">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title">⚖️ Legal Documents</h5>
                                <span class="badge bg-warning">Aktif</span>
                            </div>
                            <p class="text-muted small mb-3">Hukuki belge şablonları ve yasal döküman hazırlama.</p>
                            <div class="bg-light p-2 rounded mb-3">
                                <strong>Şu işe yarar:</strong> Sözleşmeler, gizlilik politikaları, hizmet şartları
                            </div>
                            <div class="mb-3">
                                <code class="small">AI::generateLegalDocument($type, $context)</code>
                            </div>
                            <button 
                                class="btn btn-primary btn-sm w-100 direct-test-btn" 
                                data-feature="Legal Documents"
                                data-example="Web sitesi için gizlilik politikası hazırlamak istiyorum"
                                data-target="test-legal"
                                onclick="toggleTestArea('test-legal', 'Legal Documents', 'Web sitesi için gizlilik politikası hazırlamak istiyorum')"
                            >
                                <i class="fas fa-rocket me-1"></i> Canlı Test Et
                            </button>
                            
                            <!-- Test Alanı Accordion -->
                            <div class="accordion mt-3" id="test-legal" style="display: none;">
                                <div class="accordion-item">
                                    <div class="accordion-header">
                                        <h6 class="mb-0 p-2 bg-primary text-white">
                                            <i class="fas fa-rocket me-2"></i>Legal Documents Testi
                                        </h6>
                                    </div>
                                    <div class="accordion-body">
                                        <div class="mb-3">
                                            <label class="form-label"><strong>Test Metni:</strong></label>
                                            <textarea class="form-control test-input" rows="4" 
                                                      placeholder="Hazırlamak istediğiniz yasal dökümanı tanımlayın...">Web sitesi için gizlilik politikası hazırlamak istiyorum</textarea>
                                        </div>
                                        <div class="mb-3">
                                            <div class="card bg-light">
                                                <div class="card-body py-2">
                                                    <strong class="small">Hızlı Örnekler:</strong>
                                                    <div class="mt-2">
                                                        <button class="btn btn-sm btn-outline-primary me-1 mb-1 example-btn" 
                                                                data-text="E-ticaret sitesi için kullanım şartları">Kullanım Şartları</button>
                                                        <button class="btn btn-sm btn-outline-primary me-1 mb-1 example-btn" 
                                                                data-text="Freelance iş sözleşmesi taslağı">İş Sözleşmesi</button>
                                                        <button class="btn btn-sm btn-outline-primary me-1 mb-1 example-btn" 
                                                                data-text="Yazılım lisans anlaşması">Lisans Anlaşması</button>
                                                        <button class="btn btn-sm btn-outline-danger clear-btn">Temizle</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex gap-2 mb-3">
                                            <button class="btn btn-outline-secondary demo-test-btn">
                                                <i class="fas fa-flask me-1"></i>Demo Test
                                            </button>
                                            <button class="btn btn-success real-test-btn">
                                                <i class="fas fa-bolt me-1"></i>Gerçek AI Test
                                            </button>
                                            <button class="btn btn-outline-danger" onclick="closeTestArea('test-legal')">
                                                <i class="fas fa-times me-1"></i>Kapat
                                            </button>
                                        </div>
                                        <div class="result-area" style="display: none;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Academic Essay -->
                <div class="col-12 col-lg-6 mb-4">
                    <div class="card h-100 border border-warning">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title">🎓 Academic Essay</h5>
                                <span class="badge bg-warning">Aktif</span>
                            </div>
                            <p class="text-muted small mb-3">Akademik makale yazımı ve araştırma desteği.</p>
                            <div class="bg-light p-2 rounded mb-3">
                                <strong>Şu işe yarar:</strong> Üniversite ödevleri, araştırma makaleleri, tez yazımı
                            </div>
                            <div class="mb-3">
                                <code class="small">AI::generateAcademicEssay($topic, $style)</code>
                            </div>
                            <button 
                                class="btn btn-primary btn-sm w-100 direct-test-btn" 
                                data-feature="Academic Essay"
                                data-example="Yapay zekanın eğitim üzerindeki etkilerini analiz eden makale"
                                data-target="test-academic"
                                onclick="toggleTestArea('test-academic', 'Academic Essay', 'Yapay zekanın eğitim üzerindeki etkilerini analiz eden makale')"
                            >
                                <i class="fas fa-rocket me-1"></i> Canlı Test Et
                            </button>
                            
                            <!-- Test Alanı Accordion -->
                            <div class="accordion mt-3" id="test-academic" style="display: none;">
                                <div class="accordion-item">
                                    <div class="accordion-header">
                                        <h6 class="mb-0 p-2 bg-primary text-white">
                                            <i class="fas fa-rocket me-2"></i>Academic Essay Testi
                                        </h6>
                                    </div>
                                    <div class="accordion-body">
                                        <div class="mb-3">
                                            <label class="form-label"><strong>Test Metni:</strong></label>
                                            <textarea class="form-control test-input" rows="4" 
                                                      placeholder="Akademik makale konusunu ve yaklaşımını girin...">Yapay zekanın eğitim üzerindeki etkilerini analiz eden makale</textarea>
                                        </div>
                                        <div class="mb-3">
                                            <div class="card bg-light">
                                                <div class="card-body py-2">
                                                    <strong class="small">Hızlı Örnekler:</strong>
                                                    <div class="mt-2">
                                                        <button class="btn btn-sm btn-outline-primary me-1 mb-1 example-btn" 
                                                                data-text="Sürdürülebilir kalkınma amaçları araştırması">Sürdürülebilirlik</button>
                                                        <button class="btn btn-sm btn-outline-primary me-1 mb-1 example-btn" 
                                                                data-text="Sosyal medyanın toplum üzerindeki etkisi">Sosyoloji</button>
                                                        <button class="btn btn-sm btn-outline-primary me-1 mb-1 example-btn" 
                                                                data-text="İklim değişikliği ve çözüm önerileri">Çevre</button>
                                                        <button class="btn btn-sm btn-outline-danger clear-btn">Temizle</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex gap-2 mb-3">
                                            <button class="btn btn-outline-secondary demo-test-btn">
                                                <i class="fas fa-flask me-1"></i>Demo Test
                                            </button>
                                            <button class="btn btn-success real-test-btn">
                                                <i class="fas fa-bolt me-1"></i>Gerçek AI Test
                                            </button>
                                            <button class="btn btn-outline-danger" onclick="closeTestArea('test-academic')">
                                                <i class="fas fa-times me-1"></i>Kapat
                                            </button>
                                        </div>
                                        <div class="result-area" style="display: none;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Creative Writing -->
                <div class="col-12 col-lg-6 mb-4">
                    <div class="card h-100 border border-warning">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title">✍️ Creative Writing</h5>
                                <span class="badge bg-warning">Aktif</span>
                            </div>
                            <p class="text-muted small mb-3">Yaratıcı yazma desteği ve hikaye oluşturma.</p>
                            <div class="bg-light p-2 rounded mb-3">
                                <strong>Şu işe yarar:</strong> Hikaye yazımı, senaryo, şiir, roman taslakları
                            </div>
                            <div class="mb-3">
                                <code class="small">AI::generateCreativeContent($type, $theme)</code>
                            </div>
                            <button 
                                class="btn btn-primary btn-sm w-100 direct-test-btn" 
                                data-feature="Creative Writing"
                                data-example="Uzayda geçen kısa bilim kurgu hikayesi yazmak istiyorum"
                                data-target="test-creative"
                                onclick="toggleTestArea('test-creative', 'Creative Writing', 'Uzayda geçen kısa bilim kurgu hikayesi yazmak istiyorum')"
                            >
                                <i class="fas fa-rocket me-1"></i> Canlı Test Et
                            </button>
                            
                            <!-- Test Alanı Accordion -->
                            <div class="accordion mt-3" id="test-creative" style="display: none;">
                                <div class="accordion-item">
                                    <div class="accordion-header">
                                        <h6 class="mb-0 p-2 bg-primary text-white">
                                            <i class="fas fa-rocket me-2"></i>Creative Writing Testi
                                        </h6>
                                    </div>
                                    <div class="accordion-body">
                                        <div class="mb-3">
                                            <label class="form-label"><strong>Test Metni:</strong></label>
                                            <textarea class="form-control test-input" rows="4" 
                                                      placeholder="Yaratıcı yazı türü ve konusunu girin...">Uzayda geçen kısa bilim kurgu hikayesi yazmak istiyorum</textarea>
                                        </div>
                                        <div class="mb-3">
                                            <div class="card bg-light">
                                                <div class="card-body py-2">
                                                    <strong class="small">Hızlı Örnekler:</strong>
                                                    <div class="mt-2">
                                                        <button class="btn btn-sm btn-outline-primary me-1 mb-1 example-btn" 
                                                                data-text="Aşk konulu romantik şiir yazması">Şiir</button>
                                                        <button class="btn btn-sm btn-outline-primary me-1 mb-1 example-btn" 
                                                                data-text="Çocuklar için masalsı öykü">Masal</button>
                                                        <button class="btn btn-sm btn-outline-primary me-1 mb-1 example-btn" 
                                                                data-text="Polisiye gerilim hikayesi">Polisiye</button>
                                                        <button class="btn btn-sm btn-outline-danger clear-btn">Temizle</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex gap-2 mb-3">
                                            <button class="btn btn-outline-secondary demo-test-btn">
                                                <i class="fas fa-flask me-1"></i>Demo Test
                                            </button>
                                            <button class="btn btn-success real-test-btn">
                                                <i class="fas fa-bolt me-1"></i>Gerçek AI Test
                                            </button>
                                            <button class="btn btn-outline-danger" onclick="closeTestArea('test-creative')">
                                                <i class="fas fa-times me-1"></i>Kapat
                                            </button>
                                        </div>
                                        <div class="result-area" style="display: none;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Market Research -->
                <div class="col-12 col-lg-6 mb-4">
                    <div class="card h-100 border border-info">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title">📊 Market Research</h5>
                                <span class="badge bg-info">Aktif</span>
                            </div>
                            <p class="text-muted small mb-3">Pazar araştırması ve tüketici analizi raporları.</p>
                            <div class="bg-light p-2 rounded mb-3">
                                <strong>Şu işe yarar:</strong> Rekabet analizi, hedef kitle araştırması, pazar trendleri
                            </div>
                            <div class="mb-3">
                                <code class="small">AI::generateMarketResearch($industry, $focus)</code>
                            </div>
                            <button 
                                class="btn btn-primary btn-sm w-100 direct-test-btn" 
                                data-feature="Market Research"
                                data-example="E-ticaret sektöründe tüketici davranışları analizi"
                                data-target="test-market"
                                onclick="toggleTestArea('test-market', 'Market Research', 'E-ticaret sektöründe tüketici davranışları analizi')"
                            >
                                <i class="fas fa-rocket me-1"></i> Canlı Test Et
                            </button>
                            
                            <!-- Test Alanı Accordion -->
                            <div class="accordion mt-3" id="test-market" style="display: none;">
                                <div class="accordion-item">
                                    <div class="accordion-header">
                                        <h6 class="mb-0 p-2 bg-primary text-white">
                                            <i class="fas fa-rocket me-2"></i>Market Research Testi
                                        </h6>
                                    </div>
                                    <div class="accordion-body">
                                        <div class="mb-3">
                                            <label class="form-label"><strong>Test Metni:</strong></label>
                                            <textarea class="form-control test-input" rows="4" 
                                                      placeholder="Araştırma konusu ve sektörü girin...">E-ticaret sektöründe tüketici davranışları analizi</textarea>
                                        </div>
                                        <div class="mb-3">
                                            <div class="card bg-light">
                                                <div class="card-body py-2">
                                                    <strong class="small">Hızlı Örnekler:</strong>
                                                    <div class="mt-2">
                                                        <button class="btn btn-sm btn-outline-primary me-1 mb-1 example-btn" 
                                                                data-text="Mobil uygulama pazarı büyüme analizi">Mobil App</button>
                                                        <button class="btn btn-sm btn-outline-primary me-1 mb-1 example-btn" 
                                                                data-text="Gıda sektörü rekabet durumu">Gıda</button>
                                                        <button class="btn btn-sm btn-outline-primary me-1 mb-1 example-btn" 
                                                                data-text="Fintech startup'ları pazar araştırması">Fintech</button>
                                                        <button class="btn btn-sm btn-outline-danger clear-btn">Temizle</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex gap-2 mb-3">
                                            <button class="btn btn-outline-secondary demo-test-btn">
                                                <i class="fas fa-flask me-1"></i>Demo Test
                                            </button>
                                            <button class="btn btn-success real-test-btn">
                                                <i class="fas fa-bolt me-1"></i>Gerçek AI Test
                                            </button>
                                            <button class="btn btn-outline-danger" onclick="closeTestArea('test-market')">
                                                <i class="fas fa-times me-1"></i>Kapat
                                            </button>
                                        </div>
                                        <div class="result-area" style="display: none;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Survey Generator -->
                <div class="col-12 col-lg-6 mb-4">
                    <div class="card h-100 border border-info">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title">📋 Survey Generator</h5>
                                <span class="badge bg-info">Aktif</span>
                            </div>
                            <p class="text-muted small mb-3">Anket soruları ve araştırma formları oluşturma.</p>
                            <div class="bg-light p-2 rounded mb-3">
                                <strong>Şu işe yarar:</strong> Müşteri memnuniyeti, pazar araştırması, feedback formları
                            </div>
                            <div class="mb-3">
                                <code class="small">AI::generateSurvey($purpose, $targetAudience)</code>
                            </div>
                            <button 
                                class="btn btn-primary btn-sm w-100 direct-test-btn" 
                                data-feature="Survey Generator"
                                data-example="Müşteri memnuniyeti anketi hazırlamak istiyorum"
                                data-target="test-survey"
                                onclick="toggleTestArea('test-survey', 'Survey Generator', 'Müşteri memnuniyeti anketi hazırlamak istiyorum')"
                            >
                                <i class="fas fa-rocket me-1"></i> Canlı Test Et
                            </button>
                            
                            <!-- Test Alanı Accordion -->
                            <div class="accordion mt-3" id="test-survey" style="display: none;">
                                <div class="accordion-item">
                                    <div class="accordion-header">
                                        <h6 class="mb-0 p-2 bg-primary text-white">
                                            <i class="fas fa-rocket me-2"></i>Survey Generator Testi
                                        </h6>
                                    </div>
                                    <div class="accordion-body">
                                        <div class="mb-3">
                                            <label class="form-label"><strong>Test Metni:</strong></label>
                                            <textarea class="form-control test-input" rows="4" 
                                                      placeholder="Anket amacı ve hedef kitleyi girin...">Müşteri memnuniyeti anketi hazırlamak istiyorum</textarea>
                                        </div>
                                        <div class="mb-3">
                                            <div class="card bg-light">
                                                <div class="card-body py-2">
                                                    <strong class="small">Hızlı Örnekler:</strong>
                                                    <div class="mt-2">
                                                        <button class="btn btn-sm btn-outline-primary me-1 mb-1 example-btn" 
                                                                data-text="Çalışan memnuniyet anketi">İnsan Kaynakları</button>
                                                        <button class="btn btn-sm btn-outline-primary me-1 mb-1 example-btn" 
                                                                data-text="Ürün geri bildirim formu">Ürün Feedback</button>
                                                        <button class="btn btn-sm btn-outline-primary me-1 mb-1 example-btn" 
                                                                data-text="Etkinlik değerlendirme anketi">Etkinlik</button>
                                                        <button class="btn btn-sm btn-outline-danger clear-btn">Temizle</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex gap-2 mb-3">
                                            <button class="btn btn-outline-secondary demo-test-btn">
                                                <i class="fas fa-flask me-1"></i>Demo Test
                                            </button>
                                            <button class="btn btn-success real-test-btn">
                                                <i class="fas fa-bolt me-1"></i>Gerçek AI Test
                                            </button>
                                            <button class="btn btn-outline-danger" onclick="closeTestArea('test-survey')">
                                                <i class="fas fa-times me-1"></i>Kapat
                                            </button>
                                        </div>
                                        <div class="result-area" style="display: none;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>

<!-- Evrensel Test Alanı (Kalan Kartlar İçin) -->
<div class="modal fade" id="universalTestModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><i class="ti ti-rocket me-2"></i><span id="modal-feature-name">AI Test</span></h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label"><strong>Test Metni:</strong></label>
                    <textarea id="universal-test-input" class="form-control" rows="4" 
                              placeholder="Test edilecek metni girin..."></textarea>
                </div>
                <div class="d-flex gap-2 mb-3">
                    <button class="btn btn-outline-secondary" onclick="runUniversalDemo()">
                        <i class="ti ti-flask me-1"></i>Demo Test
                    </button>
                    <button class="btn btn-success" onclick="runUniversalRealTest()">
                        <i class="ti ti-zap me-1"></i>Gerçek AI Test
                    </button>
                </div>
                <div id="universal-result-area" style="display: none;"></div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('js')
<script>
// Basit global fonksiyonlar - Modal yok!

function toggleTestArea(testId, featureName, defaultExample) {
    console.log('Test alanı açılıyor:', testId, featureName);
    
    // Test alanını göster
    const testArea = document.getElementById(testId);
    
    if (!testArea) {
        // Eğer accordion yapısı yoksa, uyarı ver
        alert('Bu özellik için test alanı hazırlanıyor. Lütfen diğer özellikleri deneyin.');
        return;
    }
    
    if (testArea.style.display === 'none') {
        testArea.style.display = 'block';
        
        // Başlığı güncelle
        const title = testArea.querySelector('h6');
        if (title) {
            title.innerHTML = `<i class="ti ti-rocket me-2"></i>${featureName} Testi`;
        }
        
        // Textarea'ya varsayılan örneği yükle
        const textarea = testArea.querySelector('.test-input');
        if (textarea) {
            textarea.value = defaultExample;
        }
        
        // Scroll
        testArea.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

function closeTestArea(testId) {
    console.log('Test alanı kapatılıyor:', testId);
    const testArea = document.getElementById(testId);
    testArea.style.display = 'none';
    
    // Sonuç alanını da gizle
    const resultArea = testArea.querySelector('.result-area');
    if (resultArea) {
        resultArea.style.display = 'none';
    }
}

// Document ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('Sayfa hazır - Basit sistem aktif');
    
    // Tüm butonlara event listener ekle
    document.addEventListener('click', function(e) {
        const target = e.target;
        const testArea = target.closest('.accordion-body');
        
        // Örnek butonları
        if (target.classList.contains('example-btn')) {
            console.log('Örnek butonu tıklandı');
            const exampleText = target.getAttribute('data-text');
            const textarea = testArea.querySelector('.test-input');
            if (textarea) {
                textarea.value = exampleText;
            }
        }
        
        // Temizle butonu
        if (target.classList.contains('clear-btn')) {
            console.log('Temizle butonu tıklandı');
            const textarea = testArea.querySelector('.test-input');
            if (textarea) {
                textarea.value = '';
                textarea.focus();
            }
        }
        
        // Demo test butonu
        if (target.classList.contains('demo-test-btn')) {
            console.log('Demo test butonu tıklandı');
            const textarea = testArea.querySelector('.test-input');
            const resultArea = testArea.querySelector('.result-area');
            
            if (textarea && resultArea) {
                const testText = textarea.value;
                if (testText.length < 3) {
                    alert('Test metni en az 3 karakter olmalıdır.');
                    return;
                }
                
                // Demo sonuç göster
                resultArea.innerHTML = `
                    <div class="alert alert-info">
                        <strong><i class="ti ti-flask me-1"></i> Demo Test Sonucu</strong>
                        <div class="mt-2">Bu bir demo testtir. Gerçek AI testi için "Gerçek AI Test" butonunu kullanın.</div>
                    </div>
                    <div class="mb-3">
                        <strong>Test Metni:</strong>
                        <div class="bg-light p-2 rounded mt-1">${testText}</div>
                    </div>
                    <div>
                        <strong>Demo AI Yanıtı:</strong>
                        <div class="ai-result-content bg-white border p-4 rounded mt-1 shadow-sm">
                            <div class="markdown-content">
                                <strong>Demo Sonucu</strong><br><br>
                                Bu bir <em>demo yanıttır</em>. Gerçek AI testi yapmak için <strong>"Gerçek AI Test"</strong> butonunu kullanın.<br><br>
                                • Demo modda token harcanmaz<br>
                                • Sadece arayüz testine yarar<br>
                                • Gerçek AI sonuçları için yeşil butonu kullanın
                            </div>
                        </div>
                    </div>
                `;
                resultArea.style.display = 'block';
            }
        }
        
        // Gerçek AI test butonu
        if (target.classList.contains('real-test-btn')) {
            console.log('Gerçek AI test butonu tıklandı');
            const textarea = testArea.querySelector('.test-input');
            const resultArea = testArea.querySelector('.result-area');
            const featureName = testArea.closest('.accordion').id.replace('test-', '').replace('-', ' ');
            
            if (textarea && resultArea) {
                const testText = textarea.value;
                if (testText.length < 3) {
                    alert('Test metni en az 3 karakter olmalıdır.');
                    return;
                }
                
                // Loading göster
                resultArea.innerHTML = `
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Yükleniyor...</span>
                        </div>
                        <div class="mt-2">
                            <strong>AI testi çalışıyor...</strong><br>
                            <small class="text-muted">Lütfen bekleyin, sonuçlar hazırlanıyor.</small>
                        </div>
                    </div>
                `;
                resultArea.style.display = 'block';
                
                // API çağrısı
                runRealAITest(featureName, testText, resultArea);
            }
        }
    });
});

function runRealAITest(featureName, inputText, resultArea) {
    console.log('Gerçek AI test başlatılıyor:', featureName, inputText);
    
    const startTime = Date.now();
    const token = document.querySelector('meta[name="csrf-token"]');
    const csrfToken = token ? token.getAttribute('content') : '';
    
    fetch('/admin/ai/test-feature', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            feature_name: featureName,
            input_text: inputText,
            tenant_id: 1,
            real_ai: true
        })
    })
    .then(response => response.json())
    .then(data => {
        const processingTime = Date.now() - startTime;
        
        if (data.success) {
            resultArea.innerHTML = `
                <div class="alert alert-success">
                    <div class="d-flex justify-content-between align-items-center">
                        <strong><i class="ti ti-check-circle me-1"></i> AI Test Başarılı</strong>
                        <div class="small text-muted">
                            ${processingTime}ms • ${data.tokens_used || 0} token
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <strong>Gönderilen İçerik:</strong>
                    <div class="bg-light p-2 rounded mt-1">
                        <small>${inputText}</small>
                    </div>
                </div>
                
                <div>
                    <strong>AI Yanıtı:</strong>
                    <div class="ai-result-content bg-white border p-4 rounded mt-1 shadow-sm">
                        <div class="markdown-content">${formatMarkdownContent(data.ai_result || data.result)}</div>
                    </div>
                </div>
            `;
            
            // Token durumunu AJAX ile güncelle (sayfa yenileme YOK!)
            updateTokenStatus();
        } else {
            resultArea.innerHTML = `
                <div class="alert alert-danger">
                    <strong><i class="ti ti-x-circle me-1"></i> Test Hatası</strong>
                    <div class="mt-2">${data.message || data.error || 'AI servisi ile iletişim kurulamadı'}</div>
                </div>
                
                <div class="mb-3">
                    <strong>Gönderilen İçerik:</strong>
                    <div class="bg-light p-2 rounded mt-1">
                        <small>${inputText}</small>
                    </div>
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        resultArea.innerHTML = `
            <div class="alert alert-danger">
                <strong><i class="ti ti-x-circle me-1"></i> Bağlantı Hatası</strong>
                <div class="mt-2">Bir hata oluştu: ${error.message}</div>
            </div>
        `;
    });
}

// Markdown content formatlaması
function formatMarkdownContent(text) {
    if (!text) return '';
    
    return text
        // Bold formatı **text** -> <strong>text</strong>
        .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
        
        // Italic formatı *text* -> <em>text</em>
        .replace(/\*(.*?)\*/g, '<em>$1</em>')
        
        // Code formatı `code` -> <code>code</code>
        .replace(/`([^`]+)`/g, '<code class="bg-light px-1 rounded">$1</code>')
        
        // Liste itemleri - * item -> <li>item</li>
        .replace(/^\s*[\*\-]\s+(.+)$/gm, '<li>$1</li>')
        
        // Başlıklar # Title -> <h4>Title</h4>
        .replace(/^#+\s+(.+)$/gm, '<h5 class="text-primary mt-3 mb-2">$1</h5>')
        
        // Çizgi ---
        .replace(/^---+$/gm, '<hr class="my-3">')
        
        // Satır sonları
        .replace(/\n/g, '<br>')
        
        // Liste wrapper'ı ekle
        .replace(/(<li>.*<\/li>)/g, '<ul class="ms-3 mb-2">$1</ul>')
        
        // Çoklu <br> temizle
        .replace(/<br>\s*<br>/g, '<br>');
}

// Token durumu AJAX güncelleme fonksiyonu
function updateTokenStatus() {
    fetch('/admin/ai/examples', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => response.text())
    .then(html => {
        // Sadece token kartlarını güncelle
        const parser = new DOMParser();
        const newDoc = parser.parseFromString(html, 'text/html');
        const newTokenCards = newDoc.querySelectorAll('.card-body h2');
        const currentTokenCards = document.querySelectorAll('.card-body h2');
        
        // Token sayılarını güncelle
        if (newTokenCards.length >= 3 && currentTokenCards.length >= 3) {
            currentTokenCards[0].textContent = newTokenCards[0].textContent; // Kalan
            currentTokenCards[1].textContent = newTokenCards[1].textContent; // Günlük
            currentTokenCards[2].textContent = newTokenCards[2].textContent; // Aylık
        }
    })
    .catch(error => {
        console.log('Token durumu güncellenemedi:', error);
    });
}

// Evrensel Test Fonksiyonları
let currentUniversalFeature = '';

function openUniversalTest(featureName, defaultExample) {
    currentUniversalFeature = featureName;
    document.getElementById('modal-feature-name').textContent = featureName + ' Testi';
    document.getElementById('universal-test-input').value = defaultExample || '';
    document.getElementById('universal-result-area').style.display = 'none';
    
    // Bootstrap modal aç
    const modal = new bootstrap.Modal(document.getElementById('universalTestModal'));
    modal.show();
}

function runUniversalDemo() {
    const textarea = document.getElementById('universal-test-input');
    const resultArea = document.getElementById('universal-result-area');
    
    if (textarea.value.length < 3) {
        alert('Test metni en az 3 karakter olmalıdır.');
        return;
    }
    
    resultArea.innerHTML = `
        <div class="alert alert-info">
            <strong><i class="ti ti-flask me-1"></i> Demo Test Sonucu</strong>
            <div class="mt-2">Bu bir demo testtir. Gerçek AI testi için "Gerçek AI Test" butonunu kullanın.</div>
        </div>
        <div class="ai-result-content bg-white border p-4 rounded mt-3 shadow-sm">
            <div class="markdown-content">
                <strong>Demo Sonucu - ${currentUniversalFeature}</strong><br><br>
                Test metniniz: "${textarea.value}"<br><br>
                • Bu özellik için demo yanıt<br>
                • Gerçek AI testi yapın<br>
                • Token harcanmaz
            </div>
        </div>
    `;
    resultArea.style.display = 'block';
}

function runUniversalRealTest() {
    const textarea = document.getElementById('universal-test-input');
    const resultArea = document.getElementById('universal-result-area');
    
    if (textarea.value.length < 3) {
        alert('Test metni en az 3 karakter olmalıdır.');
        return;
    }
    
    // Loading göster
    resultArea.innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Yükleniyor...</span>
            </div>
            <div class="mt-2">
                <strong>AI testi çalışıyor...</strong><br>
                <small class="text-muted">Lütfen bekleyin, sonuçlar hazırlanıyor.</small>
            </div>
        </div>
    `;
    resultArea.style.display = 'block';
    
    // API çağrısı
    runRealAITest(currentUniversalFeature, textarea.value, resultArea);
}
</script>

<style>
.test-btn {
    transition: all 0.3s ease;
}

.test-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.card.border {
    border-width: 2px !important;
}

.accordion-button.disabled {
    pointer-events: none;
    opacity: 0.6;
}

/* AI Sonuç İçeriği Stilleri */
.ai-result-content {
    font-family: 'Inter', 'Segoe UI', system-ui, -apple-system, sans-serif;
    line-height: 1.7;
    font-size: 15px;
    color: #2c3e50;
}

.markdown-content {
    max-height: 600px;
    overflow-y: auto;
}

.markdown-content strong {
    color: #1e40af;
    font-weight: 600;
}

.markdown-content em {
    color: #7c3aed;
    font-style: italic;
}

.markdown-content h5 {
    border-bottom: 2px solid #e5e7eb;
    padding-bottom: 8px;
    font-weight: 700;
}

.markdown-content ul {
    list-style-type: none;
    padding-left: 0;
}

.markdown-content li {
    position: relative;
    padding-left: 24px;
    margin-bottom: 8px;
}

.markdown-content li::before {
    content: '•';
    position: absolute;
    left: 8px;
    color: #3b82f6;
    font-weight: bold;
    font-size: 16px;
}

.markdown-content code {
    font-family: 'JetBrains Mono', 'Fira Code', 'Monaco', monospace;
    font-size: 13px;
    color: #dc2626;
    background-color: #f8fafc !important;
    border: 1px solid #e2e8f0;
    padding: 2px 6px;
}

.markdown-content hr {
    border: none;
    height: 2px;
    background: linear-gradient(90deg, #3b82f6, #8b5cf6, #06b6d4);
    border-radius: 1px;
}

/* Scrollbar stilleri */
.markdown-content::-webkit-scrollbar {
    width: 8px;
}

.markdown-content::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 4px;
}

.markdown-content::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}

.markdown-content::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>
@endpush
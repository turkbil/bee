<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Writer - Universal Input System Demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons@latest/icons-sprite.svg">
    <style>
        .demo-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 3rem 0;
        }
        
        .form-floating > label {
            font-weight: 500;
        }
        
        .btn-ai-generate {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            font-weight: 500;
        }
        
        .btn-ai-generate:hover {
            background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
            color: white;
        }
        
        .accordion-button:not(.collapsed) {
            background-color: #f8f9fc;
        }
        
        .feature-badge {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            display: inline-block;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>

<div class="demo-header">
    <div class="container">
        <div class="row justify-content-center text-center">
            <div class="col-lg-8">
                <div class="feature-badge">🎉 Universal Input System Demo</div>
                <h1 class="display-5 fw-bold mb-3">Blog Yazısı Oluşturucu</h1>
                <p class="lead">Yapay zeka ile kişiselleştirilmiş blog içeriği üretin. Yazma tarzınızı, uzunluğunu ve hedef kitlenizi belirleyin.</p>
            </div>
        </div>
    </div>
</div>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            
            <!-- Ana Form -->
            <div class="card shadow-sm mb-4">
                <div class="card-body p-4">
                    
                    <!-- Ana Alan - Textarea -->
                    <div class="mb-4">
                        <label for="blogTopic" class="form-label fw-bold text-start d-block">Blog Konusu *</label>
                        <textarea class="form-control" id="blogTopic" 
                                 placeholder="Hangi konu hakkında blog yazısı yazmak istiyorsunuz? (örn: 'Evden çalışma ipuçları' veya 'Sağlıklı yaşam tavsiyeleri')" 
                                 rows="4" required></textarea>
                        <div class="form-text text-start">
                            Konunuzu detaylı bir şekilde açıklayın. Ne kadar detay verirseniz, o kadar kaliteli içerik üretiriz.
                        </div>
                    </div>

                    <!-- İleri Düzey Ayarlar (Accordion) -->
                    <div class="accordion" id="advancedSettings">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                                        data-bs-target="#settingsCollapse" aria-expanded="false">
                                    📋 İleri Düzey Ayarlar
                                </button>
                            </h2>
                            <div id="settingsCollapse" class="accordion-collapse collapse">
                                <div class="accordion-body">
                                    
                                    <div class="row">
                                        <!-- Yazı Tonu -->
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold text-start d-block">✨ Yazı Tonu</label>
                                            <select class="form-select" id="writingTone" data-choices>
                                                <option value="professional" selected>Profesyonel</option>
                                                <option value="friendly">Samimi</option>
                                                <option value="casual">Günlük</option>
                                                <option value="technical">Teknik</option>
                                                <option value="creative">Yaratıcı</option>
                                                <option value="authoritative">Otoriter</option>
                                                <option value="empathetic">Empatik</option>
                                                <option value="humorous">Esprili</option>
                                            </select>
                                        </div>

                                        <!-- Yazı Uzunluğu -->
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold text-start d-block">📏 Yazı Uzunluğu</label>
                                            <input type="range" class="form-range" id="contentLength" 
                                                   min="1" max="3" value="2" oninput="updateLengthLabel()">
                                            <div class="d-flex justify-content-between">
                                                <small>Kısa<br>(200-400)</small>
                                                <small><strong id="lengthLabel">Orta<br>(400-700)</strong></small>
                                                <small>Uzun<br>(700-1000+)</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <!-- Hedef Kitle -->
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold text-start d-block">👥 Hedef Kitle</label>
                                            <select class="form-select" id="targetAudience" data-choices>
                                                <option value="">Hedef kitlenizi seçin...</option>
                                                <option value="business-professionals">İş Profesyonelleri</option>
                                                <option value="entrepreneurs">Girişimciler</option>
                                                <option value="students">Öğrenciler</option>
                                                <option value="tech-enthusiasts">Teknoloji Meraklıları</option>
                                                <option value="general-audience">Genel Okur Kitlesi</option>
                                                <option value="industry-experts">Sektör Uzmanları</option>
                                                <option value="beginners">Yeni Başlayanlar</option>
                                                <option value="advanced-users">İleri Düzey Kullanıcılar</option>
                                                <option value="parents">Ebeveynler</option>
                                                <option value="young-adults">Genç Yetişkinler</option>
                                            </select>
                                        </div>

                                        <!-- Firma Bilgilerini Kullan (Gelecek Özellik) -->
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold text-start d-block">🏢 Şirket Profili</label>
                                            <div class="form-check form-switch mt-2">
                                                <input class="form-check-input" type="checkbox" id="useCompanyInfo" disabled>
                                                <label class="form-check-label text-start" for="useCompanyInfo">
                                                    Firma Bilgilerini Kullan <span class="badge bg-secondary">Yakında</span>
                                                </label>
                                            </div>
                                            <small class="text-muted text-start d-block">AI Profiles sistemi ile otomatik firma bilgisi ekleme</small>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Generate Button -->
                    <div class="d-grid gap-2 mt-4">
                        <button class="btn btn-ai-generate btn-lg" type="button" onclick="generateBlog()">
                            ✨ Blog Yazısı Oluştur
                        </button>
                    </div>

                </div>
            </div>

            <!-- Sonuç Alanı -->
            <div id="resultArea" class="card shadow-sm" style="display: none;">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">📝 Oluşturulan Blog Yazısı</h5>
                </div>
                <div class="card-body">
                    <div id="generatedContent"></div>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />

<script>
// Choices.js dropdown'ları initialize et
document.addEventListener('DOMContentLoaded', function() {
    // Yazı Tonu dropdown
    const writingToneSelect = new Choices('#writingTone', {
        searchEnabled: false,
        itemSelectText: '',
    });

    // Hedef Kitle dropdown  
    const targetAudienceSelect = new Choices('#targetAudience', {
        searchEnabled: true,
        searchPlaceholderValue: 'Hedef kitle ara...',
        itemSelectText: '',
    });
});
</script>
<script>
function updateLengthLabel() {
    const length = document.getElementById('contentLength').value;
    const label = document.getElementById('lengthLabel');
    
    switch(length) {
        case '1':
            label.innerHTML = '<strong>Kısa<br>(200-400)</strong>';
            break;
        case '2':
            label.innerHTML = '<strong>Orta<br>(400-700)</strong>';
            break;
        case '3':
            label.innerHTML = '<strong>Uzun<br>(700-1000+)</strong>';
            break;
    }
}

function generateBlog() {
    // Form verilerini topla
    const topic = document.getElementById('blogTopic').value;
    const tone = document.getElementById('writingTone').value;
    const length = document.getElementById('contentLength').value;
    const audience = document.getElementById('targetAudience').value;
    
    if (!topic.trim()) {
        alert('Lütfen blog konusunu girin!');
        return;
    }
    
    // Buton durumunu değiştir
    const button = document.querySelector('.btn-ai-generate');
    const originalText = button.innerHTML;
    button.innerHTML = '⏳ Oluşturuluyor...';
    button.disabled = true;
    
    // Demo için sahte içerik oluştur (gerçekte API'ye gönderilecek)
    setTimeout(() => {
        const lengthText = {1: 'Kısa', 2: 'Orta', 3: 'Uzun'}[length];
        const toneText = {
            'professional': 'Profesyonel',
            'friendly': 'Samimi', 
            'educational': 'Eğitici',
            'fun': 'Eğlenceli'
        }[tone];
        
        const result = `
            <h3>${topic}</h3>
            
            <div class="alert alert-info">
                <strong>Seçtiğiniz Ayarlar:</strong><br>
                • Ton: ${toneText}<br>
                • Uzunluk: ${lengthText} (${{'1': '200-400', '2': '400-700', '3': '700-1000+'}[length]} kelime)<br>
                • Hedef Kitle: ${audience || 'Genel'}<br>
            </div>
            
            <p><strong>Giriş:</strong> ${topic} konusu günümüzde oldukça önemli hale gelmiştir...</p>
            
            <h4>Ana Bölümler</h4>
            <ul>
                <li>Temel Kavramlar</li>
                <li>Pratik Uygulamalar</li>
                <li>İpuçları ve Öneriler</li>
                <li>Sonuç</li>
            </ul>
            
            <p class="text-muted"><em>Bu bir demo içeriğidir. Gerçek AI sistemi çok daha detaylı ve ${toneText.toLowerCase()} tonunda içerik üretecektir.</em></p>
        `;
        
        document.getElementById('generatedContent').innerHTML = result;
        document.getElementById('resultArea').style.display = 'block';
        
        // Butonu eski haline getir
        button.innerHTML = originalText;
        button.disabled = false;
        
        // Sonuca scroll yap
        document.getElementById('resultArea').scrollIntoView({behavior: 'smooth'});
        
    }, 2000);
}
</script>

</body>
</html>
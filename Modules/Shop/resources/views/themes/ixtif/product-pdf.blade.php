<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} - Ürün Kataloğu</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            line-height: 1.6;
            color: #1e293b;
            padding: 20px;
            background: #ffffff;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 3px solid #667eea;
        }

        .company-name {
            font-size: 20px;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 5px;
            letter-spacing: 0.5px;
        }

        .company-tagline {
            font-size: 10px;
            color: #64748b;
            font-style: italic;
        }

        .product-title {
            font-size: 18px;
            font-weight: bold;
            color: #1e293b;
            margin: 0 0 20px 0;
            padding: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px;
            text-align: center;
        }

        .section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #1e293b;
            margin-bottom: 12px;
            padding: 10px 14px;
            background: #f1f5f9;
            border-left: 4px solid #667eea;
        }

        .description {
            font-size: 11px;
            color: #475569;
            line-height: 1.8;
            padding: 12px 15px;
            background: #f8fafc;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        /* Primary Specs - Gradient Cards */
        .specs-grid {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }

        .spec-card {
            display: table-row;
            margin-bottom: 10px;
        }

        .spec-card-inner {
            display: table-cell;
            padding: 12px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            margin: 5px 0;
            background: #ffffff;
        }

        .spec-icon {
            display: inline-block;
            width: 30px;
            height: 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 6px;
            text-align: center;
            line-height: 30px;
            color: white;
            font-weight: bold;
            margin-right: 10px;
        }

        .spec-label {
            font-size: 10px;
            color: #64748b;
            font-weight: 600;
            display: block;
            margin-bottom: 4px;
        }

        .spec-value {
            font-size: 13px;
            color: #1e293b;
            font-weight: bold;
        }

        /* Features */
        .feature-item {
            padding: 10px 12px;
            margin-bottom: 8px;
            background: #f8fafc;
            border-left: 3px solid #10b981;
            border-radius: 6px;
        }

        .feature-title {
            font-size: 11px;
            font-weight: bold;
            color: #1e293b;
            margin-bottom: 4px;
        }

        .feature-desc {
            font-size: 10px;
            color: #64748b;
            line-height: 1.6;
        }

        /* Technical Specs Table */
        .tech-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .tech-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 10px;
            font-size: 12px;
            font-weight: bold;
            text-align: left;
        }

        .tech-row {
            border-bottom: 1px solid #e2e8f0;
        }

        .tech-label {
            padding: 8px 10px;
            background: #f8fafc;
            color: #64748b;
            font-weight: 600;
            font-size: 10px;
            width: 40%;
        }

        .tech-value {
            padding: 8px 10px;
            color: #1e293b;
            font-weight: 600;
            font-size: 10px;
        }

        /* List Items */
        .list-item {
            padding: 8px 0 8px 20px;
            position: relative;
            font-size: 10px;
            color: #475569;
            line-height: 1.6;
        }

        .list-item:before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #10b981;
            font-weight: bold;
            font-size: 12px;
        }

        /* Warranty Box */
        .warranty-box {
            background: #ecfdf5;
            border: 2px solid #10b981;
            padding: 15px;
            border-radius: 10px;
            margin: 15px 0;
        }

        .warranty-title {
            font-size: 12px;
            font-weight: bold;
            color: #047857;
            margin-bottom: 8px;
        }

        .warranty-text {
            font-size: 10px;
            color: #065f46;
            line-height: 1.6;
        }

        /* FAQ */
        .faq-item {
            margin-bottom: 15px;
            padding: 12px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            background: #f8fafc;
        }

        .faq-question {
            font-size: 11px;
            font-weight: bold;
            color: #1e293b;
            margin-bottom: 6px;
        }

        .faq-answer {
            font-size: 10px;
            color: #64748b;
            line-height: 1.7;
        }

        /* Gallery */
        .gallery-note {
            padding: 12px;
            background: #f1f5f9;
            border-left: 3px solid #667eea;
            font-size: 10px;
            color: #475569;
            margin-bottom: 20px;
        }

        /* Footer */
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px solid #e2e8f0;
            text-align: center;
            font-size: 9px;
            color: #64748b;
        }

        .footer strong {
            color: #667eea;
            font-size: 10px;
        }

        /* Page break helpers */
        .page-break {
            page-break-after: always;
        }

        .no-break {
            page-break-inside: avoid;
        }
    </style>
</head>
<body>
    {{-- HEADER --}}
    <div class="header">
        <div class="company-name">İXTİF</div>
        <div class="company-tagline">Türkiye'nin İstif Pazarı - Forklift ve İstif Makineleri Merkezi</div>
    </div>

    {{-- PRODUCT TITLE --}}
    <div class="product-title">
        {{ $title }}
    </div>

    {{-- SHORT DESCRIPTION --}}
    @if($shortDescription)
    <div class="description">
        {{ $shortDescription }}
    </div>
    @endif

    {{-- GALLERY NOTE --}}
    @if($galleryImages && $galleryImages->count() > 0)
    <div class="gallery-note">
        📷 Bu ürüne ait {{ $galleryImages->count() }} adet görsel web sitemizde mevcuttur.
    </div>
    @endif

    {{-- LONG DESCRIPTION --}}
    @if($longDescription)
    <div class="section no-break">
        <div class="section-title">Ürün Açıklaması</div>
        <div class="description">
            {!! nl2br(strip_tags($longDescription)) !!}
        </div>
    </div>
    @endif

    {{-- PRIMARY SPECS --}}
    @if(!empty($primarySpecs))
    <div class="section no-break">
        <div class="section-title">Temel Özellikler</div>
        @foreach($primarySpecs as $spec)
        <div style="padding: 10px 0; border-bottom: 1px solid #e2e8f0;">
            <span class="spec-icon">⚡</span>
            <div style="display: inline-block; vertical-align: top; width: 80%;">
                <span class="spec-label">{{ $spec['label'] }}</span>
                <span class="spec-value">{{ $spec['value'] }}</span>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- HIGHLIGHTED FEATURES --}}
    @if(!empty($highlightedFeatures))
    <div class="section">
        <div class="section-title">Öne Çıkan Özellikler</div>
        @foreach($highlightedFeatures as $feature)
        <div class="feature-item">
            @if($feature['title'])
            <div class="feature-title">✨ {{ $feature['title'] }}</div>
            @endif
            @if($feature['description'])
            <div class="feature-desc">{{ $feature['description'] }}</div>
            @endif
        </div>
        @endforeach
    </div>
    @endif

    {{-- FEATURES LIST --}}
    @if(!empty($featuresList))
    <div class="section">
        <div class="section-title">Ürün Özellikleri</div>
        @foreach($featuresList as $feature)
        <div class="list-item">{{ $feature['text'] }}</div>
        @endforeach
    </div>
    @endif

    {{-- COMPETITIVE ADVANTAGES --}}
    @if(!empty($competitiveAdvantages))
    <div class="section">
        <div class="section-title">Rekabet Avantajları</div>
        @foreach($competitiveAdvantages as $advantage)
        <div class="list-item">{{ $advantage['text'] }}</div>
        @endforeach
    </div>
    @endif

    {{-- TECHNICAL SPECS --}}
    @if(!empty($technicalSpecs))
    <div class="section">
        <div class="section-title">Detaylı Teknik Özellikler</div>
        <table class="tech-table">
            @php
                $isFlat = !collect($technicalSpecs)->contains(fn($v) => is_array($v));
            @endphp

            @if($isFlat)
                <tr>
                    <td class="tech-header" colspan="2">Teknik Özellikler</td>
                </tr>
                @foreach($technicalSpecs as $key => $value)
                <tr class="tech-row">
                    <td class="tech-label">{{ $key }}</td>
                    <td class="tech-value">{{ is_array($value) ? json_encode($value) : $value }}</td>
                </tr>
                @endforeach
            @else
                @foreach($technicalSpecs as $sectionKey => $sectionValues)
                    @if(is_array($sectionValues) && !empty($sectionValues))
                        @php
                            $sectionTitle = $sectionValues['_title'] ?? \Illuminate\Support\Str::headline(str_replace('_', ' ', $sectionKey));
                            if(isset($sectionValues['_title'])) unset($sectionValues['_title']);
                            if(isset($sectionValues['_icon'])) unset($sectionValues['_icon']);
                        @endphp
                        <tr>
                            <td class="tech-header" colspan="2">{{ $sectionTitle }}</td>
                        </tr>
                        @foreach($sectionValues as $key => $value)
                        <tr class="tech-row">
                            <td class="tech-label">{{ $key }}</td>
                            <td class="tech-value">{{ is_array($value) ? json_encode($value) : $value }}</td>
                        </tr>
                        @endforeach
                    @endif
                @endforeach
            @endif
        </table>
    </div>
    @endif

    {{-- USE CASES --}}
    @if(!empty($useCases))
    <div class="section">
        <div class="section-title">Kullanım Alanları</div>
        @foreach($useCases as $case)
        <div class="list-item">{{ $case['text'] }}</div>
        @endforeach
    </div>
    @endif

    {{-- TARGET INDUSTRIES --}}
    @if(!empty($targetIndustries))
    <div class="section">
        <div class="section-title">Hedef Sektörler</div>
        @foreach($targetIndustries as $industry)
        <div class="list-item">{{ $industry['text'] }}</div>
        @endforeach
    </div>
    @endif

    {{-- ACCESSORIES --}}
    @if(!empty($accessories))
    <div class="section">
        <div class="section-title">Aksesuarlar ve Opsiyonlar</div>
        @foreach($accessories as $accessory)
        <div class="feature-item">
            <div class="feature-title">
                @if($accessory['is_standard'] ?? false)
                    ✓ {{ $accessory['name'] }} <span style="color: #10b981;">(Standart)</span>
                @else
                    + {{ $accessory['name'] }} <span style="color: #667eea;">(Opsiyonel)</span>
                @endif
            </div>
            @if(!empty($accessory['description']))
            <div class="feature-desc">{{ $accessory['description'] }}</div>
            @endif
        </div>
        @endforeach
    </div>
    @endif

    {{-- CERTIFICATIONS --}}
    @if(!empty($certifications))
    <div class="section">
        <div class="section-title">Sertifikalar</div>
        @foreach($certifications as $cert)
        <div class="list-item">
            {{ $cert['name'] }}
            @if(!empty($cert['year']))
                ({{ $cert['year'] }})
            @endif
        </div>
        @endforeach
    </div>
    @endif

    {{-- WARRANTY --}}
    @if($warrantyInfo)
    <div class="section no-break">
        <div class="section-title">Garanti Bilgisi</div>
        <div class="warranty-box">
            <div class="warranty-title">🛡️ Garanti Kapsamı</div>
            <div class="warranty-text">
                @if(is_array($warrantyInfo))
                    @if($warrantyInfo['coverage'] ?? false)
                        {{ $warrantyInfo['coverage'] }}
                    @endif
                    @if($warrantyInfo['duration_months'] ?? false)
                        <br><strong>{{ $warrantyInfo['duration_months'] }} ay garanti</strong>
                    @endif
                @else
                    {{ $warrantyInfo }}
                @endif
            </div>
        </div>
    </div>
    @endif

    {{-- FAQ --}}
    @if($faqEntries && $faqEntries->isNotEmpty())
    <div class="section">
        <div class="section-title">Sık Sorulan Sorular</div>
        @foreach($faqEntries as $faq)
        <div class="faq-item no-break">
            <div class="faq-question">❓ {{ $faq['question'] }}</div>
            <div class="faq-answer">{{ $faq['answer'] }}</div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- CONTACT INFO --}}
    <div class="section no-break">
        <div class="section-title">İletişim Bilgileri</div>
        <div class="feature-item">
            <div class="feature-title">📞 Telefon</div>
            <div class="feature-desc">0216 755 3 555</div>
        </div>
        <div class="feature-item">
            <div class="feature-title">📱 WhatsApp</div>
            <div class="feature-desc">0501 005 67 58</div>
        </div>
        <div class="feature-item">
            <div class="feature-title">✉️ E-posta</div>
            <div class="feature-desc">info@ixtif.com</div>
        </div>
    </div>

    {{-- FOOTER --}}
    <div class="footer">
        <p><strong>İXTİF - Türkiye'nin İstif Pazarı</strong></p>
        <p>Forklift, İstif ve Depo Makineleri Merkezi</p>
        <p style="margin-top: 10px;">
            Bu katalog {{ date('d.m.Y H:i') }} tarihinde oluşturulmuştur.<br>
            Güncel bilgiler ve fiyatlar için lütfen iletişime geçiniz.
        </p>
    </div>
</body>
</html>

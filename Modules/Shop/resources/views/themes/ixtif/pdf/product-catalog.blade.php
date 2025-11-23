<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $productTitle }} - {{ $siteTitle }} Katalog</title>
    <style>
        @page {
            margin: 0;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #1f2937;
        }

        /* ===== FIRST PAGE ===== */
        .first-page {
            width: 100%;
            height: 297mm;
            position: relative;
            page-break-after: always;
        }

        .first-page-left {
            position: absolute;
            left: 0;
            top: 0;
            width: 35%;
            height: 100%;
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
            color: white;
            padding: 40px 30px;
        }

        .first-page-right {
            position: absolute;
            right: 0;
            top: 0;
            width: 65%;
            height: 100%;
            background: white;
            padding: 50px 40px;
        }

        .logo {
            max-height: 60px;
            max-width: 150px;
            margin-bottom: 30px;
        }

        .site-title-text {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 30px;
        }

        .tagline {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .domain {
            font-size: 14px;
            opacity: 0.8;
            margin-bottom: 40px;
        }

        .contact-info {
            position: absolute;
            bottom: 40px;
            left: 30px;
            font-size: 11px;
        }

        .contact-info div {
            margin-bottom: 8px;
        }

        .catalog-badge {
            display: inline-block;
            background: #fed7aa;
            color: #9a3412;
            padding: 8px 20px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 20px;
        }

        .product-title {
            font-size: 32px;
            font-weight: bold;
            color: #0f172a;
            margin-bottom: 20px;
            line-height: 1.2;
        }

        .title-underline {
            width: 150px;
            height: 4px;
            background: linear-gradient(90deg, #f97316 0%, transparent 100%);
            margin-bottom: 20px;
            border-radius: 2px;
        }

        .subtitle {
            font-size: 14px;
            color: #64748b;
            margin-bottom: 30px;
        }

        .product-image-container {
            border: 3px solid #fed7aa;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            background: #f8fafc;
        }

        .product-image {
            max-width: 100%;
            max-height: 250px;
        }

        .catalog-date {
            font-size: 10px;
            opacity: 0.7;
            margin-top: 20px;
        }

        /* ===== CONTENT PAGES ===== */
        .content-page {
            padding: 30px 40px;
            page-break-after: always;
        }

        .section-title {
            font-size: 18px;
            font-weight: bold;
            color: #f97316;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #fed7aa;
        }

        .description {
            margin-bottom: 30px;
            text-align: justify;
        }

        .specs-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .specs-table th,
        .specs-table td {
            padding: 10px 15px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }

        .specs-table th {
            background: #f8fafc;
            font-weight: bold;
            color: #374151;
            width: 40%;
        }

        .specs-table td {
            color: #4b5563;
        }

        .specs-table tr:nth-child(even) {
            background: #f9fafb;
        }

        .features-list {
            list-style: none;
            padding: 0;
        }

        .features-list li {
            padding: 8px 0 8px 25px;
            position: relative;
            border-bottom: 1px solid #f3f4f6;
        }

        .features-list li:before {
            content: "\2713";
            position: absolute;
            left: 0;
            color: #10b981;
            font-weight: bold;
        }

        .info-box {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px 20px;
            margin: 20px 0;
            font-size: 11px;
        }

        /* ===== LAST PAGE ===== */
        .last-page {
            width: 100%;
            height: 297mm;
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 50%, #0f172a 100%);
            color: white;
            padding: 50px;
            position: relative;
        }

        .last-page-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .last-page-logo {
            max-height: 50px;
            margin-bottom: 15px;
        }

        .last-page-tagline {
            font-size: 16px;
            color: #93c5fd;
        }

        .contact-grid {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }

        .contact-box {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding: 20px;
        }

        .contact-box-inner {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            padding: 25px;
        }

        .contact-box h3 {
            font-size: 16px;
            margin-bottom: 20px;
            color: #93c5fd;
        }

        .contact-item {
            margin-bottom: 15px;
        }

        .contact-label {
            font-size: 10px;
            color: #93c5fd;
            margin-bottom: 3px;
        }

        .contact-value {
            font-size: 14px;
            font-weight: bold;
        }

        .services-grid {
            display: table;
            width: 100%;
        }

        .service-item {
            display: table-cell;
            width: 33.33%;
            text-align: center;
            padding: 10px;
        }

        .service-icon {
            font-size: 20px;
            margin-bottom: 5px;
        }

        .service-name {
            font-size: 11px;
            font-weight: bold;
        }

        .last-page-footer {
            position: absolute;
            bottom: 40px;
            left: 50px;
            right: 50px;
            text-align: center;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 20px;
        }

        .footer-text {
            font-size: 10px;
            color: #93c5fd;
            margin-bottom: 5px;
        }

        .footer-copyright {
            font-size: 9px;
            color: #64748b;
        }
    </style>
</head>
<body>

    <!-- FIRST PAGE -->
    <div class="first-page">
        <div class="first-page-left">
            @if($logoBase64)
                <img src="{{ $logoBase64 }}" alt="{{ $siteTitle }}" class="logo">
            @else
                <div class="site-title-text">{{ $siteTitle }}</div>
            @endif

            <div class="tagline">{{ $siteTagline ?? $siteTitle }}</div>
            <div class="domain">{{ $siteDomain }}</div>

            <div class="contact-info">
                @if($sitePhone)
                    <div>Tel: {{ $sitePhone }}</div>
                @endif
                <div>{{ $siteEmail }}</div>
                <div class="catalog-date">Katalog: {{ $catalogDate }}</div>
            </div>
        </div>

        <div class="first-page-right">
            <div class="catalog-badge">Urun Katalogu</div>

            <h1 class="product-title">{{ $productTitle }}</h1>

            <div class="title-underline"></div>

            <p class="subtitle">Detayli teknik ozellikler ve urun bilgileri</p>

            @if($productImageBase64)
                <div class="product-image-container">
                    <img src="{{ $productImageBase64 }}" alt="{{ $productTitle }}" class="product-image">
                </div>
            @endif
        </div>
    </div>

    <!-- CONTENT PAGE -->
    <div class="content-page">
        @if($categoryName || $brandName)
            <div class="info-box">
                @if($categoryName)
                    <strong>Kategori:</strong> {{ $categoryName }}
                @endif
                @if($categoryName && $brandName) | @endif
                @if($brandName)
                    <strong>Marka:</strong> {{ $brandName }}
                @endif
            </div>
        @endif

        @if($shortDescription)
            <div class="section-title">Urun Ozeti</div>
            <div class="description">
                {!! strip_tags($shortDescription) !!}
            </div>
        @endif

        @if($description)
            <div class="section-title">Urun Aciklamasi</div>
            <div class="description">
                {!! strip_tags($description) !!}
            </div>
        @endif

        @if(is_array($specifications) && count($specifications) > 0)
            <div class="section-title">Teknik Ozellikler</div>
            <table class="specs-table">
                @foreach($specifications as $key => $value)
                    @if($value)
                        <tr>
                            <th>{{ is_string($key) ? ucfirst(str_replace('_', ' ', $key)) : $key }}</th>
                            <td>{{ is_array($value) ? implode(', ', $value) : $value }}</td>
                        </tr>
                    @endif
                @endforeach
            </table>
        @endif

        @if(is_array($features) && count($features) > 0)
            <div class="section-title">Ozellikler</div>
            <ul class="features-list">
                @foreach($features as $feature)
                    <li>{{ is_array($feature) ? ($feature['title'] ?? $feature['name'] ?? json_encode($feature)) : $feature }}</li>
                @endforeach
            </ul>
        @endif
    </div>

    <!-- LAST PAGE -->
    <div class="last-page">
        <div class="last-page-header">
            @if($logoBase64)
                <img src="{{ $logoBase64 }}" alt="{{ $siteTitle }}" class="last-page-logo">
            @else
                <div style="font-size: 28px; font-weight: bold;">{{ $siteTitle }}</div>
            @endif
            <div class="last-page-tagline">{{ $siteTagline ?? $siteTitle }}</div>
        </div>

        <div class="contact-grid">
            <div class="contact-box">
                <div class="contact-box-inner">
                    <h3>Iletisim Bilgileri</h3>

                    @if($sitePhone)
                        <div class="contact-item">
                            <div class="contact-label">Telefon</div>
                            <div class="contact-value">{{ $sitePhone }}</div>
                        </div>
                    @endif

                    @if($siteWhatsapp)
                        <div class="contact-item">
                            <div class="contact-label">WhatsApp</div>
                            <div class="contact-value">{{ $siteWhatsapp }}</div>
                        </div>
                    @endif

                    <div class="contact-item">
                        <div class="contact-label">E-posta</div>
                        <div class="contact-value">{{ $siteEmail }}</div>
                    </div>

                    <div class="contact-item">
                        <div class="contact-label">Web</div>
                        <div class="contact-value">{{ $siteDomain }}</div>
                    </div>
                </div>
            </div>

            <div class="contact-box">
                <div class="contact-box-inner">
                    <h3>Hizmetlerimiz</h3>
                    <div class="services-grid">
                        <div class="service-item">
                            <div class="service-name">Sifir Urunler</div>
                        </div>
                        <div class="service-item">
                            <div class="service-name">Ikinci El</div>
                        </div>
                        <div class="service-item">
                            <div class="service-name">Kiralama</div>
                        </div>
                    </div>
                    <div class="services-grid" style="margin-top: 10px;">
                        <div class="service-item">
                            <div class="service-name">Teknik Servis</div>
                        </div>
                        <div class="service-item">
                            <div class="service-name">Yedek Parca</div>
                        </div>
                        <div class="service-item">
                            <div class="service-name">Bakim</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="last-page-footer">
            <div class="footer-text">Bu katalog {{ $catalogDate }} tarihinde olusturulmustur.</div>
            <div class="footer-text">Guncel fiyatlar ve detayli bilgi icin lutfen bizimle iletisime geciniz.</div>
            <div class="footer-copyright">&copy; {{ date('Y') }} {{ $siteTitle }} | Tum haklari saklidir.</div>
        </div>
    </div>

</body>
</html>

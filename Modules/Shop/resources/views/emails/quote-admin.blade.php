<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yeni Teklif Talebi</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: white;
            padding: 30px;
            border-radius: 8px 8px 0 0;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            background: #f9fafb;
            padding: 30px;
            border: 1px solid #e5e7eb;
            border-top: none;
        }
        .product-info {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #2563eb;
        }
        .customer-info {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .info-row {
            display: flex;
            padding: 10px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: 600;
            width: 140px;
            color: #6b7280;
        }
        .info-value {
            flex: 1;
            color: #111827;
        }
        .message-box {
            background: white;
            padding: 20px;
            border-radius: 8px;
            white-space: pre-wrap;
            border: 1px solid #e5e7eb;
        }
        .footer {
            text-align: center;
            padding: 20px;
            color: #6b7280;
            font-size: 14px;
            border-radius: 0 0 8px 8px;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-top: none;
        }
        .cta-button {
            display: inline-block;
            background: #2563eb;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 6px;
            margin-top: 10px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🎯 Yeni Teklif Talebi</h1>
        <p style="margin: 10px 0 0 0; opacity: 0.9;">Bir müşteri ürün hakkında teklif talep etti</p>
    </div>

    <div class="content">
        <div class="product-info">
            <h2 style="margin-top: 0; color: #2563eb; font-size: 18px;">📦 Ürün Bilgileri</h2>
            <div class="info-row">
                <div class="info-label">Ürün Adı:</div>
                <div class="info-value"><strong>{{ $data['product_title'] }}</strong></div>
            </div>
            <div class="info-row">
                <div class="info-label">Ürün ID:</div>
                <div class="info-value">{{ $data['product_id'] }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">SKU:</div>
                <div class="info-value">{{ $product->sku }}</div>
            </div>
        </div>

        <div class="customer-info">
            <h2 style="margin-top: 0; color: #059669; font-size: 18px;">👤 Müşteri Bilgileri</h2>
            <div class="info-row">
                <div class="info-label">Ad Soyad:</div>
                <div class="info-value"><strong>{{ $data['name'] }}</strong></div>
            </div>
            <div class="info-row">
                <div class="info-label">E-posta:</div>
                <div class="info-value">
                    <a href="mailto:{{ $data['email'] }}" style="color: #2563eb;">{{ $data['email'] }}</a>
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Telefon:</div>
                <div class="info-value">
                    <a href="tel:{{ $data['phone'] }}" style="color: #2563eb;">{{ $data['phone'] }}</a>
                </div>
            </div>
        </div>

        @if(!empty($data['message']))
        <div style="margin-bottom: 20px;">
            <h3 style="color: #374151; font-size: 16px;">💬 Müşteri Mesajı</h3>
            <div class="message-box">{{ $data['message'] }}</div>
        </div>
        @endif

        <div style="text-align: center; margin-top: 30px;">
            <a href="mailto:{{ $data['email'] }}" class="cta-button">
                Müşteriyle İletişime Geç
            </a>
        </div>
    </div>

    <div class="footer">
        <p style="margin: 0;">Bu email otomatik olarak oluşturulmuştur.</p>
        <p style="margin: 10px 0 0 0;">
            <strong>İXTİF</strong> | 0216 755 3 555 | info@ixtif.com
        </p>
    </div>
</body>
</html>

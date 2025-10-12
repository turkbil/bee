<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teklif Talebiniz Alındı</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f3f4f6;
        }
        .container {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0 0 10px 0;
            font-size: 28px;
        }
        .header p {
            margin: 0;
            opacity: 0.95;
            font-size: 16px;
        }
        .content {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 18px;
            color: #111827;
            margin-bottom: 20px;
        }
        .product-box {
            background: #f9fafb;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            padding: 20px;
            margin: 30px 0;
        }
        .product-box h3 {
            margin: 0 0 15px 0;
            color: #2563eb;
            font-size: 16px;
        }
        .product-name {
            font-size: 20px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 10px;
        }
        .product-sku {
            color: #6b7280;
            font-size: 14px;
        }
        .info-box {
            background: #eff6ff;
            border-left: 4px solid #2563eb;
            padding: 20px;
            margin: 25px 0;
            border-radius: 4px;
        }
        .info-box p {
            margin: 0;
            color: #1e40af;
        }
        .next-steps {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 8px;
            padding: 20px;
            margin: 25px 0;
        }
        .next-steps h3 {
            color: #059669;
            margin-top: 0;
            font-size: 16px;
        }
        .next-steps ul {
            margin: 10px 0;
            padding-left: 20px;
        }
        .next-steps li {
            margin: 8px 0;
            color: #166534;
        }
        .contact-info {
            text-align: center;
            padding: 30px;
            background: #f9fafb;
            border-top: 2px solid #e5e7eb;
        }
        .contact-info h3 {
            color: #111827;
            margin-bottom: 15px;
        }
        .contact-button {
            display: inline-block;
            background: #2563eb;
            color: white;
            padding: 14px 35px;
            text-decoration: none;
            border-radius: 8px;
            margin: 10px 5px;
            font-weight: 600;
            font-size: 15px;
        }
        .contact-links {
            margin-top: 20px;
        }
        .contact-links a {
            color: #2563eb;
            text-decoration: none;
            margin: 0 10px;
            font-weight: 500;
        }
        .footer {
            text-align: center;
            padding: 25px;
            color: #6b7280;
            font-size: 13px;
            background: #f9fafb;
        }
        .footer p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✅ Talebiniz Alındı!</h1>
            <p>En kısa sürede size dönüş yapacağız</p>
        </div>

        <div class="content">
            <div class="greeting">
                Sayın <strong>{{ $data['name'] }}</strong>,
            </div>

            <p>Teklif talebiniz başarıyla alınmıştır. Uzman ekibimiz en kısa sürede sizinle iletişime geçecektir.</p>

            <div class="product-box">
                <h3>📦 Talep Ettiğiniz Ürün</h3>
                <div class="product-name">{{ $data['product_title'] }}</div>
                <div class="product-sku">Ürün Kodu: {{ $product->sku }}</div>
            </div>

            @if(!empty($data['message']))
            <div class="info-box">
                <p><strong>Notunuz:</strong> {{ $data['message'] }}</p>
            </div>
            @endif

            <div class="next-steps">
                <h3>🚀 Sonraki Adımlar</h3>
                <ul>
                    <li>Satış ekibimiz talebinizi inceleyecek</li>
                    <li>Size özel bir fiyat teklifi hazırlayacağız</li>
                    <li>24 saat içinde size geri dönüş yapacağız</li>
                    <li>Detaylı ürün bilgileri ve teslimat süreleri paylaşılacak</li>
                </ul>
            </div>

            <p style="color: #6b7280; font-size: 14px; margin-top: 30px;">
                Acil ihtiyaç durumunda bizi hemen arayabilirsiniz. Uzman ekibimiz size yardımcı olmaktan mutluluk duyacaktır.
            </p>
        </div>

        <div class="contact-info">
            <h3>Bize Ulaşın</h3>
            <div>
                <a href="tel:02167553555" class="contact-button">📞 0216 755 3 555</a>
            </div>
            <div class="contact-links">
                <a href="mailto:info@ixtif.com">✉️ info@ixtif.com</a>
                <span style="color: #d1d5db;">|</span>
                <a href="{{ config('app.url') }}">🌐 Web Sitemiz</a>
            </div>
        </div>

        <div class="footer">
            <p><strong>İXTİF Endüstriyel Ekipmanlar</strong></p>
            <p>Profesyonel Forklift ve Depolama Çözümleri</p>
            <p style="margin-top: 15px; color: #9ca3af;">
                Bu e-posta {{ $data['email'] }} adresine gönderilmiştir.
            </p>
        </div>
    </div>
</body>
</html>

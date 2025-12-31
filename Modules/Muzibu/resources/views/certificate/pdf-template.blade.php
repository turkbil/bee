<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Muzibu Premium Sertifikası - {{ $certificate->certificate_code }}</title>
    <style>
        @page {
            margin: 0;
            size: A4 landscape;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
            color: #e2e8f0;
            width: 297mm;
            height: 210mm;
        }
        .certificate {
            width: 100%;
            height: 100%;
            padding: 15mm;
            position: relative;
        }
        .outer-border {
            border: 3px solid rgba(251, 191, 36, 0.6);
            border-radius: 8px;
            padding: 8px;
            height: 100%;
        }
        .inner-border {
            border: 1.5px solid rgba(251, 191, 36, 0.4);
            border-radius: 6px;
            padding: 20px;
            height: 100%;
            position: relative;
        }
        /* Corner decorations */
        .corner {
            position: absolute;
            width: 30px;
            height: 30px;
            border-color: rgba(251, 191, 36, 0.6);
            border-style: solid;
        }
        .corner-tl { top: 8px; left: 8px; border-width: 3px 0 0 3px; border-radius: 8px 0 0 0; }
        .corner-tr { top: 8px; right: 8px; border-width: 3px 3px 0 0; border-radius: 0 8px 0 0; }
        .corner-bl { bottom: 8px; left: 8px; border-width: 0 0 3px 3px; border-radius: 0 0 0 8px; }
        .corner-br { bottom: 8px; right: 8px; border-width: 0 3px 3px 0; border-radius: 0 0 8px 0; }

        .content {
            display: table;
            width: 100%;
            height: 100%;
        }
        .left-section {
            display: table-cell;
            width: 100px;
            vertical-align: middle;
            text-align: center;
            border-right: 1px solid rgba(251, 191, 36, 0.3);
            padding-right: 15px;
        }
        .center-section {
            display: table-cell;
            vertical-align: middle;
            padding: 0 20px;
        }
        .right-section {
            display: table-cell;
            width: 120px;
            vertical-align: middle;
            text-align: center;
            border-left: 1px solid rgba(251, 191, 36, 0.3);
            padding-left: 15px;
        }
        .logo-circle {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #a855f7 0%, #ec4899 50%, #a855f7 100%);
            border-radius: 50%;
            margin: 0 auto 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .logo-text {
            color: white;
            font-size: 24px;
            font-weight: bold;
        }
        .brand-name {
            font-size: 20px;
            font-weight: bold;
            color: #fbbf24;
            margin-bottom: 2px;
        }
        .brand-sub {
            font-size: 10px;
            color: rgba(251, 191, 36, 0.6);
            text-transform: uppercase;
            letter-spacing: 3px;
        }
        .certificate-type {
            font-size: 11px;
            color: rgba(251, 191, 36, 0.6);
            text-transform: uppercase;
            letter-spacing: 4px;
            margin-bottom: 5px;
        }
        .certificate-title {
            font-size: 28px;
            font-weight: bold;
            color: white;
            margin-bottom: 15px;
        }
        .intro-text {
            font-size: 12px;
            color: #94a3b8;
            font-style: italic;
            margin-bottom: 5px;
        }
        .member-name {
            font-size: 22px;
            font-weight: bold;
            color: white;
            border-bottom: 2px solid #fbbf24;
            padding-bottom: 5px;
            display: inline-block;
            margin-bottom: 5px;
        }
        .confirmation-text {
            font-size: 12px;
            color: #94a3b8;
            margin-bottom: 15px;
        }
        .info-box {
            background: rgba(30, 41, 59, 0.5);
            border-radius: 6px;
            padding: 12px;
            margin-bottom: 15px;
        }
        .info-row {
            display: table;
            width: 100%;
            margin-bottom: 8px;
        }
        .info-cell {
            display: table-cell;
            width: 50%;
            padding: 0 5px;
        }
        .info-label {
            font-size: 9px;
            color: #64748b;
            margin-bottom: 2px;
        }
        .info-value {
            font-size: 11px;
            color: #e2e8f0;
        }
        .info-full {
            width: 100%;
            margin-top: 8px;
        }
        .date-section {
            text-align: center;
        }
        .date-row {
            display: inline-block;
            margin: 0 20px;
        }
        .date-label {
            font-size: 9px;
            color: #64748b;
            text-transform: uppercase;
            margin-bottom: 2px;
        }
        .date-value {
            font-size: 12px;
            color: #e2e8f0;
        }
        .date-note {
            font-size: 10px;
            color: #fbbf24;
        }
        .qr-box {
            background: white;
            padding: 8px;
            border-radius: 6px;
            display: inline-block;
            margin-bottom: 8px;
        }
        .qr-box img {
            width: 80px;
            height: 80px;
        }
        .qr-text {
            font-size: 9px;
            color: #64748b;
            margin-bottom: 8px;
        }
        .cert-code-label {
            font-size: 8px;
            color: #64748b;
        }
        .cert-code {
            font-size: 10px;
            font-weight: bold;
            color: #fbbf24;
            font-family: monospace;
        }
        .footer {
            position: absolute;
            bottom: 5px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9px;
            color: #475569;
        }
    </style>
</head>
<body>
    <div class="certificate">
        <div class="outer-border">
            <div class="inner-border">
                <div class="corner corner-tl"></div>
                <div class="corner corner-tr"></div>
                <div class="corner corner-bl"></div>
                <div class="corner corner-br"></div>

                <div class="content">
                    <!-- Left: Logo -->
                    <div class="left-section">
                        <div class="logo-circle">
                            <span class="logo-text">M</span>
                        </div>
                        <div class="brand-name">Muzibu</div>
                        <div class="brand-sub">Premium</div>
                    </div>

                    <!-- Center: Content -->
                    <div class="center-section">
                        <div style="text-align: center;">
                            <div class="certificate-type">Premium Üyelik</div>
                            <div class="certificate-title">SERTİFİKASI</div>

                            <div class="intro-text">Bu belge,</div>
                            <div class="member-name">{{ $certificate->member_name }}</div>
                            <div class="confirmation-text">firmasının Muzibu Premium üyesi olduğunu tasdik eder.</div>

                            <div class="info-box">
                                <div class="info-row">
                                    <div class="info-cell">
                                        <div class="info-label">Vergi Dairesi</div>
                                        <div class="info-value">{{ $certificate->tax_office ?: '-' }}</div>
                                    </div>
                                    <div class="info-cell">
                                        <div class="info-label">Vergi No</div>
                                        <div class="info-value">{{ $certificate->tax_number ?: '-' }}</div>
                                    </div>
                                </div>
                                @if($certificate->address)
                                <div class="info-full">
                                    <div class="info-label">Adres</div>
                                    <div class="info-value" style="font-size: 10px;">{{ $certificate->address }}</div>
                                </div>
                                @endif
                            </div>

                            <div class="date-section">
                                <div class="date-row">
                                    <div class="date-label">Üyelik Başlangıç</div>
                                    <div class="date-value">{{ $certificate->membership_start->format('d.m.Y') }}</div>
                                </div>
                                <span style="color: rgba(251, 191, 36, 0.3);">|</span>
                                <div class="date-row">
                                    <div class="date-label">Güncel Durum</div>
                                    <div class="date-note">QR Kod ile Doğrulayın</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right: QR -->
                    <div class="right-section">
                        <div class="qr-box">
                            <img src="{{ $qrBase64 }}" alt="QR Code">
                        </div>
                        <div class="qr-text">Güncel durumu<br>görmek için tarayın</div>
                        <div class="cert-code-label">Sertifika No</div>
                        <div class="cert-code">{{ $certificate->certificate_code }}</div>
                    </div>
                </div>

                <div class="footer">
                    muzibu.com.tr/muzibu/certificate
                </div>
            </div>
        </div>
    </div>
</body>
</html>

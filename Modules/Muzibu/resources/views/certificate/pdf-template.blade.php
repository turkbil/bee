<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Muzibu Belge - {{ $certificate->certificate_code }}</title>
    <style>
        @page { margin: 0; size: A4 landscape; }
        body { font-family: 'DejaVu Sans', sans-serif; margin: 10mm; background: #fff; }
        .wrapper { border: 4px solid #d4af37; background: #1e293b; padding: 4mm; }
        .inner { border: 2px solid #b8860b; padding: 10mm 12mm; }
        table { width: 100%; border-collapse: collapse; }
        .left { width: 68mm; text-align: center; vertical-align: middle; border-right: 2px solid #d4af37; padding: 8mm 5mm; }
        .right { vertical-align: middle; padding: 6mm 12mm; }
        .logo { width: 24mm; height: 24mm; background: #d4af37; border-radius: 50%; margin: 0 auto 5mm; line-height: 24mm; font-size: 13mm; font-weight: bold; color: #1e293b; }
        .brand { font-size: 8mm; font-weight: bold; color: #d4af37; margin-bottom: 2mm; }
        .sub { font-size: 3.5mm; color: #94a3b8; letter-spacing: 2mm; margin-bottom: 8mm; }
        .qr { background: #fff; padding: 3mm; display: inline-block; margin-bottom: 3mm; }
        .qr img { width: 30mm; height: 30mm; }
        .hint { font-size: 2.5mm; color: #64748b; }
        .title { font-size: 10mm; font-weight: bold; color: #fff; text-align: center; margin-bottom: 2mm; }
        .subtitle { font-size: 3.5mm; color: #94a3b8; text-align: center; margin-bottom: 8mm; letter-spacing: 1.5mm; }
        .intro { font-size: 3mm; color: #64748b; text-align: center; margin-bottom: 2mm; }
        .name { font-size: 7mm; font-weight: bold; color: #fff; font-family: 'DejaVu Sans Mono', monospace; border-bottom: 3px solid #d4af37; padding-bottom: 2mm; margin: 3mm 0; display: inline-block; }
        .outro { font-size: 3mm; color: #64748b; text-align: center; margin-bottom: 10mm; }
        .info { width: 100%; background: #334155; border-collapse: collapse; }
        .info td { border: 1px solid #475569; padding: 4mm; text-align: center; }
        .lbl { font-size: 2.5mm; color: #94a3b8; margin-bottom: 1mm; }
        .val { font-size: 4.5mm; color: #fff; }
        .gold { color: #d4af37; font-family: 'DejaVu Sans Mono', monospace; }
        .addr { background: #334155; border: 1px solid #475569; padding: 4mm; margin-top: 5mm; text-align: left; }
        .addr-lbl { font-size: 2.5mm; color: #94a3b8; margin-bottom: 1mm; }
        .addr-txt { font-size: 3.5mm; color: #cbd5e1; }
        .footer { text-align: center; margin-top: 10mm; font-size: 3mm; color: #64748b; }
        .footer b { color: #d4af37; }
    </style>
</head>
<body>
<div class="wrapper"><div class="inner">
<table><tr>
<td class="left">
    <div class="logo">M</div>
    <div class="brand">MUZIBU</div>
    <div class="sub">PREMIUM</div>
    <div class="qr"><img src="{{ $qrBase64 }}"></div>
    <div class="hint">Tarayarak doğrulayın</div>
</td>
<td class="right">
    <div class="title">ÜYELİK SERTİFİKASI</div>
    <div class="subtitle">MEMBERSHIP CERTIFICATE</div>
    <div style="text-align:center;">
        <div class="intro">BU BELGE</div>
        <div class="name">{{ $certificate->member_name }}</div>
        <div class="outro">ADINA DÜZENLENMİŞTİR</div>
    </div>
    <table class="info"><tr>
        <td><div class="lbl">SERTİFİKA NO</div><div class="val gold">{{ $certificate->certificate_code }}</div></td>
        <td><div class="lbl">ÜYELİK TARİHİ</div><div class="val">{{ $certificate->membership_start->format('d.m.Y') }}</div></td>
        @if($certificate->tax_office)<td><div class="lbl">VERGİ DAİRESİ</div><div class="val">{{ $certificate->tax_office }}</div></td>@endif
        @if($certificate->tax_number)<td><div class="lbl">VERGİ NO</div><div class="val">{{ $certificate->tax_number }}</div></td>@endif
    </tr></table>
    @if($certificate->address)<div class="addr"><div class="addr-lbl">KAYITLI ADRES</div><div class="addr-txt">{{ $certificate->address }}</div></div>@endif
    <div class="footer">Bu belge Muzibu A.Ş. tarafından elektronik ortamda düzenlenmiştir. · <b>muzibu.com</b></div>
</td>
</tr></table>
</div></div>
</body>
</html>

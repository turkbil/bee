<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ setting('site_title', setting('site_name', config('app.name'))) }} - Ödeme</title>
</head>
<body>
<div style="width: 100%; margin: 0 auto; display: table;">
    <script src="https://www.paytr.com/js/iframeResizer.min.js"></script>
    <iframe src="{{ $paymentIframeUrl }}" id="paytriframe" frameborder="0" scrolling="no" style="width: 100%;"></iframe>
    <script>iFrameResize({}, '#paytriframe');</script>
</div>
</body>
</html>

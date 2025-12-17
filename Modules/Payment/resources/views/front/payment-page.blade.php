<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ setting('site_title', setting('site_name', config('app.name'))) }} - Ödeme</title>

    <style>
    body {
        margin: 0;
        padding: 0;
        overflow: hidden;
    }
    #paytriframe {
        width: 100vw;
        height: 100vh;
        min-height: 100vh;
        border: 0;
        display: block;
    }
</style>
</head>
<body>
<iframe
    src="{{ $paymentIframeUrl }}"
    id="paytriframe"
    frameborder="0"
    scrolling="auto"
    allow="payment">
</iframe>

<script>
    // PayTR iframe auto height & status
    window.addEventListener('message', function(event) {
        if (event.data && event.data.height) {
            document.getElementById('paytriframe').style.height = event.data.height + 'px';
        }
        if (event.data && event.data.paytr_status) {
            if (event.data.paytr_status === 'success') {
                window.location.href = '{{ route("payment.success") }}';
            } else if (event.data.paytr_status === 'failed') {
                window.location.href = '{{ route("cart.checkout") }}?payment=failed';
            }
        }
    }, false);
</script>
</body>
</html>

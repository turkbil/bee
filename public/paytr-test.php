<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Test verileri
$merchant_id = '636312';
$merchant_key = 'PHD9F4jwxgfT7NPo';
$merchant_salt = 'DTx136YXM6Jkx832';

$email = "test@test.com";
$payment_amount = 10050; // 100.50 TL (kuruş cinsinden)
$merchant_oid = "TEST" . time() . rand(1000, 9999); // Alfanumerik, özel karakter yok
$user_name = "Test User";
$user_address = "Test Address, Istanbul, Turkey";
$user_phone = "5551234567";
$merchant_ok_url = "https://ixtif.com/payment-success";
$merchant_fail_url = "https://ixtif.com/payment-fail";

// Sepet
$user_basket = base64_encode(json_encode([
    ["Test Product", "100.50", 1]
]));

$user_ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
$timeout_limit = "30";
$debug_on = 1;
$test_mode = 1; // TEST MODU
$no_installment = 0;
$max_installment = 12;
$currency = "TL";

$hash_str = $merchant_id . $user_ip . $merchant_oid . $email . $payment_amount . $user_basket . $no_installment . $max_installment . $currency . $test_mode;
$paytr_token = base64_encode(hash_hmac('sha256', $hash_str . $merchant_salt, $merchant_key, true));

$post_vals = [
    'merchant_id' => $merchant_id,
    'user_ip' => $user_ip,
    'merchant_oid' => $merchant_oid,
    'email' => $email,
    'payment_amount' => $payment_amount,
    'paytr_token' => $paytr_token,
    'user_basket' => $user_basket,
    'debug_on' => $debug_on,
    'no_installment' => $no_installment,
    'max_installment' => $max_installment,
    'user_name' => $user_name,
    'user_address' => $user_address,
    'user_phone' => $user_phone,
    'merchant_ok_url' => $merchant_ok_url,
    'merchant_fail_url' => $merchant_fail_url,
    'timeout_limit' => $timeout_limit,
    'currency' => $currency,
    'test_mode' => $test_mode
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://www.paytr.com/odeme/api/get-token");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_vals));
curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 20);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

$result = curl_exec($ch);
curl_close($ch);

$response = json_decode($result, 1);

if ($response['status'] == 'success') {
    $token = $response['token'];
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>PayTR Test - iFrame</title>
        <script src="https://www.paytr.com/js/iframeResizer.min.js"></script>
    </head>
    <body style="margin: 0; padding: 20px; font-family: Arial;">
        <h1>PayTR Test Ödeme (TEST MODE)</h1>
        <p>Tutar: 100.50 TL</p>
        <p>Sipariş: <?php echo $merchant_oid; ?></p>
        <hr>
        <iframe src="https://www.paytr.com/odeme/guvenli/<?php echo $token; ?>" id="paytriframe" frameborder="0" scrolling="no" style="width: 100%;"></iframe>
        <script>iFrameResize({}, '#paytriframe');</script>
    </body>
    </html>
    <?php
} else {
    echo "HATA: " . $response['reason'];
}

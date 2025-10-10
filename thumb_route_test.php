<?php
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$host = 'ixtif.com.tr';
$encoded = rtrim(strtr(base64_encode('storage/tenant3/settings/files/site-logo-hMXeed.png'), '+/', '-_'), '=');
$server = [
    'HTTP_HOST' => $host,
    'REQUEST_SCHEME' => 'https',
    'HTTPS' => 'on',
    'HTTP_X_FORWARDED_PROTO' => 'https',
];
$request = Illuminate\Http\Request::create('/thumbmaker/'.$encoded.'/320/200/75', 'GET', [], [], [], $server);
$response = $kernel->handle($request);

echo 'status='.$response->getStatusCode().PHP_EOL;
foreach ($response->headers->allPreserveCase() as $key => $values) {
    echo $key.': '.implode(', ', $values).PHP_EOL;
}

$kernel->terminate($request, $response);

<?php
require 'vendor/autoload.php';
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

putenv('CACHE_DRIVER=array');
putenv('QUEUE_CONNECTION=sync');
putenv('DB_CONNECTION=sqlite');
putenv('DB_DATABASE=database/database.sqlite');

$app = require 'bootstrap/app.php';

$consoleKernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$consoleKernel->bootstrap();

Config::set('database.connections.sqlite.database', 'database/database.sqlite');
Config::set('database.default', 'sqlite');

$httpKernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$uris = [
    '/shop',
    '/shop/test-product',
    '/shop/category/kategori',
    '/shop/brand/marka',
];

foreach ($uris as $uri) {
    try {
        $request = Request::create($uri, 'GET');
        $response = $httpKernel->handle($request);
        $status = $response->getStatusCode();
        if ($status !== 200) {
            $content = substr($response->getContent(), 0, 200);
            echo $uri . ' => ' . $status . ' CONTENT: ' . $content . PHP_EOL;
        } else {
            echo $uri . ' => ' . $status . PHP_EOL;
        }
        $httpKernel->terminate($request, $response);
    } catch (Throwable $e) {
        echo $uri . ' => ERROR: ' . $e->getMessage() . PHP_EOL;
    }
}

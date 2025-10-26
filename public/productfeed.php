<?php

header('Content-Type: application/xml; charset=utf-8');

require '../vendor/autoload.php';
$app = require_once '../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Get tenant from domain
$domain = $_SERVER['HTTP_HOST'] ?? 'ixtif.com';
$baseUrl = 'https://' . $domain;
$tenantDomain = \Stancl\Tenancy\Database\Models\Domain::where('domain', $domain)->first();

if ($tenantDomain && $tenantDomain->tenant) {
    tenancy()->initialize($tenantDomain->tenant);
}

// Get settings
$companyName = setting('company_name') ?? setting('site_name') ?? 'Company';
$siteUrl = $baseUrl;
$description = setting('site_description') ?? $companyName . ' - Google Shopping Feed';

// Build XML
$xml = '<?xml version="1.0" encoding="UTF-8"?>';
$xml .= '<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">';
$xml .= '<channel>';
$xml .= '<title>' . htmlspecialchars($companyName, ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</title>';
$xml .= '<link>' . htmlspecialchars($siteUrl, ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</link>';
$xml .= '<description>' . htmlspecialchars($description, ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</description>';

// Get products from DB
try {
    $products = DB::table('shop_products')
        ->select('product_id', 'title', 'slug', 'short_description', 'base_price', 'currency', 'condition')
        ->where('is_active', 1)
        ->whereNotNull('base_price')
        ->where('base_price', '>', 0)
        ->limit(10)
        ->get();

    foreach ($products as $product) {
        // Parse JSON
        $titleData = json_decode($product->title, true);
        $title = is_array($titleData) ? ($titleData['tr'] ?? $titleData['en'] ?? 'Product') : $product->title;

        $slugData = json_decode($product->slug, true);
        $slug = is_array($slugData) ? ($slugData['tr'] ?? $slugData['en'] ?? $product->product_id) : $product->slug;

        $descData = json_decode($product->short_description ?? '{}', true);
        $desc = is_array($descData) ? ($descData['tr'] ?? $descData['en'] ?? strip_tags($title)) : strip_tags($title);

        $productUrl = $baseUrl . '/shop/' . $slug;
        $price = number_format($product->base_price, 2, '.', '');
        $currency = $product->currency ?? 'TRY';

        $xml .= '<item>';
        $xml .= '<g:id>' . htmlspecialchars($product->product_id, ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</g:id>';
        $xml .= '<g:title>' . htmlspecialchars($title, ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</g:title>';
        $xml .= '<g:description>' . htmlspecialchars($desc, ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</g:description>';
        $xml .= '<g:link>' . htmlspecialchars($productUrl, ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</g:link>';
        $xml .= '<g:price>' . $price . ' ' . $currency . '</g:price>';
        $xml .= '<g:availability>in stock</g:availability>';
        $xml .= '<g:condition>' . ($product->condition ?? 'new') . '</g:condition>';
        $xml .= '<g:brand>' . htmlspecialchars($companyName, ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</g:brand>';
        $xml .= '</item>';
    }
} catch (Exception $e) {
    // Error handling
    $xml .= '<!-- Error: ' . htmlspecialchars($e->getMessage(), ENT_XML1) . ' -->';
}

$xml .= '</channel>';
$xml .= '</rss>';

echo $xml;

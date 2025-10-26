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

// Get products from DB with brand JOIN
try {
    $products = DB::table('shop_products as p')
        ->leftJoin('shop_brands as b', 'p.brand_id', '=', 'b.brand_id')
        ->select(
            'p.product_id',
            'p.title',
            'p.slug',
            'p.short_description',
            'p.body',
            'p.base_price',
            'p.currency',
            'p.condition',
            'p.price_on_request',
            'b.title as brand_title'
        )
        ->where('p.is_active', 1)
        ->limit(500)
        ->get();

    foreach ($products as $product) {
        // Parse JSON title
        $titleData = json_decode($product->title, true);
        $title = is_array($titleData) ? ($titleData['tr'] ?? $titleData['en'] ?? 'Product') : $product->title;

        // Parse JSON slug
        $slugData = json_decode($product->slug, true);
        $slug = is_array($slugData) ? ($slugData['tr'] ?? $slugData['en'] ?? $product->product_id) : $product->slug;

        // Parse brand title
        $brandData = json_decode($product->brand_title ?? '{}', true);
        $brand = is_array($brandData) ? ($brandData['tr'] ?? $brandData['en'] ?? $companyName) : $companyName;

        // Parse description - use body first, then short_description
        $bodyData = json_decode($product->body ?? '{}', true);
        $bodyText = '';
        if (is_array($bodyData) && isset($bodyData['tr'])) {
            // Strip HTML tags and get text
            $bodyText = strip_tags($bodyData['tr']);
            // Remove extra whitespace
            $bodyText = preg_replace('/\s+/', ' ', $bodyText);
            $bodyText = trim($bodyText);
        }

        // Fallback to short_description
        if (empty($bodyText)) {
            $descData = json_decode($product->short_description ?? '{}', true);
            $bodyText = is_array($descData) ? ($descData['tr'] ?? $descData['en'] ?? '') : '';
            $bodyText = strip_tags($bodyText);
        }

        // Fallback to title
        if (empty($bodyText)) {
            $bodyText = strip_tags($title);
        }

        // Limit to 5000 characters (Google limit)
        $desc = mb_substr($bodyText, 0, 5000);

        // Product URL
        $productUrl = $baseUrl . '/shop/' . $slug;

        // Price handling
        if ($product->price_on_request || !$product->base_price || $product->base_price <= 0) {
            // Price on request - skip price field (Google allows this for some categories)
            $hasPrice = false;
            $price = 0;
            $currency = 'TRY';
        } else {
            $hasPrice = true;
            $price = number_format($product->base_price, 2, '.', '');
            $currency = $product->currency ?? 'TRY';
        }

        // Build item XML
        $xml .= '<item>';
        $xml .= '<g:id>' . htmlspecialchars($product->product_id, ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</g:id>';
        $xml .= '<g:title>' . htmlspecialchars($title, ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</g:title>';
        $xml .= '<g:description>' . htmlspecialchars($desc, ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</g:description>';
        $xml .= '<g:link>' . htmlspecialchars($productUrl, ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</g:link>';

        // Only add price if product has price
        if ($hasPrice) {
            $xml .= '<g:price>' . $price . ' ' . $currency . '</g:price>';
        }

        $xml .= '<g:availability>in stock</g:availability>';
        $xml .= '<g:condition>' . ($product->condition ?? 'new') . '</g:condition>';
        $xml .= '<g:brand>' . htmlspecialchars($brand, ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</g:brand>';
        $xml .= '</item>';
    }
} catch (Exception $e) {
    // Error handling
    $xml .= '<!-- Error: ' . htmlspecialchars($e->getMessage(), ENT_XML1) . ' -->';
}

$xml .= '</channel>';
$xml .= '</rss>';

echo $xml;

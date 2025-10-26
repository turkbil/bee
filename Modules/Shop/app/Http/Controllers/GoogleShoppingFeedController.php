<?php

declare(strict_types=1);

namespace Modules\Shop\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class GoogleShoppingFeedController extends Controller
{
    public function index(): Response
    {
        // Get tenant-aware settings
        $companyName = setting('company_name') ?? setting('site_name') ?? config('app.name');
        $siteUrl = url('/');
        $description = setting('site_description') ?? $companyName . ' - Google Shopping Feed';

        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">';
        $xml .= '<channel>';
        $xml .= '<title>' . htmlspecialchars($companyName, ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</title>';
        $xml .= '<link>' . htmlspecialchars($siteUrl, ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</link>';
        $xml .= '<description>' . htmlspecialchars($description, ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</description>';

        try {
            // Get products with price from database using DB facade
            $products = DB::table('shop_products')
                ->select('product_id', 'title', 'slug', 'short_description', 'base_price', 'currency', 'condition')
                ->where('is_active', 1)
                ->whereNotNull('base_price')
                ->where('base_price', '>', 0)
                ->limit(100)
                ->get();

            foreach ($products as $product) {
                // Parse JSON fields
                $titleData = json_decode($product->title, true);
                $title = is_array($titleData) ? ($titleData['tr'] ?? $titleData['en'] ?? 'Product') : $product->title;

                $slugData = json_decode($product->slug, true);
                $slug = is_array($slugData) ? ($slugData['tr'] ?? $slugData['en'] ?? $product->product_id) : $product->slug;

                $descData = json_decode($product->short_description ?? '{}', true);
                $description = is_array($descData) ? ($descData['tr'] ?? $descData['en'] ?? strip_tags($title)) : strip_tags($title);

                // Build product URL
                $productUrl = url('/shop/' . $slug);

                // Price
                $price = number_format($product->base_price, 2, '.', '');
                $currency = $product->currency ?? 'TRY';

                // Build item XML
                $xml .= '<item>';
                $xml .= '<g:id>' . htmlspecialchars($product->product_id, ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</g:id>';
                $xml .= '<g:title>' . htmlspecialchars($title, ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</g:title>';
                $xml .= '<g:description>' . htmlspecialchars($description, ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</g:description>';
                $xml .= '<g:link>' . htmlspecialchars($productUrl, ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</g:link>';
                $xml .= '<g:price>' . $price . ' ' . $currency . '</g:price>';
                $xml .= '<g:availability>in stock</g:availability>';
                $xml .= '<g:condition>' . ($product->condition ?? 'new') . '</g:condition>';
                $xml .= '<g:brand>' . htmlspecialchars($companyName, ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</g:brand>';
                $xml .= '</item>';
            }
        } catch (\Exception $e) {
            // If error occurs, log it
            \Log::error('Google Shopping Feed Error: ' . $e->getMessage());
        }

        $xml .= '</channel>';
        $xml .= '</rss>';

        return response($xml, 200)
            ->header('Content-Type', 'application/xml; charset=utf-8');
    }
}

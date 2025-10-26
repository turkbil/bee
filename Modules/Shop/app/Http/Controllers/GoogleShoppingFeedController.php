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

        // Get products using raw DB query (avoid model JSON issues)
        try {
            $products = DB::table('shop_products')
                ->select('product_id', 'slug', 'title', 'base_price', 'currency')
                ->where('is_active', 1)
                ->limit(100)
                ->get();

            foreach ($products as $product) {
                // Parse JSON title if needed
                $title = $product->title;
                if (is_string($title) && json_decode($title)) {
                    $titleArray = json_decode($title, true);
                    $title = $titleArray['tr'] ?? $titleArray['en'] ?? 'Product';
                }

                // Parse JSON slug if needed
                $slug = $product->slug;
                if (is_string($slug) && json_decode($slug)) {
                    $slugArray = json_decode($slug, true);
                    $slug = $slugArray['tr'] ?? $slugArray['en'] ?? $product->product_id;
                }

                $productUrl = url('/shop/' . $slug);
                $price = $product->base_price ?? 0;
                $currency = $product->currency ?? 'TRY';

                $xml .= '<item>';
                $xml .= '<g:id>' . htmlspecialchars($product->product_id, ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</g:id>';
                $xml .= '<g:title>' . htmlspecialchars($title, ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</g:title>';
                $xml .= '<g:link>' . htmlspecialchars($productUrl, ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</g:link>';
                $xml .= '<g:description>' . htmlspecialchars(strip_tags($title), ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</g:description>';
                $xml .= '<g:price>' . number_format($price, 2, '.', '') . ' ' . $currency . '</g:price>';
                $xml .= '<g:availability>in stock</g:availability>';
                $xml .= '<g:condition>new</g:condition>';
                $xml .= '</item>';
            }
        } catch (\Exception $e) {
            // If error, feed still works - just without products
        }

        $xml .= '</channel>';
        $xml .= '</rss>';

        return response($xml, 200)
            ->header('Content-Type', 'application/xml; charset=utf-8');
    }
}

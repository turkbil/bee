<?php

declare(strict_types=1);

namespace Modules\Shop\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class GoogleShoppingFeedController extends Controller
{
    /**
     * Generate Google Shopping XML Feed
     *
     * @return Response
     */
    public function index(): Response
    {
        // Get tenant-aware settings
        $companyName = setting('company_name') ?? setting('site_name') ?? config('app.name');
        $siteUrl = url('/');
        $description = setting('site_description') ?? $companyName . ' - Google Shopping Feed';

        // Build XML header
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">';
        $xml .= '<channel>';
        $xml .= '<title>' . htmlspecialchars($companyName, ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</title>';
        $xml .= '<link>' . htmlspecialchars($siteUrl, ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</link>';
        $xml .= '<description>' . htmlspecialchars($description, ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</description>';

        // Get products (RAW DB - safe from JSON issues)
        try {
            $products = DB::table('shop_products')
                ->select('product_id')
                ->where('is_active', 1)
                ->limit(10) // Test with 10 products first
                ->get();

            foreach ($products as $product) {
                $xml .= '<item>';
                $xml .= '<g:id>' . htmlspecialchars($product->product_id, ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</g:id>';
                $xml .= '<g:title>Product ' . htmlspecialchars($product->product_id, ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</g:title>';
                $xml .= '<g:link>' . htmlspecialchars(url('/shop/product/' . $product->product_id), ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</g:link>';
                $xml .= '<g:description>Product Description</g:description>';
                $xml .= '<g:price>1000.00 TRY</g:price>';
                $xml .= '<g:availability>in stock</g:availability>';
                $xml .= '<g:condition>new</g:condition>';
                $xml .= '</item>';
            }
        } catch (\Exception $e) {
            // If error, just log it - feed will work without products
            \Log::error('Google Shopping Feed - Product Error: ' . $e->getMessage());
        }

        $xml .= '</channel>';
        $xml .= '</rss>';

        return response($xml, 200)
            ->header('Content-Type', 'application/xml; charset=utf-8');
    }
}

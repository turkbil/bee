<?php

declare(strict_types=1);

namespace Modules\Shop\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Modules\Shop\App\Models\ShopProduct;

class GoogleShoppingFeedController extends Controller
{
    /**
     * Generate Google Shopping XML Feed
     *
     * @return Response
     */
    public function index(): Response
    {
        // SIMPLE TEST - No DB, no settings
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">';
        $xml .= '<channel>';
        $xml .= '<title>Test Feed</title>';
        $xml .= '<link>https://ixtif.com</link>';
        $xml .= '<description>Google Shopping Feed</description>';
        $xml .= '</channel>';
        $xml .= '</rss>';

        return response($xml, 200)
            ->header('Content-Type', 'application/xml; charset=utf-8');

        /*
        try {
            // Build XML header
            $companyName = setting('company_name') ?? setting('site_name') ?? config('app.name');

            $xml = '<?xml version="1.0" encoding="UTF-8"?>';
            $xml .= '<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">';
            $xml .= '<channel>';
            $xml .= '<title>' . $this->xmlEscape($companyName) . ' Products</title>';
            $xml .= '<link>' . $this->xmlEscape(url('/')) . '</link>';
            $xml .= '<description>Google Shopping Feed</description>';

            // Get product IDs only (avoid model issues)
            $productIds = ShopProduct::where('is_active', 1)->pluck('product_id')->take(100);

            // Generate items manually
            foreach ($productIds as $productId) {
                $xml .= '<item>';
                $xml .= '<g:id>' . $this->xmlEscape($productId) . '</g:id>';
                $xml .= '<g:title>Product ' . $this->xmlEscape($productId) . '</g:title>';
                $xml .= '<g:link>' . url('/shop/product/' . $productId) . '</g:link>';
                $xml .= '<g:description>Product description</g:description>';
                $xml .= '<g:price>1000.00 TRY</g:price>';
                $xml .= '<g:availability>in stock</g:availability>';
                $xml .= '<g:condition>new</g:condition>';
                $xml .= '</item>';
            }

            $xml .= '</channel>';
            $xml .= '</rss>';

            return response($xml, 200)
                ->header('Content-Type', 'application/xml; charset=utf-8');
        } catch (\Exception $e) {
            // Log error for debugging with full trace
            \Log::error('Google Shopping Feed Error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());

            // Return minimal valid XML
            $xml = '<?xml version="1.0" encoding="UTF-8"?>';
            $xml .= '<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">';
            $xml .= '<channel>';
            $xml .= '<title>Error</title>';
            $xml .= '<link>' . url('/') . '</link>';
            $xml .= '<description>Feed temporarily unavailable: ' . htmlspecialchars($e->getMessage()) . ' | File: ' . basename($e->getFile()) . ' | Line: ' . $e->getLine() . '</description>';
            $xml .= '</channel>';
            $xml .= '</rss>';

            return response($xml, 500)
                ->header('Content-Type', 'application/xml; charset=utf-8');
        }
    }

    /**
     * Generate XML from products
     *
     * @param \Illuminate\Database\Eloquent\Collection $products
     * @return string
     */
    private function generateXml($products): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">';
        $xml .= '<channel>';
        $xml .= '<title>' . $this->xmlEscape(setting('site_name') ?? config('app.name')) . '</title>';
        $xml .= '<link>' . $this->xmlEscape(url('/')) . '</link>';
        $xml .= '<description>' . $this->xmlEscape(setting('site_description') ?? setting('site_name') ?? 'Products') . '</description>';

        foreach ($products as $product) {
            $xml .= $this->generateProductItem($product);
        }

        $xml .= '</channel>';
        $xml .= '</rss>';

        return $xml;
    }

    /**
     * Generate XML item for a product
     *
     * @param ShopProduct $product
     * @return string
     */
    private function generateProductItem(ShopProduct $product): string
    {
        try {
            // SIMPLE VERSION - Just ID for now
            $productId = $product->product_id ?? $product->id;

            $xml = '<item>';
            $xml .= '<g:id>' . $this->xmlEscape($productId) . '</g:id>';
            $xml .= '<g:title>Product ' . $this->xmlEscape($productId) . '</g:title>';
            $xml .= '<g:link>' . url('/shop/product/' . $productId) . '</g:link>';
            $xml .= '<g:description>Product description</g:description>';
            $xml .= '<g:price>1000.00 TRY</g:price>';
            $xml .= '<g:availability>in stock</g:availability>';
            $xml .= '<g:condition>new</g:condition>';
            $xml .= '</item>';

            return $xml;

            // OLD CODE (PROBLEMATIC) - Keeping for reference
            /*
            $productUrl = url('/shop/' . $slug);

        // Get title (also JSON/array)
        $title = $product->getTranslation('title', app()->getLocale()) ?? $product->title;
        if (is_array($title)) {
            $title = $title['tr'] ?? $title['en'] ?? 'Product';
        }

        // Get description (also JSON/array)
        $description = $product->getTranslation('short_description', app()->getLocale())
            ?? $product->short_description
            ?? $product->body;

        if (is_array($description)) {
            $description = $description['tr'] ?? $description['en'] ?? '';
        }

        // Fallback: strip tags from body if description empty
        if (empty($description)) {
            $body = $product->body;
            if (is_array($body)) {
                $body = $body['tr'] ?? $body['en'] ?? '';
            }
            $description = strip_tags($body);
        }

        // Limit description to 5000 characters (Google limit)
        $description = mb_substr($description, 0, 5000);

        // Get featured image
        $imageUrl = $product->getFirstMediaUrl('featured')
            ?? $product->getFirstMediaUrl('gallery')
            ?? url('/images/placeholder.jpg');

        // Price handling
        $price = $product->base_price ?? 0;
        $currency = $product->currency ?? 'TRY';

        // Availability
        $availability = ($product->stock_status === 'in_stock' || $product->stock_quantity > 0)
            ? 'in stock'
            : 'out of stock';

        // Condition
        $condition = $product->condition ?? 'new';

        // Brand (null-safe, tenant-aware)
        $brand = optional($product->brand)->name ?? setting('company_name') ?? setting('site_name') ?? 'Products';

        // Category (null-safe)
        $categoryName = null;
        if ($product->category) {
            $categoryName = $product->category->getTranslation('name', app()->getLocale()) ?? $product->category->name;
            // If name is array, get Turkish
            if (is_array($categoryName)) {
                $categoryName = $categoryName['tr'] ?? $categoryName['en'] ?? null;
            }
        }
        $category = $categoryName ?? 'Products';

        $xml = '<item>';
        $xml .= '<g:id>' . $this->xmlEscape($product->product_id) . '</g:id>';
        $xml .= '<g:title>' . $this->xmlEscape($title) . '</g:title>';
        $xml .= '<g:description>' . $this->xmlEscape($description) . '</g:description>';
        $xml .= '<g:link>' . $this->xmlEscape($productUrl) . '</g:link>';
        $xml .= '<g:image_link>' . $this->xmlEscape($imageUrl) . '</g:image_link>';

        if ($product->price_on_request) {
            // For price on request products, don't include price
            $xml .= '<g:availability>in stock</g:availability>';
        } else {
            $xml .= '<g:price>' . number_format($price, 2, '.', '') . ' ' . $currency . '</g:price>';
            $xml .= '<g:availability>' . $availability . '</g:availability>';
        }

        $xml .= '<g:condition>' . $condition . '</g:condition>';
        $xml .= '<g:brand>' . $this->xmlEscape($brand) . '</g:brand>';
        $xml .= '<g:product_type>' . $this->xmlEscape($category) . '</g:product_type>';

        // GTIN / MPN (optional but recommended)
        if ($product->barcode) {
            $xml .= '<g:gtin>' . $this->xmlEscape($product->barcode) . '</g:gtin>';
        }
        if ($product->model_number) {
            $xml .= '<g:mpn>' . $this->xmlEscape($product->model_number) . '</g:mpn>';
        }

        $xml .= '</item>';

        return $xml;

        } catch (\Exception $e) {
            \Log::error('Error generating product item: ' . $product->product_id . ' - ' . $e->getMessage());
            \Log::error('Product data: ' . json_encode([
                'id' => $product->product_id,
                'slug' => $product->slug,
                'title' => $product->title,
            ]));
            return ''; // Skip this product
        }
    }

    /**
     * Escape XML special characters
     *
     * @param string|null $value
     * @return string
     */
    private function xmlEscape(?string $value): string
    {
        if ($value === null) {
            return '';
        }

        return htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}

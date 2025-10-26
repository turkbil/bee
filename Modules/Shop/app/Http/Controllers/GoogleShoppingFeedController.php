<?php

declare(strict_types=1);

namespace Modules\Shop\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

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

        // Add sample product for testing
        // TODO: Add real products from database
        $xml .= '<item>';
        $xml .= '<g:id>TEST001</g:id>';
        $xml .= '<g:title>Test Product</g:title>';
        $xml .= '<g:link>' . htmlspecialchars(url('/shop/test'), ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</g:link>';
        $xml .= '<g:description>Test Product Description</g:description>';
        $xml .= '<g:price>1000.00 TRY</g:price>';
        $xml .= '<g:availability>in stock</g:availability>';
        $xml .= '<g:condition>new</g:condition>';
        $xml .= '</item>';

        $xml .= '</channel>';
        $xml .= '</rss>';

        return response($xml, 200)
            ->header('Content-Type', 'application/xml; charset=utf-8');
    }
}

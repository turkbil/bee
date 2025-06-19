<?php

namespace App\Services;

use Spatie\SchemaOrg\Schema;

class SEOService
{
    /**
     * Website için genel Organization schema
     */
    public static function getOrganizationSchema(): string
    {
        $organization = Schema::organization()
            ->name(config('app.name'))
            ->url(url('/'))
            ->sameAs([
                // Sosyal medya linkleri buraya
            ]);

        return $organization->toScript();
    }

    /**
     * Sayfa için WebPage schema
     */
    public static function getWebPageSchema(string $title, string $description, string $url): string
    {
        $webPage = Schema::webPage()
            ->name($title)
            ->description($description)
            ->url($url);

        return $webPage->toScript();
    }

    /**
     * Blog yazısı için Article schema
     */
    public static function getArticleSchema(string $title, string $description, string $url, $datePublished = null): string
    {
        $article = Schema::article()
            ->headline($title)
            ->description($description)
            ->url($url);

        if ($datePublished) {
            $article->datePublished($datePublished);
        }

        return $article->toScript();
    }

    /**
     * Portfolio item için CreativeWork schema
     */
    public static function getPortfolioSchema(string $title, string $description, string $url, string $image = null): string
    {
        $portfolio = Schema::creativeWork()
            ->name($title)
            ->description($description)
            ->url($url);

        if ($image) {
            $portfolio->image($image);
        }

        return $portfolio->toScript();
    }

    /**
     * BreadcrumbList schema
     */
    public static function getBreadcrumbSchema(array $breadcrumbs): string
    {
        $breadcrumbList = Schema::breadcrumbList();
        
        foreach ($breadcrumbs as $index => $breadcrumb) {
            $breadcrumbList->itemListElement([
                Schema::listItem()
                    ->position($index + 1)
                    ->name($breadcrumb['name'])
                    ->item($breadcrumb['url'])
            ]);
        }

        return $breadcrumbList->toScript();
    }

    /**
     * Page model için otomatik schema
     */
    public static function getPageSchema($page): string
    {
        if (!$page) {
            return '';
        }

        $webPage = Schema::webPage()
            ->name($page->title)
            ->url(url()->current());

        if ($page->excerpt) {
            $webPage->description($page->excerpt);
        }

        if ($page->updated_at) {
            $webPage->dateModified($page->updated_at->toISOString());
        }

        return $webPage->toScript();
    }
}
<?php

declare(strict_types=1);

namespace Modules\Shop\App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShopResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $locale = $request->get('locale', app()->getLocale());

        return [
            'id' => $this->product_id,
            'title' => $this->getTranslated('title', $locale),
            'slug' => $this->getTranslated('slug', $locale),
            'short_description' => $this->getTranslated('short_description', $locale),
            'body' => $this->getTranslated('body', $locale),
            'category' => $this->whenLoaded('category', fn() => $this->category?->getTranslated('title', $locale)),
            'brand' => $this->whenLoaded('brand', fn() => $this->brand?->getTranslated('title', $locale)),
            'price_on_request' => (bool) $this->price_on_request,
            'base_price' => $this->base_price,
            'compare_at_price' => $this->compare_at_price,
            'currency' => $this->currency,
            'is_active' => (bool) $this->is_active,
            'is_featured' => (bool) $this->is_featured,
            'published_at' => $this->published_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}

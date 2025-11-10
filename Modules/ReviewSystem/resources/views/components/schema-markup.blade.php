{{--
    Google Schema.org JSON-LD Markup Component
    Kullanım:
    @include('reviewsystem::components.schema-markup', [
        'model' => $product,
        'productName' => $product->title,
        'productDescription' => $product->description,
        'productImage' => $product->image_url,
        'productPrice' => $product->price,
        'productCurrency' => 'TRY',
        'productAvailability' => 'InStock' // InStock, OutOfStock, PreOrder
    ])
--}}

@php
    use Modules\ReviewSystem\App\Services\ReviewService;

    $modelClass = get_class($model);
    $modelId = $model->id;
    $reviewService = app(ReviewService::class);
    $aggregateRating = $reviewService->getSchemaMarkup($modelClass, $modelId);

    // Varsayılan değerler
    $productName = $productName ?? $model->title ?? $model->name ?? 'Product';
    $productDescription = $productDescription ?? $model->description ?? '';
    $productImage = $productImage ?? ($model->image_url ?? '');
    $productPrice = $productPrice ?? ($model->price ?? null);
    $productCurrency = $productCurrency ?? 'TRY';
    $productAvailability = $productAvailability ?? 'InStock';

    // Schema availability mapping
    $availabilityMap = [
        'InStock' => 'https://schema.org/InStock',
        'OutOfStock' => 'https://schema.org/OutOfStock',
        'PreOrder' => 'https://schema.org/PreOrder',
        'Discontinued' => 'https://schema.org/Discontinued',
        'LimitedAvailability' => 'https://schema.org/LimitedAvailability'
    ];

    $schemaData = [
        '@@context' => 'https://schema.org/',
        '@@type' => 'Product',
        'name' => $productName,
        'description' => strip_tags($productDescription),
    ];

    if ($productImage) {
        $schemaData['image'] = $productImage;
    }

    if ($productPrice) {
        $schemaData['offers'] = [
            '@@type' => 'Offer',
            'url' => url()->current(),
            'priceCurrency' => $productCurrency,
            'price' => number_format($productPrice, 2, '.', ''),
            'availability' => $availabilityMap[$productAvailability] ?? $availabilityMap['InStock'],
        ];
    }

    if ($aggregateRating) {
        $schemaData['aggregateRating'] = $aggregateRating;
    }
@endphp

@if($aggregateRating || $productPrice)
<script type="application/ld+json">
{!! json_encode($schemaData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>
@endif

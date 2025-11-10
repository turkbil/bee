<?php

use Illuminate\Support\Facades\Route;
use Modules\ReviewSystem\App\Services\ReviewService;

// API rotaları - Review işlemleri
Route::middleware(['api', 'tenant'])
    ->prefix('api/reviews')
    ->name('api.reviews.')
    ->group(function () {

        // Rating ekle
        Route::post('/rating', function(\Illuminate\Http\Request $request) {
            $service = app(ReviewService::class);

            $result = $service->addRating(
                $request->input('model_class'),
                $request->input('model_id'),
                $request->input('rating_value'),
                auth()->id()
            );

            return response()->json($result);
        })->middleware('auth:sanctum')->name('rating');

        // Review ekle
        Route::post('/add', function(\Illuminate\Http\Request $request) {
            $service = app(ReviewService::class);

            $result = $service->addReview([
                'model_class' => $request->input('model_class'),
                'model_id' => $request->input('model_id'),
                'review_body' => $request->input('review_body'),
                'rating_value' => $request->input('rating_value'),
                'parent_id' => $request->input('parent_id'),
            ]);

            return response()->json($result);
        })->middleware('auth:sanctum')->name('add');

        // Review listesi
        Route::get('/{modelClass}/{modelId}', function($modelClass, $modelId) {
            $service = app(ReviewService::class);

            $reviews = $service->getReviews($modelClass, $modelId);

            return response()->json($reviews);
        })->name('list');
    });

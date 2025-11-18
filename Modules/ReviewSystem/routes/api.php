<?php

use Illuminate\Support\Facades\Route;
use Modules\ReviewSystem\App\Services\ReviewService;

// API rotaları - Review işlemleri
Route::middleware(['web', 'tenant'])
    ->prefix('api/reviews')
    ->name('api.reviews.')
    ->group(function () {

        // Rating ekle (Web session destekli)
        Route::post('/rating', function(\Illuminate\Http\Request $request) {
            // Auth kontrolü - web session veya sanctum token
            if (!auth()->check() && !auth('sanctum')->check()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
            }

            $service = app(ReviewService::class);
            $userId = auth()->id() ?? auth('sanctum')->id();

            $result = $service->addRating(
                $request->input('model_class'),
                $request->input('model_id'),
                $request->input('rating_value'),
                $userId
            );

            return response()->json($result);
        })->name('rating');

        // Review ekle (Web session destekli)
        Route::post('/add', function(\Illuminate\Http\Request $request) {
            // Auth kontrolü - web session veya sanctum token
            if (!auth()->check() && !auth('sanctum')->check()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
            }

            $service = app(ReviewService::class);

            $result = $service->addReview([
                'model_class' => $request->input('model_class'),
                'model_id' => $request->input('model_id'),
                'review_body' => $request->input('review_body'),
                'rating_value' => $request->input('rating_value'),
                'parent_id' => $request->input('parent_id'),
            ]);

            return response()->json($result);
        })->name('add');

        // Review listesi
        Route::get('/{modelClass}/{modelId}', function($modelClass, $modelId) {
            $service = app(ReviewService::class);

            $reviews = $service->getReviews($modelClass, $modelId);

            return response()->json($reviews);
        })->name('list');

        // Mark review as helpful/unhelpful
        Route::post('/helpful/{reviewId}', function($reviewId, \Illuminate\Http\Request $request) {
            $service = app(ReviewService::class);
            $isHelpful = $request->input('is_helpful', true);

            $result = $service->markHelpful($reviewId, $isHelpful);

            return response()->json($result);
        })->name('helpful');
    });

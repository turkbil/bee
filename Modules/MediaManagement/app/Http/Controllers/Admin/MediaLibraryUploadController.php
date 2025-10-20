<?php

namespace Modules\MediaManagement\App\Http\Controllers\Admin;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\MediaManagement\App\Models\MediaLibraryItem;
use Modules\MediaManagement\App\Services\MediaService;
use Throwable;

class MediaLibraryUploadController extends Controller
{
    public function __construct(protected MediaService $mediaService)
    {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'files' => ['required', 'array', 'max:20'],
            'files.*' => ['file', 'max:204800'],
        ]);

        $uploaded = [];
        $errors = [];

        foreach ($validated['files'] as $file) {
            try {
                $type = $this->mediaService->getMediaTypeFromMime($file->getMimeType());
                $validationErrors = $this->mediaService->validateFile($file, $type);
                if (!empty($validationErrors)) {
                    $errors[] = [
                        'name' => $file->getClientOriginalName(),
                        'errors' => $validationErrors,
                    ];
                    continue;
                }

                $libraryItem = MediaLibraryItem::create([
                    'name' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
                    'type' => $type,
                    'created_by' => $request->user()?->id,
                    'meta' => [
                        'original_name' => $file->getClientOriginalName(),
                        'uploaded_via' => 'media-library',
                        'source' => 'ajax-upload',
                    ],
                ]);

                $media = $this->mediaService->uploadMedia($libraryItem, $file, 'library');

                if ($media) {
                    $libraryItem->update([
                        'media_id' => $media->id,
                        'type' => $type,
                    ]);

                    $uploaded[] = [
                        'id' => $media->id,
                        'name' => $media->name,
                        'url' => $media->getUrl(),
                        'thumb' => $media->hasGeneratedConversion('thumb') ? $media->getUrl('thumb') : $media->getUrl(),
                    ];
                } else {
                    $errors[] = [
                        'name' => $file->getClientOriginalName(),
                        'errors' => ['upload_failed'],
                    ];
                }
            } catch (Throwable $exception) {
                Log::error('Media library async upload failed', [
                    'error' => $exception->getMessage(),
                ]);

                $errors[] = [
                    'name' => $file->getClientOriginalName(),
                    'errors' => [$exception->getMessage()],
                ];
            }
        }

        return response()->json([
            'uploaded_count' => count($uploaded),
            'uploaded' => $uploaded,
            'errors' => $errors,
        ]);
    }
}

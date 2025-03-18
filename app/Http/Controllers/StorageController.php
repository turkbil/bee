<?php
// app/Http/Controllers/StorageController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class StorageController extends Controller
{
    public function tenantMedia($tenantId, $mediaId, $filename)
    {
        $path = storage_path("tenant{$tenantId}/app/public/{$mediaId}/{$filename}");
        
        if (!File::exists($path)) {
            abort(404, 'Medya dosyası bulunamadı');
        }
        
        $response = new BinaryFileResponse($path);
        $response->headers->set('Content-Type', File::mimeType($path));
        $response->setCache([
            'public' => true,
            'max_age' => 86400,
            'must_revalidate' => false,
        ]);
        
        return $response;
    }
}
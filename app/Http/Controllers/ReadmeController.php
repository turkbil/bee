<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ReadmeController extends Controller
{
    /**
     * Serve static HTML files from public/readme directory
     *
     * @param Request $request
     * @param string $path
     * @return Response
     */
    public function show(Request $request, $path = 'index.html')
    {
        // Build full file path
        $filePath = public_path('readme/' . $path);

        // If path is a directory, try to serve index.html from it
        if (is_dir($filePath)) {
            $filePath = rtrim($filePath, '/') . '/index.html';
        }

        // Check if file exists
        if (!file_exists($filePath) || !is_file($filePath)) {
            abort(404, 'Readme file not found');
        }

        // Get file contents
        $content = file_get_contents($filePath);

        // Determine content type based on extension
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $contentType = match($extension) {
            'html' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'md' => 'text/markdown',
            default => 'text/plain'
        };

        // Return response with appropriate headers
        return response($content, 200, [
            'Content-Type' => $contentType . '; charset=UTF-8',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ]);
    }
}

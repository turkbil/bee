<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DesignLibraryController extends Controller
{
    public function index()
    {
        $path = public_path('design/index.html');

        if (!file_exists($path)) {
            abort(404, 'Design library index not found');
        }

        return response()->file($path);
    }

    public function show($file)
    {
        $path = public_path('design/' . $file);

        if (!file_exists($path) || !is_file($path)) {
            abort(404, 'Design file not found: ' . $file);
        }

        return response()->file($path);
    }
}

<?php
// Auto-redirect to latest version
$version = getenv('README_VERSION') ?: 'v1';
$htmlFile = __DIR__ . '/' . $version . '/index.html';

if (file_exists($htmlFile)) {
    header('Content-Type: text/html; charset=UTF-8');
    readfile($htmlFile);
    exit;
}

// Fallback: try symlink
$symlinkFile = __DIR__ . '/index.html';
if (is_link($symlinkFile) && file_exists($symlinkFile)) {
    header('Content-Type: text/html; charset=UTF-8');
    readfile($symlinkFile);
    exit;
}

// 404
http_response_code(404);
echo '<!DOCTYPE html><html><head><title>404 Not Found</title></head><body><h1>404 Not Found</h1></body></html>';

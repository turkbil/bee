<?php
/**
 * Taslak Silme API
 * Sifre korumalı klasör silme
 */

header('Content-Type: application/json');

// Silme şifresi - sadece sen biliyorsun
define('DELETE_PASSWORD', 'nn');

// POST kontrolü
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die(json_encode(['success' => false, 'error' => 'Method not allowed']));
}

// JSON input
$input = json_decode(file_get_contents('php://input'), true);

$password = $input['password'] ?? '';
$templateId = $input['templateId'] ?? '';

// Şifre kontrolü
if ($password !== DELETE_PASSWORD) {
    http_response_code(403);
    die(json_encode(['success' => false, 'error' => 'Yanlis sifre!']));
}

// Template ID kontrolü
if (empty($templateId) || !preg_match('/^[a-z0-9\-]+\/v[0-9]+[a-z0-9\-]*$/i', $templateId)) {
    http_response_code(400);
    die(json_encode(['success' => false, 'error' => 'Gecersiz template ID']));
}

// Klasör yolu
$baseDir = __DIR__;
$templatePath = $baseDir . '/' . $templateId;

// Güvenlik kontrolü - base dizin dışına çıkma
$realPath = realpath($templatePath);
$realBase = realpath($baseDir);

if (!$realPath || strpos($realPath, $realBase) !== 0) {
    http_response_code(400);
    die(json_encode(['success' => false, 'error' => 'Gecersiz yol']));
}

// Klasör var mı?
if (!is_dir($templatePath)) {
    http_response_code(404);
    die(json_encode(['success' => false, 'error' => 'Klasor bulunamadi']));
}

// Recursive silme fonksiyonu
function deleteDirectory($dir) {
    if (!is_dir($dir)) return false;

    $files = array_diff(scandir($dir), ['.', '..']);
    foreach ($files as $file) {
        $path = $dir . '/' . $file;
        is_dir($path) ? deleteDirectory($path) : unlink($path);
    }
    return rmdir($dir);
}

// Sil
if (deleteDirectory($templatePath)) {
    // Kategori klasörü boş kaldıysa onu da sil
    $categoryPath = dirname($templatePath);
    $remaining = array_diff(scandir($categoryPath), ['.', '..']);
    if (empty($remaining)) {
        rmdir($categoryPath);
    }

    echo json_encode(['success' => true, 'message' => 'Taslak silindi: ' . $templateId]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Silme islemi basarisiz']);
}

<?php
/**
 * Template Action API
 * - Taslak silme (delete)
 * - Metadata kaydetme (saveMetadata, saveAllMetadata)
 */

header('Content-Type: application/json');

// Silme şifresi - hash ile korunuyor
define('DELETE_PASSWORD_HASH', '$2y$10$yfVqtNkw3aNQrlLGJ5i18ekhDZv94reeaQ.yZOrK/Fu9xB1Oq.JBq');

// POST kontrolü
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die(json_encode(['success' => false, 'error' => 'Method not allowed']));
}

// JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Action belirle
$action = $input['action'] ?? 'delete';

// Metadata dosya yolu
$metadataFile = __DIR__ . '/metadata.json';

// ============================================
// METADATA İŞLEMLERİ
// ============================================
if ($action === 'saveMetadata') {
    // Tek template metadata kaydet
    $templateId = $input['templateId'] ?? null;
    $metadata = $input['metadata'] ?? null;

    if (!$templateId || !is_array($metadata)) {
        http_response_code(400);
        die(json_encode(['success' => false, 'error' => 'Template ID and metadata required']));
    }

    // Mevcut metadata'yı oku
    $allMetadata = [];
    if (file_exists($metadataFile)) {
        $content = @file_get_contents($metadataFile);
        if ($content) {
            $allMetadata = json_decode($content, true) ?: [];
        }
    }

    // Yeni metadata'yı ekle/güncelle
    $allMetadata[$templateId] = $metadata;

    // Dosyaya yaz
    $json = json_encode($allMetadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    if (file_put_contents($metadataFile, $json, LOCK_EX) === false) {
        http_response_code(500);
        die(json_encode(['success' => false, 'error' => 'Failed to write metadata file']));
    }

    die(json_encode(['success' => true]));
}

if ($action === 'saveAllMetadata') {
    // Tüm metadata'yı toplu kaydet (localStorage migration için)
    $allData = $input['data'] ?? null;

    if (!is_array($allData)) {
        http_response_code(400);
        die(json_encode(['success' => false, 'error' => 'Data must be an object']));
    }

    // Dosyaya yaz
    $json = json_encode($allData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    if (file_put_contents($metadataFile, $json, LOCK_EX) === false) {
        http_response_code(500);
        die(json_encode(['success' => false, 'error' => 'Failed to write metadata file']));
    }

    die(json_encode(['success' => true, 'message' => 'All metadata saved']));
}

// ============================================
// SİLME İŞLEMİ (ESKİ KOD)
// ============================================

$password = $input['password'] ?? '';
$templateId = $input['templateId'] ?? '';

// Şifre kontrolü (hash ile)
if (!password_verify($password, DELETE_PASSWORD_HASH)) {
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

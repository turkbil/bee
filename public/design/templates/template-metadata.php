<?php
/**
 * Template Metadata API
 * Tasarım şablonlarının metadata'sını (puan, kategori, marka, not) kaydeder
 */

header('Content-Type: application/json');

$metadataFile = __DIR__ . '/metadata.json';

// POST request kontrolü
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

// JSON body'yi oku
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid JSON']);
    exit;
}

// Action'a göre işlem yap
$action = $data['action'] ?? null;

switch ($action) {
    case 'save':
        // Metadata'yı kaydet
        $templateId = $data['templateId'] ?? null;
        $metadata = $data['metadata'] ?? null;

        if (!$templateId || !is_array($metadata)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Template ID and metadata required']);
            exit;
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
            echo json_encode(['success' => false, 'error' => 'Failed to write metadata file']);
            exit;
        }

        echo json_encode(['success' => true]);
        break;

    case 'saveAll':
        // Tüm metadata'yı toplu kaydet (localStorage migration için)
        $allData = $data['data'] ?? null;

        if (!is_array($allData)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Data must be an object']);
            exit;
        }

        // Dosyaya yaz
        $json = json_encode($allData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        if (file_put_contents($metadataFile, $json, LOCK_EX) === false) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to write metadata file']);
            exit;
        }

        echo json_encode(['success' => true, 'message' => 'All metadata saved']);
        break;

    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid action']);
        break;
}

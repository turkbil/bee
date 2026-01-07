<?php
/**
 * Metadata API - GET/POST hybrid
 * GET: metadata.json döndür
 * POST: metadata kaydet
 */

$metadataFile = __DIR__ . '/metadata.json';

// GET - JSON döndür
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    header('Content-Type: application/json');
    if (file_exists($metadataFile)) {
        readfile($metadataFile);
    } else {
        echo '{}';
    }
    exit;
}

// POST - Kaydet
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    $input = json_decode(file_get_contents('php://input'), true);
    $action = $input['action'] ?? null;

    if ($action === 'saveMetadata') {
        $templateId = $input['templateId'] ?? null;
        $metadata = $input['metadata'] ?? null;

        if (!$templateId || !is_array($metadata)) {
            http_response_code(400);
            die(json_encode(['success' => false, 'error' => 'Invalid input']));
        }

        // Mevcut data'yı oku
        $allMetadata = [];
        if (file_exists($metadataFile)) {
            $content = @file_get_contents($metadataFile);
            if ($content) {
                $allMetadata = json_decode($content, true) ?: [];
            }
        }

        // Güncelle
        $allMetadata[$templateId] = $metadata;

        // Kaydet
        $json = json_encode($allMetadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        if (file_put_contents($metadataFile, $json, LOCK_EX) === false) {
            http_response_code(500);
            die(json_encode(['success' => false, 'error' => 'Write failed']));
        }

        die(json_encode(['success' => true]));
    }

    if ($action === 'saveAllMetadata') {
        $allData = $input['data'] ?? null;

        if (!is_array($allData)) {
            http_response_code(400);
            die(json_encode(['success' => false, 'error' => 'Invalid data']));
        }

        $json = json_encode($allData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        if (file_put_contents($metadataFile, $json, LOCK_EX) === false) {
            http_response_code(500);
            die(json_encode(['success' => false, 'error' => 'Write failed']));
        }

        die(json_encode(['success' => true]));
    }

    http_response_code(400);
    die(json_encode(['success' => false, 'error' => 'Invalid action']));
}

http_response_code(405);
die(json_encode(['success' => false, 'error' => 'Method not allowed']));

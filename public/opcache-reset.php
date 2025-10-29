<?php
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo json_encode(['success' => true, 'message' => 'OPcache reset']);
} else {
    echo json_encode(['success' => false, 'message' => 'OPcache not available']);
}

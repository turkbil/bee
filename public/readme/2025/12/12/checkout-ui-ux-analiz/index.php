<?php
// Otomatik redirect - en son versiyona yönlendir
$versions = glob(__DIR__ . '/v*', GLOB_ONLYDIR);
if (empty($versions)) {
    http_response_code(404);
    die('No versions found');
}
usort($versions, function($a, $b) {
    return version_compare(basename($b), basename($a));
});
$latest = basename($versions[0]);
header("Location: $latest/", true, 302);
exit;

<?php
require_once dirname(__FILE__) . '/../../../../../core/php/core.inc.php';

if (!isConnect()) {
    http_response_code(403);
    exit;
}

$csv = init('csv', '');
$filename = init('filename', 'export_' . date('Ymd_His'));

if (empty($csv)) {
    http_response_code(400);
    exit;
}

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

echo "\xEF\xBB\xBF";
echo $csv;

exit;
?>
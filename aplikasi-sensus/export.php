<?php
// export.php - Mengunduh file CSV sensus penduduk terbaru
require_once __DIR__ . '/config.php';

$filepath = get_csv_filepath();

if (!file_exists($filepath)) {
    die("File sensus CSV tidak ditemukan.");
}

$filename = "sensus_penduduk_indonesia_2024_export_" . date('Ymd_His') . ".csv";

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');
header('Expires: 0');

readfile($filepath);
exit;

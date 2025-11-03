<?php
// index.php
// Usage: put index.php and text.txt in same folder on PHP-enabled hosting.
// When user opens https://www.xyz.com/api it returns a JSON with one random line.

// Send JSON header
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *'); // optional: allows cross-origin access

// Determine requested path
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Normalize trailing slash
$uri = rtrim($uri, '/');

// We expect the API at /api (or root if you want change)
if ($uri !== '/api') {
    // If not /api, you can either show a small message or 404 JSON.
    http_response_code(404);
    echo json_encode([
        "status" => "error",
        "message" => "Not found. Use /api to get a random line."
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

// Path to the text file (same directory)
$file = __DIR__ . '/text.txt';

// Check file exists and is readable
if (!is_readable($file)) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "text.txt not found or not readable."
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

// Read file and split into lines
$contents = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

// Clean lines (trim)
$lines = array_map('trim', $contents);

// Remove any empty after trim
$lines = array_filter($lines, function($l){ return $l !== ''; });
$lines = array_values($lines); // reindex

if (count($lines) === 0) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "No lines found in text.txt"
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

// Pick a random line
$randomLine = $lines[array_rand($lines)];

// Build response
$response = [
    "status" => "success",
    "timestamp" => date('Y-m-d H:i:s'),
    "line" => $randomLine
];

// Output JSON
echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
exit;
?>

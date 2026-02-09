<?php

// Serves existing files directly, otherwise forwards to index.php (front controller).

$requested = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$fullPath = __DIR__ . $requested;

// If the request is for an existing file, let PHP serve it directly
if ($requested !== '/' && file_exists($fullPath) && is_file($fullPath)) {
    return false;
}

// Otherwise, dispatch to index.php (your front controller)
require __DIR__ . '/index.php';
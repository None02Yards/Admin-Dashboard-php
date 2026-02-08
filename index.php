<?php
// Simple front controller (no composer). Put this at project root.

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/app/core/Database.php';
require_once __DIR__ . '/app/core/Controller.php';
require_once __DIR__ . '/app/core/Auth.php';

// Autoload controllers and models
spl_autoload_register(function ($class) {
    $paths = [
        __DIR__ . '/app/controllers/' . $class . '.php',
        __DIR__ . '/app/models/' . $class . '.php',
    ];
    foreach ($paths as $file) {
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Start session
session_start();

// Basic routing: /controller/action/params...
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$base = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
$path = '/' . ltrim(substr($uri, strlen($base)), '/');
$segments = array_values(array_filter(explode('/', $path)));
$controllerName = !empty($segments[0]) ? ucfirst($segments[0]) . 'Controller' : 'VoteController';
$action = $segments[1] ?? 'index';
$params = array_slice($segments, 2);

// Ensure controller exists
if (!class_exists($controllerName)) {
    http_response_code(404);
    echo "Controller $controllerName not found.";
    exit;
}

$controller = new $controllerName();
if (!method_exists($controller, $action)) {
    http_response_code(404);
    echo "Action $action not found on controller $controllerName.";
    exit;
}

// Call action with params
call_user_func_array([$controller, $action], $params);
<?php

// Autoloader Simple (PSR-4 sim)
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../src/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

// Rutas
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Router básico
if ($requestUri === '/' || $requestUri === '/index.php') {
    // Servir vista principal
    require __DIR__ . '/../templates/dashboard.php';
} elseif ($requestUri === '/api/invoices/create' && $method === 'POST') {
    $controller = new \App\Controllers\InvoiceController();
    $controller->create();
} else {
    http_response_code(404);
    echo "404 Not Found";
}

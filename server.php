<?php

/**
 * Laravel - A PHP Framework for Web Artisans
 */

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? ''
);

// Determine target static file path
if (str_starts_with($uri, '/storage/')) {
    $relativePath = substr($uri, strlen('/storage/'));
    $publicFile = __DIR__ . '/storage/app/public/' . $relativePath;
} else {
    $publicFile = __DIR__ . '/public' . $uri;
}

// Serve static assets directly from public directory or storage
if ($uri !== '/' && file_exists($publicFile) && !is_dir($publicFile)) {
    $extension = strtolower(pathinfo($publicFile, PATHINFO_EXTENSION));
    $mimeTypes = [
        'css'   => 'text/css',
        'js'    => 'application/javascript',
        'png'   => 'image/png',
        'jpg'   => 'image/jpeg',
        'jpeg'  => 'image/jpeg',
        'gif'   => 'image/gif',
        'svg'   => 'image/svg+xml',
        'ico'   => 'image/x-icon',
        'woff'  => 'font/woff',
        'woff2' => 'font/woff2',
        'ttf'   => 'font/ttf',
        'json'  => 'application/json',
        'webp'  => 'image/webp',
        'pdf'   => 'application/pdf',
    ];

    if (isset($mimeTypes[$extension])) {
        header('Content-Type: ' . $mimeTypes[$extension]);
    }
    readfile($publicFile);
    exit;
}

require_once __DIR__ . '/public/index.php';

<?php
/**
 * Vercel PHP Router
 * This script routes all requests to the appropriate PHP file 
 * while maintaining compatibility with the root-relative structure.
 */

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// If the URI is just '/', serve index.php
if ($uri === '/' || $uri === '') {
    $file = '/index.php';
}
else {
    $file = $uri;
}

// Convert relative URI to absolute path in the project root
$root = dirname(__DIR__);
$filePath = $root . $file;

// If the file doesn't exist or is a directory, try adding .php
if (!file_exists($filePath) || is_dir($filePath)) {
    if (file_exists($filePath . '.php')) {
        $filePath .= '.php';
        $file .= '.php';
    }
    else {
        // Fallback to index.php or 404
        http_response_code(404);
        echo "404 Not Found: " . htmlspecialchars($file);
        exit;
    }
}

// Security: Prevent accessing files outside of root or sensitive files
if (strpos(realpath($filePath), $root) !== 0 || basename($filePath) === 'vercel.json' || basename($filePath) === '.env') {
    http_response_code(403);
    echo "403 Forbidden";
    exit;
}

// Mock $_SERVER variables to make the app think it's running in the root
$_SERVER['SCRIPT_NAME'] = $file;
$_SERVER['PHP_SELF'] = $file;
$_SERVER['SCRIPT_FILENAME'] = $filePath;

// Change directory to the file's directory to handle relative includes
chdir(dirname($filePath));

// Include the requested file
require $filePath;

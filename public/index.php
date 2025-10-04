<?php
// public/index.php - Front-Controller

// --- BOOTSTRAP ---
session_start();

// Define a base path for includes
define('BASE_PATH', dirname(__DIR__));

// Load configuration and core files
$config_path = BASE_PATH . '/config/config.php';
if (!file_exists($config_path)) {
    // If config doesn't exist, redirect to setup.
    $setup_path = rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . '/../setup.php';
    header('Location: ' . $setup_path);
    exit("Configuration file not found. Please run the setup script.");
}

$config = require $config_path;
require_once BASE_PATH . '/app/core/helpers.php';
require_once BASE_PATH . '/app/core/Database.php';


// --- ROUTING ---
$request_uri = $_GET['url'] ?? '';
$url_parts = explode('/', filter_var(rtrim($request_uri, '/'), FILTER_SANITIZE_URL));

// Default route for empty URL
if (empty($url_parts[0])) {
    if (isset($_SESSION['user_id'])) {
        redirect(base_url('dashboard'));
    } else {
        // The login form is at auth/index
        redirect(base_url('auth'));
    }
}

// Simple alias for /login to point to auth/index
if (strtolower($url_parts[0]) === 'login') {
    $url_parts[0] = 'auth';
    $url_parts[1] = 'index';
}

// Simple alias for /logout to point to auth/logout
if (strtolower($url_parts[0]) === 'logout') {
    $url_parts[0] = 'auth';
    $url_parts[1] = 'logout';
}

// Determine the controller
$controller_name = ucfirst(strtolower($url_parts[0])) . 'Controller';
$controller_file = BASE_PATH . '/app/controllers/' . $controller_name . '.php';

if (file_exists($controller_file)) {
    require_once $controller_file;

    if (class_exists($controller_name)) {
        $controller = new $controller_name($config);

        // Determine the method
        $method_name = isset($url_parts[1]) && !empty($url_parts[1]) ? strtolower($url_parts[1]) : 'index';

        if (method_exists($controller, $method_name)) {
            $params = array_slice($url_parts, 2);
            call_user_func_array([$controller, $method_name], $params);
        } else {
            http_response_code(404);
            echo "404 - Method Not Found";
        }
    } else {
        http_response_code(404);
        echo "404 - Controller Class Not Found";
    }
} else {
    http_response_code(404);
    echo "404 - Page Not Found";
}
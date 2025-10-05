<?php
// ---
// public/index.php - Main Application Entry Point (Front Controller)
// ---

session_start();

// --- Configuration and Autoloading ---

// Define a base path for cleaner includes
define('BASE_PATH', dirname(__DIR__));

// Load the database helper
require_once BASE_PATH . '/app/helpers/db.php';

// Load the configuration. It's safe to require because db.php checks for its existence.
$config = require BASE_PATH . '/config/config.php';
$base_url = $config['base_url'];

// --- Routing ---

// Get the requested URL, default to 'login'
$request_uri = $_GET['url'] ?? 'login';

// Simple, secure routing. Map URL slugs to controller files.
$routes = [
    'login' => 'login_controller.php',
    'logout' => 'logout_controller.php',
    'dashboard' => 'dashboard_controller.php',
    // Add other routes here as the application grows
    'upload' => 'upload_controller.php',
    'create_folder' => 'folder_controller.php',
    'delete' => 'delete_controller.php'
];

// --- Controller Dispatch ---

// Check if the requested route is valid
if (array_key_exists($request_uri, $routes)) {
    $controller_path = BASE_PATH . '/app/controllers/' . $routes[$request_uri];
    if (file_exists($controller_path)) {
        require_once $controller_path;
    } else {
        // This is a developer error, controller file is missing
        http_response_code(500);
        include(BASE_PATH . '/app/views/partials/header.php');
        echo "<div class='container'><div class='alert alert-error'>Error: Controller file missing for route '{$request_uri}'.</div></div>";
        include(BASE_PATH . '/app/views/partials/footer.php');
    }
} else {
    // Page not found
    http_response_code(404);
    // Before including the header, we need to set the page title for the 404 page
    $page_title = "404 Not Found";
    // We also check if the user is logged in to show the correct header
    $is_logged_in = isset($_SESSION['user_id']);
    // We can create a simple 404 view or just include the header and a message.
    // For now, let's just show a simple message.
    // To show a styled page, we would need to create a 404.php view.
    include_once BASE_PATH . '/app/views/partials/header.php';
    echo "<div class='container'><div class='alert alert-error'><h2>404 - Page Not Found</h2><p>The page you are looking for does not exist.</p><a href='{$base_url}/dashboard' class='btn'>Go to Dashboard</a></div></div>";
    include_once BASE_PATH . '/app/views/partials/footer.php';
}
?>
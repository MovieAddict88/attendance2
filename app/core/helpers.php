<?php
// app/core/helpers.php

if (!function_exists('view')) {
    /**
     * Load a view file with optional data.
     *
     * This function simplifies loading view files from the 'app/views' directory.
     * It can also extract an array of data into individual variables for use in the view.
     *
     * @param string $view_name The name of the view file (e.g., 'home/index').
     * @param array $data Data to be extracted and made available to the view.
     */
    function view($view_name, $data = []) {
        // Construct the full path to the view file
        $view_path = BASE_PATH . '/app/views/' . str_replace('.', '/', $view_name) . '.php';

        if (file_exists($view_path)) {
            // Extract the data array into individual variables (e.g., $data['title'] becomes $title)
            extract($data);

            // Include the view file
            require $view_path;
        } else {
            // Handle view not found error
            http_response_code(404);
            echo "Error: View '{$view_name}' not found at: {$view_path}";
        }
    }
}

if (!function_exists('redirect')) {
    /**
     * Redirect to a specified URL.
     *
     * @param string $url The URL to redirect to.
     */
    function redirect($url) {
        header('Location: ' . $url);
        exit;
    }
}

if (!function_exists('base_url')) {
    /**
     * Get the base URL of the application.
     *
     * @param string $path Optional path to append to the base URL.
     * @return string The full URL.
     */
    function base_url($path = '') {
        // This requires the config to be loaded. A bit of a hack for a helper,
        // might be better to set this as a constant in index.php.
        static $base_url = null;
        if ($base_url === null) {
            $config = require BASE_PATH . '/config/config.php';
            $base_url = $config['base_url'];
        }
        return rtrim($base_url, '/') . '/' . ltrim($path, '/');
    }
}

if (!function_exists('e')) {
    /**
     * Escape HTML special characters. A shortcut for htmlspecialchars.
     *
     * @param string|null $string The string to escape.
     * @return string The escaped string.
     */
    function e($string) {
        return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
    }
}
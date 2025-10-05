<?php
// app/helpers/db.php - Database Connection Helper

// This function establishes a connection to the database using the configuration
// file and returns a PDO object.

function get_db_connection() {
    static $pdo = null;

    if ($pdo === null) {
        $config_path = __DIR__ . '/../../config/config.php';

        if (!file_exists($config_path)) {
            // This message is shown if setup.php has not been run.
            die(
                "<h1>Configuration file not found!</h1>" .
                "<p>Please run the <a href='../setup.php'>setup script</a> to configure the application.</p>" .
                "<p>If you have already run the setup script and are still seeing this message, " .
                "please check the file permissions of the 'config' directory.</p>"
            );
        }

        $config = require $config_path;

        $dsn = "mysql:host={$config['db_host']};dbname={$config['db_name']};charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $pdo = new PDO($dsn, $config['db_user'], $config['db_pass'], $options);
        } catch (PDOException $e) {
            // Provide a more user-friendly error message for connection failures.
            die("<h1>Database Connection Error</h1><p>Could not connect to the database. Please check your configuration and ensure the database server is running.</p><p>Error details: " . $e->getMessage() . "</p>");
        }
    }

    return $pdo;
}
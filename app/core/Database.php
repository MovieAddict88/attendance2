<?php
// app/core/Database.php

class Database {
    private static $instance = null;
    private $pdo;
    private $host;
    private $name;
    private $user;
    private $pass;

    private function __construct($config) {
        $this->host = $config['db_host'];
        $this->name = $config['db_name'];
        $this->user = $config['db_user'];
        $this->pass = $config['db_pass'];

        $dsn = "mysql:host={$this->host};dbname={$this->name};charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->pdo = new PDO($dsn, $this->user, $this->pass, $options);
        } catch (PDOException $e) {
            // In a real app, you'd log this error, not display it to the user
            die("Database connection failed: " . $e->getMessage());
        }
    }

    /**
     * Get the singleton instance of the Database connection.
     *
     * @param array|null $config The database configuration array.
     * @return PDO The PDO database connection object.
     */
    public static function getInstance($config = null) {
        if (self::$instance === null) {
            if ($config === null) {
                // This is a fallback in case config is not passed on subsequent calls,
                // though it's best practice to always pass it on first instantiation.
                $config_path = BASE_PATH . '/config/config.php';
                if (!file_exists($config_path)) {
                    die("Configuration file is missing and is required to initialize the database.");
                }
                $config = require $config_path;
            }
            self::$instance = new self($config);
        }
        return self::$instance->pdo;
    }

    // Prevent cloning and unserialization of the instance
    private function __clone() {}
    public function __wakeup() {}
}
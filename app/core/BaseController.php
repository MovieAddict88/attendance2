<?php
// app/core/BaseController.php

class BaseController {
    protected $db;
    protected $config;

    public function __construct($config) {
        $this->config = $config;
        $this->db = Database::getInstance($config);

        // Enforce authentication for all controllers that extend this base controller
        if (!isset($_SESSION['user_id'])) {
            redirect(base_url('login'));
        }
    }

    /**
     * Check if the current user has one of the allowed roles.
     * If not, it shows an error page and terminates the script.
     *
     * @param array $allowed_roles An array of roles that are allowed to access the resource.
     */
    protected function authorize(array $allowed_roles) {
        $user_role = $_SESSION['user_role'] ?? 'guest';

        if (!in_array($user_role, $allowed_roles)) {
            // User does not have the required role, show an access denied error.
            http_response_code(403);
            view('errors/403', ['title' => 'Access Denied']);
            exit;
        }
    }

    /**
     * Loads a model.
     *
     * @param string $model_name The name of the model to load (e.g., 'User').
     * @return object The model object.
     */
    protected function model($model_name) {
        $model_file = BASE_PATH . '/app/models/' . $model_name . '.php';
        if (file_exists($model_file)) {
            require_once $model_file;
            return new $model_name($this->db);
        } else {
            die("Model '{$model_name}' not found.");
        }
    }
}
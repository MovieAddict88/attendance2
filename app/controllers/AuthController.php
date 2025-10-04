<?php
// app/controllers/AuthController.php

require_once BASE_PATH . '/app/models/User.php';

class AuthController {
    private $db;
    private $user_model;

    public function __construct($config) {
        $this->db = Database::getInstance($config);
        $this->user_model = new User($this->db);
    }

    /**
     * Display the login page.
     */
    public function index() {
        // If user is already logged in, redirect them to their dashboard
        if (isset($_SESSION['user_id'])) {
            redirect(base_url('dashboard'));
        }

        // Load the login view
        view('auth/login');
    }

    /**
     * Handle the login form submission.
     */
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(base_url('login'));
        }

        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            view('auth/login', ['error_message' => 'Username and password are required.']);
            return;
        }

        // Find user by username
        $user = $this->user_model->findByUsername($username);

        if ($user && password_verify($password, $user['password'])) {
            // Password is correct, set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_fullname'] = $user['fullname'];

            // Redirect to the dashboard
            redirect(base_url('dashboard'));
        } else {
            // Invalid credentials
            view('auth/login', ['error_message' => 'Invalid username or password.']);
        }
    }

    /**
     * Handle user logout.
     */
    public function logout() {
        session_start();
        session_unset();
        session_destroy();
        redirect(base_url('login'));
    }
}
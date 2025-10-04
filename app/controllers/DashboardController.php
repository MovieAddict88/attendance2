<?php
// app/controllers/DashboardController.php

require_once BASE_PATH . '/app/core/BaseController.php';

class DashboardController extends BaseController {

    public function __construct($config) {
        // We call parent constructor which handles the basic session check.
        parent::__construct($config);
    }

    /**
     * Redirects the user to the correct dashboard based on their role.
     */
    public function index() {
        $role = $_SESSION['user_role'] ?? 'guest';

        switch ($role) {
            case 'superadmin':
                redirect(base_url('admin'));
                break;
            case 'teacher':
                redirect(base_url('teacher'));
                break;
            case 'student':
                redirect(base_url('student'));
                break;
            case 'parent':
                redirect(base_url('parent'));
                break;
            default:
                // If role is unknown or guest, log them out for security.
                redirect(base_url('logout'));
                break;
        }
    }
}
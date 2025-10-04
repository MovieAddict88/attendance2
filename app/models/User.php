<?php
// app/models/User.php

class User {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Find a user by their username.
     *
     * @param string $username The username to search for.
     * @return array|false The user data as an associative array, or false if not found.
     */
    public function findByUsername($username) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE username = :username");
            $stmt->execute([':username' => $username]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            // In a real application, you would log this error.
            // For now, we'll just return false.
            error_log("Database error in findByUsername: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get a user by their ID.
     *
     * @param int $id The user's ID.
     * @return array|false The user data, or false if not found.
     */
    public function findById($id) {
        try {
            $stmt = $this->pdo->prepare("SELECT id, username, role, fullname, email, avatar FROM users WHERE id = :id");
            $stmt->execute([':id' => $id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Database error in findById: " . $e->getMessage());
            return false;
        }
    }
}